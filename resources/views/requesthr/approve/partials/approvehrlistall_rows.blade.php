@forelse ($hrrequests as $request)
<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
    <td class="px-6 py-4">
        <span class="font-medium text-gray-900 dark:text-gray-200">{{ $request->request_code }}</span>
    </td>
    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ optional($request->user)->employee_code }}</td>
    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ optional($request->user)->fullname }}</td>
    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
        <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ optional($request->category)->name_th ?? '-' }}</span>
    </td>
    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ optional($request->type)->name_th ?? '-' }}</td>
    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ optional($request->subtype)->name_th ?? '-' }}</td>
    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ optional($request->created_at)->format('d/m/Y') }}</td>
    <td class="px-6 py-4">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border badge {{ $request->status_color }}">
            {{ $request->status_label ?? $request->status }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <a href="{{ route('requesthr.detailhr', $request->hr_request_id ) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all group-hover:shadow-sm dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800 dark:hover:bg-indigo-900/50">
            <i class="fas fa-search text-[10px]"></i>
            รายละเอียด
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center justify-center space-y-3">
            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-search text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">ไม่พบข้อมูลคำร้อง</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">ลองปรับเปลี่ยนตัวกรองการค้นหาด้านบนดูอีกครั้ง</p>
        </div>
    </td>
</tr>
@endforelse