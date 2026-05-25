<?php

namespace App\Mail;

use App\Models\Recruitment\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;

    /**
     * Create a new message instance.
     */
    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from('Pirasorn.Ra@kumwell.com', 'Pirasorn Rangchan (Bigm)')
                    ->subject('เชิญเข้าร่วมสัมภาษณ์งาน ตำแหน่ง ' . $this->interview->application->jobPost->position_name)
                    ->view('emails.interview_scheduled');
    }
}
