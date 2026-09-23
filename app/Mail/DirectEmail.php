<?php

namespace App\Mail;

use App\Models\User;
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
    public $sender;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $content, $applicantName, $attachmentsFiles = [], ?User $sender = null)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->applicantName = $applicantName;
        $this->attachmentsFiles = $attachmentsFiles;
        $this->sender = $sender ?? auth()->user();
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

        $mail = $this->from($fromAddress, $fromName)
                    ->replyTo($senderEmail, $senderName)
                    ->subject($this->subject)
                    ->view('emails.direct_email', [
                        'subject' => $this->subject,
                        'content' => $this->content,
                        'applicantName' => $this->applicantName,
                        'sender' => $this->sender,
                        'senderName' => $senderName,
                        'senderEmail' => $senderEmail,
                        'senderPosition' => $senderPosition,
                    ]);

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
