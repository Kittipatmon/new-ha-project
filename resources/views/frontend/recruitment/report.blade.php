@extends('layouts.recruitment.app')

@php
    $isHa = $isHa ?? (Auth::check() && Auth::user()->isHrOrAdmin());
@endphp

@section('title', ($activeTab === 'requests' && $isHa) ? 'รายงานคำขอเปิดรับสมัครพนักงาน' : 'ข้อมูลผู้สมัครที่ HA ส่งมา')

@section('content')
    {{-- DataTables CSS for Requests Tab --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .report-requests-font, .report-requests-font * {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
        .section-card {
            background: #fff;
            color: #1e293b;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.07);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }
        .dark .section-card { background: #1f2937; color: #f1f5f9; border-color: #374151; }

        .badge { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem; 
            border-radius: 9999px; 
            font-size: 0.725rem; 
            font-weight: 700; 
            letter-spacing: 0.02em; 
            white-space: nowrap;
        }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }

        .btn-view {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem;
            border-radius: 0.5rem;
            color: #6366f1;
            background: #eef2ff;
            transition: background 0.15s, color 0.15s;
            border: none;
            cursor: pointer;
        }
        .btn-view:hover { background: #6366f1; color: #fff; }

        .btn-doc {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem;
            border-radius: 0.625rem;
            color: #64748b;
            background: #f1f5f9;
            transition: all 0.2s ease;
        }
        .btn-doc:hover { background: #e2e8f0; color: #0f172a; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08); }

        /* ── DataTable Overrides (Same as Picture 2) ── */
        .dataTables_wrapper {
            width: 100%;
        }
        .dataTables_wrapper .dataTables_length {
            color: #475569 !important;
            font-size: 0.875rem;
            margin-bottom: 0.875rem !important;
            padding: 0.25rem 0.25rem !important;
        }
        .dataTables_wrapper .dataTables_length label {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            white-space: nowrap !important;
        }
        .dataTables_wrapper .dataTables_length select {
            display: inline-block !important;
            padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
            margin: 0 0.35rem !important;
            min-width: 5.25rem !important;
            width: auto !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem;
            background-color: #fff;
            color: #1e293b !important;
            line-height: 1.25rem !important;
        }
        .dark .dataTables_wrapper .dataTables_length select {
            background-color: #374151;
            border-color: #4b5563 !important;
            color: #f1f5f9 !important;
        }
        .dataTables_wrapper table.dataTable {
            margin-top: 0.75rem !important;
            margin-bottom: 0.875rem !important;
            border-top: 1px solid #f1f5f9 !important;
            border-collapse: separate !important; 
            border-spacing: 0 !important;
            width: 100% !important;
        }
        .dark .dataTables_wrapper table.dataTable {
            border-top-color: #374151 !important;
        }
        table.dataTable thead th {
            background-color: #f8fafc;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.025em;
            color: #475569 !important;
            padding: 0.7rem 0.6rem !important;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle;
            white-space: nowrap;
        }
        .dark table.dataTable thead th {
            background-color: #1e293b;
            color: #94a3b8 !important;
            border-bottom-color: #334155 !important;
        }
        table.dataTable tbody td { 
            padding: 0.7rem 0.6rem !important; 
            font-size: 0.8125rem; 
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            line-height: 1.35;
            color: #1e293b;
        }
        .dark table.dataTable tbody td {
            color: #f1f5f9;
            border-bottom-color: #374151;
        }
        table.dataTable tbody tr:hover { 
            background-color: #f8fafc !important; 
        }
        .dark table.dataTable tbody tr:hover { 
            background-color: #1f2937 !important; 
        }
        .dataTables_wrapper .dataTables_info {
            color: #475569 !important;
            font-size: 0.875rem;
            padding: 0.75rem 0.25rem 0.25rem !important;
        }
        .dark .dataTables_wrapper .dataTables_info {
            color: #94a3b8 !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding: 0.75rem 0.25rem 0.25rem !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem !important;
            padding: 0.25rem 0.75rem !important;
            margin: 0 0.125rem;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #cbd5e1 !important;
            border-color: #374151 !important;
            background: #1f2937 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #fff !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e0e7ff !important;
            border-color: #e0e7ff !important;
            color: #4f46e5 !important;
        }

        #dataTableReportRequests_wrapper .dataTables_length,
        #dataTableReportRequests_wrapper .dataTables_filter {
            display: none !important;
        }
        
        #dataTableReportRequests { 
            border-collapse: separate !important; 
            border-spacing: 0 !important;
            width: 100% !important;
            table-layout: auto;
        }

        #dataTableReportRequests thead th {
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
        #dataTableReportRequests thead th:first-child {
            padding-left: 2rem !important;
        }
        #dataTableReportRequests thead th:last-child {
            padding-right: 2rem !important;
        }
        .dark #dataTableReportRequests thead th {
            background-color: #1e293b;
            color: #94a3b8 !important;
            border-bottom-color: #334155 !important;
        }

        #dataTableReportRequests tbody td { 
            padding: 1.15rem 1.5rem !important; 
            font-size: 0.845rem; 
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            line-height: 1.45;
            color: #1e293b;
        }
        #dataTableReportRequests tbody td:first-child {
            padding-left: 2rem !important;
        }
        #dataTableReportRequests tbody td:last-child {
            padding-right: 2rem !important;
        }
        .dark #dataTableReportRequests tbody td {
            color: #f1f5f9;
            border-bottom-color: #374151;
        }

        #dataTableReportRequests tbody tr {
            transition: background-color 0.15s ease;
        }
        #dataTableReportRequests tbody tr:hover { 
            background-color: #f8fafc !important; 
        }
        .dark #dataTableReportRequests tbody tr:hover { 
            background-color: #1f2937 !important; 
        }

        #dataTableReportRequests_wrapper .dataTables_info {
            padding: 1.25rem 0.5rem 0.75rem 0.5rem !important;
            font-size: 0.825rem;
            color: #64748b;
        }
        #dataTableReportRequests_wrapper .dataTables_paginate {
            padding-top: 1rem !important;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.25rem;
        }
        #dataTableReportRequests_wrapper .dataTables_paginate .paginate_button {
            padding: 0.35rem 0.75rem !important;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            color: #475569 !important;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        #dataTableReportRequests_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        #dataTableReportRequests_wrapper .dataTables_paginate .paginate_button.current,
        #dataTableReportRequests_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #e11d48 !important;
            color: #fff !important;
            border-color: #e11d48 !important;
            box-shadow: 0 2px 6px rgba(225, 29, 72, 0.35);
        }
        #dataTableReportRequests_wrapper .dataTables_paginate .paginate_button.disabled,
        #dataTableReportRequests_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f8fafc !important;
            color: #94a3b8 !important;
            border-color: #f1f5f9 !important;
        }
    </style>

    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 pb-20">
        <!-- Hero/Header Section -->
        <div class="bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 shadow-sm">
            <div class="max-w-7xl xl:max-w-[1440px] 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/50 text-kumwell-red dark:text-red-400 flex items-center justify-center text-lg shadow-sm border border-red-100 dark:border-red-900/30">
                                @if($activeTab === 'requests' && $isHa)
                                    <i class="fa-solid fa-file-invoice"></i>
                                @else
                                    <i class="fa-solid fa-user-check"></i>
                                @endif
                            </span>
                            <div>
                                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ ($activeTab === 'requests' && $isHa) ? 'รายงานคำขอเปิดรับสมัครพนักงาน' : 'ข้อมูลผู้สมัครที่ HA ส่งมา' }}
                                </h1>
                                <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">
                                    {{ ($activeTab === 'requests' && $isHa) 
                                        ? 'ติดตามสถานะคำขออนุมัติกำลังคน (Manpower Requests Management)' 
                                        : 'รายชื่อผู้สมัครที่ผ่านการคัดกรองเบื้องต้นจาก HA สำหรับการพิจารณาของหัวหน้าแผนก / หน่วยงาน' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                        <!-- Navigation Tabs Switcher (แสดงเฉพาะ HA) -->
                        @if($isHa)
                            <div class="inline-flex p-1 bg-gray-100 dark:bg-slate-700/60 rounded-2xl border border-gray-200/60 dark:border-slate-600/60 text-xs font-semibold">
                                <a href="{{ route('recruitment.reports', array_merge(request()->except(['tab', 'candidate_page', 'request_page']), ['tab' => 'candidates'])) }}"
                                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all duration-200 {{ $activeTab !== 'requests' ? 'bg-white dark:bg-slate-800 text-kumwell-red dark:text-red-400 shadow-sm font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    <i class="fa-solid fa-user-check text-xs"></i>
                                    <span>ผู้สมัครที่ HA ส่งมา</span>
                                    @if(isset($candidateStats['dept_review']) && $candidateStats['dept_review'] > 0)
                                        <span class="px-1.5 py-0.2 bg-red-600 text-white rounded-full text-[10px] font-extrabold animate-pulse">
                                            {{ $candidateStats['dept_review'] }}
                                        </span>
                                    @endif
                                </a>
                                <a href="{{ route('recruitment.reports', array_merge(request()->except(['tab', 'candidate_page', 'request_page']), ['tab' => 'requests'])) }}"
                                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all duration-200 {{ $activeTab === 'requests' ? 'bg-white dark:bg-slate-800 text-kumwell-red dark:text-red-400 shadow-sm font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    <i class="fa-solid fa-file-invoice text-xs"></i>
                                    <span>รายงานคำขอเปิดรับสมัคร</span>
                                    <span class="px-1.5 py-0.2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-full text-[10px]">
                                        {{ $totalCount ?? 0 }}
                                    </span>
                                </a>
                            </div>

                            @if($activeTab === 'requests')
                                <a href="{{ route('manpower-request.create') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium text-xs sm:text-sm shadow-md shadow-red-500/20 hover:shadow-red-500/40 transition-all duration-200 shrink-0">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>กรอกใบขออัตรากำลัง</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl xl:max-w-[1440px] 2xl:max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

            <!-- Alerts / Flash Messages -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- ========================================================================= --}}
            {{-- TAB 1: ผู้สมัครที่ HA ส่งมา (Candidates Forwarded by HA)                    --}}
            {{-- ========================================================================= --}}
            @if($activeTab !== 'requests')

                <!-- Status Notification Banner for Department Review -->
                @if(($candidateStats['dept_review'] ?? 0) > 0)
                    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50 via-amber-50/80 to-orange-50 dark:from-amber-950/50 dark:via-amber-900/30 dark:to-orange-950/40 border-2 border-amber-300 dark:border-amber-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm shadow-amber-500/30 shrink-0">
                                <i class="fa-solid fa-bell animate-bounce"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-gray-900 dark:text-white text-sm sm:text-base">แจ้งเตือนสถานะ: มีผู้สมัครรอหัวหน้าพิจารณา {{ $candidateStats['dept_review'] }} คน</span>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-extrabold tracking-wider uppercase shadow-2xs animate-pulse">Action Required</span>
                                </div>
                                <p class="text-xs text-amber-900/80 dark:text-amber-200/80 mt-0.5">
                                    ฝ่ายบุคคล (HA) ได้ทำการคัดกรองเบื้องต้นและส่งต่อมาแล้ว กรุณาตรวจสอบประวัติเพื่อพิจารณาตอบรับ นัดสัมภาษณ์ หรือส่งกลับ
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('recruitment.reports', array_merge(request()->except(['status', 'candidate_page']), ['status' => 'dept_review'])) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white text-xs font-bold shadow-sm transition-all shrink-0 self-start sm:self-auto">
                            <i class="fa-solid fa-user-clock"></i>
                            <span>ดูรายชื่อที่ต้องพิจารณา ({{ $candidateStats['dept_review'] }})</span>
                        </a>
                    </div>
                @endif

                <!-- Overview Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <!-- Total Sent by HA -->
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group hover:border-gray-200 dark:hover:border-slate-600 transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">ผู้สมัครที่ HA ส่งมา</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1 block">
                                    {{ $candidateStats['total'] ?? 0 }}
                                </span>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            <span>ผู้สมัครทั้งหมดในความดูแล</span>
                        </div>
                    </div>

                    <!-- Pending Dept Review (รอหัวหน้าแผนกพิจารณา) -->
                    @php
                        $hasPendingReview = ($candidateStats['dept_review'] ?? 0) > 0;
                    @endphp
                    <div class="p-5 rounded-2xl shadow-sm relative overflow-hidden group transition-all {{ $hasPendingReview ? 'bg-gradient-to-br from-amber-50/90 to-orange-50/50 dark:from-amber-950/40 dark:to-orange-950/20 border-2 border-amber-400 dark:border-amber-600 shadow-md shadow-amber-500/10 hover:border-amber-500' : 'bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 hover:border-gray-200 dark:hover:border-slate-600' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold {{ $hasPendingReview ? 'text-amber-800 dark:text-amber-300' : 'text-gray-500 dark:text-gray-400' }} uppercase tracking-wider block">รอหัวหน้าพิจารณา</span>
                                <span class="text-2xl sm:text-3xl font-extrabold {{ $hasPendingReview ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }} mt-1 block">
                                    {{ $candidateStats['dept_review'] ?? 0 }}
                                </span>
                            </div>
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg {{ $hasPendingReview ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30 animate-pulse' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                                <i class="fa-solid fa-user-clock"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-[11px] font-medium flex items-center gap-1.5">
                            @if($hasPendingReview)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500 text-white font-bold text-[10px] shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    <span>ต้องการการตอบรับ/พิจารณา</span>
                                </span>
                            @else
                                <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[11px]"></i>
                                    <span>ไม่มีรายการค้างพิจารณา</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Interview Scheduled -->
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group hover:border-gray-200 dark:hover:border-slate-600 transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider block">นัดสัมภาษณ์แล้ว</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1 block">
                                    {{ $candidateStats['interview'] ?? 0 }}
                                </span>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            <span>อยู่ระหว่างกระบวนการสัมภาษณ์</span>
                        </div>
                    </div>

                    <!-- Passed / Hired -->
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group hover:border-gray-200 dark:hover:border-slate-600 transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">ผ่านคัดเลือก / บรรจุ</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 block">
                                    {{ $candidateStats['passed'] ?? 0 }}
                                </span>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-[11px] text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-check text-[10px]"></i>
                            <span>ผ่านเกณฑ์ / ยื่นข้อเสนอ / เริ่มงาน</span>
                        </div>
                    </div>
                </div>

                <!-- Status Filter Toolbar -->
                <div class="bg-white dark:bg-slate-800 p-3.5 sm:p-4 rounded-2xl shadow-xs border border-gray-100 dark:border-slate-700 mb-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200/80 dark:border-slate-600/60 shadow-2xs mr-1">
                            <i class="fa-solid fa-layer-group text-kumwell-red"></i>
                            <span>สถานะ:</span>
                        </div>

                        @php
                            $statusPills = [
                                'dept_review' => [
                                    'label' => '2. รอหัวหน้าพิจารณา',
                                    'count' => $candidateStats['dept_review'] ?? 0,
                                    'icon' => 'fa-user-clock',
                                    'active' => 'bg-amber-500 text-white shadow-md shadow-amber-500/30 border-amber-500 ring-2 ring-amber-400/40',
                                    'inactive' => ($candidateStats['dept_review'] ?? 0) > 0
                                        ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 border-amber-300 dark:border-amber-700 hover:bg-amber-100 font-bold'
                                        : 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-750',
                                    'badgeActive' => 'bg-white text-amber-700 font-black shadow-2xs',
                                    'badgeInactive' => ($candidateStats['dept_review'] ?? 0) > 0
                                        ? 'bg-amber-500 text-white font-black shadow-2xs animate-pulse'
                                        : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 font-bold',
                                ],
                                'interview' => [
                                    'label' => '3. รอ HA กำหนดวันนัด',
                                    'count' => $candidateStats['interview'] ?? 0,
                                    'icon' => 'fa-calendar-days',
                                    'active' => 'bg-purple-600 text-white shadow-md shadow-purple-500/30 border-purple-600 ring-2 ring-purple-400/40',
                                    'inactive' => 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-slate-700 hover:bg-purple-50 hover:text-purple-700 hover:border-purple-300',
                                    'badgeActive' => 'bg-white text-purple-700 font-black shadow-2xs',
                                    'badgeInactive' => ($candidateStats['interview'] ?? 0) > 0 ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-extrabold' : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 font-bold',
                                ],
                                'passed' => [
                                    'label' => '5. ผ่านการคัดเลือก',
                                    'count' => $candidateStats['passed'] ?? 0,
                                    'icon' => 'fa-circle-check',
                                    'active' => 'bg-emerald-600 text-white shadow-md shadow-emerald-500/30 border-emerald-600 ring-2 ring-emerald-400/40',
                                    'inactive' => 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-slate-700 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300',
                                    'badgeActive' => 'bg-white text-emerald-700 font-black shadow-2xs',
                                    'badgeInactive' => ($candidateStats['passed'] ?? 0) > 0 ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-extrabold' : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 font-bold',
                                ],
                                'rejected' => [
                                    'label' => 'ส่งกลับ / ไม่ผ่าน',
                                    'count' => $candidateStats['rejected'] ?? 0,
                                    'icon' => 'fa-rotate-left',
                                    'active' => 'bg-rose-600 text-white shadow-md shadow-rose-500/30 border-rose-600 ring-2 ring-rose-400/40',
                                    'inactive' => 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-slate-700 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-300',
                                    'badgeActive' => 'bg-white text-rose-700 font-black shadow-2xs',
                                    'badgeInactive' => ($candidateStats['rejected'] ?? 0) > 0 ? 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 font-extrabold' : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 font-bold',
                                ],
                                'all' => [
                                    'label' => 'ทั้งหมดที่ HA เคยส่ง',
                                    'count' => $candidateStats['total'] ?? 0,
                                    'icon' => 'fa-list-check',
                                    'active' => 'bg-slate-800 dark:bg-slate-700 text-white shadow-md shadow-slate-800/30 border-slate-800 ring-2 ring-slate-400/40',
                                    'inactive' => 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750',
                                    'badgeActive' => 'bg-white text-slate-800 font-black shadow-2xs',
                                    'badgeInactive' => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-extrabold',
                                ],
                            ];
                        @endphp

                        @foreach($statusPills as $statusKey => $pill)
                            @php
                                $isActive = ($candidateStatus === $statusKey);
                            @endphp
                            <a href="#" onclick="filterCandidateByPill('{{ $statusKey }}'); return false;"
                                id="pill-status-{{ $statusKey }}"
                                class="status-pill-btn inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all duration-200 {{ $isActive ? $pill['active'] . ' font-bold' : $pill['inactive'] }}">
                                <i class="fa-solid {{ $pill['icon'] }} text-xs"></i>
                                <span>{{ $pill['label'] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $isActive ? $pill['badgeActive'] : $pill['badgeInactive'] }}">
                                    {{ $pill['count'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- ════════════════════════════════════════════
                     Section Card: Candidates List (Picture 2 Style)
                     ════════════════════════════════════════════ --}}
                <div class="section-card mb-8">
                    <div class="px-6 pt-6 pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                        รายชื่อผู้สมัครที่ส่งมาพิจารณา 
                                        <span id="candidatesCountBadge" class="text-sm font-normal text-gray-400">({{ $candidates->count() }} รายการ)</span>
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">All Candidates forwarded by HA to Department Head</p>
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 hidden sm:block">
                                คลิกที่ปุ่ม <strong class="text-indigo-600 dark:text-indigo-400"><i class="fa-solid fa-eye text-[11px] mr-0.5"></i> ดูรายละเอียด</strong> ในคอลัมน์จัดการเพื่อดูข้อมูลฉบับเต็ม
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                    <div class="p-4 sm:p-6">
                        <!-- Filters (Exactly like Picture 2) -->
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <div class="relative flex-1 min-w-[220px] max-w-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="candidateCustomSearch" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="ค้นหารายละเอียด...">
                            </div>
                            <select id="candidateFilterPosition" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-2.5 pl-3.5 pr-9 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer min-w-[175px]">
                                <option value="">ทุกตำแหน่งงาน</option>
                                @foreach($availableJobPosts as $post)
                                    <option value="{{ $post->title }}">{{ $post->title }}</option>
                                @endforeach
                            </select>
                            <select id="candidateFilterStatus" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-2.5 pl-3.5 pr-9 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer min-w-[150px]">
                                <option value="">ทุกสถานะ</option>
                                <option value="dept_review" {{ $candidateStatus === 'dept_review' ? 'selected' : '' }}>2. รอหัวหน้าแผนกพิจารณา</option>
                                <option value="interview" {{ $candidateStatus === 'interview' ? 'selected' : '' }}>3. รอ HA กำหนดวันนัดสัมภาษณ์</option>
                                <option value="interview_scheduled" {{ $candidateStatus === 'interview_scheduled' ? 'selected' : '' }}>4. กำหนดวันสัมภาษณ์แล้ว</option>
                                <option value="passed" {{ $candidateStatus === 'passed' ? 'selected' : '' }}>5. ผ่านการคัดเลือก</option>
                                <option value="rejected" {{ $candidateStatus === 'rejected' ? 'selected' : '' }}>ส่งกลับ HA / ไม่ผ่าน</option>
                            </select>
                            <select id="candidateFilterDepartment" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-2.5 pl-3.5 pr-9 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer min-w-[160px]">
                                <option value="">ทุกฝ่าย / แผนก</option>
                                @foreach($availableDepartments as $deptTitle)
                                    <option value="{{ $deptTitle }}">{{ $deptTitle }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="candidateBtnReset" class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/80 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors cursor-pointer" title="ล้างค่าตัวกรองทั้งหมด">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                                <span>ล้างตัวกรอง</span>
                            </button>
                        </div>

                        <!-- Inner Box with DataTable (Picture 2 style) -->
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-2xs bg-white dark:bg-gray-800">
                            <table id="dataTableCandidates" class="display responsive nowrap w-full text-xs sm:text-sm">
                                <thead>
                                    <tr>
                                        <th class="all whitespace-nowrap text-center" style="width: 50px;">ID</th>
                                        <th class="whitespace-nowrap text-center" style="width: 100px;">วันที่ยื่นเรื่อง</th>
                                        <th class="all whitespace-nowrap text-left" style="min-width: 150px;">ผู้สมัคร</th>
                                        <th class="all whitespace-nowrap text-left" style="min-width: 130px;">ตำแหน่งที่สมัคร</th>
                                        <th class="whitespace-nowrap text-left" style="width: 90px;">ฝ่าย / แผนก</th>
                                        <th class="whitespace-nowrap text-left">การศึกษา & ประสบการณ์</th>
                                        <th class="whitespace-nowrap text-left" style="width: 110px;">ข้อมูลจาก HA</th>
                                        <th class="all whitespace-nowrap text-center" style="width: 145px;">สถานะ</th>
                                        <th class="all whitespace-nowrap text-center" style="width: 160px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($candidates as $candidate)
                                        @php
                                            $applicant = $candidate->applicant;
                                            $jobPost = $candidate->jobPost;

                                            // Education fallbacks
                                            $allEdu = $candidate->education->isNotEmpty() 
                                                ? $candidate->education 
                                                : ($applicant && $applicant->education->isNotEmpty() 
                                                    ? $applicant->education 
                                                    : collect($applicant && ($applicant->education_level || $applicant->university_name) ? [(object)[
                                                        'institution_name' => $applicant->university_name ?: '-',
                                                        'level' => $applicant->education_level ?: '-',
                                                        'gpa' => $applicant->gpa,
                                                        'faculty' => $applicant->faculty,
                                                        'major' => $applicant->major,
                                                        'end_year' => null,
                                                    ]] : []));
                                            $latestEdu = $allEdu->first();

                                            // Experience fallbacks
                                            $allExp = $candidate->experience->isNotEmpty() 
                                                ? $candidate->experience 
                                                : ($applicant && $applicant->experience->isNotEmpty() 
                                                    ? $applicant->experience 
                                                    : collect($applicant && ($applicant->current_company || $applicant->current_position) ? [(object)[
                                                        'company_name' => $applicant->current_company ?: 'ไม่ระบุชื่อบริษัท',
                                                        'position' => $applicant->current_position ?: 'ไม่ระบุตำแหน่ง',
                                                        'start_date' => null,
                                                        'end_date' => null,
                                                        'salary' => $applicant->expected_salary,
                                                        'job_detail' => $applicant->years_of_experience ? "ประสบการณ์ทำงานรวม {$applicant->years_of_experience} ปี" : null,
                                                    ]] : []));
                                            $latestExp = $allExp->first();

                                            $initials = mb_substr($applicant?->first_name ?? 'A', 0, 1) . mb_substr($applicant?->last_name ?? '', 0, 1);

                                            $resolveDocUrl = function($filePath) {
                                                if (!$filePath) return null;
                                                $b = basename($filePath);
                                                if (file_exists(public_path('files/recruitment_applicant_documents/' . $b))) {
                                                    return asset('files/recruitment_applicant_documents/' . $b);
                                                }
                                                if (file_exists(public_path('files/recruitment_applicant_documents/' . $filePath))) {
                                                    return asset('files/recruitment_applicant_documents/' . $filePath);
                                                }
                                                if (file_exists(public_path('storage/' . $filePath))) {
                                                    return asset('storage/' . $filePath);
                                                }
                                                if (file_exists(public_path($filePath))) {
                                                    return asset($filePath);
                                                }
                                                return asset('files/recruitment_applicant_documents/' . $b);
                                            };

                                            $allDocs = collect($candidate->documents);
                                            $resumeDocs = $allDocs->where('document_type', 'resume');
                                            if ($resumeDocs->isEmpty() && $applicant && $applicant->resume_file) {
                                                $resumeDocs = collect([(object)[
                                                    'document_type' => 'resume',
                                                    'file_name' => 'Resume_' . $applicant->first_name . '.pdf',
                                                    'file_path' => $applicant->resume_file,
                                                ]]);
                                            }

                                            $department = $jobPost?->department ?: $jobPost?->recruitmentRequest?->department;
                                            $deptName = $department?->department_name ?: ($department?->department_fullname ?: ($department?->name ?: 'ไม่ระบุแผนก'));
                                            $deptFullName = $department?->department_fullname;
                                        @endphp
                                        <tr data-position="{{ $jobPost?->title ?? '' }}"
                                            data-department="{{ $deptName }}"
                                            data-status="{{ $candidate->status }}">
                                            
                                            <!-- 1. ID -->
                                            <td data-order="{{ $candidate->id }}" class="text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                                #{{ $candidate->id }}
                                            </td>

                                            <!-- 2. วันที่ยื่นเรื่อง -->
                                            <td data-order="{{ $candidate->applied_at ? $candidate->applied_at->timestamp : $candidate->created_at->timestamp }}" class="text-center whitespace-nowrap text-gray-600 dark:text-gray-400">
                                                <div class="inline-flex items-center justify-center gap-1.5">
                                                    <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                                    <span>{{ $candidate->applied_at ? $candidate->applied_at->format('d/m/Y') : $candidate->created_at->format('d/m/Y') }}</span>
                                                </div>
                                            </td>

                                            <!-- 3. ผู้สมัคร -->
                                            <td class="text-left whitespace-nowrap">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 text-gray-700 dark:text-gray-200 flex items-center justify-center font-bold text-xs shadow-2xs shrink-0 border border-gray-200/70 dark:border-slate-600 overflow-hidden">
                                                        @if($applicant?->photo_file && file_exists(public_path($applicant->photo_file)))
                                                            <img src="{{ asset($applicant->photo_file) }}" alt="Photo" class="w-full h-full object-cover">
                                                        @else
                                                            <span>{{ $initials ?: 'AP' }}</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-gray-900 dark:text-white text-xs">
                                                            {{ $applicant?->first_name }} {{ $applicant?->last_name }}
                                                        </div>
                                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-0.5">
                                                            @if($applicant?->email)
                                                                <span class="truncate max-w-[140px]" title="{{ $applicant->email }}">{{ $applicant->email }}</span>
                                                            @endif
                                                            @if($applicant?->phone)
                                                                <span>{{ $applicant->phone }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- 4. ตำแหน่งที่สมัคร -->
                                            <td class="text-left whitespace-nowrap">
                                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-xs">
                                                    {{ $jobPost?->title ?? 'ไม่ระบุตำแหน่ง' }}
                                                </div>
                                            </td>

                                            <!-- 5. ฝ่าย / แผนก -->
                                            <td class="text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1.5 max-w-[180px] truncate" title="{{ $deptFullName ?: $deptName }}">
                                                    <i class="fa-regular fa-building text-gray-400 text-xs shrink-0"></i>
                                                    <span class="truncate font-medium">{{ $deptName }}</span>
                                                </span>
                                            </td>

                                            <!-- 6. การศึกษา & ประสบการณ์ -->
                                            <td class="text-left whitespace-nowrap">
                                                <div class="cursor-pointer group/edu" onclick="openCandidateModal({{ $candidate->id }})" title="คลิกเพื่อดูรายละเอียด">
                                                    @if($latestEdu)
                                                        <div class="text-xs text-gray-800 dark:text-gray-200 font-medium flex items-center gap-1.5 truncate max-w-[220px]">
                                                            <i class="fa-solid fa-graduation-cap text-indigo-500 text-xs shrink-0"></i>
                                                            <span class="truncate">{{ $latestEdu->level }} {{ $latestEdu->major ? '- ' . $latestEdu->major : '' }}</span>
                                                        </div>
                                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate max-w-[220px]">
                                                            {{ $latestEdu->institution_name }}
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">-</span>
                                                    @endif
                                                    @if($latestExp)
                                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1 truncate max-w-[220px] mt-0.5">
                                                            <i class="fa-solid fa-briefcase text-gray-400 text-[10px] shrink-0"></i>
                                                            <span class="truncate">{{ $latestExp->position }} @ {{ $latestExp->company_name }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>


                                            <!-- 8. ข้อมูลจาก HA -->
                                            <td class="text-left whitespace-nowrap">
                                                <div class="text-xs">
                                                    <div class="inline-flex items-center gap-1.5 font-semibold text-gray-800 dark:text-gray-200">
                                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                        <span>{{ $candidate->screener?->firstname ?? 'ทีม HA' }}</span>
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                                        {{ $candidate->screened_at ? $candidate->screened_at->format('d/m/Y H:i') : $candidate->updated_at->format('d/m/Y H:i') }} น.
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- 9. สถานะ (Picture 2 style badges) -->
                                            <td class="text-center whitespace-nowrap">
                                                @if($candidate->status === 'dept_review')
                                                    <span class="badge badge-blue shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping mr-1"></span>
                                                        2. รอหัวหน้าแผนกพิจารณา
                                                    </span>
                                                @elseif($candidate->status === 'interview')
                                                    <span class="badge badge-amber shadow-2xs bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">
                                                        <i class="fa-regular fa-clock text-[10px] mr-1"></i>
                                                        3. รอ HA กำหนดวันนัดสัมภาษณ์
                                                    </span>
                                                @elseif($candidate->status === 'interview_scheduled')
                                                    <span class="badge badge-purple shadow-2xs">
                                                        <i class="fa-solid fa-calendar-check text-[10px] mr-1"></i>
                                                        4. กำหนดวันสัมภาษณ์แล้ว
                                                    </span>
                                                @elseif($candidate->status === 'interview_completed')
                                                    <span class="badge badge-indigo shadow-2xs">
                                                        <i class="fa-solid fa-square-poll-vertical text-[10px] mr-1"></i>
                                                        5. สัมภาษณ์เสร็จสิ้น
                                                    </span>
                                                @elseif(in_array($candidate->status, ['passed_selection', 'selection_approved', 'offered', 'hired']))
                                                    <span class="badge badge-green shadow-2xs">
                                                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i>
                                                        ผ่านการคัดเลือก
                                                    </span>
                                                @elseif(in_array($candidate->status, ['dept_rejected', 'interview_failed', 'rejected']))
                                                    <span class="badge badge-red shadow-2xs">
                                                        <i class="fa-solid fa-circle-xmark text-[10px] mr-1"></i>
                                                        ส่งกลับ HA / ไม่ผ่าน
                                                    </span>
                                                @else
                                                    <span class="badge badge-gray shadow-2xs">
                                                        {{ $candidate->status_label }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- 10. จัดการ (Picture 2 style btn-view + action buttons) -->
                                            <td class="text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('backend.recruitment.applications.show', $candidate->id) }}"
                                                        class="btn-view"
                                                        title="ดูประวัติและรายละเอียด">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                    </a>

                                                    @if($candidate->status === 'dept_review')
                                                        <form id="form-interview-{{ $candidate->id }}" action="{{ route('backend.recruitment.applications.update-status', $candidate->id) }}" method="POST" class="inline m-0">
                                                            @csrf
                                                            <input type="hidden" name="status" value="interview">
                                                            <input type="hidden" name="note" value="หัวหน้าแผนกเลือกสัมภาษณ์: ส่งต่อให้ฝ่าย HA กำหนดวันเวลานัดหมายและแจ้งผู้สมัคร">
                                                            <button type="button" onclick="confirmDeptInterviewReport(this.form)"
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-2xs transition-colors cursor-pointer"
                                                                title="หัวหน้าแผนกเลือกผู้สมัครนี้เพื่อให้ฝ่าย HA กำหนดวันนัดสัมภาษณ์">
                                                                <i class="fa-solid fa-calendar-plus text-[10px]"></i>
                                                                <span>เลือกสัมภาษณ์ (ส่งต่อ HA)</span>
                                                            </button>
                                                        </form>

                                                        <button type="button" onclick="openDeptRejectModal({{ $candidate->id }}, '{{ addslashes($applicant?->first_name . ' ' . $applicant?->last_name) }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 dark:text-rose-300 font-semibold text-xs border border-rose-200 dark:border-rose-800 transition-colors cursor-pointer"
                                                            title="ส่งกลับให้ทีม HA พิจารณาใหม่">
                                                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                                            <span>ส่งกลับ HA</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

            {{-- ========================================================================= --}}
            {{-- TAB 2: รายงานคำขอเปิดรับสมัครพนักงาน (Manpower Requests - เฉพาะ HA)          --}}
            {{-- ========================================================================= --}}
            @elseif($isHa)

                <!-- Stats/Overview for Requests -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 sm:gap-6">
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">คำขอทั้งหมด</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCount ?? $requests->total() }}</div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700">
                        <div class="text-xs font-bold text-yellow-500 uppercase tracking-wider mb-2">รออนุมัติ</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $pendingCount ?? 0 }}
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════
                     SECTION CARD : ALL RECRUITMENT REQUESTS (Matching รูปที่ 1)
                ════════════════════════════════════════════ --}}
                <div class="section-card">
                    <div class="px-6 pt-6 pb-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 shadow-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                        รายการคำขอเปิดรับสมัครพนักงานทั้งหมด
                                        <span id="reportRequestsCountBadge" class="text-sm font-normal text-gray-400">({{ $requests->count() }} รายการ)</span>
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">All Recruitment Requests (แบบตารางจัดการง่าย สะดวกต่อการค้นหาและสร้าง Job Post)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                    <div class="p-4 sm:p-6">
                        <!-- Filters Bar (Matching รูปที่ 1) -->
                        <div class="flex flex-wrap items-center gap-3 mb-5">
                            <div class="relative flex-1 min-w-[220px] max-w-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </div>
                                <input type="text" id="reportCustomSearch" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white placeholder-gray-400" placeholder="ค้นหารายละเอียด...">
                            </div>
                            
                            <select id="reportFilterDepartment" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                                <option value="">ทุกฝ่าย / แผนก</option>
                                @foreach($requestDepartments as $dept)
                                    <option value="{{ $dept->department_fullname }}">{{ $dept->department_fullname }}</option>
                                @endforeach
                            </select>

                            <select id="reportFilterStatus" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                                <option value="">ทุกสถานะ</option>
                                <option value="approved">อนุมัติแล้ว</option>
                                <option value="pending">รอพิจารณา</option>
                                <option value="rejected">ไม่อนุมัติ</option>
                            </select>

                            <select id="reportFilterJobPost" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                                <option value="">สถานะ Job Post ทั้งหมด</option>
                                <option value="created">สร้าง Job Post แล้ว</option>
                                <option value="pending_create">รอ HA สร้าง Job Post</option>
                            </select>

                            <select id="reportFilterPageSize" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer" title="จำนวนรายการต่อหน้า">
                                <option value="5">แสดง 5 รายการ</option>
                                <option value="10" selected>แสดง 10 รายการ</option>
                                <option value="25">แสดง 25 รายการ</option>
                                <option value="50">แสดง 50 รายการ</option>
                                <option value="-1">แสดงทั้งหมด</option>
                            </select>

                            <button type="button" id="reportBtnResetFilters" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/80 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors cursor-pointer" title="ล้างค่าตัวกรองทั้งหมด">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span>ล้างตัวกรอง</span>
                            </button>
                        </div>

                        <!-- Table Container -->
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs">
                            <table id="dataTableReportRequests" class="display responsive nowrap w-full text-xs sm:text-sm">
                                <thead>
                                    <tr>
                                        <th class="all whitespace-nowrap text-center" style="width: 140px;">ประเภทแบบฟอร์ม</th>
                                        <th class="whitespace-nowrap text-left" style="width: 130px;">ID</th>
                                        <th class="whitespace-nowrap text-center" style="width: 120px;">วันที่ยื่นเรื่อง</th>
                                        <th class="all whitespace-nowrap text-left" style="min-width: 180px;">รายละเอียด</th>
                                        <th class="whitespace-nowrap text-left" style="min-width: 190px;">ฝ่าย / แผนก</th>
                                        <th class="whitespace-nowrap text-center" style="width: 70px;">จำนวน</th>
                                        <th class="whitespace-nowrap text-center" style="width: 140px;">การสร้าง Job Post</th>
                                        <th class="all whitespace-nowrap text-center" style="width: 110px;">สถานะ</th>
                                        <th class="text-center all whitespace-nowrap" style="width: 90px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        @php
                                            $mPost = $request->job_post;
                                            $hasJobPost = !empty($mPost);

                                            $formattedId = '#REQ-' . str_pad($request->id, 5, '0', STR_PAD_LEFT);

                                            $statusGroup = 'pending';
                                            if ($request->status === 'approved') {
                                                $statusGroup = 'approved';
                                            } elseif ($request->status === 'rejected') {
                                                $statusGroup = 'rejected';
                                            }

                                            $jobPostState = 'none';
                                            if ($request->status === 'approved') {
                                                $jobPostState = $hasJobPost ? 'created' : 'pending_create';
                                            }

                                            $deptFull = trim(($request->department ?? '') . ($request->section ? ' / ' . $request->section : ''));
                                            $deptFormatted = \App\Http\Controllers\ManpowerRequestController::formatDeptSection($request->department, $request->section);
                                            $jobTitle = $request->job_title_th ?: ($request->job_title_en ?: 'ไม่ระบุตำแหน่ง');
                                        @endphp
                                        <tr data-department="{{ $deptFull }}"
                                            data-status="{{ $statusGroup }}"
                                            data-jobpost="{{ $jobPostState }}"
                                            data-id="{{ $request->id }}">

                                            <!-- ประเภทแบบฟอร์ม -->
                                            <td class="text-center whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800">
                                                    ใบขออนุมัติกำลังคน
                                                </span>
                                            </td>

                                            <!-- ID -->
                                            <td data-order="{{ $request->id }}" class="whitespace-nowrap text-left font-mono font-bold text-gray-900 dark:text-white">
                                                <span class="tracking-tight">{{ $formattedId }}</span>
                                            </td>

                                            <!-- วันที่ยื่นเรื่อง -->
                                            <td data-order="{{ $request->created_at ? $request->created_at->timestamp : ($request->date ? \Carbon\Carbon::parse($request->date)->timestamp : 0) }}" class="text-center whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                                    </svg>
                                                    <span>{{ $request->created_at ? $request->created_at->format('d/m/Y') : ($request->date ? \Carbon\Carbon::parse($request->date)->format('d/m/Y') : '-') }}</span>
                                                </div>
                                            </td>

                                            <!-- รายละเอียด / ตำแหน่ง -->
                                            <td class="text-left font-medium text-gray-900 dark:text-white">
                                                <div class="font-bold text-gray-800 dark:text-gray-100">{{ $jobTitle }}</div>
                                                @if($request->hire_type)
                                                    <div class="text-[11px] text-gray-400">
                                                        @if($request->hire_type === 'replacement') (ทดแทน)
                                                        @elseif($request->hire_type === 'new') (จ้างเพิ่ม/ใหม่)
                                                        @elseif($request->hire_type === 'temporary') (ชั่วคราว)
                                                        @else ({{ $request->hire_type }})
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>

                                            <!-- ฝ่าย / แผนก -->
                                            <td class="text-left whitespace-nowrap text-gray-700 dark:text-gray-300">
                                                <div class="inline-flex items-center gap-1.5 max-w-[200px] truncate" title="{{ $deptFull }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                                    </svg>
                                                    <span class="truncate font-medium">{{ $deptFormatted ?: '-' }}</span>
                                                </div>
                                            </td>

                                            <!-- จำนวน -->
                                            <td class="text-center whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                                {{ $request->headcount }}
                                            </td>

                                            <!-- การสร้าง Job Post -->
                                            <td class="text-center whitespace-nowrap">
                                                @if($request->status === 'approved')
                                                    @if($hasJobPost)
                                                        <a href="{{ route('backend.recruitment.posts.edit', $mPost->id) }}"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 transition shadow-2xs"
                                                            title="ดูประกาศรับสมัครงาน">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                                            </svg>
                                                            <span>สร้างแล้ว</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('backend.recruitment.posts.create', ['manpower_request_id' => $request->id]) }}"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all active:scale-95 animate-pulse"
                                                            title="คลิกเพื่อสร้าง Job Post ทันที">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                                                            </svg>
                                                            <span>สร้าง Job Post</span>
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-gray-300 dark:text-gray-600">-</span>
                                                @endif
                                            </td>

                                            <!-- สถานะ -->
                                            <td class="text-center whitespace-nowrap">
                                                @if($request->status === 'approved')
                                                    <span class="badge badge-green shadow-2xs">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>อนุมัติแล้ว</span>
                                                    </span>
                                                @elseif($request->status === 'rejected')
                                                    <span class="badge badge-red shadow-2xs">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>ไม่อนุมัติ</span>
                                                    </span>
                                                @else
                                                    @php
                                                        $statusLabel = [
                                                            'pending_manager' => 'รอ ผจก.แผนก',
                                                            'pending_vp' => 'รอ ปธ.สายงาน',
                                                            'pending_hr' => 'รอ ผจก.HR',
                                                            'pending_ceo' => 'รอ CEO',
                                                            'draft' => 'แบบร่าง',
                                                        ][$request->status] ?? 'รอพิจารณา';
                                                    @endphp
                                                    <span class="badge badge-yellow shadow-2xs">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                        <span>{{ $statusLabel }}</span>
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- จัดการ -->
                                            <td class="text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center">
                                                    <a href="{{ route('manpower-request.show', $request->id) }}" class="btn-doc" title="ดูรายละเอียดคำขอ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </div>

    <!-- Dept Reject Modal -->
    <div id="deptRejectModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 dark:border-slate-700 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-rotate-left"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white">ส่งกลับให้ HA</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ระบุเหตุผลที่ผู้สมัครไม่ผ่านเกณฑ์การพิจารณา</p>
                    </div>
                </div>
                <button type="button" onclick="closeDeptRejectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-sm">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="deptRejectForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="status" value="dept_rejected">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        ผู้สมัคร: <span id="rejectApplicantName" class="text-kumwell-red"></span>
                    </label>
                    <textarea name="note" rows="3" required
                        placeholder="ระบุเหตุผล เช่น คุณสมบัติหรือประสบการณ์ยังไม่ตรงตามที่แผนกต้องการ, ต้องการผู้มีทักษะเฉพาะด้าน..."
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" onclick="closeDeptRejectModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">
                        ยกเลิก
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition-all">
                        ยืนยันส่งกลับให้ HA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCandidateModal(id) {
            const modal = document.getElementById('candidateModal-' + id);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeCandidateModal(id) {
            const modal = document.getElementById('candidateModal-' + id);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function openDeptRejectModal(applicationId, applicantName) {
            const modal = document.getElementById('deptRejectModal');
            const form = document.getElementById('deptRejectForm');
            const nameEl = document.getElementById('rejectApplicantName');
            
            form.action = "{{ url('backend/recruitment/applications') }}/" + applicationId + "/update-status";
            nameEl.textContent = applicantName;
            modal.classList.remove('hidden');
        }

        function closeDeptRejectModal() {
            document.getElementById('deptRejectModal').classList.add('hidden');
        }

        window.addEventListener('click', function(e) {
            const rejectModal = document.getElementById('deptRejectModal');
            if (e.target === rejectModal) {
                closeDeptRejectModal();
            }
            if (e.target.classList.contains('candidate-quickview-modal')) {
                e.target.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeptRejectModal();
                document.querySelectorAll('.candidate-quickview-modal').forEach(m => m.classList.add('hidden'));
                document.body.style.overflow = '';
            }
        });

        function confirmDeptInterviewReport(form) {
            Swal.fire({
                title: 'ยืนยันเลือกสัมภาษณ์ผู้สมัคร?',
                html: '<span class="text-xs text-gray-600 dark:text-gray-300">ยืนยันเลือกผู้สมัครรายนี้เข้าสู่ขั้นตอนการสัมภาษณ์งาน<br>โดยฝ่าย <b>HA จะเป็นผู้กำหนดวัน เวลา และส่งอีเมลนัดหมายผู้สมัคร</b></span>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="fa-solid fa-calendar-plus mr-1.5"></i> ยืนยัน (ส่งต่อให้ HA กำหนดวัน)',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl dark:bg-slate-800 dark:text-white',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold shadow-md shadow-emerald-600/20 text-xs',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-bold text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    {{-- DataTables scripts for both Candidates and Requests tabs --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            var thLanguage = {
                lengthMenu: "แสดง _MENU_ รายการ",
                zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                search: "ค้นหา:",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                },
                emptyTable: "ไม่มีข้อมูลในตาราง"
            };

            // 1. DataTables for Candidate Table (Picture 2 style)
            if ($('#dataTableCandidates').length) {
                window.tableCandidates = $('#dataTableCandidates').DataTable({
                    language: thLanguage,
                    order: [[1, 'desc']], // Sort by date
                    responsive: false,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
                    dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"l>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4"ip>',
                    columnDefs: [
                        { targets: [8], orderable: false, searchable: false }
                    ]
                });

                // Update counter badge
                $('#dataTableCandidates').on('draw.dt', function() {
                    var info = window.tableCandidates.page.info();
                    var badge = document.getElementById('candidatesCountBadge');
                    if (badge) {
                        badge.textContent = '(' + info.recordsDisplay + ' รายการ)';
                    }
                });

                // Instant real-time search
                $('#candidateCustomSearch').on('input keyup search paste', function() {
                    window.tableCandidates.search(this.value).draw();
                });

                // Custom DataTables filter for position, status, department
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var tid = settings.sTableId || (settings.nTable ? settings.nTable.id : '');
                    if (tid !== 'dataTableCandidates') return true;

                    var posFilter = ($('#candidateFilterPosition').val() || '').trim().toLowerCase();
                    var statusFilter = ($('#candidateFilterStatus').val() || '').trim().toLowerCase();
                    var deptFilter = ($('#candidateFilterDepartment').val() || '').trim().toLowerCase();

                    var row = (settings.aoData && settings.aoData[dataIndex]) ? settings.aoData[dataIndex].nTr : null;
                    if (!row) return true;

                    var rowPos = (row.getAttribute('data-position') || '').toLowerCase();
                    var rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
                    var rowDept = (row.getAttribute('data-department') || '').toLowerCase();

                    // 1. Position Filter
                    if (posFilter && rowPos.indexOf(posFilter) === -1) {
                        return false;
                    }

                    // 2. Status Filter
                    if (statusFilter) {
                        if (statusFilter === 'dept_review') {
                            if (rowStatus !== 'dept_review') return false;
                        } else if (statusFilter === 'interview') {
                            if (rowStatus !== 'interview') return false;
                        } else if (statusFilter === 'interview_scheduled') {
                            if (!['interview_scheduled', 'interview_completed'].includes(rowStatus)) return false;
                        } else if (statusFilter === 'passed') {
                            if (!['passed_selection', 'selection_approved', 'offered', 'hired'].includes(rowStatus)) return false;
                        } else if (statusFilter === 'rejected') {
                            if (!['dept_rejected', 'interview_failed', 'rejected'].includes(rowStatus)) return false;
                        } else {
                            if (rowStatus !== statusFilter) return false;
                        }
                    }

                    // 3. Department Filter
                    if (deptFilter && rowDept.indexOf(deptFilter) === -1) {
                        return false;
                    }

                    return true;
                });

                $('#candidateFilterPosition, #candidateFilterStatus, #candidateFilterDepartment').on('change', function() {
                    window.tableCandidates.draw();
                });

                // Reset filter button
                $('#candidateBtnReset').on('click', function() {
                    $('#candidateCustomSearch').val('');
                    $('#candidateFilterPosition').val('');
                    $('#candidateFilterStatus').val('');
                    $('#candidateFilterDepartment').val('');
                    window.tableCandidates.search('').draw();

                    document.querySelectorAll('.status-pill-btn').forEach(function(btn) {
                        btn.classList.remove('ring-2', 'shadow-md', 'font-bold');
                    });
                });

                // Pre-filter if status was set
                if ($('#candidateFilterStatus').val()) {
                    window.tableCandidates.draw();
                }
            }

            // 2. DataTables for TAB 2 (Requests)
            if ($('#dataTableReportRequests').length) {
                const table = $('#dataTableReportRequests').DataTable({
                    responsive: false,
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "ทั้งหมด"]],
                    order: [[2, 'desc'], [1, 'desc']],
                    language: thLanguage
                });

                $('#reportCustomSearch').on('keyup input search paste', function() {
                    table.search(this.value).draw();
                });

                $('#reportFilterPageSize').on('change', function() {
                    table.page.len(parseInt(this.value, 10)).draw();
                });

                function applyReportFilters() {
                    const deptVal = $('#reportFilterDepartment').val();
                    const statusVal = $('#reportFilterStatus').val();
                    const jobPostVal = $('#reportFilterJobPost').val();

                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        const tid = settings.sTableId || (settings.nTable ? settings.nTable.id : '');
                        if (tid !== 'dataTableReportRequests') return true;

                        const row = $(settings.aoData[dataIndex].nTr);
                        const rowDept = row.attr('data-department') || '';
                        const rowStatus = row.attr('data-status') || '';
                        const rowJobPost = row.attr('data-jobpost') || '';

                        if (deptVal && !rowDept.includes(deptVal)) return false;
                        if (statusVal && rowStatus !== statusVal) return false;
                        if (jobPostVal && rowJobPost !== jobPostVal) return false;

                        return true;
                    });

                    table.draw();
                }

                $('#reportFilterDepartment, #reportFilterStatus, #reportFilterJobPost').on('change', applyReportFilters);

                $('#reportBtnResetFilters').on('click', function() {
                    $('#reportCustomSearch').val('');
                    $('#reportFilterDepartment').val('');
                    $('#reportFilterStatus').val('');
                    $('#reportFilterJobPost').val('');
                    $('#reportFilterPageSize').val('10');
                    table.page.len(10);
                    table.search('').draw();
                });
            }
        });

        // Pill filter helper function
        function filterCandidateByPill(statusKey) {
            var statusSelect = document.getElementById('candidateFilterStatus');
            if (!statusSelect) return;

            statusSelect.value = (statusKey === 'all') ? '' : statusKey;
            $(statusSelect).trigger('change');

            document.querySelectorAll('.status-pill-btn').forEach(function(btn) {
                btn.classList.remove('ring-2', 'shadow-md', 'font-bold');
            });
            var activeBtn = document.getElementById('pill-status-' + statusKey);
            if (activeBtn) {
                activeBtn.classList.add('ring-2', 'font-bold');
            }
        }
    </script>
@endsection
