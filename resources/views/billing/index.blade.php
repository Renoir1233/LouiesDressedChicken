<!-- resources/views/billing/index.blade.php -->
@extends('layouts.orders')

@section('title', 'Billing Management')
@section('page_title', 'Billing Management')

@section('content')
<div class="container-fluid">
    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #E8F5E9;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small" style="color: #388E3C;">Today's Sales</p>
                            <h2 id="todaySales" class="mb-0 fw-bold" style="color: #4CAF50;">₱0.00</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255,255,255,0.8);">
                            <i class="fas fa-shopping-cart fa-lg" style="color: #4CAF50;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #E3F2FD;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small" style="color: #1976D2;">Completed Today</p>
                            <h2 id="todayCompleted" class="mb-0 fw-bold" style="color: #2196F3;">0</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255,255,255,0.8);">
                            <i class="fas fa-check-circle fa-lg" style="color: #2196F3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #FFF3E0;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small" style="color: #F57C00;">Pending Orders</p>
                            <h2 id="pendingCount" class="mb-0 fw-bold" style="color: #FF9800;">0</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255,255,255,0.8);">
                            <i class="fas fa-clock fa-lg" style="color: #FF9800;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #F3E5F5;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 small" style="color: #7B1FA2;">Total Receivables</p>
                            <h2 id="totalReceivables" class="mb-0 fw-bold" style="color: #9C27B0;">₱0.00</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255,255,255,0.8);">
                            <i class="fas fa-money-bill-wave fa-lg" style="color: #9C27B0;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Overview Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Sales Analytics</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                id="periodDropdownBtn" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <span id="periodDisplay">Today</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item period-option" href="#" data-period="today">Today</a></li>
                            <li><a class="dropdown-item period-option" href="#" data-period="week">This Week</a></li>
                            <li><a class="dropdown-item period-option" href="#" data-period="month">This Month</a></li>
                            <li><a class="dropdown-item period-option" href="#" data-period="year">This Year</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item custom-range-option" href="#">
                                <i class="fas fa-calendar-range me-2"></i>Custom Range
                            </a></li>
                        </ul>
                        </div>
                        </div>

                        <!-- Custom Date Filter Section -->
                        <div id="customDateSection" class="card-body border-bottom pb-3" style="display: none;">
                        <div class="row g-2 mb-3">
                         <div class="col-6">
                             <label for="cardStartDate" class="form-label small mb-1">Start Date</label>
                             <input type="date" class="form-control form-control-sm" id="cardStartDate">
                         </div>
                         <div class="col-6">
                             <label for="cardEndDate" class="form-label small mb-1">End Date</label>
                             <input type="date" class="form-control form-control-sm" id="cardEndDate">
                         </div>
                        </div>
                        <div id="cardDateError" class="alert alert-danger alert-sm py-2 px-2 mb-2" style="display: none; font-size: 0.85rem;"></div>
                        <button type="button" class="btn btn-sm btn-primary w-100" id="applyCustomDateBtn">
                            <i class="fas fa-check me-1"></i>Apply
                        </button>
                        </div>

                        <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block">Total Orders</small>
                                <h4 id="totalOrders" class="mb-0">0</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block">Total Sales</small>
                                <h4 id="totalSales" class="mb-0">₱0.00</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="mb-4">
                        <h6 class="section-title mb-3">Payment Breakdown</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Cash Collected:</span>
                            <strong id="cashCollected" class="text-success">₱0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Outstanding:</span>
                            <strong id="outstandingAmount" class="text-warning">₱0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Avg Payment:</span>
                            <strong id="avgPayment" class="text-info">₱0.00</strong>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="billingManager.printReport()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                        <button class="btn btn-outline-primary" onclick="billingManager.viewAllOrders()">
                            <i class="fas fa-list me-2"></i>View All Orders
                        </button>
                    </div>
                </div>
            </div>


        </div>

        <!-- Main Content Area -->
        <div class="col-lg-8">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="billingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" 
                            data-bs-target="#pending-tab-pane" type="button">
                        <i class="fas fa-clock me-2"></i>Pending & Partial
                        <span id="pendingBadge" class="badge bg-warning ms-2">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" 
                            data-bs-target="#completed-tab-pane" type="button">
                        <i class="fas fa-check-circle me-2"></i>Completed
                        <span id="completedBadge" class="badge bg-success ms-2">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-tab" data-bs-toggle="tab" 
                            data-bs-target="#all-tab-pane" type="button">
                        <i class="fas fa-list me-2"></i>All Orders
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="billingTabsContent">
                <!-- Pending Orders Tab -->
                <div class="tab-pane fade show active" id="pending-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Orders Requiring Payment</h5>
                                <div class="d-flex gap-2">
                                    <input type="text" id="searchPending" class="form-control form-control-sm" 
                                           placeholder="Search orders...">
                                    <select id="pendingFilter" class="form-select form-select-sm">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="partial">Partial</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="pendingOrdersContainer" class="orders-container">
                                <!-- Pending orders will be loaded here -->
                            </div>
                            <div id="noPendingOrders" class="text-center py-5" style="display: none;">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success">No Pending Orders</h5>
                                <p class="text-muted">All orders have been processed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Orders Tab -->
                <div class="tab-pane fade" id="completed-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Completed Orders</h5>
                                <div class="d-flex gap-2">
                                    <input type="text" id="searchCompleted" class="form-control form-control-sm" 
                                           placeholder="Search completed orders...">
                                    <select id="dateFilter" class="form-select form-select-sm">
                                        <option value="today">Today</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="completedOrdersContainer" class="orders-container">
                                <!-- Completed orders will be loaded here -->
                            </div>
                            <div id="noCompletedOrders" class="text-center py-5" style="display: none;">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success">No Completed Orders</h5>
                                <p class="text-muted">No orders have been completed yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Orders Tab -->
                <div class="tab-pane fade" id="all-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">All Orders</h5>
                                <div class="d-flex gap-2">
                                    <input type="text" id="searchAll" class="form-control form-control-sm" 
                                           placeholder="Search all orders...">
                                    <select id="statusFilterAll" class="form-select form-select-sm">
                                        <option value="all">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="partial">Partial</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="allOrdersContainer" class="orders-container">
                                <!-- All orders will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i>Process Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="payment-summary p-3 border rounded mb-3">
                            <h6 class="mb-3">Order Summary</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Order Number:</span>
                                <strong id="paymentOrderNumber"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Customer:</span>
                                <strong id="paymentCustomer"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Amount:</span>
                                <strong id="paymentTotal" class="text-primary"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Amount Paid:</span>
                                <strong id="paymentPaid" class="text-success"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Remaining Balance:</span>
                                <strong id="paymentBalance" class="text-danger"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form id="paymentForm">
                            <input type="hidden" id="paymentOrderId">
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="payment_amount" id="paymentAmount" 
                                           class="form-control" placeholder="0.00" min="0.01" step="0.01" required>
                                </div>
                                <small class="text-muted">Max: <span id="maxPaymentAmount">0.00</span></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="paymentMethod" class="form-select" required>
                                    <option value="cash">Cash</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" id="paymentNotes" class="form-control" 
                                          rows="2" placeholder="Add payment notes..."></textarea>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Payment will be recorded and a receipt will be generated.</small>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="generateReceipt" checked>
                                <label class="form-check-label" for="generateReceipt">
                                    Generate receipt after payment
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="billingManager.processPayment()">
                    <i class="fas fa-money-bill-wave me-2"></i>Process Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i>Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               
            </div>
        </div>
    </div>
