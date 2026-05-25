@extends('layouts.hrrequest.app')

@section('content')
<div class="w-full mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6 font-medium">
        <a href="/" class="hover:text-red-500 transition-colors">Home</a>
        <span class="text-gray-400">&gt;</span>
        <a href="{{ route('request.hr') }}" class="hover:text-red-500 transition-colors">Request HR</a>
        <span class="text-gray-400">&gt;</span>
        <span class="text-red-600">รายงานข้อมูลทั้งหมด</span>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden transition-all">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 dark:bg-purple-500/20 p-2 rounded-lg text-purple-600 dark:text-purple-500">
                    <i class="fas fa-list text-lg"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">ประวัติคำร้องขอทั้งหมด</h2>
            </div>
            <span class="bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400 py-1 px-3 rounded-full text-xs font-bold">
                {{ $hrrequests->count() }} รายการ
            </span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium">เลขที่รายการ</th>
                        <th scope="col" class="px-6 py-4 font-medium">ชื่อ-สกุล</th>
                        <th scope="col" class="px-6 py-4 font-medium">หมวดหมู่คำร้อง</th>
                        <th scope="col" class="px-6 py-4 font-medium">ประเภทคำร้อง</th>
                        <th scope="col" class="px-6 py-4 font-medium">ประเภทย่อย</th>
                        <th scope="col" class="px-6 py-4 font-medium">วันที่ส่งคำร้อง</th>
                        <th scope="col" class="px-6 py-4 font-medium">สถานะ</th>
                        <th scope="col" class="px-6 py-4 font-medium text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($hrrequests as $request)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900 dark:text-gray-200">{{ $request->request_code }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                            {{ $request->user->fullname }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ $request->category->name_th ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $request->type->name_th ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $request->subtype->name_th ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $request->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border badge {{ $request->status_color }}">
                                {{ $request->status_label }}
                            </span>
                            @if($request->approver_hr_status == 3)
                            <div class="text-orange-600 dark:text-orange-400 text-xs mt-1 font-medium bg-orange-50 dark:bg-orange-500/10 px-2 py-0.5 rounded inline-block">
                                (ส่งกลับโดยฝ่ายบุคคล)
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('requesthr.detailUser', $request->hr_request_id ) }}" 
                                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-500 border border-transparent rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all group-hover:shadow-md" title="ดูรายละเอียด">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                                <a href="#" 
                                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-500 border border-transparent rounded-lg shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all group-hover:shadow-md" title="ยกเลิกคำร้อง">
                                    <i class="fas fa-times text-[10px]"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-history text-2xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">ไม่พบประวัติคำร้อง</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">คุณยังไม่มีประวัติการส่งคำร้องใดๆ ในระบบ</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection