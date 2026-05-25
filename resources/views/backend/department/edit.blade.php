@extends('layouts.sidebar')
@section('title', 'แก้ไขข้อมูลแผนก (Edit Department)')
@section('content')
<div class="max-w-8xl rounded-xl shadow-xl p-6 border border-gray-300/60">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">แก้ไขข้อมูลแผนก</h1>
        <a href="{{ route('departments.index') }}" class="btn btn-primary">
            <i class="fa fa-arrow-left mr-1"></i>
            กลับ
        </a>
    </div>

    <form action="{{ route('departments.update', $department->department_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-control">
                <label class="label" for="department_name">
                    <span class="label-text">ชื่อ(ย่อ)</span>
                </label>
                <input type="text" id="department_name" name="department_name" class="input input-bordered w-full dark:bg-gray-800" value="{{ $department->department_name }}" required>
            </div>

            <div class="form-control">
                <label class="label" for="department_fullname">
                    <span class="label-text">ชื่อเต็ม</span>
                </label>
                <input type="text" id="department_fullname" name="department_fullname" class="input input-bordered w-full dark:bg-gray-800" value="{{ $department->department_fullname }}">
            </div>

            <div class="form-control">
                <label class="label" for="division_id">
                    <span class="label-text">ฝ่าย</span>
                </label>
                <select id="division_id" name="division_id" class="select select-bordered w-full dark:bg-gray-800" >
                    <option value="">เลือกฝ่าย</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->division_id }}" {{ $department->division_id == $division->division_id ? 'selected' : '' }}>
                            {{ $division->division_fullname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label class="label" for="department_status">
                    <span class="label-text">สถานะ</span>
                </label>
                <select id="department_status" name="department_status" class="select select-bordered w-full dark:bg-gray-800" required>
                    <option value="0" {{ $department->department_status == 0 ? 'selected' : '' }}>ใช้งาน</option>
                    <option value="1" {{ $department->department_status == 1 ? 'selected' : '' }}>ไม่ใช้งาน</option>
                </select>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn btn-success text-white">
                <i class="fa fa-save mr-1"></i>
                บันทึกการเปลี่ยนแปลง
            </button>
        </div>
    </form>
</div>
@endsection
