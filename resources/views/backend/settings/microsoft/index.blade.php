@extends('layouts.app')

@section('content')
<div class="w-full" x-data="microsoftSettings()">

    {{-- Main Framed Container Card --}}
    <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-5 sm:p-7 transition-all">

        <!-- Breadcrumb & Header (Inside Frame) -->
        <div class="pb-5 mb-6 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <nav class="flex text-xs font-semibold text-slate-500 mb-1" aria-label="Breadcrumb">
                <a href="{{ route('welcome') }}" class="hover:text-indigo-600 transition">หน้าหลัก</a>
                <span class="mx-2 text-slate-400">/</span>
                <span class="text-slate-500">ตั้งค่าระบบ</span>
                <span class="mx-2 text-slate-400">/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-bold">Microsoft 365 & Azure Entra ID</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-sky-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
                    <i class="fa-brands fa-microsoft text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        ตั้งค่า Microsoft 365 (Azure Entra ID)
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        จัดการ Application Client ID, Client Secret และต่ออายุเมื่อ Secret หมดอายุการใช้งาน สำหรับส่งอีเมลผ่าน Microsoft Graph API
                    </p>
                </div>
            </div>
        </div>

        <!-- Status Badges -->
        <div class="flex flex-wrap items-center gap-2">
            @if($isConfigured)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    พร้อมใช้งาน (Configured)
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    ยังตั้งค่าไม่สมบูรณ์
                </span>
            @endif

            @if($expiryStatus === 'red')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-200 border border-rose-300 dark:border-rose-800 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400"></i>
                    Secret หมดอายุแล้ว!
                </span>
            @elseif($expiryStatus === 'orange')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-orange-50 text-orange-800 dark:bg-orange-950/50 dark:text-orange-300 border border-orange-300 dark:border-orange-700">
                    <i class="fa-solid fa-triangle-exclamation text-orange-600 dark:text-orange-400"></i>
                    ใกล้หมดอายุ (อีก {{ $daysRemaining }} วัน)
                </span>
            @elseif($expiryStatus === 'yellow')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-800 dark:bg-yellow-950/50 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700">
                    <i class="fa-solid fa-calendar-days text-yellow-600 dark:text-yellow-400"></i>
                    คงเหลือ {{ $daysRemaining }} วัน (เตือนล่วงหน้า)
                </span>
            @elseif($expiryStatus === 'blue')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                    <i class="fa-solid fa-calendar-check text-sky-600 dark:text-sky-400"></i>
                    คงเหลือ {{ $daysRemaining }} วัน
                </span>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-200 flex items-start gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg mt-0.5 shrink-0"></i>
            <div>
                <h4 class="font-bold text-sm">ดำเนินการสำเร็จ</h4>
                <p class="text-xs sm:text-sm mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 flex items-start gap-3 shadow-sm">
            <i class="fa-solid fa-circle-xmark text-rose-600 dark:text-rose-400 text-lg mt-0.5 shrink-0"></i>
            <div>
                <h4 class="font-bold text-sm">เกิดข้อผิดพลาด</h4>
                <p class="text-xs sm:text-sm mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 shadow-sm">
            <div class="flex items-center gap-2 font-bold text-sm mb-1 text-rose-700 dark:text-rose-300">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>กรุณาตรวจสอบข้อมูลที่กรอก:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Expiration Warning Alert Banner (if red, orange, or yellow) -->
    @if($expiryStatus === 'red')
        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-rose-500/10 via-rose-500/5 to-transparent border border-rose-300 dark:border-rose-800/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-300 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-rose-900 dark:text-rose-200">
                        Microsoft Client Secret หมดอายุแล้วเมื่อวันที่ {{ $expiryDate ? $expiryDate->format('d/m/Y') : '-' }}!
                    </h3>
                    <p class="text-xs text-rose-700 dark:text-rose-300/90 mt-0.5">
                        ส่งผลให้การเชื่อมต่อ Microsoft 365 หรือการรีเฟรช Token ของพนักงานไม่สามารถใช้งานได้ กรุณาสร้าง Secret ใหม่บน Azure Portal และนำ Value มาใส่ด้านล่างนี้
                    </p>
                </div>
            </div>
            <a href="#how-to-renew" class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-rose-600 text-white hover:bg-rose-700 transition shrink-0 self-start md:self-center shadow-sm">
                ดูขั้นตอนต่ออายุ <i class="fa-solid fa-arrow-down ml-1"></i>
            </a>
        </div>
    @elseif($expiryStatus === 'orange')
        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-orange-500/10 via-orange-500/5 to-transparent border border-orange-300 dark:border-orange-800/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/60 text-orange-700 dark:text-orange-300 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-orange-900 dark:text-orange-200">
                        แจ้งเตือนด่วน: Client Secret จะหมดอายุในอีก {{ $daysRemaining }} วัน ({{ $expiryDate ? $expiryDate->format('d/m/Y') : '-' }})
                    </h3>
                    <p class="text-xs text-orange-700 dark:text-orange-300/90 mt-0.5">
                        เหลือเวลาไม่ถึง 30 วัน ควรรีบสร้าง Client Secret ชุดใหม่บน Azure Portal มาเปลี่ยนเพื่อความต่อเนื่องในการทำงาน
                    </p>
                </div>
            </div>
            <a href="#how-to-renew" class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-orange-600 text-white hover:bg-orange-700 transition shrink-0 self-start md:self-center shadow-sm">
                ดูวิธีสร้าง Secret ใหม่ <i class="fa-solid fa-arrow-down ml-1"></i>
            </a>
        </div>
    @elseif($expiryStatus === 'yellow')
        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-yellow-500/10 via-yellow-500/5 to-transparent border border-yellow-300 dark:border-yellow-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-yellow-100 dark:bg-yellow-900/60 text-yellow-800 dark:text-yellow-300 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-yellow-900 dark:text-yellow-200">
                        แจ้งเตือนล่วงหน้า: Client Secret จะหมดอายุในอีก {{ $daysRemaining }} วัน ({{ $expiryDate ? $expiryDate->format('d/m/Y') : '-' }})
                    </h3>
                    <p class="text-xs text-yellow-800 dark:text-yellow-300/90 mt-0.5">
                        ระบบเริ่มเหลือน้อยลง (เหลือไม่ถึง 60 วัน) แนะนำให้เตรียมวางแผนสร้าง Secret ชุดใหม่
                    </p>
                </div>
            </div>
            <a href="#how-to-renew" class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-yellow-600 text-white hover:bg-yellow-700 transition shrink-0 self-start md:self-center shadow-sm">
                ดูวิธีเตรียมต่ออายุ <i class="fa-solid fa-arrow-down ml-1"></i>
            </a>
        </div>
    @endif

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Stat 1 -->
        <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 text-lg">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 dark:text-slate-400">สถานะระบบคลาวด์</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                    {{ $isConfigured ? 'Microsoft Entra ID' : 'ยังไม่ได้เชื่อมต่อ' }}
                </p>
            </div>
        </div>

        <!-- Stat 2 -->
        <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-lg">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 dark:text-slate-400">บัญชีที่เชื่อมต่อแล้ว</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white">
                    {{ $connectedUsersCount }} บัญชีพนักงาน
                </p>
            </div>
        </div>

        <!-- Stat 3 -->
        <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
            @php
                $statIconBg = match($expiryStatus) {
                    'red' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400',
                    'orange' => 'bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400',
                    'yellow' => 'bg-yellow-50 dark:bg-yellow-950/50 text-yellow-600 dark:text-yellow-400',
                    'blue' => 'bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400',
                    default => 'bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400',
                };
            @endphp
            <div class="w-11 h-11 rounded-xl {{ $statIconBg }} flex items-center justify-center shrink-0 text-lg transition-colors">
                <i class="fa-regular fa-calendar-xmark"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 dark:text-slate-400">วันหมดอายุ Secret</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                    @if($expiryDate)
                        {{ $expiryDate->format('d/m/Y') }} 
                        @if($expiryStatus === 'red')
                            <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">(หมดอายุแล้ว)</span>
                        @elseif($expiryStatus === 'orange')
                            <span class="text-[11px] font-bold text-orange-600 dark:text-orange-400">(เหลือ {{ $daysRemaining }} วัน)</span>
                        @elseif($expiryStatus === 'yellow')
                            <span class="text-[11px] font-semibold text-yellow-600 dark:text-yellow-400">(เหลือ {{ $daysRemaining }} วัน)</span>
                        @elseif($expiryStatus === 'blue')
                            <span class="text-[11px] font-medium text-sky-600 dark:text-sky-400">(เหลือ {{ $daysRemaining }} วัน)</span>
                        @endif
                    @else
                        <span class="text-slate-400 text-xs font-normal">ยังไม่ได้ระบุ</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Stat 4 -->
        <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 text-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 dark:text-slate-400">ช่องทางสำรอง</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                    Hybrid SMTP Fallback
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Form Section (2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-key text-indigo-600 dark:text-indigo-400"></i>
                            ข้อมูลการกำหนดค่า Azure App Credentials
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            แก้ไขค่าและบันทึกลงไฟล์ระบบเพื่ออัปเดตการเชื่อมต่อทันที
                        </p>
                    </div>

                    <!-- Test Connection Button -->
                    <button type="button" @click="testConnection()" :disabled="testing"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 dark:bg-sky-950/50 dark:text-sky-300 dark:hover:bg-sky-900/60 border border-sky-200 dark:border-sky-800 transition disabled:opacity-50">
                        <i class="fa-solid fa-bolt" x-show="!testing"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="testing"></i>
                        <span x-text="testing ? 'กำลังตรวจสอบ...' : 'ทดสอบการเชื่อมต่อ Azure'"></span>
                    </button>
                </div>

                <!-- Test Connection Alert Result -->
                <div x-show="testResult" x-cloak class="p-4 mx-5 mt-4 rounded-xl border text-xs leading-relaxed transition-all"
                    :class="testResult && testResult.success 
                        ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200' 
                        : 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200'">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2">
                            <i class="text-sm mt-0.5 shrink-0" :class="testResult && testResult.success ? 'fa-solid fa-circle-check text-emerald-600' : 'fa-solid fa-circle-xmark text-rose-600'"></i>
                            <div>
                                <p class="font-bold" x-text="testResult ? testResult.message : ''"></p>
                                <template x-if="testResult && testResult.details">
                                    <div class="mt-1 text-[11px] opacity-80 space-y-0.5">
                                        <p><strong>Tenant:</strong> <span x-text="testResult.details.tenant"></span></p>
                                        <p><strong>Token Endpoint:</strong> <span x-text="testResult.details.token_endpoint"></span></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <button type="button" @click="testResult = null" class="text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <!-- Save Status Alerts -->
                <div x-show="saveMessage" x-cloak class="p-4 mx-5 mt-4 rounded-xl border bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 flex items-center gap-2 text-xs font-bold shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    <span x-text="saveMessage"></span>
                </div>
                <div x-show="saveError" x-cloak class="p-4 mx-5 mt-4 rounded-xl border bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 flex items-center gap-2 text-xs font-bold shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-sm"></i>
                    <span x-text="saveError"></span>
                </div>

                <form @submit.prevent="saveSettings()" action="{{ route('backend.settings.microsoft.update') }}" method="POST" class="p-5 space-y-5">
                    @csrf

                    <!-- Client ID -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="client_id" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span>Application (Client) ID</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" @click="copyToClipboard(clientId, 'คัดลอก Client ID แล้ว')" class="text-[11px] text-indigo-600 hover:text-indigo-700 font-medium">
                                <i class="fa-regular fa-copy"></i> คัดลอก
                            </button>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-id-badge text-sm"></i>
                            </div>
                            <input type="text" id="client_id" name="client_id" x-model="clientId" required
                                placeholder="เช่น 9715f2f3-b558-4c7a-9fc3-34c699766f3b"
                                class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#16181D] text-slate-900 dark:text-white text-xs sm:text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            ได้จากหน้า <em>Azure Portal > Microsoft Entra ID > App registrations > Overview</em>
                        </p>
                    </div>

                    <!-- Client Secret Value -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="client_secret" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span>Client Secret (Value)</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="showSecret = !showSecret" class="text-[11px] text-slate-500 hover:text-indigo-600 font-medium">
                                    <i class="fa-solid" :class="showSecret ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    <span x-text="showSecret ? 'ซ่อนรหัส' : 'แสดงรหัส'"></span>
                                </button>
                                <button type="button" @click="copyToClipboard(clientSecret, 'คัดลอก Secret Value แล้ว')" class="text-[11px] text-indigo-600 hover:text-indigo-700 font-medium">
                                    <i class="fa-regular fa-copy"></i> คัดลอก
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input :type="showSecret ? 'text' : 'password'" id="client_secret" name="client_secret" x-model="clientSecret" required
                                placeholder="กรอก Client Secret Value (ไม่ใช่ Secret ID)"
                                class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#16181D] text-slate-900 dark:text-white text-xs sm:text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            <button type="button" @click="showSecret = !showSecret" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                <i class="fa-solid" :class="showSecret ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            สำคัญ: นำค่าจากคอลัมน์ <strong>Value</strong> เท่านั้น (ไม่ใช่ Secret ID) และเมื่อสร้างใหม่บน Azure ต้องคัดลอกทันทีก่อนที่ Azure จะซ่อนค่า
                        </p>
                    </div>

                    <!-- Directory (Tenant) ID -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="tenant_id" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span>Directory (Tenant) ID</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="tenantId = 'common'" class="text-[11px] text-slate-500 hover:text-indigo-600 font-medium">
                                    ใช้ 'common' (ทุกองค์กร)
                                </button>
                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                <button type="button" @click="copyToClipboard(tenantId, 'คัดลอก Tenant ID แล้ว')" class="text-[11px] text-indigo-600 hover:text-indigo-700 font-medium">
                                    <i class="fa-regular fa-copy"></i> คัดลอก
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-building text-sm"></i>
                            </div>
                            <input type="text" id="tenant_id" name="tenant_id" x-model="tenantId" required
                                placeholder="เช่น aed6dd7a-baec-4ce4-b204-a6bbc71b2748 หรือ common"
                                class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#16181D] text-slate-900 dark:text-white text-xs sm:text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            ระบุ Tenant ID เฉพาะขององค์กร Kumwell เพื่อความปลอดภัย หรือใส่ <code>common</code> สำหรับ Multi-tenant
                        </p>
                    </div>

                    <!-- Redirect URI -->
                    <div x-data="{ customRedirectUri: false }">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <label for="redirect_uri" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                    <span>Redirect URI (Callback URL)</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                    :class="!customRedirectUri ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800'"
                                    x-text="!customRedirectUri ? 'Auto ตามระบบ' : 'กำหนดเอง (Custom)'">
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="customRedirectUri = !customRedirectUri; if(!customRedirectUri) redirectUri = recommendedUri;"
                                    class="text-[11px] font-medium text-slate-500 hover:text-indigo-600 transition">
                                    <i class="fa-solid" :class="customRedirectUri ? 'fa-lock mr-1' : 'fa-pen mr-1'"></i>
                                    <span x-text="customRedirectUri ? 'กลับไปใช้ Auto' : 'แก้ไขเอง'"></span>
                                </button>
                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                <button type="button" @click="copyToClipboard(redirectUri, 'คัดลอก Redirect URI แล้ว')" class="text-[11px] text-indigo-600 hover:text-indigo-700 font-bold">
                                    <i class="fa-regular fa-copy"></i> คัดลอก URL
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-link text-sm"></i>
                            </div>
                            <input type="url" id="redirect_uri" name="redirect_uri" x-model="redirectUri" required
                                :readonly="!customRedirectUri"
                                placeholder="https://your-domain.com/auth/microsoft/callback"
                                :class="!customRedirectUri ? 'bg-slate-50 dark:bg-[#13151A] text-slate-600 dark:text-slate-300 cursor-not-allowed' : 'bg-white dark:bg-[#16181D] text-slate-900 dark:text-white'"
                                class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs sm:text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        </div>
                        <div class="mt-1 flex items-start gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-circle-info text-indigo-500 mt-0.5 shrink-0"></i>
                            <span>
                                <strong>ไม่ต้องแก้ไขในช่องนี้</strong> ให้ใช้ค่า Auto ของระบบ แล้วกดปุ่ม <strong>"คัดลอก URL"</strong> นำไปวางใน <em>Azure Portal &gt; Authentication &gt; Redirect URIs</em> ได้เลยครับ
                            </span>
                        </div>
                    </div>

                    <!-- Secret Expiry Date -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="secret_expires_at" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-indigo-600"></i>
                                <span>วันหมดอายุของ Client Secret (Secret Expiry Date)</span>
                                <span class="text-[11px] font-normal text-slate-400">(แนะนำให้ระบุ)</span>
                            </label>
                        </div>
                        <div class="relative max-w-sm">
                            <input type="date" id="secret_expires_at" name="secret_expires_at" x-model="secretExpiresAt"
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#16181D] text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            ระบุวันหมดอายุตามที่สร้างไว้บน Azure Portal ระบบจะช่วยนับถอยหลังและแจ้งเตือนล่วงหน้า 30 วันก่อนที่ระบบจะหยุดทำงาน
                        </p>
                    </div>

                    <!-- Submit Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="text-[11px] text-slate-400">
                            <i class="fa-solid fa-circle-info mr-1"></i> เมื่อกดบันทึก ระบบจะเขียนไฟล์ <code>.env</code> และล้างแคชให้โดยอัตโนมัติ
                        </div>

                        <div class="flex items-center gap-2.5">
                            <button type="submit" :disabled="saving"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white shadow-md shadow-indigo-600/25 transition disabled:opacity-50 cursor-pointer">
                                <i class="fa-solid fa-spinner fa-spin" x-show="saving"></i>
                                <i class="fa-solid fa-floppy-disk" x-show="!saving"></i>
                                <span x-text="saving ? 'กำลังบันทึกข้อมูล...' : 'บันทึกการตั้งค่า'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Connected Accounts List -->
            <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-users text-indigo-600 dark:text-indigo-400"></i>
                            บัญชีเจ้าหน้าที่ที่เชื่อมต่อ Microsoft 365 อยู่ในระบบ
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            เจ้าหน้าที่เหล่านี้สามารถส่งอีเมลออกในนามตนเองผ่าน Microsoft Graph API ได้ทันที
                        </p>
                    </div>
                    
                    @if(Auth::user()->hasMicrosoftConnected())
                        <form action="{{ route('auth.microsoft.disconnect') }}" method="POST" onsubmit="return confirm('ยืนยันยกเลิกการเชื่อมต่อบัญชี Microsoft ของคุณ?')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800 transition">
                                <i class="fa-solid fa-link-slash mr-1"></i> ยกเลิกบัญชีของฉัน
                            </button>
                        </form>
                    @else
                        <a href="{{ route('auth.microsoft.redirect') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                            <i class="fa-brands fa-microsoft mr-1"></i> เชื่อมต่อบัญชีของฉัน
                        </a>
                    @endif
                </div>

                @if($connectedUsers->isEmpty())
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <i class="fa-brands fa-microsoft text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                        ยังไม่มีเจ้าหน้าที่ท่านใดเชื่อมต่อบัญชี Microsoft 365 (ระบบจะส่งผ่าน SMTP ปกติ)
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-100 dark:border-slate-800 font-medium">
                                    <th class="pb-2">เจ้าหน้าที่</th>
                                    <th class="pb-2">อีเมล Microsoft 365</th>
                                    <th class="pb-2">เชื่อมต่อล่าสุด</th>
                                    <th class="pb-2 text-center">สถานะ</th>
                                    <th class="pb-2 text-center">จัดการ (Admin)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
                                @foreach($connectedUsers as $token)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                        <td class="py-2.5 font-medium">
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ $token->user->fullname ?? 'ผู้ใช้งาน' }}</span>
                                                @if($token->user_id === Auth::id())
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">คุณ</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-2.5 font-mono text-slate-600 dark:text-slate-400">
                                            <i class="fa-brands fa-microsoft text-indigo-500 mr-1"></i>
                                            {{ $token->microsoft_email ?? '-' }}
                                        </td>
                                        <td class="py-2.5 text-slate-400">
                                            {{ $token->updated_at ? $token->updated_at->diffForHumans() : '-' }}
                                        </td>
                                        <td class="py-2.5 text-center">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        </td>
                                        <td class="py-2.5 text-center">
                                            <form action="{{ route('backend.settings.microsoft.disconnect-user', $token->id) }}" method="POST" 
                                                class="inline-block" 
                                                onsubmit="return confirm('ยืนยันยกเลิกการเชื่อมต่อบัญชี Microsoft 365 ของคุณ {{ addslashes($token->user->fullname ?? $token->microsoft_email ?? 'ผู้ใช้งานนี้') }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    title="ยกเลิกการเชื่อมต่อบัญชีนี้"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-700 border border-rose-200 dark:border-rose-800 transition">
                                                    <i class="fa-solid fa-link-slash text-xs"></i>
                                                    <span>ยกเลิกการเชื่อมต่อ</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar / Guidance Column (1 Column) -->
        <div class="space-y-6">

            <!-- How to renew guide card -->
            <div id="how-to-renew" class="bg-gradient-to-br from-indigo-50/70 via-white to-sky-50/50 dark:from-slate-800/60 dark:via-[#1E2129] dark:to-slate-800/30 rounded-2xl border border-indigo-100 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center gap-2.5 mb-3 text-indigo-700 dark:text-indigo-400">
                    <i class="fa-solid fa-book-open text-base"></i>
                    <h3 class="font-bold text-sm">ขั้นตอนสร้างหรือต่ออายุ Client Secret</h3>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                    เมื่อ Client Secret หมดอายุการใช้งาน (ปกติทุก 6 เดือน หรือ 1-2 ปี) เจ้าหน้าที่ไอทีสามารถสร้างรหัสชุดใหม่ได้ใน 4 ขั้นตอน:
                </p>

                <ol class="text-xs text-slate-600 dark:text-slate-400 space-y-3">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-200">เข้าสู่ Azure Portal</p>
                            <p class="text-[11px] text-slate-500">ไปที่ <a href="https://portal.azure.com/#view/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/~/RegisteredApps" target="_blank" class="text-indigo-600 hover:underline">portal.azure.com</a> > <strong>Microsoft Entra ID</strong> > <strong>App registrations</strong></p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-200">เลือก Certificates & secrets</p>
                            <p class="text-[11px] text-slate-500">คลิกที่แอปพลิเคชัน Kumwell HR แล้วเลือกเมนู <strong>Certificates & secrets</strong> ในแถบด้านซ้าย</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-200">กด + New client secret</p>
                            <p class="text-[11px] text-slate-500">ตั้งคำอธิบาย เช่น <code>Kumwell-HR-2026</code> เลือกวันหมดอายุ (เช่น 24 months) แล้วกด <strong>Add</strong></p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">4</span>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-slate-200">คัดลอก Value มาวางในหน้านี้</p>
                            <p class="text-[11px] text-slate-500">คัดลอกค่าจากคอลัมน์ <strong>Value</strong> (ไม่ใช่ Secret ID) นำมาวางในช่อง Client Secret ด้านซ้าย แล้วกด <strong>บันทึกการตั้งค่า</strong></p>
                        </div>
                    </li>
                </ol>

                <div class="mt-4 pt-3 border-t border-indigo-100 dark:border-slate-800">
                    <a href="https://portal.azure.com" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-slate-700 hover:bg-indigo-50 transition shadow-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>เปิด Azure Portal ในแท็บใหม่</span>
                    </a>
                </div>
            </div>

            <!-- Permission Checklist Card -->
            <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                    <i class="fa-solid fa-list-check text-indigo-600"></i>
                    สิทธิ์ Delegated Permissions ที่จำเป็น
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                    ใน Azure App Registration > API permissions ต้องมีสิทธิ์ Microsoft Graph เหล่านี้:
                </p>

                <div class="space-y-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-800">
                        <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">Mail.Send</div>
                        <p class="text-[11px] text-slate-500 mt-0.5">สิทธิ์ในการส่งอีเมลในนามผู้ใช้</p>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-800">
                        <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">User.Read</div>
                        <p class="text-[11px] text-slate-500 mt-0.5">อ่านโปรไฟล์และอีเมลของผู้ใช้ที่ล็อกอิน</p>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-800">
                        <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">offline_access</div>
                        <p class="text-[11px] text-slate-500 mt-0.5">รับ Refresh Token เพื่อให้ระบบส่งเมลต่อได้เมื่อ Access Token หมดอายุ</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    </div>

