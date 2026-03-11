<!-- resources/views/inventory/print/expiring.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiring Items Report</title>
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
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-info { background-color: #17a2b8; }
        .bg-success { background-color: #28a745; }
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
        <h1>Expiring Items Report</h1>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
        <div class="date">Period: Next 30 Days</div>
    </div>

    <div class="summary">
        <h3>Report Summary</h3>
        <p><strong>Total Items Expiring Soon:</strong> {{ $expiringItems->count() }}</p>
        <p><strong>Total Quantity:</strong> {{ $expiringItems->sum('remaining_quantity') }}</p>
        <p><strong>Total Value:</strong> ₱{{ number_format($expiringItems->sum(function($item) { return $item->remaining_quantity * $item->unit_cost; }), 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Batch</th>
                <th>Quantity</th>
                <th>Date Received</th>
                <th>Expiry Date</th>
                <th>Days Left</th>
                <th>Unit Cost</th>
                <th class="text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expiringItems as $index => $item)
                @php
                    $daysLeft = now()->diffInDays($item->expiry_date, false);
                    $badgeClass = $daysLeft < 0 ? 'bg-danger' : ($daysLeft < 7 ? 'bg-danger' : ($daysLeft < 15 ? 'bg-warning' : 'bg-info'));
                    $badgeText = $daysLeft < 0 ? 'Expired' : ($daysLeft . ' days');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div><strong>{{ $item->inventory->product_name }}</strong></div>
                        <small>{{ $item->inventory->product_code }}</small>
                    </td>
                    <td>{{ $item->batch_number ?? 'N/A' }}</td>
                    <td>{{ $item->remaining_quantity }} {{ $item->inventory->unit }}</td>
                    <td>{{ $item->date_received->format('M d, Y') }}</td>
                    <td>{{ $item->expiry_date->format('M d, Y') }}</td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                    </td>
                    <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->remaining_quantity * $item->unit_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        @if($expiringItems->count() > 0)
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="3">TOTAL</td>
                <td>{{ $expiringItems->sum('remaining_quantity') }}</td>
                <td colspan="4"></td>
                <td class="text-right">₱{{ number_format($expiringItems->sum(function($item) { return $item->remaining_quantity * $item->unit_cost; }), 2) }}</td>
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