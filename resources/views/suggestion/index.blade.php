@extends('layouts.suggestion.app')

@section('content')
    <div class="min-h-screen p-6 pt-10 pb-20 bg-gray-50 dark:bg-[#15171e] text-slate-800 dark:text-gray-200">
        <div class="max-w-8xl mx-auto px-4">
            <!-- Breadcrumbs -->
            <div class="flex items-center text-sm mb-4 space-x-2">
                <a href="{{ route('welcome') }}"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
                <span class="text-red-500 font-medium">รับเรื่องร้องเรียน</span>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">กล่องรับข้อเสนอแนะและร้องเรียน</h1>
                <p class="text-slate-500 dark:text-slate-400">ระบุรายละเอียดเรื่องร้องเรียนหรือข้อเสนอแนะ
                    เพื่อให้เราดำเนินการแก้ไขและพัฒนาต่อไป</p>
            </div>

            <!-- My Suggestions Button & Modal -->
            @if(isset($mySuggestions) && $mySuggestions->count() > 0)
                <div class="mb-8 flex justify-end">
                    <button type="button" onclick="document.getElementById('mySuggestionsModal').classList.remove('hidden')"
                        class="px-6 py-3 bg-white dark:bg-[#1E2129] border border-blue-200 dark:border-blue-900/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left"></i> สถานะคำร้องของคุณ
                        <span
                            class="bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-300 text-xs py-1 px-2 rounded-full ml-1">{{ $mySuggestions->count() }}</span>
                    </button>
                </div>

                <!-- My Suggestions Modal -->
                <div id="mySuggestionsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
                    role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80 transition-opacity"
                            aria-hidden="true" onclick="document.getElementById('mySuggestionsModal').classList.add('hidden')">
                        </div>

                        <!-- This element is to trick the browser into centering the modal contents. -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Modal panel -->
                        <div
                            class="inline-block align-bottom bg-white dark:bg-[#1E2129] rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100 dark:border-white/5 relative">
                            <div class="absolute top-0 w-full h-2 bg-gradient-to-r from-blue-500 to-blue-600 left-0"></div>

                            <div class="p-6 sm:p-8">
                                <div
                                    class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
                                    <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2"
                                        id="modal-title">
                                        <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> สถานะคำร้องของคุณ
                                    </h3>
                                </div>

                                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($mySuggestions as $sugg)
                                                        <div
                                                            class="flex flex-col sm:flex-row sm:items-center justify-between p-5 rounded-2xl border {{ $sugg->status == 'รอรับเรื่องคำร้อง' ? 'border-yellow-200 bg-yellow-50 dark:border-yellow-900/50 dark:bg-yellow-900/10' : ($sugg->status == 'รับเรื่อง' || $sugg->status == 'รับเรื่องคำร้องแล้ว' || $sugg->status == 'กำลังดำเนินการ' || $sugg->status == 'ดำเนินการ' ? 'border-blue-200 bg-blue-50 dark:border-blue-900/50 dark:bg-blue-900/10' : ($sugg->status == 'ยุติเรื่อง' || $sugg->status == 'เสร็จสิ้น' ? 'border-gray-200 bg-gray-50 dark:border-gray-700/50 dark:bg-gray-800/30' : 'border-green-200 bg-green-50 dark:border-green-900/50 dark:bg-green-900/10')) }} transition-colors">
                                                            <div class="flex flex-col mb-3 sm:mb-0">
                                                                <span
                                                                    class="font-bold text-slate-800 dark:text-gray-200 text-lg">{{ $sugg->topic }}</span>
                                                                <span class="text-sm text-slate-500 dark:text-slate-400 mt-1"><i
                                                                        class="fa-regular fa-calendar mr-1"></i> ส่งเมื่อ:
                                                                    {{ $sugg->created_at->format('d/m/Y H:i') }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-3">
                                                                <span
                                                                    class="px-4 py-2 text-sm font-bold rounded-xl whitespace-nowrap shadow-sm
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $sugg->status == 'รอรับเรื่องคำร้อง' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400' :
                                        ($sugg->status == 'รับเรื่อง' || $sugg->status == 'รับเรื่องคำร้องแล้ว' || $sugg->status == 'กำลังดำเนินการ' || $sugg->status == 'ดำเนินการ' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400' :
                                            ($sugg->status == 'ยุติเรื่อง' || $sugg->status == 'เสร็จสิ้น' ? 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300' :
                                                'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400')) }}">
                                                                    {{ $sugg->status }}
                                                                </span>
                                                                <a href="{{ route('suggestion.user.show', $sugg->id) }}"
                                                                    class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded-lg transition-colors"
                                                                    title="ดูข้อมูล">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div
                                class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-800">
                                <button type="button"
                                    onclick="document.getElementById('mySuggestionsModal').classList.add('hidden')"
                                    class="w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-6 py-3 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                    ปิด
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Display -->
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

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden relative">

                <div class="absolute top-0 w-full h-2 bg-gradient-to-r from-red-500 to-red-600"></div>

                <form action="{{ route('suggestion.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-8 sm:p-12">
                    @csrf

                    <!-- Section 1: กาารร้องเรียน -->
                    <div class="mb-10">
                        <h3
                            class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                            <i class="fa-solid fa-list-check text-red-500"></i> ประเภทการร้องเรียน
                        </h3>
                        <div class="flex flex-wrap gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="complaint_type" value="self"
                                    class="w-5 h-5 text-red-600 border-gray-300 dark:border-gray-600 focus:ring-red-500 dark:bg-gray-700"
                                    checked>
                                <span
                                    class="text-slate-700 dark:text-slate-300 group-hover:text-red-600 transition-colors">ร้องเรียนด้วยตนเอง</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="complaint_type" value="other"
                                    class="w-5 h-5 text-red-600 border-gray-300 dark:border-gray-600 focus:ring-red-500 dark:bg-gray-700">
                                <span
                                    class="text-slate-700 dark:text-slate-300 group-hover:text-red-600 transition-colors">ร้องเรียนแทนผู้อื่น</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="complaint_type" value="phone"
                                    class="w-5 h-5 text-red-600 border-gray-300 dark:border-gray-600 focus:ring-red-500 dark:bg-gray-700">
                                <span
                                    class="text-slate-700 dark:text-slate-300 group-hover:text-red-600 transition-colors">ร้องเรียนทางโทรศัพท์</span>
                            </label>
                        </div>
                    </div>

                    <!-- Section 2: ข้อมูลเรื่อง -->
                    <div class="grid grid-cols-1 gap-6 mb-10">
                        <div>
                            <label for="topic"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">เรื่อง<span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="topic" name="topic" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors"
                                placeholder="ระบุชื่อเรื่องที่ต้องการร้องเรียนหรือเสนอแนะ">
                        </div>
                        <div>
                            <label for="to_person"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">เรียน<span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="to_person" name="to_person" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors"
                                placeholder="เช่น ผู้บริหาร / ฝ่ายทรัพยากรบุคคล">
                        </div>
                    </div>

                    <!-- Section 3: ข้อมูลผู้ร้องเรียน -->
                    <div class="mb-10">
                        <h3
                            class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                            <i class="fa-solid fa-user text-red-500"></i> ข้อมูลผู้ร้องเรียน
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            <div class="md:col-span-6">
                                <label for="fullname"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ชื่อ-สกุล
                                    ผู้ร้องเรียน<span class="text-red-500">*</span></label>
                                <select id="fullname" name="fullname" required
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors select2-initialized">
                                    <option value="">-- เลือกชื่อผู้ร้องเรียน --</option>
                                    @isset($users)
                                        @foreach($users as $user)
                                            <option value="{{ $user->fullname }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>
                                                {{ $user->employee_code }} - {{ $user->fullname }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="age"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">อายุ
                                    (ปี)</label>
                                <input type="number" id="age" name="age" min="15" max="100"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors"
                                    placeholder="อายุ">
                            </div>
                            <div class="md:col-span-4">
                                <label for="phone"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">เบอร์โทรศัพท์<span
                                        class="text-red-500">*</span></label>
                                <input type="tel" id="phone" name="phone" required
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors"
                                    placeholder="08x-xxxxxxx">
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: ที่อยู่ -->
                    <div class="mb-10">
                        <h3
                            class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                            <i class="fa-solid fa-map-location-dot text-red-500"></i> ที่อยู่
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="col-span-2 md:col-span-1">
                                <label for="address_no"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">บ้านเลขที่</label>
                                <input type="text" id="address_no" name="address_no"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label for="moo"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">หมู่ที่</label>
                                <input type="text" id="moo" name="moo"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                            </div>
                            <div class="col-span-2 md:col-span-2">
                                <label for="soi"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ตรอก/ซอย</label>
                                <input type="text" id="soi" name="soi"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label for="road"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ถนน</label>
                                <input type="text" id="road" name="road"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label for="subdistrict"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ตำบล</label>
                                <input type="text" id="subdistrict" name="subdistrict"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label for="district"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">อำเภอ</label>
                                <input type="text" id="district" name="district"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label for="province"
                                    class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">จังหวัด</label>
                                <input type="text" id="province" name="province"
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: รายละเอียด -->
                    <div class="mb-10 space-y-6">
                        <div>
                            <label for="details"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">รายละเอียดของปัญหาที่ได้รับ<span
                                    class="text-red-500">*</span></label>
                            <textarea id="details" name="details" rows="4" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors resize-none"
                                placeholder="ระบุรายละเอียดของปัญหา สถานที่เกิดเหตุ เวลา หรืออื่นๆ ที่เกี่ยวข้อง"></textarea>
                        </div>
                        <div>
                            <label for="demands"
                                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ความต้องการของผู้ร้องเรียน<span
                                    class="text-red-500">*</span></label>
                            <textarea id="demands" name="demands" rows="3" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors resize-none"
                                placeholder="ระบุความต้องการหรือสิ่งที่อยากให้องค์กรดำเนินการแก้ไข"></textarea>
                        </div>
                    </div>

                    <!-- Section 6: เอกสารหลักฐานประกอบ -->
                    <div class="mb-10">
                        <h3
                            class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                            <i class="fa-solid fa-paperclip text-red-500"></i>
                            โดยข้าพเจ้าขอแนบเอกสารหลักฐานประกอบการร้องทุกข์
                        </h3>
                        <div class="space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="docs[]" value="id_card"
                                    class="w-5 h-5 text-red-600 border-gray-300 rounded dark:border-gray-600 focus:ring-red-500 dark:bg-gray-700">
                                <span
                                    class="text-slate-700 dark:text-slate-300 group-hover:text-red-600 transition-colors">สำเนาบัตรประจำตัวประชาชน</span>
                            </label>

                            <div class="flex flex-col sm:flex-row gap-3 sm:items-center w-full">
                                <label class="flex items-center gap-3 cursor-pointer group whitespace-nowrap">
                                    <input type="checkbox" name="docs[]" id="other_docs_checkbox" value="other"
                                        class="w-5 h-5 text-red-600 border-gray-300 rounded dark:border-gray-600 focus:ring-red-500 dark:bg-gray-700">
                                    <span
                                        class="text-slate-700 dark:text-slate-300 group-hover:text-red-600 transition-colors">เอกสารอื่นๆ
                                        ประกอบด้วย</span>
                                </label>
                                <input type="text" id="other_docs_detail" name="other_docs_detail"
                                    class="flex-1 w-full sm:w-auto px-4 py-2 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors"
                                    placeholder="ระบุเอกสารอื่นๆ (ถ้ามี)" disabled>
                            </div>

                            <div
                                class="mt-4 p-5 bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-300 dark:border-gray-700 rounded-2xl text-center">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">อัปโหลดไฟล์เอกสาร / รูปภาพประกอบ
                                    (ถ้ามี)</p>
                                <input type="file" name="attachments[]" multiple
                                    class="block w-full text-sm text-slate-500 dark:text-gray-400
                                                                                                                        file:mr-4 file:py-2.5 file:px-6
                                                                                                                        file:rounded-full file:border-0
                                                                                                                        file:text-sm file:font-semibold
                                                                                                                        file:bg-red-50 file:text-red-600 dark:file:bg-red-500/10 dark:file:text-red-400
                                                                                                                        hover:file:bg-red-100 dark:hover:file:bg-red-500/20 transition-colors cursor-pointer" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: หมายเหตุ -->
                    <div
                        class="mb-10 bg-red-50 dark:bg-red-900/10 p-6 rounded-2xl border border-red-100 dark:border-red-900/30">
                        <h3 class="text-lg font-bold text-red-800 dark:text-red-400 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i> หมายเหตุประวัติการร้องเรียน
                        </h3>
                        <div class="flex flex-wrap gap-6 mb-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="history" value="never"
                                    class="w-5 h-5 text-red-600 border-gray-300 dark:border-gray-600 focus:ring-red-500 dark:bg-gray-700"
                                    checked>
                                <span class="text-red-900 dark:text-red-300 font-medium">ไม่เคยร้องเรียน</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="history" value="ever"
                                    class="w-5 h-5 text-red-600 border-gray-300 dark:border-gray-600 focus:ring-red-500 dark:bg-gray-700">
                                <span class="text-red-900 dark:text-red-300 font-medium">เคยร้องเรียนมาแล้ว</span>
                            </label>
                        </div>

                        <div
                            class="flex gap-3 text-red-700 dark:text-red-400/80 text-sm mt-4 border-t border-red-200 dark:border-red-900/50 pt-4">
                            <i class="fa-solid fa-check-circle mt-0.5"></i>
                            <p>ข้าพเจ้าขอรับรองว่าเป็นความจริงทุกประการ หากปรากฏว่าไม่เป็นความจริง
                                ข้าพเจ้ายอมรับผิดและให้ดำเนินการได้ตามกฎหมาย</p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-center pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" onclick="history.back()"
                            class="px-8 py-3.5 rounded-xl border border-gray-300 dark:border-gray-600 text-slate-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-800">
                            ยกเลิก
                        </button>
                        <button type="submit"
                            class="px-8 py-3.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow-lg shadow-red-600/30 hover:shadow-red-600/50 transition-all hover:-translate-y-0.5 focus:ring-4 focus:ring-red-500/30">
                            <i class="fa-solid fa-paper-plane mr-2"></i> ส่งแบบฟอร์ม
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const otherCheckbox = document.getElementById('other_docs_checkbox');
            const otherInput = document.getElementById('other_docs_detail');

            // Toggle input validation and disable state based on checkbox
            otherCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    otherInput.disabled = false;
                    otherInput.focus();
                    otherInput.required = true;
                } else {
                    otherInput.disabled = true;
                    otherInput.value = '';
                    otherInput.required = false;
                }
            });

            // Initialize Select2 for fullname if jQuery is available
            if (typeof jQuery !== 'undefined') {
                $('#fullname').select2({
                    placeholder: '-- เลือกชื่อผู้ร้องเรียน --',
                    width: '100%',
                    theme: 'default',
                    dropdownCssClass: 'select2-dropdown-red',
                    selectionCssClass: 'select2-selection-red',
                    language: {
                        noResults: function () {
                            return 'ไม่พบข้อมูล';
                        }
                    }
                });
            }
        });
    </script>

    <style>
        /* Styling specifically for the fullname Select2 dropdown to be red */
        .select2-selection-red {
            height: 48px !important;
            /* py-3 equivalent */
            padding: 8px 16px !important;
            /* px-4 equivalent */
            background-color: #dc2626 !important;
            /* bg-red-600 */
            border-color: #dc2626 !important;
            border-radius: 0.75rem !important;
            /* rounded-xl */
            color: white !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s !important;
        }

        .select2-selection-red .select2-selection__rendered {
            color: white !important;
            line-height: normal !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            font-weight: 500 !important;
        }

        .select2-selection-red .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }

        .select2-selection-red .select2-selection__arrow b {
            border-color: white transparent transparent transparent !important;
        }

        /* Hover and Focus States */
        .select2-container--default .select2-selection--single.select2-selection-red:hover {
            background-color: #b91c1c !important;
            /* hover:bg-red-700 */
            border-color: #b91c1c !important;
        }

        /* Dark mode adjustments - keeping it red based on the image */
        .dark .select2-selection-red {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }

        /* Dropdown options styling */
        .select2-dropdown-red {
            border-radius: 0.5rem !important;
            border-color: #f3f4f6 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
        }

        .dark .select2-dropdown-red {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        .select2-dropdown-red .select2-results__option {
            padding: 10px 16px !important;
        }

        .select2-dropdown-red .select2-results__option--highlighted[aria-selected] {
            background-color: #dc2626 !important;
            /* bg-red-600 */
            color: white !important;
        }

        .dark .select2-dropdown-red .select2-results__option {
            color: #f3f4f6 !important;
        }

        .dark .select2-dropdown-red .select2-results__option[aria-selected="true"] {
            background-color: #374151 !important;
        }

        .dark .select2-dropdown-red .select2-results__option--highlighted[aria-selected] {
            background-color: #dc2626 !important;
            color: white !important;
        }

        /* Search Box in dropdown */
        .select2-dropdown-red .select2-search__field {
            border-radius: 0.375rem !important;
            border: 1px solid #d1d5db !important;
        }

        .dark .select2-dropdown-red .select2-search__field {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: white !important;
        }
    </style>
@endsection