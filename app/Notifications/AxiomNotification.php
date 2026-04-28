<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AxiomNotification extends Notification
{
    public function __construct(
        public string   $message,
        public string   $type     = 'info',    // info | success | warning | alert
        public ?string  $link     = null,       // optional URL to redirect on click
        public ?int     $borrowId = null,       // set for due-date reminders
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $data = [
            'message' => $this->message,
            'type'    => $this->type,
            'link'    => $this->link,
        ];

        if ($this->borrowId !== null) {
            $data['borrow_id'] = $this->borrowId;
        }

        return $data;
    }
}