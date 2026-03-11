<!-- resources/views/inventory/movement-history.blade.php -->
@extends('layouts.inventory')

@section('title', 'Stock Movement History')

@section('content')
@php
    // Ensure variables exist with default values
    $totalIn = $totalIn ?? 0;
    $totalOut = $totalOut ?? 0;
    $movements = $movements ?? collect();
    $products = $products ?? collect();
@endphp

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background-color: #E3F2FD;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #1976D2;">{{ $movements->total() ?? 0 }}</h2>
                        <small class="d-block" style="color: #1976D2;">Total Movements</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-history fa-lg" style="color: #2196F3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background-color: #E8F5E9;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #388E3C;">{{ number_format($totalIn) }}</h2>
                        <small class="d-block" style="color: #388E3C;">Total Stock In</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-arrow-down fa-lg" style="color: #4CAF50;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background-color: #FFEBEE;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 fw-bold" style="color: #C62828;">{{ number_format($totalOut) }}</h2>
                        <small class="d-block" style="color: #C62828;">Total Stock Out</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: rgba(255, 255, 255, 0.8);">
                        <i class="fas fa-arrow-up fa-lg" style="color: #f44336;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #2c3e50;"><i class="fas fa-history me-2" style="color: #2196F3;"></i>Stock Movement History</h5>
        <div>
          
            <a href="javascript:void(0)" class="btn btn-success" onclick="exportToCSV()">
                <i class="fas fa-file-export me-2"></i>Export CSV
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-2">
                <input type="text" class="form-control" name="search" placeholder="Search..." 
                       value="{{ request('search') ?? '' }}">
            </div>
            <div class="col-md-2">
                <select class="form-control" name="product_id">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" name="movement_type">
                    <option value="">All Types</option>
                    <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>Stock In</option>
                    <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control datepicker" name="date_from" 
                       value="{{ request('date_from') ?? '' }}" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control datepicker" name="date_to" 
                       value="{{ request('date_to') ?? '' }}" placeholder="To Date">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <a href="{{ route('inventory.movement-history') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>

        @if($movements->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped" id="movementTable">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Reference</th>
                            <th>User</th>
                            <th>Remaining</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($movement->inventory && $movement->inventory->image)
                                        <img src="{{ asset('storage/' . $movement->inventory->image) }}" 
                                             alt="{{ $movement->inventory->product_name }}" 
                                             class="me-2" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                                    @endif
                                    <div>
                                        <div>{{ $movement->inventory->product_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $movement->inventory->product_code ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="{{ $movement->movement_type == 'in' ? 'badge bg-success' : 'badge bg-danger' }}">
                                    {{ $movement->movement_type == 'in' ? 'Stock In' : 'Stock Out' }}
                                </span>
                            </td>
                            <td class="{{ $movement->movement_type == 'in' ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                                {{ $movement->quantity }} {{ $movement->inventory->unit ?? '' }}
                            </td>
                            <td>₱{{ number_format($movement->unit_price, 2) }}</td>
                            <td class="fw-bold">
                                ₱{{ number_format($movement->quantity * $movement->unit_price, 2) }}
                            </td>
                            <td>
                                @if($movement->reference_type == 'stock_in_transaction' && $movement->reference)
                                    <span class="badge bg-info">{{ $movement->reference->reference_number ?? 'N/A' }}</span>
                                @elseif($movement->reference_type == 'stock_out_transaction' && $movement->reference)
                                    <span class="badge bg-warning">{{ $movement->reference->reference_number ?? 'N/A' }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $movement->reference_type }}</span>
                                @endif
                            </td>
                            <td>{{ $movement->user->name ?? 'System' }}</td>
                            <td>{{ $movement->remaining_quantity }} {{ $movement->inventory->unit ?? '' }}</td>
                            <td>
                                @if($movement->notes)
                                    <button type="button" class="btn btn-sm btn-link" data-bs-toggle="popover" 
                                            title="Notes" data-bs-content="{{ $movement->notes }}">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $movements->links('pagination.bootstrap-4') }}
            </div>
            
            <!-- Summary -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Summary</h6>
                            <p class="mb-1">Total Stock In: <span class="fw-bold text-success">{{ number_format($totalIn) }}</span></p>
                            <p class="mb-1">Total Stock Out: <span class="fw-bold text-danger">{{ number_format($totalOut) }}</span></p>
                            <p class="mb-0">Net Movement: <span class="fw-bold {{ ($totalIn - $totalOut) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($totalIn - $totalOut) }}
                            </span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Filter Summary</h6>
                            <p class="mb-1">Showing: <span class="fw-bold">{{ $movements->count() }}</span> of {{ $movements->total() }}</p>
                            <p class="mb-1">Current Page: <span class="fw-bold">{{ $movements->currentPage() }}</span> of {{ $movements->lastPage() }}</p>
                            <p class="mb-0">Per Page: <span class="fw-bold">{{ $movements->perPage() }}</span> records</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Value Summary</h6>
                            @php
                                $totalInValue = $movements->where('movement_type', 'in')->sum(function($m) { return $m->quantity * $m->unit_price; });
                                $totalOutValue = $movements->where('movement_type', 'out')->sum(function($m) { return $m->quantity * $m->unit_price; });
                            @endphp
                            <p class="mb-1">Total In Value: <span class="fw-bold text-success">₱{{ number_format($totalInValue, 2) }}</span></p>
                            <p class="mb-1">Total Out Value: <span class="fw-bold text-danger">₱{{ number_format($totalOutValue, 2) }}</span></p>
                            <p class="mb-0">Net Value: <span class="fw-bold {{ ($totalInValue - $totalOutValue) >= 0 ? 'text-success' : 'text-danger' }}">
                                ₱{{ number_format($totalInValue - $totalOutValue, 2) }}
                            </span></p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No stock movements found</h5>
                <p class="text-muted">Stock movements will appear here when you perform stock in or stock out transactions.</p>
                <div class="mt-3">
                    <a href="{{ route('inventory.stock-in.index') }}" class="btn btn-primary me-2">
                        <i class="fas fa-arrow-down me-2"></i>Stock In
                    </a>
                    <a href="{{ route('inventory.stock-out.index') }}" class="btn btn-danger">
                        <i class="fas fa-arrow-up me-2"></i>Stock Out
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
});

