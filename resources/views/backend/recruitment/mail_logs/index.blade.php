@extends('layouts.recruitment.app')

@section('title', 'ประวัติการส่งอีเมล (Mail Logs)')

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
        #dataTableMailLogs {
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

        /* Modern pagination buttons matching Image 2 */
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
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: transparent !important;
            border-color: transparent !important;
            color: #4b5563 !important;
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

        /* ── Status badges ─────────────────────── */
        .badge { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            gap: 0.35rem;
            padding: 0.3rem 0.85rem; 
            border-radius: 9999px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            letter-spacing: 0.02em; 
            white-space: nowrap;
        }
        .badge-blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .badge-green  { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .badge-red    { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .badge-yellow { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .badge-gray   { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        /* ── Action Dropdown ─────────────────────── */
        .action-dropdown-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            color: #94a3b8;
            background: transparent;
            transition: all 0.15s ease;
        }
        .action-dropdown-btn:hover {
            color: #0f172a;
            background: #f1f5f9;
        }
        .dark .action-dropdown-btn:hover {
            color: #f8fafc;
            background: #374151;
        }

        .action-dropdown-menu {
            position: fixed;
            z-index: 9999;
            min-width: 160px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            padding: 0.35rem;
        }
        .dark .action-dropdown-menu {
            background: #1e293b;
            border-color: #334155;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        /* ── DataTable table header styling ─────────────────── */
        table.dataTable thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.75rem 0.85rem !important;
            border-bottom: 1px solid #e2e8f0 !important;
            white-space: nowrap;
        }
        .dark table.dataTable thead th {
            background-color: #111827;
            color: #94a3b8;
            border-bottom-color: #374151 !important;
        }

        table.dataTable tbody td {
            padding: 0.75rem 0.85rem !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .dark table.dataTable tbody td {
            border-bottom-color: #374151/40 !important;
        }

        table.dataTable tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .dark table.dataTable tbody tr:hover {
            background-color: #1f2937/80 !important;
        }
    </style>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl xl:max-w-[1440px] mx-auto space-y-6 recruitment-table-font">

        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    <a href="{{ route('backend.recruitment.dashboard') }}" class="hover:text-kumwell-red transition-colors">Recruitment</a>
                    <span>/</span>
                    <span class="text-slate-700 dark:text-slate-300">Mail Logs</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                    <i class="fa-solid fa-envelope-circle-check text-kumwell-red"></i>
                    ประวัติการส่งอีเมล (Recruitment Mail Logs)
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    ตรวจสอบสถานะการส่งอีเมล ประวัติการนัดสัมภาษณ์ และผลการพิจารณารับเข้าทำงาน
                </p>
            </div>

            <!-- Microsoft 365 Connection Status Card in Header -->
            <div class="flex items-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-3 rounded-2xl shadow-xs">
                @if(Auth::user()->hasMicrosoftConnected())
                    <div class="size-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-brands fa-microsoft"></i>
                    </div>
                    <div class="text-xs">
                        <div class="flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400">
                            <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Microsoft 365 เชื่อมต่อแล้ว
                        </div>
                        <div class="text-slate-500 dark:text-slate-400 truncate max-w-[180px]">
                            {{ Auth::user()->microsoftToken?->microsoft_email }}
                        </div>
                    </div>
                    <form action="{{ route('auth.microsoft.disconnect') }}" method="POST" class="ml-2 m-0" onsubmit="return confirmDisconnectMicrosoft(this, event)">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="ยกเลิกการเชื่อมต่อ">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                @else
                    <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-brands fa-microsoft"></i>
                    </div>
                    <div class="text-xs">
                        <div class="font-bold text-slate-700 dark:text-slate-300">ยังไม่เชื่อมต่อ Microsoft</div>
                        <div class="text-slate-500 text-[11px]">ส่งผ่านระบบ SMTP ทั่วไป</div>
                    </div>
                    <a href="{{ route('auth.microsoft.redirect') }}" class="ml-2 inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                        <i class="fa-solid fa-link text-[10px]"></i> เชื่อมต่อ
                    </a>
                @endif
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-xs">
                <div class="text-xs font-bold text-slate-500 uppercase">อีเมลทั้งหมด</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">บันทึกทั้งหมดในระบบ</div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-xs">
                <div class="text-xs font-bold text-emerald-600 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i> ส่งสำเร็จ
                </div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['sent']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">ส่งถึงผู้สมัครเรียบร้อย</div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-xs">
                <div class="text-xs font-bold text-rose-600 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i> ล้มเหลว
                </div>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['failed']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">มีปัญหาในการส่ง</div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-xs">
                <div class="text-xs font-bold text-amber-600 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> รอในคิว (Queue)
                </div>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['queued']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">กำลังรอ Worker ประมวลผล</div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-xs col-span-2 sm:col-span-1">
                <div class="text-xs font-bold text-slate-500 uppercase">สัดส่วนช่องทาง</div>
                <div class="flex items-center gap-3 mt-2 text-xs">
                    <div class="flex items-center gap-1 font-semibold text-blue-600" title="Microsoft Graph">
                        <i class="fa-brands fa-microsoft"></i> {{ $stats['graph'] }}
                    </div>
                    <span class="text-slate-300 dark:text-slate-600">|</span>
                    <div class="flex items-center gap-1 font-semibold text-slate-600 dark:text-slate-400" title="SMTP">
                        <i class="fa-solid fa-server"></i> {{ $stats['smtp'] }}
                    </div>
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Graph vs SMTP</div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════
             SECTION CARD : ALL MAIL LOGS TABLE (Matching รูปที่ 2)
        ════════════════════════════════════════════ --}}
        <div class="section-card">
            <div class="px-6 pt-6 pb-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                รายการประวัติการส่งอีเมลทั้งหมด
                                <span id="mailLogsCountBadge" class="text-sm font-normal text-gray-400">({{ $logs->count() }} รายการ)</span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">All Recruitment Mail Logs (แบบตารางจัดการง่าย สะดวกต่อการค้นหาและตรวจสอบสถานะ)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            <div class="p-4 sm:p-6">
                <!-- Filters Bar (Matching รูปที่ 2) -->
                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <div class="relative flex-1 min-w-[220px] max-w-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="customSearch" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white placeholder-gray-400" placeholder="ค้นหารายละเอียด...">
                    </div>
                    
                    <select id="filterStatus" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                        <option value="">ทุกสถานะ</option>
                        <option value="sent">ส่งสำเร็จ</option>
                        <option value="failed">ล้มเหลว</option>
                        <option value="queued">รอในคิว</option>
                    </select>

                    <select id="filterMailType" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                        <option value="">ประเภทอีเมล ทั้งหมด</option>
                        <option value="interview_scheduled">นัดสัมภาษณ์</option>
                        <option value="application_hired">รับเข้าทำงาน</option>
                        <option value="application_rejected">ไม่ผ่านการคัดเลือก</option>
                        <option value="direct_email">อีเมลตรง</option>
                    </select>

                    <select id="filterChannel" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                        <option value="">ทุกช่องทาง</option>
                        <option value="microsoft_graph">Microsoft Graph</option>
                        <option value="smtp">SMTP</option>
                    </select>

                    <select id="filterPageSize" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer" title="จำนวนรายการต่อหน้า">
                        <option value="5">แสดง 5 รายการ</option>
                        <option value="10" selected>แสดง 10 รายการ</option>
                        <option value="25">แสดง 25 รายการ</option>
                        <option value="50">แสดง 50 รายการ</option>
                        <option value="-1">แสดงทั้งหมด</option>
                    </select>

                    <button type="button" id="btnResetFilters" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/80 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors cursor-pointer" title="ล้างค่าตัวกรองทั้งหมด">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>ล้างตัวกรอง</span>
                    </button>
                </div>

                <!-- Table Matching รูปที่ 2 -->
                <table id="dataTableMailLogs" class="display responsive nowrap w-full text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th class="all whitespace-nowrap text-center" style="width: 140px;">ประเภทอีเมล</th>
                            <th class="whitespace-nowrap text-left" style="width: 90px;">ID</th>
                            <th class="whitespace-nowrap text-center" style="width: 120px;">วันที่ส่ง</th>
                            <th class="all whitespace-nowrap text-left">ผู้รับ (Recipient)</th>
                            <th class="whitespace-nowrap text-left">ตำแหน่งที่สมัคร</th>
                            <th class="all whitespace-nowrap text-left">หัวข้ออีเมล</th>
                            <th class="whitespace-nowrap text-center" style="width: 110px;">ช่องทาง</th>
                            <th class="all whitespace-nowrap text-center" style="width: 110px;">สถานะ</th>
                            <th class="text-center all whitespace-nowrap" style="width: 60px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $displayLogCode = 'LOG-' . str_pad($log->id, 5, '0', STR_PAD_LEFT);
                            @endphp
                            <tr data-status="{{ $log->status }}" data-mailtype="{{ $log->mail_type }}" data-channel="{{ $log->channel }}">
                                <!-- 1. ประเภทอีเมล (Matching badge-blue รูปที่ 2) -->
                                <td class="text-center whitespace-nowrap">
                                    <span class="badge badge-blue shadow-2xs">{{ $log->mail_type_label }}</span>
                                </td>

                                <!-- 2. ID -->
                                <td data-order="{{ $log->id }}" class="text-left font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    <span class="font-mono text-xs text-indigo-600 dark:text-indigo-400">
                                        #{{ $displayLogCode }}
                                    </span>
                                </td>

                                <!-- 3. วันที่ส่ง (Calendar Icon + Date) -->
                                <td data-order="{{ $log->created_at ? $log->created_at->timestamp : 0 }}" class="text-center whitespace-nowrap text-gray-600 dark:text-gray-400">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                        </svg>
                                        <span>{{ $log->created_at ? $log->created_at->copy()->addYears(543)->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    @if($log->sent_at)
                                        <div class="text-[10px] text-emerald-600 dark:text-emerald-400">{{ $log->sent_at->format('H:i:s') }} น.</div>
                                    @endif
                                </td>

                                <!-- 4. ผู้รับ (Recipient Name + Email) -->
                                <td class="text-left whitespace-nowrap">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $log->recipient_name ?: '-' }}
                                    </div>
                                    <a href="mailto:{{ $log->recipient_email }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline block truncate max-w-[200px]" title="{{ $log->recipient_email }}">
                                        {{ $log->recipient_email }}
                                    </a>
                                </td>

                                <!-- 5. ตำแหน่งที่สมัคร -->
                                <td class="text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    @if($log->position_name)
                                        <div class="inline-flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                            </svg>
                                            <span class="truncate max-w-[180px]" title="{{ $log->position_name }}">{{ $log->position_name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- 6. หัวข้ออีเมล -->
                                <td class="text-left">
                                    <div class="font-medium text-gray-900 dark:text-gray-100 line-clamp-1 max-w-xs" title="{{ $log->subject }}">
                                        {{ $log->subject }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 truncate max-w-xs">
                                        ผู้ส่ง: {{ $log->user?->fullname ?: 'ระบบอัตโนมัติ' }}
                                    </div>
                                </td>

                                <!-- 7. ช่องทาง (Channel) -->
                                <td class="text-center whitespace-nowrap">
                                    @if($log->channel === 'microsoft_graph')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800 shadow-2xs">
                                            <i class="fa-brands fa-microsoft text-[11px]"></i> Graph API
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 shadow-2xs">
                                            <i class="fa-solid fa-server text-[10px]"></i> SMTP
                                        </span>
                                    @endif
                                </td>

                                <!-- 8. สถานะ (Matching badge-green/red/yellow รูปที่ 2) -->
                                <td class="text-center whitespace-nowrap">
                                    @if($log->status === 'sent')
                                        <span class="badge badge-green shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                            </svg>
                                            <span>ส่งสำเร็จ</span>
                                        </span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge badge-red shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-rose-600 dark:text-rose-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                                            </svg>
                                            <span>ล้มเหลว</span>
                                        </span>
                                    @else
                                        <span class="badge badge-yellow shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
                                            </svg>
                                            <span>รอในคิว</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 9. จัดการ (3-dots vertical action menu) -->
                                <td class="text-center whitespace-nowrap">
                                    <div class="relative inline-block text-left">
                                        <button type="button"
                                            class="action-dropdown-btn"
                                            onclick="toggleActionMenu(this, 'menu-{{ $log->id }}')"
                                            title="ตัวเลือกเพิ่มเติม">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div id="menu-{{ $log->id }}" class="action-dropdown-menu hidden text-xs">
                                            @if($log->error_message)
                                                <button type="button"
                                                    onclick="showErrorModal('{{ addslashes($log->recipient_email) }}', '{{ addslashes($log->subject) }}', '{{ addslashes($log->error_message) }}')"
                                                    class="flex items-center gap-2 w-full px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-md transition-colors text-left">
                                                    <i class="fa-solid fa-circle-exclamation w-3.5 text-center"></i>
                                                    <span>ดูข้อผิดพลาด</span>
                                                </button>
                                            @endif

                                            @if(in_array($log->status, ['failed', 'queued']))
                                                <form action="{{ route('backend.recruitment.mail-logs.retry', $log->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-md transition-colors text-left">
                                                        <i class="fa-solid fa-rotate-right w-3.5 text-center"></i>
                                                        <span>ส่งทันที / ส่งซ้ำ</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="mailto:{{ $log->recipient_email }}" class="flex items-center gap-2 w-full px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md transition-colors text-left">
                                                <i class="fa-regular fa-envelope w-3.5 text-center text-slate-400"></i>
                                                <span>ส่งอีเมลหาผู้รับ</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <i class="fa-regular fa-envelope-open text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                                    ไม่พบข้อมูลประวัติการส่งอีเมล
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Error Detail Modal -->
    <div id="errorModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-2.5 text-rose-600 font-bold text-base">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    รายละเอียดข้อผิดพลาดในการส่ง
                </div>
                <button type="button" onclick="closeErrorModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="mt-4 space-y-3 text-xs">
                <div>
                    <span class="text-slate-400">ผู้รับ:</span>
                    <span id="modalRecipient" class="font-semibold text-slate-800 dark:text-slate-200 ml-1"></span>
                </div>
                <div>
                    <span class="text-slate-400">หัวข้อ:</span>
                    <span id="modalSubject" class="font-semibold text-slate-800 dark:text-slate-200 ml-1"></span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">ข้อความแจ้งเตือน (Error Message):</span>
                    <div id="modalErrorMessage" class="p-3 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 rounded-xl font-mono text-[11px] leading-relaxed border border-rose-200 dark:border-rose-900/50 break-words max-h-48 overflow-y-auto"></div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeErrorModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs transition-colors">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

    {{-- DataTables JavaScript --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        // Modal helpers
        function showErrorModal(recipient, subject, error) {
            document.getElementById('modalRecipient').innerText = recipient;
            document.getElementById('modalSubject').innerText = subject;
            document.getElementById('modalErrorMessage').innerText = error;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        // Action menu dropdown toggle
        window.toggleActionMenu = function(btn, menuId) {
            const menu = document.getElementById(menuId);
            if (!menu) return;

            const isCurrentlyHidden = menu.classList.contains('hidden');

            // Hide all other open menus
            document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                m.classList.add('hidden');
            });

            if (isCurrentlyHidden) {
                menu.classList.remove('hidden');

                const rect = btn.getBoundingClientRect();
                const menuWidth = 160;
                const menuHeight = menu.offsetHeight || 100;

                // Horizontal positioning (align right edge with button)
                let left = rect.right - menuWidth;
                if (left < 10) left = 10;
                menu.style.left = left + 'px';

                // Vertical positioning
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

        // Close dropdown when scrolling
        window.addEventListener('scroll', function() {
            document.querySelectorAll('.action-dropdown-menu:not(.hidden)').forEach(function(m) {
                m.classList.add('hidden');
            });
        }, true);

        // DataTables init
        $(document).ready(function() {
            let table = null;
            const dtOptions = {
                dom: '<"overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs"t><"dataTables_bottom_bar"ip>',
                autoWidth: false,
                responsive: false,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "ทั้งหมด"]],
                order: [[2, 'desc'], [1, 'desc']], // เรียงวันที่ส่งล่าสุดก่อน
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
                    table = new window.DataTable('#dataTableMailLogs', dtOptions);
                } else if (window.$ && typeof window.$.fn.DataTable === 'function') {
                    table = $('#dataTableMailLogs').DataTable(dtOptions);
                }
            } catch (err) {
                console.warn('DataTable init notice:', err);
            }

            if (table) {
                // Custom search box
                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                // Page size dropdown
                $('#filterPageSize').on('change', function() {
                    const len = parseInt(this.value, 10);
                    table.page.len(len).draw();
                });

                // Filter logic
                function applyFilters() {
                    const statusVal = $('#filterStatus').val();
                    const mailTypeVal = $('#filterMailType').val();
                    const channelVal = $('#filterChannel').val();

                    const filterFn = function(settings, data, dataIndex) {
                        const row = $(table.row(dataIndex).node());
                        const rowStatus = row.attr('data-status') || '';
                        const rowMailType = row.attr('data-mailtype') || '';
                        const rowChannel = row.attr('data-channel') || '';

                        if (statusVal && rowStatus !== statusVal) return false;
                        if (mailTypeVal && rowMailType !== mailTypeVal) return false;
                        if (channelVal && rowChannel !== channelVal) return false;

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

                $('#filterStatus, #filterMailType, #filterChannel').on('change', applyFilters);

                // Reset filters
                $('#btnResetFilters').on('click', function() {
                    $('#customSearch').val('');
                    $('#filterStatus').val('');
                    $('#filterMailType').val('');
                    $('#filterChannel').val('');
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
                    $('#mailLogsCountBadge').text(`(${visibleRows} รายการ)`);
                }

                table.on('draw', function() {
                    document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                        m.classList.add('hidden');
                    });
                    updateCount();
                });
            }
        });
    </script>
@endsection
