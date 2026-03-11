<!-- resources/views/orders/index.blade.php -->
@extends('layouts.orders')

@section('title', 'Order Management')
@section('page_title', 'Order Management')

@section('content')
<!-- Toast Notification Container -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="toastContainer"></div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Products Section -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-boxes me-2"></i>Available Products</h4>
                    <div class="d-flex gap-2">
                        <input type="text" id="searchProducts" class="form-control search-box" placeholder="Search products..." style="width: 250px;">
                        <select id="categoryFilter" class="form-select" style="width: auto;">
                            <option value="">All Categories</option>
                            <option value="Whole Chicken">Whole Chicken</option>
                            <option value="Chicken Part">Chicken Part</option>
                            <option value="Hotdog">Hotdog</option>
                            <option value="Fish">Fish</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading State -->
                    <div id="productsLoading" class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading products...</p>
                    </div>

                    <!-- Error State -->
                    <div id="productsError" class="text-center py-4" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                        <h5 class="text-danger">Failed to load products</h5>
                        <p id="errorMessage" class="text-muted"></p>
                        <button class="btn btn-primary mt-2" onclick="orderManager.loadProducts()">
                            <i class="fas fa-redo me-2"></i>Retry
                        </button>
                    </div>

                    <!-- Products Grid -->
                    <div id="productsContainer" class="row g-3" style="display: none;">
                        <!-- Products will be loaded here -->
                    </div>

                    <!-- No Products State -->
                    <div id="noProducts" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products found</h5>
                        <p class="text-muted">Try adjusting your search or filter</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="col-lg-4">
            <div class="cart-sidebar">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Cart</h4>
                    <button class="btn btn-sm btn-outline-primary" onclick="orderManager.viewPendingOrders()">
                        <i class="fas fa-clock me-1"></i>Pending
                    </button>
                </div>
                <div class="card-body">
                    <form id="orderForm">
                        <!-- Customer Type Selection -->
                        <div class="mb-4">
                            <h6 class="section-header">Customer Type</h6>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="customer_type" id="walkInCustomer" value="walk-in" checked autocomplete="off">
                                <label class="btn btn-outline-primary" for="walkInCustomer">
                                    <i class="fas fa-walking me-2"></i>Walk-In
                                </label>
                                
                                <input type="radio" class="btn-check" name="customer_type" id="regularCustomer" value="regular" autocomplete="off">
                                <label class="btn btn-outline-primary" for="regularCustomer">
                                    <i class="fas fa-user-tie me-2"></i>Regular Customer
                                </label>
                            </div>
                        </div>

                        <!-- Regular Customer Search Section -->
                        <div id="regularCustomerSection" class="mb-4" style="display: none;">
                            <div class="alert alert-info small mb-2">
                                <i class="fas fa-info-circle me-1"></i>Search for regular customer to link order
                            </div>
                            <div class="position-relative" style="z-index: 1051;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           id="customerSearchInput" 
                                           class="form-control" 
                                           placeholder="Type customer name or phone..."
                                           autocomplete="off">
                                </div>
                                <div id="customerSearchDropdown" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1051; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);">
                                    <div id="customerSearchResults" class="p-1">
                                        <!-- Search results will appear here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-4">
                            <h6 class="section-header">
                                Customer Information
                                <small class="text-muted" id="customerInfoLabel">(Optional)</small>
                            </h6>
                            <input type="hidden" name="existing_customer_id" id="existingCustomerId">
                            <input type="hidden" name="link_to_existing_order" id="linkToExistingOrder">
                            <input type="hidden" name="existing_order_id" id="existingOrderId">
                            
                            <div class="row g-2">
                                <div class="col-12">
                                    <input type="text" name="customer_name" id="customerName" class="form-control" placeholder="Customer Name">
                                </div>
                                <div class="col-12">
                                    <input type="text" name="customer_phone" id="customerPhone" class="form-control" placeholder="Phone Number" inputmode="numeric" pattern="[0-9]*">
                                </div>
                                <div class="col-12">
                                    <input type="text" name="customer_address" id="customerAddress" class="form-control" placeholder="Address (Optional)">
                                </div>
                            </div>
                            
                            <!-- Existing Orders Info -->
                            <div id="existingOrdersInfo" class="mt-2" style="display: none;">
                                <div class="alert alert-warning p-2 small mb-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    This customer has <span id="pendingOrdersCount">0</span> pending/partial order(s)
                                </div>
                                <select id="existingOrdersSelect" class="form-select form-select-sm">
                                    <option value="">Select order to add items to...</option>
                                </select>
                                <small class="text-muted">Leave empty to create new order</small>
                            </div>
                        </div>

                        <!-- Cart Items -->
                        <div class="mb-4">
                            <h6 class="section-header">Order Items</h6>
                            <div id="cartItems" class="mb-3">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <p>No items in cart</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div id="orderSummary" style="display: none;">
                            <div class="total-display">
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <small>Subtotal</small>
                                        <h5 id="subtotal">₱0.00</h5>
                                    </div>
                                    <div class="col-4">
                                        <small>Tax (12%)</small>
                                        <h5 id="tax">₱0.00</h5>
                                    </div>
                                    <div class="col-4">
                                        <small>Total</small>
                                        <h4 id="total">₱0.00</h4>
                                    </div>
                                </div>

                                <!-- Payment Section -->
                                <div class="payment-section">
                                    <input type="hidden" name="payment_status" id="paymentStatusHidden" value="paid">
                                    <div class="row g-2 mb-2" id="cashPaymentFields">
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Amount Paid</label>
                                            <input type="number" name="amount_paid" id="amountPaid" class="form-control" placeholder="0.00" min="0" step="0.01" inputmode="decimal" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Change</label>
                                            <input type="number" name="change" class="form-control" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="payment-status text-center small">
                                        <span id="paymentStatus" class="badge bg-secondary">Enter payment amount</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-3">
                                <label class="form-label">Order Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <button type="button" onclick="confirmCompleteOrder()" class="btn btn-lg" style="background-color: #660B05; color: white; border: none;">
                                    <i class="fas fa-check-circle me-2"></i>Complete Order
                                </button>
                                <button type="submit" name="status" value="pending" class="btn btn-outline-warning" id="savePendingBtn">
                                    <i class="fas fa-clock me-2"></i>Save as Pending
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Orders Modal -->
<div class="modal fade" id="pendingOrdersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clock me-2"></i>Pending Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="pendingOrdersList">
                    <!-- Pending orders will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Confirmation Modal -->
