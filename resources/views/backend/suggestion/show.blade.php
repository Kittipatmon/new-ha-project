@extends('layouts.sidebar')
@section('title', 'รายละเอียดข้อเสนอแนะและร้องเรียน')

@section('content')
    <div class="container mx-auto px-4 py-3">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <a href="{{ route('suggestion.list') }}" class="btn btn-sm btn-circle btn-ghost">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                รายละเอียดการร้องเรียน
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('suggestion.edit', $suggestion->id) }}" class="btn btn-warning text-white shadow-sm">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> แก้ไข
                </a>
            </div>
        </div>

        <!-- Main Content Container -->
        <div
            class="bg-white dark:bg-[#1E2129] rounded-xl shadow-lg border border-slate-100 dark:border-white/5 overflow-hidden relative mb-10">
            <div class="absolute top-0 w-full h-1 bg-info"></div>

            <div class="p-6 sm:p-8">

                <!-- Section 1: ข้อมูลเรื่อง -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-gray-50 dark:bg-gray-800/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"><i
                                class="fa-solid fa-heading mr-1"></i> เรื่อง</h4>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $suggestion->topic }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"><i
                                class="fa-solid fa-user-tie mr-1"></i> เรียน</h4>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $suggestion->to_person }}</p>
                    </div>
                </div>

                <!-- Header Info -->
                <div class="flex flex-wrap items-center gap-4 mb-8 pb-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-calendar mr-1"></i> {{ $suggestion->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($suggestion->complaint_type == 'self')
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="fa-solid fa-user-pen mr-1"></i> ร้องเรียนด้วยตนเอง
                            </span>
                        @elseif($suggestion->complaint_type == 'other')
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                <i class="fa-solid fa-users mr-1"></i> ร้องเรียนแทนผู้อื่น
                            </span>
                        @elseif($suggestion->complaint_type == 'phone')
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                <i class="fa-solid fa-phone mr-1"></i> ร้องเรียนทางโทรศัพท์
                            </span>
                        @else
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                {{ $suggestion->complaint_type }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if($suggestion->history == 'ever')
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                <i class="fa-solid fa-clock-rotate-left mr-1"></i> เคยร้องเรียนมาแล้ว
                            </span>
                        @else
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <i class="fa-solid fa-check-circle mr-1"></i> ไม่เคยร้องเรียน
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                    @if(trim($suggestion->status) == 'รอรับเรื่องคำร้อง')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800 whitespace-nowrap">
                            <i class="fa-solid fa-hourglass-start mr-1"></i> {{ $suggestion->status }}
                        </span>
                    @elseif(in_array(trim($suggestion->status), ['รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว']))
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800 whitespace-nowrap">
                            <i class="fa-solid fa-inbox mr-1"></i> {{ $suggestion->status }}
                        </span>
                    @elseif(trim($suggestion->status) == 'ตรวจสอบ')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800 whitespace-nowrap">
                            <i class="fa-solid fa-magnifying-glass mr-1"></i> {{ $suggestion->status }}
                        </span>
                    @elseif(trim($suggestion->status) == 'ดำเนินการ')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800 whitespace-nowrap">
                            <i class="fa-solid fa-spinner mr-1"></i> {{ $suggestion->status }}
                        </span>
                    @elseif(trim($suggestion->status) == 'เสร็จสิ้น')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 whitespace-nowrap">
                            <i class="fa-solid fa-check mr-1"></i> {{ $suggestion->status }}
                        </span>
                    @elseif(trim($suggestion->status) == 'ปิดเรื่อง')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 whitespace-nowrap">
                            <i class="fa-solid fa-lock mr-1"></i> {{ $suggestion->status }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Progress Notes -->
            @if($suggestion->progress_notes)
            <div class="mb-8 p-6 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/30">
                <h3 class="text-lg font-bold text-blue-800 dark:text-blue-400 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-bars-progress text-blue-500"></i> ผลการดำเนินงาน / หมายเหตุความคืบหน้า
                </h3>
                <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line text-base leading-relaxed">{!! nl2br(e($suggestion->progress_notes)) !!}</p>
            </div>
            @endif

                <!-- Personal Info & Address -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 pb-6 border-b border-gray-100 dark:border-gray-800">
                    <!-- Data: Person -->
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-user text-info"></i> ข้อมูลผู้ร้องเรียน
                        </h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-2">
                                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">ชื่อ-สกุล:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white col-span-2">{{ $suggestion->fullname }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">อายุ:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white col-span-2">{{ $suggestion->age ? $suggestion->age . ' ปี' : '-' }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">เบอร์โทรศัพท์:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white col-span-2">{{ $suggestion->phone ?? '-' }}</span>
                            </div>
                            @if($suggestion->user_id)
                                <div class="grid grid-cols-3 gap-2">
                                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">ผู้ใช้ระบบ:</span>
                                    <span class="text-sm text-info col-span-2">มีบัญชีผู้ใช้ (ID:
                                        {{ $suggestion->user_id }})</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Data: Address -->
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-info"></i> ที่อยู่
                        </h3>
                        <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                                @if(empty($suggestion->address_no) && empty($suggestion->moo) && empty($suggestion->soi) && empty($suggestion->road) && empty($suggestion->subdistrict) && empty($suggestion->district) && empty($suggestion->province))
                                    <span class="text-gray-400 italic">ไม่ได้ระบุที่อยู่</span>
                                @else
                                    {{ $suggestion->address_no ? 'บ้านเลขที่ ' . $suggestion->address_no : '' }}
                                    {{ $suggestion->moo ? 'หมู่ที่ ' . $suggestion->moo : '' }}
                                    {{ $suggestion->soi ? 'ตรอก/ซอย ' . $suggestion->soi : '' }}
                                    {{ $suggestion->road ? 'ถนน ' . $suggestion->road : '' }}<br>
                                    {{ $suggestion->subdistrict ? 'ตำบล' . $suggestion->subdistrict : '' }}
                                    {{ $suggestion->district ? 'อำเภอ' . $suggestion->district : '' }}
                                    {{ $suggestion->province ? 'จังหวัด' . $suggestion->province : '' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Details & Demands -->
                <div class="mb-8 pb-6 border-b border-gray-100 dark:border-gray-800 space-y-8">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-file-lines text-info"></i> รายละเอียดของปัญหาที่ได้รับ
                        </h3>
                        <div
                            class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line text-base leading-relaxed">
                                {!! nl2br(e($suggestion->details)) !!}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-bullseye text-info"></i> ความต้องการของผู้ร้องเรียน
                        </h3>
                        <div
                            class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line text-base leading-relaxed">
                                {!! nl2br(e($suggestion->demands)) !!}</p>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                @if((is_array($suggestion->docs) && count($suggestion->docs) > 0) || $suggestion->other_docs_detail || (is_array($suggestion->attachments) && count($suggestion->attachments) > 0))
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-paperclip text-info"></i> เอกสารหลักฐานประกอบ
                        </h3>

                        <div class="space-y-4">
                            <!-- Doc Types -->
                            @if(is_array($suggestion->docs) || $suggestion->other_docs_detail)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if(is_array($suggestion->docs) && in_array('id_card', $suggestion->docs))
                                        <span
                                            class="px-3 py-1.5 rounded-lg text-sm bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                            <i class="fa-regular fa-id-card mr-2 text-gray-400"></i> สำเนาบัตรประจำตัวประชาชน
                                        </span>
                                    @endif
                                    @if(is_array($suggestion->docs) && in_array('other', $suggestion->docs) && $suggestion->other_docs_detail)
                                        <span
                                            class="px-3 py-1.5 rounded-lg text-sm bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                            <i class="fa-solid fa-file-alt mr-2 text-gray-400"></i> {{ $suggestion->other_docs_detail }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Uploaded Files -->
                            @if(is_array($suggestion->attachments) && count($suggestion->attachments) > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($suggestion->attachments as $index => $attachment)
                                        <a href="{{ Storage::url($attachment) }}" target="_blank"
                                            class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-colors shrink-0">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">เอกสารแนบ
                                                    {{ $index + 1 }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">คลิกเพื่อดูไฟล์</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection