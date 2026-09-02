@extends('layouts.app')
@section('title', 'จัดการประเภทพนักงาน')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl text-kumwell-red font-bold text-center md:text-left flex-grow w-full md:w-auto">
                จัดการประเภทพนักงาน
            </h1>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <button type="button" id="openCreateModal" class="btn btn-success text-white shadow-sm w-full sm:w-auto">
                    <i class="fa-solid fa-plus mr-2"></i> เพิ่มประเภท
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead
                        class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-[11px] font-bold tracking-wider border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left rounded-tl-lg">
                                ระดับพนักงาน
                            </th>
                            <th class="px-6 py-4 text-left">คำอธิบาย</th>
                            <th class="px-6 py-4 text-center">สถานะ</th>
                            
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($userTypes as $index => $userType)
                            <tr class="hover:bg-red-50/40 dark:hover:bg-gray-700/40 transition-colors group border-b border-gray-100 dark:border-gray-800/50">
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-0 border-b-0"><x-empty-state icon="users-gear" title="ไม่พบข้อมูล" description="ไม่มีข้อมูลประเภทพนักงาน" /></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <x-backend-modal id="createModal" title="เพิ่มระดับพนักงาน" icon="plus-circle">
        <form method="POST" action="{{ route('usertypes.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="create_type_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        ระดับพนักงาน<span class="text-red-500">*</span>
                    </label>
                    <input id="create_type_name" name="type_name" type="text" class="input input-bordered w-full dark:text-black"
                        placeholder="เช่น คำร้องทั่วไป" required />
                    @error('type_name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <label for="create_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">คำอธิบาย</label>
                <textarea id="create_description" name="description" rows="3"
                    class="textarea textarea-bordered w-full dark:text-black"></textarea>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                <button type="button" class="btn btn-ghost" data-close-modal="createModal">ยกเลิก</button>
                <button type="submit" class="btn btn-success text-white px-6">บันทึก</button>
            </div>
        </form>
    </x-backend-modal>

    <x-backend-modal id="editModal" title="แก้ไขระดับพนักงาน" icon="edit">
        <form method="POST" id="editForm" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_type_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ระดับพนักงาน<span
                            class="text-red-500">*</span></label>
                    <input name="type_name" id="edit_type_name" type="text"
                        class="input input-bordered w-full dark:text-black" required />
                </div>
                <div>
                    <label for="edit_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                    <select name="status" id="edit_status" class="select select-bordered w-full dark:text-black">
                        <option value="0">ใช้งาน</option>
                        <option value="1">ไม่ใช้งาน</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">คำอธิบาย</label>
                <textarea name="description" id="edit_description" rows="3"
                    class="textarea textarea-bordered w-full dark:text-black"></textarea>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                <button type="button" class="btn btn-ghost" data-close-modal="editModal">ยกเลิก</button>
                <button type="submit" class="btn btn-warning text-white px-6">อัปเดต</button>
            </div>
        </form>
    </x-backend-modal>

    @push('scripts')
        <script>
            // Utility functions to Open/Close Modals
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // --- Create Modal Logic ---
            document.getElementById('openCreateModal')?.addEventListener('click', () => {
                const form = document.getElementById('createModal').querySelector('form');
                if (form) form.reset();
                openModal('createModal');
            });

            // --- Edit Modal Logic ---
            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    document.getElementById('edit_type_name').value = btn.dataset.type_name || '';
                    document.getElementById('edit_description').value = btn.dataset.description || '';
                    document.getElementById('edit_status').value = btn.dataset.status || '0';

                    const editForm = document.getElementById('editForm');
                    editForm.action = `{{ url('usertypes') }}/${id}`;

                    openModal('editModal');
                });
            });

            // --- Global Close Handlers ---
            document.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', () => {
                    closeModal(btn.dataset.closeModal);
                });
            });

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.fixed.inset-0.flex').forEach(modal => {
                        closeModal(modal.id);
                    });
                }
            });

            document.querySelectorAll('.fixed.inset-0').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal(modal.id);
                    }
                });
            });
        </script>
    @endpush
@endsection