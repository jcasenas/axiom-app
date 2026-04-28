<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AccountDeactivated extends Notification
{
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Account Deactivated',
            'message' => 'Your account has been deactivated. Please contact the administrator.',
            'url'     => null,
        ];
    }
}