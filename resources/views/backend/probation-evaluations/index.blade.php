<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('แบบประเมินทดลองงาน (Probation Evaluations)') }}
            </h2>
            <a href="{{ route('probation-evaluation.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                + สร้างแบบประเมิน
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">



            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- KPI counts are now computed server-side in the controller (independent of table pagination) --}}

                    <!-- KPI Cards for Probation Evaluations -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <!-- Total -->
                        <div data-status="" class="kpi-card cursor-pointer bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">แบบประเมิน (ทั้งหมด)</p>
                                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $totalPro }}</h3>
                                </div>
                                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg dark:bg-blue-900/50 dark:text-blue-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm relative z-10">
                                <span class="text-gray-500">แบบประเมินทดลองงาน</span>
                            </div>
                        </div>

                        <!-- Pending -->
                        <div data-status="pending" class="kpi-card cursor-pointer bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-yellow-50 dark:bg-yellow-900/20 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">รอพิจารณา (Pending)</p>
                                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $pendingPro }}</h3>
                                </div>
                                <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg dark:bg-yellow-900/50 dark:text-yellow-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm relative z-10">
                                <span class="text-yellow-600 dark:text-yellow-400 font-medium">รอการตรวจสอบ</span>
                            </div>
                        </div>

                        <!-- Approved -->
                        <div data-status="approved" class="kpi-card cursor-pointer bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 dark:bg-green-900/20 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">อนุมัติแล้ว (Approved)</p>
                                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $approvedPro }}</h3>
                                </div>
                                <div class="p-3 bg-green-100 text-green-600 rounded-lg dark:bg-green-900/50 dark:text-green-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm relative z-10">
                                <span class="text-green-500 font-medium">เสร็จสิ้นสมบูรณ์</span>
                            </div>
                        </div>

                        <!-- Rejected -->
                        <div data-status="rejected" class="kpi-card cursor-pointer bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 dark:bg-red-900/20 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ไม่อนุมัติ (Rejected)</p>
                                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $rejectedPro }}</h3>
                                </div>
                                <div class="p-3 bg-red-100 text-red-600 rounded-lg dark:bg-red-900/50 dark:text-red-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm relative z-10">
                                <span class="text-red-500 font-medium">ถูกปฏิเสธ</span>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="probationTable" class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3">รหัสพนักงาน</th>
                                    <th scope="col" class="px-6 py-3">ชื่อ-นามสกุล</th>
                                    <th scope="col" class="px-6 py-3">แผนก</th>
                                    <th scope="col" class="px-6 py-3">วันที่เริ่มงาน</th>
                                    <th scope="col" class="px-6 py-3">สถานะ</th>
                                    <th scope="col" class="px-6 py-3 text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($probationEvaluations ?? [] as $eval)
                                    <tr>
                                        <td>{{ $eval->emp_code ?? '-' }}</td>
                                        <td class="font-medium">{{ ($eval->prefix ?? '') . ($eval->employee_name ?? 'ไม่ระบุ') }}</td>
                                        <td>{{ $eval->department ?? '-' }}</td>
                                        <td>{{ $eval->start_date ? \Carbon\Carbon::parse($eval->start_date)->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @if($eval->status === 'approved')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">อนุมัติแล้ว</span>
                                            @elseif($eval->status === 'rejected')
                                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">ไม่อนุมัติ</span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">รอพิจารณา</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="flex items-center justify-end gap-2 text-gray-500 dark:text-gray-400">
                                                <a href="{{ route('probation-evaluation.show', $eval->id) }}" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="ดูรายละเอียด">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                </a>
                                                <a href="{{ route('probation-evaluation.pdf', $eval->id) }}" target="_blank" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="Export PDF">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-gray-400 py-6">ไม่พบข้อมูลแบบประเมินทดลองงาน</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('#probationTable').DataTable({
                responsive: true,
                order: [[ 3, "desc" ]],
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/th.json' }
            });
        });
    </script>
    <style>
        /* Fix DataTables Select & Input overlapping with Tailwind Forms */
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem !important;
            width: auto !important;
            background-position: right 0.5rem center !important;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.25rem 0.75rem !important;
            margin-left: 0.75rem !important;
            border-radius: 0.375rem !important;
            border-color: #d1d5db !important;
        }
        .dataTables_wrapper {
            padding: 1rem 0;
        }
    </style>
</x-app-layout>
