@extends('layouts.app')
@section('title', 'ข้อมูลสายงาน (Section)')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h1 class="text-2xl text-kumwell-red font-bold text-center md:text-left flex-grow min-w-0 w-full md:w-auto">
            ข้อมูลสายงาน (Section)
        </h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <button onclick="openModal()" class="btn btn-success text-white shadow-sm w-full sm:w-auto">
                <i class="fa-solid fa-plus mr-2"></i> สร้างสายงานใหม่
            </button>
        </div>
    </div>

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead
                    class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-2 text-left">ลำดับ</th>
                        <th class="px-4 py-2 text-left">ชื่อ(ย่อ)</th>
                        <th class="px-4 py-2 text-left">ชื่อเต็ม</th>
                        <th class="px-4 py-2 text-left">สถานะ</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sections as $section)
                    <tr id="section-{{ $section->section_id }}" class="hover:bg-red-50/30 dark:hover:bg-gray-700/50 transition-colors group">
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Save Modal -->
<x-backend-modal id="saveModal" title="สร้างสายงานใหม่" icon="plus-circle">
    <form id="save-form" class="space-y-4">
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
            <button type="button" class="btn btn-ghost" data-close-modal="saveModal">ยกเลิก</button>
            <button type="submit" class="btn btn-success text-white px-6">บันทึกข้อมูล</button>
        </div>
    </form>
</x-backend-modal>

@endsection

@push('scripts')
<script>
    function openModal() {
        const modal = document.getElementById('saveModal');
        const form = document.getElementById('save-form');
        const modalTitle = document.getElementById('saveModal-title');
        
        form.reset();
        document.getElementById('section_id').value = '';
        
        modalTitle.innerHTML = '<i class="fa-solid fa-plus-circle mr-2 text-green-500"></i> สร้างสายงานใหม่';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Edit logic
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('section_id').value = btn.dataset.id;
            document.getElementById('section_code').value = btn.dataset.code;
            document.getElementById('section_name').value = btn.dataset.name;
            document.getElementById('section_status').value = btn.dataset.status;
            
            const modalTitle = document.getElementById('saveModal-title');
            modalTitle.innerHTML = '<i class="fa-solid fa-edit mr-2 text-yellow-500"></i> แก้ไขสายงาน';
            
            const modal = document.getElementById('saveModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    // Close logic
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

    // Save Form Submission
    document.getElementById('save-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const sectionId = document.getElementById('section_id').value;
        let url = '{{ route("sections.store") }}';
        
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        if (sectionId) {
            url = `/sections/${sectionId}`;
            data['_method'] = 'PUT';
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            location.reload();
        } else {
            console.error(await response.json());
            alert('Error saving section.');
        }
    });

    // Delete confirmation
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