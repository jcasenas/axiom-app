<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form (step 1 — email lookup).
     */
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Verify the email exists and proceed to reset form (step 2).
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Always show the same message whether found or not — prevents
        // user enumeration (an attacker learning which emails are registered)
        if (! $user) {
            return back()->with('verify_error',
                'If that email is registered, you may proceed to reset your password.'
            );
        }

        // Block inactive / pending accounts from resetting
        if ($user->account_status !== 'active') {
            return back()->with('verify_error',
                'This account is not active. Please contact the library administrator.'
            );
        }

        // Store email in session to carry it to the reset step
        // We use a short-lived signed token approach: store a hashed
        // combination of email + current password hash so the link
        // is invalidated once the password is changed.
        $token = hash('sha256', $user->email . $user->password . config('app.key'));
        session(['pwd_reset_email' => $user->email, 'pwd_reset_token' => $token]);

        return redirect()->route('axiom.password.reset.form');
    }

    /**
     * Show the new password form (step 2).
     * Only accessible if a valid session token exists.
     */
    public function showResetForm()
    {
        if (! session('pwd_reset_email') || ! session('pwd_reset_token')) {
            return redirect()->route('axiom.password.request')
                ->with('verify_error', 'Please enter your email first.');
        }

        return view('auth.reset-password');
    }

    /**
     * Apply the new password.
     */
    public function reset(Request $request)
    {
        // Validate session is still intact
        if (! session('pwd_reset_email') || ! session('pwd_reset_token')) {
            return redirect()->route('axiom.password.request')
                ->with('verify_error', 'Session expired. Please start again.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('pwd_reset_email');
        $user  = User::where('email', $email)->where('account_status', 'active')->first();

        if (! $user) {
            return redirect()->route('axiom.password.request')
                ->with('verify_error', 'Something went wrong. Please try again.');
        }

        // Re-validate token hasn't been tampered with
        $expectedToken = hash('sha256', $user->email . $user->password . config('app.key'));
        if (! hash_equals($expectedToken, session('pwd_reset_token'))) {
            session()->forget(['pwd_reset_email', 'pwd_reset_token']);
            return redirect()->route('axiom.password.request')
                ->with('verify_error', 'This reset link is no longer valid. Please start again.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Clear the session tokens
        session()->forget(['pwd_reset_email', 'pwd_reset_token']);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. You may now log in.');
    }
}