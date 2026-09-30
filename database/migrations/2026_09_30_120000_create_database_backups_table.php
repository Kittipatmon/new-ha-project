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
        if (!Schema::hasTable('database_backups')) {
            Schema::create('database_backups', function (Blueprint $table) {
                $table->comment('ตารางประวัติการสำรองฐานข้อมูลอัตโนมัติและการเข้ารหัส AES-256');
                $table->id()->comment('รหัสรายการสำรองฐานข้อมูล (Primary Key Auto-Increment)');
                $table->string('filename')->comment('ชื่อไฟล์ ZIP สำรองฐานข้อมูล (เช่น backup_db_hrsystem_YYYYMMDD_HHMMSS.zip)');
                $table->string('file_path')->comment('เส้นทางจัดเก็บไฟล์ ZIP ในระบบ (Relative Storage Path)');
                $table->unsignedBigInteger('file_size')->default(0)->comment('ขนาดไฟล์ ZIP เป็นหน่วยไบต์ (Bytes)');
                $table->string('file_size_human', 50)->default('0 KB')->comment('ขนาดไฟล์ ZIP ที่อ่านง่าย (เช่น 1.27 MB)');
                $table->unsignedInteger('tables_count')->default(0)->comment('จำนวนตารางในฐานข้อมูลที่ถูกสำรอง');
                $table->unsignedBigInteger('rows_count')->default(0)->comment('จำนวนแถวข้อมูลรวมทั้งหมดที่ถูกสำรอง');
                $table->string('checksum_sha256', 64)->nullable()->comment('ค่าแฮชตรวจสอบความสมบูรณ์ของไฟล์สำรอง (SHA-256 Checksum)');
                $table->string('dumper_engine', 50)->default('pdo_native')->comment('เครื่องมือหรือ Engine ที่ใช้สำรองฐานข้อมูล (เช่น pdo_native, mysqldump)');
                $table->unsignedBigInteger('created_by')->nullable()->comment('รหัสผู้ใช้ที่เป็นผู้สั่งสำรองฐานข้อมูล (User ID หรือ null หากเป็นระบบอัตโนมัติ)');
                $table->string('created_by_name')->nullable()->comment('ชื่อผู้สั่งสำรองฐานข้อมูล (หรือ ระบบอัตโนมัติ)');
                $table->text('notes')->nullable()->comment('หมายเหตุหรือรายละเอียดเพิ่มเติมเกี่ยวกับการสำรองฐานข้อมูล');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_backups');
    }
};
