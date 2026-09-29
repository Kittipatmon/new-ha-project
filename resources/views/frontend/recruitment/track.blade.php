@extends('layouts.recruitment.app')

@section('title', 'ตรวจสอบสถานะการสมัครงาน | Kumwell Recruitment')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-950 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Header / Breadcrumbs -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 border border-red-200/60 dark:border-red-900/60">
                <i class="fa-solid fa-satellite-dish animate-pulse"></i>
                <span>Candidate Portal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                ตรวจสอบสถานะการสมัครงาน
            </h1>
            <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 max-w-xl mx-auto">
                ติดตามขั้นตอนและความคืบหน้าการพิจารณาใบสมัครของท่านได้ตลอด 24 ชั่วโมง โดยกรอกเลขที่ใบสมัคร หรือเบอร์โทรศัพท์ หรืออีเมล <strong class="text-slate-700 dark:text-slate-200">(พิมพ์ค้นหาอย่างใดอย่างหนึ่งได้)</strong>
            </p>
        </div>

        <!-- Search Form Card -->
        <div class="bg-white dark:bg-slate-800/95 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 backdrop-blur-sm">
            @if(isset($errors) && $errors->has('search_error'))
                <div class="mb-5 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs sm:text-sm flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-amber-600 text-base shrink-0"></i>
                    <span>{{ $errors->first('search_error') }}</span>
                </div>
            @endif

            <form id="track-search-form" action="{{ route('recruitment.track.post') }}" method="POST" class="space-y-5">
                @csrf
                <div class="relative grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 items-start">
                    <!-- Application No -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="application_no" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                1. เลขที่ใบสมัคร (Application No.)
                            </label>
                            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">หากจำรหัสได้</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-hashtag text-sm"></i>
                            </div>
                            <input type="text"
                                id="application_no"
                                name="application_no"
                                value="{{ old('application_no', $applicationNo ?? '') }}"
                                autocomplete="off"
                                placeholder="เช่น APP-DUN5PPHQ"
                                class="w-full pl-10 pr-4 py-3 rounded-2xl border {{ (isset($errors) && $errors->has('application_no')) ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 dark:border-slate-600' }} bg-slate-50/50 dark:bg-slate-900/50 text-slate-900 dark:text-white text-sm font-semibold uppercase tracking-wider focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all">
                        </div>
                        @if(isset($errors) && $errors->has('application_no'))
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $errors->first('application_no') }}</span>
                            </p>
                        @endif
                    </div>

                    <!-- Divider OR for Desktop -->
                    <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 items-center justify-center z-10 pointer-events-none">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 border border-slate-200 dark:border-slate-600 shadow-sm">
                            หรือ
                        </span>
                    </div>

                    <!-- Identifier (Phone or Email) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="identifier" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                2. เบอร์โทรศัพท์ หรือ อีเมลที่ใช้สมัคร
                            </label>
                            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">ข้อมูลติดต่อ</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-user-shield text-sm"></i>
                            </div>
                            <input type="text"
                                id="identifier"
                                name="identifier"
                                value="{{ old('identifier', '') }}"
                                autocomplete="off"
                                placeholder="เช่น 081-234-5678 หรือ youremail@kumwell.com"
                                class="w-full pl-10 pr-4 py-3 rounded-2xl border {{ (isset($errors) && $errors->has('identifier')) ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 dark:border-slate-600' }} bg-slate-50/50 dark:bg-slate-900/50 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all">
                        </div>
                        @if(isset($errors) && $errors->has('identifier'))
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $errors->first('identifier') }}</span>
                            </p>
                        @endif
                    </div>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        <span>พิมพ์ค้นหาอย่างใดอย่างหนึ่ง หรือกรอกทั้งสองช่องก็ได้</span>
                    </p>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold text-sm shadow-lg shadow-red-600/25 hover:shadow-red-600/40 active:scale-95 transition-all cursor-pointer">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>ตรวจสอบสถานะ</span>
                    </button>
                </div>
            </form>
        </div>

        @if($searched && $application)
            @if(isset($applications) && $applications->count() > 1)
                <!-- Multi-Application Switcher -->
                <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-red-600"></i>
                            <span>พบประวัติการสมัครงานของคุณ {{ $applications->count() }} ตำแหน่ง:</span>
                        </span>
                        <span class="text-[11px] text-slate-400">คลิกเลือกเพื่อดูสถานะแต่ละตำแหน่ง</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($applications as $itemApp)
                            @php
                                $isItemActive = ($itemApp->id === $application->id);
                            @endphp
                            <a href="{{ route('recruitment.track', ['app_no' => $itemApp->application_no]) }}"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer {{ $isItemActive ? 'bg-red-600 text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200' }}">
                                <span>{{ $itemApp->jobPost->position_name ?? ($itemApp->jobPost->title ?? 'ตำแหน่งงาน') }}</span>
                                <span class="font-mono text-[10px] {{ $isItemActive ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300' }} px-1.5 py-0.5 rounded">
                                    {{ $itemApp->application_no }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $status = $application->status;
                $applicant = $application->applicant;
                $post = $application->jobPost;

                // Mask applicant name for privacy (e.g., นาย กิตติพัฒน์ ม.)
                $prefix = $applicant->prefix ?? '';
                $fname = $applicant->first_name ?? '';
                $lname = $applicant->last_name ?? '';
                $lnameMasked = mb_strlen($lname) > 1 ? (mb_substr($lname, 0, 1) . '...') : $lname;
                $maskedName = trim("{$prefix} {$fname} {$lnameMasked}");

                // Stepper state determination
                // Step 1: ยื่นใบสมัคร (Submitted)
                // Step 2: ตรวจสอบคุณสมบัติ (HR Screening)
                // Step 3: หัวหน้าแผนกพิจารณา (Dept Review)
                // Step 4: สัมภาษณ์งาน (Interview)
                // Step 5: ผลการคัดเลือก (Result)
                
                $step1 = 'completed';
                $step2 = 'upcoming';
                $step3 = 'upcoming';
                $step4 = 'upcoming';
                $step5 = 'upcoming';

                $isRejected = false;
                $rejectedAtStep = 0;

                if (in_array($status, ['new', 'submitted', 'pending'])) {
                    $step2 = 'current';
                } elseif (in_array($status, ['screening', 'screening_passed'])) {
                    $step2 = 'completed';
                    $step3 = 'current';
                } elseif ($status === 'screening_failed') {
                    $step2 = 'rejected';
                    $isRejected = true;
                    $rejectedAtStep = 2;
                } elseif ($status === 'dept_review') {
                    $step2 = 'completed';
                    $step3 = 'current';
                } elseif ($status === 'dept_rejected') {
                    $step2 = 'completed';
                    $step3 = 'rejected';
                    $isRejected = true;
                    $rejectedAtStep = 3;
                } elseif (in_array($status, ['interview', 'interview_scheduled', 'interview_completed'])) {
                    $step2 = 'completed';
                    $step3 = 'completed';
                    $step4 = ($status === 'interview_completed') ? 'completed' : 'current';
                    if ($status === 'interview_completed') {
                        $step5 = 'current';
                    }
                } elseif ($status === 'interview_failed') {
                    $step2 = 'completed';
                    $step3 = 'completed';
                    $step4 = 'rejected';
                    $isRejected = true;
                    $rejectedAtStep = 4;
                } elseif (in_array($status, ['passed_selection', 'selection_approved', 'offered', 'hired'])) {
                    $step2 = 'completed';
                    $step3 = 'completed';
                    $step4 = 'completed';
                    $step5 = 'completed';
                } elseif ($status === 'rejected') {
                    $step2 = 'completed';
                    $step5 = 'rejected';
                    $isRejected = true;
                    $rejectedAtStep = 5;
                }

                // Status labels & badges
                $statusMap = [
                    'new' => ['text' => 'รอคัดกรองเบื้องต้น', 'color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300'],
                    'submitted' => ['text' => 'ส่งใบสมัครแล้ว', 'color' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/60 dark:text-blue-300'],
                    'pending' => ['text' => 'รอการตรวจสอบ', 'color' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300'],
                    'screening' => ['text' => 'อยู่ระหว่างคัดกรอง', 'color' => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-950/60 dark:text-indigo-300'],
                    'screening_passed' => ['text' => 'ผ่านการคัดกรองเบื้องต้น', 'color' => 'bg-teal-100 text-teal-800 border-teal-300 dark:bg-teal-950/60 dark:text-teal-300'],
                    'screening_failed' => ['text' => 'ไม่ผ่านการคัดกรอง', 'color' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300'],
                    'dept_review' => ['text' => 'อยู่ระหว่างแผนกพิจารณา', 'color' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-950/60 dark:text-purple-300'],
                    'dept_rejected' => ['text' => 'ไม่ผ่านการพิจารณาจากแผนก', 'color' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300'],
                    'interview' => ['text' => 'รอนัดหมายสัมภาษณ์', 'color' => 'bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-950/60 dark:text-sky-300'],
                    'interview_scheduled' => ['text' => 'นัดหมายสัมภาษณ์แล้ว', 'color' => 'bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-950/60 dark:text-sky-300'],
                    'interview_completed' => ['text' => 'สัมภาษณ์แล้ว (รอผล)', 'color' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/60 dark:text-blue-300'],
                    'interview_failed' => ['text' => 'ไม่ผ่านการสัมภาษณ์', 'color' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300'],
                    'passed_selection' => ['text' => 'ผ่านการคัดเลือก', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300'],
                    'selection_approved' => ['text' => 'อนุมัติการรับเข้าทำงาน', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300'],
                    'offered' => ['text' => 'เสนอจ้างงาน (Offer)', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300'],
                    'hired' => ['text' => 'รับเข้าทำงานแล้ว', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300'],
                    'rejected' => ['text' => 'ไม่ผ่านการคัดเลือก', 'color' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300'],
                ];
                $currentBadge = $statusMap[$status] ?? ['text' => $status, 'color' => 'bg-slate-100 text-slate-800 border-slate-300'];
            @endphp

            <!-- Application Details Header Card -->
            <div class="bg-white dark:bg-slate-800/95 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                <div class="p-6 sm:p-8 bg-gradient-to-r from-red-600 to-red-800 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs uppercase tracking-wider font-semibold text-red-200">เลขที่ใบสมัคร</span>
                            <span class="font-mono font-extrabold text-white text-base sm:text-lg bg-black/20 px-2.5 py-0.5 rounded-lg border border-white/20">
                                {{ $application->application_no }}
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white">
                            {{ $post->position_name ?? ($post->jobPosition?->position_name ?? $post->title) }}
                        </h2>
                        <p class="text-xs sm:text-sm text-red-100 mt-1 flex items-center gap-3">
                            <span><i class="fa-solid fa-building mr-1"></i> {{ $post->department->department_name ?? 'Kumwell Corporation' }}</span>
                            <span>&bull;</span>
                            <span><i class="fa-solid fa-calendar-day mr-1"></i> วันที่สมัคร: {{ $application->created_at ? $application->created_at->locale('th')->translatedFormat('d F Y') : '-' }}</span>
                        </p>
                    </div>

                    <div class="sm:text-right shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $currentBadge['color'] }} shadow-sm">
                            <i class="fa-solid fa-circle-dot text-[9px] animate-pulse"></i>
                            <span>{{ $currentBadge['text'] }}</span>
                        </span>
                        <p class="text-xs text-red-100 mt-1.5">ผู้สมัคร: <strong class="text-white">{{ $maskedName }}</strong></p>
                    </div>
                </div>

                <!-- Progress Stepper Section -->
                <div class="p-6 sm:p-8 space-y-8">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        ขั้นตอนการพิจารณาใบสมัคร (5 ขั้นตอน)
                    </h3>

                    <!-- Stepper Bar (Horizontal on Desktop, Vertical on Mobile) -->
                    <div class="relative">
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 sm:gap-2">
                            @php
                                $steps = [
                                    1 => ['title' => 'ยื่นใบสมัคร', 'desc' => 'บันทึกเข้าระบบแล้ว', 'icon' => 'fa-file-lines', 'state' => $step1],
                                    2 => ['title' => 'HA คัดกรอง', 'desc' => 'ตรวจสอบคุณสมบัติเบื้องต้น', 'icon' => 'fa-user-check', 'state' => $step2],
                                    3 => ['title' => 'แผนกพิจารณา', 'desc' => 'หัวหน้าแผนกพิจารณา', 'icon' => 'fa-clipboard-check', 'state' => $step3],
                                    4 => ['title' => 'การสัมภาษณ์', 'desc' => 'นัดหมายและสัมภาษณ์', 'icon' => 'fa-comments', 'state' => $step4],
                                    5 => ['title' => 'ผลการคัดเลือก', 'desc' => 'ผลการพิจารณาขั้นสุดท้าย', 'icon' => 'fa-award', 'state' => $step5],
                                ];
                            @endphp

                            @foreach($steps as $num => $s)
                                <div class="relative flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2 p-3 sm:p-2 rounded-2xl {{ $s['state'] === 'current' ? 'bg-red-50/60 dark:bg-red-950/30 border border-red-200/80 dark:border-red-900/60' : '' }}">
                                    <!-- Step Icon Bubble -->
                                    <div class="relative shrink-0">
                                        @if($s['state'] === 'completed')
                                            <div class="w-11 h-11 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-emerald-500/30">
                                                <i class="fa-solid fa-check text-base"></i>
                                            </div>
                                        @elseif($s['state'] === 'current')
                                            <div class="w-11 h-11 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-red-600/40 ring-4 ring-red-200 dark:ring-red-950/80 animate-pulse">
                                                <i class="fa-solid {{ $s['icon'] }} text-base"></i>
                                            </div>
                                        @elseif($s['state'] === 'rejected')
                                            <div class="w-11 h-11 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-rose-500/30">
                                                <i class="fa-solid fa-xmark text-base"></i>
                                            </div>
                                        @else
                                            <div class="w-11 h-11 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-400 flex items-center justify-center font-bold text-sm border border-slate-200 dark:border-slate-600">
                                                <span>{{ $num }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Step Text -->
                                    <div class="min-w-0 flex-1 sm:w-full">
                                        <div class="text-xs sm:text-sm font-bold {{ $s['state'] === 'current' ? 'text-red-600 dark:text-red-400' : ($s['state'] === 'completed' ? 'text-emerald-700 dark:text-emerald-400' : ($s['state'] === 'rejected' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-400')) }}">
                                            {{ $s['title'] }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate sm:whitespace-normal">
                                            {{ $s['desc'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Step Explanation Box -->
                    <div class="rounded-2xl p-5 sm:p-6 {{ $isRejected ? 'bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/60' : 'bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80' }} space-y-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid {{ $isRejected ? 'fa-circle-info text-rose-500' : 'fa-circle-info text-red-600 dark:text-red-400' }} text-base"></i>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $isRejected ? 'สถานะการพิจารณา' : 'สถานะปัจจุบันและคำแนะนำ' }}
                            </h4>
                        </div>

                        <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed space-y-2">
                            @if(in_array($status, ['new', 'submitted', 'pending']))
                                <p>ใบสมัครงานของท่านได้รับการบันทึกเข้าสู่ระบบเรียบร้อยแล้ว ขณะนี้อยู่ระหว่างรอการตรวจสอบคุณสมบัติเบื้องต้นโดยฝ่ายทรัพยากรบุคคล (HA) ระยะเวลาดำเนินการประมาณ 1-3 วันทำการ</p>
                            @elseif(in_array($status, ['screening', 'screening_passed']))
                                <p>ใบสมัครผ่านเกณฑ์คุณสมบัติเบื้องต้นแล้ว และเจ้าหน้าที่กำลังส่งต่อประวัติให้หัวหน้าแผนกที่เกี่ยวข้องทำการพิจารณาในลำดับถัดไป</p>
                            @elseif($status === 'dept_review')
                                <p>ใบสมัครของท่านอยู่ระหว่างการพิจารณาความเหมาะสมและประสบการณ์โดยหัวหน้าแผนก หากผ่านเกณฑ์ เจ้าหน้าที่จะติดต่อเพื่อนัดหมายวันสัมภาษณ์งานต่อไป</p>
                            @elseif($status === 'interview_scheduled')
                                <div class="space-y-2 bg-sky-50 dark:bg-sky-950/40 p-4 rounded-xl border border-sky-200 dark:border-sky-800">
                                    <p class="font-bold text-sky-900 dark:text-sky-200 flex items-center gap-1.5">
                                        <i class="fa-solid fa-calendar-check text-sky-600"></i>
                                        <span>ท่านได้รับการนัดหมายสัมภาษณ์งาน</span>
                                    </p>
                                    <p class="text-xs text-sky-800 dark:text-sky-300">
                                        ทางฝ่ายทรัพยากรบุคคลได้จัดส่งรายละเอียด วันเวลา สถานที่ และลิงก์การสัมภาษณ์ พร้อมไฟล์ปฏิทินนัดหมาย (.ics) ไปยังอีเมลของท่านแล้ว โปรดตรวจสอบกล่องข้อความหรืออีเมลขยะ (Junk/Spam)
                                    </p>
                                </div>
                            @elseif($status === 'interview_completed')
                                <p>ท่านได้เข้ารับการสัมภาษณ์งานเรียบร้อยแล้ว ขณะนี้คณะกรรมการและผู้บริหารอยู่ระหว่างการสรุปผลการประเมินรอบสุดท้าย</p>
                            @elseif(in_array($status, ['passed_selection', 'selection_approved', 'offered', 'hired']))
                                <div class="space-y-2 bg-emerald-50 dark:bg-emerald-950/40 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800">
                                    <p class="font-bold text-emerald-900 dark:text-emerald-200 flex items-center gap-1.5">
                                        <i class="fa-solid fa-trophy text-emerald-600"></i>
                                        <span>ขอแสดงความยินดีด้วย! ท่านผ่านการคัดเลือก</span>
                                    </p>
                                    <p class="text-xs text-emerald-800 dark:text-emerald-300">
                                        ท่านผ่านการพิจารณาคัดเลือกร่วมงานกับ บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ทางฝ่ายทรัพยากรบุคคลจะติดต่อกลับผ่านทางเบอร์โทรศัพท์และอีเมลเพื่อแจ้งรายละเอียดข้อเสนอจ้างงานและเอกสารรายงานตัว
                                    </p>
                                </div>
                            @elseif($isRejected)
                                <p>
                                    ทางบริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ขอขอบพระคุณเป็นอย่างยิ่งที่ท่านให้ความสนใจร่วมงานกับเรา
                                    เนื่องจากในตำแหน่งนี้มีผู้สนใจสมัครเป็นจำนวนมากและมีเกณฑ์พิจารณาเฉพาะด้านสำหรับรอบนี้
                                    ทางบริษัทจึงขออนุญาตเก็บประวัติของท่านไว้ในระบบ Candidate Pool เพื่อการติดต่อกลับเมื่อมีตำแหน่งงานที่ตรงกับคุณสมบัติของท่านในอนาคต
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer contact info -->
                <div class="px-6 sm:px-8 py-4 bg-slate-50 dark:bg-slate-900/80 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-red-600"></i>
                        <span>ติดต่อฝ่ายบุคคล: <a href="mailto:recruitment@kumwell.com" class="text-red-600 hover:underline font-semibold">recruitment@kumwell.com</a></span>
                    </div>
                    <div>
                        <span>โทร: 02-954-3455 ต่อ ฝ่ายทรัพยากรบุคคล (จันทร์ - ศุกร์ 08:30 - 17:30 น.)</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Help Card -->
        <div class="p-6 bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    ยังไม่มีรหัสใบสมัคร หรือ กำลังมองหางานใหม่?
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    ดูตำแหน่งงานว่างที่เปิดรับสมัครล่าสุด และยื่นใบสมัครออนไลน์ได้ทันที
                </p>
            </div>
            <a href="{{ route('recruitment.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-bold transition-all shrink-0">
                <i class="fa-solid fa-briefcase"></i>
                <span>ดูตำแหน่งงานว่างทั้งหมด</span>
            </a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('track-search-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const appNo = (document.getElementById('application_no')?.value || '').trim();
            const identifier = (document.getElementById('identifier')?.value || '').trim();
            if (!appNo && !identifier) {
                e.preventDefault();
                alert('กรุณากรอกเลขที่ใบสมัคร หรือ เบอร์โทรศัพท์ หรือ อีเมล อย่างใดอย่างหนึ่งเพื่อค้นหา');
                const firstInput = document.getElementById('application_no') || document.getElementById('identifier');
                if (firstInput) firstInput.focus();
            }
        });
    }
});
</script>
@endsection

