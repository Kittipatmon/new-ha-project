<?php

namespace App\Console\Commands;

use App\Services\AuditLogService;
use Illuminate\Console\Command;

class ArchiveAuditLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:archive 
                            {--years=5 : ระยะเวลาเก็บรักษาไฟล์ ZIP (ปี) ค่าเริ่มต้น 5 ปี}
                            {--year= : ระบุปี ค.ศ. เจาะจงที่ต้องการบีบอัด เช่น 2025}
                            {--purge : ล้างประวัติ Log ในฐานข้อมูลหลังจากสร้างและตรวจสอบไฟล์ ZIP สำเร็จ เพื่อเริ่มรอบปีใหม่}
                            {--clean-expired : ลบไฟล์ ZIP เก่าที่จัดเก็บครบตามระยะเวลา 5 ปีออกเพื่อคืนพื้นที่จัดเก็บ}
                            {--auto-yearly : บีบอัดรอบปีที่เพิ่งสิ้นสุดโดยอัตโนมัติ (ปี ค.ศ. ก่อนหน้า)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'จัดการคลัง Audit Log ตามนโยบาย 5 ปี (บีบอัดเป็น ZIP, ล้างฐานข้อมูลรอบปีเก่า, และทำความสะอาดไฟล์ที่ครบ 5 ปี)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $specificYear = $this->option('year');
        $yearsThreshold = (int)($this->option('years') ?: 5);
        $purge = (bool)$this->option('purge');
        $cleanExpired = (bool)$this->option('clean-expired');
        $autoYearly = (bool)$this->option('auto-yearly');

        $this->info("=================================================================");
        $this->info("  Kumwell HR System: ระบบจัดการคลัง Audit Log (นโยบายเก็บรักษา 5 ปี)");
        $this->info("=================================================================");
        $this->line(" - นโยบายเก็บรักษาไฟล์ ZIP: {$yearsThreshold} ปี (Data Retention Policy)");
        $this->line(" - ตัวเลือกการล้าง DB: " . ($purge ? "เปิดใช้งาน (Purge Live DB)" : "ปิด (เก็บข้อมูลทั้งใน DB และ ZIP)"));

        // 1. Process clean expired archives if requested
        if ($cleanExpired) {
            $this->info("\n[1/2] ตรวจสอบไฟล์ ZIP ที่มีอายุเกิน {$yearsThreshold} ปี...");
            $cleanup = AuditLogService::cleanupExpiredArchives($yearsThreshold);
            if ($cleanup['count'] > 0) {
                $this->info(" -> ลบไฟล์ ZIP ที่ครบกำหนด {$yearsThreshold} ปีเรียบร้อย: {$cleanup['count']} ไฟล์");
                $this->line(" -> พื้นที่จัดเก็บที่ได้คืน: {$cleanup['freed_human']}");
                foreach ($cleanup['deleted_files'] as $f) {
                    $this->line("    * {$f}");
                }
            } else {
                $this->comment(" -> ไม่พบไฟล์ ZIP ที่มีอายุเกิน {$yearsThreshold} ปี (ยังอยู่ในระยะเวลาจัดเก็บ)");
            }
        }

        // 2. Determine target year(s) to archive
        $yearsToArchive = [];

        if ($specificYear) {
            $yearsToArchive[] = (int)$specificYear;
        } elseif ($autoYearly) {
            // Target the previous full calendar year
            $prevYear = (int)date('Y') - 1;
            $yearsToArchive[] = $prevYear;
            $this->info("\n[2/2] ดำเนินการบีบอัดรอบปีที่เพิ่งสิ้นสุด: ค.ศ. {$prevYear} (พ.ศ. " . ($prevYear + 543) . ")");
        } else {
            // Find all completed years from DB logs
            $currentYear = (int)date('Y');
            $distinctYears = \App\Models\SystemAuditLog::selectRaw('YEAR(created_at) as yr')
                ->whereYear('created_at', '<', $currentYear)
                ->distinct()
                ->orderBy('yr', 'asc')
                ->pluck('yr')
                ->toArray();

            $yearsToArchive = array_map('intval', $distinctYears);
        }

        if (empty($yearsToArchive) && !$cleanExpired) {
            $this->warn("ไม่พบปีที่ต้องบีบอัดข้อมูล (หรือไม่มีประวัติ Log ของปีก่อนหน้า)");
            return Command::SUCCESS;
        }

        if (!empty($yearsToArchive)) {
            $this->info("\nกำลังดำเนินการบีบอัด Audit Log เป็นไฟล์ ZIP...");
            foreach ($yearsToArchive as $year) {
                $count = \App\Models\SystemAuditLog::whereYear('created_at', $year)->count();
                if ($count === 0 && !$specificYear) {
                    continue;
                }

                $this->line("\n-------------------------------------------------------------");
                $this->line(" กำลังประมวลผลปี ค.ศ. {$year} (พ.ศ. " . ($year + 543) . ") [พบ {$count} รายการ]...");

                try {
                    $note = "บีบอัดรอบปีอัตโนมัติตามนโยบายเก็บรักษา {$yearsThreshold} ปี" . ($purge ? " (ล้าง DB รอบปีเก่า)" : "");
                    $archive = AuditLogService::createArchive($year, null, $note, $purge);

                    $this->info(" -> สร้างไฟล์ ZIP สำเร็จ: {$archive->filename}");
                    $this->line("    ขนาดไฟล์: {$archive->file_size_human}");
                    $this->line("    จำนวนบันทึก: " . number_format($archive->records_count) . " รายการ");
                    $this->line("    SHA-256 Checksum: {$archive->checksum_sha256}");
                    $this->line("    กำหนดจัดเก็บถึง: {$archive->thai_retain_until} (ครบ {$yearsThreshold} ปี)");

                    if ($purge) {
                        $this->warn("    [PURGE] ล้างข้อมูลปี {$year} ออกจากฐานข้อมูลเรียบร้อย เพื่อเริ่มรอบปีใหม่");
                    }
                } catch (\Exception $e) {
                    $this->error(" -> เกิดข้อผิดพลาดในปี {$year}: {$e->getMessage()}");
                }
            }
        }

        $this->info("\n=================================================================");
        $this->info(" เสร็จสิ้นการทำงานตามนโยบายจัดเก็บ Log 5 ปี");
        $this->info("=================================================================");

        return Command::SUCCESS;
    }
}
