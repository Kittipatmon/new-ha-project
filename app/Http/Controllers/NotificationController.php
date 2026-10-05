<?php

namespace App\Http\Controllers;

use App\Models\ManpowerRequest;
use App\Models\hrrequest\HrRequests;
use App\Models\Recruitment\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    /**
     * Display the full notifications page.
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser) {
            return redirect()->route('login');
        }

        $filterType = $request->query('type', 'all'); // 'all', 'recruitment', 'manpower', 'hr'
        $search = trim($request->query('search', ''));

        $notifications = collect();

        $userName = trim(($authUser->firstname ?? '') . ' ' . ($authUser->lastname ?? ''));
        $userFullName = trim($authUser->fullname ?? $userName);

        // 1. Recruitment Applications (ผู้สมัครงานส่งใบสมัครเข้ามาใหม่ & ส่งแผนกพิจารณา)
        if (Schema::hasTable('recruitment_applications')) {
            $userDeptId = $authUser->dept_id;
            $userId = $authUser->id;
            $isCentralHr = ($authUser->dept_id == 15 || $authUser->role === 'admin'); // ฝ่าย HA / Admin

            // A. ผู้สมัครส่งใบสมัครเข้ามาใหม่
            // แสดงเฉพาะฝ่าย HA / Admin เท่านั้น เพื่อให้ HA เป็นผู้คัดกรองเบื้องต้นก่อน
            if ($isCentralHr) {
                $newApplications = Application::with(['applicant', 'jobPost.department'])
                    ->whereIn('status', ['new', 'submitted'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($newApplications as $app) {
                    $applicantName = $app->applicant 
                        ? trim(($app->applicant->prefix ?? '') . ' ' . $app->applicant->first_name . ' ' . $app->applicant->last_name)
                        : 'ผู้สมัคร';
                    $jobTitle = $app->jobPost->title ?? ($app->jobPost->position_name ?? 'ตำแหน่งงาน');
                    $deptName = $app->jobPost->department->department_name ?? ($app->jobPost->department->department_fullname ?? '-');

                    $notifications->push([
                        'id' => 'recruitment_app_' . $app->id,
                        'category' => 'recruitment',
                        'category_label' => 'ระบบสรรหาบุคลากร',
                        'title' => 'ผู้สมัครส่งใบสมัครใหม่: ' . $applicantName,
                        'description' => "สมัครตำแหน่ง \"{$jobTitle}\" แผนก {$deptName} • เลขที่ใบสมัคร: " . ($app->application_no ?? '-'),
                        'extra_info' => 'สถานะ: ' . ($app->status_label ?? 'รอคัดกรองเบื้องต้น'),
                        'url' => route('backend.recruitment.applications.show', ['application' => $app->id]),
                        'icon' => 'fa-user-plus',
                        'color' => 'text-emerald-500',
                        'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
                        'border' => 'border-emerald-200 dark:border-emerald-800/40',
                        'badge_bg' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                        'badge_text' => 'ใบสมัครใหม่',
                        'created_at' => $app->applied_at ?? $app->created_at,
                        'time_ago' => ($app->applied_at ?? $app->created_at) ? ($app->applied_at ?? $app->created_at)->diffForHumans() : '',
                    ]);
                }
            }

            // B. ผู้สมัครที่ผ่านคุณสมบัติแล้ว ซึ่ง HA ส่งต่อให้แผนกพิจารณา (Dept Review)
            // หัวหน้าแต่ละแผนกจะเห็นเฉพาะคนที่ HA คัดกรองและส่งมาให้แผนกตนเองเท่านั้น (อ้างอิง manager_id จากตาราง departments)
            // คนทั่วไปในแผนกจะไม่ได้รับการแจ้งเตือนนี้
            $deptReviewQuery = Application::with(['applicant', 'jobPost.department'])
                ->where('status', 'dept_review');

            if (!$isCentralHr) {
                if ($authUser->isDepartmentManager()) {
                    $managedDeptIds = $authUser->getManagedDepartmentIds();
                    $deptReviewQuery->whereHas('jobPost', function($jq) use ($managedDeptIds) {
                        $jq->whereIn('department_id', $managedDeptIds);
                    });
                } else {
                    // ไม่ใช่หัวหน้าแผนก (ไม่ใช่ manager_id) -> ไม่เห็นการแจ้งเตือนนี้
                    $deptReviewQuery->whereRaw('1 = 0');
                }
            }

            $orderCol = \Illuminate\Support\Facades\Schema::hasColumn('recruitment_applications', 'dept_reviewed_at') ? 'dept_reviewed_at' : 'updated_at';
            $deptReviewApps = $deptReviewQuery->orderBy($orderCol, 'desc')->get();

            foreach ($deptReviewApps as $app) {
                $applicantName = $app->applicant 
                    ? trim(($app->applicant->prefix ?? '') . ' ' . $app->applicant->first_name . ' ' . $app->applicant->last_name)
                    : 'ผู้สมัคร';
                $jobTitle = $app->jobPost->title ?? ($app->jobPost->position_name ?? 'ตำแหน่งงาน');

                $notifications->push([
                    'id' => 'recruitment_dept_' . $app->id,
                    'category' => 'recruitment',
                    'category_label' => 'ระบบสรรหาบุคลากร',
                    'title' => 'HA ส่งผู้สมัครให้แผนกพิจารณา: ' . $applicantName,
                    'description' => "ตำแหน่ง \"{$jobTitle}\" ผ่านการคัดกรองเบื้องต้นจาก HA แล้ว รอหัวหน้างานประเมิน",
                    'extra_info' => 'สถานะ: รอพิจารณาความเหมาะสม',
                    'url' => route('recruitment.reports'),
                    'icon' => 'fa-user-check',
                    'color' => 'text-blue-500',
                    'bg' => 'bg-blue-50 dark:bg-blue-950/40',
                    'border' => 'border-blue-200 dark:border-blue-800/40',
                    'badge_bg' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                    'badge_text' => 'รอแผนกพิจารณา',
                    'created_at' => $app->dept_reviewed_at ?? $app->updated_at,
                    'time_ago' => ($app->dept_reviewed_at ?? $app->updated_at) ? ($app->dept_reviewed_at ?? $app->updated_at)->diffForHumans() : '',
                ]);
            }

            // C. ผู้สมัครที่มีการนัดหมายสัมภาษณ์ (Interview Scheduled)
            // สำหรับหัวหน้าแผนกผู้ขออัตรากำลัง, กรรมการสัมภาษณ์, หรือหัวหน้าแผนกที่เกี่ยวข้อง
            $scheduledInterviewsQuery = \App\Models\Recruitment\Interview::with([
                'application.applicant',
                'application.jobPost.department',
                'application.jobPost.recruitmentRequest',
                'interviewers'
            ])
            ->where('status', 'scheduled')
            ->where('interview_date', '>=', now()->subDays(1)->startOfDay());

            if (!$isCentralHr) {
                $managedDeptIds = $authUser->isDepartmentManager() ? $authUser->getManagedDepartmentIds() : [];
                $scheduledInterviewsQuery->where(function($q) use ($authUser, $managedDeptIds) {
                    $q->where(function($sub) use ($authUser) {
                        $sub->whereIn('id', function($iq) use ($authUser) {
                            $iq->select('interview_id')
                               ->from('recruitment_interview_interviewer')
                               ->where('user_id', $authUser->id);
                        })
                        ->orWhere('interviewer_id', $authUser->id);
                    })
                    ->orWhereHas('application.jobPost.recruitmentRequest', function($rq) use ($authUser) {
                        $rq->where('requested_by', $authUser->id);
                    })
                    ->orWhereHas('application', function($aq) use ($authUser) {
                        $aq->where('dept_reviewed_by', $authUser->id);
                    });

                    if (!empty($managedDeptIds)) {
                        $q->orWhereHas('application.jobPost', function($jq) use ($managedDeptIds) {
                            $jq->whereIn('department_id', $managedDeptIds);
                        });
                    }
                });
            }

            $scheduledInterviews = $scheduledInterviewsQuery->orderBy('interview_date', 'asc')->limit(10)->get();

            foreach ($scheduledInterviews as $iv) {
                $applicant = $iv->application?->applicant;
                $applicantName = $applicant ? trim(($applicant->prefix ?? '') . ' ' . $applicant->first_name . ' ' . $applicant->last_name) : 'ผู้สมัคร';
                $jobTitle = $iv->application?->jobPost?->title ?? ($iv->application?->jobPost?->position_name ?? 'ตำแหน่งงาน');
                $dateFormatted = \Carbon\Carbon::parse($iv->interview_date)->locale('th')->isoFormat('D MMM') . ' ' . (\Carbon\Carbon::parse($iv->interview_date)->year + 543);
                $timeFormatted = \Carbon\Carbon::parse($iv->interview_time)->format('H:i') . ' น.';

                $notifications->push([
                    'id' => 'recruitment_interview_' . $iv->id,
                    'category' => 'recruitment',
                    'category_label' => 'ระบบสรรหาบุคลากร',
                    'title' => 'นัดสัมภาษณ์: ' . $applicantName . ' (' . $jobTitle . ')',
                    'description' => "รอบที่ {$iv->interview_round} วันที่ {$dateFormatted} เวลา {$timeFormatted}" . ($iv->meeting_link ? ' (Online)' : ''),
                    'extra_info' => $iv->meeting_link ? "ลิ้งค์: {$iv->meeting_link}" : ($iv->location ? "สถานที่: {$iv->location}" : ''),
                    'url' => route('backend.recruitment.applications.show', ['application' => $iv->application_id]),
                    'icon' => 'fa-calendar-check',
                    'color' => 'text-purple-500',
                    'bg' => 'bg-purple-50 dark:bg-purple-950/40',
                    'border' => 'border-purple-200 dark:border-purple-800/40',
                    'badge_bg' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                    'badge_text' => 'นัดสัมภาษณ์',
                    'created_at' => $iv->updated_at ?? $iv->created_at,
                    'time_ago' => ($iv->updated_at ?? $iv->created_at) ? ($iv->updated_at ?? $iv->created_at)->diffForHumans() : '',
                ]);
            }
        }

        // 2. Manpower Requests (คำขออัตรากำลังคน)
        if (Schema::hasTable('manpower_requests')) {
            $userDeptName = $authUser->department->department_name ?? ($authUser->department->department_fullname ?? '');
            $isCentralHr = ($authUser->dept_id == 15);

            $mpQuery = ManpowerRequest::whereNotIn('status', ['approved', 'rejected', 'draft']);

            if (!$isCentralHr) {
                // แสดงเฉพาะคำขอของแผนกตนเอง หรือคำขอที่ตนเองเกี่ยวข้อง
                $mpQuery->where(function($q) use ($authUser, $userName, $userFullName, $userDeptName) {
                    $q->where('user_id', $authUser->id)
                      ->orWhere(function($sub) use ($userName, $userFullName) {
                          $sub->where('status', 'pending_manager')
                              ->where(function($m) use ($userName, $userFullName) {
                                  $m->where('manager_name', 'like', "%{$userName}%")
                                    ->orWhere('manager_name', 'like', "%{$userFullName}%");
                              });
                      })
                      ->orWhere(function($sub) use ($userName, $userFullName) {
                          $sub->where('status', 'pending_vp')
                              ->where(function($v) use ($userName, $userFullName) {
                                  $v->where('vp_name', 'like', "%{$userName}%")
                                    ->orWhere('vp_name', 'like', "%{$userFullName}%");
                              });
                      });

                    if (!empty($userDeptName)) {
                        $q->orWhere('department', 'like', "%{$userDeptName}%");
                    }

                    if ($authUser->level_user == '9') {
                        $q->orWhere('status', 'pending_ceo');
                    }
                });
            }

            $mpRequests = $mpQuery->orderBy('updated_at', 'desc')->get();
            foreach ($mpRequests as $mp) {
                $notifications->push([
                    'id' => 'manpower_' . $mp->id,
                    'category' => 'manpower',
                    'category_label' => 'ใบขออัตรากำลังคน',
                    'title' => 'ใบขออนุมัติกำลังคน (' . ($mp->request_code ?? 'QF-HR-13') . ')',
                    'description' => 'ตำแหน่งที่ขอ: ' . ($mp->position_title ?? 'ไม่ระบุ') . ' • แผนก: ' . ($mp->department_name ?? ($mp->department ?? 'ไม่ระบุ')),
                    'extra_info' => 'สถานะปัจจุบัน: ' . ($mp->status_label ?? $mp->status),
                    'url' => route('manpower-request.index'),
                    'icon' => 'fa-users-gear',
                    'color' => 'text-amber-500',
                    'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                    'border' => 'border-amber-200 dark:border-amber-800/40',
                    'badge_bg' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                    'badge_text' => 'กำลังคน',
                    'created_at' => $mp->updated_at ?? $mp->created_at,
                    'time_ago' => $mp->updated_at ? $mp->updated_at->diffForHumans() : '',
                ]);
            }

            // 2.1 Approved Manpower Requests waiting for Job Post creation / คำขอเปิดรับสมัครพนักงาน (แจ้งเตือน Admin, Editor และ HA)
            $isAdminOrEditor = ($authUser->role === 'admin' || (method_exists($authUser, 'isEditor') && $authUser->isEditor()) || (method_exists($authUser, 'isAdmin') && $authUser->isAdmin()));
            $canManageRecruitment = ($isCentralHr || $isAdminOrEditor || (method_exists($authUser, 'isHrOrAdmin') && $authUser->isHrOrAdmin()));

            if ($canManageRecruitment) {
                $approvedWithoutJobPost = ManpowerRequest::where('status', 'approved')
                    ->get()
                    ->filter(function ($m) {
                        return !$m->hasJobPost();
                    });

                foreach ($approvedWithoutJobPost as $mreq) {
                    $notifications->push([
                        'id' => 'manpower_need_jobpost_' . $mreq->id,
                        'category' => 'recruitment',
                        'category_label' => 'คำขอเปิดรับสมัครพนักงาน',
                        'title' => 'คำขอเปิดรับสมัคร: ' . ($mreq->job_title_th ?: 'ตำแหน่งงาน'),
                        'description' => "ใบขออนุมัติกำลังคนอนุมัติแล้ว แผนก {$mreq->department} ({$mreq->headcount} อัตรา) • รอสร้าง Job Post",
                        'extra_info' => 'สถานะ: ผ่านการอนุมัติแล้ว รอเปิดประกาศรับสมัคร',
                        'url' => route('backend.recruitment.requests.index'),
                        'icon' => 'fa-bullhorn',
                        'color' => 'text-amber-500',
                        'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                        'border' => 'border-amber-300 dark:border-amber-800/60',
                        'badge_bg' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                        'badge_text' => 'รอสร้าง Job Post',
                        'created_at' => $mreq->updated_at ?? $mreq->created_at,
                        'time_ago' => $mreq->updated_at ? $mreq->updated_at->diffForHumans() : '',
                    ]);
                }
            }
        }

        // 3. HR Requests (คำร้องทั่วไป)
        if (Schema::hasTable('hr_requests')) {
            $isCentralHr = ($authUser->dept_id == 15);

            if ($isCentralHr) {
                // ฝ่าย HR ส่วนกลาง เห็นคำร้องที่รอ HR ตรวจสอบ หรือคำร้องทั้งหมดที่รอดำเนินการ
                $hrReqs = HrRequests::whereIn('status', ['pending', 'approved_hr', 'approved_manager'])
                    ->orderBy('updated_at', 'desc')
                    ->get();
            } else {
                // แผนกอื่นๆ: กรองเฉพาะคำร้องที่ตนเองเป็นผู้อนุมัติ (ผู้จัดการ) หรือเป็นคำร้องของตนเองเท่านั้น
                $hrReqs = HrRequests::where(function($q) use ($authUser) {
                    // 1. ตนเองเป็นผู้อนุมัติ (ผู้จัดการ)
                    $q->where('approver_manager_id', $authUser->id)
                      ->where('status', 'pending');
                })->orWhere(function($q) use ($authUser) {
                    // 2. ตนเองเป็นผู้ยื่นคำร้อง
                    $q->where('employee_id', $authUser->id)
                      ->whereIn('status', ['pending', 'returned', 'approved_manager', 'approved_hr']);
                })->orderBy('updated_at', 'desc')->get();
            }

            foreach ($hrReqs as $hrReq) {
                $notifications->push([
                    'id' => 'hr_req_' . $hrReq->id,
                    'category' => 'hr',
                    'category_label' => 'คำร้องงานบุคคล (HR Request)',
                    'title' => 'คำร้อง HR (' . ($hrReq->request_code ?? 'HR-REQ') . ')',
                    'description' => ($hrReq->title ?? 'รายละเอียดคำร้อง') . ' • โดย: ' . ($hrReq->user->firstname ?? 'พนักงาน'),
                    'extra_info' => 'สถานะ: ' . ($hrReq->status_label ?? $hrReq->status),
                    'url' => route('request.hr'),
                    'icon' => 'fa-file-signature',
                    'color' => 'text-red-500',
                    'bg' => 'bg-red-50 dark:bg-red-950/40',
                    'border' => 'border-red-200 dark:border-red-800/40',
                    'badge_bg' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                    'badge_text' => 'คำร้อง HR',
                    'created_at' => $hrReq->updated_at ?? $hrReq->created_at,
                    'time_ago' => $hrReq->updated_at ? $hrReq->updated_at->diffForHumans() : '',
                ]);
            }
        }

        // Sort by created_at / updated_at descending
        $sortedNotifications = $notifications->sortByDesc(function ($item) {
            return $item['created_at'] ?? now();
        });

        // Counts for category tabs
        $counts = [
            'all' => $sortedNotifications->count(),
            'recruitment' => $sortedNotifications->where('category', 'recruitment')->count(),
            'manpower' => $sortedNotifications->where('category', 'manpower')->count(),
            'hr' => $sortedNotifications->where('category', 'hr')->count(),
        ];

        // Filter by category
        if ($filterType !== 'all') {
            $sortedNotifications = $sortedNotifications->where('category', $filterType);
        }

        // Filter by search
        if (!empty($search)) {
            $sortedNotifications = $sortedNotifications->filter(function ($item) use ($search) {
                $searchLower = mb_strtolower($search);
                return str_contains(mb_strtolower($item['title']), $searchLower)
                    || str_contains(mb_strtolower($item['description']), $searchLower)
                    || str_contains(mb_strtolower($item['extra_info']), $searchLower);
            });
        }

        // Pagination (แบ่งหน้า เช่น หน้าละ 8 รายการ)
        $perPage = 8;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedNotifications->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedNotifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $sortedNotifications->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('notifications.index', [
            'notifications' => $paginatedNotifications,
            'counts' => $counts,
            'filterType' => $filterType,
            'search' => $search,
        ]);
    }
}
