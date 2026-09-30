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

        return view('backend.database-backups.index', compact(
            'backups',
            'totalCount',
            'totalBytes',
            'totalSizeHuman',
            'latestBackup'
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
     * Reveal password for authorized Admin (AJAX JSON) with security audit trail
     */
    public function showPassword(int $id)
    {
        $backup = DatabaseBackup::findOrFail($id);
        $password = $backup->getDecryptedPassword();

        if (!$password) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรหัสผ่านสำหรับไฟล์นี้ หรือไฟล์นี้ไม่ได้ถูกเข้ารหัส',
            ], 404);
        }

        if (class_exists(AuditLogService::class)) {
            AuditLogService::log(
                action: 'read',
                description: "ผู้ดูแลระบบเปิดดูรหัสผ่านถอดรหัสไฟล์สำรองฐานข้อมูล: {$backup->filename}",
                model: $backup,
                module: 'system',
                moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                user: Auth::user()
            );
        }

        return response()->json([
            'success' => true,
            'filename' => $backup->filename,
            'password' => $password,
            'email_sent_to' => $backup->email_sent_to ?: 'ไม่ได้ระบุ',
            'email_sent_at' => $backup->email_sent_at ? $backup->email_sent_at->format('d/m/Y H:i:s') : '-',
        ]);
    }
}
