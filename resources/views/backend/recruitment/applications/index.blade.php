@extends('layouts.recruitment.app')

@section('title', 'จัดการรายชื่อผู้สมัคร')

@section('content')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .recruitment-table-font, .recruitment-table-font * {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }

        /* Hide default DataTables controls since custom toolbar exists */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }

        /* ── DataTables modern full-width layout ────────────────── */
        table.dataTable,
        #dataTableApps {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
        }

        /* Bottom info & pagination bar (centered pagination, full width expand) */
        .dataTables_bottom_bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-top: 1rem;
            padding: 0.25rem 0.25rem 0;
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .dataTables_bottom_bar {
                display: grid;
                grid-template-columns: 1fr auto 1fr;
                align-items: center;
                gap: 1rem;
            }
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 0.8125rem;
            color: #64748b;
            padding: 0 !important;
            margin: 0 !important;
            text-align: left;
            white-space: nowrap;
        }
        .dark .dataTables_wrapper .dataTables_info {
            color: #94a3b8;
        }

        /* Center pagination controls */
        .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 0 !important;
            margin: 0 auto !important;
            float: none !important;
            text-align: center !important;
        }

        /* Modern pagination buttons */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 0.6rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            color: #475569 !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            margin: 0 1px !important;
            user-select: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #F2704E !important;
            color: #ffffff !important;
            border-color: #F2704E !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 4px rgba(242, 112, 78, 0.25) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            color: #cbd5e1 !important;
            background: transparent !important;
            border-color: transparent !important;
            cursor: not-allowed !important;
            opacity: 0.4 !important;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #1f2937 !important;
            border-color: #374151 !important;
            color: #94a3b8 !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #374151 !important;
            color: #f8fafc !important;
            border-color: #4b5563 !important;
        }

        /* ── Section card ─────────────────────── */
        .section-card {
            background: #fff;
            color: #1e293b;
            border-radius: 1.25rem;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.04), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }
        .dark .section-card { background: #1f2937; color: #f1f5f9; border-color: #374151; }

        /* ── DataTable layout and spacing ─────────────────── */
        table.dataTable { 
            border-collapse: separate !important; 
            border-spacing: 0 !important;
            width: 100% !important;
            table-layout: auto;
        }

        table.dataTable thead th {
            background-color: #f8fafc;
            font-size: 0.775rem;
            font-weight: 700;
            letter-spacing: 0.025em;
            color: #475569 !important;
            padding: 1.1rem 1.5rem !important;
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle;
            white-space: nowrap;
        }
        table.dataTable thead th:first-child {
            padding-left: 2rem !important;
        }
        table.dataTable thead th:last-child {
            padding-right: 2rem !important;
        }
        .dark table.dataTable thead th {
            background-color: #1e293b;
            color: #94a3b8 !important;
            border-bottom-color: #334155 !important;
        }

        table.dataTable tbody td { 
            padding: 1.15rem 1.5rem !important; 
            font-size: 0.845rem; 
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            line-height: 1.45;
            color: #1e293b;
        }
        table.dataTable tbody td:first-child {
            padding-left: 2rem !important;
        }
        table.dataTable tbody td:last-child {
            padding-right: 2rem !important;
        }
        .dark table.dataTable tbody td {
            color: #f1f5f9;
            border-bottom-color: #374151;
        }

        table.dataTable tbody tr {
            transition: background-color 0.15s ease;
        }
        table.dataTable tbody tr:hover { 
            background-color: #f8fafc !important; 
        }
        .dark table.dataTable tbody tr:hover { 
            background-color: #1f2937 !important; 
        }
    </style>

    <div class="recruitment-table-font max-w-[1700px] w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 space-y-6 pb-16">
        
        <!-- Top Action Bar & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold dark:text-white text-slate-900 tracking-tight flex items-center gap-2.5">
                    <svg class="w-6 h-6 text-slate-700 dark:text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>รายชื่อผู้สมัครงาน</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Applicant Tracking · จัดการรายชื่อและติดตามกระบวนการคัดเลือกผู้สมัคร</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('backend.recruitment.posts.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold shadow-2xs transition-all">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>จัดการประกาศรับสมัคร</span>
                </a>
            </div>
        </div>

        <!-- Quick Summary Stats (Interactive KPI Cards for Fast Filtering) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <!-- All -->
            <div onclick="filterByQuickCard('')" class="kpi-card bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs hover:shadow-md hover:border-slate-400 transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">ผู้สมัครทั้งหมด</span>
                    <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs font-bold group-hover:bg-slate-800 group-hover:text-white dark:group-hover:bg-slate-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-2">
                    {{ $applications->count() }}
                </div>
            </div>

            <!-- Open Positions -->
            @php
                $openPostsCount = isset($openJobPosts) ? $openJobPosts->count() : 0;
            @endphp
            <div onclick="filterByOpenOnly(true)" class="kpi-card bg-white dark:bg-slate-800 p-4 rounded-2xl border border-emerald-200/80 dark:border-emerald-900/40 shadow-2xs hover:shadow-md hover:border-emerald-500 transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">ตำแหน่งเปิดรับสมัครอยู่</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2 flex items-center gap-2">
                    <span>{{ $openPostsCount }}</span>
                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">ตำแหน่ง</span>
                </div>
            </div>

            <!-- Pending Review -->
            @php
                $pendingCount = $applications->filter(fn($a) => in_array($a->status, ['submitted', 'dept_review']))->count();
            @endphp
            <div onclick="filterByQuickCard('dept_review')" class="kpi-card bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs hover:shadow-md hover:border-blue-300 transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">รอคัดกรอง / พิจารณา</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-2">
                    {{ $pendingCount }}
                </div>
            </div>

            <!-- Passed / Hired -->
            @php
                $hiredCount = $applications->filter(fn($a) => in_array($a->status, ['passed_selection', 'offered', 'hired']))->count();
            @endphp
            <div onclick="filterByQuickCard('hired')" class="kpi-card bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs hover:shadow-md hover:border-emerald-300 transition-all cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">รับเข้าทำงาน (บรรจุ)</span>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-bold group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-2">
                    {{ $hiredCount }}
                </div>
            </div>
        </div>

        {{-- SECTION CARD : APPLICANT TRACKING TABLE --}}
        <div class="section-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>ตารางผู้สมัครงานทั้งหมด</span>
                            <span id="appsCountBadge" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">({{ $applications->count() }} รายการ)</span>
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-slate-400">ระบบติดตามและค้นหาข้อมูลผู้สมัครอย่างละเอียดเรียงตามลำดับล่าสุด</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6 space-y-4">

                <!-- 🟢 Dynamic List of Open Recruitment Positions (ตำแหน่งที่ยังเปิดรับสมัครอยู่) -->
                @if(isset($openJobPosts) && $openJobPosts->count() > 0)
                    <div class="p-3.5 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-xl border border-emerald-200/70 dark:border-emerald-800/40">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300">ตำแหน่งที่ยังเปิดรับสมัครอยู่ขณะนี้ ({{ $openJobPosts->count() }} ตำแหน่ง):</span>
                            </div>
                            <span class="text-[11px] text-emerald-700 dark:text-emerald-400 font-medium">คลิกที่ตำแหน่งเพื่อกรองผู้สมัครทันที</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="filterByJobPost('')" class="open-pos-pill px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-slate-400 transition-all cursor-pointer shadow-2xs">
                                ทั้งหมด ({{ $applications->count() }})
                            </button>
                            @foreach($openJobPosts as $openPost)
                                @php
                                    $appCountForPost = $applications->filter(fn($a) => $a->job_post_id == $openPost->id)->count();
                                    $startDateStr = $openPost->start_date ? $openPost->start_date->format('d/m/Y') : null;
                                    $endDateStr = $openPost->end_date ? $openPost->end_date->format('d/m/Y') : null;
                                    $periodTooltip = 'ระยะเวลาตั้งรับสมัคร: ' . ($startDateStr ? $startDateStr : 'ไม่ระบุ') . ' ถึง ' . ($endDateStr ? $endDateStr : 'เปิดรับต่อเนื่อง');
                                @endphp
                                <button type="button" onclick="filterByJobPost({{ $openPost->id }}, '{{ addslashes($openPost->title) }}')" class="open-pos-pill group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-slate-800 border border-emerald-300 dark:border-emerald-700/60 text-slate-800 dark:text-slate-200 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white transition-all cursor-pointer shadow-2xs" title="{{ $periodTooltip }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 group-hover:bg-white shrink-0"></span>
                                    <span>{{ $openPost->title }}</span>
                                    @if($openPost->vacancy)
                                        <span class="text-[10px] text-slate-400 group-hover:text-emerald-100">({{ $openPost->vacancy }} อัตรา)</span>
                                    @endif
                                    <span class="ml-1 px-1.5 py-0.2 rounded-md bg-emerald-100 dark:bg-emerald-900/60 group-hover:bg-white/20 text-emerald-800 dark:text-emerald-200 group-hover:text-white font-extrabold text-[10px]">
                                        {{ $appCountForPost }} คน
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Filters Bar -->
                <div class="flex flex-wrap items-center gap-3 bg-slate-50/70 dark:bg-slate-900/40 p-3.5 rounded-xl border border-slate-200/60 dark:border-slate-700/50">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="customSearch" class="bg-white border border-slate-200 text-slate-900 text-xs rounded-xl focus:ring-slate-500 focus:border-slate-500 block w-full pl-9 pr-3 py-2.5 dark:bg-slate-800 dark:border-slate-700 dark:text-white placeholder-slate-400 shadow-2xs" placeholder="ค้นหาชื่อ, อีเมล หรือตำแหน่ง...">
                    </div>

                    <!-- Job Post Filter (List ตำแหน่งเปิดรับสมัคร / ทั้งหมด) -->
                    <div class="relative w-full sm:w-auto">
                        <select id="filterJobPost" class="bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-slate-500 focus:border-slate-500 block py-2.5 pl-3 pr-8 dark:bg-slate-800 dark:border-slate-700 dark:text-white w-full cursor-pointer shadow-2xs">
                            <option value="">ทุกตำแหน่งงาน</option>
                            @if(isset($openJobPosts) && $openJobPosts->count() > 0)
                                <optgroup label="🟢 ตำแหน่งที่ยังเปิดรับสมัครอยู่">
                                    @foreach($openJobPosts as $post)
                                        <option value="{{ $post->id }}">
                                            🟢 {{ $post->title }} ({{ $post->department?->department_name ?: 'ไม่ระบุแผนก' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if(isset($allJobPosts) && $allJobPosts->where('publish_status', '!=', 'published')->count() > 0)
                                <optgroup label="⚪ ตำแหน่งอื่นๆ / ปิดรับสมัครแล้ว">
                                    @foreach($allJobPosts->where('publish_status', '!=', 'published') as $post)
                                        <option value="{{ $post->id }}">
                                            ⚪ {{ $post->title }} [{{ $post->publish_status === 'closed' ? 'ปิดรับแล้ว' : 'ร่าง' }}]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                    
                    <!-- Department Select -->
                    <div class="relative w-full sm:w-auto">
                        <select id="filterDepartment" class="bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-slate-500 focus:border-slate-500 block py-2.5 pl-3 pr-8 dark:bg-slate-800 dark:border-slate-700 dark:text-white w-full cursor-pointer shadow-2xs">
                            <option value="">ทุกฝ่าย / แผนก</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->department_fullname }}">{{ $dept->department_fullname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Select -->
                    <div class="relative w-full sm:w-auto">
                        <select id="filterStatus" class="bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-slate-500 focus:border-slate-500 block py-2.5 pl-3 pr-8 dark:bg-slate-800 dark:border-slate-700 dark:text-white w-full cursor-pointer shadow-2xs">
                            <option value="">ทุกสถานะกระบวนการ</option>
                            <option value="submitted">1. รอคัดกรองเบื้องต้น</option>
                            <option value="dept_review">2. ส่งแผนกพิจารณา</option>
                            <option value="interview_scheduled">3. นัดสัมภาษณ์</option>
                            <option value="interview_completed">4. สัมภาษณ์เสร็จสิ้น</option>
                            <option value="passed_selection">5. ผ่านการคัดเลือก</option>
                            <option value="offered">6. ยื่นข้อเสนอ</option>
                            <option value="hired">7. รับเข้าทำงาน (บรรจุ)</option>
                            <option value="screening_failed">ไม่ผ่านคุณสมบัติ</option>
                            <option value="dept_rejected">หัวหน้าแผนกส่งกลับ</option>
                            <option value="interview_failed">ไม่ผ่านสัมภาษณ์</option>
                        </select>
                    </div>

                    <!-- Open Only Toggle Checkbox -->
                    <div class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-xl shadow-2xs cursor-pointer" onclick="toggleOpenCheckbox()">
                        <input type="checkbox" id="filterOpenOnly" class="w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <label for="filterOpenOnly" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer select-none whitespace-nowrap">
                            เฉพาะตำแหน่งเปิดรับ
                        </label>
                    </div>

                    <!-- Items Per Page -->
                    <div class="relative w-full sm:w-auto">
                        <select id="filterPageSize" class="bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-slate-500 focus:border-slate-500 block py-2.5 pl-3 pr-8 dark:bg-slate-800 dark:border-slate-700 dark:text-white w-full cursor-pointer shadow-2xs" title="จำนวนรายการต่อหน้า">
                            <option value="5">แสดง 5 รายการ</option>
                            <option value="10" selected>แสดง 10 รายการ</option>
                            <option value="25">แสดง 25 รายการ</option>
                            <option value="50">แสดง 50 รายการ</option>
                            <option value="-1">แสดงทั้งหมด</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <button type="button" id="btnResetFilters" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer shadow-2xs ml-auto" title="ล้างค่าตัวกรองทั้งหมด">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>ล้างตัวกรอง</span>
                    </button>
                </div>

                <!-- Table -->
                <table id="dataTableApps" class="display responsive nowrap w-full text-xs sm:text-sm">
                    <thead class="bg-slate-50/80 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[11px] font-semibold border-y border-slate-200/80 dark:border-slate-700/60">
                        <tr>
                            <th class="all whitespace-nowrap text-center py-3">ประเภท</th>
                            <th class="all whitespace-nowrap text-left py-3">ID</th>
                            <th class="all whitespace-nowrap text-left py-3">ผู้สมัคร</th>
                            <th class="all whitespace-nowrap text-left py-3">ตำแหน่งที่สมัคร</th>
                            <th class="whitespace-nowrap text-left py-3">ฝ่าย / แผนก</th>
                            <th class="whitespace-nowrap text-center py-3">วันที่สมัคร</th>
                            <th class="all whitespace-nowrap text-center py-3">สถานะ</th>
                            <th class="text-center all whitespace-nowrap py-3" style="width: 70px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($applications as $app)
                            @php
                                $deptName = $app->jobPost?->department?->department_fullname ?: ($app->jobPost?->department?->department_name ?: '-');
                                $posName = $app->jobPost?->position_name ?: ($app->jobPost?->jobPosition?->position_name ?: ($app->jobPost?->title ?? '-'));
                                $applicantName = ($app->applicant->first_name ?? '') . ' ' . ($app->applicant->last_name ?? '');
                                $isPostOpen = ($app->jobPost?->publish_status === 'published');
                                $appliedDateFormatted = $app->applied_at ? $app->applied_at->format('Y-m-d') : ($app->created_at ? $app->created_at->format('Y-m-d') : '');
                            @endphp
                            <tr data-status="{{ $app->status }}" data-department="{{ $deptName }}" data-job-post-id="{{ $app->job_post_id }}" data-post-open="{{ $isPostOpen ? '1' : '0' }}" data-applied-date="{{ $appliedDateFormatted }}" class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- ประเภท -->
                                <td class="text-center whitespace-nowrap py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>ใบสมัครงาน</span>
                                    </span>
                                </td>

                                <!-- ID -->
                                <td data-order="{{ $app->id }}" class="text-left font-bold whitespace-nowrap py-3.5">
                                    <a href="{{ route('backend.recruitment.applications.show', $app->id) }}" class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-xs bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-blue-600 border border-slate-200 dark:border-slate-700" title="ดูรายละเอียดผู้สมัคร">
                                        #APP-{{ str_pad($app->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>

                                <!-- ผู้สมัคร -->
                                <td class="text-left whitespace-nowrap py-3.5">
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

                                <!-- ตำแหน่งที่สมัคร -->
                                <td class="text-left whitespace-nowrap py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>{{ $posName }}</span>
                                        @if($isPostOpen)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800" title="ตำแหน่งนี้กำลังเปิดรับสมัครอยู่">
                                                เปิดรับสมัคร
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- ฝ่าย / แผนก -->
                                <td class="text-left text-slate-600 dark:text-slate-300 whitespace-nowrap py-3.5">
                                    <div class="inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0v-5a2 2 0 012-2h2a2 2 0 012 2v5m-4 0h4"/>
                                        </svg>
                                        <span>{{ $deptName }}</span>
                                    </div>
                                </td>

                                <!-- วันที่สมัคร -->
                                <td data-order="{{ $appliedDateFormatted }}" class="text-center whitespace-nowrap text-slate-600 dark:text-slate-400 py-3.5">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-mono text-xs">{{ $app->applied_at ? $app->applied_at->addYears(543)->format('d/m/Y') : ($app->created_at ? $app->created_at->addYears(543)->format('d/m/Y') : '-') }}</span>
                                    </div>
                                </td>

                                <!-- สถานะ -->
                                <td class="text-center whitespace-nowrap py-3.5">
                                    @if(in_array($app->status, ['hired', 'passed_selection']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/40">
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span>{{ $app->status_label }}</span>
                                        </span>
                                    @elseif(in_array($app->status, ['interview', 'interview_scheduled', 'interview_completed']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-50 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300 border border-purple-200 dark:border-purple-900/40">
                                            <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                                            <span>{{ $app->status_label }}</span>
                                        </span>
                                    @elseif($app->status === 'dept_review')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-900/40">
                                            <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                            <span>{{ $app->status_label }}</span>
                                        </span>
                                    @elseif(in_array($app->status, ['dept_rejected', 'interview_failed', 'screening_failed']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-900/40">
                                            <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                            <span>{{ $app->status_label }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                            <span>{{ $app->status_label }}</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- จัดการ -->
                                <td class="text-center whitespace-nowrap py-3.5">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Quick Card Filter Trigger
        window.filterByQuickCard = function(statusVal) {
            $('#filterStatus').val(statusVal).trigger('change');
        };

        // Job Post Filter Helper
        window.filterByJobPost = function(jobPostId) {
            $('#filterJobPost').val(jobPostId).trigger('change');
        };

        // Open Only Checkbox Toggle Helper
        window.toggleOpenCheckbox = function() {
            const chk = $('#filterOpenOnly');
            chk.prop('checked', !chk.prop('checked')).trigger('change');
        };

        window.filterByOpenOnly = function(isOpen) {
            $('#filterOpenOnly').prop('checked', isOpen).trigger('change');
        };

        // Global Dropdown Handler
        window.toggleActionDropdown = function(btn, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const menu = btn.nextElementSibling;
            if (!menu) return;

            const isCurrentlyHidden = menu.classList.contains('hidden');

            // Close all open action menus first
            document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                m.classList.add('hidden');
            });

            if (isCurrentlyHidden) {
                menu.classList.remove('hidden');

                const rect = btn.getBoundingClientRect();
                const menuWidth = 176;
                const menuHeight = menu.offsetHeight || 90;

                // Horizontal positioning (align right edge with button)
                let left = rect.right - menuWidth;
                if (left < 10) left = 10;
                menu.style.left = left + 'px';

                // Vertical positioning (flip up if not enough space below)
                const spaceBelow = window.innerHeight - rect.bottom;
                if (spaceBelow < menuHeight + 10 && rect.top > menuHeight + 10) {
                    menu.style.top = 'auto';
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                } else {
                    menu.style.bottom = 'auto';
                    menu.style.top = (rect.bottom + 4) + 'px';
                }
            }
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown-menu') && !e.target.closest('.action-dropdown-btn')) {
                document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                    m.classList.add('hidden');
                });
            }
        });

        // Close dropdown when scrolling any container or window
        window.addEventListener('scroll', function() {
            document.querySelectorAll('.action-dropdown-menu:not(.hidden)').forEach(function(m) {
                m.classList.add('hidden');
            });
        }, true);

        // Close when clicking any link inside dropdown
        document.addEventListener('click', function(e) {
            if (e.target.closest('.action-dropdown-menu a')) {
                document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                    m.classList.add('hidden');
                });
            }
        });
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = null;
            const dtOptions = {
                dom: '<"overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-2xs"t><"dataTables_bottom_bar flex items-center justify-between pt-4"ip>',
                autoWidth: false,
                responsive: false,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "ทั้งหมด"]],
                order: [[1, 'desc']],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
                    paginate: {
                        first: "«",
                        previous: "‹",
                        next: "›",
                        last: "»"
                    }
                }
            };

            try {
                if (typeof window.DataTable === 'function') {
                    table = new window.DataTable('#dataTableApps', dtOptions);
                } else if (window.$ && typeof window.$.fn.DataTable === 'function') {
                    table = $('#dataTableApps').DataTable(dtOptions);
                }
            } catch (err) {
                console.warn('DataTable init notice:', err);
            }

            if (table) {
                // Custom search box
                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                // Page size dropdown change
                $('#filterPageSize').on('change', function() {
                    const len = parseInt(this.value, 10);
                    table.page.len(len).draw();
                });

                // Filter logic
                function applyFilters() {
                    const deptVal = $('#filterDepartment').val();
                    const statusVal = $('#filterStatus').val();
                    const jobPostIdVal = $('#filterJobPost').val();
                    const isOpenOnlyVal = $('#filterOpenOnly').is(':checked');

                    const filterFn = function(settings, data, dataIndex) {
                        const row = $(table.row(dataIndex).node());
                        const rowDept = row.attr('data-department') || '';
                        const rowStatus = row.attr('data-status') || '';
                        const rowJobPostId = row.attr('data-job-post-id') || '';
                        const rowPostOpen = row.attr('data-post-open') || '0';

                        if (deptVal && !rowDept.includes(deptVal)) return false;
                        if (statusVal && rowStatus !== statusVal) return false;
                        if (jobPostIdVal && rowJobPostId !== jobPostIdVal) return false;
                        if (isOpenOnlyVal && rowPostOpen !== '1') return false;

                        return true;
                    };

                    if (window.DataTable && window.DataTable.ext) {
                        window.DataTable.ext.search = [filterFn];
                    } else if (window.$.fn && window.$.fn.dataTable) {
                        window.$.fn.dataTable.ext.search = [filterFn];
                    }

                    table.draw();
                    updateCount();
                }

                $('#filterDepartment, #filterStatus, #filterJobPost, #filterOpenOnly').on('change', applyFilters);

                // Reset filters
                $('#btnResetFilters').on('click', function() {
                    $('#customSearch').val('');
                    $('#filterDepartment').val('');
                    $('#filterStatus').val('');
                    $('#filterJobPost').val('');
                    $('#filterOpenOnly').prop('checked', false);
                    $('#filterPageSize').val('10');
                    table.page.len(10);
                    table.search('');
                    if (window.DataTable && window.DataTable.ext) {
                        window.DataTable.ext.search = [];
                    } else if (window.$.fn && window.$.fn.dataTable) {
                        window.$.fn.dataTable.ext.search = [];
                    }
                    table.draw();
                    updateCount();
                });

                function updateCount() {
                    const visibleRows = table.rows({ filter: 'applied' }).count();
                    $('#appsCountBadge').text(`(${visibleRows} รายการ)`);
                }

                table.on('draw', function() {
                    document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                        m.classList.add('hidden');
                    });
                    updateCount();
                });
            }

            // Toggle Application History Accordion (Slide Down / Up)
            $('#dataTableApps tbody').on('click', '.btn-toggle-history', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const btn = $(this);
                const tr = btn.closest('tr');
                const chevron = btn.find('.chevron-icon');

                if (table && typeof table.row === 'function') {
                    const row = table.row(tr);
                    if (row.child.isShown()) {
                        // Slide Up and close
                        $('div.history-slider', row.child()).slideUp(200, function() {
                            row.child.hide();
                            tr.removeClass('shown bg-rose-50/20 dark:bg-rose-950/10');
                            chevron.removeClass('rotate-180');
                        });
                    } else {
                        // Close any other open rows for clean accordion UX
                        $('#dataTableApps tbody tr.shown').each(function() {
                            const otherTr = $(this);
                            const otherRow = table.row(otherTr);
                            const otherChevron = otherTr.find('.chevron-icon');
                            $('div.history-slider', otherRow.child()).slideUp(150, function() {
                                otherRow.child.hide();
                                otherTr.removeClass('shown bg-rose-50/20 dark:bg-rose-950/10');
                                otherChevron.removeClass('rotate-180');
                            });
                        });

                        const templateHtml = tr.find('.app-history-template').html();
                        if (templateHtml) {
                            const childContent = $('<div class="history-slider" style="display:none;">' + templateHtml + '</div>');
                            row.child(childContent, 'p-0 bg-transparent border-0').show();
                            tr.addClass('shown bg-rose-50/20 dark:bg-rose-950/10');
                            chevron.addClass('rotate-180');
                            childContent.slideDown(250);
                        }
                    }
                } else {
                    // Fallback without DataTables API
                    let childRow = tr.next('tr.history-row-fallback');
                    if (childRow.length) {
                        childRow.find('.history-slider').slideToggle(200, function() {
                            if (!$(this).is(':visible')) {
                                childRow.remove();
                                tr.removeClass('shown bg-rose-50/20 dark:bg-rose-950/10');
                                chevron.removeClass('rotate-180');
                            }
                        });
                    } else {
                        const templateHtml = tr.find('.app-history-template').html();
                        if (templateHtml) {
                            const newRow = $('<tr class="history-row-fallback"><td colspan="8" class="p-0 border-0 bg-transparent"><div class="history-slider" style="display:none;">' + templateHtml + '</div></td></tr>');
                            tr.after(newRow);
                            tr.addClass('shown bg-rose-50/20 dark:bg-rose-950/10');
                            chevron.addClass('rotate-180');
                            newRow.find('.history-slider').slideDown(250);
                        }
                    }
                }
            });
        });
    </script>
@endpush