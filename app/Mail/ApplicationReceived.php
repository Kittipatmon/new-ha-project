<?php

namespace App\Mail;

use App\Models\Recruitment\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

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
        return $this->subject('ยืนยันการรับสมัครงาน - ' . $this->application->jobPost->title)
                    ->view('emails.application_received');
    }
}
