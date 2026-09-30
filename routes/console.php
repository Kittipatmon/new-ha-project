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
