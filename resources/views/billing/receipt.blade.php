
@php
    $order = $payment->order;
    // Use the same logic as the order model's getRemainingBalanceAttribute
    $balance = isset($order->remaining_balance) ? $order->remaining_balance : ((isset($order->total) && isset($order->amount_paid)) ? max(0, $order->total - $order->amount_paid) : 0);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->reference_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .receipt {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-row {
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }
        .status-badge {
            background: #ffc107;
            color: #000;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
            .receipt { border: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="company-name">LOUIES DRESSED CHICKEN</div>
            <div>Fresh Chicken Products</div>
            <div>Contact: +63 XXX-XXXX-XXX</div>
        </div>

        <div class="divider"></div>

        <div class="item-row">
            <div>Receipt #:</div>
            <div>{{ $payment->reference_number }}</div>
        </div>
        <div class="item-row">
            <div>Date:</div>
            <div>{{ $payment->payment_date->format('M d, Y h:i A') }}</div>
        </div>
        <div class="item-row">
            <div>Order #:</div>
            <div>{{ $order->order_number ?? $order->id }}</div>
        </div>
        @if($order->user_id)
        <div class="item-row">
            <div>Staff:</div>
            <div>{{ $order->user->name ?? 'Staff #' . $order->user_id }}</div>
        </div>
        @endif
        @if($order->customer_name)
        <div class="item-row">
            <div>Customer:</div>
            <div>{{ $order->customer_name }}</div>
        </div>
        @endif
        @if($order->customer_phone)
        <div class="item-row">
            <div>Contact:</div>
            <div>{{ $order->customer_phone }}</div>
        </div>
        @endif
        @if($order->customer_address)
        <div class="item-row">
            <div>Address:</div>
            <div>{{ $order->customer_address }}</div>
        </div>
        @endif

        <div class="divider"></div>

        <!-- Order Items -->
        @foreach($order->items as $item)
        <div class="item-row">
            <div>{{ $item->product_name }}</div>
            <div>&#8369;{{ number_format((float)($item->price ?? 0), 2) }}</div>
        </div>
        <div class="item-row">
            <div>{{ $item->quantity }} {{ $item->unit ?? '' }} × &#8369;{{ number_format((float)($item->price ?? 0), 2) }}</div>
            <div>&#8369;{{ number_format((float)($item->total ?? ($item->price * $item->quantity)), 2) }}</div>
        </div>
        @endforeach

        <div class="divider"></div>

        <!-- Totals -->
        <div class="item-row">
            <div>Subtotal:</div>
            <div>&#8369;{{ number_format((float)($order->subtotal ?? 0), 2) }}</div>
        </div>
        <div class="item-row">
            <div>Tax (12%):</div>
            <div>&#8369;{{ number_format((float)($order->tax ?? 0), 2) }}</div>
        </div>
        <div class="item-row total-row">
            <div>TOTAL:</div>
            <div>&#8369;{{ number_format((float)($order->total ?? 0), 2) }}</div>
        </div>
        <div class="item-row">
            <div>Amount Paid:</div>
            <div>&#8369;{{ number_format((float)($payment->amount ?? 0), 2) }}</div>
        </div>
        <div class="item-row">
            <div>Balance:</div>
            <div>&#8369;{{ number_format((float)($balance ?? 0), 2) }}</div>
        </div>
        <div class="item-row">
            <div>Payment:</div>
            <div>{{ strtoupper($payment->payment_method) }}</div>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <div>Thank you for your payment!</div>
            <div>Please come again</div>
            <div>*** END OF RECEIPT ***</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 1000);
        }
    </script>
</body>
</html>
