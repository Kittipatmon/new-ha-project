<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight print:hidden">
            {{ __('ใบขออนุมัติกำลังคน (Manpower Request)') }}
            </h2>
    </x-slot>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, button, input, select, textarea, .font-sans, h1, h2, h3, h4, h5, h6, label, span, div {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
    </style>

    <div class="py-8 bg-gray-100 dark:bg-gray-900 min-h-screen relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Left: Form Categories Sidebar -->
                <x-hr-forms-sidebar />

                <!-- Right: Form Paper Container -->
                <div class="flex-1 w-full min-w-0">
                    <form action="{{ route('manpower-request.store') }}" method="POST">
                        @csrf
                        
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

                        <!-- Paper Container Wrapper for Mobile -->
                        <div class="w-full pb-4">
                            <div class="w-full mx-auto bg-white p-6 sm:p-10 lg:p-12 shadow-xl border border-gray-300 text-[15px] text-black font-sans rounded-xl print:shadow-none print:border-none print:px-0 print:py-0">
                            
                            <!-- Title -->
                            <div class="text-center mb-6">
                                <h1 class="text-2xl font-bold tracking-wide">ใบขออนุมัติกำลังคน</h1>
                            </div>

                <!-- Date right aligned -->
                <div class="flex justify-end items-end mb-6">
                    <span class="mr-2">วันที่</span>
                    <input type="text" name="date" class="datepicker-th border-b border-dotted border-gray-800 bg-transparent focus:outline-none w-56 text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('date') placeholder="{{ $message }}" @enderror value="{{ old('date', date('Y-m-d')) }}">
                </div>

                <!-- Line 1: ฝ่าย, แผนก -->
                <div class="flex items-end mb-4 min-w-0">
                    <span class="mr-2 whitespace-nowrap shrink-0">ฝ่าย</span>
                    <input type="text" name="department" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('department') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('department') placeholder="{{ $message }}" @enderror value="{{ old('department') }}">
                    <span class="mx-2 whitespace-nowrap shrink-0">แผนก</span>
                    <input type="text" name="section" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('section') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('section') placeholder="{{ $message }}" @enderror value="{{ old('section') }}">
                </div>

                <!-- Line 2: ขออนุมัติตำแหน่ง -->
                <div class="flex items-end mb-4 min-w-0">
                    <span class="mr-2 whitespace-nowrap shrink-0">ขออนุมัติตำแหน่ง (ชื่อไทย)</span>
                    <input type="text" name="job_title_th" class="flex-1 min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('job_title_th') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('job_title_th') placeholder="{{ $message }}" @enderror value="{{ old('job_title_th') }}">
                    <span class="mx-2 whitespace-nowrap shrink-0">(ชื่ออังกฤษ)</span>
                    <input type="text" name="job_title_en" class="flex-1 min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('job_title_en') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('job_title_en') placeholder="{{ $message }}" @enderror value="{{ old('job_title_en') }}">
                    <span class="mx-2 whitespace-nowrap shrink-0">จำนวน</span>
                    <input type="number" name="headcount" class="w-20 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('headcount') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('headcount') placeholder="{{ $message }}" @enderror value="{{ old('headcount') }}">
                    <span class="ml-2 whitespace-nowrap shrink-0">อัตรา</span>
                </div>

                <!-- Line 3: พนักงานทั้งหมด -->
                <div class="flex items-end mb-6 min-w-0">
                    <span class="mr-2 whitespace-nowrap shrink-0">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด</span>
                    <input type="number" name="current_headcount" class="w-24 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('current_headcount') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('current_headcount') placeholder="{{ $message }}" @enderror value="{{ old('current_headcount') }}">
                    <span class="mx-2 whitespace-nowrap shrink-0">อัตรา</span>
                    <span class="mr-2 whitespace-nowrap ml-4 shrink-0">ต้องการรับเข้าทำงานภายในวันที่</span>
                    <input type="text" name="expected_start_date" class="datepicker-th flex-grow min-w-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('expected_start_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('expected_start_date') placeholder="{{ $message }}" @enderror value="{{ old('expected_start_date') }}">
                </div>

                <!-- ระดับ -->
                <div class="flex items-center mb-4">
                    <span class="w-24 whitespace-nowrap shrink-0">ระดับ</span>
                    <div class="flex-grow flex justify-between">
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="บริหาร (Lv.9-8)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'บริหาร (Lv.9-8)' ? 'checked' : '' }}><span>บริหาร</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.9-8)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="ผู้จัดการ (Lv.7)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'ผู้จัดการ (Lv.7)' ? 'checked' : '' }}><span>ผู้จัดการ</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.7)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="ผจก./หัวหน้าส่วนงาน (Lv.6-5)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'ผจก./หัวหน้าส่วนงาน (Lv.6-5)' ? 'checked' : '' }}><span>ผจก.ผจก./หัวหน้าส่วนงาน</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.6-5)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)' ? 'checked' : '' }}><span>วิศวกร/อาวุโสเจ้าหน้าที่</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.4-3)</span>
                        </label>
                        <label class="flex flex-col items-center cursor-pointer">
                            <div class="flex items-center"><input type="radio" name="job_level" value="Sup/ปฏิบัติการ (Lv.2-1)" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'Sup/ปฏิบัติการ (Lv.2-1)' ? 'checked' : '' }}><span>Sup/ปฏิบัติการ</span></div>
                            <span class="text-xs text-gray-700 mt-0.5">(Lv.2-1)</span>
                        </label>
                    </div>
                </div>

                <!-- ลักษณะการว่าจ้าง -->
                <div class="flex items-start mb-6 mt-6">
                    <span class="w-32 whitespace-nowrap pt-0.5 shrink-0">ลักษณะการว่าจ้าง</span>
                    <div class="flex-grow min-w-0 flex flex-col space-y-3" x-data="{ hire_type: '{{ old('hire_type', '') }}' }">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="hire_type" value="จ้างเพิ่มเติม" x-model="hire_type" class="mr-3 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                            <span>จ้างเพิ่มเติม</span>
                        </label>
                        <div class="flex items-end min-w-0">
                            <label class="flex items-center cursor-pointer mr-2 shrink-0">
                                <input type="radio" name="hire_type" value="จ้างทดแทน" x-model="hire_type" class="mr-3 mb-1 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                                <span class="whitespace-nowrap">จ้างทดแทนคนเก่า คือ นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_replacement_name" list="employee_names" x-bind:disabled="hire_type !== 'จ้างทดแทน'" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed @error('hire_replacement_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_replacement_name') placeholder="{{ $message }}" @enderror value="{{ old('hire_replacement_name') }}">
                        </div>
                        <div class="flex items-end min-w-0">
                            <label class="flex items-center cursor-pointer mr-2 shrink-0">
                                <input type="radio" name="hire_type" value="โอนย้าย" x-model="hire_type" class="mr-3 mb-1 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                                <span class="whitespace-nowrap">โอนย้าย / ปรับเปลี่ยนตำแหน่ง คือ นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_transfer_name" list="employee_names" x-bind:disabled="hire_type !== 'โอนย้าย'" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed @error('hire_transfer_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_transfer_name') placeholder="{{ $message }}" @enderror value="{{ old('hire_transfer_name') }}">
                        </div>
                        <div class="flex items-end min-w-0">
                            <label class="flex items-center cursor-pointer mr-2 shrink-0">
                                <input type="radio" name="hire_type" value="จ้างชั่วคราว" x-model="hire_type" class="mr-3 mb-1 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                                <span class="whitespace-nowrap mr-8">จ้างชั่วคราว</span>
                                <span class="whitespace-nowrap mr-2">ระยะเวลา จากวันที่</span>
                            </label>
                            <input type="text" name="hire_temp_start" x-bind:disabled="hire_type !== 'จ้างชั่วคราว'" class="datepicker-th w-32 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed @error('hire_temp_start') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_temp_start') placeholder="{{ $message }}" @enderror value="{{ old('hire_temp_start') }}">
                            <span class="mx-2 whitespace-nowrap shrink-0">ถึง</span>
                            <input type="text" name="hire_temp_end" x-bind:disabled="hire_type !== 'จ้างชั่วคราว'" class="datepicker-th flex-grow min-w-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed @error('hire_temp_end') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_temp_end') placeholder="{{ $message }}" @enderror value="{{ old('hire_temp_end') }}">
                        </div>
                    </div>
                </div>

                <!-- เอกสารแนบ -->
                <div class="flex items-center mb-6 pl-8">
                    <span class="w-24 whitespace-nowrap">เอกสารแนบ</span>
                    <div class="flex-grow flex justify-between pr-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_org_chart" value="1" class="mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800 @error('attachment_org_chart') ring-2 ring-red-500 border-red-500 @enderror" {{ old('attachment_org_chart') ? 'checked' : '' }}>
                            <span>Organization Chart</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_jd" value="1" class="@error('attachment_org_chart') ring-2 ring-red-500 border-red-500 @enderror mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800 @error('attachment_jd') ring-2 ring-red-500 border-red-500 @enderror" {{ old('attachment_jd') ? 'checked' : '' }}>
                            <span>Job Description</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="attachment_manpower_plan" value="1" class="@error('attachment_org_chart') ring-2 ring-red-500 border-red-500 @enderror mr-3 w-4 h-4 rounded-full text-black focus:ring-black border-gray-800 @error('attachment_manpower_plan') ring-2 ring-red-500 border-red-500 @enderror" {{ old('attachment_manpower_plan') ? 'checked' : '' }}>
                            <span>แผนอัตรากำลังคน</span>
                        </label>
                    </div>
                </div>

                <!-- 2 Column Table -->
                <div class="border-[1.5px] border-black mb-6 flex">
                    <!-- Left Column -->
                    <div class="w-1/2 border-r-[1.5px] border-black">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">คุณสมบัติที่ต้องการ</div>
                        <div class="p-3 space-y-4">
                            <div class="flex items-center">
                                <span class="w-16 whitespace-nowrap">เพศ</span>
                                <label class="flex items-center mr-4 cursor-pointer"><input type="radio" name="req_gender" value="ชาย" class="mr-2 w-4 h-4 text-black border-gray-800 focus:ring-black @error('req_gender') ring-2 ring-red-500 border-red-500 @enderror" {{ old('req_gender') == 'ชาย' ? 'checked' : '' }}> ชาย</label>
                                <label class="flex items-center mr-4 cursor-pointer"><input type="radio" name="req_gender" value="หญิง" class="mr-2 w-4 h-4 text-black border-gray-800 focus:ring-black @error('req_gender') ring-2 ring-red-500 border-red-500 @enderror" {{ old('req_gender') == 'หญิง' ? 'checked' : '' }}> หญิง</label>
                                <label class="flex items-center cursor-pointer"><input type="radio" name="req_gender" value="ชาย/หญิง" class="mr-2 w-4 h-4 text-black border-gray-800 focus:ring-black @error('req_gender') ring-2 ring-red-500 border-red-500 @enderror" {{ old('req_gender') == 'ชาย/หญิง' ? 'checked' : '' }}> ชาย/หญิง</label>
                            </div>
                            <div class="flex items-end flex-nowrap w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">อายุ :</span>
                                <input type="text" name="req_age" class="w-16 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_age') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_age') placeholder="{{ $message }}" @enderror value="{{ old('req_age') }}">
                                <span class="mx-2 whitespace-nowrap">วุฒิการศึกษา :</span>
                                <input type="text" name="req_education" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_education') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_education') placeholder="{{ $message }}" @enderror value="{{ old('req_education') }}">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">สาขาวิชา :</span>
                                <input type="text" name="req_major" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_major') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_major') placeholder="{{ $message }}" @enderror value="{{ old('req_major') }}">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">ประสบการณ์ทำงาน :</span>
                                <input type="text" name="req_experience" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_experience') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_experience') placeholder="{{ $message }}" @enderror value="{{ old('req_experience') }}">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">คุณสมบัติพิเศษ :</span>
                                <input type="text" name="req_special" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_special') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_special') placeholder="{{ $message }}" @enderror value="{{ old('req_special') }}">
                            </div>
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2 whitespace-nowrap">อื่นๆ :</span>
                                <input type="text" name="req_other" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_other') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_other') placeholder="{{ $message }}" @enderror value="{{ old('req_other') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="w-1/2">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">หน้าที่รับผิดชอบโดยสังเขป</div>
                        <div class="p-3 space-y-[1.125rem]">
                            @for($i=1; $i<=6; $i++)
                            <div class="flex items-end w-full overflow-hidden">
                                <span class="mr-2">{{ $i }}</span>
                                <input type="text" name="res_{{ $i }}" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('res_'.$i) border-red-500 border-b-2 placeholder-red-400 @enderror" @error('res_'.$i) placeholder="{{ $message }}" @enderror value="{{ old('res_'.$i) }}">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="space-y-6 mb-10 mt-8 min-w-0">
                    <div class="flex justify-between items-end gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                            <input type="text" name="requester_name" list="employee_names" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('requester_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('requester_name') placeholder="{{ $message }}" @enderror value="{{ old('requester_name') }}">
                            <span class="ml-2 whitespace-nowrap shrink-0">ผู้ร้องขอ</span>
                        </div>
                        <div class="flex items-end w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="requester_date" class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('requester_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('requester_date') placeholder="{{ $message }}" @enderror value="{{ old('requester_date') }}">
                        </div>
                    </div>
                    <div class="flex justify-between items-end gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                            <input type="text" name="manager_name" list="employee_names" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('manager_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('manager_name') placeholder="{{ $message }}" @enderror value="{{ old('manager_name') }}">
                            <span class="ml-2 whitespace-nowrap shrink-0">ผู้จัดการแผนก/ฝ่าย</span>
                        </div>
                        <div class="flex items-end w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="manager_date" class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('manager_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('manager_date') placeholder="{{ $message }}" @enderror value="{{ old('manager_date') }}">
                        </div>
                    </div>
                    <div class="flex justify-between items-end gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ</span>
                            <input type="text" name="vp_name" list="employee_names" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('vp_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('vp_name') placeholder="{{ $message }}" @enderror value="{{ old('vp_name') }}">
                            <span class="ml-2 whitespace-nowrap shrink-0">ประธานสายงาน</span>
                        </div>
                        <div class="flex items-end w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="vp_date" class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('vp_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('vp_date') placeholder="{{ $message }}" @enderror value="{{ old('vp_date') }}">
                        </div>
                    </div>
                </div>

                <!-- Approvals Table -->
                <div class="border-[1.5px] border-black mb-4 flex">
                    <!-- Left Column: HR -->
                    <div class="w-1/2 flex flex-col border-r-[1.5px] border-black">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นฝ่ายบุคคล</div>
                        <div class="p-4 flex flex-col flex-grow relative pb-20">
                            <div class="w-full flex items-start">
                                <span class="mr-2 mt-2 whitespace-nowrap">ความเห็น :</span>
                                <textarea disabled class="flex-grow bg-transparent border-none resize-none focus:ring-0 px-2 py-2 h-16"></textarea>
                            </div>
                            <div class="w-full px-8 absolute bottom-4 left-0">
                                <div class="text-center w-full mb-2">(.......................................................................................)</div>
                                <div class="text-center w-full">ผจก.แผนก/ฝ่ายทรัพยากรบุคคล</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: CEO -->
                    <div class="w-1/2 flex flex-col">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นประธานเจ้าหน้าที่บริหาร</div>
                        <div class="p-4 flex flex-col flex-grow relative pb-20">
                            <div class="flex justify-center space-x-12 w-full mt-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" disabled class="mr-3 w-4 h-4 text-black border-black"> 
                                    <span>อนุมัติตามคำขอ</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" disabled class="mr-3 w-4 h-4 text-black border-black"> 
                                    <span>ไม่อนุมัติตามคำขอ</span>
                                </label>
                            </div>
                            <div class="w-full px-8 absolute bottom-4 left-0">
                                <div class="text-center w-full mb-2">(.......................................................................................)</div>
                                <div class="text-center w-full">ประธานเจ้าหน้าที่บริหาร</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-red-600 text-[13px] mb-8">
                    หมายเหตุ : ใบ Request กำลังคนต้องขออนุมัติล่วงหน้า 30 วัน และกำหนดคุณสมบัติให้ละเอียด
                </div>

                <!-- HR Use Only -->
                <div class="mb-10 font-bold overflow-hidden w-full">
                    <div class="mb-4">สำหรับเจ้าหน้าที่ฝ่ายบุคคลบันทึก :</div>
                    <div class="flex items-end w-full flex-nowrap">
                        <span class="mr-2 whitespace-nowrap font-normal">ได้รับพนักงาน (รหัสพนักงาน)</span>
                        <input type="text" disabled class="w-16 sm:w-20 flex-shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        <span class="mx-2 whitespace-nowrap font-normal">(ชื่อ-สกุล)</span>
                        <input type="text" disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        <span class="mx-2 whitespace-nowrap font-normal">เข้าทำงานในวันที่</span>
                        <input type="text" disabled class="w-20 sm:w-24 flex-shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                    </div>
                </div>

                <!-- Document ID -->
                <div class="text-right text-sm mb-6">
                    QF-HR-13 Rev.08 : 01-06-25
                </div>

                <!-- Submit Button -->
                <div class="mt-12 flex justify-center space-x-4 print:hidden">
                    <a href="{{ url()->previous() }}" class="px-8 py-3 border border-gray-300 rounded hover:bg-gray-50 transition-colors duration-200 font-medium tracking-wider text-sm shadow-sm flex items-center text-gray-700">
                        ยกเลิก
                    </a>
                    <button type="submit" class="bg-black text-white px-10 py-3 rounded hover:bg-gray-800 transition-colors duration-200 font-medium tracking-wider text-sm shadow-md flex items-center group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 group-hover:-translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        บันทึกข้อมูลแบบฟอร์ม
                    </button>
                </div>
                </div>
                <!-- End of Paper Container -->
                </div>
                <!-- End of Wrapper -->

            </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Flatpickr for Date Format -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    
    <style>
        /* Flatpickr Custom style to fit underline inputs */
        .flatpickr-calendar {
            font-family: inherit;
        }
    </style>
    
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

            flatpickr.localize(flatpickr.l10ns.th);
            
            // Format dates (assuming buddhist year is handled server-side or just formatting dd/mm/yyyy)
            flatpickr(".datepicker-th", {
                locale: "th",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                allowInput: true,
                onReady: function(selectedDates, dateStr, instance) {
                    // Update the alt input class to match paper style
                    if (instance.altInput) {
                        // Copy classes from original to altInput, removing standard borders
                        instance.altInput.className = instance.input.className;
                        instance.altInput.classList.remove('datepicker-th');
                        // Add flatpickr-input to prevent double binding
                        instance.altInput.classList.add('flatpickr-input');
                    }
                }
            });
        });
    </script>
</x-app-layout>
