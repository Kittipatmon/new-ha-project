<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center print:hidden">
            <div class="flex items-center gap-3">
                <a href="{{ route('manpower-request.index') }}" onclick="if(document.referrer){ history.back(); return false; }" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    ย้อนกลับ
                </a>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('รายละเอียดใบขออนุมัติกำลังคน #') . $manpowerRequest->id }}
                </h2>
            </div>
            <a href="{{ route('manpower-request.pdf', $manpowerRequest->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-black focus:bg-black active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                พิมพ์ (Print)
            </a>
        </div>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, button, input, select, textarea, .font-sans, h1, h2, h3, h4, h5, h6, label, span, div {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
    </style>

    <div class="py-6 sm:py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto flex flex-col 2xl:flex-row gap-6 items-start px-4 sm:px-6 lg:px-8">
            <!-- Left: Paper Container -->
            <div class="flex-1 w-full min-w-0">
                <!-- Paper Container -->
                <div class="w-full bg-white px-6 sm:px-10 lg:px-12 py-8 sm:py-10 shadow-xl border border-gray-300 rounded-xl text-[14px] sm:text-[15px] text-black font-sans print:shadow-none print:border-none print:px-0 print:py-0">
                
                <!-- Title -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold tracking-wide">ใบขออนุมัติกำลังคน</h1>
                </div>

                <!-- Date right aligned -->
                <div class="flex justify-end items-end mb-6">
                    <span class="mr-2">วันที่</span>
                    <input type="text" name="date" value="{{ $manpowerRequest->date ? \Carbon\Carbon::parse($manpowerRequest->date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th border-b border-dotted border-gray-800 bg-transparent focus:outline-none w-56 text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                </div>

                <!-- Line 1: ฝ่าย, แผนก -->
                <div class="flex items-end mb-4 w-full overflow-hidden">
                    <span class="mr-2 whitespace-nowrap flex-shrink-0">ฝ่าย</span>
                    <input type="text" name="department" value="{{ $manpowerRequest->department ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                    <span class="mx-2 whitespace-nowrap flex-shrink-0">แผนก</span>
                    <input type="text" name="section" value="{{ $manpowerRequest->section ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                </div>

                <!-- Line 2: ขออนุมัติตำแหน่ง -->
                <div class="flex items-end mb-4 w-full overflow-hidden">
                    <span class="mr-2 whitespace-nowrap flex-shrink-0">ขออนุมัติตำแหน่ง (ชื่อไทย)</span>
                    <input type="text" name="job_title_th" value="{{ $manpowerRequest->job_title_th ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                    <span class="mx-2 whitespace-nowrap flex-shrink-0">(ชื่ออังกฤษ)</span>
                    <input type="text" name="job_title_en" value="{{ $manpowerRequest->job_title_en ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                    <span class="mx-2 whitespace-nowrap flex-shrink-0">จำนวน</span>
                    <input type="number" name="headcount" value="{{ $manpowerRequest->headcount ?? '' }}" readonly disabled class="w-16 sm:w-20 flex-shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                    <span class="ml-1 sm:ml-2 whitespace-nowrap flex-shrink-0">อัตรา</span>
                </div>

                <!-- Line 3: พนักงานทั้งหมด -->
                <div class="flex items-end mb-6 w-full overflow-hidden">
                    <span class="mr-2 whitespace-nowrap flex-shrink-0">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด</span>
                    <input type="number" name="current_headcount" value="{{ $manpowerRequest->current_headcount ?? '' }}" readonly disabled class="w-16 sm:w-24 flex-shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                    <span class="mx-1 sm:mx-2 whitespace-nowrap flex-shrink-0">อัตรา</span>
                    <span class="mr-2 whitespace-nowrap ml-2 sm:ml-4 flex-shrink-0">ต้องการรับเข้าทำงานภายในวันที่</span>
                    <input type="text" name="expected_start_date" value="{{ $manpowerRequest->expected_start_date ? \Carbon\Carbon::parse($manpowerRequest->expected_start_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                </div>

                <!-- ระดับ -->
                <div class="flex items-center mb-4">
                    <span class="w-24 whitespace-nowrap">ระดับ</span>
                    <div class="flex-grow flex justify-between">
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="บริหาร (Lv.9-8)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 " {{ ($manpowerRequest->job_level ?? '') == 'บริหาร (Lv.9-8)' ? 'checked' : '' }} disabled><span>บริหาร</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.9-8)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="ผู้จัดการ (Lv.7)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 " {{ ($manpowerRequest->job_level ?? '') == 'ผู้จัดการ (Lv.7)' ? 'checked' : '' }} disabled><span>ผู้จัดการ</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.7)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="ผจก./หัวหน้าส่วนงาน (Lv.6-5)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 " {{ ($manpowerRequest->job_level ?? '') == 'ผจก./หัวหน้าส่วนงาน (Lv.6-5)' ? 'checked' : '' }} disabled><span>ผจก.ผจก./หัวหน้าส่วนงาน</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.6-5)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 " {{ ($manpowerRequest->job_level ?? '') == 'วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)' ? 'checked' : '' }} disabled><span>วิศวกร/อาวุโสเจ้าหน้าที่</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.4-3)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="Sup/ปฏิบัติการ (Lv.2-1)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 " {{ ($manpowerRequest->job_level ?? '') == 'Sup/ปฏิบัติการ (Lv.2-1)' ? 'checked' : '' }} disabled><span>Sup/ปฏิบัติการ</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.2-1)</span>
                        </label>
                    </div>
                </div>

                <!-- ลักษณะการว่าจ้าง -->
                <div class="flex items-start mb-6 mt-6 overflow-hidden">
                    <span class="w-32 whitespace-nowrap pt-0.5 flex-shrink-0">ลักษณะการว่าจ้าง</span>
                    <div class="flex-grow min-w-0 flex flex-col space-y-3" x-data="{ hire_type: '{{ $manpowerRequest->hire_type ?? '' }}' }">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="hire_type" value="จ้างเพิ่มเติม" x-model="hire_type" class="mr-3 w-4 h-4 text-black focus:ring-black border-gray-800 " disabled>
                            <span>จ้างเพิ่มเติม</span>
                        </label>
                        <div class="flex items-end w-full min-w-0 overflow-hidden">
                            <label class="flex items-center cursor-pointer mr-2 flex-shrink-0">
                                <input type="radio" name="hire_type" value="จ้างทดแทน" x-model="hire_type" class="mr-3 mb-1 w-4 h-4 text-black focus:ring-black border-gray-800 " disabled>
                                <span class="whitespace-nowrap">จ้างทดแทนคนเก่า คือ นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_replacement_name" value="{{ $manpowerRequest->hire_replacement_name ?? '' }}" readonly disabled  x-bind:disabled="hire_type !== 'จ้างทดแทน'" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed ">
                        </div>
                        <div class="flex items-end w-full min-w-0 overflow-hidden">
                            <label class="flex items-center cursor-pointer mr-2 flex-shrink-0">
                                <input type="radio" name="hire_type" value="โอนย้าย" x-model="hire_type" class="mr-3 mb-1 w-4 h-4 text-black focus:ring-black border-gray-800 " disabled>
                                <span class="whitespace-nowrap">โอนย้าย / ปรับเปลี่ยนตำแหน่ง คือ นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_transfer_name" value="{{ $manpowerRequest->hire_transfer_name ?? '' }}" readonly disabled  x-bind:disabled="hire_type !== 'โอนย้าย'" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed ">
                        </div>
                        <div class="flex items-end w-full min-w-0 overflow-hidden">
                            <label class="flex items-center cursor-pointer mr-2 flex-shrink-0">
                                <input type="radio" name="hire_type" value="จ้างชั่วคราว" x-model="hire_type" class="mr-3 mb-1 w-4 h-4 text-black focus:ring-black border-gray-800 " disabled>
                                <span class="whitespace-nowrap mr-8">จ้างชั่วคราว</span>
                                <span class="whitespace-nowrap mr-2">ระยะเวลา จากวันที่</span>
                            </label>
                            <input type="text" name="hire_temp_start" value="{{ $manpowerRequest->hire_temp_start ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_start)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled x-bind:disabled="hire_type !== 'จ้างชั่วคราว'" class="datepicker-th w-32 sm:w-40 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed flex-shrink-0">
                            <span class="mx-2 whitespace-nowrap flex-shrink-0">ถึง</span>
                            <input type="text" name="hire_temp_end" value="{{ $manpowerRequest->hire_temp_end ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_end)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled x-bind:disabled="hire_type !== 'จ้างชั่วคราว'" class="datepicker-th flex-grow min-w-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed ">
                        </div>
                    </div>
                </div>

                <!-- เอกสารแนบ -->
                <div class="flex items-center mb-6 pl-8">
                    <span class="w-24 whitespace-nowrap">เอกสารแนบ</span>
                    <div class="flex-grow flex justify-between pr-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_org_chart" value="1" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800 " {{ $manpowerRequest->attachment_org_chart ? 'checked' : '' }} disabled>
                            <span>Organization Chart</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_jd" value="1" class=" mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800 " {{ $manpowerRequest->attachment_jd ? 'checked' : '' }} disabled>
                            <span>Job Description</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_manpower_plan" value="1" class=" mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800 " {{ $manpowerRequest->attachment_manpower_plan ? 'checked' : '' }} disabled>
                            <span>แผนอัตรากำลังคน</span>
                        </label>
                    </div>
                </div>

                <!-- 2 Column Table -->
                <div class="border-[1.5px] border-black mb-6 flex">
                    <!-- Left Column -->
                    <div class="w-1/2 border-r-[1.5px] border-black">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">คุณสมบัติที่ต้องการ</div>
                        <div class="p-3 space-y-4">
                            <div class="flex items-center">
                                <span class="w-16 whitespace-nowrap">เพศ</span>
                                <label class="flex items-center mr-4 cursor-pointer"><input type="radio" name="req_gender" value="ชาย" class="mr-2 w-4 h-4 text-black border-gray-800 focus:ring-black " {{ ($manpowerRequest->req_gender ?? '') == 'ชาย' ? 'checked' : '' }} disabled> ชาย</label>
                                <label class="flex items-center mr-4 cursor-pointer"><input type="radio" name="req_gender" value="หญิง" class="mr-2 w-4 h-4 text-black border-gray-800 focus:ring-black " {{ ($manpowerRequest->req_gender ?? '') == 'หญิง' ? 'checked' : '' }} disabled> หญิง</label>
                                <label class="flex items-center cursor-pointer"><input type="radio" name="req_gender" value="ชาย/หญิง" class="mr-2 w-4 h-4 text-black border-gray-800 focus:ring-black " {{ ($manpowerRequest->req_gender ?? '') == 'ชาย/หญิง' ? 'checked' : '' }} disabled> ชาย/หญิง</label>
                            </div>
                            <div class="flex items-end flex-nowrap w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">อายุ :</span>
                                <input type="text" name="req_age" value="{{ $manpowerRequest->req_age ?? '' }}" readonly disabled class="w-16 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                                <span class="mx-2 whitespace-nowrap">วุฒิการศึกษา :</span>
                                <input type="text" name="req_education" value="{{ $manpowerRequest->req_education ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">สาขาวิชา :</span>
                                <input type="text" name="req_major" value="{{ $manpowerRequest->req_major ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">ประสบการณ์ทำงาน :</span>
                                <input type="text" name="req_experience" value="{{ $manpowerRequest->req_experience ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">คุณสมบัติพิเศษ :</span>
                                <input type="text" name="req_special" value="{{ $manpowerRequest->req_special ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">อื่นๆ :</span>
                                <input type="text" name="req_other" value="{{ $manpowerRequest->req_other ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 ">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="w-1/2">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">หน้าที่รับผิดชอบโดยสังเขป</div>
                        <div class="p-3 space-y-[1.125rem]">
                            @for($i=1; $i<=6; $i++)
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2">{{ $i }}</span>
                                <input type="text" name="res_{{ $i }}" value="{{ $manpowerRequest->{'res_'.$i} ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="space-y-6 mb-10 mt-8 w-full overflow-hidden">
                    <div class="flex flex-wrap sm:flex-nowrap justify-between items-end gap-3 w-full overflow-hidden">
                        <div class="flex items-end flex-grow min-w-0">
                            <span class="mr-2 whitespace-nowrap flex-shrink-0">ลงชื่อ</span>
                            <input type="text" name="requester_name" value="{{ $manpowerRequest->requester_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            <span class="ml-2 whitespace-nowrap flex-shrink-0">ผู้ร้องขอ</span>
                        </div>
                        <div class="flex items-end w-44 sm:w-52 flex-shrink-0">
                            <span class="mr-2 whitespace-nowrap flex-shrink-0">วันที่</span>
                            <input type="text" name="requester_date" value="{{ $manpowerRequest->requester_date ? \Carbon\Carbon::parse($manpowerRequest->requester_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap justify-between items-end gap-3 w-full overflow-hidden">
                        <div class="flex items-end flex-grow min-w-0">
                            <span class="mr-2 whitespace-nowrap flex-shrink-0">ลงชื่อ</span>
                            <input type="text" name="manager_name" value="{{ $manpowerRequest->manager_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            <span class="ml-2 whitespace-nowrap flex-shrink-0">ผู้จัดการแผนก/ฝ่าย</span>
                        </div>
                        <div class="flex items-end w-44 sm:w-52 flex-shrink-0">
                            <span class="mr-2 whitespace-nowrap flex-shrink-0">วันที่</span>
                            <input type="text" name="manager_date" value="{{ $manpowerRequest->manager_date ? \Carbon\Carbon::parse($manpowerRequest->manager_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap justify-between items-end gap-3 w-full overflow-hidden">
                        <div class="flex items-end flex-grow min-w-0">
                            <span class="mr-2 whitespace-nowrap flex-shrink-0">ลงชื่อ</span>
                            <input type="text" name="vp_name" value="{{ $manpowerRequest->vp_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            <span class="ml-2 whitespace-nowrap flex-shrink-0">ประธานสายงาน</span>
                        </div>
                        <div class="flex items-end w-44 sm:w-52 flex-shrink-0">
                            <span class="mr-2 whitespace-nowrap flex-shrink-0">วันที่</span>
                            <input type="text" name="vp_date" value="{{ $manpowerRequest->vp_date ? \Carbon\Carbon::parse($manpowerRequest->vp_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                    </div>
                </div>

                <!-- Approvals Table -->
                <div class="border-[1.5px] border-black mb-4 flex w-full overflow-hidden">
                    <!-- Left Column: HR -->
                    <div class="w-1/2 flex flex-col border-r-[1.5px] border-black overflow-hidden">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นฝ่ายบุคคล</div>
                        <div class="p-4 flex flex-col flex-grow relative pb-20 overflow-hidden">
                            <div class="w-full flex items-start">
                                <span class="mr-2 mt-2 whitespace-nowrap flex-shrink-0">ความเห็น :</span>
                                <textarea disabled class="flex-grow min-w-0 bg-transparent border-none resize-none focus:ring-0 px-2 py-2 h-16 font-semibold {{ $manpowerRequest->hr_approved_at ? 'text-green-600' : ($manpowerRequest->status === 'rejected' && !$manpowerRequest->ceo_approved_at && !$manpowerRequest->hr_approved_at && $manpowerRequest->rejection_reason ? 'text-red-600' : '') }}">{{ $manpowerRequest->hr_approved_at ? 'อนุมัติการตรวจสอบ/เห็นชอบ' : ($manpowerRequest->status === 'rejected' && !$manpowerRequest->ceo_approved_at && !$manpowerRequest->hr_approved_at && $manpowerRequest->rejection_reason ? 'ไม่อนุมัติ : ' . $manpowerRequest->rejection_reason : '') }}</textarea>
                            </div>
                            <div class="w-full px-4 absolute bottom-4 left-0 overflow-hidden">
                                <div class="text-center w-full mb-1 overflow-hidden truncate">
                                    @if($manpowerRequest->hr_approved_at)
                                        <span class="text-green-600 font-semibold block">{{ $manpowerRequest->hrApprover ? $manpowerRequest->hrApprover->firstname . ' ' . $manpowerRequest->hrApprover->lastname : '' }}</span>
                                        <span class="text-xs text-gray-500">( {{ \Carbon\Carbon::parse($manpowerRequest->hr_approved_at)->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} )</span>
                                    @else
                                        (...................................................................)
                                    @endif
                                </div>
                                <div class="text-center w-full">ผจก.แผนก/ฝ่ายทรัพยากรบุคคล</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: CEO -->
                    <div class="w-1/2 flex flex-col overflow-hidden">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นประธานเจ้าหน้าที่บริหาร</div>
                        <div class="p-4 flex flex-col flex-grow relative pb-20 overflow-hidden">
                            <div class="flex justify-center space-x-6 sm:space-x-12 w-full mt-4 flex-wrap gap-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" disabled class="mr-2 w-4 h-4 text-black border-black" {{ $manpowerRequest->ceo_approved_at ? 'checked' : '' }}> 
                                    <span class="whitespace-nowrap">อนุมัติตามคำขอ</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" disabled class="mr-2 w-4 h-4 text-black border-black" {{ ($manpowerRequest->status == 'rejected' && $manpowerRequest->rejection_reason) ? 'checked' : '' }}> 
                                    <span class="whitespace-nowrap">ไม่อนุมัติตามคำขอ</span>
                                </label>
                            </div>
                            <div class="w-full px-4 absolute bottom-4 left-0 overflow-hidden">
                                <div class="text-center w-full mb-1 overflow-hidden truncate">
                                    @if($manpowerRequest->ceo_approved_at)
                                        <span class="text-green-600 font-semibold block">{{ $manpowerRequest->ceoApprover ? $manpowerRequest->ceoApprover->firstname . ' ' . $manpowerRequest->ceoApprover->lastname : '' }}</span>
                                        <span class="text-xs text-gray-500">( {{ \Carbon\Carbon::parse($manpowerRequest->ceo_approved_at)->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} )</span>
                                    @elseif($manpowerRequest->status == 'rejected' && $manpowerRequest->rejection_reason)
                                        <span class="text-red-600 font-semibold block">ปฏิเสธ: {{ $manpowerRequest->rejection_reason }}</span>
                                    @else
                                        (...................................................................)
                                    @endif
                                </div>
                                <div class="text-center w-full">ประธานเจ้าหน้าที่บริหาร</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-red-600 text-[13px] mb-8">
                    หมายเหตุ : ใบ Request กำลังคนต้องขออนุมัติล่วงหน้า 30 วัน และกำหนดคุณสมบัติให้ละเอียด
                </div>

                <!-- HR Use Only -->
                <div class="mb-10 font-bold overflow-hidden w-full">
                    <div class="mb-4">สำหรับเจ้าหน้าที่ฝ่ายบุคคลบันทึก :</div>
                    <div class="flex items-end w-full overflow-hidden flex-wrap sm:flex-nowrap gap-y-2">
                        <span class="mr-2 whitespace-nowrap font-normal flex-shrink-0">ได้รับพนักงาน (รหัสพนักงาน)</span>
                        <input type="text" value="{{ $manpowerRequest->onboard_employee_code ?? '' }}" disabled class="w-16 sm:w-20 font-semibold text-center flex-shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-black disabled:text-black opacity-100">
                        <span class="mx-2 whitespace-nowrap font-normal flex-shrink-0">(ชื่อ-สกุล)</span>
                        <input type="text" value="{{ $manpowerRequest->onboard_employee_name ?? '' }}" disabled class="flex-grow min-w-0 font-semibold border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-black disabled:text-black opacity-100">
                        <span class="mx-2 whitespace-nowrap font-normal flex-shrink-0">เข้าทำงานในวันที่</span>
                        <input type="text" value="{{ $manpowerRequest->onboard_date ? \Carbon\Carbon::parse($manpowerRequest->onboard_date)->addYears(543)->format('d/m/Y') : ($manpowerRequest->onboard_date ?? '') }}" disabled class="w-28 sm:w-32 text-center font-semibold flex-shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-black disabled:text-black opacity-100">
                    </div>
                </div>

                <!-- Document ID -->
                <div class="text-right text-sm mb-6">
                    QF-HR-13 Rev.08 : 01-06-25
                </div>
            </div>

            </div>
            <!-- Right: Workflow Container -->
            <div class="w-full 2xl:w-[360px] flex-shrink-0 print:hidden 2xl:sticky 2xl:top-6">
                <!-- Workflow -->
                            @php
                                $statusIndex = array_search($manpowerRequest->status, [
                                    'pending_manager', 'pending_vp', 'pending_hr', 'pending_ceo', 'approved'
                                ]);
                                if ($statusIndex === false) $statusIndex = -1; // rejected or unknown
                                $isRejected = $manpowerRequest->status === 'rejected';
                            @endphp

                            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-md">
                                <h3 class="text-lg font-bold mb-5">ขั้นตอนการอนุมัติ<br><span class="text-sm font-normal text-gray-500">Approval Workflow</span></h3>
                                
                                @if($isRejected)
                                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                                        <strong>คำขอถูกปฏิเสธ:</strong> {{ $manpowerRequest->rejection_reason }}
                                    </div>
                                @endif
        
                                <div class="relative">
                                    <!-- Vertical line -->
                                    <div class="absolute left-4 top-0 h-full w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                                    
                                    <div class="space-y-6">
                                        <!-- Step 1 -->
                                        <div class="relative pl-10">
                                            <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex >= 0 ? 'bg-green-500' : 'bg-blue-500' }} border-4 border-white dark:border-gray-800 shadow"></div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">1. ผู้ร้องขอ (Requester)</p>
                                            <p class="text-sm text-gray-500">จัดทำและยื่นใบขออนุมัติกำลังคน</p>
                                        </div>
                                        
                                        <!-- Step 2 -->
                                        <div class="relative pl-10">
                                            <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex > 0 ? 'bg-green-500' : ($statusIndex === 0 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }} border-4 border-white dark:border-gray-800 shadow"></div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">2. ผู้จัดการแผนก/ฝ่าย</p>
                                            <p class="text-sm text-gray-500">ตรวจสอบและลงนามอนุมัติเบื้องต้น</p>
                                            @if($manpowerRequest->manager_approved_at)
                                                <p class="text-xs text-green-600">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->manager_approved_at)->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Step 3 -->
                                        <div class="relative pl-10">
                                            <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex > 1 ? 'bg-green-500' : ($statusIndex === 1 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }} border-4 border-white dark:border-gray-800 shadow"></div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">3. ประธานสายงาน (C Level)</p>
                                            <p class="text-sm text-gray-500">พิจารณาเห็นชอบความต้องการกำลังคน</p>
                                            @if($manpowerRequest->vp_approved_at)
                                                <p class="text-xs text-green-600">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->vp_approved_at)->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Step 4 -->
                                        <div class="relative pl-10">
                                            <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex > 2 ? 'bg-green-500' : ($statusIndex === 2 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }} border-4 border-white dark:border-gray-800 shadow"></div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">4. ผจก.ทรัพยากรบุคคล (HR)</p>
                                            <p class="text-sm text-gray-500">ตรวจสอบความถูกต้อง</p>
                                            @if($manpowerRequest->hr_approved_at)
                                                <p class="text-xs text-green-600">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->hr_approved_at)->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Step 5 -->
                                        <div class="relative pl-10">
                                            <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex > 3 ? 'bg-green-500' : ($statusIndex === 3 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }} border-4 border-white dark:border-gray-800 shadow"></div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">5. ประธานเจ้าหน้าที่บริหาร (CEO)</p>
                                            <p class="text-sm text-gray-500">พิจารณาอนุมัติขั้นตอนสุดท้าย</p>
                                            @if($manpowerRequest->ceo_approved_at)
                                                <p class="text-xs text-green-600">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->ceo_approved_at)->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $canApprove = false;
                                    if (auth()->check() && $statusIndex >= 0 && $statusIndex < 4 && $manpowerRequest->status !== 'rejected') {
                                        $u = auth()->user();
                                        $userName = trim(($u->firstname ?? '') . ' ' . ($u->lastname ?? ''));
                                        $userFullName = trim($u->fullname ?? $userName);

                                        if ($u->role === 'admin' || $u->isHrOrAdmin()) {
                                            $canApprove = true;
                                        } elseif ($manpowerRequest->status === 'pending_manager') {
                                            if (empty($manpowerRequest->manager_name) || str_contains($userName, $manpowerRequest->manager_name) || str_contains($manpowerRequest->manager_name, $u->firstname) || $userFullName === $manpowerRequest->manager_name || $u->id === $manpowerRequest->user_id) {
                                                $canApprove = true;
                                            }
                                        } elseif ($manpowerRequest->status === 'pending_vp') {
                                            if (empty($manpowerRequest->vp_name) || str_contains($userName, $manpowerRequest->vp_name) || str_contains($manpowerRequest->vp_name, $u->firstname) || $userFullName === $manpowerRequest->vp_name) {
                                                $canApprove = true;
                                            }
                                        } elseif ($manpowerRequest->status === 'pending_hr') {
                                            if ($u->hr_status == '0' || $u->dept_id == 15) {
                                                $canApprove = true;
                                            }
                                        } elseif ($manpowerRequest->status === 'pending_ceo') {
                                            if ($u->level_user == '9') {
                                                $canApprove = true;
                                            }
                                        }
                                    }
                                @endphp

                                @if($canApprove)
                                    <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-2">
                                        <form action="{{ route('admin.manpower-requests.reject', $manpowerRequest) }}" method="POST" class="w-full" id="reject-form-{{ $manpowerRequest->id }}">
                                            @csrf
                                            <input type="hidden" name="reason" value="ไม่อนุมัติในขั้นตอนนี้">
                                            <button type="button" onclick="confirmReject({{ $manpowerRequest->id }})" class="w-full inline-flex items-center justify-center px-2 py-2.5 bg-red-600 border border-transparent rounded-lg font-bold text-[11px] sm:text-xs text-white uppercase tracking-wider hover:bg-red-700 active:bg-red-900 focus:outline-none transition duration-150 shadow-md gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                                <span>ไม่อนุมัติ</span>
                                            </button>
                                        </form>

                                        @if($manpowerRequest->status === 'pending_hr')
                                            <form action="{{ route('admin.manpower-requests.approve', $manpowerRequest) }}" method="POST" class="w-full" id="approve-form-{{ $manpowerRequest->id }}">
                                                @csrf
                                                <button type="button" onclick="confirmApprove({{ $manpowerRequest->id }}, true)" class="w-full inline-flex items-center justify-center px-2 py-2.5 bg-emerald-600 border border-transparent rounded-lg font-bold text-[11px] sm:text-xs text-white uppercase tracking-wider hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition duration-150 shadow-md gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                                    <span>อนุมัติ</span>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.manpower-requests.approve', $manpowerRequest) }}" method="POST" class="w-full" id="approve-form-{{ $manpowerRequest->id }}">
                                                @csrf
                                                <button type="button" onclick="confirmApprove({{ $manpowerRequest->id }}, false)" class="w-full inline-flex items-center justify-center px-2 py-2.5 bg-emerald-600 border border-transparent rounded-lg font-bold text-[11px] sm:text-xs text-white uppercase tracking-wider hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition duration-150 shadow-md gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                                    <span>อนุมัติ</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmApprove(id, isHrStep = false) {
            if (isHrStep) {
                Swal.fire({
                    title: 'ระบุข้อมูลผู้เข้าปฏิบัติงานใหม่',
                    html:
                        '<div class="text-left space-y-3 px-1">' +
                        '  <div class="mb-3">' +
                        '    <label class="block text-sm font-semibold mb-1">รหัสพนักงาน:</label>' +
                        '    <input id="swal-input-code" class="swal2-input w-full m-0" placeholder="ตัวอย่าง: 11223">' +
                        '  </div>' +
                        '  <div class="mb-3">' +
                        '    <label class="block text-sm font-semibold mb-1">ชื่อ-สกุล:</label>' +
                        '    <input id="swal-input-name" class="swal2-input w-full m-0" placeholder="ชื่อ และนามสกุล">' +
                        '  </div>' +
                        '  <div>' +
                        '    <label class="block text-sm font-semibold mb-1">เข้าทำงานในวันที่:</label>' +
                        '    <input id="swal-input-date" type="date" class="swal2-input w-full m-0" style="padding: 0 10px;">' +
                        '  </div>' +
                        '</div>',
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'บันทึกและอนุมัติ',
                    cancelButtonText: 'ยกเลิก',
                    preConfirm: () => {
                        const code = document.getElementById('swal-input-code').value.trim();
                        const name = document.getElementById('swal-input-name').value.trim();
                        const date = document.getElementById('swal-input-date').value;
                        if (!code || !name || !date) {
                            Swal.showValidationMessage('กรุณากรอกข้อมูลให้ครบถ้วนทุกช่องครับ!');
                            return false;
                        }
                        return { code: code, name: name, date: date };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('approve-form-' + id);
                        
                        const inputCode = document.createElement('input');
                        inputCode.type = 'hidden';
                        inputCode.name = 'onboard_employee_code';
                        inputCode.value = result.value.code;
                        form.appendChild(inputCode);
                        
                        const inputName = document.createElement('input');
                        inputName.type = 'hidden';
                        inputName.name = 'onboard_employee_name';
                        inputName.value = result.value.name;
                        form.appendChild(inputName);
                        
                        const inputDate = document.createElement('input');
                        inputDate.type = 'hidden';
                        inputDate.name = 'onboard_date';
                        inputDate.value = result.value.date;
                        form.appendChild(inputDate);
                        
                        form.submit();
                    }
                });
            } else {
                Swal.fire({
                    title: 'ยืนยันการพิจารณาอนุมัติ?',
                    text: "คุณต้องการพิจารณาอนุมัติคำขออัตรากำลังนี้ใช่หรือไม่?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'ใช่, อนุมัติ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('approve-form-' + id).submit();
                    }
                });
            }
        }

        function confirmReject(id) {
            Swal.fire({
                title: 'ระบุเหตุผลการไม่อนุมัติ',
                input: 'text',
                inputPlaceholder: 'ใส่เหตุผลการไม่อนุมัติที่นี่...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ยืนยันไม่อนุมัติ',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณากรอกเหตุผลด้วยครับ!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('reject-form-' + id);
                    form.querySelector('input[name="reason"]').value = result.value;
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>