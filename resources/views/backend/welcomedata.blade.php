@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-5 sm:p-7 space-y-6">
        
        <!-- Header Bar Inside Frame -->
        <div class="pb-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-semibold text-slate-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('welcome') }}" class="hover:text-indigo-600 transition">หน้าหลัก</a>
                    <span class="mx-2 text-slate-400">/</span>
                    <span class="text-slate-800 dark:text-slate-200">ระบบจัดการข้อมูลหลัก HR (Master Data Management)</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <i class="fa-solid fa-database text-red-600"></i> ระบบจัดการข้อมูลหลัก HR (Master Data Management)
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    ระบบศูนย์กลางสำหรับแอดมินและฝ่ายบุคคลในการจัดการข้อมูลตั้งค่าหลักของระบบ เช่น ข้อมูลโครงสร้างองค์กร สิทธิ์ผู้ใช้งาน และรูปแบบเอกสารคำร้องต่างๆ
                </p>
            </div>
            <div class="flex items-center gap-2.5 bg-slate-50 dark:bg-slate-800/80 px-4 py-2 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-xs shrink-0 self-start sm:self-auto">
                <i class="fa-solid fa-shield-halved text-amber-500 text-lg"></i>
                <div class="text-left">
                    <div class="text-[10px] text-slate-400">สิทธิ์การเข้าถึง</div>
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200">ผู้ดูแลระบบสูงสุด (HR Admin)</div>
                </div>
            </div>
        </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200/50 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-gray-400">บุคลากรทั้งหมด</div>
                <div class="text-lg font-bold">{{ $counts['users'] }} คน</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200/50 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                <i class="fa-solid fa-sitemap text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-gray-400">ฝ่าย/แผนก/ส่วนงาน</div>
                <div class="text-lg font-bold">{{ $counts['divisions'] + $counts['departments'] + $counts['sections'] }} กลุ่ม</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200/50 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                <i class="fa-solid fa-file-invoice text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-gray-400">รูปแบบประเภทคำร้อง</div>
                <div class="text-lg font-bold">{{ $counts['types'] }} รายการ</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200/50 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg">
                <i class="fa-solid fa-newspaper text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-gray-400">ข่าวประชาสัมพันธ์</div>
                <div class="text-lg font-bold">{{ $counts['news'] }} เรื่อง</div>
            </div>
        </div>
    </div>

    <!-- Master Data Modules Grid -->
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <i class="fa-solid fa-cubes text-red-500"></i> โมดูลการจัดการข้อมูลหลัก (Master Modules)
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- 1. Users Management -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users-gear text-2xl"></i>
                    </div>
                    <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['users'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">จัดการข้อมูลพนักงาน</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    เพิ่ม แก้ไข และลบข้อมูลพนักงาน กำหนดข้อมูลสังกัด สิทธิ์การใช้งาน และสถานะการทำงานของพนักงานทั้งหมดในระบบ
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('users.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                    <span>จัดการพนักงาน</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 2. User Types Management -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user-shield text-2xl"></i>
                    </div>
                    <span class="bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['usertypes'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">จัดการประเภทผู้ใช้งาน</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    จัดการบทบาทและสิทธิ์เข้าใช้งานระบบ (Roles & Permissions) ของสมาชิกแต่ละกลุ่ม เช่น Admin, HR, Manager หรือ พนักงานทั่วไป
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('usertypes.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 transition-colors">
                    <span>จัดการประเภทผู้ใช้</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 3. Divisions Management -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-sitemap text-2xl"></i>
                    </div>
                    <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['divisions'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">จัดการฝ่ายงาน (Divisions)</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    จัดการโครงสร้างสายงานในระดับสูงสุด (ฝ่าย) เช่น ฝ่ายปฏิบัติการ, ฝ่ายเทคโนโลยีสารสนเทศ, ฝ่ายการตลาดและจัดซื้อ
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('divisions.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    <span>จัดการฝ่าย</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 4. Departments Management -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-building-user text-2xl"></i>
                    </div>
                    <span class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['departments'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">จัดการแผนกงาน (Departments)</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    จัดการโครงสร้างสังกัดย่อยในระดับแผนกที่เชื่อมต่อกับฝ่ายงาน เช่น แผนกพัฒนาซอฟต์แวร์, แผนกบัญชีและการเงิน
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('departments.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                    <span>จัดการแผนก</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 5. Sections Management -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-lime-50 dark:bg-lime-900/20 text-lime-650 dark:text-lime-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-network-wired text-2xl"></i>
                    </div>
                    <span class="bg-lime-50 dark:bg-lime-900/30 text-lime-650 dark:text-lime-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['sections'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">จัดการส่วนงาน (Sections)</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    จัดการส่วนงานย่อยยิบย่อยในโครงสร้างระดับล่างสุดของแต่ละแผนก เพื่อระบุทีมงานเฉพาะทางให้ชัดเจนยิ่งขึ้น
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('sections.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-lime-600 dark:text-lime-400 hover:text-lime-700 dark:hover:text-lime-300 transition-colors">
                    <span>จัดการส่วนงาน</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 6. Request Categories -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-folder-tree text-2xl"></i>
                    </div>
                    <span class="bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['categories'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">หมวดหมู่เอกสารคำร้อง</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    กำหนดหมวดหมู่กลุ่มใหญ่สำหรับการร้องขอสวัสดิการ สัญญาการทำงาน หรือคำร้องทั่วไปของทรัพยากรบุคคล
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('request-categories.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                    <span>จัดการหมวดหมู่คำร้อง</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 7. Request Types -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-file-invoice text-2xl"></i>
                    </div>
                    <span class="bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['types'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">ประเภทเอกสารคำร้อง</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    เพิ่มและตั้งค่าแบบฟอร์มคำร้อง เช่น คำร้องขอใบรับรองการทำงาน, คำร้องสวัสดิการเครื่องแบบ, หรือคำร้องแก้ไขเวลาเข้างาน
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('request-types.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition-colors">
                    <span>จัดการประเภทคำร้อง</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 8. Request Subtypes -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-file-signature text-2xl"></i>
                    </div>
                    <span class="bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['subtypes'] }} รายการ
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">ประเภทย่อยเอกสารคำร้อง</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    จัดการข้อมูลตัวเลือกเพิ่มเติมในระดับย่อยของคำร้อง เช่น จุดประสงค์การขอหนังสือรับรอง หรือขนาดชุดยูนิฟอร์มเฉพาะ
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('request-subtypes.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 transition-colors">
                    <span>จัดการประเภทย่อยคำร้อง</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- 9. News Management -->
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3.5 bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 rounded-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-newspaper text-2xl"></i>
                    </div>
                    <span class="bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 text-xs px-2.5 py-1 rounded-full font-semibold">
                        {{ $counts['news'] }} เรื่อง
                    </span>
                </div>
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-2">จัดการข่าวประชาสัมพันธ์</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    ลงทะเบียนข่าวสารประกาศ กิจกรรมบริษัท และประกาศสำคัญขององค์กรที่แสดงผลให้พนักงานทุกคนรับทราบที่หน้าแรก
                </p>
            </div>
            <div class="px-5 py-3.5 bg-gray-50 dark:bg-gray-850/50 border-t border-gray-100 dark:border-gray-700/50">
                <a href="{{ route('news.index') }}" class="w-full flex items-center justify-between text-xs font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
                    <span>จัดการข่าวสาร</span>
                    <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection