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

    async viewPassword(id) {
        this.isFetchingPassword = true;
        this.currentPasswordInfo = null;
        this.passwordModalOpen = true;
        this.copied = false;
        try {
            const res = await fetch(`{{ url('backend/database-backups') }}/${id}/password`);
            const data = await res.json();
            if (data.success) {
                this.currentPasswordInfo = data;
            } else {
                alert(data.message || 'ไม่สามารถดึงข้อมูลรหัสผ่านได้');
                this.passwordModalOpen = false;
            }
        } catch (e) {
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
            this.passwordModalOpen = false;
        } finally {
            this.isFetchingPassword = false;
        }
    },

    copyPasswordText() {
        if (!this.currentPasswordInfo?.password) return;
        navigator.clipboard.writeText(this.currentPasswordInfo.password);
        this.copied = true;
        setTimeout(() => { this.copied = false; }, 2500);
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
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-indigo-50/70 via-purple-50/50 to-white dark:from-indigo-950/20 dark:via-purple-950/10 dark:to-[#1E2129] border border-indigo-100 dark:border-indigo-900/40 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/20">
                <i class="fa-solid fa-key text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>ความปลอดภัยระดับสูง: เข้ารหัส AES-256 + ส่งรหัสเฉพาะ ICT + คู่มือ .md</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                        Zero-Knowledge Security
                    </span>
                </h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                    ไฟล์สำรองถูกบีบอัดและใส่รหัสผ่านแบบสุ่มที่มีความปลอดภัยสูงด้วยอัลกอริทึม <strong>AES-256</strong> (ไม่สามารถเปิดได้หากไม่มีรหัสผ่าน) โดยระบบจะส่งรหัสผ่านพร้อมคู่มือการกู้คืน (<code>.md</code>) ตรงไปยังอีเมลของแผนก ICT โดยอัตโนมัติทันทีที่สำรองเสร็จ และจัดเก็บไฟล์ไว้ใน Private Storage ปลอดภัยจากการเข้าถึงผ่านเว็บ 100%
                </p>
            </div>
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

                                    <!-- Download .md Guide -->
                                    @if($b->md_file_path)
                                        <a href="{{ route('backend.database-backups.download-md', $b->id) }}"
                                            class="px-2 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:hover:bg-purple-900/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="ดาวน์โหลดคู่มือการกู้คืน (.md)">
                                            <i class="fa-solid fa-file-lines"></i>
                                            <span>คู่มือ .md</span>
                                        </a>
                                    @endif

                                    <!-- Show Decrypted Password (Admin only) -->
                                    @if($b->encrypted_password)
                                        <button type="button" @click="viewPassword({{ $b->id }})"
                                            class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:hover:bg-amber-900/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="ดูรหัสผ่านถอดรหัส (สำหรับ ICT / ผู้ดูแลระบบ)">
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
                    รหัสผ่านสำหรับเปิดไฟล์จะถูกส่งไปยังอีเมล ICT ทันที พร้อมสร้างเอกสารคู่มือ <code>.md</code> แนบไปด้วย
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
    <!-- MODAL 2: VIEW DECRYPTION PASSWORD (ADMIN ONLY) -->
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
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">
                            รหัสผ่านถอดรหัสไฟล์ (AES-256 Decryption Key)
                        </h3>
                        <p class="text-[11px] text-slate-400">สำหรับเจ้าหน้าที่ ICT / ผู้ดูแลระบบระดับสูงเท่านั้น</p>
                    </div>
                </div>
                <button type="button" @click="passwordModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Loading Spinner -->
            <template x-if="isFetchingPassword">
                <div class="py-12 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl mb-2 text-indigo-500"></i>
                    <p class="text-xs">กำลังถอดรหัสความปลอดภัย...</p>
                </div>
            </template>

            <!-- Password Content -->
            <template x-if="!isFetchingPassword && currentPasswordInfo">
                <div class="space-y-4">
                    <div>
                        <span class="text-[11px] text-slate-400 font-semibold block mb-1">ไฟล์สำรอง:</span>
                        <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-200 break-all" x-text="currentPasswordInfo.filename"></span>
                    </div>

                    <!-- Password Box -->
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-center relative group">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                            AES-256 Decryption Password
                        </span>
                        <div class="font-mono text-xl font-black text-cyan-400 tracking-wider break-all select-all py-1"
                            x-text="currentPasswordInfo.password">
                        </div>
                        <div class="mt-3 flex items-center justify-center gap-2">
                            <button type="button" @click="copyPasswordText()"
                                class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                                <i :class="copied ? 'fa-solid fa-check text-emerald-300' : 'fa-regular fa-copy'"></i>
                                <span x-text="copied ? 'คัดลอกรหัสผ่านแล้ว!' : 'คัดลอกรหัสผ่าน'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Email Delivery Info -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">ส่งเข้าอีเมล ICT:</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200 font-bold truncate max-w-xs" x-text="currentPasswordInfo.email_sent_to"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">เวลาที่ส่งอีเมล:</span>
                            <span class="text-slate-800 dark:text-slate-200" x-text="currentPasswordInfo.email_sent_at"></span>
                        </div>
                    </div>

                    <!-- Quick Command -->
                    <div class="p-3 rounded-xl bg-slate-900/90 text-slate-300 font-mono text-[11px] space-y-1">
                        <span class="text-slate-400 text-[10px] block">คำสั่งแตกไฟล์ผ่าน Linux / macOS CLI:</span>
                        <code class="text-cyan-300 select-all block" x-text="`7z x -p&quot;${currentPasswordInfo.password}&quot; ${currentPasswordInfo.filename}`"></code>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end">
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
