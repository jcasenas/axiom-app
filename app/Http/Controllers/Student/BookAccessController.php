<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookAccessController extends Controller
{
    public function read(Borrowing $borrowing)
    {
        $user = Auth::user();

        // Security: borrow must belong to the authenticated user
        if ($borrowing->user_id !== $user->user_id) {
            abort(403, 'This book does not belong to you.');
        }

        // Status check: only active or due_soon borrows may be read
        if (! in_array($borrowing->status, ['active', 'due_soon'])) {
            return back()->with('toast', [
                'message' => 'You do not have active access to this book.',
                'type'    => 'error',
            ]);
        }

        // Deny access and flip status immediately rather than waiting for cron.
        if ($borrowing->access_expires_at && Carbon::now()->gte($borrowing->access_expires_at)) {
            $borrowing->update(['status' => 'expired']);

            if ($borrowing->ebook) {
                $borrowing->ebook->refresh();
                if ($borrowing->ebook->available_copies < $borrowing->ebook->total_copies) {
                    $borrowing->ebook->increment('available_copies');
                }
            }

            return back()->with('toast', [
                'message' => 'Your access to this book has expired.',
                'type'    => 'error',
            ]);
        }

        $ebook = $borrowing->ebook;

        if (! $ebook || empty($ebook->file_url)) {
            return back()->with('toast', [
                'message' => 'No file is available for this book yet. Please contact the library.',
                'type'    => 'error',
            ]);
        }

        // Google Drive links open directly in a new tab (most reliable cross-browser)
        // All other hosts use the embedded reader page
        if (str_contains($ebook->file_url, 'drive.google.com')) {
            return redirect()->away($ebook->file_url);
        }

        return view('student.my-books.read', compact('borrowing', 'ebook'));
    }
}