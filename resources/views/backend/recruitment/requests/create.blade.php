@extends('layouts.sidebar')

@section('title', 'สร้างคำขอเปิดรับสมัครพนักงาน')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('backend.recruitment.requests.index') }}"
                class="text-gray-400 hover:text-kumwell-red transition-colors">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold dark:text-white text-gray-800">สร้างคำขอเปิดอัตราใหม่</h2>
        </div>

        <form action="{{ route('backend.recruitment.requests.store') }}" method="POST"
            class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">แผนกที่ต้องการรับ
                        (Department)</label>
                    <select name="department_id"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"
                        required>
                        <option value="">เลือกแผนก</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}">{{ $dept->department_fullname }}
                                ({{ $dept->department_name }})</option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ตำแหน่งงาน (Position)</label>
                    <input type="text" name="position_name" list="position-list"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"
                        placeholder="พิมพ์หรือเลือกตำแหน่งงาน..." required>
                    <datalist id="position-list">
                        @foreach($employeePositions as $pName)
                            <option value="{{ $pName }}"></option>
                        @endforeach
                        @foreach($positions as $pos)
                            <option value="{{ $pos->position_name }}">{{ $pos->position_code }}</option>
                        @endforeach
                    </datalist>
                    @error('position_name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">จำนวนอัตรา (Headcount)</label>
                    <input type="number" name="headcount" min="1"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"
                        required>
                    @error('headcount') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">วันที่ต้องการเริ่มงาน (Required
                        Start Date)</label>
                    <input type="date" name="required_start_date"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all">
                    @error('required_start_date') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เงินเดือนเริ่มต้น (Salary
                        Min)</label>
                    <input type="number" name="salary_min"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เงินเดือนสูงสุด (Salary
                        Max)</label>
                    <input type="number" name="salary_max"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ผู้อนุมัติขั้นต้น (Manager Approver)</label>
                    <select name="approver_manager_id"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all select2-approver"
                        required>
                        <option value="">เลือกผู้อนุมัติ</option>
                        @foreach($approvers as $approver)
                            <option value="{{ $approver->id }}">{{ $approver->fullname }} ({{ $approver->position }})</option>
                        @endforeach
                    </select>
                    @error('approver_manager_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">ผู้อนุมัติสูงสุด (Executive Approver)</label>
                    <select name="approver_executive_id"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all select2-approver"
                        required>
                        <option value="">เลือกผู้อนุมัติ</option>
                        @foreach($approvers as $approver)
                            <option value="{{ $approver->id }}">{{ $approver->fullname }} ({{ $approver->position }})</option>
                        @endforeach
                    </select>
                    @error('approver_executive_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">เหตุผลในการเปิดรับ (Reason)</label>
                <textarea name="reason" rows="3"
                    class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">รายละเอียดงาน (Job
                    Description)</label>
                <textarea name="job_description" rows="5"
                    class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">คุณสมบัติ (Qualification)</label>
                <textarea name="qualification" rows="5"
                    class="w-full bg-gray-50 dark:bg-kumwell-dark border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-kumwell-red transition-all"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="reset"
                    class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all font-semibold">
                    ยกเลิก
                </button>
                <button type="submit"
                    class="px-8 py-2.5 rounded-xl bg-kumwell-red hover:bg-red-700 text-white font-bold shadow-lg shadow-red-500/30 transition-all active:scale-95">
                    ส่งคำขอ
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-approver').select2({
                placeholder: "เลือกผู้อนุมัติ",
                allowClear: false,
                width: '100%'
            });
        });
    </script>
    <style>
        .select2-container--default .select2-selection--single {
            background-color: rgb(249 250 251 / var(--tw-bg-opacity)); /* bg-gray-50 */
            border: 1px solid rgb(229 231 235 / var(--tw-border-opacity)); /* border-gray-200 */
            border-radius: 0.75rem; /* rounded-xl */
            height: 42px;
            display: flex;
            align-items: center;
        }
        .dark .select2-container--default .select2-selection--single {
            background-color: #121418; /* bg-kumwell-dark */
            border-color: rgb(55 65 81 / var(--tw-border-opacity)); /* border-gray-700 */
            color: white;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: inherit;
            padding-left: 1rem;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: rgb(209 213 219); /* text-gray-300 */
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