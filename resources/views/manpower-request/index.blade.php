<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
                    พิจารณาคำขอ HR
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">ติดตามและตรวจสอบรายการคำร้องทั้งหมด (ใบขออนุมัติกำลังคน, แบบประเมินทดลองงาน, แบบประเมินผลสัมภาษณ์)</p>
            </div>
            <div class="flex gap-3">
                
                
            </div>
        </div>
    </x-slot>

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, button, input, select, textarea, .font-sans, h1, h2, h3, h4, h5, h6, table, td, th {
            font-family: 'Prompt', 'Kanit', 'Inter', sans-serif !important;
        }

        /* ── Tab styles ─────────────────────────── */
        .tab-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
            white-space: nowrap;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
            outline: none;
        }
        .tab-btn:hover { color: #4f46e5; }
        .tab-btn.active { color: #4f46e5; border-bottom-color: #4f46e5; }
        .tab-btn.active-green { color: #059669; border-bottom-color: #059669; }
        .tab-btn:hover.active-green-hover { color: #059669; }

        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeIn 0.25s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── KPI Cards ─────────────────────────── */
        .kpi-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.05);
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .kpi-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1); transform: translateY(-2px); }
        .kpi-icon {
            width: 2.75rem; height: 2.75rem;
            border-radius: 0.75rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .dark .kpi-card { background: #1f2937; border-color: #374151; }

        /* ── DataTable overrides ─────────────────── */
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
            background-position: right 0.6rem center !important;
            background-repeat: no-repeat !important;
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
        }
        .dark .dataTables_wrapper table.dataTable {
            border-top-color: #374151 !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem;
            margin-left: 0.5rem;
            color: #1e293b !important;
            background-color: #fff;
        }
        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #374151;
            border-color: #4b5563 !important;
            color: #f1f5f9 !important;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
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
        table.dataTable { 
            border-collapse: separate !important; 
            border-spacing: 0 !important;
            width: 100% !important;
            color: #1e293b;
        }
        .dark table.dataTable {
            color: #f1f5f9;
        }
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter { float: none; text-align: left; margin-bottom: 0.75rem; }
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate { float: none; text-align: center; margin-top: 0.5rem; }
            .header-actions { flex-direction: column; gap: 0.5rem; }
            .tab-btn { padding: 0.625rem 1rem; font-size: 0.8rem; }
        }

        /* ── Status badges ─────────────────────── */
        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.03em; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }

        /* ── Action button ─────────────────────── */
        .btn-view {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem;
            border-radius: 0.5rem;
            color: #6366f1;
            background: #eef2ff;
            transition: background 0.15s, color 0.15s;
        }
        .btn-view:hover { background: #6366f1; color: #fff; }

        .btn-share {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem;
            border-radius: 0.5rem;
            color: #0284c7;
            background: #f0f9ff;
            transition: background 0.15s, color 0.15s;
            border: none;
            cursor: pointer;
        }
        .btn-share:hover { background: #0284c7; color: #fff; }

        .btn-delete {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem;
            border-radius: 0.5rem;
            color: #ef4444;
            background: #fef2f2;
            transition: background 0.15s, color 0.15s;
            border: none;
            cursor: pointer;
        }
        .btn-delete:hover { background: #ef4444; color: #fff; }

        /* ── Section card ─────────────────────── */
        .section-card {
            background: #fff;
            color: #1e293b;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.07);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }
        .dark .section-card { background: #1f2937; color: #f1f5f9; border-color: #374151; }
    </style>

    <div class="py-6 sm:py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-[1700px] w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10">
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Left: Form Categories Sidebar -->
                <x-hr-forms-sidebar />

                <!-- Right: Main Table Content Container -->
                <div class="flex-1 w-full min-w-0 space-y-6">

            {{-- Toast success --}}
            @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: 'สำเร็จ!', text: '{{ session('success') }}',
                        showConfirmButton: false, timer: 3000, timerProgressBar: true
                    });
                });
            </script>
            @endif

            {{-- KPI counts are now computed server-side in the controller (independent of table pagination) --}}

            {{-- ════════════════════════════════════════════
                 SECTION 0 : APPROVALS AWAITING MY ACTION
            ════════════════════════════════════════════ --}}
            @php
                $totalPendingAction = ($pendingApprovals->count() ?? 0) + ($pendingProbations->count() ?? 0) + ($pendingInterviews->count() ?? 0);
            @endphp
            @if($totalPendingAction > 0)
            <div class="section-card border-2 border-amber-200 dark:border-amber-900/50 shadow-md">
                <div class="px-6 pt-5 pb-4 bg-amber-50/60 dark:bg-amber-950/20 border-b border-amber-100 dark:border-amber-900/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-500 text-white shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    รายการคำขอรอการอนุมัติ/ลงนามจากคุณ
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm">
                                        {{ $totalPendingAction }} รายการ
                                    </span>
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ใบขอกำลังคน, แบบประเมินทดลองงาน และแบบประเมินสัมภาษณ์ที่รอการลงนาม/อนุมัติจากคุณ</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table id="dataTablePendingAction" class="display responsive nowrap w-full text-sm">
                        <thead>
                            <tr>
                                <th class="all whitespace-nowrap">ประเภทคำขอ</th>
                                <th class="whitespace-nowrap text-center" style="width: 80px;">ID</th>
                                <th class="whitespace-nowrap" style="width: 110px;">วันที่</th>
                                <th class="all min-w-[160px]">ชื่อพนักงาน / ผู้ร้องขอ</th>
                                <th class="min-w-[140px]">ฝ่าย / แผนก</th>
                                <th class="min-w-[140px]">ชื่อตำแหน่ง</th>
                                <th class="all whitespace-nowrap text-center" style="width: 110px;">สถานะ</th>
                                <th class="all text-right whitespace-nowrap" style="width: 90px;">ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingApprovals as $appReq)
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <span class="badge badge-blue shadow-xs">ใบขอกำลังคน</span>
                                    </td>
                                    <td data-order="{{ $appReq->id }}" class="text-center font-semibold text-gray-700 dark:text-gray-300">
                                        #{{ $appReq->id }}
                                    </td>
                                    <td data-order="{{ $appReq->date }}" class="whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                            <span>{{ \Carbon\Carbon::parse($appReq->date)->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="font-medium text-gray-800 dark:text-gray-200">{{ $appReq->requester_name ?: '-' }}</td>
                                    <td class="text-gray-600 dark:text-gray-300">
                                        @php
                                            $deptFormatted = \App\Http\Controllers\ManpowerRequestController::formatDeptSection($appReq->department, $appReq->section);
                                            $deptFull = 'ฝ่าย: ' . ($appReq->department ?: '-') . ' | แผนก: ' . ($appReq->section ?: '-');
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 max-w-[200px] truncate" title="{{ $deptFull }}">
                                            <i class="fa-regular fa-building text-gray-400 text-xs shrink-0"></i>
                                            <span class="truncate font-medium">{{ $deptFormatted }}</span>
                                        </span>
                                    </td>
                                    <td class="font-medium text-gray-900 dark:text-gray-100">{{ $appReq->job_title_th }}</td>
                                    <td class="text-center whitespace-nowrap">
                                        @php
                                            $appStatusMap = [
                                                'pending_manager' => ['badge-blue', 'รอ ผจก.แผนก'],
                                                'pending_vp' => ['badge-purple', 'รอ ปธ.สายงาน'],
                                                'pending_hr' => ['badge-yellow', 'รอ ผจก.HR'],
                                                'pending_ceo' => ['badge-yellow', 'รอ CEO'],
                                            ];
                                            [$aClass, $aText] = $appStatusMap[$appReq->status] ?? ['badge-gray', $appReq->status];
                                        @endphp
                                        <span class="badge {{ $aClass }}">{{ $aText }}</span>
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <a href="{{ route('manpower-request.show', $appReq->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                            <span>พิจารณา</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($pendingProbations as $appPro)
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <span class="badge badge-green shadow-xs">ประเมินทดลองงาน</span>
                                    </td>
                                    <td data-order="{{ $appPro->id }}" class="text-center font-semibold text-gray-700 dark:text-gray-300">
                                        #{{ $appPro->id }}
                                    </td>
                                    <td data-order="{{ $appPro->start_date ?? '1970-01-01' }}" class="whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                            <span>{{ $appPro->start_date ? \Carbon\Carbon::parse($appPro->start_date)->format('d/m/Y') : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="font-medium text-gray-800 dark:text-gray-200">{{ ($appPro->prefix ?? '') . ($appPro->employee_name ?? 'ไม่ระบุ') }}</td>
                                    <td class="text-gray-600 dark:text-gray-300">
                                        @php
                                            $probDeptFormatted = \App\Http\Controllers\ManpowerRequestController::formatDeptSection($appPro->department);
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 max-w-[200px] truncate" title="{{ $appPro->department ?: '-' }}">
                                            <i class="fa-regular fa-building text-gray-400 text-xs shrink-0"></i>
                                            <span class="truncate font-medium">{{ $probDeptFormatted }}</span>
                                        </span>
                                    </td>
                                    <td class="font-medium text-gray-900 dark:text-gray-100">{{ $appPro->position ?: '-' }}</td>
                                    <td class="text-center whitespace-nowrap">
                                        <span class="badge badge-yellow">รอการพิจารณา</span>
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <a href="{{ route('probation-evaluation.show', $appPro->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                            <span>ตรวจสอบ</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($pendingInterviews as $appInt)
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <span class="badge badge-purple shadow-xs">ประเมินสัมภาษณ์</span>
                                    </td>
                                    <td data-order="{{ $appInt->id }}" class="text-center font-semibold text-gray-700 dark:text-gray-300">
                                        #{{ $appInt->id }}
                                    </td>
                                    <td data-order="{{ $appInt->evaluation_date ?? '1970-01-01' }}" class="whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                            <span>{{ $appInt->evaluation_date ? \Carbon\Carbon::parse($appInt->evaluation_date)->format('d/m/Y') : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="font-medium text-gray-800 dark:text-gray-200">{{ ($appInt->candidate_prefix ?? '') . ($appInt->candidate_name ?? 'ไม่ระบุ') }}</td>
                                    <td class="text-gray-600 dark:text-gray-300">
                                        @php
                                            $intDeptFormatted = \App\Http\Controllers\ManpowerRequestController::formatDeptSection($appInt->department, $appInt->division);
                                            $intDeptFull = 'แผนก: ' . ($appInt->department ?: '-') . ' | ฝ่าย: ' . ($appInt->division ?: '-');
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 max-w-[200px] truncate" title="{{ $intDeptFull }}">
                                            <i class="fa-regular fa-building text-gray-400 text-xs shrink-0"></i>
                                            <span class="truncate font-medium">{{ $intDeptFormatted }}</span>
                                        </span>
                                    </td>
                                    <td class="font-medium text-gray-900 dark:text-gray-100">{{ $appInt->position_applied_for ?: '-' }}</td>
                                    <td class="text-center whitespace-nowrap">
                                        <span class="badge badge-yellow">รอการพิจารณา</span>
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <a href="{{ route('interview-evaluation.show', $appInt->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                            <span>ตรวจสอบ</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- ════════════════════════════════════════════
                 SECTION 1 : ALL FORMS (UNIFIED)
            ════════════════════════════════════════════ --}}
            <div class="section-card">
                <div class="px-6 pt-6 pb-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">รายการแบบฟอร์มทั้งหมด <span id="allFormsCountBadge" class="text-sm font-normal text-gray-400">({{ $allFormRequests->count() }} รายการ)</span></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">All Forms (QF-HR-13, QF-HR-18, QF-HR-25)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700"></div>

                <div class="p-4 sm:p-6">
                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <div class="relative flex-1 min-w-[220px] max-w-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="customSearch" oninput="window.triggerFilter && window.triggerFilter()" onkeyup="window.triggerFilter && window.triggerFilter()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="ค้นหารายละเอียด...">
                        </div>
                        <select id="filterType" onchange="window.triggerFilter && window.triggerFilter()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-2.5 pl-3.5 pr-9 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer min-w-[175px]">
                            <option value="">ทุกประเภทแบบฟอร์ม</option>
                            <option value="ใบขออนุมัติกำลังคน">ใบขออนุมัติกำลังคน (QF-HR-13)</option>
                            <option value="แบบประเมินทดลองงาน">แบบประเมินทดลองงาน (QF-HR-18)</option>
                            <option value="แบบประเมินผลสัมภาษณ์">แบบประเมินผลสัมภาษณ์ (QF-HR-25)</option>
                        </select>
                        <select id="filterStatus" onchange="window.triggerFilter && window.triggerFilter()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-2.5 pl-3.5 pr-9 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer min-w-[140px]">
                            <option value="">ทุกสถานะ</option>
                            <option value="pending">รอพิจารณา / รอลงนาม</option>
                            <option value="approved">อนุมัติแล้ว / เสร็จสิ้น</option>
                            <option value="rejected">ไม่อนุมัติ / ยกเลิก</option>
                        </select>
                        <select id="filterShare" onchange="window.triggerFilter && window.triggerFilter()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-2.5 pl-3.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer min-w-[210px]">
                            <option value="">สถานะการแชร์ทั้งหมด</option>
                            <option value="shared">เฉพาะรายการที่แชร์</option>
                            <option value="not_shared">เฉพาะรายการที่ไม่ได้แชร์</option>
                        </select>
                        <button type="button" onclick="window.resetFilters && window.resetFilters()" class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/80 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors cursor-pointer" title="ล้างค่าตัวกรองทั้งหมด">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                            <span>ล้างตัวกรอง</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-2xs p-3.5 sm:p-5 bg-white dark:bg-gray-800">
                        <table id="dataTableAllForms" class="display responsive nowrap w-full text-xs sm:text-sm">
                            <thead>
                                <tr>
                                    <th class="all whitespace-nowrap text-center" style="min-width: 105px;">ประเภทแบบฟอร์ม</th>
                                    <th class="whitespace-nowrap text-center" style="width: 50px;">ID</th>
                                    <th class="whitespace-nowrap text-center" style="min-width: 95px;">วันที่ยื่นเรื่อง</th>
                                    <th class="all whitespace-nowrap text-left" style="min-width: 140px;">รายละเอียด</th>
                                    <th class="whitespace-nowrap text-left" style="min-width: 110px;">ฝ่าย / แผนก</th>
                                    <th class="whitespace-nowrap text-center" style="min-width: 110px;">การแชร์</th>
                                    <th class="all whitespace-nowrap text-center" style="min-width: 90px;">สถานะ</th>
                                    <th class="text-center all whitespace-nowrap" style="width: 75px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allFormRequests as $row)
                                <tr data-form-type="{{ $row['form_type'] }}" data-status="{{ $row['status'] }}" data-is-shared="{{ !empty($row['is_shared']) ? '1' : '0' }}">
                                    <td class="text-center whitespace-nowrap">
                                        <span class="badge {{ $row['form_badge'] }} shadow-xs">{{ $row['form_type'] }}</span>
                                    </td>
                                    <td data-order="{{ $row['id'] }}" class="text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        #{{ $row['id'] }}
                                    </td>
                                    <td data-order="{{ $row['raw_date'] ?? $row['date'] }}" class="text-center whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                            <span>{{ $row['date'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-left whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{!! $row['details'] !!}</div>
                                    </td>
                                    <td class="text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 max-w-[200px] truncate" title="{{ $row['department_full'] ?? $row['department'] }}">
                                            <i class="fa-regular fa-building text-gray-400 text-xs shrink-0"></i>
                                            <span class="truncate font-medium">{{ $row['department'] }}</span>
                                        </span>
                                    </td>
                                    <td class="text-center whitespace-nowrap">
                                        @if(!empty($row['is_shared']))
                                            @if(!empty($row['is_revoked']))
                                                {{-- Revoked share: show with red/gray style --}}
                                                <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap text-[11px] text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 rounded-md px-2 py-0.5 shadow-2xs opacity-80"
                                                     title="แชร์ถูกยกเลิกแล้ว&#10;แชร์เมื่อ {{ $row['shared_at'] ?? '-' }}&#10;ผู้แชร์: {{ $row['shared_by_name'] ?? '-' }}&#10;ผู้รับ: {{ $row['shared_to_name'] ?? '-' }}">
                                                    <i class="fa-solid fa-ban text-red-400 dark:text-red-500 text-[10px] shrink-0"></i>
                                                    <span class="line-through">{{ $row['shared_by_name'] ?? '-' }}</span>
                                                    <span class="text-red-300 dark:text-red-600">→</span>
                                                    <span class="font-medium line-through">{{ $row['shared_to_name'] ?? '-' }}</span>
                                                    <span class="px-1 py-0 rounded text-[9px] font-bold bg-red-100 dark:bg-red-900/50 text-red-500 dark:text-red-400 ml-0.5">ยกเลิก</span>
                                                </div>
                                            @else
                                                {{-- Active share: show with blue style --}}
                                                <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap text-[11px] text-sky-800 dark:text-sky-200 bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800/70 rounded-md px-2 py-0.5 shadow-2xs" 
                                                     title="แชร์เมื่อ {{ $row['shared_at'] ?? '-' }}&#10;ผู้แชร์: {{ $row['shared_by_name'] ?? '-' }}&#10;ผู้รับ: {{ $row['shared_to_name'] ?? '-' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-sky-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                                    </svg>
                                                    <span>{{ $row['shared_by_name'] ?? '-' }}</span>
                                                    <span class="text-sky-400 dark:text-sky-500">→</span>
                                                    <span class="font-medium text-sky-900 dark:text-sky-100">{{ $row['shared_to_name'] ?? '-' }}</span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-300 dark:text-gray-600">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center whitespace-nowrap">{!! $row['status_label'] !!}</td>
                                    <td class="text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ $row['show_url'] }}" class="btn-view" title="ดูรายละเอียด">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                            </a>
                                            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->isHrOrAdmin()))
                                                 <button type="button" onclick="openShareModal('{{ $row['type_code'] ?? 'manpower_request' }}', {{ $row['id'] }}, '{{ addslashes($row['form_type']) }} #{{ $row['id'] }}')" class="btn-share" title="แชร์เอกสารนี้">
                                                     <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                         <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                                     </svg>
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
            </div>

                </div>
            </div>
        </div>
    </div>

    @include('components.form-share-modal')

    <script>
        // Pure Instant Real-time Filter Engine (0ms Latency, zero dependencies)
        function executeRealtimeFilter() {
            var searchInput = document.getElementById('customSearch');
            var typeSelect = document.getElementById('filterType');
            var statusSelect = document.getElementById('filterStatus');
            var shareSelect = document.getElementById('filterShare');

            var searchVal = (searchInput ? searchInput.value : '').toLowerCase().trim();
            var typeVal = (typeSelect ? typeSelect.value : '').trim();
            var statusVal = (statusSelect ? statusSelect.value : '').trim();
            var shareVal = (shareSelect ? shareSelect.value : '').trim();

            // If DataTables is active, use DataTables search & redraw
            if (window.tableAll && typeof window.tableAll.draw === 'function') {
                window.tableAll.search(searchVal).draw();
                return;
            }

            // Direct DOM Real-time Filter
            var tbody = document.querySelector('#dataTableAllForms tbody');
            if (!tbody) return;

            var rows = tbody.querySelectorAll('tr:not(.empty-state-row)');
            var visibleCount = 0;

            rows.forEach(function(tr) {
                var rowText = (tr.textContent || tr.innerText || '').toLowerCase();
                var rowType = tr.getAttribute('data-form-type') || '';
                var rowStatus = (tr.getAttribute('data-status') || '').toLowerCase();
                var isShared = tr.getAttribute('data-is-shared') === '1';

                // 1. Text Search Filter (Real-time)
                var matchSearch = !searchVal || (rowText.indexOf(searchVal) !== -1);

                // 2. Form Type Filter
                var matchType = !typeVal || (rowType.indexOf(typeVal) !== -1) || (rowText.indexOf(typeVal.toLowerCase()) !== -1);

                // 3. Share Status Filter
                var matchShare = true;
                if (shareVal === 'shared') {
                    matchShare = isShared || (tr.children[5] && tr.children[5].innerText.trim() !== '-' && tr.children[5].innerText.trim() !== '');
                } else if (shareVal === 'not_shared') {
                    matchShare = !isShared && (!tr.children[5] || tr.children[5].innerText.trim() === '-' || tr.children[5].innerText.trim() === '');
                }

                // 4. Status Filter
                var matchStatus = true;
                if (statusVal === 'pending') {
                    matchStatus = rowStatus.indexOf('pending') !== -1 ||
                                  rowStatus === 'draft' ||
                                  rowStatus === 'submitted' ||
                                  rowText.indexOf('รอ') !== -1 ||
                                  rowText.indexOf('ร่าง') !== -1;
                } else if (statusVal === 'approved') {
                    matchStatus = rowStatus.indexOf('approved') !== -1 ||
                                  rowStatus === 'completed' ||
                                  rowText.indexOf('อนุมัติ') !== -1 ||
                                  rowText.indexOf('ลงนามครบ') !== -1;
                } else if (statusVal === 'rejected') {
                    matchStatus = rowStatus.indexOf('reject') !== -1 ||
                                  rowStatus.indexOf('cancel') !== -1 ||
                                  rowText.indexOf('ไม่อนุมัติ') !== -1 ||
                                  rowText.indexOf('ยกเลิก') !== -1;
                }

                if (matchSearch && matchType && matchShare && matchStatus) {
                    tr.style.display = '';
                    visibleCount++;
                } else {
                    tr.style.display = 'none';
                }
            });

            // Update Counter Badge
            var badge = document.getElementById('allFormsCountBadge');
            if (badge) {
                badge.textContent = '(' + visibleCount + ' รายการ)';
            }

            // Show Empty State if 0 matching items
            var emptyRow = tbody.querySelector('.empty-state-row');
            if (visibleCount === 0) {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.className = 'empty-state-row';
                    emptyRow.innerHTML = '<td colspan="8" class="text-center py-10 text-gray-500 dark:text-gray-400 font-medium"><div class="flex flex-col items-center justify-center gap-2"><i class="fa-solid fa-inbox text-3xl text-gray-300 dark:text-gray-600"></i><span class="text-sm">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</span></div></td>';
                    tbody.appendChild(emptyRow);
                } else {
                    emptyRow.style.display = '';
                }
            } else if (emptyRow) {
                emptyRow.style.display = 'none';
            }
        }

        window.triggerFilter = executeRealtimeFilter;
        window.resetFilters = function() {
            var searchInput = document.getElementById('customSearch'); if (searchInput) searchInput.value = '';
            var typeSelect = document.getElementById('filterType'); if (typeSelect) typeSelect.value = '';
            var statusSelect = document.getElementById('filterStatus'); if (statusSelect) statusSelect.value = '';
            var shareSelect = document.getElementById('filterShare'); if (shareSelect) shareSelect.value = '';
            executeRealtimeFilter();
        };

        // Bind immediate events on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            var s = document.getElementById('customSearch');
            if (s) {
                s.addEventListener('input', executeRealtimeFilter);
                s.addEventListener('keyup', executeRealtimeFilter);
                s.addEventListener('paste', executeRealtimeFilter);
                s.addEventListener('search', executeRealtimeFilter);
            }
            ['filterType', 'filterStatus', 'filterShare'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', executeRealtimeFilter);
                    el.addEventListener('input', executeRealtimeFilter);
                }
            });
        });
    </script>

    @push('scripts')
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

            // 1. Pending Approvals Table (Section 0)
            if ($('#dataTablePendingAction').length) {
                $('#dataTablePendingAction').DataTable({
                    language: thLanguage,
                    order: [[1, 'desc']],
                    responsive: true,
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "ทั้งหมด"]],
                    dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3"l>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-3"ip>',
                    columnDefs: [
                        { targets: [7], orderable: false, searchable: false }
                    ]
                });
            }

            // 2. All Forms Unified Table (Section 1)
            window.tableAll = $('#dataTableAllForms').DataTable({
                language: thLanguage,
                order: [[2, 'desc']],
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"l>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4"ip>',
                columnDefs: [
                    { targets: [7], orderable: false, searchable: false }
                ]
            });

            // Update Counter Badge on DataTable draw
            $('#dataTableAllForms').on('draw.dt', function() {
                var info = window.tableAll.page.info();
                var badge = document.getElementById('allFormsCountBadge');
                if (badge) {
                    badge.textContent = '(' + info.recordsDisplay + ' รายการ)';
                }
            });

            // Custom filter extension logic for DataTables
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var tid = settings.sTableId || (settings.nTable ? settings.nTable.id : '');
                if (tid !== 'dataTableAllForms') return true;

                var typeFilter = ($('#filterType').val() || '').trim();
                var statusFilter = ($('#filterStatus').val() || '').trim();
                var shareFilter = ($('#filterShare').val() || '').trim();

                var row = (settings.aoData && settings.aoData[dataIndex]) ? settings.aoData[dataIndex].nTr : null;

                // 1. Filter by Form Type
                if (typeFilter) {
                    var cellType = (data[0] || '').trim();
                    var rowType = row ? (row.getAttribute('data-form-type') || '') : '';
                    if (cellType.indexOf(typeFilter) === -1 && rowType.indexOf(typeFilter) === -1) {
                        return false;
                    }
                }

                // 2. Filter by Share Status
                if (shareFilter) {
                    var cellShare = (data[5] || '').trim();
                    var isShared = false;
                    if (row && row.hasAttribute('data-is-shared')) {
                        isShared = (row.getAttribute('data-is-shared') === '1');
                    } else {
                        isShared = (cellShare !== '-' && cellShare !== '' && (cellShare.indexOf('→') !== -1 || cellShare.length > 2));
                    }

                    if (shareFilter === 'shared' && !isShared) return false;
                    if (shareFilter === 'not_shared' && isShared) return false;
                }

                // 3. Filter by Status
                if (statusFilter) {
                    var cellStatus = (data[6] || '').toLowerCase();
                    var rowStatus = row ? (row.getAttribute('data-status') || '').toLowerCase() : '';
                    var fullStatus = rowStatus + ' ' + cellStatus;

                    if (statusFilter === 'pending') {
                        var isPending = fullStatus.indexOf('pending') !== -1 ||
                                        fullStatus.indexOf('draft') !== -1 ||
                                        fullStatus.indexOf('submitted') !== -1 ||
                                        fullStatus.indexOf('รอ') !== -1 ||
                                        fullStatus.indexOf('ร่าง') !== -1;
                        if (!isPending) return false;
                    } else if (statusFilter === 'approved') {
                        var isApproved = fullStatus.indexOf('approved') !== -1 ||
                                         fullStatus.indexOf('completed') !== -1 ||
                                         fullStatus.indexOf('อนุมัติ') !== -1 ||
                                         fullStatus.indexOf('ลงนามครบ') !== -1;
                        if (!isApproved) return false;
                    } else if (statusFilter === 'rejected') {
                        var isRejected = fullStatus.indexOf('reject') !== -1 ||
                                         fullStatus.indexOf('cancel') !== -1 ||
                                         fullStatus.indexOf('ไม่อนุมัติ') !== -1 ||
                                         fullStatus.indexOf('ยกเลิก') !== -1;
                        if (!isRejected) return false;
                    }
                }

                return true;
            });
        });

        function deleteFormRequest(deleteUrl) {
            Swal.fire({
                title: 'ยืนยันการลบรายการ?',
                text: 'รายการนี้จะถูกเก็บบันทึกในระบบ (Soft Delete) และสามารถลบออกจากหน้าแสดงผลนี้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ใช่, ต้องการลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ!',
                                text: response.message || 'ลบรายการเรียบร้อยแล้ว',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            location.reload();
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'เกิดข้อผิดพลาดในการลบรายการ';
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: msg
                            });
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
