@extends('layouts.inventory')

@section('title', 'Product Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Product Details</h4>
        <div class="btn-group">
            <a href="{{ route('inventory.edit', $inventory->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                @if($inventory->image)
                    <img src="{{ asset('storage/' . $inventory->image) }}" 
                         alt="{{ $inventory->product_name }}" 
                         class="img-fluid rounded" 
                         style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                @else
                    <div class="no-image-placeholder bg-light rounded d-flex align-items-center justify-content-center" 
                         style="height: 250px; border: 2px dashed #dee2e6;">
                        <div class="text-center text-muted">
                            <i class="fas fa-box fa-3x mb-2"></i>
                            <p class="mb-0">No Image</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Product Code</th>
                                <td><strong>{{ $inventory->product_code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Product Name</th>
                                <td>{{ $inventory->product_name }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $inventory->description ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>
                                    <a href="{{ route('suppliers.show', $inventory->supplier_id) }}" class="text-decoration-none">
                                        {{ $inventory->supplier->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td><span class="badge bg-secondary">{{ $inventory->category }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Cost Price</th>
                                <td>₱{{ number_format($inventory->cost_price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Selling Price</th>
                                <td>₱{{ number_format($inventory->selling_price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Profit Margin</th>
                                <td>
                                    <span class="fw-bold {{ $inventory->profit_margin > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($inventory->profit_margin, 2) }}%
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Current Stock</th>
                                <td>
                                    <span class="fw-bold {{ $inventory->quantity <= $inventory->low_stock_alert ? 'text-warning' : '' }} {{ $inventory->quantity == 0 ? 'text-danger' : '' }}">
                                        {{ $inventory->quantity }} {{ $inventory->unit }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Low Stock Alert</th>
                                <td>{{ $inventory->low_stock_alert }} {{ $inventory->unit }}</td>
                            </tr>
                            <tr>
                                <th>Stock Status</th>
                                <td>
                                    @if($inventory->quantity == 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($inventory->quantity <= $inventory->low_stock_alert)
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($inventory->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Created: {{ $inventory->created_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Last Updated: {{ $inventory->updated_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Inventory
            </a>
        </div>
    </div>
</div>

@endsection