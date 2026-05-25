@extends('layouts.hrrequest.app')
@section('content')
    <div class="text-sm breadcrumbs text-base-content/60 mt-4">
        <ul>
            <li><a>Home</a></li>
            <li><a href="{{ route('request.hr') }}">Request HR</a></li>
            <li><a href="{{ route('approve.approvemanalist') }}">รายการรอดำเนินการ</a></li>
            <li class="text-red-600">รายละเอียดคำร้อง</li>
        </ul>
    </div>
    <div class="mb-3 p-6 border border-gray-300 dark:border-gray-200/40 rounded-xl bg-base-100 dark:bg-gray-800 shadow-lg">


        <div class="flex justify-between items-start mb-6 ">
            <div>
                <h1 class="text-2xl font-bold">รายละเอียดคำร้อง</h1>
                <div class="text-error text-sm font-semibold mt-1">
                    {{ $hrrequest->request_code }}
                </div>
            </div>
            <div>
                <div class="badge {{ $hrrequest->status_color }} p-3 font-semibold text-white">
                    {{ $hrrequest->status_label }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
            <div class="lg:col-span-7 flex flex-col gap-3 ">

                <div class="card bg-base-100 shadow-sm border border-gray-300 dark:border-gray-200/40 rounded-xl">
                    <div class="card-body p-6">
                        <h2 class="card-title text-base font-bold mb-2">ข้อมูลผู้ร้องขอ</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">ชื่อ-นามสกุล</div>
                                <div class="font-semibold text-sm">
                                    {{ $hrrequest->user->fullname }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">แผนก</div>
                                <div class="text-sm">
                                    {{ $hrrequest->user->department->department_name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">ฝ่าย</div>
                                <div class="text-sm">
                                    {{ $hrrequest->user->division->division_name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">ส่วน</div>
                                <div class="text-sm">
                                    {{ $hrrequest->user->section->section_code ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm border border-gray-300 dark:border-gray-200/40 rounded-xl">
                    <div class="card-body p-6">
                        <h2 class="card-title text-base font-bold mb-2">ข้อมูลคำร้อง</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">ประเภทคำขอ</div>
                                <div class="text-sm">{{ $hrrequest->category->name_th ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">ตัวเลือกการร้องขอ</div>
                                <div class="text-sm">{{ $hrrequest->type->name_th ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">ประเภทย่อย</div>
                                <div class="text-sm">{{ $hrrequest->subtype->name_th ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">รายละเอียด</div>
                                <div class="text-sm">
                                    @if($hrrequest->detail)
                                        {!! nl2br(e($hrrequest->detail)) !!}
                                    @endif

                                    @if(!empty($hrrequest->welfares->welfare_reason))
                                        <div>{{ $hrrequest->welfares->welfare_reason ?? '-' }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>

                            @if($hrrequest->timeEdits && $hrrequest->timeEdits->count())
                                @foreach($hrrequest->timeEdits as $timeEdit)
                                    @if($timeEdit->timefile)
                                        ไฟล์แนบ :
                                        <a href="{{ asset($timeEdit->timefile) }}" target="_blank" class="text-error hover:underline">
                                            {{ basename($timeEdit->timefile) }}
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-base-content/60">ไม่มีไฟล์แนบ</span>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm border border-gray-300 dark:border-gray-200/40 rounded-xl">
                    <div class="card-body p-6">
                        <h2 class="card-title text-base font-bold mb-2">สถานะการอนุมัติ</h2>
                        <div class="flex justify-between items-center border-b border-base-200 pb-4 mb-2">
                            <div>
                                <div class="font-bold text-sm">
                                    {{ $hrrequest->approverManager->fullname ?? '-' }}
                                </div>
                                <div class="text-xs text-base-content/60">
                                    <span
                                        class="px-2 py-1 rounded-full badge {{ $hrrequest->approver_manager_status_color }} badge-sm">
                                        {{ $hrrequest->approver_manager_status_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-sm">
                                    {{ $hrrequest->approverManager->fullname ?? '-' }}
                                </div>
                                <div class="text-xs text-base-content/60">
                                    @if($hrrequest->approver_manager_status == '1')
                                        <span class="text-green-500 font-semibold">
                                            {{ $hrrequest->approver_manager_comment ?? '-' }}
                                        </span>
                                    @endif
                                    @if($hrrequest->approver_manager_status == '2')
                                        <span class="text-red-500 font-semibold">
                                            {{ $hrrequest->approver_manager_comment ?? '-' }}
                                        </span>
                                    @endif
                                    @if($hrrequest->approver_manager_status == '3')
                                        <span class="text-orange-500 font-semibold">
                                            {{ $hrrequest->approver_manager_comment ?? '-' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center border-b border-base-200 pb-4 mb-2">
                            <div>
                                <div class="font-bold text-sm">
                                    {{ $hrrequest->approverhr->fullname ?? '-' }}
                                </div>
                                <div class="text-xs text-base-content/60">
                                    <span
                                        class="px-2 py-1 rounded-full badge {{ $hrrequest->approver_hr_status_color }} badge-sm">
                                        {{ $hrrequest->approver_hr_status_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-sm">
                                    {{ $hrrequest->approverhr->fullname ?? '-' }}
                                </div>
                                <div class="text-xs text-base-content/60">
                                    @if($hrrequest->approver_hr_status == '1')
                                        <span class="text-green-500 font-semibold">
                                            {{ $hrrequest->approver_hr_comment ?? '-' }}
                                        </span>
                                    @endif
                                    @if($hrrequest->approver_hr_status == '2')
                                        <span class="text-red-500 font-semibold">
                                            {{ $hrrequest->approver_hr_comment ?? '-' }}
                                        </span>
                                    @endif
                                    @if($hrrequest->approver_hr_status == '3')
                                        <span class="text-orange-500 font-semibold">
                                            {{ $hrrequest->approver_hr_comment ?? '-' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-red-500/60">
                            วันที่ส่งคำร้อง: {{ $hrrequest->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 flex flex-col gap-6">
                @if($hrrequest->type_id == '9')
                    <div class="card bg-base-100 shadow-sm border border-gray-300 dark:border-gray-200/40 rounded-xl">
                        <div class="card-body p-6">
                            <h2 class="card-title text-base font-bold mb-2">ความปลอดภัย/อุปกรณ์</h2>

                            <div class="mb-4">
                                <div class="text-xs text-base-content/60 mb-1">เหตุผล</div>
                                <div class="text-sm">test</div>
                            </div>

                            <div class="text-xs text-base-content/60 mb-2">รายการอุปกรณ์ที่ขอ</div>
                            <div class="overflow-x-auto">
                                <table class="table table-sm w-full">
                                    <thead>
                                        <tr class="border-b border-base-200 text-base-content/60">
                                            <th>ลำดับ</th>
                                            <th>รายการ</th>
                                            <th class="text-right">จำนวน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-base-200">
                                            <td>1</td>
                                            <td>test</td>
                                            <td class="text-right">1</td>
                                        </tr>
                                        <tr class="border-b-0">
                                            <td>2</td>
                                            <td>test1</td>
                                            <td class="text-right">2</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- <div class="card bg-base-100 shadow-sm border border-gray-300 dark:border-gray-200/40 rounded-xl">
                        <div class="card-body p-6">
                            <h2 class="card-title text-base font-bold mb-2">ดำเนินการอนุมัติ</h2>
                            <form action="#" method="POST">
                                @csrf
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text text-xs text-base-content/60">หมายเหตุ (ถ้ามี)</span>
                                    </label>
                                    <textarea class="textarea textarea-bordered h-24 bg-base-200" placeholder="ระบุหมายเหตุ..."></textarea>
                                </div>

                                <div class="flex gap-2">
                                    <button type="submit" class="btn btn-success text-white rounded-full px-6 btn-sm md:btn-md font-normal">อนุมัติ</button>
                                    <button type="button" class="btn btn-error text-white rounded-full px-6 btn-sm md:btn-md font-normal">ปฏิเสธ</button>
                                </div>
                            </form>
                        </div>
                    </div> -->
                <div class="card bg-base-100 shadow-sm border border-gray-300 dark:border-gray-200/40 rounded-xl">
                    <div class="card-body p-6">
                        <h2 class="card-title text-base font-bold">ดำเนินการอนุมัติ</h2>
                        <form id="approveForm" action="{{ route('approve.managerCheck', $hrrequest->hr_request_id) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="status" id="statusInput">
                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text text-sm text-red-500">หมายเหตุ (ถ้ามี)</span>
                                </label> <br>
                                <textarea name="comment" class="textarea textarea-bordered h-24 bg-base-200"
                                    placeholder="ระบุหมายเหตุ..."></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="button"
                                    onclick="confirmAction('1', 'อนุมัติ', 'คุณต้องการอนุมัติคำร้องนี้ใช่หรือไม่?', 'success')"
                                    class="btn btn-success text-white rounded-full px-6 btn-sm md:btn-md font-normal">
                                    <i class="fas fa-check"></i> อนุมัติ
                                </button>
                                <button type="button"
                                    onclick="confirmAction('3', 'ส่งกลับแก้ไข', 'คุณต้องการส่งกลับแก้ไขคำร้องนี้ใช่หรือไม่?', 'warning')"
                                    class="btn btn-warning text-white rounded-full px-6 btn-sm md:btn-md font-normal">
                                    <i class="fas fa-undo"></i> ส่งกลับแก้ไข
                                </button>
                                <button type="button"
                                    onclick="confirmAction('2', 'ไม่อนุมัติ', 'คุณต้องการไม่อนุมัติคำร้องนี้ใช่หรือไม่?', 'error')"
                                    class="btn btn-error text-white rounded-full px-6 btn-sm md:btn-md font-normal">
                                    <i class="fas fa-times"></i> ไม่อนุมัติ
                                </button>
                            </div>
                        </form>
                    </div>
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
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('statusInput').value = status;
                    document.getElementById('approveForm').submit();
                }
            });
        }
    </script>
@endpush