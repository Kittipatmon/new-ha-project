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
        <div @click="setQuickAction('all')"
            class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-all duration-200 cursor-pointer select-none hover:-translate-y-0.5 hover:shadow-md"
            :class="filters.action === 'all' ? 'ring-2 ring-indigo-500 shadow-indigo-500/10' : ''">
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
        <div @click="setQuickAction('created')"
            class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-emerald-100 dark:border-emerald-950/40 shadow-sm bg-gradient-to-br from-emerald-50/40 via-transparent to-transparent transition-all duration-200 cursor-pointer select-none hover:-translate-y-0.5 hover:shadow-md"
            :class="filters.action === 'created' ? 'ring-2 ring-emerald-500 shadow-emerald-500/10' : ''">
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
        <div @click="setQuickAction('updated')"
            class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-amber-100 dark:border-amber-950/40 shadow-sm bg-gradient-to-br from-amber-50/40 via-transparent to-transparent transition-all duration-200 cursor-pointer select-none hover:-translate-y-0.5 hover:shadow-md"
            :class="filters.action === 'updated' ? 'ring-2 ring-amber-500 shadow-amber-500/10' : ''">
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
        <div @click="setQuickAction('deleted')"
            class="bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-rose-100 dark:border-rose-950/40 shadow-sm bg-gradient-to-br from-rose-50/40 via-transparent to-transparent transition-all duration-200 cursor-pointer select-none hover:-translate-y-0.5 hover:shadow-md"
            :class="filters.action === 'deleted' ? 'ring-2 ring-rose-500 shadow-rose-500/10' : ''">
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
        <div @click="activeTab = 'archives'"
            class="col-span-2 sm:col-span-1 bg-white dark:bg-[#1E2129] p-4 rounded-xl border border-purple-100 dark:border-purple-950/40 shadow-sm bg-gradient-to-br from-purple-50/40 via-transparent to-transparent transition-all duration-200 cursor-pointer select-none hover:-translate-y-0.5 hover:shadow-md"
            :class="activeTab === 'archives' ? 'ring-2 ring-purple-500 shadow-purple-500/10' : ''">
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
                    <!-- ปุ่มเปิด/ปิด (กดเก็บได้) ตัวกรอง -->
                    <button type="button" @click="toggleFilter()"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition flex items-center gap-1.5 shadow-2xs"
                        :class="filterOpen ? 'bg-indigo-50 border-indigo-300 text-indigo-700 dark:bg-indigo-950/60 dark:border-indigo-700 dark:text-indigo-300' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'">
                        <i class="fa-solid fa-filter text-[11px]" :class="filterOpen ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500'"></i>
                        <span>ตัวกรองข้อมูล</span>
                        <span x-show="activeFilterCount > 0" x-text="activeFilterCount" style="display: none;"
                            class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-indigo-600 text-white">
                        </span>
                        <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200 text-slate-400"
                            :class="{ 'rotate-180': filterOpen }"></i>
                    </button>

                    <button type="button" @click="manualRefresh()" :disabled="isRefreshing"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 shadow-sm transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrows-rotate" :class="{ 'fa-spin': isRefreshing }"></i>
                        <span>รีเฟรชข้อมูล</span>
                    </button>
                </div>
            </div>

            <!-- Collapsible Filter Panel (สามารถกดเก็บได้) -->
            <div x-show="filterOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/60 space-y-4">
                
                <!-- Quick Filter Chips -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-amber-500"></i>
                            <span>คัดกรองด่วนตามประเภทกิจกรรม:</span>
                        </span>
                        <button type="button" @click="toggleFilter()" class="text-[11px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 flex items-center gap-1">
                            <i class="fa-solid fa-chevron-up text-[9px]"></i>
                            <span>กดเก็บตัวกรอง</span>
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" @click="setQuickAction('all')"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition flex items-center gap-1"
                            :class="filters.action === 'all' ? 'bg-slate-800 text-white border-slate-800 dark:bg-white dark:text-slate-900 shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700'">
                            <i class="fa-solid fa-list-ul text-[10px]"></i>
                            <span>ทั้งหมด</span>
                        </button>

                        <button type="button" @click="setQuickAction('backup')"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition flex items-center gap-1"
                            :class="filters.action === 'backup' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-blue-50/70 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800 hover:bg-blue-100'">
                            <i class="fa-solid fa-database text-[10px]"></i>
                            <span>สำรองข้อมูล (Backup)</span>
                        </button>

                        <button type="button" @click="setQuickAction('created')"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition flex items-center gap-1"
                            :class="filters.action === 'created' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-emerald-50/70 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 hover:bg-emerald-100'">
                            <i class="fa-solid fa-circle-plus text-[10px]"></i>
                            <span>เพิ่มข้อมูล (Created)</span>
                        </button>

                        <button type="button" @click="setQuickAction('updated')"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition flex items-center gap-1"
                            :class="filters.action === 'updated' ? 'bg-amber-600 text-white border-amber-600 shadow-sm' : 'bg-amber-50/70 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 hover:bg-amber-100'">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            <span>แก้ไขข้อมูล (Updated)</span>
                        </button>

                        <button type="button" @click="setQuickAction('deleted')"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition flex items-center gap-1"
                            :class="filters.action === 'deleted' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-rose-50/70 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800 hover:bg-rose-100'">
                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                            <span>ลบข้อมูล (Deleted)</span>
                        </button>

                        <button type="button" @click="setQuickAction('password')"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition flex items-center gap-1"
                            :class="filters.action === 'password' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-amber-50/70 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 hover:bg-amber-100'">
                            <i class="fa-solid fa-key text-[10px]"></i>
                            <span>ขอรับรหัสผ่าน (Password)</span>
                        </button>

                        <button type="button" @click="setQuickAction('security')"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition flex items-center gap-1"
                            :class="filters.action === 'security' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-purple-50/70 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800 hover:bg-purple-100'">
                            <i class="fa-solid fa-shield-halved text-[10px]"></i>
                            <span>ความปลอดภัย / Login</span>
                        </button>
                    </div>
                </div>

                <!-- Form Controls Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    
                    <!-- Action Dropdown -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            ประเภทกิจกรรม (Action)
                        </label>
                        <select x-model="filters.action" @change="applyFilters()"
                            class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white py-1.5 px-2.5 focus:ring-2 focus:ring-indigo-500">
                            <option value="all">-- ทุกประเภทกิจกรรม --</option>
                            <option value="backup">💾 สำรองฐานข้อมูล (Backup)</option>
                            <option value="created">➕ เพิ่มข้อมูล (Created)</option>
                            <option value="updated">✏️ แก้ไขข้อมูล (Updated)</option>
                            <option value="deleted">🗑️ ลบข้อมูล (Deleted)</option>
                            <option value="password">🔑 ขอรับรหัสผ่านถอดรหัสไฟล์</option>
                            <option value="login">🚪 เข้าสู่ระบบ / ตรวจสอบสิทธิ์</option>
                            <option value="exported">📥 ดาวน์โหลด / ส่งออกไฟล์</option>
                            <option value="security">⚠️ แจ้งเตือนความปลอดภัย</option>
                        </select>
                    </div>

                    <!-- Module Dropdown -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            ระบบ / โมดูล (Module)
                        </label>
                        <select x-model="filters.module" @change="applyFilters()"
                            class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white py-1.5 px-2.5 focus:ring-2 focus:ring-indigo-500">
                            <option value="all">-- ทุกระบบ / โมดูล --</option>
                            @foreach($modules as $m)
                                <option value="{{ $m->module_name ?: $m->module }}">{{ $m->module_name ?: ucfirst($m->module) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            ช่วงวันที่ (Date Range)
                        </label>
                        <div class="grid grid-cols-2 gap-1.5">
                            <input type="date" x-model="filters.startDate" @change="applyFilters()" @input="applyFilters()"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white py-1.5 px-2 focus:ring-2 focus:ring-indigo-500"
                                placeholder="เริ่มต้น">
                            <input type="date" x-model="filters.endDate" @change="applyFilters()" @input="applyFilters()"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white py-1.5 px-2 focus:ring-2 focus:ring-indigo-500"
                                placeholder="สิ้นสุด">
                        </div>
                    </div>

                    <!-- Search / Keyword & User -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            ค้นหาผู้ดำเนินการ / คำเฉพาะ
                        </label>
                        <div class="relative">
                            <input type="text" x-model="filters.keyword" @input="applyFilters()" @keydown.enter.prevent="applyFilters()"
                                placeholder="พิมพ์เพื่อค้นหาทันที: ชื่อ, รหัส, IP, หรือคำอธิบาย..."
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white py-1.5 pl-8 pr-3 focus:ring-2 focus:ring-indigo-500">
                            <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                </div>

                <!-- Bottom Filter Toolbar: Quick dates + Reset + Collapse -->
                <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-2.5 text-xs">
                    <div class="flex items-center flex-wrap gap-1.5">
                        <span class="text-[11px] text-slate-400 mr-1 font-medium">ลัดช่วงเวลา:</span>
                        <button type="button" @click="setDateShortcut('today')"
                            class="px-2 py-0.5 rounded text-[11px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                            วันนี้
                        </button>
                        <button type="button" @click="setDateShortcut('7days')"
                            class="px-2 py-0.5 rounded text-[11px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                            7 วันล่าสุด
                        </button>
                        <button type="button" @click="setDateShortcut('30days')"
                            class="px-2 py-0.5 rounded text-[11px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                            30 วันล่าสุด
                        </button>
                        <button type="button" @click="setDateShortcut('all')"
                            class="px-2 py-0.5 rounded text-[11px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                            ทุกช่วงเวลา
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-1 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>ล้างตัวกรองทั้งหมด</span>
                        </button>

                        <button type="button" @click="toggleFilter()"
                            class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-chevron-up"></i>
                            <span>กดเก็บตัวกรอง</span>
                        </button>
                    </div>
                </div>

            </div>

@php
if (!function_exists('renderAuditDescHtml')) {
    function renderAuditDescHtml($desc) {
        if (!$desc) return '-';
        $safe = e($desc);

        // 1. Password & Auth Badges
        $safe = preg_replace(
            '/ขอรับรหัสผ่านถอดรหัสไฟล์(?:\:)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shadow-2xs"><i class="fa-solid fa-key text-[10px] text-amber-500"></i> ขอรับรหัสผ่าน:</span>',
            $safe
        );

        $safe = preg_replace(
            '/ยืนยันตัวตน(?:บัญชี)?\s*Microsoft 365(?:\s*แผนก ICT)?(?:\s*สำเร็จ)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px]"><i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i> ยืนยันตัวตน Microsoft 365</span>',
            $safe
        );

        $safe = preg_replace(
            '/พยายามเข้าถึงรหัสผ่านสำรองฐานข้อมูลโดยไม่ผ่านการยืนยันตัวตน/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px]"><i class="fa-solid fa-triangle-exclamation text-[10px] text-rose-500"></i> ไม่ผ่านการยืนยันสิทธิ์ ICT</span>',
            $safe
        );

        $safe = preg_replace(
            '/ปฏิเสธการเข้าถึงรหัสผ่าน(?:\:)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px]"><i class="fa-solid fa-ban text-[10px] text-rose-500"></i> ปฏิเสธสิทธิ์:</span>',
            $safe
        );

        $safe = preg_replace(
            '/ผู้ดูแลระบบเปิดดูรหัสผ่านถอดรหัสไฟล์สำรองฐานข้อมูล(?:\:)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold border border-purple-200 dark:border-purple-800 text-[11px] shadow-2xs"><i class="fa-solid fa-eye text-[10px] text-purple-500"></i> ดูรหัสผ่านสำรอง:</span>',
            $safe
        );

        // 2. Backup Badges (Blue / Sky)
        $safe = preg_replace(
            '/สำรองฐานข้อมูลสำเร็จ\s*(?:\(เข้ารหัส\s*AES-256\))?(?:\:)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-800 text-[11px] shadow-2xs"><i class="fa-solid fa-database text-[10px] text-blue-500"></i> สำรองฐานข้อมูลสำเร็จ:</span>',
            $safe
        );

        $safe = preg_replace(
            '/ดาวน์โหลดไฟล์สำรองฐานข้อมูล(?:\:)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 text-[11px] shadow-2xs"><i class="fa-solid fa-download text-[10px] text-indigo-500"></i> ดาวน์โหลดสำรอง:</span>',
            $safe
        );

        $safe = preg_replace(
            '/สร้างไฟล์คลังบีบอัด Audit Log(?:\:)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800 text-[11px] shadow-2xs"><i class="fa-solid fa-file-zipper text-[10px] text-sky-500"></i> สร้างไฟล์คลัง Log:</span>',
            $safe
        );

        $safe = preg_replace(
            '/ดาวน์โหลดไฟล์คลัง Audit Log(?:\:)?/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800 text-[11px] shadow-2xs"><i class="fa-solid fa-download text-[10px] text-sky-500"></i> ดาวน์โหลดคลัง Log:</span>',
            $safe
        );

        // 3. Delete Badges (Rose / Red)
        $safe = preg_replace(
            '/^(ลบข้อมูล[^\:\：]+|ลบไฟล์[^\:\：]+|ล้างไฟล์[^\:\：]+|ล้างข้อมูล[^\:\：]+|ลบ[^\:\：]+)([\:\：])\s*/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shadow-2xs"><i class="fa-solid fa-trash-can text-[10px] text-rose-500"></i> $1:</span> ',
            $safe
        );
        $safe = preg_replace(
            '/^(ลบข้อมูล|ลบไฟล์|ล้างข้อมูล|ล้างไฟล์|ลบ)\s+/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shadow-2xs"><i class="fa-solid fa-trash-can text-[10px] text-rose-500"></i> $1:</span> ',
            $safe
        );

        // 4. Update / Edit Badges (Amber / Warm)
        $safe = preg_replace(
            '/^(แก้ไขข้อมูล[^\:\：]+|แก้ไข[^\:\：]+|ปรับปรุงการตั้งค่า[^\:\：]*|ปรับปรุงข้อมูล[^\:\：]*|ปรับปรุง[^\:\：]+|ปิดการรับสมัคร[^\:\：]*)([\:\：])\s*/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shadow-2xs"><i class="fa-solid fa-pen-to-square text-[10px] text-amber-500"></i> $1:</span> ',
            $safe
        );
        $safe = preg_replace(
            '/^(แก้ไขข้อมูล|แก้ไข|ปรับปรุงการตั้งค่า|ปรับปรุงข้อมูล|ปรับปรุง|ปิดการรับสมัคร)\s+/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shadow-2xs"><i class="fa-solid fa-pen-to-square text-[10px] text-amber-500"></i> $1:</span> ',
            $safe
        );

        // 5. Create / Add Badges (Emerald / Green)
        $safe = preg_replace(
            '/^(เพิ่มประกาศ[^\:\：]+|เพิ่มหลักสูตร[^\:\：]+|เพิ่มข้อมูล[^\:\：]+|เพิ่ม[^\:\：]+)([\:\：])\s*/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shadow-2xs"><i class="fa-solid fa-circle-plus text-[10px] text-emerald-500"></i> $1:</span> ',
            $safe
        );
        $safe = preg_replace(
            '/^(เพิ่มข้อมูล|เพิ่ม)\s+/u',
            '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shadow-2xs"><i class="fa-solid fa-circle-plus text-[10px] text-emerald-500"></i> $1:</span> ',
            $safe
        );

        // 6. Highlight backup/archive filenames: backup_db_*.zip / audit_archive_*.zip
        $safe = preg_replace(
            '/((?:backup_db_|audit_archive_)[a-zA-Z0-9_\.]+\.zip)/u',
            '<code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/50 px-1.5 py-0.5 rounded border border-purple-200 dark:border-purple-800 break-all">$1</code>',
            $safe
        );

        return $safe;
    }
}

if (!function_exists('renderAuditPreviewHtml')) {
    function renderAuditPreviewHtml($desc) {
        if (!$desc) return '-';

        // 1. Password request
        if (str_contains($desc, 'ขอรับรหัสผ่านถอดรหัสไฟล์')) {
            if (preg_match('/(backup_db_[a-zA-Z0-9_\.]+\.zip)/u', $desc, $matches)) {
                return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shrink-0"><i class="fa-solid fa-key text-[10px] text-amber-500"></i> ขอรับรหัสผ่าน:</span> <code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 truncate max-w-[130px] inline-block align-bottom">' . e($matches[1]) . '</code>';
            }
            return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shrink-0"><i class="fa-solid fa-key text-[10px] text-amber-500"></i> ขอรับรหัสผ่าน</span>';
        }

        // 2. Microsoft 365 Auth
        if (str_contains($desc, 'ยืนยันตัวตน') && str_contains($desc, 'Microsoft 365')) {
            return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shrink-0"><i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i> ยืนยันตัวตน Microsoft 365</span>';
        }

        // 3. Security alert
        if (str_contains($desc, 'ไม่ผ่านการยืนยันตัวตน') || str_contains($desc, 'ปฏิเสธการเข้าถึง')) {
            return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shrink-0"><i class="fa-solid fa-triangle-exclamation text-[10px] text-rose-500"></i> ไม่ผ่านการยืนยันสิทธิ์ ICT</span>';
        }

        // 4. Backup
        if (str_contains($desc, 'สำรองฐานข้อมูลสำเร็จ')) {
            if (preg_match('/(backup_db_[a-zA-Z0-9_\.]+\.zip)/u', $desc, $matches)) {
                return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-800 text-[11px] shrink-0"><i class="fa-solid fa-database text-[10px] text-blue-500"></i> สำรองฐานข้อมูล:</span> <code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 truncate max-w-[130px] inline-block align-bottom">' . e($matches[1]) . '</code>';
            }
            return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-800 text-[11px] shrink-0"><i class="fa-solid fa-database text-[10px] text-blue-500"></i> สำรองฐานข้อมูลสำเร็จ</span>';
        }

        // 5. Download backup / Archive
        if (str_contains($desc, 'ดาวน์โหลดไฟล์สำรองฐานข้อมูล')) {
            if (preg_match('/(backup_db_[a-zA-Z0-9_\.]+\.zip)/u', $desc, $matches)) {
                return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 text-[11px] shrink-0"><i class="fa-solid fa-download text-[10px] text-indigo-500"></i> ดาวน์โหลด:</span> <code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 truncate max-w-[130px] inline-block align-bottom">' . e($matches[1]) . '</code>';
            }
            return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 text-[11px] shrink-0"><i class="fa-solid fa-download text-[10px] text-indigo-500"></i> ดาวน์โหลดสำรอง</span>';
        }

        if (str_contains($desc, 'สร้างไฟล์คลังบีบอัด')) {
            return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800 text-[11px] shrink-0"><i class="fa-solid fa-file-zipper text-[10px] text-sky-500"></i> บีบอัดคลัง Log</span>';
        }

        // 6. Delete (ลบ / ล้าง)
        if (preg_match('/^(?:ลบข้อมูล|ลบไฟล์|ล้างไฟล์|ล้างข้อมูล|ลบ|ล้าง)\s*(?:[^\:\：]*[\:\：]\s*|\s+)(.*)$/u', $desc, $matches)) {
            $badge = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shrink-0"><i class="fa-solid fa-trash-can text-[10px] text-rose-500"></i> ลบ:</span>';
            $rem = isset($matches[1]) ? ' ' . e(mb_substr($matches[1], 0, 35)) . (mb_strlen($matches[1]) > 35 ? '...' : '') : '';
            return $badge . $rem;
        }

        // 7. Update (แก้ไข / ปรับปรุง / ปิด)
        if (preg_match('/^(?:แก้ไขข้อมูล|แก้ไข|ปรับปรุงการตั้งค่า|ปรับปรุงข้อมูล|ปรับปรุง|ปิดการรับสมัคร)\s*(?:[^\:\：]*[\:\：]\s*|\s+)(.*)$/u', $desc, $matches)) {
            $badge = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shrink-0"><i class="fa-solid fa-pen-to-square text-[10px] text-amber-500"></i> แก้ไข:</span>';
            $rem = isset($matches[1]) ? ' ' . e(mb_substr($matches[1], 0, 35)) . (mb_strlen($matches[1]) > 35 ? '...' : '') : '';
            return $badge . $rem;
        }

        // 8. Create (เพิ่ม)
        if (preg_match('/^(?:เพิ่มประกาศรับสมัครงานใหม่|เพิ่มหลักสูตรฝึกอบรม|เพิ่มข้อมูล|เพิ่ม)\s*(?:[^\:\：]*[\:\：]\s*|\s+)(.*)$/u', $desc, $matches)) {
            $badge = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shrink-0"><i class="fa-solid fa-circle-plus text-[10px] text-emerald-500"></i> เพิ่ม:</span>';
            $rem = isset($matches[1]) ? ' ' . e(mb_substr($matches[1], 0, 35)) . (mb_strlen($matches[1]) > 35 ? '...' : '') : '';
            return $badge . $rem;
        }

        return e(mb_substr($desc, 0, 50)) . (mb_strlen($desc) > 50 ? '...' : '');
    }
}
@endphp

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
                                    <div class="desc-container" style="max-width: 320px; white-space: normal; word-break: break-word;">
                                        @if(mb_strlen($log->description) > 45 || str_contains($log->description, 'ขอรับรหัสผ่านถอดรหัสไฟล์') || str_contains($log->description, 'สำรองฐานข้อมูล') || str_contains($log->description, 'ดาวน์โหลดไฟล์สำรอง') || preg_match('/^(?:เพิ่ม|แก้ไข|ปรับปรุง|ลบ|ล้าง)/u', $log->description))
                                            <div class="text-xs text-slate-800 dark:text-slate-100 font-medium leading-relaxed">
                                                <span class="desc-short-text">{!! renderAuditPreviewHtml($log->description) !!}</span>
                                                <span class="desc-full-text" style="display: none;">{!! renderAuditDescHtml($log->description) !!}</span>
                                            </div>
                                            <div class="mt-1">
                                                <button type="button" onclick="toggleAuditDesc(this)" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold text-[11px] inline-flex items-center gap-1 cursor-pointer">
                                                    <span class="desc-btn-label">เพิ่มเติม</span>
                                                    <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-xs text-slate-800 dark:text-slate-100 font-medium leading-relaxed">
                                                {!! renderAuditDescHtml($log->description) !!}
                                            </div>
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
function highlightAuditText(text) {
    if (!text) return '';
    let safe = $('<div>').text(text).html();

    // 1. Password & Auth Badges
    safe = safe.replace(
        /ขอรับรหัสผ่านถอดรหัสไฟล์(?::)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shadow-2xs"><i class="fa-solid fa-key text-[10px] text-amber-500"></i> ขอรับรหัสผ่าน:</span>'
    );

    safe = safe.replace(
        /ยืนยันตัวตน(?:บัญชี)?\s*Microsoft 365(?:\s*แผนก ICT)?(?:\s*สำเร็จ)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px]"><i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i> ยืนยันตัวตน Microsoft 365</span>'
    );

    safe = safe.replace(
        /พยายามเข้าถึงรหัสผ่านสำรองฐานข้อมูลโดยไม่ผ่านการยืนยันตัวตน/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px]"><i class="fa-solid fa-triangle-exclamation text-[10px] text-rose-500"></i> ไม่ผ่านการยืนยันสิทธิ์ ICT</span>'
    );

    safe = safe.replace(
        /ปฏิเสธการเข้าถึงรหัสผ่าน(?::)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px]"><i class="fa-solid fa-ban text-[10px] text-rose-500"></i> ปฏิเสธสิทธิ์:</span>'
    );

    safe = safe.replace(
        /ผู้ดูแลระบบเปิดดูรหัสผ่านถอดรหัสไฟล์สำรองฐานข้อมูล(?::)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold border border-purple-200 dark:border-purple-800 text-[11px] shadow-2xs"><i class="fa-solid fa-eye text-[10px] text-purple-500"></i> ดูรหัสผ่านสำรอง:</span>'
    );

    // 2. Backup Badges (Blue / Sky)
    safe = safe.replace(
        /สำรองฐานข้อมูลสำเร็จ\s*(?:\(เข้ารหัส\s*AES-256\))?(?::)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-800 text-[11px] shadow-2xs"><i class="fa-solid fa-database text-[10px] text-blue-500"></i> สำรองฐานข้อมูลสำเร็จ:</span>'
    );

    safe = safe.replace(
        /ดาวน์โหลดไฟล์สำรองฐานข้อมูล(?::)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 text-[11px] shadow-2xs"><i class="fa-solid fa-download text-[10px] text-indigo-500"></i> ดาวน์โหลดสำรอง:</span>'
    );

    safe = safe.replace(
        /สร้างไฟล์คลังบีบอัด Audit Log(?::)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800 text-[11px] shadow-2xs"><i class="fa-solid fa-file-zipper text-[10px] text-sky-500"></i> สร้างไฟล์คลัง Log:</span>'
    );

    safe = safe.replace(
        /ดาวน์โหลดไฟล์คลัง Audit Log(?::)?/g,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800 text-[11px] shadow-2xs"><i class="fa-solid fa-download text-[10px] text-sky-500"></i> ดาวน์โหลดคลัง Log:</span>'
    );

    // 3. Delete Badges (Rose / Red)
    safe = safe.replace(
        /^(ลบข้อมูล[^:：]+|ลบไฟล์[^:：]+|ล้างไฟล์[^:：]+|ล้างข้อมูล[^:：]+|ลบ[^:：]+)([:：])\s*/u,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shadow-2xs"><i class="fa-solid fa-trash-can text-[10px] text-rose-500"></i> $1:</span> '
    );
    safe = safe.replace(
        /^(ลบข้อมูล|ลบไฟล์|ล้างข้อมูล|ล้างไฟล์|ลบ)\s+/u,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shadow-2xs"><i class="fa-solid fa-trash-can text-[10px] text-rose-500"></i> $1:</span> '
    );

    // 4. Update / Edit Badges (Amber / Warm)
    safe = safe.replace(
        /^(แก้ไขข้อมูล[^:：]+|แก้ไข[^:：]+|ปรับปรุงการตั้งค่า[^:：]*|ปรับปรุงข้อมูล[^:：]*|ปรับปรุง[^:：]+|ปิดการรับสมัคร[^:：]*)([:：])\s*/u,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shadow-2xs"><i class="fa-solid fa-pen-to-square text-[10px] text-amber-500"></i> $1:</span> '
    );
    safe = safe.replace(
        /^(แก้ไขข้อมูล|แก้ไข|ปรับปรุงการตั้งค่า|ปรับปรุงข้อมูล|ปรับปรุง|ปิดการรับสมัคร)\s+/u,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shadow-2xs"><i class="fa-solid fa-pen-to-square text-[10px] text-amber-500"></i> $1:</span> '
    );

    // 5. Create / Add Badges (Emerald / Green)
    safe = safe.replace(
        /^(เพิ่มประกาศ[^:：]+|เพิ่มหลักสูตร[^:：]+|เพิ่มข้อมูล[^:：]+|เพิ่ม[^:：]+)([:：])\s*/u,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shadow-2xs"><i class="fa-solid fa-circle-plus text-[10px] text-emerald-500"></i> $1:</span> '
    );
    safe = safe.replace(
        /^(เพิ่มข้อมูล|เพิ่ม)\s+/u,
        '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shadow-2xs"><i class="fa-solid fa-circle-plus text-[10px] text-emerald-500"></i> เพิ่มข้อมูล:</span> '
    );

    // 6. Highlight backup/archive filenames: backup_db_*.zip / audit_archive_*.zip
    safe = safe.replace(
        /((?:backup_db_|audit_archive_)[a-zA-Z0-9_\.]+\.zip)/g,
        '<code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/50 px-1.5 py-0.5 rounded border border-purple-200 dark:border-purple-800 break-all">$1</code>'
    );

    return safe;
}

