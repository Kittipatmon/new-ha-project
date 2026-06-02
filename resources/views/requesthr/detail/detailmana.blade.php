@extends('layouts.hrrequest.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 font-prompt">
    <!-- Breadcrumbs -->
    <div class="text-sm breadcrumbs text-gray-500 dark:text-gray-400 mb-6">
        <ul>
            <li><a href="{{ route('welcome') }}" class="hover:text-red-500 transition-colors">Home</a></li>
            <li><a href="{{ route('request.hr') }}" class="hover:text-red-500 transition-colors">Request HR</a></li>
            <li><a href="{{ route('approve.approvemanalist') }}" class="hover:text-red-500 transition-colors">รายการรอดำเนินการ</a></li>
            <li class="font-medium text-red-600 dark:text-red-500">รายละเอียดคำร้อง</li>
        </ul>
    </div>

    <!-- Header Block -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fas fa-file-alt text-red-500"></i> รายละเอียดคำร้อง
            </h1>
            <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                รหัสอ้างอิง: <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $hrrequest->request_code }}</span>
            </div>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold border badge {{ $hrrequest->status_color }} shadow-sm">
                {{ $hrrequest->status_label }}
            </span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Primary Details -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Unified Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <!-- Section 1: User Info -->
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-gray-400"></i> ข้อมูลผู้ร้องขอ
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 bg-gray-50/50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">ชื่อ-นามสกุล</div>
                            <div class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                                {{ $hrrequest->user?->fullname ?? 'ไม่พบข้อมูล' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">แผนก</div>
                            <div class="font-medium text-sm text-gray-700 dark:text-gray-300">
                                {{ $hrrequest->user->department->department_name ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">ฝ่าย</div>
                            <div class="font-medium text-sm text-gray-700 dark:text-gray-300">
                                {{ $hrrequest->user->division->division_name ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">ส่วน</div>
                            <div class="font-medium text-sm text-gray-700 dark:text-gray-300">
                                {{ $hrrequest->user->section->section_code ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 dark:border-gray-700">

                <!-- Section 2: Request Info -->
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <i class="fas fa-list-alt text-gray-400"></i> ข้อมูลคำร้อง
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">หมวดหมู่คำร้อง</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $hrrequest->category->name_th ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">ประเภทคำขอ</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $hrrequest->type->name_th ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">ประเภทย่อย</div>
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $hrrequest->subtype->name_th ?? '-' }}</div>
                        </div>
                    </div>

                    @if($hrrequest->detail || !empty($hrrequest->welfares->welfare_reason) || ($hrrequest->timeEdits && $hrrequest->timeEdits->count()))
                    <div class="bg-gray-50/50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 space-y-4">
                        
                        @if($hrrequest->detail || !empty($hrrequest->welfares->welfare_reason))
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">รายละเอียดเพิ่มเติม</div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                @if($hrrequest->detail)
                                    {!! nl2br(e($hrrequest->detail)) !!}
                                @endif
                                @if(!empty($hrrequest->welfares->welfare_reason))
                                    @if($hrrequest->detail) <br><br> @endif
                                    {{ $hrrequest->welfares->welfare_reason ?? '-' }}
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($hrrequest->timeEdits && $hrrequest->timeEdits->count())
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">เอกสารแนบ</div>
                            <div class="flex flex-col gap-2">
                                @foreach($hrrequest->timeEdits as $timeEdit)
                                    @if($timeEdit->timefile)
                                        <a href="{{ asset($timeEdit->timefile) }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-2 rounded-lg w-fit">
                                            <i class="fas fa-paperclip text-gray-400"></i>
                                            {{ basename($timeEdit->timefile) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            @php
                                $hasFiles = $hrrequest->timeEdits->filter(function($te) { return !empty($te->timefile); })->count() > 0;
                            @endphp
                            @if(!$hasFiles)
                                <span class="text-sm text-gray-400 dark:text-gray-500 italic">ไม่มีไฟล์แนบ</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                
                <hr class="border-gray-100 dark:border-gray-700">

                <!-- Section 3: Approval Status -->
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <i class="fas fa-tasks text-gray-400"></i> ลำดับการพิจารณา
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Manager Approval -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-4 rounded-xl border {{ $hrrequest->approver_manager_status == '0' ? 'border-yellow-200 bg-yellow-50/50 dark:border-yellow-900/50 dark:bg-yellow-900/10' : 'border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5">
                                    <i class="fas fa-user-tie text-gray-400"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">พิจารณาโดย (หัวหน้างาน)</div>
                                    <div class="font-bold text-sm text-gray-800 dark:text-gray-200">
                                        {{ $hrrequest->approverManager->fullname ?? '-' }}
                                    </div>
                                    @if($hrrequest->approver_manager_comment)
                                    <div class="text-xs font-medium mt-1 {{ $hrrequest->approver_manager_status == '1' ? 'text-green-600' : ($hrrequest->approver_manager_status == '2' ? 'text-red-600' : 'text-orange-600') }}">
                                        "{{ $hrrequest->approver_manager_comment }}"
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-medium badge {{ $hrrequest->approver_manager_status_color }}">
                                    {{ $hrrequest->approver_manager_status_label }}
                                </span>
                            </div>
                        </div>

                        <!-- HR Approval -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-4 rounded-xl border {{ $hrrequest->approver_hr_status == '0' ? 'border-blue-200 bg-blue-50/50 dark:border-blue-900/50 dark:bg-blue-900/10' : 'border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5">
                                    <i class="fas fa-user-shield text-gray-400"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">พิจารณาโดย (ฝ่ายบุคคล)</div>
                                    <div class="font-bold text-sm text-gray-800 dark:text-gray-200">
                                        {{ $hrrequest->approverhr->fullname ?? 'รอรับเรื่อง' }}
                                    </div>
                                    @if($hrrequest->approver_hr_comment)
                                    <div class="text-xs font-medium mt-1 {{ $hrrequest->approver_hr_status == '1' ? 'text-green-600' : ($hrrequest->approver_hr_status == '2' ? 'text-red-600' : 'text-orange-600') }}">
                                        "{{ $hrrequest->approver_hr_comment }}"
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-medium badge {{ $hrrequest->approver_hr_status_color }}">
                                    {{ $hrrequest->approver_hr_status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-dashed border-gray-200 dark:border-gray-700 text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                        <i class="far fa-clock"></i> วันที่ส่งคำร้อง: {{ $hrrequest->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Contextual & Actions -->
        <div class="lg:col-span-5 space-y-6">
            
            @if($hrrequest->type_id == '9')
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <i class="fas fa-hard-hat text-gray-400"></i> ความปลอดภัย/อุปกรณ์
                </h2>

                <div class="mb-5 bg-gray-50/50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">เหตุผล</div>
                    <div class="text-sm text-gray-800 dark:text-gray-200">test</div>
                </div>

                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">รายการอุปกรณ์ที่ขอ</div>
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ลำดับ</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รายการ</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500">1</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 font-medium">test</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-right">1</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500">2</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 font-medium">test1</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 text-right">2</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-yellow-100 dark:border-yellow-900 overflow-hidden p-6 ring-1 ring-yellow-50 dark:ring-yellow-900/30">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-yellow-500"></i> ดำเนินการอนุมัติ (หัวหน้างาน)
                </h2>
                <form id="approveForm" action="{{ route('approve.managerCheck', $hrrequest->hr_request_id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" id="statusInput">
                    
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            หมายเหตุ <span class="text-gray-400 font-normal">(ถ้ามี)</span>
                        </label>
                        <textarea name="comment" class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 transition-colors p-3 text-sm h-24" placeholder="ระบุเหตุผลประกอบการพิจารณา..."></textarea>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" onclick="confirmAction('1', 'อนุมัติ', 'คุณต้องการอนุมัติคำร้องนี้ใช่หรือไม่?', 'success')" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-check"></i> อนุมัติ
                        </button>
                        <button type="button" onclick="confirmAction('3', 'ส่งกลับแก้ไข', 'คุณต้องการส่งกลับแก้ไขคำร้องนี้ใช่หรือไม่?', 'warning')" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-xl shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <i class="fas fa-undo"></i> ส่งกลับ
                        </button>
                        <button type="button" onclick="confirmAction('2', 'ไม่อนุมัติ', 'คุณต้องการไม่อนุมัติคำร้องนี้ใช่หรือไม่?', 'error')" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-times"></i> ไม่อนุมัติ
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmAction(status, title, text, icon) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('statusInput').value = status;
                document.getElementById('approveForm').submit();
            }
        });
    }
</script>
@endpush