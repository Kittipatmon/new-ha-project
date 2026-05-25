@extends('layouts.sidebar')
@section('title', 'Dashboard สรุปขาด ลา มาสาย')

@section('content')
    <div class="max-w-8xl mx-auto">

        {{-- Header Section --}}
        <div class="md:flex md:items-center md:justify-between mb-4">
            <div class="flex-1 min-w-0">
                <form action="{{ route('leavereports.import') }}" method="POST" enctype="multipart/form-data"
                    class="flex items-center gap-2 bg-white dark:bg-slate-800 p-1.5 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-slate-500
                                                file:mr-4 file:py-1.5 file:px-4
                                                file:rounded-lg file:border-0
                                                file:text-xs file:font-semibold
                                                file:bg-red-50 file:text-red-700
                                                hover:file:bg-red-100 dark:file:bg-red-900 dark:file:text-white
                                                cursor-pointer" required>
                    <button type="submit"
                        class="inline-flex items-center justify-center p-2 rounded-lg text-green-600 hover:bg-green-50 transition-colors tooltip"
                        title="นำเข้า Excel">
                        <i class="fas fa-file-import text-lg"></i>
                    </button>
                </form>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3 relative" x-data="{ open: false }">
                <span class="h-8 w-px bg-gray-300 dark:bg-gray-600 self-center hidden md:block"></span>

                {{-- Export Dropdown Button --}}
                <div class="relative inline-block text-left">
                    <div>
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="inline-flex justify-center items-center w-full rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            id="menu-button" aria-expanded="true" aria-haspopup="true">
                            <i class="fas fa-file-export mr-2"></i>
                            Export
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    {{-- Dropdown Panel --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10 dark:bg-gray-800 dark:border-gray-700 dark:ring-gray-700"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        style="display: none;">
                        <div class="py-1" role="none">
                            {{-- PDF Option --}}
                            <a href="#"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-red-900 group dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-red-400"
                                role="menuitem" tabindex="-1" id="openPdfModalBtn" @click.prevent="open = false">
                                <i class="fas fa-file-pdf mr-2 text-red-600 group-hover:text-red-700"></i> PDF
                            </a>
                            {{-- Excel Option --}}
                            <a href="#"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-green-900 group dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-green-400"
                                role="menuitem" tabindex="-1" id="openExcelModalBtn" @click.prevent="open = false">
                                <i class="fas fa-file-excel mr-2 text-green-600 group-hover:text-green-700"></i> EXCEL
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="filterYear" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ปี</label>
                <select id="filterYear"
                    class="px-4 py-1 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500"></select>
            </div>
            <div>
                <label for="filterDivision"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">สายงาน</label>
                <select id="filterDivision"
                    class="px-4 py-1 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">ทั้งหมด</option>
                </select>
            </div>
            <div>
                <label for="filterMonth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เดือน
                    (ดูรายละเอียด)</label>
                <select id="filterMonth"
                    class="px-4 py-1 mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">ทั้งหมด</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="resetFilters" type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors w-full md:w-auto">
                    <i class="fas fa-undo mr-2"></i>
                    รีเซ็ตตัวกรอง
                </button>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Chart 1 --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        สัดส่วนวันลาตามสายงาน
                    </h3>
                    <span
                        class="text-xs font-medium px-2.5 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        Latest Month
                    </span>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="leaveByDivisionChart"></canvas>
                </div>
            </div>

            {{-- Chart 2 --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        แนวโน้มวันลาทั้งหมด (รายเดือน)
                    </h3>
                    <span
                        class="text-xs font-medium px-2.5 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300">
                        Trend
                    </span>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="leaveByMonthChart"></canvas>
                </div>
            </div>
        </div>
        {{-- Chart: Leave Type Proportion by Division --}}
        <div class="grid grid-cols-1 gap-6 mb-8">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">สัดส่วนการลาตามประเภท (แยกตามสายงาน)
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">แสดงสัดส่วนลาป่วย ลากิจ ลาพักร้อน ลาคลอด
                            และอื่นๆ ต่อจำนวนวันลาทั้งหมดของแต่ละสายงาน</p>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">อัปเดตตามตัวกรองด้านบน</div>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="leaveTypeByDivisionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- <div class="grid grid-cols-1 gap-6 mb-8">
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-shadow hover:shadow-md">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            กราฟสัดสวนการลาตามประเภทการลา (แยกสายงาน)
                                        </h3>
                                        <span
                                            class="text-xs font-medium px-2.5 py-0.5 rounded bg-indigo-100 text-red-800 dark:bg-red-900 dark:text-indigo-300">
                                            Trend
                                        </span>
                                    </div>
                                    <div class="relative h-72 w-full">
                                        <canvas id="leaveByTypeChart"></canvas>
                                    </div>
                                </div>
                            </div> -->

        {{-- Summary Table --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div
                class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    ตารางสรุปข้อมูล (รวมทั้งปี แยกสายงาน) และรายละเอียดเดือน
                </h3>
                <div class="text-sm text-gray-500">
                    แสดงข้อมูลล่าสุด
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="summaryTable">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                รหัสสายงาน</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">ปี
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                รวมวันทำงาน (คน/วัน)</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                พนักงาน (คน)</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                รวมวันลา</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                รายละเอียด (เดือน)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="summaryBody">
                        {{-- Rows rendered by JS --}}
                    </tbody>
                </table>
            </div>
        </div>
        <!-- PDF Modal -->
        <div id="pdfModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    id="closePdfModalBg"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form id="exportForm" action="{{ route('leavereports.pdf') }}" method="GET" target="_blank">
                        <!-- Add hidden input for format -->
                        <input type="hidden" name="export_format" id="exportFormatInput" value="pdf">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                                        id="modal-title">
                                        พิมพ์รายงานสรุปข้อมูล
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">รูปแบบรายงาน</label>
                                            <select name="report_type" id="pdfReportType"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                <option value="yearly">แนวโน้มรายปี</option>
                                                <option value="monthly_range">ช่วงเดือน</option>
                                            </select>
                                        </div>

                                        <div id="pdfYearContainer">
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">ปี</label>
                                            <select name="year" id="pdfYear"
                                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                <!-- populate by JS -->
                                            </select>
                                        </div>

                                        <div id="pdfMonthRangeContainer" class="hidden grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตั้งแต่เดือน</label>
                                                <input type="month" name="start_month" id="pdfStartMonth"
                                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">ถึงเดือน</label>
                                                <input type="month" name="end_month" id="pdfEndMonth"
                                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" id="submitExportBtn"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <i class="fas fa-print mr-2 self-center" id="submitExportIcon"></i> <span
                                    id="submitExportText">พิมพ์ PDF</span>
                            </button>
                            <button type="button" id="closePdfModalBtn"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                ยกเลิก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Data Preparation
            const rawReports = @json($leaveReports ?? []);

            // Normalize Data
            const reports = rawReports.map(r => ({
                division_code: r.division_code,
                report_month: r.report_month, // expect ISO string like 2025-03-01
                year: (() => {
                    try {
                        return new Date(r.report_month).getFullYear();
                    } catch {
                        return null;
                    }
                })(),
                monthKey: (() => {
                    try {
                        const d = new Date(r.report_month);
                        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                    } catch {
                        return '';
                    }
                })(),
                total_leave_days: Number(r.total_leave_days ?? 0),
                working_days: Number(r.working_days ?? 0),
                total_working_days: Number(r.total_working_days ?? 0),
                total_employees: Number(r.total_employees ?? 0),
                sick_days: Number(r.sick_days ?? 0),
                sick_times: Number(r.sick_times ?? 0),
                personal_days: Number(r.personal_days ?? 0),
                personal_times: Number(r.personal_times ?? 0),
                annual_days: Number(r.annual_days ?? 0),
                annual_times: Number(r.annual_times ?? 0),
                maternity_days: Number(r.maternity_days ?? 0),
                maternity_times: Number(r.maternity_times ?? 0),
                other_days: Number(r.other_days ?? 0),
                other_times: Number(r.other_times ?? 0),
            }));

            // Modern Chart Defaults
            Chart.defaults.font.family = "'Inter', 'Sarabun', system-ui, sans-serif";
            Chart.defaults.color = '#64748b'; // slate-500

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Hide default legend for cleaner look usually
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)', // slate-900
                        titleColor: '#f8fafc',
                        bodyColor: '#f8fafc',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: {
                            dash: [4, 4],
                            display: false
                        },
                        grid: {
                            color: '#e2e8f0', // slate-200
                            drawBorder: false,
                        },
                        ticks: {
                            padding: 10
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
            };

            // Dark mode check for chart colors
            if (document.documentElement.classList.contains('dark')) {
                commonOptions.scales.y.grid.color = '#334155'; // slate-700
            }

            // Prepare filter options
            const years = Array.from(new Set(reports.map(r => r.year).filter(Boolean))).sort((a, b) => a - b);
            const divisions = Array.from(new Set(reports.map(r => r.division_code))).sort();
            const monthsByYear = new Map();
            reports.forEach(r => {
                if (!monthsByYear.has(r.year)) monthsByYear.set(r.year, new Set());
                monthsByYear.get(r.year).add(r.monthKey);
            });

            const yearSelect = document.getElementById('filterYear');
            const divisionSelect = document.getElementById('filterDivision');
            const monthSelect = document.getElementById('filterMonth');
            const resetBtn = document.getElementById('resetFilters');

            if (yearSelect) {
                yearSelect.innerHTML = years.map(y => `<option value="${y}">${y}</option>`).join('');
                if (years.length) yearSelect.value = years[years.length - 1];
            }
            if (divisionSelect) {
                divisionSelect.insertAdjacentHTML('beforeend', divisions.map(d => `<option value="${d}">${d}</option>`)
                    .join(''));
            }

            function populateMonths() {
                const y = Number(yearSelect?.value);
                const monthsSet = monthsByYear.get(y) || new Set();
                const monthsArr = Array.from(monthsSet).sort();
                const labels = monthsArr.map(m => {
                    const [yy, mm] = m.split('-');
                    const date = new Date(Number(yy), Number(mm) - 1, 1);
                    return {
                        key: m,
                        label: date.toLocaleDateString('th-TH', {
                            month: 'short',
                            year: '2-digit'
                        })
                    };
                });
                if (monthSelect) {
                    monthSelect.innerHTML = `<option value="">ทั้งหมด</option>` + labels.map(x =>
                        `<option value="${x.key}">${x.label}</option>`).join('');
                }
            }
            populateMonths();

            // Filter logic
            function getFilteredReports() {
                const y = yearSelect?.value || '';
                const d = divisionSelect?.value || '';
                const m = monthSelect?.value || '';
                return reports.filter(r => {
                    const byYear = y ? String(r.year) === String(y) : true;
                    const byDivision = d ? r.division_code === d : true;
                    const byMonth = m ? r.monthKey === m : true;
                    return byYear && byDivision && byMonth;
                });
            }

            // Aggregation per division per selected year
            function aggregateByDivisionYear(filtered) {
                const map = new Map();
                filtered.forEach(r => {
                    const key = r.division_code;
                    const agg = map.get(key) || {
                        division_code: r.division_code,
                        year: r.year,
                        total_working_days: 0,
                        total_employees: 0,
                        total_leave_days: 0,
                    };
                    agg.total_working_days += r.total_working_days || 0;
                    agg.total_employees = Math.max(agg.total_employees, r.total_employees || 0);
                    agg.total_leave_days += r.total_leave_days || 0;
                    map.set(key, agg);
                });
                return Array.from(map.values()).sort((a, b) => a.division_code.localeCompare(b.division_code));
            }

            // Charts
            let barChart = null;
            let lineChart = null;
            let stackedTypeChart = null;

            function renderBarChart(filtered) {
                const divCtx = document.getElementById('leaveByDivisionChart');
                if (!divCtx) return;
                const agg = aggregateByDivisionYear(filtered);
                const labels = agg.map(a => a.division_code);
                const data = agg.map(a => a.total_leave_days);
                const palette = ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6',
                    '#64748b', '#94a3b8'
                ];
                const barData = {
                    labels,
                    datasets: [{
                        label: 'วันลา',
                        data,
                        backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                };
                if (barChart) barChart.destroy();
                barChart = new Chart(divCtx, {
                    type: 'bar',
                    data: barData,
                    options: commonOptions
                });
            }

            function renderLineChart(filtered) {
                const monthCtx = document.getElementById('leaveByMonthChart');
                if (!monthCtx) return;
                const months = Array.from(new Set(filtered.map(r => r.monthKey))).sort();
                const monthlyTotals = months.map(m => filtered.filter(r => r.monthKey === m).reduce((sum, r) => sum + r
                    .total_leave_days, 0));
                const formattedMonths = months.map(m => {
                    const [yy, mm] = m.split('-');
                    const date = new Date(Number(yy), Number(mm) - 1, 1);
                    return date.toLocaleDateString('th-TH', {
                        month: 'short',
                        year: '2-digit'
                    });
                });
                const lineData = {
                    labels: formattedMonths,
                    datasets: [{
                        label: 'จำนวนวันลาทั้งหมด',
                        data: monthlyTotals,
                        borderColor: '#4f46e5',
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');
                            return gradient;
                        },
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointHoverBackgroundColor: '#4f46e5',
                        pointHoverBorderColor: '#ffffff',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                };
                if (lineChart) lineChart.destroy();
                lineChart = new Chart(monthCtx, {
                    type: 'line',
                    data: lineData,
                    options: commonOptions
                });
            }

            // Stacked 100% bar chart: leave type proportions by division
            function renderLeaveTypeByDivisionChart(filtered) {
                const ctx = document.getElementById('leaveTypeByDivisionChart');
                if (!ctx) return;

                // Aggregate totals per division
                const byDivision = new Map();
                filtered.forEach(r => {
                    const key = r.division_code;
                    const agg = byDivision.get(key) || {
                        division: key,
                        sick: 0,
                        personal: 0,
                        annual: 0,
                        maternity: 0,
                        other: 0,
                        total: 0,
                    };
                    agg.sick += r.sick_days || 0;
                    agg.personal += r.personal_days || 0;
                    agg.annual += r.annual_days || 0;
                    agg.maternity += r.maternity_days || 0;
                    agg.other += r.other_days || 0;
                    agg.total += (r.total_leave_days || 0);
                    byDivision.set(key, agg);
                });

                const divisions = Array.from(byDivision.values())
                    .sort((a, b) => String(a.division).localeCompare(String(b.division)));

                const labels = divisions.map(d => d.division);
                // If total is zero, avoid divide by zero and keep 0
                const toPct = (val, total) => total > 0 ? (val / total) * 100 : 0;

                const sickData = divisions.map(d => toPct(d.sick, d.total));
                const personalData = divisions.map(d => toPct(d.personal, d.total));
                const annualData = divisions.map(d => toPct(d.annual, d.total));
                const maternityData = divisions.map(d => toPct(d.maternity, d.total));
                const otherData = divisions.map(d => toPct(d.other, d.total));

                const data = {
                    labels,
                    datasets: [
                        { label: 'ลาป่วย', data: sickData, backgroundColor: '#3b82f6' },
                        { label: 'ลากิจ', data: personalData, backgroundColor: '#10b981' },
                        { label: 'ลาพักร้อน', data: annualData, backgroundColor: '#f59e0b' },
                        { label: 'ลาคลอด', data: maternityData, backgroundColor: '#ef4444' },
                        { label: 'อื่นๆ', data: otherData, backgroundColor: '#64748b' },
                    ]
                };

                const stackedOptions = JSON.parse(JSON.stringify(commonOptions));
                stackedOptions.plugins.legend.display = true;
                stackedOptions.scales.x.stacked = true;
                stackedOptions.scales.y.stacked = true;
                stackedOptions.scales.y.suggestedMax = 100;
                stackedOptions.scales.y.ticks = {
                    callback: (value) => `${value}%`
                };
                stackedOptions.plugins.tooltip = {
                    callbacks: {
                        label: function (context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y ?? context.raw;
                            return `${label}: ${value.toFixed(1)}%`;
                        }
                    }
                };

                if (stackedTypeChart) stackedTypeChart.destroy();
                stackedTypeChart = new Chart(ctx, {
                    type: 'bar',
                    data,
                    options: stackedOptions
                });
            }

            // Summary table rendering with expandable monthly details and type breakdown
            function renderSummaryTable(filtered) {
                const tbody = document.getElementById('summaryBody');
                if (!tbody) return;
                const agg = aggregateByDivisionYear(filtered);
                if (!agg.length) {
                    tbody.innerHTML =
                        `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400"><div class="flex flex-col items-center justify-center"><i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i><p>ไม่พบข้อมูลรายงานในช่วงเวลานี้</p></div></td></tr>`;
                    return;
                }

                const numberFmt = new Intl.NumberFormat();
                tbody.innerHTML = agg.map(a => {
                    const rowId = `row-${a.division_code}-${a.year}`;
                    // monthly breakdown for this division/year
                    const monthly = filtered.filter(r => r.division_code === a.division_code && r.year === a
                        .year)
                        .reduce((acc, r) => {
                            acc[r.monthKey] = acc[r.monthKey] || {
                                monthKey: r.monthKey,
                                working_days: 0,
                                total_working_days: 0,
                                total_leave_days: 0,
                                sick_days: 0,
                                sick_times: 0,
                                personal_days: 0,
                                personal_times: 0,
                                annual_days: 0,
                                annual_times: 0,
                                maternity_days: 0,
                                maternity_times: 0,
                                other_days: 0,
                                other_times: 0,
                            };
                            acc[r.monthKey].working_days += r.working_days;
                            acc[r.monthKey].total_working_days += r.total_working_days;
                            acc[r.monthKey].total_leave_days += r.total_leave_days;
                            acc[r.monthKey].sick_days += r.sick_days;
                            acc[r.monthKey].sick_times += r.sick_times;
                            acc[r.monthKey].personal_days += r.personal_days;
                            acc[r.monthKey].personal_times += r.personal_times;
                            acc[r.monthKey].annual_days += r.annual_days;
                            acc[r.monthKey].annual_times += r.annual_times;
                            acc[r.monthKey].maternity_days += r.maternity_days;
                            acc[r.monthKey].maternity_times += r.maternity_times;
                            acc[r.monthKey].other_days += r.other_days;
                            acc[r.monthKey].other_times += r.other_times;
                            return acc;
                        }, {});
                    const monthlyRows = Object.values(monthly).sort((x, y) => x.monthKey.localeCompare(y
                        .monthKey)).map(m => {
                            const [yy, mm] = m.monthKey.split('-');
                            const label = new Date(Number(yy), Number(mm) - 1, 1).toLocaleDateString(
                                'th-TH', {
                                month: 'short',
                                year: '2-digit'
                            });
                            return `
                                        <tr>
                                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">${label}</td>
                                            <td class="px-6 py-3 text-left text-sm">${numberFmt.format(m.working_days)} วัน</td>
                                            <td class="px-6 py-3 text-left text-sm">${numberFmt.format(m.total_working_days)} วัน</td>
                                            <td class="px-6 py-3 text-right text-sm">${numberFmt.format(m.total_leave_days)} วัน</td>
                                            <td class="px-6 py-3">
                                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                                                    <div class="text-xs text-red-600">ลาป่วย: ${numberFmt.format(m.sick_days)} วัน • ${numberFmt.format(m.sick_times)} ครั้ง</div>
                                                    <div class="text-xs text-amber-600">ลากิจ: ${numberFmt.format(m.personal_days)} วัน • ${numberFmt.format(m.personal_times)} ครั้ง</div>
                                                    <div class="text-xs text-blue-600">พักร้อน: ${numberFmt.format(m.annual_days)} วัน • ${numberFmt.format(m.annual_times)} ครั้ง</div>
                                                    <div class="text-xs text-purple-600">ลาคลอด: ${numberFmt.format(m.maternity_days)} วัน • ${numberFmt.format(m.maternity_times)} ครั้ง</div>
                                                    <div class="text-xs text-gray-600">อื่นๆ: ${numberFmt.format(m.other_days)} วัน • ${numberFmt.format(m.other_times)} ครั้ง</div>
                                                </div>
                                            </td>
                                        </tr>`;
                        }).join('');
                    return `
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">${a.division_code.substring(0, 2)}</div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">${a.division_code}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">${a.year}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-200 font-medium">${numberFmt.format(a.total_working_days)}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">${numberFmt.format(a.total_employees)}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">${numberFmt.format(a.total_leave_days)} วัน</span></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button type="button" class="btn btn-info btn-sm btn-outline" data-toggle="${rowId}">ดูรายละเอียด</button>
                                        </td>
                                    </tr>
                                    <tr id="${rowId}" class="hidden">
                                        <td colspan="6" class="p-0 border-none">
                                            <div class="bg-slate-50 dark:bg-gray-700/30 px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">รายละเอียดเดือน (แบ่งประเภทการลา)</p>
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full">
                                                        <thead>
                                                            <tr>
                                                                <th class="px-6 py-3 text-left text-xs text-gray-500">เดือน</th>
                                                                <th class="px-6 py-3 text-left text-xs text-gray-500">วันทำงาน</th>
                                                                <th class="px-6 py-3 text-left text-xs text-gray-500">วันทำงานทั้งหมด</th>
                                                                <th class="px-6 py-3 text-right text-xs text-gray-500">รวมวันลา</th>
                                                                <th class="px-6 py-3 text-left text-xs text-gray-500">แยกประเภท</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ${monthlyRows}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`;
                }).join('');

                // attach toggles
                tbody.querySelectorAll('button[data-toggle]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-toggle');
                        const row = document.getElementById(id);
                        if (!row) return;
                        row.classList.toggle('hidden');
                    });
                });
            }

            function renderAll() {
                const filtered = getFilteredReports();
                renderBarChart(filtered);
                renderLineChart(filtered);
                renderLeaveTypeByDivisionChart(filtered);
                renderSummaryTable(filtered);
            }

            renderAll();

            yearSelect?.addEventListener('change', () => {
                populateMonths();
                renderAll();
            });
            divisionSelect?.addEventListener('change', renderAll);
            monthSelect?.addEventListener('change', renderAll);
            resetBtn?.addEventListener('click', () => {
                if (years.length) yearSelect.value = years[years.length - 1];
                if (divisionSelect) divisionSelect.value = '';
                populateMonths();
                if (monthSelect) monthSelect.value = '';
                renderAll();
            });

            // PDF Modal Logic
            const pdfModal = document.getElementById('pdfModal');
            const openPdfModalBtn = document.getElementById('openPdfModalBtn');
            const openExcelModalBtn = document.getElementById('openExcelModalBtn');
            const closePdfModalBtn = document.getElementById('closePdfModalBtn');
            const closePdfModalBg = document.getElementById('closePdfModalBg');

            const pdfReportType = document.getElementById('pdfReportType');
            const pdfYearContainer = document.getElementById('pdfYearContainer');
            const pdfMonthRangeContainer = document.getElementById('pdfMonthRangeContainer');
            const pdfYear = document.getElementById('pdfYear');
            const pdfStartMonth = document.getElementById('pdfStartMonth');
            const pdfEndMonth = document.getElementById('pdfEndMonth');

            const exportForm = document.getElementById('exportForm');
            const exportFormatInput = document.getElementById('exportFormatInput');
            const submitExportBtn = document.getElementById('submitExportBtn');
            const submitExportIcon = document.getElementById('submitExportIcon');
            const submitExportText = document.getElementById('submitExportText');
            const modalTitle = document.getElementById('modal-title');

            function openExportModal(format) {
                // Populate years if empty
                if (pdfYear.options.length === 0) {
                    pdfYear.innerHTML = years.map(y => `<option value="${y}">${y}</option>`).join('');
                    if (years.length) pdfYear.value = years[years.length - 1];
                }

                // Set default month range to current year
                if (!pdfStartMonth.value && years.length) {
                    const maxYear = years[years.length - 1];
                    pdfStartMonth.value = `${maxYear}-01`;
                    pdfEndMonth.value = `${maxYear}-12`;
                }

                exportFormatInput.value = format;

                if (format === 'pdf') {
                    modalTitle.innerText = 'พิมพ์รายงานสรุปข้อมูล (PDF)';
                    submitExportBtn.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm';
                    submitExportIcon.className = 'fas fa-file-pdf mr-2 self-center';
                    submitExportText.innerText = 'Export PDF';
                } else if (format === 'excel') {
                    modalTitle.innerText = 'ส่งออกรายงานสรุปข้อมูล (EXCEL)';
                    submitExportBtn.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm';
                    submitExportIcon.className = 'fas fa-file-excel mr-2 self-center';
                    submitExportText.innerText = 'Export Excel';
                }

                pdfModal.classList.remove('hidden');
            }

            if (openPdfModalBtn && openExcelModalBtn && pdfModal) {
                openPdfModalBtn.addEventListener('click', () => openExportModal('pdf'));
                openExcelModalBtn.addEventListener('click', () => openExportModal('excel'));

                const closeModal = () => pdfModal.classList.add('hidden');
                closePdfModalBtn.addEventListener('click', closeModal);
                closePdfModalBg.addEventListener('click', closeModal);

                pdfReportType.addEventListener('change', (e) => {
                    if (e.target.value === 'yearly') {
                        pdfYearContainer.classList.remove('hidden');
                        pdfMonthRangeContainer.classList.add('hidden');
                    } else {
                        pdfYearContainer.classList.add('hidden');
                        pdfMonthRangeContainer.classList.remove('hidden');
                        pdfMonthRangeContainer.classList.add('grid');
                    }
                });
            }
        });
    </script>
@endsection