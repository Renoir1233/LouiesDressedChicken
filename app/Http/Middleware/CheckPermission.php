<?php
// app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}