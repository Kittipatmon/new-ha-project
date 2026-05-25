@extends('layouts.recruitment.app')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-20">
        <!-- Hero/Header Section -->
        <div class="bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">รายงานคำขอเปิดรับสมัครพนักงาน</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Recruitment Requests Management</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Stats/Overview (Optional but adds premium feel) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">คำขอทั้งหมด</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $requests->total() }}</div>
                </div>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="text-xs font-bold text-yellow-500 uppercase tracking-wider mb-2">รออนุมัติ</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Models\Recruitment\RecruitmentRequest::whereIn('status', ['pending_manager', 'pending_executive'])->count() }}
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-slate-700/30 text-gray-400 dark:text-gray-500 text-[11px] font-bold uppercase tracking-[0.1em]">
                                <th class="px-8 py-5 border-b border-gray-100 dark:border-slate-700">เลขที่คำขอ</th>
                                <th class="px-6 py-5 border-b border-gray-100 dark:border-slate-700">ตำแหน่ง</th>
                                <th class="px-6 py-5 border-b border-gray-100 dark:border-slate-700">แผนก</th>
                                <th class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 text-center">จำนวน</th>
                                <th class="px-6 py-5 border-b border-gray-100 dark:border-slate-700">สถานะ</th>
                                <th class="px-6 py-5 border-b border-gray-100 dark:border-slate-700">ผู้ขอ</th>
                                <th class="px-8 py-5 border-b border-gray-100 dark:border-slate-700 text-right">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                            @forelse($requests as $request)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/20 transition-colors group">
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $request->request_no }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $request->position_name ?: ($request->jobPosition?->position_name ?? 'N/A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $request->department?->department_shortname ?? ($request->department?->department_fullname ?? 'N/A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $request->headcount }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        @php
                                            $statusClasses = [
                                                'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                'pending_manager' => 'bg-yellow-101 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                'pending_executive' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                            ];
                                            $currentClass = $statusClasses[$request->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400';
                                            $statusText = [
                                                'pending_manager' => 'รอหัวหน้างานอนุมัติ',
                                                'pending_executive' => 'รอผู้บริหารอนุมัติ',
                                                'approved' => 'อนุมัติแล้ว',
                                                'rejected' => 'ไม่อนุมัติ',
                                            ][$request->status] ?? $request->status;
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $currentClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $request->requester?->fullname ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('recruitment.request_show', $request->id) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 hover:bg-kumwell-red hover:text-white transition-all">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-20 text-center text-gray-400 dark:text-gray-500">
                                        ไม่พบข้อมูลคำขอเปิดรับสมัคร
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="px-8 py-6 border-t border-gray-50 dark:border-slate-700">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
