@extends('layouts.recruitment.app')

@section('title', 'สร้างประกาศรับสมัครงาน')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('backend.recruitment.posts.index') }}"
                class="text-gray-400 hover:text-kumwell-red transition-colors">
                <i class="fa-solid fa-arrow-left text-2xl"></i>
            </a>
            <h2 class="text-3xl font-extrabold dark:text-white text-gray-800">สร้างประกาศรับสมัครงานใหม่</h2>
        </div>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-2xl p-5">
                <div class="flex items-center gap-3 text-red-800 dark:text-red-300 font-bold mb-2 text-base">
                    <i class="fa-solid fa-circle-exclamation text-2xl"></i>
                    <span>เกิดข้อผิดพลาดในการบันทึกข้อมูล</span>
                </div>
                <ul class="list-disc list-inside text-base text-red-600 dark:text-red-400 space-y-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Recruitment Request Selector (อ้างอิงจากคำขอที่อนุมัติแล้ว) -->
        <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/30 text-kumwell-red flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <span>อ้างอิงจากคำขอเปิดรับสมัครพนักงาน</span>
                            <span class="text-sm px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold border border-emerald-200 dark:border-emerald-800/40">อนุมัติแล้ว</span>
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">เลือกคำขอรับสมัครงานที่ผ่านการอนุมัติ เพื่อดึงข้อมูลเข้าสู่แบบฟอร์มประกาศอัตโนมัติ</p>
                    </div>
                </div>
                @if(isset($approvedRequests) && $approvedRequests->count() > 0)
                    <span class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold self-start sm:self-center">
                        มีคำขออนุมัติ {{ $approvedRequests->count() }} รายการ
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                <div class="lg:col-span-8 space-y-2">
                    <label class="text-sm font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-kumwell-red text-base"></i>
                        <span>เลือกคำขอที่ต้องการอ้างอิง (Recruitment Request)</span>
                    </label>
                    <div class="relative">
                        <select id="request_selector" class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-kumwell-red transition-all cursor-pointer">
                            <option value="">-- ไม่ระบุ (กรอกข้อมูลประกาศเองแบบกำหนดเอง) --</option>
                            @if(isset($approvedRequests))
                                @foreach($approvedRequests as $appReq)
                                    @php
                                        $isThisSelected = (isset($recruitmentRequest) && $recruitmentRequest->id == $appReq->id);
                                        $hasJobPost = $appReq->jobPosts && $appReq->jobPosts->count() > 0;
                                    @endphp
                                    <option value="{{ $appReq->id }}" 
                                        {{ $isThisSelected ? 'selected' : '' }}
                                        data-req-id="{{ $appReq->id }}"
                                        data-req-no="{{ $appReq->request_no }}"
                                        data-position="{{ $appReq->position_name ?: ($appReq->jobPosition?->position_name ?? '') }}"
                                        data-job-position-id="{{ $appReq->job_position_id ?? '' }}"
                                        data-department-id="{{ $appReq->department_id }}"
                                        data-department-name="{{ $appReq->department?->department_name }}"
                                        data-headcount="{{ $appReq->headcount ?? 1 }}"
                                        data-salary-min="{{ $appReq->salary_min ?? '' }}"
                                        data-salary-max="{{ $appReq->salary_max ?? '' }}"
                                        data-job-desc="{{ e($appReq->job_description ?? '') }}"
                                        data-qualification="{{ e($appReq->qualification ?? '') }}"
                                        data-required-start-date="{{ $appReq->required_start_date ? $appReq->required_start_date->format('Y-m-d') : '' }}"
                                        data-has-jobpost="{{ $hasJobPost ? '1' : '0' }}">
                                        {{ $appReq->request_no }} - {{ $appReq->position_name }} ({{ $appReq->department?->department_name ?? 'ไม่ระบุแผนก' }}) [{{ $appReq->headcount }} อัตรา] {{ $hasJobPost ? '• [สร้างประกาศแล้ว]' : '⭐ [รอสร้างประกาศ]' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="lg:col-span-4 flex items-center gap-2 pt-1 lg:pt-6">
                    <button type="button" id="btn_clear_request" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>ล้างการเลือก</span>
                    </button>
                    <div id="request_status_badge" class="flex-1 text-sm">
                        @if($recruitmentRequest)
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold">
                                <i class="fa-solid fa-circle-check"></i> อ้างอิง #{{ $recruitmentRequest->request_no }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 font-medium">
                                ยังไม่ได้เลือกคำขอ
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Info Preview Box -->
            <div id="selected_request_preview" class="{{ $recruitmentRequest ? '' : 'hidden' }} p-4 bg-red-50/50 dark:bg-slate-800/60 border border-red-100 dark:border-slate-700 rounded-xl text-sm space-y-2 transition-all">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="font-bold text-kumwell-red dark:text-red-400 flex items-center gap-2 text-base">
                        <i class="fa-solid fa-circle-info text-lg"></i>
                        <span id="preview_req_title">อ้างอิงคำขอ: {{ $recruitmentRequest ? $recruitmentRequest->request_no . ' (' . $recruitmentRequest->position_name . ')' : '' }}</span>
                    </div>
                    <div id="preview_post_warn" class="{{ ($recruitmentRequest && $recruitmentRequest->jobPosts && $recruitmentRequest->jobPosts->count() > 0) ? '' : 'hidden' }} text-amber-700 dark:text-amber-300 font-bold flex items-center gap-1.5 text-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>คำขอนี้เคยมีประกาศรับสมัครแล้ว</span>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-xs">
                    ระบบได้กรอกข้อมูลตำแหน่ง, แผนก, จำนวนรับ, อัตราเงินเดือน, รายละเอียดงาน และคุณสมบัติจากคำขอนี้ให้โดยอัตโนมัติแล้ว คุณสามารถปรับแต่งรายละเอียดเพิ่มเติมได้ตามต้องการ
                </p>
            </div>
        </div>

        <form action="{{ route('backend.recruitment.posts.store') }}" method="POST"
            class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 space-y-8">
            @csrf
            <input type="hidden" name="recruitment_request_id" id="form_recruitment_request_id"
                value="{{ $recruitmentRequest ? $recruitmentRequest->id : '' }}">

            <div class="space-y-6">
                <h3
                    class="text-xl font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-kumwell-red"></i>
                    <span>ข้อมูลประกาศ (Basic Info)</span>
                </h3>

                <div class="space-y-2">
                    <label class="text-base font-bold text-gray-700 dark:text-gray-300">หัวข้อประกาศ (Job Title)</label>
                    <input type="text" name="title"
                        value="{{ $recruitmentRequest ? ($recruitmentRequest->position_name ?: ($recruitmentRequest->jobPosition?->position_name ?? '')) : '' }}"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium focus:ring-2 focus:ring-kumwell-red transition-all"
                        required placeholder="ชื่อตำแหน่งงานที่ต้องการประกาศ">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">แผนก (Department)</label>
                        <select name="department_id"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium"
                            required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->department_id }}" {{ (isset($recruitmentRequest) && $recruitmentRequest->department_id == $dept->department_id) ? 'selected' : '' }}>
                                    {{ $dept->department_fullname }} ({{ $dept->department_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">ตำแหน่ง (Position)</label>
                        <input type="text" name="position_name" list="position-list"
                            value="{{ $recruitmentRequest ? ($recruitmentRequest->position_name ?: ($recruitmentRequest->jobPosition?->position_name ?? '')) : '' }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium focus:ring-2 focus:ring-kumwell-red transition-all"
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
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">จำนวนที่เปิดรับ
                            (Vacancy)</label>
                        <input type="number" name="vacancy"
                            value="{{ $recruitmentRequest ? $recruitmentRequest->headcount : 1 }}" min="1"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium"
                            required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">ประเภทการจ้างงาน</label>
                        <select name="employment_type"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium"
                            required>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-fire text-kumwell-red text-sm"></i>
                            <span>แท็กความด่วน (Urgency Tag)</span>
                        </label>
                        <select name="urgency"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium">
                            <option value="normal">ปกติ (ไม่แสดงแท็กด่วน)</option>
                            <option value="urgent" selected>🔥 รับสมัครด่วน (Urgent)</option>
                            <option value="very_urgent">⚡ รับสมัครด่วนมาก (Very Urgent)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-kumwell-red text-sm"></i>
                                <span>สถานที่ทำงาน (Location)</span>
                            </span>
                        </label>
                        
                        @php
                            $currentLocation = old('location', $recruitmentRequest?->location ?? 'สำนักงานใหญ่');
                            $isPreset = in_array($currentLocation, ['สำนักงานใหญ่', 'Factory สาขาบางเลน', 'Factory สาขาไทรน้อย']);
                            $selectedKey = $isPreset ? $currentLocation : (!empty($currentLocation) ? 'custom' : 'สำนักงานใหญ่');
                        @endphp

                        <select id="location_select"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium focus:ring-2 focus:ring-kumwell-red transition-all">
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
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium focus:ring-2 focus:ring-kumwell-red">
                        </div>

                        <!-- Actual Input submitted with form -->
                        <input type="hidden" name="location" id="final_location_input" value="{{ $currentLocation ?: 'สำนักงานใหญ่' }}">

                        <!-- Location Preset Preview Card -->
                        <div id="location_preview_card" class="{{ $selectedKey === 'custom' ? 'hidden' : '' }} p-3.5 bg-red-50/50 dark:bg-slate-800/80 border border-red-100 dark:border-slate-700 rounded-xl text-sm space-y-1.5 transition-all">
                            <div class="flex items-center gap-1.5 font-bold text-gray-800 dark:text-gray-200">
                                <i class="fa-solid fa-building-circle-check text-kumwell-red"></i>
                                <span id="loc_preview_title">สำนักงานใหญ่</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-xs" id="loc_preview_address">
                                358 ถนนเลี่ยงเมืองนนทบุรี ตำบลบางกระสอ เมืองนนทบุรี จังหวัดนนทบุรี 11000
                            </p>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-gray-500 dark:text-gray-400 text-xs pt-1 border-t border-red-100/60 dark:border-slate-700/60" id="loc_preview_contacts">
                                <!-- Injected via JS -->
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">ตารางงาน (Work
                            Schedule)</label>
                        <input type="text" name="work_schedule"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium"
                            placeholder="เช่น จันทร์-ศุกร์ 08:30 - 17:30">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">เงินเดือนเริ่มต้น
                            (Min)</label>
                        <input type="number" name="salary_min" value="{{ $recruitmentRequest?->salary_min }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">เงินเดือนสูงสุด (Max)</label>
                        <input type="number" name="salary_max" value="{{ $recruitmentRequest?->salary_max }}"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">หมายเหตุเงินเดือน</label>
                        <input type="text" name="salary_note"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium"
                            placeholder="เช่น ตามตกลง, ไม่รวมค่าคอมมิชชั่น">
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3
                    class="text-xl font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-text text-kumwell-red"></i>
                    <span>รายละเอียดและคุณสมบัติ</span>
                </h3>

                <div class="space-y-2">
                    <label class="text-base font-bold text-gray-700 dark:text-gray-300">รายละเอียดงาน (Job
                        Description)</label>
                    <textarea name="job_description" rows="6"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium leading-relaxed"
                        required>{{ $recruitmentRequest ? $recruitmentRequest->job_description : '' }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-base font-bold text-gray-700 dark:text-gray-300">คุณสมบัติผู้สมัคร
                        (Qualification)</label>
                    <textarea name="qualification" rows="6"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium leading-relaxed"
                        required>{{ $recruitmentRequest ? $recruitmentRequest->qualification : '' }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">สวัสดิการ (Benefits)</label>
                        <textarea name="benefits" rows="4"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium leading-relaxed">{{ $recruitmentRequest?->benefits }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">เอกสารที่ต้องการ (Required
                            Documents)</label>
                        <div id="documents-container" class="space-y-2.5">
                            @php
                                $oldDocs = old('required_documents');
                                $docs = is_array($oldDocs) ? array_filter($oldDocs) : [''];
                                if (empty($docs))
                                    $docs = [''];
                            @endphp
                            @foreach($docs as $index => $doc)
                                <div class="flex gap-2 group">
                                    <div
                                        class="flex-none flex items-center justify-center w-11 h-11 bg-gray-100 dark:bg-gray-800 rounded-xl text-sm font-bold text-gray-500">
                                        {{ $index + 1 }}
                                    </div>
                                    <input type="text" name="required_documents[]" value="{{ $doc }}"
                                        class="flex-1 bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-base font-medium"
                                        placeholder="เช่น สำเนาบัตรประชาชน">
                                    <button type="button"
                                        class="remove-doc px-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                        <i class="fa-solid fa-trash-can text-base"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-doc"
                            class="mt-2.5 text-base font-bold text-kumwell-red hover:text-red-700 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-plus-circle text-lg"></i>
                            เพิ่มแถว
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3
                    class="text-xl font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-kumwell-red"></i>
                    <span>การตั้งค่าการประกาศ</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">วันที่เริ่มประกาศ</label>
                        <input type="text" name="start_date" id="start_date" value="{{ date('Y-m-d') }}"
                            placeholder="วว/ดด/ปปปป"
                            class="datepicker-th w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">วันสิ้นสุดประกาศ</label>
                        <input type="text" name="end_date" id="end_date"
                            placeholder="วว/ดด/ปปปป"
                            class="datepicker-th w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-base font-bold text-gray-700 dark:text-gray-300">สถานะการประกาศ</label>
                        <select name="publish_status"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-base font-medium"
                            required>
                            <option value="draft">Draft (ฉบับร่าง)</option>
                            <option value="published">Published (ประกาศทันที)</option>
                            <option value="closed">Closed (ปิดรับสมัคร)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex items-center justify-end gap-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('backend.recruitment.posts.index') }}"
                    class="px-8 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-base font-bold inline-flex items-center justify-center">
                    ยกเลิก
                </a>
                <button type="submit"
                    class="px-12 py-3.5 rounded-xl bg-kumwell-red hover:bg-red-700 text-white text-base font-bold shadow-lg shadow-red-500/30 transition-all active:scale-95">
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

            // Recruitment Request Auto-Fill Logic
            const requestSelector = document.getElementById('request_selector');
            const clearRequestBtn = document.getElementById('btn_clear_request');
            const statusBadge = document.getElementById('request_status_badge');
            const previewBox = document.getElementById('selected_request_preview');
            const previewTitleText = document.getElementById('preview_req_title');
            const previewPostWarn = document.getElementById('preview_post_warn');
            const formReqIdInput = document.getElementById('form_recruitment_request_id');

            // Form inputs to fill
            const inputTitle = document.querySelector('input[name="title"]');
            const selectDept = document.querySelector('select[name="department_id"]');
            const inputPosName = document.querySelector('input[name="position_name"]');
            const inputJobPosId = document.querySelector('input[name="job_position_id"]');
            const inputVacancy = document.querySelector('input[name="vacancy"]');
            const inputSalMin = document.querySelector('input[name="salary_min"]');
            const inputSalMax = document.querySelector('input[name="salary_max"]');
            const textareaJobDesc = document.querySelector('textarea[name="job_description"]');
            const textareaQual = document.querySelector('textarea[name="qualification"]');

            function handleRequestChange() {
                if (!requestSelector) return;
                const selectedOpt = requestSelector.options[requestSelector.selectedIndex];
                const reqId = selectedOpt ? selectedOpt.value : '';

                if (!reqId) {
                    // Reset or clear request linkage
                    if (formReqIdInput) formReqIdInput.value = '';
                    if (statusBadge) {
                        statusBadge.innerHTML = `<span class="text-gray-400 dark:text-gray-500">ยังไม่ได้เลือกคำขอ (สร้างแบบอิสระ)</span>`;
                    }
                    if (previewBox) previewBox.classList.add('hidden');
                    return;
                }

                // Get dataset
                const ds = selectedOpt.dataset;
                const reqNo = ds.reqNo || '';
                const position = ds.position || '';
                const deptId = ds.departmentId || '';
                const jobPosId = ds.jobPositionId || '';
                const headcount = ds.headcount || 1;
                const salMin = ds.salaryMin || '';
                const salMax = ds.salaryMax || '';
                const jobDesc = ds.jobDesc || '';
                const qual = ds.qualification || '';
                const hasJobPost = ds.hasJobpost === '1';

                // Update hidden ID
                if (formReqIdInput) formReqIdInput.value = reqId;

                // Pre-populate fields
                if (inputTitle) inputTitle.value = position;
                if (inputPosName) inputPosName.value = position;
                if (inputJobPosId) inputJobPosId.value = jobPosId;
                if (selectDept && deptId) {
                    selectDept.value = deptId;
                }
                if (inputVacancy) inputVacancy.value = headcount;
                if (inputSalMin) inputSalMin.value = salMin;
                if (inputSalMax) inputSalMax.value = salMax;
                if (textareaJobDesc) textareaJobDesc.value = jobDesc;
                if (textareaQual) textareaQual.value = qual;

                // Update Status Badge & Preview Card
                if (statusBadge) {
                    statusBadge.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800/50">
                            <i class="fa-solid fa-circle-check"></i> อ้างอิง #${reqNo}
                        </span>
                    `;
                }

                if (previewBox) {
                    previewBox.classList.remove('hidden');
                    if (previewTitleText) {
                        previewTitleText.textContent = `อ้างอิงคำขอ: ${reqNo} (${position})`;
                    }
                    if (previewPostWarn) {
                        if (hasJobPost) {
                            previewPostWarn.classList.remove('hidden');
                        } else {
                            previewPostWarn.classList.add('hidden');
                        }
                    }
                }

                // Add visual pulse effect on form
                const basicInfo = document.querySelector('form');
                if (basicInfo) {
                    basicInfo.classList.add('ring-2', 'ring-red-400/40', 'transition-all', 'duration-300');
                    setTimeout(() => {
                        basicInfo.classList.remove('ring-2', 'ring-red-400/40');
                    }, 800);
                }
            }

            if (requestSelector) {
                requestSelector.addEventListener('change', handleRequestChange);
            }

            if (clearRequestBtn) {
                clearRequestBtn.addEventListener('click', function() {
                    if (requestSelector) {
                        requestSelector.value = '';
                        handleRequestChange();
                    }
                });
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