</div>

<!-- Receipts Modal -->
<div class="modal fade" id="receiptsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Payment Receipts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="receiptsContent">
                    <!-- Receipts will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
class BillingManager {
        filterAndRenderCompletedOrders() {
            const searchInput = document.getElementById('searchCompleted');
            const dateSelect = document.getElementById('dateFilter');
            let search = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let period = dateSelect ? dateSelect.value : 'today';
            // Optionally, reload completed orders if period changed
            if (this.lastCompletedPeriod !== period) {
                this.lastCompletedPeriod = period;
                this.loadCompletedOrders(period);
                return;
            }
            let filtered = this.orders.completed.filter(order => {
                let matchesSearch = !search || (
                    (order.order_number && order.order_number.toLowerCase().includes(search)) ||
                    (order.customer_name && order.customer_name.toLowerCase().includes(search))
                );
                return matchesSearch;
            });
            this.renderOrders('completed', filtered);
        }

        filterAndRenderPendingOrders() {
            const searchInput = document.getElementById('searchPending');
            const statusSelect = document.getElementById('pendingFilter');
            let search = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let status = statusSelect ? statusSelect.value : '';
            let filtered = this.orders.pending.filter(order => {
                let matchesStatus = (!status) || (order.status && order.status.toLowerCase() === status);
                let matchesSearch = !search || (
                    (order.order_number && order.order_number.toLowerCase().includes(search)) ||
                    (order.customer_name && order.customer_name.toLowerCase().includes(search))
                );
                return matchesStatus && matchesSearch;
            });
            this.renderOrders('pending', filtered);
        }
    async loadAllOrders(period = 'today') {
        try {
            const response = await fetch(`/billing/all-orders?date=${period}`);
            const data = await response.json();
            if (data.success) {
                this.orders.all = data.orders || [];
                this.filterAndRenderAllOrders();
            }
        } catch (error) {
            console.error('Error loading all orders:', error);
        }
    }

