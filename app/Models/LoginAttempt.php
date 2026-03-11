<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'failed_attempts',
        'first_attempt_at',
        'last_attempt_at',
        'locked_until',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
        'first_attempt_at' => 'datetime',
        'last_attempt_at' => 'datetime',
    ];

    /**
     * Get or create a login attempt record.
     */
    public static function getOrCreate($email, $ipAddress)
    {
        return self::firstOrCreate(
            ['email' => $email, 'ip_address' => $ipAddress],
            ['failed_attempts' => 0]
        );
    }

    /**
     * Record a failed login attempt.
     */
    public function recordFailedAttempt()
    {
        $this->increment('failed_attempts');
        $this->update(['last_attempt_at' => now()]);
    }

    /**
     * Check if account is locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && now()->isBefore($this->locked_until);
    }

    /**
     * Get remaining lockout time in minutes.
     */
    public function getRemainingLockoutMinutes(): int
    {
        if (!$this->isLocked()) {
            return 0;
        }
        return (int) ceil($this->locked_until->diffInSeconds(now()) / 60);
    }

    /**
     * Lock the account for a specified number of minutes.
     */
    public function lockFor($minutes)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Clear failed attempts.
     */
    public function clearAttempts()
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_until' => null,
            'first_attempt_at' => now(),
        ]);
    }

    /**
     * Reset if enough time has passed (1 hour).
     */
    public function resetIfExpired()
    {
        if (!$this->last_attempt_at) {
            return; // No last attempt recorded yet
        }
        
        if ($this->last_attempt_at->diffInMinutes(now()) >= 60) {
            $this->clearAttempts();
        }
    }
}
