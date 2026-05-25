@extends('layouts.hrrequest.app')

@section('content')
    <div class="p-8">
        <div class="max-w-7xl mx-auto">

            <!-- Breadcrumb -->
            <nav class="text-sm mb-6 font-light">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <a href="{{ route('welcome') }}" class="hover:text-gray-700">Home</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
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
                class="rounded-[2.5rem] p-8 md:p-12 pb-80 bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] w-full relative overflow-visible border border-white dark:border-gray-700/50">

                <div
                    class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-6 border-b border-gray-200 dark:border-gray-100/60 pb-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-red-100 p-2 rounded-lg text-red-600">
                                <i class="fa-solid fa-file-signature text-xl"></i>
                            </div>
                            <h1 class="text-3xl font-bold text-red-500 tracking-tight">ระบบ Request HR</h1>
                        </div>
                        <p class="text-gray-500 dark:text-white text-base font-light pl-1">จัดการคำขอ แจ้งเปลี่ยนแปลงแก้ไขเวลา และติดตามสถานะ</p>
                    </div>

                    <div
                        class="flex gap-3 text-xs font-medium text-gray-500 dark:bg-gray-800 px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-200/20 dark:text-gray-300">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span> รออนุมัติ/ตรวจสอบ
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span> อยู่ระหว่างรอดำเนินการ
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-600"></span> เสร็จสิ้น
                        </div>
                    </div>
                </div>

                <div class="flex justify-center items-center">
                    <a href="{{ route('requesthr.index') }}" class="w-full max-w-sm">
                        <button
                            class="group relative w-full flex items-center justify-center gap-4 px-10 py-5 bg-gradient-to-br from-red-600 via-red-500 to-rose-500 text-white rounded-3xl shadow-[0_20px_40px_-10px_rgba(220,38,38,0.4)] hover:shadow-[0_25px_50px_-12px_rgba(220,38,38,0.6)] hover:-translate-y-1.5 active:scale-95 transition-all duration-300">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-md group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-paper-plane text-xl"></i>
                            </div>
                            <div class="text-left">
                                <div class="text-xs font-bold uppercase tracking-widest opacity-80 mb-0.5">Start New</div>
                                <div class="text-xl font-black tracking-tight">สร้างคำขอใหม่</div>
                            </div>
                            <i class="fa-solid fa-arrow-right ml-auto opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                        </button>
                    </a>
                </div>
                <div class="flex flex-wrap justify-center items-center gap-4 mt-12 px-4">
                    @if(Auth::check() && (Auth::user()->hr_status == '0' || Auth::user()->employee_code == '11648'))
                        <div class="dropdown dropdown-bottom">
                            <div tabindex="0" role="button"
                                class="group flex items-center gap-4 px-5 py-4 rounded-[1.5rem] bg-white dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 hover:border-red-500/50 hover:bg-red-50/50 dark:hover:bg-red-500/5 transition-all duration-300 shadow-sm hover:shadow-xl hover:-translate-y-1 min-w-[280px]">

                                <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-red-500/20 group-hover:rotate-6">
                                    <i class="fa-solid fa-file-circle-check text-2xl"></i>
                                </div>

                                <div class="flex-1 text-left">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-red-500 transition-colors">Reports</div>
                                    <div class="text-sm font-black text-slate-700 dark:text-gray-100">รอ HR ตรวจสอบ</div>
                                </div>

                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-3 py-1 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black shadow-sm" title="ตัวเลขจำนวนรอการตรวจสอบ">
                                        {{ $hrrequestapprovehrcount }}
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-300 group-hover:text-red-500 transition-all group-hover:translate-y-0.5"></i>
                                </div>
                            </div>

                            <ul tabindex="-1" class="dropdown-content menu p-3 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-3xl w-80 mt-4 border border-white dark:border-gray-700/50 z-[100] animate-fade-in">
                                <li class="menu-title px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Report Type</li>
                                <li>
                                    <a href="{{ route('approve.approvehrlist') }}" class="flex items-center gap-3 rounded-2xl py-4 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors">
                                            <i class="fa-regular fa-file-lines text-lg text-slate-400 group-hover:text-red-500"></i>
                                        </div>
                                        <div class="flex-1 font-bold text-sm">รายการที่รอตรวจสอบ</div>
                                        <span class="px-2.5 py-1 bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black">{{ $hrrequestapprovehrcount }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('approve.approvehrlistall') }}" class="flex items-center gap-3 rounded-2xl py-4 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors">
                                            <i class="fa-solid fa-database text-lg text-slate-400 group-hover:text-red-500"></i>
                                        </div>
                                        <div class="flex-1 font-bold text-sm">รายการคำร้องขอทั้งหมด</div>
                                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-gray-300 rounded-lg text-xs font-black">{{ $hrrequestCounts }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @php
                        use App\Models\hrrequest\HrRequests;
                    @endphp

                    @if(HrRequests::where('approver_manager_id', Auth::id())->count() > 0)
                        <div class="dropdown dropdown-bottom">
                            <div tabindex="0" role="button"
                                class="group flex items-center gap-4 px-5 py-4 rounded-[1.5rem] bg-white dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 hover:border-red-500/50 hover:bg-red-50/50 dark:hover:bg-red-500/5 transition-all duration-300 shadow-sm hover:shadow-xl hover:-translate-y-1 min-w-[280px]">

                                <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-red-500/20 group-hover:rotate-6">
                                    <i class="fa-solid fa-users-gear text-2xl"></i>
                                </div>

                                <div class="flex-1 text-left">
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-red-500 transition-colors">Management</div>
                                    <div class="text-sm font-black text-slate-700 dark:text-gray-100">รออนุมัติ</div>
                                </div>

                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-3 py-1 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black shadow-sm" title="ตัวเลขจำนวนรอการอนุมัติ">
                                        {{ $hrrequestapprovemanacount }}
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-300 group-hover:text-red-500 transition-all group-hover:translate-y-0.5"></i>
                                </div>
                            </div>

                            <ul tabindex="-1" class="dropdown-content menu p-3 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-3xl w-80 mt-4 border border-white dark:border-gray-700/50 z-[100] animate-fade-in">
                                <li class="menu-title px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Management Action</li>
                                <li>
                                    <a href="{{ route('approve.approvemanalist') }}" class="flex items-center gap-3 rounded-2xl py-4 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors">
                                            <i class="fa-regular fa-file-lines text-lg text-slate-400 group-hover:text-red-500"></i>
                                        </div>
                                        <div class="flex-1 font-bold text-sm">รายการที่รออนุมัติ</div>
                                        <span class="px-2.5 py-1 bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-black">{{ $hrrequestapprovemanacount }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <div class="dropdown dropdown-bottom">
                        <div tabindex="0" role="button"
                            class="group flex items-center gap-4 px-5 py-4 rounded-[1.5rem] bg-white dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 hover:border-red-500/50 hover:bg-red-50/50 dark:hover:bg-red-500/5 transition-all duration-300 shadow-sm hover:shadow-xl hover:-translate-y-1 min-w-[280px]">

                            <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-red-500/20 group-hover:rotate-6">
                                <i class="fa-solid fa-chart-line text-2xl"></i>
                            </div>

                            <div class="flex-1 text-left">
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-red-500 transition-colors">Statistics</div>
                                <div class="text-sm font-black text-slate-700 dark:text-gray-100">ข้อมูลคำขอ</div>
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-black shadow-sm" title="ตัวเลขจำนวนรอรอดำเนินการ">
                                    {{ $hrrequests }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-gray-300 group-hover:text-red-500 transition-all group-hover:translate-y-0.5"></i>
                            </div>
                        </div>

                        <ul tabindex="-1" class="dropdown-content menu p-3 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-3xl w-80 mt-4 border border-white dark:border-gray-700/50 z-[100] animate-fade-in shadow-xl">
                            <li class="menu-title px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Data List</li>
                            <li>
                                <a href="{{ route('requesthr.list') }}" class="flex items-center gap-3 rounded-2xl py-4 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors">
                                        <i class="fa-regular fa-file-lines text-lg text-slate-400 group-hover:text-red-500"></i>
                                    </div>
                                    <div class="flex-1 font-bold text-sm">รายการที่รอดำเนินการ</div>
                                    <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-black">{{ $hrrequests }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('requesthr.listall') }}" class="flex items-center gap-3 rounded-2xl py-4 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 active:bg-red-100 group transition-all duration-200">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-red-100 dark:group-hover:bg-red-500/20 transition-colors">
                                        <i class="fa-solid fa-clock-rotate-left text-lg text-slate-400 group-hover:text-red-500"></i>
                                    </div>
                                    <div class="flex-1 font-bold text-sm">รายการทั้งหมด</div>
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-gray-300 rounded-lg text-xs font-black">{{ $hrrequestsCount }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection