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

    <div class="py-6 sm:py-8 bg-gray-100 dark:bg-gray-900 min-h-screen relative">
        <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- Left: Form Categories Sidebar -->
                <x-hr-forms-sidebar />

                <!-- Right: Form Paper Container -->
                <div class="flex-1 w-full min-w-0">
                    <form action="{{ route('manpower-request.store') }}" method="POST" id="manpowerRequestForm">
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

                            // Load departments & divisions for "ฝ่าย" and "แผนก"
                            try {
                                $allDivisions = \App\Models\Division::all();
                                $allDepartments = \App\Models\Department::all();
                                $allSections = \App\Models\Section::all();

                                $deptOptions = collect();
                                foreach ($allDivisions as $div) {
                                    $c = trim($div->division_name ?? '');
                                    $f = trim($div->division_fullname ?? '');
                                    if ($c && $c !== '-') $deptOptions->push(['code' => $c, 'name' => $f]);
                                }
                                foreach ($allDepartments as $dept) {
                                    $c = trim($dept->department_name ?? '');
                                    $f = trim($dept->department_fullname ?? '');
                                    if ($c && $c !== '-') $deptOptions->push(['code' => $c, 'name' => $f]);
                                }
                                $deptOptions = $deptOptions->unique('code')->sortBy('code')->values();

                                $secOptions = collect();
                                foreach ($allDepartments as $dept) {
                                    $c = trim($dept->department_name ?? '');
                                    $f = trim($dept->department_fullname ?? '');
                                    if ($c && $c !== '-') $secOptions->push(['code' => $c, 'name' => $f]);
                                }
                                foreach ($allDivisions as $div) {
                                    $c = trim($div->division_name ?? '');
                                    $f = trim($div->division_fullname ?? '');
                                    if ($c && $c !== '-') $secOptions->push(['code' => $c, 'name' => $f]);
                                }
                                foreach ($allSections as $sec) {
                                    $c = trim($sec->section_code ?? '');
                                    $f = trim($sec->section_name ?? '');
                                    if ($c && $c !== '-') $secOptions->push(['code' => $c, 'name' => $f]);
                                }
                                $secOptions = $secOptions->unique('code')->sortBy('code')->values();
                            } catch (\Throwable $e) {
                                $deptOptions = collect([]);
                                $secOptions = collect([]);
                            }
                        @endphp
                        
                        <datalist id="employee_names">
                            @foreach($kumEmployees as $emp)
                                <option value="{{ $emp->firstname }} {{ $emp->lastname }}"></option>
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

                        <datalist id="section_list">
                            @foreach($secOptions as $s)
                                <option value="{{ $s['code'] }}">{{ $s['name'] ?: $s['code'] }}</option>
                                @if(!empty($s['name']) && $s['name'] !== $s['code'])
                                    <option value="{{ $s['name'] }}">{{ $s['code'] }}</option>
                                @endif
                            @endforeach
                        </datalist>

                        <!-- Paper Container Wrapper for Mobile -->
                        <div class="hr-form-container">
                            <div class="hr-form-paper">
                            
                            <!-- Title -->
                            <div class="text-center pt-2 sm:pt-4 mb-6 sm:mb-8">
                                <h1 class="text-2xl sm:text-3xl font-bold tracking-wide">ใบขออนุมัติกำลังคน</h1>
                            </div>

                <!-- Date right aligned -->
                <div class="flex justify-end items-end mb-6">
                    <span class="mr-2 shrink-0 font-medium">วันที่ <span class="text-red-500">*</span></span>
                    <input type="text" name="date" class="datepicker-th border-b border-dotted border-gray-800 bg-transparent focus:outline-none w-36 sm:w-48 text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('date') placeholder="{{ $message }}" @enderror value="{{ old('date', date('Y-m-d')) }}">
                </div>

                <!-- Line 1: ฝ่าย, แผนก -->
                <div class="flex flex-wrap sm:flex-nowrap items-end gap-4 mb-4">
                    <div class="flex items-end flex-1 min-w-[200px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ฝ่าย <span class="text-red-500">*</span></span>
                        <input type="text" name="department" id="department_input" list="department_list" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('department') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('department') placeholder="{{ $message }}" @enderror value="{{ old('department') }}">
                    </div>
                    <div class="flex items-end flex-1 min-w-[200px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">แผนก <span class="text-red-500">*</span></span>
                        <input type="text" name="section" id="section_input" list="section_list" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('section') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('section') placeholder="{{ $message }}" @enderror value="{{ old('section') }}">
                    </div>
                </div>

                <!-- Line 2: ขออนุมัติตำแหน่ง (ชื่อไทย, ชื่ออังกฤษ, จำนวน) -->
                <div class="flex flex-wrap lg:flex-nowrap items-end gap-x-4 gap-y-3 mb-4">
                    <div class="flex items-end flex-1 min-w-[220px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ขออนุมัติตำแหน่ง (ชื่อไทย) <span class="text-red-500">*</span></span>
                        <input type="text" name="job_title_th" class="flex-1 min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('job_title_th') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('job_title_th') placeholder="{{ $message }}" @enderror value="{{ old('job_title_th') }}">
                    </div>
                    <div class="flex items-end flex-1 min-w-[190px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">(ชื่ออังกฤษ) <span class="text-red-500">*</span></span>
                        <input type="text" name="job_title_en" class="flex-1 min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('job_title_en') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('job_title_en') placeholder="{{ $message }}" @enderror value="{{ old('job_title_en') }}">
                    </div>
                    <div class="flex items-end shrink-0">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">จำนวน <span class="text-red-500">*</span></span>
                        <input type="number" name="headcount" class="w-16 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('headcount') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('headcount') placeholder="{{ $message }}" @enderror value="{{ old('headcount') }}">
                        <span class="ml-2 whitespace-nowrap shrink-0">อัตรา</span>
                    </div>
                </div>

                <!-- Line 3: พนักงานทั้งหมด & วันที่ต้องการ -->
                <div class="flex flex-wrap lg:flex-nowrap items-end gap-x-6 gap-y-3 mb-6">
                    <div class="flex items-end shrink-0 min-w-0">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด <span class="text-red-500">*</span></span>
                        <input type="number" name="current_headcount" class="w-16 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('current_headcount') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('current_headcount') placeholder="{{ $message }}" @enderror value="{{ old('current_headcount') }}">
                        <span class="ml-2 whitespace-nowrap shrink-0">อัตรา</span>
                    </div>
                    <div class="flex items-end flex-grow min-w-[260px]">
                        <span class="mr-2 whitespace-nowrap shrink-0 font-medium">ต้องการรับเข้าทำงานภายในวันที่ <span class="text-red-500">*</span></span>
                        <input type="text" name="expected_start_date" class="datepicker-th flex-grow min-w-[110px] text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('expected_start_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('expected_start_date') placeholder="{{ $message }}" @enderror value="{{ old('expected_start_date') }}">
                    </div>
                </div>

                <!-- ระดับ -->
                <div class="flex flex-col sm:flex-row sm:items-start gap-2 mb-6">
                    <span class="mr-4 whitespace-nowrap shrink-0 font-medium pt-1">ระดับ <span class="text-red-500">*</span></span>
                    <div class="flex-grow min-w-0 flex flex-wrap items-center gap-x-6 gap-y-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="บริหาร (Lv.9-8)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'บริหาร (Lv.9-8)' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-900">บริหาร <span class="text-xs text-gray-500 font-normal">(Lv.9-8)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="ผู้จัดการ (Lv.7)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'ผู้จัดการ (Lv.7)' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-900">ผู้จัดการ <span class="text-xs text-gray-500 font-normal">(Lv.7)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="ผจก./หัวหน้าส่วนงาน (Lv.6-5)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'ผจก./หัวหน้าส่วนงาน (Lv.6-5)' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-900">ผจก./หัวหน้าส่วนงาน <span class="text-xs text-gray-500 font-normal">(Lv.6-5)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-900">วิศวกร/อาวุโสเจ้าหน้าที่ <span class="text-xs text-gray-500 font-normal">(Lv.4-3)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                            <input type="radio" name="job_level" value="Sup/ปฏิบัติการ (Lv.2-1)" class="w-4 h-4 text-black focus:ring-black border-gray-800 shrink-0 @error('job_level') ring-2 ring-red-500 border-red-500 @enderror" {{ old('job_level') == 'Sup/ปฏิบัติการ (Lv.2-1)' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-900">Sup/ปฏิบัติการ <span class="text-xs text-gray-500 font-normal">(Lv.2-1)</span></span>
                        </label>
                    </div>
                </div>

                <!-- ลักษณะการว่าจ้าง -->
                <div class="flex flex-col sm:flex-row items-start gap-2 mb-6 mt-6">
                    <span class="mr-4 whitespace-nowrap pt-0.5 shrink-0 font-medium">ลักษณะการว่าจ้าง <span class="text-red-500">*</span></span>
                    <div class="flex-grow min-w-0 flex flex-col space-y-3 w-full" 
                         x-data="{ hire_type: '{{ old('hire_type', '') }}' }"
                         x-effect="
                             const isTemp = (hire_type === 'จ้างชั่วคราว');
                             ['hire_temp_start', 'hire_temp_end'].forEach(name => {
                                 const input = $el.querySelector(`input[name='${name}']`);
                                 if (input && input._flatpickr) {
                                     const fp = input._flatpickr;
                                     fp.input.disabled = !isTemp;
                                     if (fp.altInput) {
                                         fp.altInput.disabled = !isTemp;
                                         if (!isTemp) {
                                             fp.altInput.setAttribute('disabled', 'disabled');
                                             fp.altInput.classList.add('opacity-30', 'cursor-not-allowed');
                                             fp.altInput.classList.remove('opacity-100', 'cursor-pointer');
                                         } else {
                                             fp.altInput.removeAttribute('disabled');
                                             fp.altInput.removeAttribute('readonly');
                                             fp.altInput.classList.remove('opacity-30', 'cursor-not-allowed');
                                             fp.altInput.classList.add('opacity-100', 'cursor-pointer');
                                         }
                                     }
                                 }
                             });
                         ">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="hire_type" value="จ้างเพิ่มเติม" x-model="hire_type" class="mr-3 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                            <span>จ้างเพิ่มเติม</span>
                        </label>
                        <div class="flex items-end gap-2 min-w-0 w-full">
                            <label class="flex items-center cursor-pointer shrink-0">
                                <input type="radio" name="hire_type" value="จ้างทดแทน" x-model="hire_type" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                                <span class="whitespace-nowrap">จ้างทดแทนคนเก่า นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_replacement_name" list="employee_names" x-bind:disabled="hire_type !== 'จ้างทดแทน'" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed @error('hire_replacement_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_replacement_name') placeholder="{{ $message }}" @enderror value="{{ old('hire_replacement_name') }}">
                        </div>
                        <div class="flex items-end gap-2 min-w-0 w-full">
                            <label class="flex items-center cursor-pointer shrink-0">
                                <input type="radio" name="hire_type" value="โอนย้าย" x-model="hire_type" class="mr-2 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                                <span class="whitespace-nowrap">โอนย้าย / ปรับเปลี่ยนตำแหน่ง นาย / นาง / นางสาว</span>
                            </label>
                            <input type="text" name="hire_transfer_name" list="employee_names" x-bind:disabled="hire_type !== 'โอนย้าย'" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 disabled:opacity-30 disabled:cursor-not-allowed @error('hire_transfer_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_transfer_name') placeholder="{{ $message }}" @enderror value="{{ old('hire_transfer_name') }}">
                        </div>
                        <div class="flex items-end gap-2 min-w-0 w-full">
                            <label class="flex items-center cursor-pointer shrink-0 gap-2">
                                <input type="radio" name="hire_type" value="จ้างชั่วคราว" x-model="hire_type" class="mr-0 w-4 h-4 text-black focus:ring-black border-gray-800 @error('hire_type') ring-2 ring-red-500 border-red-500 @enderror">
                                <span class="whitespace-nowrap">จ้างชั่วคราว</span>
                                <span class="whitespace-nowrap shrink-0">ระยะเวลา จากวันที่</span>
                            </label>
                            <input type="text" name="hire_temp_start" class="datepicker-th w-28 shrink-0 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 opacity-30 cursor-not-allowed @error('hire_temp_start') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_temp_start') placeholder="{{ $message }}" @enderror value="{{ old('hire_temp_start') }}">
                            <span class="whitespace-nowrap shrink-0">ถึง</span>
                            <input type="text" name="hire_temp_end" class="datepicker-th flex-grow min-w-[80px] text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-2 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 opacity-30 cursor-not-allowed @error('hire_temp_end') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('hire_temp_end') placeholder="{{ $message }}" @enderror value="{{ old('hire_temp_end') }}">
                        </div>
                    </div>
                </div>

                <!-- เอกสารแนบ -->
                <div class="flex flex-col sm:flex-row sm:items-center mb-6 pl-0 sm:pl-8 gap-2">
                    <span class="mr-4 whitespace-nowrap shrink-0 font-medium">เอกสารแนบ <span class="text-red-500">*</span></span>
                    <div class="flex-grow min-w-0 flex flex-wrap gap-4 pr-0 sm:pr-8">
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
                <div class="border-[1.5px] border-black mb-6 flex flex-col md:flex-row">
                    <!-- Left Column -->
                    <div class="w-full md:w-1/2 border-b-[1.5px] md:border-b-0 md:border-r-[1.5px] border-black">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">คุณสมบัติที่ต้องการ</div>
                        <div class="p-3 space-y-4">
                            <div class="flex items-center flex-wrap gap-2">
                                <span class="w-16 whitespace-nowrap shrink-0">เพศ <span class="text-red-500">*</span></span>
                                <label class="flex items-center mr-3 cursor-pointer"><input type="radio" name="req_gender" value="ชาย" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black @error('req_gender') ring-2 ring-red-500 border-red-500 @enderror" {{ old('req_gender') == 'ชาย' ? 'checked' : '' }}> ชาย</label>
                                <label class="flex items-center mr-3 cursor-pointer"><input type="radio" name="req_gender" value="หญิง" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black @error('req_gender') ring-2 ring-red-500 border-red-500 @enderror" {{ old('req_gender') == 'หญิง' ? 'checked' : '' }}> หญิง</label>
                                <label class="flex items-center cursor-pointer"><input type="radio" name="req_gender" value="ชาย/หญิง" class="mr-1.5 w-4 h-4 text-black border-gray-800 focus:ring-black @error('req_gender') ring-2 ring-red-500 border-red-500 @enderror" {{ old('req_gender') == 'ชาย/หญิง' ? 'checked' : '' }}> ชาย/หญิง</label>
                            </div>
                            <div class="flex flex-wrap sm:flex-nowrap items-end gap-2 w-full">
                                <div class="flex items-end shrink-0">
                                    <span class="mr-2 whitespace-nowrap shrink-0">อายุ <span class="text-red-500">*</span> :</span>
                                    <input type="text" name="req_age" class="w-14 text-center border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_age') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_age') placeholder="{{ $message }}" @enderror value="{{ old('req_age') }}">
                                </div>
                                <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                                    <span class="mx-1 sm:mx-2 whitespace-nowrap shrink-0">วุฒิการศึกษา <span class="text-red-500">*</span> :</span>
                                    <input type="text" name="req_education" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_education') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_education') placeholder="{{ $message }}" @enderror value="{{ old('req_education') }}">
                                </div>
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">สาขาวิชา <span class="text-red-500">*</span> :</span>
                                <input type="text" name="req_major" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_major') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_major') placeholder="{{ $message }}" @enderror value="{{ old('req_major') }}">
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">ประสบการณ์ทำงาน <span class="text-red-500">*</span> :</span>
                                <input type="text" name="req_experience" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_experience') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_experience') placeholder="{{ $message }}" @enderror value="{{ old('req_experience') }}">
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">คุณสมบัติพิเศษ :</span>
                                <input type="text" name="req_special" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_special') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_special') placeholder="{{ $message }}" @enderror value="{{ old('req_special') }}">
                            </div>
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 whitespace-nowrap shrink-0">อื่นๆ :</span>
                                <input type="text" name="req_other" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('req_other') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('req_other') placeholder="{{ $message }}" @enderror value="{{ old('req_other') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="w-full md:w-1/2">
                        <div class="text-center font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2">หน้าที่รับผิดชอบโดยสังเขป</div>
                        <div class="p-3 space-y-[1.125rem]">
                            @for($i=1; $i<=6; $i++)
                            <div class="flex items-end w-full min-w-0">
                                <span class="mr-2 shrink-0">{{ $i }}</span>
                                <input type="text" name="res_{{ $i }}" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('res_'.$i) border-red-500 border-b-2 placeholder-red-400 @enderror" @error('res_'.$i) placeholder="{{ $message }}" @enderror value="{{ old('res_'.$i) }}">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="space-y-6 mb-10 mt-8 min-w-0">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ <span class="text-red-500">*</span></span>
                            <input type="text" name="requester_name" id="requester_name_input" list="employee_names" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('requester_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('requester_name') placeholder="{{ $message }}" @enderror value="{{ old('requester_name') }}">
                            <span class="ml-2 whitespace-nowrap shrink-0">ผู้ร้องขอ</span>
                        </div>
                        <div class="flex items-end w-full sm:w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="requester_date" id="requester_date_input" class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('requester_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('requester_date') placeholder="{{ $message }}" @enderror value="{{ old('requester_date') }}">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ <span class="text-red-500">*</span></span>
                            <input type="text" name="manager_name" id="manager_name_input" list="employee_names" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('manager_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('manager_name') placeholder="{{ $message }}" @enderror value="{{ old('manager_name') }}">
                            <span class="ml-2 whitespace-nowrap shrink-0">ผู้จัดการแผนก/ฝ่าย</span>
                        </div>
                        <div class="flex items-end w-full sm:w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="manager_date" id="manager_date_input" class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('manager_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('manager_date') placeholder="{{ $message }}" @enderror value="{{ old('manager_date') }}">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-4 min-w-0">
                        <div class="flex items-end flex-grow min-w-0 w-full sm:w-auto">
                            <span class="mr-2 whitespace-nowrap shrink-0">ลงชื่อ <span class="text-red-500">*</span></span>
                            <input type="text" name="vp_name" id="vp_name_input" list="employee_names" class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('vp_name') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('vp_name') placeholder="{{ $message }}" @enderror value="{{ old('vp_name') }}">
                            <span class="ml-2 whitespace-nowrap shrink-0">ประธานสายงาน</span>
                        </div>
                        <div class="flex items-end w-full sm:w-48 shrink-0">
                            <span class="mr-2 whitespace-nowrap shrink-0">วันที่</span>
                            <input type="text" name="vp_date" id="vp_date_input" class="datepicker-th flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none text-center px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0 @error('vp_date') border-red-500 border-b-2 placeholder-red-400 @enderror" @error('vp_date') placeholder="{{ $message }}" @enderror value="{{ old('vp_date') }}">
                        </div>
                    </div>
                </div>

                <!-- Approvals Table -->
                <div class="border-[1.5px] border-black mb-4 flex flex-col md:flex-row">
                    <!-- Left Column: HR -->
                    <div class="w-full md:w-1/2 flex flex-col border-b-[1.5px] md:border-b-0 md:border-r-[1.5px] border-black">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นฝ่ายบุคคล</div>
                        <div class="p-4 flex flex-col flex-grow min-w-0 relative pb-20">
                            <div class="w-full flex items-start">
                                <span class="mr-2 mt-2 whitespace-nowrap shrink-0">ความเห็น :</span>
                                <textarea disabled class="flex-grow min-w-0 bg-transparent border-none resize-none focus:ring-0 px-2 py-2 h-16"></textarea>
                            </div>
                            <div class="w-full px-4 sm:px-8 absolute bottom-4 left-0">
                                <div class="text-center w-full mb-2 truncate text-gray-500">(...................................................)</div>
                                <div class="text-center w-full">ผจก.แผนก/ฝ่ายทรัพยากรบุคคล</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: CEO -->
                    <div class="w-full md:w-1/2 flex flex-col">
                        <div class="font-bold bg-[#e5e7eb] border-b-[1.5px] border-black py-2 text-center w-full">ความเห็นประธานเจ้าหน้าที่บริหาร</div>
                        <div class="p-4 flex flex-col flex-grow min-w-0 relative pb-20">
                            <div class="flex justify-center space-x-4 sm:space-x-12 w-full mt-4 flex-wrap gap-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" disabled class="mr-2 w-4 h-4 text-black border-black"> 
                                    <span class="whitespace-nowrap">อนุมัติตามคำขอ</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" disabled class="mr-2 w-4 h-4 text-black border-black"> 
                                    <span class="whitespace-nowrap">ไม่อนุมัติตามคำขอ</span>
                                </label>
                            </div>
                            <div class="w-full px-4 sm:px-8 absolute bottom-4 left-0">
                                <div class="text-center w-full mb-2 truncate text-gray-500">(...................................................)</div>
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
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-2 w-full">
                        <div class="flex items-end w-full sm:w-auto min-w-0">
                            <span class="mr-2 whitespace-nowrap font-normal shrink-0">ได้รับพนักงาน (รหัสพนักงาน)</span>
                            <input type="text" disabled class="w-16 sm:w-20 flex-shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                        <div class="flex items-end flex-grow w-full sm:w-auto min-w-0">
                            <span class="mx-0 sm:mx-2 whitespace-nowrap font-normal shrink-0">(ชื่อ-สกุล)</span>
                            <input type="text" disabled class="flex-grow min-w-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
                        <div class="flex items-end w-full sm:w-auto min-w-0">
                            <span class="mx-0 sm:mx-2 whitespace-nowrap font-normal shrink-0">เข้าทำงานในวันที่</span>
                            <input type="text" disabled class="w-24 flex-shrink-0 border-b border-dotted border-gray-800 bg-transparent focus:outline-none px-1 py-0 border-t-0 border-l-0 border-r-0 ring-0 focus:ring-0">
                        </div>
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
                parseDate: function(dateStr, formatStr) {
                    if (typeof dateStr === 'string' && dateStr.includes('/')) {
                        const parts = dateStr.split('/');
                        if (parts.length === 3) {
                            let day = parseInt(parts[0], 10);
                            let month = parseInt(parts[1], 10) - 1;
                            let year = parseInt(parts[2], 10);
                            if (year > 2400) {
                                year -= 543;
                            }
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
                        if (year < 2400) {
                            year += 543;
                        }
                        return day + '/' + month + '/' + year;
                    }
                    return flatpickr.formatDate(date, formatStr, locale);
                },
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.altInput) {
                        instance.altInput.className = instance.input.className;
                        instance.altInput.classList.remove('datepicker-th');
                        instance.altInput.classList.add('flatpickr-input');

                        instance.altInput.addEventListener('click', function(e) {
                            const isTempInput = (instance.input.name === 'hire_temp_start' || instance.input.name === 'hire_temp_end');
                            if (isTempInput) {
                                const radio = document.querySelector('input[name="hire_type"][value="จ้างชั่วคราว"]');
                                if (radio && !radio.checked) {
                                    radio.click();
                                }
                                setTimeout(function() {
                                    if (instance.altInput) {
                                        instance.altInput.disabled = false;
                                        instance.altInput.removeAttribute('disabled');
                                        instance.altInput.removeAttribute('readonly');
                                    }
                                    instance.open();
                                }, 50);
                            } else {
                                instance.open();
                            }
                        });
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

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถบันทึกข้อมูลได้',
                    html: '<div class="text-left text-sm text-gray-700 dark:text-gray-200 mb-2 font-medium">กรุณาตรวจสอบข้อมูลที่ต้องระบุดังนี้:</div><ul class="text-left text-sm text-red-600 space-y-1 list-disc list-inside max-h-60 overflow-y-auto pl-2">{!! implode("", $errors->all("<li>:message</li>")) !!}</ul>',
                    confirmButtonColor: '#000000',
                    confirmButtonText: 'รับทราบและแก้ไข',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl'
                    }
                });
            @endif

            function updateSignatureDate(nameInputId, dateInputId) {
                const nameInput = document.getElementById(nameInputId);
                const dateInput = document.getElementById(dateInputId);
                if (!nameInput || !dateInput) return;

                const setDateIfEmpty = () => {
                    if (nameInput.value.trim() !== '') {
                        if (dateInput._flatpickr) {
                            if (!dateInput._flatpickr.selectedDates.length) {
                                dateInput._flatpickr.setDate(new Date(), true);
                            }
                        } else if (!dateInput.value) {
                            const today = new Date();
                            const y = today.getFullYear();
                            const m = String(today.getMonth() + 1).padStart(2, '0');
                            const d = String(today.getDate()).padStart(2, '0');
                            dateInput.value = `${y}-${m}-${d}`;
                        }
                    }
                };

                nameInput.addEventListener('change', setDateIfEmpty);
                nameInput.addEventListener('input', setDateIfEmpty);
                nameInput.addEventListener('blur', setDateIfEmpty);

                // Initial check if reloaded with value
                setDateIfEmpty();
            }

            updateSignatureDate('requester_name_input', 'requester_date_input');
            updateSignatureDate('manager_name_input', 'manager_date_input');
            updateSignatureDate('vp_name_input', 'vp_date_input');

            // Auto open datalist dropdown on click
            ['department_input', 'section_input', 'requester_name_input', 'manager_name_input', 'vp_name_input'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('click', function() {
                        try {
                            if (typeof this.showPicker === 'function') {
                                this.showPicker();
                            }
                        } catch (e) {}
                    });
                }
            });

            // Form Submit Confirmation Alert
            const manpowerForm = document.getElementById('manpowerRequestForm') || document.querySelector('form[action*="manpower-request"]');
            if (manpowerForm) {
                let isFormSubmitting = false;

                manpowerForm.addEventListener('submit', function(e) {
                    if (isFormSubmitting) return;
                    e.preventDefault();

                    Swal.fire({
                        title: 'ยืนยันการบันทึกข้อมูลแบบฟอร์ม?',
                        html: '<div class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">คุณต้องการบันทึกและส่ง <strong>ใบขออนุมัติกำลังคน (QF-HR-13)</strong> ใช่หรือไม่?</div>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#000000',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1.5"></i> ใช่, บันทึกข้อมูลแบบฟอร์ม',
                        cancelButtonText: 'ยกเลิก',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-2xl shadow-xl',
                            confirmButton: 'px-5 py-2.5 rounded-xl font-medium shadow',
                            cancelButton: 'px-5 py-2.5 rounded-xl font-medium'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            isFormSubmitting = true;
                            Swal.fire({
                                title: 'กำลังบันทึกข้อมูล...',
                                text: 'กรุณารอสักครู่ ระบบกำลังประมวลผลคำขอ',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            manpowerForm.submit();
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
