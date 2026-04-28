<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Department;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with([
            'user.department',
            'ebook.author',
            'ebook.category',
            'ebook.format',
        ]);

        // Special filter from "Expiring Today" KPI — matches both active and due_soon
        if ($request->input('filter') === 'expiring_today') {
            $query->whereIn('status', ['active', 'due_soon'])
                  ->whereDate('access_expires_at', today());
        }
        // Normal department filter
        elseif ($request->filled('department') && $request->department !== 'all') {
            $query->whereHas('user', fn ($q) =>
                $q->where('department_id', $request->department)
            );
        }

        // Status filter (ignored when expiring_today is active — status is already fixed)
        if ($request->input('filter') !== 'expiring_today'
            && $request->filled('status')
            && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $borrows     = $query->latest('requested_at')->paginate(15)->withQueryString();
        $departments = Department::orderBy('department_name')->get();

        return view('librarian.borrows.index', compact('borrows', 'departments'));
    }

    /**
     * Approve a pending borrow request.
     */
    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $ebook = $borrowing->ebook;
        if ($ebook->available_copies < 1) {
            return back()->with('error', 'No available copies left for this book.');
        }

        $windowDays = (int) SystemSetting::getValue('borrow_window_days', 7);
        $now        = Carbon::now();
        $due        = $now->copy()->addDays($windowDays);

        $borrowing->update([
            'approved_by'       => Auth::id(),
            'borrow_date'       => $now->toDateString(),
            'due_date'          => $due->toDateString(),
            'access_expires_at' => $due->copy()->startOfDay(),
            'status'            => 'active',
        ]);

        $ebook->decrement('available_copies');

        $borrowing->user->notify(new \App\Notifications\BorrowApproved($borrowing));

        $lastAccessDay = $due->copy()->subDay()->format('M d, Y');
        return back()->with('success', "Borrow request approved. Borrower's last day of access is {$lastAccessDay}.");
    }

    /**
     * Reject a pending borrow request.
     */
    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $borrowing->update(['status' => 'cancelled']);

        $borrowing->user->notify(new \App\Notifications\BorrowRejected($borrowing));

        return back()->with('success', 'Borrow request rejected.');
    }

    /**
     * Generate filtered PDF report — same filter logic as index().
     */
    public function pdf(Request $request)
    {
        $query = Borrowing::with([
            'user.department',
            'ebook.author',
            'ebook.category',
            'ebook.format',
        ]);

        if ($request->input('filter') === 'expiring_today') {
            $query->whereIn('status', ['active', 'due_soon'])
                  ->whereDate('access_expires_at', today());
        } elseif ($request->filled('department') && $request->department !== 'all') {
            $query->whereHas('user', fn ($q) =>
                $q->where('department_id', $request->department)
            );
        }

        if ($request->input('filter') !== 'expiring_today'
            && $request->filled('status')
            && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $borrows = $query->latest('requested_at')->get();

        $pdf = Pdf::loadView('librarian.borrows.pdf', compact('borrows'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('borrow-records-' . now()->format('Ymd') . '.pdf');
    }
}