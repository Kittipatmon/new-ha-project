@extends('layouts.hrrequest.app')

@section('content')
    <div class="px-3 sm:px-6 lg:px-8 py-6 sm:py-8 pb-16">
        <div class="max-w-7xl mx-auto">

            <!-- Breadcrumb -->
            <nav class="text-sm mb-6 font-light">
                <ol class="list-none p-0 inline-flex flex-wrap items-center">
                    <li class="flex items-center">
                        <a href="{{ route('welcome') }}" class="hover:text-gray-700">Home</a>
                        <svg class="fill-current w-3 h-3 mx-2 sm:mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path
                                d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                        </svg>
                    </li>
                    <li>
                        <span class="text-red-500 font-medium">Request HR</span>
                    </li>
                </ol>
            </nav>

            <!-- Main Card Container -->
            <div
                class="rounded-2xl sm:rounded-[2.5rem] p-4 sm:p-8 md:p-12 pb-8 sm:pb-12 bg-white dark:bg-gray-800 shadow-[0_16px_40px_-12px_rgba(0,0,0,0.08)] w-full relative overflow-visible border border-slate-100 dark:border-gray-700/50">

                <div
                    class="flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 mb-6 border-b border-gray-200 dark:border-gray-100/60 pb-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-red-100 p-2 rounded-lg text-red-600 shrink-0">
                                <i class="fa-solid fa-file-signature text-xl"></i>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-red-500 tracking-tight">ระบบ Request HR</h1>
                        </div>
                        <p class="text-gray-500 dark:text-white text-sm sm:text-base font-light">จัดการคำขอ แจ้งเปลี่ยนแปลงแก้ไขเวลา และติดตามสถานะ</p>
                    </div>

                    <div
                        class="flex flex-wrap sm:flex-nowrap items-center justify-between sm:justify-start gap-2.5 sm:gap-4 text-xs font-medium text-gray-500 dark:bg-gray-800 px-3 sm:px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:text-gray-300 w-full sm:w-auto">
                        <div class="flex items-center gap-1.5 whitespace-nowrap">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500 shrink-0"></span> รออนุมัติ/ตรวจสอบ
                        </div>
                        <div class="flex items-center gap-1.5 whitespace-nowrap">
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 shrink-0"></span> อยู่ระหว่างรอดำเนินการ
                        </div>
                        <div class="flex items-center gap-1.5 whitespace-nowrap">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-600 shrink-0"></span> เสร็จสิ้น
                        </div>
                    </div>
                </div>

                <div class="flex justify-center items-center my-6 sm:my-8">
                    <a href="{{ route('requesthr.index') }}" class="w-full sm:max-w-md">
                        <button
                            class="group relative w-full flex items-center justify-center gap-3 sm:gap-4 px-6 sm:px-10 py-4 sm:py-5 bg-gradient-to-br from-red-600 via-red-500 to-rose-500 text-white rounded-2xl sm:rounded-3xl shadow-[0_15px_30px_-8px_rgba(220,38,38,0.4)] hover:shadow-[0_20px_40px_-10px_rgba(220,38,38,0.6)] hover:-translate-y-1 active:scale-95 transition-all duration-300">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-md group-hover:scale-110 transition-transform shrink-0">
                                <i class="fa-solid fa-paper-plane text-lg sm:text-xl"></i>
                            </div>
                            <div class="text-left flex-1 sm:flex-none">
                                <div class="text-[10px] sm:text-xs font-bold uppercase tracking-widest opacity-80 mb-0.5">Start New</div>
                                <div class="text-lg sm:text-xl font-black tracking-tight">สร้างคำขอใหม่</div>
                            </div>
                            <i class="fa-solid fa-arrow-right ml-auto sm:ml-2 opacity-80 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300"></i>
                        </button>
                    </a>
                </div>

                <div class="flex flex-wrap justify-center gap-4 sm:gap-6 mt-8 sm:mt-12">
                    @if(Auth::check() && Auth::user()->isHrOrAdmin())
                        <div class="dropdown dropdown-bottom w-full flex-1 min-w-[280px] max-w-[400px]">
                            <div tabindex="0" role="button"
                                class="group flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 sm:py-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-red-500/50 hover:bg-red-50/50 dark:hover:bg-red-500/5 transition-all duration-300 shadow-sm hover:shadow-lg w-full">

                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:rotate-6 shrink-0">
                                    <i class="fa-solid fa-file-circle-check text-xl sm:text-2xl"></i>
                                </div>

                                <div class="flex-1 text-left min-w-0">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-red-500 transition-colors truncate">Reports</div>
                                    <div class="text-xs sm:text-sm font-black text-slate-700 dark:text-gray-100 truncate">รอ HR ตรวจสอบ</div>
                                </div>

                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black shadow-sm" title="ตัวเลขจำนวนรอการตรวจสอบ">
                                        {{ $hrrequestapprovehrcount }}
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-300 group-hover:text-red-500 transition-all group-hover:translate-y-0.5"></i>
                                </div>
                            </div>

                            <ul tabindex="-1" class="dropdown-content menu p-3 shadow-2xl bg-white dark:bg-gray-800 rounded-2xl w-72 sm:w-80 max-w-[calc(100vw-2rem)] mt-2 border border-slate-100 dark:border-gray-700 z-[100] animate-fade-in">
                                <li class="menu-title px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Report Type</li>
                                <li>
                                    <a href="{{ route('approve.approvehrlist') }}" class="flex items-center gap-3 rounded-xl py-3 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors shrink-0">
                                            <i class="fa-regular fa-file-lines text-base text-slate-400 group-hover:text-red-500"></i>
                                        </div>
                                        <div class="flex-1 font-bold text-xs sm:text-sm truncate">รายการที่รอตรวจสอบ</div>
                                        <span class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black shrink-0">{{ $hrrequestapprovehrcount }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('approve.approvehrlistall') }}" class="flex items-center gap-3 rounded-xl py-3 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors shrink-0">
                                            <i class="fa-solid fa-database text-base text-slate-400 group-hover:text-red-500"></i>
                                        </div>
                                        <div class="flex-1 font-bold text-xs sm:text-sm truncate">รายการคำร้องขอทั้งหมด</div>
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-gray-300 rounded-lg text-xs font-black shrink-0">{{ $hrrequestCounts }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @php
                        use App\Models\hrrequest\HrRequests;
                    @endphp

                    @if(HrRequests::where('approver_manager_id', Auth::id())->count() > 0)
                        <div class="dropdown dropdown-bottom w-full flex-1 min-w-[280px] max-w-[400px]">
                            <div tabindex="0" role="button"
                                class="group flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 sm:py-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-red-500/50 hover:bg-red-50/50 dark:hover:bg-red-500/5 transition-all duration-300 shadow-sm hover:shadow-lg w-full">

                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:rotate-6 shrink-0">
                                    <i class="fa-solid fa-users-gear text-xl sm:text-2xl"></i>
                                </div>

                                <div class="flex-1 text-left min-w-0">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-red-500 transition-colors truncate">Management</div>
                                    <div class="text-xs sm:text-sm font-black text-slate-700 dark:text-gray-100 truncate">รออนุมัติ</div>
                                </div>

                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black shadow-sm" title="ตัวเลขจำนวนรอการอนุมัติ">
                                        {{ $hrrequestapprovemanacount }}
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-300 group-hover:text-red-500 transition-all group-hover:translate-y-0.5"></i>
                                </div>
                            </div>

                            <ul tabindex="-1" class="dropdown-content menu p-3 shadow-2xl bg-white dark:bg-gray-800 rounded-2xl w-72 sm:w-80 max-w-[calc(100vw-2rem)] mt-2 border border-slate-100 dark:border-gray-700 z-[100] animate-fade-in">
                                <li class="menu-title px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Management Action</li>
                                <li>
                                    <a href="{{ route('approve.approvemanalist') }}" class="flex items-center gap-3 rounded-xl py-3 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors shrink-0">
                                            <i class="fa-regular fa-file-lines text-base text-slate-400 group-hover:text-red-500"></i>
                                        </div>
                                        <div class="flex-1 font-bold text-xs sm:text-sm truncate">รายการที่รออนุมัติ</div>
                                        <span class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black shrink-0">{{ $hrrequestapprovemanacount }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <div class="dropdown dropdown-bottom w-full flex-1 min-w-[280px] max-w-[400px]">
                        <div tabindex="0" role="button"
                            class="group flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 sm:py-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-red-500/50 hover:bg-red-50/50 dark:hover:bg-red-500/5 transition-all duration-300 shadow-sm hover:shadow-lg w-full">

                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:rotate-6 shrink-0">
                                <i class="fa-solid fa-chart-line text-xl sm:text-2xl"></i>
                            </div>

                            <div class="flex-1 text-left min-w-0">
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-red-500 transition-colors truncate">Statistics</div>
                                <div class="text-xs sm:text-sm font-black text-slate-700 dark:text-gray-100 truncate">ข้อมูลคำขอ</div>
                            </div>

                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-black shadow-sm" title="ตัวเลขจำนวนรอรอดำเนินการ">
                                    {{ $hrrequests }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-gray-300 group-hover:text-red-500 transition-all group-hover:translate-y-0.5"></i>
                            </div>
                        </div>

                        <ul tabindex="-1" class="dropdown-content menu p-3 shadow-2xl bg-white dark:bg-gray-800 rounded-2xl w-72 sm:w-80 max-w-[calc(100vw-2rem)] mt-2 border border-slate-100 dark:border-gray-700 z-[100] animate-fade-in">
                            <li class="menu-title px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Data List</li>
                            <li>
                                <a href="{{ route('requesthr.list') }}" class="flex items-center gap-3 rounded-xl py-3 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors shrink-0">
                                        <i class="fa-regular fa-file-lines text-base text-slate-400 group-hover:text-red-500"></i>
                                    </div>
                                    <div class="flex-1 font-bold text-xs sm:text-sm truncate">รายการที่รอดำเนินการ</div>
                                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-black shrink-0">{{ $hrrequests }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('requesthr.listall') }}" class="flex items-center gap-3 rounded-xl py-3 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors shrink-0">
                                        <i class="fa-solid fa-clock-rotate-left text-base text-slate-400 group-hover:text-red-500"></i>
                                    </div>
                                    <div class="flex-1 font-bold text-xs sm:text-sm truncate">รายการทั้งหมด</div>
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-gray-300 rounded-lg text-xs font-black shrink-0">{{ $hrrequestsCount }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection