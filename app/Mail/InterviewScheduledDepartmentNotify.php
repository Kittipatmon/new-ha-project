<?php

namespace App\Mail;

use App\Models\Recruitment\Interview;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledDepartmentNotify extends Mailable
{
    use Queueable, SerializesModels;

    public Interview $interview;
    public ?User $recipient;
    public ?User $sender;
    public bool $isUpdate;

    /**
     * Create a new message instance.
     */
    public function __construct(Interview $interview, ?User $recipient = null, ?User $sender = null, bool $isUpdate = false)
    {
        $this->interview = $interview;
        $this->recipient = $recipient;
        $this->sender = $sender ?? auth()->user();
        $this->isUpdate = $isUpdate;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $fromAddress = config('mail.from.address', 'recruitment@kumwell.com');
        $fromName = !empty($this->sender?->fullname)
            ? ($this->sender->fullname . ' (Kumwell Recruitment)')
            : config('mail.from.name', 'Kumwell Recruitment Team');

        $senderEmail = (!empty($this->sender?->email) && filter_var($this->sender->email, FILTER_VALIDATE_EMAIL))
            ? $this->sender->email
            : $fromAddress;

        $senderName = !empty($this->sender?->fullname)
            ? $this->sender->fullname
            : config('mail.from.name');

        $senderPosition = $this->sender?->position ?? 'ฝ่ายทรัพยากรบุคคล (Human Resources)';

        $applicant = $this->interview->application?->applicant;
        $applicantName = $applicant ? trim($applicant->first_name . ' ' . $applicant->last_name) : 'ผู้สมัครงาน';

        $positionName = $this->interview->application?->jobPost?->position_name 
            ?? ($this->interview->application?->jobPost?->jobPosition?->position_name ?? 'Kumwell Corporation');

        $round = $this->interview->interview_round ?? 1;

        $subject = $this->isUpdate
            ? "[แจ้งเปลี่ยนแปลงเวลานัดสัมภาษณ์งาน] ตำแหน่ง {$positionName} - คุณ{$applicantName} (รอบที่ {$round})"
            : "[นัดสัมภาษณ์งาน] ตำแหน่ง {$positionName} - คุณ{$applicantName} (รอบที่ {$round})";

        $mail = $this->from($fromAddress, $fromName)
                    ->replyTo($senderEmail, $senderName)
                    ->subject($subject)
                    ->view('emails.interview_scheduled_department', [
                        'interview' => $this->interview,
                        'recipient' => $this->recipient,
                        'sender' => $this->sender,
                        'senderName' => $senderName,
                        'senderEmail' => $senderEmail,
                        'senderPosition' => $senderPosition,
                        'applicant' => $applicant,
                        'applicantName' => $applicantName,
                        'positionName' => $positionName,
                        'isUpdate' => $this->isUpdate,
                    ]);

        // แนบไฟล์ปฏิทิน .ics สำหรับเพิ่มลงในปฏิทิน Outlook / Google Calendar ของผู้สัมภาษณ์
        try {
            $icsContent = \App\Services\CalendarInviteService::generateIcs(
                $this->interview,
                $this->recipient?->email,
                $this->recipient?->fullname,
                $senderEmail,
                $senderName
            );

            if ($icsContent) {
                $mail->attachData($icsContent, 'interview-invite.ics', [
                    'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to attach .ics calendar to InterviewScheduledDepartmentNotify: ' . $e->getMessage());
        }

        return $mail;
    }
}
