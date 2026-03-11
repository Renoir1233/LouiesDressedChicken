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
                                   id="passwordInput" name="password" required placeholder="Enter password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <!-- Password strength indicator -->
                            <div id="passwordStrength" class="mt-2" style="display:none;">
                                <div class="progress" style="height:6px;">
                                    <div id="strengthBar" class="progress-bar" role="progressbar" style="width:0%"></div>
                                </div>
                                <ul class="list-unstyled small mt-2 mb-0" id="passwordChecks">
                                    <li id="check-length"><i class="fas fa-times-circle text-danger me-1"></i>At least 8 characters</li>
                                    <li id="check-upper"><i class="fas fa-times-circle text-danger me-1"></i>At least 1 uppercase letter</li>
                                    <li id="check-lower"><i class="fas fa-times-circle text-danger me-1"></i>At least 1 lowercase letter</li>
                                    <li id="check-number"><i class="fas fa-times-circle text-danger me-1"></i>At least 1 number</li>
                                    <li id="check-special"><i class="fas fa-times-circle text-danger me-1"></i>At least 1 special character</li>
                                </ul>
                            </div>
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
                        <strong>Password Requirements:</strong> Minimum 8 characters with at least one uppercase letter, one lowercase letter, one number, and one special character (e.g. !@#$%).
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

    // Password strength checker
    const passwordInput = document.getElementById('passwordInput');
    const strengthDiv = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');

    const checks = {
        length:  { el: document.getElementById('check-length'),  test: v => v.length >= 8 },
        upper:   { el: document.getElementById('check-upper'),   test: v => /[A-Z]/.test(v) },
        lower:   { el: document.getElementById('check-lower'),   test: v => /[a-z]/.test(v) },
        number:  { el: document.getElementById('check-number'),  test: v => /[0-9]/.test(v) },
        special: { el: document.getElementById('check-special'), test: v => /[^A-Za-z0-9]/.test(v) },
    };

    passwordInput.addEventListener('input', function () {
        const val = this.value;
        strengthDiv.style.display = val.length > 0 ? 'block' : 'none';

        let passed = 0;
        for (const key in checks) {
            const ok = checks[key].test(val);
            if (ok) passed++;
            checks[key].el.innerHTML = ok
                ? `<i class="fas fa-check-circle text-success me-1"></i>${checks[key].el.textContent.trim()}`
                : `<i class="fas fa-times-circle text-danger me-1"></i>${checks[key].el.textContent.trim()}`;
        }

        const pct = (passed / 5) * 100;
        const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];
        strengthBar.style.width = pct + '%';
        strengthBar.style.backgroundColor = colors[passed - 1] || '#dc3545';
    });
</script>
@endsection