<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DirectEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $applicantName;
    public $attachmentsFiles;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $content, $applicantName, $attachmentsFiles = [])
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->applicantName = $applicantName;
        $this->attachmentsFiles = $attachmentsFiles;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->from('Pirasorn.Ra@kumwell.com', 'Pirasorn Rangchan (Bigm)')
                    ->subject($this->subject)
                    ->view('emails.direct_email');

        if (!empty($this->attachmentsFiles)) {
            foreach ($this->attachmentsFiles as $file) {
                $mail->attach($file->getRealPath(), [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                ]);
            }
        }

        return $mail;
    }
}
