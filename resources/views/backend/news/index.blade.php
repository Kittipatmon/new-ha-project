@extends('layouts.app')
@section('title', 'ข่าวสารและกิจกรรม')
@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-newspaper text-red-600"></i> จัดการข้อมูลข่าวสารและกิจกรรม
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                จัดการบทความข่าวสาร กิจกรรมภายในองค์กร พร้อมสถิติยอดการเข้าชมและเปิดอ่าน
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="showNewsAnalyticsModal()" id="openNewsAnalyticsModal" class="btn bg-blue-600 hover:bg-blue-700 text-white shadow-md flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all cursor-pointer">
                <i class="fa-solid fa-chart-line"></i> สถิติการเข้าชม
            </button>
            <button type="button" id="openCreateModal" class="btn btn-success bg-green-600 hover:bg-green-700 text-white shadow-md flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all cursor-pointer">
                <i class="fa-solid fa-plus"></i> เพิ่มข่าวสารใหม่
            </button>
        </div>
    </div>

    <!-- Analytics Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400">ยอดการแสดงผลทั้งหมด (Total Views)</span>
                <h3 id="news_overview_total_views" class="text-2xl font-black text-slate-800 dark:text-white">{{ number_format($newsItems->sum('views')) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-arrow-pointer"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400">ยอดการคลิกเข้าชมทั้งหมด (Total Clicks)</span>
                <h3 id="news_overview_total_clicks" class="text-2xl font-black text-slate-800 dark:text-white">{{ number_format($newsItems->sum('clicks')) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                @php
                    $totalNewsViews = $newsItems->sum('views');
                    $totalNewsClicks = $newsItems->sum('clicks');
                    $newsCtr = $totalNewsViews > 0 ? round(($totalNewsClicks / $totalNewsViews) * 100, 2) : 0;
                @endphp
                <span class="text-xs text-slate-500 dark:text-slate-400">อัตราการคลิกต่อการมองเห็น (CTR)</span>
                <h3 id="news_overview_ctr" class="text-2xl font-black text-slate-800 dark:text-white">{{ $newsCtr }}%</h3>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            รูปภาพ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">หัวข้อข่าวสาร</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">สถิติ (VIEW / CLICK)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">วันที่เผยแพร่ / สร้าง</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">สถานะ</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">การกระทำ</th>
                    </tr>
                </thead>
                <tbody id="news-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($newsItems as $news)
                    <tr id="news-{{ $news->news_id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            @php
                                $images = is_array($news->image_path) ? $news->image_path : ($news->image_path ? [$news->image_path] : []);
                            @endphp
                            @if(count($images))
                                <div class="flex space-x-2">
                                    @foreach(array_slice($images,0,3) as $img)
                                        <img src="{{ asset($img) }}" 
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/150?text=No+Image';" 
                                            alt="News Image" 
                                            class="h-12 w-12 object-cover rounded-md border border-gray-200" loading="lazy">
                                    @endforeach
                                    @if(count($images) > 3)
                                        <span class="flex items-center justify-center h-12 w-12 rounded-md bg-gray-100 text-xs text-gray-500 dark:text-gray-400">
                                            +{{ count($images) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">ไม่มีภาพ</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white min-w-[200px] break-words whitespace-normal">
                            <a href="{{ route('news.detail', $news->news_id) }}" target="_blank" class="hover:text-red-600 transition-colors">
                                {{ $news->title }}
                            </a>
                        </td>
                        <!-- Views & Clicks Statistics Badge -->
                        <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                            <div class="inline-flex items-center gap-2 bg-slate-100 dark:bg-gray-700/60 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-gray-600">
                                <span class="flex items-center gap-1 text-slate-700 dark:text-slate-200" title="ยอดการมองเห็น (Views)">
                                    <i class="fa-regular fa-eye text-blue-500"></i>
                                    <strong id="news_views_{{ $news->news_id }}">{{ number_format($news->views ?? 0) }}</strong>
                                </span>
                                <span class="text-slate-300 dark:text-slate-600">|</span>
                                <span class="flex items-center gap-1 text-slate-700 dark:text-slate-200" title="ยอดคลิกเข้าชม (Clicks)">
                                    <i class="fa-solid fa-arrow-pointer text-green-500"></i>
                                    <strong id="news_clicks_{{ $news->news_id }}">{{ number_format($news->clicks ?? 0) }}</strong>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-300">
                            @if($news->published_date)
                                <div class="font-medium text-slate-700 dark:text-slate-200">
                                    <i class="fa-regular fa-calendar-check text-green-500 mr-1"></i> {{ \Carbon\Carbon::parse($news->published_date)->format('d/m/Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400">สร้าง: {{ $news->created_at->format('d/m/Y') }}</div>
                            @else
                                <div>{{ $news->created_at->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                            @if($news->is_active)
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                    <i class="fa-solid fa-circle-check mr-1 text-[10px] self-center"></i> เผยแพร่
                                </span>
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                    <i class="fa-solid fa-pause mr-1 text-[10px] self-center"></i> ไม่ใช้งาน
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <a href="{{ route('news.detail', $news->news_id) }}" target="_blank" class="btn btn-sm p-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white" title="ดูหน้าข่าวจริง">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                            <button class="btn btn-warning btn-sm edit-btn p-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white" data-id="{{ $news->news_id }}" title="แก้ไข">
                                <i class="fa-solid fa-pen-to-square"></i> 
                            </button>
                            @if(Auth::check() && Auth::user()->canDelete())
                            <button class="btn btn-error btn-sm text-white delete-btn p-2 rounded-lg bg-red-600 hover:bg-red-700" data-id="{{ $news->news_id }}" title="ลบ">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
<!-- Modal Analytics Statistics for News -->
<div id="newsAnalyticsModal" onclick="if(event.target === this) closeNewsAnalyticsModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-[9999] p-4 hidden" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
        
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-blue-600"></i> สถิติการแสดงผลและการคลิกเข้าชมข่าวสาร
                </h3>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    เรียลไทม์ (Live)
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.loadNewsAnalyticsData(true)" title="รีเฟรชข้อมูลตอนนี้" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i id="newsAnalyticsRefreshIcon" class="fa-solid fa-arrows-rotate text-sm"></i>
                </button>
                <button type="button" onclick="closeNewsAnalyticsModal()" class="close-analytics-modal text-gray-400 hover:text-red-500 transition-colors focus:outline-none cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
            <!-- Filter toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-gray-700/40 p-3 rounded-xl border border-slate-100 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <label for="news_analytics_select" class="text-xs font-semibold text-slate-600 dark:text-slate-300">เลือกข่าวสาร:</label>
                    <select id="news_analytics_select" class="text-xs rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-1.5 px-3 max-w-[260px]">
                        <option value="">-- ข่าวสารทั้งหมด --</option>
                        @foreach ($newsItems as $n)
                            <option value="{{ $n->news_id }}">{{ $n->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <button type="button" class="news-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-600 text-white" data-days="7">7 วันล่าสุด</button>
                    <button type="button" class="news-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300" data-days="14">14 วัน</button>
                    <button type="button" class="news-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300" data-days="30">30 วัน</button>
                </div>
            </div>

            <!-- Peak Hours & Key Metrics Highlight Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/20 border border-amber-200 dark:border-amber-800/60 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 dark:bg-amber-400/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] font-semibold text-amber-700 dark:text-amber-300">ช่วงเวลาที่มีการเข้าชมมากที่สุด</span>
                        <span id="newsPeakHourValue" class="text-sm font-extrabold text-amber-900 dark:text-amber-100 truncate block">กำลังคำนวณ...</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/20 border border-blue-200 dark:border-blue-800/60 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 dark:bg-blue-400/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] font-semibold text-blue-700 dark:text-blue-300">ยอดการแสดงผลรวม</span>
                        <span id="newsPeriodViewsValue" class="text-sm font-extrabold text-blue-900 dark:text-blue-100">0 ครั้ง</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20 border border-emerald-200 dark:border-emerald-800/60 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-400/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-arrow-pointer"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">ยอดคลิก / CTR รวม</span>
                        <span id="newsPeriodClicksValue" class="text-sm font-extrabold text-emerald-900 dark:text-emerald-100">0 ครั้ง (0%)</span>
                    </div>
                </div>
            </div>

            <!-- Chart Switch Tabs -->
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                <div class="flex items-center gap-2">
                    <button type="button" id="tabNewsChartDaily" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors bg-slate-900 text-white dark:bg-white dark:text-slate-900">
                        <i class="fa-solid fa-calendar-day mr-1"></i> กราฟรายวัน
                    </button>
                    <button type="button" id="tabNewsChartHourly" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">
                        <i class="fa-solid fa-clock mr-1"></i> แจกแจงตามช่วงเวลา 24 ชม. (Peak Times)
                    </button>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative h-64">
                <canvas id="newsAnalyticsChart"></canvas>
            </div>

            <!-- Summary Table of Daily Stats -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-2">
                    ตารางแจกแจงสถิติรายวัน
                </h4>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-slate-600 dark:text-slate-300">
                            <tr>
                                <th class="px-4 py-2 text-left">วันที่ (Date)</th>
                                <th class="px-4 py-2 text-center text-blue-600">การแสดงผล (Views)</th>
                                <th class="px-4 py-2 text-center text-green-600">การคลิก (Clicks)</th>
                                <th class="px-4 py-2 text-center text-purple-600">อัตราการคลิก (CTR)</th>
                            </tr>
                        </thead>
                        <tbody id="newsAnalyticsTableBody" class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex justify-end px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <button type="button" onclick="closeNewsAnalyticsModal()" class="close-analytics-modal px-4 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                ปิด
            </button>
        </div>
    </div>
</div>

@include('backend.news._modal')

@endsection

@section('scripts')
<!-- Chart.js CDN for Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let newsAnalyticsChart = null;
    let currentNewsAnalyticsDays = 7;
    let currentNewsChartMode = 'daily'; // 'daily' or 'hourly'
    let currentNewsLogs = [];
    let currentNewsHourlyLogs = [];
    let newsAnalyticsPollingTimer = null;

    window.showNewsAnalyticsModal = function() {
        const modal = document.getElementById('newsAnalyticsModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
        window.loadNewsAnalyticsData(false);

        // Start real-time background polling every 5 seconds while modal is open
        if (newsAnalyticsPollingTimer) clearInterval(newsAnalyticsPollingTimer);
        newsAnalyticsPollingTimer = setInterval(() => {
            window.loadNewsAnalyticsData(true);
        }, 5000);
    };

    window.closeNewsAnalyticsModal = function() {
        const modal = document.getElementById('newsAnalyticsModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        if (newsAnalyticsPollingTimer) {
            clearInterval(newsAnalyticsPollingTimer);
            newsAnalyticsPollingTimer = null;
        }
    };

    window.renderNewsDailyChart = function(logs) {
        const dates = [];
        const viewsMap = {};
        const clicksMap = {};

        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        for (let i = currentNewsAnalyticsDays - 1; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            const dateStr = formatDate(d);
            dates.push(dateStr);
            viewsMap[dateStr] = 0;
            clicksMap[dateStr] = 0;
        }

        logs.forEach(item => {
            if (item.view_date) {
                const dateOnly = String(item.view_date).split('T')[0];
                if (viewsMap.hasOwnProperty(dateOnly)) {
                    if (item.event_type === 'view') {
                        viewsMap[dateOnly] += parseInt(item.count || 0);
                    } else if (item.event_type === 'click') {
                        clicksMap[dateOnly] += parseInt(item.count || 0);
                    }
                }
            }
        });

        const viewsData = dates.map(d => viewsMap[d] || 0);
        const clicksData = dates.map(d => clicksMap[d] || 0);

        const canvasEl = document.getElementById('newsAnalyticsChart');
        if (!canvasEl) return;
        const ctx = canvasEl.getContext('2d');
        if (newsAnalyticsChart) {
            newsAnalyticsChart.destroy();
        }

        newsAnalyticsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates.map(d => {
                    const parts = d.split('-');
                    return parts[2] + '/' + parts[1];
                }),
                datasets: [
                    {
                        label: 'การแสดงผล (Views)',
                        data: viewsData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'การคลิก (Clicks)',
                        data: clicksData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    };

    window.renderNewsHourlyChart = function(hourlyLogs) {
        const hours = [];
        const hourLabels = [];
        const viewsMap = {};
        const clicksMap = {};

        for (let h = 0; h < 24; h++) {
            hours.push(h);
            hourLabels.push(String(h).padStart(2, '0') + ':00');
            viewsMap[h] = 0;
            clicksMap[h] = 0;
        }

        hourlyLogs.forEach(item => {
            const h = parseInt(item.hour);
            if (viewsMap.hasOwnProperty(h)) {
                if (item.event_type === 'view') {
                    viewsMap[h] += parseInt(item.count || 0);
                } else if (item.event_type === 'click') {
                    clicksMap[h] += parseInt(item.count || 0);
                }
            }
        });

        const viewsData = hours.map(h => viewsMap[h] || 0);
        const clicksData = hours.map(h => clicksMap[h] || 0);

        const canvasEl = document.getElementById('newsAnalyticsChart');
        if (!canvasEl) return;
        const ctx = canvasEl.getContext('2d');
        if (newsAnalyticsChart) {
            newsAnalyticsChart.destroy();
        }

        newsAnalyticsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hourLabels,
                datasets: [
                    {
                        label: 'การแสดงผลตามช่วงเวลา (Views)',
                        data: viewsData,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'การคลิกตามช่วงเวลา (Clicks)',
                        data: clicksData,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    };

    window.updateNewsPeakSummary = function(logs, hourlyLogs) {
        let totalViews = 0;
        let totalClicks = 0;
        logs.forEach(item => {
            if (item.event_type === 'view') totalViews += parseInt(item.count || 0);
            if (item.event_type === 'click') totalClicks += parseInt(item.count || 0);
        });

        $('#newsPeriodViewsValue').text(totalViews.toLocaleString() + ' ครั้ง');
        const ctr = totalViews > 0 ? ((totalClicks / totalViews) * 100).toFixed(1) + '%' : '0%';
        $('#newsPeriodClicksValue').text(totalClicks.toLocaleString() + ' ครั้ง (' + ctr + ')');

        const hourViews = {};
        for (let h = 0; h < 24; h++) hourViews[h] = 0;

        hourlyLogs.forEach(item => {
            const h = parseInt(item.hour);
            if (item.event_type === 'view' && hourViews.hasOwnProperty(h)) {
                hourViews[h] += parseInt(item.count || 0);
            }
        });

        let peakHour = null;
        let maxViews = 0;
        Object.keys(hourViews).forEach(h => {
            if (hourViews[h] > maxViews) {
                maxViews = hourViews[h];
                peakHour = parseInt(h);
            }
        });

        if (peakHour !== null && maxViews > 0) {
            const nextHour = (peakHour + 1) % 24;
            const timeStr = `${String(peakHour).padStart(2, '0')}:00 - ${String(nextHour).padStart(2, '0')}:00 น.`;
            $('#newsPeakHourValue').html(`<span class="text-amber-600 dark:text-amber-400">${timeStr}</span> <span class="text-xs font-normal text-slate-500">(${maxViews} วิว)</span>`);
        } else {
            $('#newsPeakHourValue').text('ยังไม่มีข้อมูลการเข้าชม');
        }
    };

    window.renderNewsTable = function(logs) {
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const dateSummary = {};
        for (let i = currentNewsAnalyticsDays - 1; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            const dateStr = formatDate(d);
            dateSummary[dateStr] = { views: 0, clicks: 0 };
        }

        logs.forEach(item => {
            if (item.view_date) {
                const d = String(item.view_date).split('T')[0];
                if (dateSummary[d]) {
                    if (item.event_type === 'view') dateSummary[d].views += parseInt(item.count || 0);
                    if (item.event_type === 'click') dateSummary[d].clicks += parseInt(item.count || 0);
                }
            }
        });

        let rows = '';
        Object.keys(dateSummary).sort().reverse().forEach(date => {
            const v = dateSummary[date].views;
            const c = dateSummary[date].clicks;
            const ctr = v > 0 ? ((c / v) * 100).toFixed(1) + '%' : '0%';
            rows += `
                <tr>
                    <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-300">${date}</td>
                    <td class="px-4 py-2 text-center font-bold text-blue-600">${v}</td>
                    <td class="px-4 py-2 text-center font-bold text-green-600">${c}</td>
                    <td class="px-4 py-2 text-center text-purple-600 font-semibold">${ctr}</td>
                </tr>
            `;
        });

        $('#newsAnalyticsTableBody').html(rows || '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-400">ไม่มีข้อมูลในช่วงเวลานี้</td></tr>');
    };

    window.loadNewsAnalyticsData = function(isSilent = false) {
        const newsId = $('#news_analytics_select').val() || '';

        if (!isSilent) {
            $('#newsAnalyticsTableBody').html('<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"><i class="fa-solid fa-spinner fa-spin text-xl text-blue-600 mr-2"></i> กำลังประมวลผลสถิติ...</td></tr>');
            $('#newsPeakHourValue').text('กำลังคำนวณ...');
        } else {
            $('#newsAnalyticsRefreshIcon').addClass('fa-spin text-blue-600');
        }

        $.ajax({
            url: '{{ route("news.analytics.data") }}',
            type: 'GET',
            data: { news_id: newsId, days: currentNewsAnalyticsDays },
            dataType: 'json',
            success: function(res) {
                $('#newsAnalyticsRefreshIcon').removeClass('fa-spin text-blue-600');

                currentNewsLogs = res.logs || [];
                currentNewsHourlyLogs = res.hourly_logs || [];

                window.updateNewsPeakSummary(currentNewsLogs, currentNewsHourlyLogs);

                // Update Overview Cards on Main Page in real-time
                if (res.total_views !== undefined) {
                    $('#news_overview_total_views').text(Number(res.total_views).toLocaleString());
                }
                if (res.total_clicks !== undefined) {
                    $('#news_overview_total_clicks').text(Number(res.total_clicks).toLocaleString());
                }
                if (res.total_views !== undefined && res.total_clicks !== undefined) {
                    const ctr = res.total_views > 0 ? ((res.total_clicks / res.total_views) * 100).toFixed(2) + '%' : '0%';
                    $('#news_overview_ctr').text(ctr);
                }

                // Update individual news views and clicks in the table in real-time
                if (res.news_items && Array.isArray(res.news_items)) {
                    res.news_items.forEach(n => {
                        const vEl = document.getElementById('news_views_' + n.news_id);
                        const cEl = document.getElementById('news_clicks_' + n.news_id);
                        if (vEl) vEl.innerText = Number(n.views || 0).toLocaleString();
                        if (cEl) cEl.innerText = Number(n.clicks || 0).toLocaleString();
                    });
                }

                const renderActiveChart = () => {
                    if (currentNewsChartMode === 'hourly') {
                        window.renderNewsHourlyChart(currentNewsHourlyLogs);
                    } else {
                        window.renderNewsDailyChart(currentNewsLogs);
                    }
                };

                try {
                    if (typeof Chart !== 'undefined') {
                        renderActiveChart();
                    } else {
                        $.getScript('https://cdn.jsdelivr.net/npm/chart.js', function() {
                            renderActiveChart();
                        });
                    }
                } catch (err) {
                    console.error("News Chart Render Error:", err);
                }
                window.renderNewsTable(currentNewsLogs);
            },
            error: function(xhr, status, error) {
                $('#newsAnalyticsRefreshIcon').removeClass('fa-spin text-blue-600');
                console.error("News Analytics Load Error:", error, xhr);
                if (!isSilent) {
                    $('#newsAnalyticsTableBody').html('<tr><td colspan="4" class="px-4 py-6 text-center text-red-500 font-medium"><i class="fa-solid fa-circle-exclamation mr-1.5"></i> เกิดข้อผิดพลาดในการโหลดข้อมูลสถิติ (' + (xhr.status || 'Error') + ')</td></tr>');
                    $('#newsPeakHourValue').text('ไม่สามารถโหลดข้อมูลได้');
                }
            }
        });
    };

    $(document).ready(function() {
        // CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Open Create Modal
        $('#openCreateModal').on('click', function() {
            $('#newsForm').trigger('reset');
            $('#modalTitle').text('เพิ่มข่าวสารใหม่');
            $('#news_id').val('');
            $('#images_preview').html('');
            $('#file_news_preview').html('');
            $('#link_news').val('');
            $('#newsModal').removeClass('hidden');
        });

        // Close Modal
        $('.close-modal').on('click', function() {
            $('#newsModal').addClass('hidden');
        });

        // Open Edit Modal
        $('body').on('click', '.edit-btn', function() {
            var newsId = $(this).data('id');
            $.get('/news/' + newsId + '/edit', function(data) {
                $('#modalTitle').text('แก้ไขข่าวสาร');
                $('#news_id').val(data.news_id);
                $('#title').val(data.title);
                $('#content').val(data.content);
                // Safe date handling (รองรับ null หรือรูปแบบไม่ใช่ ISO)
                let pd = '';
                if (data.published_date) {
                    // ถ้าเป็นรูปแบบ 'YYYY-MM-DDTHH:MM:SS' แยกได้
                    if (typeof data.published_date === 'string') {
                        if (data.published_date.includes('T')) {
                            pd = data.published_date.split('T')[0];
                        } else if (/^\d{4}-\d{2}-\d{2}$/.test(data.published_date)) {
                            pd = data.published_date; // already date string
                        } else {
                            // พยายาม parse เป็น Date แล้ว format
                            let dObj = new Date(data.published_date);
                            if (!isNaN(dObj.getTime())) {
                                pd = dObj.toISOString().split('T')[0];
                            }
                        }
                    }
                }
                $('#published_date').val(pd);
                $('#is_active').prop('checked', data.is_active);
                // Images preview (multiple) with individual delete
                let imgHtml = '';
                if (Array.isArray(data.image_path) && data.image_path.length) {
                    data.image_path.forEach(function(p, index){
                        imgHtml += `
                            <div class="relative group inline-block mr-2 mb-2">
                                <img src="${p}" class="h-20 w-20 object-cover rounded-md border border-gray-200 dark:border-gray-700" loading="lazy">
                                <button type="button" class="remove-old-image absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] shadow-sm opacity-0 group-hover:opacity-100 transition-opacity" data-path="${p}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <input type="hidden" name="existing_images[]" value="${p}">
                            </div>`;
                    });
                } else {
                    imgHtml = '<span class="text-sm text-gray-500">ไม่มีภาพ</span>';
                }
                $('#images_preview').html(imgHtml);

                // Files preview (multiple) with individual delete
                let fileHtml = '';
                if (Array.isArray(data.file_news) && data.file_news.length) {
                    data.file_news.forEach(function(f){
                        const name = f.split('/').pop();
                        fileHtml += `
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md group">
                                <a href="/${f}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:text-blue-600 underline truncate flex-grow min-w-0 mr-2 text-xs" title="${name}">${name}</a>
                                <button type="button" class="remove-old-file text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity" data-path="${f}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                <input type="hidden" name="existing_files[]" value="${f}">
                            </div>`;
                    });
                } else {
                    fileHtml = '<span class="text-sm text-gray-500">ไม่มีไฟล์แนบ</span>';
                }
                $('#file_news_preview').html(fileHtml);
                $('#link_news').val(data.link_news);
                $('#newsModal').removeClass('hidden');
            });
        });

        // Handle removing existing images/files in UI
        $('body').on('click', '.remove-old-image, .remove-old-file', function() {
            const path = $(this).data('path');
            const isImage = $(this).hasClass('remove-old-image');
            const inputName = isImage ? 'deleted_images[]' : 'deleted_files[]';
            
            // Add hidden input to track deletion
            $('#newsForm').append(`<input type="hidden" name="${inputName}" value="${path}">`);
            
            // Remove from UI
            $(this).closest(isImage ? '.relative' : '.flex').fadeOut(300, function() {
                $(this).remove();
                
                // Show "none" message if empty
                if (isImage && $('#images_preview').children().length === 0) {
                    $('#images_preview').html('<span class="text-sm text-gray-500">ไม่มีภาพ</span>');
                } else if (!isImage && $('#file_news_preview').children().length === 0) {
                    $('#file_news_preview').html('<span class="text-sm text-gray-500">ไม่มีไฟล์แนบ</span>');
                }
            });
        });

        // Submit Form
        $('#newsForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var newsId = $('#news_id').val();
            var url = newsId ? '/news/' + newsId : '/news';
            
            // Show loading state
            const $btn = $('#saveBtn');
            const $spinner = $('#btn-spinner');
            $btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
            $spinner.removeClass('hidden');

            if(newsId) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST', // Always POST when using FormData with _method: PUT
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#newsModal').addClass('hidden');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: 'ข้อมูลข่าวสารถูกบันทึกเรียบร้อยแล้ว',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    // Reset loading state
                    $btn.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed');
                    $spinner.addClass('hidden');

                    let errorMsg = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                    
                    if (xhr.status === 422) { // Validation error
                        const errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถบันทึกได้',
                            html: errorMsg
                        });
                    } else {
                        alert(errorMsg.replace(/<br>/g, '\n'));
                    }
                }
            });
        });

        // Delete News with SweetAlert2 confirmation
        $('body').on('click', '.delete-btn', function() {
            var newsId = $(this).data('id');

            function doDelete() {
                $.ajax({
                    url: '/news/' + newsId,
                    type: 'DELETE',
                    success: function(result) {
                        $('#news-' + newsId).remove();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'รายการถูกลบเรียบร้อยแล้ว',
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr){
                        let msg = 'เกิดข้อผิดพลาดในการลบข้อมูล';
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res && res.message) msg = res.message;
                        } catch(e) {}
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'ลบไม่สำเร็จ', text: msg });
                        } else {
                            alert(msg);
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการลบ',
                    text: 'คุณแน่ใจหรือว่าต้องการลบข่าวสารนี้?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        doDelete();
                    }
                });
            } else {
                if (confirm('คุณแน่ใจหรือว่าต้องการลบข่าวสารนี้?')) {
                    doDelete();
                }
            }
        });

        // Analytics events
        $('#news_analytics_select').on('change', function() {
            window.loadNewsAnalyticsData();
        });

        $('.news-analytics-range-btn').on('click', function() {
            $('.news-analytics-range-btn').removeClass('bg-blue-600 text-white').addClass('bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200');
            $(this).removeClass('bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200').addClass('bg-blue-600 text-white');
            currentNewsAnalyticsDays = $(this).data('days');
            window.loadNewsAnalyticsData();
        });

        // Tab switches between Daily and Hourly chart views
        $('#tabNewsChartDaily').on('click', function() {
            currentNewsChartMode = 'daily';
            $('#tabNewsChartDaily').removeClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white').addClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900');
            $('#tabNewsChartHourly').removeClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900').addClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white');
            if (currentNewsLogs.length || currentNewsHourlyLogs.length) {
                window.renderNewsDailyChart(currentNewsLogs);
            }
        });

        $('#tabNewsChartHourly').on('click', function() {
            currentNewsChartMode = 'hourly';
            $('#tabNewsChartHourly').removeClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white').addClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900');
            $('#tabNewsChartDaily').removeClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900').addClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white');
            if (currentNewsHourlyLogs.length || currentNewsLogs.length) {
                window.renderNewsHourlyChart(currentNewsHourlyLogs);
            }
        });

        // Background polling every 10s to keep main page overview and table statistics updated in real-time
        setInterval(() => {
            const isModalOpen = $('#newsAnalyticsModal').is(':visible');
            if (!isModalOpen) {
                window.loadNewsAnalyticsData(true);
            }
        }, 10000);
    });
</script>
@endsection