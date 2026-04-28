<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has the required role
        if (!$user->role || $user->role->role_name !== $role) {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['email' => "You do not have {$role} access."]);
        }

        return $next($request);
    }
}