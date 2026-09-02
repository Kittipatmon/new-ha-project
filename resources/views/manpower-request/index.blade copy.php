<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ติดตามสถานะ (My Manpower Requests)') }}
        </h2>
    </x-slot>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_length select { padding-right: 2rem; }
        .dataTables_wrapper .dataTables_filter input { margin-bottom: 0.5rem; }
        
        /* Mobile responsive adjustments for DataTables controls */
        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_length, 
            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
                margin-bottom: 0.75rem;
            }
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 0.5rem;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.25em 0.5em;
            }
        }
    </style>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            </script>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Section 1: Manpower Requests -->
                    <div>
                        <div class="flex items-center justify-between mb-4 border-b border-gray-200 dark:border-gray-700 pb-3">
                            <h3 class="text-lg font-bold text-indigo-700 dark:text-indigo-400">ใบขออนุมัติกำลังคน (Manpower Requests)</h3>
                            @php
                                $mprPending = collect($requests)->whereNotIn('status', ['approved', 'rejected', 'draft'])->count();
                            @endphp
                            @if($mprPending > 0)
                                <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                    รอดำเนินการ: {{ $mprPending }}
                                </span>
                            @endif
                        </div>
                        <div class="w-full overflow-x-auto">
                            <table id="dataTable" class="display responsive nowrap min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3 all">ID</th>
                                    <th scope="col" class="px-6 py-3">วันที่ยื่นขอ</th>
                                    <th scope="col" class="px-6 py-3">ฝ่าย/แผนก</th>
                                    <th scope="col" class="px-6 py-3 all">ชื่อตำแหน่ง</th>
                                    <th scope="col" class="px-6 py-3">ประเภทการจ้าง</th>
                                    <th scope="col" class="px-6 py-3 all">สถานะ</th>
                                    <th scope="col" class="px-6 py-3 text-right all"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $req)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $req->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($req->date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $req->department }} / {{ $req->section }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $req->job_title_th }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $req->hire_type }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($req->status === 'pending_manager')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">รอ ผจก.แผนก</span>
                                        @elseif($req->status === 'pending_vp')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">รอ ปธ.สายงาน</span>
                                        @elseif($req->status === 'pending_hr')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">รอ ผจก.HR</span>
                                        @elseif($req->status === 'pending_ceo')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">รอ CEO</span>
                                        @elseif($req->status === 'approved')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">อนุมัติแล้ว</span>
                                        @elseif($req->status === 'rejected')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">ไม่อนุมัติ</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $req->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                                        <a href="{{ route('manpower-request.show', $req->id) }}" class="inline-flex items-center p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md transition-colors dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50" title="ดูรายละเอียด">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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

            <!-- Section 2: Probation Evaluations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    <div>
                        <div class="flex items-center justify-between mb-4 border-b border-gray-200 dark:border-gray-700 pb-3">
                            <h3 class="text-lg font-bold text-indigo-700 dark:text-indigo-400">แบบประเมินทดลองงาน (Probation Evaluations)</h3>
                            @php
                                $proPending = collect($probations)->where('status', 'pending')->count();
                            @endphp
                            @if($proPending > 0)
                                <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                    รอดำเนินการ: {{ $proPending }}
                                </span>
                            @endif
                        </div>
                        <div class="w-full overflow-x-auto">
                            <table id="dataTableProbation" class="display responsive nowrap min-w-full text-sm text-left text-gray-500 dark:text-gray-400" style="width: 100%;">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3 all">ID</th>
                                    <th scope="col" class="px-6 py-3">วันที่เริ่มงาน</th>
                                    <th scope="col" class="px-6 py-3 all">ชื่อ-สกุลพนักงาน</th>
                                    <th scope="col" class="px-6 py-3">ตำแหน่ง</th>
                                    <th scope="col" class="px-6 py-3 all">สถานะ</th>
                                    <th scope="col" class="px-6 py-3 text-right all"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($probations as $prob)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $prob->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $prob->start_date ? \Carbon\Carbon::parse($prob->start_date)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $prob->employee_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $prob->position }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($prob->status === 'draft')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">แบบร่าง</span>
                                        @elseif($prob->status === 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">รออนุมัติ</span>
                                        @elseif($prob->status === 'approved')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">อนุมัติแล้ว</span>
                                        @elseif($prob->status === 'rejected')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">ไม่อนุมัติ</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $prob->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                                        <a href="{{ route('probation-evaluation.show', $prob->id) }}" class="inline-flex items-center p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md transition-colors dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50" title="ดูรายละเอียด">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
        </div>
    </div>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json"
                },
                "order": [[ 0, "desc" ]],
                "responsive": true
            });
            
            $('#dataTableProbation').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json"
                },
                "order": [[ 0, "desc" ]],
                "responsive": true
            });
        });
    </script>
</x-app-layout>
