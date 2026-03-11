<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkEmailDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'email_domain',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all active work email domains for a specific role.
     */
    public static function getAllowedDomainsForRole($role)
    {
        return self::where('role', $role)
            ->where('is_active', true)
            ->pluck('email_domain')
            ->toArray();
    }

    /**
     * Check if an email is allowed for a specific role.
     */
    public static function isEmailAllowedForRole($email, $role)
    {
        if (!$email || !$role) {
            return false;
        }

        // Extract domain from email
        $emailDomain = substr(strrchr($email, '@'), 1);
        
        // Check if domain exists in allowed domains for this role
        return self::where('role', $role)
            ->where('email_domain', $emailDomain)
            ->where('is_active', true)
            ->exists();
    }
}
