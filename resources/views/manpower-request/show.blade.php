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
            <div class="flex items-center gap-2">
                <a href="{{ route('manpower-request.pdf', $manpowerRequest->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-black focus:bg-black active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    พิมพ์ (Print)
                </a>
                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->isHrOrAdmin()))
                    <button type="button" onclick="openShareModal('manpower_request', {{ $manpowerRequest->id }}, 'ใบขออนุมัติกำลังคน #{{ $manpowerRequest->id }}')" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-900 transition ease-in-out duration-150 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                        </svg>
                        แชร์เอกสาร
                    </button>
                @endif
            </div>
        </div>
    </x-slot>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, button, input, select, textarea, .font-sans, h1, h2, h3, h4, h5, h6, label, span, div {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
    </style>

    <div class="py-6 sm:py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-[1480px] mx-auto flex flex-col 2xl:flex-row gap-6 items-start px-4 sm:px-6 lg:px-8">
            <!-- Left: Paper Container -->
            <div class="flex-1 w-full min-w-0">
                @if(!empty($activeShare))
                    @php
                        $sharedByName = $activeShare->sender 
                            ? ($activeShare->sender->fullname ?? trim(($activeShare->sender->firstname ?? '') . ' ' . ($activeShare->sender->lastname ?? '')))
                            : 'ผู้ดูแลระบบ';
                        $sharedTime = $activeShare->created_at ? \Carbon\Carbon::parse($activeShare->created_at)->format('d/m/Y H:i น.') : '-';
                    @endphp
                    <div class="mb-4 flex items-center gap-3 p-4 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-xl text-sky-800 dark:text-sky-200 text-sm shadow-sm print:hidden">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-sky-500 text-white shrink-0 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-sky-900 dark:text-sky-100 flex items-center gap-2">
                                <span>เอกสารนี้ได้รับการแชร์</span>
                                @if(!empty($activeShare->note))
                                    <span class="text-xs bg-sky-200/70 dark:bg-sky-900 px-2 py-0.5 rounded text-sky-800 dark:text-sky-300 font-normal">หมายเหตุ: {{ $activeShare->note }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-sky-700 dark:text-sky-300 mt-0.5">
                                แชร์โดย: <strong class="font-semibold">{{ $sharedByName }}</strong> เมื่อวันที่และเวลา: <strong class="font-semibold">{{ $sharedTime }}</strong>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Paper Container Card -->
                <div class="w-full bg-white px-6 sm:px-10 lg:px-12 py-8 sm:py-10 shadow-xl border border-gray-300 rounded-xl text-[14px] sm:text-[15px] text-black font-sans print:shadow-none print:border-none print:px-0 print:py-0 overflow-hidden">
                
                <!-- Title -->
                <div class="text-center pt-2 sm:pt-4 mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-wide">ใบขออนุมัติกำลังคน</h1>
                </div>

                <!-- Date right aligned -->
                <div class="flex justify-end items-end mb-6">
                    <span class="mr-2 shrink-0 font-medium">วันที่</span>
                    <input type="text" name="date" value="{{ $manpowerRequest->date ? \Carbon\Carbon::parse($manpowerRequest->date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th border-b border-dotted border-gray-800 bg-transparent focus:outline-none w-36 sm:w-48 text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                </div>

                <!-- Line 1: ฝ่าย, แผนก -->
                <div class="flex flex-wrap sm:flex-nowrap items-end gap-4 mb-4">
                    <div class="flex items-end flex-1 min-w-[200px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ฝ่าย</span>
                        <input type="text" name="department" value="{{ $manpowerRequest->department ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0" title="{{ $manpowerRequest->department ?? '' }}">
                    </div>
                    <div class="flex items-end flex-1 min-w-[200px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">แผนก</span>
                        <input type="text" name="section" value="{{ $manpowerRequest->section ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0" title="{{ $manpowerRequest->section ?? '' }}">
                    </div>
                </div>

                <!-- Line 2: ขออนุมัติตำแหน่ง (ชื่อไทย, ชื่ออังกฤษ, จำนวน) -->
                <div class="flex flex-wrap lg:flex-nowrap items-end gap-x-4 gap-y-3 mb-4">
                    <div class="flex items-end flex-1 min-w-[220px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ขออนุมัติตำแหน่ง (ชื่อไทย)</span>
                        <input type="text" name="job_title_th" value="{{ $manpowerRequest->job_title_th ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                    <div class="flex items-end flex-1 min-w-[190px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">(ชื่ออังกฤษ)</span>
                        <input type="text" name="job_title_en" value="{{ $manpowerRequest->job_title_en ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                    <div class="flex items-end shrink-0">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">จำนวน</span>
                        <input type="number" name="headcount" value="{{ $manpowerRequest->headcount ?? '' }}" readonly disabled class="w-16 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        <span class="ml-2 whitespace-nowrap shrink-0">อัตรา</span>
                    </div>
                </div>

                <!-- Line 3: พนักงานทั้งหมด & วันที่ต้องการ -->
                <div class="flex flex-wrap lg:flex-nowrap items-end gap-x-6 gap-y-3 mb-6">
                    <div class="flex items-end shrink-0 min-w-0">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด</span>
                        <input type="number" name="current_headcount" value="{{ $manpowerRequest->current_headcount ?? '' }}" readonly disabled class="w-16 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        <span class="ml-2 whitespace-nowrap shrink-0">อัตรา</span>
                    </div>
                    <div class="flex items-end flex-grow min-w-[260px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ต้องการรับเข้าทำงานภายในวันที่</span>
                        <input type="text" name="expected_start_date" value="{{ $manpowerRequest->expected_start_date ? \Carbon\Carbon::parse($manpowerRequest->expected_start_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-[110px] text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                </div>

                <!-- ระดับ -->
                <div class="flex flex-col sm:flex-row sm:items-start gap-2 mb-6">
                    <span class="mr-4 whitespace-nowrap shrink-0 font-medium pt-1">ระดับ</span>
                    <div class="flex-grow min-w-0 flex flex-wrap items-center gap-x-6 gap-y-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="บริหาร (Lv.9-8)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0" {{ ($manpowerRequest->job_level ?? '') == 'บริหาร (Lv.9-8)' ? 'checked' : '' }} disabled>
                            <span class="text-sm font-medium text-gray-900">บริหาร <span class="text-xs text-gray-500 font-normal">(Lv.9-8)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="ผู้จัดการ (Lv.7)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0" {{ ($manpowerRequest->job_level ?? '') == 'ผู้จัดการ (Lv.7)' ? 'checked' : '' }} disabled>
                            <span class="text-sm font-medium text-gray-900">ผู้จัดการ <span class="text-xs text-gray-500 font-normal">(Lv.7)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="ผจก./หัวหน้าส่วนงาน (Lv.6-5)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0" {{ ($manpowerRequest->job_level ?? '') == 'ผจก./หัวหน้าส่วนงาน (Lv.6-5)' ? 'checked' : '' }} disabled>
                            <span class="text-sm font-medium text-gray-900">ผจก./หัวหน้าส่วนงาน <span class="text-xs text-gray-500 font-normal">(Lv.6-5)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0" {{ ($manpowerRequest->job_level ?? '') == 'วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)' ? 'checked' : '' }} disabled>
                            <span class="text-sm font-medium text-gray-900">วิศวกร/อาวุโสเจ้าหน้าที่ <span class="text-xs text-gray-500 font-normal">(Lv.4-3)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="Sup/ปฏิบัติการ (Lv.2-1)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0" {{ ($manpowerRequest->job_level ?? '') == 'Sup/ปฏิบัติการ (Lv.2-1)' ? 'checked' : '' }} disabled>
                            <span class="text-sm font-medium text-gray-900">Sup/ปฏิบัติการ <span class="text-xs text-gray-500 font-normal">(Lv.2-1)</span></span>
                        </label>
                    </div>
                </div>

                <!-- ลักษณะการว่าจ้าง -->
                <div class="flex flex-col sm:flex-row items-start gap-2 mb-6 mt-6">
                    <span class="mr-4 whitespace-nowrap pt-0.5 shrink-0 font-medium">ลักษณะการว่าจ้าง</span>
                    <div class="flex-grow min-w-0 flex flex-col space-y-3 w-full" x-data="{ hire_type: '{{ $manpowerRequest->hire_type ?? '' }}' }">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="hire_type" value="จ้างเพิ่มเติม" class="mr-3 w-4 h-4 text-black focus:ring-black border-gray-800" {{ ($manpowerRequest->hire_type ?? '') == 'จ้างเพิ่มเติม' ? 'checked' : '' }} disabled>
                            <span>จ้างเพิ่มเติม</span>
                        </label>
                        <div class="flex items-end gap-2 min-w-0 w-full flex-wrap sm:flex-nowrap">
                            <label class="flex items-center cursor-pointer shrink-0">
                                <input type="radio" name="hire_type" value="จ้างทดแทน" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800" {{ ($manpowerRequest->hire_type ?? '') == 'จ้างทดแทน' ? 'checked' : '' }} disabled>
                                <span class="whitespace-nowrap">จ้างทดแทนคนเก่า นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_replacement_name" value="{{ $manpowerRequest->hire_replacement_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed">
                        </div>
                        <div class="flex items-end gap-2 min-w-0 w-full flex-wrap sm:flex-nowrap">
                            <label class="flex items-center cursor-pointer shrink-0">
                                <input type="radio" name="hire_type" value="โอนย้าย" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800" {{ ($manpowerRequest->hire_type ?? '') == 'โอนย้าย' ? 'checked' : '' }} disabled>
                                <span class="whitespace-nowrap">โอนย้าย / ปรับเปลี่ยนตำแหน่ง นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_transfer_name" value="{{ $manpowerRequest->hire_transfer_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed">
                        </div>
                        <div class="flex items-end gap-2 min-w-0 w-full flex-wrap sm:flex-nowrap">
                            <label class="flex items-center cursor-pointer shrink-0 gap-2">
                                <input type="radio" name="hire_type" value="จ้างชั่วคราว" class="mr-0 w-4 h-4 text-black focus:ring-black border-gray-800" {{ ($manpowerRequest->hire_type ?? '') == 'จ้างชั่วคราว' ? 'checked' : '' }} disabled>
                                <span class="whitespace-nowrap">จ้างชั่วคราว</span>
                                <span class="whitespace-nowrap shrink-0">ระยะเวลา จากวันที่</span>
                            </label>
                            <input type="text" name="hire_temp_start" value="{{ $manpowerRequest->hire_temp_start ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_start)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th w-28 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed">
                            <span class="whitespace-nowrap shrink-0">ถึง</span>
                            <input type="text" name="hire_temp_end" value="{{ $manpowerRequest->hire_temp_end ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_end)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-[80px] text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- เอกสารแนบ -->
                <div class="flex flex-col sm:flex-row sm:items-center mb-6 pl-0 sm:pl-8 gap-2">
                    <span class="mr-4 whitespace-nowrap shrink-0 font-medium">เอกสารแนบ</span>
                    <div class="flex-grow min-w-0 flex flex-wrap gap-4 pr-0 sm:pr-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_org_chart" value="1" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800" {{ $manpowerRequest->attachment_org_chart ? 'checked' : '' }} disabled>
                            <span>Organization Chart</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_jd" value="1" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800" {{ $manpowerRequest->attachment_jd ? 'checked' : '' }} disabled>
                            <span>Job Description</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_manpower_plan" value="1" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800" {{ $manpowerRequest->attachment_manpower_plan ? 'checked' : '' }} disabled>
                            <span>แผนอัตรากำลังคน</span>
                        </label>
                    </div>
                </div>

                <!-- 2 Column Table -->
                <div class="border-[1.5px] border-black mb-6 flex flex-col md:flex-row">
                    <!-- Left Column -->
                    <div class="w-full md:w-1/2 border-b-[1.5px] md:border-b-0 md:border-r-[1.5px] border-black">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">คุณสมบัติที่ต้องการ</div>
                        <div class="p-3 space-y-4">
                            <div class="flex items-center flex-wrap gap-2">
                                <span class="w-16 whitespace-nowrap shrink-0">เพศ</span>
                                <label class="flex items-center mr-3 cursor-pointer"><input type="radio" name="req_gender" value="ชาย" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black" {{ ($manpowerRequest->req_gender ?? '') == 'ชาย' ? 'checked' : '' }} disabled> ชาย</label>
                                <label class="flex items-center mr-3 cursor-pointer"><input type="radio" name="req_gender" value="หญิง" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black" {{ ($manpowerRequest->req_gender ?? '') == 'หญิง' ? 'checked' : '' }} disabled> หญิง</label>
                                <label class="flex items-center cursor-pointer"><input type="radio" name="req_gender" value="ชาย/หญิง" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black" {{ ($manpowerRequest->req_gender ?? '') == 'ชาย/หญิง' ? 'checked' : '' }} disabled> ชาย/หญิง</label>
                            </div>
                            <div class="flex flex-wrap sm:flex-nowrap items-end gap-2 w-full">
                                <div class="flex items-end shrink-0">
                                    <span class="mr-2 whitespace-nowrap shrink-0">อายุ :</span>
                                    <input type="text" name="req_age" value="{{ $manpowerRequest->req_age ?? '' }}" readonly disabled class="w-14 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                                </div>
                                <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                                    <span class="mx-1 sm:mx-2 whitespace-nowrap shrink-0">วุฒิการศึกษา :</span>
                                    <input type="text" name="req_education" value="{{ $manpowerRequest->req_education ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                                </div>
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">สาขาวิชา :</span>
                                <input type="text" name="req_major" value="{{ $manpowerRequest->req_major ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">ประสบการณ์ทำงาน :</span>
                                <input type="text" name="req_experience" value="{{ $manpowerRequest->req_experience ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">คุณสมบัติพิเศษ :</span>
                                <input type="text" name="req_special" value="{{ $manpowerRequest->req_special ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">อื่นๆ :</span>
                                <input type="text" name="req_other" value="{{ $manpowerRequest->req_other ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="w-full md:w-1/2">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">หน้าที่รับผิดชอบโดยสังเขป</div>
                        <div class="p-3 space-y-[1.125rem]">
                            @for($i=1; $i<=6; $i++)
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 shrink-0">{{ $i }}</span>
                                <input type="text" name="res_{{ $i }}" value="{{ $manpowerRequest->{'res_'.$i} ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="space-y-6 mb-10 mt-8 min-w-0">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                            <input type="text" name="requester_name" value="{{ $manpowerRequest->requester_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            <span class="ml-2 whitespace-nowrap shrink-0">ผู้ร้องขอ</span>
                        </div>
                        <div class="flex items-end w-full sm:w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="requester_date" value="{{ $manpowerRequest->requester_date ? \Carbon\Carbon::parse($manpowerRequest->requester_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                            <input type="text" name="manager_name" value="{{ $manpowerRequest->manager_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            <span class="ml-2 whitespace-nowrap shrink-0">ผู้จัดการแผนก/ฝ่าย</span>
                        </div>
                        <div class="flex items-end w-full sm:w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="manager_date" value="{{ $manpowerRequest->manager_date ? \Carbon\Carbon::parse($manpowerRequest->manager_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                            <input type="text" name="vp_name" value="{{ $manpowerRequest->vp_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            <span class="ml-2 whitespace-nowrap shrink-0">ประธานสายงาน</span>
                        </div>
                        <div class="flex items-end w-full sm:w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="vp_date" value="{{ $manpowerRequest->vp_date ? \Carbon\Carbon::parse($manpowerRequest->vp_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                    </div>
                </div>

                <!-- Approvals Table -->
                <div class="border-[1.5px] border-black mb-4 flex flex-col md:flex-row">
                    <!-- Left Column: HR -->
                    <div class="w-full md:w-1/2 flex flex-col border-b-[1.5px] md:border-b-0 md:border-r-[1.5px] border-black overflow-hidden">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นฝ่ายบุคคล</div>
                        <div class="p-4 flex flex-col flex-grow min-w-0 relative pb-20 overflow-hidden">
                            <div class="w-full flex items-start">
                                <span class="mr-2 mt-2 whitespace-nowrap shrink-0">ความเห็น :</span>
                                <textarea disabled class="flex-grow min-w-0 bg-transparent border-none resize-none focus:ring-0 px-2 py-2 h-16 font-semibold {{ $manpowerRequest->hr_approved_at ? 'text-green-600' : ($manpowerRequest->status === 'rejected' && !$manpowerRequest->ceo_approved_at && !$manpowerRequest->hr_approved_at && $manpowerRequest->rejection_reason ? 'text-red-600' : '') }}">{{ $manpowerRequest->hr_approved_at ? 'อนุมัติการตรวจสอบ/เห็นชอบ' : ($manpowerRequest->status === 'rejected' && !$manpowerRequest->ceo_approved_at && !$manpowerRequest->hr_approved_at && $manpowerRequest->rejection_reason ? 'ไม่อนุมัติ : ' . $manpowerRequest->rejection_reason : '') }}</textarea>
                            </div>
                            <div class="w-full px-4 absolute bottom-4 left-0 overflow-hidden">
                                <div class="text-center w-full mb-1 overflow-hidden truncate">
                                    @if($manpowerRequest->hr_approved_at)
                                        <span class="text-green-600 font-semibold block">{{ $manpowerRequest->hrApprover ? $manpowerRequest->hrApprover->firstname . ' ' . $manpowerRequest->hrApprover->lastname : '' }}</span>
                                        <span class="text-xs text-gray-500">( {{ \Carbon\Carbon::parse($manpowerRequest->hr_approved_at)->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} )</span>
                                    @else
                                        (...................................................)
                                    @endif
                                </div>
                                <div class="text-center w-full">ผจก.แผนก/ฝ่ายทรัพยากรบุคคล</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: CEO -->
                    <div class="w-full md:w-1/2 flex flex-col overflow-hidden">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นประธานเจ้าหน้าที่บริหาร</div>
                        <div class="p-4 flex flex-col flex-grow min-w-0 relative pb-20 overflow-hidden">
                            <div class="flex justify-center space-x-4 sm:space-x-12 w-full mt-4 flex-wrap gap-2">
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
                                        (...................................................)
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
                <div class="mb-10 font-bold w-full">
                    <div class="mb-4">สำหรับเจ้าหน้าที่ฝ่ายบุคคลบันทึก :</div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-2 w-full">
                        <div class="flex items-end w-full sm:w-auto shrink-0">
                            <span class="mr-2 whitespace-nowrap font-normal shrink-0">ได้รับพนักงาน (รหัสพนักงาน)</span>
                            <input type="text" value="{{ $manpowerRequest->onboard_employee_code ?? '' }}" disabled class="w-16 sm:w-20 font-semibold text-center shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-black disabled:text-black opacity-100">
                        </div>
                        <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                            <span class="mx-0 sm:mx-2 whitespace-nowrap font-normal shrink-0">(ชื่อ-สกุล)</span>
                            <input type="text" value="{{ $manpowerRequest->onboard_employee_name ?? '' }}" disabled class="flex-grow min-w-[100px] font-semibold border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-black disabled:text-black opacity-100">
                        </div>
                        <div class="flex items-end shrink-0">
                            <span class="mx-0 sm:mx-2 whitespace-nowrap font-normal shrink-0">เข้าทำงานในวันที่</span>
                            <input type="text" value="{{ $manpowerRequest->onboard_date ? \Carbon\Carbon::parse($manpowerRequest->onboard_date)->addYears(543)->format('d/m/Y') : ($manpowerRequest->onboard_date ?? '') }}" disabled class="w-32 sm:w-36 text-center font-semibold shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-black disabled:text-black opacity-100">
                        </div>
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
                                <p class="text-sm text-gray-500">ตรวจสอบความถูกต้อง / รับทราบ</p>
                                @if($manpowerRequest->hr_approved_at)
                                    <p class="text-xs text-green-600">รับทราบเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->hr_approved_at)->format('d/m/Y H:i') }}</p>
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

                            if ($u->role === 'admin' || $u->canAccessBackend()) {
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
                                if ($u->hr_status == '0' || $u->dept_id == 15 || $u->canAccessBackend()) {
                                    $canApprove = true;
                                }
                            } elseif ($manpowerRequest->status === 'pending_ceo') {
                                if ($u->level_user == '9' || (method_exists($u, 'isCeo') && $u->isCeo())) {
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
                                    <button type="button" onclick="confirmApprove({{ $manpowerRequest->id }}, 'รับทราบ')" class="w-full inline-flex items-center justify-center px-2 py-2.5 bg-emerald-600 border border-transparent rounded-lg font-bold text-[11px] sm:text-xs text-white uppercase tracking-wider hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition duration-150 shadow-md gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                        <span>รับทราบ</span>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.manpower-requests.approve', $manpowerRequest) }}" method="POST" class="w-full" id="approve-form-{{ $manpowerRequest->id }}">
                                    @csrf
                                    <button type="button" onclick="confirmApprove({{ $manpowerRequest->id }}, 'อนุมัติ')" class="w-full inline-flex items-center justify-center px-2 py-2.5 bg-emerald-600 border border-transparent rounded-lg font-bold text-[11px] sm:text-xs text-white uppercase tracking-wider hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition duration-150 shadow-md gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                        <span>อนุมัติ</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    @if($manpowerRequest->status === 'approved')
                        @php
                            $existingJobPost = $manpowerRequest->job_post;
                        @endphp
                        @if($existingJobPost)
                            {{-- กรณีสร้าง Job Post แล้ว --}}
                            <div class="mt-6 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl p-5 text-center shadow-sm">
                                <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <h4 class="text-base font-bold text-emerald-900 dark:text-emerald-100 mb-1">สร้าง Job Post แล้ว</h4>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mb-3 leading-relaxed">
                                    ประกาศ: <strong class="underline">{{ $existingJobPost->title }}</strong>
                                    <br>
                                    สถานะ: <span class="font-bold uppercase">{{ $existingJobPost->publish_status }}</span>
                                </p>
                                <a href="{{ route('backend.recruitment.posts.edit', $existingJobPost->id) }}"
                                   class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/25 transition-all active:scale-95">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    <span>ดู / แก้ไขประกาศรับสมัคร</span>
                                </a>
                            </div>
                        @else
                            {{-- กรณีอนุมัติแล้ว แต่ยังไม่ได้สร้าง Job Post (แจ้งเตือน HA) --}}
                            <div class="mt-6 bg-amber-50/90 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800/70 rounded-2xl p-5 text-center shadow-sm relative overflow-hidden">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-white uppercase tracking-wider mb-2 animate-pulse">
                                    <i class="fa-solid fa-bell"></i> แจ้งเตือน HA
                                </div>
                                <h4 class="text-base font-bold text-amber-900 dark:text-amber-100 mb-1.5">ยังไม่เปิดประกาศรับสมัคร</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-300 mb-4 leading-relaxed">
                                    ใบขออนุมัติกำลังคนนี้ผ่านการอนุมัติครบแล้ว <strong>กรุณาทำการสร้าง Job Post ประกาศรับสมัครพนักงาน</strong> ตามความต้องการ
                                </p>
                                @if(auth()->check() && auth()->user()->canAccessBackend())
                                    <a href="{{ route('backend.recruitment.posts.create', ['manpower_request_id' => $manpowerRequest->id]) }}"
                                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-red-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-bold text-xs shadow-md shadow-red-500/25 transition-all active:scale-95">
                                        <i class="fa-solid fa-bullhorn text-xs"></i>
                                        <span>สร้าง Job Post ตอนนี้</span>
                                    </a>
                                @else
                                    <button type="button" onclick="Swal.fire({icon: 'warning', title: 'ไม่มีสิทธิ์เข้าถึง', text: 'คุณไม่มีสิทธิ์เข้าสู่ระบบหลังบ้านเพื่อสร้าง Job Post (เฉพาะสิทธิ์ ADMIN และ EDITOR เท่านั้น กรุณาแจ้งฝ่าย HR หรือผู้ดูแลระบบ เพื่อดำเนินการสร้างประกาศ)', confirmButtonColor: '#f59e0b', confirmButtonText: 'รับทราบ'})"
                                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-gray-500 hover:bg-gray-600 text-white font-bold text-xs shadow-md transition-all active:scale-95 cursor-pointer">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                        <span>สร้าง Job Post ตอนนี้</span>
                                    </button>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2">
                                        *เฉพาะผู้มีสิทธิ์ ADMIN หรือ EDITOR เท่านั้นที่สามารถสร้างประกาศรับสมัครงานได้
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flatpickr for Date Format -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        function confirmApprove(id, actionType = 'อนุมัติ') {
            const isAck = (actionType === 'รับทราบ');
            const title = isAck ? 'ยืนยันการรับทราบคำขอ?' : 'ยืนยันการอนุมัติคำขอ?';
            const text = isAck ? 'คุณต้องการรับทราบคำขอนี้และส่งต่อให้ CEO พิจารณาใช่หรือไม่' : 'คุณต้องการอนุมัติคำขออัตรากำลังนี้ใช่หรือไม่';
            const btnText = isAck ? 'ใช่, รับทราบ' : 'ใช่, อนุมัติ';

            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: btnText,
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + id).submit();
                }
            });
        }

        function confirmReject(id) {
            Swal.fire({
                title: 'ปฏิเสธคำขออนุมัติ',
                text: 'กรุณาระบุเหตุผลที่ไม่อนุมัติ',
                input: 'textarea',
                inputPlaceholder: 'ระบุเหตุผลที่ปฏิเสธ...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ยืนยันปฏิเสธ',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณาระบุเหตุผลที่ไม่อนุมัติ!'
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

    @include('components.form-share-modal')
</x-app-layout>