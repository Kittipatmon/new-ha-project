@extends('layouts.training.app')

@section('content')
<style>
    /* Print Styles for Official SOP / Training Manual */
    @media print {
        nav, footer, .no-print, #tabAdminBtn, #tabEmployeeBtn {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        .print-full-width {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .guide-card-print {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>

<div class="min-h-screen px-3 sm:px-6 pt-4 sm:pt-6 pb-16 sm:pb-20 bg-slate-50/70 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
    <div class="w-full max-w-6xl mx-auto print-full-width">

        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex items-center text-xs sm:text-sm mb-6 space-x-2 text-slate-500 dark:text-slate-400 no-print">
            <a href="{{ route('welcome') }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">
                หน้าหลัก
            </a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <a href="{{ route('training.index') }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">
                กำหนดการฝึกอบรม
            </a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-slate-900 dark:text-white font-medium" aria-current="page">คู่มือการใช้งานระบบ</span>
        </nav>

        <!-- Executive Header Card (Soft Warm Tone) -->
        <header class="bg-gradient-to-br from-white via-slate-50/80 to-rose-50/30 dark:from-slate-800 dark:via-slate-800 dark:to-slate-800/90 rounded-xl sm:rounded-2xl border border-slate-200/90 dark:border-slate-700/70 p-6 sm:p-8 lg:p-9 mb-8 shadow-xs">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="space-y-3.5 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-rose-50 dark:bg-rose-950/40 border border-rose-200/70 dark:border-rose-800/50 text-rose-800 dark:text-rose-200 text-xs font-medium">
                        <i class="fa-solid fa-book-bookmark text-[#B21F24]"></i>
                        <span>คู่มือการปฏิบัติงาน (Standard Operating Procedure)</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-3.5xl font-bold text-slate-900 dark:text-white tracking-tight leading-snug">
                        คู่มือขั้นตอนการดำเนินงานระบบฝึกอบรม
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        แนะนำขั้นตอนการปฏิบัติงาน 5 ขั้นตอนหลัก สำหรับพนักงานผู้เข้าอบรมและเจ้าหน้าที่ฝ่ายพัฒนาทรัพยากรบุคคล (HRD) ครอบคลุมการค้นหาหลักสูตร การลงทะเบียนยืนยันสิทธิ์ การเข้าร่วมห้องเรียนและดาวน์โหลดสื่อการสอน ตลอดจนการบันทึกประวัติการพัฒนาตนเอง
                    </p>

                    <!-- Document Badges in Soft Semantic Pastels -->
                    <div class="flex flex-wrap items-center gap-2.5 pt-1 text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200/70 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 font-medium">
                            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-[11px]"></i> 5 ขั้นตอนเข้าใจง่าย
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-sky-50 dark:bg-sky-950/30 border border-sky-200/70 dark:border-sky-800/50 text-sky-800 dark:text-sky-300 font-medium">
                            <i class="fa-solid fa-chalkboard-user text-sky-600 dark:text-sky-400 text-[11px]"></i> รองรับ On-site & Online
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/50 text-amber-800 dark:text-amber-300 font-medium">
                            <i class="fa-solid fa-file-pdf text-amber-600 dark:text-amber-400 text-[11px]"></i> เอกสารประกอบ & สื่อการสอน
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 w-full lg:w-auto shrink-0 no-print">
                    <a href="{{ route('training.index') }}" 
                        class="inline-flex items-center justify-center gap-2 bg-[#B21F24] hover:bg-[#991B1F] text-white font-medium px-4 py-2.5 rounded-lg transition-colors text-sm shadow-xs">
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                        <span>เข้าสู่หน้ากำหนดการฝึกอบรม</span>
                    </a>
                    <button type="button" onclick="window.print()" 
                        class="inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-200 font-medium px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 transition-colors text-sm shadow-xs">
                        <i class="fa-solid fa-print text-slate-500 dark:text-slate-400 text-xs"></i>
                        <span>พิมพ์คู่มือ / สแกนเป็น PDF</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Process Overview Stepper (Soft Pastel Accents for Comfort & Readability) -->
        <section class="mb-10" aria-labelledby="pipeline-heading">
            <div class="mb-4">
                <h2 id="pipeline-heading" class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-route text-[#B21F24]"></i>
                    <span>ผังกระบวนการฝึกอบรมภาพรวม (Process Overview Map)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    ลำดับขั้นตอนการปฏิบัติงานตั้งแต่เริ่มต้นจนสำเร็จการฝึกอบรม
                </p>
            </div>

            <!-- 5 Steps Flow Layout (Soft Soothing Tones) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Step Node 1: Soft Rose -->
                <div class="bg-white dark:bg-slate-800 hover:bg-rose-50/20 dark:hover:bg-rose-950/10 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700/70 hover:border-rose-200 dark:hover:border-rose-800/40 shadow-xs flex flex-col justify-between transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/70 dark:border-rose-800/50 font-bold text-xs">
                                01
                            </span>
                            <span class="text-[11px] text-rose-600 dark:text-rose-400 font-medium">ค้นหาหลักสูตร</span>
                        </div>
                        <h3 class="font-semibold text-slate-900 dark:text-white text-sm mb-1">เลือกหลักสูตร</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            ตรวจสอบวันที่ รูปแบบการสอน และหน่วยงานจัดอบรม
                        </p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                        <span>ขั้นเตรียมตัว</span>
                        <i class="fa-solid fa-arrow-right text-[10px] text-rose-400"></i>
                    </div>
                </div>

                <!-- Step Node 2: Soft Amber -->
                <div class="bg-white dark:bg-slate-800 hover:bg-amber-50/20 dark:hover:bg-amber-950/10 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700/70 hover:border-amber-200 dark:hover:border-amber-800/40 shadow-xs flex flex-col justify-between transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/70 dark:border-amber-800/50 font-bold text-xs">
                                02
                            </span>
                            <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">ลงทะเบียน</span>
                        </div>
                        <h3 class="font-semibold text-slate-900 dark:text-white text-sm mb-1">ส่งข้อมูลสมัคร</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            กดปุ่ม "สมัครเลย" ระบุรหัสพนักงาน และยืนยันข้อมูล
                        </p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                        <span>กรอกข้อมูล</span>
                        <i class="fa-solid fa-arrow-right text-[10px] text-amber-400"></i>
                    </div>
                </div>

                <!-- Step Node 3: Soft Sky -->
                <div class="bg-white dark:bg-slate-800 hover:bg-sky-50/20 dark:hover:bg-sky-950/10 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700/70 hover:border-sky-200 dark:hover:border-sky-800/40 shadow-xs flex flex-col justify-between transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/70 dark:border-sky-800/50 font-bold text-xs">
                                03
                            </span>
                            <span class="text-[11px] text-sky-600 dark:text-sky-400 font-medium">ยืนยันสิทธิ์</span>
                        </div>
                        <h3 class="font-semibold text-slate-900 dark:text-white text-sm mb-1">รับการยืนยัน</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            ระบบบันทึกสิทธิ์ทันที ปุ่มเปลี่ยนเป็น "เข้าสู่หน้าการอบรม"
                        </p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                        <span>สำเร็จทันที</span>
                        <i class="fa-solid fa-arrow-right text-[10px] text-sky-400"></i>
                    </div>
                </div>

                <!-- Step Node 4: Soft Indigo -->
                <div class="bg-white dark:bg-slate-800 hover:bg-indigo-50/20 dark:hover:bg-indigo-950/10 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700/70 hover:border-indigo-200 dark:hover:border-indigo-800/40 shadow-xs flex flex-col justify-between transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-800/50 font-bold text-xs">
                                04
                            </span>
                            <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-medium">เข้าเรียน</span>
                        </div>
                        <h3 class="font-semibold text-slate-900 dark:text-white text-sm mb-1">เข้าร่วม & สื่อการสอน</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            เข้าเรียนตามนัด ดาวน์โหลดไฟล์ PDF หรือลิงก์ห้องเรียน
                        </p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-slate-600 dark:text-slate-300">
                        <span>ศึกษาบทเรียน</span>
                        <i class="fa-solid fa-arrow-right text-[10px] text-indigo-400"></i>
                    </div>
                </div>

                <!-- Step Node 5: Soft Emerald -->
                <div class="bg-white dark:bg-slate-800 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/10 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700/70 hover:border-emerald-200 dark:hover:border-emerald-800/40 shadow-xs flex flex-col justify-between sm:col-span-2 lg:col-span-1 transition-colors">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/50 font-bold text-xs">
                                05
                            </span>
                            <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">บันทึกประวัติ</span>
                        </div>
                        <h3 class="font-semibold text-slate-900 dark:text-white text-sm mb-1">สำเร็จการอบรม</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            บันทึกชั่วโมงอบรมในระบบ HR เพื่อใช้ในการประเมินผล
                        </p>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                        <span>เสร็จสิ้นกระบวนการ</span>
                        <i class="fa-solid fa-check text-[10px]"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Segmented Role Tabs Switcher -->
        <div class="mb-8 no-print">
            <div class="inline-flex p-1 bg-slate-200/70 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/60" role="tablist">
                <button type="button" id="tabEmployeeBtn" onclick="switchGuideTab('employee')" role="tab" aria-selected="true"
                    class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-[#B21F24]"></i>
                    <span>สำหรับพนักงานผู้สมัครอบรม</span>
                </button>
                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                <button type="button" id="tabAdminBtn" onclick="switchGuideTab('admin')" role="tab" aria-selected="false"
                    class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-slate-500"></i>
                    <span>สำหรับเจ้าหน้าที่ HR / แอดมิน</span>
                </button>
                @endif
            </div>
        </div>

        <!-- EMPLOYEE GUIDE TAB CONTENT -->
        <main id="employeeGuideContent" class="space-y-6">

            <!-- Step 1 Detailed Card (Soft Rose Accents) -->
            <article class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/70 p-5 sm:p-7 shadow-xs guide-card-print">
                <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800/60 font-bold text-sm shrink-0">
                            1
                        </span>
                        <div>
                            <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">ขั้นตอนที่ 1</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                ค้นหาและตรวจสอบข้อมูลหลักสูตร (Course Discovery)
                            </h3>
                        </div>
                    </div>
                    <span class="text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 rounded-md shrink-0">
                        <i class="fa-regular fa-clock mr-1"></i> 1-2 นาที
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-7 space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        <p class="leading-relaxed">
                            พนักงานสามารถตรวจสอบรายการหลักสูตรที่เปิดรับสมัครได้ที่หน้า <strong>กำหนดการฝึกอบรม</strong> โดยมีเครื่องมืออำนวยความสะดวกในการค้นหาข้อมูล:
                        </p>
                        <ul class="space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mt-1 shrink-0 text-xs"></i>
                                <span><strong>ช่องค้นหา:</strong> พิมพ์คำค้นหา เช่น ชื่อสาขา หรือชื่อหลักสูตร ระบบจะกรองผลการค้นหาให้ทันที</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mt-1 shrink-0 text-xs"></i>
                                <span><strong>ตัวกรองรูปแบบการเรียน:</strong> เลือกคัดกรองเฉพาะหลักสูตรแบบปกติ (On-site) หรือหลักสูตรออนไลน์ (Online)</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mt-1 shrink-0 text-xs"></i>
                                <span><strong>หน่วยงานผู้จัด:</strong> กรองดูตามสถาบันพัฒนาฝีมือแรงงานหรือสำนักงานผู้จัดอบรม</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mt-1 shrink-0 text-xs"></i>
                                <span><strong>ปุ่มรายละเอียด:</strong> คลิกปุ่ม "รายละเอียด" ในตาราง เพื่ออ่านวัตถุประสงค์และคุณสมบัติผู้เข้าอบรม</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Soft Tone Preview Box -->
                    <div class="lg:col-span-5 bg-rose-50/40 dark:bg-slate-900/60 rounded-xl p-4 border border-rose-100 dark:border-slate-700/60">
                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3 flex items-center justify-between">
                            <span>ตัวอย่างตัวกรองและป้ายกำกับ</span>
                            <span class="text-[11px] text-rose-600 dark:text-rose-400">หน้ากำหนดการ</span>
                        </div>
                        <div class="space-y-2.5">
                            <div class="bg-white dark:bg-slate-800 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center gap-2.5 text-xs text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                <span>ค้นหาชื่อสาขา / หลักสูตร...</span>
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/40">
                                    ปกติ (On-site)
                                </span>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-sky-50 text-sky-800 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/70 dark:border-sky-800/40">
                                    ออนไลน์ (Online)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Step 2 Detailed Card (Soft Amber Accents) -->
            <article class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/70 p-5 sm:p-7 shadow-xs guide-card-print">
                <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 font-bold text-sm shrink-0">
                            2
                        </span>
                        <div>
                            <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">ขั้นตอนที่ 2</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                ลงทะเบียนสมัครและยืนยันตัวตน (Application Submission)
                            </h3>
                        </div>
                    </div>
                    <span class="text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 rounded-md shrink-0">
                        <i class="fa-regular fa-clock mr-1"></i> ประมาณ 1 นาที
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-7 space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        <p class="leading-relaxed">
                            เมื่อเลือกหลักสูตรที่ประสงค์จะเข้าร่วมอบรมได้แล้ว ให้ดำเนินการตามลำดับดังนี้:
                        </p>
                        <ol class="space-y-2 list-decimal list-inside leading-relaxed">
                            <li>คลิกปุ่มสีเขียว <strong>"สมัครเลย"</strong> ในคอลัมน์การดำเนินการของตาราง</li>
                            <li>ระบบจะนำท่านเข้าสู่ฟอร์มลงทะเบียน พร้อมแสดงรายละเอียดหัวข้อและระยะเวลาอบรม</li>
                            <li>เลือกรหัสพนักงานและชื่อ-นามสกุลของตนเองจากรายการ</li>
                            <li>ตรวจสอบความถูกต้องของข้อมูล แล้วคลิกปุ่ม <strong>"ยืนยันการสมัคร"</strong></li>
                        </ol>

                        <!-- Soft Amber Advisory Box -->
                        <div class="mt-4 p-3.5 bg-amber-50/80 dark:bg-amber-950/20 rounded-lg border border-amber-200/80 dark:border-amber-800/40 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-exclamation text-amber-600 dark:text-amber-400 mt-0.5 shrink-0 text-sm"></i>
                            <div>
                                <span class="font-semibold">ข้อแนะนำสำหรับหลักสูตรที่เต็มจำนวน:</span>
                                <span>หากหลักสูตรมีผู้สมัครครบโควตาแล้ว ระบบจะแสดงสถานะ <em>"เต็มแล้ว"</em> ไม่สามารถกดสมัครได้ โดยท่านสามารถติดต่อฝ่าย HRD เพื่อขอสำรองที่นั่งในรุ่นถัดไป</span>
                            </div>
                        </div>
                    </div>

                    <!-- Soft State Box -->
                    <div class="lg:col-span-5 bg-amber-50/40 dark:bg-slate-900/60 rounded-xl p-4 border border-amber-100 dark:border-slate-700/60">
                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3">
                            สถานะปุ่มการสมัครในตาราง
                        </div>
                        <div class="space-y-3">
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                <span class="text-xs text-slate-600 dark:text-slate-300">เมื่อมีที่นั่งว่าง:</span>
                                <span class="px-3.5 py-1 text-xs font-semibold rounded-md bg-emerald-600 text-white">
                                    สมัครเลย
                                </span>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                <span class="text-xs text-slate-600 dark:text-slate-300">เมื่อที่นั่งเต็ม:</span>
                                <span class="px-3.5 py-1 text-xs font-semibold rounded-md bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                    เต็มแล้ว
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Step 3 Detailed Card (Soft Sky Accents) -->
            <article class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/70 p-5 sm:p-7 shadow-xs guide-card-print">
                <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border border-sky-200/80 dark:border-sky-800/60 font-bold text-sm shrink-0">
                            3
                        </span>
                        <div>
                            <span class="text-xs font-semibold text-sky-600 dark:text-sky-400">ขั้นตอนที่ 3</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                การยืนยันสิทธิ์และการตรวจสอบสถานะ (Instant Confirmation)
                            </h3>
                        </div>
                    </div>
                    <span class="text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 rounded-md shrink-0">
                        <i class="fa-solid fa-bolt text-sky-500 mr-1"></i> อัปเดตทันที
                    </span>
                </div>

                <div class="space-y-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    <p class="leading-relaxed">
                        เมื่อกดส่งข้อมูลสมัครสำเร็จ ระบบจะประมวลผลและปรับเปลี่ยนสถานะของหลักสูตรนั้นในหน้าจอของท่านทันที:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-xl border border-emerald-200/70 dark:border-emerald-800/40">
                            <div class="flex items-center gap-2 mb-2 font-semibold text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm">
                                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                                <span>ปุ่มเปลี่ยนเป็น "เข้าสู่หน้าการอบรม"</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                เมื่อการสมัครเสร็จสมบูรณ์ ปุ่มในตารางจะปรับสถานะให้ท่านเข้าถึงเอกสารและสื่อการสอนได้ตลอดเวลา
                            </p>
                        </div>

                        <div class="p-4 bg-sky-50/60 dark:bg-sky-950/20 rounded-xl border border-sky-200/70 dark:border-sky-800/40">
                            <div class="flex items-center gap-2 mb-2 font-semibold text-sky-900 dark:text-sky-200 text-xs sm:text-sm">
                                <i class="fa-solid fa-shield-halved text-sky-600 dark:text-sky-400"></i>
                                <span>สิทธิ์การเข้าร่วมและเอกสาร</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                ข้อมูลของท่านจะถูกส่งตรงไปยังบัญชีรายชื่อผู้เข้าอบรมของฝ่าย HRD เพื่อจัดเตรียมเอกสารและแบบประเมินผล
                            </p>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Step 4 Detailed Card (Soft Indigo Accents) -->
            <article class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/70 p-5 sm:p-7 shadow-xs guide-card-print">
                <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60 font-bold text-sm shrink-0">
                            4
                        </span>
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">ขั้นตอนที่ 4</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                การเข้าเรียนและการรับสื่อการสอน (Attending Class & Learning Materials)
                            </h3>
                        </div>
                    </div>
                    <span class="text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 rounded-md shrink-0">
                        <i class="fa-solid fa-file-pdf text-indigo-600 mr-1"></i> รองรับ PDF & สื่อออนไลน์
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-7 space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        <p class="leading-relaxed">
                            ในวันจัดอบรมหรือช่วงเตรียมตัวก่อนเรียน พนักงานสามารถเข้าถึงสื่อการเรียนการสอนได้สะดวกรวดเร็ว:
                        </p>
                        <ul class="space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 mt-1 shrink-0 text-xs"></i>
                                <span>คลิกปุ่มสีน้ำเงิน <strong>"เข้าสู่หน้าการอบรม"</strong> ในหน้าตารางกำหนดการ</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 mt-1 shrink-0 text-xs"></i>
                                <span><strong>เอกสารประกอบการสอน (PDF):</strong> หากหลักสูตรมีไฟล์เอกสาร ระบบจะเปิดไฟล์สไลด์หรือคู่มือให้ดาวน์โหลดทันที</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-check text-indigo-600 dark:text-indigo-400 mt-1 shrink-0 text-xs"></i>
                                <span><strong>ลิงก์ห้องเรียนออนไลน์:</strong> กรณีคอร์สออนไลน์ ระบบจะนำทางไปยังห้องประชุมออนไลน์ (เช่น MS Teams, Zoom หรือระบบ E-Learning องค์กร)</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Soft State Box -->
                    <div class="lg:col-span-5 bg-indigo-50/40 dark:bg-slate-900/60 rounded-xl p-4 border border-indigo-100 dark:border-slate-700/60">
                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3">
                            ปุ่มเข้าเรียนสำหรับผู้สมัครแล้ว
                        </div>
                        <div class="bg-white dark:bg-slate-800 p-4 rounded-lg border border-slate-200 dark:border-slate-700 text-center space-y-2">
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-xs">
                                <i class="fa-solid fa-file-pdf"></i> เข้าสู่หน้าการอบรม
                            </span>
                            <p class="text-[11px] text-slate-400">คลิกเพื่อเปิดดูเอกสารหรือเข้าสู่ห้องเรียน</p>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Step 5 Detailed Card (Soft Emerald Accents) -->
            <article class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/70 p-5 sm:p-7 shadow-xs guide-card-print">
                <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 font-bold text-sm shrink-0">
                            5
                        </span>
                        <div>
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">ขั้นตอนที่ 5</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                สำเร็จการฝึกอบรมและบันทึกประวัติพนักงาน (Completion & Career Record)
                            </h3>
                        </div>
                    </div>
                    <span class="text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 rounded-md shrink-0">
                        <i class="fa-solid fa-award text-emerald-600 dark:text-emerald-400 mr-1"></i> สะสมชั่วโมงพัฒนาตนเอง
                    </span>
                </div>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    <p class="leading-relaxed">
                        หลังผ่านการฝึกอบรมและได้รับการยืนยันการเข้าร่วมจากเจ้าหน้าที่ผู้จัด:
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mt-1 shrink-0 text-xs"></i>
                            <span>ระบบจะบันทึกจำนวนชั่วโมงการอบรมเข้าสู่ประวัติพนักงาน (HR Training Profile) โดยอัตโนมัติ</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mt-1 shrink-0 text-xs"></i>
                            <span>ชั่วโมงการอบรมจะถูกนำไปใช้เป็นเกณฑ์การประเมินผลการปฏิบัติงานประจำปี (KPIs) และการวางแผนเส้นทางความก้าวหน้าในสายอาชีพ (Career Path)</span>
                        </li>
                    </ul>
                </div>
            </article>

        </main>

        <!-- HR / ADMIN GUIDE TAB CONTENT (Initially Hidden) -->
        @if(Auth::check() && Auth::user()->isHrOrAdmin())
        <section id="adminGuideContent" class="hidden space-y-6">
            <div class="mb-2">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-[#B21F24]"></i>
                    <span>ขั้นตอนการจัดการระบบสำหรับฝ่าย HR และผู้ดูแลระบบ</span>
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    คู่มือการใช้งานฟังก์ชันหลังบ้านและระบบวิเคราะห์ผลการฝึกอบรม
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Admin Module 1: Soft Rose -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200/90 dark:border-slate-700/70 shadow-xs space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-folder-plus"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">1. จัดการข้อมูลหลักสูตร (Course Management)</h4>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        เข้าสู่เมนู <code class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-1.5 py-0.5 rounded">/backend/training</code> เพื่อสร้างคอร์สใหม่ ระบุหัวข้อ จำนวนชั่วโมง วันที่เริ่ม-สิ้นสุด และหน่วยงานผู้จัด
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 dark:text-slate-400">
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> อัปโหลดภาพปกหลักสูตร</li>
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> แนบเอกสาร PDF หรือลิงก์ห้องเรียนออนไลน์</li>
                    </ul>
                </div>

                <!-- Admin Module 2: Soft Sky -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200/90 dark:border-slate-700/70 shadow-xs space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/40 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">2. ตรวจสอบรายชื่อผู้สมัคร (Participant Tracking)</h4>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        ตรวจสอบรายชื่อพนักงานที่ลงทะเบียนในแต่ละหลักสูตร ตรวจสอบความถูกต้องของสังกัด และนำข้อมูลไปใช้สำหรับเช็คชื่อเข้าชั้นเรียน
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 dark:text-slate-400">
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> ดูยอดผู้สมัครแยกตามหลักสูตร</li>
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> กรองรายชื่อตามแผนกและฝ่ายงาน</li>
                    </ul>
                </div>

                <!-- Admin Module 3: Soft Indigo -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200/90 dark:border-slate-700/70 shadow-xs space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/40 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-chart-column"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">3. ติดตามสถิติ Training Dashboard</h4>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        เข้าใช้งาน <code class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-1.5 py-0.5 rounded">/training/dashboard</code> เพื่อดูแนวโน้มการอบรมประจำปีและสถิติรวมขององค์กร
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 dark:text-slate-400">
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> สรุปจำนวนชั่วโมงสะสมของพนักงาน</li>
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> วิเคราะห์หลักสูตรยอดนิยม</li>
                    </ul>
                </div>

                <!-- Admin Module 4: Soft Emerald -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200/90 dark:border-slate-700/70 shadow-xs space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">4. การออกรายงานและรับรองผล (Reporting & Compliance)</h4>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        ส่งออกข้อมูลชั่วโมงการฝึกอบรมเพื่อใช้รายงานตามเกณฑ์พระราชบัญญัติส่งเสริมการพัฒนาฝีมือแรงงาน และใช้ประกอบการพิจารณาประจำปี
                    </p>
                    <ul class="text-xs space-y-1.5 text-slate-500 dark:text-slate-400">
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> เอกสารรับรองผลการอบรม</li>
                        <li><i class="fa-solid fa-check text-emerald-600 mr-1.5"></i> ส่งออกข้อมูลรูปแบบ Excel / CSV</li>
                    </ul>
                </div>
            </div>
        </section>
        @endif

        <!-- FAQ Section (Comfortable Accordion with Soft Accents) -->
        <section class="mt-12 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/70 p-6 sm:p-8 shadow-xs guide-card-print" aria-labelledby="faq-heading">
            <div class="mb-6">
                <h3 id="faq-heading" class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-[#B21F24]"></i>
                    <span>คำถามที่พบบ่อย (Frequently Asked Questions)</span>
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    ข้อแนะนำและแนวทางแก้ไขข้อสงสัยในการใช้งานระบบสมัครฝึกอบรม
                </p>
            </div>

            <div class="space-y-3">
                <!-- FAQ 1 -->
                <details class="group bg-slate-50/70 dark:bg-slate-900/40 hover:bg-slate-50 dark:hover:bg-slate-900/60 rounded-lg border border-slate-200/80 dark:border-slate-700/60 transition-colors [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-4 cursor-pointer font-medium text-slate-900 dark:text-slate-100 text-sm">
                        <span class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 flex items-center justify-center text-xs shrink-0 font-bold">?</span>
                            <span>จะทราบได้อย่างไรว่าการสมัครฝึกอบรมสำเร็จแล้ว?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 group-open:-rotate-180 shrink-0 ml-2"></i>
                    </summary>
                    <div class="px-4 pb-4 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-700/50 leading-relaxed">
                        เมื่อลงทะเบียนสำเร็จ ระบบจะแสดงข้อความแจ้งเตือนสีเขียวที่ด้านบนของหน้าจอ และปุ่มของหลักสูตรนั้นจะเปลี่ยนจาก <strong class="text-emerald-700 dark:text-emerald-400">"สมัครเลย"</strong> เป็นปุ่มสีน้ำเงิน <strong class="text-blue-700 dark:text-blue-400">"เข้าสู่หน้าการอบรม"</strong> ทันที
                    </div>
                </details>

                <!-- FAQ 2 -->
                <details class="group bg-slate-50/70 dark:bg-slate-900/40 hover:bg-slate-50 dark:hover:bg-slate-900/60 rounded-lg border border-slate-200/80 dark:border-slate-700/60 transition-colors [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-4 cursor-pointer font-medium text-slate-900 dark:text-slate-100 text-sm">
                        <span class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 flex items-center justify-center text-xs shrink-0 font-bold">?</span>
                            <span>กรณีหลักสูตรระบุสถานะ "เต็มแล้ว" ต้องดำเนินการอย่างไร?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 group-open:-rotate-180 shrink-0 ml-2"></i>
                    </summary>
                    <div class="px-4 pb-4 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-700/50 leading-relaxed">
                        กรณีหลักสูตรเต็ม พนักงานสามารถติดต่อฝ่ายพัฒนาทรัพยากรบุคคล (HRD) โดยตรง เพื่อแจ้งความประสงค์สำรองที่นั่ง หรือขอเพิ่มรอบการจัดอบรมในรุ่นถัดไป
                    </div>
                </details>

                <!-- FAQ 3 -->
                <details class="group bg-slate-50/70 dark:bg-slate-900/40 hover:bg-slate-50 dark:hover:bg-slate-900/60 rounded-lg border border-slate-200/80 dark:border-slate-700/60 transition-colors [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between p-4 cursor-pointer font-medium text-slate-900 dark:text-slate-100 text-sm">
                        <span class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-md bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 flex items-center justify-center text-xs shrink-0 font-bold">?</span>
                            <span>สามารถดาวน์โหลดเอกสารประกอบการสอนย้อนหลังได้หรือไม่?</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 group-open:-rotate-180 shrink-0 ml-2"></i>
                    </summary>
                    <div class="px-4 pb-4 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 border-t border-slate-200/60 dark:border-slate-700/50 leading-relaxed">
                        ท่านสามารถเข้ามาคลิกปุ่ม <strong class="text-blue-700 dark:text-blue-400">"เข้าสู่หน้าการอบรม"</strong> ในหลักสูตรที่ตนเองเคยสมัครไว้ได้ตลอดเวลา เพื่อดาวน์โหลดไฟล์เอกสาร PDF หรือรับลิงก์สื่อการสอน
                    </div>
                </details>
            </div>
        </section>

        <!-- Helpdesk & Support Footer (Soft Gentle Tone) -->
        <footer class="mt-8 bg-gradient-to-r from-slate-50 via-white to-rose-50/30 dark:from-slate-800 dark:via-slate-800 dark:to-slate-800 rounded-xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-700/70 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 no-print">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-headset text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">ต้องการสอบถามข้อมูลเพิ่มเติมเกี่ยวกับการฝึกอบรม?</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">ติดต่อฝ่ายพัฒนาทรัพยากรบุคคล (HRD) ในวันและเวลาทำการ</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0">
                <a href="mailto:hr@kumwell.com" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-200 text-xs font-semibold px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 transition-colors shadow-xs">
                    <i class="fa-solid fa-envelope text-slate-400 text-xs"></i>
                    <span>ส่งอีเมลติดต่อฝ่าย HRD</span>
                </a>
            </div>
        </footer>

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
            empTab.className = "px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs transition-colors flex items-center gap-2";
            empTab.setAttribute('aria-selected', 'true');
            
            adminTab.className = "px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-2";
            adminTab.setAttribute('aria-selected', 'false');

            empContent.classList.remove('hidden');
            adminContent.classList.add('hidden');
        } else {
            adminTab.className = "px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs transition-colors flex items-center gap-2";
            adminTab.setAttribute('aria-selected', 'true');

            empTab.className = "px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-2";
            empTab.setAttribute('aria-selected', 'false');

            adminContent.classList.remove('hidden');
            empContent.classList.add('hidden');
        }
    }
</script>
@endsection
