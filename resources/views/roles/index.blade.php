<!-- resources/views/roles/index.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-user-shield me-2 text-accent"></i>Role Management</h4>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Role
        </a>
    </div>
    
    <div class="card-body">
        <!-- Roles Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Default</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>
                            <strong>{{ $role->name }}</strong>
                        </td>
                        <td>
                            <code>{{ $role->slug }}</code>
                        </td>
                        <td>
                            {{ $role->description ?? 'No description' }}
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $role->users_count }}</span>
                        </td>
                        <td>
                            @if($role->is_default)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @php
                                // FIXED: Use the array directly since it's already cast
                                $permissions = $role->permissions ?? [];
                                $permissionCount = count($permissions);
                            @endphp
                            @if(in_array('*', $permissions))
                                <span class="badge bg-dark">All Permissions</span>
                            @else
                                <span class="badge bg-info">{{ $permissionCount }} permission(s)</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('roles.edit', $role->id) }}" 
                                   class="btn btn-edit btn-sm me-1">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                @if(!$role->is_default && $role->users_count == 0)
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Delete this role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete btn-sm">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-user-shield fa-3x mb-3 d-block text-accent"></i>
                            No roles found. <a href="{{ route('roles.create') }}" class="text-primary-custom">Create the first role</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Permission Groups Reference -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-key me-2 text-accent"></i>Permission Groups Reference</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Dashboard</h6>
                            </div>
                            <div class="card-body">
                                <small>• dashboard - Access dashboard</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Orders</h6>
                            </div>
                            <div class="card-body">
                                <small>• orders.* - All order permissions</small><br>
                                <small>• orders.create - Create orders</small><br>
                                <small>• orders.edit - Edit orders</small><br>
                                <small>• orders.delete - Delete orders</small><br>
                                <small>• orders.view - View orders</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">Inventory</h6>
                            </div>
                            <div class="card-body">
                                <small>• inventory.* - All inventory permissions</small><br>
                                <small>• inventory.create - Create items</small><br>
                                <small>• inventory.edit - Edit items</small><br>
                                <small>• inventory.delete - Delete items</small><br>
                                <small>• inventory.view - View inventory</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Users & Roles</h6>
                            </div>
                            <div class="card-body">
                                <small>• users.* - All user permissions</small><br>
                                <small>• roles.* - All role permissions</small><br>
                                <small>• audit-logs.* - All audit log permissions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">Reports</h6>
                            </div>
                            <div class="card-body">
                                <small>• reports.* - All report permissions</small><br>
                                <small>• reports.generate - Generate reports</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">Other Modules</h6>
                            </div>
                            <div class="card-body">
                                <small>• employees.* - Employee permissions</small><br>
                                <small>• suppliers.* - Supplier permissions</small><br>
                                <small>• billing.* - Billing permissions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection