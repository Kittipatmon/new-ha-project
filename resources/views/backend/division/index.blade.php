@extends('layouts.app')
@section('title', 'ข้อมูลฝ่าย (Division)')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl text-kumwell-red font-bold text-center md:text-left flex-grow min-w-0 w-full md:w-auto">
                ข้อมูลฝ่าย (Division)
            </h1>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <button type="button" class="btn btn-success text-white shadow-sm w-full sm:w-auto" id="openCreateModal">
                    <i class="fa-solid fa-plus mr-2"></i> สร้างฝ่ายใหม่
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
                            <th class="px-6 py-2 text-left">สายงาน</th>
                            <th class="px-6 py-2 text-left">ชื่อ(ย่อ)</th>
                            <th class="px-6 py-2 text-left">ชื่อเต็ม</th>
                            <th class="px-6 py-2 text-left">สถานะ</th>
                            
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($divisions as $division)
                            <tr class="hover:bg-red-50/30 dark:hover:bg-gray-700/50 transition-colors group">
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-0 border-b-0">
                                    <x-empty-state icon="sitemap" title="ไม่พบข้อมูลฝ่าย" description="ยังไม่มีข้อมูลในระบบ" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-backend-modal id="divisionModal" title="สร้างฝ่ายใหม่" icon="plus-circle">
        <form id="divisionForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="id" id="divisionId">

            <div>
                <label for="section_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รหัสฝ่าย:</label>
                <select name="section_id" id="section_id"
                    class="select select-bordered w-full dark:text-black" required>
                    <option value="" disabled selected>-- เลือกรหัสฝ่าย --</option>
                    @forelse ($sections as $section)
                        <option value="{{ $section->section_id }}">{{ $section->section_code }} -
                            {{ $section->section_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="division_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ(ย่อ):</label>
                    <input type="text" name="division_name" id="division_name"
                        class="input input-bordered w-full dark:text-black" required>
                </div>
                <div>
                    <label for="division_fullname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อเต็ม:</label>
                    <input type="text" name="division_fullname" id="division_fullname"
                        class="input input-bordered w-full dark:text-black" required>
                </div>
            </div>
            
            <div>
                <label for="division_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ:</label>
                <select name="division_status" id="division_status"
                    class="select select-bordered w-full dark:text-black" required>
                    <option value="0">ใช้งาน</option>
                    <option value="1">ไม่ใช้งาน</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                <button type="button" class="btn btn-ghost" data-close-modal="divisionModal">ยกเลิก</button>
                <button type="submit" class="btn btn-success text-white px-6" id="submitButton">บันทึกข้อมูล</button>
            </div>
        </form>
    </x-backend-modal>

    @endsection

@push('scripts')
    <script>
        const form = document.getElementById('divisionForm');
        const modalTitle = document.getElementById('divisionModal-title');
        const methodField = document.getElementById('methodField');
        const divisionId = document.getElementById('divisionId');
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
            modalTitle.innerHTML = '<i class="fa-solid fa-plus-circle mr-2"></i> สร้างฝ่ายใหม่';
            form.action = '{{ route("divisions.store") }}';
            methodField.value = 'POST';
            divisionId.value = '';
            form.reset();

            document.getElementById('section_id').value = "";
            document.getElementById('division_status').value = "0";

            submitButton.innerText = 'บันทึกข้อมูล';
            openModal('divisionModal');
        });

        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                modalTitle.innerHTML = '<i class="fa-solid fa-edit mr-2 text-yellow-500"></i> แก้ไขฝ่าย';
                const id = btn.dataset.id;
                form.action = '{{ url("divisions") }}/' + id;
                methodField.value = 'PUT';
                divisionId.value = id;

                document.getElementById('section_id').value = btn.dataset.section_id;
                document.getElementById('division_name').value = btn.dataset.name;
                document.getElementById('division_fullname').value = btn.dataset.fullname;
                document.getElementById('division_status').value = btn.dataset.status;

                submitButton.innerText = 'บันทึกข้อมูล';
                openModal('divisionModal');
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
            const action = form.action;
            const formData = new FormData(form);

            fetch(action, {
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