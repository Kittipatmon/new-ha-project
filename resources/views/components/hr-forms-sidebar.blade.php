<div class="w-full lg:w-64 xl:w-72 shrink-0 space-y-4 print:hidden">
    <!-- Sidebar Card Container -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-5 shadow-lg border border-gray-200/80 dark:border-gray-700/80 sticky top-20">
        
        <!-- Header -->
        <div class="flex items-center justify-between gap-2 p-2.5 sm:p-3 rounded-xl bg-red-50/70 dark:bg-red-950/20 border border-red-100/80 dark:border-red-900/30 mb-4">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-red-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                    <i class="fa-solid fa-folder-tree"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate">หมวดหมู่แบบฟอร์ม</h3>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium truncate">HR Forms & Services</p>
                </div>
            </div>
            <span class="text-[11px] font-bold text-red-600 dark:text-red-400 bg-white dark:bg-gray-800 px-2.5 py-1 rounded-md shadow-xs border border-red-100 dark:border-red-900/50 whitespace-nowrap shrink-0">3 รายการ</span>
        </div>

        <!-- Form Category Items List -->
        <div class="space-y-2 sm:space-y-2.5">
            <!-- 1. ใบขออนุมัติกำลังคน -->
            <a href="{{ route('manpower-request.create') }}" 
               class="group flex items-center justify-between p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('manpower-request.create') ? 'bg-red-500 text-white shadow-md shadow-red-500/25' : 'hover:bg-red-50 dark:hover:bg-red-950/30 text-gray-700 dark:text-gray-200' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0 transition-colors {{ request()->routeIs('manpower-request.create') ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 group-hover:scale-105' }}">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div class="truncate">
                        <div class="text-xs sm:text-sm font-bold leading-snug truncate">ใบขออนุมัติกำลังคน</div>
                        <div class="text-[11px] opacity-80 font-mono">QF-HR-13</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs shrink-0 transition-transform group-hover:translate-x-1 opacity-70 ml-2"></i>
            </a>

            <!-- 2. แบบประเมินทดลองงาน -->
            <a href="{{ route('probation-evaluation.create') }}" 
               class="group flex items-center justify-between p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('probation-evaluation.create') ? 'bg-red-500 text-white shadow-md shadow-red-500/25' : 'hover:bg-red-50 dark:hover:bg-red-950/30 text-gray-700 dark:text-gray-200' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0 transition-colors {{ request()->routeIs('probation-evaluation.create') ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 group-hover:scale-105' }}">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div class="truncate">
                        <div class="text-xs sm:text-sm font-bold leading-snug truncate">แบบประเมินทดลองงาน</div>
                        <div class="text-[11px] opacity-80 font-mono">QF-HR-18</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs shrink-0 transition-transform group-hover:translate-x-1 opacity-70 ml-2"></i>
            </a>

            <!-- 3. แบบประเมินผลสัมภาษณ์ -->
            <a href="{{ route('interview-evaluation.create') }}" 
               class="group flex items-center justify-between p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('interview-evaluation.create') ? 'bg-red-500 text-white shadow-md shadow-red-500/25' : 'hover:bg-red-50 dark:hover:bg-red-950/30 text-gray-700 dark:text-gray-200' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0 transition-colors {{ request()->routeIs('interview-evaluation.create') ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 group-hover:scale-105' }}">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <div class="truncate">
                        <div class="text-xs sm:text-sm font-bold leading-snug truncate">แบบประเมินผลสัมภาษณ์</div>
                        <div class="text-[11px] opacity-80 font-mono">QF-HR-25</div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs shrink-0 transition-transform group-hover:translate-x-1 opacity-70 ml-2"></i>
            </a>
        </div>

        <!-- Track Status Link Footer -->
        <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 mt-3">
            <a href="{{ route('manpower-request.index') }}" 
               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors {{ request()->routeIs('manpower-request.index') ? 'bg-red-50 dark:bg-red-950/50' : '' }}">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-list-check"></i>
                    <span>ติดตามสถานะแบบฟอร์ม</span>
                </span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
        </div>
    </div>
</div>
