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

        if (!Auth::user()->isHrOrAdmin() && Auth::user()->dept_id != 15) {
            abort(403, 'เฉพาะเจ้าหน้าที่ฝ่ายทรัพยากรบุคคล (HA) หรือผู้ดูแลระบบเท่านั้นที่สามารถจัดการการเชื่อมต่อ Microsoft 365 ได้');
        }

        if (!$this->graphService->isConfigured()) {
            return back()->with('error', 'ฟังก์ชันเชื่อมต่อ Microsoft 365 ต้องกำหนดค่า MICROSOFT_CLIENT_ID และ MICROSOFT_CLIENT_SECRET ในไฟล์ .env ก่อน (ทั้งนี้ระบบสามารถส่งอีเมลผ่าน Gmail / SMTP ได้ทันทีโดยไม่ต้องเชื่อมต่อ Microsoft ครับ)');
        }

        // Store current return URL in session
        session(['microsoft_auth_return_url' => url()->previous()]);

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

        if ($request->has('error')) {
            Log::warning('Microsoft OAuth Callback Error: ' . $request->get('error_description', $request->get('error')));
            return redirect(session('microsoft_auth_return_url', route('backend.recruitment.applications.index')))
                ->with('error', 'การเชื่อมต่อ Microsoft 365 ถูกยกเลิก: ' . $request->get('error_description', $request->get('error')));
        }

        $code = $request->get('code');

        if (!$code) {
            return redirect(session('microsoft_auth_return_url', route('backend.recruitment.applications.index')))
                ->with('error', 'ไม่พบ Authorization Code จาก Microsoft');
        }

        try {
            $result = $this->graphService->handleCallback($code);
            $user = Auth::user();

            $tokenRecord = $this->graphService->saveUserToken($user, $result['token'], $result['profile']);

            $email = $tokenRecord->microsoft_email ?: $user->email;

            $returnUrl = session()->pull('microsoft_auth_return_url', route('backend.recruitment.applications.index'));

            return redirect($returnUrl)->with('success', "เชื่อมต่อบัญชี Microsoft 365 สำเร็จแล้ว ({$email})");
        } catch (Exception $e) {
            Log::error('Microsoft Auth Callback Exception: ' . $e->getMessage());

            $returnUrl = session()->pull('microsoft_auth_return_url', route('backend.recruitment.applications.index'));

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
