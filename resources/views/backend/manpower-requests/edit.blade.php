<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('แก้ไขใบขออนุมัติกำลังคน #') }}{{ $manpowerRequest->id }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen relative overflow-x-hidden">
        <form action="{{ route('admin.manpower-requests.update', $manpowerRequest) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- A4 Paper Container Wrapper -->
            <div class="w-full overflow-x-auto pb-4">
                <div class="min-w-[1000px] max-w-[1000px] mx-auto bg-white p-12 shadow-xl border border-gray-300 mb-8 text-black font-sans leading-relaxed relative">
                
                <!-- Main Title -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold tracking-wide">ใบขออนุมัติกำลังคน</h1>
                </div>

                <!-- Date on Right -->
                <div class="flex justify-end mb-6 text-sm">
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-1">วันที่</span>
                        <span class="border-b border-dotted border-black min-w-[200px] text-center px-2">
                            {{ \Carbon\Carbon::parse($manpowerRequest->date)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="space-y-4 text-sm mb-6">
                    <!-- Line 1: Dept & Section -->
                    <div class="flex items-end w-full">
                        <span class="whitespace-nowrap mr-1">ฝ่าย</span>
                        <input type="text" name="department" value="{{ old('department', $manpowerRequest->department) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        <span class="whitespace-nowrap mx-2">แผนก</span>
                        <input type="text" name="section" value="{{ old('section', $manpowerRequest->section) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                    </div>

                    <!-- Line 2: Job Titles & Headcount -->
                    <div class="flex items-end w-full flex-wrap gap-y-2">
                        <span class="whitespace-nowrap mr-1">ขออนุมัติตำแหน่ง (ชื่อไทย)</span>
                        <input type="text" name="job_title_th" value="{{ old('job_title_th', $manpowerRequest->job_title_th) }}" class="flex-grow border-b border-dotted border-black px-2 min-w-[150px] bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        <span class="whitespace-nowrap mx-2">(ชื่ออังกฤษ)</span>
                        <input type="text" name="job_title_en" value="{{ old('job_title_en', $manpowerRequest->job_title_en) }}" class="flex-grow border-b border-dotted border-black px-2 min-w-[150px] bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        <span class="whitespace-nowrap mx-2">จำนวน</span>
                        <input type="number" name="headcount" value="{{ old('headcount', $manpowerRequest->headcount) }}" class="border-b border-dotted border-black w-20 text-center px-1 font-bold bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        <span class="whitespace-nowrap ml-1">อัตรา</span>
                    </div>

                    <!-- Line 3: Current Headcount & Target Date -->
                    <div class="flex items-end w-full">
                        <span class="whitespace-nowrap mr-1">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด</span>
                        <input type="number" name="current_headcount" value="{{ old('current_headcount', $manpowerRequest->current_headcount) }}" class="border-b border-dotted border-black w-24 text-center px-1 font-bold bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        <span class="whitespace-nowrap mx-2">อัตรา</span>
                        <span class="whitespace-nowrap">ต้องการรับเข้าทำงานภายในวันที่</span>
                        <input type="date" name="expected_start_date" value="{{ old('expected_start_date', $manpowerRequest->expected_start_date) }}" class="flex-grow border-b border-dotted border-black px-2 text-center bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                    </div>

                    <!-- Line 4: Job Levels -->
                    <div class="flex items-center w-full pt-2">
                        <span class="whitespace-nowrap mr-4 font-semibold">ระดับ</span>
                        <div class="flex flex-row justify-between flex-grow items-center gap-x-1 sm:gap-x-4">
                            @php
                                $lvl = old('job_level', $manpowerRequest->job_level) ?? '';
                                $isLv98 = str_contains($lvl, 'Lv.9') || str_contains($lvl, 'บริหาร');
                                $isLv7 = str_contains($lvl, 'Lv.7') || str_contains($lvl, 'ผู้จัดการ');
                                $isLv65 = str_contains($lvl, 'Lv.6') || str_contains($lvl, 'หัวหน้าส่วนงาน');
                                $isLv43 = str_contains($lvl, 'Lv.4') || str_contains($lvl, 'วิศวกร') || str_contains($lvl, 'เจ้าหน้าที่');
                                $isLv21 = str_contains($lvl, 'Lv.2') || str_contains($lvl, 'Sup') || str_contains($lvl, 'ปฏิบัติการ');
                            @endphp
                            <label class="inline-flex items-center whitespace-nowrap cursor-pointer">
                                <input type="radio" name="job_level" value="บริหาร (Lv.9 - 8)" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isLv98 ? 'checked' : '' }}>
                                <span class="ml-1 text-xs sm:text-sm">บริหาร <span class="text-[10px] sm:text-xs text-gray-500">(Lv.9 - 8)</span></span>
                            </label>
                            <label class="inline-flex items-center whitespace-nowrap cursor-pointer">
                                <input type="radio" name="job_level" value="ผู้จัดการ (Lv.7)" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isLv7 ? 'checked' : '' }}>
                                <span class="ml-1 text-xs sm:text-sm">ผู้จัดการ <span class="text-[10px] sm:text-xs text-gray-500">(Lv.7)</span></span>
                            </label>
                            <label class="inline-flex items-center whitespace-nowrap cursor-pointer">
                                <input type="radio" name="job_level" value="ผช.ผจก./หัวหน้าส่วนงาน (Lv.6 - 5)" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isLv65 ? 'checked' : '' }}>
                                <span class="ml-1 text-xs sm:text-sm">ผช.ผจก./หัวหน้าส่วนงาน <span class="text-[10px] sm:text-xs text-gray-500">(Lv.6 - 5)</span></span>
                            </label>
                            <label class="inline-flex items-center whitespace-nowrap cursor-pointer">
                                <input type="radio" name="job_level" value="วิศวกร/อาวุโส/เจ้าหน้าที่ (Lv.4 - 3)" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isLv43 ? 'checked' : '' }}>
                                <span class="ml-1 text-xs sm:text-sm">วิศวกร/อาวุโส/เจ้าหน้าที่ <span class="text-[10px] sm:text-xs text-gray-500">(Lv.4 - 3)</span></span>
                            </label>
                            <label class="inline-flex items-center whitespace-nowrap cursor-pointer">
                                <input type="radio" name="job_level" value="Sup/ปฏิบัติการ (Lv.2 - 1)" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isLv21 ? 'checked' : '' }}>
                                <span class="ml-1 text-xs sm:text-sm">Sup/ปฏิบัติการ <span class="text-[10px] sm:text-xs text-gray-500">(Lv.2 - 1)</span></span>
                            </label>
                        </div>
                    </div>

                    <!-- Line 5: Hire Type Detail Layout -->
                    <div class="pt-2 flex items-start w-full">
                        <span class="whitespace-nowrap mr-4 font-semibold">ลักษณะการว่าจ้าง</span>
                        <div class="flex-grow space-y-2">
                            @php
                                $ht = old('hire_type', $manpowerRequest->hire_type) ?? '';
                                $isNew = !in_array($ht, ['จ้างทดแทน', 'ทดแทน', 'โอนย้าย', 'จ้างชั่วคราว', 'ชั่วคราว']);
                                $isRepl = in_array($ht, ['จ้างทดแทน', 'ทดแทน']);
                                $isTrans = $ht == 'โอนย้าย';
                                $isTemp = in_array($ht, ['จ้างชั่วคราว', 'ชั่วคราว']);
                            @endphp
                            
                            <!-- Row 1: จ้างเพิ่มเติม -->
                            <div class="flex items-center">
                                <label class="cursor-pointer flex items-center">
                                    <input type="radio" name="hire_type" value="จ้างเพิ่มเติม" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isNew ? 'checked' : '' }}>
                                    <span class="ml-2">จ้างเพิ่มเติม</span>
                                </label>
                            </div>

                            <!-- Row 2: จ้างทดแทน -->
                            <div class="flex items-end w-full">
                                <label class="cursor-pointer flex items-center mr-2">
                                    <input type="radio" name="hire_type" value="จ้างทดแทน" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isRepl ? 'checked' : '' }}>
                                    <span class="ml-2 whitespace-nowrap">จ้างทดแทนคนเก่า คือ นาย / นาง / นางสาว</span>
                                </label>
                                <input type="text" name="hire_replacement_name" value="{{ old('hire_replacement_name', $manpowerRequest->hire_replacement_name) }}" class="flex-grow border-b border-dotted border-black px-2 ml-1 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>

                            <!-- Row 3: โอนย้าย -->
                            <div class="flex items-end w-full">
                                <label class="cursor-pointer flex items-center mr-2">
                                    <input type="radio" name="hire_type" value="โอนย้าย" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isTrans ? 'checked' : '' }}>
                                    <span class="ml-2 whitespace-nowrap">โอนย้าย / ปรับเปลี่ยนตำแหน่ง คือ นาย / นาง / นางสาว</span>
                                </label>
                                <input type="text" name="hire_transfer_name" value="{{ old('hire_transfer_name', $manpowerRequest->hire_transfer_name) }}" class="flex-grow border-b border-dotted border-black px-2 ml-1 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>

                            <!-- Row 4: จ้างชั่วคราว -->
                            <div class="flex items-end w-full flex-wrap gap-y-1">
                                <label class="cursor-pointer flex items-center mr-2">
                                    <input type="radio" name="hire_type" value="จ้างชั่วคราว" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isTemp ? 'checked' : '' }}>
                                    <span class="ml-2 whitespace-nowrap">จ้างชั่วคราว</span>
                                </label>
                                <span class="whitespace-nowrap mx-4">ระยะเวลา จากวันที่</span>
                                <input type="date" name="hire_temp_start" value="{{ old('hire_temp_start', $manpowerRequest->hire_temp_start) }}" class="border-b border-dotted border-black w-32 text-center px-1 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                                <span class="whitespace-nowrap mx-4">ถึง</span>
                                <input type="date" name="hire_temp_end" value="{{ old('hire_temp_end', $manpowerRequest->hire_temp_end) }}" class="border-b border-dotted border-black w-32 text-center px-1 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>
                        </div>
                    </div>

                    <!-- Line 6: Attachments -->
                    <div class="pt-2 flex items-center w-full flex-wrap">
                        <span class="whitespace-nowrap mr-6 font-semibold">เอกสารแนบ</span>
                        <div class="flex flex-wrap gap-8">
                            <input type="hidden" name="attachment_org_chart" value="0">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="attachment_org_chart" value="1" class="form-checkbox h-4 w-4 text-black border-gray-400 rounded focus:ring-0" {{ old('attachment_org_chart', $manpowerRequest->attachment_org_chart) ? 'checked' : '' }}>
                                <span class="ml-2">Organization Chart</span>
                            </label>
                            
                            <input type="hidden" name="attachment_jd" value="0">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="attachment_jd" value="1" class="form-checkbox h-4 w-4 text-black border-gray-400 rounded focus:ring-0" {{ old('attachment_jd', $manpowerRequest->attachment_jd) ? 'checked' : '' }}>
                                <span class="ml-2">Job Description</span>
                            </label>
                            
                            <input type="hidden" name="attachment_manpower_plan" value="0">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="attachment_manpower_plan" value="1" class="form-checkbox h-4 w-4 text-black border-gray-400 rounded focus:ring-0" {{ old('attachment_manpower_plan', $manpowerRequest->attachment_manpower_plan) ? 'checked' : '' }}>
                                <span class="ml-2">แผนอัตรากำลังคน</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Two-Column Grid: Qualifications & Responsibilities -->
                <div class="grid grid-cols-1 md:grid-cols-2 border border-black text-sm mb-6">
                    
                    <!-- Left: คุณสมบัติที่ต้องการ -->
                    <div class="border-r border-black p-4 space-y-4">
                        <div class="text-center font-bold border-b border-black pb-1 mb-2 bg-gray-50">คุณสมบัติที่ต้องการ</div>
                        
                        <!-- Gender -->
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="mr-2 font-semibold">เพศ</span>
                            @php
                                $g = old('req_gender', $manpowerRequest->req_gender) ?? '';
                                $isMale = $g == 'ชาย';
                                $isFemale = $g == 'หญิง';
                                $isBoth = in_array($g, ['ชาย/หญิง', 'ชาย / หญิง', 'ไม่จำกัดเพศ', 'ชาย หรือ หญิง']);
                            @endphp
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="req_gender" value="ชาย" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isMale ? 'checked' : '' }}>
                                <span class="ml-1">ชาย</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="req_gender" value="หญิง" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ $isFemale ? 'checked' : '' }}>
                                <span class="ml-1">หญิง</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="req_gender" value="ชาย/หญิง" class="form-radio h-4 w-4 text-black border-gray-400 focus:ring-0" {{ ($isBoth || (!$isMale && !$isFemale)) ? 'checked' : '' }}>
                                <span class="ml-1">ชาย/หญิง</span>
                            </label>
                        </div>

                        <!-- Age & Education -->
                        <div class="flex items-end w-full">
                            <span class="whitespace-nowrap mr-1">อายุ :</span>
                            <input type="text" name="req_age" value="{{ old('req_age', $manpowerRequest->req_age) }}" class="w-20 border-b border-dotted border-black px-1 text-center font-semibold bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            <span class="whitespace-nowrap mx-2">วุฒิการศึกษา :</span>
                            <input type="text" name="req_education" value="{{ old('req_education', $manpowerRequest->req_education) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        </div>

                        <!-- Major -->
                        <div class="flex items-end w-full">
                            <span class="whitespace-nowrap mr-1">สาขาวิชา :</span>
                            <input type="text" name="req_major" value="{{ old('req_major', $manpowerRequest->req_major) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        </div>

                        <!-- Experience -->
                        <div class="flex items-end w-full">
                            <span class="whitespace-nowrap mr-1">ประสบการณ์ทำงาน :</span>
                            <input type="text" name="req_experience" value="{{ old('req_experience', $manpowerRequest->req_experience) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        </div>

                        <!-- Special Skill -->
                        <div class="flex items-end w-full">
                            <span class="whitespace-nowrap mr-1">คุณสมบัติพิเศษ :</span>
                            <input type="text" name="req_special" value="{{ old('req_special', $manpowerRequest->req_special) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        </div>

                        <!-- Other -->
                        <div class="flex items-end w-full">
                            <span class="whitespace-nowrap mr-1">อื่นๆ :</span>
                            <input type="text" name="req_other" value="{{ old('req_other', $manpowerRequest->req_other) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                        </div>
                    </div>

                    <!-- Right: หน้าที่รับผิดชอบโดยสังเขป -->
                    <div class="p-4 space-y-4">
                        <div class="text-center font-bold border-b border-black pb-1 mb-2 bg-gray-50">หน้าที่รับผิดชอบโดยสังเขป</div>
                        
                        <div class="space-y-3">
                            <div class="flex items-end w-full">
                                <span class="mr-2">1</span>
                                <input type="text" name="res_1" value="{{ old('res_1', $manpowerRequest->res_1) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>
                            <div class="flex items-end w-full">
                                <span class="mr-2">2</span>
                                <input type="text" name="res_2" value="{{ old('res_2', $manpowerRequest->res_2) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>
                            <div class="flex items-end w-full">
                                <span class="mr-2">3</span>
                                <input type="text" name="res_3" value="{{ old('res_3', $manpowerRequest->res_3) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>
                            <div class="flex items-end w-full">
                                <span class="mr-2">4</span>
                                <input type="text" name="res_4" value="{{ old('res_4', $manpowerRequest->res_4) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>
                            <div class="flex items-end w-full">
                                <span class="mr-2">5</span>
                                <input type="text" name="res_5" value="{{ old('res_5', $manpowerRequest->res_5) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>
                            <div class="flex items-end w-full">
                                <span class="mr-2">6</span>
                                <input type="text" name="res_6" value="{{ old('res_6', $manpowerRequest->res_6) }}" class="flex-grow border-b border-dotted border-black px-2 bg-transparent focus:outline-none focus:ring-0 py-0 border-t-0 border-l-0 border-r-0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer doc code -->
                <div class="flex justify-between items-end mt-8 text-xs text-gray-500 font-mono">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.manpower-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ย้อนกลับ
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                    <span>QF-HR-13 Rev.08 : 01-06-25</span>
                </div>
            </div>
            <!-- End of Wrapper -->
            </div>
        </form>
    </div>
</x-admin-layout>
