@extends('layouts.sidebar')

@section('title', 'รายการคำขอเปิดรับสมัครพนักงาน')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold dark:text-white text-gray-800">คำขอเปิดรับสมัครพนักงาน (Recruitment Requests)</h2>
            <a href="{{ route('backend.recruitment.requests.create') }}"
                class="bg-kumwell-red hover:bg-red-700 text-white px-4 py-2 rounded-xl transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                สร้างคำขอใหม่
            </a>
        </div>

        <div
            class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">เลขที่คำขอ</th>
                            <th class="px-6 py-4 font-semibold">ตำแหน่ง</th>
                            <th class="px-6 py-4 font-semibold">แผนก</th>
                            <th class="px-6 py-4 font-semibold">จำนวน</th>
                            <th class="px-6 py-4 font-semibold">สถานะ</th>
                            <th class="px-6 py-4 font-semibold">ผู้ขอ</th>
                            <th class="px-6 py-4 font-semibold text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                        @forelse($requests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $request->request_no }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $request->position_name ?: ($request->jobPosition?->position_name ?? 'N/A') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $request->department?->department_fullname ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-bold">{{ $request->headcount }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                @if($request->status == 'approved') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($request->status == 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                                @elseif($request->status == 'pending_manager' || $request->status == 'pending_executive') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                                @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $request->requester?->fullname ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('backend.recruitment.requests.show', $request->id) }}"
                                        class="text-blue-500 hover:text-blue-700 transition-colors">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    ยังไม่มีข้อมูลคำขอเปิดอัตรา
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection