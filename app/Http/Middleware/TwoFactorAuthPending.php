<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthPending
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and 2FA is pending
        if (Auth::check() && $request->session()->get('2fa_pending')) {
            return $next($request);
        }

        // If user is not authenticated, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // If 2FA is not pending, redirect to dashboard
        return redirect()->route('dashboard');
    }
}
