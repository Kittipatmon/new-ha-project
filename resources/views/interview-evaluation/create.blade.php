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
            
            <input type="hidden" name="interview_id" value="{{ $interviewId ?? request('interview_id') }}">
            <input type="hidden" name="application_id" value="{{ $applicationId ?? request('application_id') }}">
            <input type="hidden" name="return_url" value="{{ $returnUrl ?? request('return_url') }}">

            <!-- All Pages Wrapper -->
            <div class="hr-form-container">
            <!-- PAGE Paper Container -->
            <div class="hr-form-paper">
        
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
                <th class="py-1 px-1 font-bold text-center border-b border-black w-36 sm:w-44" colspan="4">❶ ฝ่ายบุคคล</th>
                <th class="py-1 px-1 font-bold text-center border-b border-black w-36 sm:w-44" colspan="4">❷ ต้นสังกัด</th>
              </tr>
              <tr class="border-b border-black divide-x divide-black bg-gray-50 text-[11px] sm:text-xs">
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 border-l border-black">ปรับปรุง<br><span class="font-bold">1</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11">พอใช้<br><span class="font-bold">2</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11">ดี<br><span class="font-bold">3</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11">ดีมาก<br><span class="font-bold">4</span></th>
                
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11 border-l border-black">ปรับปรุง<br><span class="font-bold">1</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11">พอใช้<br><span class="font-bold">2</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11">ดี<br><span class="font-bold">3</span></th>
                <th class="py-1 px-0.5 font-normal text-center w-9 sm:w-11">ดีมาก<br><span class="font-bold">4</span></th>
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

                  <!-- HR Rating 1-4 -->
                  @for($score = 1; $score <= 4; $score++)
                    <td class="p-0 text-center align-middle">
                      <input type="radio" name="hr_score[{{ $index }}]" value="{{ $score }}" class="hr-rating form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent cursor-pointer @error("hr_score.{$index}") ring-2 ring-red-500 border-red-500 text-red-500 @enderror" data-item="{{ $index }}" data-score="{{ $score }}" {{ old("hr_score.{$index}") == $score ? 'checked' : '' }}>
                    </td>
                  @endfor

                  <!-- Dept Rating 1-4 -->
                  @for($score = 1; $score <= 4; $score++)
                    <td class="p-0 text-center align-middle">
                      <input type="radio" name="dept_score[{{ $index }}]" value="{{ $score }}" class="dept-rating form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent cursor-pointer @error("dept_score.{$index}") ring-2 ring-red-500 border-red-500 text-red-500 @enderror" data-item="{{ $index }}" data-score="{{ $score }}" {{ old("dept_score.{$index}") == $score ? 'checked' : '' }}>
                    </td>
                  @endfor
                </tr>
              @endforeach

              <!-- Subtotal Row -->
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">รวมคะแนนแต่ละหัวข้อ</td>
                <td colspan="4" class="py-2 px-2 text-center"><span id="sum_hr_display">0</span> คะแนน</td>
                <td colspan="4" class="py-2 px-2 text-center"><span id="sum_dept_display">0</span> คะแนน</td>
              </tr>

              <!-- Total Grand Row -->
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">รวมคะแนนทั้งหมด</td>
                <td colspan="8" class="py-2 px-4 text-left"><span id="grand_total_display">0</span> คะแนน</td>
              </tr>

              <!-- Average Score Row -->
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">คะแนนรวม (คะแนน 1+2 หารสอง)</td>
                <td colspan="8" class="py-2 px-4 text-left"><span id="average_score_display">0</span> คะแนน</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Remarks Section -->
        <div class="mb-6 text-sm text-black">
          <div class="flex items-start">
            <span class="whitespace-nowrap font-bold mr-2 mt-1">หมายเหตุ :</span>
            <textarea name="remarks" rows="2" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-1 border-t-0 border-l-0 border-r-0 focus:ring-0 resize-none leading-relaxed" placeholder="ข้อความหมายเหตุเพิ่มเติม..."></textarea>
          </div>
        </div>

        <!-- Summary Result Selection Section -->
        <div class="mb-8 p-4 border border-gray-400 rounded text-sm text-black space-y-3">
          <div class="font-bold mb-2">สรุปผลการสัมภาษณ์ :</div>
          
          <label class="flex items-center space-x-3 cursor-pointer">
            <input type="radio" name="summary_result" value="hire" id="result_hire" class="summary-result-radio form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
            <span>ควรว่าจ้างในตำแหน่งที่สมัคร (30 – 40 คะแนน)</span>
          </label>

          <label class="flex items-center space-x-3 cursor-pointer">
            <input type="radio" name="summary_result" value="reserve" id="result_reserve" class="summary-result-radio form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
            <span>ควรสำรองไว้กรณีมีการร้องขอพนักงาน (20 – 29 คะแนน)</span>
          </label>

          <label class="flex items-center space-x-3 cursor-pointer">
            <input type="radio" name="summary_result" value="reject" id="result_reject" class="summary-result-radio form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
            <span>ปฏิเสธการว่าจ้างเป็นพนักงาน (ต่ำกว่า 20 คะแนน)</span>
          </label>
        </div>

        <!-- Signatures Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-black pt-4">
          <!-- HR Signatory -->
          <div class="flex flex-col items-center space-y-3">
            <div class="flex items-end w-full">
              <span class="mr-2 font-medium">ลงชื่อ</span>
              <input type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ฝ่ายบุคคล</span>
            </div>
            
            <div class="flex items-center w-full relative group">
              <span class="font-medium">(</span>
              <input type="text" name="hr_evaluator_name" id="hr_evaluator_name" list="employee_names" class="text-center flex-grow min-w-0 bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 text-sm font-medium" placeholder="พิมพ์ชื่อเพื่อค้นหา..." value="{{ old('hr_evaluator_name') }}">
              <button type="button" onclick="document.getElementById('hr_evaluator_name').value = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; if(document.getElementsByName('hr_position')[0]) document.getElementsByName('hr_position')[0].value = 'ฝ่ายทรัพยากรบุคคล';" class="absolute right-4 px-2 py-0.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition cursor-pointer" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
              <span class="font-medium">)</span>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">ตำแหน่ง</span>
              <input type="text" name="hr_position" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('hr_position') }}">
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">วัน/เดือน/ปี</span>
              <input type="text" name="hr_signed_date" class="datepicker-th flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('hr_signed_date', date('Y-m-d')) }}">
            </div>
          </div>

          <!-- Department Signatory -->
          <div class="flex flex-col items-center space-y-3">
            <div class="flex items-end w-full">
              <span class="mr-2 font-medium">ลงชื่อ</span>
              <input type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ต้นสังกัด</span>
            </div>
            
            <div class="flex items-center w-full relative group">
              <span class="font-medium">(</span>
              <input type="text" name="dept_evaluator_name" id="dept_evaluator_name" list="employee_names" class="text-center flex-grow min-w-0 bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 text-sm font-medium" placeholder="พิมพ์ชื่อเพื่อค้นหา..." value="{{ old('dept_evaluator_name') }}">
              <button type="button" onclick="document.getElementById('dept_evaluator_name').value = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; if(document.getElementsByName('dept_position')[0] && !document.getElementsByName('dept_position')[0].value) document.getElementsByName('dept_position')[0].value = '{{ auth()->user()->position ?? 'หัวหน้าแผนก' }}';" class="absolute right-4 px-2 py-0.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition cursor-pointer" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
              <span class="font-medium">)</span>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">ตำแหน่ง</span>
              <input type="text" name="dept_position" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('dept_position') }}">
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">วัน/เดือน/ปี</span>
              <input type="text" name="dept_signed_date" class="datepicker-th flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('dept_signed_date', date('Y-m-d')) }}">
            </div>
          </div>
        </div>

        <!-- Footer Document Code -->
        <div class="flex justify-end mt-10 text-xs text-gray-500 text-black">
          <div>QF-HR-25 Rev.04 : 31-07-26</div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3 print:hidden">
          <a href="{{ route('admin.interview-evaluations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition shadow-sm">
            ยกเลิก
          </a>
          <button type="submit" id="submitBtn" class="px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition flex items-center space-x-1.5 cursor-pointer">
            <i class="fa-solid fa-floppy-disk text-xs"></i>
            <span>บันทึกข้อมูล</span>
          </button>
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

        document.querySelectorAll('.hr-rating:checked').forEach(el => {
          hrTotal += parseInt(el.value) || 0;
        });

        document.querySelectorAll('.dept-rating:checked').forEach(el => {
          deptTotal += parseInt(el.value) || 0;
        });

        const grandTotal = hrTotal + deptTotal;
        const avgScore = (grandTotal / 2).toFixed(1);

        document.getElementById('sum_hr_display').innerText = hrTotal;
        document.getElementById('sum_dept_display').innerText = deptTotal;
        document.getElementById('grand_total_display').innerText = grandTotal;
        document.getElementById('average_score_display').innerText = avgScore;

        // Auto select summary result based on score criteria
        if (avgScore >= 30) {
          document.getElementById('result_hire').checked = true;
        } else if (avgScore >= 20) {
          document.getElementById('result_reserve').checked = true;
        } else if (avgScore > 0) {
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
        });
      });

      // Client-side Validation: Highlight errors in RED like Image 1
      function validateAndHighlightErrors() {
        let hasError = false;
        let firstErrorElement = null;

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

        // 7. Matrix Rating Scores (Topics 1 to 10)
        for (let i = 1; i <= 10; i++) {
          const hrRadios = document.querySelectorAll(`input[name="hr_score[${i}]"]`);
          const deptRadios = document.querySelectorAll(`input[name="dept_score[${i}]"]`);
          const hrChecked = Array.from(hrRadios).some(r => r.checked);
          const deptChecked = Array.from(deptRadios).some(r => r.checked);

          if (!hrChecked && !deptChecked) {
            hasError = true;
            hrRadios.forEach(r => r.classList.add('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500'));
            deptRadios.forEach(r => r.classList.add('ring-2', 'ring-red-500', 'border-red-500', 'text-red-500'));
            if (!firstErrorElement) firstErrorElement = hrRadios[0];
          }
        }

        if (hasError && firstErrorElement) {
          firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
          if (typeof firstErrorElement.focus === 'function') firstErrorElement.focus();
        }

        return !hasError;
      }

      // Form Submit Handling
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

          // If all required fields are filled, ask confirmation
          e.preventDefault();
          const nameVal = (candidateInput ? candidateInput.value : '').trim();

          Swal.fire({
            title: 'ยืนยันการบันทึกแบบประเมิน?',
            html: '<div class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">คุณต้องการบันทึก <strong>แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน (QF-HR-25)</strong> สำหรับ <strong class="text-gray-900">' + nameVal + '</strong> ใช่หรือไม่?</div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#000000',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1.5"></i> ใช่, บันทึกข้อมูล',
            cancelButtonText: 'ยกเลิก',
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
