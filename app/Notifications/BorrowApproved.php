<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Notifications\Notification;

class BorrowApproved extends Notification
{
    public function __construct(public Borrowing $borrowing) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $lastAccessDay = $this->borrowing->due_date->copy()->subDay()->format('M d, Y');

        return [
            'title'   => 'Borrow Request Approved',
            'message' => "Your request for \"{$this->borrowing->ebook->title}\" has been approved. "
                       . "Your last day of access is {$lastAccessDay}.",
            'url'     => route('student.books.index'),
        ];
    }
}