<div class="modal fade" id="receiptConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #660B05; color: white;">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Complete Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-question-circle fa-3x mb-3" style="color: #660B05;"></i>
                <h5 class="mb-3">Order Ready to Complete</h5>
                <p class="text-muted mb-4">Would you like to print a receipt for this order?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" onclick="completeOrderNoReceipt()">
                    <i class="fas fa-times me-2"></i>No Receipt
                </button>
                                <button type="button" class="btn" style="background-color: #660B05; color: white;" onclick="completeOrderWithReceipt()">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pending Order Receipt Modal -->
<div class="modal fade" id="pendingReceiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #660B05; color: white;">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Complete Pending Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-question-circle fa-3x mb-3" style="color: #660B05;"></i>
                <h5 class="mb-3">Complete This Order</h5>
                <p class="text-muted mb-4">Would you like to print a receipt when completing this order?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" onclick="completePendingNoReceipt()">
                    <i class="fas fa-times me-2"></i>No Receipt
                </button>
                <button type="button" class="btn" style="background-color: #660B05; color: white;" onclick="completePendingWithReceipt()">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Product Template -->
<template id="productTemplate">
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card product-card" data-product-id="{id}">
            <div class="position-relative">
                <img src="{image}" class="card-img-top product-image" alt="{product_name}" onerror="this.src='https://via.placeholder.com/300x200/EFEFEF/666666?text=No+Image'">
                <span class="stock-badge">{quantity} {unit} in stock</span>
            </div>
            <div class="card-body product-info">
                <h6 class="card-title">{product_name}</h6>
                <p class="card-text text-muted small mb-1">{product_code}</p>
                <p class="card-text text-muted small mb-2">{supplier}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 text-primary mb-0">₱{selling_price}</span>
                    <span class="unit-badge">{unit}</span>
                </div>
                <button class="btn btn-primary w-100 mt-2 add-to-cart-btn">
                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Cart Item Template -->
<template id="cartItemTemplate">
    <div class="cart-item" data-product-id="{id}" data-unit="{unit}">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <h6 class="mb-1">{product_name}</h6>
                <small class="text-muted">{product_code} • {unit}</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="quantity-controls" style="{quantity_controls_style}">
                <button type="button" class="quantity-btn decrease-qty" style="{qty_btn_style}">-</button>
                <input type="number" class="quantity-input" value="{quantity}" min="{quantity_min}" step="{quantity_step}" data-price="{price}" inputmode="decimal" {quantity_readonly}>
                <button type="button" class="quantity-btn increase-qty" style="{qty_btn_style}">+</button>
            </div>
            <span class="fw-bold">₱{total}</span>
        </div>
    </div>
</template>

<!-- Pending Order Template -->
<template id="pendingOrderTemplate">
    <div class="pending-order-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <h6 class="mb-1">{order_number}</h6>
                <small class="text-muted">{customer_name} • {created_at}</small>
                <div class="mt-1">
                    <span class="badge bg-warning">Pending</span>
                    <small class="text-muted ms-2">{items_count} items • ₱{total}</small>
                </div>
            </div>
            <div class="btn-group">
                <button class="btn btn-sm complete-order" data-order-id="{id}" style="background-color: #660B05; color: white; border: none;">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger delete-order" data-order-id="{id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="order-items">
            <!-- Order items will be populated here -->
        </div>
    </div>
</template>

<!-- Customer Search Result Template -->
<template id="customerSearchResultTemplate">
    <div class="customer-search-result" data-customer-id="{id}" data-name="{name}" data-phone="{phone}" data-address="{address}" data-has-pending="{has_pending}">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-block">{name}</strong>
                <small class="text-muted">{phone}</small>
            </div>
            <div class="text-end">
                <small class="text-muted">{order_count} order(s)</small>
                {has_pending_badge}
            </div>
        </div>
    </div>
</template>
@endsection

@section('scripts')
<script>
// Fix for customer search dropdown positioning
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('customerSearchDropdown');
    const input = document.getElementById('customerSearchInput');
    
    if (dropdown && input) {
        // Ensure dropdown is positioned relative to input
        const updateDropdownPosition = () => {
            const rect = input.getBoundingClientRect();
            dropdown.style.width = input.offsetWidth + 'px';
        };
        
        // Update on input focus/blur
        input.addEventListener('focus', updateDropdownPosition);
        input.addEventListener('blur', updateDropdownPosition);
        window.addEventListener('resize', updateDropdownPosition);
    }
});

// Toast notification function
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    const bgColor = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#660B05';
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: ${bgColor};">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Confirmation function for completing order with receipt option
function confirmCompleteOrder() {
    if (orderManager.cart.length === 0) {
        showToast('Please add items to the cart before completing order.', 'warning');
        return;
    }

    const amountPaid = parseFloat(document.querySelector('input[name="amount_paid"]').value) || 0;
    const total = orderManager.getCartTotal();

    if (amountPaid < total) {
        showToast(`Insufficient payment! Total: ₱${total.toFixed(2)}, Paid: ₱${amountPaid.toFixed(2)}`, 'error');
        return;
    }

    // Show modal instead of confirm dialog
    const modal = new bootstrap.Modal(document.getElementById('receiptConfirmModal'));
    modal.show();
}

