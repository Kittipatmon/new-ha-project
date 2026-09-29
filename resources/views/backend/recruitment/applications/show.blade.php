@extends('layouts.recruitment.app')

@section('title', 'รายละเอียดผู้สมัคร')

@section('content')
    @php
        $currentUser = Auth::user();
        $isHa = $currentUser && ($currentUser->isCentralHr() || $currentUser->role === 'admin' || (method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin()));
        $managedDeptIds = $currentUser ? $currentUser->getManagedDepartmentIds() : [];
        $isDeptManager = $currentUser && (!empty($managedDeptIds) || $currentUser->isDepartmentManager());
    @endphp
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-12">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ (url()->previous() && url()->previous() !== url()->current()) ? url()->previous() : ($isHa ? route('backend.recruitment.applications.index') : route('recruitment.reports')) }}"
                    onclick="if (window.history.length > 1 && document.referrer && document.referrer !== window.location.href) { event.preventDefault(); window.history.back(); }"
                    class="w-10 h-10 rounded-xl bg-white dark:bg-kumwell-card border border-gray-200 dark:border-gray-800 flex items-center justify-center text-gray-500 hover:text-kumwell-red hover:border-kumwell-red transition-all shadow-sm"
                    title="ย้อนกลับไปหน้าที่แล้ว">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold dark:text-white text-gray-800">
                            {{ $application->applicant->full_name }}
                        </h2>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold border shadow-2xs {{ $application->status_badge_class }}">
                            <i class="{{ $application->status_icon }} text-xs"></i>
                            <span>{{ $application->status_label }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        เลขที่ใบสมัคร: <span class="font-mono font-semibold">{{ $application->application_no ?? ('APP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT)) }}</span>
                        • สมัครเมื่อ {{ $application->applied_at ? $application->applied_at->format('d/m/Y H:i') : $application->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
            @if($application->onboarding_date)
                <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400 block">วันเริ่มงาน (Onboarding)</span>
                        <span class="text-xs font-bold text-emerald-900 dark:text-emerald-200">{{ $application->onboarding_date->format('d/m/Y') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- ==================== RECRUITMENT WORKFLOW STEPPER ==================== -->
        @php
            $currentStep = $application->workflow_step;
            $isHired = ($application->status === 'hired');
            $steps = [
                1 => ['name' => '1. ตรวจสอบคุณสมบัติ (HA)', 'desc' => 'HA คัดกรองข้อมูลผู้สมัคร', 'icon' => 'fa-clipboard-check'],
                2 => ['name' => '2. หัวหน้าแผนกพิจารณา', 'desc' => 'ส่งรายชื่อให้หัวหน้าแผนก', 'icon' => 'fa-user-tie'],
                3 => ['name' => '3. นัดสัมภาษณ์', 'desc' => 'HA กำหนดวันนัดและแจ้งผู้สมัคร', 'icon' => 'fa-calendar-days'],
                4 => ['name' => '4. สัมภาษณ์ & ประเมินผล', 'desc' => 'ดำเนินการสัมภาษณ์และให้คะแนน', 'icon' => 'fa-pen-to-square'],
                5 => ['name' => '5. อนุมัติการคัดเลือก', 'desc' => 'กดอนุมัติผ่านการคัดเลือก', 'icon' => 'fa-circle-check'],
                6 => ['name' => '6. ยื่นข้อเสนอ (Offer)', 'desc' => 'HA ติดต่อแจ้งผลผู้สมัคร', 'icon' => 'fa-handshake'],
                7 => ['name' => '7. กำหนดวันเริ่มงานและส่งแจ้งผู้สมัคร', 'desc' => 'กำหนดวันเริ่มงานและส่งแจ้งผู้สมัคร', 'icon' => 'fa-calendar-check'],
                8 => ['name' => '8. เสร็จการทำงาน (บรรจุงาน)', 'desc' => 'รับเข้าทำงานเสร็จสมบูรณ์', 'icon' => 'fa-building-user'],
            ];

            // Helper to find log for specific status
            $findLog = function($statuses) use ($application) {
                $statuses = (array) $statuses;
                return $application->statusLogs
                    ->whereIn('new_status', $statuses)
                    ->sortByDesc('created_at')
                    ->first();
            };

            // Step 1: ตรวจสอบคุณสมบัติ (HA)
            $step1Date = $application->screened_at ?? $findLog(['dept_review', 'interview', 'interview_scheduled'])?->created_at;
            $step1User = $application->screener?->fullname 
                ?? ($application->screener?->firstname ? trim($application->screener->firstname . ' ' . $application->screener->lastname) : null)
                ?? $findLog(['dept_review'])?->user?->fullname
                ?? ($step1Date ? 'ฝ่าย HA' : null);

            // Step 2: หัวหน้าแผนกพิจารณา
            $step2Date = $application->dept_reviewed_at ?? $findLog(['interview', 'interview_scheduled'])?->created_at;
            $step2User = $application->deptReviewer?->fullname
                ?? ($application->deptReviewer?->firstname ? trim($application->deptReviewer->firstname . ' ' . $application->deptReviewer->lastname) : null)
                ?? $findLog(['interview', 'interview_scheduled'])?->user?->fullname
                ?? ($step2Date ? 'หัวหน้าแผนก' : null);

            // Step 3: นัดสัมภาษณ์
            $firstInterview = $application->interviews->sortBy('created_at')->first();
            $step3Date = $firstInterview?->created_at ?? $findLog(['interview_scheduled', 'interview_completed'])?->created_at;
            $step3User = $findLog(['interview_scheduled'])?->user?->fullname
                ?? $firstInterview?->interviewer?->fullname
                ?? ($step3Date ? 'ฝ่าย HA' : null);

            // Step 4: สัมภาษณ์ & ประเมินผล
            $completedInterview = $application->interviews->where('status', 'completed')->sortByDesc('updated_at')->first();
            $step4Date = $completedInterview?->updated_at ?? $findLog(['interview_completed', 'passed_selection', 'selection_approved'])?->created_at;
            $step4User = $completedInterview?->interviewers?->first()?->fullname 
                ?? $findLog(['interview_completed'])?->user?->fullname
                ?? ($step4Date ? 'ผู้สัมภาษณ์' : null);

            // Step 5: อนุมัติการคัดเลือก
            $step5Date = $application->final_result_at ?? $findLog(['passed_selection', 'selection_approved', 'offered', 'hired'])?->created_at;
            $step5User = $findLog(['passed_selection', 'selection_approved'])?->user?->fullname 
                ?? ($step5Date ? 'ผู้อนุมัติ' : null);

            // Step 6: ยื่นข้อเสนอ (Offer)
            $step6Log = $findLog(['offered', 'hired']);
            $step6Date = $step6Log?->created_at;
            $step6User = $step6Log?->user?->fullname ?? ($step6Date ? 'ฝ่าย HA' : null);

            // Step 7: กำหนดวันเริ่มงานและส่งแจ้งผู้สมัคร
            $step7Log = $findLog(['hired']);
            $step7Date = $step7Log?->created_at ?? ($application->onboarding_date ? $application->onboarding_date : null);
            $step7User = $step7Log?->user?->fullname ?? ($step7Date ? 'ฝ่าย HA' : null);

            // Step 8: เสร็จการทำงาน (บรรจุงาน)
            $step8Date = $application->onboarding_date ?? $step7Date;
            $step8User = $application->onboarding_date ? 'เริ่มงาน ' . $application->onboarding_date->format('d/m/Y') : ($step7User ?? 'ฝ่าย HA');

            $stepApprovalInfo = [
                1 => ['date' => $step1Date, 'user' => $step1User, 'role' => 'HA'],
                2 => ['date' => $step2Date, 'user' => $step2User, 'role' => 'หัวหน้าแผนก'],
                3 => ['date' => $step3Date, 'user' => $step3User, 'role' => 'HA'],
                4 => ['date' => $step4Date, 'user' => $step4User, 'role' => 'ผู้สัมภาษณ์'],
                5 => ['date' => $step5Date, 'user' => $step5User, 'role' => 'ผู้อนุมัติ'],
                6 => ['date' => $step6Date, 'user' => $step6User, 'role' => 'HA'],
                7 => ['date' => $step7Date, 'user' => $step7User, 'role' => 'HA'],
                8 => ['date' => $step8Date, 'user' => $step8User, 'role' => 'HA'],
            ];

            $stepCount = count($steps);
            $progressPercent = $isHired ? 100 : min(100, max(0, (($currentStep - 1) / max(1, $stepCount - 1)) * 100));
        @endphp
        <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-slate-300 dark:border-slate-700 p-4 sm:p-6 overflow-x-auto">
            <div class="min-w-[960px]">
                <div class="flex items-start justify-between relative">
                    {{-- Background line (gray track) --}}
                    <div class="absolute left-[48px] right-[48px] top-6 h-1 bg-gray-200 dark:bg-gray-700 rounded-full" style="z-index: 1;"></div>
                    {{-- Active Progress line (colored) --}}
                    <div class="absolute left-[48px] top-6 h-1 bg-gradient-to-r from-red-600 via-amber-500 to-emerald-500 rounded-full transition-all duration-500"
                         style="z-index: 2; width: calc({{ $progressPercent }}% - 96px * {{ $progressPercent / 100 }});"></div>

                    @foreach($steps as $sIndex => $sData)
                        @php
                            if ($isHired) {
                                $isCompleted = true;
                                $isActive = false;
                                $isPending = false;
                            } else {
                                $isCompleted = $sIndex < $currentStep;
                                $isActive = $sIndex === $currentStep;
                                $isPending = $sIndex > $currentStep;
                            }
                            $stepInfo = $stepApprovalInfo[$sIndex] ?? [];
                            $apprDate = $stepInfo['date'] ?? null;
                            $apprUser = $stepInfo['user'] ?? null;
                        @endphp
                        <div class="flex flex-col items-center text-center w-28 sm:w-32" style="position: relative; z-index: 5;">
                            {{-- White background ring to mask the line behind the circle --}}
                            <div class="w-12 h-12 rounded-2xl bg-white dark:bg-kumwell-card flex items-center justify-center p-[3px]">
                                <div class="w-full h-full rounded-xl flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-sm
                                    @if($isCompleted) bg-emerald-600 text-white shadow-emerald-500/20
                                    @elseif($isActive) bg-kumwell-red text-white ring-4 ring-red-100 dark:ring-red-950 shadow-red-500/30 scale-110
                                    @else bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 @endif">
                                    @if($isCompleted)
                                        <i class="fa-solid fa-check"></i>
                                    @else
                                        <i class="fa-solid {{ $sData['icon'] }} text-xs"></i>
                                    @endif
                                </div>
                            </div>
                            <span class="text-[11px] font-bold mt-2 leading-tight
                                @if($isActive) text-kumwell-red dark:text-red-400
                                @elseif($isCompleted) text-gray-800 dark:text-gray-200
                                @else text-gray-400 @endif">
                                {{ $sData['name'] }}
                            </span>

                            {{-- Approval Time & Approver Badge --}}
                            @if($isCompleted)
                                <div class="mt-1.5 flex flex-col items-center w-full px-1">
                                    <span class="inline-flex items-center gap-1 text-[9.5px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/80 dark:border-emerald-800 px-1.5 py-0.5 rounded shadow-xs" title="ผ่านการอนุมัติเมื่อ: {{ $apprDate ? (is_a($apprDate, 'DateTimeInterface') ? $apprDate->format('d/m/Y H:i') : $apprDate) : '-' }}">
                                        <i class="fa-solid fa-check text-[8px]"></i>
                                        <span>{{ $sIndex === 8 && $application->onboarding_date ? 'เริ่มงาน ' . $application->onboarding_date->format('d/m/y') : ($apprDate ? (is_a($apprDate, 'DateTimeInterface') ? $apprDate->format('d/m/y H:i') : (string)$apprDate) : 'อนุมัติแล้ว') }}</span>
                                    </span>
                                    @if(!empty($apprUser))
                                        <span class="text-[9.5px] text-gray-600 dark:text-gray-300 mt-1 max-w-[120px] truncate block font-medium" title="ผู้อนุมัติ: {{ $apprUser }}">
                                            <i class="fa-solid {{ $sIndex === 8 ? 'fa-building-circle-check' : 'fa-user-check' }} text-[8.5px] text-emerald-600 mr-0.5"></i>{{ $sIndex === 8 ? 'บรรจุงานเสร็จสมบูรณ์' : $apprUser }}
                                        </span>
                                    @endif
                                </div>
                            @elseif($isActive)
                                <div class="mt-1.5 flex flex-col items-center w-full px-1">
                                    <span class="inline-flex items-center gap-1 text-[9.5px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 px-1.5 py-0.5 rounded shadow-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        <span>กำลังดำเนินการ</span>
                                    </span>
                                    @if($sIndex === 1 && $application->applied_at)
                                        <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 font-normal" title="ส่งใบสมัครเมื่อ {{ $application->applied_at->format('d/m/Y H:i') }}">
                                            ยื่น: {{ $application->applied_at->format('d/m/y H:i') }}
                                        </span>
                                    @elseif($sIndex === 6)
                                        <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 font-normal">รอส่ง Offer</span>
                                    @elseif($sIndex === 7)
                                        <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 font-normal">
                                            {{ $application->onboarding_date ? 'แผนกเสนอ: ' . $application->onboarding_date->format('d/m/y') : 'รอกำหนดวันเริ่มงาน' }}
                                        </span>
                                    @else
                                        <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 font-normal">รอการพิจารณา</span>
                                    @endif
                                </div>
                            @else
                                <div class="mt-1.5 flex flex-col items-center w-full">
                                    <span class="text-[9.5px] text-gray-300 dark:text-gray-600 font-normal">รอดำเนินการ</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <!-- Main Info (Left) -->
            <div class="xl:col-span-8 space-y-6" x-data="{ currentSheet: 1 }">
                @php
                    $appUser = $application->applicant;
                    $fam = $appUser->family_info ?? [];
                    $emerg = $appUser->emergency_contact ?? [];
                    $qAnswers = $appUser->health_criminal_questionnaire ?? [];
                    $appQuestions = $appUser->application_questions ?? [];
                    $specialAbilities = $appUser->special_abilities ?? [];
                    $refInfo = $appUser->references_info ?? [];
                    $docsCheck = $appUser->attached_documents_check ?? [];

                    $resolveFilePath = function($filePath) {
                        if (!$filePath) return null;
                        $b = basename($filePath);
                        if (file_exists(public_path('files/recruitment_applicant_documents/' . $b))) {
                            return asset('files/recruitment_applicant_documents/' . $b);
                        }
                        if (file_exists(public_path('files/recruitment_applicant_documents/' . $filePath))) {
                            return asset('files/recruitment_applicant_documents/' . $filePath);
                        }
                        if (file_exists(public_path('storage/' . $filePath))) {
                            return asset('storage/' . $filePath);
                        }
                        if (file_exists(public_path($filePath))) {
                            return asset($filePath);
                        }
                        return asset('files/recruitment_applicant_documents/' . $b);
                    };

                    $photoUrl = null;
                    if (!empty($appUser->photo_file)) {
                        if (file_exists(public_path($appUser->photo_file))) {
                            $photoUrl = asset($appUser->photo_file);
                        } elseif (file_exists(public_path('files/recruitment_applicant_documents/' . basename($appUser->photo_file)))) {
                            $photoUrl = asset('files/recruitment_applicant_documents/' . basename($appUser->photo_file));
                        } elseif (file_exists(public_path('storage/' . $appUser->photo_file))) {
                            $photoUrl = asset('storage/' . $appUser->photo_file);
                        }
                    }
                    if (!$photoUrl && $application->documents) {
                        $photoDoc = $application->documents->where('document_type', 'photo')->first();
                        if ($photoDoc) {
                            $photoUrl = $resolveFilePath($photoDoc->file_path);
                        }
                    }

                    $allAppDocs = collect($application->documents);
                    if ($allAppDocs->isEmpty() && $appUser) {
                        if ($appUser->resume_file) {
                            $allAppDocs->push((object)[
                                'document_type' => 'resume',
                                'file_name' => 'Resume_' . $appUser->first_name . '.pdf',
                                'file_path' => $appUser->resume_file,
                            ]);
                        }
                        if ($appUser->portfolio_file) {
                            $allAppDocs->push((object)[
                                'document_type' => 'portfolio',
                                'file_name' => 'Portfolio_' . $appUser->first_name . '.pdf',
                                'file_path' => $appUser->portfolio_file,
                            ]);
                        }
                    }

                    $showEducation = $application->education->isNotEmpty() 
                        ? $application->education 
                        : ($appUser && $appUser->education->isNotEmpty() 
                            ? $appUser->education 
                            : collect($appUser && ($appUser->education_level || $appUser->university_name) ? [(object)[
                                'institution_name' => $appUser->university_name ?: '-',
                                'level' => $appUser->education_level ?: '-',
                                'gpa' => $appUser->gpa,
                                'faculty' => $appUser->faculty,
                                'major' => $appUser->major,
                                'start_year' => null,
                                'end_year' => null,
                            ]] : []));

                    $showExperience = $application->experience->isNotEmpty() 
                        ? $application->experience 
                        : ($appUser && $appUser->experience->isNotEmpty() 
                            ? $appUser->experience 
                            : collect($appUser && ($appUser->current_company || $appUser->current_position) ? [(object)[
                                'company_name' => $appUser->current_company ?: 'ไม่ระบุชื่อบริษัท',
                                'position' => $appUser->current_position ?: 'ไม่ระบุตำแหน่ง',
                                'start_date' => null,
                                'end_date' => null,
                                'salary' => $appUser->expected_salary,
                                'job_detail' => $appUser->years_of_experience ? "ประสบการณ์ทำงานรวม {$appUser->years_of_experience} ปี" : null,
                                'reason_for_leaving' => null,
                            ]] : []));
                @endphp

                <!-- 5-Step Stepper Navigation Bar (Matching Picture 2) -->
                <div class="bg-white dark:bg-kumwell-card p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-300 dark:border-slate-700 overflow-x-auto">
                    <div class="flex items-center justify-between min-w-[720px] lg:min-w-0 px-1 gap-2">
                        <!-- Step 1 -->
                        <button type="button" @click="currentSheet = 1" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-sm sm:text-base font-bold transition-all shrink-0"
                                :class="currentSheet === 1 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200'">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs sm:text-sm font-bold transition-colors leading-tight"
                                    :class="currentSheet === 1 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : 'text-slate-600 dark:text-slate-300'">
                                    ข้อมูลส่วนตัว
                                </span>
                                <span class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                    Personal Info
                                </span>
                            </div>
                        </button>

                        <!-- Arrow 1 -> 2 -->
                        <div class="text-slate-300 dark:text-slate-600 text-xs sm:text-sm shrink-0">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>

                        <!-- Step 2 -->
                        <button type="button" @click="currentSheet = 2" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-sm sm:text-base font-bold transition-all shrink-0"
                                :class="currentSheet === 2 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200'">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs sm:text-sm font-bold transition-colors leading-tight"
                                    :class="currentSheet === 2 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : 'text-slate-600 dark:text-slate-300'">
                                    ประวัติครอบครัว
                                </span>
                                <span class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                    Family Info
                                </span>
                            </div>
                        </button>

                        <!-- Arrow 2 -> 3 -->
                        <div class="text-slate-300 dark:text-slate-600 text-xs sm:text-sm shrink-0">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>

                        <!-- Step 3 -->
                        <button type="button" @click="currentSheet = 3" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-sm sm:text-base font-bold transition-all shrink-0"
                                :class="currentSheet === 3 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200'">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs sm:text-sm font-bold transition-colors leading-tight"
                                    :class="currentSheet === 3 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : 'text-slate-600 dark:text-slate-300'">
                                    การศึกษาและทำงาน
                                </span>
                                <span class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                    Education & Work
                                </span>
                            </div>
                        </button>

                        <!-- Arrow 3 -> 4 -->
                        <div class="text-slate-300 dark:text-slate-600 text-xs sm:text-sm shrink-0">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>

                        <!-- Step 4 -->
                        <button type="button" @click="currentSheet = 4" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-sm sm:text-base font-bold transition-all shrink-0"
                                :class="currentSheet === 4 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200'">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs sm:text-sm font-bold transition-colors leading-tight"
                                    :class="currentSheet === 4 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : 'text-slate-600 dark:text-slate-300'">
                                    ข้อมูลเพิ่มเติม
                                </span>
                                <span class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                    Additional Info
                                </span>
                            </div>
                        </button>

                        <!-- Arrow 4 -> 5 -->
                        <div class="text-slate-300 dark:text-slate-600 text-xs sm:text-sm shrink-0">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>

                        <!-- Step 5 -->
                        <button type="button" @click="currentSheet = 5" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-sm sm:text-base font-bold transition-all shrink-0"
                                :class="currentSheet === 5 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200'">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs sm:text-sm font-bold transition-colors leading-tight"
                                    :class="currentSheet === 5 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : 'text-slate-600 dark:text-slate-300'">
                                    บุคคลอ้างอิง
                                </span>
                                <span class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                    References & Consent
                                </span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- =========================================================================
                     DOCUMENT SHEET 1 / 5: PERSONAL INFORMATION (EXACT PICTURE 2)
                ========================================================================== -->
                <div x-show="currentSheet === 1" class="paper-sheet-container">
                    <div class="paper-sheet pt-10 pb-8 px-6 sm:px-10 md:px-12 rounded-xl relative shadow-md">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <span class="doc-input w-36 text-center font-bold font-mono">
                                    {{ $application->application_no ?? ('APP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT)) }}
                                </span>
                            </div>
                            <div class="font-bold text-xs">1/5</div>
                        </div>

                        <!-- Form Title & Photo Box -->
                        <div class="relative mb-6 min-h-[140px] flex justify-between items-start">
                            <div class="text-center flex-1 pr-2 sm:pr-4 pl-0 pt-1">
                                <h2 class="text-sm sm:text-base md:text-lg font-black tracking-wide uppercase font-sans">APPLICATION FOR EMPLOYMENT</h2>
                                <h3 class="text-lg sm:text-xl md:text-2xl font-black mt-0.5 sm:mt-1">ใบสมัครงาน</h3>
                                <p class="text-xs font-bold text-gray-800 mt-1">กรอกข้อมูลด้วยตัวท่านเอง</p>
                                <p class="text-[10px] sm:text-[11px] text-gray-500 italic">(To be completed in own handwriting)</p>
                            </div>
                            <!-- Photo Box on Top Right -->
                            <div class="shrink-0">
                                <div class="w-[105px] sm:w-[115px] h-[135px] sm:h-[145px] border border-black flex flex-col items-center justify-center p-1 text-center relative bg-white overflow-hidden shadow-2xs">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="Photo" class="w-full h-full object-cover">
                                    @else
                                        <div class="space-y-1 text-center">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <p class="text-[10px] font-bold text-gray-500">ติดรูปถ่าย<br>1.5 - 2 นิ้ว<br><span class="text-[9px] font-normal text-gray-400">(Photo)</span></p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Main Form Body: Page 1 -->
                        <div class="space-y-5 text-sm">
                            <!-- Row 1: Name, Last Name & Nickname -->
                            <div class="flex items-end gap-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-base leading-tight">ชื่อ :</span>
                                    <span class="sub-label">Name</span>
                                </div>
                                <span class="doc-input flex-grow font-semibold">{{ $appUser->first_name ?: $appUser->full_name }}</span>
                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-bold text-base leading-tight">นามสกุล :</span>
                                    <span class="sub-label">Last Name</span>
                                </div>
                                <span class="doc-input flex-grow font-semibold">{{ $appUser->last_name ?: '-' }}</span>
                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-bold text-base leading-tight">ชื่อเล่น</span>
                                    <span class="sub-label">Nickname</span>
                                </div>
                                <span class="doc-input w-28 text-center font-semibold">{{ $appUser->nickname ?: '-' }}</span>
                            </div>

                            <!-- Row 2: Position Applied for -->
                            <div class="flex items-end gap-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-base leading-tight">ตำแหน่งที่ต้องการสมัคร</span>
                                    <span class="sub-label">Position Applied for</span>
                                </div>
                                <span class="doc-input flex-grow font-bold text-[#B21F24]">
                                    {{ $application->jobPost->position_name ?? ($application->jobPost->jobPosition->position_name ?? ($appUser->current_position ?? '-')) }}
                                </span>
                            </div>

                            <!-- Section: Personal Information -->
                            <div class="pt-2">
                                <h4 class="font-bold text-base text-black">Personal information (ประวัติส่วนตัว)</h4>
                            </div>

                            <!-- Present Address Line 1 -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ที่อยู่ปัจจุบันเลขที่</span>
                                    <span class="sub-label">Present address</span>
                                </div>
                                <span class="doc-input w-24 text-center font-semibold shrink-0">{{ $appUser->house_no ?: ($appUser->address ?: '-') }}</span>

                                <div class="shrink-0 flex flex-col ml-1">
                                    <span class="shrink-0 font-medium text-sm leading-tight">หมู่ที่</span>
                                    <span class="sub-label">Moo</span>
                                </div>
                                <span class="doc-input w-16 text-center font-semibold shrink-0">{{ $appUser->moo ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-1">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ถนน</span>
                                    <span class="sub-label">Road</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[80px] font-semibold">{{ $appUser->road ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-1">
                                    <span class="shrink-0 font-medium text-sm leading-tight">จังหวัด</span>
                                    <span class="sub-label">Province</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[80px] font-semibold">{{ $appUser->province ?: '-' }}</span>
                            </div>

                            <!-- Present Address Line 2 -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">อำเภอ/เขต</span>
                                    <span class="sub-label">District</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[80px] font-semibold">{{ $appUser->district ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ตำบล/แขวง</span>
                                    <span class="sub-label">Subdistrict</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[80px] font-semibold">{{ $appUser->subdistrict ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">รหัสไปรษณีย์</span>
                                    <span class="sub-label">Post code</span>
                                </div>
                                <span class="doc-input w-32 text-center font-semibold shrink-0">{{ $appUser->postcode ?: '-' }}</span>
                            </div>

                            <!-- Phone, Mobile, ID Line -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">โทรศัพท์</span>
                                    <span class="sub-label">Tel.</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[60px]">{{ $appUser->tel ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">มือถือ</span>
                                    <span class="sub-label">Mobile</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[100px] font-bold text-gray-900">{{ $appUser->phone ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ID Line</span>
                                    <span class="sub-label">ID Line</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[100px]">{{ $appUser->line_id ?: '-' }}</span>
                            </div>

                            <!-- Facebook & Email -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">Facebook</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[120px]">{{ $appUser->facebook ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">อีเมล์</span>
                                    <span class="sub-label">E-mail</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[140px] font-medium">{{ $appUser->email ?: '-' }}</span>
                            </div>

                            <!-- Housing Options -->
                            @php
                                $ht = $appUser->housing_type ?? '';
                            @endphp
                            <div class="flex flex-wrap items-center gap-y-2 gap-x-4 pt-2">
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="checkbox" disabled {{ str_contains($ht, 'ครอบครัว') ? 'checked' : '' }} class="paper-checkbox">
                                    <span class="text-sm">อาศัยกับครอบครัว <span class="sub-label">Living with parent</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="checkbox" disabled {{ str_contains($ht, 'ตัวเอง') ? 'checked' : '' }} class="paper-checkbox">
                                    <span class="text-sm">บ้านตัวเอง <span class="sub-label">Own house</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="checkbox" disabled {{ str_contains($ht, 'เช่า') ? 'checked' : '' }} class="paper-checkbox">
                                    <span class="text-sm">บ้านเช่า <span class="sub-label">Rented house</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="checkbox" disabled {{ str_contains($ht, 'หอพัก') ? 'checked' : '' }} class="paper-checkbox">
                                    <span class="text-sm">หอพัก <span class="sub-label">Dormitory / Hostel</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="checkbox" disabled {{ str_contains($ht, 'คอนโด') ? 'checked' : '' }} class="paper-checkbox">
                                    <span class="text-sm">คอนโดมิเนียม <span class="sub-label">Condominium</span></span>
                                </label>
                            </div>

                            <!-- Date of Birth, Age, Place of Birth -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">วัน เดือน ปีเกิด</span>
                                    <span class="sub-label">Date of birth</span>
                                </div>
                                <span class="doc-input w-28 text-center font-semibold">{{ $appUser->date_of_birth ? $appUser->date_of_birth->format('d/m/Y') : '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">อายุ</span>
                                    <span class="sub-label">Age Yrs.</span>
                                </div>
                                <span class="doc-input w-16 text-center font-semibold">{{ $appUser->age ?: '-' }}</span>
                                <span class="shrink-0 font-medium text-sm pb-1">ปี</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">สถานที่เกิด</span>
                                    <span class="sub-label">Place of Birth</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[80px]">{{ $appUser->place_of_birth ?: '-' }}</span>
                            </div>

                            <!-- Race, Nationality, Religion -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">เชื้อชาติ</span>
                                    <span class="sub-label">Race</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[60px] text-center">{{ $appUser->race ?: 'ไทย' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">สัญชาติ</span>
                                    <span class="sub-label">Nationality</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[60px] text-center">{{ $appUser->nationality ?: 'ไทย' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ศาสนา</span>
                                    <span class="sub-label">Religion</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[60px] text-center">{{ $appUser->religion ?: 'พุทธ' }}</span>
                            </div>

                            <!-- ID Card Details -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">บัตรประชาชนเลขที่</span>
                                    <span class="sub-label">Identity card no.</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[130px] font-mono font-semibold">{{ $appUser->national_id ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ออกโดย</span>
                                    <span class="sub-label">Issued by</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[80px]">{{ $appUser->id_card_issued_by ?: '-' }}</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">จังหวัด</span>
                                    <span class="sub-label">Province</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[80px] font-semibold">{{ $appUser->id_card_issued_province ?: '-' }}</span>
                            </div>

                            <!-- Issued Date & Expiry Date -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">วันออกบัตร</span>
                                    <span class="sub-label">Issued Date</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[100px] text-center font-semibold">
                                    {{ $appUser->id_card_issued_date ? (is_string($appUser->id_card_issued_date) ? $appUser->id_card_issued_date : $appUser->id_card_issued_date->format('d/m/Y')) : '-' }}
                                </span>

                                <div class="shrink-0 flex flex-col ml-4">
                                    <span class="shrink-0 font-medium text-sm leading-tight">วันหมดอายุ</span>
                                    <span class="sub-label">Expiration date</span>
                                </div>
                                <span class="doc-input flex-1 min-w-[100px] text-center font-semibold">
                                    {{ $appUser->id_card_expiry_date ? (is_string($appUser->id_card_expiry_date) ? $appUser->id_card_expiry_date : $appUser->id_card_expiry_date->format('d/m/Y')) : '-' }}
                                </span>
                            </div>

                            <!-- Height & Weight -->
                            <div class="flex items-end gap-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ส่วนสูง</span>
                                    <span class="sub-label">Height cm.</span>
                                </div>
                                <span class="doc-input w-24 text-center font-semibold">{{ $appUser->height_cm ?: '-' }}</span>
                                <span class="shrink-0 font-medium text-sm pb-1">ซม.</span>

                                <div class="shrink-0 flex flex-col ml-12">
                                    <span class="shrink-0 font-medium text-sm leading-tight">น้ำหนัก</span>
                                    <span class="sub-label">Weight kgs.</span>
                                </div>
                                <span class="doc-input w-24 text-center font-semibold">{{ $appUser->weight_kg ?: '-' }}</span>
                                <span class="shrink-0 font-medium text-sm pb-1">กก.</span>
                            </div>

                            <!-- Military Status -->
                            @php
                                $ms = $appUser->military_status ?? '';
                            @endphp
                            <div class="grid grid-cols-12 gap-2 pt-2 items-center">
                                <div class="col-span-3">
                                    <span class="font-medium text-sm">สถานะทางทหาร</span>
                                    <span class="sub-label">Military status</span>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $ms === 'ได้รับการยกเว้น' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">ได้รับการยกเว้น <span class="sub-label">Exempted</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $ms === 'ปลดเป็นทหารกองหนุน' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">ปลดเป็นทหารกองหนุน <span class="sub-label">Served</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $ms === 'ยังไม่ได้รับการเกณฑ์' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">ยังไม่ได้รับการเกณฑ์ <span class="sub-label">Not yet served</span></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Marital Status -->
                            @php
                                $mStat = $appUser->marital_status ?? '';
                            @endphp
                            <div class="grid grid-cols-12 gap-2 pt-2 items-center">
                                <div class="col-span-3">
                                    <span class="font-medium text-sm">สถานภาพ</span>
                                    <span class="sub-label">Marital status</span>
                                </div>
                                <div class="col-span-2">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $mStat === 'โสด' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">โสด <span class="sub-label">Single</span></span>
                                    </label>
                                </div>
                                <div class="col-span-2">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $mStat === 'แต่งงาน' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">แต่งงาน <span class="sub-label">Married</span></span>
                                    </label>
                                </div>
                                <div class="col-span-2">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $mStat === 'หม้าย' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">หม้าย <span class="sub-label">Widowed</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $mStat === 'แยกกัน' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">แยกกัน <span class="sub-label">Separated</span></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Sex -->
                            @php
                                $gnd = $appUser->gender ?? '';
                            @endphp
                            <div class="grid grid-cols-12 gap-2 pt-2 items-center">
                                <div class="col-span-3">
                                    <span class="font-medium text-sm">เพศ</span>
                                    <span class="sub-label">Sex</span>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $gnd === 'ชาย' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">ชาย <span class="sub-label">Male</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="radio" disabled {{ $gnd === 'หญิง' ? 'checked' : '' }} class="paper-checkbox">
                                        <span class="text-sm">หญิง <span class="sub-label">Female</span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-14 pt-4 border-t border-gray-300 flex justify-between items-center text-[11px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                </div>

                <!-- =========================================================================
                     DOCUMENT SHEET 2 / 5: FAMILY INFORMATION & EMERGENCY CONTACT
                ========================================================================== -->
                <div x-show="currentSheet === 2" class="paper-sheet-container">
                    <div class="paper-sheet pt-10 pb-8 px-6 sm:px-10 md:px-12 rounded-xl relative shadow-md">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <span class="doc-input w-36 text-center font-bold font-mono">
                                    {{ $application->application_no ?? ('APP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT)) }}
                                </span>
                            </div>
                            <div class="font-bold text-xs">2/5</div>
                        </div>

                        <div class="space-y-5 text-sm">
                            <h4 class="font-bold text-base text-black">Family Information (ประวัติครอบครัว)</h4>

                            <!-- Father Information -->
                            <div class="space-y-3 pt-1">
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">บิดา ชื่อ-สกุล</span>
                                            <span class="sub-label">Father’s name-surname</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['father']['name'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-1 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อายุ</span>
                                            <span class="sub-label">Age Yrs.</span>
                                        </div>
                                        <span class="doc-input w-16 text-center font-semibold">{{ $fam['father']['age'] ?? '-' }}</span>
                                        <span class="shrink-0 font-medium">ปี</span>
                                    </div>
                                    <div class="flex items-end gap-2 flex-1 min-w-[160px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อาชีพ</span>
                                            <span class="sub-label">Occupation</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['father']['occupation'] ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">สถานที่ทำงาน</span>
                                            <span class="sub-label">Place of work</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['father']['workplace'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">เบอร์โทร</span>
                                            <span class="sub-label">Mobile</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['father']['phone'] ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Mother Information -->
                            <div class="space-y-3 pt-2 border-t border-gray-200">
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">มารดา ชื่อ-สกุล</span>
                                            <span class="sub-label">Mother’s name-surname</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['mother']['name'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-1 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อายุ</span>
                                            <span class="sub-label">Age Yrs.</span>
                                        </div>
                                        <span class="doc-input w-16 text-center font-semibold">{{ $fam['mother']['age'] ?? '-' }}</span>
                                        <span class="shrink-0 font-medium">ปี</span>
                                    </div>
                                    <div class="flex items-end gap-2 flex-1 min-w-[160px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อาชีพ</span>
                                            <span class="sub-label">Occupation</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['mother']['occupation'] ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">สถานที่ทำงาน</span>
                                            <span class="sub-label">Place of work</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['mother']['workplace'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">เบอร์โทร</span>
                                            <span class="sub-label">Mobile</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $fam['mother']['phone'] ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Spouse Information -->
                            @if(($appUser->marital_status ?? '') === 'โสด')
                                <div class="p-3 my-2 rounded-lg border border-dashed border-gray-300 bg-gray-50 text-xs text-gray-500 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                                    <span>สถานภาพ <strong>โสด</strong> — ไม่มีข้อมูลคู่สมรส</span>
                                </div>
                            @else
                                <div class="space-y-3 pt-2 border-t border-gray-200">
                                    <div class="flex items-center justify-between pb-1">
                                        <span class="font-bold text-sm text-gray-800">
                                            ข้อมูลภรรยา/สามี (Spouse Information)
                                        </span>
                                    </div>

                                    <div class="flex items-end gap-3 w-full">
                                        <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                            <div class="shrink-0 flex flex-col">
                                                <span class="font-medium leading-tight">ชื่อภรรยา/สามี</span>
                                                <span class="sub-label">Name of wife / Husband</span>
                                            </div>
                                            <span class="doc-input flex-grow font-semibold">{{ $fam['spouse']['name'] ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-end gap-1 shrink-0">
                                            <div class="shrink-0 flex flex-col">
                                                <span class="font-medium leading-tight">อายุ</span>
                                                <span class="sub-label">Age Yrs.</span>
                                            </div>
                                            <span class="doc-input w-16 text-center font-semibold">{{ $fam['spouse']['age'] ?? '-' }}</span>
                                            <span class="shrink-0 font-medium">ปี</span>
                                        </div>
                                        <div class="flex items-end gap-2 flex-1 min-w-[160px]">
                                            <div class="shrink-0 flex flex-col">
                                                <span class="font-medium leading-tight">อาชีพ</span>
                                                <span class="sub-label">Occupation</span>
                                            </div>
                                            <span class="doc-input flex-grow font-semibold">{{ $fam['spouse']['occupation'] ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-end gap-3 w-full">
                                        <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                            <div class="shrink-0 flex flex-col">
                                                <span class="font-medium leading-tight">สถานที่ทำงาน</span>
                                                <span class="sub-label">Place of work</span>
                                            </div>
                                            <span class="doc-input flex-grow font-semibold">{{ $fam['spouse']['workplace'] ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-end gap-2 w-64 shrink-0">
                                            <div class="shrink-0 flex flex-col">
                                                <span class="font-medium leading-tight">เบอร์โทร</span>
                                                <span class="sub-label">Mobile</span>
                                            </div>
                                            <span class="doc-input flex-grow font-semibold">{{ $fam['spouse']['phone'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Children Count -->
                            <div class="flex items-end gap-2 pt-1">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-medium leading-tight">มีบุตร</span>
                                    <span class="sub-label">Number of children</span>
                                </div>
                                <span class="doc-input w-20 text-center font-bold">{{ $fam['children_count'] ?? '0' }}</span>
                                <span class="shrink-0 font-medium">คน</span>
                            </div>

                            <!-- Siblings Stats -->
                            <div class="flex items-end gap-2 w-full flex-wrap pt-1">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-medium leading-tight">มีพี่น้อง (รวมผู้สมัคร)</span>
                                    <span class="sub-label">Number of Members in the family</span>
                                </div>
                                <span class="doc-input w-16 text-center font-bold">{{ $fam['total_siblings'] ?? '-' }}</span>
                                <span class="shrink-0 font-medium">คน</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-medium leading-tight">ชาย</span>
                                    <span class="sub-label">Male</span>
                                </div>
                                <span class="doc-input w-14 text-center font-semibold">{{ $fam['siblings_male'] ?? '-' }}</span>
                                <span class="shrink-0 font-medium">คน</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-medium leading-tight">หญิง</span>
                                    <span class="sub-label">Female</span>
                                </div>
                                <span class="doc-input w-14 text-center font-semibold">{{ $fam['siblings_female'] ?? '-' }}</span>
                                <span class="shrink-0 font-medium">คน</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-medium leading-tight">เป็นบุตรคนที่</span>
                                    <span class="sub-label">Ordinal number of children</span>
                                </div>
                                <span class="doc-input w-14 text-center font-bold">{{ $fam['birth_order'] ?? '-' }}</span>
                            </div>

                            <!-- Siblings Table -->
                            <div class="pt-2">
                                <table class="w-full doc-table text-xs">
                                    <thead>
                                        <tr>
                                            <th class="w-1/2 py-1.5">ชื่อ<br><span class="sub-label">Name</span></th>
                                            <th class="w-1/4 py-1.5">อายุ (ปี)<br><span class="sub-label">Age</span></th>
                                            <th class="w-1/4 py-1.5">อาชีพ<br><span class="sub-label">Occupation</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($fam['siblings']) && count($fam['siblings']) > 0)
                                            @foreach($fam['siblings'] as $sib)
                                                <tr>
                                                    <td class="p-1.5 font-medium">{{ $sib['name'] ?? '-' }}</td>
                                                    <td class="p-1.5 text-center">{{ $sib['age'] ?? '-' }}</td>
                                                    <td class="p-1.5">{{ $sib['occupation'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="p-2 text-center text-gray-400 italic" colspan="3">- ไม่มีข้อมูลพี่น้อง -</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Section: Emergency Contact -->
                            <div class="pt-4 border-t-2 border-gray-400 space-y-3">
                                <h4 class="font-bold text-sm text-black">บุคคลสำหรับติดต่อในกรณีฉุกเฉิน<br><span class="sub-label text-xs">Emergency Contact</span></h4>

                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ชื่อ- สกุล</span>
                                            <span class="sub-label">Name – Surname</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold text-gray-900">{{ $emerg['name'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ความสัมพันธ์</span>
                                            <span class="sub-label">Relationship</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $emerg['relationship'] ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-end gap-2 w-full">
                                    <div class="shrink-0 flex flex-col">
                                        <span class="font-medium leading-tight">ที่อยู่เลขที่</span>
                                        <span class="sub-label">Address</span>
                                    </div>
                                    <span class="doc-input w-24 text-center font-semibold shrink-0">{{ $emerg['house_no'] ?? '-' }}</span>

                                    <div class="shrink-0 flex flex-col ml-1">
                                        <span class="font-medium leading-tight">หมู่ที่</span>
                                        <span class="sub-label">Moo</span>
                                    </div>
                                    <span class="doc-input w-16 text-center font-semibold">{{ $emerg['moo'] ?? '-' }}</span>

                                    <div class="shrink-0 flex flex-col ml-1">
                                        <span class="font-medium leading-tight">ซอย</span>
                                        <span class="sub-label">Soi</span>
                                    </div>
                                    <span class="doc-input w-28">{{ $emerg['soi'] ?? '-' }}</span>

                                    <div class="shrink-0 flex flex-col ml-1">
                                        <span class="font-medium leading-tight">ถนน</span>
                                        <span class="sub-label">Road</span>
                                    </div>
                                    <span class="doc-input flex-grow">{{ $emerg['road'] ?? '-' }}</span>
                                </div>

                                <div class="flex items-end gap-2.5 w-full">
                                    <div class="flex items-end gap-1.5 flex-1 min-w-[120px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">จังหวัด</span>
                                            <span class="sub-label">Province</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $emerg['province'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-1.5 flex-1 min-w-[120px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อำเภอ/เขต</span>
                                            <span class="sub-label">District</span>
                                        </div>
                                        <span class="doc-input flex-grow">{{ $emerg['district'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-1.5 flex-1 min-w-[120px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ตำบล/แขวง</span>
                                            <span class="sub-label">Subdistrict</span>
                                        </div>
                                        <span class="doc-input flex-grow">{{ $emerg['subdistrict'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-1.5 w-56 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium font-bold leading-tight">เบอร์โทร</span>
                                            <span class="sub-label">Mobile</span>
                                        </div>
                                        <span class="doc-input flex-grow font-bold text-[#B21F24]">{{ $emerg['mobile'] ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ที่ทำงาน</span>
                                            <span class="sub-label">Place of work</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $emerg['workplace'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">โทรศัพท์</span>
                                            <span class="sub-label">Working Place Phone</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $emerg['work_phone'] ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                </div>

                <!-- =========================================================================
                     DOCUMENT SHEET 3 / 5: EDUCATION, EXPERIENCE & LANGUAGE
                ========================================================================== -->
                <div x-show="currentSheet === 3" class="paper-sheet-container">
                    <div class="paper-sheet pt-10 pb-8 px-6 sm:px-10 md:px-12 rounded-xl relative shadow-md">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <span class="doc-input w-36 text-center font-bold font-mono">
                                    {{ $application->application_no ?? ('APP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT)) }}
                                </span>
                            </div>
                            <div class="font-bold text-xs">3/5</div>
                        </div>

                        <div class="space-y-6 text-sm">
                            <!-- Section: Education -->
                            <div class="space-y-2">
                                <h4 class="font-bold text-base text-black">Education (การศึกษา)</h4>
                                <table class="w-full doc-table text-sm">
                                    <thead>
                                        <tr>
                                            <th class="w-1/4 py-1.5">ระดับการศึกษา<br><span class="sub-label">Educational Level</span></th>
                                            <th class="w-1/3 py-1.5">สถาบันการศึกษา<br><span class="sub-label">Institution</span></th>
                                            <th class="w-1/4 py-1.5">สาขาวิชา<br><span class="sub-label">Major</span></th>
                                            <th class="w-20 py-1.5">ตั้งแต่<br><span class="sub-label">From</span></th>
                                            <th class="w-20 py-1.5">ถึง<br><span class="sub-label">To</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($showEducation as $edu)
                                            <tr>
                                                <td class="p-1.5 font-semibold">{{ $edu->level ?: '-' }}</td>
                                                <td class="p-1.5">{{ $edu->institution_name ?: '-' }}</td>
                                                <td class="p-1.5">{{ $edu->faculty ? $edu->faculty . ' ' : '' }}{{ $edu->major ?: '-' }}</td>
                                                <td class="p-1.5 text-center">{{ $edu->start_year ?: '-' }}</td>
                                                <td class="p-1.5 text-center">{{ $edu->end_year ?: '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="p-2 text-center text-gray-400 italic" colspan="5">- ไม่มีข้อมูลการศึกษา -</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Section: Working Experience -->
                            <div class="space-y-2 pt-2">
                                <h4 class="font-bold text-sm text-black">Working Experience In Chronological (รายละเอียดของงานที่ผ่าน เรียงลำดับก่อน-หลัง)</h4>
                                <table class="w-full doc-table text-xs">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="w-[20%] py-1.5">สถานที่ทำงาน<br><span class="sub-label">Company</span></th>
                                            <th colspan="2" class="w-[200px] py-1">ระยะเวลา<br><span class="sub-label">TIME</span></th>
                                            <th rowspan="2" class="w-[16%] py-1.5">ตำแหน่งงาน<br><span class="sub-label">Position</span></th>
                                            <th rowspan="2" class="w-[20%] py-1.5">ลักษณะงาน<br><span class="sub-label">Job description</span></th>
                                            <th rowspan="2" class="w-[95px] min-w-[95px] py-1.5">ค่าจ้าง<br><span class="sub-label">Salary</span></th>
                                            <th rowspan="2" class="w-[16%] py-1.5">เหตุที่ออก<br><span class="sub-label">Reasons of resignation</span></th>
                                        </tr>
                                        <tr>
                                            <th class="w-[100px] py-1">เริ่ม<br><span class="sub-label">From</span></th>
                                            <th class="w-[100px] py-1">ถึง<br><span class="sub-label">To</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($showExperience as $exp)
                                            <tr>
                                                <td class="p-1.5 font-medium">{{ $exp->company_name ?: '-' }}</td>
                                                <td class="p-1.5 text-center">{{ isset($exp->start_date) && $exp->start_date ? (is_string($exp->start_date) ? $exp->start_date : $exp->start_date->format('d/m/Y')) : '-' }}</td>
                                                <td class="p-1.5 text-center">{{ isset($exp->end_date) && $exp->end_date ? (is_string($exp->end_date) ? $exp->end_date : $exp->end_date->format('d/m/Y')) : 'ปัจจุบัน' }}</td>
                                                <td class="p-1.5 font-semibold text-gray-900">{{ $exp->position ?: '-' }}</td>
                                                <td class="p-1.5">{{ $exp->job_detail ?: '-' }}</td>
                                                <td class="p-1.5 text-right font-mono">{{ isset($exp->salary) && $exp->salary ? number_format($exp->salary, 0) : '-' }}</td>
                                                <td class="p-1.5">{{ $exp->reason_for_leaving ?: '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="p-2 text-center text-gray-400 italic" colspan="7">- ไม่มีข้อมูลประวัติการทำงาน -</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- CV explanation & attached files -->
                                <div class="pt-3 space-y-3">
                                    <p class="font-bold text-xs">
                                        เอกสาร Resume / Curriculum Vitae (CV) ที่แนบมา<br>
                                        <span class="sub-label">Attached Resume or CV</span>
                                    </p>

                                    @php
                                        $resumeDocs = $allAppDocs->filter(function($d) {
                                            return str_contains(strtolower($d->document_type ?? ''), 'resume') || str_contains(strtolower($d->file_name ?? ''), 'resume');
                                        });
                                    @endphp

                                    @if($resumeDocs->isNotEmpty())
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach($resumeDocs as $rDoc)
                                                @php $rUrl = !empty($rDoc->id) ? route('recruitment.documents.show', $rDoc->id) : $resolveFilePath($rDoc->file_path); @endphp
                                                <a href="{{ $rUrl ?: 'javascript:void(0)' }}" target="_blank" rel="noopener noreferrer"
                                                    class="flex items-center justify-between p-3 bg-red-50/60 rounded-xl border border-red-200 hover:border-[#B21F24] transition-all group shadow-2xs">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <i class="fa-solid fa-file-pdf text-[#B21F24] text-xl shrink-0"></i>
                                                        <div class="min-w-0">
                                                            <p class="font-bold text-xs text-gray-800 truncate">{{ $rDoc->file_name ?? basename($rDoc->file_path) }}</p>
                                                            <p class="text-[10px] text-gray-500">คลิกเพื่อเปิดดูไฟล์ Resume</p>
                                                        </div>
                                                    </div>
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-gray-400 group-hover:text-[#B21F24] text-xs shrink-0"></i>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-400 italic bg-gray-50 p-2.5 rounded-lg border border-gray-200">ไม่มีไฟล์ Resume / CV แนบ</p>
                                    @endif

                                    @if(!empty($appUser->experience_summary))
                                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs">
                                            <span class="font-bold text-gray-700 block mb-1">รายละเอียดประสบการณ์ทำงานเพิ่มเติม:</span>
                                            <p class="text-gray-800 whitespace-pre-line">{{ $appUser->experience_summary }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Section: Language Ability -->
                            <div class="space-y-2 pt-2">
                                <h4 class="font-bold text-sm text-black">Language Ability (ภาษา)</h4>
                                <table class="w-full doc-table text-xs text-center">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="w-1/4 py-1.5 text-left pl-3">ภาษา<br><span class="sub-label">Language</span></th>
                                            <th colspan="3" class="py-1">พูด (Speaking)</th>
                                            <th colspan="3" class="py-1">เขียน (Writing)</th>
                                            <th colspan="3" class="py-1">อ่าน (Reading)</th>
                                        </tr>
                                        <tr class="text-[10px]">
                                            <th class="w-10 py-1">ดี<br><span class="sub-label">Good</span></th>
                                            <th class="w-10 py-1">ปานกลาง<br><span class="sub-label">Fair</span></th>
                                            <th class="w-10 py-1">พอใช้<br><span class="sub-label">Poor</span></th>

                                            <th class="w-10 py-1">ดี<br><span class="sub-label">Good</span></th>
                                            <th class="w-10 py-1">ปานกลาง<br><span class="sub-label">Fair</span></th>
                                            <th class="w-10 py-1">พอใช้<br><span class="sub-label">Poor</span></th>

                                            <th class="w-10 py-1">ดี<br><span class="sub-label">Good</span></th>
                                            <th class="w-10 py-1">ปานกลาง<br><span class="sub-label">Fair</span></th>
                                            <th class="w-10 py-1">พอใช้<br><span class="sub-label">Poor</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $langSkills = $appUser->language_skills ?? [];
                                        @endphp
                                        @foreach(['thai' => 'ภาษาไทย (Thai)', 'english' => 'ภาษาอังกฤษ (English)', 'other1' => 'อื่นๆ: ' . ($langSkills['other1']['name'] ?? '...'), 'other2' => 'อื่นๆ: ' . ($langSkills['other2']['name'] ?? '...')] as $lCode => $lText)
                                            @php
                                                $lData = $langSkills[$lCode] ?? [];
                                                $spk = $lData['speaking'] ?? '';
                                                $wrt = !empty($lData['writing']) ? $lData['writing'] : $spk;
                                                $rdg = !empty($lData['reading']) ? $lData['reading'] : $spk;
                                            @endphp
                                            <tr>
                                                <td class="p-1.5 text-left pl-3 font-medium">{{ $lText }}</td>
                                                <!-- Speaking -->
                                                <td class="p-1"><input type="radio" disabled {{ $spk === 'Good' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <td class="p-1"><input type="radio" disabled {{ $spk === 'Fair' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <td class="p-1"><input type="radio" disabled {{ $spk === 'Poor' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <!-- Writing -->
                                                <td class="p-1"><input type="radio" disabled {{ $wrt === 'Good' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <td class="p-1"><input type="radio" disabled {{ $wrt === 'Fair' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <td class="p-1"><input type="radio" disabled {{ $wrt === 'Poor' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <!-- Reading -->
                                                <td class="p-1"><input type="radio" disabled {{ $rdg === 'Good' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <td class="p-1"><input type="radio" disabled {{ $rdg === 'Fair' ? 'checked' : '' }} class="paper-checkbox"></td>
                                                <td class="p-1"><input type="radio" disabled {{ $rdg === 'Poor' ? 'checked' : '' }} class="paper-checkbox"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                </div>

                <!-- =========================================================================
                     DOCUMENT SHEET 4 / 5: COMPUTER & SPECIAL ABILITY & QUESTIONNAIRE
                ========================================================================== -->
                <div x-show="currentSheet === 4" class="paper-sheet-container">
                    <div class="paper-sheet pt-10 pb-8 px-6 sm:px-10 md:px-12 rounded-xl relative shadow-md">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <span class="doc-input w-36 text-center font-bold font-mono">
                                    {{ $application->application_no ?? ('APP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT)) }}
                                </span>
                            </div>
                            <div class="font-bold text-xs">4/5</div>
                        </div>

                        <div class="space-y-5 text-sm">
                            <!-- Computer Skills -->
                            <div>
                                <h4 class="font-bold text-base text-black">ความสามารถด้านคอมพิวเตอร์ (Computer skill)</h4>
                                <div class="flex items-end gap-2 mt-1">
                                    <span class="shrink-0 font-medium">Program</span>
                                    <span class="doc-input flex-grow font-semibold">{{ $appUser->computer_skills ?: '-' }}</span>
                                </div>
                            </div>

                            <!-- Special Ability Box -->
                            <div class="pt-2">
                                <h4 class="font-bold text-base text-black mb-1.5">Special Ability (ความสามารถพิเศษ)</h4>
                                <div class="border border-black p-3 space-y-1.5 text-xs sm:text-sm">
                                    <!-- Row 1: Car Driving -->
                                    <div class="grid grid-cols-1 sm:grid-cols-[330px_1fr] items-center gap-2 py-0.5">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold w-32">ขับรถยนต์ :</span>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['car_driving'] ?? '') === 'ได้' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>ได้ <span class="sub-label">Yes</span></span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['car_driving'] ?? '') === 'ไม่ได้' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>ไม่ได้ <span class="sub-label">No</span></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold">ใบขับขี่ :</span>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['car_license'] ?? '') === 'มี' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>มี <span class="sub-label">Yes</span></span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['car_license'] ?? '') === 'ไม่มี' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>ไม่มี <span class="sub-label">No</span></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Row 2: Motorcycle Driving -->
                                    <div class="grid grid-cols-1 sm:grid-cols-[330px_1fr] items-center gap-2 py-0.5">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold w-32">ขับรถจักรยานยนต์ :</span>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['motor_driving'] ?? '') === 'ได้' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>ได้ <span class="sub-label">Yes</span></span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['motor_driving'] ?? '') === 'ไม่ได้' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>ไม่ได้ <span class="sub-label">No</span></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold">ใบขับขี่ :</span>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['motor_license'] ?? '') === 'มี' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>มี <span class="sub-label">Yes</span></span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 pointer-events-none">
                                                <input type="radio" disabled {{ ($specialAbilities['motor_license'] ?? '') === 'ไม่มี' ? 'checked' : '' }} class="paper-checkbox">
                                                <span>ไม่มี <span class="sub-label">No</span></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Row 3: Office Machine -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">ความสามารถในการใช้เครื่องใช้สำนักงาน</span>
                                            <span class="sub-label">Office Machine</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $specialAbilities['office_machine'] ?? '-' }}</span>
                                    </div>

                                    <!-- Row 4: Hobbies -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">งานอดิเรก : ระบุ</span>
                                            <span class="sub-label">Hobbies Please Mention</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $specialAbilities['hobbies'] ?? '-' }}</span>
                                    </div>

                                    <!-- Row 5: Favourite Sport -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">กีฬาทีชอบ : ระบุ</span>
                                            <span class="sub-label">Favourite Sport Please Mention</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $specialAbilities['sports'] ?? '-' }}</span>
                                    </div>

                                    <!-- Row 6: Special Knowledge -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">ความรู้พิเศษ : ระบุ</span>
                                            <span class="sub-label">Special knowledge Please Mention</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $specialAbilities['special_knowledge'] ?? '-' }}</span>
                                    </div>

                                    <!-- Row 7: Others -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">อื่นๆ : ระบุ</span>
                                            <span class="sub-label">Others Please Mention</span>
                                        </div>
                                        <span class="doc-input flex-grow font-semibold">{{ $specialAbilities['others'] ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Work Up Country -->
                            <div class="flex flex-wrap items-center gap-6 pt-3">
                                <span class="font-bold shrink-0">สามารถไปปฏิบัติงานต่างจังหวัด<br><span class="sub-label">Do you can work up Country?</span></span>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($appQuestions['work_up_country'] ?? '') === 'ได้' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>ได้ <span class="sub-label">Yes</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($appQuestions['work_up_country'] ?? '') === 'ไม่ได้' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>ไม่ได้ <span class="sub-label">No</span></span>
                                </label>
                                <div class="flex items-end gap-2 flex-grow min-w-[200px]">
                                    <span class="shrink-0 font-medium">อื่นๆ ระบุ</span>
                                    <span class="doc-input flex-grow">{{ $appQuestions['work_up_country_other'] ?? '-' }}</span>
                                </div>
                            </div>

                            <!-- Source of Job Info -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold leading-tight">ทราบข่าวการรับสมัครจาก</span>
                                    <span class="sub-label">Sources of job information</span>
                                </div>
                                <span class="doc-input flex-grow">{{ $appQuestions['source_info'] ?? '-' }}</span>
                            </div>

                            <!-- Ever Applied Before -->
                            <div class="flex flex-wrap items-center gap-6 pt-2">
                                <span class="font-bold shrink-0">ท่านเคยสมัครงานกับบริษัทฯ นี้มาก่อนหรือไม่<br><span class="sub-label">Have you ever applied for employment with us before?</span></span>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($appQuestions['applied_before'] ?? '') === 'เคย' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>เคย <span class="sub-label">Yes</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($appQuestions['applied_before'] ?? 'ไม่เคย') === 'ไม่เคย' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>ไม่เคย <span class="sub-label">No</span></span>
                                </label>
                            </div>
                            <div class="flex items-end gap-2 pt-1">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium leading-tight">ถ้าเคย เมื่อไร ?</span>
                                    <span class="sub-label">If yes, When?</span>
                                </div>
                                <span class="doc-input flex-grow">{{ $appQuestions['applied_before_when'] ?? '-' }}</span>
                            </div>

                            <!-- Relatives / Friends Working Here -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold leading-tight">ระบุชื่อญาติ / เพื่อน ที่ทำงานอยู่ในบริษัทฯ ซึ่งท่านรู้จักดี</span>
                                    <span class="sub-label">Give the name of relatives / friends , working with us known to you</span>
                                </div>
                                <span class="doc-input flex-grow">{{ $appQuestions['relative_known'] ?? '-' }}</span>
                            </div>

                            <!-- Why Apply -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold leading-tight">เพราะเหตุใดท่านจึงมาสมัครงานกับบริษัท</span>
                                    <span class="sub-label">Why did you come to apply for a job at the company?</span>
                                </div>
                                <span class="doc-input flex-grow">{{ $appQuestions['reason_to_apply'] ?? '-' }}</span>
                            </div>

                            <!-- Expected Salary -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-sm leading-tight">อัตราเงินเดือนที่คาดหวัง</span>
                                    <span class="sub-label">Expected Salary</span>
                                </div>
                                <span class="doc-input flex-grow text-center font-bold text-base text-[#B21F24]">
                                    {{ $appUser->expected_salary ? number_format($appUser->expected_salary, 0) : '-' }}
                                </span>
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-sm leading-tight">บาท / เดือน</span>
                                    <span class="sub-label">baht / month</span>
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                </div>

                <!-- =========================================================================
                     DOCUMENT SHEET 5 / 5: REFERENCES & DECLARATION
                ========================================================================== -->
                <div x-show="currentSheet === 5" class="paper-sheet-container">
                    <div class="paper-sheet pt-10 pb-8 px-6 sm:px-10 md:px-12 rounded-xl relative shadow-md">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <span class="doc-input w-36 text-center font-bold font-mono">
                                    {{ $application->application_no ?? ('APP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT)) }}
                                </span>
                            </div>
                            <div class="font-bold text-xs">5/5</div>
                        </div>

                        <div class="space-y-5 text-sm">
                            <!-- References Section -->
                            <div class="space-y-2">
                                <p class="font-bold text-black text-sm leading-relaxed">
                                    เขียนชื่อ ที่อยู่ โทรศัพท์ และอาชีพของผู้ที่อ้างถึง 2 คน (ซึ่งไม่ใช่ญาติ หรือนายจ้างเดิม) ที่รู้จักคุ้นเคยตัวท่านดี<br>
                                    <span class="sub-label font-normal">List name, address, telephone and occupation of 2 references (Other than relatives or former employers) who know you</span>
                                </p>
                                <div class="flex items-end gap-2">
                                    <span class="font-bold shrink-0">1.</span>
                                    <span class="doc-input flex-grow font-semibold">
                                        {{ $refInfo['ref1']['text'] ?? (($refInfo['ref1']['name'] ?? '') . ' ' . ($refInfo['ref1']['phone'] ?? '') . ' ' . ($refInfo['ref1']['occupation'] ?? '') . ' ' . ($refInfo['ref1']['address'] ?? '')) ?: '-' }}
                                    </span>
                                </div>
                                <div class="flex items-end gap-2">
                                    <span class="font-bold shrink-0">2.</span>
                                    <span class="doc-input flex-grow font-semibold">
                                        {{ $refInfo['ref2']['text'] ?? (($refInfo['ref2']['name'] ?? '') . ' ' . ($refInfo['ref2']['phone'] ?? '') . ' ' . ($refInfo['ref2']['occupation'] ?? '') . ' ' . ($refInfo['ref2']['address'] ?? '')) ?: '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Permission to check history -->
                            <div class="flex flex-wrap items-center gap-6 pt-3 border-t border-gray-200">
                                <span class="font-bold shrink-0">ท่านอนุญาตหรือไม่หากบริษัท ฯ จะสอบประวัติการทำงานของท่าน<br><span class="sub-label">Do you have problem if company check your work history</span></span>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($refInfo['allow_check_history'] ?? 'อนุญาต') === 'อนุญาต' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>อนุญาต <span class="sub-label">Yes</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($refInfo['allow_check_history'] ?? '') === 'ไม่อนุญาต' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>ไม่อนุญาต <span class="sub-label">No</span></span>
                                </label>
                            </div>

                            <!-- Truth Questionnaire Table -->
                            <div class="pt-2">
                                <table class="w-full doc-table text-xs">
                                    <thead>
                                        <tr>
                                            <th class="text-left pl-3 py-2 w-4/5 font-bold text-gray-800">
                                                กรุณาตอบคำถามด้านล่างนี้ตามความเป็นจริง (บริษัทจะเก็บรักษาข้อมูลไว้เป็นความลับ)<br>
                                                <span class="sub-label">Please reply the truth for these questions (Company will keep for the secret)</span>
                                            </th>
                                            <th class="w-16 text-center py-2">เคย<br><span class="sub-label">Yes</span></th>
                                            <th class="w-16 text-center py-2">ไม่เคย<br><span class="sub-label">No</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเคยเป็นโรคที่สังคมไม่ยอมรับ เช่น โรคติดต่อร้ายแรง อาการป่วยทางจิต หรือโรคพิษสุราเรื้อรัง<br>
                                                <span class="sub-label">Have you ever been seriously or contracted with contagious disease, mental illness or alcoholism?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['contagious_disease'] ?? '') === 'เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['contagious_disease'] ?? 'ไม่เคย') === 'ไม่เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเคยถูกจับกุมดำเนินคดี หรือถูกคุมขังเป็นนักโทษ เนื่องจากกระทำผิดกฎหมายบ้านเมือง<br>
                                                <span class="sub-label">Have you ever been arrested or being the prisoner because of violation the law?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['arrested_or_prisoner'] ?? '') === 'เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['arrested_or_prisoner'] ?? 'ไม่เคย') === 'ไม่เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเคยถูกไล่ออกจากงานเนื่องจากกระทำผิดกฎ หรือฝ่าฝืนระเบียบข้อบังคับของบริษัทฯ<br>
                                                <span class="sub-label">Have you ever been fired from the company because of violation the company’s regulations?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['fired_from_company'] ?? '') === 'เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['fired_from_company'] ?? 'ไม่เคย') === 'ไม่เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเป็นผู้ติดหรือเคยติดยาเสพติดหรือไม่<br>
                                                <span class="sub-label">Have you ever been an addict or addicted to drugs?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['drug_addict'] ?? '') === 'เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                            <td class="p-2 text-center"><input type="radio" disabled {{ ($qAnswers['drug_addict'] ?? 'ไม่เคย') === 'ไม่เคย' ? 'checked' : '' }} class="paper-checkbox"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Drug test consent -->
                            <div class="flex flex-wrap items-center gap-6 pt-2">
                                <span class="font-bold shrink-0">คุณยินยอมให้บริษัทฯตรวจสอบสารเสพติดในร่างกายหรือไม่<br><span class="sub-label">Do you permit company to check the narcotics in your body?</span></span>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($qAnswers['permit_drug_check'] ?? 'ยินยอม') === 'ยินยอม' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>ยินยอม <span class="sub-label">Agree</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                    <input type="radio" disabled {{ ($qAnswers['permit_drug_check'] ?? '') === 'ไม่ยินยอม' ? 'checked' : '' }} class="paper-checkbox">
                                    <span>ไม่ยินยอม <span class="sub-label">Not agree</span></span>
                                </label>
                            </div>

                            <!-- Certification Statement -->
                            <div class="pt-4 border-t border-gray-300 text-center space-y-2 px-2">
                                <p class="text-[11px] leading-relaxed text-gray-800 font-semibold">
                                    ข้าพเจ้าขอรับรองว่า ข้อความดังกล่าวทั้งหมดในใบสมัครนี้เป็นความจริงทุกประการ และยินยอมให้เก็บ ใช้ เปิดเผย ตรวจสอบ ข้อมูลดังกล่าวได้ตลอดเวลาตามที่จำเป็น<br>
                                    หลังจากบริษัทจ้างเข้ามาทำงานแล้วปรากฏว่า ข้อความในใบสมัครงานเอกสารที่นำมาแสดง หรือรายละเอียดที่ให้ไว้ไม่เป็นความจริง บริษัทฯ มีสิทธิ์ที่จะเลิกจ้างข้าพเจ้าได้โดยไม่ต้องจ่ายเงินชดเชยหรือค่าเสียหายใดๆ ทั้งสิ้น
                                </p>
                                <p class="text-[9.5px] italic text-gray-500 leading-normal max-w-2xl mx-auto">
                                    I certify all statement given in this application form is true and agree company to keep, checking, sharing my detail all necessary time. If detail is found to be untrue after engagement. The Company has right to terminate my employment without any compensation or severance pay whatsoever.
                                </p>

                                <div class="pt-2">
                                    <label class="inline-flex items-center gap-2 pointer-events-none">
                                        <input type="checkbox" checked disabled class="w-4 h-4 text-[#B21F24] rounded">
                                        <span class="text-xs font-bold text-black">ข้าพเจ้าขอรับรองว่าข้อความทั้งหมดเป็นความจริง และยินยอมตามเงื่อนไขทุกประการ</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Bottom Row: Attached Documents Checklist & Signature -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pt-4 border-t border-gray-300 items-end">
                                <!-- Attached Checklist (Left) -->
                                <div class="md:col-span-7 space-y-1.5 text-[11px]">
                                    <p class="font-bold text-black">เอกสารแนบใบสมัครงาน</p>
                                    <div class="grid grid-cols-2 gap-y-1.5 gap-x-2">
                                        @php
                                            $checkList = $docsCheck ?: [];
                                        @endphp
                                        <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                            <input type="checkbox" disabled {{ in_array('สำเนาวุฒิการศึกษา', $checkList) ? 'checked' : '' }} class="paper-checkbox">
                                            <span>สำเนาวุฒิการศึกษา</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                            <input type="checkbox" disabled {{ in_array('สำเนาบัตรประชาชน', $checkList) ? 'checked' : '' }} class="paper-checkbox">
                                            <span>สำเนาบัตรประชาชน</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                            <input type="checkbox" disabled {{ in_array('สำเนาทะเบียนบ้าน', $checkList) ? 'checked' : '' }} class="paper-checkbox">
                                            <span>สำเนาทะเบียนบ้าน</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                            <input type="checkbox" disabled {{ in_array('ใบรับรองการทำงาน', $checkList) ? 'checked' : '' }} class="paper-checkbox">
                                            <span>ใบรับรองการทำงาน</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                            <input type="checkbox" disabled {{ in_array('สำเนาใบเปลี่ยนชื่อ-สกุล', $checkList) ? 'checked' : '' }} class="paper-checkbox">
                                            <span>สำเนาใบเปลี่ยนชื่อ – สกุล</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 pointer-events-none">
                                            <input type="checkbox" disabled {{ in_array('สำเนาเอกสารทางทหาร', $checkList) ? 'checked' : '' }} class="paper-checkbox">
                                            <span>สำเนาเอกสารทางทหาร</span>
                                        </label>
                                    </div>
                                    @if(!empty($appUser->attached_documents_other))
                                        <div class="flex items-end gap-1 pt-1">
                                            <span>อื่นๆ:</span>
                                            <span class="doc-input flex-grow text-[11px]">{{ $appUser->attached_documents_other }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Signature Placeholder (Right) -->
                                <div class="md:col-span-5 text-center space-y-2">
                                    <span class="doc-input w-full text-center font-bold text-gray-900 tracking-wide text-sm pb-1 block">
                                        {{ $refInfo['applicant_signature'] ?? $appUser->full_name }}
                                    </span>
                                    <p class="font-bold text-xs text-black">ลายมือชื่อผู้สมัคร<br><span class="sub-label">Applicants signature</span></p>
                                </div>
                            </div>

                            <!-- Attached Document Files (Preview / Download) -->
                            <div class="mt-6 pt-4 border-t border-dashed border-gray-300">
                                <p class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-2">
                                    <i class="fa-solid fa-folder-open text-[#B21F24]"></i> ไฟล์เอกสารแนบทั้งหมด ({{ $allAppDocs->count() }} ไฟล์):
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @forelse($allAppDocs as $doc)
                                        @php $dUrl = !empty($doc->id) ? route('recruitment.documents.show', $doc->id) : $resolveFilePath($doc->file_path); @endphp
                                        <a href="{{ $dUrl ?: 'javascript:void(0)' }}" target="_blank" rel="noopener noreferrer"
                                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200 hover:border-[#B21F24] transition-all group shadow-2xs">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <i class="fa-solid fa-file-pdf text-[#B21F24] text-lg shrink-0"></i>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-xs text-gray-800 uppercase">{{ $doc->document_type }}</p>
                                                    <p class="text-[10px] text-gray-400 truncate">{{ $doc->file_name ?? basename($doc->file_path) }}</p>
                                                </div>
                                            </div>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-gray-400 group-hover:text-[#B21F24] text-xs shrink-0"></i>
                                        </a>
                                    @empty
                                        <p class="text-xs text-gray-400 italic col-span-2">ไม่มีไฟล์เอกสารแนบเพิ่มเติม</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                </div>

                <!-- Page Navigation Controls -->
                <div class="flex items-center justify-between bg-white dark:bg-kumwell-card p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
                    <button type="button" @click="currentSheet = Math.max(1, currentSheet - 1)"
                        :disabled="currentSheet === 1"
                        :class="currentSheet === 1 ? 'opacity-40 cursor-not-allowed text-gray-400 bg-gray-100 dark:bg-gray-800' : 'text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700'"
                        class="px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> ก่อนหน้า (Sheet <span x-text="Math.max(1, currentSheet - 1)"></span>)
                    </button>

                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                        หน้า <span class="text-[#B21F24] font-black text-sm" x-text="currentSheet"></span> จาก 5
                    </span>

                    <button type="button" @click="currentSheet = Math.min(5, currentSheet + 1)"
                        :disabled="currentSheet === 5"
                        :class="currentSheet === 5 ? 'opacity-40 cursor-not-allowed text-gray-400 bg-gray-100 dark:bg-gray-800' : 'text-white bg-[#B21F24] hover:bg-red-700 shadow-sm shadow-red-500/20'"
                        class="px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition-all">
                        ถัดไป (Sheet <span x-text="Math.min(5, currentSheet + 1)"></span>) <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>

                @if($isHa)
                    <!-- Status Logs Accordion / Card (เฉพาะ HA และ Admin) -->
                    <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-slate-300 dark:border-slate-700 p-6">
                        <h3 class="text-base font-bold text-gray-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2 flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-kumwell-red"></i> ประวัติการดำเนินการ (Status Logs)
                        </h3>
                        <div class="space-y-4 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-px before:bg-gray-100 dark:before:bg-gray-800">
                            @foreach($application->statusLogs as $log)
                                <div class="pl-10 relative">
                                    <div class="absolute left-1.5 top-1 w-3 h-3 rounded-full bg-kumwell-red border-4 border-white dark:border-kumwell-card shadow-sm"></div>
                                    <div class="text-xs">
                                        <span class="font-bold text-kumwell-red uppercase">{{ $log->new_status }}</span>
                                        <span class="text-gray-400 mx-1">โดย</span>
                                        <span class="font-bold text-gray-700 dark:text-gray-200">{{ $log->user ? $log->user->name : 'System' }}</span>
                                        <span class="text-gray-400 ml-2">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                        @if($log->remark)
                                            <p class="mt-1.5 text-gray-500 bg-gray-50 dark:bg-kumwell-dark p-2.5 rounded-lg border border-gray-100 dark:border-gray-700">{{ $log->remark }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Previous Applications History (เฉพาะ HA และ Admin) -->
                    <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-slate-300 dark:border-slate-700 p-6">
                        <h3 class="text-base font-bold text-gray-800 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2 flex items-center gap-2">
                            <i class="fa-solid fa-history text-kumwell-red"></i> ประวัติการสมัครเดิมของผู้สมัครนี้
                        </h3>
                        <div class="space-y-3">
                            @forelse($history as $hist)
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-kumwell-dark rounded-xl border border-gray-100 dark:border-gray-700 hover:border-kumwell-red transition-all group">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                                {{ $hist->jobPost->position_name ?? ($hist->jobPost->jobPosition->position_name ?? '-') }}
                                            </p>
                                            <span class="text-[10px] font-mono font-semibold text-gray-500 bg-gray-200/70 dark:bg-gray-800 dark:text-gray-400 px-1.5 py-0.5 rounded">
                                                {{ $hist->application_no ?? ('APP-' . str_pad($hist->id, 5, '0', STR_PAD_LEFT)) }}
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            สมัครเมื่อ: {{ $hist->applied_at ? $hist->applied_at->translatedFormat('d/m/Y H:i') : $hist->created_at->translatedFormat('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase 
                                            @if($hist->status == 'new') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($hist->status == 'screening') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                            @elseif($hist->status == 'interview') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                            @elseif($hist->status == 'offered') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($hist->status == 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                            @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                            {{ $hist->status }}
                                        </span>
                                        <a href="{{ route('backend.recruitment.applications.show', $hist->id) }}" class="text-xs text-kumwell-red font-bold hover:underline">ดูรายละเอียด</a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 italic">ไม่มีประวัติการสมัครอื่น</p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions (Right) -->
            <div class="xl:col-span-4 space-y-6">
                <!-- ==================== CURRENT WORKFLOW ACTION CENTER ==================== -->
                <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-slate-300 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-700 pb-3">
                        <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-amber-500"></i> การดำเนินการขั้นตอนนี้
                        </h3>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-0.5 rounded-full border shadow-2xs {{ $application->status_badge_class }}">
                            <i class="{{ $application->status_icon }} text-[10px]"></i>
                            <span>{{ $application->status_label }}</span>
                        </span>
                    </div>

                    {{-- Step 1: รอคัดกรองเบื้องต้น (HA Screen) --}}
                    @if(in_array($application->status, ['submitted', 'new']))
                        <div class="space-y-3">
                            <p class="text-xs text-slate-600 dark:text-slate-400">
                                <strong>HA ตรวจสอบคุณสมบัติ:</strong> ตรวจดูประวัติและการศึกษาของผู้สมัคร หากผ่านเกณฑ์ให้ส่งต่อหัวหน้าแผนกพิจารณา
                            </p>
                            @if($isHa)
                                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="dept_review">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-blue-600/20 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer">
                                        <i class="fa-solid fa-paper-plane"></i> ผ่านคุณสมบัติ & ส่งให้หัวหน้าแผนก
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal('screening_failed', 'ระบุเหตุผลที่ไม่ผ่านคุณสมบัติ (บันทึกจัดเก็บข้อมูล)')"
                                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold py-2.5 px-4 rounded-xl transition-all text-xs flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-box-archive"></i> ไม่ผ่านคุณสมบัติ (เก็บข้อมูล)
                                </button>
                            @else
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-center text-xs text-slate-500 dark:text-slate-400 font-medium border border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-hourglass-half mr-1.5 text-blue-500"></i>
                                    อยู่ระหว่างฝ่าย <strong>HA</strong> ตรวจสอบคุณสมบัติเบื้องต้น
                                </div>
                            @endif
                        </div>

                    {{-- Step 2: ส่งหัวหน้าแผนกพิจารณา (Dept Review) --}}
                    @elseif($application->status === 'dept_review')
                        <div class="space-y-3">
                            <div class="p-3 bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-xl text-xs text-blue-900 dark:text-blue-300">
                                <i class="fa-solid fa-user-clock mr-1"></i> อยู่ระหว่างการพิจารณาโดย <strong>หัวหน้าแผนก</strong>
                            </div>
                            @if($isDeptManager || $isHa)
                                <form id="deptInterviewForm" action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="interview">
                                    <input type="hidden" name="note" value="หัวหน้าแผนกเลือกสัมภาษณ์: ส่งต่อให้ฝ่าย HA กำหนดวันเวลานัดหมายและแจ้งผู้สมัคร">
                                    <button type="button" onclick="confirmDeptInterview()"
                                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer">
                                        <i class="fa-solid fa-calendar-check"></i> หัวหน้าแผนก: เลือกสัมภาษณ์ (ส่งต่อให้ HA)
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal('dept_rejected', 'ระบุเหตุผลที่หัวหน้าแผนกส่งกลับให้ HA พิจารณาคนอื่น')"
                                    class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 dark:text-rose-300 font-bold py-2.5 px-4 rounded-xl border border-rose-200 dark:border-rose-800 transition-all text-xs flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-rotate-left"></i> ไม่ผ่าน: ส่งกลับให้ HA พิจารณาคนอื่น
                                </button>
                            @else
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-center text-xs text-slate-500 dark:text-slate-400 font-medium border border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-user-clock mr-1.5 text-blue-500"></i>
                                    รอหัวหน้าแผนกพิจารณาผู้สมัคร
                                </div>
                            @endif
                        </div>

                    {{-- Step 2 Fail: หัวหน้าแผนกส่งกลับ --}}
                    @elseif($application->status === 'dept_rejected')
                        <div class="space-y-3">
                            <div class="p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 rounded-xl text-xs text-rose-900 dark:text-rose-300">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> หัวหน้าแผนกส่งกลับ: ไม่ผ่านการคัดเลือก (ให้ HA พิจารณาผู้สมัครท่านอื่น)
                            </div>
                            @if($isHa)
                                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="dept_review">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all text-xs flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-rotate"></i> ส่งให้หัวหน้าแผนกพิจารณาอีกครั้ง
                                    </button>
                                </form>
                            @endif
                        </div>

                    {{-- Step 3: หัวหน้าแผนกส่งนัดสัมภาษณ์มา -> รอ HA กำหนดวันนัดและแจ้งผู้สมัคร --}}
                    @elseif($application->status === 'interview')
                        <div class="space-y-3">
                            <div class="p-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-900 dark:text-amber-300 space-y-1">
                                <div class="font-bold flex items-center gap-1.5 text-amber-800 dark:text-amber-200">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> หัวหน้าแผนกเลือก "นัดสัมภาษณ์" แล้ว
                                </div>
                                <p class="text-[11px] text-amber-700 dark:text-amber-300">
                                    ขั้นตอนต่อไป: ให้ฝ่าย <strong>HA กำหนดวันเวลานัดสัมภาษณ์ และส่งอีเมลแจ้งผู้สมัคร</strong>
                                </p>
                            </div>
                            @if($isHa)
                                <button type="button" onclick="openModal('scheduleModal')"
                                    class="w-full bg-kumwell-red hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-red-600/20 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer">
                                    <i class="fa-solid fa-calendar-plus"></i> HA: กำหนดวันเวลานัดสัมภาษณ์ & แจ้งผู้สมัคร
                                </button>
                            @else
                                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-center text-xs text-slate-500 dark:text-slate-400 font-medium border border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-hourglass-half mr-1.5 text-amber-500"></i>
                                    อยู่ระหว่างรอฝ่าย <strong>HA</strong> กำหนดวันเวลาและส่งอีเมลนัดหมายผู้สมัคร
                                </div>
                            @endif
                        </div>

                    {{-- Step 3 Completed: HA กำหนดวันนัดและแจ้งผู้สมัครแล้ว (Interview Scheduled) --}}
                    @elseif($application->status === 'interview_scheduled')
                        <div class="space-y-2">
                            <div class="p-3 bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 rounded-xl text-xs text-purple-900 dark:text-purple-300">
                                <i class="fa-solid fa-calendar-check mr-1 text-purple-600"></i> HA ติดต่อนัดสัมภาษณ์และส่งอีเมลแจ้งผู้สมัครเรียบร้อยแล้ว
                            </div>
                            @php
                                $lastInterview = $application->interviews->sortBy('interview_round')->last();
                                $isLastInterviewCompleted = !$lastInterview || ($lastInterview->status === 'completed' || $lastInterview->evaluation);
                                $latestScheduled = $application->interviews->where('status', 'scheduled')->last() ?? $application->interviews->last();
                                $latestScheduledData = null;
                                if ($latestScheduled) {
                                    $sInterviewerIds = $latestScheduled->interviewers->pluck('id')->toArray();
                                    if (empty($sInterviewerIds) && $latestScheduled->interviewer_id) {
                                        $sInterviewerIds = [$latestScheduled->interviewer_id];
                                    }
                                    $latestScheduledData = [
                                        'id' => $latestScheduled->id,
                                        'interview_round' => $latestScheduled->interview_round,
                                        'interview_type' => $latestScheduled->interview_type,
                                        'interview_date' => $latestScheduled->interview_date ? $latestScheduled->interview_date->format('Y-m-d') : '',
                                        'interview_time' => $latestScheduled->interview_time ? \Carbon\Carbon::parse($latestScheduled->interview_time)->format('H:i') : '',
                                        'location' => $latestScheduled->location ?? '',
                                        'meeting_link' => $latestScheduled->meeting_link ?? '',
                                        'note' => $latestScheduled->note ?? '',
                                        'interviewer_ids' => $sInterviewerIds,
                                    ];
                                }
                            @endphp
                            @php
                                $scheduledEvaluation = $latestScheduled?->evaluation 
                                    ?? \App\Models\InterviewEvaluation::where('interview_id', $latestScheduled?->id)
                                        ->orWhere(function($q) use ($application, $latestScheduled) {
                                            $q->where('application_id', $application->id)
                                              ->where('interview_times', $latestScheduled?->interview_round ?? 1);
                                        })->latest()->first();
                            @endphp

                            @if($latestScheduled)
                                @if($scheduledEvaluation && $scheduledEvaluation->status === 'pending_dept')
                                    <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-900 dark:text-amber-200 space-y-1">
                                        <div class="font-bold flex items-center gap-1.5 text-amber-800 dark:text-amber-300">
                                            <i class="fa-solid fa-clock-rotate-left"></i> HR ให้คะแนนแล้ว ({{ $scheduledEvaluation->total_hr_score }}/40)
                                        </div>
                                        <p class="text-[11px] text-amber-700 dark:text-amber-400">
                                            อยู่ระหว่างรอ <strong>หัวหน้าแผนก / ต้นสังกัด</strong> ตรวจสอบและให้คะแนนเพื่อสรุปผล
                                        </p>
                                    </div>
                                    <a href="{{ route('interview-evaluation.create', ['interview_id' => $latestScheduled->id, 'application_id' => $application->id, 'return_url' => url()->current()]) }}"
                                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-emerald-500/20 transition-all active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square"></i> @if($isDeptManager) ✍️ ต้นสังกัด: บันทึกคะแนนสัมภาษณ์ @else ✏️ เข้าดู/แก้ไขแบบประเมินผล @endif
                                    </a>
                                @elseif($scheduledEvaluation && $scheduledEvaluation->status === 'pending_hr')
                                    <div class="p-2.5 bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 rounded-xl text-xs text-blue-900 dark:text-blue-200 space-y-1">
                                        <div class="font-bold flex items-center gap-1.5 text-blue-800 dark:text-blue-300">
                                            <i class="fa-solid fa-clock-rotate-left"></i> ต้นสังกัดให้คะแนนแล้ว ({{ $scheduledEvaluation->total_dept_score }}/40)
                                        </div>
                                        <p class="text-[11px] text-blue-700 dark:text-blue-400">
                                            อยู่ระหว่างรอฝ่าย <strong>HA / ฝ่ายบุคคล</strong> ตรวจสอบและให้คะแนนเพื่อสรุปผล
                                        </p>
                                    </div>
                                    <a href="{{ route('interview-evaluation.create', ['interview_id' => $latestScheduled->id, 'application_id' => $application->id, 'return_url' => url()->current()]) }}"
                                        class="w-full bg-kumwell-red hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-red-500/20 transition-all active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square"></i> @if($isHa) ✍️ ฝ่ายบุคคล: บันทึกคะแนนสัมภาษณ์ @else ✏️ เข้าดู/แก้ไขแบบประเมินผล @endif
                                    </a>
                                @else
                                    <a href="{{ route('interview-evaluation.create', ['interview_id' => $latestScheduled->id, 'application_id' => $application->id, 'return_url' => url()->current()]) }}"
                                        class="w-full bg-kumwell-red hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-red-500/20 transition-all active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-file-signature"></i> บันทึกคะแนนแบบประเมินผลการสัมภาษณ์ (รอบที่ {{ $latestScheduled->interview_round }})
                                    </a>
                                @endif
                            @endif
                            @if($isHa)
                                @if($latestScheduledData)
                                    <button type="button" onclick="openEditInterviewModal({{ json_encode($latestScheduledData) }})"
                                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-amber-500/20 transition-all active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-clock-rotate-left"></i> ปรับเวลานัดสัมภาษณ์ (รอบที่ {{ $latestScheduled->interview_round }})
                                    </button>
                                @endif
                                {{-- นัดสัมภาษณ์รอบใหม่ จะแสดงเมื่อสัมภาษณ์และประเมินผลรอบปัจจุบันเสร็จแล้วเท่านั้น --}}
                                @if($isLastInterviewCompleted)
                                    <button type="button" onclick="openCreateInterviewModal()"
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-purple-600/20 transition-all active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-plus"></i> นัดสัมภาษณ์รอบใหม่ (รอบที่ {{ $application->interviews->count() + 1 }})
                                    </button>
                                @endif
                            @endif
                        </div>

                    {{-- Step 4: สัมภาษณ์เสร็จสิ้น / บันทึกผลแล้ว (Interview Completed) --}}
                    @elseif($application->status === 'interview_completed')
                        <div class="space-y-3">
                            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-xl text-xs text-indigo-900 dark:text-indigo-300">
                                <i class="fa-solid fa-square-poll-vertical mr-1"></i> บันทึกผลการสัมภาษณ์เรียบร้อยแล้ว รออนุมัติการคัดเลือก
                            </div>
                            @if($isDeptManager || $isHa)
                                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="passed_selection">
                                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-teal-600/20 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer">
                                        <i class="fa-solid fa-circle-check"></i> กดอนุมัติผ่านการคัดเลือก
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal('interview_failed', 'ระบุเหตุผลที่ไม่ผ่านการสัมภาษณ์ (ส่งกลับให้ HA หาคนอื่น)')"
                                    class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 dark:text-rose-300 font-bold py-2.5 px-4 rounded-xl border border-rose-200 dark:border-rose-800 transition-all text-xs flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-rotate-left"></i> ไม่ผ่าน: ส่งกลับให้ HA พิจารณาคนอื่น
                                </button>
                                @if($isHa)
                                    <button type="button" onclick="openCreateInterviewModal()"
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-md shadow-purple-600/20 transition-all active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-plus"></i> นัดสัมภาษณ์รอบใหม่ (รอบที่ {{ $application->interviews->count() + 1 }})
                                    </button>
                                @endif
                            @else
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-center text-xs text-slate-500 dark:text-slate-400 font-medium border border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-clock mr-1 text-indigo-500"></i>
                                    รอหัวหน้าแผนกหรือฝ่าย HA พิจารณาอนุมัติผลการคัดเลือก
                                </div>
                            @endif
                        </div>

                    {{-- Step 4 Fail: ไม่ผ่านสัมภาษณ์ --}}
                    @elseif($application->status === 'interview_failed')
                        <div class="space-y-3">
                            <div class="p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 rounded-xl text-xs text-rose-900 dark:text-rose-300">
                                <i class="fa-solid fa-xmark mr-1"></i> ผู้สมัครไม่ผ่านการสัมภาษณ์ (ส่งกลับให้ HA พิจารณาคนใหม่)
                            </div>
                        </div>

                    {{-- Step 5: ผ่านการคัดเลือก (รอฝ่าย HA กดยื่นข้อเสนอ) --}}
                    @elseif(in_array($application->status, ['passed_selection', 'selection_approved']))
                        <div class="space-y-3">
                            <div class="p-3 bg-teal-50 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800 rounded-xl text-xs text-teal-900 dark:text-teal-300">
                                <i class="fa-solid fa-trophy mr-1 text-teal-600"></i> ผู้สมัคร <strong>ผ่านการคัดเลือก</strong> เรียบร้อยแล้ว
                            </div>
                            @if($isHa)
                                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="offered">
                                    <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-cyan-600/25 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-paper-plane"></i> กดยื่นข้อเสนอ (ส่งอีเมลแจ้งผู้สมัครว่าผ่าน)
                                    </button>
                                </form>
                            @else
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-center text-xs text-slate-500 dark:text-slate-400 font-medium border border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-clock mr-1 text-cyan-500"></i>
                                    ผ่านการคัดเลือกแล้ว รอฝ่าย <strong>HA</strong> กดยื่นข้อเสนอและส่งอีเมลแจ้งผู้สมัคร
                                </div>
                            @endif
                        </div>

                    {{-- Step 6: ยื่นข้อเสนอ / ต่อรองข้อเสนอ (Offered) --}}
                    @elseif($application->status === 'offered')
                        <div class="space-y-3">
                            <div class="p-3 bg-cyan-50 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-800 rounded-xl text-xs text-cyan-900 dark:text-cyan-300">
                                <i class="fa-solid fa-handshake mr-1.5 text-cyan-600"></i> อยู่ในขั้นตอน <strong>ยื่นข้อเสนอ / ต่อรองข้อเสนอ (Offer)</strong>
                            </div>

                            {{-- สำหรับหัวหน้าแผนก (Department Head) --}}
                            @if($isDeptManager && !$isHa)
                                @if($application->onboarding_date)
                                    <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-xl text-xs space-y-1.5">
                                        <div class="font-bold text-indigo-900 dark:text-indigo-200 flex items-center gap-1.5">
                                            <i class="fa-solid fa-calendar-check text-indigo-600"></i> กำหนดวันเริ่มงานส่งให้ฝ่าย HA แล้ว
                                        </div>
                                        <div class="text-sm font-bold text-indigo-700 dark:text-indigo-300">
                                            วันที่: {{ $application->onboarding_date->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-indigo-600 dark:text-indigo-400">
                                            ✓ ส่งข้อมูลให้ฝ่าย HA เรียบร้อยแล้ว (รอ HA กดส่งยืนยันให้ผู้สมัคร)
                                        </div>
                                    </div>
                                    <button type="button" onclick="openModal('deptOnboardingModal')"
                                        class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/60 dark:text-indigo-300 font-semibold py-2 px-3 rounded-xl border border-indigo-200 dark:border-indigo-800 text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square"></i> แก้ไขวันเริ่มงานที่เสนอ
                                    </button>
                                @else
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-900 dark:text-amber-300 leading-relaxed">
                                        <i class="fa-solid fa-clock mr-1 text-amber-600"></i> เมื่อต่อรองข้อเสนอเรียบร้อย ให้หัวหน้าแผนกกำหนดวันทำงานเพื่อส่งให้ฝ่าย HA
                                    </div>
                                    <button type="button" onclick="openModal('deptOnboardingModal')"
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-indigo-600/25 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-calendar-days"></i> กำหนดวันทำงาน (ส่งให้ฝ่าย HA)
                                    </button>
                                @endif

                            {{-- สำหรับฝ่าย HA --}}
                            @elseif($isHa)
                                @if($application->onboarding_date)
                                    <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs space-y-1.5">
                                        <div class="font-bold text-emerald-900 dark:text-emerald-200 flex items-center gap-1.5">
                                            <i class="fa-solid fa-user-check text-emerald-600"></i> หัวหน้าแผนกกำหนดวันเริ่มงานส่งมาแล้ว
                                        </div>
                                        <div class="text-sm font-bold text-emerald-700 dark:text-emerald-300">
                                            วันที่เริ่มงาน: {{ $application->onboarding_date->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-emerald-600 dark:text-emerald-400">
                                            ฝ่าย HA ตรวจสอบความถูกต้อง แล้วกดส่งแจ้งผู้สมัครเพื่อยืนยันการรับเข้าทำงาน
                                        </div>
                                    </div>
                                    <button type="button" onclick="openModal('onboardingModal')"
                                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-emerald-600/25 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-paper-plane"></i> HA กดส่งให้ผู้สมัคร (รับเข้าทำงาน - Hired)
                                    </button>
                                @else
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-900 dark:text-amber-300 leading-relaxed">
                                        <i class="fa-solid fa-hourglass-half mr-1 text-amber-600"></i> อยู่ระหว่างรอหัวหน้าแผนกกำหนดวันทำงานส่งมาให้ HA (หรือ HA สามารถระบุวันและกดส่งให้ผู้สมัครได้ทันที)
                                    </div>
                                    <button type="button" onclick="openModal('onboardingModal')"
                                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 text-sm cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-calendar-check"></i> กำหนดวันทำงานและกดส่งให้ผู้สมัคร (Hired)
                                    </button>
                                @endif

                            {{-- ผู้ใช้อื่นๆ --}}
                            @else
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-center text-xs text-slate-500 dark:text-slate-400 font-medium border border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-handshake mr-1.5 text-cyan-500"></i>
                                    อยู่ระหว่างหัวหน้าแผนกและฝ่าย HA กำหนดวันเริ่มงานและส่งแจ้งผู้สมัคร
                                </div>
                            @endif
                        </div>

                    {{-- Step 7: รับเข้าทำงานเรียบร้อย (Hired) --}}
                    @elseif($application->status === 'hired')
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-center space-y-2">
                            <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl mx-auto shadow-md shadow-emerald-600/20">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <h4 class="font-bold text-emerald-900 dark:text-emerald-200 text-sm">รับเข้าทำงานเสร็จสมบูรณ์</h4>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400">
                                วันที่เริ่มงาน: <strong>{{ $application->onboarding_date ? $application->onboarding_date->format('d/m/Y') : 'ยังไม่ได้ระบุ' }}</strong>
                            </p>
                            @if($isHa)
                                <button type="button" onclick="openModal('onboardingModal')" class="text-xs text-emerald-700 dark:text-emerald-400 underline font-semibold mt-1 inline-block cursor-pointer">
                                    แก้ไขวันเริ่มงาน
                                </button>
                            @endif
                        </div>

                    {{-- Rejection / Other --}}
                    @else
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-600 dark:text-slate-400">
                            สถานะปัจจุบัน: <strong>{{ $application->status_label }}</strong>
                        </div>
                    @endif

                    <!-- Collapsible Manual Status Form (เฉพาะฝ่าย HA เท่านั้น) -->
                    @if(Auth::check() && Auth::user()->isHrOrAdmin())
                        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <details class="group">
                                <summary class="text-xs font-semibold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer flex items-center justify-between select-none">
                                    <span><i class="fa-solid fa-sliders mr-1"></i> ปรับเปลี่ยนสถานะด้วยตนเอง (Manual - เฉพาะ HA)</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] group-open:rotate-180 transition-transform"></i>
                                </summary>
                                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST" class="space-y-3 mt-3">
                                    @csrf
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">เลือกสถานะ</label>
                                        <select name="status" class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3 py-2 text-xs" required>
                                            <option value="submitted" {{ in_array($application->status, ['submitted', 'new']) ? 'selected' : '' }}>1. รอคัดกรองเบื้องต้น (Submitted)</option>
                                            <option value="screening_failed" {{ $application->status == 'screening_failed' ? 'selected' : '' }}>2. ไม่ผ่านคุณสมบัติ (Screening Failed)</option>
                                            <option value="dept_review" {{ $application->status == 'dept_review' ? 'selected' : '' }}>3. ส่งหัวหน้าแผนกพิจารณา (Dept Review)</option>
                                            <option value="dept_rejected" {{ $application->status == 'dept_rejected' ? 'selected' : '' }}>4. หัวหน้าแผนกส่งกลับ (Dept Rejected)</option>
                                            <option value="interview" {{ $application->status == 'interview' ? 'selected' : '' }}>5. รอ HA กำหนดวันนัดสัมภาษณ์ (Pending Schedule)</option>
                                            <option value="interview_scheduled" {{ $application->status == 'interview_scheduled' ? 'selected' : '' }}>5. นัดสัมภาษณ์แล้ว (Interview Scheduled)</option>
                                            <option value="interview_completed" {{ $application->status == 'interview_completed' ? 'selected' : '' }}>6. สัมภาษณ์เสร็จสิ้น (Interview Completed)</option>
                                            <option value="interview_failed" {{ $application->status == 'interview_failed' ? 'selected' : '' }}>7. ไม่ผ่านสัมภาษณ์ (Interview Failed)</option>
                                            <option value="passed_selection" {{ in_array($application->status, ['passed_selection', 'selection_approved']) ? 'selected' : '' }}>8. ผ่านการคัดเลือก (Selection Passed)</option>
                                            <option value="offered" {{ $application->status == 'offered' ? 'selected' : '' }}>9. แจ้งผล/ยื่นข้อเสนอ (Offered)</option>
                                            <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>10. รับเข้าทำงาน (Hired)</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">หมายเหตุ</label>
                                        <textarea name="note" rows="2" class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3 py-2 text-xs" placeholder="ระบุเหตุผล..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 rounded-xl text-xs transition-all">
                                        บันทึกสถานะ
                                    </button>
                                </form>
                            </details>
                        </div>
                    @endif
                </div>

                <!-- Interview Management -->
                <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-slate-300 dark:border-slate-700 p-8">
                    <div class="flex justify-between items-center mb-6 border-b border-slate-200 dark:border-slate-700 pb-2">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-kumwell-red"></i> การสัมภาษณ์
                        </h3>
                        @if($isHa)
                            @php
                                $lastInterviewForHeader = $application->interviews->sortBy('interview_round')->last();
                                $canScheduleFromHeader = !$lastInterviewForHeader || ($lastInterviewForHeader->status === 'completed' || $lastInterviewForHeader->evaluation);
                            @endphp
                            @if($canScheduleFromHeader)
                                <button onclick="openCreateInterviewModal()" 
                                    class="text-xs font-bold text-kumwell-red hover:text-red-700 underline transition-all cursor-pointer">
                                    + นัดสัมภาษณ์
                                </button>
                            @endif
                        @endif
                    </div>

                    <div class="space-y-4">
                        @forelse($application->interviews as $interview)
                            <div class="p-4 bg-gray-50 dark:bg-kumwell-dark rounded-xl border border-slate-300 dark:border-slate-700 group">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="text-[10px] font-bold text-kumwell-red uppercase px-2 py-0.5 bg-red-50 dark:bg-red-500/10 rounded-full">รอบที่ {{ $interview->interview_round }}</span>
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200 ml-2">
                                            {{ $interview->interview_date->format('d/m/Y') }} @ {{ \Carbon\Carbon::parse($interview->interview_time)->format('H:i') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $interview->status == 'completed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                            {{ $interview->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
                                    <p><i class="fa-solid fa-user-tie mr-1"></i> ผู้สัมภาษณ์: 
                                        @if($interview->interviewers->count() > 0)
                                            {{ $interview->interviewers->pluck('fullname')->join(', ') }}
                                        @else
                                            {{ $interview->interviewer->fullname ?? 'ไม่ระบุ' }}
                                        @endif
                                    </p>
                                    <p><i class="fa-solid fa-location-dot mr-1"></i> {{ $interview->interview_type }} 
                                        @if($interview->location) - {{ $interview->location }} @endif
                                    </p>
                                    @if($interview->meeting_link)
                                        <a href="{{ $interview->meeting_link }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline"><i class="fa-solid fa-video mr-1"></i> ลิงก์ประชุมออนไลน์</a>
                                    @endif
                                </div>

                                @php
                                    $cardEvaluation = $interview->evaluation 
                                        ?? \App\Models\InterviewEvaluation::where('interview_id', $interview->id)
                                            ->orWhere(function($q) use ($application, $interview) {
                                                $q->where('application_id', $application->id)
                                                  ->where('interview_times', $interview->interview_round);
                                            })->latest()->first();
                                @endphp

                                @if($interview->status == 'scheduled' && !$cardEvaluation)
                                    @php
                                        $cardInterviewerIds = $interview->interviewers->pluck('id')->toArray();
                                        if (empty($cardInterviewerIds) && $interview->interviewer_id) {
                                            $cardInterviewerIds = [$interview->interviewer_id];
                                        }
                                        $cardInterviewData = [
                                            'id' => $interview->id,
                                            'interview_round' => $interview->interview_round,
                                            'interview_type' => $interview->interview_type,
                                            'interview_date' => $interview->interview_date ? $interview->interview_date->format('Y-m-d') : '',
                                            'interview_time' => $interview->interview_time ? \Carbon\Carbon::parse($interview->interview_time)->format('H:i') : '',
                                            'location' => $interview->location ?? '',
                                            'meeting_link' => $interview->meeting_link ?? '',
                                            'note' => $interview->note ?? '',
                                            'interviewer_ids' => $cardInterviewerIds,
                                        ];
                                    @endphp
                                    <div class="mt-4 flex gap-2">
                                        <a href="{{ route('interview-evaluation.create', ['interview_id' => $interview->id, 'application_id' => $application->id, 'return_url' => url()->current()]) }}" 
                                            class="flex-1 bg-kumwell-red hover:bg-red-700 text-white text-[11px] font-bold py-2 px-3 rounded-lg transition-all shadow-md shadow-red-500/20 cursor-pointer flex items-center justify-center gap-1.5 text-center">
                                            <i class="fa-solid fa-file-signature text-xs"></i>
                                            <span>บันทึกคะแนน</span>
                                        </a>
                                        @if($isHa)
                                            <button type="button" onclick="openEditInterviewModal({{ json_encode($cardInterviewData) }})"
                                                class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold rounded-lg transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer"
                                                title="ปรับเวลานัดสัมภาษณ์รอบนี้">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                                <span>ปรับเวลานัด</span>
                                            </button>
                                            <form action="{{ route('backend.recruitment.interviews.update-status', $interview->id) }}" method="POST" class="flex-none">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors border border-gray-100 dark:border-gray-700 rounded-lg cursor-pointer" title="ยกเลิกนัดหมาย">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @elseif($cardEvaluation)
                                    <div class="mt-4 pt-3.5 border-t border-gray-100 dark:border-gray-700 space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                                                <i class="fa-solid fa-clipboard-check text-emerald-500"></i> ผลประเมินสัมภาษณ์ (QF-HR-15)
                                            </span>
                                            @if($cardEvaluation->status === 'pending_dept')
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400">
                                                    ⏳ รอต้นสังกัดประเมิน
                                                </span>
                                            @elseif($cardEvaluation->status === 'pending_hr')
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400">
                                                    ⏳ รอฝ่ายบุคคลประเมิน
                                                </span>
                                            @elseif($cardEvaluation->summary_result === 'hire')
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                                                    ✓ ควรว่าจ้าง
                                                </span>
                                            @elseif($cardEvaluation->summary_result === 'reserve')
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400">
                                                    สำรอง
                                                </span>
                                            @elseif($cardEvaluation->summary_result === 'reject')
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400">
                                                    ปฏิเสธ
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-3 gap-1.5 text-center">
                                            <div class="p-2 bg-white dark:bg-kumwell-card rounded-lg border border-gray-100 dark:border-gray-800">
                                                <p class="text-[8px] text-gray-400 uppercase">HR</p>
                                                <p class="text-xs font-bold {{ $cardEvaluation->total_hr_score !== null ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400 italic' }}">
                                                    {{ $cardEvaluation->total_hr_score !== null ? $cardEvaluation->total_hr_score . '/40' : 'รอประเมิน' }}
                                                </p>
                                            </div>
                                            <div class="p-2 bg-white dark:bg-kumwell-card rounded-lg border border-gray-100 dark:border-gray-800">
                                                <p class="text-[8px] text-gray-400 uppercase">ต้นสังกัด</p>
                                                <p class="text-xs font-bold {{ $cardEvaluation->total_dept_score !== null ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400 italic' }}">
                                                    {{ $cardEvaluation->total_dept_score !== null ? $cardEvaluation->total_dept_score . '/40' : 'รอประเมิน' }}
                                                </p>
                                            </div>
                                            <div class="p-2 bg-white dark:bg-kumwell-card rounded-lg border border-gray-100 dark:border-gray-800">
                                                <p class="text-[8px] text-gray-400 uppercase">เฉลี่ย</p>
                                                <p class="text-xs font-bold {{ $cardEvaluation->average_score ? 'text-kumwell-red' : 'text-gray-400 italic' }}">
                                                    {{ $cardEvaluation->average_score ? $cardEvaluation->average_score . '/40' : '-' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1.5 pt-1">
                                            @if(in_array($cardEvaluation->status, ['pending_dept', 'pending_hr']))
                                                <a href="{{ route('interview-evaluation.create', ['interview_id' => $interview->id, 'application_id' => $application->id, 'return_url' => url()->current()]) }}"
                                                    class="flex-1 text-center bg-kumwell-red hover:bg-red-700 text-white text-[11px] font-bold py-2 px-3 rounded-lg transition-all shadow-md shadow-red-500/20 flex items-center justify-center gap-1.5">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                    <span>{{ $cardEvaluation->status === 'pending_dept' ? 'ต้นสังกัด: บันทึกคะแนนต่อ' : 'ฝ่ายบุคคล: บันทึกคะแนนต่อ' }}</span>
                                                </a>
                                                <a href="{{ route('interview-evaluation.show', $cardEvaluation->id) }}" target="_blank"
                                                    class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-[10px] font-semibold rounded-lg transition" title="ดูแบบร่าง">
                                                    <i class="fa-solid fa-file-lines"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('interview-evaluation.show', $cardEvaluation->id) }}" target="_blank"
                                                    class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-[10px] font-semibold py-1.5 px-2 rounded-lg transition">
                                                    <i class="fa-solid fa-file-lines mr-1"></i> ดูแบบประเมิน
                                                </a>
                                                <a href="{{ route('interview-evaluation.pdf', $cardEvaluation->id) }}" target="_blank"
                                                    class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/40 dark:text-red-400 text-[10px] font-semibold rounded-lg transition" title="ดาวน์โหลด PDF">
                                                    <i class="fa-solid fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('interview-evaluation.create', ['interview_id' => $interview->id, 'application_id' => $application->id, 'return_url' => url()->current()]) }}"
                                                    class="px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-[10px] font-semibold rounded-lg transition border border-gray-200 dark:border-gray-700" title="ประเมินเพิ่มเติม / ปรับปรุง">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($interview->status == 'completed' && $interview->scores->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">สรุปคะแนน</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            @php $avg = $interview->scores->avg('score'); @endphp
                                            <div class="p-2 bg-white dark:bg-kumwell-card rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                                                <p class="text-[8px] text-gray-400 uppercase">Average</p>
                                                <p class="text-sm font-bold text-kumwell-red">{{ number_format($avg, 1) }}/10</p>
                                            </div>
                                            <div class="p-2 bg-white dark:bg-kumwell-card rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                                                <p class="text-[8px] text-gray-400 uppercase">Criteria</p>
                                                <p class="text-sm font-bold text-gray-700 dark:text-white">{{ $interview->scores->count() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-8 text-center bg-gray-50 dark:bg-kumwell-dark rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                                <i class="fa-solid fa-calendar-days text-gray-300 text-3xl mb-2"></i>
                                <p class="text-xs text-gray-400 font-medium">ยังไม่มีรายการนัดหมาย</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Interview Modal -->
    <div id="scheduleModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('scheduleModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-2xl shadow-2xl w-full max-w-xl transform transition-all animate-modal-in overflow-hidden my-6">
                <div class="bg-kumwell-red px-6 py-3.5 text-white flex justify-between items-center">
                    <h3 id="scheduleModalTitle" class="text-base font-bold flex items-center gap-2.5">
                        <i id="scheduleModalIcon" class="fa-solid fa-calendar-plus text-sm"></i>
                        <span id="scheduleModalText">นัดสัมภาษณ์งาน</span>
                    </h3>
                    <button onclick="closeModal('scheduleModal')" class="text-white/80 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <form id="scheduleForm" action="{{ route('backend.recruitment.interviews.store', $application->id) }}" method="POST" class="p-5 sm:p-6 space-y-3.5">
                    @csrf
                    <input type="hidden" name="interview_id" id="modal_interview_id" value="">
                    
                    <!-- Row 1: รอบที่, รูปแบบ, วันที่, เวลา (4 columns on md) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">รอบที่</label>
                            <div class="flex items-center bg-gray-50 dark:bg-kumwell-dark rounded-xl p-0.5 border border-gray-100 dark:border-gray-800">
                                <button type="button" onclick="adjustRound(-1)" 
                                    class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white dark:hover:bg-kumwell-card hover:shadow-sm text-gray-500 transition-all active:scale-90">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                                <input type="number" id="interview_round" name="interview_round" 
                                    value="{{ $application->interviews->count() + 1 }}" required readonly
                                    class="w-full bg-transparent border-none text-center font-bold text-xs focus:ring-0 p-0">
                                <button type="button" onclick="adjustRound(1)" 
                                    class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white dark:hover:bg-kumwell-card hover:shadow-sm text-kumwell-red transition-all active:scale-90">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">รูปแบบ</label>
                            <select name="interview_type" id="modal_interview_type" required
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                                <option value="Online">Online</option>
                                <option value="On-site">On-site (บริษัท)</option>
                                <option value="Phone">Phone</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">วันที่</label>
                            <div class="relative">
                                <input type="text" id="interview_date" name="interview_date" placeholder="เลือกวันที่..." required readonly
                                    onclick="openDatePicker()"
                                    class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3 py-2 pr-7 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all cursor-pointer">
                                <i class="fa-regular fa-calendar absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">เวลา</label>
                            <div class="relative">
                                <select id="interview_time" name="interview_time" required onchange="handleTimeChange(this)"
                                    class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all cursor-pointer">
                                    <option value="">เลือกเวลา...</option>
                                    <optgroup label="ช่วงเช้า (Morning)">
                                        <option value="08:30">08:30 น.</option>
                                        <option value="09:00">09:00 น.</option>
                                        <option value="09:30">09:30 น.</option>
                                        <option value="10:00">10:00 น.</option>
                                        <option value="10:30">10:30 น.</option>
                                        <option value="11:00">11:00 น.</option>
                                        <option value="11:30">11:30 น.</option>
                                    </optgroup>
                                    <optgroup label="ช่วงบ่าย (Afternoon)">
                                        <option value="13:00">13:00 น.</option>
                                        <option value="13:30">13:30 น.</option>
                                        <option value="14:00">14:00 น.</option>
                                        <option value="14:30">14:30 น.</option>
                                        <option value="15:00">15:00 น.</option>
                                        <option value="15:30">15:30 น.</option>
                                        <option value="16:00">16:00 น.</option>
                                        <option value="16:30">16:30 น.</option>
                                        <option value="17:00">17:00 น.</option>
                                    </optgroup>
                                    <option value="custom">ระบุเวลาอื่น...</option>
                                </select>
                            </div>
                            <div id="custom_time_container" class="hidden mt-1 relative">
                                <input type="time" id="custom_time_input"
                                    class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all"
                                    placeholder="เลือกเวลา">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: ผู้สัมภาษณ์ -->
                    <div class="space-y-1 relative" id="interviewer_dropdown_container">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">ผู้สัมภาษณ์ (เลือกได้หลายคน)</label>
                        
                        <!-- Trigger button showing selected items or placeholder -->
                        <div id="interviewer_dropdown_btn" onclick="toggleInterviewerDropdown()"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark rounded-xl px-3.5 py-2 text-xs flex items-center justify-between cursor-pointer border border-transparent hover:border-gray-200 dark:hover:border-gray-700 transition-all min-h-[38px]">
                            <div id="interviewer_selected_labels" class="flex flex-wrap gap-1.5 items-center flex-1 mr-2">
                                <span class="text-gray-400 text-xs">คลิกเพื่อเลือกผู้สัมภาษณ์...</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-[10px] transition-transform duration-200" id="interviewer_arrow"></i>
                        </div>

                        <!-- Dropdown Menu with Search and Checkboxes -->
                        <div id="interviewer_dropdown_menu" 
                            class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white dark:bg-kumwell-card rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-[80] overflow-hidden">
                            <!-- Search & Department Filter inside dropdown -->
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-kumwell-dark/30 space-y-1.5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    <!-- Search Input -->
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-gray-400 text-[11px]"></i>
                                        <input type="text" id="interviewer_search_input" oninput="filterInterviewers()" placeholder="ค้นหาชื่อ..."
                                            class="w-full bg-white dark:bg-kumwell-card border border-gray-200 dark:border-gray-700 rounded-lg pl-7 pr-2 py-1 text-xs focus:ring-1 focus:ring-kumwell-red focus:border-kumwell-red outline-none">
                                    </div>
                                    <!-- Department Filter Dropdown -->
                                    <div>
                                        <select id="interviewer_dept_filter" onchange="filterInterviewers()"
                                            class="w-full bg-white dark:bg-kumwell-card border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1 text-xs text-gray-700 dark:text-gray-200 focus:ring-1 focus:ring-kumwell-red focus:border-kumwell-red outline-none">
                                            <option value="">-- ทุกแผนก --</option>
                                            @php
                                                $departments = $interviewers->map(function($u) {
                                                    return $u->department->department_fullname ?? null;
                                                })->filter()->unique()->sort();
                                            @endphp
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept }}">{{ $dept }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- List of Interviewers with Checkboxes -->
                            <div class="max-h-48 overflow-y-auto p-1.5 space-y-0.5" id="interviewer_options_list">
                                @foreach($interviewers as $user)
                                    <label class="interviewer-option flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-kumwell-dark cursor-pointer transition-colors text-xs select-none"
                                        data-name="{{ mb_strtolower($user->fullname . ' ' . ($user->department->department_fullname ?? '')) }}"
                                        data-dept="{{ $user->department->department_fullname ?? '' }}">
                                        <input type="checkbox" name="interviewer_ids[]" value="{{ $user->id }}" 
                                            data-label="{{ $user->fullname }}"
                                            onchange="updateInterviewerDisplay()"
                                            class="rounded border-gray-300 dark:border-gray-600 text-kumwell-red focus:ring-kumwell-red focus:ring-offset-0 w-3.5 h-3.5">
                                        <div class="flex-1 flex items-center justify-between">
                                            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $user->fullname }}</span>
                                            <span class="text-[10px] text-gray-400">({{ $user->department->department_fullname ?? '-' }})</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: สถานที่ / Meeting Link (2 columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">สถานที่ / ปลายทาง</label>
                            <input type="text" name="location" id="interview_location" placeholder="ระบุห้องประชุม หรือ ปลายทาง"
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Meeting Link (Zoom/Meet)</label>
                            <input type="url" name="meeting_link" id="interview_meeting_link" placeholder="https://meet.google.com/..."
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                        </div>
                    </div>

                    <!-- Row 4: บันทึกเพิ่มเติม -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">บันทึกเพิ่มเติม</label>
                        <textarea name="note" id="interview_note" rows="2" placeholder="ระบุรายละเอียดเพิ่มเติมถึงผู้สัมภาษณ์..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all resize-none"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="scheduleSubmitBtn"
                            class="w-full bg-kumwell-red hover:bg-red-700 text-white font-bold py-2.5 rounded-xl shadow-md shadow-red-500/25 transition-all active:scale-95 text-sm">
                            ยืนยันการนัดหมาย
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('emailModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-md transform transition-all animate-modal-in">
                <div class="bg-v-red p-6 text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-paper-plane"></i> ส่งอีเมลถึงผู้สมัคร
                    </h3>
                    <button onclick="closeModal('emailModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form id="emailForm" action="{{ route('backend.recruitment.applications.send-email', $application->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">หัวข้ออีเมล</label>
                        <input type="text" name="subject" required value="เรื่อง: ข้อมูลเพิ่มเติมเกี่ยวกับการสมัครงาน"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">ข้อความ</label>
                        <textarea name="content" rows="6" required placeholder="พิมพ์ข้อความที่ต้องการส่งถึงผู้สมัคร..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">แนบไฟล์ (ถ้ามี)</label>
                        <input type="file" name="attachments[]" multiple
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-kumwell-red/10 file:text-kumwell-red hover:file:bg-kumwell-red/20">
                    </div>

                    <div class="pt-4">
                        <button type="submit" id="emailSubmitBtn"
                            class="w-full bg-v-red hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-red-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <span>ส่งอีเมลทันที</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Score Recording Modal -->
    <div id="scoreModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('scoreModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-lg transform transition-all animate-modal-in">
                <div class="bg-gray-800 p-6 text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-star text-yellow-500"></i> บันทึกผลการสัมภาษณ์
                    </h3>
                    <button onclick="closeModal('scoreModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form id="scoreForm" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-4">
                        @php 
                            $criterias = ['บุคลิกภาพและการพูด', 'ความรู้ในตำแหน่งงาน', 'ทัศนคติ/Mindset', 'ความเหมาะสมกับวัฒนธรรมองค์กร', 'ความสามารถด้านภาษา/ทักษะพิเศษ'];
                        @endphp
                        
                        @foreach($criterias as $index => $criteria)
                            <div class="space-y-3 p-4 bg-gray-50 dark:bg-kumwell-dark rounded-2xl border border-gray-100 dark:border-gray-800">
                                <div class="flex justify-between items-center">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ $criteria }}</label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-gray-400 font-medium">คะแนน:</span>
                                        <span id="score_display_{{ $index }}" class="text-sm font-bold text-kumwell-red">5</span>
                                    </div>
                                </div>
                                <input type="hidden" name="scores[{{ $index }}][criteria]" value="{{ $criteria }}">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="adjustSlider({{ $index }}, -1)"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-kumwell-card shadow-sm border border-gray-100 dark:border-gray-800 text-gray-400 hover:text-kumwell-red transition-all active:scale-90">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    
                                    <input type="range" id="score_slider_{{ $index }}" name="scores[{{ $index }}][score]" min="0" max="10" value="5"
                                        oninput="document.getElementById('score_display_{{ $index }}').innerText = this.value"
                                        class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-kumwell-red">
                                    
                                    <button type="button" onclick="adjustSlider({{ $index }}, 1)"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-kumwell-card shadow-sm border border-gray-100 dark:border-gray-800 text-gray-400 hover:text-kumwell-red transition-all active:scale-90">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <input type="text" name="scores[{{ $index }}][comment]" placeholder="ความคิดเห็นเพิ่มเติม..."
                                    class="w-full bg-white dark:bg-kumwell-card border-none rounded-xl px-3 py-2 text-[10px] focus:ring-1 focus:ring-kumwell-red/20 transition-all">
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-2xl shadow-xl transition-all active:scale-95">
                            บันทึกคะแนนทั้งหมด
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Department Head Onboarding Date Modal -->
    <div id="deptOnboardingModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('deptOnboardingModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-md transform transition-all animate-modal-in">
                <div class="bg-indigo-600 p-6 text-white flex justify-between items-center rounded-t-3xl">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-calendar-days"></i> กำหนดวันทำงาน (ส่งให้ฝ่าย HA)
                    </h3>
                    <button onclick="closeModal('deptOnboardingModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <input type="hidden" name="status" value="offered">
                    
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-xl text-xs text-indigo-900 dark:text-indigo-300 leading-relaxed">
                        <i class="fa-solid fa-circle-info mr-1 text-indigo-600"></i>
                        ระบุวันที่ต้องการให้ผู้สมัครเริ่มปฏิบัติงาน จากนั้นระบบจะส่งข้อมูลวันเริ่มงานไปยังฝ่าย HA เพื่อตรวจสอบและกดส่งแจ้งผู้สมัครอย่างเป็นทางการ
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">ระบุวันเริ่มงานที่ต้องการ (Start Date) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" id="dept_onboarding_date" name="onboarding_date" required min="{{ date('Y-m-d') }}" value="{{ $application->onboarding_date ? $application->onboarding_date->format('Y-m-d') : '' }}"
                                placeholder="เลือกวันเริ่มงาน..." readonly
                                class="datepicker-th w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all font-semibold cursor-pointer">
                            <i class="fa-regular fa-calendar absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">บันทึกข้อสรุปการต่อรอง / เงื่อนไขเพิ่มเติม</label>
                        <textarea name="note" rows="3" placeholder="ระบุรายละเอียด เช่น ตกลงเริ่มงานวันจันทร์, อุปกรณ์หรือคอมพิวเตอร์ที่ต้องจัดเตรียม..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-500/30 transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-paper-plane"></i> บันทึกวันทำงานและส่งให้ฝ่าย HA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- HA Onboarding Confirm & Send to Applicant Modal -->
    <div id="onboardingModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('onboardingModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-md transform transition-all animate-modal-in">
                <div class="bg-emerald-600 p-6 text-white flex justify-between items-center rounded-t-3xl">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-calendar-check"></i> กำหนดวันเริ่มงานและส่งแจ้งผู้สมัคร (Hired)
                    </h3>
                    <button onclick="closeModal('onboardingModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <input type="hidden" name="status" value="hired">
                    
                    @if($application->onboarding_date)
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-900 dark:text-emerald-300">
                            <i class="fa-solid fa-user-tie mr-1 text-emerald-600"></i>
                            หัวหน้าแผนกเสนอวันเริ่มงาน: <strong>{{ $application->onboarding_date->format('d/m/Y') }}</strong>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">ยืนยันวันเริ่มงาน (Onboarding Date) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" id="onboarding_date" name="onboarding_date" required value="{{ $application->onboarding_date ? $application->onboarding_date->format('Y-m-d') : date('Y-m-d') }}"
                                placeholder="เลือกวันเริ่มงาน..." readonly
                                class="datepicker-th w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 pr-10 text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer">
                            <i class="fa-regular fa-calendar absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">บันทึกเพิ่มเติม / เงื่อนไข</label>
                        <textarea name="note" rows="3" placeholder="ระบุรายละเอียด เช่น แผนกที่สังกัด, เอกสารที่ต้องนำมาในวันแรก..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all"></textarea>
                    </div>

                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-900 dark:text-amber-300 flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-amber-600 text-sm"></i>
                        <span>เมื่อกดส่ง ระบบจะส่งอีเมลแจ้งผลการรับเข้าทำงานพร้อมวันเริ่มงานไปยังผู้สมัครทันที</span>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-emerald-500/30 transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-circle-check"></i> ยืนยันรับเข้าทำงานและส่งแจ้งผู้สมัคร (Hired)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection / Send-Back Modal -->
    <div id="rejectReasonModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('rejectReasonModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-md transform transition-all animate-modal-in">
                <div class="bg-rose-600 p-6 text-white flex justify-between items-center rounded-t-3xl">
                    <h3 class="text-lg font-bold flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation"></i> บันทึกผลการพิจารณา
                    </h3>
                    <button onclick="closeModal('rejectReasonModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <input type="hidden" id="reject_target_status" name="status" value="rejected">
                    
                    <div class="space-y-2">
                        <label id="reject_modal_label" class="text-xs font-bold text-gray-700 dark:text-gray-200">ระบุเหตุผลและข้อเสนอแนะ</label>
                        <textarea id="reject_modal_note" name="note" rows="4" required placeholder="พิมพ์เหตุผลหรือข้อเสนอแนะที่ส่งกลับ..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-500/20 transition-all"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 rounded-2xl shadow-xl shadow-rose-500/30 transition-all active:scale-95 flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-paper-plane"></i> ยืนยันการบันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal-in {
            animation: modal-in 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    <script>
        let fpDate = null;

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

        function initThaiDatepickers() {
            if (typeof flatpickr === 'undefined') return;

            if (flatpickr.l10ns && flatpickr.l10ns.th) {
                flatpickr.localize(flatpickr.l10ns.th);
            }
            const thLocale = (flatpickr.l10ns && flatpickr.l10ns.th) ? flatpickr.l10ns.th : 'th';

            // 1. Schedule Interview date picker
            const dateInput = document.getElementById('interview_date');
            if (dateInput && !fpDate) {
                try {
                    fpDate = flatpickr(dateInput, {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d/m/Y",
                        defaultDate: dateInput.value || null,
                        altInputClass: "w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-3 py-2 pr-7 text-xs focus:ring-2 focus:ring-kumwell-red/20 transition-all cursor-pointer font-medium text-gray-700 dark:text-gray-200",
                        locale: thLocale,
                        disableMobile: true,
                        clickOpens: true,
                        allowInput: false,
                        static: false,
                        position: "auto",
                        appendTo: document.getElementById('scheduleModal'),
                        parseDate: function(dateStr, formatStr) {
                            if (typeof dateStr === 'string' && dateStr.includes('/')) {
                                const parts = dateStr.split('/');
                                if (parts.length === 3) {
                                    let day = parseInt(parts[0], 10);
                                    let month = parseInt(parts[1], 10) - 1;
                                    let year = parseInt(parts[2], 10);
                                    if (year > 2400) year -= 543;
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
                                if (year < 2400) year += 543;
                                return day + '/' + month + '/' + year;
                            }
                            return flatpickr.formatDate(date, formatStr, locale);
                        },
                        onReady: function(selectedDates, dateStr, instance) {
                            formatHeaderBuddhistYear(instance);
                        },
                        onMonthChange: function(selectedDates, dateStr, instance) { formatHeaderBuddhistYear(instance); },
                        onYearChange: function(selectedDates, dateStr, instance) { formatHeaderBuddhistYear(instance); },
                        onOpen: function(selectedDates, dateStr, instance) { formatHeaderBuddhistYear(instance); }
                    });
                } catch (e) {
                    console.warn('Flatpickr init warning (interview_date):', e);
                    fallbackNativeDate(dateInput);
                }
            }

            // 2. Onboarding modals & all other .datepicker-th inputs
            document.querySelectorAll('.datepicker-th').forEach(function(el) {
                if (el._flatpickr) return;
                try {
                    const minD = el.getAttribute('min') || null;
                    const defaultD = el.value || null;
                    const ringColorClass = el.classList.contains('focus:ring-indigo-500/20') ? 'focus:ring-indigo-500/20' : 'focus:ring-emerald-500/20';
                    const modalParent = el.closest('#onboardingModal') || el.closest('#deptOnboardingModal') || null;

                    flatpickr(el, {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d/m/Y",
                        defaultDate: defaultD,
                        minDate: minD,
                        locale: thLocale,
                        disableMobile: true,
                        clickOpens: true,
                        allowInput: false,
                        static: false,
                        position: "auto",
                        appendTo: modalParent || document.body,
                        parseDate: function(dateStr, formatStr) {
                            if (typeof dateStr === 'string' && dateStr.includes('/')) {
                                const parts = dateStr.split('/');
                                if (parts.length === 3) {
                                    let day = parseInt(parts[0], 10);
                                    let month = parseInt(parts[1], 10) - 1;
                                    let year = parseInt(parts[2], 10);
                                    if (year > 2400) year -= 543;
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
                                if (year < 2400) year += 543;
                                return day + '/' + month + '/' + year;
                            }
                            return flatpickr.formatDate(date, formatStr, locale);
                        },
                        onReady: function(selectedDates, dateStr, instance) {
                            if (instance.altInput) {
                                instance.altInput.className = instance.input.className;
                                instance.altInput.classList.remove('datepicker-th');
                                instance.altInput.classList.add('flatpickr-input', ringColorClass);
                                instance.altInput.placeholder = instance.input.placeholder || 'เลือกวันที่...';
                            }
                            formatHeaderBuddhistYear(instance);
                        },
                        onMonthChange: function(selectedDates, dateStr, instance) { formatHeaderBuddhistYear(instance); },
                        onYearChange: function(selectedDates, dateStr, instance) { formatHeaderBuddhistYear(instance); },
                        onOpen: function(selectedDates, dateStr, instance) { formatHeaderBuddhistYear(instance); }
                    });
                } catch (e) {
                    console.warn('Flatpickr init warning (.datepicker-th):', e);
                }
            });
        }

        function initDatepicker() {
            initThaiDatepickers();
        }

        function fallbackNativeDate(el) {
            el.type = 'date';
            el.min = new Date().toISOString().split('T')[0];
            el.removeAttribute('readonly');
            el.onclick = function() { if (this.showPicker) this.showPicker(); };
        }

        function openDatePicker() {
            if (fpDate) {
                fpDate.open();
            } else {
                const el = document.getElementById('interview_date');
                if (el) {
                    if (el._flatpickr) el._flatpickr.open();
                    else if (el.showPicker) el.showPicker();
                }
            }
        }

        function handleTimeChange(select) {
            const customContainer = document.getElementById('custom_time_container');
            const customInput = document.getElementById('custom_time_input');
            if (!customContainer) return;
            if (select.value === 'custom') {
                customContainer.classList.remove('hidden');
                if (customInput) customInput.focus();
            } else {
                customContainer.classList.add('hidden');
                if (customInput) customInput.value = '';
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initThaiDatepickers);
        } else {
            initThaiDatepickers();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Handle AJAX for Schedule Form
            const scheduleForm = document.getElementById('scheduleForm');
            if (scheduleForm) {
                scheduleForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleAjaxSubmit(this, 'scheduleModal');
                });
            }

            // Handle AJAX for Email Form
            const emailForm = document.getElementById('emailForm');
            if (emailForm) {
                emailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleAjaxSubmit(this, 'emailModal');
                });
            }
        });

        async function handleAjaxSubmit(form, modalId) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                <span>กำลังดำเนินการ...</span>
            `;

            try {
                const formData = new FormData(form);

                if (form.id === 'scheduleForm') {
                    const timeSelect = document.getElementById('interview_time');
                    const customTime = document.getElementById('custom_time_input');
                    if (timeSelect && timeSelect.value === 'custom') {
                        if (!customTime || !customTime.value) {
                            throw new Error('กรุณาระบุเวลาสัมภาษณ์');
                        }
                        formData.set('interview_time', customTime.value);
                    }
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(result.message || 'เกิดข้อผิดพลาดในการดำเนินการ');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message
                });
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            }
        }

        const nextInterviewRound = {{ $application->interviews->count() + 1 }};
        const latestScheduledInterviewData = @json($latestScheduledData ?? null);
        @php
            $chkLastInterview = $application->interviews->sortBy('interview_round')->last();
            $chkCanSchedule = !$chkLastInterview || ($chkLastInterview->status === 'completed' || $chkLastInterview->evaluation);
            $pendingRoundNum = $chkLastInterview ? $chkLastInterview->interview_round : 1;
        @endphp
        const canScheduleNewRound = {{ $chkCanSchedule ? 'true' : 'false' }};
        const currentPendingRound = {{ $pendingRoundNum }};

        function openCreateInterviewModal() {
            if (!canScheduleNewRound && {{ $application->interviews->count() > 0 ? 'true' : 'false' }}) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ยังไม่สามารถนัดสัมภาษณ์รอบใหม่ได้',
                    text: 'กรุณาบันทึกแบบประเมินผลการสัมภาษณ์รอบที่ ' + currentPendingRound + ' ให้เสร็จสิ้นก่อน จึงจะสามารถนัดสัมภาษณ์รอบใหม่ได้',
                    confirmButtonColor: '#e11d48',
                    confirmButtonText: 'เข้าใจแล้ว'
                });
                return;
            }

            initDatepicker();

            const modalIdInput = document.getElementById('modal_interview_id');
            if (modalIdInput) modalIdInput.value = '';

            const iconEl = document.getElementById('scheduleModalIcon');
            const textEl = document.getElementById('scheduleModalText');
            if (iconEl) iconEl.className = 'fa-solid fa-calendar-plus text-sm';
            if (textEl) textEl.textContent = 'นัดสัมภาษณ์งาน (รอบที่ ' + nextInterviewRound + ')';

            const submitBtn = document.getElementById('scheduleSubmitBtn');
            if (submitBtn) submitBtn.textContent = 'ยืนยันการนัดหมาย';

            const roundInput = document.getElementById('interview_round');
            if (roundInput) roundInput.value = nextInterviewRound;

            const typeSelect = document.getElementById('modal_interview_type');
            if (typeSelect) typeSelect.value = 'Online';

            if (fpDate) {
                fpDate.clear();
            } else {
                const dateInput = document.getElementById('interview_date');
                if (dateInput) dateInput.value = '';
            }

            const timeSelect = document.getElementById('interview_time');
            if (timeSelect) timeSelect.value = '';
            const customContainer = document.getElementById('custom_time_container');
            if (customContainer) customContainer.classList.add('hidden');
            const customInput = document.getElementById('custom_time_input');
            if (customInput) customInput.value = '';

            document.querySelectorAll('#interviewer_options_list input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });
            updateInterviewerDisplay();

            const locInput = document.getElementById('interview_location');
            if (locInput) locInput.value = '';
            const linkInput = document.getElementById('interview_meeting_link');
            if (linkInput) linkInput.value = '';
            const noteInput = document.getElementById('interview_note');
            if (noteInput) noteInput.value = '';

            openModal('scheduleModal', true);
        }

        function openEditInterviewModal(data) {
            if (!data && latestScheduledInterviewData) {
                data = latestScheduledInterviewData;
            }
            if (!data) {
                openCreateInterviewModal();
                return;
            }

            initDatepicker();

            const modalIdInput = document.getElementById('modal_interview_id');
            if (modalIdInput) modalIdInput.value = data.id || '';

            const iconEl = document.getElementById('scheduleModalIcon');
            const textEl = document.getElementById('scheduleModalText');
            if (iconEl) iconEl.className = 'fa-solid fa-clock-rotate-left text-sm';
            if (textEl) textEl.textContent = 'ปรับเวลานัดสัมภาษณ์ (รอบที่ ' + (data.interview_round || 1) + ')';

            const submitBtn = document.getElementById('scheduleSubmitBtn');
            if (submitBtn) submitBtn.textContent = 'ยืนยันการปรับเวลานัดหมาย';

            const roundInput = document.getElementById('interview_round');
            if (roundInput) roundInput.value = data.interview_round || 1;

            const typeSelect = document.getElementById('modal_interview_type');
            if (typeSelect && data.interview_type) {
                typeSelect.value = data.interview_type;
            }

            // Set Date
            const dateInput = document.getElementById('interview_date');
            if (data.interview_date) {
                if (dateInput) dateInput.value = data.interview_date;
                if (fpDate) {
                    fpDate.setDate(data.interview_date, true);
                } else {
                    setTimeout(() => {
                        if (fpDate) fpDate.setDate(data.interview_date, true);
                    }, 60);
                }
            } else {
                if (fpDate) fpDate.clear();
                else if (dateInput) dateInput.value = '';
            }

            // Set Time
            const timeSelect = document.getElementById('interview_time');
            const customContainer = document.getElementById('custom_time_container');
            const customInput = document.getElementById('custom_time_input');
            let rawTime = (data.interview_time || '').trim();
            if (rawTime.length > 5) {
                rawTime = rawTime.substring(0, 5);
            }

            let found = false;
            if (timeSelect) {
                for (let i = 0; i < timeSelect.options.length; i++) {
                    if (timeSelect.options[i].value === rawTime) {
                        timeSelect.value = rawTime;
                        found = true;
                        break;
                    }
                }
                if (found) {
                    if (customContainer) customContainer.classList.add('hidden');
                    if (customInput) customInput.value = '';
                } else if (rawTime) {
                    timeSelect.value = 'custom';
                    if (customContainer) customContainer.classList.remove('hidden');
                    if (customInput) customInput.value = rawTime;
                } else {
                    timeSelect.value = '';
                    if (customContainer) customContainer.classList.add('hidden');
                    if (customInput) customInput.value = '';
                }
            }

            // Set Interviewers
            const targetIds = (data.interviewer_ids || []).map(id => String(id));
            document.querySelectorAll('#interviewer_options_list input[type="checkbox"]').forEach(cb => {
                cb.checked = targetIds.includes(String(cb.value));
            });
            updateInterviewerDisplay();

            // Set Location, link, note
            const locInput = document.getElementById('interview_location');
            if (locInput) locInput.value = data.location || '';

            const linkInput = document.getElementById('interview_meeting_link');
            if (linkInput) linkInput.value = data.meeting_link || '';

            const noteInput = document.getElementById('interview_note');
            if (noteInput) noteInput.value = data.note || '';

            openModal('scheduleModal', true);
        }

        function openModal(id, fromHelper = false) {
            if (id === 'scheduleModal' && !fromHelper) {
                if (latestScheduledInterviewData) {
                    openEditInterviewModal(latestScheduledInterviewData);
                    return;
                } else {
                    openCreateInterviewModal();
                    return;
                }
            }

            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (id === 'scheduleModal') {
                closeInterviewerDropdown();
                setTimeout(() => {
                    initDatepicker();
                }, 50);
            } else if (id === 'onboardingModal' || id === 'deptOnboardingModal') {
                setTimeout(() => {
                    initThaiDatepickers();
                }, 50);
            }
        }

        // Interviewer Dropdown Helpers
        function toggleInterviewerDropdown() {
            const menu = document.getElementById('interviewer_dropdown_menu');
            const arrow = document.getElementById('interviewer_arrow');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                arrow.classList.add('rotate-180');
                const searchInp = document.getElementById('interviewer_search_input');
                if (searchInp) setTimeout(() => searchInp.focus(), 50);
            } else {
                closeInterviewerDropdown();
            }
        }

        function closeInterviewerDropdown() {
            const menu = document.getElementById('interviewer_dropdown_menu');
            const arrow = document.getElementById('interviewer_arrow');
            if (menu) menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }

        function filterInterviewers() {
            const searchInput = document.getElementById('interviewer_search_input');
            const deptSelect = document.getElementById('interviewer_dept_filter');
            const q = (searchInput ? searchInput.value : '').trim().toLowerCase();
            const dept = (deptSelect ? deptSelect.value : '').trim();

            const items = document.querySelectorAll('#interviewer_options_list .interviewer-option');
            items.forEach(el => {
                const name = el.getAttribute('data-name') || '';
                const itemDept = el.getAttribute('data-dept') || '';
                
                const matchSearch = !q || name.includes(q);
                const matchDept = !dept || itemDept === dept;

                if (matchSearch && matchDept) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }

        function updateInterviewerDisplay() {
            const checkboxes = document.querySelectorAll('#interviewer_options_list input[type="checkbox"]:checked');
            const container = document.getElementById('interviewer_selected_labels');
            if (!container) return;

            if (checkboxes.length === 0) {
                container.innerHTML = '<span class="text-gray-400 text-xs">คลิกเพื่อเลือกผู้สัมภาษณ์...</span>';
                return;
            }

            let html = '';
            checkboxes.forEach(cb => {
                const label = cb.getAttribute('data-label') || cb.value;
                html += `
                    <span class="inline-flex items-center gap-1 bg-red-50 dark:bg-red-950/40 text-kumwell-red dark:text-red-400 border border-red-200 dark:border-red-900/50 px-2 py-0.5 rounded-md text-[11px] font-semibold">
                        ${label}
                        <button type="button" onclick="event.stopPropagation(); removeInterviewer('${cb.value}')" class="hover:text-red-700 ml-0.5">
                            <i class="fa-solid fa-xmark text-[9px]"></i>
                        </button>
                    </span>
                `;
            });
            container.innerHTML = html;
        }

        function removeInterviewer(val) {
            const cb = document.querySelector(`#interviewer_options_list input[value="${val}"]`);
            if (cb) {
                cb.checked = false;
                updateInterviewerDisplay();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('interviewer_dropdown_container');
            if (container && !container.contains(e.target)) {
                closeInterviewerDropdown();
            }
        });

        function adjustRound(delta) {
            const input = document.getElementById('interview_round');
            let val = parseInt(input.value) || 1;
            val = Math.max(1, val + delta);
            input.value = val;
        }

        function adjustSlider(index, delta) {
            const slider = document.getElementById('score_slider_' + index);
            const display = document.getElementById('score_display_' + index);
            let val = parseInt(slider.value) || 0;
            val = Math.min(10, Math.max(0, val + delta));
            slider.value = val;
            display.innerText = val;
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openScoreModal(interviewId) {
            const form = document.getElementById('scoreForm');
            form.action = `/backend/recruitment/interviews/${interviewId}/scores`;
            openModal('scoreModal');
        }

        function openRejectModal(status, label) {
            document.getElementById('reject_target_status').value = status;
            if (label) {
                document.getElementById('reject_modal_label').innerText = label;
            }
            openModal('rejectReasonModal');
        }

        function confirmDeptInterview() {
            Swal.fire({
                title: 'ยืนยันการนัดสัมภาษณ์?',
                html: '<span class="text-xs text-gray-600 dark:text-gray-300">ยืนยันให้ผู้สมัครรายนี้เข้าสู่ขั้นตอนการสัมภาษณ์งาน<br>และส่งต่อให้ฝ่าย <b>HA</b> ดำเนินการกำหนดวันนัดหมายและแจ้งผู้สมัคร</span>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="fa-solid fa-calendar-check mr-1.5"></i> ยืนยันนัดสัมภาษณ์',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl dark:bg-kumwell-card dark:text-white',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold shadow-md shadow-emerald-600/20 text-xs',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-bold text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deptInterviewForm');
                    if (form) form.submit();
                }
            });
        }
    </script>
    <style>
        /* Select2 Custom Styling to match Tailwind */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: #f9fafb !important; /* gray-50 */
            border: none !important;
            border-radius: 0.75rem !important;
            min-height: 46px !important;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            padding: 4px 8px !important;
            width: 100% !important;
        }
        .dark .select2-container--default .select2-selection--single,
        .dark .select2-container--default .select2-selection--multiple {
            background-color: #121418 !important;
            border: none !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding-left: 0.5rem !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
            width: 100% !important;
            display: block !important;
            line-height: normal !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered,
        .dark .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #d1d5db !important; /* text-gray-300 */
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: flex-start !important;
            gap: 6px !important;
            padding: 8px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #D71920 !important;
            border: none !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            margin: 0 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            max-width: calc(100% - 10px) !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            margin-top: 0 !important;
            padding: 4px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 5px !important;
            border-right: 1px solid rgba(255,255,255,0.2) !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: rgba(255,255,255,0.1) !important;
        }
        .select2-container--default .select2-selection--multiple {
            background-color: #f9fafb !important; /* matches other inputs bg-gray-50 */
            border: none !important;
            border-radius: 0.75rem !important;
            min-height: 46px !important;
            align-items: center !important;
            padding: 4px 12px !important;
        }
        .dark .select2-container--default .select2-selection--multiple {
            background-color: #121418 !important;
            border: none !important;
        }
        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 4px !important;
            margin-bottom: 4px !important;
            font-size: 0.875rem !important;
            font-family: inherit !important;
            color: #374151 !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
        .dark .select2-container--default .select2-search--inline .select2-search__field {
            color: #e5e7eb !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 0.75rem !important;
        }
        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden;
            z-index: 70 !important;
        }
        .dark .select2-dropdown {
            background-color: #1E2129 !important; /* kumwell-card */
            border-color: #374151 !important;
        }
        .select2-search__field {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 10px 12px !important; /* increased padding for Thai text */
            font-size: 0.875rem !important;
            min-height: 38px !important;
        }
        .dark .select2-search__field {
            background-color: #121418 !important;
            border-color: #374151 !important;
            color: white !important;
        }
        .select2-results__option {
            padding: 8px 16px !important;
            font-size: 0.875rem !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #D71920 !important; /* kumwell-red */
        }

        /* Flatpickr Premium Light & Dark Theme Custom Styling */
        .flatpickr-calendar {
            background: #ffffff !important;
            border-radius: 1.25rem !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            z-index: 9999 !important;
            padding: 12px 14px !important;
            width: 336px !important;
            max-width: 95vw !important;
            box-sizing: border-box !important;
            font-family: 'Prompt', sans-serif !important;
        }

        .dark .flatpickr-calendar {
            background: #1E2129 !important;
            border-color: #374151 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
        }

        .flatpickr-months {
            background: transparent !important;
            margin-bottom: 6px !important;
        }
        
        .flatpickr-months .flatpickr-month {
            color: #1f2937 !important;
            fill: #1f2937 !important;
            height: 44px !important;
        }

        .dark .flatpickr-months .flatpickr-month {
            color: #f3f4f6 !important;
            fill: #f3f4f6 !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            background: transparent !important;
            color: #1f2937 !important;
            padding: 4px 6px !important;
            border-radius: 8px !important;
        }

        .dark .flatpickr-current-month .flatpickr-monthDropdown-months {
            color: #f3f4f6 !important;
        }
        
        .flatpickr-current-month .flatpickr-monthDropdown-month:hover {
            background: #f3f4f6 !important;
        }

        .dark .flatpickr-current-month .flatpickr-monthDropdown-month:hover {
            background: #374151 !important;
        }

        .flatpickr-current-month input.cur-year {
            font-weight: 700 !important;
            color: #1f2937 !important;
        }

        .dark .flatpickr-current-month input.cur-year {
            color: #f3f4f6 !important;
        }

        .flatpickr-days {
            width: 308px !important;
            margin: 0 auto !important;
        }

        .dayContainer {
            width: 308px !important;
            min-width: 308px !important;
            max-width: 308px !important;
            justify-content: flex-start !important;
            padding: 0 !important;
        }

        .flatpickr-weekdays {
            width: 308px !important;
            margin: 0 auto 4px auto !important;
            height: 32px !important;
            display: flex !important;
        }

        .flatpickr-weekdaycontainer {
            width: 308px !important;
            display: flex !important;
        }

        span.flatpickr-weekday {
            width: 44px !important;
            max-width: 44px !important;
            flex: 1 !important;
            text-align: center !important;
            color: #9ca3af !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
        }

        .flatpickr-day {
            border-radius: 10px !important;
            color: #374151 !important;
            font-weight: 500 !important;
            height: 36px !important;
            line-height: 36px !important;
            max-width: 36px !important;
            width: 36px !important;
            margin: 2px 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .dark .flatpickr-day {
            color: #d1d5db !important;
        }

        .flatpickr-day:hover {
            background: #fee2e2 !important;
            color: #D71920 !important;
            transform: translateY(-1px);
        }

        .dark .flatpickr-day:hover {
            background: #374151 !important;
            color: #ffffff !important;
        }

        .flatpickr-day.selected {
            background: #D71920 !important;
            border-color: #D71920 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(215, 25, 32, 0.3) !important;
            transform: scale(1.05);
        }

        .flatpickr-day.today {
            border-color: #D71920 !important;
            color: #D71920 !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
            opacity: 0.45;
        }

        .dark .flatpickr-day.prevMonthDay, .dark .flatpickr-day.nextMonthDay {
            color: #4b5563 !important;
            opacity: 0.45;
        }

        .flatpickr-prev-month, .flatpickr-next-month {
            padding: 8px !important;
            border-radius: 50% !important;
            transition: all 0.2s !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .flatpickr-prev-month:hover, .flatpickr-next-month:hover {
            background: #f3f4f6 !important;
        }

        .dark .flatpickr-prev-month:hover, .dark .flatpickr-next-month:hover {
            background: #374151 !important;
        }

        .flatpickr-prev-month:hover svg, .flatpickr-next-month:hover svg {
            fill: #D71920 !important;
        }

        /* Paper sheet & document styling matching Picture 2 */
        .paper-sheet-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .paper-sheet-container::-webkit-scrollbar {
            height: 6px;
        }
        .paper-sheet-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .paper-sheet-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .paper-sheet {
            background-color: #ffffff !important;
            color: #111827 !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.12), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            font-family: 'Prompt', 'Kanit', sans-serif !important;
            font-size: 14.5px;
            line-height: 1.45;
            width: 100%;
            max-width: 1060px;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
        }
        .paper-sheet, .paper-sheet *,
        .paper-sheet-container, .paper-sheet-container * {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
        .paper-sheet .fa, .paper-sheet .fas, .paper-sheet .far, .paper-sheet .fa-solid, .paper-sheet .fa-regular {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "FontAwesome" !important;
        }
        .doc-input {
            border: none;
            border-bottom: 2px dotted #1f2937;
            background: transparent;
            padding: 1px 4px;
            font-weight: 600;
            color: #111827;
            outline: none;
            display: inline-block;
            vertical-align: bottom;
            min-height: 24px;
            line-height: 22px;
        }
        .sub-label {
            font-size: 11px !important;
            line-height: 1.1 !important;
            margin-top: 1px !important;
            color: #6b7280 !important;
            font-weight: 500 !important;
            display: block;
        }
        .doc-table {
            border-collapse: collapse;
            width: 100%;
            border: 1.5px solid #000000;
        }
        .doc-table th, .doc-table td {
            border: 1px solid #000000;
            padding: 4px 6px;
        }
        .doc-table th {
            background-color: #f8fafc;
            text-align: center;
            font-weight: bold;
            color: #111827;
        }
        .paper-checkbox {
            width: 15px;
            height: 15px;
            border-radius: 3px;
            border: 1.5px solid #1f2937;
            accent-color: #B21F24;
            cursor: default;
        }
        .dark .paper-sheet {
            background-color: #ffffff !important;
            color: #111827 !important;
        }
    </style>
    @endpush
@endsection