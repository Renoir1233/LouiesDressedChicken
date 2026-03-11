<!-- resources/views/employees/edit.blade.php -->
@extends('layouts.employees')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Edit Employee</h4>
    </div>
    
    <div class="card-body">
        <form action="{{ route('employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="mb-3">Edit Employee Information</h6>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Employee Name:</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $employee->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Email:</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', $employee->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Position:</label>
                            <select class="form-control @error('position') is-invalid @enderror" 
                                    name="position" required>
                                <option value="">Select Position</option>
                                <option value="Manager" {{ old('position', $employee->position) == 'Manager' ? 'selected' : '' }}>Manager</option>
                                <option value="Cashier" {{ old('position', $employee->position) == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                                <option value="Staff" {{ old('position', $employee->position) == 'Staff' ? 'selected' : '' }}>Staff</option>
                                <option value="Supervisor" {{ old('position', $employee->position) == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-3">&nbsp;</h6>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Address:</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      name="address" rows="2" required>{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Contact No:</label>
                            <input type="text" class="form-control @error('contact_no') is-invalid @enderror" 
                                   name="contact_no" value="{{ old('contact_no', $employee->contact_no) }}" required>
                            @error('contact_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Status:</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" 
                                           id="status_active" value="Active" 
                                           {{ old('status', $employee->status) == 'Active' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="status_active">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" 
                                           id="status_inactive" value="Inactive"
                                           {{ old('status', $employee->status) == 'Inactive' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_inactive">Inactive</label>
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 30px; font-size: 16px;">Update Employee</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary" style="padding: 10px 30px; font-size: 16px; margin-left: 10px;">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection