@extends('layouts.notifications')

@section('title', 'การแจ้งเตือนทั้งหมด')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">

    {{-- กรอบครอบเนื้อหาทั้งหมด (Outer Container Frame) ตามกรอบสีแดงในภาพ --}}
    <div class="bg-white dark:bg-[#1E2129] rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm p-6 sm:p-8 space-y-6">

        {{-- 1. ส่วนหัว (Header) --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center text-white shadow-xl shadow-red-600/30 shrink-0">
                    <i class="fa-solid fa-bell text-xl"></i>
                </div>
                <div>
                    <h1 class="page-title text-slate-900 dark:text-white font-extrabold text-xl sm:text-2xl flex items-center gap-2.5">
                        <span>การแจ้งเตือนทั้งหมด</span>
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-black text-white bg-red-600 rounded-full shadow-sm">
                            {{ $counts['all'] }}
                        </span>
                    </h1>
                    <p class="page-subtitle mt-0.5 text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                        ติดตามความคืบหน้าของใบสมัครงาน คำขออัตรากำลังคน และรายการคำร้องทั้งหมดในระบบ
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-center">
                <a href="javascript:location.reload();" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl hover:bg-slate-50 dark:hover:bg-gray-700/60 transition shadow-sm active:scale-95">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                    <span>รีเฟรช</span>
                </a>
            </div>
        </div>

        {{-- 2. แถบตัวกรองและช่องค้นหา (Filter Bar & Search) --}}
        <div class="bg-slate-50/70 dark:bg-gray-800/50 p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            {{-- Category Tabs --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
                {{-- ทั้งหมด --}}
                <a href="{{ route('notifications.index', ['type' => 'all', 'search' => request('search')]) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'all' ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-gray-700' }}">
                    <i class="fa-solid fa-list-check text-xs"></i>
                    <span>ทั้งหมด</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filterType === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                        {{ $counts['all'] }}
                    </span>
                </a>

                {{-- งานสรรหาบุคลากร --}}
                <a href="{{ route('notifications.index', ['type' => 'recruitment', 'search' => request('search')]) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'recruitment' ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-gray-700' }}">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>งานสรรหาบุคลากร</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filterType === 'recruitment' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                        {{ $counts['recruitment'] }}
                    </span>
                </a>

                {{-- ขออัตรากำลังคน --}}
                <a href="{{ route('notifications.index', ['type' => 'manpower', 'search' => request('search')]) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'manpower' ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-gray-700' }}">
                    <i class="fa-solid fa-users-gear text-xs"></i>
                    <span>ขออัตรากำลังคน</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filterType === 'manpower' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                        {{ $counts['manpower'] }}
                    </span>
                </a>

                {{-- คำร้อง HR --}}
                <a href="{{ route('notifications.index', ['type' => 'hr', 'search' => request('search')]) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $filterType === 'hr' ? 'bg-red-600 text-white shadow-md shadow-red-600/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-gray-700' }}">
                    <i class="fa-solid fa-file-signature text-xs"></i>
                    <span>คำร้อง HR</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filterType === 'hr' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-gray-700 text-slate-600 dark:text-slate-300' }}">
                        {{ $counts['hr'] }}
                    </span>
                </a>
            </div>

            {{-- ช่องค้นหา (Search) --}}
            <form method="GET" action="{{ route('notifications.index') }}" class="relative min-w-[240px]">
                <input type="hidden" name="type" value="{{ $filterType }}">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="ค้นหาการแจ้งเตือน..." 
                       class="w-full pl-9 pr-8 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 text-slate-700 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition">
                @if($search)
                    <a href="{{ route('notifications.index', ['type' => $filterType]) }}" 
                       class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- 3. รายการการ์ดแจ้งเตือน (Notifications Cards) --}}
        <div class="space-y-4">
            @forelse($notifications as $item)
                <div class="bg-white dark:bg-[#181B22] p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-gray-700/80 shadow-xs hover:shadow-md transition-all duration-200 group flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    
                    {{-- ด้านซ้าย: ไอคอนและเนื้อหา --}}
                    <div class="flex items-start gap-4 flex-1 min-w-0">
                        {{-- กล่องไอคอนมนมน --}}
                        <div class="w-12 h-12 rounded-2xl {{ $item['bg'] }} {{ $item['color'] }} border {{ $item['border'] }} flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform mt-0.5">
                            <i class="fa-solid {{ $item['icon'] }}"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            {{-- แถว Badge และประเภท --}}
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold {{ $item['badge_bg'] }}">
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

                            {{-- หัวข้อการแจ้งเตือน --}}
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-red-600 transition-colors">
                                {{ $item['title'] }}
                            </h3>

                            {{-- รายละเอียดคำร้อง --}}
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $item['description'] }}
                            </p>

                            {{-- แถบสถานะพร้อมจุดสีแดง --}}
                            @if(!empty($item['extra_info']))
                                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                                    <span>{{ $item['extra_info'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ด้านขวา: ปุ่มเปิดดูรายการ --}}
                    <div class="self-end sm:self-center shrink-0 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-gray-800 flex justify-end">
                        <a href="{{ $item['url'] }}" 
                           class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 text-xs font-bold rounded-xl bg-slate-100/90 hover:bg-slate-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-slate-700 dark:text-slate-200 transition-all duration-150 group/btn active:scale-95">
                            <span>เปิดดูรายการ</span>
                            <i class="fa-solid fa-arrow-right text-[10px] group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                </div>
            @empty
                <div class="bg-white dark:bg-[#181B22] py-16 px-4 rounded-2xl border border-dashed border-slate-200 dark:border-gray-700 text-center">
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

        {{-- 4. การแบ่งหน้า (Pagination) --}}
        @if($notifications->hasPages())
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 dark:border-gray-700">
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

</div>
@endsection
