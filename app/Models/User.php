<?php
// app/Models/User.php - FIXED VERSION

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'avatar',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_code_expires_at',
        'trusted_devices'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'two_factor_code_expires_at' => 'datetime',
        'trusted_devices' => 'array',
    ];

    // Relationships
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function deviceLogins()
    {
        return $this->hasMany(DeviceLogin::class);
    }

    // Helper Methods - Two-Factor Authentication
    public function hasPermission($permission)
    {
        if ($this->role === 'super-admin') {
            return true;
        }

        $role = Role::where('slug', $this->role)->first();
        if (!$role) {
            return false;
        }

        // FIXED: $role->permissions is already an array due to the cast in Role model
        // No need for json_decode() here
        $permissions = $role->permissions ?? [];
        
        // Check for wildcard or exact permission
        if (in_array('*', $permissions) || in_array($permission, $permissions)) {
            return true;
        }

        // Check for wildcard permissions (e.g., orders.*)
        foreach ($permissions as $perm) {
            if (str_contains($perm, '.*')) {
                $prefix = str_replace('.*', '', $perm);
                if (str_starts_with($permission, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isAdmin()
    {
        return in_array($this->role, ['super-admin', 'admin']);
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Generate initial avatar
        $name = str_replace(' ', '+', $this->name);
        $color = substr(md5($this->email), 0, 6);
        return "https://ui-avatars.com/api/?name={$name}&color=FFFFFF&background={$color}&size=200&bold=true&format=svg";
    }

    public function getRoleNameAttribute()
    {
        $roles = [
            'super-admin' => 'Super Admin',
            'admin' => 'Admin',
            'manager' => 'Manager',
            'cashier' => 'Cashier',
            'staff' => 'Staff'
        ];
        
        return $roles[$this->role] ?? ucfirst(str_replace('-', ' ', $this->role));
    }

    // Two-Factor Authentication Helper Methods
    public function enableTwoFactor()
    {
        $this->update(['two_factor_enabled' => true]);
    }

    public function disableTwoFactor()
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_code' => null,
            'two_factor_code_expires_at' => null,
        ]);
    }

    public function generateTwoFactorCode()
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'two_factor_code' => $code,
            'two_factor_code_expires_at' => now()->addMinutes(15), // Code expires in 15 minutes
        ]);
        return $code;
    }

    public function verifyTwoFactorCode($code)
    {
        if (!$this->two_factor_code_expires_at || now()->isAfter($this->two_factor_code_expires_at)) {
            return false; // Code has expired
        }
        
        if ($this->two_factor_code !== $code) {
            return false; // Code doesn't match
        }

        // Clear the code
        $this->update([
            'two_factor_code' => null,
            'two_factor_code_expires_at' => null,
        ]);

        return true;
    }

    public function addTrustedDevice($deviceFingerprint)
    {
        $trustedDevices = $this->trusted_devices ?? [];
        if (!in_array($deviceFingerprint, $trustedDevices)) {
            $trustedDevices[] = $deviceFingerprint;
            $this->update(['trusted_devices' => $trustedDevices]);
        }
    }

    public function isTrustedDevice($deviceFingerprint)
    {
        $trustedDevices = $this->trusted_devices ?? [];
        return in_array($deviceFingerprint, $trustedDevices);
    }

    public function removeTrustedDevice($deviceFingerprint)
    {
        $trustedDevices = $this->trusted_devices ?? [];
        $this->update(['trusted_devices' => array_values(array_filter($trustedDevices, function ($device) use ($deviceFingerprint) {
            return $device !== $deviceFingerprint;
        }))]);
    }

    public function clearTrustedDevices()
    {
        $this->update(['trusted_devices' => null]);
    }
}
