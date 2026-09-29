<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Recruitment\Application;
use App\Models\User;
use Illuminate\Mail\Mailables\Address;

class ApplicationOffered extends Mailable
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

        $posTitle = $this->application->jobPost?->position_name ?? ($this->application->jobPost?->title ?? 'งาน');

        return new Envelope(
            subject: "แจ้งผลผ่านการคัดเลือกและยื่นข้อเสนอการจ้างงาน ตำแหน่ง {$posTitle} - Kumwell Corporation",
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

        $jobPost = $this->application->jobPost;
        $positionName = $jobPost?->position_name ?? ($jobPost?->title ?? '-');
        $departmentName = $jobPost?->department?->department_name ?? 'บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)';

        return new Content(
            view: 'emails.application_offered',
            with: [
                'application' => $this->application,
                'applicantName' => $this->application->applicant ? $this->application->applicant->full_name : 'ผู้สมัคร',
                'positionName' => $positionName,
                'departmentName' => $departmentName,
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