function getAuditPreviewHtml(text) {
    if (!text) return '-';

    // 1. Password request
    if (text.includes('ขอรับรหัสผ่านถอดรหัสไฟล์')) {
        const match = text.match(/(backup_db_[a-zA-Z0-9_\.]+\.zip)/);
        if (match) {
            const fname = $('<div>').text(match[1]).html();
            return `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shrink-0"><i class="fa-solid fa-key text-[10px] text-amber-500"></i> ขอรับรหัสผ่าน:</span> <code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 truncate max-w-[130px] inline-block align-bottom">${fname}</code>`;
        }
        return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shrink-0"><i class="fa-solid fa-key text-[10px] text-amber-500"></i> ขอรับรหัสผ่าน</span>';
    }

    // 2. Microsoft 365 Auth
    if (text.includes('ยืนยันตัวตน') && text.includes('Microsoft 365')) {
        return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shrink-0"><i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i> ยืนยันตัวตน Microsoft 365</span>';
    }

    // 3. Security alert
    if (text.includes('ไม่ผ่านการยืนยันตัวตน') || text.includes('ปฏิเสธการเข้าถึง')) {
        return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shrink-0"><i class="fa-solid fa-triangle-exclamation text-[10px] text-rose-500"></i> ไม่ผ่านการยืนยันสิทธิ์ ICT</span>';
    }

    // 4. Backup
    if (text.includes('สำรองฐานข้อมูลสำเร็จ')) {
        const match = text.match(/(backup_db_[a-zA-Z0-9_\.]+\.zip)/);
        if (match) {
            const fname = $('<div>').text(match[1]).html();
            return `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-800 text-[11px] shrink-0"><i class="fa-solid fa-database text-[10px] text-blue-500"></i> สำรองฐานข้อมูล:</span> <code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 truncate max-w-[130px] inline-block align-bottom">${fname}</code>`;
        }
        return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-800 text-[11px] shrink-0"><i class="fa-solid fa-database text-[10px] text-blue-500"></i> สำรองฐานข้อมูลสำเร็จ</span>';
    }

    // 5. Download backup / Archive
    if (text.includes('ดาวน์โหลดไฟล์สำรองฐานข้อมูล')) {
        const match = text.match(/(backup_db_[a-zA-Z0-9_\.]+\.zip)/);
        if (match) {
            const fname = $('<div>').text(match[1]).html();
            return `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 text-[11px] shrink-0"><i class="fa-solid fa-download text-[10px] text-indigo-500"></i> ดาวน์โหลด:</span> <code class="font-mono text-[11px] font-semibold text-purple-700 dark:text-purple-300 truncate max-w-[130px] inline-block align-bottom">${fname}</code>`;
        }
        return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 text-[11px] shrink-0"><i class="fa-solid fa-download text-[10px] text-indigo-500"></i> ดาวน์โหลดสำรอง</span>';
    }

    if (text.includes('สร้างไฟล์คลังบีบอัด')) {
        return '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800 text-[11px] shrink-0"><i class="fa-solid fa-file-zipper text-[10px] text-sky-500"></i> บีบอัดคลัง Log</span>';
    }

    // 6. Delete (ลบ / ล้าง)
    const matchDel = text.match(/^(?:ลบข้อมูล|ลบไฟล์|ล้างไฟล์|ล้างข้อมูล|ลบ|ล้าง)\s*(?:[^:：]*[:：]\s*|\s+)(.*)$/u);
    if (matchDel) {
        const badge = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-800 text-[11px] shrink-0"><i class="fa-solid fa-trash-can text-[10px] text-rose-500"></i> ลบ:</span>';
        const rem = matchDel[1] ? ' ' + $('<div>').text(matchDel[1].substring(0, 35) + (matchDel[1].length > 35 ? '...' : '')).html() : '';
        return badge + rem;
    }

    // 7. Update (แก้ไข / ปรับปรุง / ปิด)
    const matchUpd = text.match(/^(?:แก้ไขข้อมูล|แก้ไข|ปรับปรุงการตั้งค่า|ปรับปรุงข้อมูล|ปรับปรุง|ปิดการรับสมัคร)\s*(?:[^:：]*[:：]\s*|\s+)(.*)$/u);
    if (matchUpd) {
        const badge = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold border border-amber-200 dark:border-amber-800 text-[11px] shrink-0"><i class="fa-solid fa-pen-to-square text-[10px] text-amber-500"></i> แก้ไข:</span>';
        const rem = matchUpd[1] ? ' ' + $('<div>').text(matchUpd[1].substring(0, 35) + (matchUpd[1].length > 35 ? '...' : '')).html() : '';
        return badge + rem;
    }

    // 8. Create (เพิ่ม)
    const matchAdd = text.match(/^(?:เพิ่มประกาศรับสมัครงานใหม่|เพิ่มหลักสูตรฝึกอบรม|เพิ่มข้อมูล|เพิ่ม)\s*(?:[^:：]*[:：]\s*|\s+)(.*)$/u);
    if (matchAdd) {
        const badge = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800 text-[11px] shrink-0"><i class="fa-solid fa-circle-plus text-[10px] text-emerald-500"></i> เพิ่ม:</span>';
        const rem = matchAdd[1] ? ' ' + $('<div>').text(matchAdd[1].substring(0, 35) + (matchAdd[1].length > 35 ? '...' : '')).html() : '';
        return badge + rem;
    }

    const shortStr = text.substring(0, 50) + (text.length > 50 ? '...' : '');
    return $('<div>').text(shortStr).html();
}

