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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, .font-sans { font-family: 'Inter', sans-serif; }

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

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

            @php
                $totalMpr     = $requests->count();
                $pendingMpr   = $requests->whereNotIn('status', ['approved','rejected','draft'])->count();
                $approvedMpr  = $requests->where('status','approved')->count();
                $rejectedMpr  = $requests->where('status','rejected')->count();

                $totalPro     = $probations->count();
                $pendingPro   = $probations->whereNotIn('status', ['approved','rejected'])->count();
                $approvedPro  = $probations->where('status','approved')->count();
                $rejectedPro  = $probations->where('status','rejected')->count();
            @endphp

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
                                <th>ชื่อตำแหน่ง</th>
                                <th>ประเภทการจ้าง</th>
                                <th>สถานะ</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr>
                                <td class="font-semibold text-indigo-600 dark:text-indigo-400">#{{ $req->id }}</td>
                                <td class="text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($req->date)->format('d/m/Y') }}</td>
                                <td class="text-gray-700 dark:text-gray-200">{{ $req->department }} / {{ $req->section }}</td>
                                <td class="text-gray-700 dark:text-gray-200 font-medium">{{ $req->job_title_th }}</td>
                                <td class="text-gray-600 dark:text-gray-300">{{ $req->hire_type }}</td>
                                <td>
                                    @if($req->status === 'pending_manager')
                                        <span class="badge badge-blue">รอ ผจก.แผนก</span>
                                    @elseif($req->status === 'pending_vp')
                                        <span class="badge badge-purple">รอ ปธ.สายงาน</span>
                                    @elseif($req->status === 'pending_hr')
                                        <span class="badge badge-yellow">รอ ผจก.HR</span>
                                    @elseif($req->status === 'pending_ceo')
                                        <span class="badge badge-yellow">รอ CEO</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge badge-green">อนุมัติแล้ว</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge badge-red">ไม่อนุมัติ</span>
                                    @else
                                        <span class="badge badge-gray">{{ $req->status }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('manpower-request.show', $req->id) }}" class="btn-view" title="ดูรายละเอียด">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
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
                                <th>ชื่อ-สกุลพนักงาน</th>
                                <th>ตำแหน่ง</th>
                                <th>แผนก</th>
                                <th>สถานะ</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($probations as $prob)
                            <tr>
                                <td class="font-semibold text-emerald-600 dark:text-emerald-400">#{{ $prob->id }}</td>
                                <td class="text-gray-600 dark:text-gray-300">
                                    {{ $prob->start_date ? \Carbon\Carbon::parse($prob->start_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-gray-700 dark:text-gray-200 font-medium">
                                    {{ $prob->prefix }}{{ $prob->employee_name ?? 'ไม่ระบุ' }}
                                </td>
                                <td class="text-gray-700 dark:text-gray-200">{{ $prob->position ?? '-' }}</td>
                                <td class="text-gray-600 dark:text-gray-300">{{ $prob->department ?? '-' }}</td>
                                <td>
                                    @if($prob->status === 'draft')
                                        <span class="badge badge-gray">แบบร่าง</span>
                                    @elseif($prob->status === 'pending')
                                        <span class="badge badge-blue">รออนุมัติ</span>
                                    @elseif($prob->status === 'approved')
                                        <span class="badge badge-green">อนุมัติแล้ว</span>
                                    @elseif($prob->status === 'rejected')
                                        <span class="badge badge-red">ไม่อนุมัติ</span>
                                    @else
                                        <span class="badge badge-gray">{{ $prob->status }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('probation-evaluation.show', $prob->id) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white transition-all duration-150"
                                       title="ดูรายละเอียด">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
            const dtOptions = {
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
                order: [[0, 'desc']],
                responsive: true,
                pageLength: 10,
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"lf>t<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4"ip>',
            };

            $('#dataTableMPR').DataTable(dtOptions);
            $('#dataTableProbation').DataTable(dtOptions);
        });
    </script>
</x-app-layout>
