@extends('layouts.sidebar')
@section('title', 'แก้ไขข้อเสนอแนะและร้องเรียน')

@section('content')
    <div class="container mx-auto px-4 py-3">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <a href="{{ route('suggestion.list') }}" class="btn btn-sm btn-circle btn-ghost">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                แก้ไขรายการร้องเรียน
            </h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 rounded-r-xl">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                    <h3 class="font-bold text-red-800 dark:text-red-400">กรุณาตรวจสอบข้อมูลอีกครั้ง</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div
            class="bg-white dark:bg-[#1E2129] rounded-xl shadow-lg border border-slate-100 dark:border-white/5 overflow-hidden relative mb-10">
            <div class="absolute top-0 w-full h-1 bg-yellow-500"></div>

            <form action="{{ route('suggestion.update', $suggestion->id) }}" method="POST" enctype="multipart/form-data"
                class="p-6 sm:p-8">
                @csrf
                @method('PUT')

                <!-- Section 1: กาารร้องเรียน -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                        <i class="fa-solid fa-list-check text-yellow-500"></i> ประเภทการร้องเรียน
                    </h3>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="complaint_type" value="self"
                                class="w-5 h-5 text-yellow-500 border-gray-300 dark:border-gray-600 focus:ring-yellow-500 bg-white dark:bg-gray-800"
                                {{ $suggestion->complaint_type == 'self' ? 'checked' : '' }}>
                            <span
                                class="text-slate-700 dark:text-slate-300 group-hover:text-yellow-600 transition-colors">ร้องเรียนด้วยตนเอง</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="complaint_type" value="other"
                                class="w-5 h-5 text-yellow-500 border-gray-300 dark:border-gray-600 focus:ring-yellow-500 bg-white dark:bg-gray-800"
                                {{ $suggestion->complaint_type == 'other' ? 'checked' : '' }}>
                            <span
                                class="text-slate-700 dark:text-slate-300 group-hover:text-yellow-600 transition-colors">ร้องเรียนแทนผู้อื่น</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="complaint_type" value="phone"
                                class="w-5 h-5 text-yellow-500 border-gray-300 dark:border-gray-600 focus:ring-yellow-500 bg-white dark:bg-gray-800"
                                {{ $suggestion->complaint_type == 'phone' ? 'checked' : '' }}>
                            <span
                                class="text-slate-700 dark:text-slate-300 group-hover:text-yellow-600 transition-colors">ร้องเรียนทางโทรศัพท์</span>
                        </label>
                    </div>
                </div>

                <!-- Section 2: ข้อมูลเรื่อง -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="topic"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">เรื่อง<span
                                class="text-red-500">*</span></label>
                        <input type="text" id="topic" name="topic" value="{{ old('topic', $suggestion->topic) }}" required
                            class="input input-bordered w-full dark:text-white dark:bg-gray-800">
                    </div>
                    <div>
                        <label for="to_person"
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">เรียน<span
                                class="text-red-500">*</span></label>
                        <input type="text" id="to_person" name="to_person"
                            value="{{ old('to_person', $suggestion->to_person) }}" required
                            class="input input-bordered w-full dark:text-white dark:bg-gray-800">
                    </div>
                </div>

                <!-- Section 3: ข้อมูลผู้ร้องเรียน -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                        <i class="fa-solid fa-user text-yellow-500"></i> ข้อมูลผู้ร้องเรียน
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-6">
                            <label for="fullname"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ชื่อ-สกุล
                                ผู้ร้องเรียน<span class="text-red-500">*</span></label>
                            <select id="fullname" name="fullname" required
                                class="select select-bordered w-full dark:text-white dark:bg-gray-800">
                                <option value="">-- เลือกชื่อผู้ร้องเรียน --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->fullname }}" {{ $suggestion->fullname == $user->fullname ? 'selected' : '' }}>
                                        {{ $user->employee_code }} - {{ $user->fullname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="age"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">อายุ
                                (ปี)</label>
                            <input type="number" id="age" name="age" value="{{ old('age', $suggestion->age) }}" min="15"
                                max="100" class="input input-bordered w-full dark:text-white dark:bg-gray-800">
                        </div>
                        <div class="md:col-span-4">
                            <label for="phone"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">เบอร์โทรศัพท์<span
                                    class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $suggestion->phone) }}"
                                required class="input input-bordered w-full dark:text-white dark:bg-gray-800">
                        </div>
                    </div>
                </div>

                <!-- Section 4: ที่อยู่ -->
                <div class="mb-8">
                    <h3
                        class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                        <i class="fa-solid fa-map-location-dot text-yellow-500"></i> ที่อยู่
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">บ้านเลขที่</label>
                            <input type="text" name="address_no" value="{{ old('address_no', $suggestion->address_no) }}"
                                class="input input-bordered w-full dark:text-white dark:bg-gray-800 input-sm">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">หมู่ที่</label>
                            <input type="text" name="moo" value="{{ old('moo', $suggestion->moo) }}"
                                class="input input-bordered w-full dark:text-white dark:bg-gray-800 input-sm">
                        </div>
                        <div class="col-span-2 md:col-span-2">
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">ตรอก/ซอย</label>
                            <input type="text" name="soi" value="{{ old('soi', $suggestion->soi) }}"
                                class="input input-bordered w-full dark:text-white dark:bg-gray-800 input-sm">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">ถนน</label>
                            <input type="text" name="road" value="{{ old('road', $suggestion->road) }}"
                                class="input input-bordered w-full dark:text-white dark:bg-gray-800 input-sm">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">ตำบล</label>
                            <input type="text" name="subdistrict" value="{{ old('subdistrict', $suggestion->subdistrict) }}"
                                class="input input-bordered w-full dark:text-white dark:bg-gray-800 input-sm">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">อำเภอ</label>
                            <input type="text" name="district" value="{{ old('district', $suggestion->district) }}"
                                class="input input-bordered w-full dark:text-white dark:bg-gray-800 input-sm">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">จังหวัด</label>
                            <input type="text" name="province" value="{{ old('province', $suggestion->province) }}"
                                class="input input-bordered w-full dark:text-white dark:bg-gray-800 input-sm">
                        </div>
                    </div>
                </div>

                <!-- Section 5: รายละเอียด -->
                <div class="mb-8 space-y-6">
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">รายละเอียดของปัญหาที่ได้รับ<span
                                class="text-red-500">*</span></label>
                        <textarea name="details" rows="4" required
                            class="textarea textarea-bordered w-full dark:text-white dark:bg-gray-800 resize-none">{{ old('details', $suggestion->details) }}</textarea>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ความต้องการของผู้ร้องเรียน<span
                                class="text-red-500">*</span></label>
                        <textarea name="demands" rows="3" required
                            class="textarea textarea-bordered w-full dark:text-white dark:bg-gray-800 resize-none">{{ old('demands', $suggestion->demands) }}</textarea>
                    </div>
                </div>

                <!-- Section 6: หมายเหตุ -->
                <div class="mb-8 bg-gray-50 dark:bg-gray-800/50 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left"></i> ประวัติการร้องเรียน
                    </h3>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="history" value="never"
                                class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500 bg-white" {{ $suggestion->history == 'never' ? 'checked' : '' }}>
                            <span class="text-slate-700 dark:text-slate-300">ไม่เคยร้องเรียน</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="history" value="ever"
                                class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500 bg-white" {{ $suggestion->history == 'ever' ? 'checked' : '' }}>
                            <span class="text-slate-700 dark:text-slate-300">เคยร้องเรียนมาแล้ว</span>
                        </label>
                    </div>
                </div>

                <!-- Section 6: สถานะและการดำเนินการ (Admin Only) -->
                <div
                    class="mb-8 bg-blue-50 dark:bg-blue-900/10 p-6 rounded-xl border border-blue-200 dark:border-blue-900/30">
                    <h3 class="text-lg font-bold text-blue-800 dark:text-blue-400 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-bars-progress text-blue-500"></i> สถานะและการดำเนินงาน
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">สถานะปัจจุบัน<span
                                    class="text-red-500">*</span></label>
                            <select name="status" required
                                class="select select-bordered w-full dark:text-white dark:bg-gray-800">
                                <option value="รอรับเรื่องคำร้อง" {{ trim($suggestion->status) == 'รอรับเรื่องคำร้อง' ? 'selected' : '' }}>รอรับเรื่องคำร้อง</option>
                                <option value="รับเรื่องคำร้องแล้ว" {{ in_array(trim($suggestion->status), ['รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว']) ? 'selected' : '' }}>
                                    รับเรื่องคำร้องแล้ว
                                </option>
                                <option value="ตรวจสอบ" {{ trim($suggestion->status) == 'ตรวจสอบ' ? 'selected' : '' }}>ตรวจสอบ
                                </option>
                                <option value="ดำเนินการ" {{ trim($suggestion->status) == 'ดำเนินการ' ? 'selected' : '' }}>
                                    ดำเนินการ
                                </option>
                                <option value="เสร็จสิ้น" {{ trim($suggestion->status) == 'เสร็จสิ้น' ? 'selected' : '' }}>
                                    เสร็จสิ้น
                                </option>
                                <option value="ปิดเรื่อง" {{ trim($suggestion->status) == 'ปิดเรื่อง' ? 'selected' : '' }}>
                                    ปิดเรื่อง
                                </option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ผลการดำเนินงาน /
                            หมายเหตุความคืบหน้า</label>
                        <textarea name="progress_notes" rows="4"
                            class="textarea textarea-bordered w-full dark:text-white dark:bg-gray-800 resize-none"
                            placeholder="บันทึกความคืบหน้าหรือผลการดำเนินการเกี่ยวกับการร้องเรียนนี้...">{{ old('progress_notes', $suggestion->progress_notes) }}</textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('suggestion.list') }}" class="btn btn-ghost">ยกเลิก</a>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fa-solid fa-save mr-2"></i> บันทึกการแก้ไข
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection