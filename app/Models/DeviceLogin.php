<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLogin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'device_name',
        'browser',
        'os',
        'ip_address',
        'is_trusted',
        'last_login_at',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the user that owns this device login.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark device as trusted.
     */
    public function trust()
    {
        $this->update(['is_trusted' => true]);
    }

    /**
     * Check if device is trusted.
     */
    public function isTrusted()
    {
        return $this->is_trusted;
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
