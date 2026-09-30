@extends('layouts.app')

@section('content')
<div class="w-full" x-data="emailTemplateEditor()">

    {{-- Main Framed Container Card --}}
    <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-5 sm:p-7 transition-all">
        
        <!-- Header Section -->
        <div class="pb-5 mb-6 border-b border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex text-xs font-semibold text-slate-500 mb-1" aria-label="Breadcrumb">
                <a href="{{ route('welcome') }}" class="hover:text-indigo-600">หน้าหลัก</a>
                <span class="mx-2 text-slate-400">/</span>
                <span class="text-slate-500">ระบบสรรหาบุคลากร</span>
                <span class="mx-2 text-slate-400">/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-bold">ตั้งค่าข้อความอีเมล</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-envelope-open-text text-indigo-600 dark:text-indigo-400"></i>
                ตั้งค่าข้อความแจ้งเตือนทางอีเมล (Email Templates)
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                ปรับแต่งข้อความและคำแจ้งเตือนในอีเมล พร้อมระบบแสดงผลตัวอย่างแบบ Realtime และส่งทดสอบได้ทันที
            </p>
        </div>

        <!-- Quick Actions Top -->
        <div class="flex items-center gap-2.5">
            <button type="button" @click="openTestModal()"
                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition">
                <i class="fa-regular fa-paper-plane text-emerald-600"></i>
                <span>ทดลองส่งเข้าอีเมลจริง</span>
            </button>

            <button type="button" @click="resetToDefault()"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-slate-700 transition"
                title="รีเซ็ตข้อความของเทมเพลตนี้กลับเป็นค่าเริ่มต้นของระบบ">
                <i class="fa-solid fa-rotate-left"></i>
                <span>คืนค่าเริ่มต้น</span>
            </button>

            <button type="button" @click="saveTemplate()" :disabled="isSaving"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/20 transition disabled:opacity-50">
                <i class="fa-solid fa-check" x-show="!isSaving"></i>
                <i class="fa-solid fa-spinner fa-spin" x-show="isSaving"></i>
                <span x-text="isSaving ? 'กำลังบันทึก...' : 'บันทึกการแก้ไข'"></span>
            </button>
        </div>
    </div>

    <!-- Template Selector Tabs -->
    <div class="mb-6 bg-slate-50/80 dark:bg-slate-800/40 p-2 rounded-xl border border-slate-200/70 dark:border-slate-800/80 shadow-xs overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max">
            @foreach($templates as $t)
                <button type="button" 
                    @click="selectTemplate('{{ $t->key }}')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-2"
                    :class="currentKey === '{{ $t->key }}' 
                        ? 'bg-indigo-600 text-white shadow-sm' 
                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $t->theme_color }};"></span>
                    <span>{{ $t->name }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toast.show" x-transition.opacity.duration.300ms
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl border text-sm font-semibold"
        :class="toast.type === 'success' ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-rose-600 text-white border-rose-500'"
        style="display: none;">
        <i :class="toast.type === 'success' ? 'fa-solid fa-circle-check text-lg' : 'fa-solid fa-circle-exclamation text-lg'"></i>
        <span x-text="toast.message"></span>
    </div>

    <!-- Split Screen 2-Columns Layout: LEFT = Live Preview, RIGHT = Editor Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ========================================== -->
        <!-- LEFT COLUMN: Realtime Live Preview (7 cols) -->
        <!-- ========================================== -->
        <div class="lg:col-span-6 xl:col-span-7 space-y-3 lg:sticky lg:top-4">
            
            <!-- Preview Controls Bar -->
            <div class="flex items-center justify-between px-3 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 font-bold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-desktop text-indigo-600"></i>
                    <span>ตัวแสดงผลตัวอย่าง (Live Preview)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        Realtime
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Device Selector -->
                    <div class="inline-flex rounded-md shadow-sm border border-slate-300 dark:border-slate-700 p-0.5 bg-white dark:bg-slate-900">
                        <button type="button" @click="deviceMode = 'desktop'"
                            :class="deviceMode === 'desktop' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                            class="px-2.5 py-1 text-xs rounded font-semibold transition" title="จอคอม / แท็บเล็ต iPad">
                            <i class="fa-solid fa-desktop mr-1"></i> จอคอม / iPad
                        </button>
                        <button type="button" @click="deviceMode = 'mobile'"
                            :class="deviceMode === 'mobile' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                            class="px-2.5 py-1 text-xs rounded font-semibold transition" title="จอมือถือ (ขยายเต็มซ้าย-ขวา)">
                            <i class="fa-solid fa-mobile-screen mr-1"></i> จอมือถือ
                        </button>
                    </div>

                    <!-- Variable Toggle -->
                    <button type="button" @click="showSampleData = !showSampleData"
                        :class="showSampleData ? 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300' : 'bg-white text-slate-600 dark:bg-slate-900 dark:text-slate-400 border-slate-300'"
                        class="px-2.5 py-1 text-xs rounded border font-semibold transition flex items-center gap-1"
                        title="สลับการแสดงผลระหว่างข้อมูลตัวอย่าง กับ รหัสแท็ก {tag}">
                        <i class="fa-solid fa-tags"></i>
                        <span x-text="showSampleData ? 'ข้อมูลตัวอย่าง' : 'แสดงแท็ก {tag}'"></span>
                    </button>
                </div>
            </div>

            <!-- Mail Preview Shell -->
            <div class="bg-slate-200/90 dark:bg-slate-900 p-4 sm:p-6 rounded-2xl border border-slate-300/80 dark:border-slate-800 shadow-inner overflow-x-hidden min-h-[550px] flex justify-center">
                
                <!-- Email Container Outer -->
                <div class="transition-all duration-300 w-full"
                    :class="deviceMode === 'mobile' ? 'max-w-full' : 'max-w-[620px]'">
                    
                    <!-- Email Card mimicking master layout -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-md transition-all"
                        :style="'border-top: 5px solid ' + form.theme_color + '; font-family: Sarabun, sans-serif; color: #1f2937; line-height: 1.6;'">
                        
                        <!-- Email Header -->
                        <div class="p-6 text-center border-b border-slate-100 bg-white">
                            <img :src="form.header_logo_url || defaultHeaderFooter.header_logo_url" 
                                alt="Kumwell Logo" 
                                class="mx-auto block" 
                                style="max-width: 190px; height: auto;">
                            <div style="color: #ea580c; font-size: 11px; font-weight: 700; letter-spacing: 2px; margin-top: 6px;"
                                x-text="form.header_tagline || defaultHeaderFooter.header_tagline">
                            </div>
                            <h1 class="text-xl font-bold mt-3 mb-0"
                                :style="'color: ' + form.theme_color + ';'"
                                x-text="renderedText('title') || 'หัวข้อพาดหัวอีเมล'">
                            </h1>
                        </div>

                        <!-- Email Body Content -->
                        <div class="p-6 sm:p-8 bg-white">
                            
                            <!-- Greeting -->
                            <div class="text-base font-semibold text-slate-900 mb-4"
                                x-show="form.greeting"
                                x-text="renderedText('greeting')">
                            </div>

                            <!-- Highlight Box -->
                            <div class="highlight-box my-5 transition-colors"
                                :style="'background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-left: 5px solid ' + form.theme_color + '; padding: 18px 20px; border-radius: 10px;'"
                                x-show="form.badge_text || currentKey !== 'direct_email'">
                                
                                <div style="margin-bottom: 10px;" x-show="form.badge_text">
                                    <span class="badge"
                                        :style="'display: inline-block; background-color: #f1f5f9; color: ' + form.theme_color + '; font-weight: 700; font-size: 12.5px; padding: 4px 12px; border-radius: 20px; border: 1px solid #e2e8f0;'"
                                        x-text="renderedText('badge_text')">
                                    </span>
                                </div>

                                <table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #334155;">
                                    <tr>
                                        <td style="padding: 4px 0; width: 130px; font-weight: 600; color: #64748b;">ตำแหน่งงาน:</td>
                                        <td style="padding: 4px 0; font-weight: 700; font-size: 15px;"
                                            :style="'color: ' + form.theme_color + ';'"
                                            x-text="showSampleData ? sampleVars.position_name : '{position_name}'"></td>
                                    </tr>
                                    <tr x-show="currentKey !== 'new_application_ha_notification'">
                                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">สังกัด / แผนก:</td>
                                        <td style="padding: 4px 0; font-weight: 500; color: #1e293b;"
                                            x-text="showSampleData ? sampleVars.department_name : '{department_name}'"></td>
                                    </tr>
                                    <tr x-show="currentKey !== 'new_application_ha_notification'">
                                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">เลขที่ใบสมัคร:</td>
                                        <td style="padding: 4px 0; font-family: monospace; font-weight: 600; color: #0f172a;"
                                            x-text="showSampleData ? sampleVars.application_no : '{application_no}'"></td>
                                    </tr>
                                    <tr x-show="showSampleData && sampleVars.applied_date">
                                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">วันที่ส่งใบสมัคร:</td>
                                        <td style="padding: 4px 0; color: #334155;"
                                            x-text="sampleVars.applied_date"></td>
                                    </tr>
                                    <tr x-show="currentKey.includes('interview')">
                                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">วันสัมภาษณ์:</td>
                                        <td style="padding: 4px 0; font-weight: 600; color: #ea580c;"
                                            x-text="showSampleData ? (sampleVars.interview_date + ' (เวลา ' + sampleVars.interview_time + ')') : '{interview_date} (เวลา {interview_time})'"></td>
                                    </tr>
                                    <tr x-show="currentKey.includes('hired') || currentKey.includes('offered')">
                                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">วันเริ่มงาน:</td>
                                        <td style="padding: 4px 0; font-weight: 700; color: #10b981;"
                                            x-text="showSampleData ? sampleVars.onboarding_date : '{onboarding_date}'"></td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Body Text -->
                            <div class="leading-relaxed whitespace-pre-line my-4"
                                style="font-size: 14.5px; line-height: 1.75; color: #374151;"
                                x-html="nl2br(renderedText('body_text')) || '<span class=\'text-slate-400\'>ข้อความเนื้อหาหลักของอีเมล...</span>'">
                            </div>

                            <!-- Notice Card / Next steps -->
                            <div class="notice-card my-5"
                                :style="'background-color: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid ' + form.theme_color + '; border-radius: 8px; padding: 14px 18px; margin: 22px 0; font-size: 13.5px; color: #9a3412;'"
                                x-show="form.notice_title || form.notice_text">
                                <strong style="color: #c2410c; font-size: 14px; display: block; margin-bottom: 4px;" x-text="renderedText('notice_title')"></strong>
                                <div style="margin-top: 4px; white-space: pre-line; line-height: 1.6; color: #7c2d12;" x-html="nl2br(renderedText('notice_text'))"></div>
                            </div>

                            <!-- Closing Text -->
                            <div class="my-4"
                                style="margin-top: 22px; color: #4b5563; font-size: 14px; line-height: 1.6;"
                                x-show="form.closing_text"
                                x-text="renderedText('closing_text')">
                            </div>

                            <!-- Signature -->
                            <div class="signature" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 13px;">
                                <p style="margin: 0; font-weight: 600; color: #1f2937;" x-text="form.footer_salutation || defaultHeaderFooter.footer_salutation"></p>
                                <p style="margin: 5px 0 0 0; color: #4b5563; line-height: 1.6;">
                                    <strong x-text="renderedSenderName()"></strong><br>
                                    <span style="font-size: 13px; color: #6b7280;" x-text="form.sender_position || defaultHeaderFooter.sender_position"></span><br>
                                    <span x-text="form.company_name || defaultHeaderFooter.company_name"></span><br>
                                    <span style="font-size: 13px; color: #6b7280;">
                                        โทร: <span x-text="form.contact_phone || defaultHeaderFooter.contact_phone"></span> | 
                                        เว็บไซต์: <a :href="(form.contact_website && form.contact_website.startsWith('http')) ? form.contact_website : ('https://' + (form.contact_website || 'www.kumwell.com'))" target="_blank" :style="'color: ' + form.theme_color + ';'" x-text="form.contact_website || defaultHeaderFooter.contact_website"></a> | 
                                        อีเมล: <span :style="'color: ' + form.theme_color + ';'" x-text="renderedContactEmail()"></span>
                                    </span>
                                </p>
                            </div>

                        </div>

                        <!-- Email Footer -->
                        <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 border-t border-slate-100"
                            x-html="renderedFooterCopyright()">
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <!-- ========================================== -->
        <!-- RIGHT COLUMN: Realtime Editor Form (5 cols) -->
        <!-- ========================================== -->
        <div class="lg:col-span-6 xl:col-span-5 bg-white dark:bg-[#1E2129] p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full" :style="'background-color: ' + form.theme_color + ';'"></span>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white" x-text="currentTemplate.name"></h2>
                </div>
                <span class="text-xs text-slate-400 font-mono" x-text="currentKey"></span>
            </div>

            <!-- Tab Navigation for Form -->
            <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-semibold">
                <button type="button" @click="currentTab = 'content'"
                    :class="currentTab === 'content' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 py-2 px-2.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-regular fa-envelope"></i>
                    <span>ข้อความหลัก</span>
                </button>
                <button type="button" @click="currentTab = 'header'"
                    :class="currentTab === 'header' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 py-2 px-2.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-heading"></i>
                    <span>ส่วนหัว (Header)</span>
                </button>
                <button type="button" @click="currentTab = 'footer'"
                    :class="currentTab === 'footer' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 py-2 px-2.5 rounded-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-signature"></i>
                    <span>ส่วนท้าย & ลายเซ็น</span>
                </button>
            </div>

            <!-- Form Fields -->
            <form @submit.prevent="saveTemplate()" class="space-y-4">

                <!-- ================= TAB 1: MAIN CONTENT ================= -->
                <div x-show="currentTab === 'content'" class="space-y-4">
                    <!-- Variable Tags Helper -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                            <span>💡 แท็กตัวแปรที่นำไปวางในข้อความได้ (คลิกเพื่อคัดลอก):</span>
                        </label>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="tag in availableTags" :key="tag.code">
                                <button type="button" @click="insertTag(tag.code)"
                                    class="px-2 py-1 rounded bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 dark:bg-slate-800 dark:hover:bg-slate-700 text-[11px] font-mono font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition"
                                    :title="tag.desc">
                                    <span x-text="tag.code"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            หัวข้ออีเมล (Email Subject) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" x-model="form.subject"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Title & Theme Color -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                พาดหัวในอีเมล (Header Title) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="form.title"
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                สีธีม (Theme Color)
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="form.theme_color"
                                    class="w-10 h-9 p-0.5 rounded cursor-pointer border border-slate-300 dark:border-slate-700 bg-white">
                                <input type="text" x-model="form.theme_color"
                                    class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Greeting -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                ข้อความคำทักทาย (Greeting)
                            </label>
                            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                <i class="fa-solid fa-lock text-[10px]"></i> ระบบดึงชื่อผู้รับอัตโนมัติ (Disabled)
                            </span>
                        </div>
                        <input type="text" x-model="form.greeting" disabled readonly
                            class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 cursor-not-allowed select-none focus:outline-none">
                    </div>

                    <!-- Badge Text -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            ข้อความป้ายสถานะในการ์ดไฮไลต์ (Pill Badge)
                        </label>
                        <input type="text" x-model="form.badge_text"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Body Text -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            ข้อความหลัก (Body Content)
                        </label>
                        <textarea rows="5" x-model="form.body_text"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-sans"></textarea>
                        <span class="text-[11px] text-slate-400">รองรับการขึ้นบรรทัดใหม่ และสามารถแทรกแท็กตัวแปรได้</span>
                    </div>

                    <!-- Notice Card -->
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-3">
                        <div class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-amber-500"></i>
                            <span>กล่องคำแนะนำ / ขั้นตอนถัดไป (Notice Card)</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                หัวข้อกล่องคำแนะนำ (Notice Title)
                            </label>
                            <input type="text" x-model="form.notice_title"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                เนื้อหาในกล่องคำแนะนำ (Notice Content)
                            </label>
                            <textarea rows="3" x-model="form.notice_text"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white"></textarea>
                        </div>
                    </div>

                    <!-- Closing Text -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            ข้อความปิดท้าย (Closing Text)
                        </label>
                        <textarea rows="2" x-model="form.closing_text"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white"></textarea>
                    </div>
                </div>

                <!-- ================= TAB 2: HEADER SETTINGS ================= -->
                <div x-show="currentTab === 'header'" class="space-y-4">
                    <div class="p-3 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 flex items-start gap-2.5">
                        <i class="fa-solid fa-image text-amber-600 dark:text-amber-400 mt-0.5"></i>
                        <p class="text-xs text-amber-900 dark:text-amber-200">
                            ปรับแต่งภาพโลโก้และสโลแกนส่วนหัวของอีเมล การเปลี่ยนแปลงจะแสดงตัวอย่างสดทางด้านซ้ายทันที
                        </p>
                    </div>

                    <!-- Header Logo URL -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                ลิงก์รูปภาพโลโก้ (Header Logo URL)
                            </label>
                            <button type="button" @click="form.header_logo_url = defaultHeaderFooter.header_logo_url"
                                class="text-[11px] text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-semibold underline">
                                ใช้โลโก้เริ่มต้น Kumwell
                            </button>
                        </div>
                        <input type="url" x-model="form.header_logo_url" placeholder="https://..."
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-[11px] text-slate-400 block mt-1">แนะนำรูปภาพขนาดความกว้างประมาณ 350-400px พื้นหลังโปร่งใส (PNG)</span>

                        <!-- Logo Preview -->
                        <div class="mt-2.5 p-3 rounded-lg bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-center">
                            <img :src="form.header_logo_url || defaultHeaderFooter.header_logo_url" alt="Logo Preview" class="max-h-12 object-contain"
                                onerror="this.src='{{ asset('images/logo/logo-wide.png') }}'">
                        </div>
                    </div>

                    <!-- Header Tagline -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            ข้อความสโลแกนใต้โลโก้ (Header Tagline)
                        </label>
                        <input type="text" x-model="form.header_tagline" placeholder="POWER OF INNOVATION"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-[11px] text-slate-400 block mt-1">แสดงเป็นตัวอักษรสีส้มพร้อมเว้นระยะตัวอักษรอย่างสง่างามใต้โลโก้</span>
                    </div>
                </div>

                <!-- ================= TAB 3: FOOTER & SIGNATURE ================= -->
                <div x-show="currentTab === 'footer'" class="space-y-4">
                    <div class="p-3 rounded-xl bg-indigo-50/80 dark:bg-indigo-950/30 border border-indigo-200/80 dark:border-indigo-900/50 flex items-start gap-2.5">
                        <i class="fa-solid fa-signature text-indigo-600 dark:text-indigo-400 mt-0.5"></i>
                        <p class="text-xs text-indigo-900 dark:text-indigo-200">
                            ปรับแต่งลายเซ็น คำลงท้าย ข้อมูลผู้ส่ง เบอร์โทร เว็บไซต์ และข้อความสงวนลิขสิทธิ์ด้านล่างสุด
                        </p>
                    </div>

                    <!-- Salutation -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            คำลงท้ายก่อนลงชื่อ (Salutation)
                        </label>
                        <input type="text" x-model="form.footer_salutation" placeholder="ด้วยความเคารพอย่างสูง,"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Sender Name & Position -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                ชื่อผู้ส่งในลายเซ็น (Sender Name)
                            </label>
                            <input type="text" x-model="form.sender_name" placeholder="กิตติพัฒน์ มานุช หรือ {admin_name}"
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <span class="text-[10px] text-slate-400 block mt-0.5">ระบุชื่อหรือใส่ {admin_name} ดึงชื่อผู้ส่งอัตโนมัติ</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                ตำแหน่งผู้ส่ง (Sender Position)
                            </label>
                            <input type="text" x-model="form.sender_position" placeholder="เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล"
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            ชื่อองค์กร / บริษัท (Company Name)
                        </label>
                        <input type="text" x-model="form.company_name" placeholder="บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Contact Details: Phone, Website, Email -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-850 border border-slate-200 dark:border-slate-700/80 space-y-3">
                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-address-book text-slate-500"></i>
                            <span>ข้อมูลติดต่อในลายเซ็น (Contact Info)</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                    เบอร์โทรศัพท์ (Phone)
                                </label>
                                <input type="text" x-model="form.contact_phone" placeholder="02-954-3455"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                    เว็บไซต์ (Website)
                                </label>
                                <input type="text" x-model="form.contact_website" placeholder="www.kumwell.com"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                อีเมลติดต่อ (Email)
                            </label>
                            <input type="text" x-model="form.contact_email" placeholder="Kittipat.Ma@kumwell.com หรือ {admin_email}"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                            <span class="text-[10px] text-slate-400 block mt-0.5">ระบุอีเมลตรงๆ หรือใส่ {admin_email} ดึงอีเมลผู้ส่งอัตโนมัติ</span>
                        </div>
                    </div>

                    <!-- Footer Copyright -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            ข้อความสงวนลิขสิทธิ์ด้านล่างสุด (Footer Copyright)
                        </label>
                        <input type="text" x-model="form.footer_copyright" placeholder="สงวนลิขสิทธิ์ © {year} บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)"
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <span class="text-[11px] text-slate-400 block mt-1">รองรับแท็ก <code>{year}</code> สำหรับแทนที่ปี พ.ศ. ปัจจุบัน</span>
                    </div>
                </div>

                <!-- Apply to All Templates Option -->
                <div class="p-3.5 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 flex items-start gap-2.5 transition">
                    <input type="checkbox" id="apply_to_all_templates" x-model="form.apply_to_all_templates"
                        class="mt-0.5 rounded border-indigo-300 dark:border-indigo-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <label for="apply_to_all_templates" class="text-xs text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                        <strong class="text-indigo-700 dark:text-indigo-400 font-semibold block">นำการตั้งค่า Header & Footer นี้ไปใช้กับทุกเทมเพลตอีเมล (9 รายการ)</strong>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">เมื่อบันทึก โลโก้ คำลงท้าย ลายเซ็น และข้อมูลติดต่อจะถูกซิงก์อัปเดตไปยังทุกเทมเพลตพร้อมกันทันที</span>
                    </label>
                </div>

                <!-- Save Button Bar -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-400">
                        แก้ไขล่าสุดโดย: <span x-text="currentTemplate.updater ? currentTemplate.updater.fullname : 'ค่าเริ่มต้นระบบ'"></span>
                    </span>

                    <button type="submit" :disabled="isSaving"
                        class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/25 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk" x-show="!isSaving"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="isSaving"></i>
                        <span x-text="isSaving ? 'กำลังบันทึกข้อมูล...' : 'บันทึกการแก้ไขข้อมูล'"></span>
                    </button>
                </div>

            </form>
        </div>

    </div>

    <!-- Test Send Modal -->
    <div x-show="testModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4"
        style="display: none;">
        
        <div @click.away="testModalOpen = false"
            class="bg-white dark:bg-[#1E2129] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-left">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-regular fa-paper-plane text-indigo-600"></i>
                    <span>ทดลองส่งอีเมลตัวอย่างจริง</span>
                </h3>
                <button type="button" @click="testModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                ระบบจะสร้างตัวอย่างอีเมลจากข้อความที่คุณกำลังปรับแต่ง และส่งไปยังที่อยู่อีเมลที่คุณระบุ เพื่อทดสอบการแสดงผลจริงบนโทรศัพท์มือถือ หรือโปรแกรมอ่านอีเมล
            </p>

            <form @submit.prevent="sendTestEmail()">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        ระบุอีเมลปลายทางสำหรับทดสอบ <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" x-model="testEmail" required
                        placeholder="example@gmail.com"
                        class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="testModalOpen = false"
                        class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                        ยกเลิก
                    </button>
                    <button type="submit" :disabled="isSendingTest"
                        class="px-5 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/25 transition flex items-center gap-2">
                        <i class="fa-regular fa-paper-plane" x-show="!isSendingTest"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="isSendingTest"></i>
                        <span x-text="isSendingTest ? 'กำลังส่งอีเมล...' : 'ส่งอีเมลทดสอบ'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    </div>

</div>

<script>
function emailTemplateEditor() {
    return {
        templates: @json($templates),
        currentKey: '{{ $activeTemplate->key }}',
        sampleVars: @json($sampleVariables),
        defaultHeaderFooter: @json($defaultHeaderFooter),
        currentTab: 'content', // content, header, or footer
        deviceMode: 'desktop', // desktop or mobile
        showSampleData: true,
        isSaving: false,
        testModalOpen: false,
        testEmail: '{{ Auth::user()?->email ?? "asdasdasd445566gz@gmail.com" }}',
        isSendingTest: false,
        toast: { show: false, message: '', type: 'success' },

        form: {
            subject: @json($activeTemplate->subject ?? ''),
            title: @json($activeTemplate->title ?? ''),
            theme_color: @json($activeTemplate->theme_color ?? ''),
            badge_text: @json($activeTemplate->badge_text ?? ''),
            greeting: @json($activeTemplate->greeting ?? ''),
            body_text: @json($activeTemplate->body_text ?? ''),
            notice_title: @json($activeTemplate->notice_title ?? ''),
            notice_text: @json($activeTemplate->notice_text ?? ''),
            closing_text: @json($activeTemplate->closing_text ?? ''),
            header_logo_url: @json($activeTemplate->header_logo_url ?? $defaultHeaderFooter["header_logo_url"]),
            header_tagline: @json($activeTemplate->header_tagline ?? $defaultHeaderFooter["header_tagline"]),
            footer_salutation: @json($activeTemplate->footer_salutation ?? $defaultHeaderFooter["footer_salutation"]),
            sender_name: @json($activeTemplate->sender_name ?? $defaultHeaderFooter["sender_name"]),
            sender_position: @json($activeTemplate->sender_position ?? $defaultHeaderFooter["sender_position"]),
            company_name: @json($activeTemplate->company_name ?? $defaultHeaderFooter["company_name"]),
            contact_phone: @json($activeTemplate->contact_phone ?? $defaultHeaderFooter["contact_phone"]),
            contact_website: @json($activeTemplate->contact_website ?? $defaultHeaderFooter["contact_website"]),
            contact_email: @json($activeTemplate->contact_email ?? $defaultHeaderFooter["contact_email"]),
            footer_copyright: @json($activeTemplate->footer_copyright ?? $defaultHeaderFooter["footer_copyright"]),
            apply_to_all_templates: false,
        },

        availableTags: [
            { code: '{applicant_name}', desc: 'ชื่อ-นามสกุล ผู้สมัคร' },
            { code: '{position_name}', desc: 'ตำแหน่งงานที่สมัคร' },
            { code: '{department_name}', desc: 'ฝ่าย / แผนก' },
            { code: '{application_no}', desc: 'เลขที่ใบสมัคร' },
            { code: '{applied_date}', desc: 'วันที่ส่งใบสมัคร' },
            { code: '{interview_round}', desc: 'รอบที่สัมภาษณ์' },
            { code: '{interview_date}', desc: 'วันที่นัดสัมภาษณ์' },
            { code: '{interview_time}', desc: 'เวลานัดสัมภาษณ์' },
            { code: '{onboarding_date}', desc: 'กำหนดวันเริ่มต้นทำงาน' },
            { code: '{company_name}', desc: 'ชื่อบริษัท' },
            { code: '{admin_name}', desc: 'ชื่อผู้ใช้งานที่ส่งอีเมล' },
            { code: '{admin_email}', desc: 'อีเมลของผู้ส่ง' },
        ],

        get currentTemplate() {
            return this.templates.find(t => t.key === this.currentKey) || this.templates[0];
        },

        selectTemplate(key) {
            this.currentKey = key;
            const t = this.currentTemplate;
            this.form.subject = t.subject || '';
            this.form.title = t.title || '';
            this.form.theme_color = t.theme_color || '#ea580c';
            this.form.badge_text = t.badge_text || '';
            this.form.greeting = t.greeting || '';
            this.form.body_text = t.body_text || '';
            this.form.notice_title = t.notice_title || '';
            this.form.notice_text = t.notice_text || '';
            this.form.closing_text = t.closing_text || '';
            this.form.header_logo_url = t.header_logo_url || this.defaultHeaderFooter.header_logo_url;
            this.form.header_tagline = t.header_tagline || this.defaultHeaderFooter.header_tagline;
            this.form.footer_salutation = t.footer_salutation || this.defaultHeaderFooter.footer_salutation;
            this.form.sender_name = t.sender_name || this.defaultHeaderFooter.sender_name;
            this.form.sender_position = t.sender_position || this.defaultHeaderFooter.sender_position;
            this.form.company_name = t.company_name || this.defaultHeaderFooter.company_name;
            this.form.contact_phone = t.contact_phone || this.defaultHeaderFooter.contact_phone;
            this.form.contact_website = t.contact_website || this.defaultHeaderFooter.contact_website;
            this.form.contact_email = t.contact_email || this.defaultHeaderFooter.contact_email;
            this.form.footer_copyright = t.footer_copyright || this.defaultHeaderFooter.footer_copyright;
            this.form.apply_to_all_templates = false;

            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('key', key);
            window.history.replaceState({}, '', url);
        },

        renderedText(field) {
            let val = this.form[field] || '';
            if (!this.showSampleData) {
                return val;
            }

            // Replace {tags} with sample variables
            for (const [k, v] of Object.entries(this.sampleVars)) {
                val = val.replaceAll('{' + k + '}', v);
            }
            return val;
        },

        renderedSenderName() {
            let name = this.form.sender_name || this.defaultHeaderFooter.sender_name;
            if (this.showSampleData) {
                name = name.replaceAll('{admin_name}', this.sampleVars.admin_name || 'กิตติพัฒน์ มานุช')
                           .replaceAll('{sender_name}', this.sampleVars.sender_name || 'ฝ่ายทรัพยากรบุคคล');
            }
            return name;
        },

        renderedContactEmail() {
            let email = this.form.contact_email || this.defaultHeaderFooter.contact_email;
            if (this.showSampleData) {
                email = email.replaceAll('{admin_email}', this.sampleVars.admin_email || 'Kittipat.Ma@kumwell.com')
                             .replaceAll('{sender_email}', this.sampleVars.sender_email || 'recruitment@kumwell.com');
            }
            return email;
        },

        renderedFooterCopyright() {
            let copy = this.form.footer_copyright || this.defaultHeaderFooter.footer_copyright;
            const yr = this.sampleVars.year || '2569';
            return copy.replaceAll('{year}', yr);
        },

        nl2br(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/\n/g, '<br>');
        },

        insertTag(tagCode) {
            // Append to body_text or copy to clipboard
            navigator.clipboard.writeText(tagCode);
            this.showToast('คัดลอกแท็ก ' + tagCode + ' แล้ว นำไปวางในช่องที่ต้องการได้ทันที', 'success');
        },

        showToast(message, type = 'success') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3500);
        },

        async saveTemplate() {
            this.isSaving = true;
            try {
                const res = await fetch(`{{ url('backend/recruitment/email-templates') }}/${this.currentKey}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await res.json();
                if (data.success) {
                    if (this.form.apply_to_all_templates) {
                        // Header & footer applied to all templates locally
                        this.templates.forEach(t => {
                            t.header_logo_url = this.form.header_logo_url;
                            t.header_tagline = this.form.header_tagline;
                            t.footer_salutation = this.form.footer_salutation;
                            t.sender_name = this.form.sender_name;
                            t.sender_position = this.form.sender_position;
                            t.company_name = this.form.company_name;
                            t.contact_phone = this.form.contact_phone;
                            t.contact_website = this.form.contact_website;
                            t.contact_email = this.form.contact_email;
                            t.footer_copyright = this.form.footer_copyright;
                        });
                    }
                    // Update in local templates array
                    const idx = this.templates.findIndex(t => t.key === this.currentKey);
                    if (idx !== -1) {
                        this.templates[idx] = Object.assign({}, this.templates[idx], data.template || this.form);
                    }
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'บันทึกไม่สำเร็จ', 'error');
                }
            } catch (err) {
                this.showToast('เกิดข้อผิดพลาดในการบันทึก: ' + err.message, 'error');
            } finally {
                this.isSaving = false;
            }
        },

        async resetToDefault() {
            if (!confirm('คุณต้องการรีเซ็ตข้อความของเทมเพลตนี้กลับเป็นค่าเริ่มต้นใช่หรือไม่?')) {
                return;
            }

            try {
                const res = await fetch(`{{ url('backend/recruitment/email-templates') }}/${this.currentKey}/reset`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });

                const data = await res.json();
                if (data.success) {
                    const t = data.template;
                    this.form.subject = t.subject;
                    this.form.title = t.title;
                    this.form.theme_color = t.theme_color;
                    this.form.badge_text = t.badge_text || '';
                    this.form.greeting = t.greeting || '';
                    this.form.body_text = t.body_text || '';
                    this.form.notice_title = t.notice_title || '';
                    this.form.notice_text = t.notice_text || '';
                    this.form.closing_text = t.closing_text || '';
                    this.form.header_logo_url = t.header_logo_url || this.defaultHeaderFooter.header_logo_url;
                    this.form.header_tagline = t.header_tagline || this.defaultHeaderFooter.header_tagline;
                    this.form.footer_salutation = t.footer_salutation || this.defaultHeaderFooter.footer_salutation;
                    this.form.sender_name = t.sender_name || this.defaultHeaderFooter.sender_name;
                    this.form.sender_position = t.sender_position || this.defaultHeaderFooter.sender_position;
                    this.form.company_name = t.company_name || this.defaultHeaderFooter.company_name;
                    this.form.contact_phone = t.contact_phone || this.defaultHeaderFooter.contact_phone;
                    this.form.contact_website = t.contact_website || this.defaultHeaderFooter.contact_website;
                    this.form.contact_email = t.contact_email || this.defaultHeaderFooter.contact_email;
                    this.form.footer_copyright = t.footer_copyright || this.defaultHeaderFooter.footer_copyright;
                    this.showToast(data.message, 'success');
                }
            } catch (err) {
                this.showToast('เกิดข้อผิดพลาด: ' + err.message, 'error');
            }
        },

        openTestModal() {
            this.testModalOpen = true;
        },

        async sendTestEmail() {
            if (!this.testEmail) return;
            this.isSendingTest = true;

            try {
                const res = await fetch(`{{ url('backend/recruitment/email-templates') }}/${this.currentKey}/test-send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        recipient_email: this.testEmail,
                        ...this.form
                    }),
                });

                const data = await res.json();
                if (data.success) {
                    this.testModalOpen = false;
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'ส่งอีเมลทดสอบไม่สำเร็จ', 'error');
                }
            } catch (err) {
                this.showToast('เกิดข้อผิดพลาดในการส่ง: ' + err.message, 'error');
            } finally {
                this.isSendingTest = false;
            }
        }
    };
}
</script>
@endsection