function renderAuditDescription(description, diffCount) {
    if (!description) return '-';
    const diffBadge = diffCount > 0 ? `<div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-1">มีการแก้ไข ${diffCount} ฟิลด์</div>` : '';
    const fullHtml = highlightAuditText(description);

    if (description.length > 45 || description.includes('ขอรับรหัสผ่านถอดรหัสไฟล์') || description.includes('สำรองฐานข้อมูล') || description.includes('ดาวน์โหลดไฟล์สำรอง')) {
        const previewHtml = getAuditPreviewHtml(description);
        return `
            <div class="desc-container" style="max-width: 320px; white-space: normal; word-break: break-word;">
                <div class="text-xs text-slate-800 dark:text-slate-100 font-medium leading-relaxed">
                    <span class="desc-short-text">${previewHtml}</span>
                    <span class="desc-full-text" style="display: none;">${fullHtml}</span>
                </div>
                <div class="mt-1">
                    <button type="button" onclick="toggleAuditDesc(this)" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold text-[11px] inline-flex items-center gap-1 cursor-pointer">
                        <span class="desc-btn-label">เพิ่มเติม</span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-200"></i>
                    </button>
                </div>
                ${diffBadge}
            </div>
        `;
    } else {
        return `
            <div class="desc-container" style="max-width: 320px; white-space: normal; word-break: break-word;">
                <div class="text-xs text-slate-800 dark:text-slate-100 font-medium leading-relaxed">
                    ${fullHtml}
                </div>
                ${diffBadge}
            </div>
        `;
    }
}

