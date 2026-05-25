@extends('layouts.sidebar')

@section('title', 'รายละเอียดผู้สมัคร')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('backend.recruitment.applications.index') }}"
                class="text-gray-400 hover:text-kumwell-red transition-colors">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold dark:text-white text-gray-800">ข้อมูลผู้สมัคร:
                {{ $application->applicant->full_name }}</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info (Left) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Data -->
                <div
                    class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <h3
                        class="text-lg font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-user text-kumwell-red"></i> ข้อมูลส่วนตัว
                    </h3>
                    <div class="grid grid-cols-2 gap-8 text-sm">
                        <div>
                            <p class="text-gray-400 mb-1">อีเมล</p>
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $application->applicant->email }}</p>
                                <button onclick="openModal('emailModal')" class="text-v-red hover:text-red-700 transition-colors" title="ส่งอีเมล">
                                    <i class="fa-solid fa-envelope"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-400 mb-1">เบอร์โทรศัพท์</p>
                            <p class="font-bold text-gray-700 dark:text-gray-200">{{ $application->applicant->phone }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 mb-1">สมัครตำแหน่ง</p>
                            <p class="font-bold text-v-red">{{ $application->jobPost->position_name ?? ($application->jobPost->jobPosition->position_name ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 mb-1">วันที่สมัคร</p>
                            <p class="font-bold text-gray-700 dark:text-gray-200">
                                {{ $application->applied_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Education History -->
                <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-kumwell-red"></i> ประวัติการศึกษา
                    </h3>
                    <div class="space-y-4">
                        @forelse($application->education as $edu)
                            <div class="p-4 bg-gray-50 dark:bg-kumwell-dark rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="col-span-2">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">สถาบัน</p>
                                        <p class="font-bold text-gray-700 dark:text-gray-200">{{ $edu->institution_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">ระดับ</p>
                                        <p class="text-sm dark:text-gray-300">{{ $edu->level }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">เกรดเฉลี่ย</p>
                                        <p class="font-bold text-kumwell-red">{{ $edu->gpa ?? '-' }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">คณะ/สาขา</p>
                                        <p class="text-sm dark:text-gray-300">{{ $edu->faculty }} {{ $edu->major }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">ปีที่จบ</p>
                                        <p class="text-sm dark:text-gray-300">{{ $edu->end_year ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic">ไม่มีข้อมูลประวัติการศึกษา</p>
                        @endforelse
                    </div>
                </div>

                <!-- Experience History -->
                <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-kumwell-red"></i> ประวัติการทำงาน
                    </h3>
                    <div class="space-y-4">
                        @forelse($application->experience as $exp)
                            <div class="p-4 bg-gray-50 dark:bg-kumwell-dark rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="col-span-2">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">บริษัท</p>
                                        <p class="font-bold text-gray-700 dark:text-gray-200">{{ $exp->company_name }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">ตำแหน่ง</p>
                                        <p class="font-bold text-kumwell-red">{{ $exp->position }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">ระยะเวลา</p>
                                        <p class="text-[10px] dark:text-gray-300">
                                            {{ $exp->start_date ? $exp->start_date->format('M Y') : '-' }} - 
                                            {{ $exp->end_date ? $exp->end_date->format('M Y') : 'ปัจจุบัน' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">เงินเดือน</p>
                                        <p class="text-sm dark:text-gray-300">{{ number_format($exp->salary, 0) }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">รายละเอียดงาน</p>
                                        <p class="text-[10px] dark:text-gray-400">{{ $exp->job_detail }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic">ไม่มีข้อมูลประวัติการทำงาน</p>
                        @endforelse
                    </div>
                </div>

                <!-- Documents -->
                <div
                    class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <h3
                        class="text-lg font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-kumwell-red"></i> เอกสารประกอบ
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($application->documents as $doc)
                            <a @if($doc->file_path) href="{{ asset('files/recruitment_applicant_documents/' . $doc->file_path) }}" target="_blank" @else href="javascript:void(0)" @endif
                                class="flex items-center justify-between p-4 bg-gray-50 dark:bg-kumwell-dark rounded-xl border border-gray-100 dark:border-gray-700 hover:border-kumwell-red transition-all group">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                                    <div class="text-[10px]">
                                        <p class="font-bold text-gray-700 dark:text-gray-200 uppercase">
                                            {{ $doc->document_type }}</p>
                                        <p class="text-gray-400 truncate max-w-[120px]">{{ $doc->file_name }}</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-download text-gray-300 group-hover:text-kumwell-red"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Status Logs -->
                <div
                    class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <h3
                        class="text-lg font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-2">
                        ประวัติการดำเนินการ</h3>
                    <div
                        class="space-y-6 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-px before:bg-gray-100 dark:before:bg-gray-800">
                        @foreach($application->statusLogs as $log)
                            <div class="pl-10 relative">
                                <div
                                    class="absolute left-1.5 top-1 w-3 h-3 rounded-full bg-kumwell-red border-4 border-white dark:border-kumwell-card shadow-sm">
                                </div>
                                <div class="text-xs">
                                    <span class="font-bold text-kumwell-red uppercase">{{ $log->new_status }}</span>
                                    <span class="text-gray-400 mx-1">โดย</span>
                                    <span
                                        class="font-bold text-gray-700 dark:text-gray-200">{{ $log->user ? $log->user->name : 'System' }}</span>
                                    <span class="text-gray-400 ml-2">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    @if($log->remark)
                                        <p
                                            class="mt-2 text-gray-500 bg-gray-50 dark:bg-kumwell-dark p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                            {{ $log->remark }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Application History -->
                <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 mt-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-kumwell-red"></i> ประวัติการสมัครเดิม
                    </h3>
                    <div class="space-y-4">
                        @forelse($history as $hist)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-kumwell-dark rounded-xl border border-gray-100 dark:border-gray-700 hover:border-kumwell-red transition-all group">
                                <div>
                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                        {{ $hist->jobPost->position_name ?? ($hist->jobPost->jobPosition->position_name ?? '-') }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">
                                        สมัครเมื่อ: {{ $hist->applied_at ? $hist->applied_at->translatedFormat('d/m/Y H:i') : $hist->created_at->translatedFormat('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase 
                                        @if($hist->status == 'new') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($hist->status == 'screening') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @elseif($hist->status == 'interview') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                        @elseif($hist->status == 'offered') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($hist->status == 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                        {{ $hist->status }}
                                    </span>
                                    <a href="{{ route('backend.recruitment.applications.show', $hist->id) }}" class="text-xs text-kumwell-red font-bold hover:underline">ดูรายละเอียด</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic">ไม่มีประวัติการสมัครอื่น</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Actions (Right) -->
            <div class="space-y-6">
                <div
                    class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">จัดการสถานะ</h3>

                    <form action="{{ route('backend.recruitment.applications.update-status', $application->id) }}"
                        method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase">เปลี่ยนสถานะเป็น</label>
                            <select name="status"
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-2.5 text-sm"
                                required>
                                <option value="screening" {{ $application->status == 'screening' ? 'selected' : '' }}>
                                    Screening</option>
                                <option value="interview" {{ $application->status == 'interview' ? 'selected' : '' }}>
                                    Interview (นัดสัมภาษณ์)</option>
                                <!-- <option value="offered" {{ $application->status == 'offered' ? 'selected' : '' }}>Offered
                                    (เสนอรับงาน)</option> -->
                                <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>Hired
                                    (รับเข้าทำงาน)</option>
                                <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected
                                    (ไม่ผ่าน)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase">หมายเหตุ</label>
                            <textarea name="note" rows="4"
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-2.5 text-sm"
                                placeholder="ระบุเหตุผลหรือบันทึกเพิ่มเติม..."></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-kumwell-red hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-red-500/30 transition-all active:scale-95">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </form>
                </div>

                <!-- Interview Management -->
                <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-800 pb-2">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-kumwell-red"></i> การสัมภาษณ์
                        </h3>
                        <button onclick="openModal('scheduleModal')" 
                            class="text-xs font-bold text-kumwell-red hover:text-red-700 underline transition-all">
                            + นัดสัมภาษณ์
                        </button>
                    </div>

                    <div class="space-y-4">
                        @forelse($application->interviews as $interview)
                            <div class="p-4 bg-gray-50 dark:bg-kumwell-dark rounded-xl border border-gray-100 dark:border-gray-700 group">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="text-[10px] font-bold text-kumwell-red uppercase px-2 py-0.5 bg-red-50 dark:bg-red-500/10 rounded-full">รอบที่ {{ $interview->interview_round }}</span>
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200 ml-2">
                                            {{ $interview->interview_date->format('d/m/Y') }} @ {{ \Carbon\Carbon::parse($interview->interview_time)->format('H:i') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $interview->status == 'completed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                            {{ $interview->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-1">
                                    <p><i class="fa-solid fa-user-tie mr-1"></i> ผู้สัมภาษณ์: 
                                        @if($interview->interviewers->count() > 0)
                                            {{ $interview->interviewers->pluck('fullname')->join(', ') }}
                                        @else
                                            {{ $interview->interviewer->fullname ?? 'ไม่ระบุ' }}
                                        @endif
                                    </p>
                                    <p><i class="fa-solid fa-location-dot mr-1"></i> {{ $interview->interview_type }} 
                                        @if($interview->location) - {{ $interview->location }} @endif
                                    </p>
                                    @if($interview->meeting_link)
                                        <a href="{{ $interview->meeting_link }}" target="_blank" class="text-blue-500 hover:underline"><i class="fa-solid fa-video mr-1"></i> ลิงก์ประชุมออนไลน์</a>
                                    @endif
                                </div>

                                @if($interview->status == 'scheduled')
                                    <div class="mt-4 flex gap-2">
                                        <button onclick="openScoreModal({{ $interview->id }})" 
                                            class="flex-1 bg-kumwell-red text-white text-[11px] font-bold py-2 rounded-lg hover:bg-red-700 transition-all shadow-md shadow-red-500/20">
                                            บันทึกคะแนน
                                        </button>
                                        <form action="{{ route('backend.recruitment.interviews.update-status', $interview->id) }}" method="POST" class="flex-none">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors border border-gray-100 dark:border-gray-700 rounded-lg">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                @elseif($interview->status == 'completed' && $interview->scores->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">สรุปคะแนน</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            @php $avg = $interview->scores->avg('score'); @endphp
                                            <div class="p-2 bg-white dark:bg-kumwell-card rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                                                <p class="text-[8px] text-gray-400 uppercase">Average</p>
                                                <p class="text-sm font-bold text-kumwell-red">{{ number_format($avg, 1) }}/10</p>
                                            </div>
                                            <div class="p-2 bg-white dark:bg-kumwell-card rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                                                <p class="text-[8px] text-gray-400 uppercase">Criteria</p>
                                                <p class="text-sm font-bold text-gray-700 dark:text-white">{{ $interview->scores->count() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-8 text-center bg-gray-50 dark:bg-kumwell-dark rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                                <i class="fa-solid fa-calendar-days text-gray-300 text-3xl mb-2"></i>
                                <p class="text-xs text-gray-400 font-medium">ยังไม่มีรายการนัดหมาย</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Interview Modal -->
    <div id="scheduleModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('scheduleModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-md transform transition-all animate-modal-in">
                <div class="bg-kumwell-red p-6 text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-calendar-plus"></i> นัดสัมภาษณ์งาน
                    </h3>
                    <button onclick="closeModal('scheduleModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form id="scheduleForm" action="{{ route('backend.recruitment.interviews.store', $application->id) }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">รอบที่</label>
                            <div class="flex items-center bg-gray-50 dark:bg-kumwell-dark rounded-xl p-1 border border-gray-100 dark:border-gray-800">
                                <button type="button" onclick="adjustRound(-1)" 
                                    class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-white dark:hover:bg-kumwell-card hover:shadow-sm text-gray-500 transition-all active:scale-90">
                                    <i class="fa-solid fa-minus text-xs"></i>
                                </button>
                                <input type="number" id="interview_round" name="interview_round" 
                                    value="{{ $application->interviews->count() + 1 }}" required readonly
                                    class="w-full bg-transparent border-none text-center font-bold text-sm focus:ring-0 p-0">
                                <button type="button" onclick="adjustRound(1)" 
                                    class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-white dark:hover:bg-kumwell-card hover:shadow-sm text-kumwell-red transition-all active:scale-90">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">รูปแบบ</label>
                            <select name="interview_type" required
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                                <option value="Online">Online</option>
                                <option value="On-site">On-site (บริษัท)</option>
                                <option value="Phone">Phone</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">วันที่</label>
                            <input type="text" id="interview_date" name="interview_date" placeholder="เลือกวันที่..." required
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all cursor-pointer">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">เวลา</label>
                            <input type="text" id="interview_time" name="interview_time" placeholder="เลือกเวลา..." required
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all cursor-pointer">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-end px-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">ผู้สัมภาษณ์ (เลือกได้หลายคน)</label>
                            <div class="flex items-center gap-2">
                                <input type="text" id="interviewer_search_custom" placeholder="ค้นหารายชื่อ..." autocomplete="off"
                                    class="bg-gray-50 dark:bg-kumwell-dark/50 border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-2 text-[10px] text-gray-700 dark:text-gray-200 focus:bg-white dark:focus:bg-kumwell-card focus:ring-2 focus:ring-kumwell-red/20 outline-none transition-all w-32 text-center">
                                <button type="button" onclick="$('#interviewer_select').select2('open')" 
                                    class="w-10 h-10 flex items-center justify-center bg-white dark:bg-kumwell-card rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 text-kumwell-red hover:bg-gray-50 transition-all active:scale-90">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div class="interviewer-selector-wrapper">
                            <select name="interviewer_ids[]" id="interviewer_select" required multiple
                                class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                                <option value=""></option>
                                @foreach($interviewers as $user)
                                    <option value="{{ $user->id }}">{{ $user->fullname }} ({{ $user->department->department_fullname ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">สถานที่ / ลิงก์ออนไลน์</label>
                        <input type="text" name="location" placeholder="ระบุห้องประชุม หรือ ปลายทาง"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Meeting Link (Zoom/Meet)</label>
                        <input type="url" name="meeting_link" placeholder="https://meet.google.com/..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">บันทึกเพิ่มเติม</label>
                        <textarea name="note" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติมถึงผู้สัมภาษณ์..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-kumwell-red hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-red-500/30 transition-all active:scale-95">
                            ยืนยันการนัดหมาย
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('emailModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-md transform transition-all animate-modal-in">
                <div class="bg-v-red p-6 text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-paper-plane"></i> ส่งอีเมลถึงผู้สมัคร
                    </h3>
                    <button onclick="closeModal('emailModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form id="emailForm" action="{{ route('backend.recruitment.applications.send-email', $application->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">หัวข้ออีเมล</label>
                        <input type="text" name="subject" required value="เรื่อง: ข้อมูลเพิ่มเติมเกี่ยวกับการสมัครงาน"
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">ข้อความ</label>
                        <textarea name="content" rows="6" required placeholder="พิมพ์ข้อความที่ต้องการส่งถึงผู้สมัคร..."
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">แนบไฟล์ (ถ้ามี)</label>
                        <input type="file" name="attachments[]" multiple
                            class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red/20 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-kumwell-red/10 file:text-kumwell-red hover:file:bg-kumwell-red/20">
                    </div>

                    <div class="pt-4">
                        <button type="submit" id="emailSubmitBtn"
                            class="w-full bg-v-red hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-red-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <span>ส่งอีเมลทันที</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Score Recording Modal -->
    <div id="scoreModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('scoreModal')"></div>
            
            <div class="relative bg-white dark:bg-kumwell-card rounded-3xl shadow-2xl w-full max-w-lg transform transition-all animate-modal-in">
                <div class="bg-gray-800 p-6 text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-star text-yellow-500"></i> บันทึกผลการสัมภาษณ์
                    </h3>
                    <button onclick="closeModal('scoreModal')" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form id="scoreForm" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-4">
                        @php 
                            $criterias = ['บุคลิกภาพและการพูด', 'ความรู้ในตำแหน่งงาน', 'ทัศนคติ/Mindset', 'ความเหมาะสมกับวัฒนธรรมองค์กร', 'ความสามารถด้านภาษา/ทักษะพิเศษ'];
                        @endphp
                        
                        @foreach($criterias as $index => $criteria)
                            <div class="space-y-3 p-4 bg-gray-50 dark:bg-kumwell-dark rounded-2xl border border-gray-100 dark:border-gray-800">
                                <div class="flex justify-between items-center">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ $criteria }}</label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-gray-400 font-medium">คะแนน:</span>
                                        <span id="score_display_{{ $index }}" class="text-sm font-bold text-kumwell-red">5</span>
                                    </div>
                                </div>
                                <input type="hidden" name="scores[{{ $index }}][criteria]" value="{{ $criteria }}">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="adjustSlider({{ $index }}, -1)"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-kumwell-card shadow-sm border border-gray-100 dark:border-gray-800 text-gray-400 hover:text-kumwell-red transition-all active:scale-90">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    
                                    <input type="range" id="score_slider_{{ $index }}" name="scores[{{ $index }}][score]" min="0" max="10" value="5"
                                        oninput="document.getElementById('score_display_{{ $index }}').innerText = this.value"
                                        class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-kumwell-red">
                                    
                                    <button type="button" onclick="adjustSlider({{ $index }}, 1)"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-kumwell-card shadow-sm border border-gray-100 dark:border-gray-800 text-gray-400 hover:text-kumwell-red transition-all active:scale-90">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <input type="text" name="scores[{{ $index }}][comment]" placeholder="ความคิดเห็นเพิ่มเติม..."
                                    class="w-full bg-white dark:bg-kumwell-card border-none rounded-xl px-3 py-2 text-[10px] focus:ring-1 focus:ring-kumwell-red/20 transition-all">
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-2xl shadow-xl transition-all active:scale-95">
                            บันทึกคะแนนทั้งหมด
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal-in {
            animation: modal-in 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#interviewer_select').select2({
                placeholder: 'เลือกผู้สัมภาษณ์...',
                width: '100%'
            });

            // Sync custom search input with Select2
            $('#interviewer_search_custom').on('input keydown', function(e) {
                // Prevent form submission on Enter
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }

                const term = $(this).val();
                
                // Only open if not already open to prevent focus flickering
                if (!$('#interviewer_select').data('select2').isOpen()) {
                    $('#interviewer_select').select2('open');
                    // Re-focus the custom input because Select2 steals it
                    $(this).focus();
                }

                const $searchField = $('.select2-search__field');
                if ($searchField.length) {
                    $searchField.val(term).trigger('input');
                }
            });

            // Clear custom search when item is selected
            $('#interviewer_select').on('select2:select select2:unselect', function() {
                $('#interviewer_search_custom').val('').focus();
            });

            // Initialize Flatpickr for Date
            flatpickr("#interview_date", {
                dateFormat: "Y-m-d",
                minDate: "today",
                locale: "th",
                disableMobile: true
            });

            // Initialize Flatpickr for Time
            flatpickr("#interview_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                locale: "th",
                disableMobile: true
            });

            // Handle AJAX for Schedule Form
            const scheduleForm = document.getElementById('scheduleForm');
            if (scheduleForm) {
                scheduleForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleAjaxSubmit(this, 'scheduleModal');
                });
            }

            // Handle AJAX for Email Form
            const emailForm = document.getElementById('emailForm');
            if (emailForm) {
                emailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleAjaxSubmit(this, 'emailModal');
                });
            }
        });

        async function handleAjaxSubmit(form, modalId) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                <span>กำลังดำเนินการ...</span>
            `;

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(result.message || 'เกิดข้อผิดพลาดในการดำเนินการ');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error.message
                });
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            }
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Re-initialize or refresh select2 when modal opens to fix width issues if any
            if (id === 'scheduleModal') {
                $('#interviewer_select').select2({
                    placeholder: 'เลือกผู้สัมภาษณ์...',
                    width: '100%',
                    dropdownParent: $('#scheduleModal')
                });
            }
        }

        function adjustRound(delta) {
            const input = document.getElementById('interview_round');
            let val = parseInt(input.value) || 1;
            val = Math.max(1, val + delta);
            input.value = val;
        }

        function adjustSlider(index, delta) {
            const slider = document.getElementById('score_slider_' + index);
            const display = document.getElementById('score_display_' + index);
            let val = parseInt(slider.value) || 0;
            val = Math.min(10, Math.max(0, val + delta));
            slider.value = val;
            display.innerText = val;
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openScoreModal(interviewId) {
            const form = document.getElementById('scoreForm');
            form.action = `/backend/recruitment/interviews/${interviewId}/scores`;
            openModal('scoreModal');
        }
    </script>
    <style>
        /* Select2 Custom Styling to match Tailwind */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: #f9fafb !important; /* gray-50 */
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            min-height: 48px !important; /* increased for better touch/thai text */
            display: flex;
            align-items: center;
            transition: all 0.2s;
            padding: 4px 8px !important;
            width: 100% !important;
        }
        .dark .select2-container--default .select2-selection--single,
        .dark .select2-container--default .select2-selection--multiple {
            background-color: #121418 !important;
            border-color: #374151 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding-left: 0.5rem !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
            width: 100% !important;
            display: block !important;
            line-height: normal !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered,
        .dark .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #d1d5db !important; /* text-gray-300 */
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: flex-start !important;
            gap: 6px !important;
            padding: 8px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #D71920 !important;
            border: none !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            margin: 0 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            max-width: calc(100% - 10px) !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            margin-top: 0 !important;
            padding: 4px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 5px !important;
            border-right: 1px solid rgba(255,255,255,0.2) !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: rgba(255,255,255,0.1) !important;
        }
        .select2-container--default .select2-selection--multiple {
            padding-right: 8px !important;
            min-height: 100px !important; /* taller as per mockup */
            align-items: flex-start !important;
            padding: 12px !important;
            background-color: #ffffff !important; /* white box for chips */
            border: 1px solid #e5e7eb !important;
        }
        .dark .select2-container--default .select2-selection--multiple {
            background-color: #1E2129 !important;
            border-color: #374151 !important;
        }
        .select2-container--default .select2-search--inline {
            position: absolute !important;
            opacity: 0 !important;
            pointer-events: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 0.75rem !important;
        }
        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden;
            z-index: 70 !important;
        }
        .dark .select2-dropdown {
            background-color: #1E2129 !important; /* kumwell-card */
            border-color: #374151 !important;
        }
        .select2-search__field {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 10px 12px !important; /* increased padding for Thai text */
            font-size: 0.875rem !important;
            min-height: 38px !important;
        }
        .dark .select2-search__field {
            background-color: #121418 !important;
            border-color: #374151 !important;
            color: white !important;
        }
        .select2-results__option {
            padding: 8px 16px !important;
            font-size: 0.875rem !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #D71920 !important; /* kumwell-red */
        }

        /* Flatpickr Premium Light Theme Custom Styling */
        .flatpickr-calendar {
            background: #ffffff !important;
            border-radius: 1.5rem !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            z-index: 9999 !important; /* extremely high to be on top of modals */
            padding: 12px !important;
            font-family: 'Prompt', sans-serif !important;
        }

        .flatpickr-months {
            background: transparent !important;
            margin-bottom: 8px !important;
        }
        
        .flatpickr-months .flatpickr-month {
            color: #1f2937 !important; /* gray-800 */
            fill: #1f2937 !important;
            height: 46px !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700 !important;
            font-size: 1rem !important;
            background: transparent !important;
            color: #1f2937 !important;
            padding: 4px 8px !important;
            border-radius: 8px !important;
        }
        
        .flatpickr-current-month .flatpickr-monthDropdown-month:hover {
            background: #f3f4f6 !important;
        }

        .flatpickr-current-month input.cur-year {
            font-weight: 700 !important;
            color: #1f2937 !important;
        }

        .flatpickr-weekday {
            color: #9ca3af !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
        }

        .flatpickr-day {
            border-radius: 10px !important;
            color: #374151 !important; /* gray-700 */
            font-weight: 500 !important;
            height: 38px !important;
            line-height: 38px !important;
            margin: 2px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .flatpickr-day:hover {
            background: #fee2e2 !important; /* red-100 */
            color: #D71920 !important;
            transform: translateY(-1px);
        }

        .flatpickr-day.selected {
            background: #D71920 !important;
            border-color: #D71920 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(215, 25, 32, 0.3) !important;
            transform: scale(1.05);
        }

        .flatpickr-day.today {
            border-color: #D71920 !important;
            color: #D71920 !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
            opacity: 0.5;
        }

        .flatpickr-time {
            border-top: 1px solid #f1f5f9 !important;
            margin-top: 12px !important;
            padding-top: 8px !important;
            height: 48px !important;
        }

        .flatpickr-time input {
            color: #1f2937 !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
        }

        .flatpickr-time .numInputWrapper span.arrowUp:after { border-bottom-color: #D71920; }
        .flatpickr-time .numInputWrapper span.arrowDown:after { border-top-color: #D71920; }
        
        .flatpickr-prev-month, .flatpickr-next-month {
            padding: 8px !important;
            border-radius: 50% !important;
            transition: all 0.2s !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .flatpickr-prev-month:hover, .flatpickr-next-month:hover {
            background: #f3f4f6 !important;
        }

        .flatpickr-prev-month:hover svg, .flatpickr-next-month:hover svg {
            fill: #D71920 !important;
        }


    </style>
    @endpush
@endsection