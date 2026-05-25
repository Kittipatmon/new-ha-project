@extends('layouts.sidebar')
@section('title', 'รายการข้อเสนอแนะและร้องเรียน')
@section('content')
    <div class="container mx-auto px-4 py-3">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex-grow">
                รายการร้องเรียน
            </h1>
            <button type="button" id="toggle-filter" class="btn btn-warning btn-sm w-full md:w-auto shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
        </div>

        <!-- Error/Success Display -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500 rounded-r-xl">
                <p class="text-sm text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 rounded-r-xl">
                <p class="text-sm text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @php
            $hasFilter = request()->hasAny(['search', 'complaint_type', 'status', 'start_date', 'end_date']);
        @endphp

        <!-- Filter Section -->
        <form action="{{ route('suggestion.list') }}" method="GET" id="filter-form"
            class="mb-6 border border-gray-200 rounded-xl p-5 bg-white dark:bg-gray-800 dark:border-gray-700 shadow-sm transition-all duration-300 {{ $hasFilter ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end mb-4">
                <!-- Search Input -->
                <div class="form-control">
                    <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ค้นหา</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="เรื่อง, ผู้ร้องเรียน, เบอร์โทร"
                        class="input input-bordered input-sm w-full dark:bg-gray-700 placeholder-gray-400">
                </div>

                <!-- Type Select -->
                <div class="form-control">
                    <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ประเภท</span>
                    <select name="complaint_type" class="select select-bordered select-sm w-full dark:bg-gray-700">
                        <option value="">ทั้งหมด</option>
                        <option value="self" {{ request('complaint_type') == 'self' ? 'selected' : '' }}>
                            ร้องเรียนด้วยตนเอง</option>
                        <option value="other" {{ request('complaint_type') == 'other' ? 'selected' : '' }}>
                            ร้องเรียนแทนผู้อื่น</option>
                        <option value="phone" {{ request('complaint_type') == 'phone' ? 'selected' : '' }}>
                            ร้องเรียนทางโทรศัพท์</option>
                    </select>
                </div>

                <!-- Status Select -->
                <div class="form-control">
                    <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">สถานะ</span>
                    <select name="status" class="select select-bordered select-sm w-full dark:bg-gray-700">
                        <option value="">ทั้งหมด</option>
                        <option value="รอรับเรื่องคำร้อง" {{ request('status') == 'รอรับเรื่องคำร้อง' ? 'selected' : '' }}>
                            รอรับเรื่องคำร้อง</option>
                        <option value="รับเรื่องคำร้องแล้ว" {{ request('status') == 'รับเรื่องคำร้องแล้ว' ? 'selected' : '' }}>รับเรื่องคำร้องแล้ว</option>
                        <option value="ตรวจสอบ" {{ request('status') == 'ตรวจสอบ' ? 'selected' : '' }}>ตรวจสอบ</option>
                        <option value="ดำเนินการ" {{ request('status') == 'ดำเนินการ' ? 'selected' : '' }}>ดำเนินการ</option>
                        <option value="เสร็จสิ้น" {{ request('status') == 'เสร็จสิ้น' ? 'selected' : '' }}>เสร็จสิ้น</option>
                        <option value="ปิดเรื่อง" {{ request('status') == 'ปิดเรื่อง' ? 'selected' : '' }}>ปิดเรื่อง</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="form-control">
                    <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ตั้งแต่วันที่</span>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="input input-bordered input-sm w-full dark:bg-gray-700">
                </div>
                <div class="form-control">
                    <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ถึงวันที่</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="input input-bordered input-sm w-full dark:bg-gray-700">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end mt-4">
                <button type="button" id="clear-filter" class="btn btn-ghost btn-sm text-gray-500">
                    <i class="fa-solid fa-rotate-left mr-1"></i> ล้างค่า
                </button>
            </div>
        </form>

        <div id="table-wrap" data-show-pattern="{{ url('suggestion') }}/:id/show"
            data-edit-pattern="{{ url('suggestion') }}/:id/edit" data-destroy-pattern="{{ url('suggestion') }}/:id"
            class=" relative dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden bg-white">
            <div id="loader"
                class="hidden absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm flex items-center justify-center z-20">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead
                        class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left w-16">ลำดับ</th>
                            <th class="px-6 py-3 text-left">วันที่/เวลา</th>
                            <th class="px-6 py-3 text-left w-1/4">เรื่อง</th>
                            <th class="px-6 py-3 text-left">ประเภท</th>
                            <th class="px-6 py-3 text-left">ผู้ร้องเรียน</th>
                            <th class="px-6 py-3 text-left">ถึงใคร</th>
                            <th class="px-6 py-3 text-center">สถานะ</th>
                            <th class="px-6 py-3 text-center w-28">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="suggestions-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($suggestions as $suggestion)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $suggestion->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]"
                                    title="{{ $suggestion->topic }}">{{ $suggestion->topic }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    @if($suggestion->complaint_type == 'self') ร้องเรียนด้วยตนเอง
                                    @elseif($suggestion->complaint_type == 'other') ร้องเรียนแทนผู้อื่น
                                    @elseif($suggestion->complaint_type == 'phone') ร้องเรียนทางโทรศัพท์
                                    @else {{ $suggestion->complaint_type }}
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $suggestion->fullname }}
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $suggestion->to_person }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if(trim($suggestion->status) == 'รอรับเรื่องคำร้อง')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800 whitespace-nowrap shadow-sm"><i
                                                class="fa-solid fa-hourglass-start mr-1"></i>{{ $suggestion->status }}</span>
                                    @elseif(in_array(trim($suggestion->status), ['รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว']))
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800 whitespace-nowrap shadow-sm"><i
                                                class="fa-solid fa-inbox mr-1"></i>{{ $suggestion->status }}</span>
                                    @elseif(trim($suggestion->status) == 'ตรวจสอบ')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800 whitespace-nowrap shadow-sm"><i
                                                class="fa-solid fa-magnifying-glass mr-1"></i>{{ $suggestion->status }}</span>
                                    @elseif(trim($suggestion->status) == 'ดำเนินการ')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800 whitespace-nowrap shadow-sm"><i
                                                class="fa-solid fa-spinner mr-1"></i>{{ $suggestion->status }}</span>
                                    @elseif(trim($suggestion->status) == 'เสร็จสิ้น')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 whitespace-nowrap shadow-sm"><i
                                                class="fa-solid fa-check mr-1"></i>{{ $suggestion->status }}</span>
                                    @elseif(trim($suggestion->status) == 'ปิดเรื่อง')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 whitespace-nowrap shadow-sm"><i
                                                class="fa-solid fa-lock mr-1"></i>{{ $suggestion->status }}</span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 whitespace-nowrap shadow-sm">{{ $suggestion->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center space-x-2 whitespace-nowrap">
                                    <a href="{{ route('suggestion.show', $suggestion->id) }}"
                                        class="btn btn-info btn-sm btn-square text-white shadow-sm" title="ดูรายละเอียด">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('suggestion.edit', $suggestion->id) }}"
                                        class="btn btn-warning btn-sm btn-square text-white shadow-sm" title="แก้ไข">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-error btn-sm btn-square text-white shadow-sm"
                                        onclick="confirmDelete({{ $suggestion->id }}, '{{ $suggestion->topic }}')" title="ลบ">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-inbox text-4xl mb-3 opacity-50"></i>
                                        <p>ไม่พบรายการร้องเรียน</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4" id="pagination">
        {{ $suggestions->links() }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-100">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">ยืนยันการลบ?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mb-6">
                    คุณต้องการลบรายการ "<span id="deleteName" class="font-bold text-gray-800 dark:text-white"></span>"
                    ใช่หรือไม่?<br>
                    การกระทำนี้ไม่สามารถย้อนกลับได้
                </p>
                <form method="POST" id="deleteForm" class="flex justify-center space-x-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()">ยกเลิก</button>
                    <button type="submit" class="btn btn-error text-white px-6">ยืนยันลบ</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // --- Filter Toggle ---
            const btn = document.getElementById('toggle-filter');
            const panel = document.getElementById('filter-form');
            if (btn && panel) {
                btn.addEventListener('click', function () {
                    panel.classList.toggle('hidden');
                });
            }

            function confirmDelete(id, name) {
                document.getElementById('deleteName').textContent = name;
                document.getElementById('deleteForm').action = `/suggestion/${id}`;
                document.getElementById('deleteModal').classList.remove('hidden');
                document.getElementById('deleteModal').classList.add('flex');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
                document.getElementById('deleteModal').classList.remove('flex');
            }

            // Close on Escape key
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });

            // Close when clicking outside
            document.getElementById('deleteModal').addEventListener('click', (e) => {
                if (e.target === document.getElementById('deleteModal')) {
                    closeDeleteModal();
                }
            });

            // --- Realtime Search/Pagination Logic ---
            const form = document.getElementById('filter-form');
            const tbody = document.getElementById('suggestions-body');
            const pagWrap = document.getElementById('pagination');
            const loader = document.getElementById('loader');
            const tableWrap = document.getElementById('table-wrap');

            if (form && tbody && pagWrap && loader && tableWrap) {
                const showPattern = tableWrap.dataset.showPattern;
                const editPattern = tableWrap.dataset.editPattern;
                const destroyPattern = tableWrap.dataset.destroyPattern;

                const debouncedFetch = debounce(fetchSuggestions, 300);

                const clearFilterBtn = document.getElementById('clear-filter');
                if (clearFilterBtn) {
                    clearFilterBtn.addEventListener('click', () => {
                        form.querySelectorAll('input').forEach(el => el.value = '');
                        form.querySelectorAll('select').forEach(el => el.value = '');
                        fetchSuggestions();
                    });
                }

                form.querySelectorAll('input').forEach(el => {
                    el.addEventListener('input', debouncedFetch);
                    el.addEventListener('change', debouncedFetch);
                });
                form.querySelectorAll('select').forEach(el => {
                    el.addEventListener('change', debouncedFetch);
                });

                pagWrap.addEventListener('click', (e) => {
                    const a = e.target.closest('a');
                    if (!a) return;
                    e.preventDefault();
                    const url = new URL(a.href);
                    fetchSuggestions(url.searchParams);
                });

                async function fetchSuggestions(overrideSearchParams = null) {
                    loader.classList.remove('hidden');
                    let params;
                    if (overrideSearchParams instanceof URLSearchParams) {
                        params = overrideSearchParams;
                    } else {
                        params = new URLSearchParams();
                        new FormData(form).forEach((v, k) => {
                            if (v) params.set(k, v);
                        });
                        const cur = new URLSearchParams(window.location.search);
                        if (cur.get('per_page')) params.set('per_page', cur.get('per_page'));
                    }
                    if (!params.get('per_page')) params.set('per_page', 50);

                    try {
                        const res = await fetch(`/api/suggestions?${params.toString()}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        renderRows(data.data || [], data);
                        renderPagination(data);
                        const sync = new URLSearchParams(params);
                        sync.set('page', data.current_page || 1);
                        history.replaceState({}, '', `${window.location.pathname}?${sync.toString()}`);
                    } catch (err) {
                        console.error(err);
                    } finally {
                        loader.classList.add('hidden');
                    }
                }

                function renderRows(items, dataInfo) {
                    if (!Array.isArray(items) || items.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center"><i class="fa-solid fa-inbox text-4xl mb-3 opacity-50"></i><p>ไม่พบรายการร้องเรียน</p></div></td></tr>`;
                        return;
                    }

                    let html = '';
                    items.forEach((s, idx) => {
                        let iteration = (dataInfo.current_page - 1) * dataInfo.per_page + idx + 1;
                        let urlShow = showPattern.replace(':id', s.id);
                        let urlEdit = editPattern.replace(':id', s.id);
                        let dateObj = new Date(s.created_at);
                        let fDate = ("0" + dateObj.getDate()).slice(-2) + "/" + ("0" + (dateObj.getMonth() + 1)).slice(-2) + "/" + dateObj.getFullYear() + " " + ("0" + dateObj.getHours()).slice(-2) + ":" + ("0" + dateObj.getMinutes()).slice(-2);

                        let cType = s.complaint_type;
                        if (cType === 'self') cType = 'ร้องเรียนด้วยตนเอง';
                        else if (cType === 'other') cType = 'ร้องเรียนแทนผู้อื่น';
                        else if (cType === 'phone') cType = 'ร้องเรียนทางโทรศัพท์';

                        let badge = '';
                        let status = (s.status || '').trim();
                        if (status === 'รอรับเรื่องคำร้อง') badge = `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800 whitespace-nowrap shadow-sm"><i class="fa-solid fa-hourglass-start mr-1"></i>${status}</span>`;
                        else if (['รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว'].includes(status)) badge = `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800 whitespace-nowrap shadow-sm"><i class="fa-solid fa-inbox mr-1"></i>${status}</span>`;
                        else if (status === 'ตรวจสอบ') badge = `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800 whitespace-nowrap shadow-sm"><i class="fa-solid fa-magnifying-glass mr-1"></i>${status}</span>`;
                        else if (status === 'ดำเนินการ') badge = `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800 whitespace-nowrap shadow-sm"><i class="fa-solid fa-spinner mr-1"></i>${status}</span>`;
                        else if (status === 'เสร็จสิ้น') badge = `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 whitespace-nowrap shadow-sm"><i class="fa-solid fa-check mr-1"></i>${status}</span>`;
                        else if (status === 'ปิดเรื่อง') badge = `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 whitespace-nowrap shadow-sm"><i class="fa-solid fa-lock mr-1"></i>${status}</span>`;
                        else badge = `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 whitespace-nowrap shadow-sm">${status}</span>`;

                        let safeTopic = (s.topic || '').replace(/'/g, "\\'");

                        html += `
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">${iteration}</td>
                                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">${fDate}</td>
                                                <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]" title="${safe(s.topic)}">${safe(s.topic)}</td>
                                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">${safe(cType)}</td>
                                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">${safe(s.fullname)}</td>
                                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">${safe(s.to_person)}</td>
                                                <td class="px-6 py-3 text-center">${badge}</td>
                                                <td class="px-6 py-3 text-center space-x-2 whitespace-nowrap">
                                                    <a href="${urlShow}" class="btn btn-info btn-sm btn-square text-white shadow-sm" title="ดูรายละเอียด"><i class="fa-solid fa-eye"></i></a>
                                                    <a href="${urlEdit}" class="btn btn-warning btn-sm btn-square text-white shadow-sm" title="แก้ไข"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    <button type="button" class="btn btn-error btn-sm btn-square text-white shadow-sm" onclick="confirmDelete(${s.id}, '${safeTopic}')" title="ลบ"><i class="fa-solid fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        `;
                    });
                    tbody.innerHTML = html;
                }

                function debounce(fn, wait = 300) {
                    let t;
                    return (...args) => {
                        clearTimeout(t);
                        t = setTimeout(() => fn.apply(null, args), wait);
                    };
                }

                function safe(v) {
                    return (v ?? '-').toString().replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
                }

                function renderPagination(pag) {
                    const cur = pag.current_page || 1;
                    const last = pag.last_page || 1;
                    const prev = pag.prev_page_url;
                    const next = pag.next_page_url;

                    let html = `<div class="join shadow-sm">
                                        <a class="join-item btn btn-sm ${!prev ? 'btn-disabled opacity-50' : ''}" ${prev ? `href="${prev}"` : ''}>«</a>`;

                    const arr = [];
                    if (last <= 7) {
                        for (let i = 1; i <= last; i++) arr.push(i);
                    } else {
                        arr.push(1);
                        if (cur > 3) arr.push('...');
                        const start = Math.max(2, cur - 1);
                        const end = Math.min(last - 1, cur + 1);
                        for (let i = start; i <= end; i++) arr.push(i);
                        if (cur < last - 2) arr.push('...');
                        arr.push(last);
                    }

                    arr.forEach(p => {
                        if (p === '...') html += `<button class="join-item btn btn-sm btn-disabled">...</button>`;
                        else {
                            const url = new URL(pag.path + '?page=' + p, window.location.origin);
                            new URLSearchParams(window.location.search).forEach((v, k) => {
                                if (k !== 'page') url.searchParams.set(k, v);
                            });
                            html += `<a class="join-item btn btn-sm ${p === cur ? 'btn-active btn-primary text-white' : ''}" href="${url}">${p}</a>`;
                        }
                    });
                    html += `<a class="join-item btn btn-sm ${!next ? 'btn-disabled opacity-50' : ''}" ${next ? `href="${next}"` : ''}>»</a></div>`;

                    const currentPer = new URLSearchParams(window.location.search).get('per_page') || 50;
                    html += `
                                    <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500">
                                        <span>แสดง</span>
                                        <select id="per-page" class="select select-bordered select-xs h-8">
                                            <option value="10" ${currentPer == 10 ? 'selected' : ''}>10</option>
                                            <option value="20" ${currentPer == 20 ? 'selected' : ''}>20</option>
                                            <option value="50" ${currentPer == 50 ? 'selected' : ''}>50</option>
                                            <option value="100" ${currentPer == 100 ? 'selected' : ''}>100</option>
                                        </select>
                                        <span>รายการ</span>
                                    </div>`;

                    pagWrap.innerHTML = html;
                    const perSel = document.getElementById('per-page');
                    if (perSel) perSel.addEventListener('change', () => {
                        const sp = new URLSearchParams(window.location.search);
                        sp.set('per_page', perSel.value);
                        sp.delete('page');
                        fetchSuggestions(sp);
                    });
                }
            }
        </script>
    @endpush
@endsection