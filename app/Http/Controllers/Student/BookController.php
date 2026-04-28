<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\SystemSetting;
use App\Models\User;
use App\Notifications\NewBorrowRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class BookController extends Controller
{
    /**
     * Browse the active book catalog with optional category filter and search.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Ebook::with(['author', 'category', 'format'])
            ->where('status', 'active');

        // Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Search by title, author, or ISBN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('author', fn ($a) => $a->where('author_name', 'like', "%{$search}%"));
            });
        }

        $books      = $query->orderBy('title')->paginate(10)->withQueryString();
        $categories = EbookCategory::orderBy('category_name')->get();

        // IDs of books this user already has an active/pending borrow for
        $alreadyBorrowed = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])
            ->pluck('ebook_id')
            ->toArray();

        // Borrow limit from role
        $borrowLimit   = $user->role->borrow_limit ?? 3;
        $activeBorrows = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])
            ->count();
        $atLimit = $activeBorrows >= $borrowLimit;

        return view('student.books.index', compact(
            'books',
            'categories',
            'alreadyBorrowed',
            'atLimit',
            'borrowLimit',
            'activeBorrows'
        ));
    }

    /**
     * Submit a borrow request for a book.
     */
    public function borrow(Request $request, Ebook $book)
    {
        $user = Auth::user();

        // Guard: book must be active and have copies available
        if ($book->status !== 'active' || $book->available_copies < 1) {
            return back()->with('error', 'This book has no available copies at the moment.');
        }

        // Guard: borrow limit
        $borrowLimit   = $user->role->borrow_limit ?? 3;
        $activeBorrows = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])
            ->count();

        if ($activeBorrows >= $borrowLimit) {
            return back()->with('error', "You have reached your borrow limit of {$borrowLimit} book(s).");
        }

        // Guard: not already borrowing this book
        $exists = Borrowing::where('user_id', $user->user_id)
            ->where('ebook_id', $book->ebook_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have an active or pending borrow request for this book.');
        }

        // Create the borrowing in 'pending' status
        // borrow_date / due_date / access_expires_at are set by librarian on approval
        $borrowing = Borrowing::create([
            'user_id'  => $user->user_id,
            'ebook_id' => $book->ebook_id,
            'status'   => 'pending',
        ]);

        // Notify all librarians in a single batched call — avoids one DB
        // insert per librarian that the old each() + notify() loop caused
        $librarians = User::whereHas('role', fn($q) =>
            $q->where('role_name', 'Librarian')
        )->get();

        if ($librarians->isNotEmpty()) {
            Notification::send($librarians, new NewBorrowRequest($borrowing));
        }

        $routePrefix = $user->isFaculty() ? 'faculty' : 'student';

        return redirect()->route("{$routePrefix}.books.index")
            ->with('success', "Borrow request for \"{$book->title}\" submitted successfully. Awaiting librarian approval.");
    }
}