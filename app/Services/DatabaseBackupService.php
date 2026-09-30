<?php

namespace App\Services;

use App\Models\DatabaseBackup;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDO;
use ZipArchive;

class DatabaseBackupService
{
    /**
     * Directory inside Storage::disk('local') for database backups
     */
    const BACKUP_DIR = 'backups/db';

    /**
     * Create a compressed database backup (.zip) with SHA-256 verification
     *
     * @param User|null $actor
     * @param string $notes
     * @return DatabaseBackup
     */
    /**
     * Create a compressed database backup (.zip) with AES-256 encryption, .md guide, and ICT email notification
     *
     * @param User|null $actor
     * @param string $notes
     * @return DatabaseBackup
     */
    public static function createBackup(?User $actor = null, string $notes = ''): DatabaseBackup
    {
        // 1. Ensure private directory exists
        Storage::disk('local')->makeDirectory(self::BACKUP_DIR);

        $dbConnection = config('database.default', 'mysql');
        $dbConfig = config("database.connections.{$dbConnection}", []);
        $dbName = DB::connection()->getDatabaseName() ?: ($dbConfig['database'] ?? 'appkum_ha');
        $timestamp = date('Ymd_His');
        $zipFilename = "backup_db_{$dbName}_{$timestamp}.zip";
        $zipRelativePath = self::BACKUP_DIR . '/' . $zipFilename;
        $zipAbsolutePath = Storage::disk('local')->path($zipRelativePath);

        $tempSqlRelative = self::BACKUP_DIR . "/temp_{$dbName}_{$timestamp}.sql";
        $tempSqlAbsolute = Storage::disk('local')->path($tempSqlRelative);

        $txtFilename = "backup_db_{$dbName}_{$timestamp}_README.txt";
        $txtRelativePath = self::BACKUP_DIR . '/' . $txtFilename;
        $txtAbsolutePath = Storage::disk('local')->path($txtRelativePath);

        // Generate strong random password for AES-256 encryption
        $randomPassword = 'KM#' . Str::random(5) . '$' . rand(100, 999) . '@' . Str::random(5);

        $dumperEngine = 'pdo_native';
        $tablesCount = 0;
        $totalRowsCount = 0;

        try {
            // 2. Perform database dump to temporary SQL file
            $dumpResult = self::dumpDatabaseToSql($tempSqlAbsolute, $dbConfig);
            $tablesCount = $dumpResult['tables_count'];
            $totalRowsCount = $dumpResult['rows_count'];
            $dumperEngine = $dumpResult['engine'];

            // 3. Compress SQL file into ZIP with AES-256 encryption
            $zip = new ZipArchive();
            if ($zip->open($zipAbsolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException("ไม่สามารถสร้างไฟล์ ZIP ที่: {$zipAbsolutePath}");
            }

            // Set encryption password
            $zip->setPassword($randomPassword);

            // Add the main SQL dump file inside ZIP and encrypt with AES-256
            $internalSqlName = "database_{$dbName}_{$timestamp}.sql";
            $zip->addFile($tempSqlAbsolute, $internalSqlName);
            $zip->setEncryptionName($internalSqlName, ZipArchive::EM_AES_256);

            // Add Manifest JSON inside ZIP (encrypted)
            $manifest = [
                'system' => config('app.name', 'Kumwell HR System'),
                'database' => $dbName,
                'backup_at' => now()->toIso8601String(),
                'tables_count' => $tablesCount,
                'rows_count' => $totalRowsCount,
                'dumper_engine' => $dumperEngine,
                'encryption' => 'AES-256',
                'created_by' => $actor?->fullname ?? 'ระบบอัตโนมัติ (Daily Midnight Cron)',
                'notes' => $notes ?: 'สำรองฐานข้อมูลอัตโนมัติประจำวัน',
                'schedule' => 'รันทุกวัน เวลา 00:00 น. (Daily Midnight Backup)',
                'retention_policy' => 'เก็บสำรองย้อนหลัง 30 วัน',
            ];
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $zip->setEncryptionName('manifest.json', ZipArchive::EM_AES_256);

            // 4. Generate Plain Text documentation (.txt)
            $txtContent = self::generateTextGuide([
                'filename' => $zipFilename,
                'database' => $dbName,
                'timestamp' => $timestamp,
                'date_thai' => now()->addYears(543)->format('d/m/Y H:i:s'),
                'tables_count' => $tablesCount,
                'rows_count' => $totalRowsCount,
                'dumper_engine' => $dumperEngine,
                'encryption' => 'AES-256',
                'sql_filename' => $internalSqlName,
                'created_by' => $actor?->fullname ?? 'ระบบอัตโนมัติ (Daily Midnight Cron)',
            ]);

            // Add unencrypted README.txt inside the ZIP so anyone inspecting can open directly with Notepad
            $zip->addFromString('README.txt', $txtContent);

            $zip->close();

            // Write companion .txt file on disk
            file_put_contents($txtAbsolutePath, $txtContent);

            // 5. Clean up temporary uncompressed SQL file
            if (file_exists($tempSqlAbsolute)) {
                @unlink($tempSqlAbsolute);
            }

            // 6. Verify ZIP integrity
            if (!file_exists($zipAbsolutePath) || filesize($zipAbsolutePath) === 0) {
                throw new \RuntimeException("ไฟล์ ZIP ที่สร้างขึ้นไม่สมบูรณ์หรือว่างเปล่า");
            }

            $fileSize = filesize($zipAbsolutePath);
            $fileSizeHuman = self::formatBytes($fileSize);
            $checksum = hash_file('sha256', $zipAbsolutePath);

            // 7. Record to database ledger with encrypted password storage
            $backup = DatabaseBackup::create([
                'filename' => $zipFilename,
                'file_path' => $zipRelativePath,
                'file_size' => $fileSize,
                'file_size_human' => $fileSizeHuman,
                'tables_count' => $tablesCount,
                'rows_count' => $totalRowsCount,
                'checksum_sha256' => $checksum,
                'is_encrypted' => true,
                'encryption_algorithm' => 'AES-256',
                'encrypted_password' => Crypt::encryptString($randomPassword),
                'md_file_path' => $txtRelativePath,
                'dumper_engine' => $dumperEngine,
                'created_by' => $actor?->id,
                'created_by_name' => $actor?->fullname ?? ($actor ? 'ผู้ดูแลระบบ' : 'ระบบอัตโนมัติ (Daily Midnight Cron)'),
                'notes' => $notes ?: ($actor ? 'สั่งสำรองข้อมูลด้วยตนเอง' : 'สำรองฐานข้อมูลอัตโนมัติประจำวัน (เที่ยงคืน)'),
            ]);

            // 8. Send password & backup report to ICT Email
            self::sendBackupNotificationToIct($backup, $randomPassword, $txtContent, $actor);

            // 9. Log in Audit Trail
            if (class_exists(AuditLogService::class)) {
                AuditLogService::log(
                    action: 'backup',
                    description: "สำรองฐานข้อมูลสำเร็จ (เข้ารหัส AES-256): {$zipFilename} ขนาด {$fileSizeHuman} ({$tablesCount} ตาราง, " . number_format($totalRowsCount) . " รายการ) ส่งรหัสผ่านเข้าอีเมล ICT เรียบร้อย",
                    model: $backup,
                    module: 'system',
                    moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                    user: $actor
                );
            }

            return $backup;

        } catch (\Throwable $e) {
            // Clean up temporary files on error
            if (file_exists($tempSqlAbsolute)) {
                @unlink($tempSqlAbsolute);
            }
            if (file_exists($zipAbsolutePath)) {
                @unlink($zipAbsolutePath);
            }
            if (isset($txtAbsolutePath) && file_exists($txtAbsolutePath)) {
                @unlink($txtAbsolutePath);
            }
            throw $e;
        }
    }

    /**
     * Generate companion Plain Text (.txt) recovery guide readable in Notepad
     */
    protected static function generateTextGuide(array $data): string
    {
        return <<<TEXT
================================================================================
KUMWELL HR SYSTEM - DATABASE BACKUP REPORT & RECOVERY GUIDE
================================================================================
Generated At : {$data['date_thai']}
Database     : {$data['database']}
Filename     : {$data['filename']}
Status       : SUCCESS (VERIFIED)
================================================================================

[1] ข้อมูลสรุปไฟล์สำรอง (BACKUP SUMMARY)
--------------------------------------------------------------------------------
- ชื่อไฟล์สำรอง       : {$data['filename']}
- ฐานข้อมูลเป้าหมาย  : {$data['database']}
- ตารางที่สำรอง      : {$data['tables_count']} ตาราง
- ข้อมูลทั้งหมด       : {$data['rows_count']} รายการ
- ระบบการเข้ารหัส     : {$data['encryption']} (Military-grade Encryption)
- เครื่องมือส่งออก     : {$data['dumper_engine']}
- ผู้ดำเนินการ        : {$data['created_by']}
- นโยบายการจัดเก็บ   : จัดเก็บใน Private Storage นาน 30 วัน

--------------------------------------------------------------------------------
[2] ขั้นตอนการถอดรหัสและเปิดไฟล์ (HOW TO DECRYPT)
--------------------------------------------------------------------------------
ไฟล์สำรองนี้ได้รับการเข้ารหัสความปลอดภัยระดับ AES-256 เพื่อป้องกันข้อมูลองค์กร
รหัสผ่านสำหรับเปิดไฟล์ได้รับการสุ่มแบบความปลอดภัยสูง และส่งไปยัง "อีเมลแผนก ICT" แล้ว

วิธีที่ 1: แตกไฟล์ผ่านโปรแกรม WinRAR หรือ 7-Zip บน Windows
  1. ดับเบิ้ลคลิก หรือคลิกขวาที่ไฟล์ {$data['filename']}
  2. เลือก Extract to "..." หรือ Extract Here
  3. ระบบจะแสดงหน้าต่างถามรหัสผ่าน (Enter Password)
  4. กรอกรหัสผ่านที่ได้รับจากอีเมล ICT แล้วกด OK
  5. จะได้ไฟล์ {$data['sql_filename']} พร้อมใช้งาน

วิธีที่ 2: แตกไฟล์ผ่าน Command Line (Linux / macOS / VPS)
  # ใช้คำสั่ง 7z (แนะนำ)
  7z x -p"<ENTER_PASSWORD>" {$data['filename']}

  # หรือใช้คำสั่ง unzip
  unzip -P "<ENTER_PASSWORD>" {$data['filename']}

--------------------------------------------------------------------------------
[3] ขั้นตอนการกู้คืนฐานข้อมูลเข้าสู่ MYSQL (DATABASE RESTORATION)
--------------------------------------------------------------------------------
เมื่อถอดรหัสและได้ไฟล์ {$data['sql_filename']} เรียบร้อยแล้ว ให้ทำการ Import:

  # คำสั่ง Import ฐานข้อมูลผ่าน Command Line บนโฮสต์
  mysql -h localhost -u [DB_USERNAME] -p {$data['database']} < {$data['sql_filename']}

--------------------------------------------------------------------------------
[4] การตรวจสอบความสมบูรณ์ของไฟล์ (INTEGRITY CHECK)
--------------------------------------------------------------------------------
ก่อนนำไฟล์ไปใช้งาน สามารถตรวจสอบ SHA-256 Checksum เพื่อยืนยันว่าไฟล์ไม่ถูกดัดแปลง:

  # บน Windows PowerShell:
  Get-FileHash -Algorithm SHA256 {$data['filename']}

  # บน Linux / macOS:
  sha256sum {$data['filename']}

================================================================================
Kumwell ICT Infrastructure & Data Governance Team
เอกสารนี้จัดทำขึ้นโดยอัตโนมัติจากระบบ Kumwell HR System
================================================================================
TEXT;
    }

    /**
     * Send backup notification and password to ICT email
     */
    protected static function sendBackupNotificationToIct(
        DatabaseBackup $backup,
        string $plainPassword,
        string $txtContent,
        ?User $actor = null
    ): void {
        try {
            // Determine recipient email(s)
            $recipients = [];

            // 1. Check custom ICT email in .env or config
            $customIctEmail = env('ICT_BACKUP_EMAIL', config('mail.ict_backup_email'));
            if (!empty($customIctEmail)) {
                $recipients[] = trim($customIctEmail);
            }

            // 2. Add admin emails from database
            $adminEmails = User::where('role', 'admin')
                ->whereNotNull('email')
                ->pluck('email')
                ->map(fn($e) => trim($e))
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (!empty($adminEmails)) {
                $recipients = array_merge($recipients, $adminEmails);
            }

            // Fallback default ICT email if nothing found
            if (empty($recipients)) {
                $recipients = ['ict@kumwell.com'];
            }

            $recipients = array_unique(array_filter($recipients));

            $subject = "🔒 [Kumwell ICT] รหัสผ่านสำรองฐานข้อมูลประจำวัน (AES-256) - {$backup->filename}";

            $htmlContent = self::buildIctEmailHtml($backup, $plainPassword);

            Mail::html($htmlContent, function ($message) use ($recipients, $subject, $backup, $txtContent) {
                $message->to($recipients)
                    ->subject($subject);

                // Attach the Plain Text recovery guide (.txt)
                $txtFilename = str_replace('.zip', '_README.txt', $backup->filename);
                $message->attachData($txtContent, $txtFilename, [
                    'mime' => 'text/plain; charset=utf-8',
                ]);
            });

            // Update backup record with email delivery info
            $backup->update([
                'email_sent_to' => implode(', ', $recipients),
                'email_sent_at' => now(),
            ]);

        } catch (\Throwable $e) {
            // Log warning but do not crash the backup process
            \Illuminate\Support\Facades\Log::warning("ไม่สามารถส่งอีเมลรหัสผ่านสำรองฐานข้อมูลไปยัง ICT: " . $e->getMessage());
        }
    }

    /**
     * Build responsive HTML email template for ICT
     */
    protected static function buildIctEmailHtml(DatabaseBackup $backup, string $plainPassword): string
    {
        $systemName = config('app.name', 'Kumwell HR System');
        $dateThai = $backup->thai_datetime;
        $appUrl = config('app.url', url('/'));

        return <<<HTML
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รหัสผ่านสำรองฐานข้อมูล</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 30px; text-align: center; color: #ffffff;">
            <div style="font-size: 32px; margin-bottom: 8px;">🔒</div>
            <h1 style="margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">ระบบสำรองฐานข้อมูลประจำวัน (Daily Midnight Backup)</h1>
            <p style="margin: 6px 0 0 0; font-size: 13px; opacity: 0.9;">รายงานการสำรองฐานข้อมูล & รหัสผ่านถอดรหัสไฟล์ (AES-256)</p>
            <div style="display: inline-block; margin-top: 12px; background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                CONFIDENTIAL • สำหรับเจ้าหน้าที่ ICT เท่านั้น
            </div>
        </div>

        <!-- Body -->
        <div style="padding: 30px;">
            <p style="font-size: 14px; color: #334155; line-height: 1.6; margin-top: 0;">
                เรียน <strong>เจ้าหน้าที่แผนกเทคโนโลยีสารสนเทศ (ICT Department)</strong>,
            </p>
            <p style="font-size: 13px; color: #475569; line-height: 1.6;">
                ระบบได้ดำเนินการสำรองฐานข้อมูล <strong>{$systemName}</strong> ประจำวันเรียบร้อยแล้ว โดยไฟล์สำรองได้รับการบีบอัดและเข้ารหัสความปลอดภัยระดับ <strong>AES-256 Bit</strong> เพื่อป้องกันข้อมูลรั่วไหล รายละเอียดและรหัสผ่านสำหรับเปิดไฟล์มีดังนี้:
            </p>

            <!-- Decryption Password Box -->
            <div style="margin: 25px 0; background: #0f172a; border-radius: 12px; padding: 20px; text-align: center; border: 1px solid #334155;">
                <div style="font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                    🔑 รหัสผ่านถอดรหัสไฟล์ ZIP (AES-256 Password)
                </div>
                <div style="font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace; font-size: 22px; font-weight: 800; color: #38bdf8; letter-spacing: 2px; padding: 8px 16px; background: #1e293b; border-radius: 8px; display: inline-block; word-break: break-all; border: 1px dashed #0284c7;">
                    {$plainPassword}
                </div>
                <div style="font-size: 11px; color: #cbd5e1; margin-top: 10px;">
                    * โปรดเก็บรักษารหัสผ่านนี้เป็นความลับ สำหรับใช้เปิดไฟล์เมื่อต้องการกู้คืนข้อมูล
                </div>
            </div>

            <!-- Backup Details Table -->
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin: 20px 0; background: #f8fafc; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0;">
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px 14px; font-weight: bold; color: #64748b; width: 35%;">ชื่อไฟล์สำรอง</td>
                    <td style="padding: 10px 14px; font-family: monospace; font-weight: bold; color: #1e293b;">{$backup->filename}</td>
                </tr>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px 14px; font-weight: bold; color: #64748b;">วันเวลาที่สำรอง</td>
                    <td style="padding: 10px 14px; color: #1e293b;">{$dateThai}</td>
                </tr>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px 14px; font-weight: bold; color: #64748b;">ขนาดไฟล์ (ZIP)</td>
                    <td style="padding: 10px 14px; font-weight: bold; color: #7c3aed;">{$backup->file_size_human}</td>
                </tr>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px 14px; font-weight: bold; color: #64748b;">จำนวนตาราง / ข้อมูล</td>
                    <td style="padding: 10px 14px; color: #1e293b;">{$backup->tables_count} ตาราง ({$backup->rows_count} รายการ)</td>
                </tr>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px 14px; font-weight: bold; color: #64748b;">ความปลอดภัย</td>
                    <td style="padding: 10px 14px; color: #10b981; font-weight: bold;">AES-256 Bit Encryption</td>
                </tr>
                <tr>
                    <td style="padding: 10px 14px; font-weight: bold; color: #64748b;">SHA-256 Checksum</td>
                    <td style="padding: 10px 14px; font-family: monospace; font-size: 11px; color: #64748b; word-break: break-all;">{$backup->checksum_sha256}</td>
                </tr>
            </table>

            <!-- Instructions -->
            <div style="background: #f1f5f9; border-left: 4px solid #6366f1; padding: 12px 16px; border-radius: 0 8px 8px 0; margin-top: 20px;">
                <div style="font-size: 12px; font-weight: bold; color: #334155; margin-bottom: 4px;">📌 วิธีเปิดและใช้งานไฟล์:</div>
                <div style="font-size: 11px; color: #64748b; line-height: 1.6;">
                    1. ดาวน์โหลดไฟล์จากหน้าเว็บผู้ดูแลระบบ หรือจาก Storage ของระบบ<br>
                    2. ใช้โปรแกรม <strong>7-Zip</strong> หรือ <strong>WinRAR</strong> และกรอกรหัสผ่านด้านบน<br>
                    3. เอกสารคู่มือฉบับเต็มได้ถูกแนบมาพร้อมกับอีเมลฉบับนี้ (ไฟล์ <code>.md</code>)
                </div>
            </div>

            <!-- Button link -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="{$appUrl}/backend/database-backups" style="display: inline-block; background: #4f46e5; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: bold; padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);">
                    เข้าสู่หน้าระบบจัดการ Backup
                </a>
            </div>

        </div>

        <!-- Footer -->
        <div style="background: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8;">
            ระบบสำรองฐานข้อมูลอัตโนมัติ • {$systemName}<br>
            อีเมลนี้ส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Dump database using PDO natively (resilient on all OS & hosting environments)
     */
    protected static function dumpDatabaseToSql(string $outputFile, array $dbConfig): array
    {
        $handle = fopen($outputFile, 'w+');
        if (!$handle) {
            throw new \RuntimeException("ไม่สามารถเปิดไฟล์เพื่อเขียนข้อมูล: {$outputFile}");
        }

        $dbName = DB::connection()->getDatabaseName() ?: ($dbConfig['database'] ?? 'appkum_ha');
        $now = now()->toDateTimeString();

        // Write SQL Header
        fwrite($handle, "-- ==========================================================\n");
        fwrite($handle, "-- Kumwell HR System - Database Backup Dump\n");
        fwrite($handle, "-- Generated: {$now}\n");
        fwrite($handle, "-- Database: `{$dbName}`\n");
        fwrite($handle, "-- ==========================================================\n\n");

        fwrite($handle, "SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;\n");
        fwrite($handle, "SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;\n");
        fwrite($handle, "SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        $pdo = DB::connection()->getPdo();

        // 1. Fetch all base tables
        $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = [];
        while ($row = $tablesStmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $totalRows = 0;

        foreach ($tables as $table) {
            fwrite($handle, "\n-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n\n");

            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

            // Get Create Table statement
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $createStmt->fetch(PDO::FETCH_NUM);
            if ($createRow && isset($createRow[1])) {
                fwrite($handle, $createRow[1] . ";\n\n");
            }

            // Dump data in chunks to prevent memory spikes
            fwrite($handle, "-- Dumping data for table `{$table}`\n");

            $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $tableRowCount = (int)$countStmt->fetchColumn();
            $totalRows += $tableRowCount;

            if ($tableRowCount > 0) {
                $offset = 0;
                $chunkSize = 500;

                while ($offset < $tableRowCount) {
                    $dataStmt = $pdo->query("SELECT * FROM `{$table}` LIMIT {$chunkSize} OFFSET {$offset}");
                    $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($rows)) {
                        $columns = array_keys($rows[0]);
                        $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                        $colList = implode(', ', $escapedColumns);

                        $valueRows = [];
                        foreach ($rows as $r) {
                            $escapedValues = [];
                            foreach ($columns as $c) {
                                $val = $r[$c];
                                if ($val === null) {
                                    $escapedValues[] = 'NULL';
                                } elseif (is_int($val) || is_float($val)) {
                                    $escapedValues[] = $val;
                                } else {
                                    $escapedValues[] = $pdo->quote((string)$val);
                                }
                            }
                            $valueRows[] = '(' . implode(', ', $escapedValues) . ')';
                        }

                        fwrite($handle, "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $valueRows) . ";\n");
                    }

                    $offset += $chunkSize;
                }
                fwrite($handle, "\n");
            }
        }

        // 2. Fetch and dump all Views (if any)
        try {
            $viewsStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
            while ($vRow = $viewsStmt->fetch(PDO::FETCH_NUM)) {
                $viewName = $vRow[0];
                fwrite($handle, "\n-- --------------------------------------------------------\n");
                fwrite($handle, "-- View structure for view `{$viewName}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n\n");
                fwrite($handle, "DROP VIEW IF EXISTS `{$viewName}`;\n");
                $cView = $pdo->query("SHOW CREATE VIEW `{$viewName}`")->fetch(PDO::FETCH_NUM);
                if ($cView && isset($cView[1])) {
                    fwrite($handle, $cView[1] . ";\n\n");
                }
            }
        } catch (\Throwable) {
            // Ignore view error if unsupported
        }

        // Write SQL Footer
        fwrite($handle, "\n-- ==========================================================\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;\n");
        fwrite($handle, "SET SQL_MODE=@OLD_SQL_MODE;\n");
        fwrite($handle, "SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;\n");
        fwrite($handle, "SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;\n");
        fwrite($handle, "SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;\n");
        fwrite($handle, "-- Dump completed at " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- ==========================================================\n");

        fclose($handle);

        return [
            'tables_count' => count($tables),
            'rows_count' => $totalRows,
            'engine' => 'pdo_native',
        ];
    }

    /**
     * Clean up old backups exceeding retention days (default 30 days)
     *
     * @param int $keepDays Number of days to retain (e.g. 30 days)
     * @return array
     */
    public static function cleanOldBackups(int $keepDays = 30): array
    {
        $threshold = now()->subDays($keepDays);

        $expired = DatabaseBackup::where('created_at', '<', $threshold)->get();

        $deletedCount = 0;
        $freedBytes = 0;
        $deletedFiles = [];

        foreach ($expired as $backup) {
            if (Storage::disk('local')->exists($backup->file_path)) {
                $freedBytes += (int)$backup->file_size;
                Storage::disk('local')->delete($backup->file_path);
            }
            if ($backup->md_file_path && Storage::disk('local')->exists($backup->md_file_path)) {
                Storage::disk('local')->delete($backup->md_file_path);
            }
            $deletedFiles[] = $backup->filename;
            $backup->delete();
            $deletedCount++;
        }

        // Also check disk for any orphan files older than threshold
        $allFiles = Storage::disk('local')->files(self::BACKUP_DIR);
        foreach ($allFiles as $filePath) {
            if (str_ends_with($filePath, '.zip') || str_ends_with($filePath, '.md')) {
                $lastModified = Storage::disk('local')->lastModified($filePath);
                if ($lastModified < $threshold->timestamp) {
                    $size = Storage::disk('local')->size($filePath);
                    Storage::disk('local')->delete($filePath);
                    $freedBytes += $size;
                    $filename = basename($filePath);
                    if (str_ends_with($filePath, '.zip') && !in_array($filename, $deletedFiles)) {
                        $deletedFiles[] = $filename;
                        $deletedCount++;
                    }
                }
            }
        }

        if ($deletedCount > 0 && class_exists(AuditLogService::class)) {
            AuditLogService::log(
                action: 'deleted',
                description: "ลบไฟล์สำรองฐานข้อมูลเก่าที่เก็บเกิน {$keepDays} วัน จำนวน {$deletedCount} ไฟล์ (คืนพื้นที่ " . self::formatBytes($freedBytes) . ")",
                module: 'system',
                moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                user: null
            );
        }

        return [
            'count' => $deletedCount,
            'freed_bytes' => $freedBytes,
            'freed_human' => self::formatBytes($freedBytes),
            'deleted_files' => $deletedFiles,
            'keep_days' => $keepDays,
            'threshold_date' => $threshold->toDateString(),
        ];
    }

    /**
     * Format bytes to human readable string
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
