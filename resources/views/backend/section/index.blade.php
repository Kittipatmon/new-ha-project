@extends('layouts.sidebar')
@section('title', 'ข้อมูลสายงาน (Section)')
@section('content')
<div class="max-w-8xl rounded-xl shadow-xl">
    <div class="flex justify-between items-center">
        <!-- <h1 class="text-xl font-semibold mb-4">ข้อมูลสายงาน (Section)</h1> -->
        <div class="mb-4">
            <button onclick="openModal()" class="btn btn-success text-white">
                <i class="fa fa-plus mr-1"></i>
                สร้างสายงานใหม่
            </button>
        </div>
    </div>

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class=" dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead
                    class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-2 text-left">ลำดับ</th>
                        <th class="px-4 py-2 text-left">ชื่อ(ย่อ)</th>
                        <th class="px-4 py-2 text-left">ชื่อเต็ม</th>
                        <th class="px-4 py-2 text-left">สถานะ</th>
                        <th class="px-4 py-2 text-left">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sections as $section)
                    <tr id="section-{{ $section->section_id }}">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $section->section_code }}</td>
                        <td class="px-4 py-2">{{ $section->section_name }}</td>
                        <td class="px-4 py-2">
                            @if ($section->section_status === 0)
                            <span class="badge badge-success text-white">ใช้งาน</span>
                            @else
                            <span class="badge badge-error text-white">ไม่ใช้งาน</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <button onclick="openModal({{ json_encode($section) }})" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i>
                            </button>
                            <form id="delete-form-{{ $section->section_id }}"
                                action="{{ route('sections.destroy', $section->section_id) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-error"
                                    onclick="showDeleteModal('{{ $section->section_id }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Save Modal -->
<div id="save-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg transform transition-all scale-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                <i id="modal-icon" class="fa-solid fa-plus-circle mr-2 text-green-500"></i>
                <span id="modal-title">สร้างสายงานใหม่</span>
            </h2>
            <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form id="save-form" class="px-6 mb-4 space-y-4">
            @csrf
            <input type="hidden" id="section_id" name="section_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="section_code">ชื่อ(ย่อ)</label>
                <input type="text" id="section_code" name="section_code" class="input input-bordered w-full dark:border-gray-600 dark:text-black" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="section_name">ชื่อเต็ม</label>
                <input type="text" id="section_name" name="section_name" class="input input-bordered w-full dark:border-gray-600 dark:text-black" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="section_status">สถานะ</label>
                <select id="section_status" name="section_status" class="select select-bordered w-full dark:border-gray-600 dark:text-black" required>
                    <option value="0">ใช้งาน</option>
                    <option value="1">ไม่ใช้งาน</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">ยกเลิก</button>
                <button type="submit" class="btn btn-success text-white px-6">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-100">
        <div class="p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-3xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">ยืนยันการลบ?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-300 mb-6">
                คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้? <br>
                การกระทำนี้ไม่สามารถย้อนกลับได้
            </p>
            <div class="flex justify-center space-x-3">
                <button type="button" class="btn btn-ghost" onclick="hideDeleteModal()">ยกเลิก</button>
                <button id="confirm-delete-btn" class="btn btn-error text-white px-6">ลบ</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentSectionId = null;

function openModal(section = null) {
    const modal = document.getElementById('save-modal');
    const form = document.getElementById('save-form');
    const modalTitle = document.getElementById('modal-title');
    const modalIcon = document.getElementById('modal-icon');

    form.reset();
    document.getElementById('section_id').value = '';
    currentSectionId = null;

    if (section) {
        modalTitle.textContent = 'แก้ไขสายงาน';
        modalIcon.className = 'fa-solid fa-edit mr-2 text-yellow-500';
        document.getElementById('section_id').value = section.section_id;
        document.getElementById('section_code').value = section.section_code;
        document.getElementById('section_name').value = section.section_name;
        document.getElementById('section_status').value = section.section_status;
        currentSectionId = section.section_id;
    } else {
        modalTitle.textContent = 'สร้างสายงานใหม่';
        modalIcon.className = 'fa-solid fa-plus-circle mr-2 text-green-500';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('save-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('save-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const sectionId = document.getElementById('section_id').value;
    let url = '{{ route("sections.store") }}';
    let method = 'POST';

    const data = {};
    formData.forEach((value, key) => data[key] = value);

    if (sectionId) {
        url = `/sections/${sectionId}`;
        data['_method'] = 'PUT';
    }

    const response = await fetch(url, {
        method: 'POST', // Always POST, with _method for PUT
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    });

    if (response.ok) {
        location.reload(); // Easiest way to see changes
    } else {
        const errors = await response.json();
        // Handle errors, e.g., display them to the user
        console.error(errors);
        alert('Error saving section.');
    }
});

function showDeleteModal(id) {
    const modal = document.getElementById('delete-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    const form = document.getElementById('delete-form-' + id);
    const confirmBtn = document.getElementById('confirm-delete-btn');
    confirmBtn.onclick = function() {
        form.submit();
    }
}

function hideDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal on escape key press
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal();
        hideDeleteModal();
    }
});
</script>
@endpush