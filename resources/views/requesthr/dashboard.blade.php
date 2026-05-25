@extends('layouts.hrrequest.app')
@section('content')
@if(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))
    <div class="max-w-8xl mx-auto px-4 py-4 font-prompt">
    <div class="flex items-center text-sm mb-4 space-x-2">
        <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Home</a>
        <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
        <span class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Request HR</span>
        <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
        <span class="text-red-500 font-medium">Dashboard</span>
    </div>

        <div class="border border-gray-300/60 dark:border-gray-200/40 rounded-xl shadow-xl bg-white dark:bg-gray-900">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <div class="py-6">
                <div class="max-w-8xl mx-auto px-6">
                    <!-- Filter Section -->
                    <div
                        class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    วันที่เริ่มต้น
                                </label>
                                <input type="date" id="startDate" class="input input-bordered w-full h-10">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    วันที่สิ้นสุด
                                </label>
                                <input type="date" id="endDate" class="input input-bordered w-full h-10">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    สายงาน
                                </label>
                                <select id="sectionFilter" class="select select-bordered w-full h-10">
                                    <option value="">
                                        -- เลือกสายงาน --
                                    </option>
                                    @foreach($sections as $dept)
                                        <option value="{{ $dept->section_id }}">
                                            {{ $dept->section_name }}
                                            ({{ $dept->section_fullname }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    ฝ่าย
                                </label>
                                <select id="divisionFilter" class="select select-bordered w-full h-10">
                                    <option value="">
                                        -- เลือกสายงาน --
                                    </option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->division_id }}">
                                            {{ $div->division_name }}
                                            ({{ $div->division_fullname }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    แผนก
                                </label>
                                <select id="departmentFilter" class="select select-bordered w-full h-10">
                                    <option value="">
                                        -- เลือกแผนก --
                                    </option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->department_id }}">
                                            {{ $dept->department_name }}
                                            ({{ $dept->department_fullname }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    สถานะคำขอ
                                </label>
                                <select id="statusFilter" class="select select-bordered w-full h-10">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status['id'] }}" {{ request('status') == $status['id'] ? 'selected' : '' }}>
                                            {{ $status['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="applyFilter()" class="btn btn-info h-10 min-h-0 flex-1 text-white">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <button onclick="resetFilter()" class="btn btn-outline h-10 min-h-0 flex-1">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div
                            class="card bg-base-100 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
                            <div class="card-body p-6 flex flex-row items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">จำนวนคำขอทั้งหมด</p>
                                    <h2 id="totalRequests" class="text-3xl font-bold text-gray-800 dark:text-white">
                                        {{ $totalRequests ?? 0 }}
                                    </h2>
                                    <p class="text-xs text-gray-400 mt-1">รายการ</p>
                                </div>
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-full text-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="h-1.5 w-full bg-blue-500 rounded-b-xl"></div>
                        </div>

                        <div
                            class="card bg-base-100 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
                            <div class="card-body p-6 flex flex-row items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">ดำเนินการเสร็จสิ้น</p>
                                    <h2 id="statusCompleted" class="text-3xl font-bold text-green-600 dark:text-green-400">
                                        {{ $statusCompleted ?? 0 }}
                                    </h2>
                                    <p class="text-xs text-gray-400 mt-1">อนุมัติเรียบร้อย</p>
                                </div>
                                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-full text-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="h-1.5 w-full bg-green-500 rounded-b-xl"></div>
                        </div>

                        <div
                            class="card bg-base-100 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
                            <div class="card-body p-6 flex flex-row items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">อยู่ระหว่างดำเนินการ</p>
                                    <h2 id="statusPending" class="text-3xl font-bold text-orange-500">
                                        {{ $statusPending + $statusAPPROVEDHR ?? 0 }}
                                    </h2>
                                    <p class="text-xs text-gray-400 mt-1">รอการตรวจสอบ</p>
                                </div>
                                <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-full text-orange-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="h-1.5 w-full bg-orange-500 rounded-b-xl"></div>
                        </div>

                        <div
                            class="card bg-base-100 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
                            <div class="card-body p-6 flex flex-row items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">ปฏิเสธ/ยกเลิก</p>
                                    <h2 id="statusCancelled" class="text-3xl font-bold text-red-500">
                                        {{ $statusCancelled ?? 0 }}
                                    </h2>
                                    <p class="text-xs text-gray-400 mt-1">ถูกตีกลับหรือยกเลิก</p>
                                </div>
                                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-full text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="h-1.5 w-full bg-red-500 rounded-b-xl"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div
                            class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                คำร้องตามสถานะ
                            </h3>
                            <div class="relative h-64 w-full flex justify-center">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>

                        <div
                            class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                คำร้องตามสายงาน
                            </h3>
                            <div class="relative h-64 w-full">
                                <canvas id="divisionChart"></canvas>
                            </div>
                        </div>

                        <div
                            class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                คำร้องตามหมวดหมู่
                            </h3>
                            <div class="relative h-64 w-full flex justify-center">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div
                            class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                คำร้องตามแผนก
                            </h3>
                            <div class="relative h-72 w-full">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>

                        <div
                            class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                แนวโน้มคำร้องรายเดือน
                            </h3>
                            <div class="relative h-72 w-full">
                                <canvas id="monthlyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let statusChart, divisionChart, categoryChart, departmentChart, monthlyTrendChart;

        document.addEventListener('DOMContentLoaded', function () {
            // --- Data ---
            const statusLabels = @json($statusLabels ?? []);
            const statusCounts = @json($statusCounts ?? []);
            const divisionLabels = @json($divisionLabels ?? []);
            const divisionCounts = @json($divisionCounts ?? []);
            const categoryLabels = @json($categoryLabels ?? []);
            const categoryCounts = @json($categoryCounts ?? []);
            const departmentLabels = @json($departmentLabels ?? []);
            const departmentCounts = @json($departmentCounts ?? []);
            const monthlyLabels = @json($monthlyLabels ?? []);
            const monthlyCounts = @json($monthlyCounts ?? []);

            // --- Configuration ---
            const isDarkMode = document.documentElement.classList.contains('dark');

            // Setup Chart defaults for consistent look
            Chart.defaults.font.family = "'Prompt', sans-serif";
            Chart.defaults.color = isDarkMode ? '#9ca3af' : '#4b5563';
            Chart.defaults.borderColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';

            // Modern Color Palettes
            const colors = {
                blue: '#3b82f6',
                green: '#22c55e',
                red: '#ef4444',
                orange: '#f97316',
                amber: '#f59e0b',
                purple: '#8b5cf6',
                teal: '#14b8a6',
                indigo: '#6366f1',
                pink: '#ec4899',
                gray: '#6b7280'
            };

            const statusColorMap = {
                'รอตรวจสอบโดยผู้จัดการ': colors.orange,
                'รออนุมัติโดยผู้จัดการ': colors.amber,
                'รอตรวจสอบโดยฝ่ายบุคคล': colors.blue,
                'ถูกปฏิเสธ': colors.red,
                'ยกเลิก': colors.gray,
                'ดำเนินการเสร็จสิ้น': colors.green,
                'ส่งกลับแก้ไข': colors.purple,
            };
            const statusBgColors = statusLabels.map(label => statusColorMap[label] || '#a8a29e');

            // Helper for Chart Options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 2]
                        } // Dotted grid lines
                    },
                    x: {
                        grid: {
                            display: false
                        } // Clean look without X-grid
                    }
                }
            };

            // 1. Status Chart (Pie)
            statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut', // Changed to Doughnut for modern look
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: statusBgColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '65%', // Thin ring
                    scales: {}, // Remove scales for Pie/Doughnut
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // 2. Division Chart (Vertical Bar)
            divisionChart = new Chart(document.getElementById('divisionChart'), {
                type: 'bar',
                data: {
                    labels: divisionLabels,
                    datasets: [{
                        label: 'จำนวนคำขอ',
                        data: divisionCounts,
                        backgroundColor: colors.blue,
                        borderRadius: 6, // Rounded corners
                        barPercentage: 0.6
                    }]
                },
                options: commonOptions
            });

            // 3. Category Chart (Doughnut)
            categoryChart = new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryCounts,
                        backgroundColor: [colors.pink, colors.blue, colors.amber, colors.teal, colors
                            .purple
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '65%',
                    scales: {},
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // 4. Department Chart (Vertical Bar - FIXED)
            departmentChart = new Chart(document.getElementById('departmentChart'), {
                type: 'bar', // Vertical is default
                data: {
                    labels: departmentLabels,
                    datasets: [{
                        label: 'จำนวนคำขอ',
                        data: departmentCounts,
                        backgroundColor: colors.teal,
                        borderRadius: 6, // Rounded bars
                        barPercentage: 0.5
                    }]
                },
                options: commonOptions // Uses default X/Y axes (Vertical)
            });

            // 5. Monthly Trend (Line Area)
            const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            // Create Gradient
            const gradient = trendCtx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(139, 92, 246, 0.5)'); // Purple fade
            gradient.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

            monthlyTrendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'คำขอรายเดือน',
                        data: monthlyCounts,
                        borderColor: colors.purple,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4, // Smooth curve
                        pointBackgroundColor: '#fff',
                        pointBorderColor: colors.purple,
                        pointHoverBackgroundColor: colors.purple,
                        pointHoverBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    ...commonOptions,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                }
            });
        });

        function applyFilter() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const sectionId = document.getElementById('sectionFilter').value;
            const divisionId = document.getElementById('divisionFilter').value;
            const departmentId = document.getElementById('departmentFilter').value;
            const status = document.getElementById('statusFilter').value;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                section_id: sectionId,
                division_id: divisionId,
                department_id: departmentId,
                status: status
            });

            fetch(`{{ route('requesthr.dashboard.filter') }}?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    // Update Charts
                    updateChart(statusChart, data.statusLabels, data.statusCounts);
                    updateChart(divisionChart, data.divisionLabels, data.divisionCounts);
                    updateChart(categoryChart, data.categoryLabels, data.categoryCounts);
                    updateChart(departmentChart, data.departmentLabels, data.departmentCounts);
                    updateChart(monthlyTrendChart, data.monthlyLabels, data.monthlyCounts);

                    // Update Counts
                    document.getElementById('totalRequests').innerText = data.totalRequests;
                    document.getElementById('statusCompleted').innerText = data.statusCompleted;
                    document.getElementById('statusPending').innerText = parseInt(data.statusPending) + parseInt(data
                        .statusAPPROVEDHR);
                    document.getElementById('statusCancelled').innerText = data.statusCancelled;
                });
        }

        function updateChart(chart, labels, data) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.update();
        }

        function resetFilter() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('sectionFilter').value = '';
            document.getElementById('divisionFilter').value = '';
            document.getElementById('departmentFilter').value = '';
            document.getElementById('statusFilter').value = '';
            applyFilter();
        }
    </script>
@else
    <div class="flex flex-col items-center justify-center min-h-[60vh] font-prompt text-center px-4">
        <div class="text-red-500 mb-4 text-6xl">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">ไม่มีสิทธิ์เข้าถึง</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">หน้านี้สงวนสิทธิ์เฉพาะเจ้าหน้าที่ HR เท่านั้น</p>
        <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-base font-medium text-white bg-red-600 border border-transparent rounded-xl shadow-sm hover:focus:outline-none hover:bg-red-700 transition-colors">
            <i class="fa-solid fa-house mr-2"></i> กลับหน้าหลัก
        </a>
    </div>
@endif
@endsection