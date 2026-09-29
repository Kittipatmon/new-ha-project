<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('รายละเอียดแบบประเมินผลการสัมภาษณ์ผู้สมัครงาน') }}
      </h2>
      <div class="flex items-center gap-2">
        <a href="{{ route('interview-evaluation.pdf', $evaluation->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none transition shadow-sm">
          📄 Export PDF
        </a>
        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->isHrOrAdmin()))
          <button type="button" onclick="openShareModal('interview_evaluation', {{ $evaluation->id }}, 'แบบประเมินผลสัมภาษณ์ #{{ $evaluation->id }}')" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-900 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
            </svg>
            แชร์เอกสาร
          </button>
        @endif
      </div>
    </div>
  </x-slot>

  <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen relative overflow-x-hidden">
    <div class="hr-form-container">
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
      <div class="hr-form-paper">
        
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน</h1>
        </div>

        <div class="flex justify-end mb-4 text-sm text-black">
          <div>
            <span class="mr-2 font-medium">วันที่:</span>
            <span class="border-b border-gray-400 px-3 py-0.5 inline-block font-semibold">{{ $evaluation->evaluation_date ? $evaluation->evaluation_date->format('d/m/Y') : '-' }}</span>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6 text-sm text-black">
          <div class="flex items-center">
            <span class="mr-2 font-medium">ชื่อผู้สมัคร:</span>
            <span class="border-b border-gray-400 flex-grow min-w-0 px-2 py-0.5 font-bold">{{ $evaluation->full_candidate_name }}</span>
          </div>

          <div class="flex items-center">
            <span class="mr-2 font-medium">ตำแหน่งที่สมัคร:</span>
            <span class="border-b border-gray-400 flex-grow min-w-0 px-2 py-0.5 font-bold">{{ $evaluation->position_applied ?? '-' }}</span>
          </div>

          <div class="flex items-center">
            <span class="mr-2 font-medium">แผนก:</span>
            <span class="border-b border-gray-400 w-1/2 px-2 py-0.5 font-medium">{{ $evaluation->department ?? '-' }}</span>
            <span class="mx-2 font-medium">ฝ่าย:</span>
            <span class="border-b border-gray-400 flex-grow min-w-0 px-2 py-0.5 font-medium">{{ $evaluation->division ?? '-' }}</span>
          </div>

          <div class="flex items-center">
            <span class="mr-2 font-medium">สัมภาษณ์ครั้งที่:</span>
            <span class="border-b border-gray-400 w-24 text-center px-2 py-0.5 font-bold">{{ $evaluation->interview_times }}</span>
          </div>
        </div>

        <!-- Evaluation Table -->
        <div class="mb-6 border border-black overflow-x-auto">
          <table class="w-full min-w-[700px] text-sm border-collapse text-black">
            <thead>
              <tr class="border-b border-black divide-x divide-black bg-gray-50">
                <th class="py-3 px-2 w-12 text-center font-bold align-middle" rowspan="2">ลำดับ</th>
                <th class="py-3 px-4 text-center font-bold align-middle" rowspan="2">หัวข้อในการพิจารณา</th>
                <th class="py-1 px-2 font-bold text-center border-b border-black" colspan="4">❶ ฝ่ายบุคคล</th>
                <th class="py-1 px-2 font-bold text-center border-b border-black" colspan="4">❷ ต้นสังกัด</th>
              </tr>
              <tr class="border-b border-black divide-x divide-black bg-gray-50 text-xs">
                <th class="py-1 px-1 text-center w-12 border-l border-black">1</th>
                <th class="py-1 px-1 text-center w-12">2</th>
                <th class="py-1 px-1 text-center w-12">3</th>
                <th class="py-1 px-1 text-center w-12">4</th>
                <th class="py-1 px-1 text-center w-12 border-l border-black">1</th>
                <th class="py-1 px-1 text-center w-12">2</th>
                <th class="py-1 px-1 text-center w-12">3</th>
                <th class="py-1 px-1 text-center w-12">4</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-black">
              @php
                $topics = \App\Http\Controllers\InterviewEvaluationController::$topics;
              @endphp

              @foreach($topics as $index => $topicText)
                @php
                  $scoreRecord = $scoresByItem[$index] ?? null;
                  $hrScore = $scoreRecord ? $scoreRecord->hr_score : null;
                  $deptScore = $scoreRecord ? $scoreRecord->dept_score : null;
                  $parts = explode(' : ', $topicText, 2);
                @endphp
                <tr class="divide-x divide-black">
                  <td class="py-2 px-2 text-center align-top font-medium">{{ $index }}</td>
                  <td class="py-2 px-3 text-left align-top">
                    <span class="font-bold">{{ $parts[0] }}</span>
                    @if(isset($parts[1]))
                      <span> : {{ $parts[1] }}</span>
                    @endif
                  </td>

                  <!-- HR Score -->
                  @for($s = 1; $s <= 4; $s++)
                    <td class="p-0 text-center align-middle font-bold text-base">
                      {{ $hrScore == $s ? '✓' : '' }}
                    </td>
                  @endfor

                  <!-- Dept Score -->
                  @for($s = 1; $s <= 4; $s++)
                    <td class="p-0 text-center align-middle font-bold text-base">
                      {{ $deptScore == $s ? '✓' : '' }}
                    </td>
                  @endfor
                </tr>
              @endforeach

              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">รวมคะแนนแต่ละหัวข้อ</td>
                <td colspan="4" class="py-2 px-2 text-center text-blue-800">{{ $evaluation->total_hr_score }} คะแนน</td>
                <td colspan="4" class="py-2 px-2 text-center text-blue-800">{{ $evaluation->total_dept_score }} คะแนน</td>
              </tr>
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">รวมคะแนนทั้งหมด</td>
                <td colspan="8" class="py-2 px-4 text-left text-green-800">{{ $evaluation->grand_total_score }} คะแนน</td>
              </tr>
              <tr class="divide-x divide-black bg-gray-50 font-bold">
                <td colspan="2" class="py-2 px-4 text-center">คะแนนรวม (คะแนน 1+2 หารสอง)</td>
                <td colspan="8" class="py-2 px-4 text-left text-indigo-800">{{ number_format($evaluation->average_score, 1) }} คะแนน</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mb-6 text-sm text-black">
          <div class="flex items-start">
            <span class="whitespace-nowrap font-bold mr-2">หมายเหตุ :</span>
            <div class="flex-grow min-w-0 border-b border-gray-400 pb-1 font-medium">{{ $evaluation->remarks ?? '-' }}</div>
          </div>
        </div>

        <div class="mb-8 p-4 border border-gray-400 rounded text-sm text-black space-y-3">
          <div class="font-bold mb-2">สรุปผลการสัมภาษณ์ :</div>
          <div class="flex items-center space-x-2">
            <span class="text-base font-bold text-blue-600">{{ $evaluation->summary_result_label }}</span>
          </div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-black pt-4">
          <div class="flex flex-col items-center space-y-2">
            <div class="flex items-end w-full">
              <span class="mr-2 font-medium">ลงชื่อ</span>
              <span class="flex-grow min-w-0 border-b border-dashed border-gray-400 text-center font-bold text-indigo-900 py-0.5 relative flex items-center justify-center min-h-[28px]">
                @if($evaluation->hr_evaluator_name)
                  {{ $evaluation->hr_evaluator_name }}
                @elseif(!empty($canSignHr))
                  <form method="POST" action="{{ route('admin.interview-evaluations.sign', $evaluation->id) }}" class="inline-flex">
                    @csrf
                    <input type="hidden" name="role" value="hr">
                    <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">✍️ คลิกเพื่อลงชื่อ (HA/HR)</button>
                  </form>
                @else
                  <span class="text-xs text-gray-400 italic">รอฝ่ายบุคคลลงชื่อ</span>
                @endif
              </span>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ฝ่ายบุคคล</span>
            </div>
            <div class="flex items-center w-full">
              <span class="mr-2 font-medium">ตำแหน่ง</span>
              <span class="flex-grow min-w-0 border-b border-gray-400 text-center text-xs py-0.5">{{ $evaluation->hr_position ?? 'ฝ่ายทรัพยากรบุคคล' }}</span>
            </div>
            <div class="flex items-center w-full">
              <span class="mr-2 font-medium">วัน/เดือน/ปี</span>
              <span class="flex-grow min-w-0 border-b border-gray-400 text-center text-xs py-0.5">{{ $evaluation->hr_signed_date ? \Carbon\Carbon::parse($evaluation->hr_signed_date)->format('d/m/Y') : date('d/m/Y') }}</span>
            </div>
          </div>

          <div class="flex flex-col items-center space-y-2">
            <div class="flex items-end w-full">
              <span class="mr-2 font-medium">ลงชื่อ</span>
              <span class="flex-grow min-w-0 border-b border-dashed border-gray-400 text-center font-bold text-indigo-900 py-0.5 relative flex items-center justify-center min-h-[28px]">
                @if($evaluation->dept_evaluator_name)
                  {{ $evaluation->dept_evaluator_name }}
                @elseif(!empty($canSignDept))
                  <form method="POST" action="{{ route('admin.interview-evaluations.sign', $evaluation->id) }}" class="inline-flex">
                    @csrf
                    <input type="hidden" name="role" value="dept">
                    <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">✍️ คลิกเพื่อลงชื่อ (ต้นสังกัด)</button>
                  </form>
                @else
                  <span class="text-xs text-gray-400 italic">รอต้นสังกัดลงชื่อ</span>
                @endif
              </span>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ต้นสังกัด</span>
            </div>
            <div class="flex items-center w-full">
              <span class="mr-2 font-medium">ตำแหน่ง</span>
              <span class="flex-grow min-w-0 border-b border-gray-400 text-center text-xs py-0.5">{{ $evaluation->dept_position ?? '-' }}</span>
            </div>
            <div class="flex items-center w-full">
              <span class="mr-2 font-medium">วัน/เดือน/ปี</span>
              <span class="flex-grow min-w-0 border-b border-gray-400 text-center text-xs py-0.5">{{ $evaluation->dept_signed_date ? \Carbon\Carbon::parse($evaluation->dept_signed_date)->format('d/m/Y') : date('d/m/Y') }}</span>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-10 text-xs text-gray-500 text-black">
          <div>QF-HR-25 Rev.04 : 31-07-26</div>
        </div>
      </div>
    </div>
  </div>

  @include('components.form-share-modal')
</x-app-layout>
