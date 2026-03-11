>

@extends('layouts.suppliers')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Supplier</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Supplier Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact Number *</label>
                        <input type="text" class="form-control @error('contact') is-invalid @enderror" 
                               id="contact" name="contact" value="{{ old('contact', $supplier->contact) }}" required>
                        @error('contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email', $supplier->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address *</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="4" required>{{ old('address', $supplier->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Active/Inactive Status Toggle -->
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        <strong>Supplier is Active</strong>
                    </label>
                    <small class="form-text text-muted d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Active suppliers can be selected when creating inventory items.
                    </small>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection