@auth
@php
    $authUser = auth()->user();
    $notifPendingCount = 0;
    $notifItems = collect();

    if ($authUser) {
        try {
            $userName = trim(($authUser->firstname ?? '') . ' ' . ($authUser->lastname ?? ''));
            $userFullName = trim($authUser->fullname ?? $userName);

            $userDeptId = $authUser->dept_id;
            $userId = $authUser->id;
            $isCentralHr = ($userDeptId == 15);
            $isAdminOrEditor = ($authUser->role === 'admin' || (method_exists($authUser, 'isEditor') && $authUser->isEditor()) || (method_exists($authUser, 'isAdmin') && $authUser->isAdmin()));
            $canManageRecruitment = ($isCentralHr || $isAdminOrEditor || (method_exists($authUser, 'isHrOrAdmin') && $authUser->isHrOrAdmin()));
            $userDeptName = $authUser->department->department_name ?? ($authUser->department->department_fullname ?? '');

            // 1. Manpower Requests
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('manpower_requests')) {
                    $mpQuery = \App\Models\ManpowerRequest::whereNotIn('status', ['approved', 'rejected', 'draft']);
                    if (!$isCentralHr) {
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
                    $mpRequests = $mpQuery->latest('updated_at')->take(5)->get();
                    foreach ($mpRequests as $mp) {
                        $notifItems->push([
                            'title' => 'ใบขออนุมัติกำลังคน (' . ($mp->request_code ?? 'QF-HR-13') . ')',
                            'subtitle' => 'ตำแหน่ง: ' . ($mp->position_title ?? 'ไม่ระบุ') . ' • สถานะ: ' . ($mp->status_label ?? $mp->status),
                            'url' => route('manpower-request.index'),
                            'icon' => 'fa-users-gear',
                            'color' => 'text-amber-500',
                            'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                            'time' => $mp->updated_at ? $mp->updated_at->diffForHumans() : '',
                        ]);
                    }

                    // Approved Manpower Requests needing Job Post / คำขอเปิดรับสมัครพนักงาน (แจ้งเตือน Admin, Editor และ HA)
                    if ($canManageRecruitment) {
                        $approvedWithoutPost = \App\Models\ManpowerRequest::where('status', 'approved')->get()->filter(function($m) {
                            return !$m->hasJobPost();
                        })->take(5);

                        foreach ($approvedWithoutPost as $mreq) {
                            $notifItems->push([
                                'title' => 'คำขอเปิดรับสมัคร: ' . ($mreq->job_title_th ?: 'ตำแหน่งงาน'),
                                'subtitle' => 'อนุมัติแล้ว (' . ($mreq->department ?? '-') . ') • รอสร้าง Job Post',
                                'url' => route('backend.recruitment.requests.index'),
                                'icon' => 'fa-bullhorn',
                                'color' => 'text-amber-500',
                                'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                                'time' => $mreq->updated_at ? $mreq->updated_at->diffForHumans() : '',
                            ]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Notification bell manpower error: ' . $e->getMessage());
            }

            // 2. HR Requests (HrRequests)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('hr_requests')) {
                    if ($isCentralHr) {
                        $hrReqs = \App\Models\hrrequest\HrRequests::whereIn('status', ['pending', 'approved_hr', 'approved_manager'])->latest('updated_at')->take(5)->get();
                    } else {
                        $hrReqs = \App\Models\hrrequest\HrRequests::where(function($q) use ($authUser) {
                            // ผู้จัดการที่ต้องเป็นผู้อนุมัติคำร้อง
                            $q->where('approver_manager_id', $authUser->id)
                              ->where('status', 'pending');
                        })->orWhere(function($q) use ($authUser) {
                            // หรือ เป็นคำร้องของตนเองเท่านั้น
                            $q->where('employee_id', $authUser->id)
                              ->whereIn('status', ['pending', 'returned', 'approved_manager', 'approved_hr']);
                        })->latest('updated_at')->take(5)->get();
                    }

                    foreach ($hrReqs as $hrReq) {
                        $notifItems->push([
                            'title' => 'คำร้อง HR (' . ($hrReq->request_code ?? 'HR-REQ') . ')',
                            'subtitle' => ($hrReq->title ?? 'รายละเอียดคำร้อง') . ' • ' . ($hrReq->status_label ?? $hrReq->status),
                            'url' => route('request.hr'),
                            'icon' => 'fa-file-signature',
                            'color' => 'text-red-500',
                            'bg' => 'bg-red-50 dark:bg-red-950/40',
                            'time' => $hrReq->updated_at ? $hrReq->updated_at->diffForHumans() : '',
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Notification bell hr_requests error: ' . $e->getMessage());
            }

            // 3. Recruitment Applications (ผู้สมัครงานส่งใบสมัครเข้ามาใหม่ & ส่งแผนกพิจารณา)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('recruitment_applications')) {
                    // A. ใบสมัครเข้ามาใหม่: แจ้งเตือนเฉพาะ HA / Admin เท่านั้น
                    if ($canManageRecruitment) {
                        $newApplications = \App\Models\Recruitment\Application::with(['applicant', 'jobPost'])
                            ->whereIn('status', ['new', 'submitted'])
                            ->latest('applied_at')
                            ->take(5)
                            ->get();

                        foreach ($newApplications as $app) {
                            $applicantName = $app->applicant 
                                ? trim(($app->applicant->prefix ?? '') . ' ' . $app->applicant->first_name . ' ' . $app->applicant->last_name)
                                : 'ผู้สมัคร';
                            $jobTitle = $app->jobPost->title ?? ($app->jobPost->position_name ?? 'ตำแหน่งงาน');

                            $notifItems->push([
                                'title' => 'ใบสมัครงานใหม่: ' . $applicantName,
                                'subtitle' => 'ตำแหน่ง: ' . $jobTitle . ' • ' . ($app->status_label ?? 'รอคัดกรองเบื้องต้น'),
                                'url' => route('backend.recruitment.applications.show', ['application' => $app->id]),
                                'icon' => 'fa-user-plus',
                                'color' => 'text-emerald-500',
                                'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
                                'time' => $app->applied_at ? $app->applied_at->diffForHumans() : ($app->created_at ? $app->created_at->diffForHumans() : ''),
                            ]);
                        }
                    }

                    // B. ผู้สมัครที่ผ่านคุณสมบัติแล้ว ซึ่ง HA ส่งต่อให้แผนกพิจารณา (Dept Review)
                    $deptReviewQuery = \App\Models\Recruitment\Application::with(['applicant', 'jobPost'])
                        ->where('status', 'dept_review');

                    if (!$isCentralHr) {
                        if ($authUser->isDepartmentManager()) {
                            $managedDeptIds = $authUser->getManagedDepartmentIds();
                            $deptReviewQuery->whereHas('jobPost', function($jq) use ($managedDeptIds) {
                                $jq->whereIn('department_id', $managedDeptIds);
                            });
                        } else {
                            $deptReviewQuery->whereRaw('1 = 0');
                        }
                    }

                    $orderCol = \Illuminate\Support\Facades\Schema::hasColumn('recruitment_applications', 'dept_reviewed_at') ? 'dept_reviewed_at' : 'updated_at';
                    $deptReviewApps = $deptReviewQuery->latest($orderCol)->take(5)->get();

                    foreach ($deptReviewApps as $app) {
                        $applicantName = $app->applicant 
                            ? trim(($app->applicant->prefix ?? '') . ' ' . $app->applicant->first_name . ' ' . $app->applicant->last_name)
                            : 'ผู้สมัคร';
                        $jobTitle = $app->jobPost->title ?? ($app->jobPost->position_name ?? 'ตำแหน่งงาน');

                        $notifItems->push([
                            'title' => 'HA ส่งผู้สมัครให้แผนกพิจารณา: ' . $applicantName,
                            'subtitle' => 'ตำแหน่ง: ' . $jobTitle . ' • ผ่านเกณฑ์คุณสมบัติจาก HA แล้ว',
                            'url' => route('recruitment.reports'),
                            'icon' => 'fa-user-check',
                            'color' => 'text-blue-500',
                            'bg' => 'bg-blue-50 dark:bg-blue-950/40',
                            'time' => $app->dept_reviewed_at ? $app->dept_reviewed_at->diffForHumans() : ($app->updated_at ? $app->updated_at->diffForHumans() : ''),
                        ]);
                    }

                    // C. ผู้สมัครที่มีการนัดหมายสัมภาษณ์ (Interview Scheduled)
                    if (\Illuminate\Support\Facades\Schema::hasTable('recruitment_interviews')) {
                        $scheduledInterviewsQuery = \App\Models\Recruitment\Interview::with([
                            'application.applicant',
                            'application.jobPost.department',
                            'application.jobPost.recruitmentRequest'
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

                        $scheduledInterviews = $scheduledInterviewsQuery->orderBy('interview_date', 'asc')->take(5)->get();

                        foreach ($scheduledInterviews as $iv) {
                            $applicant = $iv->application?->applicant;
                            $applicantName = $applicant ? trim(($applicant->prefix ?? '') . ' ' . $applicant->first_name . ' ' . $applicant->last_name) : 'ผู้สมัคร';
                            $jobTitle = $iv->application?->jobPost?->title ?? ($iv->application?->jobPost?->position_name ?? 'ตำแหน่งงาน');
                            $dateFormatted = \Carbon\Carbon::parse($iv->interview_date)->locale('th')->isoFormat('D MMM YYYY');
                            $timeFormatted = \Carbon\Carbon::parse($iv->interview_time)->format('H:i') . ' น.';
                            $itemUrl = $canManageRecruitment 
                                ? route('backend.recruitment.applications.show', ['application' => $iv->application_id])
                                : route('recruitment.reports');
                            $isRescheduled = ($iv->updated_at && $iv->created_at && $iv->updated_at->diffInSeconds($iv->created_at) > 60);

                            $notifItems->push([
                                'title' => ($isRescheduled ? '🔄 ปรับเวลานัดสัมภาษณ์: ' : 'นัดสัมภาษณ์: ') . $applicantName,
                                'subtitle' => "รอบที่ {$iv->interview_round} วันที่ {$dateFormatted} เวลา {$timeFormatted}" . ($iv->meeting_link ? ' (Online)' : '') . ($isRescheduled ? ' (เวลาใหม่)' : ''),
                                'url' => $itemUrl,
                                'icon' => $isRescheduled ? 'fa-clock-rotate-left' : 'fa-calendar-check',
                                'color' => $isRescheduled ? 'text-amber-500' : 'text-purple-500',
                                'bg' => $isRescheduled ? 'bg-amber-50 dark:bg-amber-950/40' : 'bg-purple-50 dark:bg-purple-950/40',
                                'time' => ($iv->updated_at ?? $iv->created_at) ? ($iv->updated_at ?? $iv->created_at)->diffForHumans() : '',
                            ]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Notification bell recruitment error: ' . $e->getMessage());
            }

            $notifPendingCount = $notifItems->count();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Notification bell overall error: ' . $e->getMessage());
        }
    }
@endphp

<div class="relative notif-bell-wrapper inline-block">
    <!-- Notification Bell Button (Vuexy Navbar Style) -->
    <button type="button" 
            onclick="toggleNotificationMenu(event, this)"
            class="relative w-9 h-9 rounded-lg text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 flex items-center justify-center transition-all duration-150 cursor-pointer shrink-0 focus:outline-none"
            title="การแจ้งเตือน">
        <i class="fa-regular fa-bell text-lg"></i>
        
        @if($notifPendingCount > 0)
            <span class="absolute -top-1 -right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold shadow-sm ring-2 ring-white dark:ring-[#1E2129]">
                {{ $notifPendingCount > 99 ? '99+' : $notifPendingCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown Menu (Smooth Animation) -->
    <div class="notif-dropdown-menu absolute right-[-4.5rem] sm:right-0 top-full mt-3 w-80 max-w-[calc(100vw-2rem)] rounded-2xl bg-white dark:bg-[#1E2129] shadow-2xl border border-slate-200/80 dark:border-slate-700/85 py-1.5 z-50 overflow-hidden text-left transition-all duration-250 ease-out opacity-0 scale-95 pointer-events-none transform origin-top-right">
        
        <!-- Header -->
        <div class="px-3.5 py-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bell text-red-600 dark:text-red-500 text-sm"></i>
                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider">การแจ้งเตือนคำขอ</span>
            </div>
            <div class="flex items-center gap-2">
                @if($notifPendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 text-[11px] font-bold">
                        {{ $notifPendingCount }} คำขอใหม่
                    </span>
                @endif
                <button type="button" 
                        onclick="closeNotificationMenu(this)"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- List Items -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
            @forelse($notifItems->take(6) as $item)
                <a href="{{ $item['url'] }}" class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <div class="w-9 h-9 rounded-xl {{ $item['bg'] }} {{ $item['color'] }} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform mt-0.5">
                        <i class="fa-solid {{ $item['icon'] }} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors truncate">
                            {{ $item['title'] }}
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                            {{ $item['subtitle'] }}
                        </p>
                        @if(!empty($item['time']))
                            <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500 mt-1 block">
                                {{ $item['time'] }}
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                    <i class="fa-regular fa-bell-slash text-2xl mb-2 opacity-50 block"></i>
                    <span class="text-xs">ไม่มีรายการคำขอรอดำเนินการในขณะนี้</span>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-3.5 py-2.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 flex items-center justify-between gap-2">
            @if($canManageRecruitment)
                <a href="{{ route('backend.recruitment.requests.index') }}" class="text-[11px] font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition">
                    คำขอเปิดรับสมัคร
                </a>
            @else
                <a href="{{ route('manpower-request.index') }}" class="text-[11px] font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition">
                    แบบฟอร์มกำลังคน
                </a>
            @endif
            <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 inline-flex items-center gap-1.5 transition">
                <span>ดูการแจ้งเตือนทั้งหมด</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>
</div>

@once
<script>
    function toggleNotificationMenu(e, btn) {
        if (e) e.stopPropagation();
        const wrapper = btn.closest('.notif-bell-wrapper');
        if (!wrapper) return;
        const menu = wrapper.querySelector('.notif-dropdown-menu');
        if (!menu) return;

        const isOpen = menu.classList.contains('opacity-100');

        // Close all notification dropdowns
        document.querySelectorAll('.notif-dropdown-menu').forEach(m => {
            m.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            m.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        });

        if (!isOpen) {
            menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            menu.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
            
            // Adjust position so it doesn't overflow left or right of viewport
            const rect = menu.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const margin = 12; // 12px padding from screen edge

            if (rect.left < margin) {
                const shiftX = margin - rect.left;
                menu.style.transform = `translateX(${shiftX}px)`;
            } else if (rect.right > (viewportWidth - margin)) {
                const shiftX = (viewportWidth - margin) - rect.right;
                menu.style.transform = `translateX(${shiftX}px)`;
            } else {
                menu.style.transform = '';
            }
        }
    }

    function closeNotificationMenu(btn) {
        const wrapper = btn.closest('.notif-bell-wrapper');
        if (!wrapper) return;
        const menu = wrapper.querySelector('.notif-dropdown-menu');
        if (menu) {
            menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            menu.style.transform = '';
        }
    }

    document.addEventListener('click', function(e) {
        document.querySelectorAll('.notif-dropdown-menu').forEach(menu => {
            if (!menu.contains(e.target) && !e.target.closest('.notif-bell-wrapper')) {
                menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                menu.style.transform = '';
            }
        });
    });
</script>
@endonce
@endauth
