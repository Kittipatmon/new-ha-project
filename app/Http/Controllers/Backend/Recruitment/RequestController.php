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
        // Auto-sync approved ManpowerRequests to RecruitmentRequests
        $approvedManpowers = \App\Models\ManpowerRequest::where('status', 'approved')->get();
        foreach ($approvedManpowers as $mReq) {
            $paddedNo = 'REQ-' . str_pad($mReq->id, 5, '0', STR_PAD_LEFT);
            $dept = Department::where('department_name', 'like', "%{$mReq->department}%")
                ->orWhere('department_fullname', 'like', "%{$mReq->department}%")
                ->orWhere('department_description', 'like', "%{$mReq->department}%")
                ->first();
            $deptId = $dept ? $dept->department_id : 1;

            $duties = array_filter([
                $mReq->res_1, $mReq->res_2, $mReq->res_3, $mReq->res_4, $mReq->res_5, $mReq->res_6
            ]);
            $qual = array_filter([
                $mReq->req_gender ? "เพศ: " . $mReq->req_gender : null,
                $mReq->req_age ? "อายุ: " . $mReq->req_age : null,
                $mReq->req_education ? "วุฒิการศึกษา: " . $mReq->req_education : null,
                $mReq->req_major ? "สาขาวิชา: " . $mReq->req_major : null,
                $mReq->req_experience ? "ประสบการณ์ทำงาน: " . $mReq->req_experience : null,
                $mReq->req_special ? "คุณสมบัติพิเศษ: " . $mReq->req_special : null,
                $mReq->req_other ? "อื่นๆ: " . $mReq->req_other : null,
            ]);

            RecruitmentRequest::firstOrCreate(
                ['request_no' => $paddedNo],
                [
                    'department_id' => $deptId,
                    'position_name' => $mReq->job_title_th ?: $mReq->job_title_en,
                    'requested_by' => $mReq->user_id ?? 0,
                    'headcount' => $mReq->headcount ?? 1,
                    'reason' => 'ลักษณะการว่าจ้าง: ' . $mReq->hire_type,
                    'job_description' => "ระดับ: " . ($mReq->job_level ?? '-') . "\nหน้าที่ความรับผิดชอบ:\n" . implode("\n", $duties),
                    'qualification' => implode("\n", $qual),
                    'required_start_date' => $mReq->expected_start_date,
                    'status' => 'approved',
                ]
            );
        }

        $requests = RecruitmentRequest::with(['department', 'jobPosition', 'requester', 'jobPosts'])
            ->withCount('jobPosts')
            ->orderBy('created_at', 'desc')
            ->get();

        // Count approved requests that do not have a JobPost yet
        $pendingJobPostCount = $requests->where('status', 'approved')
            ->where('job_posts_count', 0)
            ->count();

        $departments = Department::orderBy('department_fullname')->get();

        return view('backend.recruitment.requests.index', compact('requests', 'pendingJobPostCount', 'departments'));
    }

    public function create()
    {
        // คำขอเปิดรับสมัครพนักงานควรอ้างอิง/กรอกจากใบขออนุมัติกำลังคน (Manpower Request)
        return redirect()->route('manpower-request.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:department,department_id',
            'job_position_id' => 'nullable|exists:recruitment_job_positions,id',
            'position_name' => 'required|string|max:255',
            'headcount' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'job_description' => 'nullable|string',
            'qualification' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'required_start_date' => 'nullable|date',
            'approver_manager_id' => 'required|exists:userkml2025.employees,id',
            'approver_executive_id' => 'required|exists:userkml2025.employees,id',
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
        $recruitmentRequest->load(['department', 'jobPosition', 'requester', 'managerApprover', 'executiveApprover', 'jobPosts']);
        
        // Find associated ManpowerRequest if applicable
        $manpowerRequest = null;
        if (str_starts_with($recruitmentRequest->request_no, 'REQ-')) {
            $rawId = substr($recruitmentRequest->request_no, 4);
            if (is_numeric($rawId)) {
                $manpowerRequest = \App\Models\ManpowerRequest::with(['user', 'managerApprover', 'vpApprover', 'hrApprover', 'ceoApprover'])->find((int)$rawId);
            }
        }

        if (\Schema::connection('userkml2025')->hasColumn('employees', 'level_user')) {
            $approvers = \App\Models\User::where('level_user', '>=', '5')
                ->where('status', '0')
                ->get();
        } else {
            $approvers = \App\Models\User::active()->where('role', 'admin')->get();
        }
        return view('backend.recruitment.requests.show', compact('recruitmentRequest', 'manpowerRequest', 'approvers'));
    }

    public function updateApprover(Request $request, RecruitmentRequest $recruitmentRequest)
    {
        $validated = $request->validate([
            'approver_type' => 'required|in:manager,executive',
            'approver_id' => 'required|exists:userkml2025.employees,id',
        ]);

        // Authorization: Only HR or the requester can change approvers
        if (!Auth::user()->isHrOrAdmin() && Auth::id() != $recruitmentRequest->requested_by) {
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
