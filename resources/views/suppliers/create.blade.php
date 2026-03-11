

@extends('layouts.suppliers')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Add New Supplier</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Supplier Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact Number *</label>
                        <input type="text" class="form-control @error('contact') is-invalid @enderror" 
                               id="contact" name="contact" value="{{ old('contact') }}" required>
                        @error('contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address *</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="4" required>{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection