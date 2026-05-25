@extends('layouts.sidebar')

@section('title', 'สร้างประกาศรับสมัครงาน')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('backend.recruitment.posts.index') }}"
                class="text-gray-400 hover:text-kumwell-red transition-colors">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold dark:text-white text-gray-800">สร้างประกาศรับสมัครงานใหม่</h2>
        </div>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-2xl p-4">
                <div class="flex items-center gap-3 text-red-800 dark:text-red-300 font-bold mb-2">
                    <i class="fa-solid fa-circle-exclamation text-xl"></i>
                    <span>เกิดข้อผิดพลาดในการบันทึกข้อมูล</span>
                </div>
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($recruitmentRequest)
            <div
                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl p-4 flex items-center gap-4">
                <i class="fa-solid fa-circle-check text-blue-500 text-xl"></i>
                <div class="text-sm">
                    <p class="text-blue-800 dark:text-blue-300 font-bold">อ้างอิงจากคำขอเลขที่
                        #{{ $recruitmentRequest->request_no }}</p>
                    <p class="text-blue-600 dark:text-blue-400">ระบบได้ดึงข้อมูลเบื้องต้นจากคำขอที่ได้รับการอนุมัติมาให้แล้ว</p>
                </div>
            </div>
        @endif

        <form action="{{ route('backend.recruitment.posts.store') }}" method="POST"
            class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 space-y-8">
            @csrf
            <input type="hidden" name="recruitment_request_id"
                value="{{ $recruitmentRequest ? $recruitmentRequest->id : '' }}">

            <div class="space-y-6">
                <h3
                    class="text-lg font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                    ข้อมูลประกาศ (Basic Info)</h3>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">หัวข้อประกาศ (Job Title)</label>
                    <input type="text" name="title"
                        value="{{ $recruitmentRequest ? ($recruitmentRequest->position_name ?: ($recruitmentRequest->jobPosition?->position_name ?? '')) : '' }}"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"
                        required placeholder="ชื่อตำแหน่งงานที่ต้องการประกาศ">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">แผนก (Department)</label>
                        <select name="department_id"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->department_id }}" {{ (isset($recruitmentRequest) && $recruitmentRequest->department_id == $dept->department_id) ? 'selected' : '' }}>
                                    {{ $dept->department_fullname }} ({{ $dept->department_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ตำแหน่ง (Position)</label>
                        <input type="text" name="position_name" list="position-list"
                            value="{{ $recruitmentRequest ? ($recruitmentRequest->position_name ?: ($recruitmentRequest->jobPosition?->position_name ?? '')) : '' }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"
                            placeholder="พิมพ์หรือเลือกตำแหน่งงาน..." required>
                        <datalist id="position-list">
                            @foreach($employeePositions as $pName)
                                <option value="{{ $pName }}"></option>
                            @endforeach
                            @foreach($positions as $pos)
                                <option value="{{ $pos->position_name }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="job_position_id" value="{{ $recruitmentRequest?->job_position_id }}">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">จำนวนที่เปิดรับ
                            (Vacancy)</label>
                        <input type="number" name="vacancy"
                            value="{{ $recruitmentRequest ? $recruitmentRequest->headcount : 1 }}" min="1"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ประเภทการจ้างงาน</label>
                        <select name="employment_type"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            required>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">สถานที่ทำงาน
                            (Location)</label>
                        <input type="text" name="location" value="{{ $recruitmentRequest?->location }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            placeholder="เช่น สำนักงานใหญ่, คลังสินค้า">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ตารางงาน (Work
                            Schedule)</label>
                        <input type="text" name="work_schedule"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            placeholder="เช่น จันทร์-ศุกร์ 08:30 - 17:30">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เงินเดือนเริ่มต้น
                            (Min)</label>
                        <input type="number" name="salary_min" value="{{ $recruitmentRequest?->salary_min }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เงินเดือนสูงสุด (Max)</label>
                        <input type="number" name="salary_max" value="{{ $recruitmentRequest?->salary_max }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">หมายเหตุเงินเดือน</label>
                        <input type="text" name="salary_note"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            placeholder="เช่น ตามตกลง, ไม่รวมค่าคอมมิชชั่น">
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3
                    class="text-lg font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                    รายละเอียดและคุณสมบัติ</h3>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">รายละเอียดงาน (Job
                        Description)</label>
                    <textarea name="job_description" rows="6"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                        required>{{ $recruitmentRequest ? $recruitmentRequest->job_description : '' }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">คุณสมบัติผู้สมัคร
                        (Qualification)</label>
                    <textarea name="qualification" rows="6"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                        required>{{ $recruitmentRequest ? $recruitmentRequest->qualification : '' }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">สวัสดิการ (Benefits)</label>
                        <textarea name="benefits" rows="4"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">{{ $recruitmentRequest?->benefits }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เอกสารที่ต้องการ (Required
                            Documents)</label>
                        <div id="documents-container" class="space-y-2">
                            @php
                                $oldDocs = old('required_documents');
                                $docs = is_array($oldDocs) ? array_filter($oldDocs) : [''];
                                if (empty($docs))
                                    $docs = [''];
                            @endphp
                            @foreach($docs as $index => $doc)
                                <div class="flex gap-2 group">
                                    <div
                                        class="flex-none flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs font-bold text-gray-400">
                                        {{ $index + 1 }}
                                    </div>
                                    <input type="text" name="required_documents[]" value="{{ $doc }}"
                                        class="flex-1 bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm"
                                        placeholder="เช่น สำเนาบัตรประชาชน">
                                    <button type="button"
                                        class="remove-doc p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-doc"
                            class="mt-2 text-sm font-bold text-kumwell-red hover:text-red-700 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-plus-circle"></i>
                            เพิ่มแถว
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3
                    class="text-lg font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                    การตั้งค่าการประกาศ</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">วันที่เริ่มประกาศ</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">วันสิ้นสุดประกาศ</label>
                        <input type="date" name="end_date"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">สถานะการประกาศ</label>
                        <select name="publish_status"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            required>
                            <option value="draft">Draft (ฉบับร่าง)</option>
                            <option value="published">Published (ประกาศทันที)</option>
                            <option value="closed">Closed (ปิดรับสมัคร)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
                <button type="reset"
                    class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all font-semibold">
                    ยกเลิก
                </button>
                <button type="submit"
                    class="px-10 py-2.5 rounded-xl bg-kumwell-red hover:bg-red-700 text-white font-bold shadow-lg shadow-red-500/30 transition-all active:scale-95">
                    บันทึกประกาศ
                </button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('documents-container');
            const addButton = document.getElementById('add-doc');

            function updateNumbers() {
                container.querySelectorAll('.flex.gap-2').forEach((row, index) => {
                    row.querySelector('.flex-none').textContent = index + 1;
                });
            }

            addButton.addEventListener('click', function () {
                const rowCount = container.querySelectorAll('.flex.gap-2').length;
                const newRow = document.createElement('div');
                newRow.className = 'flex gap-2 group animate-in slide-in-from-top-2 duration-300';
                newRow.innerHTML = `
                                <div class="flex-none flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs font-bold text-gray-400">${rowCount + 1}</div>
                                <input type="text" name="required_documents[]" 
                                    class="flex-1 bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm"
                                    placeholder="เพิ่มทางเลือกเอกสาร...">
                                <button type="button" class="remove-doc p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            `;
                container.appendChild(newRow);
            });

            container.addEventListener('click', function (e) {
                if (e.target.closest('.remove-doc')) {
                    const rows = container.querySelectorAll('.flex.gap-2');
                    if (rows.length > 1) {
                        e.target.closest('.flex.gap-2').remove();
                        updateNumbers();
                    } else {
                        rows[0].querySelector('input').value = '';
                    }
                }
            });
        });
    </script>
@endsection