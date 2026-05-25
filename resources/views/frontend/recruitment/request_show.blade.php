@extends('layouts.recruitment.app')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-20">
        <!-- Navigation Breadcrumb -->
        <div class="max-w-5xl mx-auto px-6 py-6 pt-10">
            <nav class="flex text-sm text-gray-500 gap-2 items-center font-medium">
                <a href="{{ route('recruitment.reports') }}" class="hover:text-kumwell-red transition-colors text-gray-400">รายการคำขอเปิดรับสมัคร</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-gray-900 dark:text-gray-300 truncate">รายละเอียดคำขอ {{ $recruitmentRequest->request_no }}</span>
            </nav>
        </div>

        <div class="max-w-5xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Request Details Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 border border-gray-100 dark:border-slate-700 shadow-sm">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $recruitmentRequest->position_name ?: ($recruitmentRequest->jobPosition?->position_name ?? 'N/A') }}
                                </h1>
                                <p class="text-gray-500 dark:text-gray-400 font-medium mt-1">เลขที่คำขอ: {{ $recruitmentRequest->request_no }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">วันที่ขอเปิด</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $recruitmentRequest->created_at->translatedFormat('j M Y') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8 mb-10">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">แผนก</p>
                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $recruitmentRequest->department?->department_fullname ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">จำนวนที่รับ</p>
                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $recruitmentRequest->headcount }} อัตรา</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">ประเภทการจ้างงาน</p>
                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $recruitmentRequest->employment_type == 'full_time' ? 'Full-Time' : 'Part-Time' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">ผู้ส่งคำขอ</p>
                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $recruitmentRequest->requester?->fullname ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white border-l-4 border-kumwell-red pl-4 mb-3">รายละเอียดงาน / เหตุผลการขอ</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed pl-5 whitespace-pre-wrap">{{ $recruitmentRequest->remarks ?: 'ไม่ระบุ' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Workflow/Approval History Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 border border-gray-100 dark:border-slate-700 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-8">ขั้นตอนการอนุมัติ</h3>
                        
                        <div class="space-y-8 relative before:absolute before:inset-0 before:ml-[15px] before:-z-10 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-gray-100 before:to-gray-100 dark:before:from-slate-700 dark:before:to-slate-700">
                            <!-- Manager Step -->
                            <div class="relative flex items-start gap-5">
                                <div class="w-8 h-8 rounded-full border-4 border-white dark:border-slate-800 flex items-center justify-center -translate-x-[1px]
                                    {{ $recruitmentRequest->approved_at_manager ? 'bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.4)]' : 'bg-gray-200 dark:bg-slate-700' }}">
                                    @if($recruitmentRequest->approved_at_manager)
                                        <i class="fa-solid fa-check text-[10px] text-white"></i>
                                    @endif
                                </div>
                                <div class="flex-1 -mt-1">
                                    <div class="flex justify-between items-start">
                                        <p class="text-base font-bold {{ $recruitmentRequest->approved_at_manager ? 'text-green-500' : 'text-gray-900 dark:text-white' }}">หัวหน้างาน (Manager)</p>
                                        @if($recruitmentRequest->approved_at_manager)
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $recruitmentRequest->approved_at_manager->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">ผู้อนุมัติ: {{ $recruitmentRequest->managerApprover?->fullname ?? ($recruitmentRequest->targetManagerApprover?->fullname ?? 'N/A') }}</p>
                                    @if($recruitmentRequest->status === 'pending_manager')
                                        <p class="text-[10px] font-bold text-yellow-500 bg-yellow-50 dark:bg-yellow-900/10 px-2 py-0.5 rounded-full inline-block mt-2">กำลังรอพิจารณา</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Executive Step -->
                            <div class="relative flex items-start gap-5">
                                <div class="w-8 h-8 rounded-full border-4 border-white dark:border-slate-800 flex items-center justify-center -translate-x-[1px]
                                    {{ $recruitmentRequest->approved_at_executive ? 'bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.4)]' : 'bg-gray-200 dark:bg-slate-700' }}">
                                    @if($recruitmentRequest->approved_at_executive)
                                        <i class="fa-solid fa-check text-[10px] text-white"></i>
                                    @endif
                                </div>
                                <div class="flex-1 -mt-1">
                                    <div class="flex justify-between items-start">
                                        <p class="text-base font-bold {{ $recruitmentRequest->approved_at_executive ? 'text-green-500' : 'text-gray-900 dark:text-white' }}">ผู้บริหาร (Executive)</p>
                                        @if($recruitmentRequest->approved_at_executive)
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $recruitmentRequest->approved_at_executive->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">ผู้อนุมัติ: {{ $recruitmentRequest->executiveApprover?->fullname ?? ($recruitmentRequest->targetExecutiveApprover?->fullname ?? 'N/A') }}</p>
                                    @if($recruitmentRequest->status === 'pending_executive')
                                        <p class="text-[10px] font-bold text-yellow-500 bg-yellow-50 dark:bg-yellow-900/10 px-2 py-0.5 rounded-full inline-block mt-2">กำลังรอพิจารณา</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Actions -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-gray-100 dark:border-slate-700 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 text-center">สถานะปัจจุบัน</p>
                        <div class="text-center py-4 bg-gray-50 dark:bg-slate-900/50 rounded-2xl mb-6">
                            @php
                                $statusClasses = [
                                    'approved' => 'text-green-500',
                                    'rejected' => 'text-red-500',
                                    'pending_manager' => 'text-yellow-500',
                                    'pending_executive' => 'text-orange-500',
                                ];
                                $statusText = [
                                    'pending_manager' => 'รอหัวหน้างานอนุมัติ',
                                    'pending_executive' => 'รอผู้บริหารอนุมัติ',
                                    'approved' => 'อนุมัติเรียบร้อย',
                                    'rejected' => 'ไม่อนุมัติ',
                                ][$recruitmentRequest->status] ?? $recruitmentRequest->status;
                            @endphp
                            <p class="text-lg font-bold {{ $statusClasses[$recruitmentRequest->status] ?? 'text-gray-500' }}">
                                {{ $statusText }}
                            </p>
                        </div>

                        <!-- Approval Buttons -->
                        @if ($recruitmentRequest->status === 'pending_manager' || $recruitmentRequest->status === 'pending_executive')
                            @php
                                $canApprove = false;
                                if (Auth::user()->hr_status == 0) {
                                    $canApprove = true;
                                } elseif ($recruitmentRequest->status === 'pending_manager' && $recruitmentRequest->approver_manager_id == Auth::id()) {
                                    $canApprove = true;
                                } elseif ($recruitmentRequest->status === 'pending_executive' && $recruitmentRequest->approver_executive_id == Auth::id()) {
                                    $canApprove = true;
                                }
                            @endphp

                            @if ($canApprove)
                                <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-slate-700">
                                    <form action="{{ route('backend.recruitment.requests.approve', $recruitmentRequest->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-green-500/20 transition-all">
                                            อนุมัติคำขอ
                                        </button>
                                    </form>
                                    <button onclick="toggleRejectModal()"
                                        class="w-full bg-white dark:bg-slate-800 border-2 border-red-100 dark:border-red-900/30 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 font-bold py-3 rounded-xl transition-all">
                                        ไม่อนุมัติ
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl border border-white/20">
            <h3 class="text-2xl font-bold dark:text-white mb-2">ระบุเหตุผลไม่อนุมัติ</h3>
            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6">กรุณาระบุสาเหตุหรือข้อเสนอแนะเพื่อให้ผู้ขอทราบ</p>
            <form action="{{ route('backend.recruitment.requests.reject', $recruitmentRequest->id) }}" method="POST" class="space-y-6">
                @csrf
                <textarea name="remarks" rows="4"
                    class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-kumwell-red/20 outline-none transition-all dark:text-white"
                    placeholder="ระบุเหตุผลที่ไม่อนุมัติ..." required></textarea>
                <div class="flex gap-4">
                    <button type="button" onclick="toggleRejectModal()"
                        class="flex-1 py-3.5 rounded-xl border border-gray-100 dark:border-slate-700 font-bold dark:text-white hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">ยกเลิก</button>
                    <button type="submit"
                        class="flex-1 py-3.5 rounded-xl bg-red-500 text-white font-bold shadow-lg shadow-red-500/20 active:scale-95 transition-all">ยืนยัน</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.toggle('hidden');
        }
    </script>
@endsection
