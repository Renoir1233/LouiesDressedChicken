<?php

namespace App\Services;

use Illuminate\Http\Request;

class DeviceFingerprintService
{
    /**
     * Generate a unique fingerprint for a device based on request characteristics.
     */
    public static function generate(Request $request): string
    {
        $fingerprint = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accept_language' => $request->header('Accept-Language'),
            'accept_encoding' => $request->header('Accept-Encoding'),
        ];

        // Create a hash of the device fingerprint
        return hash('sha256', json_encode($fingerprint));
    }

    /**
     * Extract device information from request.
     */
    public static function extractDeviceInfo(Request $request): array
    {
        $userAgent = $request->userAgent();
        
        return [
            'browser' => self::getBrowserName($userAgent),
            'os' => self::getOSName($userAgent),
            'ip_address' => $request->ip(),
        ];
    }

    /**
     * Get browser name from user agent.
     */
    private static function getBrowserName($userAgent)
    {
        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge';
        } elseif (str_contains($userAgent, 'Opera')) {
            return 'Opera';
        }
        return 'Unknown';
    }

    /**
     * Get operating system from user agent.
     */
    private static function getOSName($userAgent)
    {
        if (str_contains($userAgent, 'Windows')) {
            return 'Windows';
        } elseif (str_contains($userAgent, 'Mac')) {
            return 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            return 'Linux';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'iOS';
        } elseif (str_contains($userAgent, 'Android')) {
            return 'Android';
        }
        return 'Unknown';
    }
}
