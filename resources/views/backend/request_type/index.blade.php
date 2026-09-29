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
                    <span class="text-slate-500">Request Settings</span>
                    <span class="mx-2 text-slate-400">/</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">ตัวเลือกการร้องขอ</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-list"></i>
                    </div>
                    <span>ตัวเลือกการร้องขอ</span>
                </h1>
            </div>

            <div>
                <button type="button" id="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 transition cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    เพิ่มประเภทตัวเลือกการร้องขอ
                </button>
            </div>
        </div>

        <div class="space-y-6">
            <div class="dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead
                        class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm font-semibold">
                        <tr>
                            <th class="px-6 py-2 text-left">ลำดับ</th>
                            <!-- <th class="px-6 py-2 text-left">รหัส</th> -->
                            <th class="px-6 py-2 text-left">ชื่อตัวเลือกการร้องขอ</th>
                            <th class="px-6 py-2 text-left">ประเภทคำร้อง</th>
                            <th class="px-6 py-2 text-center">สถานะ</th>
                            <th class="px-6 py-2 text-left">คำอธิบาย</th>
                            <th class="px-6 py-2 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($requesttypes as $index => $requesttype)
                            <tr class="hover:bg-red-50/30 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-2">{{ $loop->iteration }}</td>
                                <!-- <td class="px-6 py-2 font-mono text-sm text-blue-600 dark:text-blue-400">{{ $requesttype->code ?? '-' }}</td> -->
                                <td class="px-6 py-2 font-mono text-sm text-blue-600 dark:text-blue-400">
                                    {{ $requesttype->name_th }}</td>
                                <td class="px-6 py-2 font-medium">{{ $requesttype->requestCategory->name_th ?? '-' }}</td>
                                <td class="px-6 py-2 text-center">
                                    @if($requesttype->is_active == '0')
                                        <span class="badge badge-success text-white whitespace-nowrap px-3 py-1.5 h-auto text-[10px] font-bold gap-1 border-none">
                                            <i class="fa-solid fa-circle-check"></i> ใช้งาน
                                        </span>
                                    @else
                                        <span class="badge badge-error text-white whitespace-nowrap px-3 py-1.5 h-auto text-[10px] font-bold gap-1 border-none">
                                            <i class="fa-solid fa-circle-xmark"></i> ไม่ใช้งาน
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-2 text-gray-500 dark:text-gray-400 truncate max-w-xs"
                                    title="{{ $requesttype->description }}">
                                    {{ $requesttype->description ?? '-' }}
                                </td>
                                <td class="px-6 py-2 text-center space-x-2">
                                    <button type="button" class="btn btn-warning btn-sm btn-square text-white editBtn shadow-sm"
                                        data-id="{{ $requesttype->id }}" data-code="{{ $requesttype->code }}"
                                        data-name_th="{{ $requesttype->name_th }}" data-name_en="{{ $requesttype->name_en }}"
                                        data-description="{{ $requesttype->description }}"
                                        data-is_active="{{ $requesttype->is_active }}"
                                        data-category_id="{{ $requesttype->requestCategory->id ?? '' }}" title="แก้ไข">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                     <form action="{{ route('request-types.destroy', $requesttype->id) }}" method="POST" class="inline form-delete">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="btn btn-error btn-sm btn-square text-white shadow-sm" title="ลบ">
                                             <i class="fa-solid fa-trash"></i>
                                         </button>
                                     </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-0 border-b-0">
                                    <x-empty-state icon="file-signature" title="ไม่พบตัวเลือกการร้องขอ" description="ยังไม่มีข้อมูลในระบบ" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>


    <div id="createModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                    <i class="fa-solid fa-plus-circle mr-2 text-green-500"></i>เพิ่มตัวเลือกการร้องขอ
                </h2>

                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-close-create>
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('request-types.store') }}" class="px-6 mb-4 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รหัส <span class="text-red-500">*</span></label>
                        <input name="code" type="text" class="input input-bordered w-full dark:text-black" placeholder="เช่น REQ-01" required />
                        @error('code') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div> -->
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                        <select name="is_active" class="select select-bordered w-full">
                            <option value="0">ใช้งาน</option>
                            <option value="1">ไม่ใช้งาน</option>
                        </select>
                    </div> -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            ชื่อ<span class="text-red-500">*</span>
                        </label>
                        <input name="name_th" type="text" class="input input-bordered w-full dark:text-black"
                            placeholder="เช่น คำร้องทั่วไป" required />
                        @error('name_th') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภทคำร้อง</label>
                        <select name="category_id" class="select select-bordered w-full dark:text-black" required>
                            <option value="" disabled selected>-- เลือกประเภทคำร้อง --</option>
                            @foreach($requestcategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name_th }}</option>
                            @endforeach
                        </select>
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
                        class="fa-solid fa-edit mr-2 text-yellow-500"></i>แก้ไขตัวเลือกการร้องขอ</h2>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ<span
                                class="text-red-500">*</span></label>
                        <input name="name_th" id="edit_name_th" type="text"
                            class="input input-bordered w-full dark:text-black" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                        <select name="is_active" id="edit_is_active" class="select select-bordered w-full dark:text-black">
                            <option value="0">ใช้งาน</option>
                            <option value="1">ไม่ใช้งาน</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="edit_category_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภทคำร้อง</label>
                    <select name="category_id" id="edit_category_id" class="select select-bordered w-full dark:text-black"
                        required>
                        <option value="" disabled selected>-- เลือกประเภทคำร้อง --</option>
                        @foreach($requestcategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name_th }}</option>
                        @endforeach
                    </select>
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
                    document.getElementById('edit_name_th').value = btn.dataset.name_th || '';
                    // document.getElementById('edit_name_en').value = btn.dataset.name_en || '';
                    document.getElementById('edit_description').value = btn.dataset.description || '';
                    document.getElementById('edit_is_active').value = btn.dataset.is_active || '0';
                    const categorySelect = document.getElementById('edit_category_id');
                    if (categorySelect) {
                        categorySelect.value = btn.dataset.category_id || '';
                    }

                    const editForm = document.getElementById('editForm');
                    // Ensure the route URL is correct
                    editForm.action = `{{ url('request-types') }}/${id}`;

                    openModal(editModal);
                });
            });

            // --- Delete Confirmation Logic ---
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

            // --- Global Close Handlers ---
            // Close buttons (X and Cancel)
            document.querySelectorAll('[data-close-create]').forEach(btn => btn.addEventListener('click', () => closeModal(createModal)));
            document.querySelectorAll('[data-close-edit]').forEach(btn => btn.addEventListener('click', () => closeModal(editModal)));

            // Close on Escape key
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    [createModal, editModal].forEach(m => closeModal(m));
                }
            });

            // Close when clicking outside (Backdrop)
            [createModal, editModal].forEach(modal => {
                modal?.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal(modal);
                    }
                });
            });
        </script>
    @endpush
@endsection