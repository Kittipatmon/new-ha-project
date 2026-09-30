@extends('layouts.app')

@section('title')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        .dataTables_wrapper {
            font-size: 0.8125rem;
            color: inherit;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            padding: 0.875rem 1.25rem;
            margin-bottom: 0;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.35rem 2rem 0.35rem 0.75rem;
            font-size: 0.75rem;
            background-color: #fff;
        }
        .dark .dataTables_wrapper .dataTables_length select {
            background-color: #0f172a;
            border-color: #334155;
            color: #fff;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            margin-left: 0.5rem;
            background-color: #fff;
        }
        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #0f172a;
            border-color: #334155;
            color: #fff;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 0.875rem 1.25rem;
            font-size: 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.375rem !important;
            padding: 0.25rem 0.65rem !important;
            font-size: 0.75rem !important;
            border: 1px solid transparent !important;
            transition: all 0.15s;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            color: #fff !important;
            border-color: #4f46e5 !important;
            font-weight: 700;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e0e7ff !important;
            color: #3730a3 !important;
            border-color: #c7d2fe !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #312e81 !important;
            color: #c7d2fe !important;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #e2e8f0;
        }
        .dark table.dataTable.no-footer {
            border-bottom: 1px solid #1e293b;
        }
        table.dataTable thead th {
            border-bottom: 1px solid #e2e8f0 !important;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 0.875rem 1rem !important;
            background-color: #f8fafc;
        }
        .dark table.dataTable thead th {
            background-color: #17191e;
            border-color: #1e293b !important;
            color: #94a3b8;
        }
        table.dataTable tbody td {
            padding: 0.75rem 1rem !important;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
        }
        .dark table.dataTable tbody td {
            border-color: #1e293b;
        }
        table.dataTable tbody tr:hover {
            background-color: rgba(241, 245, 249, 0.6) !important;
        }
        .dark table.dataTable tbody tr:hover {
            background-color: rgba(30, 41, 59, 0.4) !important;
        }
        /* Real-time flash highlight */
        @keyframes flashRow {
            0% { background-color: rgba(16, 185, 129, 0.35); }
            100% { background-color: transparent; }
        }
        .row-flash {
            animation: flashRow 3.5s ease-out;
        }
        table.dataTable tbody td.col-description,
        table.dataTable thead th.col-description {
            width: 320px !important;
            min-width: 260px !important;
            max-width: 360px !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        .desc-box {
            width: 100%;
            max-width: 340px;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6" x-data="auditLogApp()" x-init="initApp()">

    <!-- ============================================================== -->
    <!-- Page Header & Action Bar -->
    <!-- ============================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-[#1E2129] p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200/60 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span>ประวัติกิจกรรมระบบ (System Audit Logs)</span>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            DataTable • Realtime
                        </span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        บันทึกการ เพิ่ม ลบ แก้ไข ข้อมูลจริงในระบบแบบเรียลไทม์ พร้อมดูค่าเก่า-ใหม่ และคลังบีบอัด ZIP 5 ปีสำหรับ Auditor
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tab Switcher -->
            <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
                <button type="button" @click="activeTab = 'logs'"
                    :class="activeTab === 'logs' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="py-2 px-3 rounded-lg transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check"></i>
                    <span>บันทึกกิจกรรม (DataTable)</span>
                </button>
                <button type="button" @click="activeTab = 'archives'"
                    :class="activeTab === 'archives' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="py-2 px-3 rounded-lg transition flex items-center gap-1.5">
                    <i class="fa-solid fa-box-archive"></i>
                    <span>คลังไฟล์บีบอัด (ZIP)</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300"
                        x-text="metrics.archives">
                        {{ $metrics['archives'] }}
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- KPI Summary Metrics Cards (Real-time Live Sync) -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
        <!-- Total Logs -->
        <div class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-transform duration-200">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-medium mb-1">
                <span>บันทึกทั้งหมด</span>
                <i class="fa-solid fa-database text-slate-400"></i>
            </div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white" x-text="Number(metrics.total).toLocaleString()">
                {{ number_format($metrics['total']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">ข้อมูลจริงในฐานข้อมูล</div>
        </div>

        <!-- Created -->
        <div class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-emerald-100 dark:border-emerald-950/40 shadow-sm bg-gradient-to-br from-emerald-50/40 via-transparent to-transparent">
            <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 text-xs font-medium mb-1">
                <span>เพิ่มข้อมูล (Created)</span>
                <i class="fa-solid fa-plus-circle text-emerald-500"></i>
            </div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400" x-text="Number(metrics.created).toLocaleString()">
                {{ number_format($metrics['created']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">การสร้างรายการใหม่</div>
        </div>

        <!-- Updated -->
        <div class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-amber-100 dark:border-amber-950/40 shadow-sm bg-gradient-to-br from-amber-50/40 via-transparent to-transparent">
            <div class="flex items-center justify-between text-amber-600 dark:text-amber-400 text-xs font-medium mb-1">
                <span>แก้ไขข้อมูล (Updated)</span>
                <i class="fa-solid fa-pen-to-square text-amber-500"></i>
            </div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400" x-text="Number(metrics.updated).toLocaleString()">
                {{ number_format($metrics['updated']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">บันทึกการเปลี่ยนแปลง</div>
        </div>

        <!-- Deleted -->
        <div class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-rose-100 dark:border-rose-950/40 shadow-sm bg-gradient-to-br from-rose-50/40 via-transparent to-transparent">
            <div class="flex items-center justify-between text-rose-600 dark:text-rose-400 text-xs font-medium mb-1">
                <span>ลบข้อมูล (Deleted)</span>
                <i class="fa-solid fa-trash-can text-rose-500"></i>
            </div>
            <div class="text-2xl font-bold text-rose-600 dark:text-rose-400" x-text="Number(metrics.deleted).toLocaleString()">
                {{ number_format($metrics['deleted']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">รายการที่ถูกลบออก</div>
        </div>

        <!-- Archives -->
        <div class="col-span-2 sm:col-span-1 bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-purple-100 dark:border-purple-950/40 shadow-sm bg-gradient-to-br from-purple-50/40 via-transparent to-transparent">
            <div class="flex items-center justify-between text-purple-600 dark:text-purple-400 text-xs font-medium mb-1">
                <span>ไฟล์คลัง (ZIP Archives)</span>
                <i class="fa-solid fa-file-zipper text-purple-500"></i>
            </div>
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" x-text="Number(metrics.archives).toLocaleString()">
                {{ number_format($metrics['archives']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">บีบอัดเก็บ 5 ปีสำหรับ Audit</div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: ACTIVE AUDIT LOGS (DATATABLE + REALTIME LIVE FEED) -->
    <!-- ============================================================== -->
    <div x-show="activeTab === 'logs'" class="space-y-4">

        <!-- DataTable Container Card -->
        <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            
            <!-- Real-time Live Control Header -->
            <div class="p-3.5 sm:p-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 bg-slate-50/60 dark:bg-slate-850/40">
                <div class="flex items-center flex-wrap gap-2.5">
                    <!-- Live indicator pulse -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold transition"
                        :class="isLive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700'">
                        <span class="relative flex h-2 w-2" x-show="isLive">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="w-2 h-2 rounded-full bg-slate-400" x-show="!isLive"></span>
                        <span x-text="isLive ? 'Real-time Live (อัปเดตสดทุก 5 วิ)' : 'โหมด Real-time พักไว้'"></span>
                    </div>

                    <button type="button" @click="toggleLive()"
                        class="px-2.5 py-1 text-xs rounded-lg border font-medium transition"
                        :class="isLive ? 'border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300' : 'border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400'">
                        <i class="fa-solid" :class="isLive ? 'fa-pause' : 'fa-play'"></i>
                        <span x-text="isLive ? 'หยุดชั่วคราว' : 'เปิดโหมดสด'"></span>
                    </button>

                    <span class="text-[11px] text-slate-400 hidden md:inline" x-text="'ซิงก์ข้อมูลล่าสุด: ' + lastSyncedTime"></span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="manualRefresh()" :disabled="isRefreshing"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 shadow-sm transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': isRefreshing }"></i>
                        <span>รีเฟรชข้อมูล</span>
                    </button>
                </div>
            </div>

            <!-- DataTable Component -->
            <div class="p-2 sm:p-3 overflow-x-auto">
                <table id="auditLogsDataTable" class="display responsive nowrap w-full text-left text-xs">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">#</th>
                            <th class="w-44">วันเวลาที่กระทำ (ไทย)</th>
                            <th class="w-48">ผู้ดำเนินการ</th>
                            <th class="w-36">ประเภทกิจกรรม</th>
                            <th class="w-44">ระบบ / โมดูล</th>
                            <th class="col-description" style="width: 320px; max-width: 360px;">คำอธิบายและรายละเอียด</th>
                            <th class="w-32">IP / วิธี</th>
                            <th class="w-28 text-center">ดูค่าเก่า-ใหม่</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr id="log-row-{{ $log->id }}">
                                <!-- ID -->
                                <td class="text-center font-mono text-slate-400">
                                    {{ $log->id }}
                                </td>

                                <!-- Timestamp in Thai format -->
                                <td class="whitespace-nowrap" data-order="{{ $log->created_at->timestamp }}">
                                    <div class="font-semibold text-slate-800 dark:text-white flex items-center gap-1.5">
                                        <i class="fa-regular fa-calendar text-indigo-500 text-[11px]"></i>
                                        <span>{{ $log->thai_date }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                        <span>{{ $log->thai_time }}</span>
                                    </div>
                                </td>

                                <!-- Actor -->
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-[11px] text-slate-600 dark:text-slate-300 shrink-0">
                                            {{ mb_substr($log->user_name ?: 'S', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100 truncate" title="{{ $log->user_name }}">
                                                {{ $log->user_name ?: 'ระบบอัตโนมัติ' }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">
                                                {{ $log->user_code ?: ($log->user_role ?: '-') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Action Badge -->
                                <td class="whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold border {{ $log->getActionBadgeClass() }}">
                                        <i class="{{ $log->getActionIcon() }}"></i>
                                        <span>{{ $log->getActionLabel() }}</span>
                                    </span>
                                </td>

                                <!-- Module -->
                                <td class="whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $log->module_name ?: ucfirst($log->module) }}
                                    </span>
                                </td>

                                <!-- Description -->
                                <td class="col-description">
                                    <div class="desc-box font-medium text-slate-800 dark:text-slate-100 text-xs">
                                        @if(mb_strlen($log->description) > 55)
                                            <div class="desc-preview flex items-center justify-between gap-1.5">
                                                <span class="truncate text-slate-800 dark:text-slate-100" style="max-width: 250px;" title="{{ $log->description }}">
                                                    {{ mb_substr($log->description, 0, 50) }}...
                                                </span>
                                                <button type="button" onclick="toggleAuditDesc(this)" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-bold text-[11px] underline shrink-0 inline-flex items-center gap-1 cursor-pointer">
                                                    <span class="btn-text">เพิ่มเติม</span>
                                                    <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"></i>
                                                </button>
                                            </div>
                                            <div class="desc-panel hidden mt-2 p-2.5 rounded-lg bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700/80 text-[11.5px] text-slate-700 dark:text-slate-200 leading-relaxed shadow-sm">
                                                <div class="break-words select-text">{{ $log->description }}</div>
                                                <div class="mt-1.5 pt-1 border-t border-slate-200/60 dark:border-slate-700/60 text-right">
                                                    <button type="button" onclick="toggleAuditDesc(this)" class="text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-300 font-semibold text-[10px] inline-flex items-center gap-1 cursor-pointer">
                                                        <span>ย่อลง</span>
                                                        <i class="fa-solid fa-chevron-up text-[8px]"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="break-words">{{ $log->description }}</div>
                                        @endif
                                        @if($log->diff)
                                            <div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-1">
                                                มีการแก้ไข {{ count($log->diff) }} ฟิลด์
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- IP Address & Method -->
                                <td class="whitespace-nowrap font-mono text-[11px]">
                                    <div class="text-slate-700 dark:text-slate-300">
                                        {{ $log->ip_address ?: '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $log->method }}
                                    </div>
                                </td>

                                <!-- Diff Viewer Button -->
                                <td class="text-center whitespace-nowrap">
                                    @if($log->hasDiff())
                                        <button type="button" @click="openDiffModal({{ $log->id }})"
                                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 transition flex items-center gap-1 mx-auto">
                                            <i class="fa-solid fa-code-compare"></i>
                                            <span>ดูรายละเอียด</span>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <!-- ============================================================== -->
    <!-- TAB 2: AUDIT ARCHIVES (5-YEAR RETENTION & ZIP ARCHIVES) -->
    <!-- ============================================================== -->
    <div x-show="activeTab === 'archives'" class="space-y-5" style="display: none;">

        <!-- 5-Year Retention Policy Information Banner -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-transparent border border-indigo-200/80 dark:border-indigo-900/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5 max-w-3xl">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/20">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span>นโยบายการจัดเก็บและรักษาข้อมูล Log 5 ปี (Audit Compliance Policy)</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                            มาตรฐาน ISO/IEC 27001
                        </span>
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                        ระบบจัดเก็บประวัติการทำงาน (Audit Logs) ครอบคลุมการเพิ่ม ลบ แก้ไข ย้อนหลัง 5 ปี เมื่อครบกำหนดหรือถึงรอบปี ระบบจะทำการบีบอัดเป็นไฟล์ ZIP (.zip) บรรจุไฟล์ JSON และ CSV สรุป พร้อมคำนวณ Checksum SHA-256 ป้องกันการดัดแปลง (Data Immutability) เพื่อให้ Auditor หรือผู้ตรวจประเมินสามารถเปิดตรวจสอบได้ตลอดเวลา
                    </p>
                </div>
            </div>

            <!-- Action Buttons for Archives -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Clean Expired Archives (5 Years Retention) -->
                <form action="{{ route('backend.audit-logs.archives.clean-expired') }}" method="POST"
                    onsubmit="return confirm('ยืนยันการตรวจสอบและลบไฟล์ ZIP ที่จัดเก็บครบกำหนด 5 ปีออกจากระบบ? (ไฟล์ที่ยังไม่ครบ 5 ปีจะไม่ถูกลบ)');">
                    @csrf
                    <button type="submit"
                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs border border-slate-300 dark:border-slate-700 transition flex items-center gap-1.5"
                        title="ตรวจสอบและลบเฉพาะไฟล์ ZIP ที่จัดเก็บครบตามนโยบาย 5 ปีแล้วเพื่อคืนพื้นที่">
                        <i class="fa-solid fa-broom text-amber-500"></i>
                        <span>ทำความสะอาดไฟล์ครบ 5 ปี</span>
                    </button>
                </form>

                <!-- On-demand Archive Trigger Button -->
                <button type="button" @click="openArchiveModal()"
                    class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-600/25 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-zipper"></i>
                    <span>สั่งบีบอัดไฟล์ Log (Zip Now)</span>
                </button>
            </div>
        </div>

        <!-- Archives Table Card -->
        <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-box-archive text-purple-600"></i>
                        <span>รายการไฟล์คลังบีบอัดย้อนหลัง (Audit Archive Ledgers)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">จัดเก็บไฟล์บีบอัดไว้นาน 5 ปี พร้อมแฮช SHA-256 สำหรับตรวจสอบความถูกต้องตามมาตรฐาน ISO/IEC 27001</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    ทั้งหมด {{ count($archives) }} ไฟล์
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-850/60 border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">#</th>
                            <th class="py-3.5 px-4">ชื่อไฟล์คลัง (Archive Filename)</th>
                            <th class="py-3.5 px-4 w-32">ช่วงเวลาข้อมูล</th>
                            <th class="py-3.5 px-4 w-28 text-center">จำนวนรายการ</th>
                            <th class="py-3.5 px-4 w-24">ขนาดไฟล์</th>
                            <th class="py-3.5 px-4">SHA-256 Checksum (ความปลอดภัย)</th>
                            <th class="py-3.5 px-4 w-36">วันที่สร้างไฟล์</th>
                            <th class="py-3.5 px-4 w-36">อายุการจัดเก็บ (5 ปี)</th>
                            <th class="py-3.5 px-4 w-44 text-center">การตรวจสอบ (Auditor Actions)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($archives as $idx => $arch)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                                    {{ $idx + 1 }}
                                </td>

                                <!-- Filename -->
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-file-zipper text-purple-600 text-base"></i>
                                        <span>{{ $arch->filename }}</span>
                                    </div>
                                    @if($arch->notes)
                                        <div class="text-[11px] font-sans font-normal text-slate-400 mt-0.5">
                                            {{ $arch->notes }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Period -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-indigo-600 dark:text-indigo-400">
                                    {{ $arch->period_label }}
                                </td>

                                <!-- Records Count -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ number_format($arch->records_count) }} รายการ
                                    </span>
                                </td>

                                <!-- File Size -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-mono">
                                    {{ $arch->file_size_human }}
                                </td>

                                <!-- Checksum -->
                                <td class="py-3.5 px-4 font-mono text-[10px] text-slate-500">
                                    <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-900 p-1.5 rounded border border-slate-200 dark:border-slate-800 max-w-xs">
                                        <span class="truncate" title="{{ $arch->checksum_sha256 }}">{{ $arch->checksum_sha256 }}</span>
                                        <button type="button" @click="copyChecksum('{{ $arch->checksum_sha256 }}')"
                                            class="text-indigo-600 hover:text-indigo-800 shrink-0" title="คัดลอก Checksum">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </div>
                                </td>

                                <!-- Created Info in Thai format -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="text-slate-800 dark:text-slate-200 font-semibold flex items-center gap-1.5">
                                        <i class="fa-regular fa-calendar-check text-purple-500 text-[11px]"></i>
                                        <span>{{ $arch->thai_date }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        เวลา {{ $arch->thai_time }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        โดย: {{ $arch->archived_by_name ?: 'System' }}
                                    </div>
                                </td>

                                <!-- 5-Year Retention Status -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="text-[11px] font-medium text-slate-700 dark:text-slate-300">
                                        เก็บถึง: <span class="font-bold">{{ $arch->thai_retain_until }}</span>
                                    </div>
                                    @php
                                        $retStatus = $arch->retention_status;
                                    @endphp
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $retStatus['badge'] }}">
                                            <i class="fa-solid fa-clock-rotate-left mr-0.5"></i> {{ $retStatus['label'] }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Inspect inside Zip without downloading -->
                                        <button type="button" @click="inspectZipArchive({{ $arch->id }})"
                                            class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="เปิดดูข้อมูลข้างในไฟล์ ZIP ได้ทันทีบนเว็บ">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>เปิดดูข้อมูล</span>
                                        </button>

                                        <!-- Download ZIP -->
                                        <a href="{{ route('backend.audit-logs.archives.download', $arch->id) }}"
                                            class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:hover:bg-purple-900/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="ดาวน์โหลดไฟล์ ZIP">
                                            <i class="fa-solid fa-download"></i>
                                            <span>ดาวน์โหลด</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-box-archive text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                                    ยังไม่มีไฟล์คลังบีบอัดในระบบ คุณสามารถคลิกปุ่ม "สั่งบีบอัดไฟล์ Log" ด้านบนเพื่อเริ่มจัดเก็บได้ทันที
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <!-- ============================================================== -->
    <!-- MODAL 1: DIFF VIEWER (รายละเอียดการแก้ไข เก่า vs ใหม่) -->
    <!-- ============================================================== -->
    <div x-show="diffModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;">
        
        <div @click.away="diffModalOpen = false"
            class="bg-white dark:bg-[#1E2129] rounded-2xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-left max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <span :class="currentLog?.badge_class" class="px-2.5 py-1 rounded-md text-xs font-semibold border flex items-center gap-1">
                        <i :class="currentLog?.icon"></i>
                        <span x-text="currentLog?.action_label"></span>
                    </span>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white" x-text="currentLog?.description"></h3>
                </div>
                <button type="button" @click="diffModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Meta details row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-850/60 border border-slate-200/80 dark:border-slate-800 text-xs mb-4 shrink-0">
                <div>
                    <span class="text-slate-400 block text-[10px] font-semibold">ผู้ดำเนินการ:</span>
                    <strong class="text-slate-700 dark:text-slate-200" x-text="currentLog?.user_name || 'ระบบอัตโนมัติ'"></strong>
                    <span class="text-slate-400 block text-[10px]" x-text="currentLog?.user_code"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-semibold">วันเวลาที่กระทำ:</span>
                    <strong class="text-slate-700 dark:text-slate-200" x-text="currentLog?.formatted_time"></strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-semibold">IP Address:</span>
                    <strong class="text-slate-700 dark:text-slate-200 font-mono" x-text="currentLog?.ip_address || '-'"></strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-semibold">HTTP Method / URL:</span>
                    <span class="font-mono text-[11px] text-slate-700 dark:text-slate-300 truncate block" :title="currentLog?.url" x-text="(currentLog?.method || '') + ' ' + (currentLog?.url || '')"></span>
                </div>
            </div>

            <!-- Scrollable Diff Area -->
            <div class="overflow-y-auto flex-1 space-y-4 pr-1">

                <!-- 1. Diff comparison table (When updated) -->
                <template x-if="currentLog?.diff && Object.keys(currentLog.diff).length > 0">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-code-compare text-amber-500"></i>
                            <span>รายละเอียดฟิลด์ที่มีการเปลี่ยนแปลง (เปรียบเทียบ ค่าเดิม vs ค่าใหม่):</span>
                        </h4>
                        
                        <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="py-2.5 px-3 w-1/4">ชื่อฟิลด์ (Field)</th>
                                        <th class="py-2.5 px-3 w-[37.5%] bg-rose-50/50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border-r border-slate-200 dark:border-slate-700">
                                            <i class="fa-solid fa-minus-circle mr-1"></i> ค่าเดิมก่อนแก้ไข (Old Value)
                                        </th>
                                        <th class="py-2.5 px-3 w-[37.5%] bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400">
                                            <i class="fa-solid fa-plus-circle mr-1"></i> ค่าใหม่หลังแก้ไข (New Value)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-sans">
                                    <template x-for="(vals, field) in currentLog.diff" :key="field">
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 dark:text-slate-200 bg-slate-50/60 dark:bg-slate-850/40" x-text="field"></td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] bg-rose-50/20 dark:bg-rose-950/10 text-rose-800 dark:text-rose-300 border-r border-slate-200 dark:border-slate-700 whitespace-pre-wrap break-all" x-text="vals.old || '(ค่าว่าง)'"></td>
                                            <td class="py-2.5 px-3 font-mono text-[11px] bg-emerald-50/20 dark:bg-emerald-950/10 text-emerald-800 dark:text-emerald-300 whitespace-pre-wrap break-all" x-text="vals.new || '(ค่าว่าง)'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <!-- 2. Created Values (When created) -->
                <template x-if="currentLog?.action === 'created' && currentLog?.new_values">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-plus-circle text-emerald-500"></i>
                            <span>ข้อมูลทั้งหมดที่ถูกสร้างขึ้น (New Created Record):</span>
                        </h4>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900 p-3 max-h-72 overflow-y-auto">
                            <pre class="text-[11px] font-mono text-slate-700 dark:text-slate-300 whitespace-pre-wrap" x-text="JSON.stringify(currentLog.new_values, null, 2)"></pre>
                        </div>
                    </div>
                </template>

                <!-- 3. Deleted Values (When deleted) -->
                <template x-if="currentLog?.action === 'deleted' && currentLog?.old_values">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can text-rose-500"></i>
                            <span>ข้อมูลเดิมที่ถูกลบออก (Deleted Record Snapshot):</span>
                        </h4>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-rose-50/30 dark:bg-rose-950/20 p-3 max-h-72 overflow-y-auto">
                            <pre class="text-[11px] font-mono text-rose-800 dark:text-rose-300 whitespace-pre-wrap" x-text="JSON.stringify(currentLog.old_values, null, 2)"></pre>
                        </div>
                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end mt-4">
                <button type="button" @click="diffModalOpen = false"
                    class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                    ปิดหน้าต่าง
                </button>
            </div>

        </div>
    </div>


    <!-- ============================================================== -->
    <!-- MODAL 2: INSPECT ARCHIVE IN-BROWSER (สำหรับ Auditor ตรวจสอบ) -->
    <!-- ============================================================== -->
    <div x-show="inspectModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;">
        
        <div @click.away="inspectModalOpen = false"
            class="bg-white dark:bg-[#1E2129] rounded-2xl max-w-5xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-left max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-300 flex items-center justify-center">
                        <i class="fa-solid fa-file-zipper"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>ตรวจสอบข้อมูลภายในไฟล์คลัง:</span>
                            <span class="font-mono text-purple-600 dark:text-purple-400" x-text="inspectedArchive?.filename"></span>
                        </h3>
                        <p class="text-xs text-slate-400">อ่านข้อมูลโดยตรงจากไฟล์ ZIP ที่บีบอัดไว้ สำหรับ Auditor ตรวจสอบ</p>
                    </div>
                </div>
                <button type="button" @click="inspectModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Archive info summary bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 p-3 rounded-xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/40 text-xs mb-3">
                <div class="flex items-center gap-4">
                    <span><strong>ช่วงเวลา:</strong> <span class="text-purple-700 dark:text-purple-300" x-text="inspectedArchive?.period_label"></span></span>
                    <span><strong>ขนาด:</strong> <span class="font-mono" x-text="inspectedArchive?.file_size_human"></span></span>
                    <span><strong>จำนวนรายการ:</strong> <span class="font-bold text-purple-700 dark:text-purple-300" x-text="inspectedLogs.length + ' รายการ'"></span></span>
                </div>
                <div class="flex items-center gap-2 font-mono text-[11px] text-slate-500">
                    <span>SHA-256:</span>
                    <span class="truncate max-w-xs" :title="inspectedArchive?.checksum_sha256" x-text="inspectedArchive?.checksum_sha256"></span>
                </div>
            </div>

            <!-- Search inside archive -->
            <div class="mb-3">
                <input type="text" x-model="inspectSearch" placeholder="พิมพ์ค้นหาคำอธิบาย, ผู้กระทำ, กิจกรรม ภายในไฟล์ Archive..."
                    class="w-full py-1.5 px-3 text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white">
            </div>

            <!-- Logs list inside archive -->
            <div class="overflow-y-auto flex-1 rounded-xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-850 text-[11px] font-bold text-slate-500 border-b border-slate-200 dark:border-slate-800 sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3 w-12 text-center">#</th>
                            <th class="py-2.5 px-3 w-36">วันเวลา</th>
                            <th class="py-2.5 px-3 w-40">ผู้ดำเนินการ</th>
                            <th class="py-2.5 px-3 w-28">กิจกรรม</th>
                            <th class="py-2.5 px-3 w-36">ระบบ/โมดูล</th>
                            <th class="py-2.5 px-3">คำอธิบาย</th>
                            <th class="py-2.5 px-3 w-28 font-mono">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <template x-for="(l, i) in filteredInspectedLogs" :key="l.id || i">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="py-2 px-3 text-center font-mono text-slate-400" x-text="i + 1"></td>
                                <td class="py-2 px-3 whitespace-nowrap text-slate-700 dark:text-slate-300 font-mono text-[11px]" x-text="formatDate(l.created_at)"></td>
                                <td class="py-2 px-3 whitespace-nowrap font-medium text-slate-800 dark:text-slate-100" x-text="l.user_name || 'System'"></td>
                                <td class="py-2 px-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold"
                                        :class="l.action === 'created' ? 'bg-emerald-100 text-emerald-700' : (l.action === 'updated' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700')"
                                        x-text="l.action"></span>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-slate-500" x-text="l.module_name || l.module"></td>
                                <td class="py-2 px-3 text-slate-800 dark:text-slate-200" x-text="l.description"></td>
                                <td class="py-2 px-3 font-mono text-[11px] text-slate-500" x-text="l.ip_address || '-'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between mt-4">
                <span class="text-xs text-slate-400">แสดงผลข้อมูลที่อ่านได้จากไฟล์ ZIP โดยไม่ต้องแตกไฟล์</span>
                <div class="flex items-center gap-2">
                    <a :href="'{{ url('backend/audit-logs/archives') }}/' + inspectedArchive?.id + '/download'"
                        class="px-4 py-2 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white transition flex items-center gap-1.5">
                        <i class="fa-solid fa-download"></i>
                        <span>ดาวน์โหลด ZIP นี้</span>
                    </a>
                    <button type="button" @click="inspectModalOpen = false"
                        class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                        ปิด
                    </button>
                </div>
            </div>

        </div>
    </div>


    <!-- ============================================================== -->
    <!-- MODAL 3: CREATE ON-DEMAND ARCHIVE (สั่งบีบอัดไฟล์ ZIP) -->
    <!-- ============================================================== -->
    <div x-show="archiveModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;">
        
        <div @click.away="archiveModalOpen = false"
            class="bg-white dark:bg-[#1E2129] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-left">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-zipper text-purple-600"></i>
                    <span>สั่งบีบอัดไฟล์ Audit Log (Archive)</span>
                </h3>
                <button type="button" @click="archiveModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="mb-4 p-3 rounded-xl bg-purple-50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800 text-xs">
                <div class="flex items-center gap-1.5 font-bold text-purple-700 dark:text-purple-300 mb-1">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>นโยบายจัดเก็บคลัง Log 5 ปี (5-Year Retention Policy)</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-normal">
                    ไฟล์ ZIP จะถูกจัดเก็บใน Private Storage เป็นเวลา 5 ปี บรรจุ JSON บันทึกเต็มรูปแบบ + CSV สรุปสำหรับ Excel + Manifest SHA-256 เพื่อการตรวจสอบของ Auditor
                </p>
            </div>

            <form action="{{ route('backend.audit-logs.archive') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        เลือกปี ค.ศ. / พ.ศ. ที่ต้องการบีบอัด <span class="text-rose-500">*</span>
                    </label>
                    <select name="year" required
                        class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500">
                        @foreach($yearsWithLogs as $yr)
                            <option value="{{ $yr }}">
                                ปี พ.ศ. {{ $yr + 543 }} (ค.ศ. {{ $yr }})
                            </option>
                        @endforeach
                        @if(empty($yearsWithLogs))
                            <option value="{{ date('Y') }}">ปี พ.ศ. {{ date('Y') + 543 }} (ค.ศ. {{ date('Y') }})</option>
                        @endif
                    </select>
                </div>

                <!-- Option to purge live DB logs for the new year -->
                <div class="mb-4 p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/80">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="purge" value="1" class="mt-0.5 rounded border-amber-400 text-purple-600 focus:ring-purple-500">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">
                                ล้าง Log ในฐานข้อมูลหลังจากสร้างไฟล์ ZIP สำเร็จ (เริ่มรอบปีใหม่)
                            </span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5 leading-normal">
                                ลบประวัติปีที่เลือกออกจากฐานข้อมูลหลังจากไฟล์ ZIP ได้รับการตรวจสอบ Checksum แล้ว เพื่อประหยัดขนาดฐานข้อมูลและเพิ่มความเร็ว โดยไฟล์ ZIP จะถูกเก็บไว้นาน 5 ปี
                            </span>
                        </div>
                    </label>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        หมายเหตุ / บันทึกเพิ่มเติมสำหรับการตรวจสอบ (Auditor Notes)
                    </label>
                    <textarea name="notes" rows="2" placeholder="เช่น บีบอัดปิดรอบปีงบประมาณ หรือจัดเตรียมสำหรับ Audit ตรวจสอบ..."
                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="archiveModalOpen = false"
                        class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                        ยกเลิก
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow-md shadow-purple-600/25 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-zipper"></i>
                        <span>เริ่มบีบอัดไฟล์ ZIP</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- DataTables Scripts -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
function renderAuditDescription(description, diffCount) {
    if (!description) return '-';
    const diffBadge = diffCount > 0 ? `<div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-1">มีการแก้ไข ${diffCount} ฟิลด์</div>` : '';
    const safeFull = $('<div>').text(description).html();

    if (description.length > 55) {
        const shortText = description.substring(0, 50) + '...';
        const safeShort = $('<div>').text(shortText).html();
        return `
            <div class="desc-box font-medium text-slate-800 dark:text-slate-100 text-xs">
                <div class="desc-preview flex items-center justify-between gap-1.5">
                    <span class="truncate text-slate-800 dark:text-slate-100" style="max-width: 250px;" title="${safeFull}">
                        ${safeShort}
                    </span>
                    <button type="button" onclick="toggleAuditDesc(this)" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-bold text-[11px] underline shrink-0 inline-flex items-center gap-1 cursor-pointer">
                        <span class="btn-text">เพิ่มเติม</span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"></i>
                    </button>
                </div>
                <div class="desc-panel hidden mt-2 p-2.5 rounded-lg bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700/80 text-[11.5px] text-slate-700 dark:text-slate-200 leading-relaxed shadow-sm">
                    <div class="break-words select-text">${safeFull}</div>
                    <div class="mt-1.5 pt-1 border-t border-slate-200/60 dark:border-slate-700/60 text-right">
                        <button type="button" onclick="toggleAuditDesc(this)" class="text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-300 font-semibold text-[10px] inline-flex items-center gap-1 cursor-pointer">
                            <span>ย่อลง</span>
                            <i class="fa-solid fa-chevron-up text-[8px]"></i>
                        </button>
                    </div>
                </div>
                ${diffBadge}
            </div>
        `;
    } else {
        return `
            <div class="desc-box font-medium text-slate-800 dark:text-slate-100 text-xs">
                <div class="break-words">${safeFull}</div>
                ${diffBadge}
            </div>
        `;
    }
}

window.toggleAuditDesc = function(btn) {
    const box = btn.closest('.desc-box');
    if (!box) return;
    const panel = box.querySelector('.desc-panel');
    const previewBtn = box.querySelector('.desc-preview .btn-text');
    const previewIcon = box.querySelector('.desc-preview i');

    if (panel) {
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            if (previewBtn) previewBtn.textContent = 'ย่อลง';
            if (previewIcon) previewIcon.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            if (previewBtn) previewBtn.textContent = 'เพิ่มเติม';
            if (previewIcon) previewIcon.classList.remove('rotate-180');
        }
    }
};

function auditLogApp() {
    return {
        activeTab: '{{ request("tab", "logs") }}',
        diffModalOpen: false,
        inspectModalOpen: false,
        archiveModalOpen: false,
        currentLog: null,
        inspectedArchive: null,
        inspectedLogs: [],
        inspectSearch: '',
        
        // Real-time Engine State
        isLive: true,
        isRefreshing: false,
        lastKnownId: {{ \App\Models\SystemAuditLog::max('id') ?? 0 }},
        lastSyncedTime: '{{ (new \App\Models\SystemAuditLog(["created_at" => now()]))->thai_time }}',
        liveTimer: null,
        metrics: @json($metrics),

        initApp() {
            window.auditApp = this;
            this.initDataTable();
            this.startLivePolling();
        },

        initDataTable() {
            const self = this;
            const thLanguage = {
                search: "ค้นหาด่วน:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                infoEmpty: "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                },
                emptyTable: "ไม่มีข้อมูลบันทึกกิจกรรมในระบบ"
            };

            const dtOptions = {
                language: thLanguage,
                order: [[1, 'desc'], [0, 'desc']], // เรียงตามวันเวลาล่าสุด (และ ID ล่าสุด)
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 bg-slate-50/40 dark:bg-slate-850/20"lf>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 border-t border-slate-100 dark:border-slate-800"ip>',
                columnDefs: [
                    { targets: [5], className: 'col-description' },
                    { targets: [7], orderable: false, searchable: false }
                ]
            };

            try {
                if (window.$ && typeof window.$.fn.DataTable === 'function') {
                    window.auditDataTable = $('#auditLogsDataTable').DataTable(dtOptions);
                } else if (typeof window.DataTable === 'function') {
                    window.auditDataTable = new window.DataTable('#auditLogsDataTable', dtOptions);
                }
            } catch (err) {
                console.warn('DataTable init notice:', err);
            }
        },

        toggleLive() {
            this.isLive = !this.isLive;
            if (this.isLive) {
                this.startLivePolling();
            } else if (this.liveTimer) {
                clearInterval(this.liveTimer);
                this.liveTimer = null;
            }
        },

        startLivePolling() {
            if (this.liveTimer) clearInterval(this.liveTimer);
            this.liveTimer = setInterval(() => {
                if (this.isLive && this.activeTab === 'logs') {
                    this.fetchLiveFeed();
                }
            }, 5000); // 5 seconds interval
        },

        async fetchLiveFeed() {
            try {
                const res = await fetch(`{{ route('backend.audit-logs.data') }}?since_id=${this.lastKnownId}`);
                const data = await res.json();
                if (data.success) {
                    this.lastSyncedTime = data.server_time || new Date().toLocaleTimeString('th-TH') + ' น.';
                    this.metrics = data.metrics;

                    if (data.data && data.data.length > 0) {
                        this.lastKnownId = data.latest_id;
                        this.addNewLogsToDataTable(data.data);
                    }
                }
            } catch (err) {
                console.warn('Real-time polling notice:', err);
            }
        },

        async manualRefresh() {
            this.isRefreshing = true;
            try {
                const res = await fetch(`{{ route('backend.audit-logs.data') }}`);
                const data = await res.json();
                if (data.success && window.auditDataTable) {
                    window.auditDataTable.clear();
                    
                    data.data.forEach(log => {
                        const rowApi = window.auditDataTable.row.add([
                            log.id,
                            `<div class="font-semibold text-slate-800 dark:text-white flex items-center gap-1.5"><i class="fa-regular fa-calendar text-indigo-500 text-[11px]"></i><span>${log.thai_date}</span></div><div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono"><i class="fa-regular fa-clock text-[10px]"></i><span>${log.thai_time}</span></div>`,
                            `<div class="flex items-center gap-2"><div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-[11px] text-slate-600 dark:text-slate-300 shrink-0">${log.user_initial}</div><div class="min-w-0"><div class="font-semibold text-slate-800 dark:text-slate-100 truncate">${log.user_name}</div><div class="text-[10px] text-slate-400 font-mono">${log.user_code}</div></div></div>`,
                            `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold border ${log.badge_class}"><i class="${log.icon}"></i><span>${log.action_label}</span></span>`,
                            `<span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">${log.module_name}</span>`,
                            renderAuditDescription(log.description, log.diff_count),
                            `<div class="font-mono text-[11px] text-slate-700 dark:text-slate-300">${log.ip_address}</div><div class="text-[10px] text-slate-400">${log.method}</div>`,
                            log.has_diff ? `<button type="button" onclick="window.auditApp.openDiffModal(${log.id})" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 transition flex items-center gap-1 mx-auto"><i class="fa-solid fa-code-compare"></i><span>ดูรายละเอียด</span></button>` : `<span class="text-slate-400">-</span>`
                        ]);

                        const rowNode = rowApi.node();
                        if (rowNode) {
                            $(rowNode).attr('id', `log-row-${log.id}`);
                            $(rowNode).find('td:eq(0)').addClass('text-center font-mono text-slate-400');
                            $(rowNode).find('td:eq(1)').addClass('whitespace-nowrap').attr('data-order', log.timestamp || log.id);
                            $(rowNode).find('td:eq(3)').addClass('whitespace-nowrap');
                            $(rowNode).find('td:eq(4)').addClass('whitespace-nowrap');
                            $(rowNode).find('td:eq(5)').addClass('col-description');
                            $(rowNode).find('td:eq(6)').addClass('whitespace-nowrap font-mono text-[11px]');
                            $(rowNode).find('td:eq(7)').addClass('text-center whitespace-nowrap');
                        }
                    });
                    
                    window.auditDataTable.draw(false);
                    this.lastSyncedTime = data.server_time || new Date().toLocaleTimeString('th-TH') + ' น.';
                    this.metrics = data.metrics;
                    this.lastKnownId = data.latest_id;
                }
            } catch (err) {
                console.error('Refresh error:', err);
            } finally {
                setTimeout(() => { this.isRefreshing = false; }, 400);
            }
        },

        addNewLogsToDataTable(newLogs) {
            if (!window.auditDataTable) return;

            // Sort chronologically ascending so newest is added last
            const sorted = [...newLogs].sort((a, b) => a.id - b.id);

            sorted.forEach(log => {
                // Check if row already exists
                if ($(`#log-row-${log.id}`).length > 0) return;

                const rowApi = window.auditDataTable.row.add([
                    log.id,
                    `<div class="font-semibold text-slate-800 dark:text-white flex items-center gap-1.5"><i class="fa-regular fa-calendar text-indigo-500 text-[11px]"></i><span>${log.thai_date}</span></div><div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono"><i class="fa-regular fa-clock text-[10px]"></i><span>${log.thai_time}</span></div>`,
                    `<div class="flex items-center gap-2"><div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-[11px] text-slate-600 dark:text-slate-300 shrink-0">${log.user_initial}</div><div class="min-w-0"><div class="font-semibold text-slate-800 dark:text-slate-100 truncate">${log.user_name}</div><div class="text-[10px] text-slate-400 font-mono">${log.user_code}</div></div></div>`,
                    `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold border ${log.badge_class}"><i class="${log.icon}"></i><span>${log.action_label}</span></span>`,
                    `<span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">${log.module_name}</span>`,
                    renderAuditDescription(log.description, log.diff_count),
                    `<div class="font-mono text-[11px] text-slate-700 dark:text-slate-300">${log.ip_address}</div><div class="text-[10px] text-slate-400">${log.method}</div>`,
                    log.has_diff ? `<button type="button" onclick="window.auditApp.openDiffModal(${log.id})" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 transition flex items-center gap-1 mx-auto"><i class="fa-solid fa-code-compare"></i><span>ดูรายละเอียด</span></button>` : `<span class="text-slate-400">-</span>`
                ]);

                const rowNode = rowApi.node();
                if (rowNode) {
                    $(rowNode).attr('id', `log-row-${log.id}`).addClass('row-flash');
                    $(rowNode).find('td:eq(0)').addClass('text-center font-mono text-slate-400');
                    $(rowNode).find('td:eq(1)').addClass('whitespace-nowrap').attr('data-order', log.timestamp || log.id);
                    $(rowNode).find('td:eq(3)').addClass('whitespace-nowrap');
                    $(rowNode).find('td:eq(4)').addClass('whitespace-nowrap');
                    $(rowNode).find('td:eq(5)').addClass('col-description');
                    $(rowNode).find('td:eq(6)').addClass('whitespace-nowrap font-mono text-[11px]');
                    $(rowNode).find('td:eq(7)').addClass('text-center whitespace-nowrap');
                }
            });

            window.auditDataTable.draw(false);
        },

        async openDiffModal(logId) {
            try {
                const res = await fetch(`{{ url('backend/audit-logs') }}/${logId}`);
                const data = await res.json();
                if (data.success) {
                    this.currentLog = {
                        ...data.log,
                        action_label: data.action_label,
                        badge_class: data.badge_class,
                        icon: data.icon,
                        formatted_time: data.formatted_time,
                    };
                    this.diffModalOpen = true;
                }
            } catch (e) {
                alert('ไม่สามารถโหลดรายละเอียดได้: ' + e.message);
            }
        },

        async inspectZipArchive(archiveId) {
            try {
                const res = await fetch(`{{ url('backend/audit-logs/archives') }}/${archiveId}/inspect`);
                const data = await res.json();
                if (data.success) {
                    this.inspectedArchive = data.archive;
                    this.inspectedLogs = data.logs || [];
                    this.inspectSearch = '';
                    this.inspectModalOpen = true;
                } else {
                    alert(data.message || 'ไม่สามารถเปิดไฟล์ ZIP ได้');
                }
            } catch (e) {
                alert('เกิดข้อผิดพลาดในการอ่านไฟล์ ZIP: ' + e.message);
            }
        },

        openArchiveModal() {
            this.archiveModalOpen = true;
        },

        copyChecksum(checksum) {
            navigator.clipboard.writeText(checksum);
            alert('คัดลอกค่า SHA-256 Checksum แล้ว: ' + checksum);
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            try {
                const d = new Date(dateStr);
                const day = d.getDate();
                const thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                const month = thaiMonths[d.getMonth()];
                const year = d.getFullYear() + 543;
                const hours = String(d.getHours()).padStart(2, '0');
                const mins = String(d.getMinutes()).padStart(2, '0');
                const secs = String(d.getSeconds()).padStart(2, '0');
                return `${day} ${month} ${year} เวลา ${hours}:${mins}:${secs} น.`;
            } catch (e) {
                return dateStr;
            }
        },

        get filteredInspectedLogs() {
            if (!this.inspectSearch) return this.inspectedLogs;
            const q = this.inspectSearch.toLowerCase();
            return this.inspectedLogs.filter(l => {
                return (l.description && l.description.toLowerCase().includes(q)) ||
                       (l.user_name && l.user_name.toLowerCase().includes(q)) ||
                       (l.action && l.action.toLowerCase().includes(q)) ||
                       (l.module && l.module.toLowerCase().includes(q));
            });
        }
    };
}
window.auditLogApp = auditLogApp;
</script>
@endpush
