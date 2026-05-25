@extends('layouts.recruitment.app')

@section('title', 'Recruitment Dashboard')

@section('content')
    <div class="space-y-8 pb-10">
        <div class="flex justify-between items-center mt-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white shadow-lg shadow-red-500/30">
                        <i class="fa-solid fa-chart-pie text-lg"></i>
                    </div>
                    Recruitment Overview
                </h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">ภาพรวมระบบสรรหาบุคลากรและสถานะการดำเนินการ</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stat 1: Pending -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-clock-rotate-left text-6xl text-yellow-500"></i>
                </div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-yellow-500/30">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">คำขอรออนุมัติ</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['pending_requests']) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stat 2: Active Posts -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-bullhorn text-6xl text-emerald-500"></i>
                </div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-emerald-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ประกาศที่เปิดอยู่</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['active_posts']) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stat 3: Total Applications -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-users text-6xl text-blue-500"></i>
                </div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ผู้สมัครทั้งหมด</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['total_applications']) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stat 4: New Applications -->
            <div class="bg-white dark:bg-[#1E2129] p-6 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-fire text-6xl text-red-500"></i>
                </div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-red-500/30">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ผู้สมัครมาใหม่</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white">
                            {{ number_format($stats['new_applications']) }}
                        </p>
                    </div>
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
                                <tr class="hover:bg-red-50/50 dark:hover:bg-red-900/10 transition-colors group">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-slate-500 dark:text-gray-400 font-bold border border-slate-200 dark:border-gray-700 shrink-0">
                                                <i class="fa-solid fa-user text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-gray-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors">
                                                    {{ $app->applicant?->full_name ?? 'ไม่มีชื่อ' }}
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
                                        @if($app->status == 'new')
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/50 uppercase">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                    {{ $app->status }}
                                                </div>
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-gray-800 text-slate-600 dark:text-gray-400 border border-slate-200 dark:border-gray-700 uppercase">
                                                {{ $app->status }}
                                            </span>
                                        @endif
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
                                <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition-colors group">
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-slate-800 dark:text-gray-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors truncate max-w-[200px]" title="{{ $req->position_name ?: ($req->jobPosition?->position_name ?? 'N/A') }}">
                                            {{ $req->position_name ?: ($req->jobPosition?->position_name ?? 'ไม่มีข้อมูลตำแหน่ง') }}
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
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800/50 uppercase">
                                                <i class="fa-solid fa-check-circle"></i> {{ $req->status }}
                                            </span>
                                        @elseif($req->status == 'pending') 
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800/50 uppercase">
                                                <i class="fa-solid fa-clock"></i> {{ $req->status }}
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
@endsection