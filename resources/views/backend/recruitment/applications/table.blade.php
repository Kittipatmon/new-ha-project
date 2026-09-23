<div id="applications-table-container">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/80 dark:bg-slate-900/60 border-b border-slate-200/80 dark:border-slate-700/60">
                    <th class="px-5 py-3.5 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider">ผู้สมัคร</th>
                    <th class="px-5 py-3.5 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider">ตำแหน่งที่สมัคร</th>
                    <th class="px-5 py-3.5 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider">แผนก</th>
                    <th class="px-5 py-3.5 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider whitespace-nowrap text-center">วันที่สมัคร</th>
                    <th class="px-5 py-3.5 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider text-center">สถานะ</th>
                    <th class="px-5 py-3.5 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider text-center" style="width: 70px;">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($applications as $app)
                    @php
                        $deptName = $app->jobPost?->department?->department_fullname ?: ($app->jobPost?->department?->department_name ?: '-');
                        $posName = $app->jobPost?->position_name ?: ($app->jobPost?->jobPosition?->position_name ?: ($app->jobPost?->title ?? '-'));
                        $applicantName = ($app->applicant->first_name ?? '') . ' ' . ($app->applicant->last_name ?? '');
                        $isPostOpen = ($app->jobPost?->publish_status === 'published');
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        {{-- Applicant Name --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-xs font-bold uppercase shrink-0 border border-slate-200 dark:border-slate-600">
                                    {{ mb_substr($app->applicant->first_name ?? '', 0, 1) }}{{ mb_substr($app->applicant->last_name ?? '', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <a href="{{ route('backend.recruitment.applications.show', $app->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="ดูรายละเอียดผู้สมัคร">
                                            {{ $applicantName }}
                                        </a>
                                        @if(($app->total_applications ?? 0) > 1)
                                            <button type="button" 
                                                class="btn-toggle-history inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 dark:text-rose-300 border border-rose-200 dark:border-rose-900/50 hover:border-rose-300 transition-all cursor-pointer shadow-2xs group active:scale-95"
                                                title="คลิกเพื่อดูรายละเอียดการสมัครรายครั้ง ({{ $app->total_applications }} ครั้ง)">
                                                <i class="fa-solid fa-clock-rotate-left text-[9px] text-rose-500 group-hover:rotate-[-45deg] transition-transform"></i>
                                                <span>สมัคร {{ $app->total_applications }} ครั้ง</span>
                                                <i class="fa-solid fa-chevron-down text-[8px] text-rose-400 transition-transform duration-200 chevron-icon"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $app->applicant->email ?? '-' }}</div>
                                </div>
                            </div>

                            @if(($app->total_applications ?? 0) > 1 && $app->applicant && $app->applicant->applications)
                                <!-- Template for Child Row Expansion -->
                                <div class="app-history-template hidden">
                                    <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-50/95 via-rose-50/25 to-slate-50/95 dark:from-slate-900 dark:via-slate-850 dark:to-slate-900 border-y-2 border-rose-300/80 dark:border-rose-800/60 shadow-inner">
                                        <div class="flex flex-wrap items-center justify-between gap-3 mb-3 pb-2.5 border-b border-rose-200/60 dark:border-rose-900/40">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm shadow-2xs">
                                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                                        ประวัติการสมัครงานทั้งหมดของ <span class="text-[#B21F24] font-black text-sm">{{ $applicantName }}</span>
                                                        <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200 border border-rose-200 dark:border-rose-800">
                                                            รวม {{ $app->total_applications }} ครั้ง
                                                        </span>
                                                    </h4>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                        <i class="fa-regular fa-envelope mr-1 text-slate-400"></i>{{ $app->applicant->email ?? '-' }} 
                                                        <span class="mx-1.5 text-slate-300 dark:text-slate-600">|</span> 
                                                        <i class="fa-solid fa-phone text-[10px] mr-1 text-slate-400"></i>{{ $app->applicant->phone ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1.5">
                                                <i class="fa-solid fa-arrow-down-wide-short text-rose-500"></i> 
                                                <span>เรียงตามลำดับการสมัครล่าสุด &rarr; อดีต</span>
                                            </div>
                                        </div>

                                        <div class="overflow-x-auto rounded-lg border border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800/95 shadow-xs">
                                            <table class="w-full text-xs text-left">
                                                <thead class="bg-slate-100/90 dark:bg-slate-900/90 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                                                    <tr>
                                                        <th class="px-4 py-2.5 text-center w-16">ครั้งที่</th>
                                                        <th class="px-4 py-2.5">รหัสใบสมัคร</th>
                                                        <th class="px-4 py-2.5">ตำแหน่งที่สมัคร</th>
                                                        <th class="px-4 py-2.5">ฝ่าย / แผนก</th>
                                                        <th class="px-4 py-2.5 text-center">วันที่และเวลาที่สมัคร</th>
                                                        <th class="px-4 py-2.5 text-center">สถานะ</th>
                                                        <th class="px-4 py-2.5 text-center w-28">จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                                    @foreach($app->applicant->applications->sortByDesc('created_at')->values() as $idx => $histApp)
                                                        @php
                                                            $histDeptName = $histApp->jobPost?->department?->department_fullname ?: ($histApp->jobPost?->department?->department_name ?: '-');
                                                            $histPosName = $histApp->jobPost?->position_name ?: ($histApp->jobPost?->jobPosition?->position_name ?: ($histApp->jobPost?->title ?? '-'));
                                                            $isCurrent = ($histApp->id === $app->id);
                                                            $attemptNum = $app->applicant->applications->count() - $idx;
                                                        @endphp
                                                        <tr class="{{ $isCurrent ? 'bg-rose-50/50 dark:bg-rose-950/20 font-medium' : 'hover:bg-slate-50 dark:hover:bg-slate-700/30' }} transition-colors">
                                                            <td class="px-4 py-2.5 text-center">
                                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $isCurrent ? 'bg-[#B21F24] text-white font-black shadow-xs' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold' }} text-[10px]">
                                                                    {{ $attemptNum }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-2.5 font-mono font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                                                #APP-{{ str_pad($histApp->id, 5, '0', STR_PAD_LEFT) }}
                                                                @if($isCurrent)
                                                                    <span class="ml-1.5 px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800">ปัจจุบัน</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-2.5 text-slate-900 dark:text-white font-semibold">
                                                                {{ $histPosName }}
                                                            </td>
                                                            <td class="px-4 py-2.5 text-slate-600 dark:text-slate-300">
                                                                {{ $histDeptName }}
                                                            </td>
                                                            <td class="px-4 py-2.5 text-center text-slate-600 dark:text-slate-400 font-mono whitespace-nowrap">
                                                                {{ $histApp->applied_at ? $histApp->applied_at->addYears(543)->format('d/m/Y H:i น.') : ($histApp->created_at ? $histApp->created_at->addYears(543)->format('d/m/Y H:i น.') : '-') }}
                                                            </td>
                                                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $histApp->status_badge_class }}">
                                                                    {{ $histApp->status_label }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                                                <a href="{{ route('backend.recruitment.applications.show', $histApp->id) }}" 
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-bold text-white bg-[#B21F24] hover:bg-red-700 transition-all shadow-2xs hover:shadow-xs active:scale-95"
                                                                    title="เปิดดูใบสมัครฉบับนี้">
                                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                                                    <span>เปิดดู</span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>

                        {{-- Position Applied --}}
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>{{ $posName }}</span>
                                @if($isPostOpen)
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800" title="ตำแหน่งนี้กำลังเปิดรับสมัครอยู่">
                                        เปิดรับสมัคร
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Department --}}
                        <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300">
                            <div class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0v-5a2 2 0 012-2h2a2 2 0 012 2v5m-4 0h4"/>
                                </svg>
                                <span>{{ $deptName }}</span>
                            </div>
                        </td>

                        {{-- Applied Date --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap text-slate-600 dark:text-slate-400">
                            <div class="inline-flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-mono text-xs">{{ $app->applied_at ? $app->applied_at->addYears(543)->format('d/m/Y') : ($app->created_at ? $app->created_at->addYears(543)->format('d/m/Y') : '-') }}</span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                <span>{{ $app->status_label }}</span>
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <div class="relative inline-block text-left">
                                <button type="button" class="action-dropdown-btn w-8 h-8 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100/80 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg transition-colors inline-flex items-center justify-center focus:outline-none cursor-pointer" title="จัดการ" onclick="toggleActionDropdown(this, event)">
                                    <svg class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/>
                                    </svg>
                                </button>

                                <div class="action-dropdown-menu hidden fixed z-[99999] w-48 rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 text-xs font-sans text-left">
                                    <a href="{{ route('backend.recruitment.applications.show', $app->id) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white transition-colors">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>ดูใบสมัครฉบับเต็ม</span>
                                    </a>
                                    <a href="{{ route('backend.recruitment.applications.show', $app->id) }}#status-timeline" class="flex items-center gap-2.5 px-3.5 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white transition-colors">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>ประวัติสถานะ (Timeline)</span>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-slate-600 dark:text-slate-300 font-semibold text-sm">ยังไม่มีรายชื่อผู้สมัคร</p>
                                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-0.5">ข้อมูลจะปรากฏเมื่อมีผู้สมัครเข้ามา</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800" id="pagination-links">
            {{ $applications->appends(request()->all())->links() }}
        </div>
    @endif
</div>