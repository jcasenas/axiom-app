<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\EbookAuthor;
use App\Models\EbookCategory;
use App\Models\EbookFormat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Ebook::with(['author', 'category', 'format']);

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $books      = $query->latest()->paginate(15)->withQueryString();
        $categories = EbookCategory::orderBy('category_name')->get();
        $authors    = EbookAuthor::orderBy('author_name')->get();
        $formats    = EbookFormat::all();

        return view('admin.books.index', compact('books', 'categories', 'authors', 'formats'));
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'isbn'         => 'nullable|string|max:20|unique:ebooks,isbn',
            'author_input' => 'required|string|max:120',
            'cover_url'    => 'nullable|url|max:2048',
            'category_id'  => 'required|exists:ebook_categories,category_id',
            'format_id'    => 'required|exists:ebook_formats,format_id',
            'total_copies' => 'required|integer|min:1',
            'file_url'     => 'nullable|string|max:2048',
            'description'  => 'nullable|string',
        ]);

        $author = EbookAuthor::firstOrCreate(
            ['author_name' => trim($validated['author_input'])],
        );

        Ebook::create([
            'title'            => $validated['title'],
            'isbn'             => $validated['isbn'] ?? null,
            'author_id'        => $author->author_id,
            'cover_url'        => $validated['cover_url'] ?? null,
            'category_id'      => $validated['category_id'],
            'format_id'        => $validated['format_id'],
            'total_copies'     => $validated['total_copies'],
            'available_copies' => $validated['total_copies'],
            'file_url'         => $validated['file_url'] ?? null,
            'description'      => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.books.index')
            ->with('success', 'Book added successfully.');
    }

    public function update(Request $request, Ebook $book)
    {
        $data = $request->validate([
            'category_id'      => 'required|exists:ebook_categories,category_id',
            'status'           => 'required|in:active,unavailable,archived',
            'total_copies'     => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0',
            'cover_url'        => 'nullable|url|max:2048',
            'file_url'         => 'nullable|string|max:2048',
            'description'      => 'nullable|string',
        ]);

        if ($data['available_copies'] > $data['total_copies']) {
            return back()->withErrors(['available_copies' => 'Available copies cannot exceed total copies.']);
        }

        $book->update($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Ebook $book)
    {
        $book->update(['status' => 'archived']);

        return redirect()->route('admin.books.index')
            ->with('success', "{$book->title} has been archived.");
    }
}