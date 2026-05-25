@extends('layouts.hrrequest.app')
@section('content')
<div class="max-w-8xl mx-auto px-4 py-6 font-prompt">
    <div class="breadcrumbs text-sm">
        <ul>
            <li><a href="{{ route('welcome') }}">Home</a></li>
            <li><a href="{{ route('request.hr') }}">Request HR</a></li>
            <li><a href="{{ route('requesthr.list') }}">รายการรอดำเนินการ</a></li>
            <li>
                <a class="font-semibold text-gray-800 dark:text-red-500">
                    แก้ไขคำร้องขอ HR เลขที่ {{ $hrrequest->request_code }}
                </a>
            </li>
        </ul>
    </div>
    <div class="border border-gray-300/60 dark:border-gray-200/40 p-4 rounded-lg shadow-xl">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-warning"></i> แก้ไขคำร้องขอ HR
                </h1>
                <p class="text-sm text-gray-500">แก้ไขข้อมูลเอกสารฝ่ายทรัพยากรบุคคล</p>
            </div>
            <div class="text-xs mt-2 md:mt-0 bg-warning text-white px-3 py-1 rounded-full font-medium shadow-sm">
                วันที่ร้องขอ: {{ \Carbon\Carbon::parse($hrrequest->submitted_at)->format('d/m/Y') }}
            </div>
        </div>

        <div class="card bg-base-100 ">
            <div class="card-body p-2 md:p-4">
                
                <form id="hrRequestForm" enctype="multipart/form-data">
                    <div class="text-sm text-gray-500 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-info"></i> กรุณากรอกข้อมูลให้ครบถ้วน (<span class="text-error">*</span> จำเป็นต้องระบุ)
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="form-control w-full">
                            <label class="label font-medium">
                                <span class="label-text">หมวดคำร้อง (Request Category) <span class="text-error">*</span></span>
                            </label>
                            <select id="categorySelect" name="category_id" class="select select-bordered w-full transition-colors">
                                <option disabled value="">เลือกหมวดคำร้อง</option>
                                @foreach ($Requestcategories as $category)
                                    <option value="{{ $category->id }}" {{ $hrrequest->category_id == $category->id ? 'selected' : '' }}>{{ $category->name_th }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control w-full">
                            <label class="label font-medium">
                                <span class="label-text">ประเภทคำร้อง (Request Type) <span class="text-error">*</span></span>
                            </label>
                            <select id="typeSelect" name="type_id" class="select select-bordered w-full transition-colors">
                                <option disabled value="">เลือกหมวดคำร้องก่อน</option>
                                @foreach ($Requesttypes as $type)
                                    @if($type->category_id == $hrrequest->category_id)
                                        <option value="{{ $type->id }}" {{ $hrrequest->type_id == $type->id ? 'selected' : '' }}>{{ $type->name_th }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control w-full">
                            <label class="label font-medium">
                                <span class="label-text">ตัวเลือกรายละเอียด (Request Subtype) <span class="text-error">*</span></span>
                            </label>
                            <select id="subtypeSelect" name="subtype_id" class="select select-bordered w-full transition-colors">
                                <option disabled value="" {{ is_null($hrrequest->subtype_id) ? 'selected' : '' }}>เลือกประเภทคำร้องก่อน</option>
                                @foreach ($Requestsubtypes as $subtype)
                                    @if($subtype->type_id == $hrrequest->type_id)
                                        <option value="{{ $subtype->id }}" {{ $hrrequest->subtype_id == $subtype->id ? 'selected' : '' }}>{{ $subtype->name_th }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- <div class="divider"></div> -->

                    <div id="dynamicContent">
                        <!-- การแจ้งแก้ไขเวลา -->
                        <div id="sectionTimeEdit" class="hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="label font-medium">เหตุผลที่ขอดำเนินการ</label>
                                    <textarea name="edit_reason" class="textarea textarea-bordered w-full h-24" placeholder="ระบุสาเหตุ...">{{ $hrrequest->timeEdit->edit_reason ?? '' }}</textarea>
                                </div>
                                
                                <div class="form-control">
                                    <label class="label">วันที่เริ่มต้น</label>
                                    <input type="date" name="edit_start_date" class="input input-bordered" value="{{ $hrrequest->timeEdit->edit_start_date ?? '' }}" />
                                </div>
                                <div class="form-control">
                                    <label class="label">เวลาเริ่มต้น</label>
                                    <input type="time" name="edit_start_time" id="edit_start_time" class="input input-bordered" value="{{ $hrrequest->timeEdit->edit_start_time ?? '' }}" />
                                </div>
                                
                                <div class="form-control">
                                    <label class="label">วันที่สิ้นสุด</label>
                                    <input type="date" name="edit_end_date" class="input input-bordered" value="{{ $hrrequest->timeEdit->edit_end_date ?? '' }}" />
                                </div>
                                <div class="form-control">
                                    <label class="label">เวลาสิ้นสุด</label>
                                    <input type="time" name="edit_end_time" id="edit_end_time" class="input input-bordered" value="{{ $hrrequest->timeEdit->edit_end_time ?? '' }}" />
                                </div>
                            </div>

                            <div class="form-control mt-4">
                                <label class="label font-medium">แนบไฟล์หลักฐาน</label>
                                @if(isset($hrrequest->timeEdit->timefile))
                                    <div class="mb-2">
                                        <a href="{{ asset($hrrequest->timeEdit->timefile) }}" target="_blank" class="link link-primary text-sm">
                                            <i class="fa-solid fa-paperclip"></i> ไฟล์แนบเดิม
                                        </a>
                                    </div>
                                @endif
                                <div class="flex items-center gap-4">
                                    <input type="file" id="fileInput" name="timefile" class="file-input file-input-bordered file-input-primary w-full max-w-md" accept="image/*,application/pdf" />
                                </div>
                                <div id="filePreviewContainer" class="mt-3 hidden p-4 border rounded-lg ">
                                    <div class="flex items-center gap-3">
                                        <div id="fileIcon" class="text-2xl"></div>
                                        <div class="flex-1 overflow-hidden">
                                            <p id="fileName" class="text-sm font-semibold truncate"></p>
                                            <button type="button" id="btnPreviewFile" class="btn btn-xs btn-outline btn-info mt-1">ดูตัวอย่าง</button>
                                        </div>
                                        <button type="button" id="btnClearFile" class="btn btn-square btn-ghost btn-sm text-error">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ร้องขอชุดยูนิฟอร์ม -->
                        <div id="sectionUniform" class="hidden space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">เพศ</label>
                                    <select name="uniform_gender" class="select select-bordered w-full">
                                        <option disabled value="" {{ !isset($hrrequest->uniform->uniform_gender) ? 'selected' : '' }}>เลือกเพศ</option>
                                        <option value="ชาย" {{ ($hrrequest->uniform->uniform_gender ?? '') == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                        <option value="หญิง" {{ ($hrrequest->uniform->uniform_gender ?? '') == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">ขนาดชุด</label>
                                    <select name="uniform_size" class="select select-bordered w-full">
                                        <option disabled value="" {{ !isset($hrrequest->uniform->uniform_size) ? 'selected' : '' }}>เลือกขนาด</option>
                                        @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                            <option value="{{ $size }}" {{ ($hrrequest->uniform->uniform_size ?? '') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="label">เหตุผลที่ขอชุด</label>
                                    <textarea name="uniform_reason" class="textarea textarea-bordered w-full" placeholder="ระบุเหตุผล...">{{ $hrrequest->uniform->uniform_reason ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                    
                        <!-- ร้องขอเอกสาร Safety Equipment -->
                        <div id="sectionSafetyEquip" class="hidden space-y-4">
                            <label class="label font-medium">รายการอุปกรณ์ที่ร้องขอ</label>
                            
                            <div id="safetyListContainer" class="space-y-3">
                                @if($hrrequest->safetyItems && $hrrequest->safetyItems->count() > 0)
                                    @foreach($hrrequest->safetyItems as $item)
                                        <div class="flex gap-2 items-center animate-fade-in-down">
                                            <div class="relative w-full">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <i class="fa-solid fa-helmet-safety text-gray-400"></i>
                                                </div>
                                                <input type="text" name="safety_item_name[]" value="{{ $item->item_name }}" class="input input-bordered pl-10 w-full" placeholder="ชื่ออุปกรณ์" />
                                            </div>
                                            <input type="number" name="safety_item_quantity[]" min="1" value="{{ $item->quantity }}" class="input input-bordered w-24 text-center" />
                                            <button type="button" class="btn btn-square btn-outline btn-error btn-sm btn-remove-safety">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <button type="button" id="btnAddSafety" class="btn btn-outline btn-info btn-sm">
                                <i class="fa-solid fa-plus"></i> เพิ่มรายการ
                            </button>
                        </div>

                        <!-- เหตุผลที่ขอใบรับรอง Certificate -->
                        <div id="sectionCertificateReason" class="hidden space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label font-medium">เหตุผลที่ขอเอกสาร Certificate</label>
                                <textarea name="certificate_reason" class="textarea textarea-bordered w-full h-24" placeholder="ระบุเหตุผล...">{{ $hrrequest->certificate->certificate_reason ?? '' }}</textarea>
                            </div>
                        </div>    

                        <!-- เหตุผลที่ขอใบรับรอ Welfare Request -->
                        <div id="sectionWelfareReason" class="hidden space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label font-medium">เหตุผลที่ขอเอกสาร Welfare Request</label>
                                <textarea name="welfare_reason" class="textarea textarea-bordered w-full h-24" placeholder="ระบุเหตุผล...">{{ $hrrequest->welfare->welfare_reason ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- เหตุผลที่ขอใบรับรอง Safety Document -->
                        <div id="sectionSafetyReason" class="hidden space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label font-medium">เหตุผลที่ขอเอกสาร Safety</label>
                                <textarea name="safety_reason" class="textarea textarea-bordered w-full h-24" placeholder="ระบุเหตุผล...">{{ $hrrequest->safetyDoc->safety_reason ?? '' }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="card-actions justify-end mt-8 pt-4 border-t border-gray-100">
                        <a href="{{ route('requesthr.list') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-xmark"></i> ยกเลิก
                        </a>
                        <button type="button" class="btn btn-success px-8 text-white shadow-lg shadow-primary/30" onclick="submitForm()">
                            <i class="fa-solid fa-save"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<dialog id="preview_modal" class="modal">
    <div class="modal-box p-0 overflow-hidden relative max-w-3xl">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-10 bg-white/50 hover:bg-white" onclick="preview_modal.close()">✕</button>
        <div class="bg-gray-100 flex justify-center items-center min-h-[300px]">
             <img id="modalPreviewImage" src="" alt="Preview" class="max-w-full max-h-[80vh] object-contain" loading="lazy" />
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

@endsection

@push('scripts')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#edit_start_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
        flatpickr("#edit_end_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ==========================================
        // 1. DATA & ELEMENTS
        // ==========================================
        const allTypes = @json($Requesttypes);
        const allSubtypes = @json($Requestsubtypes);

        const categorySelect = document.getElementById('categorySelect');
        const typeSelect = document.getElementById('typeSelect');
        const subtypeSelect = document.getElementById('subtypeSelect');

        // รวม Section ทั้งหมดไว้ใน Object เพื่อเรียกใช้ง่ายๆ
        const sections = {
            timeEdit: document.getElementById('sectionTimeEdit'),       // ส่วนแก้ไขเวลา
            uniform: document.getElementById('sectionUniform'),         // ส่วนชุดยูนิฟอร์ม
            safetyEquip: document.getElementById('sectionSafetyEquip'), // ส่วนอุปกรณ์ Safety
            safetyReason: document.getElementById('sectionSafetyReason'), // ส่วนเหตุผล Safety
            certificateReason: document.getElementById('sectionCertificateReason'),
            welfareReason: document.getElementById('sectionWelfareReason')
        };

        // ==========================================
        // 2. HELPER FUNCTIONS
        // ==========================================
        
        // ฟังก์ชันซ่อนทุก Section (Reset UI)
        function hideAllSections() {
            Object.values(sections).forEach(el => {
                if(el) el.classList.add('hidden');
            });
        }

        // ฟังก์ชันรีเซ็ต Dropdown
        function resetSelect(selectEl, placeholder) {
            selectEl.innerHTML = `<option disabled selected value="">${placeholder}</option>`;
            // selectEl.disabled = true; // Don't disable in edit mode if populated
        }

        // ==========================================
        // 3. MAIN UI LOGIC (สำคัญ! แก้ ID ตรงนี้)
        // ==========================================
        function updateFormUI() {
            // 3.1 ซ่อนทุกอย่างก่อนเสมอ เพื่อไม่ให้แสดงซ้อนกัน
            hideAllSections();

            const catId = categorySelect.value;
            const typeId = typeSelect.value;
            const subId = subtypeSelect.value;
            const selectedTypeOption = typeSelect.options[typeSelect.selectedIndex];
            const selectedTypeText = selectedTypeOption ? selectedTypeOption.text : "";

            // 3.2 เช็คเงื่อนไขเพื่อแสดงผล (*** แก้เลข ID และข้อความให้ตรงกับ DB ของคุณ ***)
            
            // --- แสดงผลตามหมวดหมู่ (Category) ---

            // ถ้าเลือกหมวด "Safety/ความปลอดภัย" (ID=3) ให้แสดงฟิลด์เหตุผลเสมอ
            if (catId == '3') { 
                if(sections.safetyReason) sections.safetyReason.classList.remove('hidden');
            }

            // ถ้าเลือกหมวด "แก้ไขเวลา/Time Attendance" (ID=1) ให้แสดงฟอร์มแก้ไขเวลา
            if (catId == '1') { 
                if(sections.timeEdit) sections.timeEdit.classList.remove('hidden');
            }

            // --- แสดงผลตามประเภท (Type) ---

            // ถ้าเลือกประเภท "ใบร้องขอชุดยูนิฟอร์ม"
            if (selectedTypeText === 'ใบร้องขอชุดยูนิฟอร์ม') {
                if(sections.uniform) sections.uniform.classList.remove('hidden');
            }
            
            // ถ้าเลือกประเภท "ใบร้องขออุปกรณ์ Safety"
            if (selectedTypeText === 'ใบร้องขออุปกรณ์ Safety') {
                if(sections.safetyEquip) sections.safetyEquip.classList.remove('hidden');
            }

            // ถ้าเลือกประเภท "ใบรับรอง"
            if (selectedTypeText === 'ใบรับรอง') {
                if(sections.certificateReason) sections.certificateReason.classList.remove('hidden');
            }

            // ถ้าเลือกประเภท "ใบร้องขอสวัสดิการ"
            if (selectedTypeText === 'ใบร้องขอสวัสดิการ') {
                if(sections.welfareReason) sections.welfareReason.classList.remove('hidden');
            }
        }

        // ==========================================
        // 4. EVENT LISTENERS (DROPDOWNS)
        // ==========================================

        // --- เมื่อเลือก Category ---
        categorySelect.addEventListener('change', function() {
            const selectedCategoryId = this.value;

            // รีเซ็ตลูกหลาน
            resetSelect(typeSelect, 'กรุณาเลือกประเภทคำร้อง');
            resetSelect(subtypeSelect, 'เลือกประเภทคำร้องก่อน');

            // กรอง Type
            const filteredTypes = allTypes.filter(item => item.category_id == selectedCategoryId);

            if (filteredTypes.length > 0) {
                typeSelect.disabled = false;
                filteredTypes.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.name_th || t.name_en || t.code;
                    typeSelect.appendChild(opt);
                });
            } else {
                resetSelect(typeSelect, 'ไม่พบประเภทคำร้อง');
            }

            // อัปเดต UI ทันที (เผื่อบางหมวดโชว์เลยโดยไม่ต้องเลือก Type)
            updateFormUI();
        });

        // --- เมื่อเลือก Type ---
        typeSelect.addEventListener('change', function() {
            const selectedTypeId = this.value;

            // รีเซ็ตหลาน
            resetSelect(subtypeSelect, 'กรุณาเลือกตัวเลือกรายละเอียด');

            // กรอง Subtype
            const filteredSubtypes = allSubtypes.filter(item => item.type_id == selectedTypeId);

            if (filteredSubtypes.length > 0) {
                subtypeSelect.disabled = false;
                filteredSubtypes.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name_th || s.name_en || s.code;
                    subtypeSelect.appendChild(opt);
                });
            } else {
                resetSelect(subtypeSelect, 'ไม่พบตัวเลือกเพิ่มเติม');
            }

            // อัปเดต UI (เผื่อบางฟอร์มขึ้นอยู่กับ Type)
            updateFormUI();
        });

        // --- เมื่อเลือก Subtype ---
        subtypeSelect.addEventListener('change', function() {
            // อัปเดต UI (เผื่อบางฟอร์มขึ้นอยู่กับ Subtype)
            updateFormUI();
        });


        // ==========================================
        // 5. SAFETY & FILE LOGIC
        // ==========================================
        
        // --- Safety List Logic ---
        const safetyContainer = document.getElementById('safetyListContainer');
        const btnAddSafety = document.getElementById('btnAddSafety');
        function addSafetyItem() {
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center animate-fade-in-down';
            div.innerHTML = `
                <div class="relative w-full">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fa-solid fa-helmet-safety text-gray-400"></i>
                    </div>
                    <input type="text" name="safety_item_name[]" class="input input-bordered pl-10 w-full" placeholder="ชื่ออุปกรณ์" />
                </div>
                <input type="number" name="safety_item_quantity[]" min="1" value="1" class="input input-bordered w-24 text-center" />
                <button type="button" class="btn btn-square btn-outline btn-error btn-sm btn-remove-safety">
                    <i class="fa-solid fa-minus"></i>
                </button>
            `;
            div.querySelector('.btn-remove-safety').addEventListener('click', function() {
                if (safetyContainer.children.length > 1) div.remove();
                else Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'ต้องมีรายการอย่างน้อย 1 รายการ', timer: 1500, showConfirmButton: false });
            });
            safetyContainer.appendChild(div);
        }
        if(safetyContainer) {
            // If empty (no existing items), add one
            if(safetyContainer.children.length === 0) {
                addSafetyItem(); 
            } else {
                // Attach events to existing items
                document.querySelectorAll('.btn-remove-safety').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (safetyContainer.children.length > 1) this.closest('.flex').remove();
                        else Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'ต้องมีรายการอย่างน้อย 1 รายการ', timer: 1500, showConfirmButton: false });
                    });
                });
            }
            btnAddSafety.addEventListener('click', addSafetyItem);
        }

        // --- File Preview Logic ---
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('filePreviewContainer');
        const fileIcon = document.getElementById('fileIcon');
        const fileName = document.getElementById('fileName');
        const btnPreviewFile = document.getElementById('btnPreviewFile');
        const btnClearFile = document.getElementById('btnClearFile');

        if(fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                fileName.textContent = file.name;
                previewContainer.classList.remove('hidden');
                if (file.type === 'application/pdf') {
                    fileIcon.innerHTML = '<i class="fa-solid fa-file-pdf text-red-500"></i>';
                    btnPreviewFile.onclick = () => window.open(URL.createObjectURL(file), '_blank');
                    btnPreviewFile.style.display = 'inline-block';
                } else if (file.type.startsWith('image/')) {
                    fileIcon.innerHTML = '<i class="fa-solid fa-file-image text-blue-500"></i>';
                    btnPreviewFile.onclick = () => {
                        document.getElementById('modalPreviewImage').src = URL.createObjectURL(file);
                        document.getElementById('preview_modal').showModal();
                    };
                    btnPreviewFile.style.display = 'inline-block';
                } else {
                    fileIcon.innerHTML = '<i class="fa-solid fa-file text-gray-500"></i>';
                    btnPreviewFile.style.display = 'none';
                }
            });
            btnClearFile.addEventListener('click', function() {
                fileInput.value = '';
                previewContainer.classList.add('hidden');
            });
        }

        // Initialize UI on load
        updateFormUI();
    });

    // Global Submit
    function submitForm() {
        const form = document.getElementById('hrRequestForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            title: 'บันทึกการแก้ไข?',
            text: "ต้องการยืนยันการแก้ไขข้อมูลหรือไม่",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(form);
                
                // Add CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch("{{ route('request.update', $hrrequest->hr_request_id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('บันทึกสำเร็จ!', 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'success')
                        .then(() => {
                            window.location.href = "{{ route('requesthr.list') }}";
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด!', data.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                });
            }
        });
    }
</script>
<style>
    /* Custom simple animation for dynamic list */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }
</style>

@endpush