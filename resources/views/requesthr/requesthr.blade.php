@extends('layouts.hrrequest.app')
@section('content')
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 font-prompt">
        <nav class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-6 font-medium">
            <ol class="list-none p-0 flex flex-wrap items-center gap-2">
                <li class="flex items-center whitespace-nowrap">
                    <a href="{{ route('welcome') }}" class="hover:text-red-600 transition-colors">Home</a>
                </li>
                <li class="text-gray-400 dark:text-gray-600">&gt;</li>
                <li class="flex items-center whitespace-nowrap">
                    <a href="{{ route('request.hr') }}" class="hover:text-red-600 transition-colors">Request HR</a>
                </li>
                <li class="text-gray-400 dark:text-gray-600">&gt;</li>
                <li class="text-red-600 font-semibold">
                    Request HR Form
                </li>
            </ol>
        </nav>

        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl sm:rounded-2xl shadow-sm overflow-hidden transition-all">
            <!-- Header -->
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 dark:bg-red-500/20 p-2 rounded-lg text-red-600 dark:text-red-500">
                        <i class="fa-solid fa-clipboard-list text-base sm:text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100">Request HR Form</h1>
                        <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5">แจ้งร้องขอดำเนินการเอกสารฝ่ายทรัพยากรบุคคล</p>
                    </div>
                </div>
                <div class="text-[11px] sm:text-xs bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 px-3 py-1.5 rounded-full font-medium border border-red-100 dark:border-red-500/20 w-full sm:w-auto text-center sm:text-left">
                    <i class="far fa-calendar-alt mr-1"></i> วันที่ร้องขอ: {{ date('d/m/Y') }}
                </div>
            </div>

            <div class="p-4 sm:p-6 md:p-8">
                <form id="hrRequestForm" enctype="multipart/form-data">
                    <div class="flex items-start gap-2 px-3 sm:px-4 py-3 mb-5 sm:mb-6 text-xs sm:text-sm text-blue-700 bg-blue-50 border border-blue-100 rounded-lg dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <span>กรุณากรอกข้อมูลให้ครบถ้วนในช่องที่มีเครื่องหมาย <span class="text-red-500 font-bold">*</span></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                        <div class="form-control w-full">
                            <label class="label font-medium mb-1 px-1 py-0.5">
                                <span class="label-text text-xs sm:text-sm text-gray-700 dark:text-gray-300">หมวดคำร้อง (Request Category) <span class="text-red-500">*</span></span>
                            </label>
                            <select id="categorySelect" name="category_id"
                                class="select select-bordered w-full bg-white dark:bg-gray-800 transition-colors focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                <option disabled selected value="">เลือกหมวดคำร้อง</option>
                                @foreach ($Requestcategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name_th }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control w-full">
                            <label class="label font-medium mb-1 px-1 py-0.5">
                                <span class="label-text text-xs sm:text-sm text-gray-700 dark:text-gray-300">ประเภทคำร้อง (Request Type) <span class="text-red-500">*</span></span>
                            </label>
                            <select id="typeSelect" name="type_id"
                                class="select select-bordered w-full bg-white dark:bg-gray-800 transition-colors disabled:bg-gray-50 dark:disabled:bg-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500" disabled>
                                <option disabled selected value="">เลือกหมวดคำร้องก่อน</option>
                            </select>
                        </div>

                        <div class="form-control w-full sm:col-span-2 md:col-span-1">
                            <label class="label font-medium mb-1 px-1 py-0.5">
                                <span class="label-text text-xs sm:text-sm text-gray-700 dark:text-gray-300">ตัวเลือกรายละเอียด (Request Subtype) <span class="text-red-500">*</span></span>
                            </label>
                            <select id="subtypeSelect" name="subtype_id"
                                class="select select-bordered w-full bg-white dark:bg-gray-800 transition-colors disabled:bg-gray-50 dark:disabled:bg-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500" disabled>
                                <option disabled selected value="">เลือกประเภทคำร้องก่อน</option>
                            </select>
                        </div>
                    </div>

                    <!-- พื้นที่ฟอร์มส่วน Dynamic -->
                    <div id="dynamicContent" class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700/50 mt-4">
                        <!-- การแจ้งแก้ไขเวลา -->
                        <div id="sectionTimeEdit" class="hidden space-y-5 sm:space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="label font-medium text-xs sm:text-sm">เหตุผลที่ขอดำเนินการ</label>
                                    <textarea name="edit_reason" class="textarea textarea-bordered w-full h-24"
                                        placeholder="ระบุสาเหตุ..."></textarea>
                                </div>

                                <div class="form-control">
                                    <label class="label text-xs sm:text-sm">วันที่เริ่มต้น</label>
                                    <input type="date" name="edit_start_date" class="input input-bordered w-full" />
                                </div>
                                <div class="form-control">
                                    <label class="label text-xs sm:text-sm">เวลาเริ่มต้น</label>
                                    <input type="time" name="edit_start_time" id="edit_start_time"
                                        class="input input-bordered w-full" />
                                </div>

                                <div class="form-control">
                                    <label class="label text-xs sm:text-sm">วันที่สิ้นสุด</label>
                                    <input type="date" name="edit_end_date" class="input input-bordered w-full" />
                                </div>
                                <div class="form-control">
                                    <label class="label text-xs sm:text-sm">เวลาสิ้นสุด</label>
                                    <input type="time" name="edit_end_time" id="edit_end_time"
                                        class="input input-bordered w-full" />
                                </div>
                            </div>

                            <div class="form-control mt-4">
                                <label class="label font-medium text-xs sm:text-sm">แนบไฟล์หลักฐาน</label>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                    <input type="file" id="fileInput" name="timefile"
                                        class="file-input file-input-bordered file-input-primary w-full max-w-full sm:max-w-md"
                                        accept="image/*,application/pdf" />
                                </div>
                                <div id="filePreviewContainer" class="mt-3 hidden p-4 border rounded-lg bg-white dark:bg-gray-800">
                                    <div class="flex items-center gap-3">
                                        <div id="fileIcon" class="text-xl sm:text-2xl"></div>
                                        <div class="flex-1 overflow-hidden">
                                            <p id="fileName" class="text-xs sm:text-sm font-semibold truncate"></p>
                                            <button type="button" id="btnPreviewFile"
                                                class="btn btn-xs btn-outline btn-info mt-1">ดูตัวอย่าง</button>
                                        </div>
                                        <button type="button" id="btnClearFile"
                                            class="btn btn-square btn-ghost btn-sm text-error">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ร้องขอชุดยูนิฟอร์ม -->
                        <div id="sectionUniform" class="hidden space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label text-xs sm:text-sm">เพศ</label>
                                    <select name="uniform_gender" class="select select-bordered w-full">
                                        <option disabled selected value="">เลือกเพศ</option>
                                        <option value="ชาย">ชาย</option>
                                        <option value="หญิง">หญิง</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label text-xs sm:text-sm">ขนาดชุด</label>
                                    <select name="uniform_size" class="select select-bordered w-full">
                                        <option disabled selected value="">เลือกขนาด</option>
                                        <option value="S">S</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                        <option value="XXL">XXL</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label text-xs sm:text-sm">เหตุผลที่ขอชุด</label>
                                    <textarea name="uniform_reason" class="textarea textarea-bordered w-full"
                                        placeholder="ระบุเหตุผล..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ร้องขอเอกสาร Safety Equipment -->
                        <div id="sectionSafetyEquip" class="hidden space-y-4">
                            <label class="label font-medium text-xs sm:text-sm">รายการอุปกรณ์ที่ร้องขอ</label>
                            <div id="safetyListContainer" class="space-y-3"></div>
                            <button type="button" id="btnAddSafety" class="btn btn-outline btn-info btn-sm w-full sm:w-auto">
                                <i class="fa-solid fa-plus mr-1"></i> เพิ่มรายการ
                            </button>
                        </div>

                        <!-- เหตุผลที่ขอใบรับรอง Certificate -->
                        <div id="sectionCertificateReason" class="hidden space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label font-medium text-xs sm:text-sm">เหตุผลที่ขอเอกสาร Certificate</label>
                                <textarea name="certificate_reason" class="textarea textarea-bordered w-full h-24"
                                    placeholder="ระบุเหตุผล..."></textarea>
                            </div>
                        </div>

                        <!-- เหตุผลที่ขอใบรับรอ Welfare Request -->
                        <div id="sectionWelfareReason" class="hidden space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label font-medium text-xs sm:text-sm">เหตุผลที่ขอเอกสาร Welfare Request</label>
                                <textarea name="welfare_reason" class="textarea textarea-bordered w-full h-24"
                                    placeholder="ระบุเหตุผล..."></textarea>
                            </div>
                        </div>

                        <!-- เหตุผลที่ขอใบรับรอง Safety Document -->
                        <div id="sectionSafetyReason" class="hidden space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label font-medium text-xs sm:text-sm">เหตุผลที่ขอเอกสาร Safety</label>
                                <textarea name="safety_reason" class="textarea textarea-bordered w-full h-24"
                                    placeholder="ระบุเหตุผล..."></textarea>
                            </div>
                        </div>
                    </div> <!-- ปิด dynamicContent -->

                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6 sm:mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('request.hr') }}" class="btn btn-ghost hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 w-full sm:w-auto text-center justify-center">
                            <i class="fa-solid fa-xmark mr-1"></i> ยกเลิก
                        </a>
                        <button type="button" class="btn bg-red-600 hover:bg-red-700 text-white border-none shadow-md hover:shadow-lg transition-all px-8 w-full sm:w-auto justify-center"
                            onclick="submitForm()">
                            <i class="fa-solid fa-save mr-1"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
                </div> <!-- ปิด p-6 -->
            </div> <!-- ปิด bg-white wrapper -->
        </div> <!-- ปิด w-full container -->

    <dialog id="preview_modal" class="modal">
        <div class="modal-box p-0 overflow-hidden relative max-w-3xl">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-10 bg-white/50 hover:bg-white"
                onclick="preview_modal.close()">✕</button>
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
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#edit_start_time", {
                disableMobile: true,
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
            flatpickr("#edit_end_time", {
                disableMobile: true,
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
                    if (el) el.classList.add('hidden');
                });
            }

            // ฟังก์ชันรีเซ็ต Dropdown
            function resetSelect(selectEl, placeholder) {
                selectEl.innerHTML = `<option disabled selected value="">${placeholder}</option>`;
                selectEl.disabled = true;
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
                // โครงสร้างถูกเปลี่ยนเป็น if เดี่ยวๆ เพื่อให้แสดงผลซ้อนกันได้ (เช่น เหตุผล + รายการอุปกรณ์)

                // --- แสดงผลตามหมวดหมู่ (Category) ---

                // ถ้าเลือกหมวด "Safety/ความปลอดภัย" (ID=3) ให้แสดงฟิลด์เหตุผลเสมอ
                if (catId == '3') {
                    if (sections.safetyReason) sections.safetyReason.classList.remove('hidden');
                }

                // ถ้าเลือกหมวด "แก้ไขเวลา/Time Attendance" (ID=1) ให้แสดงฟอร์มแก้ไขเวลา
                if (catId == '1') {
                    if (sections.timeEdit) sections.timeEdit.classList.remove('hidden');
                }

                // --- แสดงผลตามประเภท (Type) ---

                // ถ้าเลือกประเภท "ใบร้องขอชุดยูนิฟอร์ม"
                if (selectedTypeText === 'ใบร้องขอชุดยูนิฟอร์ม') {
                    if (sections.uniform) sections.uniform.classList.remove('hidden');
                }

                // ถ้าเลือกประเภท "ใบร้องขออุปกรณ์ Safety"
                if (selectedTypeText === 'ใบร้องขออุปกรณ์ Safety') {
                    if (sections.safetyEquip) sections.safetyEquip.classList.remove('hidden');
                }

                // ถ้าเลือกประเภท "ใบรับรอง"
                if (selectedTypeText === 'ใบรับรอง') {
                    if (sections.certificateReason) sections.certificateReason.classList.remove('hidden');
                }

                // ถ้าเลือกประเภท "ใบร้องขอสวัสดิการ"
                if (selectedTypeText === 'ใบร้องขอสวัสดิการ') {
                    if (sections.welfareReason) sections.welfareReason.classList.remove('hidden');
                }

                // --- ตัวอย่างเพิ่มเติม: เช็คลึกถึงระดับ Type หรือ Subtype ---
                /*
                if (typeId == '15') { 
                    // ถ้าเลือกประเภท ID 15 ให้โชว์ฟอร์มเฉพาะ
                }
                */
            }

            // ==========================================
            // 4. EVENT LISTENERS (DROPDOWNS)
            // ==========================================

            // --- เมื่อเลือก Category ---
            categorySelect.addEventListener('change', function () {
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
            typeSelect.addEventListener('change', function () {
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
            subtypeSelect.addEventListener('change', function () {
                // อัปเดต UI (เผื่อบางฟอร์มขึ้นอยู่กับ Subtype)
                updateFormUI();
            });


            // ==========================================
            // 5. SAFETY & FILE LOGIC (คงเดิม)
            // ==========================================

            // ... (ส่วน Safety List และ File Preview ใช้โค้ดเดิมได้เลยครับ) ...
            // เพื่อความกระชับ ผมละไว้ในฐานที่เข้าใจ ถ้าต้องการให้แปะซ้ำแจ้งได้ครับ

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
                div.querySelector('.btn-remove-safety').addEventListener('click', function () {
                    if (safetyContainer.children.length > 1) div.remove();
                    else Swal.fire({ icon: 'warning', title: 'แจ้งเตือน', text: 'ต้องมีรายการอย่างน้อย 1 รายการ', timer: 1500, showConfirmButton: false });
                });
                safetyContainer.appendChild(div);
            }
            if (safetyContainer) {
                addSafetyItem();
                btnAddSafety.addEventListener('click', addSafetyItem);
            }

            // --- File Preview Logic ---
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('filePreviewContainer');
            const fileIcon = document.getElementById('fileIcon');
            const fileName = document.getElementById('fileName');
            const btnPreviewFile = document.getElementById('btnPreviewFile');
            const btnClearFile = document.getElementById('btnClearFile');

            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
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
                btnClearFile.addEventListener('click', function () {
                    fileInput.value = '';
                    previewContainer.classList.add('hidden');
                });
            }
        });

        // Global Submit
        function submitForm() {
            const form = document.getElementById('hrRequestForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'บันทึกข้อมูล?',
                text: "ต้องการยืนยันการทำรายการหรือไม่",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);

                    // Add CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    fetch("{{ route('request.store') }}", {
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
                                Swal.fire('บันทึกสำเร็จ!', 'ระบบได้รับข้อมูลเรียบร้อยแล้ว', 'success')
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
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-out;
        }
    </style>

@endpush