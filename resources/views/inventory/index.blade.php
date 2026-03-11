<!-- resources/views/inventory/index.blade.php -->

@extends('layouts.inventory')

@section('title', 'Inventory Management')

@section('content')
<!-- Alert Summary -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background-color: #FFF3E0;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #F57C00;">{{ $lowStockCount }}</h2>
                        <small class="d-block" style="color: #F57C00;">Low Stock Items</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-exclamation-triangle fa-lg" style="color: #FF9800;"></i>
                    </div>
                </div>
                @if($lowStockCount > 0)
                <a href="{{ route('inventory.low-stock') }}" class="small mt-2 d-block" style="color: #F57C00;">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background-color: #FFEBEE;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #C62828;">{{ $outOfStockCount }}</h2>
                        <small class="d-block" style="color: #C62828;">Out of Stock</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-times-circle fa-lg" style="color: #f44336;"></i>
                    </div>
                </div>
                @if($outOfStockCount > 0)
                <a href="{{ route('inventory.out-of-stock') }}" class="small mt-2 d-block" style="color: #C62828;">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background-color: #E3F2FD;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #1976D2;">{{ $inventory->total() }}</h2>
                        <small class="d-block" style="color: #1976D2;">Total Products</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-boxes fa-lg" style="color: #2196F3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #2c3e50;">Inventory Management</h5>
        <div>
            @if($lowStockCount > 0)
            <a href="{{ route('inventory.low-stock') }}" class="btn btn-sm btn-outline-warning me-2">
                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock ({{ $lowStockCount }})
            </a>
            @endif
            @if($outOfStockCount > 0)
            <a href="{{ route('inventory.out-of-stock') }}" class="btn btn-sm btn-outline-danger me-2">
                <i class="fas fa-times-circle me-2"></i>Out of Stock ({{ $outOfStockCount }})
            </a>
            @endif
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                <i class="fas fa-plus me-2"></i>Add New Product
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($inventory->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Supplier</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Quantity</th>
                            <th>Stock Status</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory as $item)
                        <tr>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->product_name }}" 
                                         class="product-image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div class="no-image-placeholder" style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td><strong>{{ $item->product_code }}</strong></td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->supplier->name }}</td>
                            <td>₱{{ number_format($item->cost_price, 2) }}</td>
                            <td>₱{{ number_format($item->selling_price, 2) }}</td>
                            <td>
                                <span class="fw-bold {{ $item->quantity <= $item->low_stock_alert ? 'text-warning' : '' }} {{ $item->quantity == 0 ? 'text-danger' : '' }}">
                                    {{ number_format($item->quantity, 2) }} {{ $item->unit }}
                                </span>
                            </td>
                            <td>
                                @if($item->quantity == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($item->quantity <= $item->low_stock_alert)
                                    <span class="badge bg-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success">In Stock</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-primary me-1 view-product-btn" 
                                            data-id="{{ $item->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewProductModal"
                                            title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-edit me-1 edit-product-btn" 
                                            data-id="{{ $item->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editProductModal"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                       
                                        
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $inventory->links('pagination.bootstrap-4') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products in inventory</h5>
                <p class="text-muted">Get started by adding your first product.</p>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createProductModal">
                    <i class="fas fa-plus me-2"></i>Add New Product
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createProductForm" action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_product_name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="create_product_name" name="product_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_product_code" class="form-label">Product Code</label>
                                <input type="text" class="form-control" id="create_product_code" name="product_code">
                                <small class="form-text text-muted">Leave blank to auto-generate</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_description" class="form-label">Description</label>
                        <textarea class="form-control" id="create_description" name="description" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="create_image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="create_image" name="image" accept="image/*">
                        <div class="form-text">Max 2MB (JPEG, PNG, JPG, GIF)</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_supplier_id" class="form-label">Supplier *</label>
                                <select class="form-control" id="create_supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_category" class="form-label">Category *</label>
                                <select class="form-control" id="create_category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="create_cost_price" class="form-label">Cost Price (₱) *</label>
                                <input type="number" step="0.01" class="form-control" id="create_cost_price" name="cost_price" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="create_selling_price" class="form-label">Selling Price (₱) *</label>
                                <input type="number" step="0.01" class="form-control" id="create_selling_price" name="selling_price" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="create_unit" class="form-label">Unit *</label>
                                <select class="form-control" id="create_unit" name="unit" required>
                                    <option value="pcs">Pieces</option>
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                    <option value="l">Liters</option>
                                    <option value="pack">Packs</option>
                                    <option value="box">Boxes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_low_stock_alert" class="form-label">Low Stock Alert Level *</label>
                        <input type="number" class="form-control" id="create_low_stock_alert" name="low_stock_alert" value="10" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editProductForm" method="POST" enctype="multipart/form-data" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_product_name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_product_code" class="form-label">Product Code *</label>
                                <input type="text" class="form-control" id="edit_product_code" name="product_code" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <div class="form-text">Max 2MB (JPEG, PNG, JPG, GIF)</div>
                        <div id="current_image_preview" class="mt-2"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_supplier_id" class="form-label">Supplier *</label>
                                <select class="form-control" id="edit_supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_category" class="form-label">Category *</label>
                                <select class="form-control" id="edit_category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_cost_price" class="form-label">Cost Price (₱) *</label>
                                <input type="number" step="0.01" class="form-control" id="edit_cost_price" name="cost_price" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_selling_price" class="form-label">Selling Price (₱) *</label>
                                <input type="number" step="0.01" class="form-control" id="edit_selling_price" name="selling_price" required min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_unit" class="form-label">Unit *</label>
                                <select class="form-control" id="edit_unit" name="unit" required>
                                    <option value="pcs">Pieces</option>
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                    <option value="l">Liters</option>
                                    <option value="pack">Packs</option>
                                    <option value="box">Boxes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Current Quantity</label>
                                <input type="text" class="form-control" id="edit_quantity_display" readonly>
                                <small class="form-text text-muted">Use Stock In/Out pages to adjust</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_low_stock_alert" class="form-label">Low Stock Alert Level *</label>
                                <input type="number" class="form-control" id="edit_low_stock_alert" name="low_stock_alert" required min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Product is Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProductModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div id="view_product_image"></div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="35%">Product Code</th>
                                <td id="view_product_code"></td>
                            </tr>
                            <tr>
                                <th>Product Name</th>
                                <td id="view_product_name"></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td id="view_description"></td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td id="view_supplier"></td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td id="view_category"></td>
                            </tr>
                            <tr>
                                <th>Cost Price</th>
                                <td id="view_cost_price"></td>
                            </tr>
                            <tr>
                                <th>Selling Price</th>
                                <td id="view_selling_price"></td>
                            </tr>
                            <tr>
                                <th>Profit Margin</th>
                                <td id="view_profit_margin"></td>
                            </tr>
                            <tr>
                                <th>Current Stock</th>
                                <td id="view_quantity"></td>
                            </tr>
                            <tr>
                                <th>Low Stock Alert</th>
                                <td id="view_low_stock_alert"></td>
                            </tr>
                            <tr>
                                <th>Stock Status</th>
                                <td id="view_stock_status"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="view_is_active"></td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td id="view_created_at"></td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td id="view_updated_at"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle View Product Button Click
    const viewButtons = document.querySelectorAll('.view-product-btn');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            
            // Fetch product data
            fetch(`/inventory/${productId}/get-product`)
                .then(response => response.json())
                .then(product => {
                    // Populate product image
                    const imageDiv = document.getElementById('view_product_image');
                    if (product.image) {
                        imageDiv.innerHTML = `<img src="/storage/${product.image}" alt="${product.product_name}" class="img-fluid rounded" style="max-height: 250px; border: 1px solid #dee2e6;">`;
                    } else {
                        imageDiv.innerHTML = `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px; border: 2px dashed #dee2e6;"><div class="text-center text-muted"><i class="fas fa-box fa-3x mb-2"></i><p class="mb-0">No Image</p></div></div>`;
                    }
                    
                    // Populate product details
                    document.getElementById('view_product_code').innerHTML = `<strong>${product.product_code}</strong>`;
                    document.getElementById('view_product_name').textContent = product.product_name;
                    document.getElementById('view_description').textContent = product.description || 'N/A';
                    document.getElementById('view_supplier').textContent = product.supplier ? product.supplier.name : 'Unknown';
                    document.getElementById('view_category').innerHTML = `<span class="badge bg-secondary">${product.category}</span>`;
                    document.getElementById('view_cost_price').textContent = '₱' + parseFloat(product.cost_price).toFixed(2);
                    document.getElementById('view_selling_price').textContent = '₱' + parseFloat(product.selling_price).toFixed(2);
                    
                    // Calculate profit margin
                    const profitMargin = ((product.selling_price - product.cost_price) / product.cost_price * 100).toFixed(2);
                    const marginClass = profitMargin > 0 ? 'text-success' : 'text-danger';
                    document.getElementById('view_profit_margin').innerHTML = `<span class="fw-bold ${marginClass}">${profitMargin}%</span>`;
                    
                    // Stock quantity
                    let qtyClass = '';
                    if (product.quantity == 0) qtyClass = 'text-danger';
                    else if (product.quantity <= product.low_stock_alert) qtyClass = 'text-warning';
                    document.getElementById('view_quantity').innerHTML = `<span class="fw-bold ${qtyClass}">${product.quantity} ${product.unit}</span>`;
                    document.getElementById('view_low_stock_alert').textContent = `${product.low_stock_alert} ${product.unit}`;
                    
                    // Stock status badge
                    let stockBadge = '';
                    if (product.quantity == 0) {
                        stockBadge = '<span class="badge bg-danger">Out of Stock</span>';
                    } else if (product.quantity <= product.low_stock_alert) {
                        stockBadge = '<span class="badge bg-warning">Low Stock</span>';
                    } else {
                        stockBadge = '<span class="badge bg-success">In Stock</span>';
                    }
                    document.getElementById('view_stock_status').innerHTML = stockBadge;
                    
                    // Active status
                    const activeBadge = product.is_active == 1 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-secondary">Inactive</span>';
                    document.getElementById('view_is_active').innerHTML = activeBadge;
                    
                    // Dates
                    document.getElementById('view_created_at').textContent = new Date(product.created_at).toLocaleString();
                    document.getElementById('view_updated_at').textContent = new Date(product.updated_at).toLocaleString();
                })
                .catch(error => {
                    console.error('Error fetching product:', error);
                    alert('Error loading product details');
                });
        });
    });

    // Handle Edit Product Button Click
    const editButtons = document.querySelectorAll('.edit-product-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            console.log('Edit button clicked for product:', productId);
            
            // Fetch product data
            fetch(`/inventory/${productId}/get-product`)
                .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch product');
                    return response.json();
                })
                .then(product => {
                    console.log('Product data loaded:', product);
                    
                    // Populate the edit form
                    document.getElementById('edit_product_name').value = product.product_name || '';
                    document.getElementById('edit_product_code').value = product.product_code || '';
                    document.getElementById('edit_description').value = product.description || '';
                    document.getElementById('edit_supplier_id').value = product.supplier_id || '';
                    document.getElementById('edit_category').value = product.category || '';
                    document.getElementById('edit_cost_price').value = parseFloat(product.cost_price) || '';
                    document.getElementById('edit_selling_price').value = parseFloat(product.selling_price) || '';
                    document.getElementById('edit_unit').value = product.unit || 'pcs';
                    document.getElementById('edit_quantity_display').value = (parseFloat(product.quantity) || 0).toFixed(2) + ' ' + (product.unit || 'pcs');
                    document.getElementById('edit_low_stock_alert').value = parseFloat(product.low_stock_alert) || 0;
                    
                    // Set is_active checkbox
                    const isActive = product.is_active == 1 || product.is_active === true;
                    document.getElementById('edit_is_active').checked = isActive;
                    
                    // Show current image if exists
                    const imagePreview = document.getElementById('current_image_preview');
                    if (product.image) {
                        imagePreview.innerHTML = `
                            <p class="mb-1">Current Image:</p>
                            <img src="/storage/${product.image}" alt="Current" style="max-width: 150px; border-radius: 4px;">
                        `;
                    } else {
                        imagePreview.innerHTML = '<p class="text-muted">No image</p>';
                    }
                    
                    // Set form action to proper route
                    const form = document.getElementById('editProductForm');
                    form.action = `/inventory/${productId}`;
                    console.log('Form action set to:', form.action);
                    console.log('Form method:', form.method);
                })
                .catch(error => {
                    console.error('Error fetching product:', error);
                    alert('Error loading product data: ' + error.message);
                });
        });
    });

    // Auto-calculate selling price for create form
    const createCostPrice = document.getElementById('create_cost_price');
    const createSellingPrice = document.getElementById('create_selling_price');
    
    createCostPrice.addEventListener('input', function() {
        const costPrice = parseFloat(this.value) || 0;
        if (costPrice > 0 && !createSellingPrice.value) {
            createSellingPrice.value = (costPrice * 1.3).toFixed(2);
        }
    });

    // Reset create form when modal closes
    const createModal = document.getElementById('createProductModal');
    createModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('createProductForm').reset();
    });

    // Reset edit form when modal closes
     const editModal = document.getElementById('editProductModal');
     editModal.addEventListener('hidden.bs.modal', function () {
         document.getElementById('editProductForm').reset();
         document.getElementById('current_image_preview').innerHTML = '';
     });

     // Add price validation to edit form
     const editForm = document.getElementById('editProductForm');
     const editCostPrice = document.getElementById('edit_cost_price');
     const editSellingPrice = document.getElementById('edit_selling_price');

     function validateEditPrices() {
         const costPrice = parseFloat(editCostPrice.value) || 0;
         const sellingPrice = parseFloat(editSellingPrice.value) || 0;

         if (costPrice > 0 && sellingPrice > 0) {
             if (sellingPrice <= costPrice) {
                 editSellingPrice.classList.add('is-invalid');
                 editSellingPrice.nextElementSibling?.remove();
                 const errorDiv = document.createElement('div');
                 errorDiv.className = 'invalid-feedback';
                 errorDiv.textContent = 'Selling price must be greater than cost price.';
                 editSellingPrice.parentNode.appendChild(errorDiv);
                 return false;
             } else {
                 editSellingPrice.classList.remove('is-invalid');
                 editSellingPrice.nextElementSibling?.remove();
                 return true;
             }
         }
         return true;
     }

     editCostPrice.addEventListener('input', validateEditPrices);
     editSellingPrice.addEventListener('input', validateEditPrices);

     editForm.addEventListener('submit', function(e) {
         if (!validateEditPrices()) {
             e.preventDefault();
             alert('Please fix the price validation errors before submitting.');
         }
     });
     });
     </script>

<style>
.product-image {
    border: 1px solid #dee2e6;
    transition: transform 0.2s;
}

.product-image:hover {
    transform: scale(1.1);
}

.no-image-placeholder {
    border: 1px dashed rgba(0, 0, 0, 1)
}
</style>
@endsection