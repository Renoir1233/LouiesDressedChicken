<!-- resources/views/inventory/report.blade.php -->

@extends('layouts.inventory')

@section('title', 'Inventory Report')

@section('content')
<div class="container-fluid">
    <!-- Print Header (Professional) -->
    <div style="margin-bottom: 30px; border-bottom: 3px solid #2c3e50; padding-bottom: 20px; display: none;" id="printHeader" class="print-header">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="margin: 0; color: #2c3e50; font-size: 28px; font-weight: 700; letter-spacing: 0.5px;">{{ config('app.name') }}</h1>
            <p style="margin: 8px 0 0 0; color: #666; font-size: 16px; font-weight: 600; letter-spacing: 1px;">INVENTORY REPORT</p>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 13px; color: #333; line-height: 1.8;">
            <div>
                <p style="margin: 5px 0;"><strong>Prepared by:</strong> <span style="color: #0077BE; font-weight: 600;">{{ auth()->user()->name ?? 'System' }}</span></p>
                <p style="margin: 5px 0;"><strong>Generated on:</strong> <span style="color: #0077BE; font-weight: 600;">{{ now()->format('F d, Y') }}</span></p>
                <p style="margin: 5px 0;"><strong>Time:</strong> <span style="color: #0077BE; font-weight: 600;">{{ now()->format('h:i A') }}</span></p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 5px 0;"><strong>Report Type:</strong> Complete Inventory</p>
                <p style="margin: 5px 0;"><strong>Report Date:</strong> <span style="color: #0077BE; font-weight: 600;">{{ now()->format('F d, Y') }}</span></p>
                <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #2D6A4F; font-weight: 600;">FINAL</span></p>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2 class="fw-bold" style="color: #2c3e50;">Inventory Report</h2>
        <div>
            <button class="btn btn-info me-2" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print Report
            </button>
            <a href="{{ route('inventory.report.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export to CSV
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body" style="background-color: #F5F5F5;">
            <form method="GET" action="{{ route('inventory.report') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Category</label>
                    <select class="form-control" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Supplier</label>
                    <select class="form-control" name="supplier_id">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-control" name="status">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #E8D7F1;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 fw-bold" style="color: #7B2CBF;">{{ $stats['total_products'] }}</h2>
                            <small class="d-block" style="color: #7B2CBF;">Total Products</small>
                            <small style="color: #9D4EDD;">{{ $stats['active_products'] }} Active | {{ $stats['inactive_products'] }} Inactive</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                            <i class="fas fa-boxes fa-lg" style="color: #7B2CBF;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #FCE1E4;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 fw-bold" style="color: #D81B60;">{{ $stats['in_stock_count'] }}</h2>
                            <small class="d-block" style="color: #D81B60;">In Stock</small>
                            <small style="color: #EC407A;">{{ $stats['low_stock_count'] }} Low | {{ $stats['out_of_stock_count'] }} Out</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                            <i class="fas fa-check-circle fa-lg" style="color: #D81B60;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #DDEBF5;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 fw-bold" style="color: #0077BE;">₱{{ number_format($stats['total_stock_value'], 0) }}</h2>
                            <small class="d-block" style="color: #0077BE;">Stock Value (Cost)</small>
                            <small style="color: #4FB3D9;">Total inventory cost</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                            <i class="fas fa-money-bill-wave fa-lg" style="color: #0077BE;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: #E0F7E0;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 fw-bold" style="color: #2D6A4F;">₱{{ number_format($stats['total_potential_profit'], 0) }}</h2>
                            <small class="d-block" style="color: #2D6A4F;">Potential Profit</small>
                            <small style="color: #52B788;">If all sold now</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                            <i class="fas fa-chart-line fa-lg" style="color: #2D6A4F;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">Stock by Category</h5>
                </div>
                <div class="card-body">
                    @if($byCategory->isEmpty())
                        <p class="text-muted">No data available</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Items</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Cost Value</th>
                                        <th class="text-end">Sales Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($byCategory as $category => $data)
                                        <tr>
                                            <td><strong>{{ $category }}</strong></td>
                                            <td class="text-end">{{ $data['count'] }}</td>
                                            <td class="text-end">{{ number_format($data['total_quantity'], 2) }}</td>
                                            <td class="text-end">₱{{ number_format($data['total_value'], 2) }}</td>
                                            <td class="text-end">₱{{ number_format($data['potential_sales'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">Stock by Supplier</h5>
                </div>
                <div class="card-body">
                    @if($bySupplier->isEmpty())
                        <p class="text-muted">No data available</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th class="text-end">Items</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Cost Value</th>
                                        <th class="text-end">Sales Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bySupplier as $data)
                                        <tr>
                                            <td><strong>{{ $data['supplier_name'] }}</strong></td>
                                            <td class="text-end">{{ $data['count'] }}</td>
                                            <td class="text-end">{{ number_format($data['total_quantity'], 2) }}</td>
                                            <td class="text-end">₱{{ number_format($data['total_value'], 2) }}</td>
                                            <td class="text-end">₱{{ number_format($data['potential_sales'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 30-Day Movement Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background-color: #D4F1E4;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #2D6A4F;">{{ number_format($stockInsCount, 0) }} units</h3>
                            <small class="d-block" style="color: #2D6A4F;">Stock In (30 days)</small>
                            <small style="color: #52B788;">Items received</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255, 255, 255, 0.8);">
                            <i class="fas fa-arrow-down fa-2x" style="color: #2D6A4F;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background-color: #FFDFD3;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 fw-bold" style="color: #CC5500;">{{ number_format($stockOutsCount, 0) }} units</h3>
                            <small class="d-block" style="color: #CC5500;">Stock Out (30 days)</small>
                            <small style="color: #E8830F;">Items removed</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255, 255, 255, 0.8);">
                            <i class="fas fa-arrow-up fa-2x" style="color: #CC5500;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock In Transactions -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold">Recent Stock In Transactions (Last 50)</h5>
        </div>
        <div class="card-body">
            @if($stockInTransactions->isEmpty())
                <p class="text-muted">No stock in transactions found</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reference #</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Supplier</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Total Cost</th>
                                <th>Batch #</th>
                                <th>Received By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockInTransactions as $transaction)
                                <tr>
                                    <td><strong>{{ $transaction->reference_number }}</strong></td>
                                    <td>{{ $transaction->date_received->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $transaction->inventory->product_code }}</span>
                                        {{ $transaction->inventory->product_name }}
                                    </td>
                                    <td>{{ $transaction->supplier->name ?? 'Unknown' }}</td>
                                    <td class="text-end fw-bold">{{ $transaction->quantity_received }}</td>
                                    <td class="text-end">₱{{ number_format($transaction->unit_cost, 2) }}</td>
                                    <td class="text-end fw-bold text-primary">₱{{ number_format($transaction->total_cost, 2) }}</td>
                                    <td>{{ $transaction->batch_number ?? 'N/A' }}</td>
                                    <td>{{ $transaction->receivedBy->name ?? 'Unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Stock Out Transactions -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold">Recent Stock Out Transactions (Last 50)</h5>
        </div>
        <div class="card-body">
            @if($stockOutTransactions->isEmpty())
                <p class="text-muted">No stock out transactions found</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reference #</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Value</th>
                                <th>Reason</th>
                                <th>Handled By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockOutTransactions as $transaction)
                                <tr>
                                    <td><strong>{{ $transaction->reference_number }}</strong></td>
                                    <td>{{ $transaction->date_removed->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $transaction->inventory->product_code }}</span>
                                        {{ $transaction->inventory->product_name }}
                                    </td>
                                    <td class="text-end fw-bold">{{ $transaction->quantity_removed }}</td>
                                    <td class="text-end">₱{{ number_format($transaction->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($transaction->total_value, 2) }}</td>
                                    <td>
                                        @php
                                            $reasonLabels = [
                                                'sale' => 'Sale',
                                                'damage' => 'Damage',
                                                'expiration' => 'Expired',
                                                'return' => 'Return',
                                                'internal_use' => 'Internal',
                                                'sample' => 'Sample',
                                                'wastage' => 'Wastage',
                                                'adjustment' => 'Adjustment'
                                            ];
                                        @endphp
                                        <span class="badge bg-info">{{ $reasonLabels[$transaction->reason] ?? $transaction->reason }}</span>
                                    </td>
                                    <td>{{ $transaction->handledBy->name ?? 'Unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Top Value Items -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold">Top 10 Highest Value Items</h5>
        </div>
        <div class="card-body">
            @if($topValueItems->isEmpty())
                <p class="text-muted">No items found</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Cost Price</th>
                                <th class="text-end">Selling Price</th>
                                <th class="text-end">Stock Value</th>
                                <th class="text-end">Potential Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topValueItems as $item)
                                @php
                                    $stockValue = $item->quantity * $item->selling_price;
                                    $profit = $item->quantity * ($item->selling_price - $item->cost_price);
                                @endphp
                                <tr>
                                    <td><strong>{{ $item->product_code }}</strong></td>
                                    <td>{{ $item->product_name }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                                    <td>{{ $item->supplier->name ?? 'Unknown' }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                    <td class="text-end">₱{{ number_format($item->cost_price, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($item->selling_price, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($stockValue, 2) }}</td>
                                    <td class="text-end text-success fw-bold">₱{{ number_format($profit, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- All Inventory Items -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold">All Inventory Items ({{ $inventoryData->count() }})</h5>
        </div>
        <div class="card-body">
            @if($inventoryData->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No items found based on current filters</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Cost Price</th>
                                <th class="text-end">Selling Price</th>
                                <th class="text-end">Profit/Unit</th>
                                <th class="text-end">Cost Value</th>
                                <th class="text-end">Sales Value</th>
                                <th class="text-center">Stock Status</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryData as $item)
                                @php
                                    $costValue = $item->quantity * $item->cost_price;
                                    $salesValue = $item->quantity * $item->selling_price;
                                    $profit = $item->selling_price - $item->cost_price;
                                    
                                    $stockStatus = '';
                                    $stockBadge = '';
                                    if ($item->quantity == 0) {
                                        $stockStatus = 'Out of Stock';
                                        $stockBadge = 'bg-danger';
                                    } elseif ($item->quantity <= $item->low_stock_alert) {
                                        $stockStatus = 'Low Stock';
                                        $stockBadge = 'bg-warning';
                                    } else {
                                        $stockStatus = 'In Stock';
                                        $stockBadge = 'bg-success';
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $item->product_code }}</strong></td>
                                    <td>{{ $item->product_name }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                                    <td>{{ $item->supplier->name ?? 'Unknown' }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                    <td class="text-end">₱{{ number_format($item->cost_price, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($item->selling_price, 2) }}</td>
                                    <td class="text-end text-success fw-bold">₱{{ number_format($profit, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($costValue, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($salesValue, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $stockBadge }}">{{ $stockStatus }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .no-print {
        print-color-adjust: none;
    }

    .print-header {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    @media print {
        .no-print, .btn, button, .nav-tabs-custom {
            display: none !important;
        }

        .print-header {
            display: block !important;
            page-break-after: avoid;
        }
        
        body {
            background: white;
            margin: 0;
            padding: 15px;
        }
        
        .card {
            page-break-inside: avoid;
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            margin-bottom: 15px;
        }
        
        .card-header {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #ddd !important;
            font-weight: 600;
            color: #2c3e50 !important;
        }
        
        .table {
            font-size: 11px;
            border-collapse: collapse;
            width: 100%;
        }
        
        .table th {
            background-color: #f8f9fa !important;
            color: #2c3e50 !important;
            border: 1px solid #ddd !important;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
            font-weight: 600;
            padding: 6px;
            text-align: left;
        }
        
        .table td {
            border: 1px solid #ddd !important;
            padding: 5px;
        }
        
        h2, h3, h4, h5 {
            page-break-after: avoid;
            color: #2c3e50;
        }
        
        .row {
            page-break-inside: avoid;
        }
        
        .container-fluid {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
    }
</style>

<script>
    // Show print header when print dialog opens
    window.addEventListener('beforeprint', function() {
        document.getElementById('printHeader').style.display = 'block';
    });

    // Hide print header when print dialog closes
    window.addEventListener('afterprint', function() {
        document.getElementById('printHeader').style.display = 'none';
    });

    // Alternative for browsers that don't support beforeprint/afterprint
    const originalPrint = window.print;
    window.print = function() {
        document.getElementById('printHeader').style.display = 'block';
        originalPrint.call(this);
        setTimeout(function() {
            document.getElementById('printHeader').style.display = 'none';
        }, 100);
    };
</script>
@endsection
