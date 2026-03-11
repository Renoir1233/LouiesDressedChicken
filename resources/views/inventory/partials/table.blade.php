{{-- resources/views/inventory/partials/table.blade.php --}}
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
                            {{ $item->quantity }} {{ $item->unit }}
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
                            <button type="button" class="btn btn-sm btn-primary me-1 view-inventory-btn" 
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->product_name }}"
                                    title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-edit me-1 edit-inventory-btn" 
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->product_name }}"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-success me-1 stock-in-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#stockInModal" 
                                    data-product-id="{{ $item->id }}"
                                    data-product-name="{{ $item->product_name }}"
                                    data-current-quantity="{{ $item->quantity }}"
                                    data-unit="{{ $item->unit }}"
                                    title="Stock In">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning me-1 stock-out-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#stockOutModal" 
                                    data-product-id="{{ $item->id }}"
                                    data-product-name="{{ $item->product_name }}"
                                    data-current-quantity="{{ $item->quantity }}"
                                    data-unit="{{ $item->unit }}"
                                    title="Stock Out">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-info me-1 toggle-status-btn" 
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->product_name }}"
                                    data-status="{{ $item->is_active }}"
                                    title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-delete delete-inventory-btn" 
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->product_name }}"
                                    data-code="{{ $item->product_code }}"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $inventory->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No products in inventory</h5>
        <p class="text-muted">Get started by adding your first product.</p>
        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createInventoryModal">
            <i class="fas fa-plus me-2"></i>Add New Product
        </button>
    </div>
@endif