<?php

namespace App\Console\Commands;

use App\Models\Recruitment\RecruitmentMailLog;
use App\Models\User;
use App\Models\UserMicrosoftToken;
use App\Services\MicrosoftGraphService;
use App\Services\RecruitmentMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recruitment:test-mail {email=asdasdasd445566gz@gmail.com} {--force-smtp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email using Microsoft Graph API or SMTP fallback';

    /**
     * Execute the console command.
     */
    public function handle(MicrosoftGraphService $graphService): int
    {
        $recipient = $this->argument('email');
        $this->info("==================================================");
        $this->info("  Kumwell HR Recruitment - Mail Test Tool");
        $this->info("==================================================");
        $this->line("Recipient: {$recipient}");

        // Find user with connected Microsoft Token
        $userToken = UserMicrosoftToken::first();
        $user = $userToken ? User::find($userToken->user_id) : User::first();

        $forceSmtp = $this->option('force-smtp');
        $hasGraphToken = !$forceSmtp && $user && $graphService->getValidAccessToken($user);

        $subject = 'ทดสอบการส่งอีเมลจากระบบสรรหาบุคลากร (HR Recruitment)';
        $htmlContent = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="color: #F2704E; margin: 0;">Kumwell HR Recruitment</h2>
                    <p style="color: #64748b; font-size: 14px; margin-top: 5px;">อีเมลทดสอบการเชื่อมต่อระบบ</p>
                </div>
                <div style="background: #f8fafc; padding: 15px; border-radius: 8px; font-size: 14px; line-height: 1.6; color: #334155;">
                    <p>สวัสดีครับ,</p>
                    <p>นี่คืออีเมลทดสอบจากระบบสรรหาบุคลากร <strong>Kumwell Corporation</strong></p>
                    <p>หากคุณได้รับอีเมลนี้ แสดงว่าระบบการส่งอีเมลทำงานได้อย่างสมบูรณ์เรียบร้อยแล้วครับ</p>
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 15px 0;">
                    <p style="font-size: 12px; color: #94a3b8; margin: 0;">
                        เวลาที่ส่ง: ' . now()->format('d/m/Y H:i:s') . ' น.
                    </p>
                </div>
            </div>
        ';

        if ($hasGraphToken) {
            $this->info("Sending via: Microsoft Graph API (Account: {$userToken->microsoft_email})");
            try {
                $graphService->sendMail(
                    $user,
                    $recipient,
                    'ผู้รับทดสอบ',
                    $subject,
                    $htmlContent
                );

                RecruitmentMailLog::create([
                    'user_id' => $user->id,
                    'recipient_email' => $recipient,
                    'recipient_name' => 'ผู้รับทดสอบ',
                    'subject' => $subject,
                    'mail_type' => 'direct_email',
                    'channel' => 'microsoft_graph',
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $this->info("SUCCESS: Email sent successfully via Microsoft Graph API to {$recipient}!");
                return 0;
            } catch (\Exception $e) {
                $this->error("ERROR via Microsoft Graph: " . $e->getMessage());

                RecruitmentMailLog::create([
                    'user_id' => $user->id,
                    'recipient_email' => $recipient,
                    'recipient_name' => 'ผู้รับทดสอบ',
                    'subject' => $subject,
                    'mail_type' => 'direct_email',
                    'channel' => 'microsoft_graph',
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                return 1;
            }
        }

        // SMTP Fallback
        $smtpUser = config('mail.mailers.smtp.username');
        $this->warn("Microsoft 365 token not found or --force-smtp specified.");
        $this->info("Attempting via SMTP (Username: {$smtpUser})...");

        if (empty($smtpUser) || str_contains($smtpUser, 'your-email')) {
            $this->error("FAILED: Both Microsoft 365 Token and SMTP credentials are not configured!");
            $this->line("1. Please connect Microsoft 365 at /backend/recruitment/mail-logs by clicking 'เชื่อมต่อ'");
            $this->line("2. OR configure real SMTP credentials (e.g. Gmail) in .env");
            return 1;
        }

        try {
            Mail::html($htmlContent, function ($message) use ($recipient, $subject) {
                $message->to($recipient)
                    ->subject($subject);
            });

            RecruitmentMailLog::create([
                'user_id' => $user?->id,
                'recipient_email' => $recipient,
                'recipient_name' => 'ผู้รับทดสอบ',
                'subject' => $subject,
                'mail_type' => 'direct_email',
                'channel' => 'smtp',
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $this->info("SUCCESS: Email sent successfully via SMTP to {$recipient}!");
            return 0;
        } catch (\Exception $e) {
            $this->error("ERROR via SMTP: " . $e->getMessage());
            return 1;
        }
    }
}
