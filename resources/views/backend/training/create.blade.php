@extends('layouts.sidebar')
@section('title', 'เพิ่มข้อมูลการฝึกอบรม')
@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">เพิ่มข้อมูลการฝึกอบรม</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('backend.training.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span
                                        class="label-text text-gray-700 dark:text-gray-300 font-medium">หัวข้อการฝึกอบรม<span
                                            class="text-red-500"> *</span></span>
                                </label>
                                <input type="text" name="branch"
                                    class="input input-bordered w-full dark:bg-gray-700 dark:text-white"
                                    value="{{ old('branch') }}" required>
                                @error('branch')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">หน่วยงาน <span
                                            class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="department"
                                    class="input input-bordered w-full dark:bg-gray-700 dark:text-white"
                                    value="{{ old('department') }}" required>
                                @error('department')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">จำนวนชั่วโมง <span
                                            class="text-red-500">*</span></span>
                                </label>
                                <input type="number" name="hours" min="1"
                                    class="input input-bordered w-full dark:bg-gray-700 dark:text-white"
                                    value="{{ old('hours', 30) }}" required>
                                @error('hours')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">รูปแบบการอบรม
                                        <span class="text-red-500">*</span></span>
                                </label>
                                <select name="format" class="select select-bordered w-full dark:bg-gray-700 dark:text-white"
                                    required>
                                    <option value="ปกติ" {{ old('format') == 'ปกติ' ? 'selected' : '' }}>ปกติ</option>
                                    <option value="ออนไลน์" {{ old('format') == 'ออนไลน์' ? 'selected' : '' }}>ออนไลน์
                                    </option>
                                </select>
                                @error('format')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">วันที่เริ่มอบรม
                                        <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="start_date" placeholder="เช่น 1 เมษายน 2567"
                                    class="input input-bordered w-full dark:bg-gray-700 dark:text-white"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span
                                        class="label-text text-gray-700 dark:text-gray-300 font-medium">วันที่สิ้นสุดการอบรม
                                        <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="end_date" placeholder="เช่น 5 เมษายน 2567"
                                    class="input input-bordered w-full dark:bg-gray-700 dark:text-white"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">สถานะ</span>
                                </label>
                                <select name="status" class="select select-bordered w-full dark:bg-gray-700 dark:text-white"
                                    required>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available
                                        (เปิดรับสมัคร)</option>
                                    <option value="full" {{ old('status') == 'full' ? 'selected' : '' }}>Full (เต็มแล้ว)
                                    </option>
                                </select>
                                @error('status')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">เอกสารประกอบ
                                        (PDF)</span>
                                </label>
                                <input type="file" name="document" accept=".pdf"
                                    class="file-input file-input-bordered w-full dark:bg-gray-700 dark:text-white">
                                @error('document')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">ลิงก์สื่อการสอน
                                        (กรณีไม่มีไฟล์เอกสาร)</span>
                                </label>
                                <input type="url" name="document_link" placeholder="เช่น https://drive.google.com/..."
                                    class="input input-bordered w-full dark:bg-gray-700 dark:text-white"
                                    value="{{ old('document_link') }}">
                                @error('document_link')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">รูปภาพประกอบ
                                        (Banner/Thumbnail)</span>
                                </label>
                                <input type="file" name="image" accept="image/*"
                                    class="file-input file-input-bordered w-full dark:bg-gray-700 dark:text-white">
                                <span class="text-xs text-gray-500 mt-1">แนะนำขนาด 1200x630px หรือสัดส่วน 16:9</span>
                                @error('image')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-control w-full">
                            <label class="label">
                                <span
                                    class="label-text text-gray-700 dark:text-gray-300 font-medium">รายละเอียดของหลักสูตร</span>
                            </label>
                            <textarea name="details"
                                class="textarea textarea-bordered w-full h-32 dark:bg-gray-700 dark:text-white">{{ old('details') }}</textarea>
                            @error('details')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('backend.training.index') }}" class="btn btn-ghost">ยกเลิก</a>
                        <button type="submit"
                            class="btn border-none bg-[#5c7295] hover:bg-[#4a5f7e] text-white">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection