<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI 1 — pending borrow requests
        $borrowRequests = Borrowing::where('status', 'pending')->count();

        // KPI 2 — borrows whose access_expires_at falls today (active OR due_soon)
        $expiringToday = Borrowing::whereIn('status', ['active', 'due_soon'])
            ->whereDate('access_expires_at', today())
            ->count();

        // KPI 3 — borrows approved today (borrow_date = today)
        $borrowsToday = Borrowing::whereDate('borrow_date', today())->count();

        // Table — all borrowing activity recorded today
        $todayActivity = Borrowing::with(['user.department', 'ebook'])
            ->whereDate('requested_at', today())
            ->latest('requested_at')
            ->get();

        return view('librarian.dashboard', compact(
            'borrowRequests',
            'expiringToday',
            'borrowsToday',
            'todayActivity',
        ));
    }
}