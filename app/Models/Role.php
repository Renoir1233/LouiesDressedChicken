<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'description',
        'is_default'
    ];

    protected $casts = [
        'permissions' => 'array', // This auto-converts JSON to array
        'is_default' => 'boolean'
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }

    // Accessor for permissions (already handled by cast)
    public function getPermissionsArrayAttribute()
    {
        return $this->permissions ?? [];
    }

    // Helper Methods
    public function getPermissionsListAttribute()
    {
        $permissions = $this->permissions_array;
        $permissionGroups = [
            'Dashboard' => ['dashboard'],
            'Orders' => ['orders.*', 'orders.create', 'orders.edit', 'orders.delete', 'orders.view'],
            'Inventory' => ['inventory.*', 'inventory.create', 'inventory.edit', 'inventory.delete', 'inventory.view'],
            'Employees' => ['employees.*', 'employees.create', 'employees.edit', 'employees.delete', 'employees.view'],
            'Suppliers' => ['suppliers.*', 'suppliers.create', 'suppliers.edit', 'suppliers.delete', 'suppliers.view'],
            'Billing' => ['billing.*', 'billing.create', 'billing.edit', 'billing.view'],
            'Users' => ['users.*', 'users.create', 'users.edit', 'users.delete', 'users.view'],
            'Audit Logs' => ['audit-logs.*', 'audit-logs.view'],
            'Reports' => ['reports.*', 'reports.generate'],
        ];

        $result = [];
        foreach ($permissionGroups as $group => $groupPermissions) {
            foreach ($groupPermissions as $permission) {
                if (in_array($permission, $permissions) || in_array('*', $permissions)) {
                    $result[$group][] = $permission;
                }
            }
        }

        return $result;
    }
}