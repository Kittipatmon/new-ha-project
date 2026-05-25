@extends('layouts.suggestion.app')
@section('title', 'Dashboard รับเรื่องร้องเรียน')
@section('content')
    @if(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))
        <div class="max-w-8xl mx-auto px-4 py-4 font-prompt">
                            <div class="flex items-center text-sm mb-4 space-x-2 mt-6 ">
                                <a href="{{ route('welcome') }}"
                                    class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Home</a>
                                <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
                                <a href="{{ route('suggestion.index') }}"
                                    class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">รับเรื่องคำร้อง</a>
                                <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
                                <span class="text-red-500 font-medium">Dashboard</span>
                            </div>

                            <div class="border border-gray-300/60 dark:border-gray-200/40 rounded-xl shadow-xl bg-white dark:bg-gray-900">
                                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                                <div class="py-6">
                                    <div class="max-w-8xl mx-auto px-6">
                                        <!-- Stats Grid -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                                            <div
                                                class="card bg-base-100 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
                                                <div class="card-body p-6 flex flex-row items-center justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500 mb-1">จำนวนคำขอทั้งหมด</p>
                                                        <h2 id="totalRequests" class="text-3xl font-bold text-gray-800 dark:text-white">
                                                            {{ number_format($total ?? 0) }}
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
                                                            {{ number_format($completed ?? 0) }}
                                                        </h2>
                                                        <p class="text-xs text-gray-400 mt-1">ปิดท้าย/เรียบร้อย</p>
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
                                                            {{ number_format($inProgress ?? 0) }}
                                                        </h2>
                                                        <p class="text-xs text-gray-400 mt-1">กำลังตรวขสอบ/ดำเนินการ</p>
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
                                                        <p class="text-sm font-medium text-gray-500 mb-1">รอรับเรื่อง</p>
                                                        <h2 id="statusWait" class="text-3xl font-bold text-red-500">
                                                            {{ number_format($pending ?? 0) }}
                                                        </h2>
                                                        <p class="text-xs text-gray-400 mt-1">รอรับเรื่องคำร้อง</p>
                                                    </div>
                                                    <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-full text-red-500">
                                                        <!-- Use Hourglass for Wait -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="h-1.5 w-full bg-red-500 rounded-b-xl"></div>
                                            </div>
                                        </div>


                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                            <div
                                                class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                                    คำร้องตามรูปแบบ
                                                </h3>
                                                <div class="relative h-64 w-full flex justify-center">
                                                    <canvas id="typeChart"></canvas>
                                                </div>
                                            </div>
                                            <div
                                                class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                                    คำร้องตามสถานะ
                                                </h3>
                                                <div class="relative h-64 w-full flex justify-center">
                                                    <canvas id="statusChart"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-6">
                                            <div
                                                class="p-5 rounded-xl shadow-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                <h3 class="text-lg font-semibold mb-4 text-center text-gray-800 dark:text-white">
                                                    แนวโน้มเรื่องร้องเรียนรายเดือน
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
                            document.addEventListener('DOMContentLoaded', function () {
                                // Data Injection
                                const statusLabels = @json($statusLabels ?? []);
                                const statusCounts = @json($statusCounts ?? []);
                                const typeLabels = @json($typeLabels ?? []);
                                const typeCounts = @json($typeCounts ?? []);
                                const monthlyLabels = @json($monthlyLabels ?? []);
                                const monthlyCounts = @json($monthlyCounts ?? []);

                                const isDarkMode = document.documentElement.classList.contains('dark');
                                Chart.defaults.font.family = "'Prompt', sans-serif";
                                Chart.defaults.color = isDarkMode ? '#9ca3af' : '#4b5563';
                                Chart.defaults.borderColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';

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
                                    }
                                };


                                const statusColorMap = {
                                    'รอรับเรื่องคำร้อง': colors.red,
                                    'รับเรื่อง': colors.purple,
                                    'รับเรื่องคำร้อง': colors.purple,
                                    'รับเรื่องคำร้องแล้ว': colors.purple,
                                    'ตรวจสอบ': colors.amber,
                                    'ดำเนินการ': colors.blue,
                                    'เสร็จสิ้น': colors.green,
                                    'ปิดเรื่อง': colors.gray,
                                };

                                const statusBgColors = statusLabels.map(label => statusColorMap[label] || colors.gray);

                                new Chart(document.getElementById('statusChart'), {
                                    type: 'doughnut',
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
                                        cutout: '65%',
                                        plugins: {
                                            legend: {
                                                position: 'right'
                                            }
                                        }
                                    }
                                });


                                new Chart(document.getElementById('typeChart'), {
                                    type: 'doughnut',
                                    data: {
                                        labels: typeLabels,
                                        datasets: [{
                                            data: typeCounts,
                                            backgroundColor: [colors.blue, colors.teal, colors.pink],
                                            borderWidth: 0,
                                            hoverOffset: 4
                                        }]
                                    },
                                    options: {
                                        ...commonOptions,
                                        cutout: '65%',
                                        plugins: {
                                            legend: {
                                                position: 'right'
                                            }
                                        }
                                    }
                                });

                                // Trend
                                const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
                                const gradient = trendCtx.createLinearGradient(0, 0, 0, 400);
                                gradient.addColorStop(0, 'rgba(139, 92, 246, 0.5)'); // Purple fade
                                gradient.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

                                new Chart(trendCtx, {
                                    type: 'line',
                                    data: {
                                        labels: monthlyLabels,
                                        datasets: [{
                                            label: 'คำขอรายเดือน',
                                            data: monthlyCounts,
                                            borderColor: colors.purple,
                                            backgroundColor: gradient,
                                            fill: true,
                                            tension: 0.4,
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
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                grid: {
                                                    borderDash: [2, 2]
                                                }
                                            },
                                            x: {
                                                grid: {
                                                    display: false
                                                }
                                            }
                                        }
                                    }
                                });


                            });
                        </script>
    @else
            <div class="flex flex-col items-center justify-center min-h-[60vh] font-prompt text-center px-4">
                <div class="text-red-500 mb-4 text-6xl">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">ไม่มีสิทธิ์เข้าถึง</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">หน้านี้สงวนสิทธิ์เฉพาะเจ้าหน้าที่ HR เท่านั้น</p>
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center justify-center px-6 py-2.5 text-base font-medium text-white bg-red-600 border border-transparent rounded-xl shadow-sm hover:focus:outline-none hover:bg-red-700 transition-colors">
                    <i class="fa-solid fa-house mr-2"></i> กลับหน้าหลัก
                </a>
            </div>
        @endif
@endsection