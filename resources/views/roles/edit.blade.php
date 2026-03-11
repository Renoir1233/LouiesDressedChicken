<!-- resources/views/roles/edit.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-user-shield me-2 text-accent"></i>Edit Role: {{ $role->name }}</h4>
        <a href="{{ route('roles.index') }}" class="btn btn-delete">
            <i class="fas fa-arrow-left me-2"></i>Back to Roles
        </a>
    </div>
    
    <div class="card-body">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="section-header"><i class="fas fa-info-circle me-2 text-accent"></i>Role Information</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $role->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               name="slug" value="{{ old('slug', $role->slug) }}" required>
                        <small class="text-muted">Lowercase, hyphen-separated unique identifier</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" 
                                   id="is_default" value="1" {{ old('is_default', $role->is_default) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                Set as default role for new users
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-header"><i class="fas fa-key me-2 text-accent"></i>Permissions</h6>
                    <p class="text-muted mb-3">Select the permissions for this role.</p>
                    
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input group-checkbox" type="checkbox" 
                                           id="group_{{ $group }}" data-group="{{ $group }}">
                                    <label class="form-check-label fw-bold" for="group_{{ $group }}">
                                        {{ $group }}
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach($groupPermissions as $permission => $description)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input permission-checkbox" 
                                               type="checkbox" name="permissions[]" 
                                               id="permission_{{ $permission }}" 
                                               value="{{ $permission }}" 
                                               data-group="{{ $group }}"
                                               {{ in_array($permission, $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission }}">
                                            <strong>{{ $permission }}</strong><br>
                                            <small class="text-muted">{{ $description }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    @error('permissions')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary me-3 px-4">
                        <i class="fas fa-save me-2"></i>Update Role
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-delete px-4">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Group checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const groupCheckboxes = document.querySelectorAll('.group-checkbox');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        
        // Handle group checkbox click
        groupCheckboxes.forEach(groupCheckbox => {
            groupCheckbox.addEventListener('change', function() {
                const group = this.dataset.group;
                const groupPermissions = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
                
                groupPermissions.forEach(permissionCheckbox => {
                    permissionCheckbox.checked = this.checked;
                });
                
                updateGroupCheckbox(group);
            });
        });
        
        // Handle permission checkbox click
        permissionCheckboxes.forEach(permissionCheckbox => {
            permissionCheckbox.addEventListener('change', function() {
                const group = this.dataset.group;
                updateGroupCheckbox(group);
            });
        });
        
        function updateGroupCheckbox(group) {
            const groupCheckbox = document.querySelector(`.group-checkbox[data-group="${group}"]`);
            const groupPermissions = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]:checked`);
            
            if (groupPermissions.length === 0) {
                groupCheckbox.checked = false;
                groupCheckbox.indeterminate = false;
            } else if (groupPermissions.length === document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).length) {
                groupCheckbox.checked = true;
                groupCheckbox.indeterminate = false;
            } else {
                groupCheckbox.checked = false;
                groupCheckbox.indeterminate = true;
            }
        }
        
        // Initialize group checkboxes
        groupCheckboxes.forEach(groupCheckbox => {
            const group = groupCheckbox.dataset.group;
            updateGroupCheckbox(group);
        });
    });
</script>
@endsection