<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ใบขออนุมัติกำลังคน') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen relative overflow-x-hidden">
        
        <!-- Action Toolbar (Top Panel) - Hide during print -->
        <div class="max-w-[1000px] mx-auto mb-6 px-4 sm:px-0 flex flex-wrap justify-between items-center gap-3 print:hidden">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.manpower-requests.index') }}" onclick="if(document.referrer){ history.back(); return false; }" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    ย้อนกลับ
                </a>
            </div>
        </div>



        <!-- A4 Paper Container Wrapper -->
        <div class="w-full overflow-x-auto pb-4">
            <div class="min-w-[1000px] max-w-[1000px] mx-auto bg-white p-12 shadow-xl border border-gray-300 mb-8 print:shadow-none print:border-none print:p-0 print:mb-0 text-black font-sans leading-relaxed">
            
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
                    <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">{{ $manpowerRequest->department }}</span>
                    <span class="whitespace-nowrap mx-2">แผนก</span>
                    <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">{{ $manpowerRequest->section }}</span>
                </div>

                <!-- Line 2: Job Titles & Headcount -->
                <div class="flex items-end w-full flex-wrap gap-y-2">
                    <span class="whitespace-nowrap mr-1">ขออนุมัติตำแหน่ง (ชื่อไทย)</span>
                    <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-w-[150px]">{{ $manpowerRequest->job_title_th }}</span>
                    <span class="whitespace-nowrap mx-2">(ชื่ออังกฤษ)</span>
                    <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-w-[150px]">{{ $manpowerRequest->job_title_en ?? '-' }}</span>
                    <span class="whitespace-nowrap mx-2">จำนวน</span>
                    <span class="border-b border-dotted border-black w-16 text-center px-1 font-bold">{{ $manpowerRequest->headcount }}</span>
                    <span class="whitespace-nowrap ml-1">อัตรา</span>
                </div>

                <!-- Line 3: Current Headcount & Target Date -->
                <div class="flex items-end w-full">
                    <span class="whitespace-nowrap mr-1">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด</span>
                    <span class="border-b border-dotted border-black w-24 text-center px-1 font-bold">{{ $manpowerRequest->current_headcount }}</span>
                    <span class="whitespace-nowrap mx-2">อัตรา</span>
                    <span class="whitespace-nowrap">ต้องการรับเข้าทำงานภายในวันที่</span>
                    <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 text-center">
                        {{ \Carbon\Carbon::parse($manpowerRequest->expected_start_date)->format('d/m/Y') }}
                    </span>
                </div>

                <!-- Line 4: Job Levels -->
                <div class="flex items-center w-full pt-2">
                    <span class="whitespace-nowrap mr-4 font-semibold">ระดับ</span>
                    <div class="flex flex-row justify-between flex-grow min-w-0 items-center gap-x-1 sm:gap-x-4">
                        @php
                            $lvl = $manpowerRequest->job_level ?? '';
                            $isLv98 = str_contains($lvl, 'Lv.9') || str_contains($lvl, 'บริหาร');
                            $isLv7 = str_contains($lvl, 'Lv.7') || str_contains($lvl, 'ผู้จัดการ');
                            $isLv65 = str_contains($lvl, 'Lv.6') || str_contains($lvl, 'หัวหน้าส่วนงาน');
                            $isLv43 = str_contains($lvl, 'Lv.4') || str_contains($lvl, 'วิศวกร') || str_contains($lvl, 'เจ้าหน้าที่');
                            $isLv21 = str_contains($lvl, 'Lv.2') || str_contains($lvl, 'Sup') || str_contains($lvl, 'ปฏิบัติการ');
                        @endphp
                        <label class="inline-flex items-center whitespace-nowrap">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isLv98 ? 'checked' : '' }}>
                            <span class="ml-1 text-xs sm:text-sm">บริหาร <span class="text-[10px] sm:text-xs text-gray-500">(Lv.9 - 8)</span></span>
                        </label>
                        <label class="inline-flex items-center whitespace-nowrap">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isLv7 ? 'checked' : '' }}>
                            <span class="ml-1 text-xs sm:text-sm">ผู้จัดการ <span class="text-[10px] sm:text-xs text-gray-500">(Lv.7)</span></span>
                        </label>
                        <label class="inline-flex items-center whitespace-nowrap">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isLv65 ? 'checked' : '' }}>
                            <span class="ml-1 text-xs sm:text-sm">ผช.ผจก./หัวหน้าส่วนงาน <span class="text-[10px] sm:text-xs text-gray-500">(Lv.6 - 5)</span></span>
                        </label>
                        <label class="inline-flex items-center whitespace-nowrap">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isLv43 ? 'checked' : '' }}>
                            <span class="ml-1 text-xs sm:text-sm">วิศวกร/อาวุโส/เจ้าหน้าที่ <span class="text-[10px] sm:text-xs text-gray-500">(Lv.4 - 3)</span></span>
                        </label>
                        <label class="inline-flex items-center whitespace-nowrap">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isLv21 ? 'checked' : '' }}>
                            <span class="ml-1 text-xs sm:text-sm">Sup/ปฏิบัติการ <span class="text-[10px] sm:text-xs text-gray-500">(Lv.2 - 1)</span></span>
                        </label>
                    </div>
                </div>

                <!-- Line 5: Hire Type Detail Layout -->
                <div class="pt-2 flex items-start w-full">
                    <span class="whitespace-nowrap mr-4 font-semibold">ลักษณะการว่าจ้าง</span>
                    <div class="flex-grow min-w-0 space-y-2">
                        @php
                            $ht = $manpowerRequest->hire_type ?? '';
                            $isNew = !in_array($ht, ['จ้างทดแทน', 'ทดแทน', 'โอนย้าย', 'จ้างชั่วคราว', 'ชั่วคราว']);
                            $isRepl = in_array($ht, ['จ้างทดแทน', 'ทดแทน']);
                            $isTrans = $ht == 'โอนย้าย';
                            $isTemp = in_array($ht, ['จ้างชั่วคราว', 'ชั่วคราว']);
                        @endphp
                        
                        <!-- Row 1: จ้างเพิ่มเติม -->
                        <div class="flex items-center">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isNew ? 'checked' : '' }}>
                            <span class="ml-2">จ้างเพิ่มเติม</span>
                        </div>

                        <!-- Row 2: จ้างทดแทน -->
                        <div class="flex items-end w-full">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400 mr-2" {{ $isRepl ? 'checked' : '' }}>
                            <span class="whitespace-nowrap">จ้างทดแทนคนเก่า คือ นาย / นาง / นางสาว</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 ml-1">
                                {{ $isRepl ? $manpowerRequest->hire_replacement_name : '' }}
                            </span>
                        </div>

                        <!-- Row 3: โอนย้าย -->
                        <div class="flex items-end w-full">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400 mr-2" {{ $isTrans ? 'checked' : '' }}>
                            <span class="whitespace-nowrap">โอนย้าย / ปรับเปลี่ยนตำแหน่ง คือ นาย / นาง / นางสาว</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 ml-1">
                                {{ $isTrans ? $manpowerRequest->hire_transfer_name : '' }}
                            </span>
                        </div>

                        <!-- Row 4: จ้างชั่วคราว -->
                        <div class="flex items-end w-full flex-wrap gap-y-1">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400 mr-2" {{ $isTemp ? 'checked' : '' }}>
                            <span class="whitespace-nowrap">จ้างชั่วคราว</span>
                            <span class="whitespace-nowrap mx-4">ระยะเวลา จากวันที่</span>
                            <span class="border-b border-dotted border-black w-28 text-center px-1">
                                {{ $isTemp && $manpowerRequest->hire_temp_start ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_start)->format('d/m/Y') : '' }}
                            </span>
                            <span class="whitespace-nowrap mx-4">ถึง</span>
                            <span class="border-b border-dotted border-black w-28 text-center px-1">
                                {{ $isTemp && $manpowerRequest->hire_temp_end ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_end)->format('d/m/Y') : '' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Line 6: Attachments -->
                <div class="pt-2 flex items-center w-full flex-wrap">
                    <span class="whitespace-nowrap mr-6 font-semibold">เอกสารแนบ</span>
                    <div class="flex flex-wrap gap-8">
                        <label class="inline-flex items-center">
                            <input disabled type="checkbox" class="form-checkbox h-4 w-4 text-black border-gray-400 rounded" {{ $manpowerRequest->attachment_org_chart ? 'checked' : '' }}>
                            <span class="ml-2">Organization Chart</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input disabled type="checkbox" class="form-checkbox h-4 w-4 text-black border-gray-400 rounded" {{ $manpowerRequest->attachment_jd ? 'checked' : '' }}>
                            <span class="ml-2">Job Description</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input disabled type="checkbox" class="form-checkbox h-4 w-4 text-black border-gray-400 rounded" {{ $manpowerRequest->attachment_manpower_plan ? 'checked' : '' }}>
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
                            $g = $manpowerRequest->req_gender ?? '';
                            $isMale = $g == 'ชาย';
                            $isFemale = $g == 'หญิง';
                            $isBoth = in_array($g, ['ชาย/หญิง', 'ชาย / หญิง', 'ไม่จำกัดเพศ', 'ชาย หรือ หญิง']);
                        @endphp
                        <label class="inline-flex items-center">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isMale ? 'checked' : '' }}>
                            <span class="ml-1">ชาย</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $isFemale ? 'checked' : '' }}>
                            <span class="ml-1">หญิง</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ ($isBoth || (!$isMale && !$isFemale)) ? 'checked' : '' }}>
                            <span class="ml-1">ชาย/หญิง</span>
                        </label>
                    </div>

                    <!-- Age & Education -->
                    <div class="flex items-end w-full">
                        <span class="whitespace-nowrap mr-1">อายุ :</span>
                        <span class="w-16 border-b border-dotted border-black px-1 text-center font-semibold">{{ $manpowerRequest->req_age ?? '-' }}</span>
                        <span class="whitespace-nowrap mx-2">วุฒิการศึกษา :</span>
                        <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">{{ $manpowerRequest->req_education ?? '-' }}</span>
                    </div>

                    <!-- Major -->
                    <div class="flex items-end w-full">
                        <span class="whitespace-nowrap mr-1">สาขาวิชา :</span>
                        <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">{{ $manpowerRequest->req_major ?? '-' }}</span>
                    </div>

                    <!-- Experience -->
                    <div class="flex items-end w-full">
                        <span class="whitespace-nowrap mr-1">ประสบการณ์ทำงาน :</span>
                        <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">{{ $manpowerRequest->req_experience ?? '-' }}</span>
                    </div>

                    <!-- Special Skill -->
                    <div class="flex items-end w-full">
                        <span class="whitespace-nowrap mr-1">คุณสมบัติพิเศษ :</span>
                        <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">{{ $manpowerRequest->req_special ?? '-' }}</span>
                    </div>

                    <!-- Other -->
                    <div class="flex items-end w-full">
                        <span class="whitespace-nowrap mr-1">อื่นๆ :</span>
                        <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">{{ $manpowerRequest->req_other ?? '-' }}</span>
                    </div>
                </div>

                <!-- Right: หน้าที่รับผิดชอบโดยสังเขป -->
                <div class="p-4 space-y-4">
                    <div class="text-center font-bold border-b border-black pb-1 mb-2 bg-gray-50">หน้าที่รับผิดชอบโดยสังเขป</div>
                    
                    <div class="space-y-3">
                        <div class="flex items-end w-full">
                            <span class="mr-2">1</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-h-[20px]">{{ $manpowerRequest->res_1 }}</span>
                        </div>
                        <div class="flex items-end w-full">
                            <span class="mr-2">2</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-h-[20px]">{{ $manpowerRequest->res_2 }}</span>
                        </div>
                        <div class="flex items-end w-full">
                            <span class="mr-2">3</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-h-[20px]">{{ $manpowerRequest->res_3 }}</span>
                        </div>
                        <div class="flex items-end w-full">
                            <span class="mr-2">4</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-h-[20px]">{{ $manpowerRequest->res_4 }}</span>
                        </div>
                        <div class="flex items-end w-full">
                            <span class="mr-2">5</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-h-[20px]">{{ $manpowerRequest->res_5 }}</span>
                        </div>
                        <div class="flex items-end w-full">
                            <span class="mr-2">6</span>
                            <span class="flex-grow min-w-0 border-b border-dotted border-black px-2 min-h-[20px]">{{ $manpowerRequest->res_6 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="space-y-6 text-sm mb-6 pl-4">
                <!-- Requester -->
                <div class="flex items-end w-full flex-wrap gap-y-2">
                    <span class="whitespace-nowrap mr-2">ลงชื่อ</span>
                    <span class="border-b border-dotted border-black min-w-[300px] flex-grow min-w-0 text-center text-green-600 font-semibold">
                        {{ $manpowerRequest->requester_name ?? '-' }}
                    </span>
                    <span class="whitespace-nowrap mx-4">ผู้ร้องขอ</span>
                    <span class="whitespace-nowrap">วันที่</span>
                    <span class="border-b border-dotted border-black w-48 text-center">
                        {{ \Carbon\Carbon::parse($manpowerRequest->created_at)->format('d/m/Y') }}
                    </span>
                </div>

                <!-- Manager -->
                <div class="flex items-end w-full flex-wrap gap-y-2">
                    <span class="whitespace-nowrap mr-2">ลงชื่อ</span>
                    <span class="border-b border-dotted border-black min-w-[300px] flex-grow min-w-0 text-center">
                        @if($manpowerRequest->manager_approved_at)
                            <span class="text-green-600 font-semibold">{{ $manpowerRequest->managerApprover ? $manpowerRequest->managerApprover->firstname . ' ' . $manpowerRequest->managerApprover->lastname : 'ผู้จัดการแผนก/ฝ่าย' }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                    <span class="whitespace-nowrap mx-4">ผู้จัดการแผนก/ฝ่าย</span>
                    <span class="whitespace-nowrap">วันที่</span>
                    <span class="border-b border-dotted border-black w-48 text-center">
                        {{ $manpowerRequest->manager_approved_at ? \Carbon\Carbon::parse($manpowerRequest->manager_approved_at)->timezone('Asia/Bangkok')->format('d/m/Y') : '' }}
                    </span>
                </div>

                <!-- VP -->
                <div class="flex items-end w-full flex-wrap gap-y-2">
                    <span class="whitespace-nowrap mr-2">ลงชื่อ</span>
                    <span class="border-b border-dotted border-black min-w-[300px] flex-grow min-w-0 text-center">
                        @if($manpowerRequest->vp_approved_at)
                            <span class="text-green-600 font-semibold">{{ $manpowerRequest->vpApprover ? $manpowerRequest->vpApprover->firstname . ' ' . $manpowerRequest->vpApprover->lastname : 'ประธานสายงาน (C Level)' }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                    <span class="whitespace-nowrap mx-4">ประธานสายงาน</span>
                    <span class="whitespace-nowrap">วันที่</span>
                    <span class="border-b border-dotted border-black w-48 text-center">
                        {{ $manpowerRequest->vp_approved_at ? \Carbon\Carbon::parse($manpowerRequest->vp_approved_at)->timezone('Asia/Bangkok')->format('d/m/Y') : '' }}
                    </span>
                </div>
            </div>

            <!-- Two-Column Block: HR and CEO opinions -->
            <div class="grid grid-cols-1 md:grid-cols-2 border border-black text-sm mb-6">
                <!-- Left: ความคิดเห็นฝ่ายบุคคล -->
                <div class="border-r border-black p-4 flex flex-col justify-between min-h-[160px]">
                    <div class="text-center font-bold border-b border-black pb-1 mb-2 bg-gray-50">ความคิดเห็นฝ่ายบุคคล</div>
                    
                    <div class="flex-grow min-w-0 py-3">
                        <span class="text-gray-600">ความเห็น :</span>
                        @if($manpowerRequest->hr_approved_at)
                            <span class="ml-2 text-green-600 font-semibold">อนุมัติการตรวจสอบ/เห็นชอบ</span>
                        @elseif($manpowerRequest->status === 'rejected' && !$manpowerRequest->ceo_approved_at && !$manpowerRequest->hr_approved_at && $manpowerRequest->rejection_reason)
                            <span class="ml-2 text-red-600 font-semibold">ไม่อนุมัติ : {{ $manpowerRequest->rejection_reason }}</span>
                        @else
                            <span class="ml-2 text-gray-400">-</span>
                        @endif
                    </div>

                    <div class="flex flex-col items-center">
                        <span class="border-b border-dotted border-black w-full max-w-[250px] text-center text-xs py-1 min-h-[20px]">
                            @if($manpowerRequest->hr_approved_at)
                                <span class="text-green-600 font-semibold block mb-1">{{ $manpowerRequest->hrApprover ? $manpowerRequest->hrApprover->firstname . ' ' . $manpowerRequest->hrApprover->lastname : '' }}</span>
                                ( {{ \Carbon\Carbon::parse($manpowerRequest->hr_approved_at)->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} )
                            @endif
                        </span>
                        <span class="text-xs text-gray-600 mt-1">ผจก.แผนก/ฝ่ายทรัพยากรบุคคล</span>
                    </div>
                </div>

                <!-- Right: ความเห็นประธานเจ้าหน้าที่บริหาร -->
                <div class="p-4 flex flex-col justify-between min-h-[160px]">
                    <div class="text-center font-bold border-b border-black pb-1 mb-2 bg-gray-50">ความเห็นประธานเจ้าหน้าที่บริหาร</div>
                    
                    <div class="flex justify-around py-3">
                        <label class="inline-flex items-center">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ $manpowerRequest->ceo_approved_at ? 'checked' : '' }}>
                            <span class="ml-2">อนุมัติตามคำขอ</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input disabled type="radio" class="form-radio h-4 w-4 text-black border-gray-400" {{ ($manpowerRequest->status == 'rejected' && $manpowerRequest->rejection_reason) ? 'checked' : '' }}>
                            <span class="ml-2">ไม่อนุมัติตามคำขอ</span>
                        </label>
                    </div>

                    <div class="flex flex-col items-center">
                        <span class="border-b border-dotted border-black w-full max-w-[250px] text-center text-xs py-1 min-h-[20px]">
                            @if($manpowerRequest->ceo_approved_at)
                                <span class="text-green-600 font-semibold block mb-1">{{ $manpowerRequest->ceoApprover ? $manpowerRequest->ceoApprover->firstname . ' ' . $manpowerRequest->ceoApprover->lastname : '' }}</span>
                                ( {{ \Carbon\Carbon::parse($manpowerRequest->ceo_approved_at)->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} )
                            @elseif($manpowerRequest->status == 'rejected' && $manpowerRequest->rejection_reason)
                                ( ปฏิเสธ: {{ $manpowerRequest->rejection_reason }} )
                            @endif
                        </span>
                        <span class="text-xs text-gray-600 mt-1">ประธานเจ้าหน้าที่บริหาร</span>
                    </div>
                </div>
            </div>

            <!-- Red note -->
            <div class="text-red-600 font-bold text-xs mb-6">
                หมายเหตุ : ใบ Request กำลังคนต้องขออนุมัติล่วงหน้า 30 วัน และกำหนดคุณสมบัติให้ละเอียด
            </div>

            <!-- HR Note section at the bottom -->
            <div class="border-t border-black pt-4 text-sm">
                <p class="font-bold mb-2">สำหรับเจ้าหน้าที่ฝ่ายบุคคลบันทึก :</p>
                <div class="flex items-end w-full flex-wrap gap-y-2">
                    <span class="whitespace-nowrap mr-1">ได้รับพนักงาน (รหัสพนักงาน)</span>
                    <span class="border-b border-dotted border-black w-24 text-center px-1 font-semibold">
                        {{ $manpowerRequest->onboard_employee_code ?? '-' }}
                    </span>
                    <span class="whitespace-nowrap mx-2">(ชื่อ-สกุล)</span>
                    <span class="flex-grow min-w-0 border-b border-dotted border-black px-2">
                        {{ $manpowerRequest->onboard_employee_name ?? '-' }}
                    </span>
                    <span class="whitespace-nowrap mx-2">เข้าทำงานในวันที่</span>
                    <span class="border-b border-dotted border-black w-48 text-center">
                        {{ $manpowerRequest->onboard_date ? \Carbon\Carbon::parse($manpowerRequest->onboard_date)->format('d/m/Y') : '-' }}
                    </span>
                </div>
            </div>

            <!-- Footer doc code -->
            <div class="flex justify-end mt-8 text-xs text-gray-500 font-mono">
                <span>QF-HR-13 Rev.08 : 01-06-25</span>
            </div>
        <!-- End of Wrapper -->
        </div>
        </div>

        <!-- Float Action Buttons for Approval (Admin Role only) - Hide during print -->
        @php
            $statusIndex = array_search($manpowerRequest->status, [
                'pending_manager', 'pending_vp', 'pending_hr', 'pending_ceo', 'approved'
            ]);
            if ($statusIndex === false) $statusIndex = -1; // rejected or unknown
        @endphp
        
        @if($statusIndex >= 0 && $statusIndex < 4 && $manpowerRequest->status !== 'rejected')
            @php
                $canApprove = false;
                if (auth()->user()->hasRole('admin')) {
                    $canApprove = true;
                } elseif ($manpowerRequest->status === 'pending_hr' && auth()->user()->hasRole('hr_manager')) {
                    $canApprove = true;
                } elseif ($manpowerRequest->status === 'pending_ceo' && auth()->user()->hasRole('ceo')) {
                    $canApprove = true;
                }
            @endphp
            @if($canApprove)
            <div class="max-w-[842px] mx-auto mt-6 px-4 sm:px-0 flex justify-end gap-3 print:hidden">
                <form action="{{ route('admin.manpower-requests.reject', $manpowerRequest) }}" method="POST" class="inline" id="reject-form-{{ $manpowerRequest->id }}">
                    @csrf
                    <input type="hidden" name="reason" value="ไม่อนุมัติในขั้นตอนนี้">
                    <button type="button" onclick="confirmReject({{ $manpowerRequest->id }})" class="inline-flex items-center px-5 py-2.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        ไม่อนุมัติ (Reject)
                    </button>
                </form>
                
                @if($manpowerRequest->status === 'pending_hr')
                    <form action="{{ route('admin.manpower-requests.approve', $manpowerRequest) }}" method="POST" class="inline" id="approve-form-{{ $manpowerRequest->id }}">
                        @csrf
                        <button type="button" onclick="confirmApprove({{ $manpowerRequest->id }}, 'รับทราบ')" class="inline-flex items-center px-5 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            รับทราบ (Acknowledge)
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.manpower-requests.approve', $manpowerRequest) }}" method="POST" class="inline" id="approve-form-{{ $manpowerRequest->id }}">
                        @csrf
                        <button type="button" onclick="confirmApprove({{ $manpowerRequest->id }}, 'อนุมัติ')" class="inline-flex items-center px-5 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            พิจารณาอนุมัติ (Approve)
                        </button>
                    </form>
                @endif
            </div>
            @endif
        @endif

    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmApprove(id, actionType = 'อนุมัติ') {
            const isAck = (actionType === 'รับทราบ');
            Swal.fire({
                title: isAck ? 'ยืนยันการรับทราบคำขอ?' : 'ยืนยันการพิจารณาอนุมัติ?',
                text: isAck ? 'คุณต้องการรับทราบคำขอนี้และส่งต่อให้ CEO ใช่หรือไม่?' : 'คุณต้องการพิจารณาอนุมัติคำขออัตรากำลังนี้ใช่หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isAck ? 'ใช่, รับทราบ' : 'ใช่, อนุมัติ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + id).submit();
                }
            });
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
            })
        }
    </script>

</x-admin-layout>
