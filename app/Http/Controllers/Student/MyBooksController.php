<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\EbookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyBooksController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'active'); // 'active' or 'history'

        $query = Borrowing::with(['ebook.author', 'ebook.category', 'ebook.format'])
            ->where('user_id', $user->user_id);

        // Tab split
        if ($tab === 'history') {
            $query->whereIn('status', ['expired', 'cancelled']);
        } else {
            $query->whereIn('status', ['pending', 'active', 'due_soon']);
        }

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('ebook', fn($q) =>
                $q->where('category_id', $request->category)
            );
        }

        // Status filter (only relevant on active tab)
        if ($tab === 'active' && $request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('ebook', fn($q) =>
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('author', fn($a) =>
                      $a->where('author_name', 'like', "%{$search}%")
                  )
            );
        }

        $borrows    = $query->latest('requested_at')->paginate(10)->withQueryString();
        $categories = EbookCategory::orderBy('category_name')->get();

        // Counts for tab badges
        $activeCount  = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])->count();
        $historyCount = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['expired', 'cancelled'])->count();

        $view = Auth::user()->isFaculty()
        ? 'faculty.my-books.index'
        : 'student.my-books.index';

        return view($view, compact(
            'borrows', 'categories', 'tab', 'activeCount', 'historyCount'
        ));
    }

    public function cancel(Borrowing $borrowing)
{
    // Make sure the borrow belongs to the authenticated user
    if ($borrowing->user_id !== Auth::user()->user_id) {
        abort(403);
    }

    // Only pending borrows can be cancelled
    if ($borrowing->status !== 'pending') {
        return back()->with('toast', [
            'message' => 'Only pending requests can be cancelled.',
            'type'    => 'error'
        ]);
    }

    $borrowing->update(['status' => 'cancelled']);

    // Notify all librarians so they know it was withdrawn
    $librarians = \App\Models\User::whereHas('role', fn($q) =>
        $q->where('role_name', 'Librarian')
    )->get();

    \Illuminate\Support\Facades\Notification::send($librarians, new \App\Notifications\AxiomNotification(
        message: Auth::user()->full_name . " cancelled their borrow request for \"{$borrowing->ebook->title}\".",
        type: 'info',
        link: route('librarian.borrows.index')
    ));

    return back()->with('toast', [
        'message' => "Borrow request for \"{$borrowing->ebook->title}\" has been cancelled.",
        'type'    => 'success'
    ]);
}
}