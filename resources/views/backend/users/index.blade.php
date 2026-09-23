@extends('layouts.app')
@section('content')

<div>
    @php
        $filterFields = ['keyword', 'employee_code', 'fullname', 'position', 'employee_type', 'status', 'department', 'level_user', 'role', 'hr_status'];
        $hasActiveFilter = false;
        $activeFilterCount = 0;
        foreach ($filterFields as $f) {
            if (request()->filled($f)) {
                $hasActiveFilter = true;
                $activeFilterCount++;
            }
        }
    @endphp

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl text-slate-900 dark:text-white font-black tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-users text-primary text-xl"></i>
                ข้อมูลพนักงาน
            </h1>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full md:w-auto">
            @if(Auth::check() && Auth::user()->canCreate())
            <a href="{{ route('users.create') }}" class="btn btn-success text-white w-full sm:w-auto shadow-sm gap-2 rounded-lg font-medium">
                <i class="fa-solid fa-plus"></i>
                เพิ่มพนักงานใหม่
            </a>
            @endif
            <button type="button" id="toggle-filter" class="btn btn-sm w-full sm:w-auto shadow-sm gap-2 rounded-lg font-medium transition-all {{ $hasActiveFilter ? 'btn-warning text-white' : 'btn-outline border-gray-300 dark:border-gray-600 hover:bg-base-200 dark:hover:bg-gray-700' }}">
                <i class="fa-solid fa-filter"></i> 
                <span>ตัวกรองค้นหา</span>
                @if($activeFilterCount > 0)
                    <span class="badge badge-sm bg-white text-gray-900 font-bold ml-1 border-0">{{ $activeFilterCount }}</span>
                @endif
                <i id="filter-chevron" class="fa-solid fa-chevron-down text-[10px] ml-1 transition-transform duration-200"></i>
            </button>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-error mb-4 shadow-lg text-white rounded-xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Filter Panel --}}
    <form id="filter-form" method="GET" action="{{ route('users.index') }}"
        class="mb-6 rounded-xl sm:rounded-2xl bg-white dark:bg-gray-800 border border-slate-200/90 dark:border-gray-700 shadow-sm transition-all duration-300 relative overflow-hidden {{ $hasActiveFilter ? '' : 'hidden' }}">

        <div class="p-3.5 sm:p-6 space-y-4 sm:space-y-5">
            {{-- Header Row --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-gray-700/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm shadow-sm">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white">ค้นหาและกรองข้อมูลพนักงาน</h3>
                            @if($activeFilterCount > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-primary/10 text-primary dark:bg-primary/20">
                                    {{ $activeFilterCount }} ตัวกรองทำงานอยู่
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-gray-400">กรองข้อมูลตามข้อมูลบุคคล แผนก หรือระดับสิทธิ์การใช้งาน</p>
                    </div>
                </div>

                @if($activeFilterCount > 0)
                <a href="{{ route('users.index') }}" class="btn btn-ghost btn-xs text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 gap-1.5 font-medium rounded-lg">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i> รีเซ็ตตัวกรองทั้งหมด
                </a>
                @endif
            </div>

            {{-- Unified Filter Fields Grid --}}
            <div class="space-y-4">
                {{-- Row 1: ช่องค้นหาเดียว (Unified Keyword Search) --}}
                <div class="form-control">
                    <label class="text-xs font-semibold text-slate-700 dark:text-gray-300 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-magnifying-glass text-primary text-xs"></i>
                        <span>ค้นหาพนักงาน (รหัสพนักงาน / ชื่อ-นามสกุล / ตำแหน่ง)</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="keyword" 
                            placeholder="พิมพ์รหัสพนักงาน เช่น 11668 หรือ ชื่อ-นามสกุล หรือ ตำแหน่งงาน..."
                            value="{{ request('keyword', request('fullname', request('employee_code', request('position')))) }}"
                            class="input input-bordered w-full bg-slate-50/60 dark:bg-gray-700/50 text-sm focus:input-primary rounded-xl border-slate-300 dark:border-gray-600 shadow-sm pl-10 pr-10 h-11" />
                        <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        @if(request('keyword') || request('fullname') || request('employee_code') || request('position'))
                            <a href="{{ route('users.index', request()->except(['keyword', 'fullname', 'employee_code', 'position'])) }}" 
                               class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-white text-sm"
                               title="ล้างคำค้นหา">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Row 2: ตัวเลือกกรองเฉพาะทาง 6 ช่อง วางใน Grid 6 คอลัมน์ที่เท่ากันพอดีเป๊ะ --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 pt-1">
                    
                    {{-- 1. แผนก (Department) --}}
                    <div class="form-control">
                        <label class="text-[11px] font-medium text-slate-600 dark:text-gray-400 mb-1 flex items-center gap-1.5">
                            <i class="fa-regular fa-building text-[10px] text-slate-400"></i> แผนก
                        </label>
                        <select name="department" class="select select-bordered select-sm w-full bg-slate-50/50 dark:bg-gray-700/50 text-xs focus:select-primary rounded-lg border-slate-300 dark:border-gray-600 shadow-sm">
                            <option value="">แผนกทั้งหมด</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ (string)request('department') === (string)$dept->department_id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. สิทธิ์ในระบบ (Rule) --}}
                    <div class="form-control">
                        <label class="text-[11px] font-medium text-slate-600 dark:text-gray-400 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-[10px] text-slate-400"></i> สิทธิ์ (Rule)
                        </label>
                        <select name="role" class="select select-bordered select-sm w-full bg-slate-50/50 dark:bg-gray-700/50 text-xs focus:select-primary rounded-lg border-slate-300 dark:border-gray-600 shadow-sm font-semibold">
                            <option value="" class="font-normal">สิทธิ์ทั้งหมด</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>ADMIN</option>
                            <option value="editor" {{ request('role') === 'editor' ? 'selected' : '' }}>EDITOR</option>
                            <option value="viewer" {{ request('role') === 'viewer' ? 'selected' : '' }}>VIEWER</option>
                        </select>
                    </div>

                    {{-- 3. ระดับพนักงาน (Level) --}}
                    <div class="form-control">
                        <label class="text-[11px] font-medium text-slate-600 dark:text-gray-400 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-award text-[10px] text-slate-400"></i> ระดับ (Level)
                        </label>
                        @php
                            $levelOptions = \App\Models\User::getLevelUserOptions();
                            $selectedLevel = request('level_user');
                        @endphp
                        <select name="level_user" class="select select-bordered select-sm w-full bg-slate-50/50 dark:bg-gray-700/50 text-xs focus:select-primary rounded-lg border-slate-300 dark:border-gray-600 shadow-sm">
                            <option value="">ระดับทั้งหมด</option>
                            @foreach($levelOptions as $value => $meta)
                            <option value="{{ $value }}" {{ (string)$selectedLevel === (string)$value ? 'selected' : '' }}>
                                ระดับ {{ $value }} ({{ $meta['label'] }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. ประเภทพนักงาน --}}
                    <div class="form-control">
                        <label class="text-[11px] font-medium text-slate-600 dark:text-gray-400 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-tag text-[10px] text-slate-400"></i> ประเภท
                        </label>
                        <select name="employee_type" class="select select-bordered select-sm w-full bg-slate-50/50 dark:bg-gray-700/50 text-xs focus:select-primary rounded-lg border-slate-300 dark:border-gray-600 shadow-sm">
                            <option value="">ประเภททั้งหมด</option>
                            <option value="รายเดือน" {{ request('employee_type') === 'รายเดือน' ? 'selected' : '' }}>รายเดือน</option>
                            <option value="รายวัน" {{ request('employee_type') === 'รายวัน' ? 'selected' : '' }}>รายวัน</option>
                        </select>
                    </div>

                    {{-- 5. สถานะพนักงาน --}}
                    <div class="form-control">
                        <label class="text-[11px] font-medium text-slate-600 dark:text-gray-400 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-[10px] text-slate-400"></i> สถานะงาน
                        </label>
                        @php $statusOptions = \App\Models\User::getStatusOptions(); @endphp
                        <select name="status" class="select select-bordered select-sm w-full bg-slate-50/50 dark:bg-gray-700/50 text-xs focus:select-primary rounded-lg border-slate-300 dark:border-gray-600 shadow-sm">
                            <option value="">สถานะทั้งหมด</option>
                            @foreach($statusOptions as $value => $option)
                            @php
                                $label = is_array($option) ? ($option['label'] ?? '') : $option;
                            @endphp
                            <option value="{{ $value }}" {{ (string)request('status') === (string)$value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 6. สถานะฝ่าย HR --}}
                    <div class="form-control">
                        <label class="text-[11px] font-medium text-slate-600 dark:text-gray-400 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-shield text-[10px] text-slate-400"></i> สถานะ HR
                        </label>
                        @php
                            $hrStatusOptions = \App\Models\User::getHrStatusOptions();
                            $selectedHrStatus = request('hr_status');
                        @endphp
                        <select name="hr_status" class="select select-bordered select-sm w-full bg-slate-50/50 dark:bg-gray-700/50 text-xs focus:select-primary rounded-lg border-slate-300 dark:border-gray-600 shadow-sm">
                            <option value="">HR ทั้งหมด</option>
                            @foreach($hrStatusOptions as $value => $option)
                            @php
                                $label = is_array($option) ? ($option['label'] ?? '') : $option;
                            @endphp
                            <option value="{{ $value }}" {{ (string)$selectedHrStatus === (string)$value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- Footer Action Bar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-gray-700/80">
                <div class="text-xs text-slate-400 dark:text-gray-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-info-circle text-[11px]"></i>
                    <span>สามารถเลือกกรองข้อมูลได้หลายเงื่อนไขพร้อมกัน หรือพิมพ์บางส่วนของชื่อ/รหัส</span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm text-xs font-medium text-slate-600 hover:text-slate-900 dark:text-gray-400 dark:hover:text-white rounded-lg px-3">
                        <i class="fa-solid fa-rotate-left mr-1.5"></i> ล้างค่า
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm text-white font-medium px-6 rounded-lg shadow-sm hover:shadow transition-all gap-2">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> ค้นหาข้อมูล
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Main Table Container (Image 2 Design) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-sm overflow-hidden p-3 sm:p-6 relative" id="table-wrap">

        <div id="loader" class="hidden absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm flex items-center justify-center z-20">
            <span class="loading loading-spinner loading-lg text-primary"></span>
        </div>

        <!-- Controls Bar: Show per page & Search (Matches Image 2) -->
        <form method="GET" action="{{ route('users.index') }}" id="search-bar-form" class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            @foreach(request()->except(['fullname', 'per_page', 'page']) as $key => $val)
                @if(is_array($val))
                    @foreach($val as $v)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endif
            @endforeach

            <!-- Left: Rows per page selector -->
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>แสดง</span>
                <select name="per_page" onchange="this.form.submit()" class="select select-bordered select-xs text-xs font-bold text-gray-700 dark:text-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md cursor-pointer px-2 py-0.5">
                    <option value="10" {{ request('per_page', 50) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 50) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 50) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>แถว</span>
            </div>

            <!-- Right: Search box -->
            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium w-full sm:w-auto">
                <label for="search-fullname" class="whitespace-nowrap font-medium">ค้นหา :</label>
                <div class="relative w-full sm:w-64">
                    <input type="text" id="search-fullname" name="fullname" value="{{ request('fullname') }}" placeholder="" class="input input-bordered input-sm text-xs w-full dark:bg-gray-700 border-indigo-400 dark:border-indigo-500 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @if(request('fullname'))
                        <a href="{{ route('users.index', request()->except('fullname')) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table matching Image 2 -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider bg-gray-50/50 dark:bg-gray-900/30">
                        <th class="py-3.5 px-4 font-bold">
                            <div class="flex items-center justify-between cursor-pointer">
                                <span>USER</span>
                                <i class="fa-solid fa-arrows-up-down text-[10px] opacity-40"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4 font-bold">
                            <div class="flex items-center justify-between cursor-pointer">
                                <span>DEPARTMENT</span>
                                <i class="fa-solid fa-arrows-up-down text-[10px] opacity-40"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4 font-bold">
                            <div class="flex items-center justify-between cursor-pointer">
                                <span>EMAIL</span>
                                <i class="fa-solid fa-arrows-up-down text-[10px] opacity-40"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4 font-bold">
                            <div class="flex items-center justify-between cursor-pointer">
                                <span>ROLE</span>
                                <i class="fa-solid fa-arrows-up-down text-[10px] opacity-40"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4 font-bold">
                            <div class="flex items-center justify-between cursor-pointer">
                                <span>STATUS</span>
                                <i class="fa-solid fa-arrows-up-down text-[10px] opacity-40"></i>
                            </div>
                        </th>
                        <th class="py-3.5 px-4 font-bold text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer">
                                <span>ACTIONS</span>
                                <i class="fa-solid fa-arrows-up-down text-[10px] opacity-40"></i>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="users-body" class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @php
                        $pastelColors = [
                            'bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-300',
                            'bg-sky-100 text-sky-600 dark:bg-sky-950/60 dark:text-sky-300',
                            'bg-pink-100 text-pink-600 dark:bg-pink-950/60 dark:text-pink-300',
                            'bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-300',
                            'bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300',
                            'bg-teal-100 text-teal-600 dark:bg-teal-950/60 dark:text-teal-300',
                        ];
                    @endphp

                    @forelse($users as $index => $user)
                        @php
                            $firstLetter = mb_substr($user->firstname ?? '', 0, 1);
                            $secondLetter = mb_substr($user->lastname ?? '', 0, 1);
                            if (!$firstLetter && !$secondLetter) {
                                $initials = 'US';
                            } else {
                                $initials = mb_strtoupper($firstLetter . $secondLetter);
                            }
                            $colorClass = $pastelColors[$index % count($pastelColors)];
                            
                            $email = $user->email ?? (strtolower($user->firstname) ? strtolower($user->firstname) . '.' . strtolower(substr($user->lastname ?? '', 0, 2)) . '@kumwell.com' : '—');
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition-colors">
                            <!-- USER -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $colorClass }}">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-800 dark:text-gray-100 text-sm leading-tight">
                                            <a href="{{ route('users.show', $user->id) }}" class="hover:text-kumwell-red transition-colors">{{ $user->fullname }}</a>
                                        </div>
                                        <div class="text-[11px] text-gray-400 font-normal mt-0.5">
                                            @ {{ $user->employee_code }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- DEPARTMENT -->
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-gray-700 dark:text-gray-300 text-xs">
                                    {{ $user->department->department_name ?? '—' }}
                                </span>
                            </td>

                            <!-- EMAIL -->
                            <td class="py-3.5 px-4">
                                <span class="font-normal text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $email }}
                                </span>
                            </td>

                            <!-- ROLE -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wide {{ $user->role_badge }}">
                                    {{ $user->role_label }}
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td class="py-3.5 px-4">
                                @if((string)$user->status === '1' || strtolower($user->status_label) === 'ใช้งาน' || strtolower($user->status_label) === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                                        Offline
                                    </span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="dropdown dropdown-left dropdown-end inline-block">
                                    <button type="button" tabindex="0" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>
                                    <ul tabindex="0" class="dropdown-content z-30 menu p-1.5 shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl w-40 text-xs space-y-0.5">
                                        {{-- ดูข้อมูล: Everyone (ADMIN, EDITOR, VIEWER) --}}
                                        <li>
                                            <a href="{{ route('users.show', $user->id) }}" class="flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:text-kumwell-red py-1.5">
                                                <i class="fa-solid fa-eye text-sky-500 w-4"></i> ดูข้อมูล
                                            </a>
                                        </li>

                                        {{-- แก้ไขข้อมูล: ADMIN & EDITOR (ห้าม VIEWER) --}}
                                        @if(Auth::check() && Auth::user()->canEdit())
                                        <li>
                                            <a href="{{ route('users.edit', $user->id) }}" class="flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 py-1.5">
                                                <i class="fa-solid fa-pen-to-square text-amber-500 w-4"></i> แก้ไขข้อมูล
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center border-b-0">
                                <x-empty-state icon="users" title="ไม่พบข้อมูลพนักงาน" description="ไม่มีข้อมูลพนักงานที่ตรงกับเงื่อนไขการค้นหาของคุณ" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4" id="pagination">
        {{ $users->links() }}
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Add User Modal Logic (Specific to this page) ---
    const addUserForm = document.getElementById('add_user_form');
    const addUserModal = document.getElementById('add_user_modal');
    const modalErrors = document.getElementById('modal_errors');
    const confirmAddUserBtn = document.getElementById('confirm-add-user');

    if (confirmAddUserBtn && addUserForm) {
        confirmAddUserBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (addUserModal && typeof addUserModal.close === 'function') addUserModal.close();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการบันทึกข้อมูล?',
                    text: 'คุณต้องการบันทึกข้อมูลพนักงานใหม่ใช่หรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'บันทึก',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        addUserForm.requestSubmit();
                    } else {
                        if (addUserModal && typeof addUserModal.showModal === 'function') addUserModal.showModal();
                    }
                });
            } else {
                if (confirm('คุณต้องการบันทึกข้อมูลพนักงานใหม่ใช่หรือไม่?')) {
                    addUserForm.requestSubmit();
                } else {
                    if (addUserModal) addUserModal.showModal();
                }
            }
        });
    }

    let refreshTable;

    if (addUserForm) {
        addUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (modalErrors) {
                modalErrors.classList.add('hidden');
                modalErrors.innerHTML = '';
            }

            const formData = new FormData(this);
            const action = this.getAttribute('action');
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: formData,
                });

                if (response.status === 422) {
                    if (addUserModal && !addUserModal.open) addUserModal.showModal();
                    const errorData = await response.json();
                    let errorHtml = '<ul class="list-disc list-inside">';
                    for (const key in errorData.errors) {
                        errorData.errors[key].forEach(error => { errorHtml += `<li>${error}</li>`; });
                    }
                    errorHtml += '</ul>';
                    if (modalErrors) {
                        modalErrors.innerHTML = errorHtml;
                        modalErrors.classList.remove('hidden');
                    }
                    const modalBox = addUserModal?.querySelector?.('.modal-box');
                    if (modalBox) modalBox.scrollTop = 0;

                } else if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                } else {
                    this.reset();
                    if (addUserModal && addUserModal.open) addUserModal.close();

                    if (refreshTable) refreshTable();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: 'เพิ่มพนักงานเรียบร้อยแล้ว',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert('เพิ่มพนักงานเรียบร้อยแล้ว');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (addUserModal && !addUserModal.open) addUserModal.showModal();
                if (modalErrors) {
                    modalErrors.innerHTML = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล โปรดลองใหม่อีกครั้ง';
                    modalErrors.classList.remove('hidden');
                }
            }
        });
    }

    // --- Filter Toggle with localStorage persistence ---
    const btn = document.getElementById('toggle-filter');
    const panel = document.getElementById('filter-form');
    const chevron = document.getElementById('filter-chevron');

    // Restore state from localStorage if not explicitly forced by server active filter
    const savedFilterState = localStorage.getItem('users_filter_open');
    if (panel) {
        if (savedFilterState === 'true' && panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
        } else if (savedFilterState === 'false' && !{{ $hasActiveFilter ? 'true' : 'false' }}) {
            panel.classList.add('hidden');
        }

        if (chevron) {
            chevron.style.transform = panel.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }

    if (btn && panel) {
        btn.addEventListener('click', function() {
            const isCurrentlyHidden = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            const isNowOpen = !panel.classList.contains('hidden');
            
            localStorage.setItem('users_filter_open', isNowOpen ? 'true' : 'false');

            if (chevron) {
                chevron.style.transform = isNowOpen ? 'rotate(180deg)' : 'rotate(0deg)';
            }

            if (isNowOpen) {
                panel.animate([
                    { opacity: 0, transform: 'translateY(-8px)' },
                    { opacity: 1, transform: 'translateY(0)' }
                ], { duration: 250, easing: 'ease-out' });
            }
        });
    }

    // SweetAlert delete confirmation
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนได้',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ลบเลย',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            } else {
                if (confirm('ยืนยันการลบ?')) form.submit();
            }
        });
    });
});
</script>
@endsection