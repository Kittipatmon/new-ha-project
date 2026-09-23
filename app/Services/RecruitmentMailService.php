<?php

namespace App\Services;

use App\Jobs\SendRecruitmentEmailJob;
use App\Models\Recruitment\RecruitmentMailLog;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;

class RecruitmentMailService
{
    /**
     * Dispatch an email via Queue with MailLog tracking
     *
     * @param string $recipientEmail
     * @param string|null $recipientName
     * @param string $subject
     * @param string $htmlContent
     * @param array $attachments
     * @param string $mailType
     * @param array $payload
     * @param User|null $sender
     * @return RecruitmentMailLog
     */
    public static function queueMail(
        string $recipientEmail,
        ?string $recipientName,
        string $subject,
        string $htmlContent,
        array $attachments = [],
        string $mailType = 'direct_email',
        array $payload = [],
        ?User $sender = null
    ): RecruitmentMailLog {
        $user = $sender ?? Auth::user();
        $userId = $user?->id;

        // Determine expected channel
        $graphService = app(MicrosoftGraphService::class);
        $hasGraphToken = $user && $graphService->getValidAccessToken($user);
        $channel = $hasGraphToken ? 'microsoft_graph' : 'smtp';

        // 1. Create Log entry with queued status
        $log = RecruitmentMailLog::create([
            'user_id' => $userId,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'subject' => $subject,
            'mail_type' => $mailType,
            'channel' => $channel,
            'status' => 'queued',
            'payload' => $payload,
        ]);

        // 2. Dispatch job to execute immediately after response (works without dedicated queue:work daemon)
        SendRecruitmentEmailJob::dispatchAfterResponse(
            $log->id,
            $userId,
            $recipientEmail,
            $recipientName,
            $subject,
            $htmlContent,
            $attachments,
            $mailType
        );

        return $log;
    }

    /**
     * Helper to render a mailable and queue it
     */
    public static function queueMailable(
        Mailable $mailable,
        string $recipientEmail,
        ?string $recipientName = null,
        string $mailType = 'direct_email',
        array $payload = [],
        ?User $sender = null
    ): RecruitmentMailLog {
        $user = $sender ?? Auth::user();

        // Render mailable HTML
        $htmlContent = $mailable->render();
        $subject = $mailable->subject ?? 'แจ้งเตือนจากระบบสรรหาบุคลากร Kumwell';

        // Extract attachments if any
        $attachments = [];
        if (property_exists($mailable, 'attachmentsFiles') && !empty($mailable->attachmentsFiles)) {
            foreach ($mailable->attachmentsFiles as $file) {
                if (is_object($file) && method_exists($file, 'getRealPath')) {
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'contentType' => $file->getClientMimeType(),
                        'contentBytes' => base64_encode(file_get_contents($file->getRealPath())),
                    ];
                }
            }
        }

        // Save rendered html_content in payload for preview and retry
        $payload['html_content'] = $htmlContent;

        return self::queueMail(
            $recipientEmail,
            $recipientName,
            $subject,
            $htmlContent,
            $attachments,
            $mailType,
            $payload,
            $user
        );
    }
}
