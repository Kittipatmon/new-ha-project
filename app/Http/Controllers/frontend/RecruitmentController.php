<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\JobPost;
use App\Models\Recruitment\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationReceived;
use App\Models\Recruitment\RecruitmentRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::with(['department', 'jobPosition'])
            ->where('publish_status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });

        // Filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('type')) {
            $query->where('employment_type', $request->type);
        }

        $posts = $query->orderBy('published_at', 'desc')->paginate(12);

        $activeDepartmentIds = JobPost::where('publish_status', 'published')
            ->distinct()
            ->pluck('department_id');

        $departments = Department::whereIn('department_id', $activeDepartmentIds)->get();

        return view('frontend.recruitment.index', compact('posts', 'departments'));
    }

    public function show($slug)
    {
        $post = JobPost::with(['department', 'jobPosition'])
            ->where('slug', $slug)
            ->where('publish_status', 'published')
            ->firstOrFail();

        return view('frontend.recruitment.show', compact('post'));
    }

    public function apply($slug)
    {
        $post = JobPost::where('slug', $slug)
            ->where('publish_status', 'published')
            ->firstOrFail();

        return view('frontend.recruitment.apply', compact('post'));
    }

    public function submitApplication(Request $request, $slug)
    {
        $post = JobPost::where('slug', $slug)
            ->where('publish_status', 'published')
            ->firstOrFail();

        $validated = $request->validate([
            // Step 1: Personal Info
            'prefix' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'line_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'province' => 'nullable|string|max:100',

            // Step 2: Education & Experience (JSON or Arrays)
            'education' => 'nullable|array',
            'education.*.level' => 'required|string',
            'education.*.institution_name' => 'required|string',
            'education.*.faculty' => 'nullable|string',
            'education.*.major' => 'nullable|string',
            'education.*.start_year' => 'nullable|string',
            'education.*.end_year' => 'nullable|string',
            'education.*.gpa' => 'nullable|numeric|between:0,4.00',

            'experience' => 'nullable|array',
            'experience.*.company_name' => 'required|string',
            'experience.*.position' => 'required|string',
            'experience.*.start_date' => 'nullable|date',
            'experience.*.end_date' => 'nullable|date',
            'experience.*.job_detail' => 'nullable|string',
            'experience.*.salary' => 'nullable|numeric',
            'experience.*.reason_for_leaving' => 'nullable|string',

            // Step 3: Files
            'portfolio' => 'nullable|file|mimes:pdf|max:10240',
            'pdpa_consent' => 'accepted',
        ], [
            'required' => 'กรุณากรอกข้อมูล :attribute',
            'email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'numeric' => ':attribute ต้องเป็นตัวเลขเท่านั้น',
            'between' => ':attribute ต้องอยู่ระหว่าง :min ถึง :max',
            'max' => ':attribute ต้องไม่เกิน :max ตัวอักษร/KB',
            'mimes' => ':attribute ต้องเป็นไฟล์ประเภท :values เท่านั้น',
            'accepted' => 'กรุณายอมรับเงื่อนไขการคุ้มครองข้อมูลส่วนบุคคล (PDPA)',
        ], [
            'first_name' => 'ชื่อ',
            'last_name' => 'นามสกุล',
            'email' => 'อีเมล',
            'phone' => 'เบอร์โทรศัพท์',
            'education.*.level' => 'ระดับการศึกษา',
            'education.*.institution_name' => 'ชื่อสถาบัน',
            'education.*.gpa' => 'เกรดเฉลี่ย (GPA)',
            'experience.*.company_name' => 'ชื่อบริษัท',
            'experience.*.position' => 'ตำแหน่ง',
            'resume' => 'ไฟล์ Resume',
        ]);

        // 1. Create or Update Applicant
        $applicant = null;
        if (!empty($validated['national_id'])) {
            $applicant = \App\Models\Recruitment\Applicant::where('national_id', $validated['national_id'])->first();
        }

        if (!$applicant) {
            // Check for application limit (max 3 per day per email) ส่งเมลล์เดิมได้ 3ครั้งต่อวัน 
            $todayApplicationsCount = \App\Models\Recruitment\Application::whereHas('applicant', function ($q) use ($validated) {
                $q->where('email', $validated['email']);
            })->whereDate('applied_at', now()->toDateString())->count();

            if ($todayApplicationsCount >= 3) {
                return back()->withInput()->withErrors(['email' => 'คุณส่งใบสมัครเกินโควตา 3 ครั้งต่อวันสำหรับอีเมลนี้แล้ว กรุณาลองใหม่ในวันพรุ่งนี้ (Email limit exceeded: max 3/day)']);
            }

            $applicant = \App\Models\Recruitment\Applicant::where('email', $validated['email'])
                ->first();
        }

        $applicantData = [
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'prefix' => $validated['prefix'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'national_id' => $validated['national_id'] ?? null,
            'phone' => $validated['phone'],
            'line_id' => $validated['line_id'] ?? null,
            'address' => $validated['address'] ?? null,
            'province' => $validated['province'] ?? null,
            'status' => 'active',
        ];

        // Sync main applicant table with latest education/experience if available
        if (!empty($validated['education']) && isset($validated['education'][0])) {
            $firstEdu = $validated['education'][0];
            $applicantData['education_level'] = $firstEdu['level'] ?? null;
            $applicantData['university_name'] = $firstEdu['institution_name'] ?? null;
            $applicantData['faculty'] = $firstEdu['faculty'] ?? null;
            $applicantData['major'] = $firstEdu['major'] ?? null;
            $applicantData['gpa'] = $firstEdu['gpa'] ?? null;
        }

        if (!empty($validated['experience']) && isset($validated['experience'][0])) {
            $firstExp = $validated['experience'][0];
            $applicantData['current_company'] = $firstExp['company_name'] ?? null;
            $applicantData['current_position'] = $firstExp['position'] ?? null;
            $applicantData['expected_salary'] = $firstExp['salary'] ?? null;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            if ($applicant) {
                $applicant->update($applicantData);
            } else {
                $applicant = \App\Models\Recruitment\Applicant::create($applicantData);
            }

            // 3. Create Application
            $application = \App\Models\Recruitment\Application::create([
                'job_post_id' => $post->id,
                'applicant_id' => $applicant->id,
                'application_no' => 'APP-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'status' => 'new',
                'applied_at' => now(),
            ]);

            // 4. Handle Education History
            if (!empty($validated['education'])) {
                foreach ($validated['education'] as $edu) {
                    \App\Models\Recruitment\ApplicationEducation::create(array_merge($edu, [
                        'applicant_id' => $applicant->id,
                        'application_id' => $application->id
                    ]));
                }
            }

            // 5. Handle Experience History
            if (!empty($validated['experience'])) {
                foreach ($validated['experience'] as $exp) {
                    \App\Models\Recruitment\ApplicationExperience::create(array_merge($exp, [
                        'applicant_id' => $applicant->id,
                        'application_id' => $application->id
                    ]));
                }
            }

            // 6. Handle File Uploads
            $fileTypes = ['resume' => 'resume', 'photo' => 'photo_file', 'portfolio' => 'portfolio'];
            $targetDir = public_path('files/recruitment_applicant_documents');

            // Ensure directory exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            foreach ($fileTypes as $field => $docType) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $originalName = $file->getClientOriginalName();
                    $fileSize = $file->getSize();
                    $filename = time() . '_' . $field . '_' . $originalName;

                    // Move file to public directory
                    $file->move($targetDir, $filename);
                    $path = $filename; // Store just the filename

                    \App\Models\Recruitment\ApplicantDocument::create([
                        'application_id' => $application->id,
                        'document_type' => $field,
                        'file_name' => $originalName,
                        'file_path' => $path,
                        'file_size' => $fileSize,
                    ]);

                    // Update applicant model for legacy/shortcut access
                    if ($field === 'resume')
                        $applicant->update(['resume_file' => $path]);
                    if ($field === 'photo')
                        $applicant->update(['photo_file' => $path]);
                    if ($field === 'portfolio')
                        $applicant->update(['portfolio_file' => $path]);
                }
            }

            // 7. Log Status
            \App\Models\Recruitment\StatusLog::create([
                'application_id' => $application->id,
                'old_status' => null,
                'new_status' => 'new',
                'changed_by' => 0,
                'remark' => 'ส่งใบสมัครเรียบร้อยแล้ว (Multi-step Form)',
            ]);

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Application submission failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()]);
        }

        // ส่งอีเมลยืนยันการรับสมัคร (อยู่นอก transaction เพื่อความเร็วและไม่ให้ error การส่งเมลไปดึง DB กลับ)
        if ($applicant->email) {
            try {
                Mail::to($applicant->email)->send(new ApplicationReceived($application));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send application confirmation email: ' . $e->getMessage());
            }
        }

        return redirect()->route('recruitment.success', $post->slug);
    }

    public function success($slug)
    {
        $post = JobPost::where('slug', $slug)->firstOrFail();
        return view('frontend.recruitment.success', compact('post'));
    }

    public function requestReport()
    {
        $user = Auth::user();
        
        // Authorization: Only Managers, Executives, or HR (hr_status=0)
        // Managers/Executives are determined by being assigned to any request or having specific roles
        // For simplicity, we check if they are in any approver field or have hr_status = 0
        
        $query = RecruitmentRequest::with(['department', 'jobPosition', 'requester', 'managerApprover', 'executiveApprover', 'targetManagerApprover', 'targetExecutiveApprover'])
            ->orderBy('created_at', 'desc');

        // If not HR, filter by where they are the manager or executive approver
        if ($user->hr_status != 0) {
            $query->where(function($q) use ($user) {
                $q->where('approver_manager_id', $user->id)
                  ->orWhere('approver_executive_id', $user->id);
            });
        }

        $requests = $query->paginate(15);

        return view('frontend.recruitment.report', compact('requests'));
    }

    public function requestShow($id)
    {
        $user = Auth::user();
        $recruitmentRequest = RecruitmentRequest::with(['department', 'jobPosition', 'requester', 'managerApprover', 'executiveApprover', 'targetManagerApprover', 'targetExecutiveApprover'])->findOrFail($id);

        // Authorization check
        if ($user->hr_status != 0 && 
            $user->id != $recruitmentRequest->approver_manager_id && 
            $user->id != $recruitmentRequest->approver_executive_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('frontend.recruitment.request_show', compact('recruitmentRequest'));
    }
}
