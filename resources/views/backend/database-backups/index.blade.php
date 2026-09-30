@extends('layouts.app')

@section('title', 'ระบบสำรองฐานข้อมูลอัตโนมัติ (Database Backups)')

@section('content')
<div class="space-y-6" x-data="{ 
    backupModalOpen: false, 
    isBackingUp: false,
    passwordModalOpen: false,
    isFetchingPassword: false,
    currentPasswordInfo: null,
    copied: false,
    isIctVerified: {{ $ictAuth['is_verified'] ? 'true' : 'false' }},
    ictEmail: '{{ addslashes($ictAuth['email'] ?? '') }}',
    ictName: '{{ addslashes($ictAuth['name'] ?? '') }}',
    ictDepartment: '{{ addslashes($ictAuth['department'] ?? 'Information Communication Technology (ICT)') }}',
    hasConnectedMicrosoft: {{ $ictAuth['has_connected_microsoft'] ? 'true' : 'false' }},
    connectedEmail: '{{ addslashes($ictAuth['connected_email'] ?? '') }}',
    selectedBackupId: null,
    selectedBackupFilename: '',
    authErrorMsg: '',
    isVerifyingConnected: false,

    openPasswordModal(id, filename) {
        this.selectedBackupId = id;
        this.selectedBackupFilename = filename;
        this.currentPasswordInfo = null;
        this.authErrorMsg = '';
        this.passwordModalOpen = true;
    },

    async requestPassword() {
        if (!this.selectedBackupId) return;
        this.isFetchingPassword = true;
        this.authErrorMsg = '';
        try {
            const res = await fetch(`{{ url('backend/database-backups') }}/${this.selectedBackupId}/password`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.currentPasswordInfo = data;
            } else {
                if (data.requires_auth) {
                    this.isIctVerified = false;
                }
                this.authErrorMsg = data.message || 'ไม่สามารถดึงข้อมูลรหัสผ่านได้';
            }
        } catch (e) {
            this.authErrorMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
        } finally {
            this.isFetchingPassword = false;
        }
    },

    async quickVerifyConnected() {
        this.isVerifyingConnected = true;
        this.authErrorMsg = '';
        try {
            const res = await fetch(`{{ route('backend.database-backups.verify-connected-microsoft') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.isIctVerified = true;
                this.ictEmail = data.verification.email;
                this.ictName = data.verification.name;
                this.ictDepartment = data.verification.department;
            } else {
                this.authErrorMsg = data.message || 'การตรวจสอบล้มเหลว';
            }
        } catch (e) {
            this.authErrorMsg = 'เกิดข้อผิดพลาดในการตรวจสอบบัญชี Microsoft';
        } finally {
            this.isVerifyingConnected = false;
        }
    }
}">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-300 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-800 text-rose-800 dark:text-rose-200 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-300 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#1E2129] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-1">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Disaster Recovery & Military-Grade AES-256 Protection</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-database text-indigo-600 dark:text-indigo-400"></i>
                <span>ระบบสำรองฐานข้อมูลอัตโนมัติ (Database Backups)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                รันอัตโนมัติทุกวันตอนเที่ยงคืน (00:00 น.) เข้ารหัส ZIP ด้วย AES-256 สุ่มรหัสผ่านส่งเข้าอีเมล ICT พร้อมสร้างคู่มือ .md
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Clean Old Backups Button -->
            <form action="{{ route('backend.database-backups.clean-old') }}" method="POST"
                onsubmit="return confirm('ยืนยันการลบไฟล์สำรองฐานข้อมูลที่เก็บไว้เกิน 30 วันออกจากระบบ?');">
                @csrf
                <button type="submit"
                    class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs border border-slate-300 dark:border-slate-700 transition flex items-center gap-1.5 shadow-sm"
                    title="ลบไฟล์สำรองเก่าที่เกิน 30 วันออกเพื่อคืนพื้นที่ดิสก์">
                    <i class="fa-solid fa-broom text-amber-500"></i>
                    <span>ล้างไฟล์เกิน 30 วัน</span>
                </button>
            </form>

            <!-- Backup Now Button -->
            <button type="button" @click="backupModalOpen = true"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/25 transition flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-down"></i>
                <span>สำรองฐานข้อมูลทันที (Backup Now)</span>
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Backups -->
        <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-file-zipper text-xl"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block">ไฟล์สำรองทั้งหมด</span>
                <span class="text-2xl font-black text-slate-800 dark:text-white">{{ number_format($totalCount) }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">ไฟล์บน Private Storage</span>
            </div>
        </div>

        <!-- 2. Total Disk Usage -->
        <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-lock text-xl"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block">พื้นที่จัดเก็บ & เข้ารหัส</span>
                <span class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono">{{ $totalSizeHuman }}</span>
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium block mt-0.5">เข้ารหัส AES-256 ทุกไฟล์</span>
            </div>
        </div>

        <!-- 3. Automated Midnight Schedule -->
        <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-clock text-xl"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block">กำหนดการทำงานอัตโนมัติ</span>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-sm font-bold text-slate-800 dark:text-white">ทุกวัน 00:00 น. (เที่ยงคืน)</span>
                </div>
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium block mt-0.5">ระบบ Schedule เปิดใช้งานแล้ว</span>
            </div>
        </div>

        <!-- 4. Latest Backup -->
        <div class="bg-white dark:bg-[#1E2129] p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-envelope-circle-check text-xl"></i>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-semibold text-slate-400 block">การส่งรหัสผ่านเข้า ICT</span>
                <span class="text-sm font-black text-slate-800 dark:text-white truncate block">
                    {{ $latestBackup && $latestBackup->email_sent_to ? 'ส่งอีเมลสำเร็จ' : 'พร้อมส่งอีเมล' }}
                </span>
                <span class="text-[11px] text-slate-400 truncate block mt-0.5" title="{{ $latestBackup?->email_sent_to }}">
                    {{ $latestBackup?->email_sent_to ?: 'ICT Team / Admin' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Security & Policy Banner -->
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-indigo-50/70 via-purple-50/50 to-white dark:from-indigo-950/20 dark:via-purple-950/10 dark:to-[#1E2129] border border-indigo-100 dark:border-indigo-900/40 shadow-sm flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/20">
                <i class="fa-solid fa-shield-halved text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>ความปลอดภัยระดับสูง: เข้ารหัส AES-256 + ตรวจสอบสิทธิ์แผนก ICT ผ่าน Microsoft 365</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                        Zero-Knowledge & Role Verification
                    </span>
                </h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                    ไฟล์สำรองถูกบีบอัดและใส่รหัสผ่านแบบสุ่ม <strong>AES-256</strong> เพื่อความปลอดภัยสูงสุด การขอรับรหัสผ่านจำเป็นต้อง <strong>Login ด้วยบัญชี Microsoft 365 และผ่านการตรวจสอบว่าเป็นเจ้าหน้าที่แผนก ICT</strong> หากไม่ใช่แผนก ICT ระบบจะปฏิเสธการเข้าถึงทันที
                </p>
            </div>
        </div>

        <!-- ICT Auth Status Badge / Action -->
        <div class="shrink-0 flex items-center gap-2">
            @if($ictAuth['is_verified'])
                <div class="px-3.5 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-xs flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <div>
                        <span class="font-bold text-emerald-800 dark:text-emerald-300 block text-[11px]">ยืนยันตัวตน ICT แล้ว</span>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono">{{ $ictAuth['email'] }}</span>
                    </div>
                    <form action="{{ route('backend.database-backups.revoke-ict-auth') }}" method="POST" class="ml-1 inline">
                        @csrf
                        <button type="submit" class="text-[10px] text-slate-400 hover:text-rose-600 underline" title="ออกจากระบบ Microsoft หรือเปลี่ยนบัญชี">
                            เปลี่ยนบัญชี
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('auth.microsoft.redirect', ['purpose' => 'backup_password']) }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 text-xs font-bold transition flex items-center gap-2 shadow-sm border border-slate-700">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 23 23">
                        <path fill="#f35325" d="M1 1h10v10H1z"/>
                        <path fill="#81bc06" d="M12 1h10v10H12z"/>
                        <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                        <path fill="#ffba08" d="M12 12h10v10H12z"/>
                    </svg>
                    <span>Login Microsoft 365 (ICT)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Backups Table Card -->
    <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-database text-indigo-600"></i>
                    <span>ประวัติไฟล์สำรองฐานข้อมูล (Backup History)</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">รายการไฟล์สำรองที่เข้ารหัส พร้อมสถานะการส่งรหัสผ่านเข้าอีเมล ICT</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                ทั้งหมด {{ $backups->total() }} ไฟล์
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-850/60 border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-4">ชื่อไฟล์สำรอง (.ZIP)</th>
                        <th class="py-3.5 px-4 w-36">วันเวลาที่สำรอง</th>
                        <th class="py-3.5 px-4 w-44">ความปลอดภัย & อีเมล ICT</th>
                        <th class="py-3.5 px-4 w-28 text-center">จำนวนตาราง / ข้อมูล</th>
                        <th class="py-3.5 px-4 w-24">ขนาดไฟล์</th>
                        <th class="py-3.5 px-4">SHA-256 Checksum</th>
                        <th class="py-3.5 px-4 w-44 text-center">จัดการ (Actions)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($backups as $idx => $b)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                                {{ $backups->firstItem() + $idx }}
                            </td>

                            <!-- Filename -->
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-800 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-file-zipper text-indigo-600 text-base"></i>
                                    <span>{{ $b->filename }}</span>
                                </div>
                                @if($b->notes)
                                    <div class="text-[11px] font-sans font-normal text-slate-400 mt-0.5">
                                        {{ $b->notes }}
                                    </div>
                                @endif
                            </td>

                            <!-- Date/Time -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="text-slate-800 dark:text-slate-200 font-semibold flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar-check text-indigo-500 text-[11px]"></i>
                                    <span>{{ $b->thai_date }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    เวลา {{ $b->thai_time }}
                                </div>
                            </td>

                            <!-- Encryption & Email Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <i class="fa-solid fa-lock mr-0.5"></i> {{ $b->encryption_algorithm ?: 'AES-256' }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                        <i class="fa-solid fa-envelope-circle-check text-indigo-500"></i>
                                        <span class="truncate max-w-[150px]" title="{{ $b->email_sent_to }}">
                                            {{ $b->email_sent_to ? 'ส่งรหัสให้อีเมล ICT แล้ว' : 'ไม่มีบันทึกการส่ง' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Tables & Rows Count -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="font-bold text-slate-700 dark:text-slate-300">
                                    {{ $b->tables_count }} ตาราง
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    {{ number_format($b->rows_count) }} รายการ
                                </div>
                            </td>

                            <!-- File Size -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold text-slate-800 dark:text-white">
                                {{ $b->file_size_human }}
                            </td>

                            <!-- Checksum -->
                            <td class="py-3.5 px-4 font-mono text-[10px] text-slate-500">
                                <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-900 p-1.5 rounded border border-slate-200 dark:border-slate-800 max-w-xs">
                                    <span class="truncate" title="{{ $b->checksum_sha256 }}">{{ $b->checksum_sha256 }}</span>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $b->checksum_sha256 }}'); alert('คัดลอก Checksum เรียบร้อยแล้ว');"
                                        class="text-indigo-600 hover:text-indigo-800 shrink-0" title="คัดลอก Checksum">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Download ZIP -->
                                    <a href="{{ route('backend.database-backups.download', $b->id) }}"
                                        class="px-2 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                        title="ดาวน์โหลดไฟล์สำรองข้อมูล (.zip)">
                                        <i class="fa-solid fa-download"></i>
                                        <span>โหลด ZIP</span>
                                    </a>

                                    <!-- Download .txt Guide -->
                                    @if($b->md_file_path)
                                        <a href="{{ route('backend.database-backups.download-txt', $b->id) }}"
                                            class="px-2 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:hover:bg-purple-900/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="ดาวน์โหลดคู่มือการกู้คืน (.txt)">
                                            <i class="fa-solid fa-file-lines"></i>
                                            <span>คู่มือ .txt</span>
                                        </a>
                                    @endif

                                    <!-- Show Decrypted Password (ICT Verified only) -->
                                    @if($b->encrypted_password)
                                        <button type="button" @click="openPasswordModal({{ $b->id }}, '{{ $b->filename }}')"
                                            class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:hover:bg-amber-900/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="ขอรับรหัสผ่านถอดรหัส (สำหรับแผนก ICT ผ่านการยืนยัน Microsoft 365)">
                                            <i class="fa-solid fa-key"></i>
                                            <span>รหัสผ่าน</span>
                                        </button>
                                    @endif

                                    <!-- Delete Backup -->
                                    <form action="{{ route('backend.database-backups.destroy', $b->id) }}" method="POST"
                                        onsubmit="return confirm('ยืนยันการลบไฟล์สำรอง {{ $b->filename }}? การลบนี้ไม่สามารถย้อนคืนได้');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="ลบไฟล์สำรองนี้">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-database text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                                ยังไม่มีไฟล์สำรองฐานข้อมูลในระบบ คุณสามารถคลิกปุ่ม "สำรองฐานข้อมูลทันที" ด้านบนเพื่อเริ่มสำรองข้อมูลได้ทันที
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($backups->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $backups->links() }}
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 1: MANUAL BACKUP NOW -->
    <!-- ============================================================== -->
    <div x-show="backupModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;">
        
        <div @click.away="if (!isBackingUp) backupModalOpen = false"
            class="bg-white dark:bg-[#1E2129] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-left">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-down text-indigo-600"></i>
                    <span>สำรองฐานข้อมูลทันที (Backup Now)</span>
                </h3>
                <button type="button" @click="backupModalOpen = false" :disabled="isBackingUp" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="mb-4 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 text-xs">
                <div class="font-bold text-indigo-700 dark:text-indigo-300 flex items-center gap-1.5 mb-1">
                    <i class="fa-solid fa-lock"></i>
                    <span>ระบบจะสุ่มรหัสผ่านและเข้ารหัส AES-256 อัตโนมัติ</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-normal">
                    รหัสผ่านสำหรับเปิดไฟล์จะถูกส่งไปยังอีเมล ICT ทันที พร้อมสร้างเอกสารคู่มือ <code>.txt</code> แนบไปด้วย
                </p>
            </div>

            <form action="{{ route('backend.database-backups.create') }}" method="POST" @submit="isBackingUp = true">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        หมายเหตุ / บันทึกเพิ่มเติม (Notes)
                    </label>
                    <textarea name="notes" rows="2" placeholder="เช่น สำรองข้อมูลก่อนอัปเดตเวอร์ชันระบบ หรือสำรองข้อมูลฉุกเฉิน..."
                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="backupModalOpen = false" :disabled="isBackingUp"
                        class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                        ยกเลิก
                    </button>
                    <button type="submit" :disabled="isBackingUp"
                        class="px-5 py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white shadow-md shadow-indigo-600/25 transition flex items-center gap-1.5">
                        <template x-if="!isBackingUp">
                            <i class="fa-solid fa-cloud-arrow-down"></i>
                        </template>
                        <template x-if="isBackingUp">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </template>
                        <span x-text="isBackingUp ? 'กำลังเข้ารหัสและสำรองข้อมูล...' : 'เริ่มสำรองข้อมูลทันที'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 2: REQUEST / VIEW DECRYPTION PASSWORD (MICROSOFT 365 ICT VERIFIED) -->
    <!-- ============================================================== -->
    <div x-show="passwordModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;">
        
        <div @click.away="passwordModalOpen = false"
            class="bg-white dark:bg-[#1E2129] rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-left">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                        :class="isIctVerified ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400'">
                        <template x-if="isIctVerified">
                            <i class="fa-solid fa-key text-base"></i>
                        </template>
                        <template x-if="!isIctVerified">
                            <i class="fa-solid fa-shield-halved text-base"></i>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white"
                            x-text="isIctVerified ? 'ขอรับรหัสผ่านถอดรหัสไฟล์ (AES-256 Key)' : 'ยืนยันตัวตนผ่าน Microsoft 365'">
                        </h3>
                        <p class="text-[11px] text-slate-400">
                            เฉพาะเจ้าหน้าที่แผนก ICT (Information Communication Technology) เท่านั้น
                        </p>
                    </div>
                </div>
                <button type="button" @click="passwordModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Error Banner (if any) -->
            <template x-if="authErrorMsg">
                <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-base shrink-0 mt-0.5"></i>
                    <div class="flex-1" x-text="authErrorMsg"></div>
                </div>
            </template>

            <!-- ============================================== -->
            <!-- STEP 1: NOT VERIFIED YET (REQUIRES MICROSOFT 365 LOGIN) -->
            <!-- ============================================== -->
            <template x-if="!isIctVerified">
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        <div class="flex items-center gap-2 font-bold text-slate-800 dark:text-white mb-1.5">
                            <i class="fa-solid fa-lock text-indigo-600"></i>
                            <span>มาตรการความปลอดภัยข้อมูลชั้นสูงสุด</span>
                        </div>
                        <p>
                            รหัสผ่านสำรองฐานข้อมูลได้รับการคุ้มครองด้วยการจำกัดสิทธิ์ระดับสูง หากต้องการขอรหัสผ่าน ท่านต้อง <strong>เข้าสู่ระบบด้วยบัญชี Microsoft 365</strong> ขององค์กร เพื่อให้ระบบตรวจสอบว่าเป็นเจ้าหน้าที่ <strong>แผนก ICT</strong> จริง
                        </p>
                    </div>

                    <!-- Quick verification if user already linked Microsoft -->
                    <template x-if="hasConnectedMicrosoft">
                        <div class="p-3.5 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-indigo-700 dark:text-indigo-300 block">พบบัญชี Microsoft ที่เชื่อมต่อไว้:</span>
                                <span class="text-xs font-mono font-bold text-slate-800 dark:text-white truncate block" x-text="connectedEmail"></span>
                            </div>
                            <button type="button" @click="quickVerifyConnected()" :disabled="isVerifyingConnected"
                                class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shrink-0 transition flex items-center gap-1.5 shadow-sm">
                                <template x-if="!isVerifyingConnected">
                                    <i class="fa-solid fa-circle-check"></i>
                                </template>
                                <template x-if="isVerifyingConnected">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                </template>
                                <span x-text="isVerifyingConnected ? 'กำลังตรวจสอบ...' : 'ตรวจสอบบัญชีนี้'"></span>
                            </button>
                        </div>
                    </template>

                    <!-- Main Microsoft Login Button -->
                    <div class="pt-2">
                        <a href="{{ route('auth.microsoft.redirect', ['purpose' => 'backup_password']) }}"
                            class="w-full py-3 px-4 rounded-xl bg-[#2F2F2F] hover:bg-[#1E1E1E] text-white font-bold text-xs flex items-center justify-center gap-3 transition shadow-md border border-slate-700 hover:border-slate-600">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 23 23">
                                <path fill="#f35325" d="M1 1h10v10H1z"/>
                                <path fill="#81bc06" d="M12 1h10v10H12z"/>
                                <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                                <path fill="#ffba08" d="M12 12h10v10H12z"/>
                            </svg>
                            <span>เข้าสู่ระบบด้วย Microsoft 365 เพื่อตรวจสอบสิทธิ์แผนก ICT</span>
                        </a>
                        <p class="text-[11px] text-slate-400 text-center mt-2">
                            * หากอีเมลที่ล็อกอินไม่ได้สังกัดแผนก ICT ระบบจะปฏิเสธการเข้าถึงทันที
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <button type="button" @click="passwordModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                            ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
            </template>

            <!-- ============================================== -->
            <!-- STEP 2: VERIFIED ICT (CAN REQUEST PASSWORD) -->
            <!-- ============================================== -->
            <template x-if="isIctVerified">
                <div class="space-y-4">
                    <!-- Verified ICT Card -->
                    <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                                    <span>ผ่านการยืนยันสิทธิ์แผนก ICT แล้ว</span>
                                </div>
                                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono truncate block" x-text="`${ictName} (${ictEmail})`"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Backup Filename -->
                    <div class="text-xs">
                        <span class="text-[11px] text-slate-400 font-semibold block mb-0.5">ไฟล์สำรองที่ต้องการเปิด:</span>
                        <span class="font-mono text-xs font-bold text-slate-800 dark:text-white break-all" x-text="selectedBackupFilename"></span>
                    </div>

                    <!-- State A: Not requested yet -> Show "ส่งรหัสผ่านเข้าอีเมล" button -->
                    <template x-if="!currentPasswordInfo">
                        <div class="py-3 text-center space-y-3">
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 text-left space-y-1">
                                <div class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                    <i class="fa-solid fa-paper-plane text-indigo-600"></i>
                                    <span>ระบบจะจัดส่งรหัสผ่านไปยังอีเมลของคุณ</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    เมื่อกดปุ่มด้านล่าง รหัสผ่านถอดรหัสไฟล์ (AES-256) พร้อมเอกสารคู่มือจะถูกส่งตรงไปยังอีเมล: <strong class="text-indigo-600 dark:text-indigo-400 font-mono" x-text="ictEmail"></strong>
                                </p>
                            </div>

                            <button type="button" @click="requestPassword()" :disabled="isFetchingPassword"
                                class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                                <template x-if="!isFetchingPassword">
                                    <i class="fa-solid fa-paper-plane text-sm"></i>
                                </template>
                                <template x-if="isFetchingPassword">
                                    <i class="fa-solid fa-spinner fa-spin text-sm"></i>
                                </template>
                                <span x-text="isFetchingPassword ? 'กำลังจัดส่งรหัสผ่านเข้าอีเมล...' : `ส่งรหัสผ่านเข้าอีเมล (${ictEmail})`"></span>
                            </button>
                            <p class="text-[10px] text-slate-400">
                                * การจัดส่งรหัสผ่านจะถูกบันทึกลงในระบบ Security Audit Log เพื่อความโปร่งใส
                            </p>
                        </div>
                    </template>

                    <!-- State B: Email Dispatched! -->
                    <template x-if="currentPasswordInfo">
                        <div class="space-y-4">
                            <!-- Success Email Dispatched Card -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-50 via-teal-50 to-white dark:from-emerald-950/40 dark:via-teal-950/20 dark:to-[#1E2129] border border-emerald-200 dark:border-emerald-800/60 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center mx-auto mb-2.5 shadow-md shadow-emerald-600/25">
                                    <i class="fa-solid fa-paper-plane text-xl"></i>
                                </div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-white">
                                    ส่งรหัสผ่านเข้าอีเมลเรียบร้อยแล้ว!
                                </h4>
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 max-w-sm mx-auto leading-normal">
                                    ระบบได้ส่งรหัสผ่าน AES-256 พร้อมไฟล์คู่มือแนะนำการกู้คืน (.txt) ตรงไปยังกล่องข้อความของคุณแล้ว
                                </p>

                                <div class="mt-3.5 p-3 rounded-xl bg-white/90 dark:bg-slate-900/80 border border-emerald-100 dark:border-emerald-900/40 text-xs text-left space-y-1.5 font-sans">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px] font-semibold">ส่งไปยังอีเมล:</span>
                                        <span class="font-mono text-emerald-700 dark:text-emerald-300 font-bold" x-text="currentPasswordInfo.recipient?.email || ictEmail"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px] font-semibold">ผู้รับ (แผนก ICT):</span>
                                        <span class="text-slate-700 dark:text-slate-200 font-medium" x-text="currentPasswordInfo.recipient?.name || ictName"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px] font-semibold">เวลาที่จัดส่ง:</span>
                                        <span class="font-mono text-slate-700 dark:text-slate-200" x-text="currentPasswordInfo.recipient?.sent_at || '-'"></span>
                                    </div>
                                </div>

                                <!-- Action Buttons: Outlook Web & Resend -->
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <a href="https://outlook.office.com/mail/" target="_blank"
                                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm transition">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        <span>เปิด Microsoft Outlook</span>
                                    </a>
                                    <button type="button" @click="requestPassword()" :disabled="isFetchingPassword"
                                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition">
                                        <i class="fa-solid fa-rotate-right mr-1"></i> ส่งซ้ำอีกครั้ง
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <template x-if="isIctVerified">
                            <form action="{{ route('backend.database-backups.revoke-ict-auth') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-[11px] text-slate-400 hover:text-rose-600 underline">
                                    ออกจากระบบ Microsoft
                                </button>
                            </form>
                        </template>
                        <button type="button" @click="passwordModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                            ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

</div>
@endsection
