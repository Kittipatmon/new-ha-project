@extends('layouts.sidebar')
@section('title', 'แก้ไขข้อมูลพนักงาน : ' . $user->employee_code)

@section('content')
<div class="container mx-auto px-2 py-4">

    {{-- Main Form Card --}}
    <div class="card shadow-xl border border-base-200 dark:border-base-700 dark:bg-gray-800 rounded-2xl overflow-hidden">
        <div class="card-body p-6 md:p-8">
            
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-2">
                @csrf
                @method('PUT')

                {{-- Error Alert --}}
                @if ($errors->any())
                <div role="alert" class="alert alert-error text-white shadow-sm rounded-lg mb-6">
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
                    <div class="flex items-center gap-3 pb-3 border-b border-base-200 dark:border-base-700">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary shadow-sm">
                            <i class="fa-regular fa-id-card text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">ข้อมูลส่วนตัว</h3>
                            <p class="text-xs text-base-content/60">ข้อมูลพื้นฐานสำหรับระบุตัวตนพนักงาน</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        {{-- รหัสพนักงาน --}}
                        <div class="md:col-span-4 lg:col-span-3 form-control">
                            <label class="label" for="employee_code">
                                <span class="label-text font-medium text-base-content/80">รหัสพนักงาน <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="employee_code" name="employee_code"
                                value="{{ old('employee_code', $user->employee_code) }}"
                                class="input input-bordered w-full focus:input-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                        </div>

                        {{-- วันที่เริ่มงาน --}}
                        <div class="md:col-span-4 lg:col-span-3 form-control">
                            <label class="label" for="startwork_date">
                                <span class="label-text font-medium text-base-content/80">วันที่เริ่มงาน <span class="text-error">*</span></span>
                            </label>
                            <input type="date" id="startwork_date" name="startwork_date"
                                value="{{ old('startwork_date', isset($user->startwork_date) ? $user->startwork_date->format('Y-m-d') : '') }}"
                                class="input input-bordered w-full focus:input-primary bg-base-100 dark:bg-gray-800 dark:text-white [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        {{-- คำนำหน้า --}}
                        <div class="md:col-span-2 form-control">
                            <label class="label" for="prefix">
                                <span class="label-text font-medium text-base-content/80">คำนำหน้า <span class="text-error">*</span></span>
                            </label>
                            <select id="prefix" name="prefix" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                                <option value="นาย" {{ old('prefix', $user->prefix) == 'นาย' ? 'selected' : '' }}>นาย</option>
                                <option value="นางสาว" {{ old('prefix', $user->prefix) == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                <option value="นาง" {{ old('prefix', $user->prefix) == 'นาง' ? 'selected' : '' }}>นาง</option>
                            </select>
                        </div>

                        {{-- ชื่อ --}}
                        <div class="md:col-span-4 form-control">
                            <label class="label" for="first_name">
                                <span class="label-text font-medium text-base-content/80">ชื่อจริง <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                class="input input-bordered w-full focus:input-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                        </div>

                        {{-- นามสกุล --}}
                        <div class="md:col-span-4 form-control">
                            <label class="label" for="last_name">
                                <span class="label-text font-medium text-base-content/80">นามสกุล <span class="text-error">*</span></span>
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="input input-bordered w-full focus:input-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                        </div>

                        {{-- เพศ --}}
                        <div class="md:col-span-2 form-control">
                            <label class="label" for="sex">
                                <span class="label-text font-medium text-base-content/80">เพศ <span class="text-error">*</span></span>
                            </label>
                            <select id="sex" name="sex" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                                <option value="ชาย" {{ old('sex', $user->sex) == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                <option value="หญิง" {{ old('sex', $user->sex) == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 2: ตำแหน่งและสังกัด --}}
                <div class="space-y-5 pt-2">
                    <div class="flex items-center gap-3 pb-3 border-b border-base-200 dark:border-base-700">
                        <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary shadow-sm">
                            <i class="fa-solid fa-sitemap text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">ตำแหน่งและสังกัด</h3>
                            <p class="text-xs text-base-content/60">ข้อมูลโครงสร้างองค์กรและสถานที่ทำงาน</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {{-- ตำแหน่ง --}}
                        <div class="form-control">
                            <label class="label" for="position">
                                <span class="label-text font-medium text-base-content/80">ตำแหน่ง</span>
                            </label>
                            <input type="text" id="position" name="position" value="{{ old('position', $user->position) }}"
                                class="input input-bordered w-full focus:input-primary bg-base-100 dark:bg-gray-800 dark:text-white">
                        </div>
                        
                        {{-- แผนก --}}
                        <div class="form-control">
                            <label class="label" for="department_id">
                                <span class="label-text font-medium text-base-content/80">แผนก</span>
                            </label>
                            <select id="department_id" name="department_id" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white">
                                <option value="">-- เลือกแผนก --</option>
                                @foreach ($departments as $department)
                                <option value="{{ $department->department_id }}"
                                    {{ old('department_id', $user->department_id) == $department->department_id ? 'selected' : '' }}>
                                    {{ $department->department_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ฝ่าย --}}
                        <div class="form-control">
                            <label class="label" for="division_id">
                                <span class="label-text font-medium text-base-content/80">ฝ่าย</span>
                            </label>
                            <select id="division_id" name="division_id" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white">
                                <option value="">-- เลือกฝ่าย --</option>
                                @foreach ($divisions as $division)
                                <option value="{{ $division->division_id }}"
                                    {{ old('division_id', $user->division_id) == $division->division_id ? 'selected' : '' }}>
                                    {{ $division->division_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- สายงาน --}}
                        <div class="form-control">
                            <label class="label" for="section_id">
                                <span class="label-text font-medium text-base-content/80">สายงาน</span>
                            </label>
                            <select id="section_id" name="section_id" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white">
                                <option value="">-- เลือกสายงาน --</option>
                                @foreach ($sections as $section)
                                <option value="{{ $section->section_id }}"
                                    {{ old('section_id', $user->section_id) == $section->section_id ? 'selected' : '' }}>
                                    {{ $section->section_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                         {{-- สถานที่ทำงาน --}}
                        <div class="form-control">
                            <label class="label" for="workplace">
                                <span class="label-text font-medium text-base-content/80">สถานที่ทำงาน</span>
                            </label>
                            <select name="workplace" id="workplace" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white @error('workplace') select-error @enderror">
                                <option value="">-- เลือกสถานที่ทำงาน --</option>
                                <option value="สนง.ใหญ่" @if(old('workplace', $user->workplace) == 'สนง.ใหญ่') selected @endif>สำนักงานใหญ่</option>
                                <option value="บางเลน" @if(old('workplace', $user->workplace) == 'บางเลน') selected @endif>โรงงานบางเลน</option>
                                <option value="ไทรใหญ่" @if(old('workplace', $user->workplace) == 'ไทรใหญ่') selected @endif>โรงงานไทรใหญ่</option>
                            </select>
                        </div>

                        {{-- ประเภทพนักงาน --}}
                        <div class="form-control">
                            <label class="label" for="employee_type">
                                <span class="label-text font-medium text-base-content/80">ประเภทพนักงาน</span>
                            </label>
                            <select name="employee_type" id="employee_type" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white @error('employee_type') select-error @enderror">
                                <option value="">-- เลือกประเภทพนักงาน --</option>
                                <option value="รายเดือน" @if(old('employee_type', $user->employee_type) == 'รายเดือน') selected @endif>รายเดือน</option>
                                <option value="รายวัน" @if(old('employee_type', $user->employee_type) == 'รายวัน') selected @endif>รายวัน</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 3: สิทธิ์และการเข้าใช้งาน --}}
                <div class="space-y-5 pt-2">
                    <div class="flex items-center gap-3 pb-3 border-b border-base-200 dark:border-base-700">
                        <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent shadow-sm">
                            <i class="fa-solid fa-shield-halved text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">สิทธิ์การใช้งานและสถานะ</h3>
                            <p class="text-xs text-base-content/60">กำหนดสิทธิ์การเข้าถึงระบบและสถานะภาพปัจจุบัน</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- ระดับ User --}}
                        <div class="form-control">
                            <label class="label" for="level_user">
                                <span class="label-text font-medium text-base-content/80">ระดับพนักงาน <span class="text-error">*</span></span>
                            </label>
                            <select id="level_user" name="level_user" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                                @foreach ($userTypes as $userType)
                                <option value="{{ $userType->type_name }}"
                                    {{ old('level_user', $user->level_user) == $userType->type_name ? 'selected' : '' }}>
                                    {{ $userType->type_name }} ({{ $userType->description }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- สถานะ HR --}}
                        <div class="form-control">
                            <label class="label" for="hr_status">
                                <span class="label-text font-medium text-base-content/80">สถานะ HR <span class="text-error">*</span></span>
                            </label>
                            @php $hrStatusOptions = \App\Models\User::getHrStatusOptions(); @endphp
                            <select id="hr_status" name="hr_status" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                                @foreach($hrStatusOptions as $value => $meta)
                                <option value="{{ $value }}" @if(old('hr_status', $user->hr_status) == $value) selected @endif>
                                    {{ $meta['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- สถานะพนักงาน --}}
                        <div class="form-control">
                            <label class="label" for="status">
                                <span class="label-text font-medium text-base-content/80">สถานะการทำงาน <span class="text-error">*</span></span>
                            </label>
                            @php $statusOptions = \App\Models\User::getStatusOptions(); @endphp
                            <select id="status" name="status" class="select select-bordered w-full focus:select-primary bg-base-100 dark:bg-gray-800 dark:text-white" required>
                                @foreach($statusOptions as $value => $meta)
                                <option value="{{ $value }}" @if(old('status', $user->status) == $value) selected @endif>
                                    {{ $meta['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- End Work Section (Hidden by default) --}}
                    {{-- Note: ปรับสีแดงให้อ่อนลงใน Dark mode เพื่อไม่ให้แสบตา --}}
                    <div id="endwork_fields_wrapper" 
                         class="bg-red-50 border border-red-100 dark:bg-red-900/20 dark:border-red-900/50 p-6 rounded-xl mt-4 transition-all duration-300" 
                         style="display:none;">
                        <h4 class="text-red-600 dark:text-red-400 font-semibold mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-user-xmark"></i> ข้อมูลการสิ้นสุดงาน
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="form-control">
                                <label class="label" for="endwork_date">
                                    <span class="label-text font-medium text-red-700 dark:text-red-300">วันที่สิ้นสุดการทำงาน <span class="text-error">*</span></span>
                                </label>
                                <input type="date" id="endwork_date" name="endwork_date"
                                    value="{{ old('endwork_date', isset($user->endwork_date) ? $user->endwork_date->format('Y-m-d') : '') }}"
                                    class="input input-bordered w-full focus:input-error border-red-200 dark:border-red-800 bg-white dark:bg-gray-800 dark:text-white @error('endwork_date') input-error @enderror [color-scheme:light] dark:[color-scheme:dark]">
                                @error('endwork_date')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-3 form-control">
                                <label class="label" for="endwork_comment">
                                    <span class="label-text font-medium text-red-700 dark:text-red-300">เหตุผลกรณีไม่ใช้งาน <span class="text-error">*</span></span>
                                </label>
                                <textarea id="endwork_comment" name="endwork_comment" rows="2"
                                    class="textarea textarea-bordered w-full focus:textarea-error border-red-200 dark:border-red-800 bg-white dark:bg-gray-800 dark:text-white @error('endwork_comment') textarea-error @enderror"
                                    placeholder="ระบุเหตุผล...">{{ old('endwork_comment', $user->endwork_comment ?? '') }}</textarea>
                                @error('endwork_comment')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-base-200 dark:border-base-700">
                    <a href="{{ route('users.index') }}" class="btn btn-ghost text-base-content/70 hover:bg-base-200 dark:hover:bg-base-700">
                        ยกเลิก
                    </a>
                    <button type="submit" id="confirm-add-user" class="btn btn-success text-white px-8 shadow-lg shadow-success/20 hover:shadow-success/30">
                        <i class="fa-solid fa-save mr-2"></i> บันทึกการเปลี่ยนแปลง
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