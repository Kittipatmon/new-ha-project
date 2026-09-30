<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('system_audit_logs')) {
            Schema::create('system_audit_logs', function (Blueprint $table) {
                $table->comment('ตารางบันทึกประวัติการเปลี่ยนแปลงและกิจกรรมในระบบ (System Audit Trail)');
                $table->id()->comment('รหัสบันทึกกิจกรรม (Primary Key Auto-Increment)');
                $table->unsignedBigInteger('user_id')->nullable()->index()->comment('รหัสผู้ใช้งานที่ทำรายการ (FK อ้างอิงตาราง users / userskml)');
                $table->string('user_code')->nullable()->comment('รหัสพนักงานหรือรหัสประจำตัวของผู้ดำเนินการ (เช่น 11668)');
                $table->string('user_name')->nullable()->comment('ชื่อ-นามสกุลของผู้ดำเนินการ');
                $table->string('user_email')->nullable()->comment('อีเมลของผู้ดำเนินการ');
                $table->string('user_role')->nullable()->comment('บทบาทหรือระดับสิทธิ์ของผู้ดำเนินการ (เช่น admin, hr, user)');
                $table->string('action', 50)->index()->comment('ประเภทกิจกรรม (created, updated, deleted, login, logout, archived, restored, exported, backup, security_alert)');
                $table->string('module', 50)->index()->comment('รหัสโมดูลระบบ (recruitment, training, users, settings, system, auth)');
                $table->string('module_name', 100)->nullable()->comment('ชื่อโมดูลระบบภาษาไทย (เช่น ระบบสรรหาบุคลากร, ระบบสำรองฐานข้อมูลอัตโนมัติ)');
                $table->string('model_type')->nullable()->index()->comment('ชื่อ Model Class ของข้อมูลที่ถูกกระทำ (Eloquent Model)');
                $table->string('model_id')->nullable()->index()->comment('รหัส Primary Key ของข้อมูลที่ถูกกระทำใน Model');
                $table->text('description')->comment('คำอธิบายรายละเอียดของกิจกรรมที่เกิดขึ้น');
                $table->longText('old_values')->nullable()->comment('ค่าข้อมูลเดิมก่อนการเปลี่ยนแปลง (JSON format)');
                $table->longText('new_values')->nullable()->comment('ค่าข้อมูลใหม่หลังการเปลี่ยนแปลง (JSON format)');
                $table->longText('diff')->nullable()->comment('ข้อมูลเปรียบเทียบความแตกต่างเฉพาะฟิลด์ที่มีการแก้ไข (JSON format)');
                $table->string('ip_address', 45)->nullable()->comment('หมายเลข IP Address ของผู้ใช้งานที่ทำรายการ (IPv4 / IPv6)');
                $table->text('user_agent')->nullable()->comment('ข้อมูลเว็บบราวเซอร์และอุปกรณ์ของผู้ใช้งาน (Client User Agent)');
                $table->text('url')->nullable()->comment('URL ของหน้าเว็บหรือ API Endpoint ที่ถูกเรียกใช้งาน');
                $table->string('method', 10)->nullable()->comment('HTTP Request Method (GET, POST, PUT, PATCH, DELETE)');
                $table->timestamps();

                $table->index(['created_at', 'action']);
                $table->index(['created_at', 'module']);
            });
        }

        if (!Schema::hasTable('system_audit_archives')) {
            Schema::create('system_audit_archives', function (Blueprint $table) {
                $table->comment('ตารางประวัติคลังไฟล์บีบอัด Audit Log รายปี จัดเก็บ 5 ปี (ISO/IEC 27001)');
                $table->id()->comment('รหัสบันทึกคลังไฟล์บีบอัด (Primary Key Auto-Increment)');
                $table->string('filename')->comment('ชื่อไฟล์ ZIP คลังข้อมูล (เช่น audit_archive_2026.zip)');
                $table->string('file_path')->comment('เส้นทางจัดเก็บไฟล์ในระบบ (Relative Storage Path)');
                $table->unsignedBigInteger('file_size')->default(0)->comment('ขนาดไฟล์เป็นหน่วยไบต์ (Bytes)');
                $table->string('file_size_human', 50)->default('0 KB')->comment('ขนาดไฟล์ที่อ่านง่าย (เช่น 1.25 MB)');
                $table->unsignedInteger('records_count')->default(0)->comment('จำนวนรายการ Log ทั้งหมดที่ถูกบีบอัดในไฟล์นี้');
                $table->dateTime('period_start')->comment('วันเวลาเริ่มต้นของช่วงข้อมูลที่จัดเก็บ');
                $table->dateTime('period_end')->comment('วันเวลาสิ้นสุดของช่วงข้อมูลที่จัดเก็บ');
                $table->string('period_label', 100)->comment('ป้ายกำกับช่วงปีหรือรอบข้อมูล (เช่น ปี 2026)');
                $table->string('checksum_sha256', 64)->comment('ค่าแฮชตรวจสอบความถูกต้องของไฟล์เพื่อความปลอดภัยตามมาตรฐาน ISO (SHA-256 Checksum)');
                $table->unsignedBigInteger('archived_by')->nullable()->comment('รหัสผู้ใช้ที่สั่งบีบอัดจัดเก็บไฟล์ (User ID)');
                $table->string('archived_by_name')->nullable()->comment('ชื่อผู้สั่งบีบอัดจัดเก็บไฟล์');
                $table->text('notes')->nullable()->comment('หมายเหตุหรือคำอธิบายสำหรับการตรวจสอบของผู้ตรวจประเมิน (Auditor Notes)');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_audit_archives');
        Schema::dropIfExists('system_audit_logs');
    }
};
