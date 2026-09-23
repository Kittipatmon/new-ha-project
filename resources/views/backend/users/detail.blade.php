@extends('layouts.app')
@section('title', 'รายละเอียดพนักงาน : ' . ($user->employee_code ?: $user->emp_code))

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6 max-w-6xl">

    {{-- Breadcrumb & Top Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('users.index') }}" class="hover:text-primary transition-colors flex items-center gap-1.5 font-medium">
                <i class="fa-solid fa-users text-xs"></i> ข้อมูลพนักงาน
            </a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-gray-800 dark:text-gray-200 font-semibold">
                {{ $user->prefix }} {{ $user->first_name }} {{ $user->last_name }}
            </span>
            <span class="text-xs font-mono bg-base-200 dark:bg-gray-700 text-base-content/80 px-2 py-0.5 rounded">
                #{{ $user->employee_code }}
            </span>
        </div>

        <div class="flex items-center gap-2.5 self-end sm:self-auto">
            @if(Auth::check() && Auth::user()->canEdit())
            <a href="{{ route('users.edit', $user->id) }}" 
               class="btn btn-warning btn-sm text-white font-medium shadow-sm hover:shadow transition-all gap-2 px-4 rounded-lg">
                <i class="fa-solid fa-pen-to-square"></i> แก้ไขข้อมูล
            </a>
            @endif
            <a href="{{ route('users.index') }}" 
               class="btn btn-outline btn-sm font-medium border-base-300 dark:border-gray-600 hover:bg-base-200 dark:hover:bg-gray-700 gap-2 px-4 rounded-lg">
                <i class="fa-solid fa-arrow-left"></i> ย้อนกลับ
            </a>
        </div>
    </div>

    {{-- Unified Master Card Frame --}}
    <div class="bg-base-100 dark:bg-gray-800 rounded-2xl shadow-xl border border-base-200 dark:border-gray-700 overflow-hidden">

        {{-- 1. Profile Hero Banner --}}
        <div class="relative bg-gradient-to-r from-blue-700 via-indigo-700 to-primary text-white p-4 sm:p-8">
            <div class="absolute inset-0 bg-black/10"></div>
            {{-- Decorative pattern --}}
            <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                {{-- Avatar / Profile Picture --}}
                <div class="relative flex-shrink-0">
                    @php
                        $photoUrl = $user->photo_user ? asset($user->photo_user) : null;
                        $initialChar = mb_substr($user->first_name ?: $user->firstname ?: 'U', 0, 1, 'UTF-8');
                    @endphp

                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" 
                             alt="{{ $user->fullname }}" 
                             class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl object-cover ring-4 ring-white/90 dark:ring-gray-800 shadow-xl bg-white"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-28 h-28 sm:w-32 sm:h-32 rounded-2xl ring-4 ring-white/90 dark:ring-gray-800 shadow-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white font-bold text-4xl items-center justify-center">
                            {{ $initialChar }}
                        </div>
                    @else
                        <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl ring-4 ring-white/90 dark:ring-gray-800 shadow-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white font-bold text-4xl flex items-center justify-center shadow-inner">
                            {{ $initialChar }}
                        </div>
                    @endif

                    {{-- Status Indicator Dot --}}
                    @if($user->status == \App\Models\User::STATUS_ACTIVE)
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-white dark:border-gray-800 rounded-full shadow-sm" title="สถานะ: ใช้งาน"></span>
                    @else
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-rose-500 border-2 border-white dark:border-gray-800 rounded-full shadow-sm" title="สถานะ: ไม่ใช้งาน"></span>
                    @endif
                </div>

                {{-- Headline Details --}}
                <div class="text-center sm:text-left flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-2">
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white drop-shadow-sm">
                            {{ $user->prefix }} {{ $user->first_name }} {{ $user->last_name }}
                        </h2>
                    </div>

                    <p class="text-blue-100 font-medium text-base sm:text-lg mb-4 flex items-center justify-center sm:justify-start gap-2">
                        <i class="fa-solid fa-briefcase text-sm text-blue-200"></i>
                        {{ $user->position ?: 'ไม่ระบุตำแหน่ง' }}
                    </p>

                    {{-- Badges & Quick Stats --}}
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        {{-- Employee Code --}}
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/20 backdrop-blur-sm text-xs font-mono font-semibold tracking-wider text-white">
                            <i class="fa-solid fa-id-badge text-[11px] opacity-80"></i>
                            รหัส {{ $user->employee_code }}
                        </div>

                        {{-- Role (Rule) --}}
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/90 text-gray-900 text-xs font-bold shadow-sm">
                            <i class="fa-solid fa-shield-halved text-primary"></i>
                            {{ $user->role_label }}
                        </div>

                        {{-- Status Active / Inactive --}}
                        @php
                            $statusOptions = \App\Models\User::getStatusOptions();
                            $statusMeta = $statusOptions[$user->status] ?? ['label' => '-', 'color' => 'gray'];
                        @endphp
                        @if($user->status == \App\Models\User::STATUS_ACTIVE)
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-500/90 text-white text-xs font-semibold shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                {{ $statusMeta['label'] }}
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-rose-500/90 text-white text-xs font-semibold shadow-sm">
                                <i class="fa-solid fa-user-xmark text-xs"></i>
                                {{ $statusMeta['label'] }}
                            </div>
                        @endif

                        {{-- Tenure / อายุงาน --}}
                        @if($user->startwork_date)
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/20 backdrop-blur-sm text-xs font-medium text-white">
                                <i class="fa-regular fa-clock text-xs opacity-80"></i>
                                อายุงาน {{ \Carbon\Carbon::parse($user->startwork_date)->diffForHumans(null, true) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Direct Quick Edit Action (Desktop) --}}
                @if(Auth::check() && Auth::user()->canEdit())
                <div class="hidden sm:flex flex-col gap-2 items-end justify-start self-start">
                    <a href="{{ route('users.edit', $user->id) }}" 
                       class="btn btn-sm bg-white/20 hover:bg-white text-white hover:text-gray-900 border-white/30 backdrop-blur-sm gap-2 font-medium rounded-lg transition-all shadow-sm">
                        <i class="fa-solid fa-pen-to-square"></i> แก้ไขหน้านี้
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- 2. Resignation / Inactive Notice (If applicable) --}}
        @if($user->status == \App\Models\User::STATUS_INACTIVE)
        <div class="bg-rose-50 dark:bg-rose-950/40 border-b border-rose-200 dark:border-rose-900/60 p-4 sm:px-8">
            <div class="flex items-start gap-3 text-rose-800 dark:text-rose-200">
                <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-lg mt-0.5"></i>
                <div class="flex-1 text-sm">
                    <span class="font-bold">พนักงานพ้นสภาพการทำงาน</span>
                    <span class="ml-2 font-semibold">
                        วันที่สิ้นสุด: {{ !empty($user->endwork_date) ? \Carbon\Carbon::parse($user->endwork_date)->format('d/m/Y') : '-' }}
                    </span>
                    @if($user->endwork_comment)
                        <div class="mt-1 text-rose-700 dark:text-rose-300 text-xs bg-white/60 dark:bg-black/20 p-2 rounded-lg border border-rose-200/60 dark:border-rose-900/40">
                            <strong>เหตุผล:</strong> {{ $user->endwork_comment }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- 3. Unified Content Sections Grid --}}
        <div class="p-4 sm:p-8 divide-y divide-base-200 dark:divide-gray-700">

            {{-- SECTION 1: ข้อมูลส่วนตัว (Personal Details) --}}
            <div class="pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-primary dark:text-blue-400 flex items-center justify-center font-bold text-base shadow-sm">
                        <i class="fa-regular fa-address-card"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">ข้อมูลส่วนตัว</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">รายละเอียดข้อมูลพื้นฐานของพนักงาน</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    {{-- คำนำหน้า --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-3.5 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">คำนำหน้า</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->prefix ?: '-' }}
                        </div>
                    </div>

                    {{-- เพศ --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-3.5 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">เพศ</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-1.5">
                            @if($user->sex == 'ชาย')
                                <i class="fa-solid fa-mars text-blue-500 text-xs"></i>
                            @elseif($user->sex == 'หญิง')
                                <i class="fa-solid fa-venus text-pink-500 text-xs"></i>
                            @endif
                            {{ $user->sex ?: '-' }}
                        </div>
                    </div>

                    {{-- ชื่อจริง --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-3.5 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">ชื่อจริง</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->first_name ?: '-' }}
                        </div>
                    </div>

                    {{-- นามสกุล --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-3.5 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">นามสกุล</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->last_name ?: '-' }}
                        </div>
                    </div>

                    {{-- วันที่เริ่มงาน --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-3.5 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">วันที่เริ่มงาน</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-regular fa-calendar-check text-primary/70 text-xs"></i>
                            {{ !empty($user->startwork_date) ? \Carbon\Carbon::parse($user->startwork_date)->format('d/m/Y') : '-' }}
                        </div>
                    </div>

                    {{-- อายุงาน --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-3.5 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">อายุงาน</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            @if($user->startwork_date)
                                {{ \Carbon\Carbon::parse($user->startwork_date)->diffForHumans(null, true) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    {{-- อีเมล --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-3.5 rounded-xl border border-base-200 dark:border-gray-700 col-span-2">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">อีเมล (Email)</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 truncate">
                            <i class="fa-regular fa-envelope text-gray-400 text-xs"></i>
                            <span class="truncate">{{ $user->email ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: ข้อมูลการทำงานและสังกัด (Work & Organization) --}}
            <div class="py-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-base shadow-sm">
                        <i class="fa-solid fa-sitemap"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">ข้อมูลการทำงานและสังกัด</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">รายละเอียดโครงสร้างฝ่าย สายงาน และสถานที่ปฏิบัติงาน</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    {{-- ตำแหน่ง --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700 md:col-span-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">ตำแหน่ง (Position)</div>
                        <div class="text-base font-bold text-primary dark:text-blue-400">
                            {{ $user->position ?: '-' }}
                        </div>
                    </div>

                    {{-- แผนก --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">แผนก (Department)</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->department->department_name ?? '-' }}
                        </div>
                        @if(!empty($user->department->department_fullname))
                            <div class="text-xs text-gray-400 mt-0.5 truncate">{{ $user->department->department_fullname }}</div>
                        @endif
                    </div>

                    {{-- ฝ่าย --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">ฝ่าย (Division)</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->division->division_name ?? '-' }}
                        </div>
                        @if(!empty($user->division->division_fullname))
                            <div class="text-xs text-gray-400 mt-0.5 truncate">{{ $user->division->division_fullname }}</div>
                        @endif
                    </div>

                    {{-- สายงาน --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">สายงาน (Section)</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $user->section->section_name ?? '-' }}
                        </div>
                        @if(!empty($user->section->section_fullname))
                            <div class="text-xs text-gray-400 mt-0.5 truncate">{{ $user->section->section_fullname }}</div>
                        @endif
                    </div>

                    {{-- ประเภทพนักงาน --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">ประเภทพนักงาน</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-user-tag text-xs text-gray-400"></i>
                            {{ $user->employee_type ?: '-' }}
                        </div>
                    </div>

                    {{-- สถานที่ทำงาน --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700 md:col-span-2">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">สถานที่ปฏิบัติงาน (Workplace)</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-rose-500 text-xs"></i>
                            {{ $user->workplace ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: สิทธิ์และการเข้าใช้งานระบบ (System & Access Control) --}}
            <div class="pt-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base shadow-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">สิทธิ์และสถานะในระบบ</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">การกำหนดสิทธิ์ในการเข้าถึงและจัดการข้อมูลระบบ</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    {{-- สิทธิ์ในระบบ (Rule) --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">สิทธิ์ในระบบ (Rule)</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold tracking-wide {{ $user->role_badge }}">
                                <i class="fa-solid fa-shield-halved mr-1.5 text-[10px]"></i>
                                {{ $user->role_label }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2">
                            @if($user->isAdmin())
                                ผู้ดูแลระบบ (เข้าถึงระบบ HR ได้ทั้งหมด)
                            @elseif($user->isEditor())
                                ผู้แก้ไข (ดู/เพิ่ม/แก้ไข - ห้ามลบ)
                            @else
                                ผู้ดูข้อมูล (ดูข้อมูลได้อย่างเดียว)
                            @endif
                        </p>
                    </div>

                    {{-- ระดับพนักงาน (Level User) --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">ระดับพนักงาน (Level User)</div>
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 mt-1">
                            <span class="w-6 h-6 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold border border-indigo-200 dark:border-indigo-800">
                                {{ $user->level_user }}
                            </span>
                            <span>{{ $user->level_user_label }}</span>
                        </div>
                    </div>

                    {{-- สถานะ HR --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">สถานะสิทธิ์ HR</div>
                        @php
                            $hrStatusOptions = \App\Models\User::getHrStatusOptions();
                            $hrLabel = $hrStatusOptions[$user->hr_status]['label'] ?? '-';
                        @endphp
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 mt-1">
                            @if($user->hr_status == \App\Models\User::HR_STATUS_ACTIVE)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                    <i class="fa-solid fa-check text-[10px] mr-1"></i> เป็นฝ่าย HR
                                </span>
                            @else
                                <span class="text-gray-600 dark:text-gray-300 text-sm">
                                    {{ $hrLabel }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- สถานะการทำงาน --}}
                    <div class="bg-base-200/40 dark:bg-gray-700/30 p-4 rounded-xl border border-base-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">สถานะการทำงาน</div>
                        <div class="mt-1">
                            @if($user->status == \App\Models\User::STATUS_ACTIVE)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    ใช้งาน (Active)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                    ไม่ใช้งาน (Resign)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- 4. Footer Action Bar (In-frame) --}}
        <div class="bg-base-200/50 dark:bg-gray-900/40 px-6 sm:px-8 py-4 border-t border-base-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <i class="fa-regular fa-clock"></i>
                <span>ข้อมูลอัปเดตล่าสุดจากระบบ</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" 
                   class="btn btn-ghost btn-sm text-gray-600 dark:text-gray-300 font-medium">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> กลับหน้ารายการ
                </a>
                @if(Auth::check() && Auth::user()->canEdit())
                <a href="{{ route('users.edit', $user->id) }}" 
                   class="btn btn-warning btn-sm text-white font-medium shadow-sm hover:shadow px-5 rounded-lg">
                    <i class="fa-solid fa-pen-to-square mr-1.5"></i> ไปที่หน้าแก้ไขข้อมูล
                </a>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection