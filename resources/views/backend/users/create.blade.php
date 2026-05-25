@extends('layouts.sidebar')
@section('title', 'เพิ่มพนักงานใหม่')
@section('content')
<div class="max-w-8xl mx-auto p-6 rounded-lg shadow-md border border-gray-300/40">
    <form id="createUserForm" method="POST" action="{{ route('users.store') }}">
        @csrf

        @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="col-span-1 md:col-span-2 lg:col-span-4 pb-2 mb-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ข้อมูลส่วนตัว</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-3">
            <div>
                <label for="employee_code" class="block font-medium mb-1">รหัสพนักงาน <span
                        class="text-red-500">*</span></label>
                <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code') }}"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('employee_code') input-error @enderror"
                    required>
                @error('employee_code')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="startwork_date" class="block font-medium mb-1">วันที่เริ่มงาน <span
                        class="text-red-500">*</span></label>
                <input type="date" name="startwork_date" id="startwork_date" value="{{ old('startwork_date') }}"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('startwork_date') input-error @enderror">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div>
                <label for="prefix" class="block font-medium mb-1">คำนำหน้า <span class="text-red-500">*</span></label>
                <select name="prefix" id="prefix"
                    class="select select-bordered w-full dark:bg-gray-700 @error('prefix') select-error @enderror"
                    required>
                    <option value="">-- เลือก --</option>
                    <option value="นาย" @if(old('prefix')=='นาย' ) selected @endif>นาย</option>
                    <option value="นาง" @if(old('prefix')=='นาง' ) selected @endif>นาง</option>
                    <option value="นางสาว" @if(old('prefix')=='นางสาว' ) selected @endif>นางสาว</option>
                </select>
                @error('prefix')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="sex" class="block font-medium mb-1">เพศ <span class="text-red-500">*</span></label>
                <select name="sex" id="sex"
                    class="select select-bordered w-full dark:bg-gray-700 @error('sex') select-error @enderror"
                    required>
                    <option value="">-- เลือก --</option>
                    <option value="ชาย" @if(old('sex')=='ชาย' ) selected @endif>ชาย</option>
                    <option value="หญิง" @if(old('sex')=='หญิง' ) selected @endif>หญิง</option>
                </select>
                @error('sex')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="first_name" class="block font-medium mb-1">ชื่อจริง <span
                        class="text-red-500">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('first_name') input-error @enderror"
                    required>
                @error('first_name')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="last_name" class="block font-medium mb-1">นามสกุล <span
                        class="text-red-500">*</span></label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('last_name') input-error @enderror"
                    required>
                @error('last_name')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div
            class="col-span-1 md:col-span-2 lg:col-span-4 pb-2 mb-2 mt-4 border-b border-gray-100 dark:border-gray-700">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">ตำแหน่งและสังกัด</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="position" class="block font-medium mb-1">ตำแหน่ง</label>
                <input type="text" name="position" id="position" value="{{ old('position') }}"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('position') input-error @enderror">
                @error('position')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="department_id" class="block font-medium mb-1">แผนก</label>
                <select name="department_id" id="department_id"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('department_id') input-error @enderror">
                    <option value="">-- เลือกแผนก --</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->department_id }}" @if(old('department_id')==$department->
                        department_id) selected @endif>{{ $department->department_name }}
                        ({{ $department->department_fullname }})</option>
                    @endforeach
                </select>
                @error('department_id')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="division_id" class="block font-medium mb-1">ฝ่าย</label>
                <select name="division_id" id="division_id"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('division_id') input-error @enderror">
                    <option value="">-- เลือกฝ่าย --</option>
                    @foreach($divisions as $division)
                    <option value="{{ $division->division_id }}" @if(old('division_id')==$division->division_id)
                        selected @endif>{{ $division->division_name }} ({{ $division->division_fullname }}) </option>
                    @endforeach
                </select>
                @error('division_id')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="section_id" class="block font-medium mb-1">สายงาน</label>
                <select name="section_id" id="section_id"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('section_id') input-error @enderror">
                    <option value="">-- เลือกสายงาน --</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->section_id }}" @if(old('section_id')==$section->section_id) selected
                        @endif>{{ $section->section_code }}</option>
                    @endforeach
                </select>
                @error('section_id')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
<!-- workplace -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <div>
                <label for="workplace" class="block font-medium mb-1">สถานที่ทำงาน</label>
                <select name="workplace" id="workplace"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('workplace') input-error @enderror">
                    <option value="">-- เลือกสถานที่ทำงาน --</option>
                    <option value="สนง.ใหญ่" @if(old('workplace')=='สนง.ใหญ่' ) selected @endif>สนง.ใหญ่
                    </option>
                    <option value="บางเลน" @if(old('workplace')=='บางเลน' ) selected
                        @endif>โรงงานบางเลน</option>
                    <option value="ไทรใหญ่" @if(old('workplace')=='ไทรใหญ่' ) selected @endif>โรงงานไทรใหญ่
                    </option>
                </select>
                @error('workplace')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="employee_type" class="block font-medium mb-1">ประเภทพนักงาน</label>
                <select name="employee_type" id="employee_type"
                    class="input input-bordered w-full dark:bg-gray-700 px-3 py-2 @error('employee_type') input-error @enderror">
                    <option value="">-- เลือกประเภทพนักงาน --</option>
                    <option value="รายเดือน" @if(old('employee_type')=='รายเดือน' ) selected
                        @endif>รายเดือน</option>
                    <option value="รายวัน" @if(old('employee_type')=='รายวัน' ) selected
                        @endif>รายวัน</option>
                </select>
                @error('employee_type')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div
            class="col-span-1 md:col-span-2 lg:col-span-4 pb-2 mb-2 mt-4 border-b border-gray-100 dark:border-gray-700">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">สิทธิ์การใช้งาน</span>
        </div>
        @php
        $levelOptions = \App\Models\User::getLevelUserOptions();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control w-full">
                <label class="label"><span class="label-text font-medium">ระดับพนักงาน <span
                            class="text-red-500">*</span></span></label>
                <select name="level_user"
                    class="select select-bordered w-full dark:bg-gray-700 @error('level_user') select-error @enderror"
                    required>
                    <option value="">เลือกระดับ</option>
                    @foreach($levelOptions as $value => $meta)
                    <option value="{{ $value }}" @if(old('level_user')==$value) selected @endif>
                        {{ $meta['label'] }}
                    </option>
                    @endforeach
                </select>
                @error('level_user')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-medium">สถานะ HR <span
                            class="text-red-500">*</span></span></label>
                <select name="hr_status"
                    class="select select-bordered w-full dark:bg-gray-700 @error('hr_status') select-error @enderror"
                    required>
                    <option value="">เลือกสถานะ</option>
                    @php
                    $hrStatusOptions = \App\Models\User::getHrStatusOptions();
                    @endphp
                    @foreach($hrStatusOptions as $value => $meta)
                    <option value="{{ $value }}" @if(old('hr_status')==$value) selected @endif>{{ $meta['label'] }}
                    </option>
                    @endforeach
                </select>
                @error('hr_status')
                <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mt-6 text-center">
            <div class="modal-action mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('users.index') }}" class="btn">ยกเลิก</a>
                <button type="submit" id="confirm-add-user" class="btn btn-success text-white px-8">
                    <i class="fa-solid fa-save mr-2"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </form>
</div>
@endsection