<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->role || $user->role->role_name !== 'Admin') {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'You do not have administrator access.']);
        }

        return $next($request);
    }
}