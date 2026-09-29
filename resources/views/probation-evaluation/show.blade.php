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
                    {{ __('รายละเอียดแบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน') }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('probation-evaluation.pdf', $probationEvaluation->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-black focus:bg-black active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    พิมพ์ (Print)
                </a>
                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->isHrOrAdmin()))
                    <button type="button" onclick="openShareModal('probation_evaluation', {{ $probationEvaluation->id }}, 'แบบประเมินทดลองงาน #{{ $probationEvaluation->id }}')" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-900 transition ease-in-out duration-150 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                        </svg>
                        แชร์เอกสาร
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <style>
        body, button, input, select, textarea, .font-sans, h1, h2, h3, h4, h5, h6, label, span, div {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
    </style>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen relative overflow-x-hidden">
        


        <!-- All Pages Wrapper -->
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
        <!-- PAGE 1/3 Paper Container -->
        <div class="hr-form-paper">
            @php
                $kumEmployees = \Illuminate\Support\Facades\DB::connection('appkum_user')
                    ->table('employees')
                    ->where('status', 'active')
                    ->orderBy('firstname')
                    ->get();
            @endphp

            <datalist id="employee_names">
                @foreach($kumEmployees as $emp)
                    <option value="{{ $emp->firstname }} {{ $emp->lastname }}" {{ old('prefix', $probationEvaluation->prefix ?? '') == ($emp->firstname . ' ' . $emp->lastname) ? 'selected' : '' }}></option>
                @endforeach
            </datalist>
            
            
                
                @php
                    $absences = $probationEvaluation->absenceRecords->keyBy('round') ?? collect();
                    $exams = $probationEvaluation->examResults ?? collect();
                @endphp
                
                <!-- Header Section -->
                <div class="flex justify-between items-center gap-2 mb-6 text-sm">
                    <div class="min-w-0 flex-1">
                        <span class="whitespace-nowrap">แบบประเมินเลขที่</span>
                        <input readonly disabled type="text" name="form_no" value="{{ 'PE-' . date('Y', strtotime($probationEvaluation->created_at)) . '-' . str_pad($probationEvaluation->id, 4, '0', STR_PAD_LEFT) }}" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-32 sm:w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
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
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">ชื่อ-นามสกุล</span>
                        <div class="flex items-center space-x-2 mr-2 mb-1">
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="prefix" value="นาย" {{ old('prefix', $probationEvaluation->prefix ?? '') == 'นาย' ? 'checked' : '' }} class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-1 text-xs">นาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="prefix" value="นาง" {{ old('prefix', $probationEvaluation->prefix ?? '') == 'นาง' ? 'checked' : '' }} class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-1 text-xs">นาง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="prefix" value="นางสาว" {{ old('prefix', $probationEvaluation->prefix ?? '') == 'นางสาว' ? 'checked' : '' }} class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-1 text-xs">นางสาว</span>
                            </label>
                        </div>
                        <input readonly disabled type="text" name="employee_name" value="{{ old('employee_name', $probationEvaluation->employee_name ?? '') }}" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">ตำแหน่ง</span>
                        <input readonly disabled type="text" name="position" value="{{ old('position', $probationEvaluation->position ?? '') }}" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">รหัสพนักงาน</span>
                        <input readonly disabled type="text" name="emp_code" value="{{ old('emp_code', $probationEvaluation->emp_code ?? '') }}" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">แผนก/ ฝ่าย</span>
                        <input readonly disabled type="text" name="department" value="{{ old('department', $probationEvaluation->department ?? '') }}" class="flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">วันที่เริ่มงาน</span>
                        <input readonly disabled type="text" name="start_date" value="{{ old('start_date', $probationEvaluation->start_date ?? '') }}" class="datepicker-th flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">วันที่ครบทดลองงาน</span>
                        <input readonly disabled type="text" name="probation_due_date" value="{{ old('probation_due_date', $probationEvaluation->probation_due_date ?? '') }}" class="datepicker-th flex-grow min-w-0 border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
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
                                        <input readonly disabled type="text" name="absence[{{$i}}][start]" value="{{ old('absence.'.$i.'.start', $absences[$i]->start_date ?? '') }}" class="datepicker-th w-24 sm:w-28 border-b border-gray-400 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                                        <span class="mx-2">ถึง</span>
                                        <input readonly disabled type="text" name="absence[{{$i}}][end]" value="{{ old('absence.'.$i.'.end', $absences[$i]->end_date ?? '') }}" class="datepicker-th w-24 sm:w-28 border-b border-gray-400 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                                    </td>
                                    <td class="p-0"><input readonly disabled type="text" name="absence[{{$i}}][business_leave]" value="{{ old('absence.'.$i.'.business_leave', $absences[$i]->business_leave ?? '') }}" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input readonly disabled type="text" name="absence[{{$i}}][sick_leave]" value="{{ old('absence.'.$i.'.sick_leave', $absences[$i]->sick_leave ?? '') }}" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input readonly disabled type="text" name="absence[{{$i}}][absent]" value="{{ old('absence.'.$i.'.absent', $absences[$i]->absent ?? '') }}" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input readonly disabled type="text" name="absence[{{$i}}][late_count]" value="{{ old('absence.'.$i.'.late_count', $absences[$i]->late_count ?? '') }}" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input readonly disabled type="text" name="absence[{{$i}}][late_mins]" value="{{ old('absence.'.$i.'.late_mins', $absences[$i]->late_mins ?? '') }}" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
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
                                    <td class="py-2 px-3 text-left">{!! str_replace('................................................................', '<input readonly disabled type="text" name="exam_other_topic" value="'.e(old('exam_other_topic', $probationEvaluation->exam_other_topic ?? '')).'" class="border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 inline w-48">', $topic) !!}</td>
                                    <td class="p-0 text-center"><input readonly disabled type="radio" name="exam_passed_round[{{$loop->index}}]" value="1" {{ old('exam_passed_round.'.$loop->index, $exams[$loop->index]->passed_round ?? '') == '1' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent"></td>
                                    <td class="p-0 text-center"><input readonly disabled type="radio" name="exam_passed_round[{{$loop->index}}]" value="2" {{ old('exam_passed_round.'.$loop->index, $exams[$loop->index]->passed_round ?? '') == '2' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent"></td>
                                    <td class="p-0 text-center"><input readonly disabled type="radio" name="exam_passed_round[{{$loop->index}}]" value="3" {{ old('exam_passed_round.'.$loop->index, $exams[$loop->index]->passed_round ?? '') == '3' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent"></td>
                                    <td class="p-0"><input readonly disabled type="text" name="exam_date[{{$loop->index}}]" value="{{ old('exam_date.'.$loop->index, $exams[$loop->index]->exam_date ?? '') }}" class="datepicker-th w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input readonly disabled type="text" name="exam_tester[{{$loop->index}}]" value="{{ old('exam_tester.'.$loop->index, $exams[$loop->index]->exam_tester ?? '') }}" list="employee_names" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา..."></td>
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
                                <textarea readonly disabled name="tasks_assigned" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder="">{{ old('tasks_assigned', $probationEvaluation->tasks_assigned ?? '') }}</textarea>
                            </div>
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 1 : ผลการปฏิบัติงาน</p>
                                <textarea readonly disabled name="performance_result" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder="">{{ old('performance_result', $probationEvaluation->performance_result ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
                            <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="performance_level" value="ต้องปรับปรุง" {{ old('performance_level', $probationEvaluation->performance_level ?? '') == 'ต้องปรับปรุง' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="performance_level" value="สำเร็จตามที่คาดหมาย" {{ old('performance_level', $probationEvaluation->performance_level ?? '') == 'สำเร็จตามที่คาดหมาย' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="performance_level" value="เกินความคาดหมาย" {{ old('performance_level', $probationEvaluation->performance_level ?? '') == 'เกินความคาดหมาย' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
                            </label>
                        </div>

                        <!-- Evaluation Result -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
                            <span class="font-bold text-sm">ผลการประเมินครั้งที่ 1</span>
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="evaluation_result" value="ทดลองงานต่อ" {{ old('evaluation_result', $probationEvaluation->evaluation_result ?? '') == 'ทดลองงานต่อ' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input readonly disabled type="radio" name="evaluation_result" value="ผ่านการทดลองงาน" {{ old('evaluation_result', $probationEvaluation->evaluation_result ?? '') == 'ผ่านการทดลองงาน' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
                            </label>
                            <label class="inline-flex items-center w-full md:w-auto">
                                <input readonly disabled type="radio" name="evaluation_result" value="ไม่ผ่านการทดลองงาน" {{ old('evaluation_result', $probationEvaluation->evaluation_result ?? '') == 'ไม่ผ่านการทดลองงาน' ? 'checked' : '' }} class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm flex items-center flex-wrap">
                                    ไม่ผ่านการทดลองงาน เนื่องจาก
                                    <input readonly disabled type="text" name="evaluation_result_reason" value="{{ old('evaluation_result_reason', $probationEvaluation->evaluation_result_reason ?? '') }}" class="ml-2 flex-grow min-w-0 min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                                </span>
                            </label>
                        </div>

                        <!-- Comments -->
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
                            <textarea readonly disabled name="evaluator_comment" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]">{{ old('evaluator_comment', $probationEvaluation->evaluator_comment ?? '') }}</textarea>
                        </div>
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
                            <textarea readonly disabled name="hr_comment" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]">{{ old('hr_comment', $probationEvaluation->hr_comment ?? '') }}</textarea>
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->evaluatee_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->evaluatee_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="evaluatee">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->evaluator_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->evaluator_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="evaluator">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->manager_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->manager_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="manager">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- HR -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                                    <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->hr_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->hr_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="hr">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                        <input readonly disabled type="text" value="{{ 'PE-' . date('Y', strtotime($probationEvaluation->created_at)) . '-' . str_pad($probationEvaluation->id, 4, '0', STR_PAD_LEFT) }}" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-32 sm:w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                    <div class="text-right text-gray-600 font-medium whitespace-nowrap shrink-0">
                        <p>2 / 3</p>
                    </div>
                </div>

                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน</h1>
                </div>
                <!-- 1.4 Performance Evaluation (Round 2) -->
                <div class="mb-10 text-black">
                    <div class="border border-black">
                        <!-- Top Grid: Tasks and Results -->
                        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-black border-b border-black min-h-[160px]">
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 2 : งานที่มอบหมาย (60 วัน)</p>
                                <textarea name="tasks_assigned_2" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder="" disabled readonly>{{ $probationEvaluation->tasks_assigned_2 ?? '' }}</textarea>
                            </div>
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 2 : ผลการปฏิบัติงาน</p>
                                <textarea name="performance_result_2" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder="" disabled readonly>{{ $probationEvaluation->performance_result_2 ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
                            <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_2" value="ต้องปรับปรุง" {{ ($probationEvaluation->performance_level_2 ?? '') == 'ต้องปรับปรุง' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_2" value="สำเร็จตามที่คาดหมาย" {{ ($probationEvaluation->performance_level_2 ?? '') == 'สำเร็จตามที่คาดหมาย' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_2" value="เกินความคาดหมาย" {{ ($probationEvaluation->performance_level_2 ?? '') == 'เกินความคาดหมาย' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
                            </label>
                        </div>

                        <!-- Evaluation Result -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
                            <span class="font-bold text-sm">ผลการประเมินครั้งที่ 2</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_2" value="ทดลองงานต่อ" {{ ($probationEvaluation->evaluation_result_2 ?? '') == 'ทดลองงานต่อ' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_2" value="ผ่านการทดลองงาน" {{ ($probationEvaluation->evaluation_result_2 ?? '') == 'ผ่านการทดลองงาน' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
                            </label>
                            <label class="inline-flex items-center w-full md:w-auto">
                                <input type="radio" name="evaluation_result_2" value="ไม่ผ่านการทดลองงาน" {{ ($probationEvaluation->evaluation_result_2 ?? '') == 'ไม่ผ่านการทดลองงาน' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm flex items-center flex-wrap">
                                    ไม่ผ่านการทดลองงาน เนื่องจาก
                                    <input type="text" name="evaluation_result_reason_2" value="{{ $probationEvaluation->evaluation_result_reason_2 ?? '' }}" class="ml-2 flex-grow min-w-0 min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0" readonly disabled>
                                </span>
                            </label>
                        </div>

                        <!-- Comments -->
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
                            <textarea name="evaluator_comment_2" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" disabled readonly>{{ $probationEvaluation->evaluator_comment_2 ?? '' }}</textarea>
                        </div>
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
                            <textarea name="hr_comment_2" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" disabled readonly>{{ $probationEvaluation->hr_comment_2 ?? '' }}</textarea>
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->evaluatee_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->evaluatee_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="evaluatee">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->evaluator_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->evaluator_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="evaluator">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->manager_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->manager_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="manager">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- HR -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                                    <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->hr_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->hr_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="hr">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                        <input readonly disabled type="text" value="{{ 'PE-' . date('Y', strtotime($probationEvaluation->created_at)) . '-' . str_pad($probationEvaluation->id, 4, '0', STR_PAD_LEFT) }}" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-32 sm:w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                    <div class="text-right text-gray-600 font-medium whitespace-nowrap shrink-0">
                        <p>3 / 3</p>
                    </div>
                </div>

                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-black tracking-wide">แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน</h1>
                </div>

                <!-- 1.5 Performance Evaluation (Round 3) -->
                <div class="mb-10 text-black">
                    <div class="border border-black">
                        <!-- Top Grid: Tasks and Results -->
                        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-black border-b border-black min-h-[160px]">
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 3 : งานที่มอบหมาย (90 วัน)</p>
                                <textarea name="tasks_assigned_3" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder="" disabled readonly>{{ $probationEvaluation->tasks_assigned_3 ?? '' }}</textarea>
                            </div>
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 3 : ผลการปฏิบัติงาน</p>
                                <textarea name="performance_result_3" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder="" disabled readonly>{{ $probationEvaluation->performance_result_3 ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
                            <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_3" value="ต้องปรับปรุง" {{ ($probationEvaluation->performance_level_3 ?? '') == 'ต้องปรับปรุง' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_3" value="สำเร็จตามที่คาดหมาย" {{ ($probationEvaluation->performance_level_3 ?? '') == 'สำเร็จตามที่คาดหมาย' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_3" value="เกินความคาดหมาย" {{ ($probationEvaluation->performance_level_3 ?? '') == 'เกินความคาดหมาย' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
                            </label>
                        </div>

                        <!-- Evaluation Result -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
                            <span class="font-bold text-sm">ผลการประเมินครั้งที่ 3</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_3" value="ทดลองงานต่อ" {{ ($probationEvaluation->evaluation_result_3 ?? '') == 'ทดลองงานต่อ' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_3" value="ผ่านการทดลองงาน" {{ ($probationEvaluation->evaluation_result_3 ?? '') == 'ผ่านการทดลองงาน' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
                            </label>
                            <label class="inline-flex items-center w-full md:w-auto">
                                <input type="radio" name="evaluation_result_3" value="ไม่ผ่านการทดลองงาน" {{ ($probationEvaluation->evaluation_result_3 ?? '') == 'ไม่ผ่านการทดลองงาน' ? 'checked' : '' }} disabled class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm flex items-center flex-wrap">
                                    ไม่ผ่านการทดลองงาน เนื่องจาก
                                    <input type="text" name="evaluation_result_reason_3" value="{{ $probationEvaluation->evaluation_result_reason_3 ?? '' }}" class="ml-2 flex-grow min-w-0 min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0" readonly disabled>
                                </span>
                            </label>
                        </div>

                        <!-- Comments -->
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
                            <textarea name="evaluator_comment_3" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" disabled readonly>{{ $probationEvaluation->evaluator_comment_3 ?? '' }}</textarea>
                        </div>
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
                            <textarea name="hr_comment_3" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" disabled readonly>{{ $probationEvaluation->hr_comment_3 ?? '' }}</textarea>
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->evaluatee_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->evaluatee_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="evaluatee">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->evaluator_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->evaluator_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="evaluator">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->manager_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->manager_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="manager">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- HR -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input readonly disabled type="text" class="flex-grow min-w-0 border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                                    <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center relative">
                                    <span>(</span>
                                    @if($probationEvaluation->hr_name)
                                        <input readonly disabled type="text" value="{{ $probationEvaluation->hr_name }}" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @else
                                        <form method="POST" action="{{ route('admin.probation-evaluations.sign', $probationEvaluation->id) }}" class="absolute inset-0 flex justify-center items-center">
                                            @csrf
                                            <input type="hidden" name="role" value="hr">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs shadow-sm font-medium transition cursor-pointer">คลิกเพื่อลงชื่อ</button>
                                        </form>
                                        <input readonly disabled type="text" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0">
                                    @endif
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
                    <div class="whitespace-nowrap">บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</div>
                    <div class="text-right whitespace-nowrap">
                        <div>QF-HR-18 Rev.09 :</div>
                        <div>02-05-25</div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3 print:hidden">
                    
                    {{-- <a href="{{ route('admin.probation-evaluations.pdf', $probationEvaluation) }}" target="_blank" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Export PDF
                    </a> --}}

                    {{-- <button type="button" onclick="window.print()" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        พิมพ์ (Print)
                    </button> --}}
                </div>
        </div>
        <!-- End of All Pages Wrapper -->
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
            
        });
    </script>

    @include('components.form-share-modal')
</x-app-layout>
