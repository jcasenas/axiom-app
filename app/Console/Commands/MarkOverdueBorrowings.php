<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\User;
use App\Notifications\AxiomNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class MarkOverdueBorrowings extends Command
{
    protected $signature   = 'axiom:mark-overdue';
    protected $description = 'Mark expired borrowings and notify affected users';

    public function handle(): void
    {
        $now = Carbon::now();

        $overdue = Borrowing::with(['user', 'ebook'])
            ->whereIn('status', ['active', 'due_soon'])
            ->where('access_expires_at', '<', $now)
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue borrowings found.');
            return;
        }

        $marked = 0;

        foreach ($overdue as $borrow) {
            // Mark as expired first so a double-run won't pick this up again
            $borrow->update(['status' => 'expired']);

            // Guard against available_copies exceeding total_copies on double-run
            if ($borrow->ebook) {
                $borrow->ebook->refresh(); // get current DB value before incrementing
                if ($borrow->ebook->available_copies < $borrow->ebook->total_copies) {
                    $borrow->ebook->increment('available_copies');
                }
            }

            // Guard against deleted users — skip notify but still mark expired
            if (! $borrow->user) {
                $this->warn("Borrow #{$borrow->borrow_id} has no associated user — skipping notification.");
                $marked++;
                continue;
            }

            $rp = $borrow->user->isFaculty() ? 'faculty' : 'student';
            $borrow->user->notify(new AxiomNotification(
                message: "Your access to \"{$borrow->ebook->title}\" has expired. Visit your borrow history to see past records.",
                type:    'alert',
                link:    route("{$rp}.my-books.index", ['tab' => 'history'])
            ));

            $marked++;
        }

        // Only notify librarians if there are any registered
        $librarians = User::whereHas('role', fn($q) =>
            $q->where('role_name', 'Librarian')
        )->get();

        if ($librarians->isNotEmpty()) {
            Notification::send($librarians, new AxiomNotification(
                message: "{$marked} borrow(s) have been marked as expired and access has been revoked.",
                type:    'info',
                link:    route('librarian.borrows.index', ['status' => 'expired'])
            ));
        }

        $this->info("Marked {$marked} borrow(s) as expired.");
    }
}