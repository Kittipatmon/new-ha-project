<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProbationEvaluationNotification extends Notification
{
    use Queueable;

    public $probationEvaluation;
    public $message;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($probationEvaluation, string $message, string $url)
    {
        $this->probationEvaluation = $probationEvaluation;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'probation_evaluation_id' => $this->probationEvaluation->id ?? null,
            'title' => 'ระบบแบบประเมินทดลองงาน',
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
