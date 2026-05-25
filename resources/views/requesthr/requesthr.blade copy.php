@extends('layouts.app')

@section('content')
<div class="py-8 px-4 font-sans">
    <div class="max-w-6xl mx-auto">

        <div class="text-sm text-gray-500 mb-4">
            Home > Request HR > Request Form
        </div>

        <div class="dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-200/40 p-6 relative overflow-hidden">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-2">
                <h1 class="text-2xl font-bold">Request HR Form</h1>
                
                <div class="text-xs mt-2 md:mt-0 bg-red-500 text-white px-3 py-1 rounded-full font-medium shadow-sm">
                    วันที่ร้องขอ: {{ date('d/m/Y') }}
                </div>
            </div>

            <div class="bg-gray-200/80 rounded-lg p-2 text-sm flex items-start gap-2 dark:bg-gray-800">
                <span class="font-bold whitespace-nowrap">คำแนะนำ:</span>
                <span>กรุณาเลือกประเภทการร้องขอและกรอกข้อมูลที่จำเป็น เครื่องหมาย <span class="text-red-500">*</span> คือข้อมูลที่ต้องระบุ</span>
            </div>

            <h2 class="text-lg font-semibold mb-4">
                แจ้งร้องขอดำเนินการเอกสารฝ่ายทรัพยากรบุคคล <span class="text-red-500">*</span>
            </h2>

            <form id="hrRequestForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="form-control w-full">
                        <label class="label mb-1">
                            <span class="label-text font-semibold dark:text-white">ประเภทร้องขอ <span class="text-red-500">*</span></span>
                        </label>
                        <select id="requestType" name="type" class="select select-bordered w-full border-blue-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-lg h-10 min-h-[2.5rem]">
                            <option disabled selected value="">-- เลือกประเภทการร้องขอ --</option>
                            <option value="การแจ้งแก้ไขเวลา">การแจ้งแก้ไขเวลา</option>
                            <option value="การแจ้งขอเอกสารอื่นๆ">การแจ้งขอเอกสารอื่นๆ</option>
                            <option value="การแจ้งขอเอกสาร Safety">การแจ้งขอเอกสาร Safety</option>
                        </select>
                    </div>

                    <div class="form-control w-full">
                        <label class="label mb-1">
                            <span class="label-text font-semibold dark:text-white">ตัวเลือกการร้องขอ <span class="text-red-500">*</span></span>
                        </label>
                        <select id="requestOption" name="option" class="select select-bordered w-full disabled:text-gray-400 rounded-lg h-10 min-h-[2.5rem]" disabled>
                            <option disabled selected value="">-- เลือกตัวเลือกการร้องขอ --</option>
                        </select>
                    </div>

                    <div class="form-control w-full">
                        <label class="label mb-1">
                            <span class="label-text font-semibold dark:text-white">ประเภทย่อย</span>
                        </label>
                        <select id="subOption" class="select select-bordered w-full text-gray-400 rounded-lg h-10 min-h-[2.5rem]" disabled>
                            <option disabled selected>-- เลือกประเภทย่อย --</option>
                        </select>
                    </div>
                </div>

                <div id="reasonSection" class="mb-6 hidden">
                    <div class="form-control">
                        <textarea id="reasonInput" name="reason" class="textarea textarea-bordered h-24 w-full rounded-lg" placeholder="ระบุรายละเอียด..."></textarea>
                    </div>
                </div>

                <div id="timeEditSection" class="p-4 rounded-xl border border-gray-200/40 mb-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div>
                            <h3 class="font-bold mb-3">ช่วงเริ่มต้น</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-600 block mb-1">วันที่</label>
                                    <div class="relative">
                                        <input type="date" name="start_date" class="input input-bordered w-full h-10 rounded-lg pr-2 text-sm">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">หากเป็นวันเดียวกัน ควรระบุเวลาสิ้นสุดมากกว่าเวลาเริ่มต้น</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 block mb-1">เวลา</label>
                                    <input type="time" name="start_time" class="input input-bordered w-full h-10 rounded-lg text-sm" placeholder="เช่น 09:00">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-bold mb-3">ช่วงสิ้นสุด</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-600 block mb-1">วันที่</label>
                                    <input type="date" name="end_date" class="input input-bordered w-full h-10 rounded-lg text-sm">
                                    <p class="text-xs text-gray-400 mt-1">ใช้รูปแบบ 24 ชั่วโมง (HH:mm) เช่น 09:00</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 block mb-1">เวลา</label>
                                    <input type="time" name="end_time" class="input input-bordered w-full h-10 rounded-lg text-sm" placeholder="เช่น 17:00">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="safetySection" class="p-4 rounded-xl border border-gray-200/40 mb-6 hidden">
                    <h3 class="font-bold mb-3">รายการอุปกรณ์ Safety</h3>
                    <div id="safetyItemsContainer">
                        <div class="flex gap-2 mb-2 safety-item">
                            <input type="text" name="safety_name[]" class="input input-bordered w-3/4 h-10 rounded-lg" placeholder="ระบุชื่ออุปกรณ์">
                            <input type="number" name="safety_qty[]" value="1" min="1" class="input input-bordered w-1/4 h-10 rounded-lg" placeholder="จำนวน">
                            <button type="button" class="btn btn-sm btn-circle btn-ghost text-red-500 delete-row-btn hidden">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" id="addSafetyBtn" class="btn btn-sm btn-outline btn-info mt-2 gap-2">
                        <i class="fa-solid fa-plus"></i> เพิ่มรายการ
                    </button>
                </div>

                <div class="form-control w-full mb-8">
                    <label class="label mb-1">
                        <span class="label-text font-bold text-gray-800">แนบไฟล์</span>
                    </label>
                        <input type="file" id="fileInput" name="attachment" class="file-input file-input-info w-full max-w-full text-sm text-gray-500 "/>
             
                    <label class="label">
                        <span class="label-text-alt text-gray-400">อัปโหลดได้สูงสุด 10 ไฟล์, ไฟล์ละไม่เกิน 5MB, รวมไม่เกิน 40MB</span>
                    </label>

                    <div id="filePreview" class="mt-4 hidden">
                        <p class="text-sm text-gray-600 mb-2">ตัวอย่างไฟล์:</p>
                        <img id="imagePreview" src="" alt="Preview" class="max-h-48 rounded border border-gray-200 shadow-sm hidden">
                        <div id="pdfPreview" class="hidden items-center gap-2 text-red-500 bg-red-50 p-2 rounded w-fit">
                            <i class="fa-solid fa-file-pdf text-xl"></i>
                            <span id="pdfName" class="text-sm">filename.pdf</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" id="submitBtn" class="btn bg-[#dcfce7] hover:bg-[#bbf7d0] text-[#166534] border-transparent px-8 rounded-lg font-bold">
                        บันทึกข้อมูล
                    </button>
                    <button type="reset" id="resetBtn" class="btn bg-gray-100 hover:bg-gray-200 text-gray-600 border-transparent px-6 rounded-lg font-bold">
                        รีเซ็ต
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- 1. Data Configuration ---
    const requestOptionsData = {
        'การแจ้งแก้ไขเวลา': [
            'ลาป่วย', 'ลากิจ', 'ลาพักร้อน', 'ลืมสแกนนิ้ว', 'สแกนนิ้วไม่ติด'
        ],
        'การแจ้งขอเอกสารอื่นๆ': [
            'ใบรับรอง', 'ใบร้องขอสวัสดิการ', 'ใบร้องขอชุดยูนิฟอร์ม'
        ],
        'การแจ้งขอเอกสาร Safety': [
            'ใบร้องขออุปกรณ์ Safety', 'ใบรับรอง Safety', 'ใบขอนอนห้องพยาบาล'
        ]
    };

    // --- 2. Element References ---
    const typeSelect = document.getElementById('requestType');
    const optionSelect = document.getElementById('requestOption');
    const subOptionSelect = document.getElementById('subOption');
    
    const timeEditSection = document.getElementById('timeEditSection');
    const safetySection = document.getElementById('safetySection');
    const reasonSection = document.getElementById('reasonSection');
    const reasonInput = document.getElementById('reasonInput'); // textarea
    
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const imagePreview = document.getElementById('imagePreview');
    const pdfPreview = document.getElementById('pdfPreview');
    const pdfName = document.getElementById('pdfName');
    
    const submitBtn = document.getElementById('submitBtn');

    // --- 3. Event Listeners ---

    // Handle Type Change
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Reset and Enable Option Select
        optionSelect.innerHTML = '<option disabled selected value="">-- เลือกตัวเลือกการร้องขอ --</option>';
        optionSelect.disabled = false;
        optionSelect.classList.remove('bg-gray-50');
        // optionSelect.classList.add('bg-white');

        // Populate Options
        if (requestOptionsData[selectedType]) {
            requestOptionsData[selectedType].forEach(opt => {
                const option = document.createElement('option');
                option.value = opt;
                option.textContent = opt;
                option.classList.add('py-2'); // Add padding for better look
                optionSelect.appendChild(option);
            });
        }

        // Reset Logic based on Type
        hideAllDynamicSections();
        
        // Specifically for "การแจ้งแก้ไขเวลา", show time section immediately (or wait for option? image implies layout matches type)
        // In this logic: if Type is "Edit Time", show form.
        if (selectedType === 'การแจ้งแก้ไขเวลา') {
             // Wait for specific option or show immediately? Usually wait for option, but image shows full form.
             // Let's hide until option selected to be safe, OR show if logic dictates.
        }
        
        // Safety Section Logic
        if (selectedType === 'การแจ้งขอเอกสาร Safety') {
             // Logic specific to Safety
        }
    });

    // Handle Option Change
    optionSelect.addEventListener('change', function() {
        const selectedType = typeSelect.value;
        const selectedOption = this.value;
        
        hideAllDynamicSections();

        // Logic for Displaying Sections
        if (selectedType === 'การแจ้งแก้ไขเวลา') {
            reasonSection.classList.remove('hidden');
            timeEditSection.classList.remove('hidden');
            // Add specific placeholder to textarea based on context
            reasonInput.placeholder = "ระบุสาเหตุการ" + selectedOption; 
        } 
        else if (selectedOption === 'ใบร้องขออุปกรณ์ Safety') {
            safetySection.classList.remove('hidden');
        }
        else {
            // Default: Show Reason Textarea for general requests
            reasonSection.classList.remove('hidden');
            reasonInput.placeholder = "ระบุรายละเอียดเพิ่มเติม";
        }
    });

    // Handle File Preview
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            filePreview.classList.add('hidden');
            return;
        }

        filePreview.classList.remove('hidden');
        imagePreview.classList.add('hidden');
        pdfPreview.classList.add('hidden');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            pdfName.textContent = file.name;
            pdfPreview.classList.remove('hidden');
            pdfPreview.classList.add('flex');
        }
    });

    // Handle Safety Items (Add/Remove)
    const safetyContainer = document.getElementById('safetyItemsContainer');
    document.getElementById('addSafetyBtn').addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'flex gap-2 mb-2 safety-item';
        newRow.innerHTML = `
            <input type="text" name="safety_name[]" class="input input-bordered w-3/4 h-10 rounded-lg" placeholder="ระบุชื่ออุปกรณ์">
            <input type="number" name="safety_qty[]" value="1" min="1" class="input input-bordered w-1/4 h-10 rounded-lg" placeholder="จำนวน">
            <button type="button" class="btn btn-sm btn-circle btn-ghost text-red-500 delete-row-btn">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
        safetyContainer.appendChild(newRow);
        updateDeleteButtons();
    });

    // Use event delegation for delete buttons
    safetyContainer.addEventListener('click', function(e) {
        if (e.target.closest('.delete-row-btn')) {
            e.target.closest('.safety-item').remove();
            updateDeleteButtons();
        }
    });

    // Submit Logic
    submitBtn.addEventListener('click', function() {
        // Here you would normally submit the form
        // document.getElementById('hrRequestForm').submit();
        Swal.fire({
            title: 'บันทึกข้อมูล?',
            text: "ต้องการยืนยันการทำรายการหรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#166534',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'สำเร็จ!',
                    'ข้อมูลของคุณถูกบันทึกแล้ว (จำลอง)',
                    'success'
                )
            }
        });
    });

    // --- Helper Functions ---
    function hideAllDynamicSections() {
        timeEditSection.classList.add('hidden');
        safetySection.classList.add('hidden');
        reasonSection.classList.add('hidden');
    }

    function updateDeleteButtons() {
        const rows = safetyContainer.querySelectorAll('.safety-item');
        rows.forEach(row => {
            const btn = row.querySelector('.delete-row-btn');
            if (rows.length > 1) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush