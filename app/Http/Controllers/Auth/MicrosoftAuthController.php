<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\MicrosoftGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class MicrosoftAuthController extends Controller
{
    protected MicrosoftGraphService $graphService;

    public function __construct(MicrosoftGraphService $graphService)
    {
        $this->graphService = $graphService;
    }

    /**
     * Redirect the user to Microsoft's OAuth 2.0 authorization page.
     */
    public function redirect(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการเชื่อมต่อ Microsoft 365');
        }

        $purpose = $request->query('purpose');

        if ($purpose === 'backup_password') {
            // Allow user to authenticate via Microsoft for database backup recovery key verification
            session(['microsoft_auth_purpose' => 'backup_password']);
            session(['microsoft_auth_return_url' => route('backend.database-backups.index')]);
        } else {
            if (!Auth::user()->isHrOrAdmin() && Auth::user()->dept_id != 15 && !Auth::user()->isIctDepartment()) {
                abort(403, 'เฉพาะเจ้าหน้าที่ฝ่ายทรัพยากรบุคคล (HA), แผนก ICT หรือผู้ดูแลระบบเท่านั้นที่สามารถจัดการการเชื่อมต่อ Microsoft 365 ได้');
            }
            session(['microsoft_auth_return_url' => url()->previous()]);
        }

        if (!$this->graphService->isConfigured()) {
            return back()->with('error', 'ฟังก์ชันเชื่อมต่อ Microsoft 365 ต้องกำหนดค่า MICROSOFT_CLIENT_ID และ MICROSOFT_CLIENT_SECRET ในไฟล์ .env ก่อน');
        }

        $authUrl = $this->graphService->getAuthorizationUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Handle the OAuth 2.0 callback from Microsoft.
     */
    public function callback(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการเชื่อมต่อ Microsoft 365');
        }

        $purpose = session()->pull('microsoft_auth_purpose');
        $defaultReturn = ($purpose === 'backup_password')
            ? route('backend.database-backups.index')
            : route('backend.recruitment.applications.index');
        $returnUrl = session()->pull('microsoft_auth_return_url', $defaultReturn);

        if ($request->has('error')) {
            Log::warning('Microsoft OAuth Callback Error: ' . $request->get('error_description', $request->get('error')));
            return redirect($returnUrl)
                ->with('error', 'การเชื่อมต่อ Microsoft 365 ถูกยกเลิก: ' . $request->get('error_description', $request->get('error')));
        }

        $code = $request->get('code');

        if (!$code) {
            return redirect($returnUrl)
                ->with('error', 'ไม่พบ Authorization Code จาก Microsoft');
        }

        try {
            $result = $this->graphService->handleCallback($code);
            $user = Auth::user();

            $tokenRecord = $this->graphService->saveUserToken($user, $result['token'], $result['profile']);
            $email = $tokenRecord->microsoft_email ?: $user->email;

            // Handle purpose: backup_password (ICT department verification)
            if ($purpose === 'backup_password') {
                $verification = \App\Services\DatabaseBackupService::verifyIctAccess(
                    $user,
                    $result['profile'] ?? [],
                    $email
                );

                if ($verification['allowed']) {
                    session([
                        'backup_ict_verified' => true,
                        'backup_ict_email' => $verification['email'],
                        'backup_ict_name' => $verification['name'],
                        'backup_ict_dept' => $verification['department'],
                        'backup_ict_time' => now()->timestamp,
                    ]);

                    if (class_exists(\App\Services\AuditLogService::class)) {
                        \App\Services\AuditLogService::log(
                            action: 'login',
                            description: "ยืนยันตัวตน Microsoft 365 แผนก ICT สำเร็จ ({$verification['email']}) สำหรับขอรับรหัสผ่านสำรองฐานข้อมูล",
                            module: 'system',
                            moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                            user: $user
                        );
                    }

                    return redirect($returnUrl)->with('success', "ยืนยันตัวตน Microsoft 365 สำเร็จ: บัญชี {$verification['email']} (สังกัดแผนก ICT) ได้รับสิทธิ์ในการขอรับรหัสผ่านสำรองฐานข้อมูล");
                } else {
                    session()->forget(['backup_ict_verified', 'backup_ict_email', 'backup_ict_name', 'backup_ict_dept', 'backup_ict_time']);

                    if (class_exists(\App\Services\AuditLogService::class)) {
                        \App\Services\AuditLogService::log(
                            action: 'warning',
                            description: "ปฏิเสธการเข้าถึงรหัสผ่านสำรองฐานข้อมูล: บัญชี Microsoft ({$verification['email']}) ไม่ได้สังกัดแผนก ICT ({$verification['reason']})",
                            module: 'system',
                            moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                            user: $user
                        );
                    }

                    return redirect($returnUrl)->with('error', "เข้าถึงไม่ได้ (Access Denied): บัญชี Microsoft ({$verification['email']}) ไม่ได้สังกัดแผนก ICT (Information Communication Technology) คุณจึงไม่มีสิทธิ์เข้าถึงหรือขอรหัสผ่านสำรองฐานข้อมูล");
                }
            }

            return redirect($returnUrl)->with('success', "เชื่อมต่อบัญชี Microsoft 365 สำเร็จแล้ว ({$email})");
        } catch (Exception $e) {
            Log::error('Microsoft Auth Callback Exception: ' . $e->getMessage());

            return redirect($returnUrl)->with('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ Microsoft: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect the user's Microsoft 365 account.
     */
    public function disconnect(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isHrOrAdmin() && Auth::user()->dept_id != 15) {
            abort(403, 'เฉพาะเจ้าหน้าที่ฝ่ายทรัพยากรบุคคล (HA) หรือผู้ดูแลระบบเท่านั้นที่สามารถจัดการการเชื่อมต่อ Microsoft 365 ได้');
        }

        $this->graphService->disconnect(Auth::user());

        return back()->with('success', 'ยกเลิกการเชื่อมต่อบัญชี Microsoft 365 เรียบร้อยแล้ว');
    }
}
