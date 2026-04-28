<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Notifications\Notification;

class NewBorrowRequest extends Notification
{
    public function __construct(public Borrowing $borrowing) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'New Borrow Request',
            'message' => "{$this->borrowing->user->full_name} requested \"{$this->borrowing->ebook->title}\".",
            'url'     => route('librarian.borrows.index'),
        ];
    }
}