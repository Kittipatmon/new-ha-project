<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Recruitment\Application;
use App\Models\User;
use Illuminate\Mail\Mailables\Address;

class ApplicationHired extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $sender;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, ?User $sender = null)
    {
        $this->application = $application;
        $this->sender = $sender ?? auth()->user();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
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

        return new Envelope(
            subject: 'แจ้งผลการพิจารณารับเข้าทำงาน - Kumwell Corporation',
            from: new Address($fromAddress, $fromName),
            replyTo: [
                new Address($senderEmail, $senderName),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $senderName = !empty($this->sender?->fullname)
            ? $this->sender->fullname
            : config('mail.from.name');

        $senderEmail = (!empty($this->sender?->email) && filter_var($this->sender->email, FILTER_VALIDATE_EMAIL))
            ? $this->sender->email
            : config('mail.from.address');

        $senderPosition = $this->sender?->position ?? 'ฝ่ายทรัพยากรบุคคล (Human Resources)';

        return new Content(
            view: 'emails.application_hired',
            with: [
                'applicantName' => $this->application->applicant ? $this->application->applicant->full_name : 'ผู้สมัคร',
                'positionName' => $this->application->jobPost->position_name ?? ($this->application->jobPost->jobPosition->position_name ?? '-'),
                'sender' => $this->sender,
                'senderName' => $senderName,
                'senderEmail' => $senderEmail,
                'senderPosition' => $senderPosition,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
