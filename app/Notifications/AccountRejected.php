<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AccountRejected extends Notification
{
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Account Rejected',
            'message' => 'Your registration was not approved. Please contact the administrator.',
            'url'     => null,
        ];
    }
}