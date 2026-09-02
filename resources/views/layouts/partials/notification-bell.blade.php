@auth
@php
    $authUser = auth()->user();
    $notifPendingCount = 0;
    $notifItems = collect();

    if ($authUser) {
        $userName = trim(($authUser->firstname ?? '') . ' ' . ($authUser->lastname ?? ''));
        $userFullName = trim($authUser->fullname ?? $userName);

        // 1. Manpower Requests
        if (\Illuminate\Support\Facades\Schema::hasTable('manpower_requests')) {
            $mpQuery = \App\Models\ManpowerRequest::whereNotIn('status', ['approved', 'rejected', 'draft']);
            if ($authUser->role !== 'admin') {
                $mpQuery->where(function($q) use ($authUser, $userName, $userFullName) {
                    $q->where(function($sub) use ($userName, $userFullName) {
                        $sub->where('status', 'pending_manager')
                            ->where(function($m) use ($userName, $userFullName) {
                                $m->where('manager_name', 'like', "%{$userName}%")
                                  ->orWhere('manager_name', 'like', "%{$userFullName}%")
                                  ->orWhereNull('manager_name')
                                  ->orWhere('manager_name', '');
                            });
                    })
                    ->orWhere(function($sub) use ($userName, $userFullName) {
                        $sub->where('status', 'pending_vp')
                            ->where(function($v) use ($userName, $userFullName) {
                                $v->where('vp_name', 'like', "%{$userName}%")
                                  ->orWhere('vp_name', 'like', "%{$userFullName}%")
                                  ->orWhereNull('vp_name')
                                  ->orWhere('vp_name', '');
                            });
                    });
                    if ($authUser->isHrOrAdmin()) {
                        $q->orWhere('status', 'pending_hr');
                    }
                    if ($authUser->level_user == '9') {
                        $q->orWhere('status', 'pending_ceo');
                    }
                    $q->orWhere('user_id', $authUser->id);
                });
            }
            $mpRequests = $mpQuery->latest()->take(5)->get();
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
        }

        // 2. HR Requests (HrRequests)
        if (\Illuminate\Support\Facades\Schema::hasTable('hr_requests')) {
            if ($authUser->isHrOrAdmin()) {
                $hrReqs = \App\Models\hrrequest\HrRequests::whereIn('status', ['pending', 'approved_hr', 'approved_manager'])->latest()->take(5)->get();
            } else {
                $hrReqs = \App\Models\hrrequest\HrRequests::where(function($q) use ($authUser) {
                    $q->where('approver_manager_id', $authUser->id)->where('approver_manager_status', '0');
                })->orWhere(function($q) use ($authUser) {
                    $q->where('employee_id', $authUser->id)->whereIn('status', ['pending', 'returned']);
                })->latest()->take(5)->get();
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

        $notifPendingCount = $notifItems->count();
    }
@endphp

<div class="relative notif-bell-wrapper inline-block">
    <!-- Notification Bell Button -->
    <button type="button" 
            onclick="toggleNotificationMenu(event, this)"
            class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white shadow-md flex items-center justify-center transition-all duration-200 active:scale-95 cursor-pointer shrink-0 focus:outline-none">
        <i class="fa-solid fa-bell text-sm sm:text-base"></i>
        
        @if($notifPendingCount > 0)
            <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-red-600 text-[10px] font-black shadow-md ring-2 ring-red-600 animate-pulse">
                {{ $notifPendingCount > 99 ? '99+' : $notifPendingCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown Menu (Smooth Animation) -->
    <div class="notif-dropdown-menu absolute right-0 top-full mt-5 w-72 sm:w-96 max-w-[calc(100vw-1.5rem)] rounded-2xl bg-white dark:bg-[#1E2129] shadow-2xl border border-slate-200/80 dark:border-slate-700/85 py-2 z-50 overflow-hidden text-left transition-all duration-250 ease-out opacity-0 scale-95 pointer-events-none transform origin-top-right">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
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
                <a href="{{ $item['url'] }}" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
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
        <div class="px-3 py-2 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40 text-center">
            <a href="{{ route('manpower-request.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 inline-flex items-center gap-1">
                <span>ติดตามสถานะแบบฟอร์มทั้งหมด</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
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
        }
    }

    function closeNotificationMenu(btn) {
        const wrapper = btn.closest('.notif-bell-wrapper');
        if (!wrapper) return;
        const menu = wrapper.querySelector('.notif-dropdown-menu');
        if (menu) {
            menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        }
    }

    document.addEventListener('click', function(e) {
        document.querySelectorAll('.notif-dropdown-menu').forEach(menu => {
            if (!menu.contains(e.target) && !e.target.closest('.notif-bell-wrapper')) {
                menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            }
        });
    });
</script>
@endonce
@endauth
