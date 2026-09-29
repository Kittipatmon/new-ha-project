@extends('layouts.recruitment.app')

@section('title', 'Recruitment Dashboard & Analytics')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pt-3 pb-12">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-600 via-red-500 to-rose-600 flex items-center justify-center text-white shadow-lg shadow-red-500/25 shrink-0">
                    <i class="fa-solid fa-chart-pie text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Recruitment Data Platform</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800">
                            v2.5 Enterprise
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        ระบบสารสนเทศและการวิเคราะห์การสรรหาบุคลากร · ติดตามสถานะ (Monitor) · วิเคราะห์ (Analysis) · วางแผนคาดการณ์ (Forecast)
                    </p>
                </div>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <button type="button" onclick="showHrIntelligenceModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 hover:from-purple-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl transition-all shadow-md shadow-purple-500/20 font-semibold text-xs active:scale-[0.98] cursor-pointer">
                    <i class="fa-solid fa-brain text-amber-300"></i> 10 ดัชนีกลยุทธ์สรรหา (HR Intelligence)
                </button>
                <button type="button" onclick="showArchitectureModal()" class="inline-flex items-center gap-2 bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-700 hover:to-slate-800 text-white px-4 py-2.5 rounded-xl transition-all shadow-md shadow-slate-900/15 font-semibold text-xs active:scale-[0.98] cursor-pointer border border-slate-700">
                    <i class="fa-solid fa-sitemap text-amber-400"></i> ผังโครงสร้างข้อมูล (Data Flow)
                </button>
                <button type="button" onclick="showRecruitmentAnalyticsModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl transition-all shadow-md shadow-blue-500/20 font-semibold text-xs active:scale-[0.98] cursor-pointer">
                    <i class="fa-solid fa-chart-line"></i> สถิติการเข้าชมประกาศ
                </button>
                <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-2 bg-slate-100 dark:bg-[#2A2E39] hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-3.5 py-2.5 rounded-xl transition-all font-semibold text-xs shadow-sm active:scale-[0.98]">
                    <i class="fa-solid fa-bell text-red-500"></i>
                </a>
            </div>
        </div>

        <!-- 3-Pillar Navigation Tabs (Vuexy Executive Style) -->
        <div class="bg-white dark:bg-[#1E2129] p-2.5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-white/5 flex flex-col xl:flex-row xl:items-center justify-between gap-3">
            <!-- Segmented Control Bar -->
            <div class="inline-flex items-center p-1.5 bg-slate-100/90 dark:bg-slate-900/60 rounded-xl gap-1.5 overflow-x-auto w-full xl:w-auto border border-slate-200/50 dark:border-white/5" role="tablist">
                <button type="button" onclick="switchDashboardTab('monitor')" id="tab-btn-monitor" class="dash-tab-btn flex-1 sm:flex-none inline-flex items-center justify-center gap-2.5 px-4 sm:px-5 py-2.5 rounded-lg text-xs font-bold transition-all duration-200 bg-white dark:bg-[#252936] text-rose-600 dark:text-rose-400 shadow-sm border border-slate-200/70 dark:border-white/10 cursor-pointer select-none">
                    <i id="tab-icon-monitor" class="fa-solid fa-desktop text-xs text-rose-500"></i>
                    <span class="whitespace-nowrap">1. Dashboard (Monitor)</span>
                    <span id="tab-badge-monitor" class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 border border-rose-200/70 dark:border-rose-900/50">เรียลไทม์</span>
                </button>
                <button type="button" onclick="switchDashboardTab('analysis')" id="tab-btn-analysis" class="dash-tab-btn flex-1 sm:flex-none inline-flex items-center justify-center gap-2.5 px-4 sm:px-5 py-2.5 rounded-lg text-xs font-medium transition-all duration-200 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-white/5 border border-transparent cursor-pointer select-none">
                    <i id="tab-icon-analysis" class="fa-solid fa-magnifying-glass-chart text-xs text-slate-400"></i>
                    <span class="whitespace-nowrap">2. Analysis (Improve)</span>
                    <span id="tab-badge-analysis" class="px-2 py-0.5 text-[10px] font-semibold rounded-md bg-slate-200/70 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-transparent">Funnel</span>
                </button>
                <button type="button" onclick="switchDashboardTab('forecast')" id="tab-btn-forecast" class="dash-tab-btn flex-1 sm:flex-none inline-flex items-center justify-center gap-2.5 px-4 sm:px-5 py-2.5 rounded-lg text-xs font-medium transition-all duration-200 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-white/5 border border-transparent cursor-pointer select-none">
                    <i id="tab-icon-forecast" class="fa-solid fa-arrow-trend-up text-xs text-slate-400"></i>
                    <span class="whitespace-nowrap">3. Forecast (Planning)</span>
                    <span id="tab-badge-forecast" class="px-2 py-0.5 text-[10px] font-semibold rounded-md bg-slate-200/70 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-transparent">พยากรณ์ AI</span>
                </button>
            </div>

            <!-- Context Info Capsule -->
            <div id="tab-context-info" class="flex items-center gap-3 px-3.5 py-2 rounded-xl bg-slate-50/90 dark:bg-slate-800/40 border border-slate-200/60 dark:border-white/5 text-xs self-stretch xl:self-auto min-w-0">
                <div class="relative flex h-2.5 w-2.5 shrink-0">
                    <span id="tab-context-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span id="tab-context-dot" class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </div>
                <div class="flex items-center gap-2 min-w-0 overflow-hidden">
                    <span id="tab-context-badge" class="font-bold text-slate-800 dark:text-slate-200 shrink-0 text-xs">Real-time Monitor</span>
                    <span class="text-slate-300 dark:text-slate-600 shrink-0">|</span>
                    <span id="tab-context-text" class="text-slate-500 dark:text-slate-400 truncate text-[11px] sm:text-xs">ติดตามสถานะภาพรวมและปฏิบัติการรับสมัครงานรายวัน</span>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: MONITOR (DASHBOARD) - Real-time Operations -->
        <!-- ========================================================================= -->
        <div id="tab-content-monitor" class="dash-tab-pane space-y-6">
            <!-- Stats KPI Grid (Vuexy Elevation Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- 1. Pending Requests -->
                <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl shadow-sm hover:shadow-md border border-slate-200/70 dark:border-white/5 transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-900/50">
                            รอการพิจารณา
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">คำขอรออนุมัติ</p>
                    <div class="flex items-baseline justify-between mt-1">
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">{{ number_format($stats['pending_requests']) }}</p>
                        <span class="text-[11px] text-slate-400">จากทั้งหมด {{ number_format($stats['pending_requests'] + $stats['approved_requests']) }}</span>
                    </div>
                </div>

                <!-- 2. Active Posts -->
                <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl shadow-sm hover:shadow-md border border-slate-200/70 dark:border-white/5 transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-emerald-500/10 text-emerald-500 rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-900/50">
                            เปิดรับอยู่
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ประกาศที่เปิดรับ</p>
                    <div class="flex items-baseline justify-between mt-1">
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">{{ number_format($stats['active_posts']) }}</p>
                        <span class="text-[11px] text-emerald-600 font-medium"><i class="fa-solid fa-check"></i> รับสมัคร</span>
                    </div>
                </div>

                <!-- 3. Views & Clicks -->
                <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl shadow-sm hover:shadow-md border border-slate-200/70 dark:border-white/5 transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-sky-500/10 text-sky-500 rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border border-sky-200/60 dark:border-sky-900/50">
                            Engagement
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ยอดเข้าชมประกาศ</p>
                    <div class="flex items-baseline justify-between mt-1">
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">{{ number_format($stats['total_views']) }}</p>
                        <span class="text-[11px] text-slate-400">คลิก {{ number_format($stats['total_clicks']) }} ครั้ง</span>
                    </div>
                </div>

                <!-- 4. Total Applications -->
                <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl shadow-sm hover:shadow-md border border-slate-200/70 dark:border-white/5 transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-indigo-500/10 text-indigo-500 rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-900/50">
                            ผู้สมัครรวม
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ผู้สมัครทั้งหมด</p>
                    <div class="flex items-baseline justify-between mt-1">
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">{{ number_format($stats['total_applications']) }}</p>
                        <span class="text-[11px] text-indigo-600 font-semibold">บรรจุแล้ว {{ $stats['hired_count'] }} คน</span>
                    </div>
                </div>

                <!-- 5. New Applications -->
                <a href="{{ route('backend.recruitment.applications.index', ['status' => 'submitted']) }}" class="bg-gradient-to-br from-red-600 to-rose-700 text-white p-5 rounded-2xl shadow-md shadow-red-500/20 hover:shadow-lg hover:shadow-red-500/30 transition-all relative overflow-hidden group block">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 bg-white/20 text-white rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-fire"></i>
                        </div>
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-white/20 text-white">
                            Action Req.
                        </span>
                    </div>
                    <p class="text-xs font-bold text-red-100 uppercase tracking-wider">ผู้สมัครมาใหม่</p>
                    <div class="flex items-baseline justify-between mt-1">
                        <p class="text-2xl lg:text-3xl font-black text-white">{{ number_format($stats['new_applications']) }}</p>
                        <span class="text-xs text-red-200 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            คลิกตรวจ <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </a>
            </div>

            <!-- Recruitment Stage Quick Tracker -->
            <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white">สถานะผู้สมัครตามกระบวนการคัดเลือก (Live Pipeline Status)</h3>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">รวมผู้สมัครในระบบ {{ $stats['total_applications'] }} ราย</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <div class="bg-slate-50 dark:bg-black/20 p-3 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">1. รอคัดกรองเบื้องต้น</span>
                        <span class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $stats['new_applications'] }}</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">คน</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-black/20 p-3 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">2. รอแผนกพิจารณา</span>
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400">{{ max(0, $stats['screening_count'] - $stats['new_applications']) }}</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">คน</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-black/20 p-3 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">3. อยู่ระหว่างสัมภาษณ์</span>
                        <span class="text-xl font-black text-purple-600 dark:text-purple-400">{{ $stats['interview_scheduled'] }}</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">คน</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-black/20 p-3 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">4. ผ่านคัดเลือก / เสนองาน</span>
                        <span class="text-xl font-black text-teal-600 dark:text-teal-400">{{ $stats['offered_count'] }}</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">คน</span>
                    </div>
                    <div class="bg-slate-50 dark:bg-black/20 p-3 rounded-xl border border-slate-100 dark:border-slate-800 text-center col-span-2 sm:col-span-1">
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 block mb-1">5. บรรจุเข้าทำงาน (Hired)</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['hired_count'] }}</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">คน</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section (Vuexy Grid: Bar Chart + Donut with Side Legend) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart 1: Bar Chart (Views vs Applicants) -->
                <div class="lg:col-span-2 bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                                    <i class="fa-solid fa-chart-column text-red-600"></i>
                                    <span>สถิติยอดกดดูและจำนวนผู้สมัครแยกตามตำแหน่งงาน</span>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    เปรียบเทียบยอดเข้าชมประกาศ (ครั้ง) กับจำนวนผู้ส่งใบสมัคร (คน)
                                </p>
                            </div>
                            <span class="px-3 py-1 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg border border-red-200/60 dark:border-red-900/50">
                                Top Job Posts
                            </span>
                        </div>
                        <div id="position-interest-chart" class="w-full min-h-[320px]"></div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs text-slate-400">
                        <span><i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> คลิกที่แท่งกราฟเพื่อดูรายชื่อผู้สมัครของตำแหน่งนั้น</span>
                        <a href="{{ route('backend.recruitment.applications.index') }}" class="text-red-600 hover:underline font-semibold">ดูใบสมัครทั้งหมด &rarr;</a>
                    </div>
                </div>

                <!-- Chart 2: Donut Chart with Vuexy Side Legend -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                                    <i class="fa-solid fa-chart-pie text-cyan-500"></i>
                                    <span>สัดส่วนความสนใจตามแผนก</span>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">สัดส่วนผู้สมัครในแต่ละกลุ่มสายงาน</p>
                            </div>
                        </div>

                        <!-- Donut Graphic -->
                        <div id="department-interest-chart" class="w-full flex items-center justify-center min-h-[220px]"></div>

                        <!-- Vuexy Style Legend List with Trends -->
                        <div class="mt-4 space-y-2.5">
                            @php
                                $deptColors = ['#dc2626', '#0284c7', '#10b981', '#f59e0b', '#8b5cf6'];
                            @endphp
                            @foreach ($chartData['departments']['items'] as $index => $item)
                                @php $c = $deptColors[$index % count($deptColors)]; @endphp
                                <div class="flex items-center justify-between text-xs p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-black/20 transition-colors">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $c }}"></span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $item['name'] }}">
                                            {{ $item['name'] }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="font-bold text-slate-800 dark:text-white">{{ $item['percent'] }}%</span>
                                        <span class="text-[11px] font-bold px-1.5 py-0.5 rounded {{ str_contains($item['trend'], '+') ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                            {{ $item['trend'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 text-center">
                        <span class="text-xs text-slate-400 font-medium">รวมผู้สมัครทั้งหมด {{ number_format($stats['total_applications']) }} ราย</span>
                    </div>
                </div>
            </div>

            <!-- Recent Applications & Requests (2-column layout) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Applications -->
                <div class="bg-white dark:bg-[#1E2129] rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-black/20">
                        <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-user-clock text-red-500"></i> ผู้สมัครล่าสุด
                        </h3>
                        <a href="{{ route('backend.recruitment.applications.index') }}"
                            class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/60 px-3 py-1.5 rounded-lg transition-colors">
                            ดูทั้งหมด <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto flex-1 p-3">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                    <th class="pb-2.5 px-3 font-semibold">ชื่อผู้สมัคร / ตำแหน่ง</th>
                                    <th class="pb-2.5 px-3 font-semibold text-center">เวลา</th>
                                    <th class="pb-2.5 px-3 font-semibold text-right">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                                @forelse($recent_applications as $app)
                                    <tr onclick="window.location='{{ route('backend.recruitment.applications.show', $app->id) }}'" 
                                        class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group cursor-pointer" title="คลิกเพื่อดูรายละเอียดผู้สมัคร">
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-slate-500 dark:text-gray-400 font-bold border border-slate-200 dark:border-gray-700 shrink-0">
                                                    <i class="fa-solid fa-user text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-xs text-slate-800 dark:text-gray-200 group-hover:text-red-600 transition-colors">
                                                        {{ $app->applicant?->full_name ?? 'ไม่มีชื่อ' }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-500 dark:text-gray-400 truncate max-w-[180px]">
                                                        {{ $app->jobPost?->title ?? 'ไม่มีข้อมูลตำแหน่ง' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                                {{ $app->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border shadow-2xs {{ $app->status_badge_class }}">
                                                <i class="{{ $app->status_icon }} text-[10px]"></i>
                                                <span>{{ $app->status_label }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-slate-400 text-xs">
                                            ยังไม่มีผู้สมัครล่าสุด
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Requests -->
                <div class="bg-white dark:bg-[#1E2129] rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-black/20">
                        <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-file-signature text-slate-500"></i> คำขอเปิดรับสมัครงาน
                        </h3>
                        <a href="{{ route('backend.recruitment.requests.index') }}"
                            class="text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 px-3 py-1.5 rounded-lg transition-colors">
                            ดูทั้งหมด <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto flex-1 p-3">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                    <th class="pb-2.5 px-3 font-semibold">ตำแหน่ง / ฝ่าย</th>
                                    <th class="pb-2.5 px-3 font-semibold text-center">จำนวน</th>
                                    <th class="pb-2.5 px-3 font-semibold text-right">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                                @forelse($recent_requests as $req)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-3 py-3">
                                            <p class="font-bold text-xs text-slate-800 dark:text-gray-200">
                                                {{ $req->position_name ?: ($req->jobPosition?->position_name ?? 'ระบุในฟอร์ม') }}
                                            </p>
                                            <p class="text-[11px] text-slate-500 dark:text-gray-400 truncate max-w-[180px]">
                                                {{ $req->department?->department_name ?: ($req->department?->department_fullname ?? 'สำนักงานใหญ่') }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-3 text-center text-xs font-bold text-slate-700 dark:text-slate-300">
                                            {{ $req->headcount ?? 1 }} อัตรา
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $req->status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                                {{ $req->status_label ?? $req->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-slate-400 text-xs">
                                            ไม่มีคำขอเปิดรับสมัครล่าสุด
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: ANALYSIS (IMPROVE) - Conversion Funnel & Bottlenecks -->
        <!-- ========================================================================= -->
        <div id="tab-content-analysis" class="dash-tab-pane space-y-6 hidden">
            <!-- Funnel Overview Strip -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-filter text-purple-600"></i>
                            <span>Recruitment Conversion Funnel (ขั้นตอนการคัดเลือกและอัตราการแปลงผล)</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            วิเคราะห์อัตราการหลุดร่วง (Drop-off Rate) ในแต่ละช่วงของกระบวนการสรรหา
                        </p>
                    </div>
                    <span class="px-3 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 text-xs font-bold rounded-lg border border-purple-200/60 dark:border-purple-900/50">
                        Funnel Efficiency
                    </span>
                </div>

                <!-- Funnel Steps Visualizer -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @foreach ($chartData['funnel'] as $index => $step)
                        <div class="relative p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-black/20 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold" style="background-color: {{ $step['color'] }}">
                                        <i class="fa-solid {{ $step['icon'] }}"></i>
                                    </span>
                                    <span class="text-xs font-black text-slate-700 dark:text-slate-300">
                                        {{ $step['rate'] }}%
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 mb-1 leading-snug">
                                    {{ $step['step'] }}
                                </h4>
                            </div>
                            <div class="mt-4 pt-2 border-t border-slate-200/60 dark:border-slate-700 flex items-baseline justify-between">
                                <span class="text-xs text-slate-400">ปริมาณ</span>
                                <span class="text-lg font-black" style="color: {{ $step['color'] }}">
                                    {{ number_format($step['count']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Analysis Charts (Horizontal Bar: Position Performance + Time-to-Hire) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Vuexy Style Horizontal Bar: Balance/Top Positions Performance -->
                <div class="lg:col-span-2 bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                                <i class="fa-solid fa-bars-staggered text-cyan-500"></i>
                                <span>ประสิทธิภาพการคัดเลือกแยกตามตำแหน่งงาน (Position Hiring Funnel)</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                เปรียบเทียบผู้สมัคร สัมภาษณ์ และบรรจุงานในแต่ละตำแหน่งงาน
                            </p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-300 rounded-lg">
                            Conversion
                        </span>
                    </div>

                    <div id="position-performance-chart" class="w-full min-h-[320px]"></div>
                </div>

                <!-- Time-to-Hire & Cycle Time Metric Cards -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                                <i class="fa-solid fa-stopwatch text-amber-500"></i>
                                <span>ระยะเวลาการสรรหา (Time-to-Hire)</span>
                            </h3>
                        </div>

                        <!-- Main Big Metric -->
                        <div class="p-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/20 border border-amber-200/60 dark:border-amber-900/50 mb-4 text-center">
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-300 block mb-1">ระยะเวลาเฉลี่ยจนบรรจุงาน (Average Time to Hire)</span>
                            <span class="text-3xl font-black text-amber-900 dark:text-amber-100">{{ $chartData['timeMetrics']['avg_time_to_hire'] }}</span>
                            <span class="text-sm font-semibold text-amber-700 dark:text-amber-300 ml-1">วัน</span>
                            <div class="mt-2 text-[11px] text-amber-700/80 dark:text-amber-300/80">
                                @if($chartData['timeMetrics']['avg_time_to_hire'] <= 30)
                                    🎯 เร็วกว่าเป้าหมาย SLA (30 วัน) อยู่ <strong>{{ round(30 - $chartData['timeMetrics']['avg_time_to_hire'], 1) }} วัน</strong>
                                @else
                                    ⚠️ ใช้เวลามากกว่า SLA (30 วัน) อยู่ <strong>{{ round($chartData['timeMetrics']['avg_time_to_hire'] - 30, 1) }} วัน</strong>
                                @endif
                            </div>
                        </div>

                        <!-- Sub Cycles -->
                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 dark:bg-black/20">
                                <span class="text-slate-600 dark:text-slate-300 font-medium">1. คัดกรองใบสมัคร (Screening):</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $chartData['timeMetrics']['avg_screening_days'] }} วัน</span>
                            </div>
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 dark:bg-black/20">
                                <span class="text-slate-600 dark:text-slate-300 font-medium">2. รอบสัมภาษณ์ (Interview Cycle):</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $chartData['timeMetrics']['avg_interview_cycle_days'] }} วัน</span>
                            </div>
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 dark:bg-black/20">
                                <span class="text-slate-600 dark:text-slate-300 font-medium">3. ยื่นข้อเสนอถึงวันเริ่มงาน (Offer & Onboarding):</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $chartData['timeMetrics']['avg_offer_acceptance_days'] }} วัน</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs text-slate-400">
                        <span>ระยะเวลาสรรหาเร็วที่สุด:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $chartData['timeMetrics']['fastest_hire_days'] }} วัน</span>
                    </div>
                </div>
            </div>

            <!-- Recruitment Source & Channels Breakdown -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-network-wired text-blue-500"></i>
                            <span>แหล่งที่มาของผู้สมัครที่มีประสิทธิภาพ (Candidate Sourcing Channels)</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">ช่องทางการประชาสัมพันธ์และอัตราส่วนของผู้สมัครที่ได้คุณภาพจากฐานข้อมูลจริง</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    @foreach ($chartData['channels'] as $ch)
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-black/20">
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $ch['color'] }}"></span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $ch['percent'] }}%</span>
                            </div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate" title="{{ $ch['name'] }}">{{ $ch['name'] }}</h4>
                            <div class="mt-3 flex items-baseline justify-between">
                                <span class="text-[11px] text-slate-400">จำนวนผู้สมัคร</span>
                                <span class="text-lg font-black text-slate-800 dark:text-white">{{ $ch['count'] }} คน</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $ch['percent'] }}%; background-color: {{ $ch['color'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 10 HR Intelligence: Deep-dive Analysis (Questions 1, 2, 4, 8, 9) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 1. ตำแหน่งที่หาคนยากที่สุด (Hardest-to-Fill Positions) -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-sm font-bold">1</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">ตำแหน่งที่หาคนยากที่สุด (Hardest-to-Fill)</h4>
                                    <p class="text-[11px] text-slate-400">วัดจากอัตราการบรรจุสำเร็จ (Pass & Conversion Rate)</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-red-500 bg-red-50 dark:bg-red-950/40 px-2 py-0.5 rounded-full border border-red-200 dark:border-red-900/50">
                                Talent Scarcity
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($chartData['hrIntelligence']['hardest_positions'] as $pos)
                                <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-black/20 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-800 dark:text-slate-100 truncate">{{ $pos['position'] }}</span>
                                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded {{ $pos['color'] === 'red' ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : ($pos['color'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                                {{ $pos['difficulty'] }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $pos['reason'] }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-black {{ $pos['color'] === 'red' ? 'text-red-600 dark:text-red-400' : 'text-emerald-600' }}">{{ $pos['pass_rate'] }}%</span>
                                        <span class="block text-[10px] text-slate-400">รับ {{ $pos['hired'] }}/{{ $pos['applicants'] }} คน</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 2. แผนกไหนใช้เวลารับคนนาน (Department Hiring Speed) -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center text-sm font-bold">2</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">แผนกที่ใช้เวลารับคนนานที่สุด (Department Time-to-Hire)</h4>
                                    <p class="text-[11px] text-slate-400">ระยะเวลาเฉลี่ยจากวันยื่นคำขอจนถึงคัดเลือกเสร็จสิ้น</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-blue-500 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-900/50">
                                Hiring SLA
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($chartData['hrIntelligence']['dept_hiring_speed'] as $dSpeed)
                                <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-black/20 flex items-center justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-800 dark:text-slate-100">{{ $dSpeed['department'] }}</span>
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $dSpeed['color'] === 'amber' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700' }}">
                                                {{ $dSpeed['sla_status'] }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">รวม {{ $dSpeed['requests_count'] }} คำขอ (ต้องการ {{ $dSpeed['headcount'] }} อัตรา | รออนุมัติ {{ $dSpeed['pending_requests'] }})</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-black text-slate-800 dark:text-white">{{ $dSpeed['avg_days_to_hire'] }} วัน</span>
                                        <span class="block text-[10px] text-slate-400">เฉลี่ยต่อคำขอ</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. สาเหตุที่ผู้สมัครถอนตัว/ตกคัดกรอง & 8. ประมาณการค่าใช้จ่ายจ้างงาน (Cost-Per-Hire) & 9. Lead Time -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Drop-out & Rejection Reasons -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-500 flex items-center justify-center text-sm font-bold">4</span>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm">สาเหตุที่ผู้สมัครถอนตัวและไม่ผ่านมากที่สุด (Drop-out Root Cause)</h4>
                                <p class="text-[11px] text-slate-400">ผลการวิเคราะห์จากบันทึกสถานะและการประเมินรอบคัดกรอง</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($chartData['hrIntelligence']['rejection_reasons'] as $rReason)
                            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-black/20">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $rReason['reason'] }}</span>
                                    <span class="text-xs font-black text-purple-600 dark:text-purple-400">{{ $rReason['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden mb-1.5">
                                    <div class="bg-purple-600 h-full rounded-full" style="width: {{ $rReason['percentage'] }}%"></div>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-lightbulb text-amber-500 text-[10px]"></i>
                                    <span>แนวทางแก้ไข: {{ $rReason['solution'] }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 8. ค่าใช้จ่ายต่อการจ้าง 1 คน (Cost-Per-Hire) & 9. Lead Time Stages -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-sm font-bold">8</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">ค่าใช้จ่ายต่อการจ้าง 1 คน (Cost-per-Hire) & Lead Time</h4>
                                    <p class="text-[11px] text-slate-400">สูตรมาตรฐาน SHRM + ระยะเวลาในแต่ละช่วง</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-900/50">
                                SHRM Standard
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40">
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">ตำแหน่งทั่วไป</span>
                                <span class="text-base font-black text-emerald-700 dark:text-emerald-300">฿{{ number_format($chartData['hrIntelligence']['cost_per_hire']['avg_cost_general']) }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">รวมประกาศและตรวจสุขภาพ</span>
                            </div>
                            <div class="p-3 rounded-xl bg-purple-50/60 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/40">
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">สายเฉพาะทาง / IT</span>
                                <span class="text-base font-black text-purple-700 dark:text-purple-300">฿{{ number_format($chartData['hrIntelligence']['cost_per_hire']['avg_cost_specialist']) }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">รวมต้นทุนเวลาสัมภาษณ์</span>
                            </div>
                        </div>

                        <!-- 9. Lead Time Stages Timeline -->
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-timeline text-blue-500"></i>
                                <span>9. ระยะเวลาแต่ละช่วงตั้งแต่ขออัตรากำลังจนเริ่มงาน (Requisition to Start):</span>
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                                @foreach ($chartData['hrIntelligence']['lead_time_stages'] as $stg)
                                    <div class="p-2 rounded-lg bg-slate-50 dark:bg-black/20 border border-slate-100 dark:border-slate-800">
                                        <span class="block text-[10px] text-slate-400 truncate">{{ $stg['stage'] }}</span>
                                        <span class="font-extrabold text-slate-800 dark:text-white">{{ $stg['avg_days'] }} วัน</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 3: FORECAST (PLANNING) - Predictive Wave & Manpower Fulfillment -->
        <!-- ========================================================================= -->
        <div id="tab-content-forecast" class="dash-tab-pane space-y-6 hidden">
            <!-- Smooth Multi-Layer Area Wave Chart (Vuexy Data Science style) -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-wave-square text-cyan-500"></i>
                            <span>สถิติแนวโน้มและการคาดการณ์ผู้สมัครล่วงหน้า (Predictive Application Wave)</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            ข้อมูลจริงย้อนหลังจากฐานข้อมูล + แบบจำลองพยากรณ์ปริมาณผู้สมัครและอัตราการบรรจุใน 3 เดือนข้างหน้า
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 text-xs font-bold rounded-lg border border-cyan-200/60 dark:border-cyan-900/50">
                            Forecast Model: Moving Trend
                        </span>
                    </div>
                </div>

                <div id="forecast-wave-chart" class="w-full min-h-[340px]"></div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-black/20">
                        <div class="w-9 h-9 rounded-lg bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">ผู้สมัครเฉลี่ยจริง</span>
                            <span class="font-bold text-slate-800 dark:text-white">เฉลี่ย {{ round($stats['total_applications'] / max(1, count(array_filter($chartData['forecast']['actual_apps']))), 1) }} คน / เดือน</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-black/20">
                        <div class="w-9 h-9 rounded-lg bg-purple-500/10 text-purple-500 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">เข้ารอบสัมภาษณ์จริง</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $stats['interview_scheduled'] }} คน (จาก {{ $stats['total_applications'] }} ราย)</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-black/20">
                        <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">อัตราการบรรจุจริง (Hired Rate)</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $chartData['funnel'][4]['rate'] }}% (บรรจุแล้ว {{ $stats['hired_count'] }} อัตรา)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manpower Demand vs Supply Fulfillment Table -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-users-gear text-red-600"></i>
                            <span>การจัดสรรและเติมเต็มอัตรากำลังตามแผนก (Manpower Fulfillment Matrix)</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            ติดตามความคืบหน้าการรับคนตามคำขอจริงเทียบกับเป้าหมายที่แผนกต้องการ
                        </p>
                    </div>
                    <a href="{{ route('backend.recruitment.requests.index') }}" class="text-xs font-bold text-red-600 hover:underline">
                        จัดการคำขอรับสมัคร &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                <th class="pb-3 px-4 font-semibold">ฝ่าย / กลุ่มสายงาน</th>
                                <th class="pb-3 px-4 font-semibold text-center">ต้องการ (คน)</th>
                                <th class="pb-3 px-4 font-semibold text-center">บรรจุแล้ว (คน)</th>
                                <th class="pb-3 px-4 font-semibold text-center">อยู่ระหว่างสรรหา</th>
                                <th class="pb-3 px-4 font-semibold text-center">ความคืบหน้า (Fulfillment %)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($chartData['manpower'] as $mp)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3.5 font-bold text-xs text-slate-800 dark:text-slate-200">
                                        {{ $mp['dept'] }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-bold text-slate-700 dark:text-slate-300">
                                        {{ $mp['needed'] }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $mp['hired'] }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-medium text-slate-500 dark:text-slate-400">
                                        {{ $mp['in_progress'] }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3 justify-center">
                                            <div class="w-32 bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $mp['rate'] >= 80 ? 'bg-emerald-500' : ($mp['rate'] >= 50 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width: {{ $mp['rate'] }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 w-10 text-right">{{ $mp['rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Strategic HR Insights Cards (Derived 100% from Real Database Metrics) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($chartData['strategicInsights'] as $ins)
                    @php
                        $borderClasses = match($ins['color']) {
                            'blue' => 'from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/10 border-blue-100 dark:border-blue-900/40 text-blue-950 dark:text-blue-200',
                            'emerald' => 'from-emerald-50 to-teal-50 dark:from-emerald-950/20 dark:to-teal-950/10 border-emerald-100 dark:border-emerald-900/40 text-emerald-950 dark:text-emerald-200',
                            default => 'from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/10 border-amber-100 dark:border-amber-900/40 text-amber-950 dark:text-amber-200',
                        };
                        $bgIcon = match($ins['color']) {
                            'blue' => 'bg-blue-600',
                            'emerald' => 'bg-emerald-600',
                            default => 'bg-amber-600',
                        };
                    @endphp
                    <div class="bg-gradient-to-br p-5 rounded-2xl border {{ $borderClasses }}">
                        <div class="w-10 h-10 rounded-xl {{ $bgIcon }} text-white flex items-center justify-center text-sm font-bold mb-3 shadow-md">
                            <i class="fa-solid {{ $ins['icon'] }}"></i>
                        </div>
                        <h4 class="font-bold text-xs mb-1.5">{{ $ins['title'] }}</h4>
                        <p class="text-xs opacity-90 leading-relaxed">
                            {{ $ins['desc'] }}
                        </p>
                    </div>
                @endforeach
            </div>
            <!-- 10 HR Intelligence: Planning & Forecasting (Questions 5, 6, 7, 10) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 5. ตำแหน่งที่ควรเปิดรับล่วงหน้า (Proactive Advance Recruitment) -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-sm font-bold">5</span>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm">ตำแหน่งที่ควรเปิดรับล่วงหน้า (Proactive Advance Hiring)</h4>
                                <p class="text-[11px] text-slate-400">คำนวณจาก Lead Time คัดเลือก + ระยะเวลารอเริ่มงาน (Notice Period)</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-red-500 bg-red-50 dark:bg-red-950/40 px-2 py-0.5 rounded-full border border-red-200 dark:border-red-900/50">
                            Lead Time > 45 วัน
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($chartData['hrIntelligence']['proactive_hiring'] as $ph)
                            <div class="p-3.5 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-black/20">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-100">{{ $ph['position'] }}</span>
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded {{ $ph['color'] === 'red' ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : ($ph['color'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ $ph['urgency'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span>สรรหา: {{ $ph['sourcing_days'] }} วัน · สัมภาษณ์: {{ $ph['interview_days'] }} วัน · รอเริ่มงาน: {{ $ph['notice_period_days'] }} วัน</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-200">รวม {{ $ph['lead_time_days'] }} วัน</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 6. ช่วงเดือนไหนควรเพิ่มงบสรรหา (Seasonal Budget Allocation Roadmap) -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-sm font-bold">6</span>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm">ช่วงเดือนที่ควรเพิ่มงบประมาณสรรหา (Seasonal Budget Roadmap)</h4>
                                <p class="text-[11px] text-slate-400">แผนจัดสรรงบประมาณรายไตรมาสสอดคล้องกับวงจรตลาดแรงงาน</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-cyan-600 bg-cyan-50 dark:bg-cyan-950/40 px-2 py-0.5 rounded-full border border-cyan-200 dark:border-cyan-900/50">
                            Q1-Q4 Strategy
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($chartData['hrIntelligence']['seasonal_budget'] as $sb)
                            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-black/20">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $sb['quarter'] }} - {{ $sb['focus'] }}</span>
                                    <span class="text-xs font-black text-cyan-600 dark:text-cyan-400">งบ {{ $sb['allocation_pct'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden mb-1.5">
                                    <div class="bg-cyan-600 h-full rounded-full" style="width: {{ $sb['allocation_pct'] }}%"></div>
                                </div>
                                <p class="text-[11px] text-slate-400">{{ $sb['action'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 7. สัญญาณเตือนตำแหน่งเสี่ยงขาดคน & 10. ติดตามผลทดลองงาน (Probation Tracking) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 7. ตำแหน่งที่มีแนวโน้มขาดคน (Headcount Shortage Risk Matrix) -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center text-sm font-bold">7</span>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm">ตำแหน่งที่มีแนวโน้มขาดคน (Headcount Shortage Risk)</h4>
                                <p class="text-[11px] text-slate-400">คำขออนุมัติแล้วที่ยังไม่ได้ประกาศรับสมัคร หรือยังไม่มี Candidate ในคิว</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-900/50">
                            Shortage Alert
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($chartData['hrIntelligence']['shortage_risk'] as $sr)
                            <div class="p-3.5 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-black/20 flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-800 dark:text-slate-100">{{ $sr['position'] }}</span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $sr['color'] === 'red' ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : ($sr['color'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                            {{ $sr['risk'] }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">{{ $sr['advice'] }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-black text-red-600 dark:text-red-400">ขาด {{ $sr['approved_unposted'] }} อัตรา</span>
                                    <span class="block text-[10px] text-slate-400">Pipeline: {{ $sr['candidate_pipeline'] }} คน</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 10. หลังรับเข้ามาแล้วผ่านทดลองงานหรือไม่ (Probation Evaluation & Quality of Hire) -->
                <div class="bg-white dark:bg-[#1E2129] p-6 rounded-2xl shadow-sm border border-slate-200/70 dark:border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800 mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-500 flex items-center justify-center text-sm font-bold">10</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-sm">การติดตามผลทดลองงาน (Probation & Quality of Hire)</h4>
                                    <p class="text-[11px] text-slate-400">ติดตามผลการทดลองงาน 119 วัน ของพนักงานที่รับเข้าทำงานใหม่</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-teal-600 bg-teal-50 dark:bg-teal-950/40 px-2 py-0.5 rounded-full border border-teal-200 dark:border-teal-900/50">
                                Auto-Linked
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="p-3 rounded-xl bg-teal-50/60 dark:bg-teal-950/20 border border-teal-100 dark:border-teal-900/40">
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">พนักงานในระบบทดลองงาน</span>
                                <span class="text-xl font-black text-teal-700 dark:text-teal-300">{{ $chartData['hrIntelligence']['probation_tracking']['total_in_probation'] }} คน</span>
                                <span class="text-[10px] text-emerald-600 block mt-0.5"><i class="fa-solid fa-check"></i> เชื่อมต่อฐานข้อมูลจริง</span>
                            </div>
                            <div class="p-3 rounded-xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40">
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">เกณฑ์การประเมิน</span>
                                <span class="text-xl font-black text-blue-700 dark:text-blue-300">119 วัน</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">แบ่ง 3 ระยะ (30, 60, 119 วัน)</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-black/20 border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300">
                            <p class="flex items-center gap-2 mb-1.5 font-bold text-slate-800 dark:text-white">
                                <i class="fa-solid fa-link text-teal-500"></i>
                                <span>สถานะการเชื่อมต่อวงจรการสรรหากับทดลองงาน (HR Lifecycle Integration)</span>
                            </p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">
                                {{ $chartData['hrIntelligence']['probation_tracking']['status_note'] }} ระบบจะสร้างแบบประเมินทดลองงานอัตโนมัติเมื่อกดรับผู้สมัครเข้าทำงาน (Status = Hired) เพื่อปิดวงจรการสรรหา
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: 10 STRATEGIC HR RECRUITMENT INTELLIGENCE -->
    <!-- ========================================================================= -->
    <div id="hrIntelligenceModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm hidden flex-col items-center justify-start pt-20 sm:pt-24 pb-8 px-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#1E2129] rounded-3xl max-w-5xl w-full shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden flex flex-col max-h-[calc(100vh-7.5rem)] my-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 dark:from-purple-950/30 dark:via-indigo-950/20 dark:to-blue-950/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-purple-500/20">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base">10 ดัชนีกลยุทธ์สรรหาบุคลากรเชิงลึก (Recruitment Intelligence Report)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">ตอบทุกคำถามเชิงบริหารด้วยข้อมูลจริงจากระบบเพื่อการตัดสินใจที่แม่นยำ</p>
                    </div>
                </div>
                <button type="button" onclick="closeHrIntelligenceModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body: 10 Questions Matrix (100% Real Database Binding) -->
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Q1 -->
                    @php $topHardest = $chartData['hrIntelligence']['hardest_positions'][0] ?? null; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400 flex items-center justify-center text-xs font-black">1</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">ตำแหน่งไหนหาคนยากที่สุด?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            {{ $topHardest ? $topHardest['position'] : 'ไม่มีข้อมูล' }} (Pass Rate {{ $topHardest ? $topHardest['pass_rate'] : 0 }}%)
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: ยื่นสมัคร {{ $topHardest ? $topHardest['applicants'] : 0 }} คน บรรจุงานได้ {{ $topHardest ? $topHardest['hired'] : 0 }} คน · ระดับความยาก: {{ $topHardest ? $topHardest['difficulty'] : '-' }}
                        </p>
                    </div>

                    <!-- Q2 -->
                    @php $topSlowDept = $chartData['hrIntelligence']['dept_hiring_speed'][0] ?? null; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-black">2</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">แผนกไหนใช้เวลารับคนนานที่สุด?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            {{ $topSlowDept ? $topSlowDept['department'] : 'ไม่มีข้อมูล' }} (เฉลี่ย {{ $topSlowDept ? $topSlowDept['avg_days_to_hire'] : 0 }} วัน)
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: คำขอรวม {{ $topSlowDept ? $topSlowDept['requests_count'] : 0 }} รายการ (ต้องการ {{ $topSlowDept ? $topSlowDept['headcount'] : 0 }} อัตรา · {{ $topSlowDept ? $topSlowDept['sla_status'] : '-' }})
                        </p>
                    </div>

                    <!-- Q3 -->
                    @php $topChannel = $chartData['hrIntelligence']['quality_channels'][0] ?? null; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-black">3</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">ช่องทางไหนได้ผู้สมัครคุณภาพสูง?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            {{ $topChannel ? $topChannel['channel'] : 'ไม่มีข้อมูล' }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: ผู้สมัคร {{ $topChannel ? $topChannel['applicants'] : 0 }} คน สัมภาษณ์ {{ $topChannel ? $topChannel['interviewed'] : 0 }} คน บรรจุสำเร็จ {{ $topChannel ? $topChannel['hired'] : 0 }} คน (Conversion {{ $topChannel ? $topChannel['conversion_rate'] : 0 }}%)
                        </p>
                    </div>

                    <!-- Q4 -->
                    @php $topRejection = $chartData['hrIntelligence']['rejection_reasons'][0] ?? null; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-black">4</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">สาเหตุที่ผู้สมัครถอนตัว / ไม่ผ่านมากที่สุด?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            {{ $topRejection ? $topRejection['reason'] : 'ยังไม่มีประวัติการปฏิเสธ' }} ({{ $topRejection ? $topRejection['percentage'] : 0 }}%)
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: มีในระบบ {{ $topRejection ? $topRejection['count'] : 0 }} คน · ผลกระทบ: {{ $topRejection ? $topRejection['impact'] : '-' }}
                        </p>
                    </div>

                    <!-- Q5 -->
                    @php $topProactive = $chartData['hrIntelligence']['proactive_hiring'][0] ?? null; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-black">5</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">ตำแหน่งไหนควรเปิดรับล่วงหน้า?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            {{ $topProactive ? $topProactive['position'] : 'ไม่มีข้อมูล' }} (Lead Time {{ $topProactive ? $topProactive['lead_time_days'] : 0 }} วัน)
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: {{ $topProactive ? $topProactive['urgency'] : '-' }} · ความเสี่ยง: {{ $topProactive ? $topProactive['risk_level'] : '-' }}
                        </p>
                    </div>

                    <!-- Q6 -->
                    @php $topQuarter = collect($chartData['hrIntelligence']['seasonal_budget'])->sortByDesc('allocation_pct')->first(); @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-cyan-100 dark:bg-cyan-950 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xs font-black">6</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">ช่วงเดือนไหนควรเพิ่มงบ Recruitment?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            {{ $topQuarter ? $topQuarter['quarter'] : '-' }} (สัดส่วนกิจกรรม {{ $topQuarter ? $topQuarter['allocation_pct'] : 0 }}%)
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: ปริมาณกิจกรรมสะสม {{ $topQuarter ? $topQuarter['volume'] : 0 }} รายการ · {{ $topQuarter ? $topQuarter['action'] : '-' }}
                        </p>
                    </div>

                    <!-- Q7 -->
                    @php $topShortage = $chartData['hrIntelligence']['shortage_risk'][0] ?? null; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-black">7</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">ตำแหน่งไหนมีแนวโน้มขาดคน?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            {{ $topShortage ? $topShortage['position'] : 'ไม่มีตำแหน่งเสี่ยง' }} (แผนก {{ $topShortage ? $topShortage['department'] : '-' }})
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: อนุมัติแล้วแต่ยังขาด {{ $topShortage ? $topShortage['approved_unposted'] : 0 }} อัตรา · สถานะ: {{ $topShortage ? $topShortage['risk'] : '-' }}
                        </p>
                    </div>

                    <!-- Q8 -->
                    @php $cph = $chartData['hrIntelligence']['cost_per_hire']; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-teal-100 dark:bg-teal-950 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs font-black">8</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">ค่าใช้จ่ายต่อการจ้าง 1 คน (Cost-per-hire)?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            ทั่วไป ฿{{ number_format($cph['avg_cost_general']) }} | สายเฉพาะทาง ฿{{ number_format($cph['avg_cost_specialist']) }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: {{ $cph['benchmark_advice'] }}
                        </p>
                    </div>

                    <!-- Q9 -->
                    @php 
                        $stages = $chartData['hrIntelligence']['lead_time_stages']; 
                        $totalLeadDays = array_sum(array_column($stages, 'avg_days')); 
                    @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-black">9</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">ระยะเวลาจากขออัตรากำลัง $\rightarrow$ เริ่มงาน?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            ระยะเวลารวมเฉลี่ย: {{ round($totalLeadDays, 1) }} วัน (ตั้งแต่ขอจนเริ่มงาน)
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: อนุมัติ ({{ $stages[0]['avg_days'] }} วัน) $\rightarrow$ ประกาศ/คัดกรอง ({{ $stages[1]['avg_days'] }} วัน) $\rightarrow$ สัมภาษณ์ ({{ $stages[2]['avg_days'] }} วัน) $\rightarrow$ รอเริ่มงาน ({{ $stages[3]['avg_days'] }} วัน)
                        </p>
                    </div>

                    <!-- Q10 -->
                    @php $prob = $chartData['hrIntelligence']['probation_tracking']; @endphp
                    <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-black">10</span>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">หลังรับเข้ามาแล้วผ่านทดลองงานหรือไม่?</h4>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-200 font-bold mb-1">
                            เชื่อมต่อฐานข้อมูลทดลองงานจริง {{ $prob['total_in_probation'] }} คน (ระยะเวลา {{ $prob['probation_period_days'] }} วัน)
                        </p>
                        <p class="text-[11px] text-slate-400">
                            สถิติจริง: {{ $prob['status_note'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end px-6 py-3 border-t border-gray-100 dark:border-gray-800 bg-slate-50 dark:bg-black/20">
                <button type="button" onclick="closeHrIntelligenceModal()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: DATA ARCHITECTURE DIAGRAM (RECRUITMENT DATA FLOW) -->
    <!-- ========================================================================= -->
    <div id="architectureModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm hidden flex-col items-center justify-start pt-20 sm:pt-24 pb-8 px-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#1E2129] rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden flex flex-col max-h-[calc(100vh-7.5rem)] my-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-slate-50 dark:bg-black/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-sitemap"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base">ผังโครงสร้างสถาปัตยกรรมข้อมูลการสรรหา (Recruitment Data Architecture)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">แสดงความเชื่อมโยงของข้อมูลตั้งแต่ต้นน้ำ จนถึงการนำไปวิเคราะห์และวางแผน</p>
                    </div>
                </div>
                <button type="button" onclick="closeArchitectureModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body: Interactive Diagram -->
            <div class="p-6 overflow-y-auto space-y-6">
                <!-- Top Ingestion Layer -->
                <div class="text-center">
                    <span class="inline-block px-4 py-1 rounded-full text-xs font-black bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800 uppercase tracking-widest mb-4">
                        RECRUITMENT DATA SOURCES
                    </span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Stream 1 -->
                        <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 text-center">
                            <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center mx-auto mb-2 text-sm font-bold shadow-md shadow-blue-500/20">
                                <i class="fa-solid fa-file-signature"></i>
                            </span>
                            <h4 class="font-extrabold text-xs text-blue-900 dark:text-blue-100">Job Request</h4>
                            <div class="my-1.5 text-slate-400 text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                            <span class="inline-block px-3 py-1 rounded-lg bg-white dark:bg-black/30 font-bold text-xs text-blue-700 dark:text-blue-300 shadow-sm border border-blue-100 dark:border-blue-900/30">
                                Position Data
                            </span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">คำขออัตรากำลัง & ข้อมูลตำแหน่งงาน</p>
                        </div>

                        <!-- Stream 2 -->
                        <div class="p-4 rounded-2xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900/50 text-center">
                            <span class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center mx-auto mb-2 text-sm font-bold shadow-md shadow-purple-500/20">
                                <i class="fa-solid fa-user-tie"></i>
                            </span>
                            <h4 class="font-extrabold text-xs text-purple-900 dark:text-purple-100">Candidate</h4>
                            <div class="my-1.5 text-slate-400 text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                            <span class="inline-block px-3 py-1 rounded-lg bg-white dark:bg-black/30 font-bold text-xs text-purple-700 dark:text-purple-300 shadow-sm border border-purple-100 dark:border-purple-900/30">
                                Application
                            </span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">ผู้สมัครงาน & ประวัติการยื่นใบสมัคร</p>
                        </div>

                        <!-- Stream 3 -->
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-center">
                            <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center mx-auto mb-2 text-sm font-bold shadow-md shadow-emerald-500/20">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </span>
                            <h4 class="font-extrabold text-xs text-emerald-900 dark:text-emerald-100">Employee</h4>
                            <div class="my-1.5 text-slate-400 text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                            <span class="inline-block px-3 py-1 rounded-lg bg-white dark:bg-black/30 font-bold text-xs text-emerald-700 dark:text-emerald-300 shadow-sm border border-emerald-100 dark:border-emerald-900/30">
                                Hiring Result
                            </span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">ผลการคัดเลือก & การบรรจุพนักงาน</p>
                        </div>
                    </div>
                </div>

                <!-- Central Aggregation Hub -->
                <div class="relative py-2 flex flex-col items-center">
                    <div class="text-slate-400 text-lg mb-2 animate-bounce"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="w-full max-w-lg p-3.5 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white text-center shadow-lg border border-slate-700">
                        <div class="flex items-center justify-center gap-2 text-xs font-extrabold text-amber-400 tracking-wider uppercase mb-1">
                            <i class="fa-solid fa-bolt"></i> Recruitment Events Stream
                        </div>
                        <p class="text-[11px] text-slate-300">รวบรวม Event ทุกจุด: สถานะเปลี่ยน, วันนัดสัมภาษณ์, ยอดกดดูประกาศ, ผลคะแนนประเมิน</p>
                    </div>

                    <div class="text-slate-400 text-lg my-2 animate-bounce"><i class="fa-solid fa-arrow-down"></i></div>

                    <div class="w-full max-w-lg p-3.5 rounded-2xl bg-gradient-to-r from-red-700 via-rose-600 to-red-800 text-white text-center shadow-lg border border-red-500/40">
                        <div class="flex items-center justify-center gap-2 text-xs font-extrabold tracking-wider uppercase mb-1">
                            <i class="fa-solid fa-database"></i> Data Warehouse & Analytics Mart
                        </div>
                        <p class="text-[11px] text-red-100">จัดกลุ่มข้อมูลเป็นมิติ (Dimensions): ระยะเวลา, แผนก, ตำแหน่งงาน, ช่องทางรับสมัคร</p>
                    </div>
                </div>

                <!-- Downstream Pillars -->
                <div>
                    <div class="text-slate-400 text-center text-lg mb-3 animate-bounce"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Pillar 1 -->
                        <div class="p-4 rounded-2xl border-2 border-red-500/40 bg-red-50/40 dark:bg-red-950/20 text-center">
                            <span class="w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center mx-auto mb-2 text-xs font-bold">
                                1
                            </span>
                            <h4 class="font-extrabold text-xs text-red-700 dark:text-red-300">Dashboard</h4>
                            <div class="my-1 text-slate-400 text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                            <span class="font-black text-sm text-slate-800 dark:text-white block">Monitor</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">ติดตามสถานะภาพรวมและปฏิบัติการรับสมัครแบบ Real-time</p>
                        </div>

                        <!-- Pillar 2 -->
                        <div class="p-4 rounded-2xl border-2 border-purple-500/40 bg-purple-50/40 dark:bg-purple-950/20 text-center">
                            <span class="w-8 h-8 rounded-lg bg-purple-600 text-white flex items-center justify-center mx-auto mb-2 text-xs font-bold">
                                2
                            </span>
                            <h4 class="font-extrabold text-xs text-purple-700 dark:text-purple-300">Analysis</h4>
                            <div class="my-1 text-slate-400 text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                            <span class="font-black text-sm text-slate-800 dark:text-white block">Improve</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">วิเคราะห์จุดคอขวด Conversion Funnel เพื่อปรับปรุงความเร็ว</p>
                        </div>

                        <!-- Pillar 3 -->
                        <div class="p-4 rounded-2xl border-2 border-cyan-500/40 bg-cyan-50/40 dark:bg-cyan-950/20 text-center">
                            <span class="w-8 h-8 rounded-lg bg-cyan-600 text-white flex items-center justify-center mx-auto mb-2 text-xs font-bold">
                                3
                            </span>
                            <h4 class="font-extrabold text-xs text-cyan-700 dark:text-cyan-300">Forecast</h4>
                            <div class="my-1 text-slate-400 text-xs"><i class="fa-solid fa-arrow-down"></i></div>
                            <span class="font-black text-sm text-slate-800 dark:text-white block">Planning</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">คาดการณ์แนวโน้มผู้สมัครและวางแผนอัตรากำลังล่วงหน้า</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end px-6 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-black/20">
                <button type="button" onclick="closeArchitectureModal()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: RECRUITMENT POST ANALYTICS (EXISTING MODAL PRESERVED) -->
    <!-- ========================================================================= -->
    <div id="recruitmentAnalyticsModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex-col items-center justify-start pt-20 sm:pt-24 pb-8 px-4 overflow-y-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col max-h-[calc(100vh-7.5rem)] my-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-base">สถิติการเข้าชมประกาศรับสมัครงาน</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">วิเคราะห์จำนวนการแสดงผล (Views) และการคลิกดูรายละเอียด (Clicks)</p>
                    </div>
                </div>
                <button type="button" onclick="closeRecruitmentAnalyticsModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 space-y-4 overflow-y-auto">
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-gray-700/50 p-3 rounded-xl">
                    <div class="flex items-center gap-2">
                        <label for="rec_analytics_post_select" class="text-xs font-semibold text-slate-600 dark:text-slate-300">เลือกประกาศ:</label>
                        <select id="rec_analytics_post_select" class="text-xs rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-1.5 px-3">
                            <option value="">-- ประกาศทั้งหมด --</option>
                            @foreach ($allJobPosts as $jp)
                                <option value="{{ $jp->id }}">{{ $jp->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" class="rec-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-600 text-white cursor-pointer" data-days="7">7 วันล่าสุด</button>
                        <button type="button" class="rec-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300 cursor-pointer" data-days="14">14 วัน</button>
                        <button type="button" class="rec-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300 cursor-pointer" data-days="30">30 วัน</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/20 border border-amber-200 dark:border-amber-800/60 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 dark:bg-amber-400/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[11px] font-semibold text-amber-700 dark:text-amber-300">ช่วงเวลาที่มีการเข้าชมมากที่สุด</span>
                            <span id="recPeakHourValue" class="text-sm font-extrabold text-amber-900 dark:text-amber-100 truncate block">กำลังคำนวณ...</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/20 border border-blue-200 dark:border-blue-800/60 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 dark:bg-blue-400/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[11px] font-semibold text-blue-700 dark:text-blue-300">ยอดการแสดงผลรวม</span>
                            <span id="recPeriodViewsValue" class="text-sm font-extrabold text-blue-900 dark:text-blue-100">0 ครั้ง</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20 border border-emerald-200 dark:border-emerald-800/60 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-400/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-arrow-pointer"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">ยอดกดสมัคร (Clicks to Apply)</span>
                            <span id="recPeriodClicksValue" class="text-sm font-extrabold text-emerald-900 dark:text-emerald-100">0 ครั้ง</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                    <div class="flex items-center gap-2">
                        <button type="button" id="recTabChartDaily" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors bg-slate-900 text-white dark:bg-white dark:text-slate-900 cursor-pointer">
                            <i class="fa-solid fa-calendar-day mr-1"></i> กราฟรายวัน
                        </button>
                        <button type="button" id="recTabChartHourly" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white cursor-pointer">
                            <i class="fa-solid fa-clock mr-1"></i> แจกแจงตามช่วงเวลา 24 ชม. (Peak Times)
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative h-64">
                    <canvas id="recAnalyticsChart"></canvas>
                </div>

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
                                    <th class="px-4 py-2 text-center text-green-600">การคลิกสมัคร (Clicks)</th>
                                    <th class="px-4 py-2 text-center text-purple-600">อัตราส่วนกดสมัคร (Intent Ratio)</th>
                                </tr>
                            </thead>
                            <tbody id="recAnalyticsTableBody" class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex justify-end px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <button type="button" onclick="closeRecruitmentAnalyticsModal()" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Chart.js CDN for Recruitment Analytics Modal -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Global variables for charts
        let chartDataGlobal = @json($chartData ?? []);
        let posBarChartInstance = null;
        let deptDonutChartInstance = null;
        let posPerfChartInstance = null;
        let forecastWaveChartInstance = null;

        // Tab Switching Logic (Executive Vuexy Theme)
        window.switchDashboardTab = function(tabName) {
            const tabs = ['monitor', 'analysis', 'forecast'];
            const tabConfig = {
                'monitor': {
                    badgeTitle: 'Real-time Monitor',
                    description: 'ติดตามสถานะภาพรวมและปฏิบัติการรับสมัครงานรายวัน',
                    activeBtnClass: 'bg-white dark:bg-[#252936] text-rose-600 dark:text-rose-400 shadow-sm border border-slate-200/80 dark:border-white/10 font-bold',
                    activeIconClass: 'text-rose-500',
                    activeBadgeClass: 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 border border-rose-200/70 dark:border-rose-900/50 font-extrabold',
                    iconName: 'fa-desktop',
                    dotColor: 'bg-emerald-500',
                    pingColor: 'bg-emerald-400'
                },
                'analysis': {
                    badgeTitle: 'Recruitment Funnel',
                    description: 'วิเคราะห์ประสิทธิภาพขั้นตอนการคัดเลือกและจุดคอขวดในกระบวนการ',
                    activeBtnClass: 'bg-white dark:bg-[#252936] text-purple-600 dark:text-purple-400 shadow-sm border border-slate-200/80 dark:border-white/10 font-bold',
                    activeIconClass: 'text-purple-500',
                    activeBadgeClass: 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-300 border border-purple-200/70 dark:border-purple-900/50 font-extrabold',
                    iconName: 'fa-magnifying-glass-chart',
                    dotColor: 'bg-purple-500',
                    pingColor: 'bg-purple-400'
                },
                'forecast': {
                    badgeTitle: 'Demand & Forecast',
                    description: 'แบบจำลองคาดการณ์แนวโน้มผู้สมัครและวางแผนอัตรากำลังล่วงหน้า',
                    activeBtnClass: 'bg-white dark:bg-[#252936] text-teal-600 dark:text-teal-400 shadow-sm border border-slate-200/80 dark:border-white/10 font-bold',
                    activeIconClass: 'text-teal-500',
                    activeBadgeClass: 'bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-300 border border-teal-200/70 dark:border-teal-900/50 font-extrabold',
                    iconName: 'fa-arrow-trend-up',
                    dotColor: 'bg-teal-500',
                    pingColor: 'bg-teal-400'
                }
            };

            tabs.forEach(t => {
                const pane = document.getElementById(`tab-content-${t}`);
                const btn = document.getElementById(`tab-btn-${t}`);
                const icon = document.getElementById(`tab-icon-${t}`);
                const badge = document.getElementById(`tab-badge-${t}`);

                if (pane && btn) {
                    if (t === tabName) {
                        pane.classList.remove('hidden');
                        btn.className = `dash-tab-btn flex-1 sm:flex-none inline-flex items-center justify-center gap-2.5 px-4 sm:px-5 py-2.5 rounded-lg text-xs transition-all duration-200 cursor-pointer select-none ${tabConfig[t].activeBtnClass}`;
                        if (icon) icon.className = `fa-solid ${tabConfig[t].iconName} text-xs ${tabConfig[t].activeIconClass}`;
                        if (badge) badge.className = `px-2 py-0.5 text-[10px] rounded-md transition-all ${tabConfig[t].activeBadgeClass}`;
                    } else {
                        pane.classList.add('hidden');
                        btn.className = 'dash-tab-btn flex-1 sm:flex-none inline-flex items-center justify-center gap-2.5 px-4 sm:px-5 py-2.5 rounded-lg text-xs font-medium transition-all duration-200 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-white/5 border border-transparent cursor-pointer select-none';
                        if (icon) icon.className = `fa-solid ${tabConfig[t].iconName} text-xs text-slate-400`;
                        if (badge) badge.className = 'px-2 py-0.5 text-[10px] font-semibold rounded-md transition-all bg-slate-200/70 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-transparent';
                    }
                }
            });

            const contextTextEl = document.getElementById('tab-context-text');
            const contextBadgeEl = document.getElementById('tab-context-badge');
            const dotEl = document.getElementById('tab-context-dot');
            const pingEl = document.getElementById('tab-context-ping');

            if (tabConfig[tabName]) {
                if (contextTextEl) contextTextEl.innerText = tabConfig[tabName].description;
                if (contextBadgeEl) contextBadgeEl.innerText = tabConfig[tabName].badgeTitle;
                if (dotEl) dotEl.className = `relative inline-flex rounded-full h-2.5 w-2.5 ${tabConfig[tabName].dotColor}`;
                if (pingEl) pingEl.className = `animate-ping absolute inline-flex h-full w-full rounded-full ${tabConfig[tabName].pingColor} opacity-75`;
            }

            // Delayed resize so ApexCharts recalculate width properly
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
                if (tabName === 'analysis' && !posPerfChartInstance) {
                    initPositionPerformanceChart();
                } else if (tabName === 'forecast' && !forecastWaveChartInstance) {
                    initForecastWaveChart();
                }
            }, 100);
        };

        // Modal Controllers
        window.showHrIntelligenceModal = function() {
            const m = document.getElementById('hrIntelligenceModal');
            if (m) {
                m.classList.remove('hidden');
                m.style.display = 'flex';
            }
        };

        window.closeHrIntelligenceModal = function() {
            const m = document.getElementById('hrIntelligenceModal');
            if (m) {
                m.classList.add('hidden');
                m.style.display = 'none';
            }
        };

        window.showArchitectureModal = function() {
            const m = document.getElementById('architectureModal');
            if (m) {
                m.classList.remove('hidden');
                m.style.display = 'flex';
            }
        };

        window.closeArchitectureModal = function() {
            const m = document.getElementById('architectureModal');
            if (m) {
                m.classList.add('hidden');
                m.style.display = 'none';
            }
        };

        // Close modals when clicking on the backdrop
        document.addEventListener('DOMContentLoaded', () => {
            ['hrIntelligenceModal', 'architectureModal', 'recruitmentAnalyticsModal'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('click', function(e) {
                        if (e.target === this) {
                            if (id === 'hrIntelligenceModal') closeHrIntelligenceModal();
                            else if (id === 'architectureModal') closeArchitectureModal();
                            else if (id === 'recruitmentAnalyticsModal') closeRecruitmentAnalyticsModal();
                        }
                    });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            // -------------------------------------------------------------
            // 1. MONITOR TAB: Bar Chart (Views vs Applicants)
            // -------------------------------------------------------------
            const posLabels = chartDataGlobal.positions?.labels || [];
            const posCounts = chartDataGlobal.positions?.counts || [];
            const posViews = chartDataGlobal.positions?.views || [];
            const posIds = chartDataGlobal.positions?.ids || [];

            const positionOptions = {
                series: [
                    { name: 'ยอดกดดูประกาศ (ครั้ง)', data: posViews },
                    { name: 'จำนวนผู้สมัคร (คน)', data: posCounts }
                ],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const selectedIndex = config.dataPointIndex;
                            if (posIds[selectedIndex]) {
                                window.location.href = "{{ route('backend.recruitment.applications.index') }}?job_post_id=" + posIds[selectedIndex];
                            }
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '50%',
                        dataLabels: { position: 'top' }
                    }
                },
                colors: ['#0284c7', '#dc2626'],
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opt) {
                        return val + (opt.seriesIndex === 0 ? ' ครั้ง' : ' คน');
                    },
                    offsetY: -20,
                    style: { fontSize: '11px', fontWeight: 'bold', colors: [textColor] }
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: textColor }
                },
                xaxis: {
                    categories: posLabels,
                    labels: { style: { colors: textColor, fontSize: '12px' }, rotate: -15 },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    title: { text: 'จำนวน (ครั้ง / คน)', style: { color: textColor, fontSize: '12px', fontWeight: 500 } },
                    labels: { style: { colors: textColor }, formatter: val => Math.floor(val) }
                },
                grid: {
                    borderColor: isDark ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: (val, opt) => val + (opt.seriesIndex === 0 ? " ครั้ง" : " คน")
                    }
                }
            };

            const posChartEl = document.querySelector("#position-interest-chart");
            if (posChartEl && typeof ApexCharts !== 'undefined') {
                posBarChartInstance = new ApexCharts(posChartEl, positionOptions);
                posBarChartInstance.render();
            }

            // -------------------------------------------------------------
            // 2. MONITOR TAB: Department Donut Chart
            // -------------------------------------------------------------
            const deptLabels = chartDataGlobal.departments?.labels || [];
            const deptCounts = chartDataGlobal.departments?.counts || [];

            const deptOptions = {
                series: deptCounts,
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'inherit'
                },
                labels: deptLabels,
                colors: ['#dc2626', '#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
                stroke: {
                    colors: [isDark ? '#1E2129' : '#fff'],
                    width: 2
                },
                legend: { show: false }, // Using custom Vuexy Side Legend list
                dataLabels: {
                    enabled: true,
                    formatter: val => Math.round(val) + "%"
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '12px', color: textColor },
                                value: {
                                    show: true,
                                    fontSize: '22px',
                                    fontWeight: '900',
                                    color: isDark ? '#fff' : '#1e293b',
                                    formatter: val => val + ' คน'
                                },
                                total: {
                                    show: true,
                                    label: 'ผู้สมัครรวม',
                                    color: textColor,
                                    formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' คน'
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: { formatter: val => val + " คน" }
                }
            };

            const deptChartEl = document.querySelector("#department-interest-chart");
            if (deptChartEl && typeof ApexCharts !== 'undefined') {
                deptDonutChartInstance = new ApexCharts(deptChartEl, deptOptions);
                deptDonutChartInstance.render();
            }
        });

        // -------------------------------------------------------------
        // 3. ANALYSIS TAB: Horizontal Bar Chart (Vuexy Balance Style)
        // -------------------------------------------------------------
        function initPositionPerformanceChart() {
            const isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const topMetrics = chartDataGlobal.topPositionMetrics || [];

            const pLabels = topMetrics.map(item => item.name);
            const pApps = topMetrics.map(item => item.applications);
            const pInterviews = topMetrics.map(item => item.interviews);
            const pHired = topMetrics.map(item => item.hired);

            const perfOptions = {
                series: [
                    { name: 'ผู้ยื่นใบสมัคร (Applications)', data: pApps },
                    { name: 'เข้ารอบสัมภาษณ์ (Interviewed)', data: pInterviews },
                    { name: 'ผ่านและบรรจุงาน (Hired)', data: pHired }
                ],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        barHeight: '60%',
                        dataLabels: { position: 'top' }
                    }
                },
                colors: ['#7367f0', '#ff9f43', '#28c76f'],
                dataLabels: {
                    enabled: true,
                    offsetX: 20,
                    style: { fontSize: '11px', fontWeight: 'bold', colors: [textColor] },
                    formatter: val => val + ' คน'
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: textColor }
                },
                xaxis: {
                    categories: pLabels,
                    labels: { style: { colors: textColor }, formatter: val => Math.floor(val) },
                    axisBorder: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: textColor, fontSize: '12px' } }
                },
                grid: {
                    borderColor: isDark ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: { formatter: val => val + " คน" }
                }
            };

            const perfChartEl = document.querySelector("#position-performance-chart");
            if (perfChartEl && typeof ApexCharts !== 'undefined') {
                posPerfChartInstance = new ApexCharts(perfChartEl, perfOptions);
                posPerfChartInstance.render();
            }
        }

        // -------------------------------------------------------------
        // 4. FORECAST TAB: Smooth Area Wave Chart (Vuexy Data Science)
        // -------------------------------------------------------------
        function initForecastWaveChart() {
            const isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const forecastData = chartDataGlobal.forecast || {};

            const months = forecastData.months || [];
            const actualApps = forecastData.actual_apps || [];
            const predictedApps = forecastData.predicted_apps || [];
            const interviews = forecastData.interviews || [];
            const hires = forecastData.hires || [];

            const waveOptions = {
                series: [
                    { name: 'สถิติผู้สมัครจริง (Actual Applications)', data: actualApps },
                    { name: 'คาดการณ์ผู้สมัคร (Forecast Projection)', data: predictedApps },
                    { name: 'นัดสัมภาษณ์ (Interviews)', data: interviews },
                    { name: 'บรรจุงาน (Placements / Hires)', data: hires }
                ],
                chart: {
                    type: 'area',
                    height: 340,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                colors: ['#0284c7', '#00cfe8', '#7367f0', '#28c76f'],
                stroke: {
                    curve: 'smooth',
                    width: [3, 3, 2, 2],
                    dashArray: [0, 5, 0, 0] // Dashed line for forecast
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: textColor }
                },
                xaxis: {
                    categories: months,
                    labels: { style: { colors: textColor, fontSize: '11px' } },
                    axisBorder: { show: false }
                },
                yaxis: {
                    title: { text: 'จำนวนผู้สมัคร (คน)', style: { color: textColor, fontSize: '12px' } },
                    labels: { style: { colors: textColor }, formatter: val => Math.floor(val) }
                },
                grid: {
                    borderColor: isDark ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: { formatter: val => (val !== null ? val + " คน" : "-") }
                }
            };

            const waveChartEl = document.querySelector("#forecast-wave-chart");
            if (waveChartEl && typeof ApexCharts !== 'undefined') {
                forecastWaveChartInstance = new ApexCharts(waveChartEl, waveOptions);
                forecastWaveChartInstance.render();
            }
        }
    </script>

    <!-- Recruitment Analytics Modal Scripts (Preserved & Enhanced) -->
    <script>
        let recAnalyticsChart = null;
        let recCurrentDays = 7;
        let recChartMode = 'daily';
        let recCurrentLogs = [];
        let recCurrentHourlyLogs = [];
        let recPollingTimer = null;

        window.showRecruitmentAnalyticsModal = function() {
            const modal = document.getElementById('recruitmentAnalyticsModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            }
            window.loadRecruitmentAnalytics(false);
            if (recPollingTimer) clearInterval(recPollingTimer);
            recPollingTimer = setInterval(() => {
                window.loadRecruitmentAnalytics(true);
            }, 5000);
        };

        window.closeRecruitmentAnalyticsModal = function() {
            const modal = document.getElementById('recruitmentAnalyticsModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
            if (recPollingTimer) {
                clearInterval(recPollingTimer);
                recPollingTimer = null;
            }
        };

        window.renderRecDailyChart = function(logs) {
            const dates = [];
            const viewsMap = {};
            const clicksMap = {};

            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            for (let i = recCurrentDays - 1; i >= 0; i--) {
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

            const canvasEl = document.getElementById('recAnalyticsChart');
            if (!canvasEl) return;
            const ctx = canvasEl.getContext('2d');
            if (recAnalyticsChart) {
                recAnalyticsChart.destroy();
            }

            recAnalyticsChart = new Chart(ctx, {
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
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        };

        window.renderRecHourlyChart = function(hourlyLogs) {
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

            const canvasEl = document.getElementById('recAnalyticsChart');
            if (!canvasEl) return;
            const ctx = canvasEl.getContext('2d');
            if (recAnalyticsChart) {
                recAnalyticsChart.destroy();
            }

            recAnalyticsChart = new Chart(ctx, {
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
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        };

        window.updateRecPeakSummary = function(logs, hourlyLogs) {
            let totalViews = 0;
            let totalClicks = 0;
            logs.forEach(item => {
                if (item.event_type === 'view') totalViews += parseInt(item.count || 0);
                if (item.event_type === 'click') totalClicks += parseInt(item.count || 0);
            });

            const viewsEl = document.getElementById('recPeriodViewsValue');
            const clicksEl = document.getElementById('recPeriodClicksValue');
            if (viewsEl) viewsEl.innerText = `${totalViews.toLocaleString()} ครั้ง`;
            if (clicksEl) {
                let ratioText = '';
                if (totalViews > 0) {
                    if (totalClicks <= totalViews) {
                        ratioText = ` (${((totalClicks / totalViews) * 100).toFixed(1)}%)`;
                    } else {
                        ratioText = ` (${(totalClicks / totalViews).toFixed(1)} เท่าของยอดชม)`;
                    }
                } else if (totalClicks > 0) {
                    ratioText = ` (เข้าหน้าสมัครตรง)`;
                }
                clicksEl.innerText = `${totalClicks.toLocaleString()} ครั้ง${ratioText}`;
            }

            const hourCounts = {};
            hourlyLogs.forEach(item => {
                const h = parseInt(item.hour);
                hourCounts[h] = (hourCounts[h] || 0) + parseInt(item.count || 0);
            });

            let maxHour = null;
            let maxCount = -1;
            for (let h = 0; h < 24; h++) {
                if ((hourCounts[h] || 0) > maxCount) {
                    maxCount = hourCounts[h] || 0;
                    maxHour = h;
                }
            }

            const peakEl = document.getElementById('recPeakHourValue');
            if (peakEl) {
                if (maxCount > 0 && maxHour !== null) {
                    const startStr = String(maxHour).padStart(2, '0') + ':00';
                    const endStr = String((maxHour + 1) % 24).padStart(2, '0') + ':00';
                    peakEl.innerText = `${startStr} - ${endStr} น. (${maxCount} ครั้ง)`;
                } else {
                    peakEl.innerText = 'ยังไม่มีข้อมูลช่วงพีค';
                }
            }
        };

        window.updateRecTable = function(logs) {
            const tableBody = document.getElementById('recAnalyticsTableBody');
            if (!tableBody) return;

            const dateStats = {};
            logs.forEach(item => {
                if (!item.view_date) return;
                const d = String(item.view_date).split('T')[0];
                if (!dateStats[d]) dateStats[d] = { views: 0, clicks: 0 };
                if (item.event_type === 'view') dateStats[d].views += parseInt(item.count || 0);
                if (item.event_type === 'click') dateStats[d].clicks += parseInt(item.count || 0);
            });

            const sortedDates = Object.keys(dateStats).sort().reverse();
            if (sortedDates.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-slate-400">ยังไม่มีสถิติในรอบเวลานี้</td></tr>';
                return;
            }

            let html = '';
            sortedDates.forEach(d => {
                const row = dateStats[d];
                let ratioText = '0.0%';
                if (row.views > 0) {
                    if (row.clicks <= row.views) {
                        ratioText = ((row.clicks / row.views) * 100).toFixed(1) + '%';
                    } else {
                        ratioText = `${(row.clicks / row.views).toFixed(1)} เท่า (${((row.clicks / row.views) * 100).toFixed(0)}%)`;
                    }
                } else if (row.clicks > 0) {
                    ratioText = `${row.clicks} คลิก (เข้าสมัครโดยตรง)`;
                }

                html += `
                    <tr>
                        <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-200">${d}</td>
                        <td class="px-4 py-2 text-center text-blue-600 font-bold">${row.views.toLocaleString()}</td>
                        <td class="px-4 py-2 text-center text-green-600 font-bold">${row.clicks.toLocaleString()}</td>
                        <td class="px-4 py-2 text-center font-medium">${ratioText}</td>
                    </tr>
                `;
            });
            tableBody.innerHTML = html;
        };

        window.loadRecruitmentAnalytics = function(isBackground = false) {
            const postSelect = document.getElementById('rec_analytics_post_select');
            const postId = postSelect ? postSelect.value : '';
            const url = `{{ route('backend.recruitment.analytics.data') }}?job_post_id=${postId}&days=${recCurrentDays}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    recCurrentLogs = data.logs || [];
                    recCurrentHourlyLogs = data.hourly_logs || [];

                    if (recChartMode === 'daily') {
                        window.renderRecDailyChart(recCurrentLogs);
                    } else {
                        window.renderRecHourlyChart(recCurrentHourlyLogs);
                    }

                    window.updateRecPeakSummary(recCurrentLogs, recCurrentHourlyLogs);
                    window.updateRecTable(recCurrentLogs);
                })
                .catch(err => {
                    console.error('Error fetching analytics:', err);
                });
        };

        // Range buttons handling
        document.querySelectorAll('.rec-analytics-range-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.rec-analytics-range-btn').forEach(b => {
                    b.className = 'rec-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300 cursor-pointer';
                });
                btn.className = 'rec-analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-600 text-white cursor-pointer';
                recCurrentDays = parseInt(btn.getAttribute('data-days') || 7);
                window.loadRecruitmentAnalytics(false);
            });
        });

        const postSelectEl = document.getElementById('rec_analytics_post_select');
        if (postSelectEl) {
            postSelectEl.addEventListener('change', () => {
                window.loadRecruitmentAnalytics(false);
            });
        }

        const tabDailyBtn = document.getElementById('recTabChartDaily');
        const tabHourlyBtn = document.getElementById('recTabChartHourly');

        if (tabDailyBtn && tabHourlyBtn) {
            tabDailyBtn.addEventListener('click', () => {
                recChartMode = 'daily';
                tabDailyBtn.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-colors bg-slate-900 text-white dark:bg-white dark:text-slate-900 cursor-pointer';
                tabHourlyBtn.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-colors text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white cursor-pointer';
                window.renderRecDailyChart(recCurrentLogs);
            });

            tabHourlyBtn.addEventListener('click', () => {
                recChartMode = 'hourly';
                tabHourlyBtn.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-colors bg-slate-900 text-white dark:bg-white dark:text-slate-900 cursor-pointer';
                tabDailyBtn.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-colors text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white cursor-pointer';
                window.renderRecHourlyChart(recCurrentHourlyLogs);
            });
        }
    </script>
    @endpush
@endsection