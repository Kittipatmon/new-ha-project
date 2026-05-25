@extends('layouts.sidebar')
@section('title', 'แก้ไขข้อมูลการฝึกอบรม')
@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">แก้ไขข้อมูลการฝึกอบรม</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('backend.training.update', $training->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">สาขา <span
                                            class="text-red-500">*</span></span>
                                </label>
                                <input type="text" name="branch"
                                    class="input input-bordered w-full dark:bg-gray-700 dark:text-white"
                                    value="{{ old('branch', $training->branch) }}" required>
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
                                    value="{{ old('department', $training->department) }}" required>
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
                                    value="{{ old('hours', $training->hours) }}" required>
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
                                    <option value="ปกติ" {{ old('format', $training->format) == 'ปกติ' ? 'selected' : '' }}>
                                        ปกติ</option>
                                    <option value="ออนไลน์" {{ old('format', $training->format) == 'ออนไลน์' ? 'selected' : '' }}>ออนไลน์</option>
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
                                    value="{{ old('start_date', $training->start_date) }}" required>
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
                                    value="{{ old('end_date', $training->end_date) }}" required>
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
                                    <option value="available" {{ old('status', $training->status) == 'available' ? 'selected' : '' }}>Available (เปิดรับสมัคร)</option>
                                    <option value="full" {{ old('status', $training->status) == 'full' ? 'selected' : '' }}>
                                        Full (เต็มแล้ว)</option>
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
                                @if($training->document)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $training->document) }}" target="_blank"
                                            class="text-blue-500 hover:underline text-sm"><i class="fa-solid fa-file-pdf"></i>
                                            เอกสารปัจจุบัน</a>
                                    </div>
                                @endif
                                <input type="file" name="document" accept=".pdf"
                                    class="file-input file-input-bordered w-full dark:bg-gray-700 dark:text-white">
                                <span class="text-xs text-gray-500 mt-1">หากไม่อัปโหลดใหม่ จะใช้เอกสารเดิม</span>
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
                                    value="{{ old('document_link', $training->document_link) }}">
                                @error('document_link')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text text-gray-700 dark:text-gray-300 font-medium">รูปภาพประกอบ
                                        (Banner/Thumbnail)</span>
                                </label>
                                @if($training->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('images/training/' . $training->image) }}" loading="lazy"
                                            class="w-32 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                                            alt="Current Image">
                                        <p class="text-xs text-gray-500 mt-1">รูปปัจจุบัน</p>
                                    </div>
                                @endif
                                <input type="file" name="image" accept="image/*"
                                    class="file-input file-input-bordered w-full dark:bg-gray-700 dark:text-white">
                                <span class="text-xs text-gray-500 mt-1">หากไม่อัปโหลดใหม่ จะใช้รูปเดิม</span>
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
                                class="textarea textarea-bordered w-full h-32 dark:bg-gray-700 dark:text-white">{{ old('details', $training->details) }}</textarea>
                            @error('details')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('backend.training.index') }}" class="btn btn-ghost">ยกเลิก</a>
                        <button type="submit"
                            class="btn border-none bg-[#5c7295] hover:bg-[#4a5f7e] text-white">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection