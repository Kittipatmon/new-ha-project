<div id="applications-table-container">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold">ผู้สมัคร</th>
                    <th class="px-6 py-4 font-semibold">ตำแหน่งที่สมัคร</th>
                    <th class="px-6 py-4 font-semibold">แผนก</th>
                    <th class="px-6 py-4 font-semibold">วันที่สมัคร</th>
                    <th class="px-6 py-4 font-semibold">สถานะ</th>
                    <th class="px-6 py-4 font-semibold text-right">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                @forelse($applications as $app)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-900 dark:text-white font-medium">
                                    {{ $app->applicant->first_name }} {{ $app->applicant->last_name }}
                                </span>
                                @if($app->total_applications > 1)
                                    <span class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                        สมัครแล้ว {{ $app->total_applications }} ครั้ง
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">{{ $app->applicant->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 dark:text-gray-300">
                                {{ $app->jobPost->position_name ?? ($app->jobPost->jobPosition->position_name ?? '-') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            {{ $app->jobPost->department->department_fullname ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            {{ $app->applied_at ? $app->applied_at->translatedFormat('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase 
                                        @if($app->status == 'new') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($app->status == 'screening') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @elseif($app->status == 'interview') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                        @elseif($app->status == 'offered') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($app->status == 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('backend.recruitment.applications.show', $app->id) }}"
                                class="text-kumwell-red hover:text-red-700 font-bold">
                                ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            ยังไม่มีรายชื่อผู้สมัคร
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800" id="pagination-links">
            {{ $applications->appends(request()->all())->links() }}
        </div>
    @endif
</div>