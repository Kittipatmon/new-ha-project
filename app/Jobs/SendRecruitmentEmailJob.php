<?php

namespace App\Jobs;

use App\Models\Recruitment\RecruitmentMailLog;
use App\Models\User;
use App\Services\MicrosoftGraphService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendRecruitmentEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public int $mailLogId;
    public ?int $userId;
    public string $recipientEmail;
    public ?string $recipientName;
    public string $subject;
    public string $htmlContent;
    public array $attachments;
    public string $mailType;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $mailLogId,
        ?int $userId,
        string $recipientEmail,
        ?string $recipientName,
        string $subject,
        string $htmlContent,
        array $attachments = [],
        string $mailType = 'direct_email'
    ) {
        $this->mailLogId = $mailLogId;
        $this->userId = $userId;
        $this->recipientEmail = $recipientEmail;
        $this->recipientName = $recipientName;
        $this->subject = $subject;
        $this->htmlContent = $htmlContent;
        $this->attachments = $attachments;
        $this->mailType = $mailType;
    }

    /**
     * Execute the job.
     */
    public function handle(MicrosoftGraphService $graphService): void
    {
        $log = RecruitmentMailLog::find($this->mailLogId);
        $sender = $this->userId ? User::find($this->userId) : null;

        $hasGraphToken = $sender && $graphService->getValidAccessToken($sender);

        try {
            if ($hasGraphToken) {
                // 1. Send using Microsoft Graph API (OAuth)
                Log::info("Sending email via Microsoft Graph API for user: {$sender->email} to {$this->recipientEmail}");

                $graphService->sendMail(
                    $sender,
                    $this->recipientEmail,
                    $this->recipientName,
                    $this->subject,
                    $this->htmlContent,
                    $this->attachments
                );

                if ($log) {
                    $log->update([
                        'channel' => 'microsoft_graph',
                        'status' => 'sent',
                        'sent_at' => now(),
                        'error_message' => null,
                    ]);
                }
            } else {
                // Check if SMTP credentials are placeholder/dummy or unconfigured
                $smtpUser = (string) config('mail.mailers.smtp.username', '');
                $smtpPass = (string) config('mail.mailers.smtp.password', '');
                $defaultMailer = config('mail.default', 'smtp');

                $isPlaceholderSmtp = empty($smtpUser) 
                    || empty($smtpPass) 
                    || str_contains($smtpUser, 'your-email') 
                    || str_contains($smtpPass, 'your-password')
                    || $defaultMailer === 'log';

                if ($isPlaceholderSmtp) {
                    Log::warning("Skipping email to {$this->recipientEmail}: SMTP is not configured or uses placeholder credentials in .env ({$smtpUser}).");
                    if ($log) {
                        $log->update([
                            'channel' => 'smtp',
                            'status' => 'skipped',
                            'error_message' => 'ยังไม่ได้ตั้งค่าบัญชี/รหัสผ่านอีเมลจริงใน .env (ใช้ค่าเริ่มต้น placeholder)',
                        ]);
                    }
                    return;
                }

                // 2. Fallback to Laravel standard SMTP
                Log::info("Sending email via SMTP Fallback to {$this->recipientEmail}");

                $fromAddress = config('mail.from.address', 'recruitment@kumwell.com');
                $fromName = $sender ? ($sender->fullname . ' (Kumwell Recruitment)') : config('mail.from.name');
                $replyToEmail = $sender?->email ?: $fromAddress;
                $replyToName = $sender?->fullname ?: $fromName;

                $attachmentsData = $this->attachments;

                Mail::html($this->htmlContent, function ($message) use ($fromAddress, $fromName, $replyToEmail, $replyToName, $attachmentsData) {
                    $message->to($this->recipientEmail, $this->recipientName)
                            ->from($fromAddress, $fromName)
                            ->replyTo($replyToEmail, $replyToName)
                            ->subject($this->subject);

                    foreach ($attachmentsData as $att) {
                        if (isset($att['contentBytes'])) {
                            if (!empty($att['isInline']) && !empty($att['contentId'])) {
                                $message->embedData(
                                    base64_decode($att['contentBytes']),
                                    $att['contentId'],
                                    $att['contentType'] ?? 'image/png'
                                );
                            } elseif (isset($att['name'])) {
                                $message->attachData(
                                    base64_decode($att['contentBytes']),
                                    $att['name'],
                                    ['mime' => $att['contentType'] ?? 'application/octet-stream']
                                );
                            }
                        }
                    }
                });

                if ($log) {
                    $log->update([
                        'channel' => 'smtp',
                        'status' => 'sent',
                        'sent_at' => now(),
                        'error_message' => null,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error("Failed sending email (Job Attempt {$this->attempts()}): " . $e->getMessage());

            if ($log) {
                $log->update([
                    'status' => 'failed',
                    'error_message' => "Attempt {$this->attempts()}: " . $e->getMessage(),
                ]);
            }

            // Re-throw so queue worker knows it failed and attempts retry if attempts < $tries
            throw $e;
        }
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(?Exception $exception): void
    {
        Log::error("SendRecruitmentEmailJob permanently failed for log ID {$this->mailLogId}: " . $exception?->getMessage());

        $log = RecruitmentMailLog::find($this->mailLogId);
        if ($log) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'ส่งล้มเหลว (ครบกำหนดพยายามซ้ำ 3 ครั้ง): ' . ($exception?->getMessage() ?? 'Unknown error'),
            ]);
        }
    }
}
