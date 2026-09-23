@extends('layouts.app')
@section('title', 'แก้ไขข้อมูลพนักงาน : ' . $user->employee_code)

@section('content')
<div class="w-full pb-8">

    {{-- Main Form Card --}}
    <div class="card bg-white dark:bg-gray-800 shadow-sm border border-slate-200 dark:border-gray-700 rounded-xl sm:rounded-2xl overflow-hidden">
        <div class="card-body p-4 sm:p-8 lg:p-10 space-y-6 sm:space-y-8">
            
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-6 sm:space-y-8">
                @csrf
                @method('PUT')

                {{-- Error Alert --}}
                @if ($errors->any())
                <div role="alert" class="alert alert-error text-white shadow-sm rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <div>
                        <h3 class="font-bold">พบข้อผิดพลาด!</h3>
                        <ul class="text-sm list-disc list-inside mt-1 opacity-90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                {{-- Section 1: ข้อมูลส่วนตัว --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-regular fa-id-card text-base"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white text-base">ข้อมูลส่วนตัว</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ข้อมูลพื้นฐานสำหรับระบุตัวตนพนักงาน</p>
                        </div>
                    </div>

                    {{-- Row 1: รหัสพนักงาน (half) | วันที่เริ่มงาน (half) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="form-control">
                            <label class="label pb-1.5" for="employee_code">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">รหัสพนักงาน <span class="text-red-500">*</span></span>
                            </label>
                            <input type="text" id="employee_code" name="employee_code"
                                value="{{ old('employee_code', $user->employee_code) }}"
                                class="input input-bordered w-full h-11 rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="startwork_date">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">วันที่เริ่มงาน <span class="text-red-500">*</span></span>
                            </label>
                            <input type="date" id="startwork_date" name="startwork_date"
                                value="{{ old('startwork_date', isset($user->startwork_date) ? $user->startwork_date->format('Y-m-d') : '') }}"
                                class="input input-bordered w-full h-11 rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>

                    {{-- Row 2: คำนำหน้า (col-2) | ชื่อจริง (col-4) | นามสกุล (col-4) | เพศ (col-2) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 sm:gap-6">
                        <div class="sm:col-span-2 form-control">
                            <label class="label pb-1.5" for="prefix">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">คำนำหน้า <span class="text-red-500">*</span></span>
                            </label>
                            <select id="prefix" name="prefix" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                <option value="นาย" {{ old('prefix', $user->prefix) == 'นาย' ? 'selected' : '' }}>นาย</option>
                                <option value="นางสาว" {{ old('prefix', $user->prefix) == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                <option value="นาง" {{ old('prefix', $user->prefix) == 'นาง' ? 'selected' : '' }}>นาง</option>
                            </select>
                        </div>

                        <div class="sm:col-span-4 form-control">
                            <label class="label pb-1.5" for="first_name">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อจริง <span class="text-red-500">*</span></span>
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                class="input input-bordered w-full h-11 rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        </div>

                        <div class="sm:col-span-4 form-control">
                            <label class="label pb-1.5" for="last_name">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">นามสกุล <span class="text-red-500">*</span></span>
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="input input-bordered w-full h-11 rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        </div>

                        <div class="sm:col-span-2 form-control">
                            <label class="label pb-1.5" for="sex">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">เพศ <span class="text-red-500">*</span></span>
                            </label>
                            <select id="sex" name="sex" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                <option value="ชาย" {{ old('sex', $user->sex) == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                <option value="หญิง" {{ old('sex', $user->sex) == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 2: ตำแหน่งและสังกัด --}}
                <div class="space-y-5 pt-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-600 dark:text-slate-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-sitemap text-base"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white text-base">ตำแหน่งและสังกัด</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ข้อมูลโครงสร้างองค์กรและสถานที่ทำงาน</p>
                        </div>
                    </div>

                    {{-- Row 1: ตำแหน่ง | แผนก | ฝ่าย | สายงาน (4 cols) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div class="form-control">
                            <label class="label pb-1.5" for="position">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">ตำแหน่ง</span>
                            </label>
                            <input type="text" id="position" name="position" value="{{ old('position', $user->position) }}"
                                class="input input-bordered w-full h-11 rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        
                        <div class="form-control">
                            <label class="label pb-1.5" for="department_id">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">แผนก</span>
                            </label>
                            <select id="department_id" name="department_id" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">-- เลือกแผนก --</option>
                                @foreach ($departments as $department)
                                <option value="{{ $department->department_id }}"
                                    {{ old('department_id', $user->department_id) == $department->department_id ? 'selected' : '' }}>
                                    {{ $department->department_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="division_id">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">ฝ่าย</span>
                            </label>
                            <select id="division_id" name="division_id" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">-- เลือกฝ่าย --</option>
                                @foreach ($divisions as $division)
                                <option value="{{ $division->division_id }}"
                                    {{ old('division_id', $user->division_id) == $division->division_id ? 'selected' : '' }}>
                                    {{ $division->division_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="section_id">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">สายงาน</span>
                            </label>
                            <select id="section_id" name="section_id" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">-- เลือกสายงาน --</option>
                                @foreach ($sections as $section)
                                <option value="{{ $section->section_id }}"
                                    {{ old('section_id', $user->section_id) == $section->section_id ? 'selected' : '' }}>
                                    {{ $section->section_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: สถานที่ทำงาน (half) | ประเภทพนักงาน (half) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="form-control">
                            <label class="label pb-1.5" for="workplace">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">สถานที่ทำงาน</span>
                            </label>
                            <select name="workplace" id="workplace" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('workplace') select-error @enderror">
                                <option value="">-- เลือกสถานที่ทำงาน --</option>
                                <option value="สนง.ใหญ่" @if(old('workplace', $user->workplace) == 'สนง.ใหญ่') selected @endif>สำนักงานใหญ่</option>
                                <option value="บางเลน" @if(old('workplace', $user->workplace) == 'บางเลน') selected @endif>โรงงานบางเลน</option>
                                <option value="ไทรใหญ่" @if(old('workplace', $user->workplace) == 'ไทรใหญ่') selected @endif>โรงงานไทรใหญ่</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="employee_type">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">ประเภทพนักงาน</span>
                            </label>
                            <select name="employee_type" id="employee_type" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('employee_type') select-error @enderror">
                                <option value="">-- เลือกประเภทพนักงาน --</option>
                                <option value="รายเดือน" @if(old('employee_type', $user->employee_type) == 'รายเดือน') selected @endif>รายเดือน</option>
                                <option value="รายวัน" @if(old('employee_type', $user->employee_type) == 'รายวัน') selected @endif>รายวัน</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 3: สิทธิ์การใช้งานและสถานะ --}}
                <div class="space-y-5 pt-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shield-halved text-base"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white text-base">สิทธิ์การใช้งานและสถานะ</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">กำหนดสิทธิ์การเข้าถึงระบบและสถานะภาพปัจจุบัน</p>
                        </div>
                    </div>

                    {{-- Row 1: ระดับพนักงาน | สิทธิ์ในระบบ (Rule) | สถานะ HR | สถานะการทำงาน (4 cols) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div class="form-control">
                            <label class="label pb-1.5" for="level_user">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">ระดับพนักงาน <span class="text-red-500">*</span></span>
                            </label>
                            <select id="level_user" name="level_user" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                @foreach ($userTypes as $userType)
                                <option value="{{ $userType->type_name }}"
                                    {{ old('level_user', $user->level_user) == $userType->type_name ? 'selected' : '' }}>
                                    {{ $userType->type_name }} ({{ $userType->description }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="role">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">สิทธิ์ในระบบ (Rule) <span class="text-red-500">*</span></span>
                            </label>
                            @php 
                                $currentRole = old('role', $user->hr_role);
                                $canGrantAdmin = Auth::user()->canAssignAdminRole();
                                $isTargetAdmin = ($currentRole === 'admin');
                            @endphp
                            <select id="role" name="role" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                @if($canGrantAdmin || $isTargetAdmin)
                                    <option value="admin" {{ $currentRole == 'admin' ? 'selected' : '' }}>ADMIN (ผู้ดูแลระบบ - เข้าถึงได้ทั้งหมด)</option>
                                @else
                                    <option value="admin" disabled class="text-gray-400 bg-gray-100 dark:bg-gray-800">ADMIN (เฉพาะฝ่าย 16 Information Communication Technology)</option>
                                @endif
                                <option value="editor" {{ $currentRole == 'editor' ? 'selected' : '' }}>EDITOR (ผู้แก้ไข - ดู/เพิ่ม/แก้ไข)</option>
                                <option value="viewer" {{ $currentRole == 'viewer' ? 'selected' : '' }}>VIEWER (ผู้ดูข้อมูล - ดูอย่างเดียว)</option>
                            </select>
                            @if(!$canGrantAdmin)
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 inline shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                                    สิทธิ์ ADMIN สามารถกำหนดหรือเปลี่ยนได้โดยฝ่าย 16 Information Communication Technology เท่านั้น
                                </p>
                            @endif
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="hr_status">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">สถานะ HR <span class="text-red-500">*</span></span>
                            </label>
                            @php $hrStatusOptions = \App\Models\User::getHrStatusOptions(); @endphp
                            <select id="hr_status" name="hr_status" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                @foreach($hrStatusOptions as $value => $meta)
                                <option value="{{ $value }}" @if(old('hr_status', $user->hr_status) == $value) selected @endif>
                                    {{ $meta['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label pb-1.5" for="status">
                                <span class="label-text text-sm font-medium text-gray-700 dark:text-gray-300">สถานะการทำงาน <span class="text-red-500">*</span></span>
                            </label>
                            @php $statusOptions = \App\Models\User::getStatusOptions(); @endphp
                            <select id="status" name="status" class="select select-bordered w-full h-11 min-h-[44px] rounded-lg bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                @foreach($statusOptions as $value => $meta)
                                <option value="{{ $value }}" @if(old('status', $user->status) == $value) selected @endif>
                                    {{ $meta['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- End Work Section (Hidden by default, shown when inactive/resign) --}}
                    <div id="endwork_fields_wrapper" 
                         class="bg-red-50 border border-red-100 dark:bg-red-900/20 dark:border-red-900/50 p-6 rounded-xl mt-4 transition-all duration-300" 
                         style="display:none;">
                        <h4 class="text-kumwell-red dark:text-red-400 font-semibold mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-user-xmark"></i> ข้อมูลการสิ้นสุดงาน
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="form-control">
                                <label class="label" for="endwork_date">
                                    <span class="label-text font-medium text-red-700 dark:text-red-300">วันที่สิ้นสุดการทำงาน <span class="text-error">*</span></span>
                                </label>
                                <input type="date" id="endwork_date" name="endwork_date"
                                    value="{{ old('endwork_date', !empty($user->endwork_date) ? \Carbon\Carbon::parse($user->endwork_date)->format('Y-m-d') : '') }}"
                                    class="input input-bordered w-full h-11 rounded-lg focus:input-error border-red-200 dark:border-red-800 bg-white dark:bg-gray-800 dark:text-white @error('endwork_date') input-error @enderror [color-scheme:light] dark:[color-scheme:dark]">
                                @error('endwork_date')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-3 form-control">
                                <label class="label" for="endwork_comment">
                                    <span class="label-text font-medium text-red-700 dark:text-red-300">เหตุผลกรณีไม่ใช้งาน <span class="text-error">*</span></span>
                                </label>
                                <textarea id="endwork_comment" name="endwork_comment" rows="2"
                                    class="textarea textarea-bordered w-full rounded-lg focus:textarea-error border-red-200 dark:border-red-800 bg-white dark:bg-gray-800 dark:text-white @error('endwork_comment') textarea-error @enderror"
                                    placeholder="ระบุเหตุผล...">{{ old('endwork_comment', $user->endwork_comment ?? '') }}</textarea>
                                @error('endwork_comment')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 sm:gap-4 pt-6 mt-4 border-t border-slate-100 dark:border-gray-700">
                    <a href="{{ route('users.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        ยกเลิก
                    </a>
                    <button type="submit" id="confirm-add-user" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
                        <i class="fa-solid fa-save"></i>
                        <span>บันทึกการเปลี่ยนแปลง</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const STATUS_INACTIVE = '{{ \App\Models\User::STATUS_INACTIVE }}';
        const statusSelect = document.getElementById('status');
        const wrapper = document.getElementById('endwork_fields_wrapper');
        const endworkDate = document.getElementById('endwork_date');
        const textarea = document.getElementById('endwork_comment');

        if (!statusSelect || !wrapper || !endworkDate || !textarea) return;

        function syncEndworkComment() {
            const isInactive = String(statusSelect.value) === String(STATUS_INACTIVE);
            
            if(isInactive) {
                wrapper.style.display = 'block';
                // Small animation classes could be added here
            } else {
                wrapper.style.display = 'none';
            }
            
            endworkDate.required = isInactive;
            textarea.required = isInactive;
        }

        statusSelect.addEventListener('change', syncEndworkComment);
        syncEndworkComment();
    })();
</script>

@endsection