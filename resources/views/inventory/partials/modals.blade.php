<!-- resources/views/inventory/partials/modals.blade.php -->
<!-- Create Product Modal -->
<div class="modal fade" id="createInventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Same form as your create.blade.php -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control" name="product_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Code *</label>
                                <input type="text" class="form-control" name="product_code" required>
                                <small class="form-text text-muted">Leave blank to auto-generate</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rest of the form from create.blade.php -->
                    <!-- ... -->
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Stock In Modal -->
<div class="modal fade" id="quickStockInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('inventory.stock-in', 0) }}" method="POST" id="quickStockInForm">
                @csrf
                <input type="hidden" name="inventory_id" id="quick_stock_in_id">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Stock In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product *</label>
                        <select class="form-control" id="quickProductSelect" required>
                            <option value="">Select Product</option>
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}" 
                                        data-supplier="{{ $product->supplier_id }}"
                                        data-cost="{{ $product->cost_price }}">
                                    {{ $product->product_name }} ({{ $product->product_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit Cost (₱) *</label>
                        <input type="number" step="0.01" class="form-control" name="unit_cost" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch Number</label>
                        <input type="text" class="form-control" name="batch_number" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Stock Out Modal -->
<div class="modal fade" id="quickStockOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('inventory.stock-out', 0) }}" method="POST" id="quickStockOutForm">
                @csrf
                <input type="hidden" name="inventory_id" id="quick_stock_out_id">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Stock Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product *</label>
                        <select class="form-control" id="quickProductSelectOut" required>
                            <option value="">Select Product</option>
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}" 
                                        data-current="{{ $product->quantity }}"
                                        data-selling="{{ $product->selling_price }}">
                                    {{ $product->product_name }} (Stock: {{ $product->quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason *</label>
                        <select class="form-control" name="reason" required>
                            <option value="sale">Sale</option>
                            <option value="damage">Damage</option>
                            <option value="expiration">Expired</option>
                            <option value="internal_use">Internal Use</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>