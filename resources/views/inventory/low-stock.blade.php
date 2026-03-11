<!-- resources/views/inventory/low-stock.blade.php -->

@extends('layouts.inventory')

@section('title', 'Low Stock Alerts')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-exclamation-triangle text-warning me-2"></i>Low Stock Items</h4>
        <div>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Inventory
            </a>
            <a href="{{ route('inventory.out-of-stock') }}" class="btn btn-danger">
                <i class="fas fa-times-circle me-2"></i>View Out of Stock
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($lowStockItems->count() > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Warning:</strong> {{ $lowStockItems->total() }} product(s) are running low on stock and need attention.
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
                            <th>Difference</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockItems as $item)
                        <tr class="table-warning">
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
                                <span class="fw-bold text-warning">
                                    {{ $item->quantity }} {{ $item->unit }}
                                </span>
                            </td>
                            <td>{{ $item->low_stock_alert }} {{ $item->unit }}</td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ $item->quantity - $item->low_stock_alert }} {{ $item->unit }} below
                                </span>
                            </td>
                            <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory.stock-in.index', ['product_id' => $item->id]) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-arrow-down me-1"></i>Stock In
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $lowStockItems->links('pagination.bootstrap-4') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success">No Low Stock Items</h5>
                <p class="text-muted">All products have sufficient stock levels. Great job!</p>
                <a href="{{ route('inventory.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-boxes me-2"></i>View All Inventory
                </a>
            </div>
        @endif
    </div>
</div>

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