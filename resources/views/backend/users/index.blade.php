@extends('layouts.app')
@section('content')

<div>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
        <h1 class="text-2xl text-kumwell-red font-bold text-center md:text-left flex-grow w-full md:w-auto">
            ข้อมูลพนักงาน
        </h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <a href="{{ route('users.create') }}" class="btn btn-success text-white w-full sm:w-auto shadow-sm">
                <i class="fa-solid fa-plus mr-1"></i>
                เพิ่มพนักงานใหม่
            </a>
            <button type="button" id="toggle-filter" class="btn btn-warning btn-sm w-full sm:w-auto shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
        </div>
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
        class="mb-6 border border-gray-100 rounded-xl p-6 bg-white dark:bg-gray-800 dark:border-gray-700 shadow-sm hidden transition-all duration-300 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-kumwell-red to-red-500"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end mb-4">
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
                <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">ประเภทพนักงาน</span>
                <select name="employee_type" class="select select-bordered select-sm w-full dark:bg-gray-700">
                    <option value="">ทั้งหมด</option>
                    <option value="รายเดือน"
                        {{ request('employee_type') === 'รายเดือน' ? 'selected' : '' }}>รายเดือน</option>
                    <option value="รายวัน"
                        {{ request('employee_type') === 'รายวัน' ? 'selected' : '' }}>รายวัน</option>
                </select>
            </div>
            <div>
                <span class="label-text mb-1 text-xs text-gray-500 dark:text-gray-400">สถานะ Active</span>
                @php $statusOptions = \App\Models\User::getStatusOptions(); @endphp
                <select name="status" class="select select-bordered select-sm w-full dark:bg-gray-700">
                    <option value="">ทั้งหมด</option>
                    @foreach($statusOptions as $value => $option)
                    @php
                        $label = is_array($option) ? ($option['label'] ?? '') : $option;
                    @endphp
                    <option value="{{ $value }}"
                        {{ (string)request('status') === (string)$value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <select name="department" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">แผนก (ทั้งหมด)</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->department_id }}"
                    {{ (string)request('department') === (string)$dept->department_id ? 'selected' : '' }}>
                    {{ $dept->department_name }}
                </option>
                @endforeach
            </select>

            <select name="division" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">ฝ่าย (ทั้งหมด)</option>
                @foreach($divisions as $div)
                <option value="{{ $div->division_id }}"
                    {{ (string)request('division') === (string)$div->division_id ? 'selected' : '' }}>
                    {{ $div->division_name }}
                </option>
                @endforeach
            </select>

            <select name="section" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">สายงาน (ทั้งหมด)</option>
                @foreach($sections as $sect)
                <option value="{{ $sect->section_id }}"
                    {{ (string)request('section') === (string)$sect->section_id ? 'selected' : '' }}>
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
                <option value="{{ $value }}" {{ (string)$selectedLevel === (string)$value ? 'selected' : '' }}>
                    {{ $meta['label'] }}
                </option>
                @endforeach
            </select>

            @php
            $hrStatusOptions = \App\Models\User::getHrStatusOptions();
            $selectedHrStatus = request('hr_status');
            @endphp

            <select name="hr_status" class="select select-bordered select-sm w-full dark:bg-gray-700">
                <option value="">สถานะ HR (ทั้งหมด)</option>
                @foreach($hrStatusOptions as $value => $option)
                @php
                // รองรับทั้งกรณีเป็น string ตรง ๆ หรือเป็น array ที่มี key 'label'
                $label = is_array($option) ? ($option['label'] ?? '') : $option;
                @endphp
                <option value="{{ $value }}" {{ (string)$selectedHrStatus === (string)$value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>


        </div>

        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm text-gray-500">
                <i class="fa-solid fa-rotate-left mr-1"></i> ล้างค่า
            </a>
            <button type="submit" class="btn btn-primary btn-sm text-white">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> ค้นหา
            </button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 relative"
        id="table-wrap">

        <div id="loader"
            class="hidden absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm flex items-center justify-center z-20">
            <span class="loading loading-spinner loading-lg text-primary"></span>
        </div>
        <div>
            <div class="p-4 text-sm text-gray-500">
                แสดงผล {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} จาก
                ทั้งหมด {{ $users->total() }} รายการ
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="table w-full">
                <thead
                    class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-[11px] font-bold tracking-wider border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="py-4 px-4 rounded-tl-lg whitespace-nowrap">รหัสพนักงาน</th>
                        <th class="px-4 whitespace-nowrap">ชื่อ-นามสกุล</th>
                        <th class="px-4 whitespace-nowrap">แผนก</th>
                        <th class="px-4 whitespace-nowrap">ฝ่าย</th>
                        <th class="px-4 whitespace-nowrap">สายงาน</th>
                        <th class="px-4 whitespace-nowrap">ตำแหน่ง</th>
                        <th class="px-4 whitespace-nowrap">ประเภทพนักงาน</th>
                        <!-- <th class="whitespace-nowrap">เริ่มงาน</th> -->
                        <th class="px-4 whitespace-nowrap">ระดับ</th>
                        <th class="px-4 whitespace-nowrap">สถานะ HR</th>
                        <th class="px-4 whitespace-nowrap">สถานะ Active</th>
                        <th class="w-24 text-center px-4 rounded-tr-lg whitespace-nowrap">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="users-body"
                    class="text-gray-700 dark:text-gray-300 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-red-50/30 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="font-medium whitespace-nowrap">{{ $user->employee_code }}</td>
                        <td>
                            <div class="font-bold min-w-[150px] break-words whitespace-normal">{{ $user->fullname }}</div>
                        </td>
                        <td class="min-w-[120px] break-words whitespace-normal">{{ $user->department->department_name ?? '-' }}</td>
                        <td class="min-w-[120px] break-words whitespace-normal">{{ $user->division->division_name ?? '-' }}</td>
                        <td class="min-w-[100px] break-words whitespace-normal">{{ $user->section->section_code ?? '-' }}</td>
                        <td class="min-w-[120px] break-words whitespace-normal">{{ $user->position }}</td>
                        <td class="whitespace-nowrap">
                            {{ $user->employee_type ?? '-' }}
                        </td>
                        <!-- <td class="whitespace-nowrap">
                            {{ $user->startwork_date ? \Carbon\Carbon::parse($user->startwork_date)->format('d M Y') : '-' }}
                        </td> -->
                        <td class="whitespace-nowrap">
                            <x-status-badge :color="$user->level_user_color" :label="$user->level_user_label" />
                        </td>
                        <td class="whitespace-nowrap">
                            <x-status-badge :color="$user->hr_status_color" :label="$user->hr_status_label" />
                        </td>
                        <td class="whitespace-nowrap">
                            <x-status-badge :color="$user->status_color" :label="$user->status_label" />
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="flex justify-center gap-1">
                                <x-action-button href="{{ route('users.show', $user->id) }}" action="ดูข้อมูล" icon="eye" color="info" />
                                <x-action-button href="{{ route('users.edit', $user->id) }}" action="แก้ไข" icon="pen-to-square" color="warning" />
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <x-action-button type="submit" action="ลบ" icon="trash" color="error" />
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="p-0 border-b-0">
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

    // --- Filter Toggle ---
    const btn = document.getElementById('toggle-filter');
    const panel = document.getElementById('filter-form');
    if (btn && panel) {
        btn.addEventListener('click', function() {
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                panel.animate([
                    { opacity: 0, transform: 'translateY(-10px)' },
                    { opacity: 1, transform: 'translateY(0)' }
                ], { duration: 300, easing: 'ease-out' });
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