    filterAndRenderAllOrders() {
        const searchInput = document.getElementById('searchAll');
        const statusSelect = document.getElementById('statusFilterAll');
        let search = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let status = statusSelect ? statusSelect.value : 'all';
        let filtered = this.orders.all.filter(order => {
            let matchesStatus = (status === 'all') || (order.status && order.status.toLowerCase() === status);
            let matchesSearch = !search || (
                (order.order_number && order.order_number.toLowerCase().includes(search)) ||
                (order.customer_name && order.customer_name.toLowerCase().includes(search))
            );
            return matchesStatus && matchesSearch;
        });
        this.renderOrders('all', filtered);
    }

            viewAllOrders() {
                // Switch to All Orders tab
                const allTab = document.getElementById('all-tab');
                if (allTab) {
                    new bootstrap.Tab(allTab).show();
                }
            }
        printReport() {
            // Get the selected period from the dropdown
            const period = document.getElementById('periodDisplay')?.textContent?.toLowerCase() || 'today';
            // Map display text to period value used in backend
            let periodValue = 'today';
            if (period.includes('week')) periodValue = 'week';
            else if (period.includes('month')) periodValue = 'month';
            else if (period.includes('year')) periodValue = 'year';
            // Open the print report page in a new tab
            window.open(`/billing/print-report?period=${periodValue}`, '_blank');
        }
    constructor() {
        this.orders = {
            pending: [],
            completed: [],
            all: []
        };
        this.currentPeriod = 'today';
        this.currentTab = 'pending';
        this.selectedOrderId = null;
        this.isCustomRange = false;
        this.customStartDate = null;
        this.customEndDate = null;
        this.init();
    }

    async init() {
         await this.loadDashboardStats('today');
         await this.loadPendingOrders();
         // await this.loadRecentTransactions(); // TODO: Implement this route
         this.setupEventListeners();
         this.startAutoRefresh();
     }

    async loadDashboardStats(period = 'today', startDate = null, endDate = null) {
        try {
            let url = `{{ route("billing.reports") }}?period=${period}`;
            if (startDate && endDate) {
                url += `&start_date=${startDate}&end_date=${endDate}`;
            }
            console.log('Fetching dashboard stats with URL:', url);
            const response = await fetch(url);
            const data = await response.json();
            console.log('Dashboard stats response:', data);

            if (data.success) {
                this.updateDashboardStats(data);
            }
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
        }
    }

