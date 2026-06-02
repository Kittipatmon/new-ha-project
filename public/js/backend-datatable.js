/**
 * Backend DataTable - Standardized fetching, pagination, and delete confirmation logic.
 */
function initBackendDataTable(config) {
    const {
        formId = 'filter-form',
        tbodyId = 'users-body', // Default ID but can be overridden
        paginationId = 'pagination',
        loaderId = 'loader',
        tableWrapId = 'table-wrap',
        apiUrl,
        renderRowFn,
        emptyStateHtml = '<tr><td colspan="100%" class="text-center py-10">ไม่พบข้อมูล</td></tr>',
        defaultIncludes = '',
        defaultPerPage = 50
    } = config;

    const form = document.getElementById(formId);
    const tbody = document.getElementById(tbodyId);
    const pagWrap = document.getElementById(paginationId);
    const loader = document.getElementById(loaderId);
    const tableWrap = document.getElementById(tableWrapId);

    if (!form || !tbody || !pagWrap || !loader || !tableWrap) {
        console.warn('BackendDataTable: Required elements not found. Initialization aborted.');
        return;
    }

    const debouncedFetch = debounce(fetchData, 300);

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
        fetchData(url.searchParams);
    });

    async function fetchData(overrideSearchParams = null) {
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
        
        if (!params.get('per_page')) params.set('per_page', defaultPerPage);
        if (!params.get('include') && defaultIncludes) params.set('include', defaultIncludes);

        try {
            const res = await fetch(`${apiUrl}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            if (!Array.isArray(data.data) || data.data.length === 0) {
                tbody.innerHTML = emptyStateHtml;
            } else {
                tbody.innerHTML = data.data.map(renderRowFn).join('');
            }
            
            renderPagination(data);
            
            const sync = new URLSearchParams(params);
            sync.set('page', data.current_page || 1);
            history.replaceState({}, '', `${window.location.pathname}?${sync.toString()}`);
        } catch (err) {
            console.error('BackendDataTable Fetch Error:', err);
        } finally {
            loader.classList.add('hidden');
        }
    }

    function renderPagination(pag) {
        const cur = pag.current_page || 1;
        const last = pag.last_page || 1;
        const prev = pag.prev_page_url;
        const next = pag.next_page_url;

        let html = `<div class="join shadow-sm">
            <a class="join-item btn btn-sm ${!prev ? 'btn-disabled opacity-50' : ''}" ${prev ? `href="${prev}"` : ''}>«</a>`;

        const pages = calcWindow(cur, last);
        pages.forEach(p => {
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

        const currentPer = new URLSearchParams(window.location.search).get('per_page') || defaultPerPage;
        html += `
        <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500 ml-4">
            <span>แสดง</span>
            <select id="per-page-select" class="select select-bordered select-xs h-8 focus:outline-none focus:ring-1 focus:ring-kumwell-red">
                <option value="10" ${currentPer == 10 ? 'selected' : ''}>10</option>
                <option value="20" ${currentPer == 20 ? 'selected' : ''}>20</option>
                <option value="50" ${currentPer == 50 ? 'selected' : ''}>50</option>
                <option value="100" ${currentPer == 100 ? 'selected' : ''}>100</option>
            </select>
            <span>รายการ</span>
        </div>`;

        pagWrap.innerHTML = html;
        const perSel = document.getElementById('per-page-select');
        if (perSel) perSel.addEventListener('change', () => {
            const sp = new URLSearchParams(window.location.search);
            sp.set('per_page', perSel.value);
            sp.delete('page');
            fetchData(sp);
        });
    }

    function calcWindow(cur, last) {
        const arr = [];
        if (last <= 7) {
            for (let i = 1; i <= last; i++) arr.push(i);
            return arr;
        }
        arr.push(1);
        if (cur > 3) arr.push('...');
        const start = Math.max(2, cur - 1);
        const end = Math.min(last - 1, cur + 1);
        for (let i = start; i <= end; i++) arr.push(i);
        if (cur < last - 2) arr.push('...');
        arr.push(last);
        return arr;
    }

    function debounce(fn, wait) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }
    
    // Return fetch function in case external code needs to trigger a refresh
    return {
        refresh: () => fetchData()
    };
}

// Global Delete Confirmation Logic
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', (e) => {
        const formEl = e.target?.closest?.('form.form-delete');
        if (!formEl) return;

        if (formEl.dataset.confirmed === '1') {
            delete formEl.dataset.confirmed;
            return;
        }

        e.preventDefault();
        const proceed = () => {
            formEl.dataset.confirmed = '1';
            formEl.submit();
        };

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
                customClass: {
                    confirmButton: 'shadow-lg shadow-red-500/50'
                }
            }).then((result) => {
                if (result.isConfirmed) proceed();
            });
        } else {
            if (confirm('ยืนยันการลบ?')) proceed();
        }
    });
});

window.safe = function(v) {
    return (v ?? '-').toString().replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
};
