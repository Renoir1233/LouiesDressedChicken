<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact administrator.',
                ]);
            }

            // Log login action
            AuditLog::create([
                'action' => 'login',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => "User {$user->name} logged in",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => $user->id,
            ]);

            $request->session()->regenerate();
            
            // Redirect based on role
            if ($user->hasPermission('dashboard')) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('orders.index');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Log logout action
            AuditLog::create([
                'action' => 'logout',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => "User {$user->name} logged out",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => $user->id,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}