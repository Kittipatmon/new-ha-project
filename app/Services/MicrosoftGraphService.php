<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMicrosoftToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class MicrosoftGraphService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $tenantId;
    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = (string) config('services.microsoft.client_id', env('MICROSOFT_CLIENT_ID', ''));
        $this->clientSecret = (string) config('services.microsoft.client_secret', env('MICROSOFT_CLIENT_SECRET', ''));
        $this->tenantId = (string) config('services.microsoft.tenant_id', env('MICROSOFT_TENANT_ID', 'common'));
        $this->redirectUri = (string) config('services.microsoft.redirect_uri', url('/auth/microsoft/callback'));
    }

    /**
     * Check if Azure App credentials are configured in .env
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Generate Microsoft OAuth 2.0 Authorization URL
     */
    public function getAuthorizationUrl(?string $state = null): string
    {
        $tenant = $this->tenantId ?: 'common';
        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => 'openid profile email offline_access https://graph.microsoft.com/Mail.Send https://graph.microsoft.com/User.Read',
            'state' => $state ?? csrf_token(),
            'prompt' => 'select_account',
        ];

        return "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/authorize?" . http_build_query($params);
    }

    /**
     * Exchange authorization code for access and refresh tokens
     */
    public function handleCallback(string $code): array
    {
        $tenant = $this->tenantId ?: 'common';
        $tokenUrl = "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token";

        $response = Http::asForm()->post($tokenUrl, [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (!$response->successful()) {
            Log::error('Microsoft OAuth Token Exchange Failed: ' . $response->body());
            throw new Exception('ไม่สามารถแลกเปลี่ยน Token จาก Microsoft ได้: ' . ($response->json('error_description') ?? $response->body()));
        }

        $tokenData = $response->json();

        // Get user profile from Microsoft Graph
        $profileResponse = Http::withToken($tokenData['access_token'])
            ->get('https://graph.microsoft.com/v1.0/me?$select=id,displayName,mail,userPrincipalName,jobTitle,department,officeLocation');

        $profile = $profileResponse->successful() ? $profileResponse->json() : null;

        return [
            'token' => $tokenData,
            'profile' => $profile,
        ];
    }

    /**
     * Save or update Microsoft tokens for a specific user
     */
    public function saveUserToken(User $user, array $tokenData, ?array $profile = null): UserMicrosoftToken
    {
        $expiresIn = (int) ($tokenData['expires_in'] ?? 3600);
        $expiresAt = Carbon::now()->addSeconds($expiresIn);

        $microsoftEmail = $profile['mail'] ?? ($profile['userPrincipalName'] ?? $user->email);
        $microsoftName = $profile['displayName'] ?? $user->fullname;

        return UserMicrosoftToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'microsoft_email' => $microsoftEmail,
                'microsoft_name' => $microsoftName,
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_at' => $expiresAt,
            ]
        );
    }

    /**
     * Retrieve a valid access token for the given user, refreshing automatically if expired
     */
    public function getValidAccessToken(User $user): ?string
    {
        $tokenRecord = $user->microsoftToken;

        if (!$tokenRecord || empty($tokenRecord->access_token)) {
            return null;
        }

        // If not expired, return current access token
        if (!$tokenRecord->isExpired()) {
            return $tokenRecord->access_token;
        }

        // Need refresh token to renew
        if (empty($tokenRecord->refresh_token)) {
            Log::warning("User ID {$user->id} has expired Microsoft token with no refresh token.");
            return null;
        }

        return $this->refreshToken($tokenRecord);
    }

    /**
     * Refresh an expired access token using the refresh token
     */
    protected function refreshToken(UserMicrosoftToken $tokenRecord): ?string
    {
        $tenant = $this->tenantId ?: 'common';
        $tokenUrl = "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token";

        try {
            $response = Http::asForm()->post($tokenUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $tokenRecord->refresh_token,
            ]);

            if (!$response->successful()) {
                Log::error("Failed to refresh Microsoft token for user {$tokenRecord->user_id}: " . $response->body());
                return null;
            }

            $data = $response->json();
            $expiresIn = (int) ($data['expires_in'] ?? 3600);

            $tokenRecord->access_token = $data['access_token'];
            if (!empty($data['refresh_token'])) {
                $tokenRecord->refresh_token = $data['refresh_token'];
            }
            $tokenRecord->expires_at = Carbon::now()->addSeconds($expiresIn);
            $tokenRecord->save();

            return $tokenRecord->access_token;
        } catch (Exception $e) {
            Log::error("Exception while refreshing Microsoft token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send email via Microsoft Graph API (/v1.0/me/sendMail)
     *
     * @param User $sender The logged in user whose Microsoft account will send the email
     * @param string $recipientEmail
     * @param string|null $recipientName
     * @param string $subject
     * @param string $htmlBody
     * @param array $attachments Array of attachment items: [['name' => '...', 'contentType' => '...', 'contentBytes' => base64_string]]
     * @return bool
     * @throws Exception
     */
    public function sendMail(
        User $sender,
        string $recipientEmail,
        ?string $recipientName,
        string $subject,
        string $htmlBody,
        array $attachments = []
    ): bool {
        $accessToken = $this->getValidAccessToken($sender);

        if (!$accessToken) {
            throw new Exception("ไม่พบ Token การเข้าถึงของ Microsoft 365 สำหรับผู้ใช้ {$sender->fullname} หรือ Token หมดอายุแล้ว กรุณาเชื่อมต่อบัญชี Microsoft อีกครั้ง");
        }

        $formattedAttachments = [];
        foreach ($attachments as $att) {
            if (isset($att['contentBytes'])) {
                $item = [
                    '@odata.type' => '#microsoft.graph.fileAttachment',
                    'name' => $att['name'],
                    'contentType' => $att['contentType'] ?? 'application/octet-stream',
                    'contentBytes' => $att['contentBytes'],
                ];

                if (!empty($att['isInline'])) {
                    $item['isInline'] = true;
                }
                if (!empty($att['contentId'])) {
                    $item['contentId'] = $att['contentId'];
                }

                $formattedAttachments[] = $item;
            }
        }

        $messagePayload = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $htmlBody,
                ],
                'toRecipients' => [
                    [
                        'emailAddress' => [
                            'address' => $recipientEmail,
                            'name' => $recipientName ?: $recipientEmail,
                        ]
                    ]
                ],
            ],
            'saveToSentItems' => true,
        ];

        if (!empty($formattedAttachments)) {
            $messagePayload['message']['attachments'] = $formattedAttachments;
        }

        $response = Http::withToken($accessToken)
            ->timeout(20)
            ->post('https://graph.microsoft.com/v1.0/me/sendMail', $messagePayload);

        if (!$response->successful()) {
            Log::error("Microsoft Graph sendMail error for user {$sender->id}: " . $response->body());
            throw new Exception("Microsoft Graph API Error ({$response->status()}): " . ($response->json('error.message') ?? $response->body()));
        }

        return true;
    }

    /**
     * Disconnect user's Microsoft account
     */
    public function disconnect(User $user): bool
    {
        return (bool) $user->microsoftToken()->delete();
    }
}
