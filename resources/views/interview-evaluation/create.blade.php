<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน') }}
    </h2>
  </x-slot>

  <div class="py-8 bg-gray-100 dark:bg-gray-900 min-h-screen relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Left: Form Categories Sidebar -->
        <x-hr-forms-sidebar />

        <!-- Right: Form Paper Container -->
        <div class="flex-1 w-full min-w-0">
          @php
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
          @endphp

          <datalist id="employee_names">
            @foreach($kumEmployees as $emp)
              <option value="{{ $emp->firstname }} {{ $emp->lastname }}"></option>
            @endforeach
          </datalist>

          <datalist id="candidate_names">
            @foreach($kumEmployees as $emp)
              <option value="{{ $emp->firstname }} {{ $emp->lastname }}"></option>
            @endforeach
          </datalist>

          <form action="{{ route('interview-evaluation.store') }}" method="POST">
            @csrf
            
            <!-- All Pages Wrapper -->
            <div class="w-full pb-4">
            <!-- PAGE Paper Container -->
            <div class="w-full mx-auto bg-white p-6 sm:p-10 lg:p-12 shadow-xl border border-gray-300 rounded-xl mb-8 print:shadow-none print:border-none print:p-0 print:mb-0">
        
        <!-- Header Section -->
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน</h1>
        </div>

        <div class="flex justify-end mb-4 text-sm text-black">
          <div class="flex items-center">
            <span class="mr-2 font-medium">วันที่</span>
            <input type="text" name="evaluation_date" class="datepicker-th w-36 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-gray-800" value="{{ old('evaluation_date', date('Y-m-d')) }}">
          </div>
        </div>

        <!-- Candidate Info Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6 text-sm text-black">
          <!-- Item 1: Candidate Name -->
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">(นาย/นาง/นางสาว)</span>
            <div class="flex items-center space-x-1.5 mr-2 mb-1 shrink-0">
              <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="candidate_prefix" value="นาย" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent" {{ old('candidate_prefix') == 'นาย' ? 'checked' : '' }}>
                <span class="ml-0.5 text-xs">นาย</span>
              </label>
              <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="candidate_prefix" value="นาง" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent" {{ old('candidate_prefix') == 'นาง' ? 'checked' : '' }}>
                <span class="ml-0.5 text-xs">นาง</span>
              </label>
              <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="candidate_prefix" value="นางสาว" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent" {{ old('candidate_prefix') == 'นางสาว' ? 'checked' : '' }}>
                <span class="ml-0.5 text-xs">นางสาว</span>
              </label>
            </div>
            <input type="text" name="candidate_name" id="candidate_name_input" list="candidate_names" required class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 placeholder-gray-400 @error('candidate_name') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('candidate_name') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('candidate_name') }}">
          </div>

          <!-- Item 2: Position Applied -->
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">ตำแหน่งที่สมัคร</span>
            <input type="text" name="position_applied" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0" value="{{ old('position_applied') }}">
          </div>

          <!-- Item 3: Department -->
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">แผนก</span>
            <input type="text" name="department" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0" value="{{ old('department') }}">
          </div>

          <!-- Item 4: Division & Interview Times -->
          <div class="flex items-end w-full gap-x-4">
            <div class="flex items-end flex-1 min-w-0">
              <span class="whitespace-nowrap mr-2 font-medium shrink-0">ฝ่าย</span>
              <input type="text" name="division" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0" value="{{ old('division') }}">
            </div>
            <div class="flex items-end shrink-0">
              <span class="whitespace-nowrap mr-2 font-medium shrink-0">สัมภาษณ์ครั้งที่</span>
              <input type="number" min="1" name="interview_times" class="w-16 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center" value="{{ old('interview_times', 1) }}">
            </div>
          </div>
        </div>

        <!-- Evaluation Matrix Table -->
        <div class="mb-6 border border-black overflow-x-auto">
          <table class="w-full text-sm border-collapse text-black">
            <thead>
              <tr class="border-b border-black divide-x divide-black bg-gray-50">
                <th class="py-3 px-2 w-12 text-center font-bold align-middle" rowspan="2">ลำดับ</th>
                <th class="py-3 px-4 text-center font-bold align-middle" rowspan="2">หัวข้อในการพิจารณา</th>
                <th class="py-1 px-2 font-bold text-center border-b border-black" colspan="4">❶ ฝ่ายบุคคล</th>
                <th class="py-1 px-2 font-bold text-center border-b border-black" colspan="4">❷ ต้นสังกัด</th>
              </tr>
              <tr class="border-b border-black divide-x divide-black bg-gray-50 text-xs">
                <th class="py-1 px-1 font-normal text-center w-12 border-l border-black">ควรปรับปรุง<br><span class="font-bold">1</span></th>
                <th class="py-1 px-1 font-normal text-center w-12">พอใช้<br><span class="font-bold">2</span></th>
                <th class="py-1 px-1 font-normal text-center w-12">ดี<br><span class="font-bold">3</span></th>
                <th class="py-1 px-1 font-normal text-center w-12">ดีมาก<br><span class="font-bold">4</span></th>
                
                <th class="py-1 px-1 font-normal text-center w-12 border-l border-black">ควรปรับปรุง<br><span class="font-bold">1</span></th>
                <th class="py-1 px-1 font-normal text-center w-12">พอใช้<br><span class="font-bold">2</span></th>
                <th class="py-1 px-1 font-normal text-center w-12">ดี<br><span class="font-bold">3</span></th>
                <th class="py-1 px-1 font-normal text-center w-12">ดีมาก<br><span class="font-bold">4</span></th>
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
                      <input type="radio" name="hr_score[{{ $index }}]" value="{{ $score }}" class="hr-rating form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent cursor-pointer" data-item="{{ $index }}" data-score="{{ $score }}">
                    </td>
                  @endfor

                  <!-- Dept Rating 1-4 -->
                  @for($score = 1; $score <= 4; $score++)
                    <td class="p-0 text-center align-middle">
                      <input type="radio" name="dept_score[{{ $index }}]" value="{{ $score }}" class="dept-rating form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent cursor-pointer" data-item="{{ $index }}" data-score="{{ $score }}">
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
            <textarea name="remarks" rows="2" class="flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-1 border-t-0 border-l-0 border-r-0 focus:ring-0 resize-none leading-relaxed" placeholder="ข้อความหมายเหตุเพิ่มเติม..."></textarea>
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
              <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ฝ่ายบุคคล</span>
            </div>
            
            <div class="flex items-center w-full relative group">
              <span class="font-medium">(</span>
              <input type="text" name="hr_evaluator_name" id="hr_evaluator_name" list="employee_names" class="text-center flex-grow bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 text-sm font-medium" placeholder="พิมพ์ชื่อเพื่อค้นหา..." value="{{ old('hr_evaluator_name') }}">
              <button type="button" onclick="document.getElementById('hr_evaluator_name').value = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; if(document.getElementsByName('hr_position')[0]) document.getElementsByName('hr_position')[0].value = 'ฝ่ายทรัพยากรบุคคล';" class="absolute right-4 px-2 py-0.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition cursor-pointer" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
              <span class="font-medium">)</span>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">ตำแหน่ง</span>
              <input type="text" name="hr_position" class="flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('hr_position') }}">
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">วัน/เดือน/ปี</span>
              <input type="text" name="hr_signed_date" class="datepicker-th flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('hr_signed_date', date('Y-m-d')) }}">
            </div>
          </div>

          <!-- Department Signatory -->
          <div class="flex flex-col items-center space-y-3">
            <div class="flex items-end w-full">
              <span class="mr-2 font-medium">ลงชื่อ</span>
              <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
              <span class="ml-2 text-xs font-bold text-gray-600 w-20">ต้นสังกัด</span>
            </div>
            
            <div class="flex items-center w-full relative group">
              <span class="font-medium">(</span>
              <input type="text" name="dept_evaluator_name" list="employee_names" class="text-center flex-grow bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 text-sm font-medium" placeholder="พิมพ์ชื่อเพื่อค้นหา..." value="{{ old('dept_evaluator_name') }}">
              <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelector('input[name=\'dept_evaluator_name\']').value = n;" class="absolute right-4 px-2 py-0.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
              <span class="font-medium">)</span>
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">ตำแหน่ง</span>
              <input type="text" name="dept_position" class="flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('dept_position') }}">
            </div>

            <div class="flex items-center w-full">
              <span class="mr-2 font-medium whitespace-nowrap">วัน/เดือน/ปี</span>
              <input type="text" name="dept_signed_date" class="datepicker-th flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-center text-xs" value="{{ old('dept_signed_date', date('Y-m-d')) }}">
            </div>
          </div>
        </div>

        <!-- Footer Document Code -->
        <div class="flex justify-end mt-10 text-xs text-gray-500 text-black">
          <div>QF-HR-25 Rev.04 : 31-07-26</div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3 print:hidden">
          <a href="{{ route('admin.interview-evaluations.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
            ยกเลิก
          </a>
          <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
            บันทึกข้อมูล
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
  <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'ข้อผิดพลาด!',
          text: '{{ session('error') }}',
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true,
          background: '#ffffff',
          color: '#1f2937'
        });
      @endif

      flatpickr(".datepicker-th", {
        locale: "th",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        allowInput: true
      });

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

        // Auto select summary result based on score criteria if not manually overridden
        if (avgScore >= 30) {
          document.getElementById('result_hire').checked = true;
        } else if (avgScore >= 20) {
          document.getElementById('result_reserve').checked = true;
        } else if (avgScore > 0) {
          document.getElementById('result_reject').checked = true;
        }
      }

      document.querySelectorAll('.hr-rating, .dept-rating').forEach(el => {
        el.addEventListener('change', calculateScores);
      });

      calculateScores();
    });
  </script>
</x-app-layout>