</div>

<!-- Alpine.js Component -->
<script>
    function microsoftSettings() {
        return {
            clientId: '{{ addslashes($clientId) }}',
            clientSecret: '{{ addslashes($clientSecret) }}',
            tenantId: '{{ addslashes($tenantId) }}',
            redirectUri: '{{ addslashes($redirectUri) }}',
            recommendedUri: '{{ addslashes($recommendedRedirectUri) }}',
            secretExpiresAt: '{{ $secretExpiresAt }}',
            showSecret: false,
            testing: false,
            testResult: null,
            saving: false,
            saveMessage: null,
            saveError: null,

            saveSettings() {
                this.saving = true;
                this.saveMessage = null;
                this.saveError = null;

                fetch('{{ route("backend.settings.microsoft.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        client_id: this.clientId,
                        client_secret: this.clientSecret,
                        tenant_id: this.tenantId,
                        redirect_uri: this.redirectUri,
                        secret_expires_at: this.secretExpiresAt
                    })
                })
                .then(async (res) => {
                    let data = {};
                    try { data = await res.json(); } catch(e) {}
                    if (res.ok && data.success !== false) {
                        this.saveMessage = data.message || 'บันทึกและอัปเดตการตั้งค่า Microsoft 365 เรียบร้อยแล้ว';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } else {
                        this.saveError = data.message || 'เกิดข้อผิดพลาดในการบันทึก กรุณาตรวจสอบข้อมูลอีกครั้ง';
                    }
                })
                .catch((err) => {
                    this.saveMessage = 'บันทึกการตั้งค่าเรียบร้อยแล้ว กำลังรีโหลดหน้าเว็บ...';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                })
                .finally(() => {
                    this.saving = false;
                });
            },

            testConnection() {
                this.testing = true;
                this.testResult = null;

                fetch('{{ route("backend.settings.microsoft.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tenant_id: this.tenantId,
                        client_id: this.clientId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.testResult = data;
                })
                .catch(err => {
                    this.testResult = {
                        success: false,
                        message: 'การเชื่อมต่อขัดข้อง: ' + err.message
                    };
                })
                .finally(() => {
                    this.testing = false;
                });
            },

            copyToClipboard(text, successMsg) {
                if (!text) return;
                navigator.clipboard.writeText(text).then(() => {
                    alert(successMsg || 'คัดลอกลงคลิปบอร์ดแล้ว');
                }).catch(() => {
                    const el = document.createElement('textarea');
                    el.value = text;
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                    alert(successMsg || 'คัดลอกลงคลิปบอร์ดแล้ว');
                });
            }
        }
    }
</script>
@endsection
