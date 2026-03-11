<!-- resources/views/inventory/out-of-stock.blade.php -->
@extends('layouts.inventory')

@section('title', 'Out of Stock Items')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-times-circle text-danger me-2"></i>Out of Stock Items</h4>
        <div>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Inventory
            </a>
            <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>View Low Stock
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($outOfStockItems->count() > 0)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Urgent Attention Required:</strong> {{ $outOfStockItems->total() }} product(s) are completely out of stock and need immediate restocking.
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Supplier</th>
                            <th>Current Stock</th>
                            <th>Alert Level</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outOfStockItems as $item)
                        <tr class="table-danger">
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
                            <td>
                                <span class="fw-bold text-danger">
                                    {{ $item->quantity }} {{ $item->unit }}
                                </span>
                            </td>
                            <td>{{ $item->low_stock_alert }} {{ $item->unit }}</td>
                            <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory.stock-in.index', ['product_id' => $item->id]) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-arrow-down me-1"></i>Restock
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $outOfStockItems->links('pagination.bootstrap-4') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success">No Out of Stock Items</h5>
                <p class="text-muted">All products are in stock. Excellent inventory management!</p>
                <a href="{{ route('inventory.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-boxes me-2"></i>View All Inventory
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Include the stock modals -->
@include('inventory.partials.stock-modals')

<style>
.product-image {
    border: 1px solid #dee2e6;
    transition: transform 0.2s;
}

.product-image:hover {
    transform: scale(1.1);
}

.no-image-placeholder {
    border: 1px dashed #dee2e6;
}
</style>
@endsection