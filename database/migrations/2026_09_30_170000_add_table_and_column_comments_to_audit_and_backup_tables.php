<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table Comments
        if (Schema::hasTable('system_audit_logs')) {
            DB::statement("ALTER TABLE `system_audit_logs` COMMENT = 'ตารางบันทึกประวัติการเปลี่ยนแปลงและกิจกรรมในระบบ (System Audit Trail)'");
            
            DB::statement("ALTER TABLE `system_audit_logs` 
                MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสบันทึกกิจกรรม (Primary Key Auto-Increment)',
                MODIFY COLUMN `user_id` BIGINT UNSIGNED NULL COMMENT 'รหัสผู้ใช้งานที่ทำรายการ (FK อ้างอิงตาราง users / userskml)',
                MODIFY COLUMN `user_code` VARCHAR(255) NULL COMMENT 'รหัสพนักงานหรือรหัสประจำตัวของผู้ดำเนินการ (เช่น 11668)',
                MODIFY COLUMN `user_name` VARCHAR(255) NULL COMMENT 'ชื่อ-นามสกุลของผู้ดำเนินการ',
                MODIFY COLUMN `user_email` VARCHAR(255) NULL COMMENT 'อีเมลของผู้ดำเนินการ',
                MODIFY COLUMN `user_role` VARCHAR(255) NULL COMMENT 'บทบาทหรือระดับสิทธิ์ของผู้ดำเนินการ (เช่น admin, hr, user)',
                MODIFY COLUMN `action` VARCHAR(50) NOT NULL COMMENT 'ประเภทกิจกรรม (created, updated, deleted, login, logout, archived, restored, exported, backup, security_alert)',
                MODIFY COLUMN `module` VARCHAR(50) NOT NULL COMMENT 'รหัสโมดูลระบบ (recruitment, training, users, settings, system, auth)',
                MODIFY COLUMN `module_name` VARCHAR(100) NULL COMMENT 'ชื่อโมดูลระบบภาษาไทย (เช่น ระบบสรรหาบุคลากร, ระบบสำรองฐานข้อมูลอัตโนมัติ)',
                MODIFY COLUMN `model_type` VARCHAR(255) NULL COMMENT 'ชื่อ Model Class ของข้อมูลที่ถูกกระทำ (Eloquent Model)',
                MODIFY COLUMN `model_id` VARCHAR(255) NULL COMMENT 'รหัส Primary Key ของข้อมูลที่ถูกกระทำใน Model',
                MODIFY COLUMN `description` TEXT NOT NULL COMMENT 'คำอธิบายรายละเอียดของกิจกรรมที่เกิดขึ้น',
                MODIFY COLUMN `old_values` LONGTEXT NULL COMMENT 'ค่าข้อมูลเดิมก่อนการเปลี่ยนแปลง (JSON format)',
                MODIFY COLUMN `new_values` LONGTEXT NULL COMMENT 'ค่าข้อมูลใหม่หลังการเปลี่ยนแปลง (JSON format)',
                MODIFY COLUMN `diff` LONGTEXT NULL COMMENT 'ข้อมูลเปรียบเทียบความแตกต่างเฉพาะฟิลด์ที่มีการแก้ไข (JSON format)',
                MODIFY COLUMN `ip_address` VARCHAR(45) NULL COMMENT 'หมายเลข IP Address ของผู้ใช้งานที่ทำรายการ (IPv4 / IPv6)',
                MODIFY COLUMN `user_agent` TEXT NULL COMMENT 'ข้อมูลเว็บบราวเซอร์และอุปกรณ์ของผู้ใช้งาน (Client User Agent)',
                MODIFY COLUMN `url` TEXT NULL COMMENT 'URL ของหน้าเว็บหรือ API Endpoint ที่ถูกเรียกใช้งาน',
                MODIFY COLUMN `method` VARCHAR(10) NULL COMMENT 'HTTP Request Method (GET, POST, PUT, PATCH, DELETE)',
                MODIFY COLUMN `created_at` TIMESTAMP NULL COMMENT 'วันเวลาที่บันทึกกิจกรรม (Timestamp)',
                MODIFY COLUMN `updated_at` TIMESTAMP NULL COMMENT 'วันเวลาที่มีการแก้ไขบันทึก (Timestamp)'
            ");
        }

        // 2. system_audit_archives Comments
        if (Schema::hasTable('system_audit_archives')) {
            DB::statement("ALTER TABLE `system_audit_archives` COMMENT = 'ตารางประวัติคลังไฟล์บีบอัด Audit Log รายปี จัดเก็บ 5 ปี (ISO/IEC 27001)'");

            DB::statement("ALTER TABLE `system_audit_archives`
                MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสบันทึกคลังไฟล์บีบอัด (Primary Key Auto-Increment)',
                MODIFY COLUMN `filename` VARCHAR(255) NOT NULL COMMENT 'ชื่อไฟล์ ZIP คลังข้อมูล (เช่น audit_archive_2026.zip)',
                MODIFY COLUMN `file_path` VARCHAR(255) NOT NULL COMMENT 'เส้นทางจัดเก็บไฟล์ในระบบ (Relative Storage Path)',
                MODIFY COLUMN `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ขนาดไฟล์เป็นหน่วยไบต์ (Bytes)',
                MODIFY COLUMN `file_size_human` VARCHAR(50) NOT NULL DEFAULT '0 KB' COMMENT 'ขนาดไฟล์ที่อ่านง่าย (เช่น 1.25 MB)',
                MODIFY COLUMN `records_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'จำนวนรายการ Log ทั้งหมดที่ถูกบีบอัดในไฟล์นี้',
                MODIFY COLUMN `period_start` DATETIME NOT NULL COMMENT 'วันเวลาเริ่มต้นของช่วงข้อมูลที่จัดเก็บ',
                MODIFY COLUMN `period_end` DATETIME NOT NULL COMMENT 'วันเวลาสิ้นสุดของช่วงข้อมูลที่จัดเก็บ',
                MODIFY COLUMN `period_label` VARCHAR(100) NOT NULL COMMENT 'ป้ายกำกับช่วงปีหรือรอบข้อมูล (เช่น ปี 2026)',
                MODIFY COLUMN `checksum_sha256` VARCHAR(64) NOT NULL COMMENT 'ค่าแฮชตรวจสอบความถูกต้องของไฟล์เพื่อความปลอดภัยตามมาตรฐาน ISO (SHA-256 Checksum)',
                MODIFY COLUMN `archived_by` BIGINT UNSIGNED NULL COMMENT 'รหัสผู้ใช้ที่สั่งบีบอัดจัดเก็บไฟล์ (User ID)',
                MODIFY COLUMN `archived_by_name` VARCHAR(255) NULL COMMENT 'ชื่อผู้สั่งบีบอัดจัดเก็บไฟล์',
                MODIFY COLUMN `notes` TEXT NULL COMMENT 'หมายเหตุหรือคำอธิบายสำหรับการตรวจสอบของผู้ตรวจประเมิน (Auditor Notes)',
                MODIFY COLUMN `created_at` TIMESTAMP NULL COMMENT 'วันเวลาที่สร้างไฟล์คลัง (Timestamp)',
                MODIFY COLUMN `updated_at` TIMESTAMP NULL COMMENT 'วันเวลาที่มีการอัปเดตข้อมูล (Timestamp)'
            ");
        }

        // 3. database_backups Comments
        if (Schema::hasTable('database_backups')) {
            DB::statement("ALTER TABLE `database_backups` COMMENT = 'ตารางประวัติการสำรองฐานข้อมูลอัตโนมัติและการเข้ารหัส AES-256'");

            DB::statement("ALTER TABLE `database_backups`
                MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสรายการสำรองฐานข้อมูล (Primary Key Auto-Increment)',
                MODIFY COLUMN `filename` VARCHAR(255) NOT NULL COMMENT 'ชื่อไฟล์ ZIP สำรองฐานข้อมูล (เช่น backup_db_hrsystem_YYYYMMDD_HHMMSS.zip)',
                MODIFY COLUMN `file_path` VARCHAR(255) NOT NULL COMMENT 'เส้นทางจัดเก็บไฟล์ ZIP ในระบบ (Relative Storage Path)',
                MODIFY COLUMN `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ขนาดไฟล์ ZIP เป็นหน่วยไบต์ (Bytes)',
                MODIFY COLUMN `file_size_human` VARCHAR(50) NOT NULL DEFAULT '0 KB' COMMENT 'ขนาดไฟล์ ZIP ที่อ่านง่าย (เช่น 1.27 MB)',
                MODIFY COLUMN `tables_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'จำนวนตารางในฐานข้อมูลที่ถูกสำรอง',
                MODIFY COLUMN `rows_count` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'จำนวนแถวข้อมูลรวมทั้งหมดที่ถูกสำรอง',
                MODIFY COLUMN `checksum_sha256` VARCHAR(64) NULL COMMENT 'ค่าแฮชตรวจสอบความสมบูรณ์ของไฟล์สำรอง (SHA-256 Checksum)',
                MODIFY COLUMN `is_encrypted` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'สถานะการเข้ารหัสไฟล์ ZIP (1 = เข้ารหัส AES-256, 0 = ไม่ได้เข้ารหัส)',
                MODIFY COLUMN `encryption_algorithm` VARCHAR(50) NOT NULL DEFAULT 'AES-256' COMMENT 'อัลกอริทึมที่ใช้เข้ารหัสไฟล์ (AES-256)',
                MODIFY COLUMN `encrypted_password` TEXT NULL COMMENT 'รหัสผ่านถอดรหัสไฟล์ ZIP ที่ถูกเข้ารหัสด้วย Laravel Encryption Key',
                MODIFY COLUMN `md_file_path` VARCHAR(255) NULL COMMENT 'เส้นทางจัดเก็บไฟล์ Markdown คู่มือโครงสร้างฐานข้อมูลและตาราง',
                MODIFY COLUMN `email_sent_to` VARCHAR(255) NULL COMMENT 'อีเมลผู้รับรหัสผ่านถอดรหัสไฟล์ (เจ้าหน้าที่ ICT)',
                MODIFY COLUMN `email_sent_at` DATETIME NULL COMMENT 'วันเวลาที่ส่งรหัสผ่านถอดรหัสไปยังอีเมลเรียบร้อยแล้ว',
                MODIFY COLUMN `dumper_engine` VARCHAR(50) NOT NULL DEFAULT 'pdo_native' COMMENT 'เครื่องมือหรือ Engine ที่ใช้สำรองฐานข้อมูล (เช่น pdo_native, mysqldump)',
                MODIFY COLUMN `created_by` BIGINT UNSIGNED NULL COMMENT 'รหัสผู้ใช้ที่เป็นผู้สั่งสำรองฐานข้อมูล (User ID หรือ null หากเป็นระบบอัตโนมัติ)',
                MODIFY COLUMN `created_by_name` VARCHAR(255) NULL COMMENT 'ชื่อผู้สั่งสำรองฐานข้อมูล (หรือ ระบบอัตโนมัติ)',
                MODIFY COLUMN `notes` TEXT NULL COMMENT 'หมายเหตุหรือรายละเอียดเพิ่มเติมเกี่ยวกับการสำรองฐานข้อมูล',
                MODIFY COLUMN `created_at` TIMESTAMP NULL COMMENT 'วันเวลาที่สำรองฐานข้อมูล (Timestamp)',
                MODIFY COLUMN `updated_at` TIMESTAMP NULL COMMENT 'วันเวลาที่มีการอัปเดตข้อมูล (Timestamp)'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Comments do not require reversal
    }
};
