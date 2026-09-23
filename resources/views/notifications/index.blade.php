@extends('layouts.notifications')

@section('title', 'การแจ้งเตือนทั้งหมด')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-16">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center text-white shadow-xl shadow-red-600/30 shrink-0">
                <i class="fa-solid fa-bell text-xl"></i>
            </div>
            <div>
                <h1 class="page-title text-slate-900 dark:text-white font-extrabold flex items-center gap-2.5">
                    <span>การแจ้งเตือนทั้งหมด</span>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-black text-white bg-red-600 rounded-full shadow-sm">
                        {{ $counts['all'] }}
                    </span>
                </h1>
                <p class="page-subtitle mt-0.5 text-slate-500 dark:text-slate-400">
                    ติดตามความคืบหน้าของใบสมัครงาน คำขออัตรากำลังคน และรายการคำร้องทั้งหมดในระบบ
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="javascript:location.reload();" 
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl hover:bg-slate-50 dark:hover:bg-gray-700/60 transition shadow-sm">
                <i class="fa-solid fa-rotate-right"></i>
                <span>รีเฟรช</span>
            </a>
        </div>
    </div>

    {{-- Filter Bar & Search --}}
    <div class="bg-white dark:bg-[#1E2129] p-4 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        {{-- Category Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
            <a href="{{ route('notifications.index', ['type' => 'all', 'search' => request('search')]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'all' ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-list-check text-xs"></i>
                <span>ทั้งหมด</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filterType === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                    {{ $counts['all'] }}
                </span>
            </a>

            <a href="{{ route('notifications.index', ['type' => 'recruitment', 'search' => request('search')]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'recruitment' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>งานสรรหาบุคลากร</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filterType === 'recruitment' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                    {{ $counts['recruitment'] }}
                </span>
            </a>

            <a href="{{ route('notifications.index', ['type' => 'manpower', 'search' => request('search')]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'manpower' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-users-gear text-xs"></i>
                <span>ขออัตรากำลังคน</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filterType === 'manpower' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                    {{ $counts['manpower'] }}
                </span>
            </a>

            <a href="{{ route('notifications.index', ['type' => 'hr', 'search' => request('search')]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'hr' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-file-signature text-xs"></i>
                <span>คำร้อง HR</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filterType === 'hr' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                    {{ $counts['hr'] }}
                </span>
            </a>
        </div>

        {{-- Search Input --}}
        <form method="GET" action="{{ route('notifications.index') }}" class="relative min-w-[240px]">
            <input type="hidden" name="type" value="{{ $filterType }}">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" 
                   name="search" 
                   value="{{ $search }}"
                   placeholder="ค้นหาการแจ้งเตือน..." 
                   class="w-full pl-9 pr-8 py-2 text-xs rounded-xl bg-slate-50 dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-700 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition">
            @if($search)
                <a href="{{ route('notifications.index', ['type' => $filterType]) }}" 
                   class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Notifications List --}}
    <div class="space-y-3">
        @forelse($notifications as $item)
            <div class="bg-white dark:bg-[#1E2129] p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 hover:border-red-300 dark:hover:border-red-900/50 shadow-sm hover:shadow-md transition-all duration-200 group flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                
                {{-- Left Side: Icon & Content --}}
                <div class="flex items-start gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 rounded-2xl {{ $item['bg'] }} {{ $item['color'] }} border {{ $item['border'] }} flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform mt-0.5">
                        <i class="fa-solid {{ $item['icon'] }}"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $item['badge_bg'] }}">
                                {{ $item['badge_text'] }}
                            </span>
                            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                                {{ $item['category_label'] }}
                            </span>
                            @if(!empty($item['time_ago']))
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                    <span>•</span>
                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                    <span>{{ $item['time_ago'] }}</span>
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                            {{ $item['title'] }}
                        </h3>

                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            {{ $item['description'] }}
                        </p>

                        @if(!empty($item['extra_info']))
                            <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span>{{ $item['extra_info'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Side: Action Button --}}
                <div class="self-end sm:self-center shrink-0 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-gray-800 flex justify-end">
                    <a href="{{ $item['url'] }}" 
                       class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 text-xs font-bold rounded-xl bg-slate-100 hover:bg-red-600 text-slate-700 hover:text-white dark:bg-gray-800 dark:text-slate-200 dark:hover:bg-red-600 transition-all duration-150 group/btn">
                        <span>เปิดดูรายการ</span>
                        <i class="fa-solid fa-arrow-right text-[10px] group-hover/btn:translate-x-1 transition-transform"></i>
                    </a>
                </div>

            </div>
        @empty
            <div class="bg-white dark:bg-[#1E2129] py-16 px-4 rounded-3xl border border-dashed border-slate-200 dark:border-gray-700 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-gray-800 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-regular fa-bell-slash"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700 dark:text-slate-200">
                    ไม่พบข้อมูลการแจ้งเตือน
                </h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto">
                    @if($search)
                        ไม่พบการแจ้งเตือนที่ตรงกับคำค้นหา "{{ $search }}" ลองปรับคำค้นหาใหม่
                    @else
                        ขณะนี้ยังไม่มีรายการคำขอหรือใบสมัครใหม่ที่ต้องดำเนินการในหมวดหมู่นี้
                    @endif
                </p>
                @if($search || $filterType !== 'all')
                    <a href="{{ route('notifications.index') }}" 
                       class="inline-flex items-center gap-1.5 mt-4 text-xs font-bold text-red-600 dark:text-red-400 hover:underline">
                        <span>ดูการแจ้งเตือนทั้งหมด</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination Bar --}}
    @if($notifications->hasPages())
        <div class="bg-white dark:bg-[#1E2129] px-6 py-4 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                แสดงลำดับที่ <span class="font-bold text-slate-800 dark:text-slate-100">{{ $notifications->firstItem() ?? 0 }}</span>
                ถึง <span class="font-bold text-slate-800 dark:text-slate-100">{{ $notifications->lastItem() ?? 0 }}</span>
                จากทั้งหมด <span class="font-bold text-slate-800 dark:text-slate-100">{{ $notifications->total() }}</span> รายการ
            </div>
            <div>
                {{ $notifications->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
