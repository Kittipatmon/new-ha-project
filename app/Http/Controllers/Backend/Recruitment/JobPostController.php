<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\JobPost;
use App\Models\Recruitment\RecruitmentRequest;
use App\Models\Recruitment\Department;
use App\Models\Recruitment\JobPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JobPostController extends Controller
{
    public function index()
    {
        // Auto-close any published posts whose end_date has passed
        JobPost::autoCloseExpired();

        $posts = JobPost::with(['department', 'jobPosition'])
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = Department::orderBy('department_fullname')->get();

        return view('backend.recruitment.posts.index', compact('posts', 'departments'));
    }

    public function create(Request $request)
    {
        $recruitmentRequest = null;
        if ($request->has('request_id')) {
            $recruitmentRequest = RecruitmentRequest::find($request->request_id);
        } elseif ($request->has('manpower_request_id')) {
            $manpowerRequest = \App\Models\ManpowerRequest::find($request->manpower_request_id);
            if ($manpowerRequest) {
                $dept = Department::where('department_name', 'like', "%{$manpowerRequest->department}%")
                    ->orWhere('department_fullname', 'like', "%{$manpowerRequest->department}%")
                    ->orWhere('department_description', 'like', "%{$manpowerRequest->department}%")
                    ->first();
                $deptId = $dept ? $dept->department_id : (auth()->user()?->dept_id ?: (Department::first()?->department_id ?? 1));

                $duties = array_filter([
                    $manpowerRequest->res_1,
                    $manpowerRequest->res_2,
                    $manpowerRequest->res_3,
                    $manpowerRequest->res_4,
                    $manpowerRequest->res_5,
                    $manpowerRequest->res_6,
                ]);

                $qual = array_filter([
                    $manpowerRequest->req_gender ? "เพศ: " . $manpowerRequest->req_gender : null,
                    $manpowerRequest->req_age ? "อายุ: " . $manpowerRequest->req_age : null,
                    $manpowerRequest->req_education ? "วุฒิการศึกษา: " . $manpowerRequest->req_education : null,
                    $manpowerRequest->req_major ? "สาขาวิชา: " . $manpowerRequest->req_major : null,
                    $manpowerRequest->req_experience ? "ประสบการณ์ทำงาน: " . $manpowerRequest->req_experience : null,
                    $manpowerRequest->req_special ? "คุณสมบัติพิเศษ: " . $manpowerRequest->req_special : null,
                    $manpowerRequest->req_other ? "อื่นๆ: " . $manpowerRequest->req_other : null,
                ]);

                $recruitmentRequest = RecruitmentRequest::firstOrCreate(
                    ['request_no' => 'REQ-' . str_pad($manpowerRequest->id, 5, '0', STR_PAD_LEFT)],
                    [
                        'department_id' => $deptId,
                        'position_name' => $manpowerRequest->job_title_th ?: $manpowerRequest->job_title_en,
                        'requested_by' => $manpowerRequest->user_id ?? auth()->id() ?? 0,
                        'headcount' => $manpowerRequest->headcount ?? 1,
                        'reason' => 'ลักษณะการว่าจ้าง: ' . $manpowerRequest->hire_type,
                        'job_description' => "ระดับ: " . ($manpowerRequest->job_level ?? '-') . "\nหน้าที่ความรับผิดชอบ:\n" . implode("\n", $duties),
                        'qualification' => implode("\n", $qual),
                        'required_start_date' => $manpowerRequest->expected_start_date,
                        'status' => 'approved',
                    ]
                );
            }
        }

        $departments = Department::where('department_status', '0')->get();
        $positions = JobPosition::where('status', 'active')->get();

        // Get all approved Recruitment Requests for the dropdown reference
        $approvedRequests = RecruitmentRequest::where('status', 'approved')
            ->with(['department', 'jobPosts'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Pull distinct positions from existing employees (safely fallback if column does not exist)
        if (\Schema::connection('userkml2025')->hasColumn('employees', 'position')) {
            $employeePositions = \App\Models\User::whereNotNull('position')
                ->where('position', '!=', '')
                ->distinct()
                ->pluck('position');
        } else {
            $employeePositions = collect([]);
        }

        return view('backend.recruitment.posts.create', compact('departments', 'positions', 'recruitmentRequest', 'approvedRequests', 'employeePositions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:department,department_id',
            'job_position_id' => 'nullable|exists:recruitment_job_positions,id',
            'position_name' => 'required|string|max:255',
            'recruitment_request_id' => 'nullable|exists:recruitment_requests,id',
            'vacancy' => 'required|integer|min:1',
            'employment_type' => 'required|string',
            'urgency' => 'nullable|string|in:normal,urgent,very_urgent',
            'location' => 'nullable|string',
            'work_schedule' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'salary_note' => 'nullable|string',
            'job_description' => 'required|string',
            'qualification' => 'required|string',
            'benefits' => 'nullable|string',
            'required_documents' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'publish_status' => 'required|in:draft,published,closed',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['created_by'] = Auth::id();

        if ($validated['publish_status'] === 'published') {
            $validated['published_at'] = now();
        }

        // Handle dynamic required_documents array
        if ($request->has('required_documents') && is_array($request->required_documents)) {
            $validated['required_documents'] = implode("\n", array_filter($request->required_documents));
        }

        JobPost::create($validated);

        return redirect()->route('backend.recruitment.posts.index')
            ->with('success', 'ประกาศรับสมัครงานถูกสร้างเรียบร้อยแล้ว');
    }

    public function edit(JobPost $jobPost)
    {
        if ($jobPost->publish_status === 'closed') {
            return redirect()->route('backend.recruitment.posts.index')
                ->with('error', 'ไม่สามารถแก้ไขประกาศที่ยกเลิกแล้วได้ สามารถดูรายละเอียดได้อย่างเดียว');
        }

        $departments = Department::where('department_status', '0')->get();
        $positions = JobPosition::where('status', 'active')->get();

        // Pull distinct positions from existing employees (safely fallback if column does not exist)
        if (\Schema::connection('userkml2025')->hasColumn('employees', 'position')) {
            $employeePositions = \App\Models\User::whereNotNull('position')
                ->where('position', '!=', '')
                ->distinct()
                ->pluck('position');
        } else {
            $employeePositions = collect([]);
        }

        return view('backend.recruitment.posts.edit', compact('jobPost', 'departments', 'positions', 'employeePositions'));
    }

    public function update(Request $request, JobPost $jobPost)
    {
        if ($jobPost->publish_status === 'closed') {
            return redirect()->route('backend.recruitment.posts.index')
                ->with('error', 'ไม่สามารถแก้ไขประกาศที่ยกเลิกแล้วได้ สามารถดูรายละเอียดได้อย่างเดียว');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:department,department_id',
            'job_position_id' => 'nullable|exists:recruitment_job_positions,id',
            'position_name' => 'required|string|max:255',
            'vacancy' => 'required|integer|min:1',
            'employment_type' => 'required|string',
            'urgency' => 'nullable|string|in:normal,urgent,very_urgent',
            'location' => 'nullable|string',
            'work_schedule' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'salary_note' => 'nullable|string',
            'job_description' => 'required|string',
            'qualification' => 'required|string',
            'benefits' => 'nullable|string',
            'required_documents' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'publish_status' => 'required|in:draft,published,closed',
        ]);

        $validated['updated_by'] = Auth::id();

        if ($validated['publish_status'] === 'published' && $jobPost->publish_status !== 'published') {
            $validated['published_at'] = now();
        }

        // Handle dynamic required_documents array
        if ($request->has('required_documents') && is_array($request->required_documents)) {
            $validated['required_documents'] = implode("\n", array_filter($request->required_documents));
        }

        $jobPost->update($validated);

        return redirect()->route('backend.recruitment.posts.index')
            ->with('success', 'อัปเดตประกาศเรียบร้อยแล้ว');
    }

    public function destroy(JobPost $jobPost)
    {
        // Don't hard delete: cancel the post and mark status as closed
        $jobPost->update([
            'publish_status' => 'closed',
        ]);
        return back()->with('success', 'ยกเลิกประกาศรับสมัครงานเรียบร้อยแล้ว');
    }
}
