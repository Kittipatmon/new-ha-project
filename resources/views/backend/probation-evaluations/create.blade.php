<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('สร้างแบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen relative overflow-x-hidden">
        
        <!-- Alert / Toast -->
        @if(session('success') || session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-full"
                 class="fixed top-20 right-4 z-50 flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg max-w-sm w-full {{ session('success') ? 'text-green-800 border border-green-300 bg-green-50' : 'text-red-800 border border-red-300 bg-red-50' }}" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 me-3 {{ session('success') ? 'text-green-500' : 'text-red-500' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="flex-grow">
                    <span class="font-bold">{{ session('success') ? 'สำเร็จ!' : 'ข้อผิดพลาด!' }}</span><br>
                    <span class="opacity-90">{{ session('success') ?? session('error') }}</span>
                </div>
                <button @click="show = false" type="button" class="ms-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex items-center justify-center h-8 w-8 hover:bg-black/10 {{ session('success') ? 'focus:ring-green-400' : 'focus:ring-red-400' }}" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        @endif

        @php
            $kumEmployees = \Illuminate\Support\Facades\DB::connection('appkum_user')
                ->table('employees')
                ->where('status', 'active')
                ->orderBy('firstname')
                ->get();
        @endphp

        <datalist id="employee_names">
            @foreach($kumEmployees as $emp)
                <option value="{{ $emp->firstname }} {{ $emp->lastname }}"></option>
            @endforeach
        </datalist>
        
        <form action="{{ route('admin.probation-evaluations.store') ?? '#' }}" method="POST">
            @csrf
            
            <!-- PAGE 1/3 Paper Container -->
            <div class="max-w-[1000px] mx-auto bg-white p-10 sm:p-16 shadow-xl border border-gray-300 mb-8 print:shadow-none print:border-none print:p-0 print:mb-0">
                
                <!-- Header Section -->
                <div class="flex justify-between items-start mb-6 text-sm">
                    <div>
                        <span>แบบประเมินเลขที่</span>
                        <input type="text" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                    <div class="text-right text-gray-600">
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
                                <input type="radio" name="prefix" value="นาย" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-1 text-xs">นาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="prefix" value="นาง" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-1 text-xs">นาง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="prefix" value="นางสาว" class="form-radio h-3.5 w-3.5 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-1 text-xs">นางสาว</span>
                            </label>
                        </div>
                        <input type="text" name="employee_name" class="flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">ตำแหน่ง</span>
                        <input type="text" name="position" class="flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">รหัสพนักงาน</span>
                        <input type="text" name="emp_code" class="flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">แผนก/ ฝ่าย</span>
                        <input type="text" name="department" class="flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">วันที่เริ่มงาน</span>
                        <input type="text" name="start_date" class="datepicker-th flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                    </div>
                    <div class="flex items-end">
                        <span class="whitespace-nowrap mr-2">วันที่ครบทดลองงาน</span>
                        <input type="text" name="probation_due_date" class="datepicker-th flex-grow border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
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
                                        <input type="text" name="absence[{{$i}}][start]" class="datepicker-th w-24 sm:w-28 border-b border-gray-400 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                                        <span class="mx-2">ถึง</span>
                                        <input type="text" name="absence[{{$i}}][end]" class="datepicker-th w-24 sm:w-28 border-b border-gray-400 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 text-gray-700">
                                    </td>
                                    <td class="p-0"><input type="text" name="absence[{{$i}}][business_leave]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input type="text" name="absence[{{$i}}][sick_leave]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input type="text" name="absence[{{$i}}][absent]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input type="text" name="absence[{{$i}}][late_count]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input type="text" name="absence[{{$i}}][late_mins]" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
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
                                    <td class="py-2 px-3 text-left">{!! str_replace('................................................................', '<input type="text" name="exam_other_topic" class="border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0 inline w-48">', $topic) !!}</td>
                                    <td class="p-0 text-center"><input type="radio" name="exam_passed_round[{{$loop->index}}]" value="1" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent"></td>
                                    <td class="p-0 text-center"><input type="radio" name="exam_passed_round[{{$loop->index}}]" value="2" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent"></td>
                                    <td class="p-0 text-center"><input type="radio" name="exam_passed_round[{{$loop->index}}]" value="3" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent"></td>
                                    <td class="p-0"><input type="text" name="exam_date[{{$loop->index}}]" class="datepicker-th w-full h-full text-center border-none bg-transparent focus:ring-0 py-2"></td>
                                    <td class="p-0"><input type="text" name="exam_tester[{{$loop->index}}]" list="employee_names" class="w-full h-full text-center border-none bg-transparent focus:ring-0 py-2 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา..."></td>
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
                                <textarea name="tasks_assigned" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder=""></textarea>
                            </div>
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 1 : ผลการปฏิบัติงาน</p>
                                <textarea name="performance_result" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder=""></textarea>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
                            <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level" value="ต้องปรับปรุง" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level" value="สำเร็จตามที่คาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level" value="เกินความคาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
                            </label>
                        </div>

                        <!-- Evaluation Result -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
                            <span class="font-bold text-sm">ผลการประเมินครั้งที่ 1</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result" value="ทดลองงานต่อ" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result" value="ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
                            </label>
                            <label class="inline-flex items-center w-full md:w-auto">
                                <input type="radio" name="evaluation_result" value="ไม่ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm flex items-center flex-wrap">
                                    ไม่ผ่านการทดลองงาน เนื่องจาก
                                    <input type="text" name="evaluation_result_reason" class="ml-2 flex-grow min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                                </span>
                            </label>
                        </div>

                        <!-- Comments -->
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
                            <textarea name="evaluator_comment" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]"></textarea>
                        </div>
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
                            <textarea name="hr_comment" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]"></textarea>
                        </div>

                        <!-- Signatures -->
                        <div class="grid grid-cols-1 md:grid-cols-2 p-6 gap-x-12 gap-y-10">
                            <!-- User -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้รับการประเมิน</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="evaluatee_name" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>
                            
                            <!-- Evaluator -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้ประเมิน</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="evaluator_name" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- Manager -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้จัดการฝ่าย/แผนก</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="manager_name" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- HR -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                                    <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="hr_name" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0" readonly>
                                    <span>)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-end mt-12 text-xs text-gray-500 text-black">
                    <div>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</div>
                    <div>QF-HR-18 Rev.09 : 02-05-25</div>
                </div>
            </div>

            <!-- ==================== PAGE 2/3 ==================== -->
            <div class="max-w-[1000px] mx-auto bg-white p-10 sm:p-16 shadow-xl border border-gray-300 mb-8 print:shadow-none print:border-none print:p-0 print:mb-0 break-before-page print:break-before-page" style="page-break-before: always;">
                <!-- Header Section -->
                <div class="flex justify-between items-start mb-6 text-sm mt-8 print:mt-0">
                    <div>
                        <span>แบบประเมินเลขที่</span>
                        <input type="text" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                    <div class="text-right text-gray-600">
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
                                <textarea name="tasks_assigned_2" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder=""></textarea>
                            </div>
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 2 : ผลการปฏิบัติงาน</p>
                                <textarea name="performance_result_2" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder=""></textarea>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
                            <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_2" value="ต้องปรับปรุง" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_2" value="สำเร็จตามที่คาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_2" value="เกินความคาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
                            </label>
                        </div>

                        <!-- Evaluation Result -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
                            <span class="font-bold text-sm">ผลการประเมินครั้งที่ 2</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_2" value="ทดลองงานต่อ" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_2" value="ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
                            </label>
                            <label class="inline-flex items-center w-full md:w-auto">
                                <input type="radio" name="evaluation_result_2" value="ไม่ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm flex items-center flex-wrap">
                                    ไม่ผ่านการทดลองงาน เนื่องจาก
                                    <input type="text" name="evaluation_result_reason_2" class="ml-2 flex-grow min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                                </span>
                            </label>
                        </div>

                        <!-- Comments -->
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
                            <textarea name="evaluator_comment_2" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]"></textarea>
                        </div>
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
                            <textarea name="hr_comment_2" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]"></textarea>
                        </div>

                        <!-- Signatures -->
                        <div class="grid grid-cols-1 md:grid-cols-2 p-6 gap-x-12 gap-y-10">
                            <!-- User -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้รับการประเมิน</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="evaluatee_name_2" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>
                            
                            <!-- Evaluator -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้ประเมิน</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="evaluator_name_2" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- Manager -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้จัดการฝ่าย/แผนก</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="manager_name_2" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- HR -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                                    <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="hr_name_2" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0" readonly>
                                    <span>)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-end mt-12 text-xs text-gray-500 text-black">
                    <div>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</div>
                    <div>QF-HR-18 Rev.09 : 02-05-25</div>
                </div>
            </div>

            <!-- ==================== PAGE 3/3 ==================== -->
            <div class="max-w-[1000px] mx-auto bg-white p-10 sm:p-16 shadow-xl border border-gray-300 mb-8 print:shadow-none print:border-none print:p-0 print:mb-0 break-before-page print:break-before-page" style="page-break-before: always;">
                <!-- Header Section -->
                <div class="flex justify-between items-start mb-6 text-sm mt-8 print:mt-0">
                    <div>
                        <span>แบบประเมินเลขที่</span>
                        <input type="text" class="border-b border-gray-400 bg-transparent focus:outline-none focus:border-black w-48 px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                    <div class="text-right text-gray-600">
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
                                <textarea name="tasks_assigned_3" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder=""></textarea>
                            </div>
                            <div class="p-3">
                                <p class="font-bold text-sm mb-2">ครั้งที่ 3 : ผลการปฏิบัติงาน</p>
                                <textarea name="performance_result_3" class="w-full h-32 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]" placeholder=""></textarea>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center gap-4 md:gap-10">
                            <span class="font-bold text-sm min-w-[180px]">ระดับผลการดำเนินงานที่มอบหมาย</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_3" value="ต้องปรับปรุง" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ต้องปรับปรุง</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_3" value="สำเร็จตามที่คาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">สำเร็จตามที่คาดหมาย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="performance_level_3" value="เกินความคาดหมาย" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">เกินความคาดหมาย</span>
                            </label>
                        </div>

                        <!-- Evaluation Result -->
                        <div class="p-3 border-b border-black flex flex-col md:flex-row md:items-center flex-wrap gap-4">
                            <span class="font-bold text-sm">ผลการประเมินครั้งที่ 3</span>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_3" value="ทดลองงานต่อ" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ทดลองงานต่อ</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="evaluation_result_3" value="ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm">ผ่านการทดลองงาน</span>
                            </label>
                            <label class="inline-flex items-center w-full md:w-auto">
                                <input type="radio" name="evaluation_result_3" value="ไม่ผ่านการทดลองงาน" class="form-radio h-4 w-4 text-gray-900 border-gray-400 focus:ring-0 bg-transparent">
                                <span class="ml-2 text-sm flex items-center flex-wrap">
                                    ไม่ผ่านการทดลองงาน เนื่องจาก
                                    <input type="text" name="evaluation_result_reason_3" class="ml-2 flex-grow min-w-[150px] border-b border-gray-400 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 focus:ring-0">
                                </span>
                            </label>
                        </div>

                        <!-- Comments -->
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</span>
                            <textarea name="evaluator_comment_3" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]"></textarea>
                        </div>
                        <div class="p-3 border-b border-black">
                            <span class="font-bold text-sm">สรุปความเห็นฝ่ายทรัพยากรบุคคล</span>
                            <textarea name="hr_comment_3" class="w-full mt-2 h-12 border-none bg-transparent resize-none focus:ring-0 p-0 leading-[2.2em] bg-[linear-gradient(transparent_95%,#cbd5e1_95%)] bg-[length:100%_2.2em]"></textarea>
                        </div>

                        <!-- Signatures -->
                        <div class="grid grid-cols-1 md:grid-cols-2 p-6 gap-x-12 gap-y-10">
                            <!-- User -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้รับการประเมิน</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="evaluatee_name_3" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>
                            
                            <!-- Evaluator -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้ประเมิน</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="evaluator_name_3" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- Manager -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center">
                                    <span class="ml-2 text-xs text-gray-500 w-24">ผู้จัดการฝ่าย/แผนก</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="manager_name_3" list="employee_names" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0 placeholder-gray-400" placeholder="พิมพ์ชื่อเพื่อค้นหา...">
                                    <span>)</span>
                                </div>
                            </div>

                            <!-- HR -->
                            <div class="flex flex-col items-center">
                                <div class="flex items-end w-full mb-2">
                                    <span class="mr-2">ลงชื่อ</span>
                                    <input type="text" class="flex-grow border-b border-dashed border-gray-400 bg-transparent focus:outline-none py-0 focus:ring-0 text-center" readonly>
                                    <span class="ml-2 text-xs text-gray-500 w-24">ฝ่ายทรัพยากรบุคคล</span>
                                </div>
                                <div class="flex w-full px-6 justify-center items-center">
                                    <span>(</span>
                                    <input type="text" name="hr_name_3" class="text-center w-full bg-transparent focus:outline-none border-none focus:ring-0" readonly>
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

                <div class="flex justify-between items-end mt-12 text-xs text-gray-500 text-black">
                    <div>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</div>
                    <div>QF-HR-18 Rev.09 : 02-05-25</div>
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
        </form>
    </div>

    <!-- Flatpickr setup for Thai localization -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".datepicker-th", {
                locale: "th",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y", // display format (e.g. 24/07/2026)
                allowInput: true
            });
        });
    </script>
</x-admin-layout>
