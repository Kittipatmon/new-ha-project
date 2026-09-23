@extends('layouts.recruitment.app')

@section('title', 'จัดการประกาศรับสมัครงาน')

@section('content')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .recruitment-table-font, .recruitment-table-font * {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }

        /* Hide default DataTables controls since custom toolbar exists */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }

        /* ── DataTables modern full-width layout ────────────────── */
        table.dataTable,
        #dataTablePosts {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
        }

        /* Bottom info & pagination bar (centered pagination, full width expand) */
        .dataTables_bottom_bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-top: 1rem;
            padding: 0.25rem 0.25rem 0;
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .dataTables_bottom_bar {
                display: grid;
                grid-template-columns: 1fr auto 1fr;
                align-items: center;
                gap: 1rem;
            }
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 0.8125rem;
            color: #64748b;
            padding: 0 !important;
            margin: 0 !important;
            text-align: left;
            white-space: nowrap;
        }
        .dark .dataTables_wrapper .dataTables_info {
            color: #94a3b8;
        }

        /* Center pagination controls */
        .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 0 !important;
            margin: 0 auto !important;
            float: none !important;
            text-align: center !important;
        }

        /* Modern pagination buttons */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 0.6rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            color: #475569 !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            margin: 0 1px !important;
            user-select: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #F2704E !important;
            color: #ffffff !important;
            border-color: #F2704E !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 4px rgba(242, 112, 78, 0.25) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            color: #cbd5e1 !important;
            background: transparent !important;
            border-color: transparent !important;
            cursor: not-allowed !important;
            opacity: 0.4 !important;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #1f2937 !important;
            border-color: #374151 !important;
            color: #94a3b8 !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #374151 !important;
            color: #f8fafc !important;
            border-color: #4b5563 !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: transparent !important;
            border-color: transparent !important;
            color: #4b5563 !important;
        }

        /* ── Section card ─────────────────────── */
        .section-card {
            background: #fff;
            color: #1e293b;
            border-radius: 1.25rem;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.04), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }
        .dark .section-card { background: #1f2937; color: #f1f5f9; border-color: #374151; }

        /* ── Status badges ─────────────────────── */
        .badge { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            gap: 0.35rem;
            padding: 0.3rem 0.85rem; 
            border-radius: 9999px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            letter-spacing: 0.02em; 
            white-space: nowrap;
        }
        .badge-green  { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .badge-red    { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .badge-gray   { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        .badge-amber  { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .badge-blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }

        /* ── Action buttons ─────────────────────── */
        .btn-doc {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem;
            border-radius: 0.625rem;
            color: #64748b;
            background: #f1f5f9;
            transition: all 0.2s ease;
        }
        .btn-doc:hover { background: #e2e8f0; color: #0f172a; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08); }

        /* ── DataTable layout and spacing ─────────────────── */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }
        
        table.dataTable { 
            border-collapse: separate !important; 
            border-spacing: 0 !important;
            width: 100% !important;
            table-layout: auto;
        }

        table.dataTable thead th {
            background-color: #f8fafc;
            font-size: 0.775rem;
            font-weight: 700;
            letter-spacing: 0.025em;
            color: #475569 !important;
            padding: 1.1rem 1.5rem !important;
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle;
            white-space: nowrap;
        }
        table.dataTable thead th:first-child {
            padding-left: 2rem !important;
        }
        table.dataTable thead th:last-child {
            padding-right: 2rem !important;
        }
        .dark table.dataTable thead th {
            background-color: #1e293b;
            color: #94a3b8 !important;
            border-bottom-color: #334155 !important;
        }

        table.dataTable tbody td { 
            padding: 1.15rem 1.5rem !important; 
            font-size: 0.845rem; 
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            line-height: 1.45;
            color: #1e293b;
        }
        table.dataTable tbody td:first-child {
            padding-left: 2rem !important;
        }
        table.dataTable tbody td:last-child {
            padding-right: 2rem !important;
        }
        .dark table.dataTable tbody td {
            color: #f1f5f9;
            border-bottom-color: #374151;
        }

        table.dataTable tbody tr {
            transition: background-color 0.15s ease;
        }
        table.dataTable tbody tr:hover { 
            background-color: #f8fafc !important; 
        }
        .dark table.dataTable tbody tr:hover { 
            background-color: #1f2937 !important; 
        }

        /* ── Cancelled / Closed row styling ("สีช่องที่ยกเลิกทึบ - โทนอ่อนละมุน") ─────────── */
        table.dataTable tbody tr.row-closed,
        table.dataTable tbody tr.row-closed > td {
            background-color: #eaeff4 !important; /* Soft, delicate cool slate tint ("สีอ่อนๆ") */
            color: #475569 !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .dark table.dataTable tbody tr.row-closed,
        .dark table.dataTable tbody tr.row-closed > td {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
            border-bottom: 1px solid #334155 !important;
        }
        table.dataTable tbody tr.row-closed:hover,
        table.dataTable tbody tr.row-closed:hover > td {
            background-color: #e2e8f0 !important;
        }
        .dark table.dataTable tbody tr.row-closed:hover,
        .dark table.dataTable tbody tr.row-closed:hover > td {
            background-color: #243248 !important;
        }
        table.dataTable tbody tr.row-closed td:first-child {
            border-left: 3px solid #f87171 !important; /* Soft pastel red accent stripe */
        }
        table.dataTable tbody tr.row-closed a {
            color: #475569 !important;
        }
        .dark table.dataTable tbody tr.row-closed a {
            color: #94a3b8 !important;
        }
        table.dataTable tbody tr.row-closed a:hover {
            color: #0f172a !important;
        }

        /* ── Pagination & Info ─────────────────── */
        .dataTables_wrapper {
            margin-top: 0.5rem;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1rem !important;
            display: flex !important;
            align-items: center;
            justify-content: flex-end;
            gap: 0.25rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem !important;
            padding: 0.35rem 0.85rem !important;
            margin: 0 0.15rem;
            font-size: 0.825rem;
            font-weight: 500;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            cursor: pointer !important;
            transition: all 0.15s;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #eef2ff !important;
            border-color: #c7d2fe !important;
            color: #4338ca !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #fff !important;
            font-weight: 700;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.4;
            cursor: not-allowed !important;
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.825rem;
            color: #64748b !important;
            padding-top: 1.25rem !important;
        }
    </style>

    <div class="recruitment-table-font max-w-[1700px] w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 space-y-6 pb-16">
        
        <!-- Top Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold dark:text-white text-gray-800 tracking-tight">ประกาศรับสมัครงาน</h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Job Postings · จัดการตำแหน่งงานที่เปิดรับสมัคร</p>
            </div>
            <a href="{{ route('backend.recruitment.posts.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-kumwell-red hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-md active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>สร้างประกาศใหม่</span>
            </a>
        </div>

        {{-- SECTION CARD : ALL JOB POSTINGS (Matching รูปที่ 1) --}}
        <div class="section-card">
            <div class="px-6 pt-6 pb-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                รายการประกาศรับสมัครงานทั้งหมด
                                <span id="postsCountBadge" class="text-sm font-normal text-gray-400">({{ $posts->count() }} รายการ)</span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">All Job Postings (แบบตารางจัดการง่าย สะดวกต่อการค้นหาและแก้ไข)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700"></div>

            <div class="p-4 sm:p-6">
                <!-- Filters Bar (Matching รูปที่ 1) -->
                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <div class="relative flex-1 min-w-[220px] max-w-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="customSearch" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white placeholder-gray-400" placeholder="ค้นหาประกาศ...">
                    </div>
                    
                    <select id="filterDepartment" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                        <option value="">ทุกฝ่าย / แผนก</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_fullname }}">{{ $dept->department_fullname }}</option>
                        @endforeach
                    </select>

                    <select id="filterStatus" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer">
                        <option value="">ทุกสถานะ</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                        <option value="draft">Draft</option>
                    </select>

                    <select id="filterPageSize" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-full sm:w-auto cursor-pointer" title="จำนวนรายการต่อหน้า">
                        <option value="5">แสดง 5 รายการ</option>
                        <option value="10" selected>แสดง 10 รายการ</option>
                        <option value="25">แสดง 25 รายการ</option>
                        <option value="50">แสดง 50 รายการ</option>
                        <option value="-1">แสดงทั้งหมด</option>
                    </select>

                    <button type="button" id="btnResetFilters" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/80 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors cursor-pointer" title="ล้างค่าตัวกรองทั้งหมด">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>ล้างตัวกรอง</span>
                    </button>
                </div>

                <!-- Table -->
                <table id="dataTablePosts" class="display responsive nowrap w-full text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th class="all whitespace-nowrap text-center">ประเภท</th>
                            <th class="all whitespace-nowrap text-left">ID</th>
                            <th class="all whitespace-nowrap text-left">ชื่อประกาศ</th>
                            <th class="whitespace-nowrap text-left">ฝ่าย / แผนก</th>
                            <th class="whitespace-nowrap text-center">จำนวนรับ</th>
                            <th class="whitespace-nowrap text-center">ยอดกดดู</th>
                            <th class="whitespace-nowrap text-center">ระยะเวลาประกาศ</th>
                            <th class="all whitespace-nowrap text-center">สถานะ</th>
                            <th class="text-center all whitespace-nowrap" style="width: 80px;">จัดการ</th>
                        </tr>
                    </thead>
                        <tbody>
                            @foreach($posts as $post)
                                @php
                                    $isExpired = $post->end_date && $post->end_date->endOfDay()->isPast();
                                    $isClosed = ($post->publish_status === 'closed' || $isExpired);
                                    $deptName = $post->department?->department_fullname ?: ($post->department?->department_name ?: '-');
                                    
                                    // Status key for filtering
                                    $statusKey = $post->publish_status;
                                    if ($isExpired || $post->publish_status === 'closed') {
                                        $statusKey = 'closed';
                                    }
                                @endphp
                                <tr data-status="{{ $statusKey }}" data-department="{{ $deptName }}" class="{{ $isClosed ? 'row-closed' : '' }}">
                                    <!-- ประเภท -->
                                    <td class="text-center whitespace-nowrap">
                                        <span class="badge {{ $isClosed ? 'badge-gray' : 'badge-amber' }} shadow-2xs">ประกาศรับสมัคร</span>
                                    </td>

                                    <!-- ID -->
                                    <td data-order="{{ $post->id }}" class="text-left font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        @if($isClosed)
                                            <a href="{{ $post->slug ? route('recruitment.show', $post->slug) : '#' }}" class="font-mono text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:underline" title="ดูรายละเอียดประกาศ (ยกเลิกแล้ว - ดูได้อย่างเดียว)">
                                                #JOB-{{ str_pad($post->id, 5, '0', STR_PAD_LEFT) }}
                                            </a>
                                        @else
                                            <a href="{{ route('backend.recruitment.posts.edit', $post->id) }}" class="font-mono text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 hover:underline" title="แก้ไขประกาศ">
                                                #JOB-{{ str_pad($post->id, 5, '0', STR_PAD_LEFT) }}
                                            </a>
                                        @endif
                                    </td>

                                    <!-- ชื่อประกาศ -->
                                    <td class="text-left whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-gray-100 flex items-center gap-1.5 flex-wrap">
                                            @if($isClosed)
                                                <a href="{{ $post->slug ? route('recruitment.show', $post->slug) : '#' }}" class="font-semibold text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:underline transition-colors" title="ดูรายละเอียดประกาศ (ยกเลิกแล้ว - ดูได้อย่างเดียว)">
                                                    {{ $post->title }}
                                                </a>
                                            @else
                                                <a href="{{ route('backend.recruitment.posts.edit', $post->id) }}" class="font-semibold hover:text-kumwell-red hover:underline transition-colors" title="แก้ไขประกาศ">
                                                    {{ $post->title }}
                                                </a>
                                            @endif
                                            @if(($post->urgency ?? 'urgent') === 'very_urgent')
                                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40 text-[10px] font-bold">
                                                    ⚡ ด่วนมาก
                                                </span>
                                            @elseif(($post->urgency ?? 'urgent') === 'urgent')
                                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 border border-red-200 dark:border-red-800/40 text-[10px] font-bold">
                                                    🔥 ด่วน
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ $post->position_name ?: ($post->jobPosition?->position_name ?? '-') }}
                                        </div>
                                    </td>

                                    <!-- ฝ่าย / แผนก -->
                                    <td class="text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                            </svg>
                                            <span>{{ $deptName }}</span>
                                        </div>
                                    </td>

                                    <!-- จำนวนรับ -->
                                    <td class="text-center whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                        {{ $post->vacancy ?? 1 }}
                                    </td>

                                    <!-- ยอดกดดู -->
                                    <td class="text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200 dark:border-sky-800/40 tabular-nums">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            {{ number_format($post->views ?? 0) }}
                                        </span>
                                    </td>

                                    <!-- ระยะเวลาประกาศ -->
                                    <td class="text-center whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                            </svg>
                                            <span>
                                                {{ $post->start_date ? $post->start_date->format('d/m/Y') : '-' }} ถึง {{ $post->end_date ? $post->end_date->format('d/m/Y') : 'ไม่มีกำหนด' }}
                                            </span>
                                        </div>
                                        @if($isExpired)
                                            <div class="text-[11px] text-red-500 font-semibold mt-0.5">(หมดเวลา)</div>
                                        @endif
                                    </td>

                                    <!-- สถานะ -->
                                    <td class="text-center whitespace-nowrap">
                                        @if($post->publish_status === 'published' && !$isExpired)
                                            <span class="badge badge-green shadow-2xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Published</span>
                                            </span>
                                        @elseif($post->publish_status === 'closed' || $isExpired)
                                            <span class="badge badge-red shadow-2xs font-bold">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Closed (ยกเลิกแล้ว)</span>
                                            </span>
                                        @else
                                            <span class="badge badge-gray shadow-2xs">
                                                <span>{{ ucfirst($post->publish_status) }}</span>
                                            </span>
                                        @endif
                                    </td>

                                    <!-- จัดการ -->
                                    <td class="text-center whitespace-nowrap">
                                        <div class="relative inline-block text-left">
                                            <button type="button" class="action-dropdown-btn p-1.5 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60 rounded-lg transition-colors inline-flex items-center justify-center focus:outline-none" title="จัดการ" onclick="toggleActionDropdown(this, event)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                                </svg>
                                            </button>

                                            <div class="action-dropdown-menu hidden fixed z-[99999] w-36 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 py-1.5 text-[13px] font-sans">
                                                <!-- ดูรายละเอียดประกาศ (Details) -->
                                                <a href="{{ $post->slug ? route('recruitment.show', $post->slug) : '#' }}" class="flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span>Details</span>
                                                </a>

                                                @if(!$isClosed)
                                                    <!-- แก้ไขประกาศ (Edit) - ปรากฏเฉพาะประกาศที่ยังไม่ยกเลิก -->
                                                    <a href="{{ route('backend.recruitment.posts.edit', $post->id) }}" class="flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        <span>Edit</span>
                                                    </a>

                                                    @if(Auth::check() && Auth::user()->canDelete())
                                                    <!-- ยกเลิกประกาศ (Cancel) - กด Delete ไม่ได้ แต่ยกเลิกได้ -->
                                                    <form action="{{ route('backend.recruitment.posts.destroy', $post->id) }}" method="POST" class="cancel-post-form m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn-cancel-post w-full flex items-center gap-3 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 dark:hover:text-amber-400 transition-colors text-left">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 group-hover:text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                            <span>Cancel</span>
                                                        </button>
                                                    </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Global Dropdown Handler - Defined immediately to avoid timing/dependency issues
        window.toggleActionDropdown = function(btn, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const menu = btn.nextElementSibling;
            if (!menu) return;

            const isCurrentlyHidden = menu.classList.contains('hidden');

            // Close all open action menus first
            document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                m.classList.add('hidden');
            });

            if (isCurrentlyHidden) {
                menu.classList.remove('hidden');

                const rect = btn.getBoundingClientRect();
                const menuWidth = 144;
                const menuHeight = menu.offsetHeight || 135;

                // Horizontal positioning (align right edge with button)
                let left = rect.right - menuWidth;
                if (left < 10) left = 10;
                menu.style.left = left + 'px';

                // Vertical positioning (flip up if not enough space below)
                const spaceBelow = window.innerHeight - rect.bottom;
                if (spaceBelow < menuHeight + 10 && rect.top > menuHeight + 10) {
                    menu.style.top = 'auto';
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
                } else {
                    menu.style.bottom = 'auto';
                    menu.style.top = (rect.bottom + 4) + 'px';
                }
            }
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown-menu') && !e.target.closest('.action-dropdown-btn')) {
                document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                    m.classList.add('hidden');
                });
            }
        });

        // Close dropdown when scrolling any container or window
        window.addEventListener('scroll', function() {
            document.querySelectorAll('.action-dropdown-menu:not(.hidden)').forEach(function(m) {
                m.classList.add('hidden');
            });
        }, true);

        // Close when clicking any link inside dropdown
        document.addEventListener('click', function(e) {
            if (e.target.closest('.action-dropdown-menu a')) {
                document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                    m.classList.add('hidden');
                });
            }
        });

        // Cancel post confirmation with SweetAlert ("กด delete ไม่ได้ แต่ยกเลิกได้")
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-cancel-post, .btn-delete-post');
            if (!btn) return;

            e.preventDefault();
            const form = btn.closest('form');
            document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                m.classList.add('hidden');
            });

            if (window.Swal) {
                Swal.fire({
                    title: 'ยืนยันการยกเลิกประกาศงาน?',
                    text: 'เมื่อยกเลิกแล้ว ประกาศจะถูกปิดรับสมัครทันที และไม่สามารถแก้ไขข้อมูลได้อีก (ดูได้อย่างเดียว)',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'ใช่, ยกเลิกประกาศ',
                    cancelButtonText: 'ย้อนกลับ',
                    customClass: {
                        popup: 'recruitment-table-font rounded-2xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('ยืนยันการยกเลิกประกาศงาน? เมื่อยกเลิกแล้วจะไม่สามารถแก้ไขข้อมูลได้อีก')) {
                    form.submit();
                }
            }
        });

        // Flash message notifications
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: "{{ session('success') }}",
                        timer: 2500,
                        showConfirmButton: false,
                        customClass: { popup: 'recruitment-table-font rounded-2xl' }
                    });
                }
            });
        @endif
        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: "{{ session('error') }}",
                        timer: 3500,
                        showConfirmButton: false,
                        customClass: { popup: 'recruitment-table-font rounded-2xl' }
                    });
                }
            });
        @endif
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = null;
            const dtOptions = {
                dom: '<"overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs"t><"dataTables_bottom_bar"ip>',
                autoWidth: false,
                responsive: false,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "ทั้งหมด"]],
                order: [[1, 'desc']],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
                    paginate: {
                        first: "«",
                        previous: "‹",
                        next: "›",
                        last: "»"
                    }
                }
            };

            try {
                if (typeof window.DataTable === 'function') {
                    table = new window.DataTable('#dataTablePosts', dtOptions);
                } else if (window.$ && typeof window.$.fn.DataTable === 'function') {
                    table = $('#dataTablePosts').DataTable(dtOptions);
                }
            } catch (err) {
                console.warn('DataTable init notice:', err);
            }

            if (table) {
                // Custom search box
                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                // Page size dropdown change
                $('#filterPageSize').on('change', function() {
                    const len = parseInt(this.value, 10);
                    table.page.len(len).draw();
                });

                // Filter logic
                function applyFilters() {
                    const deptVal = $('#filterDepartment').val();
                    const statusVal = $('#filterStatus').val();

                    const filterFn = function(settings, data, dataIndex) {
                        const row = $(table.row(dataIndex).node());
                        const rowDept = row.attr('data-department') || '';
                        const rowStatus = row.attr('data-status') || '';

                        if (deptVal && !rowDept.includes(deptVal)) return false;
                        if (statusVal && rowStatus !== statusVal) return false;

                        return true;
                    };

                    if (window.DataTable && window.DataTable.ext) {
                        window.DataTable.ext.search = [filterFn];
                    } else if (window.$.fn && window.$.fn.dataTable) {
                        window.$.fn.dataTable.ext.search = [filterFn];
                    }

                    table.draw();
                    updateCount();
                }

                $('#filterDepartment, #filterStatus').on('change', applyFilters);

                // Reset filters
                $('#btnResetFilters').on('click', function() {
                    $('#customSearch').val('');
                    $('#filterDepartment').val('');
                    $('#filterStatus').val('');
                    $('#filterPageSize').val('10');
                    table.page.len(10);
                    table.search('');
                    if (window.DataTable && window.DataTable.ext) {
                        window.DataTable.ext.search = [];
                    } else if (window.$.fn && window.$.fn.dataTable) {
                        window.$.fn.dataTable.ext.search = [];
                    }
                    table.draw();
                    updateCount();
                });

                function updateCount() {
                    const visibleRows = table.rows({ filter: 'applied' }).count();
                    $('#postsCountBadge').text(`(${visibleRows} รายการ)`);
                }

                table.on('draw', function() {
                    document.querySelectorAll('.action-dropdown-menu').forEach(function(m) {
                        m.classList.add('hidden');
                    });
                    updateCount();
                });
            }
        });
    </script>
@endpush