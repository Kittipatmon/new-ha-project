@extends('layouts.sidebar')
@section('title', 'ข้อมูลแผนก (Department)')
@section('content')



    <div class="max-w-8xl rounded-xl shadow-xl">
        <div class="flex justify-between items-center">
            <div class="mb-4">
                <button type="button" class="btn btn-success text-white" onclick="openCreateModal()">
                    <i class="fa fa-plus mr-1"></i>
                    สร้างแผนกใหม่
                </button>
            </div>
        </div>
        <div class=" dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
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
                            <th class="px-6 py-2 text-left">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($departments as $department)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition duration-150">
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">
                                    {{ $department->division->division_fullname ?? '-' }}
                                </td>
                                <td class="px-4 py-2">{{ $department->department_name }}</td>
                                <td class="px-4 py-2">{{ $department->department_fullname }}</td>
                                <td class="px-4 py-2">
                                    @if ($department->department_status === 0)
                                        <span class="badge badge-success badge-sm text-white">ใช้งาน</span>
                                    @else
                                        <span class="badge badge-error badge-sm text-white">ไม่ใช้งาน</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <button type="button" class="btn btn-sm btn-warning"
                                        onclick='openEditModal(@json($department))'>
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-error"
                                        onclick="deleteDepartment({{ $department->department_id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Department Create/Edit Modal --}}
    <div id="departmentModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm transition-all duration-300">
        <div class="relative top-20 mx-auto p-5 w-full max-w-lg shadow-lg rounded-md">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modalTitle">
                        สร้างแผนกใหม่
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        onclick="closeModal()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <form id="departmentForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="methodField" value="POST">

                        <div class="mb-4">
                            <label for="division_id" class="block text-sm font-bold mb-2">ฝ่าย:</label>
                            <select name="division_id" id="division_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                                <option value="" disabled selected>-- เลือกฝ่าย --</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->division_id }}">{{ $division->division_fullname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="department_name" class="block text-sm font-bold mb-2">ชื่อ(ย่อ):</label>
                            <input type="text" name="department_name" id="department_name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-700"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="department_fullname" class="block text-sm font-bold mb-2">ชื่อเต็ม:</label>
                            <input type="text" name="department_fullname" id="department_fullname"
                                class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-700">
                        </div>
                        <div class="mb-4">
                            <label for="department_status" class="block text-sm font-bold mb-2">สถานะ:</label>
                            <select name="department_status" id="department_status"
                                class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-700"
                                required>
                                <option value="0">ใช้งาน</option>
                                <option value="1">ไม่ใช้งาน</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="btn btn-ghost" onclick="closeModal()">ยกเลิก</button>
                            <button type="submit" class="btn btn-success text-white" id="submitButton">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern Delete Modal --}}
    <div id="deleteModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-all duration-300">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-100 p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-50 mb-6">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-red-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">ยืนยันการลบ?</h3>
            <p class="text-base text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?<br>
                การกระทำนี้ไม่สามารถย้อนกลับได้
            </p>
            <div class="flex justify-center items-center gap-4">
                <button type="button" class="btn btn-ghost px-8" onclick="closeDeleteModal()">ยกเลิก</button>
                <button type="button" id="confirmDeleteBtn"
                    class="btn btn-error text-white px-10 shadow-lg shadow-red-200">ลบ</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const form = document.getElementById('departmentForm');
            const modalTitle = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const submitButton = document.getElementById('submitButton');
            const departmentModal = document.getElementById('departmentModal');
            const deleteModal = document.getElementById('deleteModal');

            function openCreateModal() {
                modalTitle.innerText = 'สร้างแผนกใหม่';
                form.action = '{{ route("departments.store") }}';
                methodField.value = 'POST';
                form.reset();
                submitButton.innerText = 'บันทึก';
                showModal();
            }

            function openEditModal(department) {
                modalTitle.innerText = 'แก้ไขข้อมูลแผนก';
                form.action = '{{ url("departments") }}/' + department.department_id;
                methodField.value = 'PUT';

                document.getElementById('division_id').value = department.division_id;
                document.getElementById('department_name').value = department.department_name;
                document.getElementById('department_fullname').value = department.department_fullname || '';
                document.getElementById('department_status').value = department.department_status;

                submitButton.innerText = 'บันทึกการเปลี่ยนแปลง';
                showModal();
            }

            function showModal() {
                departmentModal.classList.remove('hidden');
                departmentModal.classList.add('flex');
            }

            function closeModal() {
                departmentModal.classList.add('hidden');
                departmentModal.classList.remove('flex');
            }

            function openDeleteModal() {
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            }

            function closeDeleteModal() {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            }

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
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
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
                        console.error('Error:', error);
                        let msg = "เกิดข้อผิดพลาด!";
                        if (error.errors) {
                            msg = Object.values(error.errors).flat().join('\n');
                        } else if (error.message) {
                            msg = error.message;
                        }
                        alert(msg);
                    });
            });

            function deleteDepartment(id) {
                openDeleteModal();
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                newConfirmBtn.addEventListener('click', function () {
                    const formData = new FormData();
                    formData.append('_method', 'DELETE');
                    fetch('{{ url("departments") }}/' + id, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            closeDeleteModal();
                        });
                });
            }

            // Close on backdrop click
            [departmentModal, deleteModal].forEach(m => {
                m.addEventListener('click', (e) => {
                    if (e.target === m) {
                        m === departmentModal ? closeModal() : closeDeleteModal();
                    }
                });
            });
        </script>
    @endpush
@endsection