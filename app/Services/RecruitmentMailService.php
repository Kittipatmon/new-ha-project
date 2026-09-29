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
        if (!$user) {
            $tokenRecord = \App\Models\UserMicrosoftToken::latest('updated_at')->first();
            if ($tokenRecord && $tokenRecord->user_id) {
                $user = User::find($tokenRecord->user_id);
            }
        }
        $userId = $user?->id;

        // Determine expected channel
        $graphService = app(MicrosoftGraphService::class);
        $hasGraphToken = $user && $graphService->getValidAccessToken($user);
        $channel = $hasGraphToken ? 'microsoft_graph' : 'smtp';

        // Do not attach inline logos/images as separate file attachments so mail clients (Gmail/Outlook) won't show attachment chips/files
        // Attachments array only contains real user-attached documents (e.g. resumes, certificates)

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
     * Convert any embedded base64 data URIs into proper CID inline attachments
     * (Gmail and many webmail providers completely drop data:image/...;base64 URIs in email bodies)
     *
     * @param string $htmlContent
     * @return array [string $updatedHtml, array $inlineAttachments]
     */
    public static function convertBase64ImagesToInlineCid(string $htmlContent): array
    {
        $inlineAttachments = [];
        $index = 1;

        // Regex matches data:image/{type};base64,{data}
        $updatedHtml = preg_replace_callback(
            '/src=["\']data:(image\/([a-zA-Z0-9\+\-]+));base64,([A-Za-z0-9+\/=\s]+)["\']/i',
            function ($matches) use (&$inlineAttachments, &$index) {
                $mimeType = strtolower($matches[1]);
                $ext = strtolower($matches[2]);
                if ($ext === 'jpeg') $ext = 'jpg';
                if ($ext === 'svg+xml') $ext = 'svg';

                // Clean whitespace and newlines from base64 string
                $base64Data = preg_replace('/\s+/', '', $matches[3]);
                $cid = 'kumwell_inline_' . $index . '_' . substr(md5($base64Data), 0, 8);
                $name = "kumwell_logo_{$index}.{$ext}";
                $index++;

                $inlineAttachments[] = [
                    'name' => $name,
                    'contentType' => $mimeType,
                    'contentBytes' => $base64Data,
                    'contentId' => $cid,
                    'isInline' => true,
                ];

                return 'src="cid:' . $cid . '"';
            },
            $htmlContent
        );

        return [$updatedHtml, $inlineAttachments];
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

        // Determine subject (from envelope() method or property)
        $subject = null;
        if (method_exists($mailable, 'envelope')) {
            try {
                $subject = $mailable->envelope()->subject ?? null;
            } catch (\Throwable $e) {}
        }
        $subject = $subject ?: ($mailable->subject ?: 'แจ้งเตือนจากระบบสรรหาบุคลากร Kumwell');

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
