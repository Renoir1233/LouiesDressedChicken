<!-- resources/views/users/profile.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-user-circle me-2 text-accent"></i>My Profile</h4>
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-delete me-2">
                <i class="fas fa-users me-2"></i>All Users
            </a>
            <a href="{{ route('audit-logs.index') }}?user_id={{ $user->id }}" class="btn btn-primary">
                <i class="fas fa-history me-2"></i>My Activity
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <form action="{{ route('profile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-4">
                    <!-- Profile Picture -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="avatar-upload mb-3">
                                <div class="avatar-edit">
                                    <input type="file" id="avatar" name="avatar" accept=".png, .jpg, .jpeg" class="form-control">
                                    <label for="avatar" class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-camera me-1"></i>Change Photo
                                    </label>
                                </div>
                                <div class="avatar-preview">
                                    <div id="avatarPreview" style="background-image: url('{{ $user->avatar_url }}');">
                                    </div>
                                </div>
                            </div>
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-3">{{ $user->email }}</p>
                            
                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <span class="badge bg-success fs-6 px-3 py-2">
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

                    <!-- Account Statistics -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-accent"></i>Account Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @php
                                    $userStats = [
                                        'Orders Created' => $user->orders()->count(),
                                        'Inventory Items' => $user->inventory()->count(),
                                        'Suppliers Added' => $user->suppliers()->count(),
                                        'Employees Added' => $user->employees()->count(),
                                        'Payments Processed' => $user->payments()->count(),
                                        'Audit Logs' => $user->auditLogs()->count(),
                                    ];
                                @endphp
                                @foreach($userStats as $label => $count)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $label }}
                                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-user-edit me-2 text-accent"></i>Edit Profile Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="{{ $user->role_name }}" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Account Status</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $user->is_active ? 'Active' : 'Inactive' }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-lock me-2 text-accent"></i>Change Password</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Leave password fields blank if you don't want to change your password.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           name="current_password" placeholder="Enter current password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" placeholder="Enter new password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" 
                                           name="password_confirmation" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-history me-2 text-accent"></i>My Recent Activity</h6>
                            <a href="{{ route('audit-logs.index') }}?user_id={{ $user->id }}" class="btn btn-sm btn-primary">
                                View All
                            </a>
                        </div>
                        <div class="card-body">
                            @php
                                $recentActivities = $user->auditLogs()
                                    ->latest()
                                    ->limit(5)
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
                                    No recent activity found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary me-3 px-4">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                    <button type="reset" class="btn btn-delete px-4">
                        <i class="fas fa-undo me-2"></i>Reset Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .avatar-upload {
        position: relative;
        max-width: 200px;
        margin: 0 auto;
    }
    .avatar-edit {
        position: absolute;
        right: -10px;
        top: -10px;
        z-index: 1;
    }
    .avatar-edit input {
        display: none;
    }
    .avatar-preview {
        width: 200px;
        height: 200px;
        position: relative;
        border-radius: 50%;
        border: 5px solid var(--accent-color);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .avatar-preview > div {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
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

<script>
    document.getElementById('avatar').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').style.backgroundImage = `url(${e.target.result})`;
        }
        reader.readAsDataURL(e.target.files[0]);
    });
</script>
@endsection