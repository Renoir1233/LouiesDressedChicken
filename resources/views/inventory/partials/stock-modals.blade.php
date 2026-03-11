<!-- resources/views/inventory/partials/stock-modals.blade.php -->
<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="stockInForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Stock In - <span id="stockInProductName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Quantity: <strong id="stockInCurrentQuantity"></strong></label>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supplier *</label>
                            <select class="form-control" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity to Add *</label>
                            <input type="number" class="form-control" name="quantity" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unit Cost (₱) *</label>
                            <input type="number" step="0.01" class="form-control" name="unit_cost" id="stockInUnitCost" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date Received *</label>
                            <input type="date" class="form-control" name="date_received" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batch Number</label>
                            <input type="text" class="form-control" name="batch_number" placeholder="Optional">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
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

<!-- Stock Out Modal -->
<div class="modal fade" id="stockOutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="stockOutForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Stock Out - <span id="stockOutProductName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Quantity: <strong id="stockOutCurrentQuantity"></strong></label>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reason *</label>
                            <select class="form-control" name="reason" required>
                                <option value="sale">Sale</option>
                                <option value="damage">Damage</option>
                                <option value="expiration">Expired</option>
                                <option value="return">Return to Supplier</option>
                                <option value="internal_use">Internal Use</option>
                                <option value="sample">Sample</option>
                                <option value="wastage">Wastage</option>
                                <option value="adjustment">Stock Adjustment</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity to Remove *</label>
                            <input type="number" class="form-control" name="quantity" min="1" required id="stockOutQuantityInput">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unit Price (₱) *</label>
                            <input type="number" step="0.01" class="form-control" name="unit_price" id="stockOutUnitPrice" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date Removed *</label>
                            <input type="date" class="form-control" name="date_removed" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Reason details..."></textarea>
                    </div>
                    
                    <!-- FIFO Warning -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Stock will be removed using FIFO (First-In-First-Out) method. Oldest batches will be consumed first.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Remove Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stock In Modal Handler
    const stockInButtons = document.querySelectorAll('.stock-in-btn');
    const stockInModal = document.getElementById('stockInModal');
    const stockInForm = document.getElementById('stockInForm');
    const stockInProductName = document.getElementById('stockInProductName');
    const stockInCurrentQuantity = document.getElementById('stockInCurrentQuantity');
    const stockInUnitCost = document.getElementById('stockInUnitCost');
    
    stockInButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const currentQuantity = this.getAttribute('data-current-quantity');
            const unit = this.getAttribute('data-unit');
            const costPrice = this.getAttribute('data-cost-price');
            
            stockInProductName.textContent = productName;
            stockInCurrentQuantity.textContent = currentQuantity + ' ' + unit;
            stockInUnitCost.value = costPrice;
            stockInForm.action = `/inventory/${productId}/stock-in`;
            
            // Reset form
            stockInForm.reset();
            stockInForm.querySelector('input[name="date_received"]').value = new Date().toISOString().split('T')[0];
            stockInUnitCost.value = costPrice;
        });
    });
    
    // Stock Out Modal Handler
    const stockOutButtons = document.querySelectorAll('.stock-out-btn');
    const stockOutModal = document.getElementById('stockOutModal');
    const stockOutForm = document.getElementById('stockOutForm');
    const stockOutProductName = document.getElementById('stockOutProductName');
    const stockOutCurrentQuantity = document.getElementById('stockOutCurrentQuantity');
    const stockOutQuantityInput = document.getElementById('stockOutQuantityInput');
    const stockOutUnitPrice = document.getElementById('stockOutUnitPrice');
    
    stockOutButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const currentQuantity = this.getAttribute('data-current-quantity');
            const unit = this.getAttribute('data-unit');
            const sellingPrice = this.getAttribute('data-selling-price');
            
            stockOutProductName.textContent = productName;
            stockOutCurrentQuantity.textContent = currentQuantity + ' ' + unit;
            stockOutForm.action = `/inventory/${productId}/stock-out`;
            stockOutQuantityInput.max = currentQuantity;
            stockOutUnitPrice.value = sellingPrice;
            
            // Reset form
            stockOutForm.reset();
            stockOutForm.querySelector('input[name="date_removed"]').value = new Date().toISOString().split('T')[0];
            stockOutUnitPrice.value = sellingPrice;
            stockOutQuantityInput.value = '';
        });
    });
    
    // Quick Stock In Form
    const quickStockInForm = document.getElementById('quickStockInForm');
    const quickProductSelect = document.getElementById('quickProductSelect');
    const quickStockInId = document.getElementById('quick_stock_in_id');
    
    quickProductSelect.addEventListener('change', function() {
        const productId = this.value;
        quickStockInId.value = productId;
        
        if (productId) {
            const selectedOption = this.options[this.selectedIndex];
            const supplierId = selectedOption.getAttribute('data-supplier');
            const costPrice = selectedOption.getAttribute('data-cost');
            
            // You can auto-fill supplier and unit cost here
            const unitCostInput = quickStockInForm.querySelector('input[name="unit_cost"]');
            unitCostInput.value = costPrice;
        }
    });
    
    quickStockInForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const productId = quickStockInId.value;
        
        if (!productId) {
            alert('Please select a product');
            return;
        }
        
        this.action = `/inventory/${productId}/stock-in`;
        this.submit();
    });
    
    // Quick Stock Out Form
    const quickStockOutForm = document.getElementById('quickStockOutForm');
    const quickProductSelectOut = document.getElementById('quickProductSelectOut');
    const quickStockOutId = document.getElementById('quick_stock_out_id');
    
    quickProductSelectOut.addEventListener('change', function() {
        const productId = this.value;
        quickStockOutId.value = productId;
        
        if (productId) {
            const selectedOption = this.options[this.selectedIndex];
            const currentStock = selectedOption.getAttribute('data-current');
            const sellingPrice = selectedOption.getAttribute('data-selling');
            
            // Update max quantity
            const quantityInput = quickStockOutForm.querySelector('input[name="quantity"]');
            quantityInput.max = currentStock;
        }
    });
    
    quickStockOutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const productId = quickStockOutId.value;
        
        if (!productId) {
            alert('Please select a product');
            return;
        }
        
        this.action = `/inventory/${productId}/stock-out`;
        this.submit();
    });
});
</script>