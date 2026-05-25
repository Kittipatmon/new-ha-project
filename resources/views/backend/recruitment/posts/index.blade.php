@extends('layouts.sidebar')

@section('title', 'จัดการประกาศรับสมัครงาน')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold dark:text-white text-gray-800">ประกาศรับสมัครงาน (Job Postings)</h2>
            <a href="{{ route('backend.recruitment.posts.create') }}"
                class="bg-kumwell-red hover:bg-red-700 text-white px-4 py-2 rounded-xl transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                สร้างประกาศใหม่
            </a>
        </div>

        <div
            class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">ชื่อประกาศ</th>
                            <th class="px-6 py-4 font-semibold">แผนก</th>
                            <th class="px-6 py-4 font-semibold">จำนวน</th>
                            <th class="px-6 py-4 font-semibold">สถานะ</th>
                            <th class="px-6 py-4 font-semibold text-center">ระยะเวลาประกาศ (Start - End)</th>
                            <th class="px-6 py-4 font-semibold text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                        @forelse($posts as $post)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $post->title }}</div>
                                    <div class="text-xs text-gray-400">{{ $post->position_name ?: ($post->jobPosition?->position_name ?? 'N/A') }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $post->department?->department_fullname ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-bold text-center">{{ $post->vacancy }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                @if($post->publish_status == 'published') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($post->publish_status == 'closed') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                                @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400 @endif">
                                        {{ ucfirst($post->publish_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-center">
                                    <div class="font-medium">
                                        {{ $post->start_date ? $post->start_date->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        ถึง {{ $post->end_date ? $post->end_date->format('d/m/Y') : 'ไม่มีกำหนด' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('backend.recruitment.posts.edit', ['jobPost' => $post->id]) }}"
                                        class="text-yellow-500 hover:text-yellow-700 transition-colors">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('backend.recruitment.posts.destroy', ['jobPost' => $post->id]) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors"
                                            onclick="return confirm('ยืนยันการลบประกาศ?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    ยังไม่มีข้อมูลประกาศรับสมัครงาน
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($posts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection