<!-- resources/views/inventory/expiring-soon.blade.php -->
@extends('layouts.inventory')

@section('title', 'Expiring Soon Items')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm" style="background-color: #FFF3E0;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 fw-bold" style="color: #F57C00;\"><i class="fas fa-clock me-2"></i>Items Expiring Soon</h4>
                        <p class="mb-0" style="color: #E65100;\">Products expiring within the next 30 days</p>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.8);">
                        <i class="fas fa-exclamation-triangle fa-2x" style="color: #FF9800;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #2c3e50;">Expiring Items List</h5>
        <div>
            <button type="button" class="btn btn-primary me-2" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print Report
            </button>
            <a href="{{ route('inventory.print.expiring') }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>PDF Report
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search by product name, code, or batch..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Search
                </button>
                <a href="{{ route('inventory.expiring-soon') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>

        @if($expiringItems->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Batch Number</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Date Received</th>
                            <th>Expiry Date</th>
                            <th>Days Left</th>
                            <th>Unit Cost</th>
                            <th>Stock Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringItems as $item)
                        @php
                            $daysLeft = now()->diffInDays($item->expiry_date, false);
                            $badgeClass = $daysLeft < 0 ? 'bg-danger' : ($daysLeft < 7 ? 'bg-danger' : ($daysLeft < 15 ? 'bg-warning' : 'bg-info'));
                            $badgeText = $daysLeft < 0 ? 'Expired' : ($daysLeft . ' days');
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->inventory->image)
                                        <img src="{{ asset('storage/' . $item->inventory->image) }}" 
                                             alt="{{ $item->inventory->product_name }}" 
                                             class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    @endif
                                    <div>
                                        <div>{{ $item->inventory->product_name }}</div>
                                        <small class="text-muted">{{ $item->inventory->product_code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->batch_number ?? 'N/A' }}</td>
                            <td>{{ $item->supplier->name }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $item->remaining_quantity }} {{ $item->inventory->unit }}
                                </span>
                            </td>
                            <td>{{ $item->date_received->format('M d, Y') }}</td>
                            <td>{{ $item->expiry_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>
                            </td>
                            <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="fw-bold">₱{{ number_format($item->remaining_quantity * $item->unit_cost, 2) }}</td>
                            <td>
                                <a href="{{ route('inventory.show-batch', $item->id) }}" class="btn btn-sm btn-info me-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($item->remaining_quantity > 0)
                                <button type="button" class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#adjustBatchModal{{ $item->id }}">
                                    <i class="fas fa-adjust"></i>
                                </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Adjust Batch Modal -->
                        <div class="modal fade" id="adjustBatchModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('inventory.adjust-batch', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Adjust Batch Quantity</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Current Quantity</label>
                                                <input type="text" class="form-control" value="{{ $item->remaining_quantity }} {{ $item->inventory->unit }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Adjustment Type *</label>
                                                <select class="form-control" name="adjustment_type" required>
                                                    <option value="add">Add Quantity</option>
                                                    <option value="remove">Remove Quantity</option>
                                                    <option value="set">Set Specific Quantity</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Quantity *</label>
                                                <input type="number" class="form-control" name="quantity" min="1" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Reason *</label>
                                                <input type="text" class="form-control" name="reason" required placeholder="e.g., Damaged, Count error, etc.">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Notes</label>
                                                <textarea class="form-control" name="notes" rows="2" placeholder="Additional notes..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-warning">Adjust Batch</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $expiringItems->links('pagination.bootstrap-4') }}
            </div>
            
            <!-- Summary Statistics -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Summary</h6>
                            <p class="mb-1">Total Expiring Batches: <span class="fw-bold">{{ $expiringItems->total() }}</span></p>
                            <p class="mb-1">Total Quantity: <span class="fw-bold">{{ $expiringItems->sum('remaining_quantity') }}</span></p>
                            <p class="mb-0">Total Value: <span class="fw-bold text-danger">₱{{ number_format($expiringItems->sum(function($item) { return $item->remaining_quantity * $item->unit_cost; }), 2) }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Urgency Levels</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="badge bg-danger">Expired (0 days)</span>
                                    <p class="mb-0 mt-1">
                                        {{ $expiringItems->where(function($item) { return now()->diffInDays($item->expiry_date, false) < 0; })->count() }} batches
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <span class="badge bg-warning">Critical (1-7 days)</span>
                                    <p class="mb-0 mt-1">
                                        {{ $expiringItems->where(function($item) { $days = now()->diffInDays($item->expiry_date, false); return $days >= 0 && $days <= 7; })->count() }} batches
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <span class="badge bg-info">Warning (8-30 days)</span>
                                    <p class="mb-0 mt-1">
                                        {{ $expiringItems->where(function($item) { $days = now()->diffInDays($item->expiry_date, false); return $days > 7 && $days <= 30; })->count() }} batches
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success">No Expiring Items</h5>
                <p class="text-muted">Great! No items are expiring within the next 30 days.</p>
                <a href="{{ route('inventory.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-boxes me-2"></i>Back to Inventory
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        .card-header, .btn, .modal, .nav-tabs-custom, .sidebar, .header {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        table {
            width: 100% !important;
        }
    }
</style>
@endsection