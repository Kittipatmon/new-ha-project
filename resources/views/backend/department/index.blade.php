@extends('layouts.app')
@section('title', 'ข้อมูลแผนก (Department)')
@section('content')



    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl text-kumwell-red font-bold text-center md:text-left flex-grow w-full md:w-auto">
                ข้อมูลแผนก (Department)
            </h1>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <button type="button" class="btn btn-success text-white shadow-sm w-full sm:w-auto" id="openCreateModal">
                    <i class="fa-solid fa-plus mr-2"></i> สร้างแผนกใหม่
                </button>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead
                        class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm font-semibold">
                        <tr>
                            <th class="px-6 py-2 text-left">ลำดับ</th>
                            <th class="px-6 py-2 text-left">รหัสฝ่าย</th>
                            <th class="px-6 py-2 text-left">ชื่อ(ย่อ)</th>
                            <th class="px-6 py-2 text-left">ชื่อเต็ม</th>
                            <th class="px-6 py-2 text-left">สถานะ</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                            <tr class="hover:bg-red-50/30 dark:hover:bg-gray-700/50 transition-colors group">
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-0 border-b-0">
                                    <x-empty-state icon="building" title="ไม่พบข้อมูลแผนก" description="ยังไม่มีข้อมูลในระบบ" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Department Create/Edit Modal --}}
    <x-backend-modal id="departmentModal" title="สร้างแผนกใหม่" icon="plus-circle">
        <form id="departmentForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">

            <div>
                <label for="division_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ฝ่าย:</label>
                <select name="division_id" id="division_id"
                    class="select select-bordered w-full dark:text-black" required>
                    <option value="" disabled selected>-- เลือกฝ่าย --</option>
                    @forelse ($divisions as $division)
                        <option value="{{ $division->division_id }}">{{ $division->division_fullname }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="department_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ(ย่อ):</label>
                    <input type="text" name="department_name" id="department_name"
                        class="input input-bordered w-full dark:text-black" required>
                </div>
                <div>
                    <label for="department_fullname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อเต็ม:</label>
                    <input type="text" name="department_fullname" id="department_fullname"
                        class="input input-bordered w-full dark:text-black">
                </div>
            </div>
            
            <div>
                <label for="department_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ:</label>
                <select name="department_status" id="department_status"
                    class="select select-bordered w-full dark:text-black" required>
                    <option value="0">ใช้งาน</option>
                    <option value="1">ไม่ใช้งาน</option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                <button type="button" class="btn btn-ghost" data-close-modal="departmentModal">ยกเลิก</button>
                <button type="submit" class="btn btn-success text-white px-6" id="submitButton">บันทึก</button>
            </div>
        </form>
    </x-backend-modal>

    {{-- Modern Delete Modal --}}
    @push('scripts')
        <script>
            const form = document.getElementById('departmentForm');
            const modalTitle = document.getElementById('departmentModal-title');
            const methodField = document.getElementById('methodField');
            const submitButton = document.getElementById('submitButton');

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

            document.getElementById('openCreateModal')?.addEventListener('click', () => {
                modalTitle.innerHTML = '<i class="fa-solid fa-plus-circle mr-2"></i> สร้างแผนกใหม่';
                form.action = '{{ route("departments.store") }}';
                methodField.value = 'POST';
                form.reset();
                submitButton.innerText = 'บันทึก';
                openModal('departmentModal');
            });

            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    modalTitle.innerHTML = '<i class="fa-solid fa-edit mr-2 text-yellow-500"></i> แก้ไขข้อมูลแผนก';
                    form.action = '{{ url("departments") }}/' + btn.dataset.id;
                    methodField.value = 'PUT';

                    document.getElementById('division_id').value = btn.dataset.division_id;
                    document.getElementById('department_name').value = btn.dataset.name;
                    document.getElementById('department_fullname').value = btn.dataset.fullname || '';
                    document.getElementById('department_status').value = btn.dataset.status;

                    submitButton.innerText = 'บันทึกการเปลี่ยนแปลง';
                    openModal('departmentModal');
                });
            });

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

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => { throw err; });
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    let msg = "เกิดข้อผิดพลาด!";
                    if (error.errors) msg = Object.values(error.errors).flat().join('\n');
                    else if (error.message) msg = error.message;
                    alert(msg);
                });
            });

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
        </script>
    @endpush
@endsection