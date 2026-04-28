<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\User;
use App\Notifications\AxiomNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    protected $signature   = 'axiom:due-reminders';
    protected $description = 'Notify borrowers whose access expires in 1 or 2 days';

    public function handle(): void
    {
        $today   = Carbon::today();
        $in1Day  = $today->copy()->addDay()->toDateString();
        $in2Days = $today->copy()->addDays(2)->toDateString();

        $borrows = Borrowing::with(['user', 'ebook'])
            ->whereIn('status', ['active', 'due_soon'])
            ->whereRaw('DATE(access_expires_at) IN (?, ?)', [$in1Day, $in2Days])
            ->get();

        $sent = 0;

        foreach ($borrows as $borrow) {
            // Per-borrow deduplication: check whether a due-date reminder
            // for this specific borrow_id was already sent today.
            // Searching the JSON `data` column works on both MySQL and SQLite.
            $alreadyNotified = \DB::table('notifications')
                ->where('notifiable_id',   $borrow->user->user_id)
                ->where('notifiable_type', User::class)
                ->where('type',            AxiomNotification::class)
                ->whereDate('created_at',  today())
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.borrow_id')) = ?", [$borrow->borrow_id])
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            $daysLeft   = $today->diffInDays(Carbon::parse($borrow->access_expires_at));
            $label      = $daysLeft <= 1 ? 'tomorrow' : 'in 2 days';
            // Last access day is the day before access_expires_at (startOfDay of due_date)
            $lastAccess = Carbon::parse($borrow->access_expires_at)->subDay()->format('M d, Y');
            $rp         = $borrow->user->isFaculty() ? 'faculty' : 'student';

            $borrow->user->notify(new AxiomNotification(
                message:  "Reminder: Your last day of access to \"{$borrow->ebook->title}\" is {$label} ({$lastAccess}).",
                type:     'warning',
                link:     route("{$rp}.my-books.index"),
                borrowId: $borrow->borrow_id,
            ));

            if ($borrow->status !== 'due_soon') {
                $borrow->update(['status' => 'due_soon']);
            }

            $sent++;
        }

        $this->info("Sent reminders for {$sent} borrow(s).");
    }
}