    updateDateInputsForPeriod(period) {
        const startInput = document.getElementById('cardStartDate');
        const endInput = document.getElementById('cardEndDate');
        
        if (!startInput || !endInput) return;
        
        const today = new Date();
        let startDate, endDate;
        
        switch (period) {
            case 'today':
                startDate = new Date(today);
                endDate = new Date(today);
                break;
            case 'week':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - today.getDay());
                endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 6);
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date(today.getFullYear(), 11, 31);
                break;
            default:
                return;
        }
        
        // Format dates as YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        startInput.value = formatDate(startDate);
        endInput.value = formatDate(endDate);
    }

    applyCustomDateRange() {
        const startInput = document.getElementById('cardStartDate');
        const endInput = document.getElementById('cardEndDate');
        const errorDiv = document.getElementById('cardDateError');

        if (!startInput || !endInput) {
            console.error('Date input fields not found');
            return;
        }

        const startDate = startInput.value;
        const endDate = endInput.value;

        // Validation
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
        if (!startDate || !endDate) {
            if (errorDiv) {
                errorDiv.textContent = 'Please select both start and end dates';
                errorDiv.style.display = 'block';
            }
            return;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);
        if (start > end) {
            if (errorDiv) {
                errorDiv.textContent = 'Start date must be before end date';
                errorDiv.style.display = 'block';
            }
            return;
        }

        // Set custom mode
        this.isCustomRange = true;
        this.customStartDate = startDate;
        this.customEndDate = endDate;
        this.currentPeriod = 'custom';

        // Update dropdown to show "Custom Range"
        document.getElementById('periodDisplay').textContent = 'Custom Range';

        // Close the dropdown
        const dropdownBtn = document.getElementById('periodDropdownBtn');
        if (dropdownBtn) {
            const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownBtn) || new bootstrap.Dropdown(dropdownBtn);
            dropdownInstance.hide();
        }

        // Load data with custom dates
        this.loadDashboardStats('custom', startDate, endDate);
        this.loadCompletedOrdersCustom(startDate, endDate);
    }

    async loadCompletedOrdersCustom(startDate, endDate) {
        try {
            const response = await fetch(`{{ route("billing.completed-orders") }}?date=custom&start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();
            if (data.success) {
                this.orders.completed = data.orders || [];
                this.filterAndRenderCompletedOrders();
            }
        } catch (error) {
            console.error('Error loading completed orders:', error);
        }
    }

    updateDashboardStats(data) {
        // Update quick stats
        document.getElementById('todaySales').textContent = `₱${parseFloat(data.sales_data.total_sales || 0).toFixed(2)}`;
        document.getElementById('todayCompleted').textContent = data.sales_data.completed_orders || 0;
        document.getElementById('pendingCount').textContent = data.sales_data.pending_orders || 0;
        
        // Use backend calculated outstanding balance (always positive)
        const outstandingBalance = Math.max(0, parseFloat(data.sales_data.outstanding_balance || 0));
        document.getElementById('totalReceivables').textContent = `₱${outstandingBalance.toFixed(2)}`;

        // Update analytics
        document.getElementById('totalOrders').textContent = data.sales_data.total_orders || 0;
        document.getElementById('totalSales').textContent = `₱${parseFloat(data.sales_data.total_sales || 0).toFixed(2)}`;
        document.getElementById('cashCollected').textContent = `₱${parseFloat(data.sales_data.cash_payments || 0).toFixed(2)}`;
        document.getElementById('outstandingAmount').textContent = `₱${outstandingBalance.toFixed(2)}`;
        document.getElementById('avgPayment').textContent = `₱${parseFloat(data.sales_data.average_payment || 0).toFixed(2)}`;

        // Update badges
        document.getElementById('pendingBadge').textContent = data.sales_data.pending_orders || 0;
        document.getElementById('completedBadge').textContent = data.sales_data.completed_orders || 0;
    }

    async loadPendingOrders() {
        try {
            const response = await fetch('{{ route("billing.pending-orders") }}');
            const data = await response.json();
            if (data.success) {
                this.orders.pending = data.orders || [];
                this.filterAndRenderPendingOrders();
            }
        } catch (error) {
            console.error('Error loading pending orders:', error);
        }
    }

    async loadCompletedOrders(period = 'today') {
        try {
            const response = await fetch(`{{ route("billing.completed-orders") }}?date=${period}`);
            const data = await response.json();
            if (data.success) {
                this.orders.completed = data.orders || [];
                this.filterAndRenderCompletedOrders();
            }
        } catch (error) {
            console.error('Error loading completed orders:', error);
        }
    }

    async loadRecentTransactions() {
        try {
            const response = await fetch('/recent-transactions'); // You'll need to create this route
            const data = await response.json();

            if (data.success) {
                this.renderRecentTransactions(data.transactions);
            }
        } catch (error) {
            console.error('Error loading recent transactions:', error);
        }
    }

    renderOrders(type, orders) {
        const containerId = `${type}OrdersContainer`;
        const noOrdersId = `no${type.charAt(0).toUpperCase() + type.slice(1)}Orders`;
        const container = document.getElementById(containerId);
        const noOrdersElement = document.getElementById(noOrdersId);

        if (!orders || orders.length === 0) {
            container.style.display = 'none';
            if (noOrdersElement) noOrdersElement.style.display = 'block';
            return;
        }

        if (noOrdersElement) noOrdersElement.style.display = 'none';
        container.style.display = 'block';
        container.innerHTML = '';

        orders.forEach(order => {
            const orderElement = this.createOrderCard(order, type);
            container.appendChild(orderElement);
        });
    }

    createOrderCard(order, type) {
        const isPending = type === 'pending';
        const statusColor = this.getStatusColor(order.status);
        
        const card = document.createElement('div');
        card.className = 'order-card p-3 border-bottom';
        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="mb-0 me-3">${this.escapeHtml(order.order_number)}</h6>
                        <span class="badge ${statusColor}">${order.status}</span>
                        ${order.payment_count > 0 ? `<span class="badge bg-info ms-2">${order.payment_count} payment(s)</span>` : ''}
                    </div>
                    <p class="mb-1"><i class="fas fa-user me-2"></i>${this.escapeHtml(order.customer_name)}</p>
                    <small class="text-muted"><i class="fas fa-calendar me-2"></i>${order.created_at}</small>
                </div>
                <div class="text-end">
                    ${order.remaining_balance > 0 ? `
                        <div class="text-warning mb-1">
                            <h5 class="mb-0">₱${parseFloat(order.remaining_balance).toFixed(2)}</h5>
                        </div>
                    ` : ''}
                    <h6 class="text-muted mb-1">₱${parseFloat(order.total_amount).toFixed(2)}</h6>
                    <div class="mt-2">
                        ${isPending ? `
                            <button class="btn btn-sm btn-success me-1 pay-btn" data-id="${order.id}">
                                <i class="fas fa-money-bill-wave me-1"></i>Pay
                            </button>
                        ` : ''}
                        <button class="btn btn-sm btn-outline-primary me-1 details-btn" data-id="${order.id}">
                            <i class="fas fa-eye me-1"></i>Details
                        </button>
                        ${order.payment_count > 0 ? `
                            <button class="btn btn-sm btn-outline-info receipts-btn" data-id="${order.id}">
                                <i class="fas fa-receipt me-1"></i>Receipts
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;

        return card;
    }

    getStatusColor(status) {
        switch(status.toLowerCase()) {
            case 'pending': return 'bg-warning';
            case 'partial': return 'bg-info';
            case 'completed': return 'bg-success';
            case 'cancelled': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }

    openPaymentModal(orderId) {
        const order = this.orders.pending.find(o => o.id == orderId) || 
                      this.orders.all.find(o => o.id == orderId);
        
        if (!order) {
            this.showToast('Order not found!', 'error');
            return;
        }

        this.selectedOrderId = orderId;

        const totalAmount = parseFloat(order.total_amount) || 0;
        const amountPaid = parseFloat(order.amount_paid) || 0;
        const remainingBalance = parseFloat(order.remaining_balance) || 0;

        document.getElementById('paymentOrderId').value = orderId;
        document.getElementById('paymentOrderNumber').textContent = order.order_number;
        document.getElementById('paymentCustomer').textContent = order.customer_name;
        document.getElementById('paymentTotal').textContent = `₱${totalAmount.toFixed(2)}`;
        document.getElementById('paymentPaid').textContent = `₱${amountPaid.toFixed(2)}`;
        document.getElementById('paymentBalance').textContent = `₱${remainingBalance.toFixed(2)}`;
        document.getElementById('maxPaymentAmount').textContent = remainingBalance.toFixed(2);
        document.getElementById('paymentAmount').value = '';
        document.getElementById('paymentAmount').max = remainingBalance;

        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }

    async processPayment() {
        const orderId = document.getElementById('paymentOrderId').value;
        const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);
        const paymentMethod = document.getElementById('paymentMethod').value;
        const notes = document.getElementById('paymentNotes').value;
        const generateReceipt = document.getElementById('generateReceipt').checked;

        if (!paymentAmount || paymentAmount <= 0 || isNaN(paymentAmount)) {
            this.showToast('Please enter a valid payment amount', 'error');
            return;
        }

        const submitBtn = document.querySelector('#paymentModal .btn-primary');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        submitBtn.disabled = true;

        try {
            const response = await fetch(`/billing/${orderId}/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_amount: paymentAmount,
                    payment_method: paymentMethod,
                    notes: notes
                })
            });
            
            const result = await response.json();

            if (result.success) {
                this.showToast('Payment processed successfully!', 'success');
                
                // Close modal
                const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                paymentModal.hide();

                // Reload data
                await Promise.all([
                    this.loadDashboardStats(),
                    this.loadPendingOrders(),
                    this.loadCompletedOrders()
                ]);

                // Generate receipt if requested
                if (generateReceipt && result.payment_reference) {
                    this.generateReceipt(result.payment_reference);
                }

            } else {
                this.showToast('Error: ' + result.message, 'error');
            }

        } catch (error) {
            console.error('Error processing payment:', error);
            this.showToast('Failed to process payment: ' + error.message, 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    async processFullPayment() {
        const orderId = document.getElementById('paymentOrderId').value;
        
        const submitBtn = document.querySelector('#paymentModal .btn-success');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        submitBtn.disabled = true;

        try {
            const response = await fetch(`/billing/${orderId}/process-full-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();

            if (result.success) {
                this.showToast('Full payment processed successfully!', 'success');
                
                const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                paymentModal.hide();

                await Promise.all([
                    this.loadDashboardStats(),
                    this.loadPendingOrders(),
                    this.loadCompletedOrders()
                ]);

                if (result.payment_reference) {
                    this.generateReceipt(result.payment_reference);
                }

            } else {
                this.showToast('Error: ' + result.message, 'error');
            }

        } catch (error) {
            console.error('Error processing full payment:', error);
            this.showToast('Failed to process payment: ' + error.message, 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    async generateReceipt(referenceNumber) {
        try {
            window.open(`/billing/receipt/${referenceNumber}`, '_blank');
        } catch (error) {
            console.error('Error generating receipt:', error);
            this.showToast('Failed to generate receipt', 'error');
        }
    }

    async viewOrderDetails(orderId) {
        try {
            const response = await fetch(`/orders/${orderId}/details`);
            const data = await response.json();

            if (data.success) {
                this.renderOrderDetails(data.order);
            }
        } catch (error) {
            console.error('Error loading order details:', error);
            this.showToast('Failed to load order details', 'error');
        }
    }

    renderOrderDetails(order) {
        const content = document.getElementById('orderDetailsContent');
        
        let itemsHtml = '';
        if (order.items && order.items.length > 0) {
            itemsHtml = `
                <div class=\"card mt-4\">
                    <div class=\"card-header\">
                        <h6 class=\"mb-0\">Purchased Products</h6>
                    </div>
                    <div class=\"card-body p-0\">
                        <table class=\"table table-bordered mb-0\">
                             <thead>
                                 <tr>
                                     <th>Date</th>
                                     <th>Product</th>
                                     <th>Quantity</th>
                                     <th>Unit</th>
                                     <th>Price</th>
                                     <th>Subtotal</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 ${order.items.map(item => `
                                     <tr>
                                         <td>${order.created_at ? new Date(order.created_at).toLocaleDateString() : 'N/A'}</td>
                                         <td>${this.escapeHtml(item.inventory ? item.inventory.product_name : (item.product_name || 'Unknown Product'))}</td>
                                         <td>${item.quantity}</td>
                                         <td>${this.escapeHtml(item.unit || '')}</td>
                                         <td>₱${parseFloat(item.price || 0).toFixed(2)}</td>
                                         <td>₱${(parseFloat(item.price || 0) * parseFloat(item.quantity || 0)).toFixed(2)}</td>
                                     </tr>
                                 `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }
        // Fallbacks for totals
        const totalAmount = parseFloat(order.total_amount || order.total || 0).toFixed(2);
        const amountPaid = parseFloat(order.amount_paid || 0).toFixed(2);
        const remainingBalance = parseFloat(order.remaining_balance || 0).toFixed(2);
        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Order Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Order Number:</strong></td>
                                    <td>${this.escapeHtml(order.order_number)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>${this.escapeHtml(order.customer_name)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>${this.escapeHtml(order.customer_phone || 'N/A')}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge ${this.getStatusColor(order.status)}">${order.status}</span></td>
                                </tr>
                                <tr>
                                     <td><strong>Order Date:</strong></td>
                                     <td>${order.created_at ? new Date(order.created_at).toLocaleString() : 'N/A'}</td>
                                 </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Payment Summary</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="text-primary">₱${totalAmount}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount Paid:</strong></td>
                                    <td class="text-success">₱${amountPaid}</td>
                                </tr>
                                <tr>
                                    <td><strong>Remaining Balance:</strong></td>
                                    <td class="text-danger">₱${remainingBalance}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            ${itemsHtml}
        `;

        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        modal.show();
    }

    async viewPaymentReceipts(orderId) {
        try {
            const response = await fetch(`/billing/${orderId}/payments`);
            const data = await response.json();

            if (data.success) {
                this.renderReceipts(data);
            }
        } catch (error) {
            console.error('Error loading receipts:', error);
            this.showToast('Failed to load receipts', 'error');
        }
    }

    renderReceipts(data) {
        const content = document.getElementById('receiptsContent');
        const summary = data.payment_summary;
        let html = `
            <div class="mb-4">
                <h6>Order: ${this.escapeHtml(summary.order_number)}</h6>
                <p>Customer: ${this.escapeHtml(summary.customer_name)}</p>
            </div>
        `;

        if (summary.payments && summary.payments.length > 0) {
            summary.payments.forEach(payment => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${payment.reference_number}</h6>
                                    <small class="text-muted">${payment.payment_date}</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="text-success mb-1">₱${parseFloat(payment.amount).toFixed(2)}</h5>
                                    <button class="btn btn-sm btn-primary" onclick="billingManager.generateReceipt('${payment.reference_number}')">
                                        <i class="fas fa-print me-1"></i>Print Receipt
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += '<p class="text-center text-muted">No payments found</p>';
        }

        content.innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('receiptsModal'));
        modal.show();
    }

    setupEventListeners() {
        // Tab switching
        document.querySelectorAll('#billingTabs button').forEach(tab => {
            tab.addEventListener('shown.bs.tab', (event) => {
                const tabId = event.target.id.replace('-tab', '');
                this.currentTab = tabId;
                switch(tabId) {
                    case 'pending':
                        this.loadPendingOrders();
                        break;
                    case 'completed':
                        this.loadCompletedOrders(this.currentPeriod);
                        break;
                    case 'all':
                        this.loadAllOrders(this.currentPeriod);
                        break;
                }
            });
        });

        // All Orders: search and filter event listeners
        const searchAll = document.getElementById('searchAll');
        const statusFilterAll = document.getElementById('statusFilterAll');
        if (searchAll) {
            searchAll.addEventListener('input', () => {
                this.filterAndRenderAllOrders();
            });
        }
        if (statusFilterAll) {
            statusFilterAll.addEventListener('change', () => {
                this.filterAndRenderAllOrders();
            });
        }

        // Completed Orders: search and date filter event listeners
        const searchCompleted = document.getElementById('searchCompleted');
        const dateFilter = document.getElementById('dateFilter');
        if (searchCompleted) {
            searchCompleted.addEventListener('input', () => {
                this.filterAndRenderCompletedOrders();
            });
        }
        if (dateFilter) {
            dateFilter.addEventListener('change', () => {
                this.filterAndRenderCompletedOrders();
            });
        }

        // Pending & Partial Orders: search and status filter event listeners
        const searchPending = document.getElementById('searchPending');
        const pendingFilter = document.getElementById('pendingFilter');
        if (searchPending) {
            searchPending.addEventListener('input', () => {
                this.filterAndRenderPendingOrders();
            });
        }
        if (pendingFilter) {
            pendingFilter.addEventListener('change', () => {
                this.filterAndRenderPendingOrders();
            });
        }

        // Period selector
        document.querySelectorAll('.period-option').forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                const period = e.target.dataset.period;
                
                // Update dropdown display
                const displayText = period === 'today' ? 'Today' :
                                   period === 'week' ? 'This Week' :
                                   period === 'month' ? 'This Month' :
                                   period === 'year' ? 'This Year' : period;
                document.getElementById('periodDisplay').textContent = displayText;
                
                // Hide custom date section
                document.getElementById('customDateSection').style.display = 'none';
                
                // Reset custom range mode
                this.isCustomRange = false;
                this.currentPeriod = period;
                
                // Update date inputs to match the selected period
                this.updateDateInputsForPeriod(period);
                
                // Load all data with the new period
                this.loadDashboardStats(period);
                this.loadCompletedOrders(period);
            });
        });

        // Custom range option
        document.querySelector('.custom-range-option').addEventListener('click', (e) => {
            e.preventDefault();
            const customSection = document.getElementById('customDateSection');
            customSection.style.display = customSection.style.display === 'none' ? 'block' : 'none';
        });

        // Date input change listeners - switch to Custom Range mode
        const startDateInput = document.getElementById('cardStartDate');
        const endDateInput = document.getElementById('cardEndDate');
        
        if (startDateInput) {
            startDateInput.addEventListener('change', () => {
                if (this.currentPeriod !== 'custom') {
                    document.getElementById('periodDisplay').textContent = 'Custom Range';
                    this.isCustomRange = true;
                }
            });
        }
        
        if (endDateInput) {
            endDateInput.addEventListener('change', () => {
                if (this.currentPeriod !== 'custom') {
                    document.getElementById('periodDisplay').textContent = 'Custom Range';
                    this.isCustomRange = true;
                }
            });
        }

        // Apply custom date button
        const applyCustomDateBtn = document.getElementById('applyCustomDateBtn');
        if (applyCustomDateBtn) {
            applyCustomDateBtn.addEventListener('click', () => {
                this.applyCustomDateRange();
            });
        }

        // Event delegation for dynamic buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.pay-btn')) {
                const orderId = e.target.closest('.pay-btn').dataset.id;
                this.openPaymentModal(orderId);
            }
            if (e.target.closest('.details-btn')) {
                const orderId = e.target.closest('.details-btn').dataset.id;
                this.viewOrderDetails(orderId);
            }
            if (e.target.closest('.receipts-btn')) {
                const orderId = e.target.closest('.receipts-btn').dataset.id;
                this.viewPaymentReceipts(orderId);
            }
        });
    }

    showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        // Add to toast container
        const container = document.querySelector('.toast-container') || (() => {
            const div = document.createElement('div');
            div.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(div);
            return div;
        })();
        
        container.appendChild(toast);
        
        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove after hide
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    startAutoRefresh() {
        setInterval(() => {
            this.loadDashboardStats(this.currentPeriod);
            this.loadPendingOrders();
            // Refresh the current tab data if not pending
            if (this.currentTab === 'completed') {
                this.loadCompletedOrders(this.currentPeriod);
            } else if (this.currentTab === 'all') {
                this.loadAllOrders(this.currentPeriod);
            }
        }, 30000); // Refresh every 30 seconds
    }
}

let billingManager;
document.addEventListener('DOMContentLoaded', () => {
    billingManager = new BillingManager();
});
</script>

<style>
.stat-card {
    border: none;
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.order-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.order-card:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.orders-container {
    max-height: 500px;
    overflow-y: auto;
}

.section-title {
    color: #495057;
    font-weight: 600;
    position: relative;
    padding-bottom: 8px;
    margin-bottom: 15px;
}

.section-title:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    border-radius: 3px;
}

.payment-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
}

.toast-container {
    z-index: 9999;
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}
</style>

<script>
// Set default dates for custom range (last 30 days)
document.addEventListener('DOMContentLoaded', () => {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    document.getElementById('cardStartDate').valueAsDate = startDate;
    document.getElementById('cardEndDate').valueAsDate = endDate;
});
</script>

@endsection