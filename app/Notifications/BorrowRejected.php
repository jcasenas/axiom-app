<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Notifications\Notification;

class BorrowRejected extends Notification
{
    public function __construct(public Borrowing $borrowing) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Borrow Request Rejected',
            'message' => "Your request for \"{$this->borrowing->ebook->title}\" was not approved.",
            'url'     => route('student.books.index'),
        ];
    }
}