<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup 
                            {--clean : ตรวจสอบและลบไฟล์สำรองเก่าที่เกินกำหนดระยะเวลาออก}
                            {--keep=30 : จำนวนวันที่ต้องการเก็บสำรองข้อมูลไว้ (ค่าเริ่มต้น 30 วัน)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'สำรองฐานข้อมูล MySQL อัตโนมัติเป็นไฟล์ ZIP พร้อมคำนวณ Checksum SHA-256 (รันทุกวันตอนเที่ยงคืน)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $keepDays = (int)($this->option('keep') ?: 30);
        $clean = (bool)$this->option('clean');

        $this->info("=================================================================");
        $this->info(" Kumwell HR System: เริ่มกระบวนการสำรองฐานข้อมูล (Database Backup)");
        $this->info("=================================================================");
        $this->line(" - เวลาที่ดำเนินการ: " . now()->format('d/m/Y H:i:s'));
        $this->line(" - นโยบายการเก็บรักษา: {$keepDays} วัน");

        // 1. Create Backup
        try {
            $this->info("\n[1/2] กำลังรวบรวมข้อมูลและสร้างไฟล์สำรองฐานข้อมูล...");
            $backup = DatabaseBackupService::createBackup(
                actor: null,
                notes: 'สำรองฐานข้อมูลอัตโนมัติประจำวัน (เที่ยงคืน)'
            );

            $this->info(" -> สำรองข้อมูลสำเร็จเรียบร้อย!");
            $this->line("    * ชื่อไฟล์: {$backup->filename}");
            $this->line("    * ขนาดไฟล์: {$backup->file_size_human}");
            $this->line("    * จำนวนตาราง: {$backup->tables_count} ตาราง");
            $this->line("    * ข้อมูลทั้งหมด: " . number_format($backup->rows_count) . " รายการ");
            $this->line("    * SHA-256: {$backup->checksum_sha256}");
            $this->line("    * เอนจิน: {$backup->dumper_engine}");

        } catch (\Throwable $e) {
            $this->error(" -> เกิดข้อผิดพลาดในการสำรองฐานข้อมูล: " . $e->getMessage());
            return Command::FAILURE;
        }

        // 2. Clean old backups if requested
        if ($clean) {
            $this->info("\n[2/2] ตรวจสอบและทำความสะอาดไฟล์สำรองเก่าที่เกิน {$keepDays} วัน...");
            $cleanResult = DatabaseBackupService::cleanOldBackups($keepDays);

            if ($cleanResult['count'] > 0) {
                $this->info(" -> ลบไฟล์สำรองเก่าที่เกิน {$keepDays} วันแล้วจำนวน: {$cleanResult['count']} ไฟล์");
                $this->line(" -> ได้พื้นที่จัดเก็บบนโฮสต์คืน: {$cleanResult['freed_human']}");
                foreach ($cleanResult['deleted_files'] as $f) {
                    $this->line("    - {$f}");
                }
            } else {
                $this->comment(" -> ไม่พบไฟล์สำรองที่เกิน {$keepDays} วัน (ไฟล์ทั้งหมดยังอยู่ในเกณฑ์กำหนด)");
            }
        }

        $this->info("\n=================================================================");
        $this->info(" สำรองฐานข้อมูลประจำวันเสร็จสมบูรณ์ 100%");
        $this->info("=================================================================");

        return Command::SUCCESS;
    }
}
