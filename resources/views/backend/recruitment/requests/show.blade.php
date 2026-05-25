@extends('layouts.sidebar')

@section('title', 'รายละเอียดคำขอเปิดรับสมัครพนักงาน')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('backend.recruitment.requests.index') }}"
                    class="text-gray-400 hover:text-kumwell-red transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h2 class="text-2xl font-bold dark:text-white text-gray-800">รายละเอียดคำขอ
                    #{{ $recruitmentRequest->request_no }}</h2>
            </div>
            <div>
                <span class="px-4 py-1.5 rounded-full text-sm font-bold 
                    @if($recruitmentRequest->status == 'approved') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                    @elseif($recruitmentRequest->status == 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                    @elseif($recruitmentRequest->status == 'pending_manager' || $recruitmentRequest->status == 'pending_executive') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                    @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400 @endif">
                    สถานะ: {{ ucfirst(str_replace('_', ' ', $recruitmentRequest->status)) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Info -->
                <div
                    class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-kumwell-red"></i>
                        ข้อมูลเบื้องต้น
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">ตำแหน่ง (Position)</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $recruitmentRequest->position_name ?: ($recruitmentRequest->jobPosition?->position_name ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">หน่วยงาน/แผนก (Department)</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $recruitmentRequest->department?->department_fullname ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">จำนวนอัตรา (Headcount)</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $recruitmentRequest->headcount }}
                                อัตรา</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">วันที่ต้องการเริ่มงาน (Required Start Date)</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $recruitmentRequest->required_start_date ? $recruitmentRequest->required_start_date->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">งบประมาณเงินเดือน (Salary Range)</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ number_format($recruitmentRequest->salary_min) }} -
                                {{ number_format($recruitmentRequest->salary_max) }} บาท
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">ผู้ส่งคำขอ (Requested By)</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $recruitmentRequest->requester?->fullname ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Detailed Info -->
                <div
                    class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
                    <div class="space-y-8">
                        <section>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">เหตุผลในการเปิดรับ (Reason)
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ $recruitmentRequest->reason ?: 'ไม่ได้ระบุ' }}</p>
                        </section>
                        <section>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">รายละเอียดงาน (Job Description)
                            </h3>
                            <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                                {{ $recruitmentRequest->job_description ?: 'ไม่ได้ระบุ' }}</div>
                        </section>
                        <section>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">คุณสมบัติ (Qualification)</h3>
                            <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                                {{ $recruitmentRequest->qualification ?: 'ไม่ได้ระบุ' }}</div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Approval Workflow -->
                <div
                    class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
                    <h3 class="text-md font-bold text-gray-800 dark:text-white mb-6">ขั้นตอนการอนุมัติ</h3>
                    <div
                        class="space-y-6 relative before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100 dark:before:bg-gray-800">
                        <!-- Manager Approval -->
                        <div class="relative pl-8">
                            <div
                                class="absolute left-0 top-1 w-5 h-5 rounded-full border-2 border-white dark:border-kumwell-card z-10 
                                @if($recruitmentRequest->approved_by_manager) bg-green-500 @else bg-gray-200 dark:bg-gray-700 @endif">
                            </div>
                            <div>
                                <p
                                    class="text-sm font-bold @if($recruitmentRequest->approved_by_manager) text-green-600 @else text-gray-500 @endif">
                                    หัวหน้างาน (Manager) 
                                    @if($recruitmentRequest->targetManagerApprover)
                                        <span class="text-[10px] font-medium text-gray-400">
                                            (Target: {{ $recruitmentRequest->targetManagerApprover->fullname }})
                                        </span>
                                        @if(!$recruitmentRequest->approved_by_manager && (Auth::user()->hr_status == 0 || Auth::id() == $recruitmentRequest->requested_by))
                                            <button onclick="openEditApproverModal('manager', '{{ $recruitmentRequest->approver_manager_id }}')" 
                                                    class="text-kumwell-red hover:text-red-700 ml-1" title="แก้ไขผู้อนุมัติ">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </button>
                                        @endif
                                    @endif
                                </p>
                                @if($recruitmentRequest->approved_by_manager)
                                    <p class="text-xs text-gray-500">อนุมัติโดย:
                                        {{ $recruitmentRequest->managerApprover?->fullname ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-gray-400">
                                        {{ $recruitmentRequest->approved_at_manager?->format('d/m/Y H:i') ?? '-' }}</p>
                                @else
                                    <p class="text-xs text-gray-400">รอพิจารณา</p>
                                @endif
                            </div>
                        </div>

                        <!-- Executive Approval -->
                        <div class="relative pl-8">
                            <div
                                class="absolute left-0 top-1 w-5 h-5 rounded-full border-2 border-white dark:border-kumwell-card z-10 
                                @if($recruitmentRequest->approved_by_executive) bg-green-500 @else bg-gray-200 dark:bg-gray-700 @endif">
                            </div>
                            <div>
                                <p
                                    class="text-sm font-bold @if($recruitmentRequest->approved_by_executive) text-green-600 @else text-gray-500 @endif">
                                    ผู้บริหาร (Executive)
                                    @if($recruitmentRequest->targetExecutiveApprover)
                                        <span class="text-[10px] font-medium text-gray-400">
                                            (Target: {{ $recruitmentRequest->targetExecutiveApprover->fullname }})
                                        </span>
                                        @if(!$recruitmentRequest->approved_by_executive && (Auth::user()->hr_status == 0 || Auth::id() == $recruitmentRequest->requested_by))
                                            <button onclick="openEditApproverModal('executive', '{{ $recruitmentRequest->approver_executive_id }}')" 
                                                    class="text-kumwell-red hover:text-red-700 ml-1" title="แก้ไขผู้อนุมัติ">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </button>
                                        @endif
                                    @endif
                                </p>
                                @if($recruitmentRequest->approved_by_executive)
                                    <p class="text-xs text-gray-500">อนุมัติโดย:
                                        {{ $recruitmentRequest->executiveApprover?->fullname ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-gray-400">
                                        {{ $recruitmentRequest->approved_at_executive?->format('d/m/Y H:i') ?? '-' }}</p>
                                @else
                                    <p class="text-xs text-gray-400">รอพิจารณา</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(in_array($recruitmentRequest->status, ['pending_manager', 'pending_executive']))
                        <div class="mt-8 space-y-3 pt-6 border-t border-gray-100 dark:border-gray-800">
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

                            @if($canApprove)
                                <form action="{{ route('backend.recruitment.requests.approve', $recruitmentRequest->id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-green-500/20 transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-check"></i>
                                        อนุมัติคำขอ
                                    </button>
                                </form>
                            @endif
                            <button onclick="toggleRejectModal()"
                                class="w-full bg-white dark:bg-kumwell-dark border border-red-200 dark:border-red-900 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 font-bold py-2.5 rounded-xl transition-all">
                                ไม่อนุมัติ
                            </button>
                        </div>
                    @endif
                </div>

                @if($recruitmentRequest->status == 'approved')
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl p-6">
                        <h3 class="text-blue-800 dark:text-blue-300 font-bold mb-2">พร้อมสำหรับประกาศรับสมัคร</h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">คำขอนี้ได้รับการอนุมัติแล้ว
                            และสามารถนำข้อมูลไปสร้างประกาศรับสมัครงานได้ทันที</p>
                        <a href="{{ route('backend.recruitment.posts.create', ['request_id' => $recruitmentRequest->id]) }}"
                            class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-xl transition-all shadow-md">
                            สร้าง Job Post
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Simple Reject Modal (Mockup) -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-kumwell-card rounded-3xl p-8 max-w-md w-full">
            <h3 class="text-xl font-bold dark:text-white mb-4">ระบุเหตุผลที่ไม่อนุมัติ</h3>
            <form action="{{ route('backend.recruitment.requests.reject', $recruitmentRequest->id) }}" method="POST"
                class="space-y-4">
                @csrf
                <textarea name="remarks" rows="4"
                    class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-2xl p-4 text-sm"
                    placeholder="ระบุเหตุผล..." required></textarea>
                <div class="flex gap-3">
                    <button type="button" onclick="toggleRejectModal()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 font-bold dark:text-white">ยกเลิก</button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 text-white font-bold">ยืนยัน</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Approver Modal -->
    <div id="editApproverModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-kumwell-card rounded-3xl p-8 max-w-md w-full">
            <h3 class="text-xl font-bold dark:text-white mb-4">แก้ไขผู้อนุมัติ</h3>
            <form action="{{ route('backend.recruitment.requests.update-approver', $recruitmentRequest->id) }}" method="POST"
                class="space-y-4">
                @csrf
                <input type="hidden" name="approver_type" id="edit_approver_type">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เลือกผู้อนุมัติใหม่</label>
                    <div class="relative">
                        <select name="approver_id" id="edit_approver_id" class="select2-edit-approver w-full">
                            @foreach($approvers as $approver)
                                <option value="{{ $approver->id }}">{{ $approver->fullname }} ({{ $approver->position }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeEditApproverModal()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 font-bold dark:text-white">ยกเลิก</button>
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-kumwell-red text-white font-bold shadow-lg shadow-red-500/30 transition-all active:scale-95">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-edit-approver').select2({
                placeholder: "เลือกผู้อนุมัติ",
                allowClear: false,
                width: '100%',
                dropdownParent: $('#editApproverModal')
            });
        });

        function toggleRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.toggle('hidden');
        }

        function openEditApproverModal(type, currentId) {
            document.getElementById('edit_approver_type').value = type;
            $('#edit_approver_id').val(currentId).trigger('change');
            document.getElementById('editApproverModal').classList.remove('hidden');
        }

        function closeEditApproverModal() {
            document.getElementById('editApproverModal').classList.add('hidden');
        }
    </script>
    <style>
        .select2-container--default .select2-selection--single {
            background-color: rgb(249 250 251);
            border: 1px solid rgb(229 231 235);
            border-radius: 0.75rem;
            height: 42px;
            display: flex;
            align-items: center;
        }

        .dark .select2-container--default .select2-selection--single {
            background-color: #121418;
            border-color: rgb(55 65 81);
            color: white;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: inherit;
            padding-left: 1rem;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: rgb(209 213 219);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-dropdown {
            border-radius: 0.75rem;
            border-color: rgb(229 231 235);
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .dark .select2-dropdown {
            background-color: #1E2129;
            border-color: rgb(55 65 81);
            color: white;
        }

        .dark .select2-search__field {
            background-color: #121418;
            border-color: rgb(55 65 81);
            color: white;
        }

        .select2-results__option--highlighted[aria-selected] {
            background-color: #D71920 !important;
        }
    </style>
@endsection