<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
                    ติดตามสถานะของฉัน
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">ติดตามใบขออนุมัติกำลังคนและแบบประเมินทดลองงานของคุณ</p>
            </div>
            <div class="flex gap-3">
                
                
            </div>
        </div>
    </x-slot>

    {{-- External CSS --}}
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
        .dataTables_wrapper .dataTables_length select {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem;
            background-color: #fff;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem;
            margin-left: 0.5rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem !important;
            padding: 0.25rem 0.75rem !important;
            margin: 0 0.125rem;
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
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: #64748b;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        table.dataTable tbody td { padding: 0.875rem 1rem; font-size: 0.875rem; }
        table.dataTable tbody tr:hover { background-color: #f8fafc; }
        table.dataTable { border-collapse: collapse !important; }

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

        /* ── Section card ─────────────────────── */
        .section-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.07);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }
        .dark .section-card { background: #1f2937; border-color: #374151; }
    </style>

    <div class="py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-3">ประเภทคำขอ</th>
                                <th class="px-4 py-3">ID / วันที่</th>
                                <th class="px-4 py-3">ชื่อพนักงาน/ผู้สมัคร/ผู้ร้องขอ</th>
                                <th class="px-4 py-3">ฝ่าย / แผนก</th>
                                <th class="px-4 py-3">ชื่อตำแหน่ง</th>
                                <th class="px-4 py-3">สถานะ</th>
                                <th class="px-4 py-3 text-right">ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($pendingApprovals as $appReq)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-gray-800/60 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-indigo-600 dark:text-indigo-400">ใบขอกำลังคน</td>
                                    <td class="px-4 py-3 whitespace-nowrap">#{{ $appReq->id }} <span class="text-xs text-gray-400 block">{{ \Carbon\Carbon::parse($appReq->date)->format('d/m/Y') }}</span></td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $appReq->requester_name ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $appReq->department }} / {{ $appReq->section }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $appReq->job_title_th }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
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
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('manpower-request.show', $appReq->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                            <span>พิจารณาอนุมัติ</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($pendingProbations as $appPro)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-gray-800/60 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-emerald-600 dark:text-emerald-400">ประเมินทดลองงาน</td>
                                    <td class="px-4 py-3 whitespace-nowrap">#{{ $appPro->id }} <span class="text-xs text-gray-400 block">{{ $appPro->start_date ? \Carbon\Carbon::parse($appPro->start_date)->format('d/m/Y') : '-' }}</span></td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ ($appPro->prefix ?? '') . ($appPro->employee_name ?? 'ไม่ระบุ') }}</td>
                                    <td class="px-4 py-3">{{ $appPro->department ?: '-' }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $appPro->position ?: '-' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="badge badge-yellow">รอการพิจารณา/ลงนาม</span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('probation-evaluation.show', $appPro->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                            <span>ตรวจสอบ / ลงนาม</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($pendingInterviews as $appInt)
                                <tr class="hover:bg-amber-50/40 dark:hover:bg-gray-800/60 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-purple-600 dark:text-purple-400">ประเมินสัมภาษณ์</td>
                                    <td class="px-4 py-3 whitespace-nowrap">#{{ $appInt->id }} <span class="text-xs text-gray-400 block">{{ $appInt->evaluation_date ? \Carbon\Carbon::parse($appInt->evaluation_date)->format('d/m/Y') : '-' }}</span></td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ ($appInt->candidate_prefix ?? '') . ($appInt->candidate_name ?? 'ไม่ระบุ') }}</td>
                                    <td class="px-4 py-3">{{ $appInt->department ?: '-' }} / {{ $appInt->division ?: '-' }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $appInt->position_applied ?: '-' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="badge badge-purple">รอลงนาม (HA/HR/ต้นสังกัด)</span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('interview-evaluation.show', $appInt->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                            <span>ตรวจสอบ / ลงนาม</span>
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
                 SECTION 1 : MANPOWER REQUESTS
            ════════════════════════════════════════════ --}}
            <div class="section-card">
                {{-- Header --}}
                <div class="px-6 pt-6 pb-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">ใบขออนุมัติกำลังคน</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Manpower Requests</p>
                            </div>
                            @if($pendingMpr > 0)
                                <span class="badge badge-yellow animate-pulse">รอดำเนินการ {{ $pendingMpr }}</span>
                            @endif
                        </div>
                        
                    </div>

                    {{-- KPI Summary --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-indigo-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalMpr }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ทั้งหมด</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon bg-amber-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-amber-600">{{ $pendingMpr }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">รอพิจารณา</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon bg-emerald-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-emerald-600">{{ $approvedMpr }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">อนุมัติแล้ว</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon bg-red-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-red-500">{{ $rejectedMpr }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ไม่อนุมัติ</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100 dark:border-gray-700"></div>

                {{-- Table --}}
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table id="dataTableMPR" class="display responsive nowrap w-full text-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>วันที่ยื่นขอ</th>
                                <th>ฝ่าย / แผนก</th>
                                <th class="all">ชื่อตำแหน่ง</th>
                                <th>ประเภทการจ้าง</th>
                                <th class="all">สถานะ</th>
                                <th class="text-right all"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($manpowerRequests ?? [] as $req)
                                <tr>
                                    <td>#{{ $req->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($req->date)->format('d/m/Y') }}</td>
                                    <td>{{ $req->department }} / {{ $req->section }}</td>
                                    <td class="font-medium">{{ $req->job_title_th }}</td>
                                    <td>{{ $req->hire_type }}</td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'pending_manager' => ['badge-blue', 'รอ ผจก.แผนก'],
                                                'pending_vp' => ['badge-purple', 'รอ ปธ.สายงาน'],
                                                'pending_hr' => ['badge-yellow', 'รอ ผจก.HR'],
                                                'pending_ceo' => ['badge-yellow', 'รอ CEO'],
                                                'approved' => ['badge-green', 'อนุมัติแล้ว'],
                                                'rejected' => ['badge-red', 'ไม่อนุมัติ'],
                                            ];
                                            [$badgeClass, $statusText] = $statusMap[$req->status] ?? ['badge-gray', $req->status];
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('manpower-request.show', $req->id) }}" class="btn-view" title="ดูรายละเอียด">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-gray-400 py-6">ไม่พบข้อมูลใบขออนุมัติกำลังคน</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ════════════════════════════════════════════
                 SECTION 2 : PROBATION EVALUATIONS
            ════════════════════════════════════════════ --}}
            <div class="section-card">
                {{-- Header --}}
                <div class="px-6 pt-6 pb-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">แบบประเมินทดลองงาน</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Probation Evaluations</p>
                            </div>
                            @if($pendingPro > 0)
                                <span class="badge badge-yellow animate-pulse">รอดำเนินการ {{ $pendingPro }}</span>
                            @endif
                        </div>
                        
                    </div>

                    {{-- KPI Summary --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-emerald-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPro }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ทั้งหมด</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon bg-amber-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-amber-600">{{ $pendingPro }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">รอพิจารณา</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon bg-green-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-emerald-600">{{ $approvedPro }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">อนุมัติแล้ว</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon bg-red-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-red-500">{{ $rejectedPro }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ไม่อนุมัติ</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100 dark:border-gray-700"></div>

                {{-- Table --}}
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table id="dataTableProbation" class="display responsive nowrap w-full text-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>วันที่เริ่มงาน</th>
                                <th class="all">ชื่อ-สกุลพนักงาน</th>
                                <th>ตำแหน่ง</th>
                                <th>แผนก</th>
                                <th class="all">สถานะ</th>
                                <th class="text-right all"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($probationEvaluations ?? [] as $prob)
                                <tr>
                                    <td>#{{ $prob->id }}</td>
                                    <td>{{ $prob->start_date ? \Carbon\Carbon::parse($prob->start_date)->format('d/m/Y') : '-' }}</td>
                                    <td class="font-medium">{{ ($prob->prefix ?? '') . ($prob->employee_name ?? 'ไม่ระบุ') }}</td>
                                    <td>{{ $prob->position ?? '-' }}</td>
                                    <td>{{ $prob->department ?? '-' }}</td>
                                    <td>
                                        @php
                                            $pMap = [
                                                'draft' => ['badge-gray', 'แบบร่าง'],
                                                'pending' => ['badge-blue', 'รออนุมัติ'],
                                                'approved' => ['badge-green', 'อนุมัติแล้ว'],
                                                'rejected' => ['badge-red', 'ไม่อนุมัติ'],
                                            ];
                                            [$pClass, $pText] = $pMap[$prob->status] ?? ['badge-gray', $prob->status];
                                        @endphp
                                        <span class="badge {{ $pClass }}">{{ $pText }}</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('probation-evaluation.show', $prob->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white transition-all duration-150" title="ดูรายละเอียด">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-gray-400 py-6">ไม่พบข้อมูลแบบประเมินทดลองงาน</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            const dtLangCdn = { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' };

            const viewIconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>';

            var mprFilter = '';
            var tableMPR = $('#dataTableMPR').DataTable({
                language: dtLangCdn,
                order: [[0, 'desc']],
                responsive: true,
                pageLength: 10,
                serverSide: true,
                processing: true,
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"lf>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4"ip>',
                ajax: {
                    url: '{{ route('manpower-request.data') }}',
                    type: 'GET',
                    data: function(d) { d.status_filter = mprFilter; }
                },
                columns: [
                    { data: 'id', render: (d) => '#' + d },
                    { data: 'date' },
                    { data: 'department' },
                    { data: 'job_title_th', className: 'font-medium' },
                    { data: 'hire_type' },
                    { data: 'status_label', orderable: false, searchable: false },
                    {
                        data: 'show_url', orderable: false, searchable: false, className: 'text-right',
                        render: (url) => '<a href="' + url + '" class="btn-view" title="ดูรายละเอียด">' + viewIconSvg + '</a>',
                    },
                ]
            });

            var proFilter = '';
            var tableProbation = $('#dataTableProbation').DataTable({
                language: dtLangCdn,
                order: [[0, 'desc']],
                responsive: true,
                pageLength: 10,
                serverSide: true,
                processing: true,
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"lf>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4"ip>',
                ajax: {
                    url: '{{ route('manpower-request.probations-data') }}',
                    type: 'GET',
                    data: function(d) { d.status_filter = proFilter; }
                },
                columns: [
                    { data: 'id', render: (d) => '#' + d },
                    { data: 'start_date' },
                    { data: 'employee_name', className: 'font-medium' },
                    { data: 'position' },
                    { data: 'department' },
                    { data: 'status_label', orderable: false, searchable: false },
                    {
                        data: 'show_url', orderable: false, searchable: false, className: 'text-right',
                        render: (url) => '<a href="' + url + '" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white transition-all duration-150" title="ดูรายละเอียด">' + viewIconSvg + '</a>',
                    },
                ]
            });
        });
    </script>
</x-app-layout>
