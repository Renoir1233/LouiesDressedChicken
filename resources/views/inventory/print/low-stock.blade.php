<!-- resources/views/inventory/print/low-stock.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Report</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #660B05;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
        .text-right { text-align: right; }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #660B05;
        }
        .summary h3 {
            margin-top: 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            @page {
                size: A4;
                margin: 20mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">LOUIES DRESSED CHICKEN</div>
        <h1>Low Stock Report</h1>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>

    <div class="summary">
        <h3>Report Summary</h3>
        <p><strong>Total Low Stock Items:</strong> {{ $lowStockItems->count() }}</p>
        <p><strong>Total Quantity Below Alert:</strong> {{ $lowStockItems->sum(function($item) { return $item->low_stock_alert - $item->quantity; }) }}</p>
        <p><strong>Total Value at Risk:</strong> ₱{{ number_format($lowStockItems->sum(function($item) { return $item->quantity * $item->cost_price; }), 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Supplier</th>
                <th>Current Stock</th>
                <th>Alert Level</th>
                <th>Difference</th>
                <th>Cost Price</th>
                <th class="text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStockItems as $index => $item)
                @php
                    $difference = $item->low_stock_alert - $item->quantity;
                    $badgeClass = $item->quantity == 0 ? 'bg-danger' : 'bg-warning';
                    $badgeText = $item->quantity == 0 ? 'Out of Stock' : 'Low Stock';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div><strong>{{ $item->product_name }}</strong></div>
                        <small>{{ $item->product_code }}</small>
                    </td>
                    <td>{{ $item->supplier->name }}</td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $item->quantity }} {{ $item->unit }}</span>
                    </td>
                    <td>{{ $item->low_stock_alert }} {{ $item->unit }}</td>
                    <td>{{ $difference }} {{ $item->unit }}</td>
                    <td>₱{{ number_format($item->cost_price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->quantity * $item->cost_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        @if($lowStockItems->count() > 0)
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="3">TOTAL</td>
                <td>{{ $lowStockItems->sum('quantity') }}</td>
                <td>{{ $lowStockItems->sum('low_stock_alert') }}</td>
                <td>{{ $lowStockItems->sum(function($item) { return $item->low_stock_alert - $item->quantity; }) }}</td>
                <td></td>
                <td class="text-right">₱{{ number_format($lowStockItems->sum(function($item) { return $item->quantity * $item->cost_price; }), 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Report generated by: {{ auth()->user()->name ?? 'System' }}</p>
        <p>Page 1 of 1</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>