<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน') }}
    </h2>
  </x-slot>

  <div class="py-6 sm:py-8 bg-gray-100 dark:bg-gray-900 min-h-screen relative">
    <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Left: Form Categories Sidebar -->
        <x-hr-forms-sidebar />

        <!-- Right: Form Paper Container -->
        <div class="flex-1 w-full min-w-0">
          @php
            // 1. Fetch Kumwell Employees (for evaluators & internal transfers)
            try {
              $kumEmployees = \Illuminate\Support\Facades\DB::connection('appkum_user')
                ->table('employees')
                ->where('status', 'active')
                ->orderBy('firstname')
                ->get();
            } catch (\Throwable $e) {
              try {
                $kumEmployees = \App\Models\User::orderBy('firstname')->get();
              } catch (\Throwable $ex) {
                $kumEmployees = collect([]);
              }
            }

            // 2. Fetch Job Applicants & Applications
            $candidateList = collect();
            try {
              $recApplications = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.department.division'])
                ->whereNotNull('applicant_id')
                ->latest('applied_at')
                ->get();

              foreach ($recApplications as $app) {
                if ($app->applicant) {
                  $applicant = $app->applicant;
                  $fullName = trim(($applicant->first_name ?? '') . ' ' . ($applicant->last_name ?? ''));
                  if ($fullName) {
                    $jobPost = $app->jobPost;
                    $deptName = $jobPost?->department?->department_name ?: ($jobPost?->department?->department_fullname ?: '');
                    $divName = $jobPost?->department?->division?->division_name ?: ($jobPost?->department?->division?->division_fullname ?: '');
                    $posName = $jobPost?->position_name ?: '';

                    $candidateList->push([
                      'name' => $fullName,
                      'prefix' => $applicant->prefix ?: '',
                      'position' => $posName,
                      'department' => $deptName,
                      'division' => $divName,
                      'subtext' => $posName ? "($posName)" : '(ผู้สมัครงาน)',
                    ]);
                  }
                }
              }
            } catch (\Throwable $e) {}

            try {
              $allApplicants = \App\Models\Recruitment\Applicant::latest()->get();
              foreach ($allApplicants as $applicant) {
                $fullName = trim(($applicant->first_name ?? '') . ' ' . ($applicant->last_name ?? ''));
                if ($fullName && !$candidateList->contains('name', $fullName)) {
                  $candidateList->push([
                    'name' => $fullName,
                    'prefix' => $applicant->prefix ?: '',
                    'position' => $applicant->current_position ?: '',
                    'department' => '',
                    'division' => '',
                    'subtext' => '(ผู้สมัครงาน)',
                  ]);
                }
              }
            } catch (\Throwable $e) {}

            // Add internal employees as fallback options
            try {
              foreach ($kumEmployees as $emp) {
                $fullName = trim(($emp->firstname ?? '') . ' ' . ($emp->lastname ?? ''));
                if ($fullName && !$candidateList->contains('name', $fullName)) {
                  $candidateList->push([
                    'name' => $fullName,
                    'prefix' => $emp->prefix ?? '',
                    'position' => $emp->position ?? '',
                    'department' => $emp->department_name ?? '',
                    'division' => $emp->division_name ?? '',
                    'subtext' => '(พนักงานภายใน)',
                  ]);
                }
              }
            } catch (\Throwable $e) {}

            // 3. Fetch Departments and Divisions
            try {
              $allDivisions = \App\Models\Division::where('division_status', \App\Models\Division::STATUS_ACTIVE)->get();
              $allDepartments = \App\Models\Department::where('department_status', \App\Models\Department::STATUS_ACTIVE)->get();

              $deptOptions = collect();
              foreach ($allDepartments as $dept) {
                $c = trim($dept->department_name ?? '');
                $f = trim($dept->department_fullname ?? '');
                if ($c && $c !== '-') $deptOptions->push(['code' => $c, 'name' => $f]);
              }
              $deptOptions = $deptOptions->unique('code')->sortBy('code')->values();

              $divOptions = collect();
              foreach ($allDivisions as $div) {
                $c = trim($div->division_name ?? '');
                $f = trim($div->division_fullname ?? '');
                if ($c && $c !== '-') $divOptions->push(['code' => $c, 'name' => $f]);
              }
              $divOptions = $divOptions->unique('code')->sortBy('code')->values();
            } catch (\Throwable $e) {
              $deptOptions = collect([]);
              $divOptions = collect([]);
            }

            // 4. Pre-fill candidate if interview_id, application_id, or applicant_id in URL/controller
            $prefillCandidate = null;
            $reqInterviewId = request('interview_id') ?? ($interviewId ?? null);
            $reqAppId = request('application_id') ?? ($applicationId ?? null);
            $reqApplicantId = request('applicant_id');

            if ($reqInterviewId) {
              $foundInterview = \App\Models\Recruitment\Interview::with(['application.applicant', 'application.jobPost.department.division'])->find($reqInterviewId);
              if ($foundInterview && $foundInterview->application && $foundInterview->application->applicant) {
                $app = $foundInterview->application;
                $prefillCandidate = [
                  'name' => trim(($app->applicant->first_name ?? '') . ' ' . ($app->applicant->last_name ?? '')),
                  'prefix' => $app->applicant->prefix ?? '',
                  'position' => $app->jobPost?->position_name ?? ($app->jobPost?->title ?? ''),
                  'department' => $app->jobPost?->department?->department_name ?: ($app->jobPost?->department?->department_fullname ?: ''),
                  'division' => $app->jobPost?->department?->division?->division_name ?: ($app->jobPost?->department?->division?->division_fullname ?: ''),
                  'interview_times' => $foundInterview->interview_round ?? 1,
                  'evaluation_date' => $foundInterview->interview_date ? $foundInterview->interview_date->format('Y-m-d') : date('Y-m-d'),
                ];
              }
            } elseif ($reqAppId) {
              $foundApp = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.department.division', 'interviews'])->find($reqAppId);
              if ($foundApp && $foundApp->applicant) {
                $latestIv = $foundApp->interviews?->where('status', 'scheduled')->last() ?? $foundApp->interviews?->last();
                $prefillCandidate = [
                  'name' => trim(($foundApp->applicant->first_name ?? '') . ' ' . ($foundApp->applicant->last_name ?? '')),
                  'prefix' => $foundApp->applicant->prefix ?? '',
                  'position' => $foundApp->jobPost?->position_name ?? ($foundApp->jobPost?->title ?? ''),
                  'department' => $foundApp->jobPost?->department?->department_name ?: ($foundApp->jobPost?->department?->department_fullname ?: ''),
                  'division' => $foundApp->jobPost?->department?->division?->division_name ?: ($foundApp->jobPost?->department?->division?->division_fullname ?: ''),
                  'interview_times' => $latestIv?->interview_round ?? 1,
                  'evaluation_date' => $latestIv?->interview_date ? $latestIv->interview_date->format('Y-m-d') : date('Y-m-d'),
                ];
              }
            } elseif ($reqApplicantId) {
              $foundApplicant = \App\Models\Recruitment\Applicant::find($reqApplicantId);
              if ($foundApplicant) {
                $prefillCandidate = [
                  'name' => trim(($foundApplicant->first_name ?? '') . ' ' . ($foundApplicant->last_name ?? '')),
                  'prefix' => $foundApplicant->prefix ?? '',
                  'position' => $foundApplicant->current_position ?? '',
                  'department' => '',
                  'division' => '',
                  'interview_times' => 1,
                  'evaluation_date' => date('Y-m-d'),
                ];
              }
            }

            // ตรวจสอบแบบประเมินเดิมที่เคยบันทึกไว้ (สำหรับกรณีบันทึกแยกส่วน Partial Save)
            $existingEvaluation = $existingEvaluation ?? null;
            if ($existingEvaluation) {
              $prefillCandidate = [
                'name' => $existingEvaluation->candidate_name,
                'prefix' => $existingEvaluation->candidate_prefix ?? '',
                'position' => $existingEvaluation->position_applied ?? '',
                'department' => $existingEvaluation->department ?? '',
                'division' => $existingEvaluation->division ?? '',
                'interview_times' => $existingEvaluation->interview_times ?? 1,
                'evaluation_date' => $existingEvaluation->evaluation_date ? $existingEvaluation->evaluation_date->format('Y-m-d') : date('Y-m-d'),
              ];
            }
            $existingScores = $existingScores ?? ($existingEvaluation ? $existingEvaluation->scores->keyBy('item_no') : collect([]));
            $existingHrEvaluated = $existingEvaluation ? $existingEvaluation->isHrEvaluated() : false;
            $existingDeptEvaluated = $existingEvaluation ? $existingEvaluation->isDeptEvaluated() : false;

            // 5. Determine who has the permission/position to sign
            $currentUser = auth()->user();
            $userDeptName = $currentUser?->department?->department_name ?: ($currentUser?->department?->department_fullname ?: '');
            $userPosition = (string)($currentUser?->position ?? '');

            // ตรวจสอบว่าผู้ใช้สังกัดฝ่ายบุคคล (HR) หรือไม่ (Dept 14/15: HAMS / HAM - Human Assets Management):
            $isHrDept = ((int)$currentUser?->dept_id === 15 || (int)$currentUser?->department_id === 15 
                        || (int)$currentUser?->dept_id === 14 || (int)$currentUser?->department_id === 14 
                        || (strcasecmp($userDeptName, 'HAM') === 0) 
                        || (strcasecmp($userDeptName, 'HAMS') === 0) 
                        || (strcasecmp($userDeptName, 'HR') === 0) 
                        || ($userDeptName === 'Human Assets Management')
                        || preg_match('/(Human\s*Assets|HAM|HAMS|ฝ่ายทรัพยากรบุคคล|ฝ่ายบุคคล)/ui', $userDeptName));

            $isHrPos = preg_match('/\b(HR|Recruitment)\b|(ทรัพยากรบุคคล|เจ้าหน้าที่บุคคล|สรรหา|human\s*resource)/ui', $userPosition);
            $isSystemAdmin = ((int)($currentUser?->level_user ?? -1) === 0 || in_array(strtolower((string)($currentUser?->role ?? '')), ['admin', 'superadmin', 'administrator']));

            // สิทธิ์ลงชื่อฝ่ายบุคคล (HR): ต้องเป็นฝ่ายบุคคล หรือ System Admin เท่านั้น
            $canSignHr = $currentUser && ($isHrDept || $isHrPos || $isSystemAdmin);

            // ตรวจสอบว่าผู้ใช้เป็นต้นสังกัด หรือกรรมการสัมภาษณ์หรือไม่:
            $targetInterview = $foundInterview ?? $prefillInterview ?? null;
            $targetApp = $foundApp ?? $prefillApplication ?? $targetInterview?->application ?? null;

            $isAssignedInterviewer = false;
            if ($targetInterview && $currentUser) {
              if ($targetInterview->interviewer_id && $targetInterview->interviewer_id == $currentUser->id) {
                $isAssignedInterviewer = true;
              }
              if ($targetInterview->relationLoaded('interviewers') || method_exists($targetInterview, 'interviewers')) {
                try {
                  if ($targetInterview->interviewers && $targetInterview->interviewers->contains('id', $currentUser->id)) {
                    $isAssignedInterviewer = true;
                  }
                } catch (\Throwable $e) {}
              }
            }

            $jobDeptId = $targetApp?->jobPost?->department_id ?? null;
            $jobDeptName = $targetApp?->jobPost?->department?->department_name 
                        ?: ($targetApp?->jobPost?->department?->department_fullname ?: '');

            $isSameDepartment = false;
            if ($currentUser && $jobDeptId && ($currentUser->department_id == $jobDeptId || $currentUser->dept_id == $jobDeptId)) {
              $isSameDepartment = true;
            }
            if ($currentUser && $jobDeptName && $userDeptName && (strcasecmp($jobDeptName, $userDeptName) === 0 || str_contains($userDeptName, $jobDeptName) || str_contains($jobDeptName, $userDeptName))) {
              $isSameDepartment = true;
            }

            if ($targetInterview || $targetApp) {
              $canSignDept = $currentUser && ($isAssignedInterviewer || $isSameDepartment || $isSystemAdmin);
            } else {
              $canSignDept = $currentUser && (!$isHrDept && !$isHrPos || $isSystemAdmin);
            }

            // ถ้าผู้ใช้เป็นฝ่ายบุคคลเพียวๆ (ไม่ได้เป็นกรรมการสัมภาษณ์ของเคสนี้) จะไม่มีสิทธิ์ลงชื่อในช่องต้นสังกัด
            if (($isHrDept || $isHrPos) && !$isAssignedInterviewer && !$isSystemAdmin) {
              $canSignDept = false;
            }

            // ตัวแปรขั้นตอนและการอนุญาต
            $activePhase = $activePhase ?? ($existingEvaluation && $existingEvaluation->status === 'completed' ? 'completed' : ($existingEvaluation && $existingEvaluation->status === 'pending_dept' && !empty($existingEvaluation->hr_evaluator_name) ? 'dept_eval' : 'hr_eval'));
            if (request()->has('phase')) {
              $p = request()->get('phase');
              if (in_array($p, ['hr', 'dept', 'completed'])) {
                $activePhase = ($p === 'hr' ? 'hr_eval' : ($p === 'dept' ? 'dept_eval' : 'completed'));
              }
            }

            $isHrUser = $isHrUser ?? ($canSignHr ?? false);
            $isDeptUser = $isDeptUser ?? ($canSignDept ?? false);
            $isSystemAdmin = $isSystemAdmin ?? false;

            // สิทธิ์การประเมินในคอลัมน์ (ห้ามประเมินในช่องของคนอื่นอย่างเด็ดขาด):
            // 1. ฝ่ายบุคคลประเมินได้เฉพาะตอน activePhase == 'hr_eval'
            // 2. ต้นสังกัดประเมินได้เฉพาะตอน activePhase == 'dept_eval'
            $isHrColumnEditable = ($activePhase === 'hr_eval' && ($canSignHr || $isSystemAdmin));
            $isDeptColumnEditable = ($activePhase === 'dept_eval' && ($canSignDept || $isSystemAdmin));
            if ($isSystemAdmin && request()->has('edit_all')) {
              $isHrColumnEditable = true;
              $isDeptColumnEditable = true;
            }
          @endphp

          <datalist id="employee_names">
            @foreach($kumEmployees as $emp)
              <option value="{{ $emp->firstname }} {{ $emp->lastname }}"></option>
            @endforeach
          </datalist>

          <datalist id="candidate_names">
            @foreach($candidateList as $c)
              <option value="{{ $c['name'] }}" 
                      data-prefix="{{ $c['prefix'] }}"
                      data-position="{{ $c['position'] }}"
                      data-department="{{ $c['department'] }}"
                      data-division="{{ $c['division'] }}">
                {{ $c['name'] }} {{ $c['subtext'] ?? '' }}
              </option>
            @endforeach
          </datalist>

          <datalist id="department_list">
            @foreach($deptOptions as $d)
              <option value="{{ $d['code'] }}">{{ $d['name'] ?: $d['code'] }}</option>
              @if(!empty($d['name']) && $d['name'] !== $d['code'])
                <option value="{{ $d['name'] }}">{{ $d['code'] }}</option>
              @endif
            @endforeach
          </datalist>

          <datalist id="division_list">
            @foreach($divOptions as $div)
              <option value="{{ $div['code'] }}">{{ $div['name'] ?: $div['code'] }}</option>
              @if(!empty($div['name']) && $div['name'] !== $div['code'])
                <option value="{{ $div['name'] }}">{{ $div['code'] }}</option>
              @endif
            @endforeach
          </datalist>

          @if(!empty($prefillCandidate))
            <div class="mb-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-red-800 dark:text-red-200 shadow-xs">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-user-check text-base text-kumwell-red shrink-0"></i>
                <span>ประเมินผลการสัมภาษณ์สำหรับผู้สมัคร: <strong class="text-gray-900 dark:text-white">{{ $prefillCandidate['prefix'] ?? '' }} {{ $prefillCandidate['name'] }}</strong> ({{ $prefillCandidate['position'] ?? 'ผู้สมัครงาน' }}) @if(!empty($prefillCandidate['interview_times'])) • สัมภาษณ์รอบที่ {{ $prefillCandidate['interview_times'] }} @endif</span>
              </div>
              @php
                $backLink = $returnUrl ?? request('return_url') ?? ($reqAppId ? route('backend.recruitment.applications.show', $reqAppId) : null);
              @endphp
              @if($backLink)
                <a href="{{ $backLink }}" class="inline-flex items-center gap-1 font-semibold text-kumwell-red hover:underline shrink-0">
                  <i class="fa-solid fa-arrow-left text-[10px]"></i> กลับหน้ารายละเอียดใบสมัคร
                </a>
              @endif
            </div>
          @endif

          <form action="{{ route('interview-evaluation.store') }}" method="POST" id="interviewEvaluationForm">
            @csrf
            
            <input type="hidden" name="evaluation_id" value="{{ $existingEvaluation?->id }}">
            <input type="hidden" name="interview_id" value="{{ $interviewId ?? request('interview_id') ?? $existingEvaluation?->interview_id }}">
            <input type="hidden" name="application_id" value="{{ $applicationId ?? request('application_id') ?? $existingEvaluation?->application_id }}">
            <input type="hidden" name="return_url" value="{{ $returnUrl ?? request('return_url') }}">
            <input type="hidden" name="active_phase" id="active_phase" value="{{ $activePhase }}">

            <!-- All Pages Wrapper -->
            <div class="hr-form-container">
            <!-- PAGE Paper Container -->
            <div class="hr-form-paper">

            <!-- 2-Step Workflow Progress Visual Banner -->
            <div class="mb-6 bg-slate-50 dark:bg-gray-800/80 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-xs">
              <div class="flex items-center justify-between gap-3 max-w-xl mx-auto">
                <!-- Step 1: HR -->
                <div class="flex items-center gap-3 flex-1 min-w-0">
                  <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0 transition-all {{ !empty($existingEvaluation?->hr_evaluator_name) ? 'bg-emerald-600 text-white shadow-sm' : ($activePhase === 'hr_eval' ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/50 shadow-sm' : 'bg-gray-200 text-gray-600') }}">
                    @if(!empty($existingEvaluation?->hr_evaluator_name))
                      <i class="fa-solid fa-check text-sm"></i>
                    @else
                      1
                    @endif
                  </div>
                  <div class="min-w-0">
                    <div class="text-xs font-bold text-gray-900 dark:text-white truncate">ขั้นตอนที่ 1: ฝ่ายบุคคล</div>
                    <div class="text-[11px] truncate">
                      @if(!empty($existingEvaluation?->hr_evaluator_name))
                        <span class="text-emerald-700 dark:text-emerald-400 font-semibold">✓ ลงชื่อและส่งต่อแล้ว</span>
                      @elseif($activePhase === 'hr_eval')
                        <span class="text-blue-600 dark:text-blue-400 font-bold animate-pulse">● กำลังประเมิน & ลงชื่อ</span>
                      @else
                        <span class="text-gray-400">รอประเมิน</span>
                      @endif
                    </div>
                  </div>
                </div>

                <!-- Connector Line -->
                <div class="flex-1 h-0.5 max-w-[70px] bg-gray-200 dark:bg-gray-700 relative shrink-0">
                  <div class="h-0.5 bg-emerald-500 transition-all duration-300 {{ !empty($existingEvaluation?->hr_evaluator_name) ? 'w-full' : 'w-0' }}"></div>
                </div>

                <!-- Step 2: Department -->
                <div class="flex items-center gap-3 flex-1 justify-end min-w-0">
                  <div class="text-right min-w-0">
                    <div class="text-xs font-bold text-gray-900 dark:text-white truncate">ขั้นตอนที่ 2: ต้นสังกัด</div>
                    <div class="text-[11px] truncate">
                      @if($activePhase === 'completed' || !empty($existingEvaluation?->dept_evaluator_name))
                        <span class="text-emerald-700 dark:text-emerald-400 font-semibold">✓ เสร็จสมบูรณ์</span>
                      @elseif($activePhase === 'dept_eval')
                        <span class="text-purple-600 dark:text-purple-400 font-bold animate-pulse">● กำลังประเมิน & ลงชื่อ</span>
                      @else
                        <span class="text-gray-400">🔒 รอดำเนินการ</span>
                      @endif
                    </div>
                  </div>
                  <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0 transition-all {{ ($activePhase === 'completed' || !empty($existingEvaluation?->dept_evaluator_name)) ? 'bg-emerald-600 text-white shadow-sm' : ($activePhase === 'dept_eval' ? 'bg-purple-600 text-white ring-4 ring-purple-100 dark:ring-purple-900/50 shadow-sm' : 'bg-gray-100 text-gray-400') }}">
                    @if($activePhase === 'completed' || !empty($existingEvaluation?->dept_evaluator_name))
                      <i class="fa-solid fa-check text-sm"></i>
                    @else
                      2
                    @endif
                  </div>
                </div>
              </div>

              <!-- Phase Guidance Alert -->
              <div class="mt-3.5 pt-3 border-t border-gray-200/60 dark:border-gray-700/60 text-xs">
                @if($activePhase === 'hr_eval')
                  <div class="flex items-start gap-2.5 text-blue-900 dark:text-blue-200 bg-blue-50/80 dark:bg-blue-950/40 p-3 rounded-xl border border-blue-200/80 dark:border-blue-900/50">
                    <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 text-sm shrink-0"></i>
                    <div class="leading-relaxed">
                      <strong class="font-bold text-blue-950 dark:text-blue-100">ขั้นตอนของฝ่ายบุคคล (HR):</strong> เจ้าหน้าที่ฝ่ายบุคคลต้องให้คะแนนในคอลัมน์ <strong>"❶ ฝ่ายบุคคล"</strong> ให้ครบทั้ง 10 ข้อ และ <strong>กดปุ่มลงชื่อ</strong> ก่อน จึงจะสามารถกดบันทึกส่งต่อให้ต้นสังกัดประเมินต่อได้
                      <div class="mt-1 text-[11px] text-blue-700 dark:text-blue-300 font-medium">
                        <i class="fa-solid fa-lock text-[10px] mr-1"></i>ช่องประเมินและลายเซ็นของต้นสังกัดจะถูกล็อคไว้ ไม่สามารถประเมินข้ามช่องของกันและกันได้
                      </div>
                    </div>
                  </div>
                @elseif($activePhase === 'dept_eval')
                  <div class="flex items-start gap-2.5 text-purple-900 dark:text-purple-200 bg-purple-50/80 dark:bg-purple-950/40 p-3 rounded-xl border border-purple-200/80 dark:border-purple-900/50">
                    <i class="fa-solid fa-clock-rotate-left text-purple-600 mt-0.5 text-sm shrink-0"></i>
                    <div class="leading-relaxed">
                      <strong class="font-bold text-purple-950 dark:text-purple-100">ขั้นตอนของต้นสังกัด (Department):</strong> ฝ่ายบุคคลได้ประเมิน ({{ $existingEvaluation?->total_hr_score ?? 0 }}/40 คะแนน) และลงชื่อส่งต่อมาแล้ว กรุณาให้คะแนนในคอลัมน์ <strong>"❷ ต้นสังกัด"</strong> ให้ครบทั้ง 10 ข้อ สรุปผลการสัมภาษณ์ และ <strong>กดปุ่มลงชื่อ</strong> ก่อนส่งผลประเมิน
                      <div class="mt-1 text-[11px] text-purple-700 dark:text-purple-300 font-medium">
                        <i class="fa-solid fa-lock text-[10px] mr-1"></i>คะแนนและลายเซ็นของฝ่ายบุคคลถูกล็อคไว้ ไม่สามารถแก้ไขได้
                      </div>
                    </div>
                  </div>
                @elseif($activePhase === 'completed')
                  <div class="flex items-start gap-2.5 text-emerald-900 dark:text-emerald-200 bg-emerald-50/80 dark:bg-emerald-950/40 p-3 rounded-xl border border-emerald-200/80 dark:border-emerald-900/50">
                    <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-sm shrink-0"></i>
                    <div class="leading-relaxed">
                      <strong class="font-bold text-emerald-950 dark:text-emerald-100">การประเมินเสร็จสมบูรณ์แล้ว:</strong> แบบประเมินนี้ได้รับการประเมินและลงชื่อจากทั้งฝ่ายบุคคลและต้นสังกัดครบถ้วนแล้ว (คะแนนเฉลี่ย: <strong>{{ $existingEvaluation?->average_score ?? 0 }}/40</strong> • สรุปผล: <strong>{{ $existingEvaluation?->summary_result_label ?? '-' }}</strong>)
                    </div>
                  </div>
                @endif
              </div>
            </div>
        
        <!-- Header Section -->
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน</h1>
        </div>

        <div class="flex justify-end mb-4 text-sm text-black">
          <div class="flex items-center">
            <span class="mr-2 font-medium">วันที่ <span class="text-red-500">*</span></span>
            <input type="text" name="evaluation_date" class="datepicker-th w-36 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-gray-800 @error('evaluation_date') border-red-500 border-b-2 placeholder-red-400 text-red-500 @enderror" @error('evaluation_date') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluation_date', $prefillCandidate['evaluation_date'] ?? date('Y-m-d')) }}">
          </div>
        </div>

        <!-- Candidate Info Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6 text-sm text-black">
          <!-- Item 1: Candidate Name -->
          <div class="flex flex-wrap sm:flex-nowrap items-end w-full gap-y-1">
            <div class="flex items-center shrink-0 mr-2 mb-1">
              <span class="whitespace-nowrap mr-2 font-medium">ชื่อ-นามสกุล <span class="text-red-500">*</span></span>
              <span class="text-xs text-gray-500 mr-1.5">(นาย/นาง/นางสาว)</span>
              <div class="flex items-center space-x-1.5">
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="candidate_prefix" id="prefix_mr" value="นาย" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('candidate_prefix') ring-2 ring-red-500 border-red-500 text-red-500 @enderror" {{ old('candidate_prefix', $prefillCandidate['prefix'] ?? '') == 'นาย' ? 'checked' : '' }}>
                  <span class="ml-0.5 text-xs">นาย</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="candidate_prefix" id="prefix_mrs" value="นาง" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('candidate_prefix') ring-2 ring-red-500 border-red-500 text-red-500 @enderror" {{ old('candidate_prefix', $prefillCandidate['prefix'] ?? '') == 'นาง' ? 'checked' : '' }}>
                  <span class="ml-0.5 text-xs">นาง</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="candidate_prefix" id="prefix_miss" value="นางสาว" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('candidate_prefix') ring-2 ring-red-500 border-red-500 text-red-500 @enderror" {{ old('candidate_prefix', $prefillCandidate['prefix'] ?? '') == 'นางสาว' ? 'checked' : '' }}>
                  <span class="ml-0.5 text-xs">นางสาว</span>
                </label>
              </div>
            </div>
            <input type="text" name="candidate_name" id="candidate_name_input" list="candidate_names" autocomplete="off" class="flex-1 min-w-[120px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 placeholder-gray-400 @error('candidate_name') border-red-500 border-b-2 placeholder-red-400 text-red-500 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('candidate_name') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('candidate_name', $prefillCandidate['name'] ?? '') }}">
          </div>

          <!-- Item 2: Position Applied -->
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">ตำแหน่งที่สมัคร <span class="text-red-500">*</span></span>
            <input type="text" name="position_applied" id="position_applied_input" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 placeholder-gray-400 @error('position_applied') border-red-500 border-b-2 placeholder-red-400 text-red-500 @enderror" value="{{ old('position_applied', $prefillCandidate['position'] ?? '') }}" placeholder="ระบุตำแหน่งที่สมัคร..." @error('position_applied') placeholder="กรุณาระบุข้อมูล" @enderror>
          </div>

          <!-- Item 3: Department -->
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">แผนก <span class="text-red-500">*</span></span>
            <input type="text" name="department" id="department_input" list="department_list" autocomplete="off" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 placeholder-gray-400 @error('department') border-red-500 border-b-2 placeholder-red-400 text-red-500 @enderror" value="{{ old('department', $prefillCandidate['department'] ?? '') }}" placeholder="เลือกหรือพิมพ์แผนก..." @error('department') placeholder="กรุณาระบุข้อมูล" @enderror>
          </div>

          <!-- Item 4: Division & Interview Times -->
          <div class="flex items-end w-full gap-x-4">
            <div class="flex items-end flex-1 min-w-0">
              <span class="whitespace-nowrap mr-2 font-medium shrink-0">ฝ่าย <span class="text-red-500">*</span></span>
              <input type="text" name="division" id="division_input" list="division_list" autocomplete="off" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 placeholder-gray-400 @error('division') border-red-500 border-b-2 placeholder-red-400 text-red-500 @enderror" value="{{ old('division', $prefillCandidate['division'] ?? '') }}" placeholder="เลือกหรือพิมพ์ฝ่าย..." @error('division') placeholder="กรุณาระบุข้อมูล" @enderror>
            </div>
            <div class="flex items-end shrink-0">
              <span class="whitespace-nowrap mr-2 font-medium shrink-0">สัมภาษณ์ครั้งที่</span>
              <input type="number" min="1" name="interview_times" class="w-16 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center" value="{{ old('interview_times', $prefillCandidate['interview_times'] ?? 1) }}">
            </div>
          </div>
        </div>

        <!-- Evaluation Matrix Table -->
        <div class="mb-6 border border-black overflow-x-auto rounded-xs">
          <table class="w-full text-xs sm:text-sm border-collapse text-black table-fixed">
            <thead>
              <tr class="border-b border-black divide-x divide-black bg-gray-50 text-xs">
                <th class="py-2 px-1 w-10 text-center font-bold align-middle" rowspan="2">ลำดับ</th>
                <th class="py-2 px-3 text-center font-bold align-middle" rowspan="2">หัวข้อในการพิจารณา</th>
                <th class="py-1.5 px-1 font-bold text-center border-b border-black w-36 sm:w-44 {{ $isHrColumnEditable ? 'bg-blue-50/80 text-blue-950' : 'bg-gray-100/70 text-gray-700' }}" colspan="4">
                  <div class="flex flex-col items-center justify-center gap-0.5">
                    <span class="font-bold">❶ ฝ่ายบุคคล</span>
                    @if($isHrColumnEditable)
                      <span class="inline-flex items-center gap-1 text-[10px] bg-blue-600 text-white font-semibold px-2 py-0.2 rounded-full shadow-xs">● กำลังประเมิน</span>
                    @elseif(!empty($existingEvaluation?->hr_evaluator_name))
                      <span class="inline-flex items-center gap-0.5 text-[10px] bg-emerald-100 text-emerald-800 font-semibold px-1.5 py-0.2 rounded border border-emerald-300">✓ ลงชื่อแล้ว (ล็อค)</span>
                    @else
                      <span class="inline-flex items-center gap-0.5 text-[10px] bg-gray-200 text-gray-600 font-medium px-1.5 py-0.2 rounded">🔒 ล็อค</span>
                    @endif
                  </div>
                </th>
                <th class="py-1.5 px-1 font-bold text-center border-b border-black w-36 sm:w-44 {{ $isDeptColumnEditable ? 'bg-purple-50/80 text-purple-950' : 'bg-gray-100/70 text-gray-700' }}" colspan="4">
                  <div class="flex flex-col items-center justify-center gap-0.5">
                    <span class="font-bold">❷ ต้นสังกัด</span>
                    @if($isDeptColumnEditable)
                      <span class="inline-flex items-center gap-1 text-[10px] bg-purple-600 text-white font-semibold px-2 py-0.2 rounded-full shadow-xs">● กำลังประเมิน</span>
                    @elseif($activePhase === 'hr_eval')
                      <span class="inline-flex items-center gap-0.5 text-[10px] bg-amber-100 text-amber-800 font-medium px-1.5 py-0.2 rounded border border-amber-300">🔒 รอฝ่ายบุคคลส่งต่อ</span>
                    @elseif(!empty($existingEvaluation?->dept_evaluator_name))
                      <span class="inline-flex items-center gap-0.5 text-[10px] bg-emerald-100 text-emerald-800 font-semibold px-1.5 py-0.2 rounded border border-emerald-300">✓ ลงชื่อแล้ว</span>
                    @else
                      <span class="inline-flex items-center gap-0.5 text-[10px] bg-gray-200 text-gray-600 font-medium px-1.5 py-0.2 rounded">🔒 ล็อค</span>
                    @endif
                  </div>
                </th>
              </tr>
              <tr class="border-b border-black divide-x divide-black bg-gray-50 text-[11px] sm:text-xs">
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 border-l border-black {{ $isHrColumnEditable ? 'bg-blue-50/40' : '' }}">ปรับปรุง<br><span class="font-bold">1</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 {{ $isHrColumnEditable ? 'bg-blue-50/40' : '' }}">พอใช้<br><span class="font-bold">2</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 {{ $isHrColumnEditable ? 'bg-blue-50/40' : '' }}">ดี<br><span class="font-bold">3</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 {{ $isHrColumnEditable ? 'bg-blue-50/40' : '' }}">ดีมาก<br><span class="font-bold">4</span></th>
                
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 border-l border-black {{ $isDeptColumnEditable ? 'bg-purple-50/40' : '' }}">ปรับปรุง<br><span class="font-bold">1</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 {{ $isDeptColumnEditable ? 'bg-purple-50/40' : '' }}">พอใช้<br><span class="font-bold">2</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 {{ $isDeptColumnEditable ? 'bg-purple-50/40' : '' }}">ดี<br><span class="font-bold">3</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 {{ $isDeptColumnEditable ? 'bg-purple-50/40' : '' }}">ดีมาก<br><span class="font-bold">4</span></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-black">
              @php
                $topics = \App\Http\Controllers\InterviewEvaluationController::$topics;
              @endphp

              @foreach($topics as $index => $topicText)
                <tr class="divide-x divide-black">
                  <td class="py-2 px-2 text-center align-top font-medium">{{ $index }}</td>
                  <td class="py-2 px-3 text-left align-top">
                    @php
                      $parts = explode(' : ', $topicText, 2);
                    @endphp
                    <span class="font-bold">{{ $parts[0] }}</span>
                    @if(isset($parts[1]))
                      <span> : {{ $parts[1] }}</span>
                    @endif
                  </td>

                  <!-- HR Rating 1-4 (ฝ่ายบุคคล) -->
                  @for($score = 1; $score <= 4; $score++)
                    @php
                      $valHr = old("hr_score.{$index}", $existingScores[$index]->hr_score ?? null);
                    @endphp
                    <td class="p-0 text-center align-middle {{ !$isHrColumnEditable ? 'bg-gray-100/50 cursor-not-allowed' : 'hover:bg-blue-50/50' }}" title="{{ !$isHrColumnEditable ? 'ช่องฝ่ายบุคคล (ล็อค - ไม่สามารถแก้ไขได้)' : '' }}">
                      <input type="radio" 
                             name="hr_score[{{ $index }}]" 
                             value="{{ $score }}" 
                             class="hr-rating form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent {{ $isHrColumnEditable ? 'cursor-pointer' : 'cursor-not-allowed opacity-75' }} @error("hr_score.{$index}") ring-2 ring-red-500 border-red-500 text-red-500 @enderror" 
                             data-item="{{ $index }}" 
                             data-score="{{ $score }}" 
                             {{ (string)$valHr === (string)$score ? 'checked' : '' }}
                             {{ !$isHrColumnEditable ? 'disabled' : '' }}>
                    </td>
                  @endfor
                  @if(!$isHrColumnEditable && !empty($existingScores[$index]->hr_score))
                    <input type="hidden" name="hr_score[{{ $index }}]" value="{{ $existingScores[$index]->hr_score }}">
                  @endif

                  <!-- Dept Rating 1-4 (ต้นสังกัด) -->
                  @for($score = 1; $score <= 4; $score++)
                    @php
                      $valDept = old("dept_score.{$index}", $existingScores[$index]->dept_score ?? null);
                    @endphp
                    <td class="p-0 text-center align-middle {{ !$isDeptColumnEditable ? 'bg-gray-100/50 cursor-not-allowed' : 'hover:bg-purple-50/50' }}" title="{{ !$isDeptColumnEditable ? ($activePhase === 'hr_eval' ? 'ช่องต้นสังกัด (ล็อค - รอฝ่ายบุคคลประเมินและส่งต่อก่อน)' : 'ช่องต้นสังกัด (ล็อค - ไม่สามารถแก้ไขได้)') : '' }}">
                      <input type="radio" 
                             name="dept_score[{{ $index }}]" 
                             value="{{ $score }}" 
                             class="dept-rating form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent {{ $isDeptColumnEditable ? 'cursor-pointer' : 'cursor-not-allowed opacity-75' }} @error("dept_score.{$index}") ring-2 ring-red-500 border-red-500 text-red-500 @enderror" 
                             data-item="{{ $index }}" 
                             data-score="{{ $score }}" 
                             {{ (string)$valDept === (string)$score ? 'checked' : '' }}
                             {{ !$isDeptColumnEditable ? 'disabled' : '' }}>
                    </td>
                  @endfor
                  @if(!$isDeptColumnEditable && !empty($existingScores[$index]->dept_score))
                    <input type="hidden" name="dept_score[{{ $index }}]" value="{{ $existingScores[$index]->dept_score }}">
                  @endif
                </tr>
              @endforeach

              <!-- Subtotal Row -->
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">รวมคะแนนแต่ละหัวข้อ</td>
                <td colspan="4" class="py-2 px-2 text-center {{ $isHrColumnEditable ? 'bg-blue-50/60' : '' }}"><span id="sum_hr_display">{{ $existingEvaluation?->total_hr_score ?? 0 }}</span> คะแนน</td>
                <td colspan="4" class="py-2 px-2 text-center {{ $isDeptColumnEditable ? 'bg-purple-50/60' : '' }}"><span id="sum_dept_display">{{ $existingEvaluation?->total_dept_score ?? 0 }}</span> คะแนน</td>
              </tr>

              <!-- Total Grand Row -->
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">รวมคะแนนทั้งหมด</td>
                <td colspan="8" class="py-2 px-4 text-left"><span id="grand_total_display">{{ $existingEvaluation?->grand_total_score ?? 0 }}</span> คะแนน</td>
              </tr>

              <!-- Average Score Row -->
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">คะแนนรวม (คะแนน 1+2 หารสอง)</td>
                <td colspan="8" class="py-2 px-4 text-left"><span id="average_score_display">{{ $existingEvaluation?->average_score ?? 0 }}</span> คะแนน</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Remarks Section -->
        <div class="mb-6 text-sm text-black">
          <div class="flex items-start">
            <span class="whitespace-nowrap font-bold mr-2 mt-1">หมายเหตุ :</span>
            <textarea name="remarks" rows="2" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-1 border-t-0 border-l-0 border-r-0 focus:ring-0 resize-none leading-relaxed" placeholder="ข้อความหมายเหตุเพิ่มเติม...">{{ old('remarks', $existingEvaluation?->remarks) }}</textarea>
          </div>
        </div>

        <!-- Summary Result Selection Section -->
        <div class="mb-8 p-4 border border-gray-400 rounded text-sm text-black space-y-3 {{ $activePhase === 'hr_eval' && !$isSystemAdmin ? 'bg-gray-50/70 text-gray-500' : '' }}">
          <div class="flex items-center justify-between">
            <div class="font-bold">สรุปผลการสัมภาษณ์ : <span class="text-red-500 {{ $activePhase === 'hr_eval' ? 'hidden' : '' }}">*</span></div>
            @if($activePhase === 'hr_eval' && !$isSystemAdmin)
              <span class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded font-medium">
                <i class="fa-solid fa-lock text-[10px] mr-1"></i>สรุปผลจะดำเนินการในขั้นตอนที่ 2 โดยต้นสังกัด
              </span>
            @endif
          </div>
          
          @php
            $currentSummary = old('summary_result', $existingEvaluation?->summary_result);
            $isSummaryDisabled = ($activePhase === 'hr_eval' && !$isSystemAdmin);
          @endphp
          <label class="flex items-center space-x-3 {{ $isSummaryDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
            <input type="radio" name="summary_result" value="hire" id="result_hire" class="summary-result-radio form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent" {{ $currentSummary === 'hire' ? 'checked' : '' }} {{ $isSummaryDisabled ? 'disabled' : '' }}>
            <span>ควรว่าจ้างในตำแหน่งที่สมัคร (30 – 40 คะแนน)</span>
          </label>

          <label class="flex items-center space-x-3 {{ $isSummaryDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
            <input type="radio" name="summary_result" value="reserve" id="result_reserve" class="summary-result-radio form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent" {{ $currentSummary === 'reserve' ? 'checked' : '' }} {{ $isSummaryDisabled ? 'disabled' : '' }}>
            <span>ควรสำรองไว้กรณีมีการร้องขอพนักงาน (20 – 29 คะแนน)</span>
          </label>

          <label class="flex items-center space-x-3 {{ $isSummaryDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
            <input type="radio" name="summary_result" value="reject" id="result_reject" class="summary-result-radio form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent" {{ $currentSummary === 'reject' ? 'checked' : '' }} {{ $isSummaryDisabled ? 'disabled' : '' }}>
            <span>ปฏิเสธการว่าจ้างเป็นพนักงาน (ต่ำกว่า 20 คะแนน)</span>
          </label>

          @if($isSummaryDisabled && !empty($currentSummary))
            <input type="hidden" name="summary_result" value="{{ $currentSummary }}">
          @endif
        </div>

        <!-- Signatures Section with Mandatory Role Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-black pt-4">
          @php
            $hasHrSigned = !empty(old('hr_evaluator_name', $existingEvaluation?->hr_evaluator_name));
            $hasDeptSigned = !empty(old('dept_evaluator_name', $existingEvaluation?->dept_evaluator_name));
          @endphp

          <!-- HR Signatory Box -->
          <div id="hr_signature_box" class="flex flex-col items-center space-y-3 p-4 rounded-xl border {{ $activePhase === 'hr_eval' ? 'border-blue-300 bg-blue-50/20' : 'border-gray-300 bg-gray-50/40' }} relative">
            <input type="hidden" name="hr_is_signed" id="hr_is_signed" value="{{ $hasHrSigned ? '1' : '0' }}">
            
            <div class="w-full flex items-center justify-between mb-1">
              <span class="text-xs font-bold text-gray-700 flex items-center gap-1">
                <span>ฝ่ายบุคคล (HR)</span>
                @if($activePhase === 'hr_eval')
                  <span class="text-red-500 font-bold">* ต้องลงชื่อก่อนส่ง</span>
                @endif
              </span>
              @if($hasHrSigned)
                <span id="hr_signed_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-emerald-100 text-emerald-800 font-bold border border-emerald-300">
                  <i class="fa-solid fa-circle-check text-emerald-600"></i> ลงชื่อแล้ว
                </span>
              @else
                <span id="hr_signed_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-red-100 text-red-800 font-bold border border-red-200">
                  <i class="fa-solid fa-triangle-exclamation text-red-600"></i> ยังไม่ลงชื่อ
                </span>
              @endif
            </div>

            <!-- Action button for HR signature (Required before submission) -->
            @if($activePhase === 'hr_eval')
              <div class="w-full my-2">
                @if($canSignHr)
                  <button type="button" 
                          id="hr_sign_btn" 
                          onclick="signHrNow()" 
                          class="w-full py-2.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 cursor-pointer ring-2 ring-blue-300 focus:outline-none">
                    <i class="fa-solid fa-signature text-sm"></i>
                    <span id="hr_sign_btn_text">{{ $hasHrSigned ? '✍️ กดเพื่อลงชื่อใหม่ / ยืนยันอีกครั้ง' : '✍️ กดลงชื่อ ฝ่ายบุคคล (บังคับก่อนส่ง)' }}</span>
                  </button>
                @else
                  <div class="p-2.5 bg-gray-100 border border-gray-300 rounded-xl text-center text-xs text-gray-500">
                    <i class="fa-solid fa-lock mr-1"></i> เฉพาะเจ้าหน้าที่ฝ่ายบุคคลเท่านั้นที่สามารถลงชื่อในช่องนี้ได้
                  </div>
                @endif
              </div>
            @endif

            <div class="flex items-end w-full">
              <span class="mr-2 font-medium">ลงชื่อ</span>
              <input type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ฝ่ายบุคคล</span>
            </div>
            
            <div class="flex items-center w-full relative group">
              <span class="font-medium">(</span>
              <input type="text" 
                     name="hr_evaluator_name" 
                     id="hr_evaluator_name" 
                     list="employee_names" 
                     class="text-center flex-grow min-w-0 bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 text-sm font-semibold text-gray-900 {{ $activePhase !== 'hr_eval' && !$isSystemAdmin ? 'cursor-not-allowed pointer-events-none' : '' }}" 
                     placeholder="คลิกปุ่มลงชื่อด้านบน หรือพิมพ์ชื่อ..." 
                     value="{{ old('hr_evaluator_name', $existingEvaluation?->hr_evaluator_name) }}"
                     {{ $activePhase !== 'hr_eval' && !$isSystemAdmin ? 'readonly' : '' }}>
              <span class="font-medium">)</span>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">ตำแหน่ง</span>
              <input type="text" 
                     name="hr_position" 
                     id="hr_position"
                     class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs {{ $activePhase !== 'hr_eval' && !$isSystemAdmin ? 'cursor-not-allowed pointer-events-none' : '' }}" 
                     value="{{ old('hr_position', $existingEvaluation?->hr_position) }}"
                     placeholder="ระบุตำแหน่ง..."
                     {{ $activePhase !== 'hr_eval' && !$isSystemAdmin ? 'readonly' : '' }}>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">วัน/เดือน/ปี</span>
              <input type="text" 
                     name="hr_signed_date" 
                     id="hr_signed_date"
                     class="datepicker-th flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs {{ $activePhase !== 'hr_eval' && !$isSystemAdmin ? 'cursor-not-allowed pointer-events-none' : '' }}" 
                     value="{{ old('hr_signed_date', $existingEvaluation?->hr_signed_date ? $existingEvaluation->hr_signed_date->format('Y-m-d') : date('Y-m-d')) }}"
                     {{ $activePhase !== 'hr_eval' && !$isSystemAdmin ? 'readonly' : '' }}>
            </div>
          </div>

          <!-- Department Signatory Box -->
          <div id="dept_signature_box" class="flex flex-col items-center space-y-3 p-4 rounded-xl border {{ $activePhase === 'dept_eval' ? 'border-purple-300 bg-purple-50/20' : 'border-gray-300 bg-gray-50/40' }} relative">
            <input type="hidden" name="dept_is_signed" id="dept_is_signed" value="{{ $hasDeptSigned ? '1' : '0' }}">
            
            <div class="w-full flex items-center justify-between mb-1">
              <span class="text-xs font-bold text-gray-700 flex items-center gap-1">
                <span>ต้นสังกัด (Department)</span>
                @if($activePhase === 'dept_eval')
                  <span class="text-red-500 font-bold">* ต้องลงชื่อก่อนส่ง</span>
                @endif
              </span>
              @if($hasDeptSigned)
                <span id="dept_signed_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-emerald-100 text-emerald-800 font-bold border border-emerald-300">
                  <i class="fa-solid fa-circle-check text-emerald-600"></i> ลงชื่อแล้ว
                </span>
              @elseif($activePhase === 'hr_eval')
                <span id="dept_signed_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-gray-100 text-gray-500 font-medium border border-gray-200">
                  <i class="fa-solid fa-lock text-gray-400"></i> รอดำเนินการ
                </span>
              @else
                <span id="dept_signed_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-red-100 text-red-800 font-bold border border-red-200">
                  <i class="fa-solid fa-triangle-exclamation text-red-600"></i> ยังไม่ลงชื่อ
                </span>
              @endif
            </div>

            <!-- Action button for Dept signature (Required before submission in Phase 2) -->
            @if($activePhase === 'dept_eval')
              <div class="w-full my-2">
                @if($canSignDept)
                  <button type="button" 
                          id="dept_sign_btn" 
                          onclick="signDeptNow()" 
                          class="w-full py-2.5 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 cursor-pointer ring-2 ring-purple-300 focus:outline-none">
                    <i class="fa-solid fa-signature text-sm"></i>
                    <span id="dept_sign_btn_text">{{ $hasDeptSigned ? '✍️ กดเพื่อลงชื่อใหม่ / ยืนยันอีกครั้ง' : '✍️ กดลงชื่อ ต้นสังกัด (บังคับก่อนส่ง)' }}</span>
                  </button>
                @else
                  <div class="p-2.5 bg-gray-100 border border-gray-300 rounded-xl text-center text-xs text-gray-500">
                    <i class="fa-solid fa-lock mr-1"></i> เฉพาะผู้แทนต้นสังกัดหรือกรรมการสัมภาษณ์เท่านั้นที่สามารถลงชื่อได้
                  </div>
                @endif
              </div>
            @elseif($activePhase === 'hr_eval')
              <div class="w-full my-2 p-2.5 bg-gray-100/90 border border-dashed border-gray-300 rounded-xl text-center text-xs text-gray-500">
                <i class="fa-solid fa-lock mr-1 text-gray-400"></i> ต้นสังกัดจะสามารถลงชื่อได้หลังจากฝ่ายบุคคลส่งต่อ
              </div>
            @endif

            <div class="flex items-end w-full">
              <span class="mr-2 font-medium">ลงชื่อ</span>
              <input type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ต้นสังกัด</span>
            </div>
            
            <div class="flex items-center w-full relative group">
              <span class="font-medium">(</span>
              <input type="text" 
                     name="dept_evaluator_name" 
                     id="dept_evaluator_name" 
                     list="employee_names" 
                     class="text-center flex-grow min-w-0 bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 text-sm font-semibold text-gray-900 {{ $activePhase !== 'dept_eval' && !$isSystemAdmin ? 'cursor-not-allowed pointer-events-none' : '' }}" 
                     placeholder="{{ $activePhase === 'hr_eval' ? 'รอฝ่ายบุคคลส่งต่อ...' : 'คลิกปุ่มลงชื่อด้านบน หรือพิมพ์ชื่อ...' }}" 
                     value="{{ old('dept_evaluator_name', $existingEvaluation?->dept_evaluator_name) }}"
                     {{ $activePhase !== 'dept_eval' && !$isSystemAdmin ? 'readonly' : '' }}>
              <span class="font-medium">)</span>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">ตำแหน่ง</span>
              <input type="text" 
                     name="dept_position" 
                     id="dept_position"
                     class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs {{ $activePhase !== 'dept_eval' && !$isSystemAdmin ? 'cursor-not-allowed pointer-events-none' : '' }}" 
                     value="{{ old('dept_position', $existingEvaluation?->dept_position) }}"
                     placeholder="ระบุตำแหน่ง..."
                     {{ $activePhase !== 'dept_eval' && !$isSystemAdmin ? 'readonly' : '' }}>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">วัน/เดือน/ปี</span>
              <input type="text" 
                     name="dept_signed_date" 
                     id="dept_signed_date"
                     class="datepicker-th flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs {{ $activePhase !== 'dept_eval' && !$isSystemAdmin ? 'cursor-not-allowed pointer-events-none' : '' }}" 
                     value="{{ old('dept_signed_date', $existingEvaluation?->dept_signed_date ? $existingEvaluation->dept_signed_date->format('Y-m-d') : date('Y-m-d')) }}"
                     {{ $activePhase !== 'dept_eval' && !$isSystemAdmin ? 'readonly' : '' }}>
            </div>
          </div>
        </div>

        <!-- Footer Document Code -->
        <div class="flex justify-end mt-10 text-xs text-gray-500 text-black">
          <div>QF-HR-25 Rev.04 : 31-07-26</div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center space-x-3 print:hidden">
          @php
            $cancelUrl = $returnUrl ?? request('return_url') ?? route('admin.interview-evaluations.index');
          @endphp
          <a href="{{ $cancelUrl }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition shadow-xs">
            ยกเลิก
          </a>

          @if($activePhase === 'hr_eval')
            <button type="submit" id="submitBtn" class="px-7 py-3 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200 transition flex items-center space-x-2 cursor-pointer">
              <i class="fa-solid fa-paper-plane text-xs"></i>
              <span>🚀 บันทึกและส่งต่อให้ต้นสังกัดประเมิน</span>
            </button>
          @elseif($activePhase === 'dept_eval')
            <button type="submit" id="submitBtn" class="px-7 py-3 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 transition flex items-center space-x-2 cursor-pointer">
              <i class="fa-solid fa-circle-check text-xs"></i>
              <span>✅ บันทึกผลการประเมิน (เสร็จสมบูรณ์)</span>
            </button>
          @else
            <button type="submit" id="submitBtn" class="px-7 py-3 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-slate-900 hover:bg-black focus:outline-none transition flex items-center space-x-2 cursor-pointer">
              <i class="fa-solid fa-floppy-disk text-xs"></i>
              <span>บันทึกแก้ไขข้อมูลแบบประเมิน</span>
            </button>
          @endif
        </div>

      </div>
      </div>
    </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Flatpickr setup for Thai localization -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.th) {
        flatpickr.localize(flatpickr.l10ns.th);
      }

      flatpickr(".datepicker-th", {
        disableMobile: true,
        locale: (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.th) ? flatpickr.l10ns.th : "th",
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        allowInput: true,
        formatDate: function(date, formatStr, locale) {
          if (formatStr === 'd/m/Y') {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear() + 543;
            return day + '/' + month + '/' + year;
          }
          return flatpickr.formatDate(date, formatStr, locale);
        },
        onReady: function(selectedDates, dateStr, instance) {
          if (instance.altInput) {
            instance.altInput.className = instance.input.className;
            instance.altInput.classList.remove('datepicker-th');
            instance.altInput.classList.add('flatpickr-input');
          }
          formatHeaderBuddhistYear(instance);
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
          formatHeaderBuddhistYear(instance);
        },
        onYearChange: function(selectedDates, dateStr, instance) {
          formatHeaderBuddhistYear(instance);
        },
        onOpen: function(selectedDates, dateStr, instance) {
          formatHeaderBuddhistYear(instance);
        }
      });

      function formatHeaderBuddhistYear(instance) {
        setTimeout(function() {
          if (!instance || !instance.calendarContainer) return;
          const curYearElem = instance.calendarContainer.querySelector('.flatpickr-current-month .cur-year');
          if (curYearElem) {
            const bYear = instance.currentYear + 543;
            curYearElem.value = bYear;
          }
          const numYearInputs = instance.calendarContainer.querySelectorAll('.cur-year');
          numYearInputs.forEach(function(inp) {
            inp.value = instance.currentYear + 543;
          });
        }, 10);
      }

      @if(session('success'))
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'บันทึกสำเร็จ!',
          text: '{{ session('success') }}',
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true,
          background: '#ffffff',
          color: '#1f2937'
        });
      @endif

      @if(session('error'))
        Swal.fire({
          icon: 'error',
          title: 'ข้อผิดพลาด!',
          text: '{{ session('error') }}',
          confirmButtonColor: '#000000',
          confirmButtonText: 'ตกลง',
          customClass: {
            popup: 'rounded-2xl shadow-xl'
          }
        });
      @endif

      // Auto-fill candidate info on select
      const candidateInput = document.getElementById('candidate_name_input');
      const candidateDatalist = document.getElementById('candidate_names');

      function fillCandidateData(val) {
        if (!val || !candidateDatalist) return;
        const cleanVal = val.trim().toLowerCase();
        const options = candidateDatalist.querySelectorAll('option');
        for (let opt of options) {
          if (opt.value.trim().toLowerCase() === cleanVal) {
            // 1. Prefix
            const prefix = opt.getAttribute('data-prefix');
            if (prefix) {
              const r = document.querySelector(`input[name="candidate_prefix"][value="${prefix}"]`);
              if (r) {
                r.checked = true;
                document.querySelectorAll('input[name="candidate_prefix"]').forEach(el => el.classList.remove('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500'));
              }
            }
            // 2. Position
            const pos = opt.getAttribute('data-position');
            const posInp = document.getElementById('position_applied_input');
            if (pos && posInp && (!posInp.value || posInp.value.trim() === '')) {
              posInp.value = pos;
              posInp.classList.remove('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
            }
            // 3. Department
            const dept = opt.getAttribute('data-department');
            const deptInp = document.getElementById('department_input');
            if (dept && deptInp && (!deptInp.value || deptInp.value.trim() === '')) {
              deptInp.value = dept;
              deptInp.classList.remove('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
            }
            // 4. Division
            const div = opt.getAttribute('data-division');
            const divInp = document.getElementById('division_input');
            if (div && divInp && (!divInp.value || divInp.value.trim() === '')) {
              divInp.value = div;
              divInp.classList.remove('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
            }
            break;
          }
        }
      }

      if (candidateInput) {
        candidateInput.addEventListener('input', function() {
          fillCandidateData(this.value);
        });
        candidateInput.addEventListener('change', function() {
          fillCandidateData(this.value);
        });

        // Trigger on initial load if prefilled
        if (candidateInput.value) {
          fillCandidateData(candidateInput.value);
        }
      }

      // Open datalist dropdown automatically on click / focus
      ['#candidate_name_input', '#department_input', '#division_input', '#hr_evaluator_name', '#dept_evaluator_name'].forEach(sel => {
        const el = document.querySelector(sel);
        if (el) {
          const trigger = () => {
            try {
              if (typeof el.showPicker === 'function') {
                el.showPicker();
              }
            } catch (e) {}
          };
          el.addEventListener('focus', trigger);
          el.addEventListener('click', trigger);
        }
      });

      // Score matrix calculations
      function calculateScores() {
        let hrTotal = 0;
        let deptTotal = 0;
        let hrCount = 0;
        let deptCount = 0;

        document.querySelectorAll('.hr-rating:checked').forEach(el => {
          hrTotal += parseInt(el.value) || 0;
          hrCount++;
        });

        document.querySelectorAll('.dept-rating:checked').forEach(el => {
          deptTotal += parseInt(el.value) || 0;
          deptCount++;
        });

        const grandTotal = hrTotal + deptTotal;
        const bothEvaluated = (hrCount > 0 && deptCount > 0);
        const avgScore = bothEvaluated ? (grandTotal / 2).toFixed(1) : (hrCount > 0 ? (hrTotal / 2).toFixed(1) : (deptTotal / 2).toFixed(1));

        document.getElementById('sum_hr_display').innerText = hrTotal;
        document.getElementById('sum_dept_display').innerText = deptTotal;
        document.getElementById('grand_total_display').innerText = grandTotal;
        document.getElementById('average_score_display').innerText = (hrCount === 10 && deptCount === 10) ? avgScore : (avgScore + (hrCount === 0 || deptCount === 0 ? ' (รอประเมินอีกฝ่าย)' : ''));

        // Auto select summary result based on score criteria
        const scoreForEval = parseFloat(avgScore);
        if (scoreForEval >= 30) {
          document.getElementById('result_hire').checked = true;
        } else if (scoreForEval >= 20) {
          document.getElementById('result_reserve').checked = true;
        } else if (scoreForEval > 0) {
          document.getElementById('result_reject').checked = true;
        }
      }

      // Allow clicking already checked radio to uncheck it
      document.querySelectorAll('.hr-rating, .dept-rating').forEach(el => {
        el.addEventListener('mousedown', function() {
          this.dataset.wasChecked = this.checked ? 'true' : 'false';
        });
        el.addEventListener('click', function() {
          if (this.dataset.wasChecked === 'true') {
            this.checked = false;
            this.dataset.wasChecked = 'false';
            calculateScores();
          }
        });
        el.addEventListener('change', calculateScores);
      });

      calculateScores();

      // Clear red error styling as user types or selects
      document.querySelectorAll('input[type="text"], input[type="number"]').forEach(inp => {
        inp.addEventListener('input', function() {
          this.classList.remove('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
        });
      });
      document.querySelectorAll('input[name="candidate_prefix"]').forEach(r => {
        r.addEventListener('change', function() {
          document.querySelectorAll('input[name="candidate_prefix"]').forEach(el => el.classList.remove('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500'));
        });
      });
      document.querySelectorAll('.hr-rating, .dept-rating').forEach(r => {
        r.addEventListener('change', function() {
          const item = this.getAttribute('data-item');
          if (item) {
            document.querySelectorAll(`input[name="hr_score[${item}]"], input[name="dept_score[${item}]"]`).forEach(el => {
              el.classList.remove('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500');
            });
          }
      // Functions for Signing (ฝ่ายบุคคล & ต้นสังกัด)
      window.signHrNow = function() {
        const userName = "{{ auth()->user() ? (auth()->user()->firstname . ' ' . auth()->user()->lastname) : 'เจ้าหน้าที่ฝ่ายบุคคล' }}";
        const userPosition = "{{ auth()->user()?->position ?: 'ฝ่ายทรัพยากรบุคคล' }}";
        const todayDate = new Date().toISOString().split('T')[0];

        const nameInp = document.getElementById('hr_evaluator_name');
        const posInp = document.getElementById('hr_position');
        const dateInp = document.getElementById('hr_signed_date');
        const signedFlag = document.getElementById('hr_is_signed');
        const badge = document.getElementById('hr_signed_badge');
        const btnText = document.getElementById('hr_sign_btn_text');

        if (nameInp) nameInp.value = userName;
        if (posInp && (!posInp.value || posInp.value.trim() === '')) posInp.value = userPosition;
        if (dateInp) {
          dateInp.value = todayDate;
          if (dateInp._flatpickr) {
            dateInp._flatpickr.setDate(todayDate, true);
          }
        }
        if (signedFlag) signedFlag.value = '1';

        if (badge) {
          badge.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] bg-emerald-100 text-emerald-800 font-bold border border-emerald-300';
          badge.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600"></i> ลงชื่อแล้ว (' + userName + ')';
        }
        if (btnText) {
          btnText.innerText = '✍️ ลงชื่อเรียบร้อยแล้ว (กดเพื่อยืนยันใหม่)';
        }

        const hrBox = document.getElementById('hr_signature_box');
        if (hrBox) {
          hrBox.classList.remove('border-red-400', 'ring-2', 'ring-red-400');
          hrBox.classList.add('border-emerald-400', 'bg-emerald-50/20');
        }

        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'ฝ่ายบุคคลลงชื่อเรียบร้อยแล้ว',
          text: userName + ' (' + (posInp ? posInp.value : userPosition) + ')',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      };

      window.signDeptNow = function() {
        const userName = "{{ auth()->user() ? (auth()->user()->firstname . ' ' . auth()->user()->lastname) : 'หัวหน้าแผนก/ผู้แทนต้นสังกัด' }}";
        const userPosition = "{{ auth()->user()?->position ?: 'ต้นสังกัด' }}";
        const todayDate = new Date().toISOString().split('T')[0];

        const nameInp = document.getElementById('dept_evaluator_name');
        const posInp = document.getElementById('dept_position');
        const dateInp = document.getElementById('dept_signed_date');
        const signedFlag = document.getElementById('dept_is_signed');
        const badge = document.getElementById('dept_signed_badge');
        const btnText = document.getElementById('dept_sign_btn_text');

        if (nameInp) nameInp.value = userName;
        if (posInp && (!posInp.value || posInp.value.trim() === '')) posInp.value = userPosition;
        if (dateInp) {
          dateInp.value = todayDate;
          if (dateInp._flatpickr) {
            dateInp._flatpickr.setDate(todayDate, true);
          }
        }
        if (signedFlag) signedFlag.value = '1';

        if (badge) {
          badge.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] bg-emerald-100 text-emerald-800 font-bold border border-emerald-300';
          badge.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600"></i> ลงชื่อแล้ว (' + userName + ')';
        }
        if (btnText) {
          btnText.innerText = '✍️ ลงชื่อเรียบร้อยแล้ว (กดเพื่อยืนยันใหม่)';
        }

        const deptBox = document.getElementById('dept_signature_box');
        if (deptBox) {
          deptBox.classList.remove('border-red-400', 'ring-2', 'ring-red-400');
          deptBox.classList.add('border-emerald-400', 'bg-emerald-50/20');
        }

        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'ต้นสังกัดลงชื่อเรียบร้อยแล้ว',
          text: userName + ' (' + (posInp ? posInp.value : userPosition) + ')',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      };

      // Client-side Validation: Highlight errors and enforce signature
      function validateAndHighlightErrors() {
        let hasError = false;
        let firstErrorElement = null;
        const activePhase = '{{ $activePhase }}';

        // 1. Candidate Prefix
        const prefixRadios = document.querySelectorAll('input[name="candidate_prefix"]');
        const isPrefixChecked = Array.from(prefixRadios).some(r => r.checked);
        if (!isPrefixChecked) {
          hasError = true;
          prefixRadios.forEach(r => r.classList.add('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500'));
          if (!firstErrorElement) firstErrorElement = prefixRadios[0];
        }

        // 2. Candidate Name
        const nameInput = document.getElementById('candidate_name_input');
        if (nameInput && !nameInput.value.trim()) {
          hasError = true;
          nameInput.classList.add('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
          nameInput.placeholder = 'กรุณาระบุข้อมูล';
          if (!firstErrorElement) firstErrorElement = nameInput;
        }

        // 3. Position Applied
        const posInput = document.getElementById('position_applied_input');
        if (posInput && !posInput.value.trim()) {
          hasError = true;
          posInput.classList.add('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
          posInput.placeholder = 'กรุณาระบุข้อมูล';
          if (!firstErrorElement) firstErrorElement = posInput;
        }

        // 4. Department
        const deptInput = document.getElementById('department_input');
        if (deptInput && !deptInput.value.trim()) {
          hasError = true;
          deptInput.classList.add('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
          deptInput.placeholder = 'กรุณาระบุข้อมูล';
          if (!firstErrorElement) firstErrorElement = deptInput;
        }

        // 5. Division
        const divInput = document.getElementById('division_input');
        if (divInput && !divInput.value.trim()) {
          hasError = true;
          divInput.classList.add('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
          divInput.placeholder = 'กรุณาระบุข้อมูล';
          if (!firstErrorElement) firstErrorElement = divInput;
        }

        // 6. Evaluation Date
        const dateInput = document.querySelector('input[name="evaluation_date"]');
        if (dateInput && !dateInput.value.trim()) {
          hasError = true;
          dateInput.classList.add('border-red-500', 'border-b-2', 'placeholder-red-400', 'text-red-500');
          dateInput.placeholder = 'กรุณาระบุข้อมูล';
          if (!firstErrorElement) firstErrorElement = dateInput;
        }

        // 7. Matrix Rating Scores by Phase
        if (activePhase === 'hr_eval') {
          // ฝ่ายบุคคลต้องให้คะแนนครบทั้ง 10 ข้อ
          let hrMissing = 0;
          for (let i = 1; i <= 10; i++) {
            const checked = document.querySelector(`input[name="hr_score[${i}]"]:checked`);
            if (!checked) {
              hrMissing++;
              document.querySelectorAll(`input[name="hr_score[${i}]"]`).forEach(r => r.classList.add('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500'));
              if (!firstErrorElement) firstErrorElement = document.querySelectorAll(`input[name="hr_score[${i}]"]`)[0];
            }
          }
          if (hrMissing > 0) {
            hasError = true;
            Swal.fire({
              icon: 'warning',
              title: 'กรุณาประเมินคะแนนให้ครบถ้วน',
              text: 'เจ้าหน้าที่ฝ่ายบุคคลต้องให้คะแนนในคอลัมน์ "❶ ฝ่ายบุคคล" ให้ครบทั้ง 10 หัวข้อ (ขาดอีก ' + hrMissing + ' หัวข้อ)',
              confirmButtonColor: '#2563eb',
              confirmButtonText: 'ตกลง'
            });
            if (firstErrorElement) firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
          }

          // ฝ่ายบุคคลต้องกดลงชื่อก่อนส่ง
          const hrSignedName = (document.getElementById('hr_evaluator_name')?.value || '').trim();
          if (!hrSignedName) {
            hasError = true;
            const hrBox = document.getElementById('hr_signature_box');
            if (hrBox) {
              hrBox.classList.add('border-red-400', 'ring-2', 'ring-red-400');
              hrBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            Swal.fire({
              icon: 'warning',
              title: 'ฝ่ายบุคคลต้องลงชื่อก่อนส่ง',
              html: '<div class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">ฝ่ายบุคคลต้องกดปุ่ม <strong class="text-blue-600 font-bold">"✍️ กดลงชื่อ ฝ่ายบุคคล"</strong> ก่อนที่จะสามารถบันทึกและส่งต่อให้ต้นสังกัดประเมินต่อได้</div>',
              confirmButtonColor: '#2563eb',
              confirmButtonText: 'ไปที่จุดลงชื่อ'
            }).then(() => {
              const signBtn = document.getElementById('hr_sign_btn');
              if (signBtn) signBtn.focus();
            });
            return false;
          }
        } else if (activePhase === 'dept_eval') {
          // ต้นสังกัดต้องให้คะแนนครบทั้ง 10 ข้อ
          let deptMissing = 0;
          for (let i = 1; i <= 10; i++) {
            const checked = document.querySelector(`input[name="dept_score[${i}]"]:checked`);
            if (!checked) {
              deptMissing++;
              document.querySelectorAll(`input[name="dept_score[${i}]"]`).forEach(r => r.classList.add('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500'));
              if (!firstErrorElement) firstErrorElement = document.querySelectorAll(`input[name="dept_score[${i}]"]`)[0];
            }
          }
          if (deptMissing > 0) {
            hasError = true;
            Swal.fire({
              icon: 'warning',
              title: 'กรุณาประเมินคะแนนให้ครบถ้วน',
              text: 'ต้นสังกัดต้องให้คะแนนในคอลัมน์ "❷ ต้นสังกัด" ให้ครบทั้ง 10 หัวข้อ (ขาดอีก ' + deptMissing + ' หัวข้อ)',
              confirmButtonColor: '#7c3aed',
              confirmButtonText: 'ตกลง'
            });
            if (firstErrorElement) firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
          }

          // สรุปผลการสัมภาษณ์
          const summaryChecked = document.querySelector('input[name="summary_result"]:checked');
          if (!summaryChecked) {
            hasError = true;
            Swal.fire({
              icon: 'warning',
              title: 'กรุณาสรุปผลการสัมภาษณ์',
              text: 'กรุณาเลือกผลสรุปการสัมภาษณ์ (ควรว่าจ้าง / ควรสำรอง / ปฏิเสธการว่าจ้าง)',
              confirmButtonColor: '#7c3aed',
              confirmButtonText: 'ตกลง'
            }).then(() => {
              const hireEl = document.getElementById('result_hire');
              if (hireEl) hireEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
            return false;
          }

          // ต้นสังกัดต้องกดลงชื่อก่อนส่ง
          const deptSignedName = (document.getElementById('dept_evaluator_name')?.value || '').trim();
          if (!deptSignedName) {
            hasError = true;
            const deptBox = document.getElementById('dept_signature_box');
            if (deptBox) {
              deptBox.classList.add('border-red-400', 'ring-2', 'ring-red-400');
              deptBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            Swal.fire({
              icon: 'warning',
              title: 'ต้นสังกัดต้องลงชื่อก่อนส่ง',
              html: '<div class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">ต้นสังกัดต้องกดปุ่ม <strong class="text-purple-600 font-bold">"✍️ กดลงชื่อ ต้นสังกัด"</strong> ก่อนที่จะสามารถบันทึกและสรุปผลการประเมินได้</div>',
              confirmButtonColor: '#7c3aed',
              confirmButtonText: 'ไปที่จุดลงชื่อ'
            }).then(() => {
              const signBtn = document.getElementById('dept_sign_btn');
              if (signBtn) signBtn.focus();
            });
            return false;
          }
        }

        if (hasError && firstErrorElement) {
          firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
          if (typeof firstErrorElement.focus === 'function') firstErrorElement.focus();
        }

        return !hasError;
      }

      // Form Submit Handling with dynamic confirmation
      const evalForm = document.getElementById('interviewEvaluationForm');
      if (evalForm) {
        let isSubmitting = false;

        evalForm.addEventListener('submit', function(e) {
          if (isSubmitting) return;

          const isValid = validateAndHighlightErrors();
          if (!isValid) {
            e.preventDefault();
            return;
          }

          e.preventDefault();
          const nameVal = (candidateInput ? candidateInput.value : '').trim();
          const activePhase = '{{ $activePhase }}';

          let confirmTitle = 'ยืนยันการบันทึกแบบประเมิน?';
          let confirmHtml = '';
          let confirmBtnText = '<i class="fa-solid fa-paper-plane mr-1.5"></i> ใช่, บันทึกข้อมูล';
          let confirmBtnColor = '#000000';

          if (activePhase === 'hr_eval') {
            confirmTitle = 'ยืนยันส่งต่อให้ต้นสังกัดประเมินต่อ?';
            confirmHtml = '<div class="text-sm text-gray-600 leading-relaxed">คุณได้ประเมินคะแนนฝ่ายบุคคลครบ 10 ข้อและ <strong>ลงชื่อเรียบร้อยแล้ว</strong><br>ต้องการบันทึกและ <strong class="text-blue-600 font-bold">ส่งต่อให้ต้นสังกัดประเมินต่อ</strong> ใช่หรือไม่?</div>';
            confirmBtnText = '<i class="fa-solid fa-paper-plane mr-1.5"></i> ใช่, บันทึกและส่งต่อ';
            confirmBtnColor = '#2563eb';
          } else if (activePhase === 'dept_eval') {
            confirmTitle = 'ยืนยันบันทึกผลการประเมิน (เสร็จสมบูรณ์)?';
            confirmHtml = '<div class="text-sm text-gray-600 leading-relaxed">คุณได้ประเมินคะแนนต้นสังกัด สรุปผลการว่าจ้าง และ <strong>ลงชื่อเรียบร้อยแล้ว</strong><br>แบบประเมินนี้จะถูกบันทึกเป็น <strong class="text-emerald-600 font-bold">เสร็จสมบูรณ์ทั้งสองฝ่าย</strong> ใช่หรือไม่?</div>';
            confirmBtnText = '<i class="fa-solid fa-circle-check mr-1.5"></i> ใช่, บันทึกผลประเมิน';
            confirmBtnColor = '#059669';
          } else {
            confirmHtml = '<div class="text-sm text-gray-600 leading-relaxed">คุณต้องการบันทึกแก้ไข <strong>แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน (QF-HR-25)</strong> สำหรับ <strong class="text-gray-900">' + nameVal + '</strong> ใช่หรือไม่?</div>';
          }

          Swal.fire({
            title: confirmTitle,
            html: confirmHtml,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            cancelButtonColor: '#9ca3af',
            confirmButtonText: confirmBtnText,
            cancelButtonText: 'ตรวจสอบอีกครั้ง',
            reverseButtons: true,
            customClass: {
              popup: 'rounded-2xl shadow-xl',
              confirmButton: 'px-5 py-2.5 rounded-xl font-medium shadow',
              cancelButton: 'px-5 py-2.5 rounded-xl font-medium'
            }
          }).then((result) => {
            if (result.isConfirmed) {
              isSubmitting = true;
              Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                text: 'กรุณารอสักครู่ ระบบกำลังประมวลผล',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                  Swal.showLoading();
                }
              });
              evalForm.submit();
            }
          });
        });
      }
    });
  </script>
</x-app-layout>
