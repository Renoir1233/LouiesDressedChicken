<!-- resources/views/orders/sales-report.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #660B05;
            margin-bottom: 5px;
        }
        .report-period {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            flex: 1;
            background-color: #f8f9fa;
            padding: 15px;
            margin: 0 5px;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #660B05;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #660B05;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #660B05;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="report-period">Sales Report: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div>Total Revenue</div>
            <div class="summary-value">₱{{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="summary-card">
            <div>Total Orders</div>
            <div class="summary-value">{{ $totalOrders }}</div>
        </div>
        <div class="summary-card">
            <div>Items Sold</div>
            <div class="summary-value">{{ $totalItems }}</div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Payment</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
                <td>{{ $order->customer_name ?: 'Walk-in' }}</td>
                <td>{{ $order->items->count() }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td>₱{{ number_format($order->total, 2) }}</td>
            </tr>
            @endforeach
            @if($orders->isEmpty())
            <tr>
                <td colspan="6" style="text-align: center;">No orders found for this period</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ $generatedAt }}<br>
        {{ $company['name'] }} - {{ $company['address'] }}
    </div>
</body>
</html>