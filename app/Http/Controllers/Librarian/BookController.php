<?php

namespace App\Http\Controllers\Librarian;

use App\Notifications\AxiomNotification;
use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\EbookCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Ebook::with(['author', 'category', 'format'])
            ->where('status', '!=', 'archived');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('author', fn($a) =>
                      $a->where('author_name', 'like', "%{$search}%")
                  );
            });
        }

        $books      = $query->orderBy('title')->paginate(15)->withQueryString();
        $categories = EbookCategory::orderBy('category_name')->get();

        return view('librarian.books.index', compact('books', 'categories'));
    }

    /**
     * Catalog PDF Report — grouped by category, only books with borrow history.
     */
    public function catalogPdf()
    {
        $categories = EbookCategory::orderBy('category_name')
            ->with(['ebooks' => function ($q) {
                $q->with(['author', 'format', 'borrowings'])
                  ->whereHas('borrowings')
                  ->where('status', '!=', 'archived')
                  ->orderBy('title');
            }])
            ->get()
            ->filter(fn($cat) => $cat->ebooks->isNotEmpty())
            ->map(function ($category) {
                $category->books = $category->ebooks->map(function ($book) {
                    $b = $book->borrowings;

                    $book->all_borrows       = $b->count();
                    $book->active_borrows    = $b->whereIn('status', ['active', 'due_soon'])->count();
                    $book->pending_borrows   = $b->where('status', 'pending')->count();
                    $book->completed_borrows = $b->where('status', 'expired')->count();
                    $book->cancelled_borrows = $b->where('status', 'cancelled')->count();

                    return $book;
                });

                return $category;
            });

        $generatedBy = Auth::user()->full_name . ' (' . Auth::user()->role->role_name . ')';

        $pdf = Pdf::loadView('shared.catalog-pdf', compact('categories', 'generatedBy'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('catalog-report-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Librarians may only update available_copies and status.
     */
    public function update(Request $request, Ebook $book)
    {
        $data = $request->validate([
            'available_copies' => 'required|integer|min:0',
            'status'           => 'required|in:active,unavailable',
        ]);

        if ($data['available_copies'] > $book->total_copies) {
            return back()->withErrors([
                'available_copies' => 'Available copies cannot exceed total copies (' . $book->total_copies . ').',
            ]);
        }

        $book->update($data);

        return redirect()->route('librarian.books.index')
            ->with('toast', ['message' => 'Book updated successfully.', 'type' => 'success']);
    }
}