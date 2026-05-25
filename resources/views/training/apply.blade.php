@extends('layouts.training.app')

@section('content')
    <div class="min-h-screen p-6 pt-10 pb-20 bg-gray-50 dark:bg-[#15171e] text-slate-800 dark:text-gray-200">
        <div class="max-w-8xl mx-auto px-4">
            <!-- Breadcrumbs -->
            <div class="flex items-center text-sm mb-4 space-x-2">
                <a href="{{ route('welcome') }}"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
                <a href="{{ route('training.index') }}"
                    class="text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">ลงทะเบียน</a>
                <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
                <span class="text-red-500 font-medium">สมัครฝึกอบรม</span>
            </div>
        </div>

        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-10">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 mb-4 shadow-sm">
                    <i class="fa-solid fa-file-signature text-3xl"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">สมัครฝึกอบรม</h1>
                <div class="flex items-center justify-center">
                    <div class="h-[2px] bg-gray-300 dark:bg-gray-600 w-16"></div>
                    <div class="h-2 w-2 rounded-full bg-red-600 mx-3 shadow-sm shadow-red-500/50"></div>
                    <div class="h-[2px] bg-gray-300 dark:bg-gray-600 w-16"></div>
                </div>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white dark:bg-[#1E2129] rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden relative">
                <div class="absolute top-0 w-full h-2 bg-gradient-to-r from-red-500 to-red-600 left-0"></div>

                <div class="p-8 sm:p-12">
                    @if(session('success'))
                        <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500 rounded-r-xl">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                                <h3 class="font-bold text-green-800 dark:text-green-400">{{ session('success') }}</h3>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('training.store') }}" method="POST" class="space-y-8">
                        @csrf
                        <input type="hidden" name="training_id" value="{{ $training->id }}">

                        <!-- Course Info -->
                        <div
                            class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <div class="mb-4">
                                <label
                                    class="block text-slate-500 dark:text-slate-400 mb-1 text-sm font-semibold uppercase tracking-wider">สาขาที่สมัคร</label>
                                <p class="text-slate-800 dark:text-white font-bold text-lg flex items-start gap-2">
                                    <i class="fa-solid fa-bookmark text-red-500 mt-1"></i>
                                    <span>{{ $training->branch }}</span>
                                </p>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                                <label
                                    class="block text-slate-500 dark:text-slate-400 mb-1 text-sm font-semibold uppercase tracking-wider">หน่วยงานจัดอบรม</label>
                                <p class="text-slate-700 dark:text-gray-300 font-medium flex items-center gap-2">
                                    <i class="fa-solid fa-building text-gray-400 dark:text-gray-500"></i>
                                    {{ $training->department }}
                                </p>
                            </div>
                        </div>

                        <!-- Input Fields -->
                        <div>
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                                <i class="fa-solid fa-id-card text-red-500"></i> ข้อมูลประจำตัว
                            </h3>

                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">รหัสพนักงาน<span
                                            class="text-red-500">*</span></label>
                                    <input type="hidden" name="employee_code"
                                        value="{{ Auth::check() ? Auth::user()->employee_code : '' }}">
                                    <input type="text"
                                        value="{{ Auth::check() ? Auth::user()->employee_code . ' - ' . Auth::user()->fullname : 'ไม่พบข้อมูล' }}"
                                        class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/80 text-slate-500 dark:text-gray-400 cursor-not-allowed focus:ring-0 transition-colors"
                                        readonly>
                                    @error('employee_code')
                                        <span class="text-red-500 text-sm mt-1.5 flex items-center gap-1"><i
                                                class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-circle-info mr-1"></i>
                                        ดึงข้อมูลจากพนักงานที่คุณเข้าสู่ระบบ (ไม่สามารถแก้ไขได้)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-red-600/30 hover:shadow-red-600/50 transition-all hover:-translate-y-0.5 focus:ring-4 focus:ring-red-500/30 text-lg flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> ยืนยันการสมัคร
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-8">
                        <a href="#"
                            class="inline-flex items-center gap-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium group">
                            <i class="fa-solid fa-book-open text-red-500 group-hover:scale-110 transition-transform"></i>
                            คู่มือระบบสมัครฝึกอบรม
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection