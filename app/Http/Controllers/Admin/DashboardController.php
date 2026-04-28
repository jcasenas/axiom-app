<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI counts
        $pendingApprovals = User::where('account_status', 'pending')->count();
        $expiringToday    = Borrowing::where('status', 'active')
                               ->whereDate('access_expires_at', today())
                               ->count();
        $activeBorrows    = Borrowing::where('status', 'active')->count();

        // Today's borrow activity table
        $todayActivity = Borrowing::with(['user.department', 'ebook'])
                    ->whereDate('requested_at', today())
                    ->latest('requested_at')
                    ->take(10)
                    ->get();

        return view('admin.dashboard', compact(
            'pendingApprovals',
            'expiringToday',
            'activeBorrows',
            'todayActivity'
        ));
    }
}