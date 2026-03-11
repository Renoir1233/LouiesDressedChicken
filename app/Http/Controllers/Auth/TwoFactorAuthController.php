<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeviceLogin;
use App\Services\DeviceFingerprintService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TwoFactorAuthController extends Controller
{
    /**
     * Show the two-factor authentication form.
     */
    public function show(): View
    {
        return view('auth.verify-2fa');
    }

    /**
     * Verify the two-factor authentication code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (!$user->verifyTwoFactorCode($request->code)) {
            return back()->withErrors([
                'code' => 'The provided code is invalid or has expired.',
            ]);
        }

        // Device is now verified
        $deviceFingerprint = DeviceFingerprintService::generate($request);
        $deviceInfo = DeviceFingerprintService::extractDeviceInfo($request);

        // If user checked "trust this device", add it to trusted devices
        if ($request->has('trust_device') && $request->trust_device == 1) {
            $user->addTrustedDevice($deviceFingerprint);
        }

        // Update device login record
        $deviceLogin = DeviceLogin::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->first();

        if ($deviceLogin) {
            $deviceLogin->update([
                'is_trusted' => $request->has('trust_device'),
                'last_login_at' => now(),
            ]);
        } else {
            DeviceLogin::create([
                'user_id' => $user->id,
                'device_fingerprint' => $deviceFingerprint,
                'device_name' => $deviceInfo['browser'] . ' on ' . $deviceInfo['os'],
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'ip_address' => $deviceInfo['ip_address'],
                'is_trusted' => $request->has('trust_device'),
                'last_login_at' => now(),
            ]);
        }

        // Clear the 2FA session data
        $request->session()->forget('2fa_pending');

        return redirect()->route('dashboard')->with('success', 'Two-factor authentication verified successfully.');
    }

    /**
     * Resend the 2FA code to user's email.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Generate a new code
        $code = $user->generateTwoFactorCode();

        // Send code to email
        Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));

        return back()->with('status', 'Verification code has been sent to your email.');
    }

    /**
     * Trust this device for future logins.
     */
    public function trustDevice(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $deviceFingerprint = DeviceFingerprintService::generate($request);
        
        // Add to trusted devices
        $user->addTrustedDevice($deviceFingerprint);

        // Update or create device login record
        $deviceInfo = DeviceFingerprintService::extractDeviceInfo($request);
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
                'is_trusted' => true,
                'last_login_at' => now(),
            ]
        );

        return back()->with('success', 'Device has been trusted.');
    }
}
