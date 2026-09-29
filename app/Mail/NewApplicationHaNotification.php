<?php

namespace App\Mail;

use App\Models\Recruitment\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewApplicationHaNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $fromAddress = config('mail.from.address', 'recruitment@kumwell.com');
        $fromName = config('mail.from.name', 'Kumwell Recruitment System');

        $applicant = $this->application->applicant;
        $applicantName = $applicant 
            ? trim(($applicant->prefix ?? '') . ' ' . $applicant->first_name . ' ' . $applicant->last_name) 
            : 'ผู้สมัคร';

        $post = $this->application->jobPost;
        $positionName = $post->position_name 
            ?? ($post->jobPosition?->position_name ?? ($post->title ?? 'ตำแหน่งงาน'));

        $subject = "[แจ้งเตือนผู้สมัครใหม่] ตำแหน่ง {$positionName} - คุณ{$applicantName} ({$this->application->application_no})";

        return $this->from($fromAddress, $fromName)
                    ->subject($subject)
                    ->view('emails.new_application_ha_notification');
    }
}