window.toggleAuditDesc = function(btn) {
    const container = btn.closest('.desc-container');
    if (!container) return;
    const shortText = container.querySelector('.desc-short-text');
    const fullText = container.querySelector('.desc-full-text');
    const btnLabel = container.querySelector('.desc-btn-label');
    const icon = btn.querySelector('i');

    if (fullText && shortText) {
        if (fullText.style.display === 'none' || fullText.style.display === '') {
            fullText.style.display = 'inline';
            shortText.style.display = 'none';
            if (btnLabel) btnLabel.textContent = 'ย่อลง';
            if (icon) icon.classList.add('rotate-180');
        } else {
            fullText.style.display = 'none';
            shortText.style.display = 'inline';
            if (btnLabel) btnLabel.textContent = 'เพิ่มเติม';
            if (icon) icon.classList.remove('rotate-180');
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
        
        // Filter State
        filterOpen: {{ (request('action') || request('module') || request('start_date') || request('end_date') || request('search')) ? 'true' : 'false' }},
        filters: {
            action: '{{ request("action", "all") }}',
            module: '{{ request("module", "all") }}',
            startDate: '{{ request("start_date", "") }}',
            endDate: '{{ request("end_date", "") }}',
            keyword: '{{ request("search", "") }}'
        },

        activeFilterCount: 0,

        updateFilterCount() {
            let count = 0;
            if (this.filters.action && this.filters.action !== 'all') count++;
            if (this.filters.module && this.filters.module !== 'all') count++;
            if (this.filters.startDate) count++;
            if (this.filters.endDate) count++;
            if (this.filters.keyword && this.filters.keyword.trim()) count++;
            this.activeFilterCount = count;
            return count;
        },

        toggleFilter() {
            this.filterOpen = !this.filterOpen;
        },

        setQuickAction(act) {
            this.activeTab = 'logs';
            // Toggle to 'all' if clicking the active filter, otherwise switch to act
            this.filters.action = (this.filters.action === act && act !== 'all') ? 'all' : act;
            this.applyFilters();
        },

        setDateShortcut(preset) {
            const today = new Date();
            const formatYMD = d => {
                const yr = d.getFullYear();
                const mo = String(d.getMonth() + 1).padStart(2, '0');
                const da = String(d.getDate()).padStart(2, '0');
                return `${yr}-${mo}-${da}`;
            };

            if (preset === 'today') {
                this.filters.startDate = formatYMD(today);
                this.filters.endDate = formatYMD(today);
            } else if (preset === '7days') {
                const past = new Date();
                past.setDate(past.getDate() - 7);
                this.filters.startDate = formatYMD(past);
                this.filters.endDate = formatYMD(today);
            } else if (preset === '30days') {
                const past = new Date();
                past.setDate(past.getDate() - 30);
                this.filters.startDate = formatYMD(past);
                this.filters.endDate = formatYMD(today);
            } else if (preset === 'all') {
                this.filters.startDate = '';
                this.filters.endDate = '';
            }
            this.applyFilters();
        },

        applyFilters() {
            this.updateFilterCount();
            let dt = window.auditDataTable;
            if ((!dt || typeof dt.draw !== 'function') && window.$ && window.$.fn && window.$.fn.DataTable && $.fn.DataTable.isDataTable('#auditLogsDataTable')) {
                dt = $('#auditLogsDataTable').DataTable();
                window.auditDataTable = dt;
            }
            if (dt && typeof dt.draw === 'function') {
                dt.draw();
            }
        },

        resetFilters() {
            this.filters.action = 'all';
            this.filters.module = 'all';
            this.filters.startDate = '';
            this.filters.endDate = '';
            this.filters.keyword = '';
            this.applyFilters();
        },
        
        // Real-time Engine State
        isLive: true,
        isRefreshing: false,
        lastKnownId: {{ \App\Models\SystemAuditLog::max('id') ?? 0 }},
        lastSyncedTime: '{{ (new \App\Models\SystemAuditLog(["created_at" => now()]))->thai_time }}',
        liveTimer: null,
        metrics: @json($metrics),

        initApp() {
            window.auditApp = this;
            this.updateFilterCount();
            this.initDataTable();
            this.startLivePolling();
            if (this.activeFilterCount > 0) {
                this.applyFilters();
            }
        },

        initDataTable() {
            const self = this;

            // Register custom DataTable filter once
            if (!window.auditDataTableSearchRegistered) {
                window.auditDataTableSearchRegistered = true;

                const auditFilterFn = function(settings, data, dataIndex) {
                    if (!settings.nTable || settings.nTable.id !== 'auditLogsDataTable') {
                        return true;
                    }

                    const app = window.auditApp;
                    if (!app || !app.filters) return true;

                    const filters = app.filters;

                    const hasAction = filters.action && filters.action !== 'all';
                    const hasModule = filters.module && filters.module !== 'all';
                    const hasStartDate = Boolean(filters.startDate);
                    const hasEndDate = Boolean(filters.endDate);
                    const hasKeyword = Boolean(filters.keyword && filters.keyword.trim());

                    // Fast path if no active filter
                    if (!hasAction && !hasModule && !hasStartDate && !hasEndDate && !hasKeyword) {
                        return true;
                    }

                    try {
                        // Extract data safely from both rendered array (data) and raw HTML cache (_aData)
                        const aoRow = (settings.aoData && settings.aoData[dataIndex]) ? settings.aoData[dataIndex] : null;
                        const rawRowData = aoRow ? aoRow._aData : null;

                        const rawAct = (rawRowData && rawRowData[3]) ? String(rawRowData[3]).toLowerCase() : '';
                        const rawMod = (rawRowData && rawRowData[4]) ? String(rawRowData[4]).toLowerCase() : '';
                        const rawDesc = (rawRowData && rawRowData[5]) ? String(rawRowData[5]).toLowerCase() : '';
                        const rawUser = (rawRowData && rawRowData[2]) ? String(rawRowData[2]).toLowerCase() : '';
                        const rawIp = (rawRowData && rawRowData[6]) ? String(rawRowData[6]).toLowerCase() : '';

                        const actText = ((data[3] || '') + ' ' + rawAct).toLowerCase();
                        const modText = ((data[4] || '') + ' ' + rawMod).toLowerCase();
                        const descText = ((data[5] || '') + ' ' + rawDesc).toLowerCase();
                        const userText = ((data[2] || '') + ' ' + rawUser).toLowerCase();
                        const ipText = ((data[6] || '') + ' ' + rawIp).toLowerCase();

                    // 1. Action filter
                    if (hasAction) {
                        if (filters.action === 'backup') {
                            const isBackup = actText.includes('backup') || 
                                             actText.includes('สำรอง') || 
                                             descText.includes('สำรองฐานข้อมูล') || 
                                             descText.includes('สำรอง') || 
                                             descText.includes('backup_db') ||
                                             descText.includes('backup');
                            if (!isBackup) return false;
                        } else if (filters.action === 'created') {
                            const isCreated = actText.includes('created') || 
                                              actText.includes('create') || 
                                              actText.includes('สร้าง') || 
                                              actText.includes('เพิ่ม') || 
                                              descText.includes('สร้าง') || 
                                              descText.includes('เพิ่ม');
                            if (!isCreated) return false;
                        } else if (filters.action === 'updated') {
                            const isUpdated = actText.includes('updated') || 
                                              actText.includes('update') || 
                                              actText.includes('แก้ไข') || 
                                              actText.includes('ปรับปรุง') || 
                                              descText.includes('แก้ไข') || 
                                              descText.includes('ปรับปรุง') || 
                                              descText.includes('ปิดการรับสมัคร');
                            if (!isUpdated) return false;
                        } else if (filters.action === 'deleted') {
                            const isDeleted = actText.includes('deleted') || 
                                              actText.includes('delete') || 
                                              actText.includes('ลบ') || 
                                              actText.includes('ล้าง') || 
                                              descText.includes('ลบ') || 
                                              descText.includes('ล้าง');
                            if (!isDeleted) return false;
                        } else if (filters.action === 'password') {
                            const isPassword = descText.includes('ขอรับรหัสผ่าน') || 
                                               descText.includes('รหัสผ่าน') || 
                                               actText.includes('password') ||
                                               descText.includes('password');
                            if (!isPassword) return false;
                        } else if (filters.action === 'login') {
                            const isLogin = actText.includes('login') || 
                                            actText.includes('logout') || 
                                            actText.includes('เข้าสู่ระบบ') || 
                                            descText.includes('เข้าสู่ระบบ') || 
                                            descText.includes('ยืนยันตัวตน') ||
                                            descText.includes('microsoft 365');
                            if (!isLogin) return false;
                        } else if (filters.action === 'security') {
                            const isSecurity = actText.includes('security') || 
                                               actText.includes('warning') || 
                                               actText.includes('เตือน') || 
                                               actText.includes('login') || 
                                               descText.includes('ไม่ผ่านการยืนยัน') || 
                                               descText.includes('ปฏิเสธการเข้าถึง') || 
                                               descText.includes('ยืนยันตัวตน') || 
                                               descText.includes('เข้าสู่ระบบ') || 
                                               descText.includes('ความปลอดภัย');
                            if (!isSecurity) return false;
                        } else if (filters.action === 'exported') {
                            const isExport = actText.includes('export') || 
                                             actText.includes('ส่งออก') || 
                                             actText.includes('ดาวน์โหลด') || 
                                             descText.includes('ส่งออก') || 
                                             descText.includes('ดาวน์โหลด');
                            if (!isExport) return false;
                        } else {
                            const target = filters.action.toLowerCase();
                            if (!actText.includes(target) && !descText.includes(target)) {
                                return false;
                            }
                        }
                    }

                    // 2. Module filter
                    if (hasModule) {
                        const targetMod = filters.module.toLowerCase();
                        if (!modText.includes(targetMod)) {
                            return false;
                        }
                    }

                    // 3. Date range filter
                    if (hasStartDate || hasEndDate) {
                        let timestamp = 0;
                        if (aoRow) {
                            if (aoRow._aSortData && aoRow._aSortData[1]) {
                                timestamp = parseInt(aoRow._aSortData[1], 10);
                            }
                            if (!timestamp && rawRowData && rawRowData[1]) {
                                const m = String(rawRowData[1]).match(/data-order=["']?(\d+)["']?/);
                                if (m) timestamp = parseInt(m[1], 10);
                            }
                            if (!timestamp && aoRow.anCells && aoRow.anCells[1]) {
                                const orderAttr = aoRow.anCells[1].getAttribute('data-order');
                                if (orderAttr) timestamp = parseInt(orderAttr, 10);
                            }
                        }

                        if (timestamp > 0) {
                            const d = new Date(timestamp * 1000);
                            const yr = d.getFullYear();
                            const mo = String(d.getMonth() + 1).padStart(2, '0');
                            const da = String(d.getDate()).padStart(2, '0');
                            const rowDateStr = `${yr}-${mo}-${da}`;

                            if (filters.startDate && rowDateStr < filters.startDate) {
                                return false;
                            }
                            if (filters.endDate && rowDateStr > filters.endDate) {
                                return false;
                            }
                        }
                    }

                    // 4. Keyword filter (User, Module, Action, Description, IP)
                    if (hasKeyword) {
                        const kw = filters.keyword.trim().toLowerCase();
                        const allRowText = userText + ' ' + descText + ' ' + ipText + ' ' + actText + ' ' + modText;
                        if (!allRowText.includes(kw)) {
                            return false;
                        }
                    }

                        return true;
                    } catch (e) {
                        console.error('Audit DataTable filter notice:', e);
                        return true;
                    }
                };

                if (window.$ && window.$.fn && window.$.fn.dataTable && window.$.fn.dataTable.ext) {
                    window.$.fn.dataTable.ext.search.push(auditFilterFn);
                } else if (window.DataTable && window.DataTable.ext) {
                    window.DataTable.ext.search.push(auditFilterFn);
                }
            }

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
