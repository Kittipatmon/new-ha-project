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
                            {--years=5 : จำนวนปีที่ต้องการเก็บก่อนบีบอัด (ค่าเริ่มต้น 5 ปี)}
                            {--year= : ระบุปี ค.ศ. เจาะจงที่ต้องการบีบอัด เช่น 2021}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'บีบอัด Audit Log ย้อนหลังเป็นไฟล์ ZIP พร้อมสร้าง SHA-256 Checksum สำหรับการตรวจสอบของ Auditor';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $specificYear = $this->option('year');
        $yearsThreshold = (int)$this->option('years');

        if ($specificYear) {
            $year = (int)$specificYear;
            $this->info("กำลังบีบอัด Audit Log ประจำปี ค.ศ. {$year}...");
            try {
                $archive = AuditLogService::createArchive($year, null, 'สั่งบีบอัดผ่าน Artisan CLI command');
                $this->info("สร้างไฟล์ ZIP สำเร็จ: {$archive->filename}");
                $this->line(" - ขนาดไฟล์: {$archive->file_size_human}");
                $this->line(" - จำนวนรายการ: {$archive->records_count} รายการ");
                $this->line(" - SHA-256: {$archive->checksum_sha256}");
                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->error("เกิดข้อผิดพลาด: {$e->getMessage()}");
                return Command::FAILURE;
            }
        }

        // Archive logs older than threshold (5 years)
        $targetYear = date('Y') - $yearsThreshold;
        $this->info("ตรวจสอบ Log ที่มีอายุเกิน {$yearsThreshold} ปี (ก่อนปี ค.ศ. {$targetYear})...");

        // Find available years older than target
        $oldestLog = \App\Models\SystemAuditLog::orderBy('created_at', 'asc')->first();
        if (!$oldestLog) {
            $this->warn('ไม่พบข้อมูล Audit Log ในระบบ');
            return Command::SUCCESS;
        }

        $startYear = (int)$oldestLog->created_at->format('Y');
        if ($startYear > $targetYear) {
            $this->info("ยังไม่มีข้อมูล Log ที่อายุเกิน {$yearsThreshold} ปี (ข้อมูลเก่าสุดคือปี {$startYear})");
            return Command::SUCCESS;
        }

        for ($y = $startYear; $y <= $targetYear; $y++) {
            $this->info("กำลังบีบอัด Log ปี {$y}...");
            $archive = AuditLogService::createArchive($y, null, "บีบอัดอัตโนมัติตามนโยบายเก็บรักษา {$yearsThreshold} ปี");
            $this->info("สร้างไฟล์ ZIP ปี {$y} เรียบร้อย: {$archive->filename} ({$archive->records_count} รายการ)");
        }

        return Command::SUCCESS;
    }
}
