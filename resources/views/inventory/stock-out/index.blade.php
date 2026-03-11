<!-- resources/views/inventory/stock-out/index.blade.php -->
@extends('layouts.inventory')

@section('title', 'Stock Out Transactions')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background-color: #E3F2FD;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #1976D2;">{{ $stockOuts->total() }}</h2>
                        <small class="d-block" style="color: #1976D2;">Total Transactions</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-receipt fa-lg" style="color: #2196F3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background-color: #FFEBEE;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #C62828;">{{ number_format($stockOuts->sum('quantity_removed')) }}</h2>
                        <small class="d-block" style="color: #C62828;">Total Items Removed</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-box fa-lg" style="color: #f44336;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background-color: #F3E5F5;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #7B1FA2;">₱{{ number_format($stockOuts->sum('total_value'), 2) }}</h2>
                        <small class="d-block" style="color: #7B1FA2;">Total Value</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-money-bill-wave fa-lg" style="color: #9C27B0;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background-color: #FFF3E0;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #F57C00;">{{ $products->count() }}</h2>
                        <small class="d-block" style="color: #F57C00;">Active Products</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-boxes fa-lg" style="color: #FF9800;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #2c3e50;"><i class="fas fa-arrow-up me-2" style="color: #f44336;"></i>Stock Out Transactions</h5>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createStockOutModal">
                <i class="fas fa-plus me-2"></i>New Stock Out
            </button>
            <a href="{{ route('inventory.stock-out.export', request()->all()) }}" class="btn btn-success">
                <i class="fas fa-file-export me-2"></i>Export CSV
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control datepicker" name="date_from" 
                       value="{{ request('date_from') }}" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control datepicker" name="date_to" 
                       value="{{ request('date_to') }}" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <select class="form-control" name="reason">
                    <option value="">All Reasons</option>
                    @foreach($reasons as $key => $label)
                        <option value="{{ $key }}" {{ request('reason') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <a href="{{ route('inventory.stock-out.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>

        @if($stockOuts->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ref No</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Reason</th>
                            <th>Handled By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockOuts as $stockOut)
                        <tr>
                            <td><strong>{{ $stockOut->reference_number }}</strong></td>
                            <td>{{ $stockOut->date_removed->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($stockOut->inventory->image)
                                        <img src="{{ asset('storage/' . $stockOut->inventory->image) }}" 
                                             alt="{{ $stockOut->inventory->product_name }}" 
                                             class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    @endif
                                    <div>
                                        <div>{{ $stockOut->inventory->product_name }}</div>
                                        <small class="text-muted">{{ $stockOut->inventory->product_code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ number_format($stockOut->quantity_removed, 2) }} {{ $stockOut->inventory->unit ?? '' }}
                                </span>
                            </td>
                            <td>₱{{ number_format($stockOut->unit_price, 2) }}</td>
                            <td class="fw-bold">₱{{ number_format($stockOut->total_value, 2) }}</td>
                            <td>
                                <span class="badge {{ $stockOut->reason == 'sale' ? 'bg-success' : ($stockOut->reason == 'damage' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ $stockOut->getReasonLabelAttribute() }}
                                </span>
                            </td>
                            <td>{{ $stockOut->handledBy->name }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#viewStockOutModal{{ $stockOut->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $stockOuts->links('pagination.bootstrap-4') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No stock out transactions found</h5>
                <p class="text-muted">Start by adding a new stock out transaction.</p>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createStockOutModal">
                    <i class="fas fa-plus me-2"></i>Add Stock Out
                </button>
            </div>
        @endif
    </div>
</div>

<!-- View Modals (Outside the table) -->
@foreach($stockOuts as $stockOut)
<div class="modal fade" id="viewStockOutModal{{ $stockOut->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Out Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th>Reference No:</th><td>{{ $stockOut->reference_number }}</td></tr>
                            <tr><th>Date Removed:</th><td>{{ $stockOut->date_removed->format('M d, Y') }}</td></tr>
                            <tr><th>Product:</th><td>{{ $stockOut->inventory->product_name }}</td></tr>
                            <tr><th>Product Code:</th><td>{{ $stockOut->inventory->product_code }}</td></tr>
                            <tr><th>Reason:</th><td>{{ $stockOut->getReasonLabelAttribute() }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th>Quantity Removed:</th><td>{{ number_format($stockOut->quantity_removed, 2) }}</td></tr>
                            <tr><th>Unit Price:</th><td>₱{{ number_format($stockOut->unit_price, 2) }}</td></tr>
                            <tr><th>Total Value:</th><td>₱{{ number_format($stockOut->total_value, 2) }}</td></tr>
                            <tr><th>Handled By:</th><td>{{ $stockOut->handledBy->name }}</td></tr>
                            <tr><th>Date:</th><td>{{ $stockOut->created_at->format('M d, Y h:i A') }}</td></tr>
                        </table>
                    </div>
                </div>
                @if($stockOut->notes)
                <div class="mt-3">
                    <h6>Notes:</h6>
                    <p class="text-muted">{{ $stockOut->notes }}</p>
                </div>
                @endif
                
                <!-- FIFO Allocation Info -->
                <div class="mt-4">
                    <h6>FIFO Allocation:</h6>
                    @php
                        $movements = $stockOut->stockMovements()->with('reference')->get();
                    @endphp
                    @if($movements->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Batch</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                @if($movement->reference)
                                    <tr>
                                        <td>{{ $movement->reference->batch_number ?? 'N/A' }}</td>
                                        <td>{{ number_format($movement->quantity, 2) }}</td>
                                        <td>₱{{ number_format($movement->unit_price, 2) }}</td>
                                        <td>₱{{ number_format($movement->quantity * $movement->unit_price, 2) }}</td>
                                    </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No allocation details available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Create Stock Out Modal -->
<div class="modal fade" id="createStockOutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('inventory.stock-out.store') }}" method="POST" id="stockOutForm">
                @csrf
                <input type="hidden" name="inventory_id" id="stock_out_inventory_id">
                <div class="modal-header">
                    <h5 class="modal-title">New Stock Out Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product *</label>
                            <select class="form-control" id="productSelectOut" required onchange="updateAvailableStock()">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-code="{{ $product->product_code }}"
                                            data-unit="{{ $product->unit }}"
                                            data-current="{{ $product->quantity }}"
                                            data-selling="{{ $product->selling_price }}">
                                        {{ $product->product_name }} ({{ $product->product_code }}) - Stock: {{ $product->quantity }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reason *</label>
                            <select class="form-control" name="reason" required>
                                @foreach($reasons as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Available Stock</label>
                            <input type="text" class="form-control" id="availableStock" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity to Remove *</label>
                            <input type="number" step="0.01" class="form-control" name="quantity" min="0.01" required onchange="updateTotalValue()">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unit Price (₱) *</label>
                            <input type="number" step="0.01" class="form-control" name="unit_price" min="0.01" required onchange="updateTotalValue()">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Value (₱)</label>
                            <input type="text" class="form-control" id="totalValue" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Removed *</label>
                            <input type="date" class="form-control" id="dateRemoved" name="date_removed" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <!-- FIFO Batches Preview -->
                    <div class="mb-3" id="fifoBatches" style="display: none;">
                        <label class="form-label">FIFO Allocation Preview</label>
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-sm" id="fifoTable">
                                    <thead>
                                        <tr>
                                            <th>Batch No</th>
                                            <th>Expiry Date</th>
                                            <th>Available Qty</th>
                                            <th>Unit Cost</th>
                                            <th>Allocation</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fifoTableBody">
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Reason details or additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateAvailableStock() {
    const productSelect = document.getElementById('productSelectOut');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const productId = productSelect.value;
    const inventoryIdInput = document.getElementById('stock_out_inventory_id');
    const availableStockInput = document.getElementById('availableStock');
    const unitPriceInput = document.querySelector('input[name="unit_price"]');
    const fifoBatches = document.getElementById('fifoBatches');
    
    if (productId) {
        inventoryIdInput.value = productId;
        const currentStock = selectedOption.getAttribute('data-current');
        const sellingPrice = selectedOption.getAttribute('data-selling');
        const unit = selectedOption.getAttribute('data-unit');
        
        availableStockInput.value = currentStock + ' ' + unit;
        unitPriceInput.value = sellingPrice;
        
        // Update max attribute on quantity input
        const quantityInput = document.querySelector('input[name="quantity"]');
        quantityInput.max = currentStock;
        
        // Show/hide FIFO batches
        if (currentStock > 0) {
            fifoBatches.style.display = 'block';
            loadFIFOBatches(productId, parseInt(currentStock));
        } else {
            fifoBatches.style.display = 'none';
        }
        
        updateTotalValue();
    } else {
        inventoryIdInput.value = '';
        availableStockInput.value = '';
        fifoBatches.style.display = 'none';
    }
}

function updateTotalValue() {
    const quantityInput = document.querySelector('input[name="quantity"]');
    const unitPriceInput = document.querySelector('input[name="unit_price"]');
    const totalValueInput = document.getElementById('totalValue');
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const unitPrice = parseFloat(unitPriceInput.value) || 0;
    const totalValue = quantity * unitPrice;
    
    totalValueInput.value = '₱' + totalValue.toFixed(2);
}

function loadFIFOBatches(productId, currentStock) {
    // Fetch batches via AJAX
    fetch(`/inventory/${productId}/fifo-batches`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('fifoTableBody');
            tableBody.innerHTML = '';
            
            if (data.batches && data.batches.length > 0) {
                data.batches.forEach(batch => {
                    const row = document.createElement('tr');
                    const expiryDate = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString() : 'N/A';
                    const batchClass = getBatchStatusClass(batch.expiry_date);
                    
                    row.innerHTML = `
                        <td>${batch.batch_number || 'N/A'}</td>
                        <td><span class="${batchClass}">${expiryDate}</span></td>
                        <td>${batch.remaining_quantity}</td>
                        <td>₱${parseFloat(batch.unit_cost).toFixed(2)}</td>
                        <td id="alloc_${batch.id}">0</td>
                    `;
                    tableBody.appendChild(row);
                });
                
                // Initialize allocation preview
                updateAllocationPreview();
            }
        })
        .catch(error => {
            console.error('Error loading FIFO batches:', error);
        });
}

function getBatchStatusClass(expiryDate) {
    if (!expiryDate) return 'badge bg-secondary';
    
    const today = new Date();
    const expiry = new Date(expiryDate);
    const daysDiff = Math.floor((expiry - today) / (1000 * 60 * 60 * 24));
    
    if (daysDiff < 0) return 'badge bg-danger';
    if (daysDiff < 30) return 'badge bg-warning';
    return 'badge bg-success';
}

function updateAllocationPreview() {
    const quantityInput = document.querySelector('input[name="quantity"]');
    const quantity = parseInt(quantityInput.value) || 0;
    
    if (quantity > 0) {
        // This is a simplified preview
        // In a real implementation, you would calculate exact allocation
        const tableBody = document.getElementById('fifoTableBody');
        const rows = tableBody.getElementsByTagName('tr');
        
        let remaining = quantity;
        for (let row of rows) {
            if (remaining <= 0) break;
            
            const availableCell = row.cells[2];
            const allocCell = row.cells[4];
            const available = parseInt(availableCell.textContent);
            
            const alloc = Math.min(available, remaining);
            allocCell.textContent = alloc;
            allocCell.className = alloc > 0 ? 'text-success fw-bold' : '';
            
            remaining -= alloc;
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTotalValue();
    
    // Lock date to today only
    const today = new Date().toISOString().split('T')[0];
    const dateRemovedInput = document.getElementById('dateRemoved');
    if (dateRemovedInput) {
        dateRemovedInput.value = today;
        dateRemovedInput.min = today;
        dateRemovedInput.max = today;
    }
    
    // Reset date when modal opens
    const modal = document.getElementById('createStockOutModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            if (dateRemovedInput) {
                dateRemovedInput.value = today;
                dateRemovedInput.min = today;
                dateRemovedInput.max = today;
            }
        });
    }
    
    // Add event listeners
    const quantityInput = document.querySelector('input[name="quantity"]');
    quantityInput.addEventListener('input', updateAllocationPreview);
});
</script>
@endsection