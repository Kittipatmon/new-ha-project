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

        // 5.2 job_positions
        Schema::create('recruitment_job_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('department_id')->comment('ID แผนก');
            $table->string('position_code')->unique()->comment('รหัสตำแหน่ง');
            $table->string('position_name')->comment('ชื่อตำแหน่ง');
            $table->string('position_level')->nullable()->comment('ระดับตำแหน่ง');
            $table->string('employment_type')->nullable()->comment('ประเภทการจ้างงาน'); // Full-time, Part-time, etc.
            $table->text('description')->nullable()->comment('รายละเอียดงาน');
            $table->text('qualification')->nullable()->comment('คุณสมบัติผู้สมัคร');
            $table->decimal('salary_min', 12, 2)->nullable()->comment('เงินเดือนขั้นต่ำ');
            $table->decimal('salary_max', 12, 2)->nullable()->comment('เงินเดือนสูงสุด');
            $table->string('status')->default('active')->comment('สถานะตำแหน่ง (active, inactive)');
            $table->timestamps();
        });

        // 5.3 recruitment_requests
        Schema::create('recruitment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique()->comment('เลขที่ใบขออัตรากำลัง');
            $table->integer('department_id')->comment('ID แผนกที่ขอ');
            $table->foreignId('job_position_id')->constrained('recruitment_job_positions')->comment('ID ตำแหน่งงานที่ขอ');
            $table->unsignedBigInteger('requested_by')->comment('ID ผู้ขอ');
            $table->integer('headcount')->comment('จำนวนอัตราที่ขอ');
            $table->text('reason')->nullable()->comment('เหตุผลการขอ');
            $table->text('job_description')->nullable()->comment('รายละเอียดงาน');
            $table->text('qualification')->nullable()->comment('คุณสมบัติผู้สมัคร');
            $table->decimal('salary_min', 12, 2)->nullable()->comment('เงินเดือนขั้นต่ำ');
            $table->decimal('salary_max', 12, 2)->nullable()->comment('เงินเดือนสูงสุด');
            $table->date('required_start_date')->nullable()->comment('วันที่ต้องการให้เริ่มงาน');
            $table->string('status')->default('draft')->comment('สถานะใบคำขอ (draft, pending_manager, pending_executive, approved, rejected, cancelled)'); // draft, pending_manager, pending_executive, approved, rejected, cancelled
            $table->unsignedBigInteger('approved_by_manager')->nullable()->comment('ID ผู้จัดการที่อนุมัติ');
            $table->timestamp('approved_at_manager')->nullable()->comment('วันเวลาที่ผู้จัดการอนุมัติ');
            $table->unsignedBigInteger('approved_by_executive')->nullable()->comment('ID ผู้บริหารที่อนุมัติ');
            $table->timestamp('approved_at_executive')->nullable()->comment('วันเวลาที่ผู้บริหารอนุมัติ');
            $table->text('remarks')->nullable()->comment('หมายเหตุ');
            $table->timestamps();
        });

        // 5.4 job_posts
        Schema::create('recruitment_job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_request_id')->nullable()->constrained('recruitment_requests')->comment('ID ใบขออัตรากำลัง');
            $table->foreignId('job_position_id')->constrained('recruitment_job_positions')->comment('ID ตำแหน่งงาน');
            $table->string('title')->comment('หัวข้อประกาศรับสมัคร');
            $table->string('slug')->unique()->comment('Slug สำหรับ URL');
            $table->integer('department_id')->comment('ID แผนกที่ประกาศ');
            $table->integer('vacancy')->comment('จำนวนอัตราที่รับ');
            $table->string('employment_type')->comment('ประเภทการจ้างงาน');
            $table->string('location')->nullable()->comment('สถานที่ปฏิบัติงาน');
            $table->string('work_schedule')->nullable()->comment('ตารางเวลาทำงาน');
            $table->decimal('salary_min', 12, 2)->nullable()->comment('เงินเดือนขั้นต่ำ');
            $table->decimal('salary_max', 12, 2)->nullable()->comment('เงินเดือนสูงสุด');
            $table->string('salary_note')->nullable()->comment('หมายเหตุเรื่องเงินเดือน');
            $table->text('job_description')->nullable()->comment('รายละเอียดงาน');
            $table->text('qualification')->nullable()->comment('คุณสมบัติผู้สมัคร');
            $table->text('benefits')->nullable()->comment('สวัสดิการ');
            $table->text('required_documents')->nullable()->comment('เอกสารที่ต้องใช้');
            $table->date('start_date')->nullable()->comment('วันที่เริ่มประกาศ');
            $table->date('end_date')->nullable()->comment('วันที่สิ้นสุดประกาศ');
            $table->string('publish_status')->default('draft')->comment('สถานะการประกาศ (draft, published, closed, expired)'); // draft, published, closed, expired
            $table->timestamp('published_at')->nullable()->comment('วันเวลาที่ประกาศ');
            $table->unsignedBigInteger('created_by')->comment('ID ผู้สร้าง'); // No constrained('users') to remove
            $table->unsignedBigInteger('updated_by')->nullable()->comment('ID ผู้แก้ไขล่าสุด'); // No constrained('users') to remove
            $table->timestamps();
        });

        // 5.5 applicants ผู้สมัคร
        Schema::create('recruitment_applicants', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->nullable()->comment('คำนำหน้าชื่อ');
            $table->string('first_name')->comment('ชื่อ');
            $table->string('last_name')->comment('นามสกุล');
            $table->string('gender')->nullable()->comment('เพศ');
            $table->date('date_of_birth')->nullable()->comment('วันเกิด');
            $table->string('national_id')->unique()->nullable()->comment('เลขบัตรประชาชน');
            $table->string('phone')->comment('เบอร์โทรศัพท์');
            $table->string('email')->comment('อีเมล');
            $table->string('line_id')->nullable()->comment('Line ID');
            $table->text('address')->nullable()->comment('ที่อยู่');
            $table->string('province')->nullable()->comment('จังหวัด');
            $table->string('education_level')->nullable()->comment('ระดับการศึกษาล่าสุด');
            $table->string('university_name')->nullable()->comment('มหาวิทยาลัย');
            $table->string('faculty')->nullable()->comment('คณะ');
            $table->string('major')->nullable()->comment('สาขาวิชา');
            $table->decimal('gpa', 3, 2)->nullable()->comment('เกรดเฉลี่ย');
            $table->string('current_company')->nullable()->comment('บริษัทปัจจุบัน');
            $table->string('current_position')->nullable()->comment('ตำแหน่งงานปัจจุบัน');
            $table->integer('years_of_experience')->nullable()->comment('จำนวนปีประสบการณ์');
            $table->decimal('expected_salary', 12, 2)->nullable()->comment('เงินเดือนที่คาดหวัง');
            $table->string('resume_file')->nullable()->comment('ไฟล์ Resume');
            $table->string('photo_file')->nullable()->comment('ไฟล์รูปถ่าย');
            $table->string('portfolio_file')->nullable()->comment('ไฟล์ Portfolio');
            $table->boolean('pdpa_consent')->default(false)->comment('ความยินยอม PDPA');
            $table->string('status')->default('active')->comment('สถานะผู้ติดตาม (active, archive)');
            $table->timestamps();
        });

        // 5.6 applications ใบสมัคร
        Schema::create('recruitment_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no')->unique()->comment('เลขที่ใบสมัคร');
            $table->foreignId('job_post_id')->constrained('recruitment_job_posts')->comment('ID ประกาศรับสมัคร');
            $table->foreignId('applicant_id')->constrained('recruitment_applicants')->comment('ID ผู้สมัคร');
            $table->timestamp('applied_at')->useCurrent()->comment('วันเวลาที่สมัคร');
            $table->string('source')->nullable()->comment('แหล่งที่มาของใบสมัคร');
            $table->text('cover_letter')->nullable()->comment('จดหมายแนะนำตัว');
            $table->string('status')->default('submitted')->comment('สถานะใบสมัคร (submitted, under_review, shortlisted, etc.)'); // submitted, under_review, shortlisted, interview_scheduled, interviewed, passed, failed, reserve, hired, rejected, withdrawn
            $table->integer('screening_score')->nullable()->comment('คะแนนการคัดกรองเบื้องต้น');
            $table->text('screening_result')->nullable()->comment('ผลการคัดกรองเบื้องต้น');
            $table->unsignedBigInteger('screened_by')->nullable()->comment('ID ผู้คัดกรอง');
            $table->timestamp('screened_at')->nullable()->comment('วันเวลาที่คัดกรอง');
            $table->string('final_result')->nullable()->comment('ผลการพิจารณาสุดท้าย');
            $table->timestamp('final_result_at')->nullable()->comment('วันเวลาที่สรุปผลสุดท้าย');
            $table->text('remarks')->nullable()->comment('หมายเหตุ');
            $table->timestamps();
        });

        // 5.7 applicant_documents เอกสาร
        Schema::create('recruitment_applicant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('recruitment_applications')->comment('ID ใบสมัคร');
            $table->string('document_type')->comment('ประเภทเอกสาร (resume, transcript, etc.)'); // resume, transcript, certificate, license, portfolio, other
            $table->string('file_name')->comment('ชื่อไฟล์');
            $table->string('file_path')->comment('พาธที่เก็บไฟล์');
            $table->integer('file_size')->comment('ขนาดไฟล์ (bytes)');
            $table->timestamp('uploaded_at')->useCurrent()->comment('วันเวลาที่อัปโหลด');
            $table->timestamps();
        });

        // 5.8 application_education ประวัติการศึกษา
        Schema::create('recruitment_application_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('recruitment_applicants')->comment('ID ผู้สมัคร');
            $table->string('level')->comment('ระดับการศึกษา');
            $table->string('institution_name')->comment('ชื่อสถาบัน');
            $table->string('faculty')->nullable()->comment('คณะ');
            $table->string('major')->nullable()->comment('สาขาวิชา');
            $table->string('start_year')->nullable()->comment('ปีที่เริ่มเรียน');
            $table->string('end_year')->nullable()->comment('ปีที่จบการศึกษา');
            $table->decimal('gpa', 3, 2)->nullable()->comment('เกรดเฉลี่ย');
            $table->timestamps();
        });

        // 5.9 application_experiences ประวัติการทำงาน (มีไม่มีก็ได้)
        Schema::create('recruitment_application_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('recruitment_applicants')->comment('ID ผู้สมัคร');
            $table->string('company_name')->comment('ชื่อบริษัท');
            $table->string('position')->comment('ตำแหน่งงาน');
            $table->date('start_date')->nullable()->comment('วันที่เริ่มงาน');
            $table->date('end_date')->nullable()->comment('วันที่สิ้นสุดงาน');
            $table->text('job_detail')->nullable()->comment('รายละเอียดงาน');
            $table->decimal('salary', 12, 2)->nullable()->comment('เงินเดือน');
            $table->text('reason_for_leaving')->nullable()->comment('เหตุผลที่ลาออก');
            $table->timestamps();
        });

        // 5.10 interviews การสัมภาษณ์
        Schema::create('recruitment_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('recruitment_applications')->comment('ID ใบสมัคร');
            $table->integer('interview_round')->default(1)->comment('รอบการสัมภาษณ์');
            $table->string('interview_type')->nullable()->comment('รูปแบบการสัมภาษณ์ (Online, On-site)'); // Online, On-site
            $table->date('interview_date')->comment('วันที่สัมภาษณ์');
            $table->time('interview_time')->comment('เวลาที่สัมภาษณ์');
            $table->string('location')->nullable()->comment('สถานที่/ลิ้งค์การสัมภาษณ์');
            $table->string('meeting_link')->nullable()->comment('ลิงก์การประชุมออนไลน์');
            $table->unsignedBigInteger('interviewer_id')->comment('ID ผู้สัมภาษณ์');
            $table->string('status')->default('scheduled')->comment('สถานะการสัมภาษณ์ (scheduled, completed, cancelled, etc.)'); // scheduled, completed, postponed, cancelled, absent
            $table->text('note')->nullable()->comment('บันทึกเพิ่มเติม');
            $table->timestamps();
        });

        // 5.11 interview_scores คะแนนการสัมภาษณ์
        Schema::create('recruitment_interview_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('recruitment_interviews')->comment('ID การสัมภาษณ์');
            $table->string('criteria_name')->comment('ชื่อเกณฑ์การประเมิน');
            $table->integer('score')->comment('คะแนนที่ได้');
            $table->integer('max_score')->default(10)->comment('คะแนนเต็ม');
            $table->text('comment')->nullable()->comment('ความคิดเห็นเพิ่มเติมภ');
            $table->timestamps();
        });

        // 5.12 approvals อนุมัติ
        Schema::create('recruitment_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('module_type')->comment('ประเภทโมดูลที่ขออนุมัติ (recruitment_request, hiring_decision)'); // recruitment_request, hiring_decision
            $table->unsignedBigInteger('module_id')->comment('ID ของโมดูลนั้นๆ');
            $table->string('step_name')->comment('ชื่อขั้นตอนการอนุมัติ');
            $table->unsignedBigInteger('approver_id')->comment('ID ผู้อนุมัติ');
            $table->string('status')->comment('สถานะการอนุมัติ (approved, rejected, returned)'); // approved, rejected, returned
            $table->timestamp('approved_at')->nullable()->comment('วันเวลาที่อนุมัติ');
            $table->text('comment')->nullable()->comment('ความคิดเห็น');
            $table->timestamps();
        });

        // 5.13 recruitment_status_logs ประวัติสถานะ
        Schema::create('recruitment_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('recruitment_applications')->comment('ID ใบสมัคร');
            $table->string('old_status')->nullable()->comment('สถานะเดิม');
            $table->string('new_status')->comment('สถานะใหม่');
            $table->unsignedBigInteger('changed_by')->comment('ID ผู้เปลี่ยนสถานะ');
            $table->timestamp('changed_at')->useCurrent()->comment('วันเวลาที่เปลี่ยนสถานะ');
            $table->text('remark')->nullable()->comment('หมายเหตุการเปลี่ยนสถานะ');
            $table->timestamps();
        });

        // 5.14 notifications แจ้งเตือน
        Schema::create('recruitment_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID ผู้รับแจ้งเตือน');
            $table->string('type')->comment('ประเภทการแจ้งเตือน');
            $table->string('title')->comment('หัวข้อแจ้งเตือน');
            $table->text('message')->comment('ข้อความแจ้งเตือน');
            $table->string('reference_type')->nullable()->comment('ประเภทข้อมูลที่อ้างอิง');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID ข้อมูลที่อ้างอิง');
            $table->boolean('is_read')->default(false)->comment('สถานะการอ่าน (true = อ่านแล้ว)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_notifications');
        Schema::dropIfExists('recruitment_status_logs');
        Schema::dropIfExists('recruitment_approvals');
        Schema::dropIfExists('recruitment_interview_scores');
        Schema::dropIfExists('recruitment_interviews');
        Schema::dropIfExists('recruitment_application_experiences');
        Schema::dropIfExists('recruitment_application_education');
        Schema::dropIfExists('recruitment_applicant_documents');
        Schema::dropIfExists('recruitment_applications');
        Schema::dropIfExists('recruitment_applicants');
        Schema::dropIfExists('recruitment_job_posts');
        Schema::dropIfExists('recruitment_requests');
        Schema::dropIfExists('recruitment_job_positions');
    }
};
