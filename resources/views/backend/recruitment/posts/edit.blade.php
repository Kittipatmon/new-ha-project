@extends('layouts.recruitment.app')

@section('title', 'แก้ไขประกาศรับสมัครงาน')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('backend.recruitment.posts.index') }}"
                class="text-gray-400 hover:text-kumwell-red transition-colors">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold dark:text-white text-gray-800">แก้ไขประกาศ: {{ $jobPost->title }}</h2>
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

        <form action="{{ route('backend.recruitment.posts.update', $jobPost->id) }}" method="POST"
            class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <h3
                    class="text-lg font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                    ข้อมูลประกาศ (Basic Info)</h3>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">หัวข้อประกาศ (Job Title)</label>
                    <input type="text" name="title" value="{{ old('title', $jobPost->title) }}"
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
                                <option value="{{ $dept->department_id }}" {{ old('department_id', $jobPost->department_id) == $dept->department_id ? 'selected' : '' }}>
                                    {{ $dept->department_fullname }} ({{ $dept->department_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ตำแหน่ง (Position)</label>
                        <input type="text" name="position_name" list="position-list"
                            value="{{ old('position_name', $jobPost->position_name) }}"
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
                        <input type="hidden" name="job_position_id"
                            value="{{ old('job_position_id', $jobPost->job_position_id) }}">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">จำนวนที่เปิดรับ
                            (Vacancy)</label>
                        <input type="number" name="vacancy" value="{{ old('vacancy', $jobPost->vacancy) }}" min="1"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ประเภทการจ้างงาน</label>
                        <select name="employment_type"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            required>
                            <option value="Full-time" {{ old('employment_type', $jobPost->employment_type) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Part-time" {{ old('employment_type', $jobPost->employment_type) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Contract" {{ old('employment_type', $jobPost->employment_type) == 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Internship" {{ old('employment_type', $jobPost->employment_type) == 'Internship' ? 'selected' : '' }}>Internship</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-fire text-kumwell-red text-xs"></i>
                            <span>แท็กความด่วน (Urgency Tag)</span>
                        </label>
                        <select name="urgency"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                            <option value="normal" {{ old('urgency', $jobPost->urgency ?? 'normal') == 'normal' ? 'selected' : '' }}>ปกติ (ไม่แสดงแท็กด่วน)</option>
                            <option value="urgent" {{ old('urgency', $jobPost->urgency ?? 'normal') == 'urgent' ? 'selected' : '' }}>🔥 รับสมัครด่วน (Urgent)</option>
                            <option value="very_urgent" {{ old('urgency', $jobPost->urgency ?? 'normal') == 'very_urgent' ? 'selected' : '' }}>⚡ รับสมัครด่วนมาก (Very Urgent)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-kumwell-red text-xs"></i>
                                <span>สถานที่ทำงาน (Location)</span>
                            </span>
                        </label>
                        
                        @php
                            $currentLocation = old('location', $jobPost->location ?? 'สำนักงานใหญ่');
                            $isPreset = in_array($currentLocation, ['สำนักงานใหญ่', 'Factory สาขาบางเลน', 'Factory สาขาไทรน้อย']);
                            $selectedKey = $isPreset ? $currentLocation : (!empty($currentLocation) ? 'custom' : 'สำนักงานใหญ่');
                        @endphp

                        <select id="location_select"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all">
                            <option value="สำนักงานใหญ่" {{ $selectedKey === 'สำนักงานใหญ่' ? 'selected' : '' }}>🏢 สำนักงานใหญ่ (Head Office)</option>
                            <option value="Factory สาขาบางเลน" {{ $selectedKey === 'Factory สาขาบางเลน' ? 'selected' : '' }}>🏭 Factory สาขาบางเลน</option>
                            <option value="Factory สาขาไทรน้อย" {{ $selectedKey === 'Factory สาขาไทรน้อย' ? 'selected' : '' }}>🏭 Factory สาขาไทรน้อย</option>
                            <option value="custom" {{ $selectedKey === 'custom' ? 'selected' : '' }}>✍️ กำหนดเอง (ระบุสถานที่อื่นๆ)</option>
                        </select>

                        <!-- Custom Input (shown only if 'custom' is selected) -->
                        <div id="custom_location_container" class="{{ $selectedKey === 'custom' ? '' : 'hidden' }} mt-2">
                            <input type="text" id="custom_location_input"
                                value="{{ !$isPreset ? $currentLocation : '' }}"
                                placeholder="ระบุสถานที่ทำงาน เช่น คลังสินค้า หรือ Work from home..."
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red">
                        </div>

                        <!-- Actual Input submitted with form -->
                        <input type="hidden" name="location" id="final_location_input" value="{{ $currentLocation ?: 'สำนักงานใหญ่' }}">

                        <!-- Location Preset Preview Card -->
                        <div id="location_preview_card" class="{{ $selectedKey === 'custom' ? 'hidden' : '' }} p-3 bg-red-50/50 dark:bg-slate-800/80 border border-red-100 dark:border-slate-700 rounded-xl text-xs space-y-1.5 transition-all">
                            <div class="flex items-center gap-1.5 font-bold text-gray-800 dark:text-gray-200">
                                <i class="fa-solid fa-building-circle-check text-kumwell-red"></i>
                                <span id="loc_preview_title">สำนักงานใหญ่</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-[11px]" id="loc_preview_address">
                                358 ถนนเลี่ยงเมืองนนทบุรี ตำบลบางกระสอ เมืองนนทบุรี จังหวัดนนทบุรี 11000
                            </p>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-gray-500 dark:text-gray-400 text-[11px] pt-1 border-t border-red-100/60 dark:border-slate-700/60" id="loc_preview_contacts">
                                <!-- Injected via JS -->
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ตารางงาน (Work
                            Schedule)</label>
                        <input type="text" name="work_schedule" value="{{ old('work_schedule', $jobPost->work_schedule) }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            placeholder="เช่น จันทร์-ศุกร์ 08:30 - 17:30">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เงินเดือนเริ่มต้น
                            (Min)</label>
                        <input type="number" name="salary_min" value="{{ old('salary_min', $jobPost->salary_min) }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เงินเดือนสูงสุด (Max)</label>
                        <input type="number" name="salary_max" value="{{ old('salary_max', $jobPost->salary_max) }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">หมายเหตุเงินเดือน</label>
                        <input type="text" name="salary_note" value="{{ old('salary_note', $jobPost->salary_note) }}"
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
                        required>{{ old('job_description', $jobPost->job_description) }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">คุณสมบัติผู้สมัคร
                        (Qualification)</label>
                    <textarea name="qualification" rows="6"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                        required>{{ old('qualification', $jobPost->qualification) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">สวัสดิการ (Benefits)</label>
                        <textarea name="benefits" rows="4"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">{{ old('benefits', $jobPost->benefits) }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เอกสารที่ต้องการ (Required
                            Documents)</label>
                        <div id="documents-container" class="space-y-2">
                            @php
                                $oldDocs = old('required_documents');
                                if (is_array($oldDocs)) {
                                    $docs = array_filter($oldDocs);
                                } else {
                                    $docs = array_filter(explode("\n", $jobPost->required_documents ?? ''));
                                }
                                if (empty($docs))
                                    $docs = [''];
                            @endphp
                            @foreach($docs as $index => $doc)
                                <div class="flex gap-2 group">
                                    <div
                                        class="flex-none flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs font-bold text-gray-400">
                                        {{ $index + 1 }}
                                    </div>
                                    <input type="text" name="required_documents[]" value="{{ trim($doc) }}"
                                        class="flex-1 bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm"
                                        placeholder="เช่น สำเนาบัตรประชาชน">
                                    <button type="button"
                                        class="remove-doc opacity-0 group-hover:opacity-100 p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all">
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
                        <input type="text" name="start_date" id="start_date"
                            value="{{ old('start_date', $jobPost->start_date ? $jobPost->start_date->format('Y-m-d') : '') }}"
                            placeholder="วว/ดด/ปปปป"
                            class="datepicker-th w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">วันสิ้นสุดประกาศ</label>
                        <input type="text" name="end_date" id="end_date"
                            value="{{ old('end_date', $jobPost->end_date ? $jobPost->end_date->format('Y-m-d') : '') }}"
                            placeholder="วว/ดด/ปปปป"
                            class="datepicker-th w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">สถานะการประกาศ</label>
                        <select name="publish_status"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm"
                            required>
                            <option value="draft" {{ old('publish_status', $jobPost->publish_status) == 'draft' ? 'selected' : '' }}>Draft (ฉบับร่าง)</option>
                            <option value="published" {{ old('publish_status', $jobPost->publish_status) == 'published' ? 'selected' : '' }}>Published (ประกาศทันที)</option>
                            <option value="closed" {{ old('publish_status', $jobPost->publish_status) == 'closed' ? 'selected' : '' }}>Closed (ปิดรับสมัคร)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('backend.recruitment.posts.index') }}"
                    class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all font-semibold">
                    ยกเลิก
                </a>
                <button type="submit"
                    class="px-10 py-2.5 rounded-xl bg-kumwell-red hover:bg-red-700 text-white font-bold shadow-lg shadow-red-500/30 transition-all active:scale-95">
                    อัปเดตประกาศ
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

            // Location Selector Logic
            const locationData = {
                'สำนักงานใหญ่': {
                    title: 'สำนักงานใหญ่ (Head Office)',
                    address: '358 ถนนเลี่ยงเมืองนนทบุรี ตำบลบางกระสอ เมืองนนทบุรี จังหวัดนนทบุรี 11000',
                    contacts: [
                        { icon: 'fa-phone', text: '(662) 954-3455' },
                        { icon: 'fa-fax', text: '(662) 591-7891' },
                        { icon: 'fa-envelope', text: 'info@kumwell.com' },
                        { icon: 'fa-globe', text: 'www.kumwell.com' }
                    ]
                },
                'Factory สาขาบางเลน': {
                    title: 'Factory สาขาบางเลน',
                    address: '26/2 หมู่ที่ 10 ตำบลบางเลน อำเภอบางใหญ่ จังหวัดนนทบุรี 11140',
                    contacts: [
                        { icon: 'fa-phone', text: '(662) 920-0133' },
                        { icon: 'fa-fax', text: '(662) 920-0045' }
                    ]
                },
                'Factory สาขาไทรน้อย': {
                    title: 'Factory สาขาไทรน้อย',
                    address: '27 หมู่ที่ 1 ตำบลไทรใหญ่ อำเภอไทรน้อย จังหวัดนนทบุรี 11150',
                    contacts: [
                        { icon: 'fa-phone', text: '(662) 117-2483-5' },
                        { icon: 'fa-fax', text: '(662) 117-2486' }
                    ]
                }
            };

            const locationSelect = document.getElementById('location_select');
            const customContainer = document.getElementById('custom_location_container');
            const customInput = document.getElementById('custom_location_input');
            const finalInput = document.getElementById('final_location_input');
            const previewCard = document.getElementById('location_preview_card');
            const previewTitle = document.getElementById('loc_preview_title');
            const previewAddress = document.getElementById('loc_preview_address');
            const previewContacts = document.getElementById('loc_preview_contacts');

            function updateLocationUI() {
                if (!locationSelect) return;
                const val = locationSelect.value;
                if (val === 'custom') {
                    customContainer.classList.remove('hidden');
                    previewCard.classList.add('hidden');
                    finalInput.value = customInput.value.trim();
                } else if (locationData[val]) {
                    customContainer.classList.add('hidden');
                    previewCard.classList.remove('hidden');
                    finalInput.value = val;

                    const data = locationData[val];
                    previewTitle.textContent = data.title;
                    previewAddress.textContent = data.address;
                    previewContacts.innerHTML = data.contacts.map(c => 
                        `<span><i class="fa-solid ${c.icon} mr-1 text-kumwell-red/70"></i>${c.text}</span>`
                    ).join('');
                }
            }

            if (locationSelect) {
                locationSelect.addEventListener('change', updateLocationUI);
                if (customInput) {
                    customInput.addEventListener('input', function() {
                        if (locationSelect.value === 'custom') {
                            finalInput.value = this.value.trim();
                        }
                    });
                }
                updateLocationUI();
            }
        });
    </script>

    <!-- Flatpickr setup for Thai localization -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    <style>
        .flatpickr-calendar {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
            border-radius: 1rem;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #e11d48 !important;
            border-color: #e11d48 !important;
        }
        .flatpickr-day.today {
            border-color: #e11d48 !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.th) {
                flatpickr.localize(flatpickr.l10ns.th);
            }

            flatpickr(".datepicker-th", {
                disableMobile: true,
                locale: (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.th) ? flatpickr.l10ns.th : "th",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                allowInput: true,
                parseDate: function(dateStr, formatStr) {
                    if (typeof dateStr === 'string' && dateStr.includes('/')) {
                        const parts = dateStr.split('/');
                        if (parts.length === 3) {
                            let day = parseInt(parts[0], 10);
                            let month = parseInt(parts[1], 10) - 1;
                            let year = parseInt(parts[2], 10);
                            if (year > 2400) {
                                year -= 543;
                            }
                            return new Date(year, month, day);
                        }
                    }
                    return flatpickr.parseDate(dateStr, formatStr);
                },
                formatDate: function(date, formatStr, locale) {
                    if (formatStr === 'd/m/Y') {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        let year = date.getFullYear();
                        if (year < 2400) {
                            year += 543;
                        }
                        return day + '/' + month + '/' + year;
                    }
                    return flatpickr.formatDate(date, formatStr, locale);
                },
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.altInput) {
                        instance.altInput.className = instance.input.className;
                        instance.altInput.classList.remove('datepicker-th');
                        instance.altInput.classList.add('flatpickr-input');
                    }
                    formatHeaderBuddhistYear(instance);
                },
                onMonthChange: function(selectedDates, dateStr, instance) {
                    formatHeaderBuddhistYear(instance);
                },
                onYearChange: function(selectedDates, dateStr, instance) {
                    formatHeaderBuddhistYear(instance);
                },
                onOpen: function(selectedDates, dateStr, instance) {
                    formatHeaderBuddhistYear(instance);
                }
            });

            function formatHeaderBuddhistYear(instance) {
                setTimeout(function() {
                    if (!instance || !instance.calendarContainer) return;
                    let cYear = instance.currentYear;
                    if (cYear > 2400) {
                        cYear -= 543;
                    }
                    const bYear = cYear + 543;
                    const curYearElem = instance.calendarContainer.querySelector('.flatpickr-current-month .cur-year');
                    if (curYearElem) {
                        curYearElem.value = bYear;
                    }
                    const numYearInputs = instance.calendarContainer.querySelectorAll('.cur-year');
                    numYearInputs.forEach(function(inp) {
                        inp.value = bYear;
                    });
                }, 10);
            }
        });
    </script>
@endsection