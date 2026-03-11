<!-- resources/views/employees/create.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0"><i class="fas fa-user-plus me-2 text-accent"></i>Register Employee</h4>
    </div>
    
    <div class="card-body">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="section-header"><i class="fas fa-user-circle me-2 text-accent"></i>Add Employee Information</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Employee Name:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required placeholder="Enter employee name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required placeholder="Enter email address">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Position:</label>
                        <select class="form-control @error('position') is-invalid @enderror" 
                                name="position" required>
                            <option value="">Select Position</option>
                            <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Cashier" {{ old('position') == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                            <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                            <option value="Supervisor" {{ old('position') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="Assistant" {{ old('position') == 'Assistant' ? 'selected' : '' }}>Assistant</option>
                        </select>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="section-header"><i class="fas fa-info-circle me-2 text-accent"></i>Additional Details</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Address:</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" rows="3" required placeholder="Enter complete address">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact No:</label>
                        <input type="text" class="form-control @error('contact_no') is-invalid @enderror" 
                               name="contact_no" value="{{ old('contact_no') }}" required placeholder="Enter contact number">
                        @error('contact_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status:</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_active" value="Active" 
                                       {{ old('status', 'Active') == 'Active' ? 'checked' : '' }} required>
                                <label class="form-check-label fw-bold text-success" for="status_active">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_inactive" value="Inactive"
                                       {{ old('status') == 'Inactive' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="status_inactive">
                                    <i class="fas fa-times-circle me-1"></i>Inactive
                                </label>
                            </div>
                        </div>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary me-3 px-4">
                        <i class="fas fa-save me-2"></i>Save Employee
                    </button>
                    <a href="{{ route('employees.index') }}" class="btn btn-delete px-4">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection