@extends('layouts.recruitment.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .paper-font, .paper-font * {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
    </style>

    <div class="max-w-[1400px] mx-auto space-y-6 pb-16">
        <!-- Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('backend.recruitment.requests.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>ย้อนกลับ</span>
                </a>
                <h2 class="text-xl sm:text-2xl font-bold dark:text-white text-gray-800">
                    รายละเอียดคำขอ #{{ $recruitmentRequest->request_no }}
                </h2>
            </div>
            
            <div class="flex items-center gap-2 flex-wrap">
                @if($manpowerRequest)
                    <a href="{{ route('manpower-request.pdf', $manpowerRequest->id) }}" target="_blank"
                        class="inline-flex items-center px-3.5 py-2 bg-gray-900 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-wider hover:bg-black transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                        </svg>
                        พิมพ์ (Print)
                    </a>
                    <a href="{{ route('manpower-request.show', $manpowerRequest->id) }}" target="_blank"
                        class="px-3.5 py-2 rounded-xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-950/40 text-xs font-semibold text-sky-700 dark:text-sky-300 hover:bg-sky-100 dark:hover:bg-sky-900/60 transition shadow-sm inline-flex items-center gap-1.5"
                        title="เปิดดูเอกสารต้นฉบับในแท็บใหม่">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>เปิดใบขออนุมัติ #{{ $manpowerRequest->id }}</span>
                    </a>
                @endif
                <span class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm inline-flex items-center gap-1.5
                    @if($recruitmentRequest->status == 'approved') bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800
                    @elseif($recruitmentRequest->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-300 dark:border-red-800
                    @else bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-800 @endif">
                    <span class="w-2 h-2 rounded-full @if($recruitmentRequest->status == 'approved') bg-emerald-500 @elseif($recruitmentRequest->status == 'rejected') bg-red-500 @else bg-amber-500 @endif"></span>
                    สถานะ: {{ ucfirst(str_replace('_', ' ', $recruitmentRequest->status)) }}
                </span>
            </div>
        </div>

        <!-- 2 Column Layout (Left: Paper Form Document, Right: Approval Timeline & Job Post) -->
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            
            <!-- Left: Paper Document Form -->
            <div class="flex-1 w-full min-w-0">
                @php
                    // Helper variables for template display
                    $docDate = $manpowerRequest && $manpowerRequest->date 
                        ? \Carbon\Carbon::parse($manpowerRequest->date)->addYears(543)->format('d/m/Y')
                        : ($recruitmentRequest->created_at ? $recruitmentRequest->created_at->addYears(543)->format('d/m/Y') : '-');

                    $departmentName = $manpowerRequest->department ?? ($recruitmentRequest->department?->department_fullname ?? $recruitmentRequest->department?->department_name ?? '-');
                    $sectionName = $manpowerRequest->section ?? ($recruitmentRequest->department?->department_name ?? '-');
                    $jobTitleTh = $manpowerRequest->job_title_th ?? ($recruitmentRequest->position_name ?: ($recruitmentRequest->jobPosition?->position_name ?? '-'));
                    $jobTitleEn = $manpowerRequest->job_title_en ?? ($recruitmentRequest->jobPosition?->position_name_en ?? '-');
                    $headcount = $manpowerRequest->headcount ?? $recruitmentRequest->headcount;
                    $currentHeadcount = $manpowerRequest->current_headcount ?? '-';
                    $expectedStartDate = $manpowerRequest && $manpowerRequest->expected_start_date
                        ? \Carbon\Carbon::parse($manpowerRequest->expected_start_date)->addYears(543)->format('d/m/Y')
                        : ($recruitmentRequest->required_start_date ? $recruitmentRequest->required_start_date->addYears(543)->format('d/m/Y') : '-');

                    $jobLevel = $manpowerRequest->job_level ?? '';
                    if (!$jobLevel && preg_match('/ระดับ:\s*([^\n]+)/u', $recruitmentRequest->job_description ?? '', $matches)) {
                        $jobLevel = trim($matches[1]);
                    }

                    $hireType = $manpowerRequest->hire_type ?? '';
                    if (!$hireType && preg_match('/ลักษณะการว่าจ้าง:\s*([^\n]+)/u', $recruitmentRequest->reason ?? '', $matches)) {
                        $hireType = trim($matches[1]);
                    }

                    // Qualification breakdown
                    $reqGender = $manpowerRequest->req_gender ?? '';
                    $reqAge = $manpowerRequest->req_age ?? '';
                    $reqEducation = $manpowerRequest->req_education ?? '';
                    $reqMajor = $manpowerRequest->req_major ?? '';
                    $reqExperience = $manpowerRequest->req_experience ?? '';
                    $reqSpecial = $manpowerRequest->req_special ?? '';
                    $reqOther = $manpowerRequest->req_other ?? '';

                    // If manpowerRequest is missing, extract from recruitmentRequest->qualification if formatted
                    if (!$manpowerRequest && $recruitmentRequest->qualification) {
                        if (preg_match('/เพศ:\s*([^,\n]+)/u', $recruitmentRequest->qualification, $m)) $reqGender = trim($m[1]);
                        if (preg_match('/อายุ:\s*([^,\n]+)/u', $recruitmentRequest->qualification, $m)) $reqAge = trim($m[1]);
                        if (preg_match('/วุฒิ(?:การศึกษา)?:\s*([^,\n]+)/u', $recruitmentRequest->qualification, $m)) $reqEducation = trim($m[1]);
                        if (preg_match('/สาขา(?:วิชา)?:\s*([^,\n]+)/u', $recruitmentRequest->qualification, $m)) $reqMajor = trim($m[1]);
                        if (preg_match('/ประสบการณ์(?:ทำงาน)?:\s*([^,\n]+)/u', $recruitmentRequest->qualification, $m)) $reqExperience = trim($m[1]);
                        if (preg_match('/คุณสมบัติพิเศษ:\s*([^,\n]+)/u', $recruitmentRequest->qualification, $m)) $reqSpecial = trim($m[1]);
                        if (preg_match('/อื่นๆ:\s*([^,\n]+)/u', $recruitmentRequest->qualification, $m)) $reqOther = trim($m[1]);
                    }
                @endphp

                <!-- Paper Container Card (Matching รูปที่ 2) -->
                <div class="paper-font w-full bg-white px-6 sm:px-10 lg:px-12 py-8 sm:py-10 shadow-xl border border-gray-300 rounded-2xl text-[14px] sm:text-[15px] text-black overflow-hidden">
                    
                    <!-- Form Header -->
                    <div class="text-center pt-2 sm:pt-4 mb-6 sm:mb-8">
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-wide text-gray-950">ใบขออนุมัติกำลังคน</h1>
                    </div>

                    <!-- Date right aligned -->
                    <div class="flex justify-end items-end mb-6">
                        <span class="mr-2 shrink-0 font-medium">วันที่</span>
                        <input type="text" value="{{ $docDate }}" readonly disabled class="border-b border-dotted border-gray-800 bg-transparent focus:outline-none w-36 sm:w-48 text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900 font-medium">
                    </div>

                    <!-- Line 1: ฝ่าย, แผนก -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="flex items-end w-full min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ฝ่าย</span>
                            <input type="text" value="{{ $departmentName }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900 font-medium">
                        </div>
                        <div class="flex items-end w-full min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0 font-medium">แผนก</span>
                            <input type="text" value="{{ $sectionName }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900 font-medium">
                        </div>
                    </div>

                    <!-- Line 2: ขออนุมัติตำแหน่ง (ชื่อไทย, ชื่ออังกฤษ, จำนวน) -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                        <div class="flex items-end md:col-span-5 w-full min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ขออนุมัติตำแหน่ง (ชื่อไทย)</span>
                            <input type="text" value="{{ $jobTitleTh }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900 font-semibold">
                        </div>
                        <div class="flex items-end md:col-span-4 w-full min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0 font-medium">(ชื่ออังกฤษ)</span>
                            <input type="text" value="{{ $jobTitleEn }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900">
                        </div>
                        <div class="flex items-end md:col-span-3 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0 font-medium">จำนวน</span>
                            <input type="text" value="{{ $headcount }}" readonly disabled class="w-16 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 font-bold text-gray-900">
                            <span class="ml-2 whitespace-nowrap shrink-0">อัตรา</span>
                        </div>
                    </div>

                    <!-- Line 3: พนักงานทั้งหมด & วันที่ต้องการ -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="flex items-end w-full min-w-0 flex-wrap sm:flex-nowrap gap-y-1">
                            <span class="mr-2 whitespace-normal sm:whitespace-nowrap shrink-0 font-medium">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด</span>
                            <div class="flex items-end shrink-0">
                                <input type="text" value="{{ $currentHeadcount }}" readonly disabled class="w-16 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900">
                                <span class="ml-2 whitespace-nowrap shrink-0">อัตรา</span>
                            </div>
                        </div>
                        <div class="flex items-end w-full min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ต้องการรับเข้าทำงานภายในวันที่</span>
                            <input type="text" value="{{ $expectedStartDate }}" readonly disabled class="flex-grow min-w-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900 font-medium">
                        </div>
                    </div>

                    <!-- ระดับ -->
                    <div class="flex flex-col sm:flex-row sm:items-start gap-2 mb-6">
                        <span class="mr-4 whitespace-nowrap shrink-0 font-medium pt-1">ระดับ</span>
                        <div class="flex-grow min-w-0 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                            @php
                                $levels = [
                                    'บริหาร (Lv.9-8)' => ['บริหาร', '(Lv.9-8)'],
                                    'ผู้จัดการ (Lv.7)' => ['ผู้จัดการ', '(Lv.7)'],
                                    'ผจก./หัวหน้าส่วนงาน (Lv.6-5)' => ['ผจก./หัวหน้าส่วนงาน', '(Lv.6-5)'],
                                    'วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)' => ['วิศวกร/อาวุโสเจ้าหน้าที่', '(Lv.4-3)'],
                                    'Sup/ปฏิบัติการ (Lv.2-1)' => ['Sup/ปฏิบัติการ', '(Lv.2-1)'],
                                ];
                            @endphp
                            @foreach($levels as $lvlKey => $lvlDisplay)
                                <label class="flex items-start p-1.5 rounded border border-gray-100 sm:border-none">
                                    <input type="radio" class="mt-0.5 mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0" 
                                        {{ str_contains($jobLevel, $lvlDisplay[0]) || $jobLevel == $lvlKey ? 'checked' : '' }} disabled>
                                    <div class="flex flex-col leading-tight">
                                        <span class="font-medium text-sm text-gray-900">{{ $lvlDisplay[0] }}</span>
                                        <span class="text-xs text-gray-500">{{ $lvlDisplay[1] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- ลักษณะการว่าจ้าง -->
                    <div class="flex flex-col sm:flex-row items-start gap-2 mb-6 mt-6">
                        <span class="mr-4 whitespace-nowrap pt-0.5 shrink-0 font-medium">ลักษณะการว่าจ้าง</span>
                        <div class="flex-grow min-w-0 flex flex-col space-y-3 w-full">
                            <label class="flex items-center">
                                <input type="radio" class="mr-3 w-4 h-4 text-black focus:ring-black border-gray-800" 
                                    {{ $hireType == 'จ้างเพิ่มเติม' || str_contains($hireType, 'เพิ่มเติม') ? 'checked' : '' }} disabled>
                                <span class="text-gray-900">จ้างเพิ่มเติม</span>
                            </label>
                            <div class="flex items-end gap-2 min-w-0 w-full flex-wrap sm:flex-nowrap">
                                <label class="flex items-center shrink-0">
                                    <input type="radio" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800" 
                                        {{ $hireType == 'จ้างทดแทน' || str_contains($hireType, 'ทดแทน') ? 'checked' : '' }} disabled>
                                    <span class="whitespace-nowrap">จ้างทดแทนคนเก่า นาย / นาง / นางสาว</span>
                                </label>
                                <input type="text" value="{{ $manpowerRequest->hire_replacement_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                            <div class="flex items-end gap-2 min-w-0 w-full flex-wrap sm:flex-nowrap">
                                <label class="flex items-center shrink-0">
                                    <input type="radio" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800" 
                                        {{ $hireType == 'โอนย้าย' || str_contains($hireType, 'โอนย้าย') ? 'checked' : '' }} disabled>
                                    <span class="whitespace-nowrap">โอนย้าย / ปรับเปลี่ยนตำแหน่ง นาย / นาง / นางสาว</span>
                                </label>
                                <input type="text" value="{{ $manpowerRequest->hire_transfer_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                            <div class="flex items-end gap-2 min-w-0 w-full flex-wrap sm:flex-nowrap">
                                <label class="flex items-center shrink-0 gap-2">
                                    <input type="radio" class="mr-0 w-4 h-4 text-black focus:ring-black border-gray-800" 
                                        {{ $hireType == 'จ้างชั่วคราว' || str_contains($hireType, 'ชั่วคราว') ? 'checked' : '' }} disabled>
                                    <span class="whitespace-nowrap">จ้างชั่วคราว</span>
                                    <span class="whitespace-nowrap shrink-0">ระยะเวลา จากวันที่</span>
                                </label>
                                <input type="text" value="{{ $manpowerRequest && $manpowerRequest->hire_temp_start ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_start)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="w-28 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                                <span class="whitespace-nowrap shrink-0">ถึง</span>
                                <input type="text" value="{{ $manpowerRequest && $manpowerRequest->hire_temp_end ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_end)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="flex-grow min-w-[80px] text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                        </div>
                    </div>

                    <!-- เอกสารแนบ -->
                    <div class="flex flex-col sm:flex-row sm:items-center mb-6 pl-0 sm:pl-8 gap-2">
                        <span class="mr-4 whitespace-nowrap shrink-0 font-medium">เอกสารแนบ</span>
                        <div class="flex-grow min-w-0 flex flex-wrap gap-4 pr-0 sm:pr-8">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800" {{ ($manpowerRequest && $manpowerRequest->attachment_org_chart) ? 'checked' : '' }} disabled>
                                <span>Organization Chart</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800" {{ ($manpowerRequest && $manpowerRequest->attachment_jd) ? 'checked' : '' }} disabled>
                                <span>Job Description</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800" {{ ($manpowerRequest && $manpowerRequest->attachment_manpower_plan) || true ? 'checked' : '' }} disabled>
                                <span>แผนอัตรากำลังคน</span>
                            </label>
                        </div>
                    </div>

                    <!-- 2 Column Table: คุณสมบัติที่ต้องการ VS หน้าที่รับผิดชอบโดยสังเขป -->
                    <div class="border-[1.5px] border-black mb-6 flex flex-col md:flex-row">
                        <!-- Left Column: คุณสมบัติที่ต้องการ -->
                        <div class="w-full md:w-1/2 border-b-[1.5px] md:border-b-0 md:border-r-[1.5px] border-black">
                            <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">คุณสมบัติที่ต้องการ</div>
                            <div class="p-3 space-y-4">
                                <div class="flex items-center flex-wrap gap-2">
                                    <span class="w-16 whitespace-nowrap shrink-0">เพศ</span>
                                    <label class="flex items-center mr-3"><input type="radio" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black" {{ $reqGender == 'ชาย' ? 'checked' : '' }} disabled> ชาย</label>
                                    <label class="flex items-center mr-3"><input type="radio" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black" {{ $reqGender == 'หญิง' ? 'checked' : '' }} disabled> หญิง</label>
                                    <label class="flex items-center"><input type="radio" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black" {{ $reqGender == 'ชาย/หญิง' || !$reqGender ? 'checked' : '' }} disabled> ชาย/หญิง</label>
                                </div>
                                <div class="flex flex-wrap sm:flex-nowrap items-end gap-2 w-full">
                                    <div class="flex items-end shrink-0">
                                        <span class="mr-2 whitespace-nowrap shrink-0">อายุ :</span>
                                        <input type="text" value="{{ $reqAge }}" readonly disabled class="w-14 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 font-medium">
                                    </div>
                                    <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                                        <span class="mx-1 sm:mx-2 whitespace-nowrap shrink-0">วุฒิการศึกษา :</span>
                                        <input type="text" value="{{ $reqEducation }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 font-medium">
                                    </div>
                                </div>
                                <div class="flex items-end w-full min-w-0">
                                    <span class="mr-2 whitespace-nowrap shrink-0">สาขาวิชา :</span>
                                    <input type="text" value="{{ $reqMajor }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                                </div>
                                <div class="flex items-end w-full min-w-0">
                                    <span class="mr-2 whitespace-nowrap shrink-0">ประสบการณ์ทำงาน :</span>
                                    <input type="text" value="{{ $reqExperience }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                                </div>
                                <div class="flex items-end w-full min-w-0">
                                    <span class="mr-2 whitespace-nowrap shrink-0">คุณสมบัติพิเศษ :</span>
                                    <input type="text" value="{{ $reqSpecial }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                                </div>
                                <div class="flex items-end w-full min-w-0">
                                    <span class="mr-2 whitespace-nowrap shrink-0">อื่นๆ :</span>
                                    <input type="text" value="{{ $reqOther }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: หน้าที่รับผิดชอบโดยสังเขป -->
                        <div class="w-full md:w-1/2">
                            <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">หน้าที่รับผิดชอบโดยสังเขป</div>
                            <div class="p-3 space-y-[1.125rem]">
                                @php
                                    $dutiesList = [];
                                    if ($manpowerRequest) {
                                        for ($i = 1; $i <= 6; $i++) {
                                            $dutiesList[$i] = $manpowerRequest->{'res_' . $i} ?? '';
                                        }
                                    } else {
                                        $rawDesc = preg_replace('/^ระดับ:.*$/m', '', $recruitmentRequest->job_description ?? '');
                                        $rawDesc = preg_replace('/^หน้าที่ความรับผิดชอบ:.*$/m', '', $rawDesc);
                                        $lines = array_values(array_filter(array_map('trim', explode("\n", $rawDesc))));
                                        for ($i = 1; $i <= 6; $i++) {
                                            $dutiesList[$i] = $lines[$i - 1] ?? '';
                                        }
                                    }
                                @endphp
                                @for($i = 1; $i <= 6; $i++)
                                <div class="flex items-end w-full min-w-0">
                                    <span class="mr-2 shrink-0 font-medium">{{ $i }}</span>
                                    <input type="text" value="{{ $dutiesList[$i] }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 text-gray-900">
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Signatures -->
                    <div class="space-y-6 mb-8 mt-8 min-w-0">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                            <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                                <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                                <input type="text" value="{{ $manpowerRequest->requester_name ?? ($recruitmentRequest->requester?->fullname ?? '') }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 font-medium">
                                <span class="ml-2 whitespace-nowrap shrink-0">ผู้ร้องขอ</span>
                            </div>
                            <div class="flex items-end w-full sm:w-48 shrink-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                                <input type="text" value="{{ $manpowerRequest && $manpowerRequest->requester_date ? \Carbon\Carbon::parse($manpowerRequest->requester_date)->addYears(543)->format('d/m/Y') : $docDate }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                            <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                                <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                                <input type="text" value="{{ $manpowerRequest->manager_name ?? ($recruitmentRequest->managerApprover?->fullname ?? '') }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 font-medium">
                                <span class="ml-2 whitespace-nowrap shrink-0">ผู้จัดการแผนก/ฝ่าย</span>
                            </div>
                            <div class="flex items-end w-full sm:w-48 shrink-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                                <input type="text" value="{{ $manpowerRequest && $manpowerRequest->manager_date ? \Carbon\Carbon::parse($manpowerRequest->manager_date)->addYears(543)->format('d/m/Y') : ($recruitmentRequest->approved_at_manager ? $recruitmentRequest->approved_at_manager->addYears(543)->format('d/m/Y') : '') }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                            <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                                <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                                <input type="text" value="{{ $manpowerRequest->vp_name ?? '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 font-medium">
                                <span class="ml-2 whitespace-nowrap shrink-0">ประธานสายงาน</span>
                            </div>
                            <div class="flex items-end w-full sm:w-48 shrink-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                                <input type="text" value="{{ $manpowerRequest && $manpowerRequest->vp_date ? \Carbon\Carbon::parse($manpowerRequest->vp_date)->addYears(543)->format('d/m/Y') : '' }}" readonly disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                            </div>
                        </div>
                    </div>

                    <!-- Approvals Table: HR & CEO -->
                    <div class="border-[1.5px] border-black mb-4 flex flex-col md:flex-row">
                        <!-- HR -->
                        <div class="w-full md:w-1/2 flex flex-col border-b-[1.5px] md:border-b-0 md:border-r-[1.5px] border-black overflow-hidden">
                            <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นฝ่ายบุคคล</div>
                            <div class="p-4 flex flex-col flex-grow min-w-0 relative pb-20 overflow-hidden">
                                <div class="w-full flex items-start">
                                    <span class="mr-2 mt-2 whitespace-nowrap shrink-0">ความเห็น :</span>
                                    <textarea disabled class="flex-grow min-w-0 bg-transparent border-none resize-none focus:ring-0 px-2 py-2 h-16 font-semibold text-green-600">อนุมัติการตรวจสอบ/เห็นชอบความต้องการกำลังคน</textarea>
                                </div>
                                <div class="w-full px-4 absolute bottom-4 left-0 overflow-hidden">
                                    <div class="text-center w-full mb-1 overflow-hidden truncate">
                                        @if($manpowerRequest && $manpowerRequest->hrApprover)
                                            <span class="text-green-600 font-semibold block">{{ $manpowerRequest->hrApprover->firstname . ' ' . $manpowerRequest->hrApprover->lastname }}</span>
                                            <span class="text-xs text-gray-500">( {{ \Carbon\Carbon::parse($manpowerRequest->hr_approved_at)->format('d/m/Y H:i') }} )</span>
                                        @else
                                            <span class="text-green-600 font-semibold block">เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล</span>
                                        @endif
                                    </div>
                                    <div class="text-center w-full">ผจก.แผนก/ฝ่ายทรัพยากรบุคคล</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CEO -->
                        <div class="w-full md:w-1/2 flex flex-col overflow-hidden">
                            <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นประธานเจ้าหน้าที่บริหาร</div>
                            <div class="p-4 flex flex-col flex-grow min-w-0 relative pb-20 overflow-hidden">
                                <div class="flex justify-center space-x-4 sm:space-x-12 w-full mt-4 flex-wrap gap-2">
                                    <label class="flex items-center">
                                        <input type="radio" disabled class="mr-2 w-4 h-4 text-black border-black" {{ $recruitmentRequest->status == 'approved' ? 'checked' : '' }}> 
                                        <span class="whitespace-nowrap">อนุมัติตามคำขอ</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" disabled class="mr-2 w-4 h-4 text-black border-black" {{ $recruitmentRequest->status == 'rejected' ? 'checked' : '' }}> 
                                        <span class="whitespace-nowrap">ไม่อนุมัติตามคำขอ</span>
                                    </label>
                                </div>
                                <div class="w-full px-4 absolute bottom-4 left-0 overflow-hidden">
                                    <div class="text-center w-full mb-1 overflow-hidden truncate">
                                        @if($manpowerRequest && $manpowerRequest->ceoApprover)
                                            <span class="text-green-600 font-semibold block">{{ $manpowerRequest->ceoApprover->firstname . ' ' . $manpowerRequest->ceoApprover->lastname }}</span>
                                            <span class="text-xs text-gray-500">( {{ \Carbon\Carbon::parse($manpowerRequest->ceo_approved_at)->format('d/m/Y H:i') }} )</span>
                                        @elseif($recruitmentRequest->executiveApprover)
                                            <span class="text-green-600 font-semibold block">{{ $recruitmentRequest->executiveApprover->fullname }}</span>
                                            <span class="text-xs text-gray-500">( {{ $recruitmentRequest->approved_at_executive?->format('d/m/Y H:i') ?? '-' }} )</span>
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

                    <!-- Document ID -->
                    <div class="text-right text-sm">
                        QF-HR-13 Rev.08 : 01-06-25
                    </div>
                </div>
            </div>

            <!-- Right: Workflow Container & Job Post Card (Matching รูปที่ 2) -->
            <div class="w-full lg:w-[380px] flex-shrink-0 space-y-6 lg:sticky lg:top-6">
                
                <!-- 5-Step Approval Workflow (Matching รูปที่ 2) -->
                @php
                    // Map status index: 0=Requester, 1=Manager, 2=VP, 3=HR, 4=CEO/Approved
                    $statusIndex = 0;
                    if ($recruitmentRequest->status === 'approved') {
                        $statusIndex = 5;
                    } elseif ($manpowerRequest) {
                        $mMap = ['pending_manager' => 0, 'pending_vp' => 1, 'pending_hr' => 2, 'pending_ceo' => 3, 'approved' => 5];
                        $statusIndex = $mMap[$manpowerRequest->status] ?? 0;
                    } elseif ($recruitmentRequest->approved_by_executive) {
                        $statusIndex = 5;
                    } elseif ($recruitmentRequest->approved_by_manager) {
                        $statusIndex = 2;
                    }
                    $isRejected = $recruitmentRequest->status === 'rejected';
                @endphp

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-md">
                    <h3 class="text-lg font-bold mb-5 text-gray-900 dark:text-white">
                        ขั้นตอนการอนุมัติ<br>
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Approval Workflow</span>
                    </h3>
                    
                    @if($isRejected)
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-300 rounded-xl text-xs">
                            <strong>คำขอถูกปฏิเสธ</strong>
                        </div>
                    @endif

                    <div class="relative">
                        <!-- Vertical line -->
                        <div class="absolute left-4 top-0 h-full w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                        
                        <div class="space-y-6">
                            <!-- Step 1 -->
                            <div class="relative pl-10">
                                <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full bg-green-500 border-4 border-white dark:border-gray-800 shadow"></div>
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">1. ผู้ร้องขอ (Requester)</p>
                                <p class="text-xs text-gray-500">จัดทำและยื่นใบขออนุมัติกำลังคน</p>
                            </div>
                            
                            <!-- Step 2 -->
                            <div class="relative pl-10">
                                <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex >= 1 ? 'bg-green-500' : 'bg-blue-500' }} border-4 border-white dark:border-gray-800 shadow"></div>
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">2. ผู้จัดการแผนก/ฝ่าย</p>
                                <p class="text-xs text-gray-500">ตรวจสอบและลงนามอนุมัติเบื้องต้น</p>
                                @if($manpowerRequest && $manpowerRequest->manager_approved_at)
                                    <p class="text-[11px] text-green-600 font-medium mt-0.5">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->manager_approved_at)->format('d/m/Y H:i') }}</p>
                                @elseif($recruitmentRequest->approved_by_manager)
                                    <p class="text-[11px] text-green-600 font-medium mt-0.5">อนุมัติเมื่อ: {{ $recruitmentRequest->approved_at_manager?->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                            
                            <!-- Step 3 -->
                            <div class="relative pl-10">
                                <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex >= 2 ? 'bg-green-500' : ($statusIndex === 1 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }} border-4 border-white dark:border-gray-800 shadow"></div>
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">3. ประธานสายงาน (C Level)</p>
                                <p class="text-xs text-gray-500">พิจารณาเห็นชอบความต้องการกำลังคน</p>
                                @if($manpowerRequest && $manpowerRequest->vp_approved_at)
                                    <p class="text-[11px] text-green-600 font-medium mt-0.5">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->vp_approved_at)->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                            
                            <!-- Step 4 -->
                            <div class="relative pl-10">
                                <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex >= 3 ? 'bg-green-500' : ($statusIndex === 2 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }} border-4 border-white dark:border-gray-800 shadow"></div>
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">4. ผจก.ทรัพยากรบุคคล (HR)</p>
                                <p class="text-xs text-gray-500">ตรวจสอบความถูกต้อง</p>
                                @if($manpowerRequest && $manpowerRequest->hr_approved_at)
                                    <p class="text-[11px] text-green-600 font-medium mt-0.5">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->hr_approved_at)->format('d/m/Y H:i') }}</p>
                                @elseif($recruitmentRequest->status == 'approved')
                                    <p class="text-[11px] text-green-600 font-medium mt-0.5">ตรวจสอบเรียบร้อย</p>
                                @endif
                            </div>
                            
                            <!-- Step 5 -->
                            <div class="relative pl-10">
                                <div class="absolute left-4 top-1.5 -translate-x-1/2 w-4 h-4 rounded-full {{ $statusIndex >= 4 ? 'bg-green-500' : ($statusIndex === 3 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }} border-4 border-white dark:border-gray-800 shadow"></div>
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">5. ประธานเจ้าหน้าที่บริหาร (CEO)</p>
                                <p class="text-xs text-gray-500">พิจารณาอนุมัติขั้นตอนสุดท้าย</p>
                                @if($manpowerRequest && $manpowerRequest->ceo_approved_at)
                                    <p class="text-[11px] text-green-600 font-medium mt-0.5">อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($manpowerRequest->ceo_approved_at)->format('d/m/Y H:i') }}</p>
                                @elseif($recruitmentRequest->status == 'approved')
                                    <p class="text-[11px] text-green-600 font-medium mt-0.5">อนุมัติครบทุกขั้นตอน</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Post Card (Matching รูปที่ 2) -->
                @if($recruitmentRequest->status == 'approved')
                    @php
                        $linkedJobPost = $recruitmentRequest->jobPosts()->latest()->first();
                    @endphp
                    @if($linkedJobPost)
                        <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl p-6 text-center shadow-sm">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-xl shadow-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-emerald-800 dark:text-emerald-300 font-bold text-base mb-1">สร้าง Job Post แล้ว</h3>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 mb-4">
                                ประกาศ: <strong class="underline">{{ $linkedJobPost->title }}</strong>
                                <br>
                                สถานะ: <span class="font-bold uppercase">{{ $linkedJobPost->publish_status }}</span>
                            </p>
                            <a href="{{ route('backend.recruitment.posts.edit', $linkedJobPost->id) }}"
                                class="inline-flex items-center justify-center w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all shadow-md text-xs gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                <span>ดู / แก้ไขประกาศรับสมัคร</span>
                            </a>
                        </div>
                    @else
                        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800/70 rounded-2xl p-6 text-center shadow-sm relative overflow-hidden">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-white uppercase tracking-wider mb-2 animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                <span>แจ้งเตือน HA</span>
                            </div>
                            <h3 class="text-amber-800 dark:text-amber-200 font-bold mb-1">ยังไม่ได้สร้าง Job Post</h3>
                            <p class="text-xs text-amber-700 dark:text-amber-300 mb-4 leading-relaxed">
                                คำขอนี้ผ่านการอนุมัติแล้ว <strong>กรุณาทำการสร้างประกาศรับสมัครงาน</strong> ตามใบขออนุมัตินี้
                            </p>
                            <a href="{{ route('backend.recruitment.posts.create', ['request_id' => $recruitmentRequest->id]) }}"
                                class="inline-flex items-center justify-center w-full bg-gradient-to-r from-red-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all shadow-md text-xs gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                                </svg>
                                <span>สร้าง Job Post ตอนนี้</span>
                            </a>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
@endsection