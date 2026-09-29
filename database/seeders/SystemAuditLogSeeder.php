<?php

namespace Database\Seeders;

use App\Models\SystemAuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SystemAuditLogSeeder extends Seeder
{
    public function run(): void
    {
        // Check if logs already exist
        if (SystemAuditLog::count() > 0) {
            return;
        }

        $admin = User::first();
        $adminName = $admin?->fullname ?? 'กิตติพัฒน์ มานุช';
        $adminCode = $admin?->emp_code ?? '11648';

        // 1. Current Year (2026) Logs: Create, Update, Delete
        SystemAuditLog::create([
            'user_id' => $admin?->id ?? 652,
            'user_code' => $adminCode,
            'user_name' => $adminName,
            'user_email' => 'Kittipat.Ma@kumwell.com',
            'user_role' => 'Admin / HR',
            'action' => 'updated',
            'module' => 'recruitment',
            'module_name' => 'ระบบสรรหาบุคลากร (เทมเพลตอีเมล)',
            'model_type' => 'App\Models\Recruitment\RecruitmentMailTemplate',
            'model_id' => '1',
            'description' => 'แก้ไขเทมเพลตอีเมล: 1. ยืนยันการรับสมัครงาน (ส่งหาผู้สมัคร) และอัปเดตลายเซ็นผู้ส่ง',
            'old_values' => [
                'sender_name' => 'ฝ่ายทรัพยากรบุคคล',
                'sender_position' => 'เจ้าหน้าที่สรรหา',
                'footer_salutation' => 'ขอแสดงความนับถือ',
                'header_tagline' => 'LIGHTNING WARNING SYSTEM',
            ],
            'new_values' => [
                'sender_name' => 'กิตติพัฒน์ มานุช',
                'sender_position' => 'เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล',
                'footer_salutation' => 'ด้วยความเคารพอย่างสูง,',
                'header_tagline' => 'POWER OF INNOVATION',
            ],
            'diff' => [
                'sender_name' => ['old' => 'ฝ่ายทรัพยากรบุคคล', 'new' => 'กิตติพัฒน์ มานุช'],
                'sender_position' => ['old' => 'เจ้าหน้าที่สรรหา', 'new' => 'เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล'],
                'footer_salutation' => ['old' => 'ขอแสดงความนับถือ', 'new' => 'ด้วยความเคารพอย่างสูง,'],
                'header_tagline' => ['old' => 'LIGHTNING WARNING SYSTEM', 'new' => 'POWER OF INNOVATION'],
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            'url' => 'http://127.0.0.1:8000/backend/recruitment/email-templates/application_received',
            'method' => 'POST',
            'created_at' => Carbon::now()->subMinutes(12),
            'updated_at' => Carbon::now()->subMinutes(12),
        ]);

        SystemAuditLog::create([
            'user_id' => $admin?->id ?? 652,
            'user_code' => $adminCode,
            'user_name' => $adminName,
            'user_email' => 'Kittipat.Ma@kumwell.com',
            'user_role' => 'Admin / HR',
            'action' => 'created',
            'module' => 'recruitment',
            'module_name' => 'ระบบสรรหาบุคลากร',
            'model_type' => 'App\Models\Recruitment\JobPost',
            'model_id' => '12',
            'description' => 'เพิ่มประกาศรับสมัครงานใหม่: Full Stack Software Engineer (ฝ่ายเทคโนโลยีสารสนเทศ ICT)',
            'old_values' => null,
            'new_values' => [
                'position_name' => 'Full Stack Software Engineer',
                'department_id' => '11',
                'department_name' => 'ICT',
                'employment_type' => 'ประจำ (Full-time)',
                'open_positions' => 2,
                'salary_range' => '35,000 - 55,000 บาท',
                'status' => 'เปิดรับสมัคร',
            ],
            'diff' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            'url' => 'http://127.0.0.1:8000/backend/recruitment/posts',
            'method' => 'POST',
            'created_at' => Carbon::now()->subHours(2),
            'updated_at' => Carbon::now()->subHours(2),
        ]);

        SystemAuditLog::create([
            'user_id' => $admin?->id ?? 652,
            'user_code' => $adminCode,
            'user_name' => $adminName,
            'user_email' => 'Kittipat.Ma@kumwell.com',
            'user_role' => 'Admin / HR',
            'action' => 'deleted',
            'module' => 'recruitment',
            'module_name' => 'ระบบสรรหาบุคลากร',
            'model_type' => 'App\Models\Recruitment\Application',
            'model_id' => '88',
            'description' => 'ลบข้อมูลใบสมัครงานทดสอบของผู้สมัคร: นายทดสอบ ระบบดี (ใบสมัคร APP-TEST-001)',
            'old_values' => [
                'application_no' => 'APP-TEST-001',
                'applicant_name' => 'นายทดสอบ ระบบดี',
                'position_name' => 'เจ้าหน้าที่ธุรการทั่วไป',
                'status' => 'ยกเลิกคำขอ',
            ],
            'new_values' => null,
            'diff' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            'url' => 'http://127.0.0.1:8000/backend/recruitment/applications/88',
            'method' => 'DELETE',
            'created_at' => Carbon::now()->subHours(5),
            'updated_at' => Carbon::now()->subHours(5),
        ]);

        SystemAuditLog::create([
            'user_id' => $admin?->id ?? 652,
            'user_code' => $adminCode,
            'user_name' => $adminName,
            'user_email' => 'Kittipat.Ma@kumwell.com',
            'user_role' => 'Admin',
            'action' => 'updated',
            'module' => 'settings',
            'module_name' => 'ตั้งค่าระบบ (Microsoft 365)',
            'model_type' => 'Setting',
            'model_id' => 'microsoft_graph',
            'description' => 'ปรับปรุงการตั้งค่า Microsoft 365 Client Secret และทดสอบการเชื่อมต่อ API',
            'old_values' => [
                'status' => 'รอการตั้งค่า',
                'tenant_id' => 'aed6dd7a-baec-4ce4-b204-a6bbc71b2748',
            ],
            'new_values' => [
                'status' => 'เชื่อมต่อสำเร็จ',
                'tenant_id' => 'aed6dd7a-baec-4ce4-b204-a6bbc71b2748',
            ],
            'diff' => [
                'status' => ['old' => 'รอการตั้งค่า', 'new' => 'เชื่อมต่อสำเร็จ'],
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            'url' => 'http://127.0.0.1:8000/backend/settings/microsoft',
            'method' => 'POST',
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        SystemAuditLog::create([
            'user_id' => $admin?->id ?? 652,
            'user_code' => $adminCode,
            'user_name' => $adminName,
            'user_email' => 'Kittipat.Ma@kumwell.com',
            'user_role' => 'Admin',
            'action' => 'login',
            'module' => 'auth',
            'module_name' => 'ระบบความปลอดภัยและการเข้าสู่ระบบ',
            'model_type' => 'App\Models\User',
            'model_id' => (string)($admin?->id ?? 652),
            'description' => "ผู้ใช้งาน {$adminName} เข้าสู่ระบบสำเร็จผ่านระบบตรวจสอบสิทธิ์",
            'old_values' => null,
            'new_values' => null,
            'diff' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            'url' => 'http://127.0.0.1:8000/login',
            'method' => 'POST',
            'created_at' => Carbon::now()->subDays(1)->subHours(3),
            'updated_at' => Carbon::now()->subDays(1)->subHours(3),
        ]);

        // 2. Historical Logs for 2021 (5 years ago) to demonstrate the 5-Year Retention & Zip Archive
        $histLogs = [
            [
                'action' => 'created',
                'module' => 'training',
                'module_name' => 'ระบบฝึกอบรม',
                'description' => 'เพิ่มหลักสูตรฝึกอบรม: การวิเคราะห์ระบบป้องกันฟ้าผ่าและกราวด์มาตรฐานสากล รุ่นที่ 1',
                'created_at' => Carbon::create(2021, 3, 15, 9, 30, 0),
            ],
            [
                'action' => 'updated',
                'module' => 'users',
                'module_name' => 'จัดการผู้ใช้งานและพนักงาน',
                'description' => 'ปรับปรุงแผนกและระดับสิทธิ์พนักงาน: กิตติพรรณ บุญช่วย',
                'diff' => [
                    'position' => ['old' => 'Junior Developer', 'new' => 'Software Engineer'],
                    'role' => ['old' => 'viewer', 'new' => 'editor'],
                ],
                'created_at' => Carbon::create(2021, 6, 20, 14, 15, 0),
            ],
            [
                'action' => 'deleted',
                'module' => 'training',
                'module_name' => 'ระบบฝึกอบรม',
                'description' => 'ลบข้อมูลผู้ลงทะเบียนฝึกอบรมที่สละสิทธิ์: คุณสมชาย ใจดี',
                'created_at' => Carbon::create(2021, 8, 10, 11, 45, 0),
            ],
            [
                'action' => 'updated',
                'module' => 'recruitment',
                'module_name' => 'ระบบสรรหาบุคลากร',
                'description' => 'ปิดการรับสมัครงานตำแหน่ง: วิศวกรไฟฟ้าโครงการ (ครบจำนวนผู้ได้รับการคัดเลือก)',
                'diff' => [
                    'status' => ['old' => 'เปิดรับสมัคร', 'new' => 'ปิดรับสมัคร'],
                ],
                'created_at' => Carbon::create(2021, 11, 30, 16, 20, 0),
            ],
        ];

        foreach ($histLogs as $hl) {
            SystemAuditLog::create([
                'user_id' => $admin?->id ?? 652,
                'user_code' => $adminCode,
                'user_name' => $adminName,
                'user_email' => 'Kittipat.Ma@kumwell.com',
                'user_role' => 'Admin',
                'action' => $hl['action'],
                'module' => $hl['module'],
                'module_name' => $hl['module_name'],
                'description' => $hl['description'],
                'diff' => $hl['diff'] ?? null,
                'ip_address' => '192.168.1.105',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'url' => 'http://127.0.0.1:8000/backend/archive',
                'method' => 'POST',
                'created_at' => $hl['created_at'],
                'updated_at' => $hl['created_at'],
            ]);
        }

        // Generate the 2021 Archive ZIP for auditor demonstration!
        try {
            AuditLogService::createArchive(2021, $admin, 'จัดเก็บคลัง Log ประจำปี 2564 ตามนโยบาย 5 ปีสำหรับ Audit ตรวจสอบ');
        } catch (\Throwable $e) {
            // ignore if error in zip
        }
    }
}
