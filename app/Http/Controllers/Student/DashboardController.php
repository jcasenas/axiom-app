<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Ebook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // KPI 1 – borrow requests awaiting librarian approval
        $pendingBorrows = Borrowing::where('user_id', $user->user_id)
            ->where('status', 'pending')
            ->count();

        // KPI 2 – active borrows whose due date is within 2 days (due_soon)
        $dueSoon = Borrowing::where('user_id', $user->user_id)
            ->where('status', 'due_soon')
            ->count();

        // KPI 3 – currently active (approved) borrows
        $activeBorrows = Borrowing::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->count();

        // Table – currently borrowed (active + due_soon) with due dates
        $currentlyBorrowed = Borrowing::with(['ebook.author', 'ebook.category'])
            ->where('user_id', $user->user_id)
            ->whereIn('status', ['active', 'due_soon', 'pending'])
            ->latest('requested_at')
            ->take(10)
            ->get();

        // Discover – recently added active books (newest 8)
        $recentBooks = Ebook::with(['author', 'category', 'format'])
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get();

        // IDs the user already has an active/pending borrow for (used in modal state)
        $alreadyBorrowed = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])
            ->pluck('ebook_id')
            ->toArray();

        $borrowLimit   = $user->role->borrow_limit ?? 3;
        $activeBorrowCount = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])
            ->count();
        $atLimit = $activeBorrowCount >= $borrowLimit;

        $routePrefix = $user->isFaculty() ? 'faculty' : 'student';

        return view('student.dashboard', compact(
            'pendingBorrows',
            'dueSoon',
            'activeBorrows',
            'currentlyBorrowed',
            'recentBooks',
            'alreadyBorrowed',
            'atLimit',
            'borrowLimit',
            'routePrefix'
        ));
    }
}