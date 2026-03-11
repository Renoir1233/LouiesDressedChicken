<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Block access to protected routes while 2FA verification is still pending.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && $request->session()->get('2fa_pending')) {
            return redirect()->route('verify.2fa.show');
        }

        return $next($request);
    }
}
