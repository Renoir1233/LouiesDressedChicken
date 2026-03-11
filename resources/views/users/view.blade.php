<!-- resources/views/users/show.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-user me-2 text-accent"></i>User Details: {{ $user->name }}</h4>
        <div>
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-edit me-2">
                <i class="fas fa-edit me-2"></i>Edit User
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-delete">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <!-- User Profile Card -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="user-avatar-lg mb-3">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                 class="rounded-circle border border-4 border-accent" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-3">{{ $user->email }}</p>
                        
                        <div class="d-flex justify-content-center gap-3 mb-4">
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} fs-6 px-3 py-2">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="badge bg-dark fs-6 px-3 py-2">{{ $user->role_name }}</span>
                        </div>
                        
                        <div class="row text-start">
                            <div class="col-6">
                                <p class="mb-1"><strong>User ID:</strong></p>
                                <p class="text-muted">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><strong>Member Since:</strong></p>
                                <p class="text-muted">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2 text-accent"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($user->id !== auth()->id())
                                @if($user->trashed())
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-grid">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-undo me-2"></i>Restore User
                                        </button>
                                    </form>
                                    <form action="{{ route('users.force-delete', $user->id) }}" method="POST" 
                                          class="d-grid" onsubmit="return confirm('Permanently delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash-alt me-2"></i>Delete Permanently
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
                                          class="d-grid" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash me-2"></i>Delete User
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('profile') }}" class="btn btn-primary">
                                    <i class="fas fa-user-edit me-2"></i>Edit My Profile
                                </a>
                            @endif
                            <a href="{{ route('audit-logs.index') }}?user_id={{ $user->id }}" class="btn btn-info">
                                <i class="fas fa-history me-2"></i>View Activity Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- User Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-accent"></i>User Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Full Name</strong></label>
                                <p class="form-control-plaintext">{{ $user->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Email Address</strong></label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Role</strong></label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-dark">{{ $user->role_name }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Account Status</strong></label>
                                <p class="form-control-plaintext">
                                    @if($user->trashed())
                                        <span class="badge bg-danger">Deleted</span>
                                    @elseif($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Created At</strong></label>
                                <p class="form-control-plaintext">
                                    {{ $user->created_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Last Updated</strong></label>
                                <p class="form-control-plaintext">
                                    {{ $user->updated_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            @if($user->trashed())
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Deleted At</strong></label>
                                    <p class="form-control-plaintext text-danger">
                                        {{ $user->deleted_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-history me-2 text-accent"></i>Recent Activity</h6>
                        <a href="{{ route('audit-logs.index') }}?user_id={{ $user->id }}" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            $recentActivities = $user->auditLogs()
                                ->latest()
                                ->limit(10)
                                ->get();
                        @endphp
                        
                        @if($recentActivities->count() > 0)
                            <div class="timeline">
                                @foreach($recentActivities as $activity)
                                    <div class="timeline-item mb-3">
                                        <div class="timeline-marker bg-{{ $activity->action_color }}"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">{{ $activity->description }}</h6>
                                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="text-muted mb-0">
                                                <small>
                                                    <i class="fas fa-{{ $activity->action == 'login' ? 'sign-in-alt' : ($activity->action == 'logout' ? 'sign-out-alt' : 'edit') }} me-1"></i>
                                                    {{ ucfirst($activity->action) }}
                                                    @if($activity->ip_address)
                                                        • IP: {{ $activity->ip_address }}
                                                    @endif
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-history fa-3x mb-3 d-block text-accent"></i>
                                No recent activity found for this user.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .user-avatar-lg {
        position: relative;
        display: inline-block;
    }
    .user-avatar-lg::after {
        content: '';
        position: absolute;
        top: -8px;
        left: -8px;
        right: -8px;
        bottom: -8px;
        border: 2px solid var(--accent-color);
        border-radius: 50%;
        opacity: 0.3;
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 10px;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .timeline-content {
        padding-left: 10px;
        border-left: 2px solid #e9ecef;
    }
</style>
@endsection