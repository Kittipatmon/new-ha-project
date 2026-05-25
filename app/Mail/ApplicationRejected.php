<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Recruitment\Application;

class ApplicationRejected extends Mailable
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
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'แจ้งผลการพิจารณาใบสมัครงาน - Kumwell Corporation',
            from: new \Illuminate\Mail\Mailables\Address('Pirasorn.Ra@kumwell.com', 'Pirasorn Rangchan (Bigm)'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application_rejected',
            with: [
                'applicantName' => $this->application->applicant ? $this->application->applicant->full_name : 'ผู้สมัคร',
                'positionName' => $this->application->jobPost->position_name ?? ($this->application->jobPost->jobPosition->position_name ?? '-'),
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
