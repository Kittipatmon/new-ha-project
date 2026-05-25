<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\RecruitmentRequest;
use App\Models\Recruitment\Department;
use App\Models\Recruitment\JobPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function index()
    {
        $requests = RecruitmentRequest::with(['department', 'jobPosition', 'requester'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.recruitment.requests.index', compact('requests'));
    }

    public function create()
    {
        $departments = Department::where('department_status', '0')->get();
        $positions = JobPosition::where('status', 'active')->get();

        $employeePositions = \App\Models\User::whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position');

        // Fetch potential approvers (Level 5 and above: Head Section, Dept Mgr, etc.)
        $approvers = \App\Models\User::where('level_user', '>=', '5')
            ->where('status', '0')
            ->get();

        return view('backend.recruitment.requests.create', compact('departments', 'positions', 'employeePositions', 'approvers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:userkml2025.department,department_id',
            'job_position_id' => 'nullable|exists:recruitment_job_positions,id',
            'position_name' => 'required|string|max:255',
            'headcount' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'job_description' => 'nullable|string',
            'qualification' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'required_start_date' => 'nullable|date',
            'approver_manager_id' => 'required|exists:userkml2025.userskml,id',
            'approver_executive_id' => 'required|exists:userkml2025.userskml,id',
        ]);

        $validated['request_no'] = 'REQ-' . strtoupper(Str::random(8));
        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'pending_manager'; // Default status when submitted

        RecruitmentRequest::create($validated);

        return redirect()->route('backend.recruitment.requests.index')
            ->with('success', 'คำขอเปิดอัตรากำลังถูกสร้างเรียบร้อยแล้ว');
    }

    public function show(RecruitmentRequest $recruitmentRequest)
    {
        $recruitmentRequest->load(['department', 'jobPosition', 'requester', 'managerApprover', 'executiveApprover']);
        $approvers = \App\Models\User::where('level_user', '>=', '5')
            ->where('status', '0')
            ->get();
        return view('backend.recruitment.requests.show', compact('recruitmentRequest', 'approvers'));
    }

    public function updateApprover(Request $request, RecruitmentRequest $recruitmentRequest)
    {
        $validated = $request->validate([
            'approver_type' => 'required|in:manager,executive',
            'approver_id' => 'required|exists:userkml2025.userskml,id',
        ]);

        // Authorization: Only HR or the requester can change approvers
        if (Auth::user()->hr_status != 0 && Auth::id() != $recruitmentRequest->requested_by) {
            return back()->with('error', 'คุณไม่มีสิทธิ์แก้ไขผู้อนุมัติสำหรับคำขอนี้');
        }

        if ($validated['approver_type'] === 'manager') {
            $recruitmentRequest->update(['approver_manager_id' => $validated['approver_id']]);
        } else {
            $recruitmentRequest->update(['approver_executive_id' => $validated['approver_id']]);
        }

        return back()->with('success', 'แก้ไขผู้อนุมัติเรียบร้อยแล้ว');
    }

    public function approve(Request $request, RecruitmentRequest $recruitmentRequest)
    {
        // Simple approval logic for now
        $user = Auth::user();

        if ($recruitmentRequest->status === 'pending_manager') {
            if ($user->id != $recruitmentRequest->approver_manager_id) { #&& $user->hr_status != 0
                return back()->with('error', 'คุณไม่ใช่ผู้อนุมัติที่ได้รับมอบหมายสำหรับขั้นตอนนี้');
            }
            $recruitmentRequest->update([
                'status' => 'pending_executive',
                'approved_by_manager' => $user->id,
                'approved_at_manager' => now(),
            ]);
        } elseif ($recruitmentRequest->status === 'pending_executive') {
            if ($user->id != $recruitmentRequest->approver_executive_id) { #&& $user->hr_status != 0
                return back()->with('error', 'คุณไม่ใช่ผู้อนุมัติที่ได้รับมอบหมายสำหรับขั้นตอนนี้');
            }
            $recruitmentRequest->update([
                'status' => 'approved',
                'approved_by_executive' => $user->id,
                'approved_at_executive' => now(),
            ]);
        }

        return back()->with('success', 'อนุมัติคำขอเรียบร้อยแล้ว');
    }

    public function reject(Request $request, RecruitmentRequest $recruitmentRequest)
    {
        $recruitmentRequest->update([
            'status' => 'rejected',
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'ไม่อนุมัติคำขอเรียบร้อยแล้ว');
    }
}
