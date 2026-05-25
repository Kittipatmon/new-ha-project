@extends('layouts.sidebar')
@section('content')

<div>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <!-- <button onclick="add_user_modal.showModal()" class="btn btn-success text-white w-full md:w-auto shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i>
            เพิ่มพนักงานใหม่
        </button> -->
        <a href="{{ route('users.create') }}" class="btn btn-success text-white w-full md:w-auto shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i>
            เพิ่มพนักงานใหม่
        </a>
        <h1 class="text-2xl text-red-600 font-bold text-center flex-grow">
            ข้อมูลพนักงาน
        </h1>

        <button type="button" id="toggle-filter" class="btn btn-warning btn-sm w-full md:w-auto shadow-sm">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
    </div>

    @if ($errors->any())
    <div class="alert alert-error mb-4 shadow-lg">
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

    <form id="filter-form" method="GET" action="{{ route('users.index') }}"
        class="mb-6 border border-gray-200 rounded-xl p-5 bg-white dark:bg-gray-800 dark:border-gray-700 shadow-sm hidden transition-all duration-300">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div class="form-control">
                <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">รหัสพนักงาน</span>
                <input type="text" name="employee_code" placeholder="ค้นหารหัส..."
                    value="{{ request('employee_code') }}"
                    class="input input-bordered input-sm w-full dark:bg-gray-700" />
            </div>

            <div class="form-control">
                <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ชื่อ-นามสกุล</span>
                <input type="text" name="fullname" placeholder="ค้นหาชื่อ..." value="{{ request('fullname') }}"
                    class="input input-bordered input-sm w-full dark:bg-gray-700" />
            </div>

            <div class="form-control">
                <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ตำแหน่ง</span>
                <input type="text" name="position" placeholder="ค้นหาตำแหน่ง..." value="{{ request('position') }}"
                    class="input input-bordered input-sm w-full dark:bg-gray-700" />
            </div>

            <div class="form-control">
                <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ระดับพนักงาน</span>
                <input type="text" name="level_user" placeholder="ค้นหาระดับ..." value="{{ request('level_user') }}"
                    class="input input-bordered input-sm w-full dark:bg-gray-700" />
            </div>

            <select name="department" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">แผนก (ทั้งหมด)</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->department_id }}" {{ (string)request('department')===(string)$dept->
                    department_id ? 'selected' : '' }}>
                    {{ $dept->department_name }}
                </option>
                @endforeach
            </select>

            <select name="division" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">ฝ่าย (ทั้งหมด)</option>
                @foreach($divisions as $div)
                <option value="{{ $div->division_id }}" {{ (string)request('division')===(string)$div->division_id ?
                    'selected' : '' }}>
                    {{ $div->division_name }}
                </option>
                @endforeach
            </select>

            <select name="section" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">สายงาน (ทั้งหมด)</option>
                @foreach($sections as $sect)
                <option value="{{ $sect->section_id }}" {{ (string)request('section')===(string)$sect->section_id ?
                    'selected' : '' }}>
                    {{ $sect->section_code }}
                </option>
                @endforeach
            </select>

            @php
            $levelOptions = \App\Models\User::getLevelUserOptions();
            $selectedLevel = request('level_user');
            @endphp
            <select name="level_user" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">ระดับพนักงาน (ทั้งหมด)</option>
                @foreach($levelOptions as $value => $meta)
                <option value="{{ $value }}" {{ (string)$selectedLevel===(string)$value ? 'selected' : '' }}>
                    {{ $meta['label'] }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm text-gray-500">
                <i class="fa-solid fa-rotate-left mr-1"></i> ล้างค่า
            </a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white dark:bg-gray-800 dark:border-gray-700 relative"
        id="table-wrap" data-show-pattern="{{ url('users') }}/:id" data-edit-pattern="{{ url('users') }}/:id/edit"
        data-destroy-pattern="{{ url('users') }}/:id">

        <div id="loader"
            class="hidden absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm flex items-center justify-center z-20">
            <span class="loading loading-spinner loading-lg text-primary"></span>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead
                    class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-200 uppercase text-xs font-semibold">
                    <tr>
                        <th class="py-3">รหัสพนักงาน</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>แผนก</th>
                        <th>ฝ่าย</th>
                        <th>สายงาน</th>
                        <th>ตำแหน่ง</th>
                        <th>เริ่มงาน</th>
                        <th>ระดับ</th>
                        <th>สถานะ HR</th>
                        <th class="w-24 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="users-body"
                    class="text-gray-700 dark:text-gray-300 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="font-medium">{{ $user->employee_code }}</td>
                        <td>
                            <div class="font-bold">{{ $user->fullname }}</div>
                        </td>
                        <td>{{ $user->department->department_name ?? '-' }}</td>
                        <td>{{ $user->division->division_name ?? '-' }}</td>
                        <td>{{ $user->section->section_code ?? '-' }}</td>
                        <td>{{ $user->position }}</td>
                        <td class="whitespace-nowrap">{{ $user->startwork_date ?
                            \Carbon\Carbon::parse($user->startwork_date)->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $user->level_user_color }} badge-outline badge-sm font-medium">
                                {{ $user->level_user_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->hr_status_color }} badge-sm text-xs">
                                {{ $user->hr_status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-center gap-1">
                                <a href="#" class="btn btn-square btn-xs btn-info text-white" title="ดูข้อมูล">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="#" class="btn btn-square btn-xs btn-warning text-white" title="แก้ไข">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="#" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-square btn-xs btn-error text-white"
                                        onclick="return confirm('ยืนยันการลบ?')" title="ลบ">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-gray-500 py-10">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-4xl mb-2 text-gray-300"></i>
                                <span>ไม่พบข้อมูลตามเงื่อนไข</span>
                            </div>
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

<dialog id="add_user_modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-11/12 max-w-5xl bg-white dark:bg-gray-800 p-0 overflow-hidden shadow-2xl">

        <div
            class="bg-gray-100 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-success"></i> เพิ่มพนักงานใหม่
            </h3>
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost">✕</button>
            </form>
        </div>

        <form method="POST" action="{{ route('users.store') }}" id="add_user_form" class="p-6">
            @csrf

            <div id="modal_errors" class="alert alert-error mb-6 hidden text-sm"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-2">

                <div
                    class="col-span-1 md:col-span-2 lg:col-span-4 pb-2 mb-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ข้อมูลส่วนตัว</span>
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">รหัสพนักงาน <span
                                class="text-red-500">*</span></span></label>
                    <input type="text" name="employee_code" class="input input-bordered w-full dark:bg-gray-700"
                        required />
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">เพศ <span
                                class="text-red-500">*</span></span></label>
                    <select name="sex" class="select select-bordered w-full dark:bg-gray-700" required>
                        <option disabled selected>เลือกเพศ</option>
                        <option value="ชาย">ชาย</option>
                        <option value="หญิง">หญิง</option>
                    </select>
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">คำนำหน้า <span
                                class="text-red-500">*</span></span></label>
                    <input type="text" name="prefix" class="input input-bordered w-full dark:bg-gray-700" required />
                </div>

                <div class="hidden lg:block"></div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">ชื่อ <span
                                class="text-red-500">*</span></span></label>
                    <input type="text" name="first_name" class="input input-bordered w-full dark:bg-gray-700"
                        required />
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">นามสกุล <span
                                class="text-red-500">*</span></span></label>
                    <input type="text" name="last_name" class="input input-bordered w-full dark:bg-gray-700" required />
                </div>

                <div
                    class="col-span-1 md:col-span-2 lg:col-span-4 pb-2 mb-2 mt-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ตำแหน่งและสังกัด</span>
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">ตำแหน่ง <span
                                class="text-red-500">*</span></span></label>
                    <input type="text" name="position" class="input input-bordered w-full dark:bg-gray-700" />
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">แผนก <span
                                class="text-red-500">*</span></span></label>
                    <select name="department_id" class="select select-bordered w-full dark:bg-gray-700">
                        <option disabled selected>เลือกแผนก</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">ฝ่าย <span
                                class="text-red-500">*</span></span></label>
                    <select name="division_id" class="select select-bordered w-full dark:bg-gray-700">
                        <option disabled selected>เลือกฝ่าย</option>
                        @foreach($divisions as $div)
                        <option value="{{ $div->division_id }}">{{ $div->division_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">สายงาน <span
                                class="text-red-500">*</span></span></label>
                    <select name="section_id" class="select select-bordered w-full dark:bg-gray-700">
                        <option disabled selected>เลือกสายงาน</option>
                        @foreach($sections as $sect)
                        <option value="{{ $sect->section_id }}">{{ $sect->section_code }}</option>
                        @endforeach
                    </select>
                </div>

                <div
                    class="col-span-1 md:col-span-2 lg:col-span-4 pb-2 mb-2 mt-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">สิทธิ์การใช้งาน</span>
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">ระดับพนักงาน <span
                                class="text-red-500">*</span></span></label>
                    <select name="level_user" class="select select-bordered w-full dark:bg-gray-700">
                        <option disabled selected>เลือกระดับ</option>
                        @foreach($levelOptions as $value => $meta)
                        <option value="{{ $value }}">{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-medium">สถานะ HR <span
                                class="text-red-500">*</span></span></label>
                    <select name="hr_status" class="select select-bordered w-full dark:bg-gray-700">
                        <option disabled selected>เลือกสถานะ</option>
                        @php
                        $hrStatusOptions = \App\Models\User::getHrStatusOptions();
                        @endphp
                        @foreach($hrStatusOptions as $value => $meta)
                        <option value="{{ $value }}">{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-action mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" class="btn" onclick="add_user_modal.close()">ยกเลิก</button>
                <button type="button" id="confirm-add-user" class="btn btn-success text-white px-8">
                    <i class="fa-solid fa-save mr-2"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Modal & Form Handling ---
        const addUserForm = document.getElementById('add_user_form');
        const addUserModal = document.getElementById('add_user_modal');
        const modalErrors = document.getElementById('modal_errors');
        const confirmAddUserBtn = document.getElementById('confirm-add-user');

        if (confirmAddUserBtn && addUserForm) {
            confirmAddUserBtn.addEventListener('click', function (e) {
                e.preventDefault();

                // 1. ปิด Modal ก่อนเรียก SweetAlert เพื่อป้องกัน Z-Index Conflict
                if (addUserModal && typeof addUserModal.close === 'function') {
                    addUserModal.close();
                }

                // 2. เรียก SweetAlert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'ยืนยันการบันทึกข้อมูล?',
                        text: 'คุณต้องการบันทึกข้อมูลพนักงานใหม่ใช่หรือไม่?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'บันทึก',
                        cancelButtonText: 'ยกเลิก',
                        confirmButtonColor: '#10b981', // Tailwind green-500 hex
                        cancelButtonColor: '#6b7280',  // Tailwind gray-500 hex
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit จริง
                            addUserForm.requestSubmit();
                        } else {
                            // ถ้ากดยกเลิก ให้เปิด Modal กลับมา
                            if (addUserModal && typeof addUserModal.showModal === 'function') {
                                addUserModal.showModal();
                            }
                        }
                    });
                } else {
                    // Fallback ถ้าไม่มี SweetAlert
                    if (confirm('คุณต้องการบันทึกข้อมูลพนักงานใหม่ใช่หรือไม่?')) {
                        addUserForm.requestSubmit();
                    } else {
                        if (addUserModal) addUserModal.showModal();
                    }
                }
            });
        }

        if (addUserForm) {
            addUserForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                // ถ้า User submit เอง (กด enter) ให้ปิด modal ก่อนเล็กน้อยหรือโชว์ Loading
                // แต่ใน flow นี้เราใช้ปุ่มแยก ดังนั้นโค้ดนี้จะทำงานตอน requestSubmit()

                modalErrors.classList.add('hidden');
                modalErrors.innerHTML = '';

                // Re-open modal ถ้าเป็นการ submit แบบปกติเพื่อให้เห็น loading หรือ error (กรณีไม่ได้ผ่าน swal logic)
                // แต่เนื่องจาก requestSubmit() มาจาก logic ข้างบน เราอาจจะต้องเปิด Modal มารับ Error
                // หากเป็นการ submit success โค้ดด้านล่างจะจัดการเอง

                const formData = new FormData(this);
                const action = this.getAttribute('action');
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                try {
                    const response = await fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (response.status === 422) {
                        // Validation Errors: ต้องเปิด Modal กลับมาแสดง Error
                        if (!addUserModal.open) addUserModal.showModal();

                        const errorData = await response.json();
                        let errorHtml = '<ul class="list-disc list-inside">';
                        for (const key in errorData.errors) {
                            errorData.errors[key].forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                        }
                        errorHtml += '</ul>';
                        modalErrors.innerHTML = errorHtml;
                        modalErrors.classList.remove('hidden');

                        // Scroll to top of modal
                        addUserModal.querySelector('.modal-box').scrollTop = 0;

                    } else if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    } else {
                        // Success
                        this.reset();
                        // Modal ปิดอยู่แล้วจาก step ก่อนหน้า หรือถ้าเปิดอยู่ก็ปิดเลย
                        if (addUserModal.open) addUserModal.close();

                        await fetchUsers(); // Refresh Table

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
                    if (!addUserModal.open) addUserModal.showModal();
                    modalErrors.innerHTML = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล โปรดลองใหม่อีกครั้ง';
                    modalErrors.classList.remove('hidden');
                }
            });
        }

        // ... (ส่วน Logic อื่นๆ เช่น Filter, Pagination คงเดิม) ...
        // --- Filter Toggle ---
        const btn = document.getElementById('toggle-filter');
        const panel = document.getElementById('filter-form');
        if (btn && panel) {
            btn.addEventListener('click', function () {
                panel.classList.toggle('hidden');
            });
        }

        // --- Realtime Search/Pagination Logic (Code เดิมของคุณใส่ตรงนี้ต่อได้เลย) ---
        // (copy logic fetchUsers, renderRows, renderPagination เดิมมาใส่)
        const form = document.getElementById('filter-form');
        const tbody = document.getElementById('users-body');
        const pagWrap = document.getElementById('pagination');
        const loader = document.getElementById('loader');
        const tableWrap = document.getElementById('table-wrap');

        const showPattern = tableWrap.dataset.showPattern || '/users/:id';
        const editPattern = tableWrap.dataset.editPattern || '/users/:id/edit';
        const destroyPattern = tableWrap.dataset.destroyPattern || '/users/:id';

        const debouncedFetch = debounce(fetchUsers, 300);

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
            fetchUsers(url.searchParams);
        });

        // ... (ฟังก์ชัน fetchUsers, renderRows, helpers เดิม) ...
        async function fetchUsers(overrideSearchParams = null) {
            loader.classList.remove('hidden');
            let params;
            if (overrideSearchParams instanceof URLSearchParams) {
                params = overrideSearchParams;
            } else {
                params = new URLSearchParams();
                new FormData(form).forEach((v, k) => { if (v) params.set(k, v); });
                const cur = new URLSearchParams(window.location.search);
                if (cur.get('per_page')) params.set('per_page', cur.get('per_page'));
                if (cur.get('page')) params.set('page', cur.get('page'));
            }
            if (!params.get('per_page')) params.set('per_page', 20);

            try {
                const res = await fetch(`/api/users?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                renderRows(data.data || []);
                renderPagination(data);
                const sync = new URLSearchParams(params);
                sync.set('page', data.current_page || 1);
                history.replaceState({}, '', `${window.location.pathname}?${sync.toString()}`);
            } catch (err) { console.error(err); }
            finally { loader.classList.add('hidden'); }
        }

        function renderRows(items) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            if (!Array.isArray(items) || items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10" class="text-center text-gray-500 py-10">ไม่พบข้อมูลตามเงื่อนไข</td></tr>`;
                return;
            }
            tbody.innerHTML = items.map(u => {
                const showUrl = showPattern.replace(':id', u.id);
                const editUrl = editPattern.replace(':id', u.id);
                const destroyUrl = destroyPattern.replace(':id', u.id);
                // ... (Logic การสร้าง HTML Row เหมือนเดิม แต่ปรับ class เล็กน้อยให้เข้ากับ Tailwind) ...
                return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="font-medium">${safe(u.employee_code)}</td>
                <td><div class="font-bold">${safe(u.fullname)}</div></td>
                <td>${safe(u.department?.department_name)}</td>
                <td>${safe(u.division?.division_name)}</td>
                <td>${safe(u.section?.section_code)}</td>
                <td>${safe(u.position)}</td>
                <td class="whitespace-nowrap">${formatDate(u.startwork_date)}</td>
                <td>${levelBadge(u)}</td>
                <td>${u.hr_status ? `<span class="badge badge-${u.hr_status_color} badge-sm text-xs">${u.hr_status_label}</span>` : ''}</td>
                <td>
                    <div class="flex justify-center gap-1">
                        <a href="${showUrl}" class="btn btn-square btn-xs btn-info text-white"><i class="fa-solid fa-eye"></i></a>
                        <a href="${editUrl}" class="btn btn-square btn-xs btn-warning text-white"><i class="fa-solid fa-pen-to-square"></i></a>
                        <form action="${destroyUrl}" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-square btn-xs btn-error text-white" onclick="return confirm('ยืนยันการลบ?')"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>`;
            }).join('');
        }

        // Helper functions (debounce, safe, etc.) ... keep as is
        function debounce(fn, wait = 300) { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), wait); }; }
        function formatDate(iso) { if (!iso) return '-'; const d = new Date(iso); return isNaN(d) ? '-' : d.toLocaleDateString('th-TH', { day: '2-digit', month: 'short', year: 'numeric' }); }
        function safe(v) { return (v ?? '-').toString().replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;'); }
        function levelBadge(u) { return `<span class="badge badge-${safe(u.level_user_color ?? 'neutral')} badge-outline badge-sm font-medium">${safe(u.level_user_label ?? u.level_user)}</span>`; }

        // ... renderPagination logic (keep mostly same, just check classes) ...
        function renderPagination(pag) {
            // ... (Logic เดิม) ...
            // เพียงแค่ตรวจสอบว่า class ที่ gen ออกมาเป็น Tailwind/DaisyUI ที่ถูกต้อง
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
                    new URLSearchParams(window.location.search).forEach((v, k) => { if (k !== 'page') url.searchParams.set(k, v); });
                    html += `<a class="join-item btn btn-sm ${p === cur ? 'btn-active btn-primary text-white' : ''}" href="${url}">${p}</a>`;
                }
            });
            html += `<a class="join-item btn btn-sm ${!next ? 'btn-disabled opacity-50' : ''}" ${next ? `href="${next}"` : ''}>»</a></div>`;

            // Per page dropdown
            const currentPer = new URLSearchParams(window.location.search).get('per_page') || 20;
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
                fetchUsers(sp);
            });
        }

        function calcWindow(cur, last) {
            const arr = [];
            if (last <= 7) { for (let i = 1; i <= last; i++) arr.push(i); return arr; }
            arr.push(1);
            if (cur > 3) arr.push('...');
            const start = Math.max(2, cur - 1);
            const end = Math.min(last - 1, cur + 1);
            for (let i = start; i <= end; i++) arr.push(i);
            if (cur < last - 2) arr.push('...');
            arr.push(last);
            return arr;
        }
    });
</script>
@endsection