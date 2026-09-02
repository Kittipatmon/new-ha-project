@extends('layouts.training.app')

@section('content')
<style>
    /* Gradient Badges & Glowing Effects */
    .step-glow {
        box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.25), 0 8px 10px -6px rgba(239, 68, 68, 0.2);
    }
    
    @media (min-width: 640px) {
        .timeline-line::before {
            content: '';
            position: absolute;
            top: 2rem;
            bottom: 2rem;
            left: 20px;
            width: 3px;
            background: linear-gradient(to bottom, #ef4444 0%, #3b82f6 50%, #10b981 100%);
            border-radius: 9999px;
            z-index: 0;
        }
    }

    /* Print Styles */
    @media print {
        nav, footer, .no-print {
            display: none !important;
        }
        body {
            background-color: white !important;
            color: black !important;
        }
        .print-full-width {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .card-print {
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
    }
</style>

<div class="min-h-screen px-3 sm:px-6 pt-4 sm:pt-8 pb-16 sm:pb-20 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-gray-200">
    <div class="w-full max-w-7xl mx-auto print-full-width">

        <!-- Breadcrumbs -->
        <div class="flex items-center flex-wrap text-xs sm:text-sm mb-6 sm:mb-8 space-x-1.5 sm:space-x-2 no-print mt-1">
            <a href="{{ route('welcome') }}" class="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors flex items-center gap-1">
                หน้าหลัก
            </a>
            <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
            <a href="{{ route('training.index') }}" class="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
                ระบบสมัครฝึกอบรม
            </a>
            <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
            <span class="text-red-500 font-medium">คู่มือขั้นตอนการทำงาน</span>
        </div>

        <!-- Executive Hero Header Card (#01579b Deep Navy Tone) -->
        <div class="bg-[#01579b] text-white rounded-2xl p-6 sm:p-8 lg:p-10 mb-8 border border-[#01457b] shadow-md">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="max-w-3xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-white/15 border border-white/25 text-white text-xs font-semibold backdrop-blur-xs">
                        <i class="fa-solid fa-graduation-cap"></i> คู่มือปฏิบัติงานระบบฝึกอบรม (Training Flow Manual)
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-snug">
                        ขั้นตอนและกระบวนการทำงาน<br class="hidden sm:inline">
                        ตั้งแต่เริ่มสมัครจนสำเร็จการฝึกอบรม
                    </h1>
                    <p class="text-sm sm:text-base text-sky-100 leading-relaxed font-normal">
                        แนะนำขั้นตอนการปฏิบัติงานอย่างเป็นระบบ 5 ขั้นตอนหลัก สำหรับพนักงานผู้เข้าอบรมและเจ้าหน้าที่ผู้ดูแลระบบ พร้อมการเข้าร่วมเรียน On-site และ Online การดาวน์โหลดสื่อการสอน และการติดตามผลบันทึกประวัติ HR
                    </p>

                    <!-- Feature Badges (Darker Background) -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#003359] text-white border border-sky-900/70 text-xs font-medium shadow-xs">
                            <i class="fa-solid fa-check text-emerald-400"></i> 5 ขั้นตอนเข้าใจง่าย
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#003359] text-white border border-sky-900/70 text-xs font-medium shadow-xs">
                            <i class="fa-solid fa-laptop text-cyan-300"></i> รองรับ On-site & Online
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#003359] text-white border border-sky-900/70 text-xs font-medium shadow-xs">
                            <i class="fa-solid fa-file-lines text-amber-400"></i> สื่อการสอน PDF & E-Learning
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row lg:flex-col gap-3 w-full lg:w-auto shrink-0 no-print">
                    <a href="{{ route('training.index') }}" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#B21F24] hover:bg-red-700 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors text-sm text-center shadow-sm">
                        <i class="fa-solid fa-paper-plane"></i> เข้าสู่หน้าสมัครอบรม
                    </a>
                    <button type="button" onclick="window.print()" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#003359] hover:bg-[#002440] text-white font-medium px-4 py-2.5 rounded-lg border border-sky-900/70 transition-colors text-sm text-center shadow-xs">
                        <i class="fa-solid fa-print text-slate-200"></i> พิมพ์ / สแกน PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Section 1: Overview Process Map (5 Main Steps Summary Card Flow) -->
        <div class="mb-10 sm:mb-12">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-1">
                <div>
                    <h2 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-red-500"></i> ผังกระบวนการทำงานภาพรวม (Process Overview Map)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">สรุป 5 ขั้นตอนหลักเพื่อเข้ารับการอบรมพัฒนาทักษะ</p>
                </div>
            </div>

            <!-- Flowchart Grid Cards: Responsive layout (1 col mobile, 2 col iPad/Tablet, 5 col Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4 relative">
                <!-- Step 1 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/60 shadow-sm hover:shadow-md transition-all relative flex flex-col justify-between group border-t-4 border-t-red-500">
                    <div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-bold flex items-center justify-center mb-3 text-base sm:text-lg group-hover:scale-110 transition-transform">
                            01
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">1. เลือกหลักสูตร</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            ค้นหาหัวข้ออบรมที่สนใจ ตรวจสอบวันที่ รูปแบบ (Online/On-site) และหน่วยงานจัดอบรม
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-red-500 font-semibold">
                        <span>ค้นหา & รายละเอียด</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/60 shadow-sm hover:shadow-md transition-all relative flex flex-col justify-between group border-t-4 border-t-orange-500">
                    <div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 font-bold flex items-center justify-center mb-3 text-base sm:text-lg group-hover:scale-110 transition-transform">
                            02
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">2. ลงทะเบียนสมัคร</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            กดปุ่ม "สมัครเลย" ระบุรหัสพนักงาน และตรวจสอบความถูกต้องก่อนยืนยัน
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-orange-500 font-semibold">
                        <span>ส่งข้อมูลสมัคร</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/60 shadow-sm hover:shadow-md transition-all relative flex flex-col justify-between group border-t-4 border-t-amber-500">
                    <div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 font-bold flex items-center justify-center mb-3 text-base sm:text-lg group-hover:scale-110 transition-transform">
                            03
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">3. ยืนยันสิทธิ์</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            ระบบบันทึกสถานะการสมัคร ปุ่มเปลี่ยนเป็น "เข้าร่วมอบรม" พร้อมรับการยืนยัน
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-amber-500 font-semibold">
                        <span>ตรวจสอบสถานะ</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/60 shadow-sm hover:shadow-md transition-all relative flex flex-col justify-between group border-t-4 border-t-blue-500">
                    <div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold flex items-center justify-center mb-3 text-base sm:text-lg group-hover:scale-110 transition-transform">
                            04
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">4. เข้าร่วม & สื่อการสอน</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            เข้าเรียนตามวันเวลากำหนด ดาวน์โหลดเอกสารประกอบการสอน PDF หรือลิงก์สื่อออนไลน์
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-blue-500 font-semibold">
                        <span>เข้าเรียน & โหลดเอกสาร</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/60 shadow-sm hover:shadow-md transition-all relative flex flex-col justify-between group border-t-4 border-t-emerald-500 sm:col-span-2 md:col-span-1">
                    <div>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center mb-3 text-base sm:text-lg group-hover:scale-110 transition-transform">
                            05
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">5. สำเร็จการอบรม</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            บันทึกประวัติการอบรมลงระบบ HR และวิเคราะห์สถิติจำนวนชั่วโมงการพัฒนาตนเอง
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-emerald-500 font-semibold">
                        <span>ประวัติ & รายงานผล</span>
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Toggle Tabs (สำหรับผู้เรียน vs สำหรับฝ่าย HR/Admin) -->
        <div class="mb-8 sm:mb-10 no-print overflow-x-auto scrollbar-none border-b border-slate-200 dark:border-slate-700">
            <div class="flex min-w-max space-x-4 sm:space-x-6">
                <button type="button" id="tabEmployeeBtn" onclick="switchGuideTab('employee')" 
                    class="pb-3 text-xs sm:text-base font-bold text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400 flex items-center gap-2 transition-all shrink-0">
                    <i class="fa-solid fa-user-graduate"></i> สำหรับพนักงานผู้สมัครอบรม <span class="hidden sm:inline">(Employee View)</span>
                </button>
                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                <button type="button" id="tabAdminBtn" onclick="switchGuideTab('admin')" 
                    class="pb-3 text-xs sm:text-base font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent flex items-center gap-2 transition-all shrink-0">
                    <i class="fa-solid fa-user-gear"></i> สำหรับเจ้าหน้าที่ HR / Admin <span class="hidden sm:inline">(HR Management View)</span>
                </button>
                @endif
            </div>
        </div>

        <!-- EMPLOYEE GUIDE TAB CONTENT -->
        <div id="employeeGuideContent" class="space-y-6 sm:space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-red-500"></i> รายละเอียดขั้นตอนการอบรมสำหรับพนักงาน (Step-by-Step Guide)
                </h3>
            </div>

            <div class="relative sm:pl-10 lg:pl-12 space-y-6 sm:space-y-8 timeline-line">

                <!-- Detailed Step 1 -->
                <div class="relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 border border-slate-200/80 dark:border-slate-700/60 shadow-sm card-print">
                    <!-- Timeline Node Icon -->
                    <div class="hidden sm:flex absolute -left-10 lg:-left-[43px] top-6 w-9 h-9 lg:w-10 lg:h-10 rounded-2xl bg-red-600 text-white font-extrabold items-center justify-center text-xs lg:text-sm shadow-lg shadow-red-600/30 border-4 border-slate-50 dark:border-slate-900">
                        1
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="sm:hidden flex w-8 h-8 rounded-xl bg-red-600 text-white font-bold items-center justify-center text-xs shrink-0 shadow-md">
                                1
                            </span>
                            <div>
                                <span class="text-[10px] sm:text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-widest bg-red-50 dark:bg-red-900/30 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full">
                                    ขั้นตอนที่ 1
                                </span>
                                <h4 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white mt-1">
                                    ค้นหาและเลือกหลักสูตรฝึกอบรม (Search & Select Course)
                                </h4>
                            </div>
                        </div>
                        <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 self-start sm:self-auto">
                            <i class="fa-regular fa-clock mr-1"></i> ใช้เวลาประมาณ 1-2 นาที
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 items-center">
                        <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                            <p class="leading-relaxed">
                                พนักงานสามารถเข้ามายังระบบฝึกอบรมเพื่อตรวจสอบตารางและกำหนดการฝึกอบรมที่เปิดรับสมัคร โดยมีฟังก์ชันอำนวยความสะดวก:
                            </p>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-circle-check text-green-500 mt-1 shrink-0"></i>
                                    <span><strong>ช่องค้นหา:</strong> พิมพ์ชื่อหลักสูตร หรือ สาขาการเรียนรู้ที่ต้องการ</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-circle-check text-green-500 mt-1 shrink-0"></i>
                                    <span><strong>ตัวกรองรูปแบบ:</strong> เลือกเรียนแบบ On-site (ปกติ) หรือ Online (ออนไลน์)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-circle-check text-green-500 mt-1 shrink-0"></i>
                                    <span><strong>ตัวกรองหน่วยงาน:</strong> เลือกดูหลักสูตรตามสถาบันหรือศูนย์ฝึกอบรม</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-circle-check text-green-500 mt-1 shrink-0"></i>
                                    <span><strong>ปุ่มรายละเอียด:</strong> กดเพื่ออ่านวัตถุประสงค์ คุณสมบัติผู้เรียน และรายละเอียดเต็ม</span>
                                </li>
                            </ul>
                        </div>

                        <!-- UI Preview Mockup Card -->
                        <div class="bg-slate-100 dark:bg-slate-900/80 p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                                <span><i class="fa-solid fa-eye text-red-500"></i> ตัวอย่างหน้าจอค้นหา</span>
                                <span class="bg-red-500/10 text-red-500 px-2 py-0.5 rounded text-[10px]">หน้ากำหนดการ</span>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                                <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                <span class="text-xs text-slate-400 truncate">ค้นหาชื่อสาขา / หลักสูตร...</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-[11px] bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-lg font-bold">
                                    ออนไลน์ (Online)
                                </span>
                                <span class="text-[11px] bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-lg font-bold">
                                    ปกติ (On-site)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Step 2 -->
                <div class="relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 border border-slate-200/80 dark:border-slate-700/60 shadow-sm card-print">
                    <div class="hidden sm:flex absolute -left-10 lg:-left-[43px] top-6 w-9 h-9 lg:w-10 lg:h-10 rounded-2xl bg-orange-500 text-white font-extrabold items-center justify-center text-xs lg:text-sm shadow-lg shadow-orange-500/30 border-4 border-slate-50 dark:border-slate-900">
                        2
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="sm:hidden flex w-8 h-8 rounded-xl bg-orange-500 text-white font-bold items-center justify-center text-xs shrink-0 shadow-md">
                                2
                            </span>
                            <div>
                                <span class="text-[10px] sm:text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest bg-orange-50 dark:bg-orange-900/30 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full">
                                    ขั้นตอนที่ 2
                                </span>
                                <h4 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white mt-1">
                                    กรอกข้อมูลสมัครฝึกอบรม (Submit Registration Application)
                                </h4>
                            </div>
                        </div>
                        <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 self-start sm:self-auto">
                            <i class="fa-regular fa-clock mr-1"></i> ใช้เวลาประมาณ 1 นาที
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 items-center">
                        <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                            <p class="leading-relaxed">
                                เมื่อเลือกหลักสูตรที่ต้องการเรียบร้อยแล้ว ให้ดำเนินการลงทะเบียนดังนี้:
                            </p>
                            <ol class="space-y-2 list-decimal list-inside">
                                <li class="leading-relaxed">กดปุ่มสีเขียว <strong>"สมัครเลย"</strong> ในคอลัมน์การดำเนินการ</li>
                                <li class="leading-relaxed">ระบบจะนำท่านไปยังฟอร์มลงทะเบียน โดยจะแสดงรายละเอียดคอร์สที่เลือก</li>
                                <li class="leading-relaxed">เลือกรหัสพนักงาน/ชื่อ-นามสกุล ของท่านจากรายการ</li>
                                <li class="leading-relaxed">ตรวจสอบความถูกต้องของข้อมูลแล้วกดปุ่ม <strong>"ยืนยันการสมัคร"</strong></li>
                            </ol>
                            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-xl text-xs text-amber-800 dark:text-amber-300 mt-2">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> <strong>ข้อควรระวัง:</strong> หากหลักสูตรเต็มแล้ว ปุ่มจะขึ้นสถานะ <em>"เต็มแล้ว"</em> ไม่สามารถกดสมัครได้
                            </div>
                        </div>

                        <!-- UI Preview Mockup Card -->
                        <div class="bg-slate-100 dark:bg-slate-900/80 p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                                <span><i class="fa-solid fa-square-check text-green-500"></i> ปุ่มสมัครในตาราง</span>
                                <span class="bg-green-500/10 text-green-600 px-2 py-0.5 rounded text-[10px]">การดำเนินการ</span>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 text-center space-y-3">
                                <span class="inline-block bg-green-600 text-white font-bold py-2 px-6 rounded-full text-xs shadow-md shadow-green-600/20">
                                    สมัครเลย
                                </span>
                                <p class="text-[11px] text-slate-400">คลิกเพื่อเข้าสู่หน้ายืนยันรหัสพนักงาน</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Step 3 -->
                <div class="relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 border border-slate-200/80 dark:border-slate-700/60 shadow-sm card-print">
                    <div class="hidden sm:flex absolute -left-10 lg:-left-[43px] top-6 w-9 h-9 lg:w-10 lg:h-10 rounded-2xl bg-amber-500 text-white font-extrabold items-center justify-center text-xs lg:text-sm shadow-lg shadow-amber-500/30 border-4 border-slate-50 dark:border-slate-900">
                        3
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="sm:hidden flex w-8 h-8 rounded-xl bg-amber-500 text-white font-bold items-center justify-center text-xs shrink-0 shadow-md">
                                3
                            </span>
                            <div>
                                <span class="text-[10px] sm:text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest bg-amber-50 dark:bg-amber-900/30 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full">
                                    ขั้นตอนที่ 3
                                </span>
                                <h4 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white mt-1">
                                    ยืนยันสิทธิ์และตรวจสอบรายการสมัคร (Confirmation & Status Check)
                                </h4>
                            </div>
                        </div>
                        <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 self-start sm:self-auto">
                            <i class="fa-solid fa-bolt text-amber-500 mr-1"></i> ทำงานทันทีแบบ Real-time
                        </span>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        <p class="leading-relaxed">
                            หลังกดส่งสมัคร ระบบจะทำการบันทึกข้อมูลการสมัครลงในฐานข้อมูล และแจ้งเตือนข้อความสำเร็จ:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                            <div class="p-3.5 sm:p-4 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-800 rounded-2xl flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-green-500 text-lg sm:text-xl mt-0.5 shrink-0"></i>
                                <div>
                                    <h5 class="font-bold text-green-900 dark:text-green-300 text-xs sm:text-sm mb-1">สมัครฝึกอบรมสำเร็จ</h5>
                                    <p class="text-[11px] sm:text-xs text-green-700 dark:text-green-400">ระบบเปลี่ยนปุ่มกดในรายการคอร์สของท่านจาก "สมัครเลย" เป็น "เข้าร่วมอบรม" ทันที</p>
                                </div>
                            </div>

                            <div class="p-3.5 sm:p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-2xl flex items-start gap-3">
                                <i class="fa-solid fa-shield-halved text-blue-500 text-lg sm:text-xl mt-0.5 shrink-0"></i>
                                <div>
                                    <h5 class="font-bold text-blue-900 dark:text-blue-300 text-xs sm:text-sm mb-1">การคุ้มครองสิทธิ์</h5>
                                    <p class="text-[11px] sm:text-xs text-blue-700 dark:text-blue-400">พนักงานที่สมัครแล้ว จะได้รับการจัดสรรที่นั่งอบรมและเอกสารประกอบการสอนครบถ้วน</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Step 4 -->
                <div class="relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 border border-slate-200/80 dark:border-slate-700/60 shadow-sm card-print">
                    <div class="hidden sm:flex absolute -left-10 lg:-left-[43px] top-6 w-9 h-9 lg:w-10 lg:h-10 rounded-2xl bg-blue-600 text-white font-extrabold items-center justify-center text-xs lg:text-sm shadow-lg shadow-blue-600/30 border-4 border-slate-50 dark:border-slate-900">
                        4
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="sm:hidden flex w-8 h-8 rounded-xl bg-blue-600 text-white font-bold items-center justify-center text-xs shrink-0 shadow-md">
                                4
                            </span>
                            <div>
                                <span class="text-[10px] sm:text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/30 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full">
                                    ขั้นตอนที่ 4
                                </span>
                                <h4 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white mt-1">
                                    การเข้าเรียน & เข้าถึงสื่อการสอน (Attending Class & Materials)
                                </h4>
                            </div>
                        </div>
                        <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 self-start sm:self-auto">
                            <i class="fa-solid fa-file-pdf text-red-500 mr-1"></i> รองรับ PDF / Zoom Link
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 items-center">
                        <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                            <p class="leading-relaxed">
                                เมื่อถึงวันอบรม หรือต้องการเปิดดูเอกสารเตรียมตัวฝึกอบรม:
                            </p>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-file-pdf text-blue-500 mt-1 shrink-0"></i>
                                    <span>กดปุ่มสีฟ้า <strong>"เข้าร่วมอบรม"</strong> ในหน้าตารางกำหนดการ</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-file-pdf text-blue-500 mt-1 shrink-0"></i>
                                    <span><strong>ไฟล์เอกสาร PDF:</strong> ระบบจะเปิดหน้าสไลด์สื่อการสอน/คู่มือบทเรียนให้ดาวน์โหลด</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fa-solid fa-video text-indigo-500 mt-1 shrink-0"></i>
                                    <span><strong>ลิงก์ห้องเรียนออนไลน์:</strong> กรณีคอร์สออนไลน์ ระบบจะนำทางเข้าสู่ลิงก์ Zoom / MS Teams / WebEx โดยตรง</span>
                                </li>
                            </ul>
                        </div>

                        <!-- UI Preview Mockup Card -->
                        <div class="bg-slate-100 dark:bg-slate-900/80 p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                                <span><i class="fa-solid fa-folder-open text-blue-500"></i> ปุ่มเปิดเข้าเรียน/ดูสื่อ</span>
                                <span class="bg-blue-500/10 text-blue-600 px-2 py-0.5 rounded text-[10px]">สำหรับผู้สมัครแล้ว</span>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 text-center space-y-2">
                                <button type="button" class="bg-blue-600 text-white font-bold py-2 px-5 rounded-full text-xs shadow-md shadow-blue-600/20 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-pdf"></i> เข้าร่วมอบรม
                                </button>
                                <p class="text-[11px] text-slate-400">คลิกเพื่อเปิดดูไฟล์เอกสารหรือเข้าห้องเรียน</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Step 5 -->
                <div class="relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 border border-slate-200/80 dark:border-slate-700/60 shadow-sm card-print">
                    <div class="hidden sm:flex absolute -left-10 lg:-left-[43px] top-6 w-9 h-9 lg:w-10 lg:h-10 rounded-2xl bg-emerald-600 text-white font-extrabold items-center justify-center text-xs lg:text-sm shadow-lg shadow-emerald-600/30 border-4 border-slate-50 dark:border-slate-900">
                        5
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="sm:hidden flex w-8 h-8 rounded-xl bg-emerald-600 text-white font-bold items-center justify-center text-xs shrink-0 shadow-md">
                                5
                            </span>
                            <div>
                                <span class="text-[10px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/30 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full">
                                    ขั้นตอนที่ 5
                                </span>
                                <h4 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white mt-1">
                                    สำเร็จการอบรม & บันทึกประวัติ (Completion & History)
                                </h4>
                            </div>
                        </div>
                        <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 self-start sm:self-auto">
                            <i class="fa-solid fa-award text-emerald-500 mr-1"></i> สะสมชั่วโมงฝึกอบรม HR
                        </span>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        <p class="leading-relaxed">
                            หลังสิ้นสุดการฝึกอบรม ข้อมูลประวัติการเข้าร่วมของท่านจะถูกบันทึกประวัติไว้ในระบบประวัติพนักงาน (HR Profile) เพื่อใช้เป็นหลักฐานประกอบการประเมินประจำปีและพัฒนาสายอาชีพ (Career Path)
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- HR / ADMIN GUIDE TAB CONTENT (Hidden by Default) -->
        @if(Auth::check() && Auth::user()->isHrOrAdmin())
        <div id="adminGuideContent" class="hidden space-y-6 sm:space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-base sm:text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-red-500"></i> ขั้นตอนการจัดการระบบฝึกอบรมสำหรับฝ่าย HR / แอดมิน
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Admin Task 1 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-sm space-y-3 sm:space-y-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center text-lg sm:text-xl font-bold">
                        <i class="fa-solid fa-plus-minus"></i>
                    </div>
                    <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">1. เพิ่ม / แก้ไข / ลบหลักสูตร (Backend Training Management)</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        เข้าสู่เมนู <code class="text-red-500 bg-red-50 dark:bg-red-900/30 px-1.5 py-0.5 rounded">/backend/training</code> เพื่อสร้างคอร์สใหม่ กำหนดจำนวนชั่วโมง วันที่เริ่ม-สิ้นสุด รูปแบบ และหน่วยงานจัดอบรม
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-600 dark:text-slate-300">
                        <li><i class="fa-solid fa-check text-green-500 mr-1"></i> อัปโหลดรูปภาพปกหลักสูตร</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-1"></i> แนบไฟล์สื่อการสอน PDF หรือระบุลิงก์สื่อภายนอก</li>
                    </ul>
                </div>

                <!-- Admin Task 2 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-sm space-y-3 sm:space-y-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg sm:text-xl font-bold">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">2. ตรวจสอบรายชื่อผู้สมัคร (Applicants Management)</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        ตรวจสอบรายชื่อพนักงานที่ลงทะเบียนในแต่ละหลักสูตร ส่งออกข้อมูลเพื่อเช็คชื่อเข้าเรียน On-site หรือส่งอีเมลแจ้งเตือน
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-600 dark:text-slate-300">
                        <li><i class="fa-solid fa-check text-green-500 mr-1"></i> ดูสถิติรายชื่อแยกตามหลักสูตร</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-1"></i> ตรวจสอบรหัสพนักงานและสังกัดหน่วยงาน</li>
                    </ul>
                </div>

                <!-- Admin Task 3 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-sm space-y-3 sm:space-y-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg sm:text-xl font-bold">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">3. ติดตามสถิติ Training Dashboard</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        เปิดดู <code class="text-purple-500 bg-purple-50 dark:bg-purple-900/30 px-1.5 py-0.5 rounded">/training/dashboard</code> เพื่อวิเคราะห์หลักสูตรยอดนิยม แนวโน้มการสมัครประจำปี และสัดส่วนแยกตามหน่วยงาน
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-600 dark:text-slate-300">
                        <li><i class="fa-solid fa-check text-green-500 mr-1"></i> กรองสถิติตาม ปี/เดือน/วัน</li>
                        <li><i class="fa-solid fa-check text-green-500 mr-1"></i> แสดงกราฟวิเคราะห์และสรุปยอดผู้เข้าอบรม</li>
                    </ul>
                </div>

                <!-- Admin Task 4 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-sm space-y-3 sm:space-y-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg sm:text-xl font-bold">
                        <i class="fa-solid fa-file-export"></i>
                    </div>
                    <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">4. ออกรายงานและรับรองผลการอบรม</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        สรุปรายงานจำนวนชั่วโมงการฝึกอบรมของพนักงานแต่ละท่าน เพื่อใช้เป็นดัชนีชี้วัด (KPIs) และรายงานต่อกรมพัฒนาฝีมือแรงงาน
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- FAQ Section -->
        <div class="mt-12 sm:mt-16 bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-10 border border-slate-200/80 dark:border-slate-700/60 shadow-sm card-print">
            <div class="text-center max-w-2xl mx-auto mb-6 sm:mb-8">
                <span class="text-[10px] sm:text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-widest bg-red-50 dark:bg-red-900/30 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full">
                    FAQ & Trouble Shooting
                </span>
                <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white mt-2">
                    คำถามที่พบบ่อย (Frequently Asked Questions)
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    รวบรวมข้อสงสัยและวิธีการแก้ไขปัญหาในการใช้งานระบบสมัครฝึกอบรม
                </p>
            </div>

            <div class="space-y-3 sm:space-y-4 max-w-4xl mx-auto">
                <!-- FAQ 1 -->
                <details class="group bg-slate-50 dark:bg-slate-900/60 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700/80 transition-all [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-3.5 sm:p-4 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs sm:text-base">
                        <span class="flex items-center gap-2.5 sm:gap-3">
                            <i class="fa-solid fa-circle-question text-red-500 text-base sm:text-lg shrink-0"></i>
                            จะทราบได้อย่างไรว่าการสมัครฝึกอบรมสำเร็จแล้ว?
                        </span>
                        <span class="transition group-open:-rotate-180 shrink-0 ml-2">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs sm:text-sm"></i>
                        </span>
                    </summary>
                    <div class="px-3.5 sm:px-4 pb-4 pt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 border-t border-slate-200/60 dark:border-slate-800 leading-relaxed">
                        เมื่อสมัครสำเร็จ ระบบจะแสดงข้อความแจ้งเตือน "สมัครฝึกอบรมสำเร็จ" สีเขียวที่ด้านบนของหน้าเว็บ และปุ่มกดในตารางของคอร์สนั้นจะเปลี่ยนจาก <span class="text-green-600 font-bold">"สมัครเลย"</span> เป็นปุ่มสีฟ้า <span class="text-blue-600 font-bold">"เข้าร่วมอบรม"</span> ทันที
                    </div>
                </details>

                <!-- FAQ 2 -->
                <details class="group bg-slate-50 dark:bg-slate-900/60 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700/80 transition-all [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-3.5 sm:p-4 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs sm:text-base">
                        <span class="flex items-center gap-2.5 sm:gap-3">
                            <i class="fa-solid fa-circle-question text-red-500 text-base sm:text-lg shrink-0"></i>
                            หากขึ้นสถานะ "เต็มแล้ว" สามารถขอเพิ่มที่นั่งได้อย่างไร?
                        </span>
                        <span class="transition group-open:-rotate-180 shrink-0 ml-2">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs sm:text-sm"></i>
                        </span>
                    </summary>
                    <div class="px-3.5 sm:px-4 pb-4 pt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 border-t border-slate-200/60 dark:border-slate-800 leading-relaxed">
                        กรณีหลักสูตรเต็ม พนักงานสามารถติดต่อฝ่าย HR / พัฒนาทรัพยากรบุคคลโดยตรง เพื่อแจ้งความประสงค์สำรองที่นั่ง หรือขอเพิ่มรอบการจัดอบรมในรุ่นถัดไป
                    </div>
                </details>

                <!-- FAQ 3 -->
                <details class="group bg-slate-50 dark:bg-slate-900/60 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-700/80 transition-all [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-3.5 sm:p-4 cursor-pointer font-bold text-slate-800 dark:text-slate-200 text-xs sm:text-base">
                        <span class="flex items-center gap-2.5 sm:gap-3">
                            <i class="fa-solid fa-circle-question text-red-500 text-base sm:text-lg shrink-0"></i>
                            สามารถดาวน์โหลดสื่อการสอนย้อนหลังได้จากที่ไหน?
                        </span>
                        <span class="transition group-open:-rotate-180 shrink-0 ml-2">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs sm:text-sm"></i>
                        </span>
                    </summary>
                    <div class="px-3.5 sm:px-4 pb-4 pt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 border-t border-slate-200/60 dark:border-slate-800 leading-relaxed">
                        ท่านสามารถเข้ามากดปุ่ม <span class="text-blue-600 font-bold">"เข้าร่วมอบรม"</span> ในรายการหลักสูตรที่เคยสมัครไว้ได้ตลอดเวลา เพื่อดาวน์โหลดเอกสาร PDF หรือรับลิงก์สื่อการสอน
                    </div>
                </details>
            </div>
        </div>

        <!-- Helpdesk Contact Card -->
        <div class="mt-6 sm:mt-8 bg-gradient-to-r from-red-600 to-rose-700 rounded-2xl sm:rounded-3xl p-5 sm:p-8 text-white flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 shadow-xl no-print text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 flex items-center justify-center text-xl sm:text-2xl shrink-0 backdrop-blur-sm">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h4 class="text-base sm:text-lg font-bold">มีข้อสงสัยเพิ่มเติมเกี่ยวกับระบบฝึกอบรม?</h4>
                    <p class="text-xs sm:text-sm text-red-100 font-light">ติดต่อฝ่ายพัฒนาทรัพยากรบุคคล (HRD Team) ได้ในวันและเวลาทำการ</p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0 w-full sm:w-auto">
                <a href="mailto:hr@kumwell.com" class="w-full sm:w-auto text-center bg-white text-red-600 hover:bg-red-50 font-bold px-5 py-2.5 rounded-xl text-xs shadow-md transition-all">
                    <i class="fa-solid fa-envelope mr-1"></i> ส่งอีเมลสอบถาม
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    function switchGuideTab(tab) {
        const empTab = document.getElementById('tabEmployeeBtn');
        const adminTab = document.getElementById('tabAdminBtn');
        const empContent = document.getElementById('employeeGuideContent');
        const adminContent = document.getElementById('adminGuideContent');

        if (!adminTab || !adminContent) return;

        if (tab === 'employee') {
            empTab.className = "pb-3 text-xs sm:text-base font-bold text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400 flex items-center gap-2 transition-all shrink-0";
            adminTab.className = "pb-3 text-xs sm:text-base font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent flex items-center gap-2 transition-all shrink-0";
            empContent.classList.remove('hidden');
            adminContent.classList.add('hidden');
        } else {
            adminTab.className = "pb-3 text-xs sm:text-base font-bold text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400 flex items-center gap-2 transition-all shrink-0";
            empTab.className = "pb-3 text-xs sm:text-base font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white border-b-2 border-transparent flex items-center gap-2 transition-all shrink-0";
            adminContent.classList.remove('hidden');
            empContent.classList.add('hidden');
        }
    }
</script>
@endsection
