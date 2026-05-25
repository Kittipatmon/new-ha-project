@extends('layouts.training.app')

@section('content')
    <style>
        /* Custom Table Styling */
        .table-rounded {
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            /* border-slate-100 */
        }

        .dark .table-rounded {
            border-color: rgba(255, 255, 255, 0.05); /* dark:border-white/5 */
        }

        .table-header {
            background: linear-gradient(to right, #ef4444, #dc2626); /* from-red-500 to-red-600 */
            color: white;
        }

        .table-row {
            border-bottom: 1px dashed #e2e8f0;
        }

        .dark .table-row {
            border-bottom: 1px dashed #334155;
        }

        .table-row:last-child {
            border-bottom: none;
        }
    </style>

    <div class="min-h-screen p-6 pt-10 pb-20 bg-gray-50 dark:bg-[#15171e] text-slate-800 dark:text-gray-200">
        <div class="max-w-8xl mx-auto px-4">
            <!-- Breadcrumbs -->
            <div class="flex items-center text-sm mb-4 space-x-2">
                <a href="{{ route('welcome') }}"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
                <span class="text-red-500 font-medium">ลงทะเบียน</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-10 text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">กำหนดการฝึกอบรม</h1>
                <div class="flex items-center justify-center">
                    <div class="h-[2px] bg-gray-300 dark:bg-gray-600 w-16"></div>
                    <div class="h-2 w-2 rounded-full bg-red-600 mx-3 shadow-sm shadow-red-500/50"></div>
                    <div class="h-[2px] bg-gray-300 dark:bg-gray-600 w-16"></div>
                </div>
                <p class="text-slate-500 dark:text-slate-400 mt-4">ตรวจสอบกำหนดการและสมัครเข้าร่วมฝึกอบรมเพื่อพัฒนาทักษะของคุณ</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500 rounded-r-xl">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                        <h3 class="font-bold text-green-800 dark:text-green-400">{{ session('success') }}</h3>
                    </div>
                </div>
            @endif

            <!-- Filter Form Card -->
            <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 p-6 mb-8 relative overflow-hidden">
                <div class="absolute top-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-600 left-0"></div>

                <form id="searchForm" action="{{ route('training.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 relative">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="ค้นหาชื่อสาขา / หลักสูตร"
                            class="w-full pl-10 pr-32 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-slate-800 dark:text-white rounded-xl py-3 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                        
                        <!-- Loading Indicator -->
                        <div id="searchLoading" class="hidden absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-sm font-medium bg-gray-50 dark:bg-gray-800 pl-2">
                            <i class="fa-solid fa-circle-notch fa-spin mr-1"></i> กำลังค้นหา...
                        </div>
                    </div>
                    <div class="w-full md:w-48">
                        <select name="format" id="formatSelect"
                            class="w-full border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1E2129] text-slate-800 dark:text-white rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors appearance-none cursor-pointer text-center">
                            <option value="">รูปแบบทั้งหมด</option>
                            <option value="ปกติ" {{ request('format') == 'ปกติ' ? 'selected' : '' }}>ปกติ (On-site)</option>
                            <option value="ออนไลน์" {{ request('format') == 'ออนไลน์' ? 'selected' : '' }}>ออนไลน์ (Online)</option>
                        </select>
                    </div>
                    <div class="w-full md:w-64">
                        <select name="department" id="departmentSelect"
                            class="w-full border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1E2129] text-slate-800 dark:text-white rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors appearance-none cursor-pointer text-center">
                            <option value="">- แสดงหน่วยงานทั้งหมด -</option>
                            <option value="สถาบันพัฒนาฝีมือแรงงาน 27 สมุทรสาคร" {{ request('department') == 'สถาบันพัฒนาฝีมือแรงงาน 27 สมุทรสาคร' ? 'selected' : '' }}>สถาบันพัฒนาฝีมือแรงงาน 27 สมุทรสาคร</option>
                            <option value="สำนักงานพัฒนาฝีมือแรงงานเลย" {{ request('department') == 'สำนักงานพัฒนาฝีมือแรงงานเลย' ? 'selected' : '' }}>สำนักงานพัฒนาฝีมือแรงงานเลย</option>
                            <option value="สำนักงานพัฒนาฝีมือแรงงานพัทลุง" {{ request('department') == 'สำนักงานพัฒนาฝีมือแรงงานพัทลุง' ? 'selected' : '' }}>สำนักงานพัฒนาฝีมือแรงงานพัทลุง</option>
                            <option value="สำนักงานพัฒนาฝีมือแรงงานบึงกาฬ" {{ request('department') == 'สำนักงานพัฒนาฝีมือแรงงานบึงกาฬ' ? 'selected' : '' }}>สำนักงานพัฒนาฝีมือแรงงานบึงกาฬ</option>
                        </select>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div id="searchLoading" class="hidden absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-red-500 text-sm font-medium">
                        <i class="fa-solid fa-circle-notch fa-spin mr-1"></i> กำลังค้นหา...
                    </div>
                </form>
            </div>

            <div class="flex justify-end mb-4">
                <a href="#" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-bold flex items-center gap-2 group">
                    <i class="fa-solid fa-book-open text-red-500 group-hover:scale-110 transition-transform"></i> คู่มือระบบสมัครฝึกอบรม
                </a>
            </div>

            <!-- Results Container -->
            <div id="resultsContainer">
                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white dark:bg-[#1E2129] table-rounded shadow-xl shadow-slate-200/50 dark:shadow-black/50 relative">
                    <div class="absolute top-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-600 left-0 z-10"></div>
                    <div class="overflow-x-auto relative z-0">
                        <table class="w-full text-center border-collapse">
                            <thead>
                                <tr class="table-header text-sm">
                                    <th class="py-5 px-3 font-semibold border-r border-red-400/30 w-16">ลำดับ</th>
                                    <th class="py-5 px-4 font-semibold border-r border-red-400/30 min-w-[200px] text-center">หัวข้อการฝึกอบรม</th>
                                    <th class="py-5 px-3 font-semibold border-r border-red-400/30 w-28 leading-tight">ระยะเวลา<br><span class="text-xs font-normal opacity-80">(ชั่วโมง)</span></th>
                                    <th class="py-5 px-3 font-semibold border-r border-red-400/30 w-24">รูปแบบ</th>
                                    <th class="py-5 px-3 font-semibold border-r border-red-400/30 w-28 leading-tight">วันเริ่ม<br>ฝึกอบรม</th>
                                    <th class="py-5 px-3 font-semibold border-r border-red-400/30 w-28 leading-tight">วันสิ้นสุด<br>ฝึกอบรม</th>
                                    <th class="py-5 px-4 font-semibold border-r border-red-400/30 min-w-[150px]">หน่วยงานจัดอบรม</th>
                                    <th class="py-5 px-3 font-semibold w-32">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody id="desktopTableBody">
                                @forelse($courses as $course)
                                    <tr class="table-row hover:bg-red-50/50 dark:hover:bg-red-900/10 transition duration-200 group">
                                        <td class="py-4 px-3 text-slate-500 dark:text-slate-400 font-medium border-r border-dashed border-gray-200 dark:border-gray-700">
                                            {{ $courses->firstItem() + $loop->index }}
                                        </td>
                                        <td class="py-5 px-4 text-left border-r border-dashed border-gray-200 dark:border-gray-700">
                                            <div class="flex items-start gap-4">
                                                @if($course['image'])
                                                    <div class="w-20 h-14 flex-shrink-0 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm">
                                                        <img src="{{ asset('images/training/' . $course['image']) }}" class="w-full h-full object-cover" alt="{{ $course['branch'] }}" loading="lazy">
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-bold text-slate-800 dark:text-white mb-1.5 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors">{{ $course['branch'] }}</div>
                                                    <button type="button"
                                                        onclick="showDetails('{{ addslashes($course['branch']) }}', '{{ addslashes($course['details']) }}', '{{ $course['image'] ? asset('images/training/' . $course['image']) : '' }}')"
                                                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded w-fit transition-colors">
                                                        <i class="fa-solid fa-circle-info"></i> รายละเอียด
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-3 text-slate-700 dark:text-slate-300 font-medium border-r border-dashed border-gray-200 dark:border-gray-700">
                                            <span class="bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-lg">{{ $course['hours'] }}</span>
                                        </td>
                                        <td class="py-4 px-3 text-slate-700 dark:text-slate-300 border-r border-dashed border-gray-200 dark:border-gray-700">
                                            <span class="{{ $course['format'] == 'ออนไลน์' ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' }} px-3 py-1 rounded-full text-xs font-bold">
                                                {{ $course['format'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-3 text-slate-600 dark:text-slate-400 text-sm border-r border-dashed border-gray-200 dark:border-gray-700 leading-tight">
                                            <div class="flex flex-col items-center">
                                                <span class="font-bold text-slate-800 dark:text-gray-200">{{ explode(' ', $course['start_date'])[0] }}</span>
                                                <span class="text-xs">{{ explode(' ', $course['start_date'])[1] }} {{ explode(' ', $course['start_date'])[2] }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-3 text-slate-600 dark:text-slate-400 text-sm border-r border-dashed border-gray-200 dark:border-gray-700 leading-tight">
                                            <div class="flex flex-col items-center">
                                                <span class="font-bold text-slate-800 dark:text-gray-200">{{ explode(' ', $course['end_date'])[0] }}</span>
                                                <span class="text-xs">{{ explode(' ', $course['end_date'])[1] }} {{ explode(' ', $course['end_date'])[2] }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-slate-600 dark:text-slate-400 text-sm border-r border-dashed border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-building text-gray-400 dark:text-gray-500"></i>
                                                <span class="text-left leading-tight">{{ $course['department'] }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-3 border-l border-dashed border-gray-200 dark:border-gray-700">
                                            @if($course['status'] == 'available')
                                                @php
                                                    $isApplied = in_array($course['id'], $appliedTrainings ?? []);
                                                @endphp
                                                @if($isApplied)
                                                    <button type="button" onclick="showDocument('{{ $course['document'] ? asset('storage/' . $course['document']) : '' }}', '{{ $course['document_link'] ?? '' }}')"
                                                        class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full text-sm shadow-md shadow-blue-600/20 transition-all hover:-translate-y-0.5 whitespace-nowrap">
                                                        <i class="fa-solid fa-file-pdf"></i> เข้าร่วมอบรม
                                                    </button>
                                                @else
                                                    <a href="{{ route('training.apply', $course['id']) }}"
                                                        class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-full text-sm shadow-md shadow-green-600/20 transition-all hover:-translate-y-0.5">
                                                        สมัครเลย
                                                    </a>
                                                @endif
                                            @else
                                                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-bold py-2 px-5 rounded-full text-sm cursor-not-allowed">
                                                    เต็มแล้ว
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-16 text-center">
                                            <div class="inline-flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                                <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                                                <p class="text-lg">ไม่พบข้อมูลกำหนดการฝึกอบรมในขณะนี้</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-6" id="mobileCardContainer">
                    @forelse($courses as $course)
                        <div class="bg-white dark:bg-[#1E2129] rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden flex flex-col h-full group">
                            @if($course['image'])
                                <div class="relative h-48 overflow-hidden">
                                    <img src="{{ asset('images/training/' . $course['image']) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="{{ $course['branch'] }}" loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-4 left-6">
                                        <span class="text-xs font-bold text-white bg-red-600 px-3 py-1 rounded-full shadow-lg">ลำดับที่ {{ $courses->firstItem() + $loop->index }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="relative h-32 bg-gradient-to-br from-red-50 to-gray-100 dark:from-red-900/20 dark:to-gray-800 flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-300 dark:text-gray-600 text-4xl"></i>
                                    <div class="absolute bottom-4 left-6">
                                        <span class="text-xs font-bold text-white bg-red-600 px-3 py-1 rounded-full shadow-lg">ลำดับที่ {{ $courses->firstItem() + $loop->index }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="p-6">
                                <h3 class="font-bold text-slate-800 dark:text-white text-xl mb-3 leading-tight">{{ $course['branch'] }}</h3>

                                <button type="button"
                                    onclick="showDetails('{{ addslashes($course['branch']) }}', '{{ addslashes($course['details']) }}', '{{ $course['image'] ? asset('images/training/' . $course['image']) : '' }}')"
                                    class="text-xs text-blue-600 dark:text-blue-400 font-bold flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-xl w-fit transition-colors mb-5">
                                    <i class="fa-solid fa-circle-info"></i> ดูรายละเอียดหลักสูตร
                                </button>

                                <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50/50 dark:bg-black/20 p-5 rounded-3xl border border-gray-100 dark:border-white/5 mb-6">
                                    <div class="flex flex-col">
                                        <span class="text-slate-500 dark:text-slate-400 text-xs mb-1 uppercase tracking-wider font-semibold">ระยะเวลา</span>
                                        <span class="font-bold text-slate-800 dark:text-gray-200">{{ $course['hours'] }} ชั่งโมง</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-slate-500 dark:text-slate-400 text-xs mb-1 uppercase tracking-wider font-semibold">รูปแบบ</span>
                                        <span class="{{ $course['format'] == 'ออนไลน์' ? 'text-indigo-600 dark:text-indigo-400' : 'text-emerald-600 dark:text-emerald-400' }} font-bold">{{ $course['format'] }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-slate-500 dark:text-slate-400 text-xs mb-1 uppercase tracking-wider font-semibold">วันเริ่มอบรม</span>
                                        <span class="font-bold text-slate-800 dark:text-gray-200">{{ $course['start_date'] }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-slate-500 dark:text-slate-400 text-xs mb-1 uppercase tracking-wider font-semibold">วันสิ้นสุด</span>
                                        <span class="font-bold text-slate-800 dark:text-gray-200">{{ $course['end_date'] }}</span>
                                    </div>
                                    <div class="col-span-2 pt-3 border-t border-gray-200 dark:border-gray-700/50">
                                        <span class="text-slate-500 dark:text-slate-400 text-xs mb-1 uppercase tracking-wider font-semibold block">หน่วยงานที่รับผิดชอบ</span>
                                        <span class="font-bold text-slate-800 dark:text-gray-200 text-xs">{{ $course['department'] }}</span>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    @if($course['status'] == 'available')
                                        @php
                                            $isApplied = in_array($course['id'], $appliedTrainings ?? []);
                                        @endphp
                                        @if($isApplied)
                                            <button type="button" onclick="showDocument('{{ $course['document'] ? asset('storage/' . $course['document']) : '' }}', '{{ $course['document_link'] ?? '' }}')"
                                                class="w-full flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-blue-600/30 transition-all hover:-translate-y-1">
                                                <i class="fa-solid fa-file-pdf"></i> เข้าสู่หน้าการอบรม
                                            </button>
                                        @else
                                            <a href="{{ route('training.apply', $course['id']) }}"
                                                class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-red-600/30 transition-all hover:-translate-y-1">
                                                สมัครอบรมตอนนี้
                                            </a>
                                        @endif
                                    @else
                                        <button disabled class="w-full text-center bg-gray-200 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold py-4 px-6 rounded-2xl cursor-not-allowed">
                                            หลักสูตรเต็มแล้ว
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-12 text-center">
                            <i class="fa-solid fa-calendar-xmark text-5xl mb-4 text-gray-200 dark:text-gray-700"></i>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">ไม่พบรายการฝึกอบรมที่เปิดรับสมัคร</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($courses->hasPages())
                    <div class="mt-8 flex justify-center pb-2">
                        {{ $courses->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 dark:bg-black/80 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-2xl w-full max-w-2xl transform scale-95 transition-all duration-300 flex flex-col max-h-[90vh] border border-slate-100 dark:border-white/10 relative overflow-hidden" id="modalContent">
            <!-- Decoration -->
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-red-600"></div>

            <div class="p-6 sm:p-8 border-b border-gray-100 dark:border-gray-800 flex justify-between items-start pt-8">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fa-solid fa-circle-info text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white" id="modalTitle">รายละเอียดหลักสูตร</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">ข้อมูลประกอบการพิจารณาสมัครฝึกอบรม</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetails()" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-red-600 transition-colors focus:outline-none">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/50 dark:bg-[#15171e]/50">
                <div id="modalImageContainer" class="hidden mb-6">
                    <img id="modalImage" src="" class="w-full h-auto max-h-64 object-cover rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md" alt="Course Image">
                </div>
                <div class="bg-white dark:bg-gray-800/80 rounded-2xl p-5 sm:p-6 border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <div class="prose dark:prose-invert max-w-none">
                        <h4 class="text-lg font-bold text-red-600 dark:text-red-400 mb-3 border-b border-gray-100 dark:border-gray-700 pb-2">คำอธิบายเพิ่มเติม</h4>
                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed font-light whitespace-pre-line" id="modalText"></p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:px-8 sm:py-5 border-t border-gray-100 dark:border-gray-800 flex justify-end bg-white dark:bg-[#1E2129] rounded-b-3xl">
                <button type="button" onclick="closeDetails()"
                    class="px-8 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-slate-700 dark:text-gray-200 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-800 transition-all shadow-sm">
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // --- Modal Logic ---
            function showDetails(title, text, imageUrl) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalText').textContent = text;

                const imgContainer = document.getElementById('modalImageContainer');
                const imgElement = document.getElementById('modalImage');

                if (imageUrl) {
                    imgElement.src = imageUrl;
                    imgContainer.classList.remove('hidden');
                } else {
                    imgElement.src = '';
                    imgContainer.classList.add('hidden');
                }

                const modal = document.getElementById('detailsModal');
                const content = document.getElementById('modalContent');

                modal.classList.remove('hidden');
                // Prevent body scroll
                document.body.style.overflow = 'hidden';

                // Trigger reflow
                void modal.offsetWidth;

                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');

                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }

            function closeDetails() {
                const modal = document.getElementById('detailsModal');
                const content = document.getElementById('modalContent');

                modal.classList.remove('opacity-100');
                modal.classList.add('opacity-0');

                content.classList.remove('scale-100');
                content.classList.add('scale-95');

                // Restore body scroll
                document.body.style.overflow = '';

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // --- Document Logic ---
            function showDocument(docUrl, linkUrl) {
                if (docUrl) {
                    window.open(docUrl, '_blank');
                } else if (linkUrl) {
                    window.open(linkUrl, '_blank');
                } else {
                    alert('ไม่มีเอกสารประกอบหรือลิงก์สื่อการสอนสำหรับหลักสูตรนี้');
                }
            }

            // Close on clicking outside
            document.getElementById('detailsModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeDetails();
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !document.getElementById('detailsModal').classList.contains('hidden')) {
                    closeDetails();
                }
            });

            // --- AJAX Search Logic ---
            document.addEventListener('DOMContentLoaded', function() {
                const searchForm = document.getElementById('searchForm');
                const searchInput = document.getElementById('searchInput');
                const formatSelect = document.getElementById('formatSelect');
                const departmentSelect = document.getElementById('departmentSelect');
                const resultsContainer = document.getElementById('resultsContainer');
                const searchLoading = document.getElementById('searchLoading');

                let debounceTimer;

                function fetchResults() {
                    // Show loading
                    searchLoading.classList.remove('hidden');
                    
                    const url = new URL(searchForm.action);
                    url.searchParams.append('search', searchInput.value);
                    url.searchParams.append('format', formatSelect.value);
                    url.searchParams.append('department', departmentSelect.value);

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Create a temporary DOM element to parse the returned HTML
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Extract the new results container
                        const newResults = doc.getElementById('resultsContainer');
                        
                        if (newResults) {
                            resultsContainer.innerHTML = newResults.innerHTML;
                        }

                        // Hide loading
                        searchLoading.classList.add('hidden');
                        
                        // Update URL without reloading (optional, for bookmarking)
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                        searchLoading.classList.add('hidden');
                    });
                }

                // Listen for input on search box with debounce
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(fetchResults, 500); // 500ms delay
                });

                // Listen for changes on dropdowns
                formatSelect.addEventListener('change', fetchResults);
                departmentSelect.addEventListener('change', fetchResults);

                // Listen for clicks on pagination links
                resultsContainer.addEventListener('click', function(e) {
                    const paginationLink = e.target.closest('nav[role="navigation"] a');
                    if (paginationLink) {
                        e.preventDefault();
                        const pageUrl = new URL(paginationLink.href);
                        // Extract query string to update the form fields as well if needed? No, just pass the URL directly
                        searchInput.value = pageUrl.searchParams.get('search') || '';
                        formatSelect.value = pageUrl.searchParams.get('format') || '';
                        departmentSelect.value = pageUrl.searchParams.get('department') || '';
                        
                        // Show loading
                        searchLoading.classList.remove('hidden');
                        
                        fetch(pageUrl.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newResults = doc.getElementById('resultsContainer');
                            
                            if (newResults) {
                                resultsContainer.innerHTML = newResults.innerHTML;
                            }

                            searchLoading.classList.add('hidden');
                            window.history.pushState({}, '', pageUrl);
                            
                            // Scroll up slightly
                            window.scrollTo({
                                top: document.getElementById('searchForm').offsetTop - 20,
                                behavior: 'smooth'
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching pagination:', error);
                            searchLoading.classList.add('hidden');
                        });
                    }
                });

                // Prevent default form submission
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetchResults();
                });
            });
        </script>

        <style>
            /* Custom Scrollbar for Modal */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #cbd5e1;
                border-radius: 20px;
            }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #475569;
            }
        </style>
    @endpush
@endsection