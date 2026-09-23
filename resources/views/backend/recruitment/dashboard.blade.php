@extends('layouts.recruitment.app')

@section('title', 'Recruitment Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white shadow-lg shadow-red-500/25 shrink-0">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                </div>
                <div>
                    <h2 class="page-title text-gray-900 dark:text-white">Recruitment Overview</h2>
                    <p class="page-subtitle mt-0.5">ภาพรวมระบบสรรหาบุคลากร · สถานะการดำเนินการ</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-2 bg-slate-100 dark:bg-[#2A2E39] hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2.5 rounded-xl transition-all font-semibold text-sm shadow-sm active:scale-[0.98]">
                    <i class="fa-solid fa-bell text-red-500"></i> ศูนย์แจ้งเตือน
                </a>
                <button type="button" onclick="showRecruitmentAnalyticsModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-500/20 font-semibold text-sm active:scale-[0.98] cursor-pointer">
                    <i class="fa-solid fa-chart-line"></i> สถิติการเข้าชมประกาศ
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <!-- Stat 1: Pending -->
            <div class="bg-white dark:bg-[#1E2129] p-5 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-clock-rotate-left text-6xl text-yellow-500"></i>
                </div>
                <div class="flex items-center gap-3.5 relative z-10">
                    <div class="w-13 h-13 bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-yellow-500/30 shrink-0">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">คำขอรออนุมัติ</p>
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['pending_requests']) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stat 2: Active Posts -->
            <div class="bg-white dark:bg-[#1E2129] p-5 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-bullhorn text-6xl text-emerald-500"></i>
                </div>
                <div class="flex items-center gap-3.5 relative z-10">
                    <div class="w-13 h-13 bg-gradient-to-br from-emerald-400 to-emerald-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-emerald-500/30 shrink-0">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ประกาศที่เปิดอยู่</p>
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['active_posts']) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stat 3: Total Views (ยอดกดดูประกาศ) -->
            <div class="bg-white dark:bg-[#1E2129] p-5 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-eye text-6xl text-sky-500"></i>
                </div>
                <div class="flex items-center gap-3.5 relative z-10">
                    <div class="w-13 h-13 bg-gradient-to-br from-sky-400 to-blue-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-sky-500/30 shrink-0">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ยอดเข้าชม (กดดู)</p>
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['total_views'] ?? 0) }} <span class="text-xs font-medium text-slate-400">ครั้ง</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stat 4: Total Applications -->
            <div class="bg-white dark:bg-[#1E2129] p-5 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-users text-6xl text-blue-500"></i>
                </div>
                <div class="flex items-center gap-3.5 relative z-10">
                    <div class="w-13 h-13 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-blue-500/30 shrink-0">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ผู้สมัครทั้งหมด</p>
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['total_applications']) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stat 5: New Applications -->
            <a href="{{ route('backend.recruitment.applications.index', ['status' => 'submitted']) }}" class="bg-white dark:bg-[#1E2129] p-5 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group block cursor-pointer">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-fire text-6xl text-red-500"></i>
                </div>
                <div class="flex items-center gap-3.5 relative z-10">
                    <div class="w-13 h-13 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-red-500/30 shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ผู้สมัครมาใหม่</p>
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping inline-block -mt-1"></span>
                        </div>
                        <p class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['new_applications']) }}
                        </p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Applicant Interest Charts Section (กราฟเก็บข้อมูลความสนใจของผู้สมัคร) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Chart 1: ความสนใจตามตำแหน่งงาน (Bar Chart) -->
            <div class="lg:col-span-2 bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-2">
                            <i class="fa-solid fa-chart-column text-[#B21F24]"></i>
                            <span>สถิติยอดกดดูและจำนวนผู้สมัครแยกตามตำแหน่งงาน</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5 flex-wrap">
                            <span>เปรียบเทียบยอดเข้าชมประกาศ (ครั้ง) กับจำนวนผู้ส่งใบสมัคร (คน)</span>
                            <span class="text-[#B21F24] font-medium">• คลิกที่แท่งกราฟเพื่อดูรายชื่อผู้สมัคร</span>
                        </p>
                    </div>
                    <span class="px-3 py-1 bg-red-50 dark:bg-red-950/40 text-[#B21F24] dark:text-red-400 text-xs font-bold rounded-full border border-red-200 dark:border-red-900/50">
                        Top Positions
                    </span>
                </div>
                <div id="position-interest-chart" class="w-full min-h-[300px]"></div>
            </div>

            <!-- Chart 2: ความสนใจตามฝ่าย/แผนก (Donut / Pie Chart) -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-2">
                                <i class="fa-solid fa-chart-pie text-blue-500"></i>
                                <span>สัดส่วนความสนใจตามแผนก</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">สัดส่วนผู้สมัครในแต่ละกลุ่มสายงาน</p>
                        </div>
                    </div>
                    <div id="department-interest-chart" class="w-full flex items-center justify-center min-h-[260px]"></div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-center">
                    <span class="text-xs text-slate-400 font-medium">รวมผู้สมัครทั้งหมด {{ number_format($stats['total_applications']) }} รายการ</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Applications -->
            <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden flex flex-col relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-black/20">
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-2">
                        <i class="fa-solid fa-user-clock text-red-500"></i> ผู้สมัครล่าสุด
                    </h3>
                    <a href="{{ route('backend.recruitment.applications.index') }}"
                        class="text-xs font-bold text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded-full shadow-md shadow-red-500/20 transition-colors">
                        ดูทั้งหมด <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto flex-1 p-4">
                    <table class="w-full text-left text-sm border-collapse">
                        <!-- Add header for better structure -->
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                <th class="pb-3 px-4 font-semibold">ชื่อผู้สมัคร / ตำแหน่ง</th>
                                <th class="pb-3 px-4 font-semibold text-center">เวลา</th>
                                <th class="pb-3 px-4 font-semibold text-right">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                            @forelse($recent_applications as $app)
                                <tr onclick="window.location='{{ route('backend.recruitment.applications.show', $app->id) }}'" 
                                    class="hover:bg-red-50/60 dark:hover:bg-red-900/10 transition-colors group cursor-pointer" title="คลิกเพื่อดูรายละเอียดผู้สมัคร">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-slate-500 dark:text-gray-400 font-bold border border-slate-200 dark:border-gray-700 shrink-0 group-hover:border-red-400 transition-colors">
                                                <i class="fa-solid fa-user text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-gray-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors flex items-center gap-1.5">
                                                    {{ $app->applicant?->full_name ?? 'ไม่มีชื่อ' }}
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-0 group-hover:opacity-100 transition-opacity text-red-500"></i>
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-gray-500 mt-0.5 max-w-[200px] truncate" title="{{ $app->jobPost?->title ?? 'N/A' }}">
                                                    {{ $app->jobPost?->title ?? 'ไม่มีข้อมูลตำแหน่ง' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-slate-600 dark:text-gray-400 text-[10px] font-medium px-2.5 py-1 rounded-md border border-gray-200 dark:border-gray-700">
                                            <i class="fa-regular fa-clock mr-1"></i> {{ $app->created_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $app->status_badge_class }}">
                                            @if(in_array($app->status, ['new', 'submitted']))
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            @endif
                                            {{ $app->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-regular fa-folder-open text-3xl mb-2 opacity-50"></i>
                                            <p class="text-sm">ยังไม่มีผู้สมัครล่าสุด</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden flex flex-col relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-gray-500 to-slate-800 dark:from-gray-600 dark:to-gray-400"></div>
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-black/20">
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-slate-500"></i> คำขอเปิดรับสมัคร
                    </h3>
                    <a href="{{ route('backend.recruitment.requests.index') }}"
                        class="rounded-full bg-slate-800 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2">
                        ดูทั้งหมด <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto flex-1 p-4">
                    <table class="w-full text-left text-sm border-collapse">
                        <!-- Add header for better structure -->
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                <th class="pb-3 px-4 font-semibold">ตำแหน่ง / ฝ่าย</th>
                                <th class="pb-3 px-4 font-semibold text-center">จำนวน</th>
                                <th class="pb-3 px-4 font-semibold text-right">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                            @forelse($recent_requests as $req)
                                <tr onclick="window.location='{{ route('backend.recruitment.requests.show', $req->id) }}'" 
                                    class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50 transition-colors group cursor-pointer" title="คลิกเพื่อดูรายละเอียดคำขอ">
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-slate-800 dark:text-gray-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors truncate max-w-[200px] flex items-center gap-1.5" title="{{ $req->position_name ?: ($req->jobPosition?->position_name ?? 'N/A') }}">
                                            <span>{{ $req->position_name ?: ($req->jobPosition?->position_name ?? 'ไม่มีข้อมูลตำแหน่ง') }}</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-0 group-hover:opacity-100 transition-opacity text-slate-400"></i>
                                        </p>
                                        <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500 dark:text-gray-500">
                                            <i class="fa-solid fa-building text-[10px]"></i> 
                                            <span class="truncate max-w-[180px]" title="{{ $req->department?->department_name ?? 'N/A' }}">{{ $req->department?->department_name ?? 'ไม่มีข้อมูลฝ่าย' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="inline-flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800/80 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <span class="text-sm font-black text-slate-700 dark:text-gray-300 leading-none mb-1">{{ $req->headcount }}</span>
                                            <span class="text-[9px] text-slate-400 uppercase leading-none font-bold">อัตรา</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        @if($req->status == 'approved') 
                                            @if($req->jobPosts->count() > 0)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800/50 uppercase" title="สร้าง Job Post แล้ว">
                                                    <i class="fa-solid fa-check-circle"></i> มี Job Post
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm animate-pulse uppercase" title="อนุมัติแล้ว แต่ยังไม่ได้สร้าง Job Post">
                                                    <i class="fa-solid fa-bullhorn text-[10px]"></i> รอสร้าง Post
                                                </span>
                                            @endif
                                        @elseif($req->status == 'pending' || str_starts_with($req->status, 'pending_')) 
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800/50 uppercase">
                                                <i class="fa-solid fa-clock"></i> รออนุมัติ
                                            </span>
                                        @else 
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 uppercase">
                                                <i class="fa-solid fa-circle-minus"></i> {{ $req->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-regular fa-folder-open text-3xl mb-2 opacity-50"></i>
                                            <p class="text-sm">ไม่มีคำขอที่กำลังดำเนินการ</p>
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

<!-- Recruitment Analytics Modal -->
<div id="recruitmentAnalyticsModal" onclick="if(event.target === this) closeRecruitmentAnalyticsModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-[9999] p-4 hidden" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
        
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-blue-600"></i> สถิติการแสดงผลและการคลิกเข้าชมประกาศรับสมัครงาน
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
                <button type="button" onclick="window.loadRecruitmentAnalytics(true)" title="รีเฟรชข้อมูลตอนนี้" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                    <i id="recAnalyticsRefreshIcon" class="fa-solid fa-arrows-rotate text-sm"></i>
                </button>
                <button type="button" onclick="closeRecruitmentAnalyticsModal()" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
            <!-- Filter toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-gray-700/40 p-3 rounded-xl border border-slate-100 dark:border-gray-700">
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

            <!-- Peak Hours & Key Metrics Highlight Cards -->
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
                        <span class="block text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">ยอดคลิก / CTR รวม</span>
                        <span id="recPeriodClicksValue" class="text-sm font-extrabold text-emerald-900 dark:text-emerald-100">0 ครั้ง (0%)</span>
                    </div>
                </div>
            </div>

            <!-- Chart Switch Tabs -->
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

            <!-- Chart Canvas -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative h-64">
                <canvas id="recAnalyticsChart"></canvas>
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
                        <tbody id="recAnalyticsTableBody" class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            <!-- Injected by JS -->
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
    <!-- Chart.js CDN for Recruitment Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartData = @json($chartData ?? []);
            const isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            // 1. Position Interest Bar Chart (Views vs Applicants)
            const posLabels = chartData.positions?.labels || [];
            const posCounts = chartData.positions?.counts || [];
            const posViews = chartData.positions?.views || [];
            const posIds = chartData.positions?.ids || [];

            const positionOptions = {
                series: [
                    {
                        name: 'ยอดกดดูประกาศ (ครั้ง)',
                        data: posViews
                    },
                    {
                        name: 'จำนวนผู้สมัคร (คน)',
                        data: posCounts
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Prompt, sans-serif',
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
                        columnWidth: '55%',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                colors: ['#0284C7', '#B21F24'],
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opt) {
                        if (opt.seriesIndex === 0) {
                            return val + ' ครั้ง';
                        }
                        return val + ' คน';
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '11px',
                        fontWeight: 'bold',
                        colors: [textColor]
                    }
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: {
                        colors: textColor
                    }
                },
                xaxis: {
                    categories: posLabels,
                    labels: {
                        style: {
                            colors: textColor,
                            fontSize: '12px'
                        },
                        rotate: -20
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    title: {
                        text: 'จำนวน (ครั้ง / คน)',
                        style: {
                            color: textColor,
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    },
                    labels: {
                        style: {
                            colors: textColor
                        },
                        formatter: function(val) {
                            return Math.floor(val);
                        }
                    }
                },
                grid: {
                    borderColor: isDark ? '#334155' : '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val, opt) {
                            if (opt.seriesIndex === 0) {
                                return val + " ครั้ง";
                            }
                            return val + " คน";
                        }
                    }
                }
            };

            const posChartEl = document.querySelector("#position-interest-chart");
            if (posChartEl && typeof ApexCharts !== 'undefined') {
                const posChart = new ApexCharts(posChartEl, positionOptions);
                posChart.render();
            }

            // 2. Department Interest Donut Chart
            const deptLabels = chartData.departments?.labels || [];
            const deptCounts = chartData.departments?.counts || [];

            const deptOptions = {
                series: deptCounts,
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Prompt, sans-serif'
                },
                labels: deptLabels,
                colors: ['#B21F24', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'],
                stroke: {
                    colors: [isDark ? '#1E2129' : '#fff'],
                    width: 2
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 4
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return Math.round(val) + "%";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '13px',
                                    color: textColor
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontWeight: 'bold',
                                    color: isDark ? '#fff' : '#1e293b',
                                    formatter: function (val) {
                                        return val + ' คน';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'ผู้สมัครรวม',
                                    color: textColor,
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' คน';
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return val + " คน";
                        }
                    }
                }
            };

            const deptChartEl = document.querySelector("#department-interest-chart");
            if (deptChartEl && typeof ApexCharts !== 'undefined') {
                const deptChart = new ApexCharts(deptChartEl, deptOptions);
                deptChart.render();
            }
        });
    </script>

    <!-- Recruitment Analytics Scripts -->
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
                            ticks: {
                                precision: 0
                            }
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
                            ticks: {
                                precision: 0
                            }
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

            document.getElementById('recPeriodViewsValue').textContent = totalViews.toLocaleString() + ' ครั้ง';
            const ctr = totalViews > 0 ? ((totalClicks / totalViews) * 100).toFixed(1) + '%' : '0%';
            document.getElementById('recPeriodClicksValue').textContent = totalClicks.toLocaleString() + ' ครั้ง (' + ctr + ')';

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

            const peakEl = document.getElementById('recPeakHourValue');
            if (peakHour !== null && maxViews > 0) {
                const nextHour = (peakHour + 1) % 24;
                const timeStr = `${String(peakHour).padStart(2, '0')}:00 - ${String(nextHour).padStart(2, '0')}:00 น.`;
                peakEl.innerHTML = `<span class="text-amber-600 dark:text-amber-400">${timeStr}</span> <span class="text-xs font-normal text-slate-500">(${maxViews} วิว)</span>`;
            } else {
                peakEl.textContent = 'ยังไม่มีข้อมูลการเข้าชม';
            }
        };

        window.renderRecTable = function(logs) {
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            const dateSummary = {};
            for (let i = recCurrentDays - 1; i >= 0; i--) {
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
                const ctr = v > 0 ? ((c / v) * 100).toFixed(1) + '%' : '0.0%';
                rows += `
                    <tr>
                        <td class="px-4 py-2 font-medium text-slate-700 dark:text-slate-300">${date}</td>
                        <td class="px-4 py-2 text-center font-bold text-blue-600">${v}</td>
                        <td class="px-4 py-2 text-center font-bold text-green-600">${c}</td>
                        <td class="px-4 py-2 text-center text-purple-600 font-semibold">${ctr}</td>
                    </tr>
                `;
            });

            document.getElementById('recAnalyticsTableBody').innerHTML = rows || '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-400">ไม่มีข้อมูลในช่วงเวลานี้</td></tr>';
        };

        window.loadRecruitmentAnalytics = function(isSilent = false) {
            const jobPostId = document.getElementById('rec_analytics_post_select').value || '';

            if (!isSilent) {
                document.getElementById('recAnalyticsTableBody').innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"><i class="fa-solid fa-spinner fa-spin text-xl text-blue-600 mr-2"></i> กำลังประมวลผลสถิติ...</td></tr>';
                document.getElementById('recPeakHourValue').textContent = 'กำลังคำนวณ...';
            } else {
                document.getElementById('recAnalyticsRefreshIcon').classList.add('fa-spin');
            }

            fetch(`{{ route('backend.recruitment.analytics.data') }}?job_post_id=${jobPostId}&days=${recCurrentDays}`)
                .then(r => r.json())
                .then(res => {
                    document.getElementById('recAnalyticsRefreshIcon').classList.remove('fa-spin');

                    recCurrentLogs = res.logs || [];
                    recCurrentHourlyLogs = res.hourly_logs || [];

                    window.updateRecPeakSummary(recCurrentLogs, recCurrentHourlyLogs);

                    const renderActiveChart = () => {
                        if (recChartMode === 'hourly') {
                            window.renderRecHourlyChart(recCurrentHourlyLogs);
                        } else {
                            window.renderRecDailyChart(recCurrentLogs);
                        }
                    };

                    try {
                        if (typeof Chart !== 'undefined') {
                            renderActiveChart();
                        }
                    } catch (err) {
                        console.error("Chart Render Error:", err);
                    }
                    window.renderRecTable(recCurrentLogs);
                })
                .catch(err => {
                    document.getElementById('recAnalyticsRefreshIcon').classList.remove('fa-spin');
                    console.error("Analytics Load Error:", err);
                    if (!isSilent) {
                        document.getElementById('recAnalyticsTableBody').innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-red-500 font-medium"><i class="fa-solid fa-circle-exclamation mr-1.5"></i> เกิดข้อผิดพลาดในการโหลดข้อมูลสถิติ</td></tr>';
                        document.getElementById('recPeakHourValue').textContent = 'ไม่สามารถโหลดข้อมูลได้';
                    }
                });
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Post selector change
            document.getElementById('rec_analytics_post_select').addEventListener('change', function() {
                window.loadRecruitmentAnalytics();
            });

            // Range buttons
            document.querySelectorAll('.rec-analytics-range-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.rec-analytics-range-btn').forEach(b => {
                        b.classList.remove('bg-blue-600', 'text-white');
                        b.classList.add('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-200');
                    });
                    this.classList.remove('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-200');
                    this.classList.add('bg-blue-600', 'text-white');
                    recCurrentDays = parseInt(this.dataset.days);
                    window.loadRecruitmentAnalytics();
                });
            });

            // Tab switches
            document.getElementById('recTabChartDaily').addEventListener('click', function() {
                recChartMode = 'daily';
                this.classList.remove('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-white');
                this.classList.add('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');
                const hourlyBtn = document.getElementById('recTabChartHourly');
                hourlyBtn.classList.remove('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');
                hourlyBtn.classList.add('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-white');
                if (recCurrentLogs.length || recCurrentHourlyLogs.length) {
                    window.renderRecDailyChart(recCurrentLogs);
                }
            });

            document.getElementById('recTabChartHourly').addEventListener('click', function() {
                recChartMode = 'hourly';
                this.classList.remove('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-white');
                this.classList.add('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');
                const dailyBtn = document.getElementById('recTabChartDaily');
                dailyBtn.classList.remove('bg-slate-900', 'text-white', 'dark:bg-white', 'dark:text-slate-900');
                dailyBtn.classList.add('text-slate-500', 'hover:text-slate-800', 'dark:text-slate-400', 'dark:hover:text-white');
                if (recCurrentHourlyLogs.length || recCurrentLogs.length) {
                    window.renderRecHourlyChart(recCurrentHourlyLogs);
                }
            });
        });
    </script>
    @endpush
@endsection