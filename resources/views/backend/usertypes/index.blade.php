@extends('layouts.sidebar')
@section('title', 'จัดการประเภทพนักงาน')
@section('content')
    <div class="container mx-auto px-4 py-3">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-4">
            <!-- <h1 class="text-2xl font-bold text-gray-800 dark:text-white">จัดการประเภทคำร้อง</h1> -->
            <button type="button" id="openCreateModal" class="btn btn-success text-white shadow-md">
                <i class="fa-solid fa-plus mr-2"></i> เพิ่มประเภท
            </button>
        </div>

        <div class=" dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead
                        class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm font-semibold">
                        <tr>
                            <th class="px-6 py-2 text-left">
                                ระดับพนักงาน
                            </th>
                            <th class="px-6 py-2 text-left">คำอธิบาย</th>
                            <th class="px-6 py-2 text-center">สถานะ</th>
                            <th class="px-6 py-2 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($userTypes as $index => $userType)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition duration-150">
                                <td class="px-6 py-2 font-mono text-sm text-blue-600 dark:text-blue-400">
                                    {{ $userType->type_name }}
                                </td>
                                <td class="px-6 py-2 text-gray-500 dark:text-gray-400 truncate max-w-xs"
                                    title="{{ $userType->description }}">
                                    {{ $userType->description ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-center">
                                    @if($userType->status == '0')
                                        <span class="badge badge-success text-white gap-1 p-3">
                                            <i class="fa-solid fa-check-circle text-xs"></i> ใช้งาน
                                        </span>
                                    @else
                                        <span class="badge badge-error text-white gap-1 p-3">
                                            <i class="fa-solid fa-times-circle text-xs"></i> ไม่ใช้งาน
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-2 text-center space-x-2">
                                    <button type="button" class="btn btn-warning btn-sm btn-square text-white editBtn shadow-sm"
                                        data-id="{{ $userType->id }}" data-type_name="{{ $userType->type_name }}"
                                        data-description="{{ $userType->description }}" data-status="{{ $userType->status }}"
                                        title="แก้ไข">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-error btn-sm btn-square text-white deleteBtn shadow-sm"
                                        data-id="{{ $userType->id }}" data-name="{{ $userType->type_name }}" title="ลบ">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-50"></i>
                                        <p>ไม่พบข้อมูลประเภทคำร้อง</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="createModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                    <i class="fa-solid fa-plus-circle mr-2 text-green-500"></i>เพิ่มระดับพนักงาน
                </h2>

                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-close-create>
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('usertypes.store') }}" class="px-6 mb-4 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รหัส <span class="text-red-500">*</span></label>
                        <input name="code" type="text" class="input input-bordered w-full dark:text-black" placeholder="เช่น REQ-01" required />
                        @error('code') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div> -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            ระดับพนักงาน<span class="text-red-500">*</span>
                        </label>
                        <input name="type_name" type="text" class="input input-bordered w-full dark:text-black"
                            placeholder="เช่น คำร้องทั่วไป" required />
                        @error('type_name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>


                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ (EN)</label>
                    <input name="name_en" type="text" class="input input-bordered w-full" />
                </div> -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">คำอธิบาย</label>
                    <textarea name="description" rows="3"
                        class="textarea textarea-bordered w-full dark:text-black"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                    <button type="button" class="btn btn-ghost" data-close-create>ยกเลิก</button>
                    <button type="submit" class="btn btn-success text-white px-6">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100"><i
                        class="fa-solid fa-edit mr-2 text-yellow-500"></i>แก้ไขระดับพนักงาน</h2>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-close-edit>
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" id="editForm" class="px-6 mb-4 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รหัส</label>
                        <input name="code" id="edit_code" type="text" class="input input-bordered w-full dark:text-black" required />
                    </div> -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ระดับพนักงาน<span
                                class="text-red-500">*</span></label>
                        <input name="type_name" id="edit_type_name" type="text"
                            class="input input-bordered w-full dark:text-black" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                        <select name="status" id="edit_status" class="select select-bordered w-full dark:text-black">
                            <option value="0">ใช้งาน</option>
                            <option value="1">ไม่ใช้งาน</option>
                        </select>
                    </div>
                </div>
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ (EN)</label>
                    <input name="name_en" id="edit_name_en" type="text" class="input input-bordered w-full" />
                </div> -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">คำอธิบาย</label>
                    <textarea name="description" id="edit_description" rows="3"
                        class="textarea textarea-bordered w-full dark:text-black"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                    <button type="button" class="btn btn-ghost" data-close-edit>ยกเลิก</button>
                    <button type="submit" class="btn btn-warning text-white px-6">อัปเดต</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-100">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">ยืนยันการลบ?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mb-6">
                    คุณต้องการลบรายการ <span id="deleteName" class="font-bold text-gray-800 dark:text-white"></span>
                    ใช่หรือไม่?<br>
                    การกระทำนี้ไม่สามารถย้อนกลับได้
                </p>
                <form method="POST" id="deleteForm" class="flex justify-center space-x-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-ghost" data-close-delete>ยกเลิก</button>
                    <button type="submit" class="btn btn-error text-white px-6">ยืนยันลบ</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Utility functions to Open/Close Modals
            function openModal(modal) {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                // Animation effect (optional)
                setTimeout(() => {
                    modal.firstElementChild.classList.remove('scale-95', 'opacity-0');
                    modal.firstElementChild.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Elements
            const createModal = document.getElementById('createModal');
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');

            // --- Create Modal Logic ---
            document.getElementById('openCreateModal')?.addEventListener('click', () => {
                // Optional: Reset form when opening create modal
                const form = createModal.querySelector('form');
                if (form) form.reset();
                openModal(createModal);
            });

            // --- Edit Modal Logic ---
            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    // Use Optional Chaining (?.) for safety
                    // document.getElementById('edit_code').value = btn.dataset.code || '';
                    document.getElementById('edit_type_name').value = btn.dataset.type_name || '';
                    // document.getElementById('edit_name_en').value = btn.dataset.name_en || '';
                    document.getElementById('edit_description').value = btn.dataset.description || '';
                    document.getElementById('edit_status').value = btn.dataset.status || '0';
                    // const categorySelect = document.getElementById('edit_category_id');
                    // if (categorySelect) {
                    //     categorySelect.value = btn.dataset.category_id || '';
                    // }

                    const editForm = document.getElementById('editForm');
                    // Ensure the route URL is correct
                    editForm.action = `{{ url('usertypes') }}/${id}`;

                    openModal(editModal);
                });
            });

            // --- Delete Modal Logic ---
            document.querySelectorAll('.deleteBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const name = btn.dataset.name;

                    document.getElementById('deleteName').textContent = name;
                    const deleteForm = document.getElementById('deleteForm');
                    deleteForm.action = `{{ url('usertypes') }}/${id}`;

                    openModal(deleteModal);
                });
            });

            // --- Global Close Handlers ---
            // Close buttons (X and Cancel)
            document.querySelectorAll('[data-close-create]').forEach(btn => btn.addEventListener('click', () => closeModal(
                createModal)));
            document.querySelectorAll('[data-close-edit]').forEach(btn => btn.addEventListener('click', () => closeModal(
                editModal)));
            document.querySelectorAll('[data-close-delete]').forEach(btn => btn.addEventListener('click', () => closeModal(
                deleteModal)));

            // Close on Escape key
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    [createModal, editModal, deleteModal].forEach(m => closeModal(m));
                }
            });

            // Close when clicking outside (Backdrop)
            [createModal, editModal, deleteModal].forEach(modal => {
                modal?.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal(modal);
                    }
                });
            });
        </script>
    @endpush
@endsection