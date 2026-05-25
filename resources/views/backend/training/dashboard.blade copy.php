@extends('layouts.training.app')

@section('title', 'Training Dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold dark:text-white text-gray-800">รายงานความสนใจฝึกอบรม</h2>
    </div>

    <!-- Filter Bar -->
    <div
        class="bg-white dark:bg-kumwell-card rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-800 transition-all">
        <form action="{{ route('backend.training.dashboard') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative group">
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-kumwell-red transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อหลักสูตร..."
                    class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-kumwell-dark border-none rounded-2xl text-sm focus:ring-2 focus:ring-kumwell-red/20 dark:text-white transition-all">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select name="year"
                    class="px-4 py-3 bg-gray-50 dark:bg-kumwell-dark border-none rounded-2xl text-sm focus:ring-2 focus:ring-kumwell-red/20 dark:text-white appearance-none cursor-pointer">
                    <option value="">ทุกปี</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>ปี {{ $year + 543 }}
                        </option>
                    @endforeach
                </select>

                <select name="month"
                    class="px-4 py-3 bg-gray-50 dark:bg-kumwell-dark border-none rounded-2xl text-sm focus:ring-2 focus:ring-kumwell-red/20 dark:text-white appearance-none cursor-pointer">
                    <option value="">ทุกเดือน</option>
                    @php
                        $months = [1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'];
                    @endphp
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>

                <button type="submit"
                    class="bg-kumwell-red hover:bg-red-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-red-500/20 transition-all active:scale-95">
                    ค้นหา
                </button>

                @if(request()->anyFilled(['search', 'year', 'month']))
                    <a href="{{ route('backend.training.dashboard') }}"
                        class="p-3 text-gray-400 hover:text-kumwell-red transition-colors">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
            class="bg-white dark:bg-kumwell-card p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">หลักสูตรทั้งหมด</p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($totalCourses) }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-kumwell-card p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ผู้สนใจสมัครรวม</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($totalApplies) }}
                        </p>
                        @if(isset($growth))
                            <span class="text-[10px] font-bold {{ $growth >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                <i class="fa-solid fa-caret-{{ $growth >= 0 ? 'up' : 'down' }} mr-0.5"></i>
                                {{ abs($growth) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-kumwell-card p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-lg transition-all">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-amber-100 dark:bg-amber-900/20 text-amber-600 rounded-xl flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">เฉลี่ยต่อหลักสูตร</p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white">{{ $avgApplies }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-kumwell-red p-6 rounded-3xl shadow-lg shadow-red-500/30 border border-red-400/50 hover:shadow-xl transition-all text-white">
            <div class="flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-red-100 text-[10px] font-bold uppercase tracking-widest">สนใจมากที่สุด</p>
                    <i class="fa-solid fa-fire text-yellow-300"></i>
                </div>
                <h4 class="text-sm font-bold leading-tight line-clamp-2 mb-1">{{ $popularCourseName }}</h4>
                @if($popularCourseCount > 0)
                    <p class="text-[10px] text-red-100 font-medium">{{ number_format($popularCourseCount) }} applicants</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div
            class="bg-white dark:bg-kumwell-card rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8">
            <h3 class="font-bold text-gray-800 dark:text-white mb-6 uppercase tracking-wider text-sm">
                การจัดอันดับความสนใจ (RANKING)</h3>
            <div id="trainingRankingChart" class="h-[400px] w-full"></div>
        </div>

        <div
            class="bg-white dark:bg-kumwell-card rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8">
            <h3 class="font-bold text-gray-800 dark:text-white mb-6 uppercase tracking-wider text-sm">สัดส่วนรูปแบบ
                (FORMAT)</h3>
            <div id="trainingComparisonChart" class="h-[400px] w-full"></div>
        </div>

        <div
            class="bg-white dark:bg-kumwell-card rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8">
            <h3 class="font-bold text-gray-800 dark:text-white mb-6 uppercase tracking-wider text-sm">สถิติรายปี (YEARLY
                TREND)</h3>
            <div id="trainingYearlyChart" class="h-[400px] w-full"></div>
        </div>
    </div>

    <!-- Recent Applications (Connect with training_applies) -->
    <div
        class="bg-white dark:bg-kumwell-card rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                    รายการลงทะเบียนล่าสุด (RECENT)</h3>
                <p class="text-slate-400 text-[10px] mt-1">แสดงข้อมูลการลงทะเบียนเข้าร่วม 10 รายการล่าสุดจากระบบ</p>
            </div>
            <div
                class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-kumwell-dark/30">
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                            พนักงาน/รหัส</th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                            หลักสูตร (COURSE)</th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">
                            วันเวลา (DATETIME)</th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">
                            สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentApplies as $apply)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg- kumwell-dark/20 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-kumwell-red group-hover:text-white transition-all text-[10px]">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-700 dark:text-slate-200">
                                            {{ $apply->user ? $apply->user->fullname : ('รหัส: ' . $apply->employee_code) }}
                                        </p>
                                        <p class="text-[9px] text-slate-400">{{ $apply->employee_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-[11px] font-medium text-gray-600 dark:text-slate-300 line-clamp-1 max-w-xs"
                                    title="{{ $apply->training ? $apply->training->branch : '-' }}">
                                    {{ $apply->training ? $apply->training->branch : 'ไม่พบข้อมูลหลักสูตร' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="text-[9px] text-slate-500 dark:text-slate-400 bg-gray-100/80 dark:bg-gray-800 py-1 px-2 rounded-lg font-mono">
                                    {{ $apply->created_at->format('d/m/Y H:i:s') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[9px] font-black bg-blue-50 dark:bg-blue-900/10 text-blue-600 border border-blue-200/50 dark:border-blue-900/30 uppercase">
                                    SUCCESS
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-inbox text-4xl mb-4 opacity-10"></i>
                                <p class="text-[10px] uppercase tracking-widest font-bold">ไม่มีข้อมูลการสมัครในช่วงเวลานี้
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endpush

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const labels = @json($labels);
            const dataCounts = @json($data);

            const deptLabels = @json($deptLabels);
            const deptData = @json($deptData);

            const formatLabels = @json($formatLabels);
            const formatData = @json($formatData);

            const yearlyLabels = @json($yearlyLabels);
            const yearlyCounts = @json($yearlyCounts);

            if (dataCounts.length === 0) return;

            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#94a3b8' : '#64748b';
            const gridColor = isDarkMode ? '#334155' : '#f1f5f9';

            // 1. Ranking Chart (Horizontal Bar)
            new ApexCharts(document.querySelector("#trainingRankingChart"), {
                series: [{ name: 'จำนวนผู้สนใจ', data: dataCounts }],
                chart: {
                    height: 400,
                    type: 'bar',
                    toolbar: { show: false },
                    fontFamily: 'Prompt, sans-serif'
                },
                colors: ['#D71920', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6,
                        distributed: true,
                        barHeight: '60%',
                        dataLabels: { position: 'end' }
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetX: 30,
                    style: { fontSize: '10px', colors: [textColor] },
                    formatter: function (val) { return val + " pax"; }
                },
                xaxis: {
                    categories: labels,
                    labels: { style: { colors: textColor, fontSize: '10px' } }
                },
                yaxis: {
                    labels: {
                        maxWidth: 200,
                        style: { colors: textColor, fontSize: '10px' }
                    }
                },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                legend: { show: false },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            }).render();

            // 2. Comparison/Format Chart (Donut)
            new ApexCharts(document.querySelector("#trainingComparisonChart"), {
                series: formatData,
                chart: { height: 350, type: 'donut', fontFamily: 'Prompt, sans-serif' },
                labels: formatLabels,
                colors: ['#D71920', '#3b82f6', '#10b981', '#f59e0b'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: { show: true, label: 'รวมทั้งสิ้น', color: textColor }
                            }
                        }
                    }
                },
                legend: { position: 'bottom', labels: { colors: textColor } },
                stroke: { show: false },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            }).render();

            // 3. Yearly Trend Chart (Area/Line)
            new ApexCharts(document.querySelector("#trainingYearlyChart"), {
                series: [{ name: 'จำนวนการลงทะเบียน', data: yearlyCounts }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: { show: false },
                    fontFamily: 'Prompt, sans-serif',
                    sparkline: { enabled: false }
                },
                colors: ['#D71920'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: yearlyLabels,
                    labels: { style: { colors: textColor, fontSize: '10px' } }
                },
                yaxis: {
                    labels: { style: { colors: textColor, fontSize: '10px' } }
                },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            }).render();
        });
    </script>
@endsection