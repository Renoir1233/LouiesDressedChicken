<!-- resources/views/inventory/stock-in/index.blade.php -->
@extends('layouts.inventory')

@section('title', 'Stock In Transactions')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background-color: #E3F2FD;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #1976D2;">{{ $stockIns->total() }}</h2>
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
        <div class="card border-0 shadow-sm" style="background-color: #E8F5E9;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #388E3C;">{{ number_format($stockIns->sum('quantity_received')) }}</h2>
                        <small class="d-block" style="color: #388E3C;">Total Items Received</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-box fa-lg" style="color: #4CAF50;"></i>
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
                        <h2 class="mb-1 fw-bold" style="color: #7B1FA2;">₱{{ number_format($stockIns->sum('total_cost'), 2) }}</h2>
                        <small class="d-block" style="color: #7B1FA2;">Total Cost</small>
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
                        <h2 class="mb-1 fw-bold" style="color: #F57C00;">{{ $suppliers->count() }}</h2>
                        <small class="d-block" style="color: #F57C00;">Active Suppliers</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-truck fa-lg" style="color: #FF9800;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #2c3e50;"><i class="fas fa-arrow-down me-2" style="color: #4CAF50;"></i>Stock In Transactions</h5>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createStockInModal">
                <i class="fas fa-plus me-2"></i>New Stock In
            </button>
            <a href="{{ route('inventory.stock-in.export', request()->all()) }}" class="btn btn-success">
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
                <select class="form-control" name="supplier_id">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <a href="{{ route('inventory.stock-in.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>

        @if($stockIns->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Ref No</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>Total Cost</th>
                            <th>Batch No</th>
                            <th>Expiry</th>
                            <th>Received By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockIns as $stockIn)
                        <tr>
                            <td><strong>{{ $stockIn->reference_number }}</strong></td>
                            <td>{{ $stockIn->date_received->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($stockIn->inventory && $stockIn->inventory->image)
                                        @php
                                            $imagePath = $stockIn->inventory->image;
                                            // Check if image path is stored correctly
                                            if (strpos($imagePath, 'storage/') === false) {
                                                $imagePath = 'storage/' . $imagePath;
                                            }
                                        @endphp
                                        <img src="{{ asset($imagePath) }}" 
                                             alt="{{ $stockIn->inventory->product_name }}" 
                                             class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;"
                                             onerror="this.src='https://via.placeholder.com/40?text=No+Image';">
                                    @else
                                        <div class="me-2" style="width: 40px; height: 40px; background: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-box text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div>{{ $stockIn->inventory->product_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $stockIn->inventory->product_code ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $stockIn->supplier->name ?? 'N/A' }}</td>
                            <td>
                                <div class="mb-2">
                                    <span class="badge bg-primary">
                                        {{ number_format($stockIn->quantity_received, 2) }} {{ $stockIn->inventory->unit ?? '' }}
                                    </span>
                                </div>
                                <small class="text-muted">Remaining: {{ number_format($stockIn->remaining_quantity, 2) }} {{ $stockIn->inventory->unit ?? '' }}</small>
                            </td>
                            <td>₱{{ number_format($stockIn->unit_cost, 2) }}</td>
                            <td class="fw-bold">₱{{ number_format($stockIn->total_cost, 2) }}</td>
                            <td>{{ $stockIn->batch_number ?? 'N/A' }}</td>
                            <td>
                                @if($stockIn->expiry_date)
                                    @php
                                        $daysUntilExpiry = now()->diffInDays($stockIn->expiry_date, false);
                                    @endphp
                                    <span class="badge {{ $daysUntilExpiry < 0 ? 'bg-danger' : ($daysUntilExpiry < 30 ? 'bg-warning' : 'bg-success') }}">
                                        {{ $stockIn->expiry_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>{{ $stockIn->receivedBy->name ?? 'N/A' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info me-1" data-bs-toggle="modal" 
                                        data-bs-target="#viewStockInModal{{ $stockIn->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($stockIn->remaining_quantity > 0)
                                <a href="{{ route('inventory.show-batch', $stockIn->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-adjust"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $stockIns->links('pagination.bootstrap-4') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-arrow-down fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No stock in transactions found</h5>
                <p class="text-muted">Start by adding a new stock in transaction.</p>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createStockInModal">
                    <i class="fas fa-plus me-2"></i>Add Stock In
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Create Stock In Modal -->
<div class="modal fade" id="createStockInModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- FIXED FORM: Changed action to POST to inventory.stock-in without ID initially -->
            <form id="stockInForm" method="POST">
                @csrf
                <input type="hidden" name="inventory_id" id="stock_in_inventory_id">
                <div class="modal-header">
                    <h5 class="modal-title">New Stock In Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product *</label>
                            <select class="form-control" id="productSelect" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-code="{{ $product->product_code }}"
                                            data-unit="{{ $product->unit }}"
                                            data-current="{{ $product->quantity }}"
                                            data-cost="{{ $product->cost_price }}"
                                            data-supplier="{{ $product->supplier_id }}">
                                        {{ $product->product_name }} ({{ $product->product_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supplier *</label>
                            <select class="form-control" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity Received *</label>
                            <input type="number" class="form-control" name="quantity" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unit Cost (₱) *</label>
                            <input type="number" step="0.01" class="form-control" name="unit_cost" id="unitCost" min="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Cost (₱)</label>
                            <input type="text" class="form-control" id="totalCost" readonly>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date Received *</label>
                            <input type="date" class="form-control" id="dateReceived" name="date_received" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiryDate" name="expiry_date">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Batch Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="batchNumber" name="batch_number" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="generateBatchBtn" title="Generate New Batch Number">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Auto-generated. Click refresh icon to generate new.</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Any additional notes..."></textarea>
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

<!-- View Modals (placed outside the table to fix layout) -->
@foreach($stockIns as $stockIn)
<div class="modal fade" id="viewStockInModal{{ $stockIn->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock In Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th width="40%">Reference No:</th><td>{{ $stockIn->reference_number }}</td></tr>
                            <tr><th>Date Received:</th><td>{{ $stockIn->date_received->format('M d, Y') }}</td></tr>
                            <tr><th>Product:</th><td>{{ $stockIn->inventory->product_name ?? 'N/A' }}</td></tr>
                            <tr><th>Product Code:</th><td>{{ $stockIn->inventory->product_code ?? 'N/A' }}</td></tr>
                            <tr><th>Supplier:</th><td>{{ $stockIn->supplier->name ?? 'N/A' }}</td></tr>
                            <tr><th>Batch No:</th><td>{{ $stockIn->batch_number ?? 'N/A' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th width="40%">Quantity Received:</th><td>{{ number_format($stockIn->quantity_received, 2) }}</td></tr>
                            <tr><th>Remaining:</th><td>{{ number_format($stockIn->remaining_quantity, 2) }}</td></tr>
                            <tr><th>Unit Cost:</th><td>₱{{ number_format($stockIn->unit_cost, 2) }}</td></tr>
                            <tr><th>Total Cost:</th><td>₱{{ number_format($stockIn->total_cost, 2) }}</td></tr>
                            <tr><th>Expiry Date:</th>
                                <td>
                                    @if($stockIn->expiry_date)
                                        {{ $stockIn->expiry_date->format('M d, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Received By:</th><td>{{ $stockIn->receivedBy->name ?? 'N/A' }}</td></tr>
                        </table>
                    </div>
                </div>
                @if($stockIn->notes)
                <div class="mt-3">
                    <h6>Notes:</h6>
                    <p class="text-muted">{{ $stockIn->notes }}</p>
                </div>
                @endif
                
                <!-- Stock Image -->
                @if($stockIn->inventory && $stockIn->inventory->image)
                <div class="mt-3">
                    <h6>Product Image:</h6>
                    @php
                        $imagePath = $stockIn->inventory->image;
                        if (strpos($imagePath, 'storage/') === false) {
                            $imagePath = 'storage/' . $imagePath;
                        }
                    @endphp
                    <img src="{{ asset($imagePath) }}" 
                         alt="{{ $stockIn->inventory->product_name }}" 
                         class="img-fluid rounded" 
                         style="max-height: 200px; object-fit: cover;"
                         onerror="this.src='https://via.placeholder.com/400x200?text=Image+Not+Found';">
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($stockIn->remaining_quantity > 0)
                
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if product_id is in URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('product_id');
    
    // Product selection handler
    const productSelect = document.getElementById('productSelect');
    const inventoryIdInput = document.getElementById('stock_in_inventory_id');
    const quantityInput = document.querySelector('input[name="quantity"]');
    const unitCostInput = document.getElementById('unitCost');
    const totalCostInput = document.getElementById('totalCost');
    const batchNumberInput = document.getElementById('batchNumber');
    const generateBatchBtn = document.getElementById('generateBatchBtn');
    const stockInForm = document.getElementById('stockInForm');
    const dateReceivedInput = document.getElementById('dateReceived');
    const expiryDateInput = document.getElementById('expiryDate');
    
    // Generate batch number
    function generateBatchNumber() {
        const date = new Date();
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        
        return `BATCH-${year}${month}${day}-${random}`;
    }
    
    // Generate batch number on modal open
    const modal = document.getElementById('createStockInModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            // Don't reset form if product is already selected from URL
            if (!productId) {
                batchNumberInput.value = generateBatchNumber();
                stockInForm.reset();
            }
            
            // Set date constraints
            const today = new Date().toISOString().split('T')[0];
            if (dateReceivedInput) {
                dateReceivedInput.value = today;
                dateReceivedInput.min = today;
                dateReceivedInput.max = today;
            }
            // Set expiry date min to tomorrow
            if (expiryDateInput) {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                expiryDateInput.min = tomorrow.toISOString().split('T')[0];
            }
            // Initialize calculations
            calculateTotalCost();
        });
    }
    
    // Generate batch number when button clicked
    if (generateBatchBtn) {
        generateBatchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            batchNumberInput.value = generateBatchNumber();
        });
    }
    
    // Helper function to update fields when product is selected
    function updateProductFields() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const productId = productSelect.value;
        
        if (productId) {
            inventoryIdInput.value = productId;
            
            // Auto-fill unit cost from product's cost price
            const costPrice = selectedOption.getAttribute('data-cost');
            console.log('Cost Price:', costPrice);
            if (costPrice && parseFloat(costPrice) > 0) {
                unitCostInput.value = parseFloat(costPrice).toFixed(2);
            }
            
            // Auto-fill supplier from product's supplier
            const supplierId = selectedOption.getAttribute('data-supplier');
            console.log('Supplier ID:', supplierId);
            const supplierSelect = document.querySelector('select[name="supplier_id"]');
            if (supplierId && supplierSelect) {
                supplierSelect.value = supplierId;
                console.log('Supplier set to:', supplierId);
            }
            
            calculateTotalCost();
        } else {
            inventoryIdInput.value = '';
        }
    }
    
    productSelect.addEventListener('change', function() {
        updateProductFields();
    });
    
    // Calculate total cost
    function calculateTotalCost() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;
        const totalCost = quantity * unitCost;
        
        totalCostInput.value = '₱' + totalCost.toFixed(2);
    }
    
    quantityInput.addEventListener('input', calculateTotalCost);
    unitCostInput.addEventListener('input', calculateTotalCost);
    
    // Form submission - FIXED
    stockInForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const productId = inventoryIdInput.value;
        if (!productId) {
            alert('Please select a product');
            productSelect.focus();
            return;
        }
        
        // Set the correct route - use inventory.stock-in.store
        this.action = '{{ route("inventory.stock-in.store") }}';
        
        // Submit form
        this.submit();
    });
    
    // Initialize total cost
    calculateTotalCost();
    
    // Initialize batch number
    batchNumberInput.value = generateBatchNumber();
    
    // Set today's date as default and lock it for date_received
    const today = new Date().toISOString().split('T')[0];
    if (dateReceivedInput) {
        dateReceivedInput.value = today;
        dateReceivedInput.min = today;
        dateReceivedInput.max = today; // Lock to today only
        dateReceivedInput.disabled = false;
    }
    
    // Set expiry date minimum to tomorrow (future dates only)
    if (expiryDateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        expiryDateInput.min = tomorrow.toISOString().split('T')[0];
    }
    
    // Auto-open modal and pre-select product if product_id is in URL
    if (productId) {
        // Generate batch number first
        batchNumberInput.value = generateBatchNumber();
        
        // Set today's date
        const today = new Date().toISOString().split('T')[0];
        if (dateReceivedInput) {
            dateReceivedInput.value = today;
            dateReceivedInput.min = today;
            dateReceivedInput.max = today;
        }
        
        // Set expiry date min to tomorrow
        if (expiryDateInput) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            expiryDateInput.min = tomorrow.toISOString().split('T')[0];
        }
        
        // Set the product select value
        productSelect.value = productId;
        console.log('Setting product to:', productId);
        
        // Call the helper function to update all related fields
        updateProductFields();
        
        // Open the modal after a small delay to ensure values are set
        setTimeout(function() {
            const modalElement = document.getElementById('createStockInModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }, 200);
        
        // Remove product_id from URL to clean up
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>

<style>
    /* Fix table layout */
    .table {
        margin-bottom: 0;
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .table img {
        max-width: 100%;
        height: auto;
    }
    
    /* Modal fixes */
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    /* Badge styling */
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    
    /* Form styling */
    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.2rem rgba(244, 163, 0, 0.25);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9em;
        }
        
        .table td, .table th {
            padding: 0.5rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8em;
        }
    }
</style>
@endsection