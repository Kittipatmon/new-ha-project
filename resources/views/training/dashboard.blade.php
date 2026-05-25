@extends('layouts.training.app')

@section('content')
    @php
        $maxCount = count($data) > 0 ? max($data) : 0;
        $popularCourseCount = $popularCourseCount ?? 0;
    @endphp

    <div class="min-h-screen p-6 pt-8 pb-20 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-gray-200">
        <div class="max-w-8xl mx-auto space-y-6">
            <!-- Top Navigation & Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white shadow-lg shadow-red-500/30">
                            <i class="fa-solid fa-graduation-cap text-lg"></i>
                        </div>
                        ฝ่ายฝึกอบรมและพัฒนาทักษะ (Dashboard)
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">
                        ภาพรวมและสถิติการสมัครเข้าร่วมกิจกรรมการฝึกอบรมทั้งหมด
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('training.dashboard') }}" method="GET" id="dashboardFilterForm"
                        class="flex items-center gap-2">
                        <div class="relative group">
                            <select name="year" onchange="this.form.submit()"
                                class="pl-4 pr-10 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-gray-700/50 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500/20 appearance-none cursor-pointer min-w-[120px] shadow-sm transition-all hover:border-red-300 dark:hover:border-gray-600">
                                <option value="">รายปี (ทั้งหมด)</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        ปี {{ $year + 543 }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('training.index') }}"
                        class="inline-flex items-center justify-center gap-2 bg-slate-800 border border-transparent text-white font-medium py-2 px-5 rounded-xl text-sm shadow-md hover:shadow-lg focus:bg-slate-700 transition-all active:scale-95">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        กลับหน้ารายการ
                    </a>
                </div>
            </div>

            <!-- Stats/Summary Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-book-open text-6xl text-blue-500"></i>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-blue-500/30 shrink-0">
                            <i class="fa-solid fa-book-journal-whills"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">หลักสูตรทั้งหมด</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white">
                                {{ number_format($totalCourses) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-users text-6xl text-emerald-500"></i>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-emerald-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30 shrink-0">
                            <i class="fa-solid fa-users-viewfinder"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ยอดผู้สนใจรวม</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white">
                                {{ number_format($totalApplies) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-chart-pie text-6xl text-amber-500"></i>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-amber-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-amber-500/30 shrink-0">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ความสนใจเฉลี่ย</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white">
                                {{ $avgApplies }}%
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-fire text-6xl text-red-500"></i>
                    </div>
                    <div class="flex items-center gap-4 relative z-10 w-full">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-red-500/30 shrink-0">
                            <i class="fa-solid fa-fire-flame-curved"></i>
                        </div>
                        <div class="overflow-hidden flex-1">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">สนใจมากที่สุด</p>
                            <p class="text-lg font-black text-slate-800 dark:text-white truncate pb-0.5" title="{{ $popularCourseName }}">
                                {{ $popularCourseName }}
                            </p>
                            <p class="text-[10px] text-red-500 font-bold -mt-0.5">
                                {{ number_format($popularCourseCount) }} <span class="text-slate-400 font-normal">คน</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Bar Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 p-6 flex flex-col min-h-[450px]">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-6 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-ranking-star text-amber-500"></i> สถิติความสนใจรายหลักสูตร
                    </h3>
                    <div id="trainingMainChart" class="flex-grow w-full" style="min-height: 400px;"></div>
                </div>

                <!-- Right Side Donut/Bar -->
                <div class="flex flex-col gap-6">
                    <!-- Format Breakdown -->
                    <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 p-6">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-6 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-chalkboard-user text-blue-500"></i> รูปแบบการฝึกอบรม
                        </h3>
                        <div id="trainingFormatChart" class="w-full flex items-center justify-center" style="min-height: 256px;"></div>
                    </div>

                    <!-- Additional Stats -->
                    <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 p-6 flex-grow text-center flex flex-col items-center justify-center">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-6 uppercase tracking-wider w-full text-left flex items-center gap-2">
                            <i class="fa-solid fa-building-user text-emerald-500"></i> ความสนใจแยกตามหน่วยงาน
                        </h3>
                        <div id="trainingDeptChart" class="w-full" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>

            <!-- Monthly Comparison Chart (Full Width) -->
            <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-6 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-red-500"></i> เปรียบเทียบจำนวผู้สมัครแต่ละเดือน (รายปี)
                </h3>
                <div id="monthlyComparisonChart" class="w-full" style="min-height: 350px;"></div>
            </div>

            <!-- Recent Registrations (Connect with training_applies) -->
            <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden flex flex-col relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-black/20">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-red-500"></i> รายการลงทะเบียนล่าสุด
                        </h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 ml-6">ผู้ลงทะเบียน 10 รายการล่าสุด</p>
                    </div>
                    <a href="{{ route('training.index') }}"
                        class="rounded-full bg-slate-800 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 active:bg-slate-700 w-auto ml-2 hidden sm:block">
                        ดูการรับสมัครทั้งหมด <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto flex-1 p-4">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                <th class="pb-3 px-4 font-semibold">พนักงาน</th>
                                <th class="pb-3 px-4 font-semibold">หลักสูตร</th>
                                <th class="pb-3 px-4 font-semibold text-center">เวลาที่ลงทะเบียน</th>
                                <th class="pb-3 px-4 font-semibold text-right">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                            @forelse($recentApplies as $apply)
                                <tr class="hover:bg-red-50/50 dark:hover:bg-red-900/10 transition-colors group">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-slate-500 dark:text-gray-400 font-bold border border-slate-200 dark:border-gray-700 shrink-0 group-hover:bg-red-100 group-hover:text-red-500 dark:group-hover:bg-red-900/40 transition-colors">
                                                <i class="fa-solid fa-user text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-gray-200">
                                                    {{ $apply->user ? $apply->user->fullname : ('รหัส: ' . $apply->employee_code) }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                    <i class="fa-regular fa-id-badge text-[10px]"></i> {{ $apply->employee_code }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm font-medium text-slate-700 dark:text-gray-300">
                                            {{ $apply->training ? $apply->training->branch : 'ไม่มีข้อมูลหลักสูตร' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-slate-600 dark:text-gray-400 text-xs font-medium px-2.5 py-1 rounded-md border border-gray-200 dark:border-gray-700">
                                            <i class="fa-regular fa-clock mr-1.5"></i> {{ $apply->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800/50 uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            สำเร็จ
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-slate-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-solid fa-box-open text-4xl mb-3 opacity-20"></i>
                                            <p class="text-sm font-medium uppercase tracking-widest">ยังไม่มีข้อมูลการลงทะเบียน</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Delay rendering slightly to allow Tailwind CDN to inject styles before ApexCharts calculates dimensions
            setTimeout(function() {
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
                    formatter: function (val) { return val + " คน"; }
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

            // 4. Monthly Comparison (Line/Area Chart)
            const monthlySeries = @json($monthlySeries);
            const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

            var monthlyChartOptions = {
                series: monthlySeries,
                chart: {
                    type: 'area',
                    height: 350,
                    fontFamily: 'Prompt, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                colors: ['#ef4444', '#3b82f6', '#10b981', '#f59e0b'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: monthNames,
                    labels: { style: { colors: textColor, fontSize: '11px' } }
                },
                yaxis: {
                    labels: { style: { colors: textColor, fontSize: '11px' } }
                },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right', labels: { colors: textColor } },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            };
            var monthlyChart = new ApexCharts(document.querySelector("#monthlyComparisonChart"), monthlyChartOptions);
            monthlyChart.render();

            // Theme Observer
            const observer = new MutationObserver(() => {
                const dark = document.documentElement.classList.contains('dark');
                const newText = dark ? '#94a3b8' : '#64748b';
                const newGrid = dark ? '#334155' : '#f1f5f9';
                [mainChart, formatChart, deptChart, monthlyChart].forEach(c => c.updateOptions({
                    xaxis: { labels: { style: { colors: newText } } },
                    yaxis: { labels: { style: { colors: newText } } },
                    grid: { borderColor: newGrid },
                    tooltip: { theme: dark ? 'dark' : 'light' },
                    legend: { labels: { colors: newText } }
                }));
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            }, 100);
        });
    </script>
@endsection