function completeOrderWithReceipt() {
    bootstrap.Modal.getInstance(document.getElementById('receiptConfirmModal')).hide();
    orderManager.submitOrder('completed', true);
}

function completeOrderNoReceipt() {
    bootstrap.Modal.getInstance(document.getElementById('receiptConfirmModal')).hide();
    orderManager.submitOrder('completed', false);
}

// Pending order receipt confirmation functions
function completePendingWithReceipt() {
    bootstrap.Modal.getInstance(document.getElementById('pendingReceiptModal')).hide();
    orderManager.completePendingOrder(window.pendingOrderId, true);
}

function completePendingNoReceipt() {
    bootstrap.Modal.getInstance(document.getElementById('pendingReceiptModal')).hide();
    orderManager.completePendingOrder(window.pendingOrderId, false);
}

class OrderManager {
    constructor() {
        this.products = [];
        this.cart = [];
        this.searchTimeout = null;
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.loadProducts();
    }

    async loadProducts() {
        try {
            this.showLoadingState();
            
            const response = await fetch('{{ route("orders.products") }}');
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to load products');
            }

            if (data.success) {
                this.products = data.products;
                this.renderProducts(this.products);
                this.hideErrorState();
            } else {
                throw new Error(data.message || 'Failed to load products');
            }

        } catch (error) {
            console.error('Error loading products:', error);
            this.showErrorState(error.message);
        }
    }

    showLoadingState() {
        document.getElementById('productsLoading').style.display = 'block';
        document.getElementById('productsContainer').style.display = 'none';
        document.getElementById('productsError').style.display = 'none';
        document.getElementById('noProducts').style.display = 'none';
    }

    showErrorState(message) {
        document.getElementById('productsLoading').style.display = 'none';
        document.getElementById('productsContainer').style.display = 'none';
        document.getElementById('productsError').style.display = 'block';
        document.getElementById('noProducts').style.display = 'none';
        document.getElementById('errorMessage').textContent = message;
    }

    hideErrorState() {
        document.getElementById('productsError').style.display = 'none';
    }

    renderProducts(products) {
        const container = document.getElementById('productsContainer');
        const template = document.getElementById('productTemplate');
        
        document.getElementById('productsLoading').style.display = 'none';

        if (products.length === 0) {
            document.getElementById('noProducts').style.display = 'block';
            container.style.display = 'none';
            return;
        }

        container.innerHTML = '';
        products.forEach(product => {
            const html = template.innerHTML
                .replace(/{id}/g, product.id)
                .replace(/{product_name}/g, this.escapeHtml(product.product_name))
                .replace(/{product_code}/g, this.escapeHtml(product.product_code))
                .replace(/{selling_price}/g, parseFloat(product.selling_price).toFixed(2))
                .replace(/{quantity}/g, parseFloat(product.quantity).toFixed(1))
                .replace(/{unit}/g, this.escapeHtml(product.unit))
                .replace(/{supplier}/g, this.escapeHtml(product.supplier))
                .replace(/{image}/g, product.image);
            
            container.innerHTML += html;
        });

        container.style.display = 'flex';
        document.getElementById('noProducts').style.display = 'none';
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    setupEventListeners() {
        // Search functionality
        document.getElementById('searchProducts').addEventListener('input', (e) => {
            this.filterProducts();
        });

        // Category filter
        document.getElementById('categoryFilter').addEventListener('change', () => {
            this.filterProducts();
        });

        // Add to cart
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-to-cart-btn')) {
                const productCard = e.target.closest('.product-card');
                const productId = parseInt(productCard.dataset.productId);
                this.addToCart(productId);
            }
        });

        // Cart quantity controls
        document.getElementById('cartItems').addEventListener('click', (e) => {
            if (e.target.classList.contains('decrease-qty')) {
                const cartItem = e.target.closest('.cart-item');
                const unit = cartItem.dataset.unit;
                const isPack = unit && unit.toLowerCase() === 'pack';
                const change = isPack ? -1 : -0.1;
                this.adjustQuantity(cartItem, change);
            } else if (e.target.classList.contains('increase-qty')) {
                const cartItem = e.target.closest('.cart-item');
                const unit = cartItem.dataset.unit;
                const isPack = unit && unit.toLowerCase() === 'pack';
                const change = isPack ? 1 : 0.1;
                this.adjustQuantity(cartItem, change);
            } else if (e.target.classList.contains('remove-item')) {
                this.removeFromCart(e.target.closest('.cart-item'));
            }
        });

        // Quantity input changes
        document.getElementById('cartItems').addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                this.updateItemTotal(e.target.closest('.cart-item'));
            }
        });

        // Payment amount input
        document.querySelector('input[name="amount_paid"]').addEventListener('input', (e) => {
            this.calculateChange();
        });

        // Prevent non-numeric input in number fields
        this.setupNumberFieldValidation();

        // Form submission
        document.getElementById('orderForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const submitter = e.submitter;
            const status = submitter.value;
            this.submitOrder(status);
        });

        // Pending orders modal events
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('complete-order') || e.target.closest('.complete-order')) {
                const button = e.target.classList.contains('complete-order') ? e.target : e.target.closest('.complete-order');
                this.confirmCompletePendingOrder(button.dataset.orderId);
            } else if (e.target.classList.contains('delete-order') || e.target.closest('.delete-order')) {
                const button = e.target.classList.contains('delete-order') ? e.target : e.target.closest('.delete-order');
                this.deletePendingOrder(button.dataset.orderId);
            }
        });

        // Customer search input with autocomplete
        const customerSearchInput = document.getElementById('customerSearchInput');
        const customerSearchDropdown = document.getElementById('customerSearchDropdown');
        
        if (customerSearchInput) {
            // Input event for real-time search
            customerSearchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.trim();
                
                // Clear previous timeout
                if (this.searchTimeout) {
                    clearTimeout(this.searchTimeout);
                }
                
                if (searchTerm.length >= 2) {
                    this.showCustomerSearchLoading();
                    
                    // Set new timeout for search (debounce)
                    this.searchTimeout = setTimeout(() => {
                        this.searchCustomers(searchTerm);
                    }, 300);
                } else {
                    this.hideCustomerSearchDropdown();
                }
            });

            // Show dropdown when input is focused
            customerSearchInput.addEventListener('focus', (e) => {
                const searchTerm = e.target.value.trim();
                if (searchTerm.length >= 2) {
                    this.searchCustomers(searchTerm);
                }
            });

            // Handle keyboard navigation
            customerSearchInput.addEventListener('keydown', (e) => {
                const results = document.querySelectorAll('.customer-search-result');
                
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        this.navigateCustomerResults('down', results);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        this.navigateCustomerResults('up', results);
                        break;
                    case 'Enter':
                        e.preventDefault();
                        const activeResult = document.querySelector('.customer-search-result.active');
                        if (activeResult) {
                            this.selectCustomerFromResult(activeResult);
                        }
                        break;
                    case 'Escape':
                        this.hideCustomerSearchDropdown();
                        break;
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const searchInput = document.getElementById('customerSearchInput');
            const dropdown = document.getElementById('customerSearchDropdown');
            
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                this.hideCustomerSearchDropdown();
            }
        });

        // Existing orders select change
        document.getElementById('existingOrdersSelect')?.addEventListener('change', (e) => {
            const orderId = e.target.value;
            if (orderId) {
                document.getElementById('linkToExistingOrder').value = 'true';
                document.getElementById('existingOrderId').value = orderId;
            } else {
                document.getElementById('linkToExistingOrder').value = 'false';
                document.getElementById('existingOrderId').value = '';
            }
        });
    }

    navigateCustomerResults(direction, results) {
        const activeResult = document.querySelector('.customer-search-result.active');
        let nextIndex = -1;
        
        if (activeResult) {
            const currentIndex = Array.from(results).indexOf(activeResult);
            activeResult.classList.remove('active');
            
            if (direction === 'down') {
                nextIndex = currentIndex < results.length - 1 ? currentIndex + 1 : 0;
            } else {
                nextIndex = currentIndex > 0 ? currentIndex - 1 : results.length - 1;
            }
        } else {
            nextIndex = direction === 'down' ? 0 : results.length - 1;
        }
        
        if (results[nextIndex]) {
            results[nextIndex].classList.add('active');
            results[nextIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    setupNumberFieldValidation() {
        // Prevent non-numeric input in amount paid field
        const amountPaidInput = document.querySelector('input[name="amount_paid"]');
        if (amountPaidInput) {
            amountPaidInput.addEventListener('keydown', (e) => {
                this.validateNumberInput(e, true);
            });
            amountPaidInput.addEventListener('paste', (e) => {
                this.validatePastedNumber(e);
            });
        }

        // Prevent non-numeric input in customer phone field
        const customerPhoneInput = document.getElementById('customerPhone');
        if (customerPhoneInput) {
            customerPhoneInput.addEventListener('keydown', (e) => {
                this.validateNumberInput(e, false);
            });
            customerPhoneInput.addEventListener('paste', (e) => {
                this.validatePastedNumber(e);
            });
        }

        // Prevent non-numeric input in quantity inputs (delegated event)
        document.getElementById('cartItems').addEventListener('keydown', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                this.validateNumberInput(e, true);
            }
        });

        document.getElementById('cartItems').addEventListener('paste', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                this.validatePastedNumber(e);
            }
        });
    }

    validateNumberInput(e, allowDecimal = true) {
        // Allow: backspace, delete, tab, escape, enter
        if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }

        // Allow decimal point if enabled and not already present
        if (allowDecimal && e.key === '.' && !e.target.value.includes('.')) {
            return;
        }

        // Prevent if not a number (0-9)
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    }

    validatePastedNumber(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const numericValue = pastedText.replace(/[^0-9.]/g, '');
        
        // Only allow one decimal point
        const parts = numericValue.split('.');
        const cleanedValue = parts.length > 2 ? parts[0] + '.' + parts.slice(1).join('') : numericValue;
        
        // Insert the cleaned value
        const input = e.target;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const currentValue = input.value;
        
        input.value = currentValue.substring(0, start) + cleanedValue + currentValue.substring(end);
        input.setSelectionRange(start + cleanedValue.length, start + cleanedValue.length);
        
        // Trigger input event to update calculations
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    showCustomerSearchLoading() {
        const resultsContainer = document.getElementById('customerSearchResults');
        resultsContainer.innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2 text-muted">Searching...</span>
            </div>
        `;
        this.showCustomerSearchDropdown();
    }

    showCustomerSearchDropdown() {
        const dropdown = document.getElementById('customerSearchDropdown');
        dropdown.style.display = 'block';
    }

    hideCustomerSearchDropdown() {
        const dropdown = document.getElementById('customerSearchDropdown');
        dropdown.style.display = 'none';
    }

    async searchCustomers(searchTerm) {
        try {
            const response = await fetch(`/orders/search-customers?search=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();
            
            const resultsContainer = document.getElementById('customerSearchResults');
            const dropdown = document.getElementById('customerSearchDropdown');
            
            // Ensure dropdown exists and is properly initialized
            if (!dropdown || !resultsContainer) {
                console.error('Customer search dropdown or results container not found');
                return;
            }
            
            if (data.success && data.customers.length > 0) {
                resultsContainer.innerHTML = '';
                
                data.customers.forEach(customer => {
                    const resultElement = document.createElement('div');
                    resultElement.className = 'customer-search-result p-2 border-bottom';
                    resultElement.style.cursor = 'pointer';
                    resultElement.dataset.customerId = customer.id || '';
                    resultElement.dataset.name = customer.name || '';
                    resultElement.dataset.phone = customer.phone || '';
                    resultElement.dataset.address = customer.address || '';
                    resultElement.dataset.hasPending = customer.has_pending || false;
                    
                    resultElement.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block text-dark">${this.escapeHtml(customer.name)}</strong>
                                <small class="text-muted">${this.escapeHtml(customer.phone)}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">${customer.order_count || 0} order(s)</small>
                                ${customer.has_pending ? '<br><small class="text-warning"><i class="fas fa-clock me-1"></i>Has pending</small>' : ''}
                            </div>
                        </div>
                    `;
                    
                    // Add click event
                    resultElement.addEventListener('click', () => {
                        this.selectCustomerFromResult(resultElement);
                    });
                    
                    // Add hover effect
                    resultElement.addEventListener('mouseenter', () => {
                        resultElement.style.backgroundColor = '#f8f9fa';
                    });
                    
                    resultElement.addEventListener('mouseleave', () => {
                        resultElement.style.backgroundColor = '';
                    });
                    
                    resultsContainer.appendChild(resultElement);
                });
                
                // Add "Create new customer" option
                const newCustomerElement = document.createElement('div');
                newCustomerElement.className = 'customer-search-result p-2 border-bottom bg-light';
                newCustomerElement.style.cursor = 'pointer';
                newCustomerElement.dataset.customerId = 'new';
                newCustomerElement.dataset.name = searchTerm;
                newCustomerElement.dataset.phone = '';
                newCustomerElement.dataset.address = '';
                newCustomerElement.dataset.hasPending = false;
                
                newCustomerElement.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="d-block text-dark">Create new customer: "${this.escapeHtml(searchTerm)}"</strong>
                            <small class="text-muted">Click to create new customer</small>
                        </div>
                        <div class="text-end">
                            <i class="fas fa-plus-circle text-success"></i>
                        </div>
                    </div>
                `;
                
                newCustomerElement.addEventListener('click', () => {
                    this.selectCustomerFromResult(newCustomerElement);
                });
                
                newCustomerElement.addEventListener('mouseenter', () => {
                    newCustomerElement.style.backgroundColor = '#e9ecef';
                });
                
                newCustomerElement.addEventListener('mouseleave', () => {
                    newCustomerElement.style.backgroundColor = '#f8f9fa';
                });
                
                resultsContainer.appendChild(newCustomerElement);
                
                this.showCustomerSearchDropdown();
            } else {
                // Show only "Create new customer" option
                resultsContainer.innerHTML = `
                    <div class="customer-search-result p-2 border-bottom bg-light" data-customer-id="new" data-name="${this.escapeHtml(searchTerm)}" data-phone="" data-address="" data-has-pending="false">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block text-dark">Create new customer: "${this.escapeHtml(searchTerm)}"</strong>
                                <small class="text-muted">Click to create new customer</small>
                            </div>
                            <div class="text-end">
                                <i class="fas fa-plus-circle text-success"></i>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add click event to the new customer option
                const newCustomerOption = resultsContainer.querySelector('.customer-search-result');
                if (newCustomerOption) {
                    newCustomerOption.addEventListener('click', () => {
                        this.selectCustomerFromResult(newCustomerOption);
                    });
                }
                
                this.showCustomerSearchDropdown();
            }
        } catch (error) {
            console.error('Error searching customers:', error);
            const resultsContainer = document.getElementById('customerSearchResults');
            resultsContainer.innerHTML = '<div class="text-danger p-2">Error searching customers</div>';
            this.showCustomerSearchDropdown();
        }
    }

    selectCustomerFromResult(resultElement) {
        const customerId = resultElement.dataset.customerId;
        const name = resultElement.dataset.name;
        const phone = resultElement.dataset.phone;
        const address = resultElement.dataset.address;
        const hasPending = resultElement.dataset.hasPending === 'true';
        
        // Fill customer information
        document.getElementById('customerName').value = name;
        document.getElementById('customerPhone').value = phone;
        document.getElementById('customerAddress').value = address;
        document.getElementById('existingCustomerId').value = customerId;
        
        // Clear search input but keep value for reference
        document.getElementById('customerSearchInput').value = name;
        this.hideCustomerSearchDropdown();
        
        // Show existing orders info if customer has pending orders and it's not a new customer
        if (hasPending && customerId !== 'new') {
            this.loadCustomerPendingOrders(name, phone);
        } else {
            document.getElementById('existingOrdersInfo').style.display = 'none';
            document.getElementById('linkToExistingOrder').value = 'false';
            document.getElementById('existingOrderId').value = '';
        }
        
        showToast('Customer information loaded successfully', 'success');
    }

    async loadCustomerPendingOrders(customerName, customerPhone) {
        try {
            const response = await fetch(`/orders/customer-pending-orders?name=${encodeURIComponent(customerName)}&phone=${encodeURIComponent(customerPhone)}`);
            const data = await response.json();
            
            // Ensure select element exists before manipulating
            const select = document.getElementById('existingOrdersSelect');
            const existingOrdersInfo = document.getElementById('existingOrdersInfo');
            const pendingOrdersCount = document.getElementById('pendingOrdersCount');
            
            if (!select || !existingOrdersInfo) {
                console.error('Required elements not found for pending orders');
                return;
            }
            
            if (data.success && data.orders && data.orders.length > 0) {
                if (pendingOrdersCount) {
                    pendingOrdersCount.textContent = data.orders.length;
                }
                
                // Clear existing options
                select.innerHTML = '';
                
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select order to add items to...';
                select.appendChild(defaultOption);
                
                // Add order options
                data.orders.forEach(order => {
                    const option = document.createElement('option');
                    option.value = order.id;
                    option.textContent = `${order.order_number} - ₱${parseFloat(order.total).toFixed(2)} (${order.status})`;
                    select.appendChild(option);
                });
                
                // Make section visible and force reflow
                existingOrdersInfo.style.display = 'block';
                // Trigger reflow to ensure dropdown visibility
                void existingOrdersInfo.offsetHeight;
            } else {
                // No pending orders found
                existingOrdersInfo.style.display = 'none';
                select.innerHTML = '<option value="">Select order to add items to...</option>';
                document.getElementById('linkToExistingOrder').value = 'false';
                document.getElementById('existingOrderId').value = '';
            }
        } catch (error) {
            console.error('Error loading customer pending orders:', error);
            const existingOrdersInfo = document.getElementById('existingOrdersInfo');
            if (existingOrdersInfo) {
                existingOrdersInfo.style.display = 'none';
            }
            showToast('Error loading pending orders: ' + error.message, 'error');
        }
    }

    filterProducts() {
        const searchTerm = document.getElementById('searchProducts').value.toLowerCase();
        const category = document.getElementById('categoryFilter').value;

        const filtered = this.products.filter(product => {
            const matchesSearch = product.product_name.toLowerCase().includes(searchTerm) ||
                                product.product_code.toLowerCase().includes(searchTerm);
            const matchesCategory = !category || product.category === category;
            return matchesSearch && matchesCategory;
        });

        this.renderProducts(filtered);
    }

    addToCart(productId) {
        const product = this.products.find(p => p.id === productId);
        if (!product) {
            showToast('Product not found!', 'error');
            return;
        }

        // Check if product has sufficient stock
        if (product.quantity <= 0) {
            showToast('This product is out of stock!', 'warning');
            return;
        }

        const existingItem = this.cart.find(item => item.id === productId);
        
        if (existingItem) {
            // Check if adding more would exceed stock
            if (existingItem.quantity + 1 > product.quantity) {
                showToast(`Only ${product.quantity} ${product.unit} available in stock!`, 'warning');
                return;
            }
            existingItem.quantity += 1;
        } else {
            this.cart.push({
                id: product.id,
                product_name: product.product_name,
                product_code: product.product_code,
                price: product.selling_price,
                quantity: 1,
                unit: product.unit
            });
        }

        this.renderCart();
    }

    removeFromCart(cartItem) {
        const productId = parseInt(cartItem.dataset.productId);
        this.cart = this.cart.filter(item => item.id !== productId);
        this.renderCart();
    }

    adjustQuantity(cartItem, change) {
        const productId = parseInt(cartItem.dataset.productId);
        const item = this.cart.find(item => item.id === productId);
        if (!item) return;

        const product = this.products.find(p => p.id === productId);
        const input = cartItem.querySelector('.quantity-input');
        const isPack = product && product.unit.toLowerCase() === 'pack';
        
        let newQuantity = parseFloat(input.value) + change;
        
        // For pack items, ensure whole numbers
        if (isPack) {
            newQuantity = Math.round(newQuantity);
            if (newQuantity < 1) newQuantity = 1;
        } else {
            if (newQuantity < 0.1) newQuantity = 0.1;
        }
        
        // Check stock availability
        if (product && newQuantity > product.quantity) {
            showToast(`Only ${product.quantity} ${product.unit} available in stock!`, 'warning');
            newQuantity = product.quantity;
        }
        
        item.quantity = newQuantity;
        input.value = isPack ? newQuantity : newQuantity.toFixed(1);
        this.updateItemTotal(cartItem);
    }

    updateItemTotal(cartItem) {
        const productId = parseInt(cartItem.dataset.productId);
        const item = this.cart.find(item => item.id === productId);
        if (!item) return;

        const product = this.products.find(p => p.id === productId);
        const input = cartItem.querySelector('.quantity-input');
        const isPack = product && product.unit.toLowerCase() === 'pack';
        
        let newQuantity = parseFloat(input.value);
        
        // For pack items, ensure whole numbers
        if (isPack) {
            newQuantity = Math.round(newQuantity);
            if (newQuantity < 1) newQuantity = 1;
        }
        
        // Validate against stock
        if (product && newQuantity > product.quantity) {
            showToast(`Only ${product.quantity} ${product.unit} available in stock!`, 'warning');
            newQuantity = product.quantity;
            input.value = isPack ? newQuantity : newQuantity.toFixed(1);
        }
        
        item.quantity = newQuantity;
        this.renderCart();
    }

    calculateChange() {
        const amountPaid = parseFloat(document.querySelector('input[name="amount_paid"]').value) || 0;
        const total = this.getCartTotal();
        const change = amountPaid - total;

        document.querySelector('input[name="change"]').value = change >= 0 ? change.toFixed(2) : '0.00';
        
        // Update payment status
        const statusElement = document.getElementById('paymentStatus');
        if (amountPaid === 0) {
            statusElement.textContent = 'Enter payment amount';
            statusElement.className = 'badge bg-secondary';
        } else if (change >= 0) {
            statusElement.textContent = 'Payment OK';
            statusElement.className = 'badge bg-success';
        } else {
            statusElement.textContent = 'Insufficient payment';
            statusElement.className = 'badge bg-danger';
        }
    }

    getCartTotal() {
        let subtotal = 0;
        this.cart.forEach(item => {
            subtotal += item.price * item.quantity;
        });
        const tax = subtotal * 0.12;
        return subtotal + tax;
    }

    renderCart() {
        const container = document.getElementById('cartItems');
        const template = document.getElementById('cartItemTemplate');
        const orderSummary = document.getElementById('orderSummary');

        if (this.cart.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <p>No items in cart</p>
                </div>
            `;
            orderSummary.style.display = 'none';
            return;
        }

        container.innerHTML = '';
        let subtotal = 0;

        this.cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;

            // Check if unit is "pack"
            const isPack = item.unit.toLowerCase() === 'pack';
            const quantityDisplay = isPack ? Math.round(item.quantity) : item.quantity.toFixed(1);
            const quantityMin = isPack ? '1' : '0.1';
            const quantityStep = isPack ? '1' : '0.1';
            const quantityReadonly = '';
            const qtyBtnStyle = '';
            const quantityControlsStyle = '';

            const html = template.innerHTML
                .replace(/{id}/g, item.id)
                .replace(/{product_name}/g, this.escapeHtml(item.product_name))
                .replace(/{product_code}/g, this.escapeHtml(item.product_code))
                .replace(/{price}/g, item.price)
                .replace(/{quantity}/g, quantityDisplay)
                .replace(/{unit}/g, this.escapeHtml(item.unit))
                .replace(/{total}/g, itemTotal.toFixed(2))
                .replace(/{quantity_min}/g, quantityMin)
                .replace(/{quantity_step}/g, quantityStep)
                .replace(/{quantity_readonly}/g, quantityReadonly)
                .replace(/{qty_btn_style}/g, qtyBtnStyle)
                .replace(/{quantity_controls_style}/g, quantityControlsStyle);

            container.innerHTML += html;
        });

        const tax = subtotal * 0.12;
        const total = subtotal + tax;

        document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
        document.getElementById('tax').textContent = `₱${tax.toFixed(2)}`;
        document.getElementById('total').textContent = `₱${total.toFixed(2)}`;

        // Update payment calculation
        this.calculateChange();

        orderSummary.style.display = 'block';
    }

    async submitOrder(status, printReceipt = false) {
        if (this.cart.length === 0) {
            showToast('Please add items to the cart before submitting order.', 'warning');
            return;
        }

        const formData = new FormData(document.getElementById('orderForm'));
        const customerType = formData.get('customer_type');
        const linkToExistingOrder = formData.get('link_to_existing_order') === 'true';
        const existingOrderId = formData.get('existing_order_id');
        let paymentStatus = 'paid';
        
        if (customerType === 'regular' && status === 'pending') {
            paymentStatus = 'credit';
        }
        
        const amountPaid = parseFloat(formData.get('amount_paid')) || 0;
        const total = this.getCartTotal();
        const change = amountPaid - total;

        // For credit orders, require customer info
        if (paymentStatus === 'credit') {
            const customerName = formData.get('customer_name');
            const customerPhone = formData.get('customer_phone');
            if (!customerName || !customerPhone) {
                showToast('Customer name and phone are required for credit orders!', 'error');
                return;
            }
        }

        // Validate payment for completed (cash) orders
        if (status === 'completed' && amountPaid < total) {
            showToast(`Insufficient payment! Total: ₱${total.toFixed(2)}, Paid: ₱${amountPaid.toFixed(2)}`, 'error');
            return;
        }

        // Validate stock for completed orders
        if (status === 'completed') {
            for (const item of this.cart) {
                const product = this.products.find(p => p.id === item.id);
                if (!product) {
                    showToast(`Product ${item.product_name} no longer available!`, 'error');
                    return;
                }
                if (item.quantity > product.quantity) {
                    showToast(`Insufficient stock for ${item.product_name}. Available: ${product.quantity} ${product.unit}`, 'error');
                    return;
                }
            }
        }

        const orderData = {
            customer_name: formData.get('customer_name'),
            customer_phone: formData.get('customer_phone'),
            customer_address: formData.get('customer_address'),
            customer_type: customerType,
            payment_status: paymentStatus,
            amount_paid: paymentStatus === 'credit' ? 0 : amountPaid,
            change: paymentStatus === 'credit' ? 0 : (change >= 0 ? change : 0),
            notes: formData.get('notes'),
            status: status,
            items: this.cart.map(item => ({
                inventory_id: item.id,
                quantity: item.quantity,
                price: item.price,
                unit: item.unit
            }))
        };

        // Add linking information if applicable
        if (linkToExistingOrder && existingOrderId) {
            orderData.link_to_existing_order = true;
            orderData.existing_order_id = existingOrderId;
        }

        try {
            const url = linkToExistingOrder && existingOrderId 
                ? `/orders/${existingOrderId}/add-items` 
                : '{{ route("orders.store") }}';
                
            const method = linkToExistingOrder && existingOrderId ? 'POST' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (result.success) {
                if (linkToExistingOrder && existingOrderId) {
                    showToast('Items added to existing order successfully!', 'success');
                } else {
                    if (status === 'completed') {
                        if (printReceipt) {
                            // Open receipt in new window
                            window.open(`/orders/${result.order_id}/print`, '_blank');
                            showToast('Order completed successfully! Receipt opened in new window.', 'success');
                        } else {
                            showToast('Order completed successfully!', 'success');
                        }
                    } else {
                        showToast('Order saved as pending successfully!', 'success');
                    }
                }
                
                // Reset cart and reload products
                this.cart = [];
                this.renderCart();
                document.getElementById('orderForm').reset();
                // Reset to walk-in customer
                document.getElementById('walkInCustomer').checked = true;
                document.getElementById('regularCustomerSection').style.display = 'none';
                document.getElementById('existingOrdersInfo').style.display = 'none';
                document.getElementById('customerSearchInput').value = '';
                await this.loadProducts(); // Reload products to update stock
                
            } else {
                showToast('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error submitting order:', error);
            showToast('Failed to submit order. Please try again.', 'error');
        }
    }

    async viewPendingOrders() {
        try {
            const response = await fetch('{{ route("orders.pending") }}');
            const data = await response.json();

            if (data.success) {
                this.renderPendingOrders(data.orders);
                new bootstrap.Modal(document.getElementById('pendingOrdersModal')).show();
            } else {
                showToast('Failed to load pending orders', 'error');
            }
        } catch (error) {
            console.error('Error loading pending orders:', error);
            showToast('Failed to load pending orders', 'error');
        }
    }

    renderPendingOrders(orders) {
        const container = document.getElementById('pendingOrdersList');
        const template = document.getElementById('pendingOrderTemplate');

        if (orders.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <h5>No Pending Orders</h5>
                    <p>All orders have been completed</p>
            </div>
            `;
            return;
        }

        container.innerHTML = '';
        orders.forEach(order => {
            const html = template.innerHTML
                .replace(/{id}/g, order.id)
                .replace(/{order_number}/g, order.order_number)
                .replace(/{customer_name}/g, order.customer_name || 'Walk-in Customer')
                .replace(/{created_at}/g, new Date(order.created_at).toLocaleString())
                .replace(/{items_count}/g, order.items.length)
                .replace(/{total}/g, parseFloat(order.total).toFixed(2));

            const orderElement = document.createElement('div');
            orderElement.innerHTML = html;
            
            // Add order items
            const itemsContainer = orderElement.querySelector('.order-items');
            order.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'd-flex justify-content-between small py-1 border-bottom';
                itemElement.innerHTML = `
                    <span>${this.escapeHtml(item.product_name)}</span>
                    <span>${item.quantity} ${this.escapeHtml(item.unit)} × ₱${parseFloat(item.price).toFixed(2)}</span>
                `;
                itemsContainer.appendChild(itemElement);
            });

            container.appendChild(orderElement);
        });
    }

    confirmCompletePendingOrder(orderId) {
        // Store orderId for later use
        window.pendingOrderId = orderId;
        
        // Show modal instead of confirm dialog
        const modal = new bootstrap.Modal(document.getElementById('pendingReceiptModal'));
        modal.show();
    }

    async completePendingOrder(orderId, printReceipt = false) {
        try {
            const response = await fetch(`/orders/${orderId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                if (printReceipt) {
                    window.open(`/orders/${orderId}/print`, '_blank');
                    showToast('Order completed successfully! Receipt opened in new window.', 'success');
                } else {
                    showToast('Order completed successfully!', 'success');
                }
                this.viewPendingOrders(); // Refresh the list
                await this.loadProducts(); // Reload products
            } else {
                showToast('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error completing order:', error);
            showToast('Failed to complete order', 'error');
        }
    }

    async deletePendingOrder(orderId) {
        if (!confirm('Delete this pending order? This action cannot be undone.')) return;

        try {
            const response = await fetch(`/orders/${orderId}/delete-pending`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                showToast('Pending order deleted successfully!', 'success');
                this.viewPendingOrders(); // Refresh the list
            } else {
                showToast('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error deleting order:', error);
            showToast('Failed to delete order', 'error');
        }
    }
}

// Initialize order manager when page loads
let orderManager;

document.addEventListener('DOMContentLoaded', () => {
     orderManager = new OrderManager();
     
     // Handle customer type change
     document.querySelectorAll('input[name="customer_type"]').forEach(radio => {
         radio.addEventListener('change', function() {
             const isRegular = this.value === 'regular';
             const regularCustomerSection = document.getElementById('regularCustomerSection');
             const customerInfoLabel = document.getElementById('customerInfoLabel');
             const customerName = document.getElementById('customerName');
             const customerPhone = document.getElementById('customerPhone');
             const existingOrdersInfo = document.getElementById('existingOrdersInfo');
             const existingOrdersSelect = document.getElementById('existingOrdersSelect');
             
             if (isRegular) {
                 regularCustomerSection.style.display = 'block';
                 customerInfoLabel.textContent = '(Required for credit)';
                 customerName.setAttribute('required', 'required');
                 customerPhone.setAttribute('required', 'required');
                 
                 // Ensure select dropdown is properly initialized
                 if (existingOrdersSelect) {
                     existingOrdersSelect.innerHTML = '<option value="">Select order to add items to...</option>';
                 }
                 
                 // Focus on search input
                 setTimeout(() => {
                     const searchInput = document.getElementById('customerSearchInput');
                     if (searchInput) {
                         searchInput.focus();
                         searchInput.value = '';
                     }
                 }, 100);
             } else {
                 regularCustomerSection.style.display = 'none';
                 customerInfoLabel.textContent = '(Optional)';
                 customerName.removeAttribute('required');
                 customerPhone.removeAttribute('required');
                 existingOrdersInfo.style.display = 'none';
                 
                 // Ensure select dropdown is reset
                 if (existingOrdersSelect) {
                     existingOrdersSelect.innerHTML = '<option value="">Select order to add items to...</option>';
                 }
                 
                 const customerSearchInput = document.getElementById('customerSearchInput');
                 if (customerSearchInput) {
                     customerSearchInput.value = '';
                 }
                 
                 const customerSearchResults = document.getElementById('customerSearchResults');
                 if (customerSearchResults) {
                     customerSearchResults.innerHTML = '';
                 }
                 
                 document.getElementById('linkToExistingOrder').value = 'false';
                 document.getElementById('existingOrderId').value = '';
                 document.getElementById('existingCustomerId').value = '';
                orderManager.hideCustomerSearchDropdown();
            }
        });
    });
    
    // Add click event to customer search results (delegated)
    document.addEventListener('click', (e) => {
        if (e.target.closest('.customer-search-result')) {
            const result = e.target.closest('.customer-search-result');
            orderManager.selectCustomerFromResult(result);
        }
    });
});

// Make orderManager globally available for the retry button
window.orderManager = orderManager;
</script>

<style>
.customer-search-result {
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.customer-search-result:hover {
    background-color: #f8f9fa;
    border-left-color: var(--primary-color);
    transform: translateX(2px);
}

.customer-search-result.active {
    background-color: #e9ecef;
    border-left-color: var(--primary-color);
}

#customerSearchDropdown {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    margin-top: 2px;
    z-index: 1050;
}

#customerSearchInput {
    position: relative;
    z-index: 1051;
}

.position-relative {
    position: relative;
}

.text-warning {
    color: #ffc107 !important;
}

.cursor-pointer {
    cursor: pointer;
}

.bg-light {
    background-color: #f8f9fa !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    #customerSearchDropdown {
        position: fixed !important;
        top: auto !important;
        left: 10px !important;
        right: 10px !important;
        width: calc(100% - 20px) !important;
        z-index: 1050;
    }
}
</style>
@endsection