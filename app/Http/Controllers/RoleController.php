<?php
// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.*')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->getPermissionGroups();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
            'is_default' => 'sometimes|boolean'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'permissions' => $request->permissions, // Already an array, will be auto-encoded by cast
            'is_default' => $request->has('is_default') ? 1 : 0
        ]);

        // Log role creation
        AuditLog::create([
            'action' => 'created',
            'model_type' => Role::class,
            'model_id' => $role->id,
            'new_values' => $role->toArray(),
            'description' => "Role {$role->name} was created",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = $this->getPermissionGroups();
        
        // FIXED: Use the permissions array directly (already decoded by cast)
        $currentPermissions = $role->permissions ?? [];
        
        return view('roles.edit', compact('role', 'permissions', 'currentPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
            'is_default' => 'sometimes|boolean'
        ]);

        $oldValues = $role->toArray();
        
        $role->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'permissions' => $request->permissions, // Already an array
            'is_default' => $request->has('is_default') ? 1 : 0
        ]);

        $newValues = $role->fresh()->toArray();

        // Log role update
        AuditLog::create([
            'action' => 'updated',
            'model_type' => Role::class,
            'model_id' => $role->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => "Role {$role->name} was updated",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_default) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete default role.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that has assigned users.');
        }

        $oldValues = $role->toArray();
        $role->delete();

        // Log role deletion
        AuditLog::create([
            'action' => 'deleted',
            'model_type' => Role::class,
            'model_id' => $role->id,
            'old_values' => $oldValues,
            'description' => "Role {$oldValues['name']} was deleted",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    private function getPermissionGroups()
    {
        return [
            'Dashboard' => [
                'dashboard' => 'Access Dashboard'
            ],
            'Orders' => [
                'orders.*' => 'All Order Permissions',
                'orders.create' => 'Create Orders',
                'orders.edit' => 'Edit Orders',
                'orders.delete' => 'Delete Orders',
                'orders.view' => 'View Orders'
            ],
            'Inventory' => [
                'inventory.*' => 'All Inventory Permissions',
                'inventory.create' => 'Create Inventory Items',
                'inventory.edit' => 'Edit Inventory Items',
                'inventory.delete' => 'Delete Inventory Items',
                'inventory.view' => 'View Inventory'
            ],
            'Employees' => [
                'employees.*' => 'All Employee Permissions',
                'employees.create' => 'Create Employees',
                'employees.edit' => 'Edit Employees',
                'employees.delete' => 'Delete Employees',
                'employees.view' => 'View Employees'
            ],
            'Suppliers' => [
                'suppliers.*' => 'All Supplier Permissions',
                'suppliers.create' => 'Create Suppliers',
                'suppliers.edit' => 'Edit Suppliers',
                'suppliers.delete' => 'Delete Suppliers',
                'suppliers.view' => 'View Suppliers'
            ],
            'Billing' => [
                'billing.*' => 'All Billing Permissions',
                'billing.create' => 'Process Payments',
                'billing.edit' => 'Edit Payments',
                'billing.view' => 'View Billing'
            ],
            'Users' => [
                'users.*' => 'All User Permissions',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
                'users.view' => 'View Users'
            ],
            'Roles' => [
                'roles.*' => 'All Role Permissions',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles',
                'roles.view' => 'View Roles'
            ],
            'Audit Logs' => [
                'audit-logs.*' => 'All Audit Log Permissions',
                'audit-logs.view' => 'View Audit Logs'
            ],
            'Reports' => [
                'reports.*' => 'All Report Permissions',
                'reports.generate' => 'Generate Reports'
            ],
        ];
    }
}