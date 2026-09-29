@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-5 sm:p-7 space-y-6">
        
        <!-- Header Bar Inside Frame -->
        <div class="pb-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-semibold text-slate-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('welcome') }}" class="hover:text-indigo-600 transition">หน้าหลัก</a>
                    <span class="mx-2 text-slate-400">/</span>
                    <span class="text-slate-800 dark:text-slate-200">จัดการโปสเตอร์และแบนเนอร์ประชาสัมพันธ์</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <i class="fa-solid fa-images text-red-600"></i> จัดการโปสเตอร์และแบนเนอร์ประชาสัมพันธ์
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    กำหนดรูปภาพโปสเตอร์ สไลด์ Carousel และแบนเนอร์ด้านข้าง พร้อมลิงก์หรือไฟล์แนบที่จะแสดงในหน้าแรก
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="showAnalyticsModal()" id="openAnalyticsModal" class="btn bg-blue-600 hover:bg-blue-700 text-white shadow-md flex items-center gap-2 px-4 py-2 rounded-xl font-medium text-sm transition-all cursor-pointer">
                    <i class="fa-solid fa-chart-line"></i> สถิติการเข้าชม
                </button>
                <button type="button" onclick="showPosterModal()" id="openCreatePosterModal" class="btn btn-success bg-green-600 hover:bg-green-700 text-white shadow-md flex items-center gap-2 px-4 py-2 rounded-xl font-medium text-sm transition-all cursor-pointer">
                    <i class="fa-solid fa-plus"></i> เพิ่มโปสเตอร์ใหม่
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
                <h3 id="overview_total_views" class="text-2xl font-black text-slate-800 dark:text-white">{{ number_format($posters->sum('views')) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-arrow-pointer"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400">ยอดการคลิกเข้าชมทั้งหมด (Total Clicks)</span>
                <h3 id="overview_total_clicks" class="text-2xl font-black text-slate-800 dark:text-white">{{ number_format($posters->sum('clicks')) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                @php
                    $totalViews = $posters->sum('views');
                    $totalClicks = $posters->sum('clicks');
                    $ctr = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;
                @endphp
                <span class="text-xs text-slate-500 dark:text-slate-400">อัตราการคลิกต่อการมองเห็น (CTR)</span>
                <h3 id="overview_ctr" class="text-2xl font-black text-slate-800 dark:text-white">{{ $ctr }}%</h3>
            </div>
        </div>
    </div>

    <!-- Poster Table Card -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            รูปภาพโปสเตอร์
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ชื่อโปสเตอร์ / หัวข้อ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ตำแหน่งแสดงผล
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            การคลิก / ปลายทาง
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            สถิติ (View / Click)
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ลำดับ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            สถานะ
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            การกระทำ
                        </th>
                    </tr>
                </thead>
                <tbody id="posters-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($posters as $poster)
                    <tr id="poster-row-{{ $poster->id }}" class="hover:bg-slate-50 dark:hover:bg-gray-700/40 transition-colors">
                        <!-- Image Preview -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ asset($poster->image_path) }}" target="_blank" rel="noopener noreferrer" class="block w-24 h-14 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm bg-slate-100 dark:bg-slate-900 group relative">
                                <img src="{{ asset($poster->image_path) }}" alt="{{ $poster->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy">
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                            </a>
                        </td>

                        <!-- Title -->
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white min-w-[200px] break-words">
                            <div class="font-semibold">{{ $poster->title }}</div>
                            <div class="text-[11px] text-gray-400">สร้างเมื่อ {{ $poster->created_at->format('d/m/Y H:i') }}</div>
                        </td>

                        <!-- Position -->
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            @if ($poster->position === 'hero_banner')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    <i class="fa-solid fa-panorama mr-1"></i> แบนเนอร์หัวเว็บด้านบน (Hero Banner)
                                </span>
                            @elseif ($poster->position === 'hero_top')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                    <i class="fa-solid fa-crown mr-1"></i> โปสเตอร์ใหญ่หัวข้อใหญ่ (ด้านบนสุดเต็มความกว้าง)
                                </span>
                            @elseif ($poster->position === 'main_carousel')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                    <i class="fa-solid fa-sliders mr-1"></i> สไลด์หลัก (Carousel ฝั่งซ้าย)
                                </span>
                            @elseif ($poster->position === 'side_top')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                    <i class="fa-solid fa-square-caret-up mr-1"></i> แบนเนอร์ข้าง (บน)
                                </span>
                            @elseif ($poster->position === 'side_bottom')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    <i class="fa-solid fa-square-caret-down mr-1"></i> แบนเนอร์ข้าง (ล่าง)
                                </span>
                            @elseif ($poster->position === 'recruitment')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <i class="fa-solid fa-bullhorn mr-1"></i> โปสเตอร์หน้ารับสมัครงาน (Recruitment)
                                </span>
                            @endif
                        </td>

                        <!-- Action Target -->
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 dark:text-gray-300">
                            @if ($poster->target_type === 'link' && $poster->link_url)
                                <a href="{{ $poster->link_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 underline truncate max-w-[220px]" title="{{ $poster->link_url }}">
                                    <i class="fa-solid fa-link text-[10px]"></i> {{ $poster->link_url }}
                                </a>
                            @elseif ($poster->target_type === 'file' && $poster->file_path)
                                <a href="{{ asset($poster->file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-red-600 hover:text-red-700 font-medium underline">
                                    <i class="fa-solid fa-file-arrow-down"></i> เปิดไฟล์แนบ ({{ pathinfo($poster->file_path, PATHINFO_EXTENSION) }})
                                </a>
                            @else
                                <span class="text-gray-400">ไม่มีการลิงก์</span>
                            @endif
                        </td>

                        <!-- Views & Clicks Statistics -->
                        <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                            <div class="inline-flex items-center gap-2 bg-slate-100 dark:bg-gray-700/60 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-gray-600">
                                <span class="flex items-center gap-1 text-slate-700 dark:text-slate-200" title="ยอดการมองเห็น (Views)">
                                    <i class="fa-regular fa-eye text-blue-500"></i>
                                    <strong id="poster_views_{{ $poster->id }}">{{ number_format($poster->views ?? 0) }}</strong>
                                </span>
                                <span class="text-slate-300 dark:text-slate-600">|</span>
                                <span class="flex items-center gap-1 text-slate-700 dark:text-slate-200" title="ยอดคลิก (Clicks)">
                                    <i class="fa-solid fa-arrow-pointer text-green-500"></i>
                                    <strong id="poster_clicks_{{ $poster->id }}">{{ number_format($poster->clicks ?? 0) }}</strong>
                                </span>
                            </div>
                        </td>

                        <!-- Sort Order -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ $poster->sort_order }}
                        </td>

                        <!-- Status & Schedule -->
                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                            @if(!$poster->is_active)
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <i class="fa-solid fa-pause mr-1 text-[10px] self-center"></i> ปิดใช้งาน
                                </span>
                            @elseif($poster->start_at && $poster->start_at->isFuture())
                                <div class="space-y-0.5">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                        <i class="fa-solid fa-clock mr-1 text-[10px] self-center"></i> รอถึงเวลาโพสต์
                                    </span>
                                    <span class="block text-[10px] text-gray-400">เริ่ม: {{ $poster->start_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @elseif($poster->end_at && $poster->end_at->isPast())
                                <div class="space-y-0.5">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                        <i class="fa-solid fa-calendar-xmark mr-1 text-[10px] self-center"></i> หมดเวลาโพสต์
                                    </span>
                                    <span class="block text-[10px] text-gray-400">สิ้นสุด: {{ $poster->end_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @else
                                <div class="space-y-0.5">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        <i class="fa-solid fa-circle-check mr-1 text-[10px] self-center"></i> เผยแพร่
                                    </span>
                                    @if($poster->end_at)
                                        <span class="block text-[10px] text-gray-400">ถึง: {{ $poster->end_at->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button type="button" class="btn btn-warning btn-sm edit-poster-btn p-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white" data-id="{{ $poster->id }}" title="แก้ไข">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            @if(Auth::check() && Auth::user()->canDelete())
                            <button type="button" class="btn btn-error btn-sm text-white delete-poster-btn p-2 rounded-lg bg-red-600 hover:bg-red-700" data-id="{{ $poster->id }}" title="ลบ">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <i class="fa-regular fa-image text-3xl mb-2 block opacity-40"></i>
                            ยังไม่มีโปสเตอร์ในระบบ กดปุ่ม "เพิ่มโปสเตอร์ใหม่" เพื่อเริ่มต้น
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create / Edit Poster -->
<div id="posterModal" onclick="if(event.target === this) closePosterModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-[9999] p-4 hidden" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2" id="posterModalTitle">
                <i class="fa-solid fa-image text-red-600"></i> เพิ่มโปสเตอร์ใหม่
            </h3>
            <button type="button" onclick="closePosterModal()" class="close-poster-modal text-gray-400 hover:text-red-500 transition-colors focus:outline-none cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="posterForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="poster_id" id="poster_id">
            
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <!-- Title -->
                <div>
                    <label for="poster_title" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        ชื่อโปสเตอร์ / หัวข้อประชาสัมพันธ์ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="poster_title" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="เช่น รับสมัครงาน, โครงการอบรม..." required>
                </div>

                <!-- Position & Sort Order -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="poster_position" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            หน้าและตำแหน่งที่ต้องการแสดงผล <span class="text-red-500">*</span>
                        </label>
                        <select name="position" id="poster_position" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <optgroup label="🏠 หน้าหลัก (Home / Welcome)">
                                <option value="hero_banner">✨ แบนเนอร์หัวเว็บด้านบน (Hero Banner ด้านบนสุด)</option>
                                <option value="hero_top">👑 โปสเตอร์ใหญ่หัวข้อใหญ่ (ด้านบนสุดเต็มความกว้าง)</option>
                                <option value="main_carousel">สไลด์หลัก (Carousel ฝั่งซ้าย)</option>
                                <option value="side_top">แบนเนอร์ด้านข้าง (แถวบน)</option>
                                <option value="side_bottom">แบนเนอร์ด้านข้าง (แถวล่าง)</option>
                            </optgroup>
                            <optgroup label="📢 หน้ารับสมัครงาน (Recruitment)">
                                <option value="recruitment">📢 แสดงที่หน้ารับสมัครงาน (แถบซ้าย - ขนาดแนะนำ 50 x 150 ซม.)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label for="poster_sort_order" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            ลำดับการแสดงผล (น้อยไปมาก)
                        </label>
                        <input type="number" name="sort_order" id="poster_sort_order" value="0" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <!-- Poster Image -->
                <div>
                    <label for="poster_image" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        รูปภาพโปสเตอร์ <span id="image_required_marker" class="text-red-500">*</span>
                        <span id="poster_size_hint" class="text-slate-400 font-normal text-[11px] block transition-all duration-200">
                            (ขนาดแนะนำ: <strong class="text-emerald-600 dark:text-emerald-400">หน้ารับสมัครงาน 50 x 150 ซม. (150*50 ซม.)</strong>, สไลด์หลัก 16:9 หรือ 2:1, แบนเนอร์ข้าง 2.5:1)
                        </span>
                    </label>
                    <input type="file" name="image" id="poster_image" accept="image/*" class="block w-full text-xs text-slate-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                    <div id="poster_image_preview" class="mt-2.5"></div>
                </div>

                <!-- Target Action Type (Link, File, None) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        การทำงานเมื่อคลิกโปสเตอร์
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-gray-700/50">
                            <input type="radio" name="target_type" value="link" checked class="text-red-600 focus:ring-red-500">
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200">เปิดลิงก์ URL</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-gray-700/50">
                            <input type="radio" name="target_type" value="file" class="text-red-600 focus:ring-red-500">
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200">เปิดไฟล์แนบ</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-gray-700/50">
                            <input type="radio" name="target_type" value="none" class="text-red-600 focus:ring-red-500">
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200">รูปภาพอย่างเดียว</span>
                        </label>
                    </div>
                </div>

                <!-- Option: Link URL Input -->
                <div id="link_group">
                    <label for="poster_link_url" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        ระบุ URL ลิงก์ปลายทาง
                    </label>
                    <input type="url" name="link_url" id="poster_link_url" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="https://example.com หรือ /recruitment">
                </div>

                <!-- Option: File Attachment Input -->
                <div id="file_group" class="hidden">
                    <label for="poster_attachment_file" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        แนบไฟล์เอกสาร (PDF, Word, Excel, รูปภาพ ฯลฯ)
                    </label>
                    <input type="file" name="attachment_file" id="poster_attachment_file" class="block w-full text-xs text-slate-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                    <div id="poster_file_preview" class="mt-2 text-xs"></div>
                </div>

                <!-- Schedule / วันเวลาที่ต้องการโพสต์ -->
                <div class="bg-slate-50 dark:bg-gray-700/40 p-3.5 rounded-xl border border-slate-200/80 dark:border-gray-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-clock text-red-500"></i> กำหนดวันเวลาที่จะโพสต์ (ไม่บังคับ)
                        </label>
                        <span class="text-[11px] text-slate-400 dark:text-slate-400">หากไม่ระบุ จะแสดงผลทันทีแบบไม่มีวันหมดอายุ</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="poster_start_at" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-300 mb-1">
                                เริ่มแสดงผลตั้งแต่ (วัน-เวลา)
                            </label>
                            <input type="datetime-local" name="start_at" id="poster_start_at" class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="poster_end_at" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-300 mb-1">
                                สิ้นสุดการแสดงผล (วัน-เวลา)
                            </label>
                            <input type="datetime-local" name="end_at" id="poster_end_at" class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Is Active -->
                <div class="flex items-center pt-1">
                    <input type="checkbox" name="is_active" id="poster_is_active" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" value="1" checked>
                    <label for="poster_is_active" class="ml-2 block text-xs font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                        เปิดใช้งานโปสเตอร์นี้ (Active)
                    </label>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <button type="button" onclick="closePosterModal()" class="close-poster-modal px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                    ยกเลิก
                </button>
                <button type="submit" id="savePosterBtn" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                    <svg id="poster-spinner" class="animate-spin h-3.5 w-3.5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>บันทึกข้อมูล</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Analytics Statistics -->
<div id="analyticsModal" onclick="if(event.target === this) closeAnalyticsModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center z-[9999] p-4 hidden" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
        
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-blue-600"></i> สถิติการแสดงผลและการคลิกเข้าชมโปสเตอร์
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
                <button type="button" onclick="window.loadAnalyticsData(true)" title="รีเฟรชข้อมูลตอนนี้" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i id="analyticsRefreshIcon" class="fa-solid fa-arrows-rotate text-sm"></i>
                </button>
                <button type="button" onclick="closeAnalyticsModal()" class="close-analytics-modal text-gray-400 hover:text-red-500 transition-colors focus:outline-none cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
            <!-- Filter toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-gray-700/40 p-3 rounded-xl border border-slate-100 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <label for="analytics_poster_select" class="text-xs font-semibold text-slate-600 dark:text-slate-300">เลือกโปสเตอร์:</label>
                    <select id="analytics_poster_select" class="text-xs rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-1.5 px-3">
                        <option value="">-- โปสเตอร์ทั้งหมด --</option>
                        @foreach ($posters as $p)
                            <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <button type="button" class="analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-600 text-white" data-days="7">7 วันล่าสุด</button>
                    <button type="button" class="analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300" data-days="14">14 วัน</button>
                    <button type="button" class="analytics-range-btn px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300" data-days="30">30 วัน</button>
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
                        <span id="peakHourValue" class="text-sm font-extrabold text-amber-900 dark:text-amber-100 truncate block">กำลังคำนวณ...</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/20 border border-blue-200 dark:border-blue-800/60 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 dark:bg-blue-400/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] font-semibold text-blue-700 dark:text-blue-300">ยอดการแสดงผลรวม</span>
                        <span id="periodViewsValue" class="text-sm font-extrabold text-blue-900 dark:text-blue-100">0 ครั้ง</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20 border border-emerald-200 dark:border-emerald-800/60 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-400/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-arrow-pointer"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">ยอดคลิก / CTR รวม</span>
                        <span id="periodClicksValue" class="text-sm font-extrabold text-emerald-900 dark:text-emerald-100">0 ครั้ง (0%)</span>
                    </div>
                </div>
            </div>

            <!-- Chart Switch Tabs -->
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                <div class="flex items-center gap-2">
                    <button type="button" id="tabChartDaily" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors bg-slate-900 text-white dark:bg-white dark:text-slate-900">
                        <i class="fa-solid fa-calendar-day mr-1"></i> กราฟรายวัน
                    </button>
                    <button type="button" id="tabChartHourly" class="px-3 py-1 rounded-lg text-xs font-bold transition-colors text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">
                        <i class="fa-solid fa-clock mr-1"></i> แจกแจงตามช่วงเวลา 24 ชม. (Peak Times)
                    </button>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative h-64">
                <canvas id="posterAnalyticsChart"></canvas>
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
                        <tbody id="analyticsTableBody" class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex justify-end px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <button type="button" onclick="closeAnalyticsModal()" class="close-analytics-modal px-4 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                ปิด
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN for Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let analyticsChart = null;
    let currentAnalyticsDays = 7;
    let currentChartMode = 'daily'; // 'daily' or 'hourly'
    let currentLogs = [];
    let currentHourlyLogs = [];

    window.updateSizeHint = function(position) {
        if (position === 'recruitment') {
            $('#poster_size_hint').html('<strong class="text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800 inline-block mt-0.5">💡 ขนาดแนะนำสำหรับหน้ารับสมัครงาน: 50 x 150 ซม. (150*50 ซม. / อัตราส่วน 1:3)</strong>');
        } else if (position === 'hero_banner') {
            $('#poster_size_hint').html('(ขนาดแนะนำ: แนวนอนหัวเว็บ 1920 x 450 - 550 px หรือ อัตราส่วน 3.5:1 ถึง 4:1)');
        } else if (position === 'hero_top') {
            $('#poster_size_hint').html('(ขนาดแนะนำ: แนวนอนเต็มความกว้าง 1920 x 480 px หรือ อัตราส่วน 4:1)');
        } else if (position === 'main_carousel') {
            $('#poster_size_hint').html('(ขนาดแนะนำ: แนวนอน 16:9 หรือ 2:1 สำหรับสไลด์หลัก)');
        } else {
            $('#poster_size_hint').html('(ขนาดแนะนำ: แนวนอน 2.5:1 สำหรับแบนเนอร์ด้านข้าง)');
        }
    };

    window.toggleTargetFields = function(type) {
        if (type === 'link') {
            $('#link_group').removeClass('hidden');
            $('#file_group').addClass('hidden');
        } else if (type === 'file') {
            $('#link_group').addClass('hidden');
            $('#file_group').removeClass('hidden');
        } else {
            $('#link_group').addClass('hidden');
            $('#file_group').addClass('hidden');
        }
    };

    // Global Modal Functions
    window.showPosterModal = function() {
        window.closeAnalyticsModal();
        const form = document.getElementById('posterForm');
        if (form) form.reset();
        $('#poster_id').val('');
        $('#posterModalTitle').html('<i class="fa-solid fa-plus text-red-600"></i> เพิ่มโปสเตอร์ใหม่');
        $('#image_required_marker').removeClass('hidden');
        $('#poster_image').prop('required', true);
        $('#poster_image_preview').html('');
        $('#poster_file_preview').html('');
        $('input[name="target_type"][value="link"]').prop('checked', true);
        window.toggleTargetFields('link');
        window.updateSizeHint($('#poster_position').val());

        const modal = document.getElementById('posterModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    };

    window.closePosterModal = function() {
        const modal = document.getElementById('posterModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    };

    let analyticsPollingTimer = null;

    window.showAnalyticsModal = function() {
        window.closePosterModal();
        const modal = document.getElementById('analyticsModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
        window.loadAnalyticsData(false);

        // Start real-time background polling every 5 seconds while modal is open
        if (analyticsPollingTimer) clearInterval(analyticsPollingTimer);
        analyticsPollingTimer = setInterval(() => {
            window.loadAnalyticsData(true);
        }, 5000);
    };

    window.closeAnalyticsModal = function() {
        const modal = document.getElementById('analyticsModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        if (analyticsPollingTimer) {
            clearInterval(analyticsPollingTimer);
            analyticsPollingTimer = null;
        }
    };

    window.renderDailyChart = function(logs) {
        const dates = [];
        const viewsMap = {};
        const clicksMap = {};

        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        for (let i = currentAnalyticsDays - 1; i >= 0; i--) {
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

        const canvasEl = document.getElementById('posterAnalyticsChart');
        if (!canvasEl) return;
        const ctx = canvasEl.getContext('2d');
        if (analyticsChart) {
            analyticsChart.destroy();
        }

        analyticsChart = new Chart(ctx, {
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

    window.renderHourlyChart = function(hourlyLogs) {
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

        const canvasEl = document.getElementById('posterAnalyticsChart');
        if (!canvasEl) return;
        const ctx = canvasEl.getContext('2d');
        if (analyticsChart) {
            analyticsChart.destroy();
        }

        analyticsChart = new Chart(ctx, {
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

    window.updatePeakSummary = function(logs, hourlyLogs) {
        // Calculate total views and clicks
        let totalViews = 0;
        let totalClicks = 0;
        logs.forEach(item => {
            if (item.event_type === 'view') totalViews += parseInt(item.count || 0);
            if (item.event_type === 'click') totalClicks += parseInt(item.count || 0);
        });

        $('#periodViewsValue').text(totalViews.toLocaleString() + ' ครั้ง');
        const ctr = totalViews > 0 ? ((totalClicks / totalViews) * 100).toFixed(1) + '%' : '0%';
        $('#periodClicksValue').text(totalClicks.toLocaleString() + ' ครั้ง (' + ctr + ')');

        // Find peak hour
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
            $('#peakHourValue').html(`<span class="text-amber-600 dark:text-amber-400">${timeStr}</span> <span class="text-xs font-normal text-slate-500">(${maxViews} วิว)</span>`);
        } else {
            $('#peakHourValue').text('ยังไม่มีข้อมูลการเข้าชม');
        }
    };

    window.renderTable = function(logs) {
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const dateSummary = {};
        for (let i = currentAnalyticsDays - 1; i >= 0; i--) {
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

        $('#analyticsTableBody').html(rows || '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-400">ไม่มีข้อมูลในช่วงเวลานี้</td></tr>');
    };

    window.loadAnalyticsData = function(isSilent = false) {
        const posterId = $('#analytics_poster_select').val() || '';
        
        if (!isSilent) {
            $('#analyticsTableBody').html('<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"><i class="fa-solid fa-spinner fa-spin text-xl text-blue-600 mr-2"></i> กำลังประมวลผลสถิติ...</td></tr>');
            $('#peakHourValue').text('กำลังคำนวณ...');
        } else {
            $('#analyticsRefreshIcon').addClass('fa-spin text-blue-600');
        }

        $.ajax({
            url: '{{ route("posters.analytics.data") }}',
            type: 'GET',
            data: { poster_id: posterId, days: currentAnalyticsDays },
            dataType: 'json',
            success: function(res) {
                $('#analyticsRefreshIcon').removeClass('fa-spin text-blue-600');

                currentLogs = res.logs || [];
                currentHourlyLogs = res.hourly_logs || [];

                window.updatePeakSummary(currentLogs, currentHourlyLogs);

                // Update Overview Cards on Main Page in real-time
                if (res.total_views !== undefined) {
                    $('#overview_total_views').text(Number(res.total_views).toLocaleString());
                }
                if (res.total_clicks !== undefined) {
                    $('#overview_total_clicks').text(Number(res.total_clicks).toLocaleString());
                }
                if (res.total_views !== undefined && res.total_clicks !== undefined) {
                    const ctr = res.total_views > 0 ? ((res.total_clicks / res.total_views) * 100).toFixed(2) + '%' : '0%';
                    $('#overview_ctr').text(ctr);
                }

                // Update individual poster views and clicks in the table in real-time
                if (res.posters && Array.isArray(res.posters)) {
                    res.posters.forEach(p => {
                        const vEl = document.getElementById('poster_views_' + p.id);
                        const cEl = document.getElementById('poster_clicks_' + p.id);
                        if (vEl) vEl.innerText = Number(p.views || 0).toLocaleString();
                        if (cEl) cEl.innerText = Number(p.clicks || 0).toLocaleString();
                    });
                }

                const renderActiveChart = () => {
                    if (currentChartMode === 'hourly') {
                        window.renderHourlyChart(currentHourlyLogs);
                    } else {
                        window.renderDailyChart(currentLogs);
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
                    console.error("Chart Render Error:", err);
                }
                window.renderTable(currentLogs);
            },
            error: function(xhr, status, error) {
                $('#analyticsRefreshIcon').removeClass('fa-spin text-blue-600');
                console.error("Analytics Load Error:", error, xhr);
                if (!isSilent) {
                    $('#analyticsTableBody').html('<tr><td colspan="4" class="px-4 py-6 text-center text-red-500 font-medium"><i class="fa-solid fa-circle-exclamation mr-1.5"></i> เกิดข้อผิดพลาดในการโหลดข้อมูลสถิติ (' + (xhr.status || 'Error') + ')</td></tr>');
                    $('#peakHourValue').text('ไม่สามารถโหลดข้อมูลได้');
                }
            }
        });
    };

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#poster_position').on('change', function() {
            window.updateSizeHint($(this).val());
        });

        $('input[name="target_type"]').on('change', function() {
            window.toggleTargetFields($(this).val());
        });

        // Live Preview when selecting an Image
        $('#poster_image').on('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    $('#poster_image_preview').html(`
                        <div class="relative group mt-2 p-2.5 bg-slate-50 dark:bg-gray-700/60 rounded-xl border border-dashed border-red-300 dark:border-red-900 flex items-center gap-4">
                            <div class="w-28 h-16 sm:w-36 sm:h-20 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-600 shadow-sm bg-slate-900 shrink-0">
                                <img src="${evt.target.result}" alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="block text-xs font-bold text-slate-800 dark:text-white truncate">${file.name}</span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">${(file.size / 1024).toFixed(1)} KB</span>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 text-[10px] font-semibold">
                                    <i class="fa-solid fa-check mr-1"></i> พร้อมอัปโหลด
                                </span>
                            </div>
                            <button type="button" id="clearSelectedImage" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors" title="ยกเลิกรูปนี้">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            }
        });

        // Clear Selected Image preview
        $(document).on('click', '#clearSelectedImage', function() {
            $('#poster_image').val('');
            $('#poster_image_preview').html('');
        });

        // Edit Poster
        $(document).on('click', '.edit-poster-btn', function() {
            const id = $(this).data('id');
            $.get('/posters/' + id + '/edit', function(data) {
                $('#posterModalTitle').html('<i class="fa-solid fa-pen-to-square text-amber-500"></i> แก้ไขโปสเตอร์');
                $('#poster_id').val(data.id);
                $('#poster_title').val(data.title);
                $('#poster_position').val(data.position);
                $('#poster_sort_order').val(data.sort_order);
                $('#poster_is_active').prop('checked', data.is_active);

                // Image is optional when updating
                $('#image_required_marker').addClass('hidden');
                $('#poster_image').prop('required', false);

                if (data.image_path) {
                    $('#poster_image_preview').html(`
                        <div class="mt-2 p-3 bg-slate-50 dark:bg-gray-700/60 rounded-xl border border-slate-200 dark:border-gray-600 flex items-center gap-4">
                            <div class="w-28 h-16 sm:w-36 sm:h-20 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-600 shadow-sm bg-slate-900 shrink-0">
                                <img src="/${data.image_path}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="block text-xs font-bold text-slate-800 dark:text-white">รูปภาพปัจจุบันในระบบ</span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">หากไม่ต้องการเปลี่ยนรูปภาพ ไม่จำเป็นต้องเลือกไฟล์ใหม่</span>
                            </div>
                        </div>
                    `);
                } else {
                    $('#poster_image_preview').html('');
                }

                // Target Type
                $(`input[name="target_type"][value="${data.target_type}"]`).prop('checked', true);
                window.toggleTargetFields(data.target_type);

                $('#poster_link_url').val(data.link_url || '');
                $('#poster_start_at').val(data.start_at || '');
                $('#poster_end_at').val(data.end_at || '');

                if (data.file_path) {
                    const fname = data.file_path.split('/').pop();
                    $('#poster_file_preview').html(`
                        <div class="flex items-center gap-2 p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded border border-blue-200 dark:border-blue-800">
                            <i class="fa-solid fa-file"></i>
                            <a href="/${data.file_path}" target="_blank" class="underline truncate flex-1">${fname}</a>
                            <span class="text-[10px] text-slate-400">(ไฟล์ปัจจุบัน)</span>
                        </div>
                    `);
                } else {
                    $('#poster_file_preview').html('');
                }

                const modal = document.getElementById('posterModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.style.display = 'flex';
                }
                window.updateSizeHint(data.position);
            });
        });

        // Submit Form (Create / Update)
        $('#posterForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#poster_id').val();
            const url = id ? '/posters/' + id : '/posters';
            const formData = new FormData(this);

            if (id) {
                formData.append('_method', 'PUT');
            }

            const $btn = $('#savePosterBtn');
            const $spinner = $('#poster-spinner');
            $btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
            $spinner.removeClass('hidden');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    window.closePosterModal();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: res.message || 'บันทึกข้อมูลโปสเตอร์เรียบร้อยแล้ว',
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed');
                    $spinner.addClass('hidden');
                    let msg = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถบันทึกได้',
                            html: msg
                        });
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // Delete Poster
        $(document).on('click', '.delete-poster-btn', function() {
            const id = $(this).data('id');
            const doDelete = () => {
                $.ajax({
                    url: '/posters/' + id,
                    method: 'DELETE',
                    success: function() {
                        $('#poster-row-' + id).fadeOut(400, function() {
                            $(this).remove();
                        });
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'ลบโปสเตอร์เรียบร้อยแล้ว',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        alert('เกิดข้อผิดพลาดในการลบ');
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการลบโปสเตอร์?',
                    text: 'ข้อมูลและไฟล์โปสเตอร์นี้จะถูกลบออกจากระบบอย่างถาวร',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'ยืนยันลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        doDelete();
                    }
                });
            } else if (confirm('ยืนยันการลบโปสเตอร์นี้?')) {
                doDelete();
            }
        });

        // Analytics events
        $('#analytics_poster_select').on('change', function() {
            window.loadAnalyticsData();
        });

        $('.analytics-range-btn').on('click', function() {
            $('.analytics-range-btn').removeClass('bg-blue-600 text-white').addClass('bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200');
            $(this).removeClass('bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200').addClass('bg-blue-600 text-white');
            currentAnalyticsDays = $(this).data('days');
            window.loadAnalyticsData();
        });

        // Tab switches between Daily and Hourly chart views
        $('#tabChartDaily').on('click', function() {
            currentChartMode = 'daily';
            $('#tabChartDaily').removeClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white').addClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900');
            $('#tabChartHourly').removeClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900').addClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white');
            if (currentLogs.length || currentHourlyLogs.length) {
                window.renderDailyChart(currentLogs);
            }
        });

        $('#tabChartHourly').on('click', function() {
            currentChartMode = 'hourly';
            $('#tabChartHourly').removeClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white').addClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900');
            $('#tabChartDaily').removeClass('bg-slate-900 text-white dark:bg-white dark:text-slate-900').addClass('text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white');
            if (currentHourlyLogs.length || currentLogs.length) {
                window.renderHourlyChart(currentHourlyLogs);
            }
        });

        // Background polling every 10s to keep main page overview and table statistics updated in real-time
        setInterval(() => {
            const isModalOpen = $('#analyticsModal').is(':visible');
            // If modal is not open, silently poll to update the overview metrics cards and table counts
            if (!isModalOpen) {
                window.loadAnalyticsData(true);
            }
        }, 10000);
    });
</script>
@endsection
