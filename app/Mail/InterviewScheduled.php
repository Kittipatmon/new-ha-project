<?php

namespace App\Mail;

use App\Models\Recruitment\Interview;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $sender;
    public bool $isUpdate;

    /**
     * Create a new message instance.
     */
    public function __construct(Interview $interview, ?User $sender = null, bool $isUpdate = false)
    {
        $this->interview = $interview;
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

        $positionName = $this->interview->application->jobPost->position_name 
            ?? ($this->interview->application->jobPost->jobPosition->position_name ?? 'Kumwell Corporation');

        $round = $this->interview->interview_round ?? 1;
        $subject = $this->isUpdate
            ? "[แจ้งเปลี่ยนแปลงวันเวลานัดสัมภาษณ์งาน] ตำแหน่ง {$positionName} (รอบที่ {$round})"
            : "เชิญเข้าร่วมสัมภาษณ์งาน ตำแหน่ง {$positionName}";

        return $this->from($fromAddress, $fromName)
                    ->replyTo($senderEmail, $senderName)
                    ->subject($subject)
                    ->view('emails.interview_scheduled', [
                        'interview' => $this->interview,
                        'sender' => $this->sender,
                        'senderName' => $senderName,
                        'senderEmail' => $senderEmail,
                        'senderPosition' => $senderPosition,
                        'isUpdate' => $this->isUpdate,
                    ]);
    }
}
