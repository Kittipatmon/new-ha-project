@extends('layouts.hrrequest.app')
@section('content')
<div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-3">
    <div class="breadcrumbs text-sm">
        <ul>
            <li><a>Home</a></li>
            <li><a href="{{ route('request.hr') }}">Request HR</a></li>
            <li class="text-red-600">รายงานคำร้องขอ (รอรับทราบ/อนุมัติ)</li>
        </ul>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-md overflow-hidden mb-2">
        <div class="px-6 py-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between  gap-2">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                    </path>
                </svg>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">ตัวกรองค้นหา
                    (Search Filters)
                </h3>
            </div>
            <div class="flex items-center">
                <button type="button" data-collapse-toggle="filterFormHr" aria-expanded="true"
                    aria-controls="filterFormHr"
                    class="btn btn-sm btn-circle btn-ghost hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-3"  id="filterForm">
            <form action="{{ route('approve.approvehrlistall') }}" method="GET">
                @include('requesthr.approve.filter.filter')
            </form>
            <script>
                // filterFormHr
                document.addEventListener('DOMContentLoaded', function() {
                    const filterButton = document.querySelector('button[data-collapse-toggle="filterFormHr"]');
                    const filterForm = document.getElementById('filterForm');
                    if (filterButton && filterForm) {
                        filterButton.addEventListener('click', function() {
                            filterForm.classList.toggle('hidden');
                        });
                    }
                });
            </script>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-100 dark:bg-indigo-500/20 p-2 rounded-lg text-indigo-600 dark:text-indigo-500">
                    <i class="fas fa-list-alt text-lg"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">รายการคำร้องทั้งหมด</h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400 py-1 px-3 rounded-full text-xs font-bold">
                    {{ $hrrequests->count() }} รายการ
                </span>
                <a href="{{ route('approve.approvehrlistall.pdf', request()->all()) }}" target="_blank" class="btn btn-sm bg-red-50 text-red-600 hover:bg-red-100 border-none transition-colors">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium">เลขที่รายการ</th>
                        <th scope="col" class="px-6 py-4 font-medium">รหัสผู้ขอ</th>
                        <th scope="col" class="px-6 py-4 font-medium">ชื่อ-สกุล</th>
                        <th scope="col" class="px-6 py-4 font-medium">หมวดหมู่คำร้อง</th>
                        <th scope="col" class="px-6 py-4 font-medium">ประเภทคำร้อง</th>
                        <th scope="col" class="px-6 py-4 font-medium">ประเภทย่อย</th>
                        <th scope="col" class="px-6 py-4 font-medium">วันที่ส่งคำร้อง</th>
                        <th scope="col" class="px-6 py-4 font-medium">สถานะ</th>
                        <th scope="col" class="px-6 py-4 font-medium text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="hr-list-tbody" class="divide-y divide-gray-100 dark:divide-gray-700">
                    @include('requesthr.approve.partials.approvehrlistall_rows')
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function() {
    const form = document.querySelector('form[action="{{ route('approve.approvehrlistall') }}"]');
    if (!form) return;
    const tbody = document.getElementById('hr-list-tbody');
    let debounceTimer = null;
    let activeController = null;

    function buildQuery() {
        const data = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of data.entries()) {
            if (value !== '') params.append(key, value.trim());
        }
        return params.toString();
    }

    function setLoading() {
        tbody.innerHTML =
            `<tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">กำลังโหลดข้อมูล...</td></tr>`;
    }

    async function refreshTable(isDebounced = false) {
        if (!isDebounced) setLoading();
        const qs = buildQuery();
        const url = `{{ route('approve.approvehrlistall.data') }}?${qs}`;

        // update URL (no reload)
        if (window.history && window.history.replaceState) {
            const newUrl = `${location.pathname}?${qs}`;
            window.history.replaceState(null, '', newUrl);
        }

        // abort previous request
        if (activeController) activeController.abort();
        activeController = new AbortController();

        try {
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: activeController.signal
            });
            if (!res.ok) return;
            const html = await res.text();
            tbody.innerHTML = html;
        } catch (err) {
            if (err.name !== 'AbortError') console.error(err);
        }
    }

    function debouncedRefresh() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => refreshTable(true), 250);
    }

    // Attach events: text inputs debounced, others immediate
    form.querySelectorAll('input, select').forEach(el => {
        if (el.name === 'search') {
            el.addEventListener('input', debouncedRefresh);
        } else if (el.type === 'date') {
            el.addEventListener('change', refreshTable);
        } else if (el.tagName === 'SELECT') {
            el.addEventListener('change', refreshTable);
        }
    });

    // Intercept submit (manual click)
    form.addEventListener('submit', e => {
        e.preventDefault();
        refreshTable();
    });

    // Optional: handle clear button without full reload
    const clearLink = form.parentElement.querySelector('a[href="{{ route('approve.approvehrlistall') }}"]');
    if (clearLink) {
        clearLink.addEventListener('click', e => {
            e.preventDefault();
            form.reset();
            refreshTable();
        });
    }
})();
</script>
@endpush