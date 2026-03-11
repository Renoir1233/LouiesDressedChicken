<!-- resources/views/inventory/create.blade.php -->

@extends('layouts.inventory')

@section('title', 'Add New Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Add New Product to Inventory</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control @error('product_name') is-invalid @enderror" 
                               id="product_name" name="product_name" value="{{ old('product_name') }}" required>
                        @error('product_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="product_code" class="form-label">Product Code *</label>
                        <input type="text" class="form-control @error('product_code') is-invalid @enderror" 
                               id="product_code" name="product_code" value="{{ old('product_code') }}" required>
                        <small class="form-text text-muted">Leave blank to auto-generate</small>
                        @error('product_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Image Upload Field -->
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                <div class="form-text">Upload a product image (JPEG, PNG, JPG, GIF, max 2MB)</div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                <!-- Image Preview -->
                <div id="imagePreview" class="mt-2" style="display: none;">
                    <p>Preview:</p>
                    <img id="previewImage" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 4px; border: 1px solid #dee2e6;">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier *</label>
                        <select class="form-control @error('supplier_id') is-invalid @enderror" 
                                id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category" class="form-label">Category *</label>
                        <select class="form-control @error('category') is-invalid @enderror" 
                                id="category" name="category" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="cost_price" class="form-label">Cost Price (₱) *</label>
                        <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                               id="cost_price" name="cost_price" value="{{ old('cost_price') }}" required min="0">
                        @error('cost_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="selling_price" class="form-label">Selling Price (₱) *</label>
                        <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" 
                               id="selling_price" name="selling_price" value="{{ old('selling_price') }}" required min="0">
                        @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit *</label>
                        <select class="form-control @error('unit') is-invalid @enderror" 
                                id="unit" name="unit" required>
                            <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pieces</option>
                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilograms</option>
                            <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>Grams</option>
                            <option value="l" {{ old('unit') == 'l' ? 'selected' : '' }}>Liters</option>
                            <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Packs</option>
                            <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Boxes</option>
                        </select>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="low_stock_alert" class="form-label">Low Stock Alert Level *</label>
                        <input type="number" class="form-control @error('low_stock_alert') is-invalid @enderror" 
                               id="low_stock_alert" name="low_stock_alert" value="{{ old('low_stock_alert', 10) }}" required min="0">
                        <small class="form-text text-muted">System will alert when stock reaches this level</small>
                        @error('low_stock_alert')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Active/Inactive Status Toggle -->
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        <strong>Product is Active</strong>
                    </label>
                    <small class="form-text text-muted d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Active products appear in inventory listings and stock alerts. Inactive products are hidden from most views.
                    </small>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const costPriceInput = document.getElementById('cost_price');
    const sellingPriceInput = document.getElementById('selling_price');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    
    // Image preview functionality
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });
    
    function validatePrices() {
        const costPrice = parseFloat(costPriceInput.value) || 0;
        const sellingPrice = parseFloat(sellingPriceInput.value) || 0;
        
        if (costPrice > 0 && sellingPrice > 0) {
            if (sellingPrice <= costPrice) {
                sellingPriceInput.classList.add('is-invalid');
                sellingPriceInput.nextElementSibling?.remove();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Selling price must be greater than cost price.';
                sellingPriceInput.parentNode.appendChild(errorDiv);
                return false;
            } else {
                sellingPriceInput.classList.remove('is-invalid');
                sellingPriceInput.nextElementSibling?.remove();
                return true;
            }
        }
        return true;
    }
    
    // Auto-calculate selling price with 30% markup
    costPriceInput.addEventListener('input', function() {
        const costPrice = parseFloat(this.value) || 0;
        
        if (costPrice > 0 && (!sellingPriceInput.value || sellingPriceInput.value <= costPrice)) {
            const sellingPrice = costPrice * 1.3; // 30% markup
            sellingPriceInput.value = sellingPrice.toFixed(2);
        }
        
        validatePrices();
    });
    
    sellingPriceInput.addEventListener('input', validatePrices);
    
    // Form submission validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!validatePrices()) {
            e.preventDefault();
            alert('Please fix the price validation errors before submitting.');
        }
    });

    // Style the form switch
    const statusSwitch = document.getElementById('is_active');
    statusSwitch.addEventListener('change', function() {
        if (this.checked) {
            this.parentNode.classList.add('text-success');
            this.parentNode.classList.remove('text-muted');
        } else {
            this.parentNode.classList.add('text-muted');
            this.parentNode.classList.remove('text-success');
        }
    });

    // Initialize switch style
    if (statusSwitch.checked) {
        statusSwitch.parentNode.classList.add('text-success');
    } else {
        statusSwitch.parentNode.classList.add('text-muted');
    }
});
</script>

<style>
.form-switch .form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}
.form-switch .form-check-input:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>
@endsection