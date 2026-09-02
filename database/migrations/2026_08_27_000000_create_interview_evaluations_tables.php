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
        Schema::create('interview_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('ID ผู้คีย์ข้อมูล');
            $table->date('evaluation_date')->nullable()->comment('วันที่สัมภาษณ์');
            $table->string('candidate_prefix')->nullable()->comment('คำนำหน้าชื่อผู้สมัคร');
            $table->string('candidate_name')->comment('ชื่อ-นามสกุล ผู้สมัคร');
            $table->string('position_applied')->nullable()->comment('ตำแหน่งที่สมัคร');
            $table->string('department')->nullable()->comment('แผนก');
            $table->string('division')->nullable()->comment('ฝ่าย');
            $table->integer('interview_times')->default(1)->comment('สัมภาษณ์ครั้งที่');

            // คะแนนรวมและเฉลี่ย
            $table->integer('total_hr_score')->default(0)->comment('รวมคะแนนฝ่ายบุคคล');
            $table->integer('total_dept_score')->default(0)->comment('รวมคะแนนต้นสังกัด');
            $table->integer('grand_total_score')->default(0)->comment('รวมคะแนนทั้งหมด');
            $table->decimal('average_score', 5, 2)->default(0)->comment('คะแนนรวม (1+2 หารสอง)');

            $table->text('remarks')->nullable()->comment('หมายเหตุ');
            $table->string('summary_result')->nullable()->comment('สรุปผลสัมภาษณ์: hire (30-40), reserve (20-29), reject (<20)');
            
            // ลายเซ็นต์ฝ่ายบุคคล
            $table->string('hr_evaluator_name')->nullable()->comment('ชื่อผู้ประเมิน ฝ่ายบุคคล');
            $table->string('hr_position')->nullable()->comment('ตำแหน่ง ฝ่ายบุคคล');
            $table->date('hr_signed_date')->nullable()->comment('วันที่ลงชื่อ ฝ่ายบุคคล');

            // ลายเซ็นต์ต้นสังกัด
            $table->string('dept_evaluator_name')->nullable()->comment('ชื่อผู้ประเมิน ต้นสังกัด');
            $table->string('dept_position')->nullable()->comment('ตำแหน่ง ต้นสังกัด');
            $table->date('dept_signed_date')->nullable()->comment('วันที่ลงชื่อ ต้นสังกัด');

            $table->string('status')->default('completed')->comment('สถานะแบบประเมิน');
            $table->timestamps();
        });

        Schema::create('interview_evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_evaluation_id')->constrained('interview_evaluations')->onDelete('cascade');
            $table->integer('item_no')->comment('ลำดับข้อ 1-10');
            $table->string('topic_title')->comment('หัวข้อในการพิจารณา');
            $table->integer('hr_score')->nullable()->comment('คะแนนฝ่ายบุคคล (1-4)');
            $table->integer('dept_score')->nullable()->comment('คะแนนต้นสังกัด (1-4)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_evaluation_scores');
        Schema::dropIfExists('interview_evaluations');
    }
};
