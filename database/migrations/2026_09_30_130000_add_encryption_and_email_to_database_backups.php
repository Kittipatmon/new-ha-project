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
        Schema::table('database_backups', function (Blueprint $table) {
            $table->boolean('is_encrypted')->default(true)->after('checksum_sha256')->comment('สถานะการเข้ารหัสไฟล์ ZIP (1 = เข้ารหัส AES-256, 0 = ไม่ได้เข้ารหัส)');
            $table->string('encryption_algorithm', 50)->default('AES-256')->after('is_encrypted')->comment('อัลกอริทึมที่ใช้เข้ารหัสไฟล์ (AES-256)');
            $table->text('encrypted_password')->nullable()->after('encryption_algorithm')->comment('รหัสผ่านถอดรหัสไฟล์ ZIP ที่ถูกเข้ารหัสด้วย Laravel Encryption Key');
            $table->string('md_file_path')->nullable()->after('encrypted_password')->comment('เส้นทางจัดเก็บไฟล์ Markdown คู่มือโครงสร้างฐานข้อมูลและตาราง');
            $table->string('email_sent_to')->nullable()->after('md_file_path')->comment('อีเมลผู้รับรหัสผ่านถอดรหัสไฟล์ (เจ้าหน้าที่ ICT)');
            $table->dateTime('email_sent_at')->nullable()->after('email_sent_to')->comment('วันเวลาที่ส่งรหัสผ่านถอดรหัสไปยังอีเมลเรียบร้อยแล้ว');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('database_backups', function (Blueprint $table) {
            $table->dropColumn([
                'is_encrypted',
                'encryption_algorithm',
                'encrypted_password',
                'md_file_path',
                'email_sent_to',
                'email_sent_at',
            ]);
        });
    }
};
