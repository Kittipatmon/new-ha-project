<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('branch')->comment('ชื่อหัวข้อการฝึกอบรม');
            $table->integer('hours')->comment('จำนวนชั่วโมง');
            $table->string('format')->comment('รูปแบบการอบรม (เช่น ปกติ, ออนไลน์)');
            $table->string('start_date')->comment('วันที่เริ่มอบรม');
            $table->string('end_date')->comment('วันที่สิ้นสุดการอบรม');
            $table->string('department')->comment('หน่วยงานที่จัด');
            $table->string('status')->comment('สถานะ (available, full)');
            $table->text('details')->nullable()->comment('รายละเอียดหลักสูตร');
            $table->string('document')->nullable()->comment('เอกสารประกอบอัพโหลด PDF');
            $table->string('document_link')->nullable()->comment('ลิงก์สื่่อการสอน');
            $table->string('image')->nullable()->comment('รูปภาพประกอบ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
