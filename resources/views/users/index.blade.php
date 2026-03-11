<!-- resources/views/users/index.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-users me-2 text-accent"></i>User Management</h4>
        <div>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-user-shield me-2"></i>Roles
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Add User
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box" 
                               placeholder="Search users..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="with_trashed" 
                               id="with_trashed" value="1" {{ request('with_trashed') ? 'checked' : '' }}>
                        <label class="form-check-label" for="with_trashed">
                            Show Deleted Users
                        </label>
                    </div>
                </div>
            </div>
        </form>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>2FA</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="{{ $user->trashed() ? 'table-danger' : '' }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                     class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->trashed())
                                        <span class="badge bg-danger ms-2">Deleted</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-dark">{{ $user->role_name }}</span>
                        </td>
                        <td>
                            @if($user->trashed())
                                <span class="status-inactive">Deleted</span>
                            @elseif($user->is_active)
                                <span class="status-active">Active</span>
                            @else
                                <span class="status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($user->trashed())
                                <span class="badge bg-secondary">N/A</span>
                            @elseif($user->two_factor_enabled)
                                <span class="badge bg-success"><i class="fas fa-shield-alt me-1"></i>Enabled</span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-shield-alt me-1"></i>Disabled</span>
                            @endif
                        </td>
                        <td>
                                    ->where('action', 'login')
                                    ->latest()
                                    ->first();
                            @endphp
                            @if($lastLogin)
                                {{ $lastLogin->created_at->diffForHumans() }}
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($user->trashed())
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm me-1">
                                            <i class="fas fa-undo me-1"></i>Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('users.force-delete', $user->id) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Permanently delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt me-1"></i>Delete
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('users.edit', $user->id) }}" 
                                       class="btn btn-edit btn-sm me-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    @if(auth()->user()->role === 'super-admin')
                                        <form action="{{ route('users.toggle-2fa', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm me-1 {{ $user->two_factor_enabled ? 'btn-warning' : 'btn-outline-success' }}"
                                                title="{{ $user->two_factor_enabled ? 'Disable 2FA' : 'Enable 2FA' }}"
                                                onclick="return confirm('{{ $user->two_factor_enabled ? 'Disable' : 'Enable' }} 2FA for {{ addslashes($user->name) }}?')">
                                                <i class="fas fa-shield-alt me-1"></i>{{ $user->two_factor_enabled ? 'Disable 2FA' : 'Enable 2FA' }}
                                            </button>
                                        </form>
                                    @endif
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('Archive this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-delete btn-sm">
                                                <i class="fas fa-archive me-1"></i>Archive
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block text-accent"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection