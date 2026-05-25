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
        $posts = JobPost::with(['department', 'jobPosition'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.recruitment.posts.index', compact('posts'));
    }

    public function create(Request $request)
    {
        $recruitmentRequest = null;
        if ($request->has('request_id')) {
            $recruitmentRequest = RecruitmentRequest::findOrFail($request->request_id);
        }

        $departments = Department::where('department_status', '0')->get();
        $positions = JobPosition::where('status', 'active')->get();

        // Pull distinct positions from existing employees
        $employeePositions = \App\Models\User::whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position');

        return view('backend.recruitment.posts.create', compact('departments', 'positions', 'recruitmentRequest', 'employeePositions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:userkml2025.department,department_id',
            'job_position_id' => 'nullable|exists:recruitment_job_positions,id',
            'position_name' => 'required|string|max:255',
            'recruitment_request_id' => 'nullable|exists:recruitment_requests,id',
            'vacancy' => 'required|integer|min:1',
            'employment_type' => 'required|string',
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
        $departments = Department::where('department_status', '0')->get();
        $positions = JobPosition::where('status', 'active')->get();

        // Pull distinct positions from existing employees
        $employeePositions = \App\Models\User::whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position');

        return view('backend.recruitment.posts.edit', compact('jobPost', 'departments', 'positions', 'employeePositions'));
    }

    public function update(Request $request, JobPost $jobPost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:userkml2025.department,department_id',
            'job_position_id' => 'nullable|exists:recruitment_job_positions,id',
            'position_name' => 'required|string|max:255',
            'vacancy' => 'required|integer|min:1',
            'employment_type' => 'required|string',
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
        $jobPost->delete();
        return back()->with('success', 'ลบประกาศเรียบร้อยแล้ว');
    }
}
