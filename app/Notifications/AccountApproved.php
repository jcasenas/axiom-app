<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Account Approved',
            'message' => 'Your account has been approved. You can now log in.',
            'url'     => route('student.books.index'),
        ];
    }
}