function printMovementReport() {
    const printWindow = window.open('', '_blank');
    
    // Get current date
    const today = new Date();
    const dateStr = today.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    // Get filter information
    const filters = {
        search: "{{ request('search') ?? '' }}",
        product_id: "{{ request('product_id') ?? '' }}",
        movement_type: "{{ request('movement_type') ?? '' }}",
        date_from: "{{ request('date_from') ?? '' }}",
        date_to: "{{ request('date_to') ?? '' }}"
    };
    
    let filterText = 'All movements';
    if (filters.search) filterText += ` containing "${filters.search}"`;
    if (filters.movement_type) filterText += ` - Type: ${filters.movement_type == 'in' ? 'Stock In' : 'Stock Out'}`;
    if (filters.date_from || filters.date_to) {
        filterText += ` - Date range: ${filters.date_from || 'Start'} to ${filters.date_to || 'End'}`;
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Stock Movement Report - ${dateStr}</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 20px; 
                        color: #333;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                        border-bottom: 2px solid #333;
                        padding-bottom: 20px;
                    }
                    .header h1 { 
                        color: #333; 
                        margin: 0;
                        font-size: 24px;
                    }
                    .header .company {
                        font-size: 18px;
                        font-weight: bold;
                        color: #660B05;
                    }
                    .header .date {
                        color: #666;
                        font-size: 14px;
                        margin-top: 5px;
                    }
                    .header .filters {
                        color: #666;
                        font-size: 12px;
                        margin-top: 10px;
                        font-style: italic;
                    }
                    .summary {
                        background: #f8f9fa;
                        padding: 15px;
                        border-radius: 5px;
                        margin-bottom: 20px;
                        border-left: 4px solid #660B05;
                    }
                    .summary h3 {
                        margin-top: 0;
                        color: #333;
                        font-size: 16px;
                    }
                    .summary-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 15px;
                        margin-top: 10px;
                    }
                    .summary-item {
                        text-align: center;
                        padding: 10px;
                        border-radius: 5px;
                        color: white;
                    }
                    .total-in { background: #28a745; }
                    .total-out { background: #dc3545; }
                    .net { background: #007bff; }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-top: 20px;
                        font-size: 11px;
                    }
                    th { 
                        background-color: #660B05; 
                        color: white; 
                        font-weight: bold;
                        padding: 8px;
                        text-align: left;
                        border: 1px solid #ddd;
                    }
                    td { 
                        padding: 6px; 
                        text-align: left;
                        border: 1px solid #ddd;
                        vertical-align: top;
                    }
                    tr:nth-child(even) {
                        background-color: #f8f9fa;
                    }
                    .badge { 
                        padding: 2px 6px; 
                        border-radius: 3px; 
                        font-size: 10px; 
                        font-weight: bold;
                    }
                    .badge-in { background-color: #28a745; color: white; }
                    .badge-out { background-color: #dc3545; color: white; }
                    .text-success { color: #28a745; }
                    .text-danger { color: #dc3545; }
                    .text-right { text-align: right; }
                    .footer {
                        margin-top: 30px;
                        padding-top: 20px;
                        border-top: 1px solid #ddd;
                        text-align: center;
                        color: #666;
                        font-size: 11px;
                    }
                    @media print {
                        @page {
                            size: landscape;
                            margin: 15mm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="company">LOUIES DRESSED CHICKEN</div>
                    <h1>Stock Movement Report</h1>
                    <div class="date">Generated on: ${dateStr}</div>
                    <div class="filters">${filterText}</div>
                </div>
                
                <div class="summary">
                    <h3>Summary Report</h3>
                    <div class="summary-grid">
                        <div class="summary-item total-in">
                            <div class="label">Total Stock In</div>
                            <div class="value" style="font-size: 18px; font-weight: bold;">${formatNumber({{ $totalIn }})}</div>
                            <div style="font-size: 11px;">Units</div>
                        </div>
                        <div class="summary-item total-out">
                            <div class="label">Total Stock Out</div>
                            <div class="value" style="font-size: 18px; font-weight: bold;">${formatNumber({{ $totalOut }})}</div>
                            <div style="font-size: 11px;">Units</div>
                        </div>
                        <div class="summary-item net">
                            <div class="label">Net Movement</div>
                            <div class="value" style="font-size: 18px; font-weight: bold;">${formatNumber({{ $totalIn - $totalOut }})}</div>
                            <div style="font-size: 11px;">Units</div>
                        </div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th class="text-right">Total Value</th>
                            <th>Reference</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($movements) && $movements->count() > 0)
                            @foreach($movements as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $movement->inventory->product_name ?? 'N/A' }}</td>
                                <td><span class="badge {{ $movement->movement_type == 'in' ? 'badge-in' : 'badge-out' }}">{{ $movement->movement_type == 'in' ? 'Stock In' : 'Stock Out' }}</span></td>
                                <td>{{ $movement->quantity }} {{ $movement->inventory->unit ?? '' }}</td>
                                <td>₱{{ number_format($movement->unit_price, 2) }}</td>
                                <td class="text-right">₱{{ number_format($movement->quantity * $movement->unit_price, 2) }}</td>
                                <td>
                                    @if($movement->reference_type == 'stock_in_transaction' && $movement->reference)
                                        {{ $movement->reference->reference_number ?? 'N/A' }}
                                    @elseif($movement->reference_type == 'stock_out_transaction' && $movement->reference)
                                        {{ $movement->reference->reference_number ?? 'N/A' }}
                                    @else
                                        {{ $movement->reference_type }}
                                    @endif
                                </td>
                                <td>{{ $movement->user->name ?? 'System' }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px;">No movements to display</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                
                <div class="footer">
                    <p>Report generated by: {{ auth()->user()->name ?? 'System' }}</p>
                    <p>Page 1 of 1 • Total Records: {{ $movements->total() ?? 0 }}</p>
                </div>
                
                <script>
                    function formatNumber(num) {
                        return num.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, ",");
                    }
                <\/script>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
    }, 500);
}

function exportToCSV() {
    // Get table data
    const table = document.getElementById('movementTable');
    if (!table) {
        alert('No data to export');
        return;
    }
    
    const rows = table.querySelectorAll('tr');
    const csvData = [];
    
    // Add headers
    const headers = [];
    table.querySelectorAll('th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csvData.push(headers.join(','));
    
    // Add rows
    rows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach(td => {
            let cellContent = td.textContent.trim();
            
            // Clean up content for CSV
            cellContent = cellContent.replace(/,/g, ';'); // Replace commas with semicolons
            cellContent = cellContent.replace(/"/g, '""'); // Escape double quotes
            cellContent = `"${cellContent}"`; // Wrap in quotes
            
            rowData.push(cellContent);
        });
        
        // Only add rows with data (skip empty rows)
        if (rowData.length > 0 && rowData.some(cell => cell !== '""')) {
            csvData.push(rowData.join(','));
        }
    });
    
    // Create CSV content
    const csvContent = csvData.join('\n');
    
    // Create download link
    const today = new Date().toISOString().split('T')[0];
    const filename = `stock_movements_${today}.csv`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        alert('Your browser does not support CSV download. Please try a different browser.');
    }
}
</script>

<style>
    .stats-card {
        transition: transform 0.2s;
        cursor: pointer;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .datepicker {
        cursor: pointer;
    }
    
    .popover {
        max-width: 400px;
    }
    
    .badge.bg-success, .badge.bg-danger, .badge.bg-info, .badge.bg-warning {
        font-size: 0.8em;
        padding: 4px 8px;
    }
    
    .table th {
        background-color: var(--primary-color);
        color: white;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(244, 163, 0, 0.1);
    }
</style>
@endsection