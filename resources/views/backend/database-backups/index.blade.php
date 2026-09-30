@extends('layouts.app')

@section('title', 'ระบบสำรองฐานข้อมูลอัตโนมัติ (Database Backups)')

@section('content')
<div class="space-y-6" x-data="{ backupModalOpen: false, isBackingUp: false }">

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
                <span>Disaster Recovery & Data Protection</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-database text-indigo-600 dark:text-indigo-400"></i>
                <span>ระบบสำรองฐานข้อมูลอัตโนมัติ (Database Backups)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                กำหนดการทำงานอัตโนมัติทุกวันตอนเที่ยงคืน (00:00 น.) บีบอัดเป็นไฟล์ ZIP และเก็บรักษาบน Private Storage นาน 30 วัน
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
                <i class="fa-solid fa-hard-drive text-xl"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block">ขนาดพื้นที่จัดเก็บรวม</span>
                <span class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono">{{ $totalSizeHuman }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">บีบอัดลดขนาดลง 80-90%</span>
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
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-semibold text-slate-400 block">สำรองข้อมูลล่าสุด</span>
                <span class="text-sm font-black text-slate-800 dark:text-white truncate block">
                    {{ $latestBackup ? $latestBackup->thai_date : 'ยังไม่มีไฟล์' }}
                </span>
                <span class="text-[11px] text-slate-400 font-mono block mt-0.5">
                    {{ $latestBackup ? 'เวลา ' . $latestBackup->thai_time : '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Security & Policy Banner -->
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-indigo-50/70 via-purple-50/50 to-white dark:from-indigo-950/20 dark:via-purple-950/10 dark:to-[#1E2129] border border-indigo-100 dark:border-indigo-900/40 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/20">
                <i class="fa-solid fa-lock text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>ความปลอดภัยและความต่อเนื่องของข้อมูล (Zero-Risk & Secure Isolation)</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                        Private Storage
                    </span>
                </h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                    ไฟล์สำรองถูกจัดเก็บในโฟลเดอร์ <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-mono text-[11px] text-indigo-600 dark:text-indigo-400">storage/app/backups/db/</code> ซึ่งอยู่นอก <code class="font-mono">public_html</code> ป้องกันการเข้าถึงผ่าน Web Browser 100% สิทธิ์การดาวน์โหลดสงวนไว้สำหรับผู้ดูแลระบบ (<code class="font-mono">role:admin</code>) พร้อม SHA-256 Checksum ตรวจสอบความถูกต้อง
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
                <p class="text-xs text-slate-400 mt-0.5">รายการไฟล์สำรองฐานข้อมูลย้อนหลัง พร้อมฟังก์ชันดาวน์โหลดและตรวจสอบความสมบูรณ์</p>
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
                        <th class="py-3.5 px-4 w-24 text-center">จำนวนตาราง</th>
                        <th class="py-3.5 px-4 w-28 text-center">ข้อมูลทั้งหมด</th>
                        <th class="py-3.5 px-4 w-24">ขนาดไฟล์</th>
                        <th class="py-3.5 px-4">SHA-256 Checksum</th>
                        <th class="py-3.5 px-4 w-36">ผู้สำรองข้อมูล</th>
                        <th class="py-3.5 px-4 w-32 text-center">จัดการ (Actions)</th>
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

                            <!-- Tables Count -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    {{ $b->tables_count }} ตาราง
                                </span>
                            </td>

                            <!-- Rows Count -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap font-mono text-slate-700 dark:text-slate-300">
                                {{ number_format($b->rows_count) }} รายการ
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

                            <!-- Creator -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">
                                    {{ $b->created_by_name ?: 'ระบบอัตโนมัติ' }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ $b->dumper_engine }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Download ZIP -->
                                    <a href="{{ route('backend.database-backups.download', $b->id) }}"
                                        class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                        title="ดาวน์โหลดไฟล์สำรองข้อมูล (.zip)">
                                        <i class="fa-solid fa-download"></i>
                                        <span>โหลด ZIP</span>
                                    </a>

                                    <!-- Delete Backup -->
                                    <form action="{{ route('backend.database-backups.destroy', $b->id) }}" method="POST"
                                        onsubmit="return confirm('ยืนยันการลบไฟล์สำรอง {{ $b->filename }}? การลบนี้ไม่สามารถย้อนคืนได้');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 transition flex items-center gap-1 font-semibold text-[11px]"
                                            title="ลบไฟล์สำรองนี้">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
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
    <!-- MODAL: MANUAL BACKUP NOW -->
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

            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                ระบบจะรวบรวมโครงสร้างและข้อมูลของทุกตารางในฐานข้อมูลปัจจุบัน บีบอัดเป็นไฟล์ ZIP และจัดเก็บบน Private Storage พร้อมบันทึกประวัติและ SHA-256 Checksum
            </p>

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
                        <span x-text="isBackingUp ? 'กำลังสำรองข้อมูล...' : 'เริ่มสำรองข้อมูลทันที'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
