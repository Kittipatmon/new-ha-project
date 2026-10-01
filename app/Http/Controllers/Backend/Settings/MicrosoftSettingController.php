<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\UserMicrosoftToken;
use App\Services\MicrosoftGraphService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftSettingController extends Controller
{
    protected MicrosoftGraphService $graphService;

    public function __construct(MicrosoftGraphService $graphService)
    {
        $this->graphService = $graphService;
    }

    /**
     * Display Microsoft 365 / Azure Entra ID settings page
     */
    public function index()
    {
        if (!Auth::check() || (!Auth::user()->isHrOrAdmin() && !Auth::user()->canManageUsers())) {
            abort(403, 'เฉพาะผู้ดูแลระบบหรือเจ้าหน้าที่ฝ่าย HR เท่านั้นที่สามารถเข้าถึงการตั้งค่านี้ได้');
        }

        $clientId = (string) config('services.microsoft.client_id', env('MICROSOFT_CLIENT_ID', ''));
        $clientSecret = (string) config('services.microsoft.client_secret', env('MICROSOFT_CLIENT_SECRET', ''));
        $tenantId = (string) config('services.microsoft.tenant_id', env('MICROSOFT_TENANT_ID', 'common'));
        $redirectUri = (string) config('services.microsoft.redirect_uri', env('MICROSOFT_REDIRECT_URI', url('/auth/microsoft/callback')));
        $secretExpiresAt = config('services.microsoft.secret_expires_at', env('MICROSOFT_SECRET_EXPIRES_AT', ''));

        // Recommended redirect URI based on current APP_URL
        $recommendedRedirectUri = url('/auth/microsoft/callback');
        if (str_contains($recommendedRedirectUri, 'ha.appkumwell.com') || str_starts_with(config('app.url'), 'https://')) {
            $recommendedRedirectUri = preg_replace('/^http:/i', 'https:', $recommendedRedirectUri);
            $redirectUri = preg_replace('/^http:/i', 'https:', $redirectUri);
        }

        // Expiration status
        $expiryDate = null;
        $daysRemaining = null;
        $expiryStatus = 'none'; // 'none', 'blue', 'yellow', 'orange', 'red'

        if (!empty($secretExpiresAt)) {
            try {
                $expiryDate = Carbon::parse($secretExpiresAt);
                $daysRemaining = (int) now()->startOfDay()->diffInDays($expiryDate->startOfDay(), false);

                if ($daysRemaining <= 0) {
                    $expiryStatus = 'red'; // หมดอายุแล้ว หรือเหลือ 0 วัน (สีแดง)
                } elseif ($daysRemaining <= 30) {
                    $expiryStatus = 'orange'; // เหลือน้อยมาก 1-30 วัน (สีส้ม)
                } elseif ($daysRemaining <= 60) {
                    $expiryStatus = 'yellow'; // เริ่มเหลือน้อย 31-60 วัน (สีเหลือง)
                } else {
                    $expiryStatus = 'blue'; // เวลาเหลือเยอะ > 60 วัน (สีฟ้า)
                }
            } catch (Exception $e) {
                // Ignore parse errors
            }
        }

        $isConfigured = !empty($clientId) && !empty($clientSecret);
        $connectedUsersCount = UserMicrosoftToken::whereNotNull('access_token')->count();
        $connectedUsers = UserMicrosoftToken::with('user')
            ->whereNotNull('access_token')
            ->latest('updated_at')
            ->take(10)
            ->get();

        $hasSecret = !empty($clientSecret);
        $maskedSecret = $hasSecret ? ('••••••••••••••••' . (strlen($clientSecret) > 4 ? substr($clientSecret, -4) : '')) : '';
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        $clientSecretForView = $isAdmin ? $clientSecret : '';

        // Load audit logs history for Microsoft 365 settings & renewals
        $historyLogs = class_exists(\App\Models\SystemAuditLog::class)
            ? \App\Models\SystemAuditLog::where('module', 'microsoft_setting')
                ->latest('id')
                ->take(8)
                ->get()
            : collect();

        return view('backend.settings.microsoft.index', compact(
            'clientId',
            'hasSecret',
            'maskedSecret',
            'isAdmin',
            'tenantId',
            'redirectUri',
            'recommendedRedirectUri',
            'secretExpiresAt',
            'expiryDate',
            'daysRemaining',
            'expiryStatus',
            'isConfigured',
            'connectedUsersCount',
            'connectedUsers',
            'historyLogs'
        ))->with('clientSecret', $clientSecretForView);
    }

    /**
     * Update Microsoft configuration in .env file
     */
    public function update(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->isHrOrAdmin() && !Auth::user()->canManageUsers())) {
            abort(403, 'เฉพาะผู้ดูแลระบบหรือเจ้าหน้าที่ฝ่าย HR เท่านั้นที่สามารถเข้าถึงการตั้งค่านี้ได้');
        }

        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        $existingSecret = (string) config('services.microsoft.client_secret', env('MICROSOFT_CLIENT_SECRET', ''));
        $hasExistingSecret = !empty($existingSecret);

        $validated = $request->validate([
            'client_id' => 'required|string',
            'client_secret' => ($hasExistingSecret || !$isAdmin) ? 'nullable|string' : 'required|string',
            'tenant_id' => 'required|string',
            'redirect_uri' => 'required|url',
            'secret_expires_at' => 'nullable|date',
            'change_reason' => 'required|string|max:500',
        ], [
            'client_id.required' => 'กรุณาระบุ Microsoft Application (Client) ID',
            'client_secret.required' => 'กรุณาระบุ Client Secret Value',
            'tenant_id.required' => 'กรุณาระบุ Directory (Tenant) ID',
            'redirect_uri.required' => 'กรุณาระบุ Redirect URI',
            'redirect_uri.url' => 'รูปแบบ Redirect URI ต้องเป็น URL ที่ถูกต้อง (เช่น https://example.com/auth/microsoft/callback)',
            'secret_expires_at.date' => 'รูปแบบวันที่หมดอายุไม่ถูกต้อง',
            'change_reason.required' => 'กรุณาระบุสาเหตุการบันทึกหรือต่ออายุการตั้งค่าก่อนกดบันทึก',
        ]);

        $clientId = trim($validated['client_id']);
        // Only Admin can modify client_secret
        $newSecret = ($isAdmin && !empty($validated['client_secret'])) ? trim($validated['client_secret']) : null;
        $clientSecret = $newSecret ?: $existingSecret;
        $tenantId = trim($validated['tenant_id']);
        $redirectUri = trim($validated['redirect_uri']);
        $secretExpiresAt = !empty($validated['secret_expires_at']) ? trim($validated['secret_expires_at']) : '';
        $changeReason = !empty($validated['change_reason']) ? trim($validated['change_reason']) : '';

        // Capture previous values for audit trail
        $oldClientId = (string) config('services.microsoft.client_id', env('MICROSOFT_CLIENT_ID', ''));
        $oldTenantId = (string) config('services.microsoft.tenant_id', env('MICROSOFT_TENANT_ID', ''));
        $oldRedirectUri = (string) config('services.microsoft.redirect_uri', env('MICROSOFT_REDIRECT_URI', ''));
        $oldExpiresAt = (string) config('services.microsoft.secret_expires_at', env('MICROSOFT_SECRET_EXPIRES_AT', ''));
        $oldHasSecret = !empty($existingSecret);

        try {
            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                $updates = [
                    'MICROSOFT_CLIENT_ID' => $clientId,
                    'MICROSOFT_TENANT_ID' => $tenantId,
                    'MICROSOFT_REDIRECT_URI' => $redirectUri,
                    'MICROSOFT_SECRET_EXPIRES_AT' => $secretExpiresAt,
                ];

                if ($newSecret !== null) {
                    $updates['MICROSOFT_CLIENT_SECRET'] = $newSecret;
                }

                $this->updateEnvFile($envPath, $updates);
            }

            // Update in runtime config
            config([
                'services.microsoft.client_id' => $clientId,
                'services.microsoft.client_secret' => $clientSecret,
                'services.microsoft.tenant_id' => $tenantId,
                'services.microsoft.redirect_uri' => $redirectUri,
                'services.microsoft.secret_expires_at' => $secretExpiresAt,
            ]);

            // Clear config cache safely ONLY if already cached
            if (app()->configurationIsCached()) {
                try {
                    Artisan::call('config:clear');
                } catch (Exception $e) {
                    Log::warning('Config clear error: ' . $e->getMessage());
                }
            }

            // Record audit trail for setting changes and renewals
            if (class_exists(\App\Services\AuditLogService::class)) {
                $isSecretRenewed = ($newSecret !== null);
                $isExpiryChanged = ($oldExpiresAt !== $secretExpiresAt && !empty($secretExpiresAt));

                $changedParts = [];
                if ($isSecretRenewed) {
                    $changedParts[] = 'ต่ออายุ Client Secret ชุดใหม่';
                }
                if ($isExpiryChanged) {
                    try {
                        $newDateFormatted = Carbon::parse($secretExpiresAt)->format('d/m/Y');
                        $changedParts[] = 'ปรับปรุงวันหมดอายุเป็น ' . $newDateFormatted;
                    } catch (Exception $e) {
                        $changedParts[] = 'ปรับปรุงวันหมดอายุเป็น ' . $secretExpiresAt;
                    }
                }
                if ($oldClientId !== $clientId) {
                    $changedParts[] = 'แก้ไข Client ID';
                }
                if ($oldTenantId !== $tenantId) {
                    $changedParts[] = 'แก้ไข Tenant ID';
                }
                if ($oldRedirectUri !== $redirectUri) {
                    $changedParts[] = 'แก้ไข Redirect URI';
                }

                $action = ($isSecretRenewed || $isExpiryChanged) ? 'renewed' : 'updated';
                $description = !empty($changedParts)
                    ? implode(', ', $changedParts)
                    : 'บันทึกยืนยันข้อมูลการตั้งค่า Microsoft 365';

                if (!empty($changeReason)) {
                    $description .= ' [สาเหตุ: ' . $changeReason . ']';
                }

                try {
                    \App\Services\AuditLogService::log(
                        action: $action,
                        description: $description,
                        model: null,
                        oldValues: [
                            'client_id' => $oldClientId,
                            'tenant_id' => $oldTenantId,
                            'redirect_uri' => $oldRedirectUri,
                            'secret_expires_at' => $oldExpiresAt,
                            'secret_status' => $oldHasSecret ? 'มี Secret เดิม' : 'ยังไม่ได้ระบุ',
                        ],
                        newValues: [
                            'client_id' => $clientId,
                            'tenant_id' => $tenantId,
                            'redirect_uri' => $redirectUri,
                            'secret_expires_at' => $secretExpiresAt,
                            'secret_status' => $isSecretRenewed ? 'ต่ออายุรหัสชุดใหม่เรียบร้อย' : 'ใช้รหัสเดิม',
                            'reason' => $changeReason ?: 'ไม่ได้ระบุ',
                        ],
                        module: 'microsoft_setting',
                        moduleName: 'ตั้งค่า Microsoft 365'
                    );
                } catch (Exception $logEx) {
                    Log::warning('AuditLog recording failed for Microsoft settings: ' . $logEx->getMessage());
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'บันทึกและอัปเดตการตั้งค่า Microsoft 365 เรียบร้อยแล้ว'
                ]);
            }

            return redirect()->route('backend.settings.microsoft')
                ->with('success', 'บันทึกและอัปเดตการตั้งค่า Microsoft 365 เรียบร้อยแล้ว');
        } catch (Exception $e) {
            Log::error('Microsoft setting update error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Test connection to Microsoft Azure Entra ID OpenID endpoint
     */
    public function testConnection(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->isHrOrAdmin() && !Auth::user()->canManageUsers())) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่มีสิทธิ์ดำเนินการ'
            ], 403);
        }

        $tenantId = trim($request->input('tenant_id', ''));
        if (empty($tenantId)) {
            $tenantId = config('services.microsoft.tenant_id', env('MICROSOFT_TENANT_ID', 'common'));
        }

        try {
            $discoveryUrl = "https://login.microsoftonline.com/{$tenantId}/v2.0/.well-known/openid-configuration";
            $response = Http::timeout(10)->get($discoveryUrl);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'สามารถเชื่อมต่อกับ Microsoft Azure Entra ID ได้สำเร็จ!',
                    'details' => [
                        'tenant' => $tenantId,
                        'token_endpoint' => $data['token_endpoint'] ?? null,
                        'authorization_endpoint' => $data['authorization_endpoint'] ?? null,
                        'issuer' => $data['issuer'] ?? null,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "ไม่สามารถเชื่อมต่อกับ Tenant: {$tenantId} (HTTP Status {$response->status()}) กรุณาตรวจสอบ Tenant ID อีกครั้ง",
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถเชื่อมต่อกับ Microsoft Cloud ได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disconnect/Revoke a user's Microsoft 365 token (by Admin)
     */
    public function disconnectUser(Request $request, $id)
    {
        if (!Auth::check() || (!Auth::user()->isHrOrAdmin() && !Auth::user()->canManageUsers())) {
            abort(403, 'เฉพาะผู้ดูแลระบบหรือเจ้าหน้าที่ HR เท่านั้นที่สามารถดำเนินการนี้ได้');
        }

        $tokenRecord = UserMicrosoftToken::with('user')->findOrFail($id);
        $userName = $tokenRecord->user->fullname ?? $tokenRecord->microsoft_email ?? 'ผู้ใช้งาน';
        $userEmail = $tokenRecord->microsoft_email ?? ($tokenRecord->user->email ?? null);

        $tokenRecord->delete();

        if (class_exists(\App\Services\AuditLogService::class)) {
            try {
                \App\Services\AuditLogService::log(
                    action: 'revoked',
                    description: "ยกเลิกการเชื่อมต่อบัญชี Microsoft 365 ของคุณ {$userName} ({$userEmail})",
                    model: null,
                    oldValues: [
                        'user_id' => $tokenRecord->user_id,
                        'microsoft_email' => $tokenRecord->microsoft_email,
                        'tenant_id' => $tokenRecord->tenant_id,
                    ],
                    newValues: ['status' => 'disconnected'],
                    module: 'microsoft_setting',
                    moduleName: 'ตั้งค่า Microsoft 365'
                );
            } catch (\Exception $e) {
                Log::warning('AuditLog recording failed for disconnectUser: ' . $e->getMessage());
            }
        }

        return back()->with('success', "ยกเลิกการเชื่อมต่อบัญชี Microsoft 365 ของคุณ {$userName} เรียบร้อยแล้ว");
    }

    /**
     * Helper to safely update or append keys in .env
     */
    protected function updateEnvFile(string $filePath, array $values): void
    {
        $content = File::get($filePath);

        foreach ($values as $key => $value) {
            // Check if wrapping with quotes is needed
            $needsQuotes = str_contains((string)$value, ' ') || str_contains((string)$value, '$') || str_contains((string)$value, '#') || $value === '';
            $formattedValue = $needsQuotes ? '"' . str_replace('"', '\"', (string)$value) . '"' : (string)$value;

            $pattern = "/^{$key}=.*$/m";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$formattedValue}", $content);
            } else {
                // Key does not exist, append it at the end
                $content = rtrim($content) . "\n{$key}={$formattedValue}\n";
            }
        }

        File::put($filePath, $content);
    }
}
