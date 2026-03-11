@extends('layouts.inventory')

@section('content')
<div class="container-fluid px-4">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div>
                    <h1 class="mb-1 fw-bold" style="color: var(--primary-color); font-size: 2rem;">Dashboard Overview</h1>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">
                        <i class="fas fa-calendar-alt me-2"></i>{{ now()->format('l, F j, Y') }}
                    </p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <hr style="border-top: 2px solid var(--accent-color); opacity: 1; margin: 0;">
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Sales -->
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-uppercase mb-1 fw-semibold" style="color: #2E7D32; font-size: 0.7rem; letter-spacing: 0.5px;">Total Sales</p>
                            <h3 class="mb-0 fw-bold" style="color: #1B5E20; font-size: 1.5rem;">₱{{ number_format($stats['totalSales'], 2) }}</h3>
                        </div>
                        <div class="icon-box" style="background: rgba(255, 255, 255, 0.9); border-radius: 10px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <i class="fas fa-dollar-sign" style="color: #4CAF50; font-size: 1.3rem;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-2 pt-2" style="border-top: 1px solid rgba(46, 125, 50, 0.2);">
                        <i class="fas fa-calendar-day me-2" style="color: #4CAF50; font-size: 0.85rem;"></i>
                        <small class="mb-0" style="color: #2E7D32; font-weight: 500; font-size: 0.8rem;">
                            Today: <strong>₱{{ number_format($stats['todaySales'], 2) }}</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-uppercase mb-1 fw-semibold" style="color: #1565C0; font-size: 0.7rem; letter-spacing: 0.5px;">Total Orders</p>
                            <h3 class="mb-0 fw-bold" style="color: #0D47A1; font-size: 1.5rem;">{{ $stats['totalOrders'] }}</h3>
                        </div>
                        <div class="icon-box" style="background: rgba(255, 255, 255, 0.9); border-radius: 10px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <i class="fas fa-shopping-cart" style="color: #2196F3; font-size: 1.3rem;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2" style="border-top: 1px solid rgba(21, 101, 192, 0.2);">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-1" style="color: #4CAF50; font-size: 0.75rem;"></i>
                            <small style="color: #1565C0; font-weight: 500; font-size: 0.8rem;">{{ $stats['completedOrders'] }} Done</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-1" style="color: #FF9800; font-size: 0.75rem;"></i>
                            <small style="color: #1565C0; font-weight: 500; font-size: 0.8rem;">{{ $stats['pendingOrders'] }} Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status -->
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-uppercase mb-1 fw-semibold" style="color: #E65100; font-size: 0.7rem; letter-spacing: 0.5px;">Stock Status</p>
                            <h3 class="mb-0 fw-bold" style="color: #BF360C; font-size: 1.5rem;">{{ $stats['totalProducts'] }}</h3>
                        </div>
                        <div class="icon-box" style="background: rgba(255, 255, 255, 0.9); border-radius: 10px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <i class="fas fa-boxes" style="color: #FF9800; font-size: 1.3rem;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2" style="border-top: 1px solid rgba(230, 81, 0, 0.2);">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-1" style="color: #F57C00; font-size: 0.75rem;"></i>
                            <small style="color: #E65100; font-weight: 500; font-size: 0.8rem;">{{ $stats['lowStockCount'] }} Low</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-times-circle me-1" style="color: #D32F2F; font-size: 0.75rem;"></i>
                            <small style="color: #E65100; font-weight: 500; font-size: 0.8rem;">{{ $stats['outOfStockCount'] }} Out</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Overview -->
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-uppercase mb-1 fw-semibold" style="color: #6A1B9A; font-size: 0.7rem; letter-spacing: 0.5px;">Business Overview</p>
                        </div>
                        <div class="icon-box" style="background: rgba(255, 255, 255, 0.9); border-radius: 10px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <i class="fas fa-chart-line" style="color: #9C27B0; font-size: 1.3rem;"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1" style="border-bottom: 1px solid rgba(106, 27, 154, 0.15);">
                            <small class="text-uppercase fw-semibold" style="color: #6A1B9A; font-size: 0.65rem; letter-spacing: 0.3px;">Suppliers</small>
                            <span class="fw-bold" style="color: #4A148C; font-size: 1rem;">{{ $stats['totalSuppliers'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1" style="border-bottom: 1px solid rgba(106, 27, 154, 0.15);">
                            <small class="text-uppercase fw-semibold" style="color: #6A1B9A; font-size: 0.65rem; letter-spacing: 0.3px;">Employees</small>
                            <span class="fw-bold" style="color: #4A148C; font-size: 1rem;">{{ $stats['totalEmployees'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-uppercase fw-semibold" style="color: #6A1B9A; font-size: 0.65rem; letter-spacing: 0.3px;">Payments</small>
                            <span class="fw-bold" style="color: #4A148C; font-size: 1rem;">{{ $stats['totalPayments'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Detailed Sections -->
    <div class="row g-3 mb-4">
        <!-- Sales Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold" style="color: var(--primary-color);">
                            <i class="fas fa-chart-area me-2" style="color: var(--accent-color);"></i>Sales Overview
                        </h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-primary active" onclick="updateChart('week')">
                                <i class="fas fa-calendar-week me-1"></i>Week
                            </button>
                            <button class="btn btn-outline-primary" onclick="updateChart('month')">
                                <i class="fas fa-calendar-alt me-1"></i>Month
                            </button>
                        </div>
                    </div>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.75rem;">Last 7 days performance</p>
                </div>
                <div class="card-body p-3">
                    <canvas id="salesChart" style="max-height: 240px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Payment Summary -->
        <div class="col-xl-4">
            <div class="row g-3">
                <!-- Quick Actions -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom-0 py-2 px-3">
                            <h6 class="mb-0 fw-bold" style="color: var(--primary-color);">
                                <i class="fas fa-bolt me-2" style="color: var(--accent-color);"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-grid gap-2">
                                <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center">
                                    <i class="fas fa-plus-circle me-2"></i>Create New Order
                                </a>
                                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center">
                                    <i class="fas fa-boxes me-2"></i>Manage Inventory
                                </a>
                                <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>View Billing
                                </a>
                               
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom-0 py-2 px-3">
                            <h6 class="mb-0 fw-bold" style="color: var(--primary-color);">
                                <i class="fas fa-money-check-alt me-2" style="color: var(--accent-color);"></i>Payment Summary
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row text-center g-2">
                                <div class="col-4">
                                    <div class="p-2 rounded" style="background-color: #E8F5E9;">
                                        <div class="fw-bold mb-1" style="color: #2E7D32; font-size: 1rem;">₱{{ number_format($paymentSummary['today']['amount'], 2) }}</div>
                                        <small class="text-uppercase d-block" style="color: #66BB6A; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px;">Today</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded" style="background-color: #E3F2FD;">
                                        <div class="fw-bold mb-1" style="color: #1565C0; font-size: 1rem;">₱{{ number_format($paymentSummary['week']['amount'], 2) }}</div>
                                        <small class="text-uppercase d-block" style="color: #42A5F5; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px;">Week</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded" style="background-color: #F3E5F5;">
                                        <div class="fw-bold mb-1" style="color: #6A1B9A; font-size: 1rem;">₱{{ number_format($paymentSummary['month']['amount'], 2) }}</div>
                                        <small class="text-uppercase d-block" style="color: #AB47BC; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px;">Month</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row g-3 mb-4">
        <!-- Recent Orders -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold" style="color: var(--primary-color);">
                            <i class="fas fa-shopping-bag me-2" style="color: var(--accent-color);"></i>Recent Orders
                        </h6>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #ffffff;">
                                    <tr>
                                        <th class="py-3 px-3 fw-semibold" style="font-size: 0.85rem; color: #ffffff;">Order #</th>
                                        <th class="py-3 px-3 fw-semibold" style="font-size: 0.85rem; color: #ffffff;">Customer</th>
                                        <th class="py-3 px-3 fw-semibold text-end" style="font-size: 0.85rem; color: #ffffff;">Amount</th>
                                        <th class="py-3 px-3 fw-semibold text-center" style="font-size: 0.85rem; color: #ffffff;">Status</th>
                                        <th class="py-3 px-3 fw-semibold" style="font-size: 0.85rem; color: #ffffff;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="py-3 px-3">
                                            <strong style="color: #000000;">{{ $order->order_number }}</strong>
                                        </td>
                                        <td class="py-3 px-3" style="color: #000000;">{{ $order->customer_name ?? 'Walk-in Customer' }}</td>
                                        <td class="py-3 px-3 text-end">
                                            <strong style="color: #000000;">₱{{ number_format($order->total, 2) }}</strong>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            @if($order->status == 'completed')
                                                <span class="badge bg-success px-3 py-1">Completed</span>
                                            @elseif($order->status == 'pending')
                                                <span class="badge bg-warning px-3 py-1">Pending</span>
                                            @elseif($order->status == 'partial')
                                                <span class="badge bg-info px-3 py-1">Partial</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-1">Cancelled</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3" style="font-size: 0.9rem; color: #000000;">{{ $order->created_at->format('M j, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No recent orders found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock Alerts & Top Products -->
        <div class="col-xl-6">
            <!-- Stock Alerts -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom-0 py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold" style="color: var(--primary-color);">
                            <i class="fas fa-exclamation-circle me-2" style="color: var(--accent-color);"></i>Stock Alerts
                        </h6>
                        <div>
                            <a href="{{ route('inventory.low-stock') }}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                            </a>
                            <a href="{{ route('inventory.out-of-stock') }}" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-times-circle me-1"></i>Out of Stock
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Low Stock Items -->
                    @if($lowStockItems->count() > 0)
                        <div class="mb-3">
                            <h6 class="fw-bold mb-3" style="color: #F57C00; font-size: 0.9rem;">
                                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Items ({{ $lowStockItems->count() }})
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead style="background-color: #FFF3E0;">
                                        <tr>
                                            <th class="py-2 px-3 fw-semibold" style="font-size: 0.8rem; color: #E65100;">Product</th>
                                            <th class="py-2 px-3 fw-semibold text-center" style="font-size: 0.8rem; color: #E65100;">Stock</th>
                                            <th class="py-2 px-3 fw-semibold text-center" style="font-size: 0.8rem; color: #E65100;">Alert</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowStockItems as $item)
                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                            <td class="py-2 px-3" style="font-size: 0.85rem;">{{ $item->product_name }}</td>
                                            <td class="py-2 px-3 text-center">
                                                <span class="badge bg-warning">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="py-2 px-3 text-center" style="font-size: 0.85rem; color: #6c757d;">{{ $item->low_stock_alert }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Out of Stock Items -->
                    @if($outOfStockItems->count() > 0)
                        <div class="{{ $lowStockItems->count() > 0 ? 'pt-3 mt-3' : '' }}" style="{{ $lowStockItems->count() > 0 ? 'border-top: 2px solid #f0f0f0;' : '' }}">
                            <h6 class="fw-bold mb-3" style="color: #D32F2F; font-size: 0.9rem;">
                                <i class="fas fa-times-circle me-2"></i>Out of Stock Items ({{ $outOfStockItems->count() }})
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead style="background-color: #FFEBEE;">
                                        <tr>
                                            <th class="py-2 px-3 fw-semibold" style="font-size: 0.8rem; color: #C62828;">Product</th>
                                            <th class="py-2 px-3 fw-semibold" style="font-size: 0.8rem; color: #C62828;">Last Updated</th>
                                            <th class="py-2 px-3 fw-semibold text-center" style="font-size: 0.8rem; color: #C62828;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($outOfStockItems as $item)
                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                            <td class="py-2 px-3" style="font-size: 0.85rem;">{{ $item->product_name }}</td>
                                            <td class="py-2 px-3" style="font-size: 0.85rem; color: #6c757d;">{{ $item->updated_at->format('M j, Y') }}</td>
                                            <td class="py-2 px-3 text-center">
                                                <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    
                    @if($lowStockItems->count() == 0 && $outOfStockItems->count() == 0)
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="mb-0 fw-semibold">All stock levels are good!</p>
                            <small>No low or out of stock items</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Selling Products -->
            @if($topProducts->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="mb-0 fw-bold" style="color: var(--primary-color);">
                        <i class="fas fa-fire me-2" style="color: var(--accent-color);"></i>Top Selling Products
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="py-3 px-3 fw-semibold" style="font-size: 0.85rem; color: #ffffff;">Product</th>
                                    <th class="py-3 px-3 fw-semibold text-center" style="font-size: 0.85rem; color: #ffffff;">Sold</th>
                                    <th class="py-3 px-3 fw-semibold text-end" style="font-size: 0.85rem; color: #ffffff;">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td class="py-3 px-3">
                                        <strong>{{ $product->product_name }}</strong>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="badge bg-primary">{{ $product->total_quantity }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-end">
                                        <strong style="color: #2E7D32;">₱{{ number_format($product->total_revenue, 2) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 py-2 px-3">
                    <h6 class="mb-0 fw-bold" style="color: var(--primary-color);">
                        <i class="fas fa-chart-bar me-2" style="color: var(--accent-color);"></i>Performance Metrics
                    </h6>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.75rem;">Key performance indicators at a glance</p>
                </div>
                <div class="card-body p-3">
                    <div class="row text-center g-3">
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="p-3 rounded h-100" style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); border: 2px solid #90CAF9;">
                                <div class="mb-2">
                                    <i class="fas fa-calculator" style="color: #1976D2; font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #0D47A1; font-size: 1.2rem;">₱{{ number_format($stats['totalSales'] / max($stats['completedOrders'], 1), 2) }}</h4>
                                <small class="text-uppercase d-block fw-semibold" style="color: #1976D2; font-size: 0.65rem; letter-spacing: 0.5px;">Avg Order Value</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="p-3 rounded h-100" style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); border: 2px solid #A5D6A7;">
                                <div class="mb-2">
                                    <i class="fas fa-check-double" style="color: #388E3C; font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #1B5E20; font-size: 1.2rem;">{{ $stats['completedOrders'] }}</h4>
                                <small class="text-uppercase d-block fw-semibold" style="color: #2E7D32; font-size: 0.65rem; letter-spacing: 0.5px;">Completed Orders</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="p-3 rounded h-100" style="background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); border: 2px solid #FFCC80;">
                                <div class="mb-2">
                                    <i class="fas fa-hourglass-half" style="color: #F57C00; font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #BF360C; font-size: 1.2rem;">{{ $stats['pendingOrders'] }}</h4>
                                <small class="text-uppercase d-block fw-semibold" style="color: #E65100; font-size: 0.65rem; letter-spacing: 0.5px;">Pending Orders</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="p-3 rounded h-100" style="background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%); border: 2px solid #CE93D8;">
                                <div class="mb-2">
                                    <i class="fas fa-box-open" style="color: #7B1FA2; font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #4A148C; font-size: 1.2rem;">{{ $stats['activeProducts'] }}</h4>
                                <small class="text-uppercase d-block fw-semibold" style="color: #6A1B9A; font-size: 0.65rem; letter-spacing: 0.5px;">Active Products</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="p-3 rounded h-100" style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); border: 2px solid #A5D6A7;">
                                <div class="mb-2">
                                    <i class="fas fa-user-check" style="color: #388E3C; font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #1B5E20; font-size: 1.2rem;">{{ $stats['activeEmployees'] }}</h4>
                                <small class="text-uppercase d-block fw-semibold" style="color: #2E7D32; font-size: 0.65rem; letter-spacing: 0.5px;">Active Employees</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="p-3 rounded h-100" style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); border: 2px solid #90CAF9;">
                                <div class="mb-2">
                                    <i class="fas fa-receipt" style="color: #1976D2; font-size: 1.5rem;"></i>
                                </div>
                                <h4 class="fw-bold mb-1" style="color: #0D47A1; font-size: 1.2rem;">{{ $stats['todayPayments'] }}</h4>
                                <small class="text-uppercase d-block fw-semibold" style="color: #1976D2; font-size: 0.65rem; letter-spacing: 0.5px;">Today's Payments</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesData['labels']),
            datasets: [{
                label: 'Daily Sales (₱)',
                data: @json($salesData['data']),
                borderColor: '#F4A300',
                backgroundColor: 'rgba(244, 163, 0, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#F4A300',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2.8,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value;
                        },
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            }
        }
    });

    // Chart update function
    window.updateChart = function(period) {
        // You can implement AJAX call here to update chart data
        console.log('Updating chart for period:', period);
        // For now, we'll just toggle active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    };
});

// Auto-refresh dashboard every 60 seconds
setTimeout(function() {
    window.location.reload();
}, 60000);
</script>

<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
}

.icon-box {
    transition: all 0.3s ease;
}

.hover-lift:hover .icon-box {
    transform: scale(1.1);
}

.card {
    transition: all 0.3s ease;
}

.btn-outline-primary {
    border-color: var(--accent-color);
    color: var(--accent-color);
    transition: all 0.3s ease;
}

.btn-outline-primary:hover,
.btn-outline-primary.active {
    background-color: var(--accent-color);
    border-color: var(--accent-color);
    color: var(--primary-color);
    transform: translateY(-1px);
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
    transform: translateY(-1px);
}

.table thead {
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(244, 163, 0, 0.05) !important;
    transform: scale(1.01);
}

.badge {
    font-weight: 600;
    letter-spacing: 0.3px;
    padding: 0.35em 0.65em;
}

/* Chart container */
#salesChart {
    display: block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem !important;
    }
    
    .icon-box {
        padding: 10px !important;
    }
    
    h1 {
        font-size: 1.5rem !important;
    }
    
    h2 {
        font-size: 1.3rem !important;
    }
    
    h3 {
        font-size: 1.1rem !important;
    }
}

/* Loading animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeIn 0.5s ease-out;
}

/* Scrollbar styling */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: var(--accent-color);
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: var(--primary-color);
}
</style>
@endsection