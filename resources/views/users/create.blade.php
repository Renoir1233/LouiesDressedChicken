<!-- resources/views/users/create.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-user-plus me-2 text-accent"></i>Create New User</h4>
        <a href="{{ route('users.index') }}" class="btn btn-delete">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>
    
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="section-header"><i class="fas fa-user-circle me-2 text-accent"></i>Basic Information</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required placeholder="Enter full name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required placeholder="Enter email address">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required placeholder="Enter password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   name="password_confirmation" required placeholder="Confirm password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-control @error('role') is-invalid @enderror" 
                                name="role" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->slug }}" {{ old('role') == $role->slug ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-header"><i class="fas fa-info-circle me-2 text-accent"></i>Additional Details</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <div class="avatar-upload mb-3">
                            <div class="avatar-edit">
                                <input type="file" id="avatar" name="avatar" accept=".png, .jpg, .jpeg" class="form-control">
                                <label for="avatar" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-camera me-1"></i>Upload Photo
                                </label>
                            </div>
                            <div class="avatar-preview">
                                <div id="avatarPreview" style="background-image: url('https://ui-avatars.com/api/?name=New+User&color=FFFFFF&background=666B05&size=150');">
                                </div>
                            </div>
                        </div>
                        @error('avatar')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Status</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_active" 
                                       id="status_active" value="1" 
                                       {{ old('is_active', '1') == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label fw-bold text-success" for="status_active">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_active" 
                                       id="status_inactive" value="0"
                                       {{ old('is_active') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="status_inactive">
                                    <i class="fas fa-times-circle me-1"></i>Inactive
                                </label>
                            </div>
                        </div>
                        @error('is_active')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Password Requirements:</strong> Minimum 8 characters with at least one uppercase letter, one lowercase letter, and one number.
                    </div>
                </div>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary me-3 px-4">
                        <i class="fas fa-save me-2"></i>Create User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-delete px-4">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .avatar-upload {
        position: relative;
        max-width: 150px;
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
        width: 150px;
        height: 150px;
        position: relative;
        border-radius: 50%;
        border: 4px solid var(--accent-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .avatar-preview > div {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
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