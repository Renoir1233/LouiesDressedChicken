<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\DeviceLogin;
use App\Providers\RouteServiceProvider;
use App\Services\DeviceFingerprintService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $deviceFingerprint = DeviceFingerprintService::generate($request);
        $deviceInfo = DeviceFingerprintService::extractDeviceInfo($request);

        // Check if this is a trusted device
        $isKnownDevice = DeviceLogin::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->exists();

        $isTrustedDevice = $user->isTrustedDevice($deviceFingerprint);

        // Always require 2FA verification for untrusted devices
        if (!$isTrustedDevice) {
            // Generate 2FA code
            $code = $user->generateTwoFactorCode();

            // Send code to email
            Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));

            // Store device info temporarily
            if (!$isKnownDevice) {
                DeviceLogin::create([
                    'user_id' => $user->id,
                    'device_fingerprint' => $deviceFingerprint,
                    'device_name' => $deviceInfo['browser'] . ' on ' . $deviceInfo['os'],
                    'browser' => $deviceInfo['browser'],
                    'os' => $deviceInfo['os'],
                    'ip_address' => $deviceInfo['ip_address'],
                    'is_trusted' => false,
                ]);
            }

            // Mark session as pending 2FA
            $request->session()->put('2fa_pending', true);
            $request->session()->regenerate();

            return redirect()->route('verify.2fa.show')->with('status', 'A verification code has been sent to your email address.');
        }

        // Update or create device login record
        DeviceLogin::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_fingerprint' => $deviceFingerprint,
            ],
            [
                'device_name' => $deviceInfo['browser'] . ' on ' . $deviceInfo['os'],
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'ip_address' => $deviceInfo['ip_address'],
                'last_login_at' => now(),
            ]
        );

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

