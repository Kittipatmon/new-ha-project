@extends('layouts.training.app')

@section('content')
    @php
        $maxCount = count($data) > 0 ? max($data) : 0;
        $popularCourseCount = $popularCourseCount ?? 0;
    @endphp

    <div class="min-h-screen p-6 pt-8 pb-20 bg-slate-50 dark:bg-[#0f1117] text-slate-800 dark:text-gray-200">
        <div class="max-w-8xl mx-auto space-y-6">
            <!-- Top Navigation & Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                        ฝ่ายฝึกอบรมและพัฒนาทักษะ (Dashboard)
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-xs">
                        ภาพรวมและสถิติการสมัครเข้าร่วมกิจกรรมการฝึกอบรมทั้งหมด
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('training.dashboard') }}" method="GET" id="dashboardFilterForm" class="flex items-center gap-2">
                         <div class="relative group">
                            <select name="year" onchange="this.form.submit()"
                                class="pl-4 pr-10 py-1.5 bg-white dark:bg-[#1E2129] border border-gray-200 dark:border-gray-700/50 rounded-lg text-xs font-medium focus:ring-2 focus:ring-red-500/20 appearance-none cursor-pointer min-w-[100px]">
                                <option value="">รายปี</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>ปี {{ $year + 543 }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('training.index') }}"
                        class="inline-flex items-center justify-center gap-2 bg-white dark:bg-[#1E2129] border border-gray-200 dark:border-gray-700/50 text-gray-700 dark:text-slate-300 font-medium py-1.5 px-4 rounded-lg text-xs shadow-sm hover:shadow-md transition-all duration-300">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        กลับหน้ารายการ
                    </a>
                </div>
            </div>

            <!-- Stats/Summary Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all group">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">หลักสูตรทั้งหมด</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($totalCourses) }}</p>
                            <p class="text-[10px] text-green-500 font-bold mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-arrow-trend-up"></i> +0.0% <span class="text-gray-400 font-normal">จากเดือนที่แล้ว</span>
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all group">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ยอดผู้สนใจรวม</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($totalApplies) }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">สมัครเข้าร่วมใหม่ในวันนี้</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 flex items-center justify-center text-lg shadow-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all group">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ความสนใจเฉลี่ย</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $avgApplies }}%</p>
                            <p class="text-[10px] text-gray-400 mt-1">อัตราการสมัครต่อหลักสูตร</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 flex items-center justify-center text-lg shadow-sm">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all group">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">สนใจมากที่สุด</p>
                            <p class="text-lg font-black text-gray-800 dark:text-white truncate" title="{{ $popularCourseName }}">
                                {{ $popularCourseName }}
                            </p>
                            <p class="text-[10px] text-red-500 font-bold mt-1">
                                {{ number_format($popularCourseCount) }} <span class="text-gray-400 font-normal">คน</span>
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center text-lg shadow-sm flex-shrink-0">
                            <i class="fa-solid fa-fire"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Bar Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-[#1E2129] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800/60 p-6 flex flex-col min-h-[450px]">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-white mb-6 uppercase tracking-wider">สถิติความสนใจรายหลักสูตร (RANKING)</h3>
                    <div id="trainingMainChart" class="flex-grow"></div>
                </div>

                <!-- Right Side Donut/Bar -->
                <div class="flex flex-col gap-6">
                    <!-- Format Breakdown -->
                    <div class="bg-white dark:bg-[#1E2129] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800/60 p-6">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-white mb-6 uppercase tracking-wider">รูปแบบการฝึกอบรม (Format)</h3>
                        <div id="trainingFormatChart" class="h-64 flex items-center justify-center"></div>
                    </div>

                    <!-- Additional Stats -->
                    <div class="bg-white dark:bg-[#1E2129] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800/60 p-6 flex-grow text-center flex flex-col items-center justify-center">
                         <h3 class="text-sm font-bold text-gray-700 dark:text-white mb-6 uppercase tracking-wider w-full text-left">ความสนใจแยกตามหน่วยงาน</h3>
                         <div id="trainingDeptChart" class="w-full"></div>
                    </div>
                </div>
            </div>

            <!-- Recent Registrations (Connect with training_applies) -->
            <div class="bg-white dark:bg-[#1E2129] rounded-xl shadow-sm border border-gray-100 dark:border-gray-800/60 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">รายการลงทะเบียนล่าสุด</h3>
                        <p class="text-slate-400 text-[10px] mt-1">แสดงข้อมูลพนักงานที่ลงทะเบียนเข้าร่วมล่าสุด 10 รายการ</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/20">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">พนักงาน</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">หลักสูตร</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">เวลาที่ลงทะเบียน</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60">
                            @forelse($recentApplies as $apply)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-red-500 group-hover:text-white transition-all">
                                                <i class="fa-solid fa-user text-[10px]"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-700 dark:text-slate-200">
                                                    {{ $apply->user ? $apply->user->fullname : ('รหัส: ' . $apply->employee_code) }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">{{ $apply->employee_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-medium text-gray-600 dark:text-slate-300">
                                            {{ $apply->training ? $apply->training->branch : 'ไม่พบข้อมูลหลักสูตร' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 py-1 px-2 rounded-md">
                                            <i class="fa-regular fa-clock mr-1"></i>
                                            {{ $apply->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-bold bg-green-50 dark:bg-green-900/20 text-green-600 border border-green-200/50 dark:border-green-900/50">
                                            <span class="w-1 h-1 rounded-full bg-green-600 animate-pulse"></span>
                                            สำเร็จ
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-inbox text-3xl mb-3 opacity-20"></i>
                                        <p class="text-xs font-medium uppercase tracking-widest">ยังไม่มีข้อมูลการลงทะเบียน</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Require ApexCharts via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const labels = @json($labels);
            const dataCounts = @json($data);
            
            const formatLabels = @json($formatLabels);
            const formatData = @json($formatData);
            
            const deptLabels = @json($deptLabels);
            const deptData = @json($deptData);

            if (dataCounts.length === 0) {
                console.log("No data for charts");
                return;
            }

            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#94a3b8' : '#64748b';
            const gridColor = isDarkMode ? '#334155' : '#f1f5f9';

            // 1. Main Horizontal Bar Chart (Ranking by Interest)
            var mainChartOptions = {
                series: [{ name: 'จำนวนผู้สมัคร', data: dataCounts }],
                chart: { 
                    height: Math.max(400, labels.length * 40), 
                    type: 'bar', 
                    fontFamily: 'Prompt, sans-serif', 
                    toolbar: { show: false } 
                },
                plotOptions: { 
                    bar: { 
                        horizontal: true, 
                        borderRadius: 6, 
                        barHeight: '60%', 
                        distributed: true, 
                        dataLabels: { position: 'end' } 
                    } 
                },
                colors: ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#f43f5e', '#6366f1', '#14b8a6'],
                dataLabels: { 
                    enabled: true, 
                    offsetX: 25, 
                    style: { fontSize: '12px', colors: [textColor], fontWeight: 'bold' },
                    formatter: function(val) { return val + " คน"; }
                },
                xaxis: { 
                    categories: labels, 
                    labels: { style: { colors: textColor, fontSize: '11px' } }, 
                    axisBorder: { show: false }, 
                    axisTicks: { show: false } 
                },
                yaxis: { 
                    labels: { 
                        maxWidth: 300,
                        style: { colors: textColor, fontSize: '11px' } 
                    } 
                },
                grid: { borderColor: gridColor, strokeDashArray: 4, xaxis: { lines: { show: true } } },
                legend: { show: false },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            };
            var mainChart = new ApexCharts(document.querySelector("#trainingMainChart"), mainChartOptions);
            mainChart.render();

            // 2. Training Format (Donut Chart)
            var formatChartOptions = {
                series: formatData,
                chart: { type: 'donut', height: 280, fontFamily: 'Prompt, sans-serif' },
                labels: formatLabels,
                colors: ['#3b82f6', '#10b981', '#f59e0b'],
                legend: { position: 'bottom', labels: { colors: textColor } },
                plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'รวมทั้งสิ้น', color: textColor } } } } },
                stroke: { show: false },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            };
            var formatChart = new ApexCharts(document.querySelector("#trainingFormatChart"), formatChartOptions);
            formatChart.render();

            // 3. Department Breakdown (Horizontal Bar)
            var deptChartOptions = {
                series: [{ name: 'ผู้สมัคร', data: deptData }],
                chart: { type: 'bar', height: 250, toolbar: { show: false }, fontFamily: 'Prompt, sans-serif' },
                plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '50%', distributed: true } },
                colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
                xaxis: { categories: deptLabels, labels: { style: { colors: textColor, fontSize: '10px' } } },
                yaxis: { labels: { show: true, maxWidth: 150, style: { colors: textColor, fontSize: '10px' } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                legend: { show: false },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            };
            var deptChart = new ApexCharts(document.querySelector("#trainingDeptChart"), deptChartOptions);
            deptChart.render();

            // Theme Observer
            const observer = new MutationObserver(() => {
                const dark = document.documentElement.classList.contains('dark');
                const newText = dark ? '#94a3b8' : '#64748b';
                const newGrid = dark ? '#334155' : '#f1f5f9';
                [mainChart, formatChart, deptChart].forEach(c => c.updateOptions({
                    xaxis: { labels: { style: { colors: newText } } },
                    yaxis: { labels: { style: { colors: newText } } },
                    grid: { borderColor: newGrid },
                    tooltip: { theme: dark ? 'dark' : 'light' }
                }));
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        });
    </script>
@endsection
