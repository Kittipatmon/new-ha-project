<?php

namespace App\Mail;

use App\Models\Recruitment\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCandidateDeptReviewNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public ?User $departmentHead;
    public ?User $sender;
    public ?string $note;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, ?User $departmentHead = null, ?User $sender = null, ?string $note = null)
    {
        $this->application = $application;
        $this->departmentHead = $departmentHead;
        $this->sender = $sender ?? auth()->user();
        $this->note = $note;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $fromAddress = config('mail.from.address', 'recruitment@kumwell.com');
        $fromName = !empty($this->sender?->fullname)
            ? ($this->sender->fullname . ' (Kumwell HA Recruitment)')
            : config('mail.from.name', 'Kumwell Recruitment Team');

        $applicant = $this->application->applicant;
        $applicantName = $applicant 
            ? trim(($applicant->prefix ?? '') . ' ' . $applicant->first_name . ' ' . $applicant->last_name) 
            : 'ผู้สมัคร';

        $post = $this->application->jobPost;
        $positionName = $post->position_name 
            ?? ($post->jobPosition?->position_name ?? ($post->title ?? 'ตำแหน่งงาน'));

        $subject = "[รอหัวหน้าแผนกพิจารณา] ตำแหน่ง {$positionName} - คุณ{$applicantName} ({$this->application->application_no})";

        return $this->from($fromAddress, $fromName)
                    ->subject($subject)
                    ->view('emails.new_candidate_dept_review', [
                        'application' => $this->application,
                        'departmentHead' => $this->departmentHead,
                        'sender' => $this->sender,
                        'note' => $this->note,
                    ]);
    }
}
