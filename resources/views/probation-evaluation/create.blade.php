<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('สร้างแบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน') }}
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
          
          <form action="{{ route('probation-evaluation.store') ?? '#' }}" method="POST">
            @csrf
            
            <!-- All Pages Wrapper -->
            <div class="hr-form-container">
            <!-- PAGE 1/3 Paper Container -->
            <div class="hr-form-paper">
        
        <!-- Header Section -->
        <div class="flex justify-between items-center gap-2 mb-6 text-sm">
          <div class="min-w-0 flex-1">
            <span class="whitespace-nowrap">แบบประเมินเลขที่</span>
            <input type="text" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-32 sm:w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
          </div>
          <div class="text-right text-gray-600 font-medium whitespace-nowrap shrink-0">
            <p>1 / 3</p>
          </div>
        </div>

        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน</h1>
        </div>

        <!-- Employee Info Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-8 text-sm text-black">
          <div class="flex flex-wrap sm:flex-nowrap items-end w-full gap-y-1">
            <div class="flex items-center shrink-0 mr-2 mb-1">
              <span class="whitespace-nowrap mr-2 font-medium">ชื่อ-นามสกุล <span class="text-red-500">*</span></span>
              <div class="flex items-center space-x-1.5">
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="prefix" value="นาย" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('prefix') ring-2 ring-red-500 border-red-500 @enderror">
                  <span class="ml-0.5 text-xs">นาย</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="prefix" value="นาง" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('prefix') ring-2 ring-red-500 border-red-500 @enderror">
                  <span class="ml-0.5 text-xs">นาง</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="prefix" value="นางสาว" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('prefix') ring-2 ring-red-500 border-red-500 @enderror">
                  <span class="ml-0.5 text-xs">นางสาว</span>
                </label>
              </div>
            </div>
            <input type="text" name="employee_name" list="employee_names" class="flex-1 min-w-[120px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 placeholder-gray-400 @error('employee_name') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('employee_name') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('employee_name') }}">
          </div>
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">ตำแหน่ง <span class="text-red-500">*</span></span>
            <input type="text" name="position" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 @error('position') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('position') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('position') }}">
          </div>
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">รหัสพนักงาน <span class="text-red-500">*</span></span>
            <input type="text" name="emp_code" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 @error('emp_code') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('emp_code') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('emp_code') }}">
          </div>
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">แผนก/ ฝ่าย <span class="text-red-500">*</span></span>
            <input type="text" name="department" class="flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 @error('department') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('department') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('department') }}">
          </div>
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">วันที่เริ่มงาน <span class="text-red-500">*</span></span>
            <input type="text" name="start_date" class="datepicker-th flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700 @error('start_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('start_date') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('start_date') }}">
          </div>
          <div class="flex items-end w-full">
            <span class="whitespace-nowrap mr-2 font-medium shrink-0">วันที่ครบทดลองงาน <span class="text-red-500">*</span></span>
            <input type="text" name="probation_due_date" class="datepicker-th flex-1 min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700 @error('probation_due_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('probation_due_date') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('probation_due_date') }}">
          </div>
        </div>

        <!-- 1.1 Absence Record -->
        <div class="mb-8">
          <h3 class="font-bold text-sm mb-2 text-black">1.1 สถิติการหยุดงาน</h3>
          <div class="border border-black overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-center border-collapse">
              <thead>
                <tr class="border-b border-black divide-x divide-black bg-gray-50">
                  <th class="py-2 px-2 w-1/3 font-normal text-black align-middle" rowspan="2">รอบการประเมิน</th>
                  <th class="py-2 px-2 font-normal text-black align-middle" rowspan="2">ลากิจ</th>
                  <th class="py-2 px-2 font-normal text-black align-middle" rowspan="2">ลาป่วย</th>
                  <th class="py-2 px-2 font-normal text-black align-middle" rowspan="2">ขาดงาน</th>
                  <th class="py-1 px-0 font-normal text-black text-center" colspan="2">สาย</th>
                </tr>
                <tr class="border-b border-black divide-x divide-black bg-gray-50">
                  <th class="py-1 px-2 font-normal text-black text-center border-l border-black">ครั้ง</th>
                  <th class="py-1 px-2 font-normal text-black text-center">นาที</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black">
                @for($i = 1; $i <= 3; $i++)
                <tr class="divide-x divide-black text-black">
                  <td class="py-2 px-2 text-left flex items-center whitespace-nowrap flex-nowrap">
                    <span class="mr-2">ครั้งที่ {{ $i }} จาก</span>
                    <input type="text" name="absence[{{ $i }}][start]" class="datepicker-th w-24 sm:w-28 border-b border-gray-400 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                    <span class="mx-2">ถึง</span>
                    <input type="text" name="absence[{{ $i }}][end]" class="datepicker-th w-24 sm:w-28 border-b border-gray-400 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                  </td>
                  <td class="p-0"><input type="number" name="absence[{{ $i }}][business_leave]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                  <td class="p-0"><input type="number" name="absence[{{ $i }}][sick_leave]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                  <td class="p-0"><input type="number" name="absence[{{ $i }}][absent]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                  <td class="p-0"><input type="number" name="absence[{{ $i }}][late_count]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                  <td class="p-0"><input type="number" name="absence[{{ $i }}][late_mins]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                </tr>
                @endfor
              </tbody>
            </table>
          </div>
        </div>

        <!-- 1.2 Knowledge Exam -->
        <div class="mb-8">
          <h3 class="font-bold text-sm mb-2 text-black">1.2 การสอบวัดความรู้</h3>
          <div class="border border-black overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm border-collapse text-black">
              <thead>
                <tr class="border-b border-black divide-x divide-black bg-gray-50">
                  <th class="py-4 px-4 w-1/2 font-normal text-center align-middle" rowspan="2">หัวข้อการทดสอบ</th>
                  <th class="py-1 px-0 font-normal text-center border-b border-black" colspan="3">สอบผ่าน<br>ในครั้งที่</th>
                  <th class="py-4 px-2 font-normal text-center align-middle" rowspan="2">วันที่สอบผ่าน</th>
                  <th class="py-4 px-2 font-normal text-center align-middle" rowspan="2">ผู้ทดสอบ<br>(ฝ่ายทรัพยากรบุคคล)</th>
                </tr>
                <tr class="border-b border-black divide-x divide-black bg-gray-50">
                  <th class="py-1 px-2 font-normal text-center w-8 border-l border-black">1</th>
                  <th class="py-1 px-2 font-normal text-center w-8">2</th>
                  <th class="py-1 px-2 font-normal text-center w-8">3</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black">
                @php
                $topics = [
                  '1. การใช้งานคอมพิวเตอร์และ IT เบื้องต้น',
                  '2. กฎระเบียบของบริษัท',
                  '3. ทิศทางการบริหาร นโยบาย วิสัยทัศน์ พันธกิจ ของบริษัท',
                  '4. ผลิตภัณฑ์ของบริษัท',
                  '5. การทำ Creating Shared Value (CSV) ของบริษัท',
                  '6. การทำ 5 ส',
                  '7. อื่นๆ ................................................................'
                ];
                @endphp
                @foreach($topics as $topic)
                <tr class="divide-x divide-black">
                  <td class="py-2 px-3 text-left">
                    @if($loop->last)
                      7. อื่นๆ <input type="text" name="exam_other_topic" class="border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 inline w-48 @error('exam_other_topic') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('exam_other_topic') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('exam_other_topic') }}">
                    @else
                      {{ $topic }}
                    @endif
                  </td>
                  <td class="p-0 text-center"><input type="radio" name="exam_passed_round[{{$loop->index}}]" value="1" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error("exam_passed_round.{$loop->index}") ring-2 ring-red-500 border-red-500 @enderror"></td>
                  <td class="p-0 text-center"><input type="radio" name="exam_passed_round[{{$loop->index}}]" value="2" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error("exam_passed_round.{$loop->index}") ring-2 ring-red-500 border-red-500 @enderror"></td>
                  <td class="p-0 text-center"><input type="radio" name="exam_passed_round[{{$loop->index}}]" value="3" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error("exam_passed_round.{$loop->index}") ring-2 ring-red-500 border-red-500 @enderror"></td>
                  <td class="p-0"><input type="text" name="exam_date[{{$loop->index}}]" class="datepicker-th w-full h-full text-center border-none bg-transparent focus:ring-0 py-2 @error("exam_date.{$loop->index}") border-red-500 border-b-2 placeholder-red-400 @enderror" @error("exam_date.{$loop->index}") placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old("exam_date.{$loop->index}") }}"></td>
                  <td class="p-0"><input type="text" name="exam_tester[{{$loop->index}}]" list="employee_names" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2 placeholder-gray-400 @error("exam_tester.{$loop->index}") border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error("exam_tester.{$loop->index}") placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old("exam_tester.{$loop->index}") }}"></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- 1.3 Performance Evaluation (Round 1) -->
        <div class="mb-10 text-black">
          <h3 class="font-bold text-sm mb-2">1.3 การปฏิบัติงาน</h3>
          
          <div class="border border-black">
            <!-- Top Grid: Tasks and Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-black border-b border-black min-h-[160px]">
              <div class="p-3">
                <p class="font-bold text-sm mb-2">ครั้งที่ 1 : งานที่มอบหมาย (30 วัน)</p>
                <textarea name="tasks_assigned" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('tasks_assigned') bg-red-50 placeholder-red-400 @enderror" @error('tasks_assigned') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('tasks_assigned') }}</textarea>
              </div>
              <div class="p-3">
                <p class="font-bold text-sm mb-2">ครั้งที่ 1 : ผลการปฏิบัติงาน</p>
                <textarea name="performance_result" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('performance_result') bg-red-50 placeholder-red-400 @enderror" @error('performance_result') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('performance_result') }}</textarea>
              </div>
            </div>

            <!-- Level -->
            <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
              <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level" value="ต้องปรับปรุง" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level') == 'ต้องปรับปรุง' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level" value="สำเร็จตามที่คาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level') == 'สำเร็จตามที่คาดหมาย' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level" value="เกินความคาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level') == 'เกินความคาดหมาย' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
              </label>
            </div>

            <!-- Evaluation Result -->
            <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
              <span class="font-bold text-sm">ผลการประเมินครั้งที่ 1</span>
              <label class="inline-flex items-center">
                <input type="radio" name="evaluation_result" value="ทดลองงานต่อ" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result') == 'ทดลองงานต่อ' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="evaluation_result" value="ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result') == 'ผ่านการทดลองงาน' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
              </label>
              <label class="inline-flex items-center w-full md:w-auto">
                <input type="radio" name="evaluation_result" value="ไม่ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result') == 'ไม่ผ่านการทดลองงาน' ? 'checked' : '' }}>
                <span class="ml-2 text-sm flex items-center flex-wrap">
                  ไม่ผ่านการทดลองงาน เนื่องจาก
                  <input type="text" name="evaluation_result_reason" class="ml-2 flex-grow min-w-0 min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 @error('evaluation_result_reason') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('evaluation_result_reason') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluation_result_reason') }}">
                </span>
              </label>
            </div>

            <!-- Comments -->
            <div class="p-3 border-b border-black">
              <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
              <textarea name="evaluator_comment" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('evaluator_comment') bg-red-50 placeholder-red-400 @enderror" @error('evaluator_comment') placeholder="กรุณาระบุข้อมูล" @enderror></textarea>
            </div>
            <div class="p-3 border-b border-black">
              <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
              <textarea name="hr_comment" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('hr_comment') bg-red-50 placeholder-red-400 @enderror" @error('hr_comment') placeholder="กรุณาระบุข้อมูล" @enderror></textarea>
            </div>

            <!-- Signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 p-6 gap-x-12 gap-y-10">
              <!-- User -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้รับการประเมิน</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="evaluatee_name" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('evaluatee_name') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('evaluatee_name') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluatee_name') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'evaluatee_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>
              
              <!-- Evaluator -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้ประเมิน</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="evaluator_name" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('evaluator_name') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('evaluator_name') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluator_name') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'evaluator_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>

              <!-- Manager -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้จัดการฝ่าย/แผนก</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="manager_name" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('manager_name') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('manager_name') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('manager_name') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'manager_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>

              <!-- HR -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                  <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center">
                  <span>(</span>
                  <input type="text" name="hr_name" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 @error('hr_name') border-red-500 border-b-2 placeholder-red-400 @enderror" readonly @error('hr_name') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('hr_name') }}">
                  <span>)</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- End of Page 1 -->
        <div class="flex justify-between items-end gap-2 mt-12 text-xs text-gray-500 text-black leading-tight">
          <div>
            <div>บริษัท คัมเวล คอร์ปอเรชั่น</div>
            <div>จำกัด (มหาชน)</div>
          </div>
          <div class="text-right whitespace-nowrap">
            <div>QF-HR-18 Rev.09 :</div>
            <div>02-05-25</div>
          </div>
        </div>
      </div>

      <!-- ==================== PAGE 2/3 ==================== -->
      <div class="hr-form-paper break-before-page print:break-before-page" style="page-break-before: always;">
        <!-- Header Section -->
        <div class="flex justify-between items-center gap-2 mb-6 text-sm mt-8 print:mt-0">
          <div class="min-w-0 flex-1">
            <span class="whitespace-nowrap">แบบประเมินเลขที่</span>
            <input type="text" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-32 sm:w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
          </div>
          <div class="text-right text-gray-600 font-medium whitespace-nowrap shrink-0">
            <p>2 / 3</p>
          </div>
        </div>

        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน</h1>
        </div>
        <!-- 1.4 Performance Evaluation (Round 2) -->
        <h3 class="font-bold text-sm mb-2 text-black">1.3 การปฏิบัติงาน</h3>
          
        <div class="mb-10 text-black">
          <div class="border border-black">
            <!-- Top Grid: Tasks and Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-black border-b border-black min-h-[160px]">
              <div class="p-3">
                <p class="font-bold text-sm mb-2">ครั้งที่ 2 : งานที่มอบหมาย (60 วัน)</p>
                <textarea name="tasks_assigned_2" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('tasks_assigned_2') bg-red-50 placeholder-red-400 @enderror" @error('tasks_assigned_2') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('tasks_assigned_2') }}</textarea>
              </div>
              <div class="p-3">
                <p class="font-bold text-sm mb-2">ครั้งที่ 2 : ผลการปฏิบัติงาน</p>
                <textarea name="performance_result_2" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('performance_result_2') bg-red-50 placeholder-red-400 @enderror" @error('performance_result_2') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('performance_result_2') }}</textarea>
              </div>
            </div>

            <!-- Level -->
            <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
              <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level_2" value="ต้องปรับปรุง" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level_2') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level_2') == 'ต้องปรับปรุง' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level_2" value="สำเร็จตามที่คาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level_2') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level_2') == 'สำเร็จตามที่คาดหมาย' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level_2" value="เกินความคาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level_2') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level_2') == 'เกินความคาดหมาย' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
              </label>
            </div>

            <!-- Evaluation Result -->
            <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
              <span class="font-bold text-sm">ผลการประเมินครั้งที่ 2</span>
              <label class="inline-flex items-center">
                <input type="radio" name="evaluation_result_2" value="ทดลองงานต่อ" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result_2') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result_2') == 'ทดลองงานต่อ' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="evaluation_result_2" value="ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result_2') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result_2') == 'ผ่านการทดลองงาน' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
              </label>
              <label class="inline-flex items-center w-full md:w-auto">
                <input type="radio" name="evaluation_result_2" value="ไม่ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result_2') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result_2') == 'ไม่ผ่านการทดลองงาน' ? 'checked' : '' }}>
                <span class="ml-2 text-sm flex items-center flex-wrap">
                  ไม่ผ่านการทดลองงาน เนื่องจาก
                  <input type="text" name="evaluation_result_reason_2" class="ml-2 flex-grow min-w-0 min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 @error('evaluation_result_reason_2') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('evaluation_result_reason_2') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluation_result_reason_2') }}">
                </span>
              </label>
            </div>

            <!-- Comments -->
            <div class="p-3 border-b border-black">
              <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
              <textarea name="evaluator_comment_2" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('evaluator_comment_2') bg-red-50 placeholder-red-400 @enderror" @error('evaluator_comment_2') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('evaluator_comment_2') }}</textarea>
            </div>
            <div class="p-3 border-b border-black">
              <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
              <textarea name="hr_comment_2" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('hr_comment_2') bg-red-50 placeholder-red-400 @enderror" @error('hr_comment_2') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('hr_comment_2') }}</textarea>
            </div>

            <!-- Signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 p-6 gap-x-12 gap-y-10">
              <!-- User -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้รับการประเมิน</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="evaluatee_name_2" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('evaluatee_name_2') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('evaluatee_name_2') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluatee_name_2') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'evaluatee_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>
              
              <!-- Evaluator -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้ประเมิน</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="evaluator_name_2" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('evaluator_name_2') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('evaluator_name_2') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluator_name_2') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'evaluator_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>

              <!-- Manager -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้จัดการฝ่าย/แผนก</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="manager_name_2" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('manager_name_2') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('manager_name_2') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('manager_name_2') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'manager_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>

              <!-- HR -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                  <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center">
                  <span>(</span>
                  <input type="text" name="hr_name_2" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 @error('hr_name_2') border-red-500 border-b-2 placeholder-red-400 @enderror" readonly @error('hr_name_2') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('hr_name_2') }}">
                  <span>)</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-between items-end gap-2 mt-12 text-xs text-gray-500 text-black leading-tight">
          <div>
            <div>บริษัท คัมเวล คอร์ปอเรชั่น</div>
            <div>จำกัด (มหาชน)</div>
          </div>
          <div class="text-right whitespace-nowrap">
            <div>QF-HR-18 Rev.09 :</div>
            <div>02-05-25</div>
          </div>
        </div>
      </div>

      <!-- ==================== PAGE 3/3 ==================== -->
      <div class="hr-form-paper break-before-page print:break-before-page" style="page-break-before: always;">
        <!-- Header Section -->
        <div class="flex justify-between items-center gap-2 mb-6 text-sm mt-8 print:mt-0">
          <div class="min-w-0 flex-1">
            <span class="whitespace-nowrap">แบบประเมินเลขที่</span>
            <input type="text" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-32 sm:w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
          </div>
          <div class="text-right text-gray-600 font-medium whitespace-nowrap shrink-0">
            <p>3 / 3</p>
          </div>
        </div>

        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน</h1>
        </div>
        
        <!-- 1.5 Performance Evaluation (Round 3) -->
        <h3 class="font-bold text-sm mb-2 text-black">1.3 การปฏิบัติงาน</h3>
          
        <div class="mb-10 text-black">
          <div class="border border-black">
            <!-- Top Grid: Tasks and Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-black border-b border-black min-h-[160px]">
              <div class="p-3">
                <p class="font-bold text-sm mb-2">ครั้งที่ 3 : งานที่มอบหมาย (90 วัน)</p>
                <textarea name="tasks_assigned_3" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('tasks_assigned_3') bg-red-50 placeholder-red-400 @enderror" @error('tasks_assigned_3') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('tasks_assigned_3') }}</textarea>
              </div>
              <div class="p-3">
                <p class="font-bold text-sm mb-2">ครั้งที่ 3 : ผลการปฏิบัติงาน</p>
                <textarea name="performance_result_3" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('performance_result_3') bg-red-50 placeholder-red-400 @enderror" @error('performance_result_3') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('performance_result_3') }}</textarea>
              </div>
            </div>

            <!-- Level -->
            <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
              <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level_3" value="ต้องปรับปรุง" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level_3') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level_3') == 'ต้องปรับปรุง' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level_3" value="สำเร็จตามที่คาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level_3') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level_3') == 'สำเร็จตามที่คาดหมาย' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="performance_level_3" value="เกินความคาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('performance_level_3') ring-2 ring-red-500 border-red-500 @enderror" {{ old('performance_level_3') == 'เกินความคาดหมาย' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
              </label>
            </div>

            <!-- Evaluation Result -->
            <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
              <span class="font-bold text-sm">ผลการประเมินครั้งที่ 3</span>
              <label class="inline-flex items-center">
                <input type="radio" name="evaluation_result_3" value="ทดลองงานต่อ" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result_3') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result_3') == 'ทดลองงานต่อ' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="evaluation_result_3" value="ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result_3') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result_3') == 'ผ่านการทดลองงาน' ? 'checked' : '' }}>
                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
              </label>
              <label class="inline-flex items-center w-full md:w-auto">
                <input type="radio" name="evaluation_result_3" value="ไม่ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent @error('evaluation_result_3') ring-2 ring-red-500 border-red-500 @enderror" {{ old('evaluation_result_3') == 'ไม่ผ่านการทดลองงาน' ? 'checked' : '' }}>
                <span class="ml-2 text-sm flex items-center flex-wrap">
                  ไม่ผ่านการทดลองงาน เนื่องจาก
                  <input type="text" name="evaluation_result_reason_3" class="ml-2 flex-grow min-w-0 min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 @error('evaluation_result_reason_3') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('evaluation_result_reason_3') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluation_result_reason_3') }}">
                </span>
              </label>
            </div>

            <!-- Comments -->
            <div class="p-3 border-b border-black">
              <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
              <textarea name="evaluator_comment_3" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('evaluator_comment_3') bg-red-50 placeholder-red-400 @enderror" @error('evaluator_comment_3') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('evaluator_comment_3') }}</textarea>
            </div>
            <div class="p-3 border-b border-black">
              <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
              <textarea name="hr_comment_3" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em] @error('hr_comment_3') bg-red-50 placeholder-red-400 @enderror" @error('hr_comment_3') placeholder="กรุณาระบุข้อมูล" @enderror>{{ old('hr_comment_3') }}</textarea>
            </div>

            <!-- Signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 p-6 gap-x-12 gap-y-10">
              <!-- User -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้รับการประเมิน</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="evaluatee_name_3" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('evaluatee_name_3') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('evaluatee_name_3') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluatee_name_3') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'evaluatee_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>
              
              <!-- Evaluator -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้ประเมิน</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="evaluator_name_3" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('evaluator_name_3') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('evaluator_name_3') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('evaluator_name_3') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'evaluator_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>

              <!-- Manager -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                  <span class="ml-2 text-xs text-gray-500 w-24">ผู้จัดการฝ่าย/แผนก</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center relative group">
                  <span>(</span>
                  <input type="text" name="manager_name_3" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400 @error('manager_name_3') border-red-500 border-b-2 placeholder-red-400 @enderror" placeholder="พิมพ์ชื่อเพื่อค้นหา..." @error('manager_name_3') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('manager_name_3') }}">
                  <button type="button" onclick="const n = '{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}'; document.querySelectorAll('input[name^=\'manager_name\']').forEach(el => el.value = n);" class="absolute right-8 px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-[10px] shadow-sm transition" title="คลิกเพื่อลงชื่อของคุณ">✍️ ลงชื่อ</button>
                  <span>)</span>
                </div>
              </div>

              <!-- HR -->
              <div class="flex flex-col items-center">
                <div class="flex items-end w-full mb-2">
                  <span class="mr-2">ลงชื่อ</span>
                  <input type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                  <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                </div>
                <div class="flex w-full px-6 justify-center items-center">
                  <span>(</span>
                  <input type="text" name="hr_name_3" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 @error('hr_name_3') border-red-500 border-b-2 placeholder-red-400 @enderror" readonly @error('hr_name_3') placeholder="กรุณาระบุข้อมูล" @enderror value="{{ old('hr_name_3') }}">
                  <span>)</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Note -->
        <div class="text-xs text-gray-700 font-bold mb-10 text-black">
          * หมายเหตุ : เงื่อนไขการผ่านทดลองงาน ต้องผ่านการสอบวัดความรู้จากฝ่ายทรัพยากรบุคคล ในข้อ 1.2 และผ่านการประเมินผลการปฏิบัติงาน<br>จากหัวหน้างาน/ผู้ประเมิน ในข้อ 1.3
        </div>

        <div class="flex justify-between items-end gap-2 mt-12 text-xs text-gray-500 text-black leading-tight">
          <div>
            <div>บริษัท คัมเวล คอร์ปอเรชั่น</div>
            <div>จำกัด (มหาชน)</div>
          </div>
          <div class="text-right whitespace-nowrap">
            <div>QF-HR-18 Rev.09 :</div>
            <div>02-05-25</div>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3 print:hidden">
          <a href="{{ url()->previous() }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
            ยกเลิก
          </a>
          <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
            บันทึกข้อมูล
          </button>
        </div>
      </div>
      <!-- End of All Pages Wrapper -->
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
      
    });
  </script>
<script>
document.addEventListener('input', function(e) {
    // Exclude radio and checkbox inputs from auto-sync so selecting choices in one section doesn't affect other sections
    if (e.target.type === 'radio' || e.target.type === 'checkbox') {
        return;
    }
    if(e.target.name && !e.target.name.startsWith('_')) {
        // Skip auto-syncing row-specific inputs like absence table or eval items
        if (e.target.name.includes('absence') || e.target.name.includes('eval_') || e.target.name.includes('exam_')) {
            return;
        }
        document.querySelectorAll('[name="' + e.target.name + '"]').forEach(el => {
            if(el !== e.target) {
                el.value = e.target.value;
            }
        });
    }
});
</script>
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
    });
  </script>
</x-app-layout>
