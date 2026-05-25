@extends('layouts.sidebar')
@section('title', 'รายการฝึกอบรมและพัฒนาทักษะ')
@section('content')
    <div class="container mx-auto px-4 py-3">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex-grow">
                รายการข้อมูลการฝึกอบรม
            </h1>
            <a href="{{ route('backend.training.create') }}" class="btn btn-primary btn-sm w-full md:w-auto shadow-sm">
                <i class="fa-solid fa-plus mr-1"></i> เพิ่มข้อมูล
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500 rounded-r-xl">
                <p class="text-sm text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead
                        class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left w-16">ลำดับ</th>
                            <th class="px-6 py-3 text-left">หัวข้อการฝึกอบรม</th>
                            <th class="px-6 py-3 text-left">ชั่วโมง</th>
                            <th class="px-6 py-3 text-left">รูปแบบ</th>
                            <th class="px-6 py-3 text-left">วันที่เริ่ม</th>
                            <th class="px-6 py-3 text-left">วันที่สิ้นสุด</th>
                            <th class="px-6 py-3 text-left">หน่วยงาน</th>
                            <th class="px-6 py-3 text-left">สถานะ</th>
                            <th class="px-6 py-3 text-center w-28">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($trainings as $training)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $trainings->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        @if($training->image)
                                            <div class="w-12 h-12 flex-shrink-0 mr-3">
                                                <img class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-700" src="{{ asset('images/training/' . $training->image) }}" alt="" loading="lazy">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 flex-shrink-0 mr-3 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                                                <i class="fa-solid fa-image text-xl"></i>
                                            </div>
                                        @endif
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $training->branch }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">{{ $training->hours }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">{{ $training->format }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $training->start_date }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $training->end_date }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $training->department }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <span
                                        class="inline-block px-2 py-1 rounded text-xs {{ $training->status == 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $training->status == 'available' ? 'Available' : 'Full' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center space-x-2 whitespace-nowrap">
                                    <a href="{{ route('backend.training.course.applicants', $training->id) }}"
                                        class="btn btn-info btn-sm btn-square text-white shadow-sm" title="รายชื่อคนสมัคร">
                                        <i class="fa-solid fa-users"></i>
                                    </a>
                                    <a href="{{ route('backend.training.edit', $training->id) }}"
                                        class="btn btn-warning btn-sm btn-square text-white shadow-sm" title="แก้ไข">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('backend.training.destroy', $training->id) }}" method="POST"
                                        class="inline-block" onsubmit="return confirm('คุณต้องการลบข้อมูลนี้ใช่หรือไม่?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-error btn-sm btn-square text-white shadow-sm"
                                            title="ลบ">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-10 text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-inbox text-4xl mb-3 opacity-50"></i>
                                        <p>ไม่พบรายการข้อมูลการฝึกอบรม</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">
            {{ $trainings->links() }}
        </div>
    </div>
@endsection