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

        /* ── Expandable Group Row & Chevron (Image 1 Style) ── */
        .group-parent-row {
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .group-parent-row:hover {
            background-color: #f8fafc !important;
        }
        .dark .group-parent-row:hover {
            background-color: #1e293b/80 !important;
        }
        .group-parent-row.is-active {
            background-color: #f1f5f9 !important;
        }
        .dark .group-parent-row.is-active {
            background-color: #334155/60 !important;
        }
        .group-chevron {
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .group-chevron.is-open {
            transform: rotate(180deg);
        }

        /* ── Action Box (Matching Image 1 [✏️][v]) ── */
        .action-box-erp {
            display: inline-flex;
            align-items: center;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .dark .action-box-erp {
            border-color: #374151;
            background: #1e293b;
        }
        .action-box-erp button,
        .action-box-erp a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            color: #64748b;
            background: transparent;
            transition: all 0.15s ease;
            font-size: 0.75rem;
        }
        .action-box-erp button:hover,
        .action-box-erp a:hover {
            color: #F2704E;
            background: #f8fafc;
        }
        .dark .action-box-erp button:hover,
        .dark .action-box-erp a:hover {
            color: #F2704E;
            background: #374151;
        }
        .action-box-erp .divider {
            width: 1px;
            height: 1.25rem;
            background: #e2e8f0;
        }
        .dark .action-box-erp .divider {
            background: #374151;
        }

        /* ── Specification grid text styles (Image 1 ERP style) ── */
        .erp-spec-label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
            width: 155px;
            flex-shrink: 0;
        }
        .dark .erp-spec-label {
            color: #94a3b8;
        }
        .erp-spec-value {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #1e293b;
            word-break: break-word;
        }
        .dark .erp-spec-value {
            color: #f1f5f9;
        }

        /* ── DataTable table header styling ─────────────────── */
        table.dataTable thead th,
        .erp-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.75rem 0.85rem !important;
            border-bottom: 1px solid #e2e8f0 !important;
            white-space: nowrap;
        }
        .dark table.dataTable thead th,
        .dark .erp-table thead th {
            background-color: #111827;
            color: #94a3b8;
            border-bottom-color: #374151 !important;
        }

        table.dataTable tbody td,
        .erp-table tbody td {
            padding: 0.75rem 0.85rem !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .dark table.dataTable tbody td,
        .dark .erp-table tbody td {
            border-bottom-color: #374151/40 !important;
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
                    ตรวจสอบสถานะการส่งอีเมล แยกตามกลุ่มตำแหน่งงาน พร้อมรายละเอียดสเปกและการส่ง (สไตล์เดียวกับรูปที่ 1)
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

        {{-- ══════════════════════════════════════════════════════════
             SECTION CARD : GROUPED MAIL LOGS (Matching รูปที่ 1)
        ══════════════════════════════════════════════════════════ --}}
        <div class="section-card">
            <!-- Header with Title & View Mode Switcher -->
            <div class="px-5 sm:px-6 pt-6 pb-4">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Left: Title -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-rose-50 text-kumwell-red dark:bg-rose-950/50 dark:text-rose-400 shadow-xs border border-rose-100 dark:border-rose-900/40">
                            <i class="fa-solid fa-boxes-stacked text-lg"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                    ประวัติการส่งอีเมลแยกตามตำแหน่งงาน
                                </h3>
                                <span id="groupedSummaryBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-kumwell-red border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300">
                                    {{ $groupedLogs->count() }} ตำแหน่ง ({{ $logs->count() }} อีเมล)
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                รวมรายการตามตำแหน่งงาน คลิกแถวหรือปุ่มลูกศรเพื่อเปิดดูสเปกและรายการย่อยแบบรูปที่ 1
                            </p>
                        </div>
                    </div>

                    <!-- Right: View Mode Toggle & Expand/Collapse Controls -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Expand / Collapse All -->
                        <div id="groupedExpandControls" class="inline-flex items-center rounded-xl bg-slate-100 dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700 text-xs">
                            <button type="button" onclick="expandAllGroups()" class="px-2.5 py-1 text-slate-600 dark:text-slate-300 hover:text-kumwell-red rounded-lg transition-colors flex items-center gap-1" title="เปิดรายละเอียดทุกตำแหน่งพร้อมกัน">
                                <i class="fa-solid fa-angles-down text-[10px]"></i>
                                <span>เปิดทั้งหมด</span>
                            </button>
                            <span class="text-slate-300 dark:text-slate-600">|</span>
                            <button type="button" onclick="collapseAllGroups()" class="px-2.5 py-1 text-slate-600 dark:text-slate-300 hover:text-kumwell-red rounded-lg transition-colors flex items-center gap-1" title="พับปิดรายละเอียดทั้งหมด">
                                <i class="fa-solid fa-angles-up text-[10px]"></i>
                                <span>พับทั้งหมด</span>
                            </button>
                        </div>

                        <!-- View Switcher (Grouped vs Flat) -->
                        <div class="inline-flex items-center rounded-xl bg-slate-100 dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700 text-xs font-semibold">
                            <button type="button" id="btnModeGrouped" onclick="switchViewMode('grouped')" class="px-3 py-1.5 rounded-lg transition-all shadow-xs bg-white dark:bg-slate-700 text-kumwell-red dark:text-white font-bold flex items-center gap-2">
                                <i class="fa-solid fa-layer-group"></i>
                                <span>จัดกลุ่มตำแหน่ง (รูปที่ 1)</span>
                            </button>
                            <button type="button" id="btnModeFlat" onclick="switchViewMode('flat')" class="px-3 py-1.5 rounded-lg transition-all text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-table-list"></i>
                                <span>รายการทั้งหมด (ตารางเดี่ยว)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800"></div>

            <div class="p-4 sm:p-6">
                <!-- Filters Bar (ERP Style Matching รูปที่ 1) -->
                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-[240px] max-w-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input type="text" id="customSearch" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-kumwell-red/20 focus:border-kumwell-red block w-full pl-10 pr-3.5 py-2.5 placeholder-slate-400 transition-all" placeholder="ค้นหาตำแหน่ง, ผู้รับ, อีเมล, หัวข้อ...">
                    </div>
                    
                    <!-- Filter Status -->
                    <div class="relative w-full sm:w-auto">
                        <select id="filterStatus" class="appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-kumwell-red/20 focus:border-kumwell-red block py-2.5 pl-3.5 pr-10 w-full sm:w-auto cursor-pointer shadow-2xs">
                            <option value="">ทุกสถานะ (All Status)</option>
                            <option value="sent">ส่งสำเร็จ</option>
                            <option value="failed">ล้มเหลว</option>
                            <option value="queued">รอในคิว</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>

                    <!-- Filter Mail Type -->
                    <div class="relative w-full sm:w-auto">
                        <select id="filterMailType" class="appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-kumwell-red/20 focus:border-kumwell-red block py-2.5 pl-3.5 pr-10 w-full sm:w-auto cursor-pointer shadow-2xs">
                            <option value="">ประเภทอีเมล ทั้งหมด</option>
                            <option value="new_application_ha_notification">แจ้งเตือนผู้สมัครใหม่ (HA)</option>
                            <option value="dept_review_notification">ส่งต่อหัวหน้าแผนกพิจารณา</option>
                            <option value="interview_scheduled">นัดหมายสัมภาษณ์งาน</option>
                            <option value="interview_scheduled_dept">แจ้งแผนก/กรรมการสัมภาษณ์</option>
                            <option value="interview_rescheduled">ปรับเวลานัดสัมภาษณ์</option>
                            <option value="application_received">ยืนยันการรับสมัครงาน</option>
                            <option value="application_hired">แจ้งผลรับเข้าทำงาน</option>
                            <option value="application_rejected">แจ้งผลไม่ผ่านการคัดเลือก</option>
                            <option value="direct_email">อีเมลติดต่อโดยตรง</option>
                            <option value="test">ทดสอบระบบอีเมล</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>

                    <!-- Filter Channel -->
                    <div class="relative w-full sm:w-auto">
                        <select id="filterChannel" class="appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-kumwell-red/20 focus:border-kumwell-red block py-2.5 pl-3.5 pr-10 w-full sm:w-auto cursor-pointer shadow-2xs">
                            <option value="">ทุกช่องทาง (All Channels)</option>
                            <option value="microsoft_graph">Microsoft Graph API</option>
                            <option value="smtp">SMTP</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>

                    <!-- Reset Filters Button (Matching Image 1 red outline style) -->
                    <button type="button" id="btnResetFilters" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 border border-rose-200 dark:border-rose-900/60 rounded-xl transition-colors cursor-pointer" title="ล้างค่าตัวกรองทั้งหมด">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>ล้างตัวกรอง</span>
                    </button>
                </div>

                {{-- ──────────────────────────────────────────
                     VIEW 1 : GROUPED BY POSITION (รูปที่ 1)
                ────────────────────────────────────────── --}}
                <div id="groupedViewContainer" class="space-y-4">
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-2xs">
                        <table id="tableGroupedMailLogs" class="erp-table w-full text-left text-xs sm:text-sm">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap" style="width: 220px;">SO / ตำแหน่งงาน</th>
                                    <th class="whitespace-nowrap text-center" style="width: 90px;">SO_LINE</th>
                                    <th class="whitespace-nowrap">CUS_ITEM / หัวข้อล่าสุด</th>
                                    <th class="whitespace-nowrap text-center" style="width: 100px;">ITEM / ผู้รับ</th>
                                    <th class="whitespace-nowrap text-center" style="width: 130px;">PROD_ITEM / ช่องทาง</th>
                                    <th class="whitespace-nowrap text-center" style="width: 140px;">SIZE / ส่งล่าสุด</th>
                                    <th class="whitespace-nowrap text-center" style="width: 130px;">สถานะภาพรวม</th>
                                    <th class="whitespace-nowrap text-right" style="width: 110px;">PROD_PCS_QTY</th>
                                    <th class="whitespace-nowrap text-center" style="width: 80px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedLogs as $idx => $group)
                                    @php
                                        $soLineCode = '000' . ($idx + 1);
                                        $latestLog = $group->latest_log;
                                        $firstDate = $group->logs->min('created_at');
                                        $latestDate = $group->latest_sent_at;
                                        $hasGraph = in_array('microsoft_graph', $group->channels);
                                        $hasSmtp = in_array('smtp', $group->channels);
                                        $successPct = $group->total_count > 0 ? round(($group->sent_count / $group->total_count) * 100) : 0;
                                        $rowSearchString = strtolower($group->position_name . ' ' . $group->logs->pluck('recipient_name')->join(' ') . ' ' . $group->logs->pluck('recipient_email')->join(' ') . ' ' . $group->logs->pluck('subject')->join(' '));
                                    @endphp
                                    {{-- Parent Position Row --}}
                                    <tr id="group-parent-{{ $idx }}"
                                        class="group-parent-row border-b border-slate-100 dark:border-slate-800 select-none"
                                        onclick="toggleGroupDetail({{ $idx }}, event)"
                                        data-search="{{ $rowSearchString }}"
                                        data-sent="{{ $group->sent_count }}"
                                        data-failed="{{ $group->failed_count }}"
                                        data-queued="{{ $group->queued_count }}"
                                        data-has-graph="{{ $hasGraph ? '1' : '0' }}"
                                        data-has-smtp="{{ $hasSmtp ? '1' : '0' }}"
                                        data-mail-types="{{ $group->logs->pluck('mail_type')->filter()->unique()->join(',') }}">
                                        
                                        <!-- 1. SO / ตำแหน่งงาน (Image 1 style: Bold Title + subtext) -->
                                        <td class="font-medium">
                                            <div class="flex items-center gap-2.5">
                                                <div class="size-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0 border border-slate-200/80 dark:border-slate-700/80">
                                                    <i class="fa-solid fa-briefcase text-xs text-kumwell-red"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 dark:text-white truncate max-w-[200px]" title="{{ $group->position_name }}">
                                                        {{ $group->position_name }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-400 truncate">
                                                        {{ $group->recipients_count }} ผู้สมัครบันทึก
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 2. SO_LINE -->
                                        <td class="text-center font-mono text-xs text-slate-500 dark:text-slate-400">
                                            {{ $soLineCode }}
                                        </td>

                                        <!-- 3. CUS_ITEM (Latest subject / details) -->
                                        <td class="max-w-[260px]">
                                            <div class="truncate font-medium text-slate-800 dark:text-slate-200 text-xs" title="{{ $latestLog?->subject }}">
                                                {{ $latestLog?->subject ?: '-' }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 truncate">
                                                {{ $latestLog?->recipient_name ?: '-' }} ({{ $latestLog?->recipient_email }})
                                            </div>
                                        </td>

                                        <!-- 4. ITEM / จำนวนผู้รับ -->
                                        <td class="text-center font-semibold text-slate-700 dark:text-slate-300">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs">
                                                <i class="fa-regular fa-user text-[10px]"></i> {{ $group->recipients_count }} ท่าน
                                            </span>
                                        </td>

                                        <!-- 5. PROD_ITEM / ช่องทาง -->
                                        <td class="text-center whitespace-nowrap">
                                            @if($hasGraph && $hasSmtp)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300">
                                                    Graph + SMTP
                                                </span>
                                            @elseif($hasGraph)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300">
                                                    <i class="fa-brands fa-microsoft text-[10px]"></i> Graph API
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300">
                                                    <i class="fa-solid fa-server text-[9px]"></i> SMTP
                                                </span>
                                            @endif
                                        </td>

                                        <!-- 6. SIZE / วันที่ส่งล่าสุด -->
                                        <td class="text-center whitespace-nowrap text-xs text-slate-600 dark:text-slate-400">
                                            @if($latestDate)
                                                <div>{{ \Carbon\Carbon::parse($latestDate)->copy()->addYears(543)->format('d/m/Y') }}</div>
                                                <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($latestDate)->format('H:i:s') }} น.</div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <!-- 7. สถานะภาพรวม (Badges for Sent/Failed/Queued) -->
                                        <td class="text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1">
                                                @if($group->sent_count > 0)
                                                    <span class="badge badge-green text-[11px] py-0.5 px-2" title="ส่งสำเร็จ {{ $group->sent_count }} รายการ">
                                                        <i class="fa-solid fa-check text-[10px]"></i> {{ $group->sent_count }}
                                                    </span>
                                                @endif
                                                @if($group->failed_count > 0)
                                                    <span class="badge badge-red text-[11px] py-0.5 px-2" title="ล้มเหลว {{ $group->failed_count }} รายการ">
                                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> {{ $group->failed_count }}
                                                    </span>
                                                @endif
                                                @if($group->queued_count > 0)
                                                    <span class="badge badge-yellow text-[11px] py-0.5 px-2" title="รอในคิว {{ $group->queued_count }} รายการ">
                                                        <i class="fa-solid fa-clock text-[10px]"></i> {{ $group->queued_count }}
                                                    </span>
                                                @endif
                                                @if($group->total_count === 0)
                                                    <span class="text-slate-400 text-xs">-</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- 8. PROD_PCS_QTY (จำนวนอีเมลรวม) -->
                                        <td class="text-right font-black text-slate-900 dark:text-white font-mono text-sm pr-4">
                                            {{ number_format($group->total_count) }}
                                        </td>

                                        <!-- 9. จัดการ (Image 1 Style: [✏️] [v] Action Box) -->
                                        <td class="text-center whitespace-nowrap">
                                            <div class="action-box-erp" onclick="event.stopPropagation()">
                                                <!-- Action 1: Toggle details -->
                                                <button type="button"
                                                    onclick="toggleGroupDetail({{ $idx }}, event)"
                                                    title="เปิดดูรายละเอียดสเปกและประวัติย่อย">
                                                    <i class="fa-solid fa-chevron-down group-chevron" id="chevron-{{ $idx }}"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Expandable Child Detail Row (Matching รูปที่ 1 3-column specs + CONTENT Sub-table) --}}
                                    <tr id="group-detail-{{ $idx }}" class="group-detail-row hidden bg-slate-50/70 dark:bg-slate-900/60 border-b-2 border-slate-200 dark:border-slate-700">
                                        <td colspan="9" class="p-0">
                                            <div id="group-content-{{ $idx }}" class="p-4 sm:p-6 space-y-6">

                                                <!-- Top 3-Column Specifications Grid (Matching รูปที่ 1) -->
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs">
                                                    
                                                    <!-- Column 1: 🏷️ ข้อมูลตำแหน่งและผู้สมัคร (Title in Red) -->
                                                    <div class="space-y-2.5">
                                                        <div class="text-rose-600 dark:text-rose-400 font-bold text-xs uppercase tracking-wide flex items-center gap-1.5 pb-2 border-b border-rose-100 dark:border-rose-900/40">
                                                            <i class="fa-solid fa-tag text-kumwell-red"></i>
                                                            <span>ข้อมูลตำแหน่งและผู้สมัคร</span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">DESCRIPTION</span>
                                                            <span class="erp-spec-value font-bold text-slate-900 dark:text-white">{{ $group->position_name }}</span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">HPL_DESC</span>
                                                            <span class="erp-spec-value text-slate-600 dark:text-slate-300">ตำแหน่งงาน recruitment / สมัครงาน</span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">CANDIDATES</span>
                                                            <span class="erp-spec-value">{{ $group->recipients_count }} รายชื่อผู้รับ</span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">LATEST_APPLICANT</span>
                                                            <span class="erp-spec-value truncate" title="{{ $latestLog?->recipient_name }} ({{ $latestLog?->recipient_email }})">
                                                                {{ $latestLog?->recipient_name ?: '-' }}
                                                            </span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">SENDER_BY</span>
                                                            <span class="erp-spec-value text-slate-500">
                                                                {{ $latestLog?->user?->fullname ?: 'ระบบอัตโนมัติ' }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Column 2: 🖨️ สเปกและบรรจุภัณฑ์ / ช่องทางการส่ง (Title in Red) -->
                                                    <div class="space-y-2.5">
                                                        <div class="text-rose-600 dark:text-rose-400 font-bold text-xs uppercase tracking-wide flex items-center gap-1.5 pb-2 border-b border-rose-100 dark:border-rose-900/40">
                                                            <i class="fa-solid fa-server text-kumwell-red"></i>
                                                            <span>สเปกและช่องทางการส่ง</span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">CHANNEL_TYPE</span>
                                                            <span class="erp-spec-value font-semibold">
                                                                {{ $hasGraph ? 'Microsoft Graph API' : 'SMTP Server' }}
                                                            </span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">GATEWAY_STATUS</span>
                                                            <span class="erp-spec-value text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                                                <span class="size-1.5 rounded-full bg-emerald-500"></span> พร้อมใช้งาน
                                                            </span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">SUCCESS_RATE</span>
                                                            <span class="erp-spec-value font-bold {{ $successPct >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                                                {{ $successPct }}% ({{ $group->sent_count }}/{{ $group->total_count }})
                                                            </span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">QUEUED_MAILS</span>
                                                            <span class="erp-spec-value font-mono">{{ $group->queued_count }} ฉบับ</span>
                                                        </div>

                                                        <div class="flex items-baseline text-xs">
                                                            <span class="erp-spec-label">FAILED_MAILS</span>
                                                            <span class="erp-spec-value font-mono {{ $group->failed_count > 0 ? 'text-rose-600 font-bold' : '' }}">
                                                                {{ $group->failed_count }} ฉบับ
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Column 3: 📅 วันที่และแผนการผลิต / ประวัติการส่ง (Title in Red) -->
                                                    <div class="space-y-2.5 flex flex-col justify-between">
                                                        <div class="space-y-2.5">
                                                            <div class="text-rose-600 dark:text-rose-400 font-bold text-xs uppercase tracking-wide flex items-center gap-1.5 pb-2 border-b border-rose-100 dark:border-rose-900/40">
                                                                <i class="fa-solid fa-calendar-check text-kumwell-red"></i>
                                                                <span>วันที่และประวัติการส่ง</span>
                                                            </div>

                                                            <div class="flex items-baseline text-xs">
                                                                <span class="erp-spec-label">BOOKING_DATE</span>
                                                                <span class="erp-spec-value">
                                                                    {{ $firstDate ? \Carbon\Carbon::parse($firstDate)->copy()->addYears(543)->format('d/m/Y H:i') . ' น.' : '-' }}
                                                                </span>
                                                            </div>

                                                            <div class="flex items-baseline text-xs">
                                                                <span class="erp-spec-label">LATEST_SENT</span>
                                                                <span class="erp-spec-value">
                                                                    {{ $latestDate ? \Carbon\Carbon::parse($latestDate)->copy()->addYears(543)->format('d/m/Y H:i') . ' น.' : '-' }}
                                                                </span>
                                                            </div>

                                                            <div class="flex items-baseline text-xs">
                                                                <span class="erp-spec-label">CONFIRM_STATUS</span>
                                                                <span class="erp-spec-value">
                                                                    @if($group->failed_count == 0 && $group->queued_count == 0)
                                                                        <span class="text-emerald-600 font-semibold">ส่งสำเร็จครบถ้วน</span>
                                                                    @elseif($group->failed_count > 0)
                                                                        <span class="text-rose-600 font-semibold">พบข้อผิดพลาดบางรายการ</span>
                                                                    @else
                                                                        <span class="text-amber-600 font-semibold">กำลังรอ Worker ประมวลผล</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Action button matching Image 1: [ ✏️ แก้ไขแผนการผลิต ] -->
                                                        <div class="pt-3 flex justify-end">
                                                            @if($group->failed_count > 0 || $group->queued_count > 0)
                                                                @php
                                                                    $pendingLog = $group->logs->first(fn($l) => in_array($l->status, ['failed', 'queued']));
                                                                @endphp
                                                                @if($pendingLog)
                                                                    <form action="{{ route('backend.recruitment.mail-logs.retry', $pendingLog->id) }}" method="POST" class="m-0">
                                                                        @csrf
                                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-900/60 border border-amber-300 dark:border-amber-700/80 rounded-xl text-xs font-semibold shadow-2xs transition-colors">
                                                                            <i class="fa-solid fa-rotate-right text-xs"></i>
                                                                            <span>ส่งซ้ำรายการที่ค้าง (#LOG-{{ str_pad($pendingLog->id, 5, '0', STR_PAD_LEFT) }})</span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @else
                                                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-700/40 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                                                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                                                    <span>ข้อมูลการส่งสมบูรณ์</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CONTENT Banner (Matching Image 1: [📄 CONTENT: Combine only 1 shipment...]) -->
                                                <div class="flex items-center justify-between px-4 py-2.5 bg-slate-200/70 dark:bg-slate-800/90 rounded-xl text-xs text-slate-700 dark:text-slate-300 font-semibold border border-slate-300/50 dark:border-slate-700">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fa-regular fa-file-lines text-kumwell-red"></i>
                                                        <span>CONTENT: รายการประวัติการส่งอีเมลในตำแหน่งนี้ ({{ $group->total_count }} รายการ)</span>
                                                    </div>
                                                    <span class="text-[11px] font-normal text-slate-500 dark:text-slate-400">
                                                        คลิกที่เมนูจัดการเพื่อดูข้อผิดพลาด หรือกดส่งซ้ำ
                                                    </span>
                                                </div>

                                                <!-- Sub-table: All Mails for this Position (Image 1 Nested Table) -->
                                                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-2xs">
                                                    <table class="w-full text-left text-xs">
                                                        <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-bold text-[11px]">
                                                            <tr>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-left" style="width: 80px;">ID</th>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-center" style="width: 110px;">วันที่ส่ง</th>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-left">ผู้รับ (Recipient)</th>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-left">หัวข้ออีเมล</th>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-center" style="width: 120px;">ประเภท</th>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-center" style="width: 100px;">ช่องทาง</th>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-center" style="width: 100px;">สถานะ</th>
                                                                <th class="py-2.5 px-3 whitespace-nowrap text-center" style="width: 60px;">จัดการ</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                                            @foreach($group->logs as $subIdx => $subLog)
                                                                @php
                                                                    $subDisplayCode = 'LOG-' . str_pad($subLog->id, 5, '0', STR_PAD_LEFT);
                                                                @endphp
                                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-sub-mailtype="{{ $subLog->mail_type }}">
                                                                    <!-- ID -->
                                                                    <td class="py-2.5 px-3 font-mono font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                                                        #{{ $subDisplayCode }}
                                                                    </td>

                                                                    <!-- วันที่ส่ง -->
                                                                    <td class="py-2.5 px-3 text-center whitespace-nowrap text-slate-600 dark:text-slate-400">
                                                                        <div>{{ $subLog->created_at ? $subLog->created_at->copy()->addYears(543)->format('d/m/Y') : '-' }}</div>
                                                                        @if($subLog->sent_at)
                                                                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400">{{ $subLog->sent_at->format('H:i:s') }} น.</div>
                                                                        @endif
                                                                    </td>

                                                                    <!-- ผู้รับ -->
                                                                    <td class="py-2.5 px-3 whitespace-nowrap">
                                                                        <div class="font-medium text-slate-900 dark:text-slate-100">
                                                                            {{ $subLog->recipient_name ?: '-' }}
                                                                        </div>
                                                                        <a href="mailto:{{ $subLog->recipient_email }}" class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline block truncate max-w-[200px]" title="{{ $subLog->recipient_email }}">
                                                                            {{ $subLog->recipient_email }}
                                                                        </a>
                                                                    </td>

                                                                    <!-- หัวข้ออีเมล -->
                                                                    <td class="py-2.5 px-3">
                                                                        <div class="font-medium text-slate-900 dark:text-slate-100 line-clamp-1 max-w-sm" title="{{ $subLog->subject }}">
                                                                            {{ $subLog->subject }}
                                                                        </div>
                                                                        <div class="text-[10px] text-slate-400 truncate">
                                                                            ผู้ส่ง: {{ $subLog->user?->fullname ?: 'ระบบอัตโนมัติ' }}
                                                                        </div>
                                                                    </td>

                                                                    <!-- ประเภทอีเมล -->
                                                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold border shadow-2xs {{ $subLog->mail_type_badge }}">
                                                                            <i class="{{ $subLog->mail_type_icon }} text-[9px]"></i>
                                                                            <span>{{ $subLog->mail_type_label }}</span>
                                                                        </span>
                                                                    </td>

                                                                    <!-- ช่องทาง -->
                                                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                                                        @if($subLog->channel === 'microsoft_graph')
                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300">
                                                                                <i class="fa-brands fa-microsoft"></i> Graph
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300">
                                                                                <i class="fa-solid fa-server"></i> SMTP
                                                                            </span>
                                                                        @endif
                                                                    </td>

                                                                    <!-- สถานะ -->
                                                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                                                        @if($subLog->status === 'sent')
                                                                            <span class="badge badge-green text-[10px] py-0.5 px-2">ส่งสำเร็จ</span>
                                                                        @elseif($subLog->status === 'failed')
                                                                            <span class="badge badge-red text-[10px] py-0.5 px-2">ล้มเหลว</span>
                                                                        @else
                                                                            <span class="badge badge-yellow text-[10px] py-0.5 px-2">รอในคิว</span>
                                                                        @endif
                                                                    </td>

                                                                    <!-- จัดการ -->
                                                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                                                        <div class="relative inline-block text-left">
                                                                            <button type="button"
                                                                                class="action-dropdown-btn"
                                                                                onclick="toggleActionMenu(this, 'menu-sub-{{ $subLog->id }}')"
                                                                                title="ตัวเลือก">
                                                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                            </button>

                                                                            <div id="menu-sub-{{ $subLog->id }}" class="action-dropdown-menu hidden text-xs">
                                                                                @if($subLog->error_message)
                                                                                    <button type="button"
                                                                                        onclick="showErrorModal('{{ addslashes($subLog->recipient_email) }}', '{{ addslashes($subLog->subject) }}', '{{ addslashes($subLog->error_message) }}')"
                                                                                        class="flex items-center gap-2 w-full px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-md transition-colors text-left">
                                                                                        <i class="fa-solid fa-circle-exclamation w-3.5 text-center"></i>
                                                                                        <span>ดูข้อผิดพลาด</span>
                                                                                    </button>
                                                                                @endif

                                                                                @if(in_array($subLog->status, ['failed', 'queued']))
                                                                                    <form action="{{ route('backend.recruitment.mail-logs.retry', $subLog->id) }}" method="POST" class="m-0">
                                                                                        @csrf
                                                                                        <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-md transition-colors text-left">
                                                                                            <i class="fa-solid fa-rotate-right w-3.5 text-center"></i>
                                                                                            <span>ส่งทันที / ส่งซ้ำ</span>
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                <a href="mailto:{{ $subLog->recipient_email }}" class="flex items-center gap-2 w-full px-3 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md transition-colors text-left">
                                                                                    <i class="fa-regular fa-envelope w-3.5 text-center text-slate-400"></i>
                                                                                    <span>ส่งอีเมลหาผู้รับ</span>
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-12 text-center text-slate-400">
                                            <i class="fa-regular fa-folder-open text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                                            ไม่พบข้อมูลกลุ่มตำแหน่งงาน
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ──────────────────────────────────────────
                     VIEW 2 : FLAT TABLE (รูปที่ 2)
                ────────────────────────────────────────── --}}
                <div id="flatViewContainer" class="hidden">
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
                                    <!-- 1. ประเภทอีเมล -->
                                    <td class="text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border shadow-2xs {{ $log->mail_type_badge }}">
                                            <i class="{{ $log->mail_type_icon }} text-[10px]"></i>
                                            <span>{{ $log->mail_type_label }}</span>
                                        </span>
                                    </td>

                                    <!-- 2. ID -->
                                    <td data-order="{{ $log->id }}" class="text-left font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        <span class="font-mono text-xs text-indigo-600 dark:text-indigo-400">
                                            #{{ $displayLogCode }}
                                        </span>
                                    </td>

                                    <!-- 3. วันที่ส่ง -->
                                    <td data-order="{{ $log->created_at ? $log->created_at->timestamp : 0 }}" class="text-center whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-xs text-slate-400"></i>
                                            <span>{{ $log->created_at ? $log->created_at->copy()->addYears(543)->format('d/m/Y') : '-' }}</span>
                                        </div>
                                        @if($log->sent_at)
                                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400">{{ $log->sent_at->format('H:i:s') }} น.</div>
                                        @endif
                                    </td>

                                    <!-- 4. ผู้รับ -->
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
                                                <i class="fa-solid fa-briefcase text-xs text-slate-400"></i>
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

                                    <!-- 7. ช่องทาง -->
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

                                    <!-- 8. สถานะ -->
                                    <td class="text-center whitespace-nowrap">
                                        @if($log->status === 'sent')
                                            <span class="badge badge-green shadow-2xs">
                                                <i class="fa-solid fa-check text-xs"></i>
                                                <span>ส่งสำเร็จ</span>
                                            </span>
                                        @elseif($log->status === 'failed')
                                            <span class="badge badge-red shadow-2xs">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                                <span>ล้มเหลว</span>
                                            </span>
                                        @else
                                            <span class="badge badge-yellow shadow-2xs">
                                                <i class="fa-solid fa-clock text-xs"></i>
                                                <span>รอในคิว</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- 9. จัดการ -->
                                    <td class="text-center whitespace-nowrap">
                                        <div class="relative inline-block text-left">
                                            <button type="button"
                                                class="action-dropdown-btn"
                                                onclick="toggleActionMenu(this, 'menu-flat-{{ $log->id }}')"
                                                title="ตัวเลือกเพิ่มเติม">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div id="menu-flat-{{ $log->id }}" class="action-dropdown-menu hidden text-xs">
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

                let left = rect.right - menuWidth;
                if (left < 10) left = 10;
                menu.style.left = left + 'px';

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

        // ── Expandable Group Detail Toggle (Matching Image 1) ─────────────
        window.toggleGroupDetail = function(idx, event) {
            if (event) {
                // If user clicked directly on a link or button, do not toggle accordion
                if (event.target.closest('a') || event.target.closest('form')) {
                    return;
                }
            }

            const detailRow = document.getElementById(`group-detail-${idx}`);
            const chevron = document.getElementById(`chevron-${idx}`);
            const parentRow = document.getElementById(`group-parent-${idx}`);
            const contentDiv = document.getElementById(`group-content-${idx}`);

            if (!detailRow) return;

            const isCurrentlyHidden = detailRow.classList.contains('hidden');

            if (isCurrentlyHidden) {
                // Open row
                detailRow.classList.remove('hidden');
                if (window.$ && contentDiv) {
                    $(contentDiv).hide().slideDown(220);
                }
                if (chevron) chevron.classList.add('is-open');
                if (parentRow) parentRow.classList.add('is-active');
            } else {
                // Close row
                if (window.$ && contentDiv) {
                    $(contentDiv).slideUp(180, function() {
                        detailRow.classList.add('hidden');
                    });
                } else {
                    detailRow.classList.add('hidden');
                }
                if (chevron) chevron.classList.remove('is-open');
                if (parentRow) parentRow.classList.remove('is-active');
            }
        };

        // Expand All Groups
        window.expandAllGroups = function() {
            document.querySelectorAll('.group-detail-row').forEach(function(row) {
                row.classList.remove('hidden');
                const content = row.querySelector('[id^="group-content-"]');
                if (content && window.$) $(content).show();
            });
            document.querySelectorAll('.group-chevron').forEach(function(ch) {
                ch.classList.add('is-open');
            });
            document.querySelectorAll('.group-parent-row').forEach(function(pr) {
                pr.classList.add('is-active');
            });
        };

        // Collapse All Groups
        window.collapseAllGroups = function() {
            document.querySelectorAll('.group-detail-row').forEach(function(row) {
                row.classList.add('hidden');
            });
            document.querySelectorAll('.group-chevron').forEach(function(ch) {
                ch.classList.remove('is-open');
            });
            document.querySelectorAll('.group-parent-row').forEach(function(pr) {
                pr.classList.remove('is-active');
            });
        };

        // Switch View Modes (Grouped vs Flat)
        window.switchViewMode = function(mode) {
            const groupedView = document.getElementById('groupedViewContainer');
            const flatView = document.getElementById('flatViewContainer');
            const btnGrouped = document.getElementById('btnModeGrouped');
            const btnFlat = document.getElementById('btnModeFlat');
            const expandControls = document.getElementById('groupedExpandControls');

            if (mode === 'grouped') {
                groupedView.classList.remove('hidden');
                flatView.classList.add('hidden');
                if (expandControls) expandControls.classList.remove('hidden');

                btnGrouped.className = "px-3 py-1.5 rounded-lg transition-all shadow-xs bg-white dark:bg-slate-700 text-kumwell-red dark:text-white font-bold flex items-center gap-2";
                btnFlat.className = "px-3 py-1.5 rounded-lg transition-all text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center gap-2";
            } else {
                groupedView.classList.add('hidden');
                flatView.classList.remove('hidden');
                if (expandControls) expandControls.classList.add('hidden');

                btnFlat.className = "px-3 py-1.5 rounded-lg transition-all shadow-xs bg-white dark:bg-slate-700 text-kumwell-red dark:text-white font-bold flex items-center gap-2";
                btnGrouped.className = "px-3 py-1.5 rounded-lg transition-all text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center gap-2";
            }
        };

        // ── DataTables & Filtering Logic ──────────────────────────────
        $(document).ready(function() {
            let flatTable = null;
            const dtOptions = {
                dom: '<"overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs"t><"dataTables_bottom_bar"ip>',
                autoWidth: false,
                responsive: false,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "ทั้งหมด"]],
                order: [[2, 'desc'], [1, 'desc']],
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
                    flatTable = new window.DataTable('#dataTableMailLogs', dtOptions);
                } else if (window.$ && typeof window.$.fn.DataTable === 'function') {
                    flatTable = $('#dataTableMailLogs').DataTable(dtOptions);
                }
            } catch (err) {
                console.warn('DataTable init notice:', err);
            }

            // Grouped view filtering logic
            function filterGroupedView() {
                const keyword = ($('#customSearch').val() || '').toLowerCase().trim();
                const status = $('#filterStatus').val();
                const mailType = $('#filterMailType').val();
                const channel = $('#filterChannel').val();

                let visibleCount = 0;

                $('.group-parent-row').each(function() {
                    const row = $(this);
                    const idx = row.attr('id').replace('group-parent-', '');
                    const detailRow = $(`#group-detail-${idx}`);

                    const searchData = row.attr('data-search') || '';
                    const sentCount = parseInt(row.attr('data-sent') || '0', 10);
                    const failedCount = parseInt(row.attr('data-failed') || '0', 10);
                    const queuedCount = parseInt(row.attr('data-queued') || '0', 10);
                    const hasGraph = row.attr('data-has-graph') === '1';
                    const hasSmtp = row.attr('data-has-smtp') === '1';
                    const mailTypes = (row.attr('data-mail-types') || '').split(',');

                    let match = true;

                    // Search keyword
                    if (keyword && !searchData.includes(keyword)) {
                        match = false;
                    }

                    // Status
                    if (match && status) {
                        if (status === 'sent' && sentCount === 0) match = false;
                        if (status === 'failed' && failedCount === 0) match = false;
                        if (status === 'queued' && queuedCount === 0) match = false;
                    }

                    // Mail Type
                    if (match && mailType) {
                        if (!mailTypes.includes(mailType)) match = false;
                    }

                    // Channel
                    if (match && channel) {
                        if (channel === 'microsoft_graph' && !hasGraph) match = false;
                        if (channel === 'smtp' && !hasSmtp) match = false;
                    }

                    if (match) {
                        row.show();
                        visibleCount++;

                        if (mailType) {
                            detailRow.find('tr[data-sub-mailtype]').each(function() {
                                const subRow = $(this);
                                if (subRow.attr('data-sub-mailtype') === mailType) {
                                    subRow.show();
                                } else {
                                    subRow.hide();
                                }
                            });
                        } else {
                            detailRow.find('tr[data-sub-mailtype]').show();
                        }
                    } else {
                        row.hide();
                        detailRow.addClass('hidden');
                        $(`#chevron-${idx}`).removeClass('is-open');
                        row.removeClass('is-active');
                    }
                });

                $('#groupedSummaryBadge').text(`${visibleCount} ตำแหน่ง`);
            }

            // Flat Table filtering logic
            function filterFlatTable() {
                if (!flatTable) return;

                const statusVal = $('#filterStatus').val();
                const mailTypeVal = $('#filterMailType').val();
                const channelVal = $('#filterChannel').val();

                const filterFn = function(settings, data, dataIndex) {
                    const row = $(flatTable.row(dataIndex).node());
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

                flatTable.draw();
            }

            // Universal event bindings
            $('#customSearch').on('keyup', function() {
                filterGroupedView();
                if (flatTable) {
                    flatTable.search(this.value).draw();
                }
            });

            $('#filterStatus, #filterChannel, #filterMailType').on('change', function() {
                filterGroupedView();
                filterFlatTable();
            });

            // Reset filters
            $('#btnResetFilters').on('click', function() {
                $('#customSearch').val('');
                $('#filterStatus').val('');
                $('#filterMailType').val('');
                $('#filterChannel').val('');
                
                filterGroupedView();

                if (flatTable) {
                    flatTable.search('');
                    if (window.DataTable && window.DataTable.ext) {
                        window.DataTable.ext.search = [];
                    } else if (window.$.fn && window.$.fn.dataTable) {
                        window.$.fn.dataTable.ext.search = [];
                    }
                    flatTable.draw();
                }
            });
        });
    </script>
@endsection
