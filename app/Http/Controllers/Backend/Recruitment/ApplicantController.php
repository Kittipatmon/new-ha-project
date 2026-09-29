<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\Application;
use App\Models\Recruitment\StatusLog;
use App\Models\User;
use App\Models\OrgDepartment;
use App\Mail\NewCandidateDeptReviewNotification;
use App\Services\RecruitmentMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        // Get only the latest application ID for each applicant + job post combination
        $latestIds = Application::selectRaw('MAX(id) as id')
            ->groupBy('applicant_id', 'job_post_id')
            ->pluck('id');

        $query = Application::select('recruitment_applications.*')
            ->with([
                'applicant.applications.jobPost.department',
                'applicant.applications.jobPost.jobPosition',
                'jobPost.jobPosition',
                'jobPost.department'
            ])
            ->addSelect([
                'total_applications' => Application::from('recruitment_applications as app_count')
                    ->selectRaw('count(*)')
                    ->whereColumn('app_count.applicant_id', 'recruitment_applications.applicant_id')
            ])
            ->whereIn('id', $latestIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                // ค้นหาเฉพาะ: 1. ชื่อ-นามสกุล 2. อีเมล
                $sub->whereHas('applicant', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                // และ 3. ตำแหน่งงาน
                })->orWhereHas('jobPost', function ($q) use ($search) {
                    $q->where('position_name', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhereHas('jobPosition', function ($jq) use ($search) {
                            $jq->where('position_name', 'like', "%{$search}%");
                        });
                });
            });
        }

        if ($request->filled('job_post_id')) {
            $query->where('job_post_id', $request->job_post_id);
        }

        if ($request->filled('open_only') && $request->open_only == '1') {
            $query->whereHas('jobPost', function ($q) {
                $q->where('publish_status', 'published');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('applied_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('applied_at', '<=', $request->date_to);
        }

        $departments = \App\Models\Recruitment\Department::orderBy('department_fullname')->get();
        $applications = $query->orderBy('created_at', 'desc')->get();

        // Fetch open job posts (currently active recruitment)
        $openJobPosts = \App\Models\Recruitment\JobPost::with('department')
            ->where('publish_status', 'published')
            ->orderBy('title')
            ->get();

        // Fetch all job posts for list selection
        $allJobPosts = \App\Models\Recruitment\JobPost::with('department')
            ->orderByRaw("FIELD(publish_status, 'published', 'draft', 'closed')")
            ->orderBy('title')
            ->get();

        // Fetch dynamic popular positions from Job Posts + common default recruitment terms
        $dbPositions = \App\Models\Recruitment\JobPost::select('position_name')
            ->whereNotNull('position_name')
            ->where('position_name', '!=', '')
            ->distinct()
            ->limit(10)
            ->pluck('position_name')
            ->toArray();

        $defaultKeywords = ['ช่างซ่อมคอม', 'Developer', 'Programmer', 'วิศวกร', 'HR', 'บัญชี', 'การตลาด', 'ช่างไฟฟ้า'];
        $popularSearches = array_values(array_unique(array_filter(array_merge($dbPositions, $defaultKeywords))));

        return view('backend.recruitment.applications.index', compact(
            'applications',
            'departments',
            'popularSearches',
            'openJobPosts',
            'allJobPosts'
        ));
    }

    public function show($id)
    {
        $application = Application::with([
            'applicant.education',
            'applicant.experience',
            'education',
            'experience',
            'jobPost.department',
            'jobPost.jobPosition',
            'documents',
            'screener',
            'deptReviewer',
            'statusLogs.user',
            'interviews.interviewers',
            'interviews.interviewer',
            'interviews.scores',
        ])->findOrFail($id);

        // อัปเดตสถานะอัตโนมัติ ถ้ามีการนัดสัมภาษณ์แล้ว แต่สถานะยังเป็น new/screening
        if ($application->interviews->count() > 0 && in_array($application->status, ['new', 'screening'])) {
            $oldStatus = $application->status;
            $application->update([
                'status' => 'interview',
                'screened_by' => Auth::id(),
                'screened_at' => now(),
            ]);

            StatusLog::create([
                'application_id' => $application->id,
                'old_status' => $oldStatus,
                'new_status' => 'interview',
                'changed_by' => Auth::id(),
                'remark' => 'เปลี่ยนสถานะอัตโนมัติ — ตรวจพบว่ามีการนัดสัมภาษณ์แล้ว',
            ]);

            // Refresh เพื่อให้ view แสดงสถานะใหม่
            $application->refresh();
            $application->load('statusLogs.user');
        }

        $interviewers = \App\Models\User::orderBy('firstname')->get();

        // Fetch application history (excluding current)
        $history = Application::where('applicant_id', $application->applicant_id)
            ->where('id', '!=', $application->id)
            ->with(['jobPost.jobPosition'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.recruitment.applications.show', compact('application', 'interviewers', 'history'));
    }

    public function updateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $oldStatus = $application->status;

        $validated = $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
            'onboarding_date' => 'nullable|date',
        ]);

        // Guard status changes: non-HA users can only make department review & interview selection decisions, or propose start date
        $user = Auth::user();
        $isDeptUser = $user && !$user->isHrOrAdmin();
        if ($isDeptUser) {
            $allowedForDept = [
                'interview',
                'dept_rejected',
                'passed_selection',
                'selection_approved',
                'interview_failed',
                'offered',
            ];
            if (!in_array($validated['status'], $allowedForDept)) {
                return back()->with('error', 'สิทธิ์ไม่เพียงพอ: การปรับเปลี่ยนสถานะด้วยตนเองสามารถทำได้เฉพาะฝ่าย HA เท่านั้น');
            }
        }

        $updateData = [
            'status' => $validated['status'],
        ];

        // Track who performed the action based on status
        if (in_array($validated['status'], ['dept_review', 'screening_failed'])) {
            $updateData['screened_by'] = Auth::id();
            $updateData['screened_at'] = now();
        } elseif (in_array($validated['status'], ['dept_rejected', 'interview', 'interview_scheduled'])) {
            $updateData['dept_reviewed_by'] = Auth::id();
            $updateData['dept_reviewed_at'] = now();
        } elseif (in_array($validated['status'], ['passed_selection', 'selection_approved', 'hired', 'offered'])) {
            $updateData['final_result'] = $validated['status'];
            $updateData['final_result_at'] = now();
        }

        if ($request->filled('onboarding_date')) {
            $updateData['onboarding_date'] = $validated['onboarding_date'];
        }

        $application->update($updateData);

        // Meaningful default remark if note not provided
        $remark = $validated['note'] ?? $request->get('remark') ?? null;
        if (empty($remark)) {
            if ($validated['status'] === 'offered') {
                if ($isDeptUser) {
                    $dateStr = $application->onboarding_date ? $application->onboarding_date->format('d/m/Y') : '-';
                    $remark = "หัวหน้าแผนกกำหนดวันเริ่มงาน: {$dateStr} (ส่งต่อให้ฝ่าย HA ตรวจสอบและกดส่งแจ้งผู้สมัคร)";
                } else {
                    $remark = 'HA ดำเนินการยื่นข้อเสนอและส่งอีเมลแจ้งผลผ่านการคัดเลือกให้ผู้สมัครเรียบร้อยแล้ว';
                }
            } else {
                $remark = match ($validated['status']) {
                    'screening_failed' => 'HA ตรวจสอบข้อมูลแล้ว: ไม่ผ่านคุณสมบัติ (บันทึกจัดเก็บข้อมูล)',
                    'dept_review' => 'HA ตรวจสอบคุณสมบัติผ่าน: ส่งรายชื่อให้หัวหน้าแผนกพิจารณา',
                    'dept_rejected' => 'หัวหน้าแผนกพิจารณา: ไม่ผ่าน (ส่งกลับให้ HA พิจารณาผู้สมัครคนอื่น)',
                    'interview', 'interview_scheduled' => 'หัวหน้าแผนกพิจารณาผ่าน: ดำเนินการเตรียมนัดสัมภาษณ์',
                    'interview_failed' => 'ผลสัมภาษณ์: ไม่ผ่านเกณฑ์ (ส่งกลับให้ HA พิจารณาผู้สมัครคนอื่น)',
                    'passed_selection', 'selection_approved' => 'ผลสัมภาษณ์: ผ่านเกณฑ์ และกดอนุมัติผ่านการคัดเลือก',
                    'hired' => 'HA ตรวจสอบยืนยันและกดส่งแจ้งผู้สมัคร: รับเข้าทำงานเรียบร้อย (เริ่มงาน: ' . ($application->onboarding_date ? $application->onboarding_date->format('d/m/Y') : '-') . ')',
                    default => 'เปลี่ยนสถานะเป็น ' . $application->status_label,
                };
            }
        }

        StatusLog::create([
            'application_id' => $application->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'changed_by' => Auth::id(),
            'remark' => $remark,
        ]);

        // เมื่อ HA ตรวจสอบคุณสมบัติผ่าน และเปลี่ยนสถานะส่งให้ทางหัวหน้าแผนกพิจารณา (dept_review) -> ส่งอีเมลแจ้งเตือนหัวหน้าแผนก
        if ($oldStatus !== 'dept_review' && $validated['status'] === 'dept_review') {
            try {
                $deptRecipients = collect();
                $jobPost = $application->jobPost;
                $deptId = $jobPost?->department_id;

                // 1. ผู้จัดการแผนกตามโครงสร้างองค์กร (OrgDepartment manager_id)
                if ($deptId) {
                    $deptManagerId = OrgDepartment::where('id', $deptId)->value('manager_id');
                    if ($deptManagerId && $manager = User::find($deptManagerId)) {
                        if (!empty($manager->email) && filter_var($manager->email, FILTER_VALIDATE_EMAIL)) {
                            $deptRecipients->put($manager->email, $manager);
                        }
                    }
                }

                // 2. ผู้ร้องขออัตรากำลัง (Requester)
                $reqUser = $jobPost?->recruitmentRequest?->requester
                    ?? ($jobPost?->recruitmentRequest?->requested_by ? User::find($jobPost->recruitmentRequest->requested_by) : null);
                if ($reqUser && !empty($reqUser->email) && filter_var($reqUser->email, FILTER_VALIDATE_EMAIL)) {
                    $deptRecipients->put($reqUser->email, $reqUser);
                }

                // 3. Fallback: หากไม่พบอีเมลหัวหน้าแผนก ให้ส่งไปยังอีเมลกลาง/ทดสอบ (Kittipat.Ma@kumwell.com)
                if ($deptRecipients->isEmpty()) {
                    $fallbackEmail = config('recruitment.ha_notification_email', 'Kittipat.Ma@kumwell.com');
                    if (!empty($fallbackEmail)) {
                        $fallbackUser = User::where('email', $fallbackEmail)->first();
                        $deptRecipients->put($fallbackEmail, $fallbackUser);
                    }
                }

                foreach ($deptRecipients as $recipientEmail => $recipientUser) {
                    $mailable = new NewCandidateDeptReviewNotification(
                        $application,
                        $recipientUser,
                        Auth::user(),
                        $validated['note'] ?? null
                    );
                    RecruitmentMailService::queueMailable(
                        $mailable,
                        $recipientEmail,
                        $recipientUser?->fullname ?? 'หัวหน้าแผนก',
                        'dept_review_notification',
                        [
                            'application_id' => $application->id,
                            'application_no' => $application->application_no,
                            'position_name' => $jobPost?->position_name ?? ($jobPost?->jobPosition?->position_name ?? $jobPost?->title),
                            'applicant_name' => $application->applicant?->full_name ?? 'ผู้สมัคร',
                            'recipient_role' => 'Department Head',
                        ]
                    );
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send Dept Review email notification: ' . $e->getMessage());
            }
        }

        if ($validated['status'] === 'hired') {
            // Auto-link to Probation Evaluations table
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('probation_evaluations')) {
                    $applicant = $application->applicant;
                    $jobPost = $application->jobPost;
                    $empName = $applicant ? $applicant->full_name : "พนักงาน รหัสใบสมัคร {$application->application_no}";
                    $posName = $jobPost ? ($jobPost->position_name ?: $jobPost->title) : 'พนักงาน';
                    $deptName = $jobPost && $jobPost->department ? ($jobPost->department->department_name ?: 'สำนักงานใหญ่') : 'สำนักงานใหญ่';
                    $startDate = $application->onboarding_date ?: now();
                    $probDueDate = \Carbon\Carbon::parse($startDate)->addDays(119);

                    $exists = \Illuminate\Support\Facades\DB::table('probation_evaluations')->where('employee_name', $empName)->first();
                    if (!$exists) {
                        \Illuminate\Support\Facades\DB::table('probation_evaluations')->insert([
                            'user_id' => $applicant?->user_id ?? 0,
                            'prefix' => $applicant?->prefix ?? 'นาย',
                            'employee_name' => $empName,
                            'position' => $posName,
                            'emp_code' => 'EMP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT),
                            'department' => $deptName,
                            'start_date' => $startDate,
                            'probation_due_date' => $probDueDate,
                            'status' => 'pending_round_1',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Auto probation record notice: ' . $e->getMessage());
            }

            if ($application->applicant && $application->applicant->email) {
                try {
                    $mailable = new \App\Mail\ApplicationHired($application, Auth::user());
                    \App\Services\RecruitmentMailService::queueMailable(
                        $mailable,
                        $application->applicant->email,
                        $application->applicant->full_name,
                        'application_hired',
                        ['application_id' => $application->id, 'status' => 'hired'],
                        Auth::user()
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to queue hired email: ' . $e->getMessage());
                }
            }
        } elseif ($oldStatus !== 'offered' && $validated['status'] === 'offered' && $application->applicant && $application->applicant->email) {
            try {
                $mailable = new \App\Mail\ApplicationOffered($application, Auth::user());
                \App\Services\RecruitmentMailService::queueMailable(
                    $mailable,
                    $application->applicant->email,
                    $application->applicant->full_name,
                    'application_offered',
                    ['application_id' => $application->id, 'status' => 'offered'],
                    Auth::user()
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to queue offered email: ' . $e->getMessage());
            }
        } elseif (in_array($validated['status'], ['screening_failed', 'rejected']) && $application->applicant && $application->applicant->email) {
            try {
                $mailable = new \App\Mail\ApplicationRejected($application, Auth::user());
                \App\Services\RecruitmentMailService::queueMailable(
                    $mailable,
                    $application->applicant->email,
                    $application->applicant->full_name,
                    'application_rejected',
                    ['application_id' => $application->id, 'status' => $validated['status']],
                    Auth::user()
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to queue rejected email: ' . $e->getMessage());
            }
        }

        $successMsg = 'ดำเนินการอัปเดตสถานะ: ' . $application->status_label . ' เรียบร้อยแล้ว';
        if ($validated['status'] === 'offered') {
            if ($isDeptUser) {
                $successMsg = 'บันทึกกำหนดวันเริ่มงานเรียบร้อยแล้ว ส่งข้อมูลต่อให้ฝ่าย HA เพื่อยืนยันและส่งแจ้งผู้สมัคร';
            } else {
                $successMsg = 'ยื่นข้อเสนอและส่งอีเมลแจ้งผลผ่านการคัดเลือกให้ผู้สมัครเรียบร้อยแล้ว';
            }
        } elseif ($validated['status'] === 'hired') {
            $successMsg = 'บันทึกรับเข้าทำงานและส่งอีเมลยืนยันวันเริ่มงานให้ผู้สมัครเรียบร้อยแล้ว';
        }

        return back()->with('success', $successMsg);
    }
}
