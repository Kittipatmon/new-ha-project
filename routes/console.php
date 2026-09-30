<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * กำหนดเวลางานอัตโนมัติประจำปี (Data Retention Policy 5 Years):
 * ทุกวันที่ 1 มกราคม เวลา 00:05 น. ระบบจะ:
 * 1. บีบอัด Audit Log ของปีที่เพิ่งสิ้นสุดเป็นไฟล์ ZIP พร้อมคำนวณ SHA-256
 * 2. ล้างข้อมูล Log ในตารางฐานข้อมูลเพื่อเริ่มรอบปีใหม่ (ลดภาระ DB)
 * 3. ตรวจสอบและทำความสะอาดไฟล์ ZIP คลังเก่าที่เก็บเกิน 5 ปีออก
 */
Schedule::command('audit:archive --auto-yearly --purge --clean-expired --years=5')
    ->yearlyOn(1, 1, '00:05')
    ->name('annual-audit-archive-5years')
    ->withoutOverlapping()
    ->runInBackground();

/**
 * กำหนดเวลางานสำรองฐานข้อมูลอัตโนมัติประจำวัน (Daily Midnight Database Backup):
 * รันทุกวัน เวลา 00:00 น. (เที่ยงคืน) เพื่อ:
 * 1. บันทึกโครงสร้างและข้อมูลของทุกตารางใน MySQL
 * 2. บีบอัดเป็นไฟล์ ZIP (.zip) พร้อมแฮช SHA-256 ป้องกันความเสียหาย
 * 3. จัดเก็บใน Private Storage นอกเว็บ (storage/app/backups/db/)
 * 4. ลบไฟล์สำรองเก่าที่เกิน 30 วันออกอัตโนมัติเพื่อป้องกันพื้นที่โฮสต์เต็ม
 */
Schedule::command('db:backup --clean --keep=30')
    ->dailyAt('00:00')
    ->name('daily-database-backup-midnight')
    ->withoutOverlapping()
    ->runInBackground();

