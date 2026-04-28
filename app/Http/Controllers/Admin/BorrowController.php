<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

        // Special filter from "Expiring Today" KPI — must include due_soon too
        if ($request->input('filter') === 'expiring_today') {
            $query->whereIn('status', ['active', 'due_soon'])
                  ->whereDate('access_expires_at', today());
        }
        // Normal Department filter
        elseif ($request->filled('department') && $request->department !== 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        // Status filter — skip when expiring_today is active (status already fixed)
        if ($request->input('filter') !== 'expiring_today'
            && $request->filled('status')
            && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $borrows = $query->latest('requested_at')
                         ->paginate(15)
                         ->withQueryString();

        $departments = Department::orderBy('department_name')->get();

        return view('admin.borrows.index', compact('borrows', 'departments'));
    }

    /**
     * Generate PDF report — same filter logic as index().
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

        $pdf = Pdf::loadView('admin.borrows.pdf', compact('borrows'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('borrow-records-' . now()->format('Ymd') . '.pdf');
    }
}