<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManpowerRequestNotification extends Notification
{
    use Queueable;

    public $manpowerRequest;
    public $message;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($manpowerRequest, string $message, string $url)
    {
        $this->manpowerRequest = $manpowerRequest;
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
            'manpower_request_id' => $this->manpowerRequest->id ?? null,
            'title' => 'ระบบคำขออัตรากำลังคน',
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
