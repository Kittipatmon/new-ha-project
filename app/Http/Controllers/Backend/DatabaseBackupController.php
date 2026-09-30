<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DatabaseBackup;
use App\Services\AuditLogService;
use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupController extends Controller
{
    /**
     * Display list of database backups and system status
     */
    public function index()
    {
        $backups = DatabaseBackup::orderBy('id', 'desc')->paginate(15);
        $totalCount = DatabaseBackup::count();
        $totalBytes = (int)DatabaseBackup::sum('file_size');
        $totalSizeHuman = DatabaseBackupService::formatBytes($totalBytes);
        $latestBackup = DatabaseBackup::orderBy('id', 'desc')->first();
        $ictAuth = self::getIctVerificationStatus();

        return view('backend.database-backups.index', compact(
            'backups',
            'totalCount',
            'totalBytes',
            'totalSizeHuman',
            'latestBackup',
            'ictAuth'
        ));
    }

    /**
     * Create an on-demand database backup
     */
    public function create(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255',
        ]);

        $notes = $request->input('notes', 'สั่งสำรองฐานข้อมูลด้วยตนเองผ่านระบบจัดการ');

        try {
            $backup = DatabaseBackupService::createBackup(Auth::user(), $notes);

            $msg = "สำรองฐานข้อมูลสำเร็จเรียบร้อย! ไฟล์: {$backup->filename} (ขนาด {$backup->file_size_human}, {$backup->tables_count} ตาราง)";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'backup' => $backup,
                ]);
            }

            return redirect()->route('backend.database-backups.index')->with('success', $msg);

        } catch (\Throwable $e) {
            $errorMsg = "เกิดข้อผิดพลาดในการสำรองฐานข้อมูล: " . $e->getMessage();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 500);
            }

            return redirect()->route('backend.database-backups.index')->with('error', $errorMsg);
        }
    }

    /**
     * Download the backup ZIP file (Admin only)
     */
    public function download(int $id)
    {
        $backup = DatabaseBackup::findOrFail($id);

        if (!$backup->fileExists()) {
            return back()->with('error', 'ไม่พบไฟล์สำรองข้อมูลบน Private Storage');
        }

        // Record download event in Audit Log
        if (class_exists(AuditLogService::class)) {
            AuditLogService::log(
                action: 'exported',
                description: "ดาวน์โหลดไฟล์สำรองฐานข้อมูล: {$backup->filename} (ขนาด {$backup->file_size_human})",
                model: $backup,
                module: 'system',
                moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                user: Auth::user()
            );
        }

        return Response::download($backup->getAbsolutePath(), $backup->filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Delete a specific backup file
     */
    public function destroy(int $id)
    {
        $backup = DatabaseBackup::findOrFail($id);

        if (Storage::disk('local')->exists($backup->file_path)) {
            Storage::disk('local')->delete($backup->file_path);
        }

        $filename = $backup->filename;
        $size = $backup->file_size_human;
        $backup->delete();

        if (class_exists(AuditLogService::class)) {
            AuditLogService::log(
                action: 'deleted',
                description: "ลบไฟล์สำรองฐานข้อมูล: {$filename} (ขนาด {$size})",
                module: 'system',
                moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                user: Auth::user()
            );
        }

        return redirect()->route('backend.database-backups.index')
            ->with('success', "ลบไฟล์สำรองข้อมูล {$filename} เรียบร้อยแล้ว");
    }

    /**
     * Clean old backups exceeding retention days (30 days)
     */
    public function cleanOld(Request $request)
    {
        try {
            $result = DatabaseBackupService::cleanOldBackups(30);

            if ($result['count'] > 0) {
                $msg = "ทำความสะอาดไฟล์สำรองเก่าที่เกิน 30 วันเรียบร้อยแล้ว {$result['count']} ไฟล์ (คืนพื้นที่ {$result['freed_human']})";
            } else {
                $msg = "ไม่พบไฟล์สำรองที่เก่าเกิน 30 วัน (ไฟล์ทั้งหมดยังอยู่ในช่วงเวลาเก็บรักษา)";
            }

            return redirect()->route('backend.database-backups.index')->with('success', $msg);

        } catch (\Throwable $e) {
            return redirect()->route('backend.database-backups.index')
                ->with('error', "เกิดข้อผิดพลาดในการทำความสะอาด: " . $e->getMessage());
        }
    }

    /**
     * Download or view the companion Plain Text guide file (.txt)
     */
    public function downloadTxt(int $id)
    {
        $backup = DatabaseBackup::findOrFail($id);

        if (!$backup->md_file_path || !Storage::disk('local')->exists($backup->md_file_path)) {
            return back()->with('error', 'ไม่พบไฟล์คู่มือ .txt ในระบบจัดเก็บ');
        }

        $fileName = basename($backup->md_file_path);
        $mime = str_ends_with($fileName, '.md') ? 'text/markdown' : 'text/plain; charset=utf-8';

        return Response::download(Storage::disk('local')->path($backup->md_file_path), $fileName, [
            'Content-Type' => $mime,
        ]);
    }

    /**
     * Backward-compatible alias for downloadTxt
     */
    public function downloadMd(int $id)
    {
        return $this->downloadTxt($id);
    }

    /**
     * Get current Microsoft 365 ICT verification status from session
     */
    public static function getIctVerificationStatus(): array
    {
        $verified = session('backup_ict_verified') === true;
        $authTime = session('backup_ict_time');

        // Session timeout for security: 30 minutes (1800 seconds)
        if ($verified && $authTime && (time() - $authTime > 1800)) {
            session()->forget(['backup_ict_verified', 'backup_ict_email', 'backup_ict_name', 'backup_ict_dept', 'backup_ict_time']);
            $verified = false;
        }

        $user = Auth::user();
        $hasConnectedMicrosoft = $user && $user->hasMicrosoftConnected();
        $connectedEmail = $hasConnectedMicrosoft ? $user->microsoftToken?->microsoft_email : null;

        return [
            'is_verified' => $verified,
            'email' => session('backup_ict_email'),
            'name' => session('backup_ict_name'),
            'department' => session('backup_ict_dept'),
            'verified_at' => $authTime,
            'time_human' => $authTime ? date('d/m/Y H:i:s', $authTime) : null,
            'has_connected_microsoft' => $hasConnectedMicrosoft,
            'connected_email' => $connectedEmail,
        ];
    }

    /**
     * One-click verification using previously connected Microsoft 365 account
     */
    public function verifyConnectedMicrosoft(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasMicrosoftConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบบัญชี Microsoft 365 ที่เชื่อมต่อไว้ กรุณาเข้าสู่ระบบด้วย Microsoft 365 ใหม่',
            ], 400);
        }

        $tokenRecord = $user->microsoftToken;
        $email = $tokenRecord->microsoft_email ?: $user->email;

        $verification = DatabaseBackupService::verifyIctAccess($user, [
            'displayName' => $tokenRecord->microsoft_name ?: $user->fullname,
            'mail' => $email,
        ], $email);

        if ($verification['allowed']) {
            session([
                'backup_ict_verified' => true,
                'backup_ict_email' => $verification['email'],
                'backup_ict_name' => $verification['name'],
                'backup_ict_dept' => $verification['department'],
                'backup_ict_time' => now()->timestamp,
            ]);

            if (class_exists(AuditLogService::class)) {
                AuditLogService::log(
                    action: 'login',
                    description: "ยืนยันตัวตนบัญชี Microsoft 365 แผนก ICT ({$verification['email']}) สำหรับขอรับรหัสผ่านสำรองฐานข้อมูล",
                    module: 'system',
                    moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                    user: $user
                );
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "ยืนยันตัวตนสำเร็จ: บัญชี {$verification['email']} (แผนก ICT) ได้รับสิทธิ์ในการขอรับรหัสผ่านแล้ว",
                    'verification' => $verification,
                ]);
            }

            return back()->with('success', "ยืนยันตัวตนสำเร็จ: บัญชี {$verification['email']} (แผนก ICT) ได้รับสิทธิ์ในการขอรับรหัสผ่านแล้ว");
        }

        session()->forget(['backup_ict_verified', 'backup_ict_email', 'backup_ict_name', 'backup_ict_dept', 'backup_ict_time']);

        if (class_exists(AuditLogService::class)) {
            AuditLogService::log(
                action: 'warning',
                description: "ปฏิเสธการเข้าถึงรหัสผ่าน: บัญชี Microsoft ({$verification['email']}) ไม่ได้สังกัดแผนก ICT",
                module: 'system',
                moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                user: $user
            );
        }

        $errMsg = "เข้าถึงไม่ได้ (Access Denied): บัญชี Microsoft ({$verification['email']}) ไม่ได้สังกัดแผนก ICT คุณจึงไม่มีสิทธิ์ขอรับรหัสผ่าน";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errMsg,
            ], 403);
        }

        return back()->with('error', $errMsg);
    }

    /**
     * Revoke ICT authentication session
     */
    public function revokeIctAuth(Request $request)
    {
        session()->forget(['backup_ict_verified', 'backup_ict_email', 'backup_ict_name', 'backup_ict_dept', 'backup_ict_time']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกการยืนยันตัวตน Microsoft 365 เรียบร้อยแล้ว',
            ]);
        }

        return back()->with('success', 'ยกเลิกการยืนยันตัวตน Microsoft 365 เรียบร้อยแล้ว');
    }

    /**
     * Reveal password for authorized ICT personnel (AJAX JSON) with security audit trail
     */
    public function showPassword(Request $request, int $id)
    {
        $ictAuth = self::getIctVerificationStatus();

        if (!$ictAuth['is_verified']) {
            if (class_exists(AuditLogService::class)) {
                AuditLogService::log(
                    action: 'security_alert',
                    description: "พยายามเข้าถึงรหัสผ่านสำรองฐานข้อมูลโดยไม่ผ่านการยืนยันตัวตน Microsoft 365 แผนก ICT (ผู้ใช้งาน: " . (Auth::user()?->fullname ?? 'Unknown') . ")",
                    module: 'system',
                    moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                    user: Auth::user()
                );
            }

            return response()->json([
                'success' => false,
                'requires_auth' => true,
                'message' => 'เข้าถึงไม่ได้: คุณต้องเข้าสู่ระบบผ่าน Microsoft 365 และได้รับการตรวจสอบว่าเป็นเจ้าหน้าที่แผนก ICT ก่อน จึงจะสามารถขอรับรหัสผ่านได้',
            ], 403);
        }

        $backup = DatabaseBackup::findOrFail($id);
        $password = $backup->getDecryptedPassword();

        if (!$password) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรหัสผ่านสำหรับไฟล์นี้ หรือไฟล์นี้ไม่ได้ถูกเข้ารหัส',
            ], 404);
        }

        $recipientEmail = $ictAuth['email'];
        $recipientName = $ictAuth['name'] ?: 'เจ้าหน้าที่ ICT';

        // Dispatch password directly to verified Microsoft email
        $mailResult = DatabaseBackupService::sendPasswordToRequesterEmail(
            $backup,
            $password,
            $recipientEmail,
            $recipientName,
            Auth::user()
        );

        if (class_exists(AuditLogService::class)) {
            AuditLogService::log(
                action: 'export',
                description: "เจ้าหน้าที่แผนก ICT ({$recipientName}, {$recipientEmail}) ขอรับรหัสผ่านถอดรหัสไฟล์: {$backup->filename} -> ระบบจัดส่งรหัสผ่านไปยังอีเมล {$recipientEmail} เรียบร้อยแล้ว",
                model: $backup,
                module: 'system',
                moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                user: Auth::user()
            );
        }

        return response()->json([
            'success' => true,
            'email_sent' => $mailResult['success'],
            'sent_via' => $mailResult['sent_via'] ?? 'smtp',
            'filename' => $backup->filename,
            'password' => $password,
            'recipient' => [
                'name' => $recipientName,
                'email' => $recipientEmail,
                'department' => $ictAuth['department'],
                'sent_at' => now()->format('d/m/Y H:i:s'),
            ],
            'message' => "ระบบได้จัดส่งรหัสผ่านถอดรหัสไฟล์ (AES-256) ไปยังอีเมล {$recipientEmail} เรียบร้อยแล้ว กรุณาตรวจสอบกล่องข้อความ (Inbox) ของคุณ",
        ]);
    }
}
