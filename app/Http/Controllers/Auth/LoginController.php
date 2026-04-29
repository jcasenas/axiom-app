<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\User;
use App\Notifications\AxiomNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // ── Show login form ────────────────────────────────────────────
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ── Handle login submission ────────────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        // ── Rate Limiting with safety for local development ─────────
        try {
            if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                return back()->withErrors([
                    'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
                ])->onlyInput('email');
            }
        } catch (\Throwable $e) {
            \Log::warning('RateLimiter failed: ' . $e->getMessage());
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            try {
                RateLimiter::hit($throttleKey, 60);
            } catch (\Throwable $e) {
                \Log::warning('RateLimiter::hit failed: ' . $e->getMessage());
            }

            return back()->withErrors(['email' => 'Invalid email or password.'])
                         ->onlyInput('email');
        }

        // Clear rate limiter on successful login
        try {
            RateLimiter::clear($throttleKey);
        } catch (\Throwable $e) {
            \Log::warning('RateLimiter::clear failed: ' . $e->getMessage());
        }

        $user = Auth::user();

        // Block inactive / pending accounts
        if ($user->account_status !== 'active') {
            Auth::logout();
            $msg = $user->account_status === 'pending'
                ? 'Your account is pending approval. Please wait for an administrator to activate it.'
                : 'Your account has been deactivated. Please contact the library.';
            return back()->withErrors(['email' => $msg])->onlyInput('email');
        }

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        // Scheduler fallbacks (runs on every login since Render free tier sleeps)
        $this->markOverdueBorrowings();
        $this->markDueSoonBorrowings();

        return $this->redirectAfterLogin($user);
    }

    // ── Role-based redirect ────────────────────────────────────────
    private function redirectAfterLogin($user)
    {
        $role = $user->role?->role_name;

        return match ($role) {
            'Admin'     => redirect()->route('admin.dashboard'),
            'Librarian' => redirect()->route('librarian.dashboard'),
            'Faculty'   => redirect()->route('faculty.dashboard'),
            'Student'   => redirect()->route('student.dashboard'),
            default     => redirect('/'),
        };
    }

    // ── Overdue check (mirrors axiom:mark-overdue command) ─────────
    private function markOverdueBorrowings(): void
    {
        try {
            $now = Carbon::now();

            $overdue = Borrowing::with(['user', 'ebook'])
                ->whereIn('status', ['active', 'due_soon'])
                ->where('access_expires_at', '<', $now)
                ->get();

            if ($overdue->isEmpty()) {
                return;
            }

            foreach ($overdue as $borrow) {
                $borrow->update(['status' => 'expired']);

                if ($borrow->ebook) {
                    $borrow->ebook->refresh();
                    if ($borrow->ebook->available_copies < $borrow->ebook->total_copies) {
                        $borrow->ebook->increment('available_copies');
                    }
                }

                if (! $borrow->user) {
                    continue;
                }

                $rp = $borrow->user->isFaculty() ? 'faculty' : 'student';
                $borrow->user->notify(new AxiomNotification(
                    message: "Your access to \"{$borrow->ebook->title}\" has expired. Visit your borrow history to see past records.",
                    type:    'alert',
                    link:    route("{$rp}.my-books.index", ['tab' => 'history'])
                ));
            }

            // Notify librarians once for the batch
            $librarians = User::whereHas('role', fn($q) =>
                $q->where('role_name', 'Librarian')
            )->get();

            if ($librarians->isNotEmpty()) {
                Notification::send($librarians, new AxiomNotification(
                    message: "{$overdue->count()} borrow(s) have been marked as expired and access has been revoked.",
                    type:    'info',
                    link:    route('librarian.borrows.index', ['status' => 'expired'])
                ));
            }
        } catch (\Throwable $e) {
            \Log::error('markOverdueBorrowings failed on login: ' . $e->getMessage());
        }
    }

    // ── Due Soon check (mirrors axiom:due-reminders command) ───────
    private function markDueSoonBorrowings(): void
    {
        try {
            $today   = Carbon::today();
            $in1Day  = $today->copy()->addDay()->toDateString();
            $in2Days = $today->copy()->addDays(2)->toDateString();

            $borrows = Borrowing::with(['user', 'ebook'])
                ->where('status', 'active')
                ->whereRaw('DATE(access_expires_at) IN (?, ?)', [$in1Day, $in2Days])
                ->get();

            if ($borrows->isEmpty()) {
                return;
            }

            foreach ($borrows as $borrow) {
                // Mark as due_soon
                $borrow->update(['status' => 'due_soon']);

                if (! $borrow->user || ! $borrow->ebook) {
                    continue;
                }

                $daysLeft = $today->diffInDays(Carbon::parse($borrow->access_expires_at));
                $label    = $daysLeft <= 1 ? 'tomorrow' : 'in 2 days';
                $rp       = $borrow->user->isFaculty() ? 'faculty' : 'student';

                $borrow->user->notify(new AxiomNotification(
                    message: "Reminder: Your access to \"{$borrow->ebook->title}\" expires {$label} on " .
                             Carbon::parse($borrow->access_expires_at)->format('M d, Y') . ".",
                    type:    'warning',
                    link:    route("{$rp}.my-books.index")
                ));
            }
        } catch (\Throwable $e) {
            \Log::error('markDueSoonBorrowings failed on login: ' . $e->getMessage());
        }
    }
}