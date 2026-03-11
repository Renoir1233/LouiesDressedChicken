<?php

namespace App\Services;

use App\Models\LoginAttempt;
use Illuminate\Support\Facades\RateLimiter;

class ProgressiveLoginLockoutService
{
    /**
     * Get the lockout duration in minutes based on number of attempts
     * 
     * Lockout progression:
     * - 1-3 attempts: No lockout, show warning at attempt 3
     * - 4 attempts: 2 minute lockout
     * - 5 attempts: 10 minute lockout
     * - 6 attempts: 1 hour lockout
     * - 7+ attempts: 24 hour lockout
     */
    public static function getLockoutDuration($attemptCount): ?int
    {
        return match($attemptCount) {
            4 => 2,      // 2 minutes
            5 => 10,     // 10 minutes
            6 => 60,     // 1 hour
            default => $attemptCount >= 7 ? 1440 : null, // 24 hours if 7+
        };
    }

    /**
     * Get the lockout threshold for showing warnings
     */
    public static function getWarningThreshold(): int
    {
        return 3; // Show warning after 3 attempts
    }

    /**
     * Get the next lockout duration message
     */
    public static function getNextLockoutWarning($attemptCount): ?string
    {
        return match($attemptCount) {
            3 => 'Warning: Your next failed attempt will lock your account for 2 minutes.',
            4 => 'Warning: Your next failed attempt will lock your account for 10 minutes.',
            5 => 'Warning: Your next failed attempt will lock your account for 1 hour.',
            6 => 'Warning: Your next failed attempt will lock your account for 24 hours.',
            default => null,
        };
    }

    /**
     * Check if account is locked and get remaining lockout time
     */
    public static function isLockedOut($email, $ipAddress): array
    {
        try {
            $attempt = LoginAttempt::where('email', $email)
                ->where('ip_address', $ipAddress)
                ->first();

            if (!$attempt) {
                return ['locked' => false];
            }

            // Check if time has reset
            $attempt->resetIfExpired();

            if (!$attempt->isLocked()) {
                return ['locked' => false];
            }

            $minutesRemaining = $attempt->getRemainingLockoutMinutes();

            return [
                'locked' => true,
                'minutes' => $minutesRemaining,
                'message' => "Account locked. Please try again in {$minutesRemaining} minute(s).",
            ];
        } catch (\Exception $e) {
            // Fallback if database is unavailable
            return ['locked' => false];
        }
    }

    /**
     * Record a failed login attempt and apply progressive lockout
     */
    public static function recordFailedAttempt($email, $ipAddress): ?array
    {
        try {
            $attempt = LoginAttempt::getOrCreate($email, $ipAddress);
            $attempt->resetIfExpired(); // Reset if more than 1 hour has passed
            
            $attempt->recordFailedAttempt();
            $totalAttempts = $attempt->failed_attempts;

            // Get lockout duration based on attempts
            $lockoutDuration = self::getLockoutDuration($totalAttempts);

            // If we have a lockout duration, apply the lockout
            if ($lockoutDuration !== null) {
                $attempt->lockFor($lockoutDuration);
            }

            return [
                'attempts' => $totalAttempts,
                'warning' => self::getNextLockoutWarning($totalAttempts),
                'is_locked' => $lockoutDuration !== null,
                'lockout_duration' => $lockoutDuration,
            ];
        } catch (\Exception $e) {
            // Fallback to RateLimiter if database fails
            return null;
        }
    }

    /**
     * Get current attempt count
     */
    public static function getAttemptCount($email, $ipAddress): int
    {
        try {
            $attempt = LoginAttempt::where('email', $email)
                ->where('ip_address', $ipAddress)
                ->first();

            if (!$attempt) {
                return 0;
            }

            $attempt->resetIfExpired();
            return $attempt->failed_attempts;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Clear failed attempts
     */
    public static function clearAttempts($email, $ipAddress): void
    {
        try {
            $attempt = LoginAttempt::where('email', $email)
                ->where('ip_address', $ipAddress)
                ->first();

            if ($attempt) {
                $attempt->clearAttempts();
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}

