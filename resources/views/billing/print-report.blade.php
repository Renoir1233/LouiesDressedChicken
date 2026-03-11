<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business License Renewal Document - Louies Dressed Chicken</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #fff;
            color: #000;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background: white;
            border: 3px solid #660B05;
            border-radius: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        
        .official-mark {
            font-size: 12px;
            color: #660B05;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #660B05;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .document-type {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin: 15px 0 5px 0;
            text-transform: uppercase;
        }
        
        .document-subtitle {
            font-size: 14px;
            color: #333;
            font-style: italic;
        }
        
        .document-number {
            text-align: right;
            margin-top: 20px;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .content-section {
            margin: 25px 0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .business-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 15px 0;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            color: #660B05;
        }
        
        .info-value {
            font-size: 13px;
            margin-top: 3px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        
        .financial-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        
        .financial-box {
            border: 2px solid #660B05;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        
        .financial-label {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #660B05;
            margin-bottom: 8px;
        }
        
        .financial-value {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }
        
        .status-breakdown {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        
        .status-box {
            border: 2px solid #333;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
        }
        
        .status-count {
            font-size: 24px;
            font-weight: bold;
            color: #660B05;
            margin: 5px 0;
        }
        
        .status-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .orders-section {
            margin-top: 30px;
            page-break-before: avoid;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }
        
        .orders-table th {
            background-color: #660B05;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        .orders-table td {
            padding: 8px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        
        .orders-table tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-partial {
            background-color: #17a2b8;
            color: #fff;
        }
        
        .status-completed {
            background-color: #28a745;
            color: #fff;
        }
        
        .footer-section {
            margin-top: 40px;
            border-top: 2px solid #000;
            padding-top: 20px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .signature-block {
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 12px;
        }
        
        .official-seal {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #666;
            font-style: italic;
        }
        
        .date-printed {
            text-align: right;
            font-size: 11px;
            margin-bottom: 20px;
            color: #666;
        }
        
        .validity-notice {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 12px;
            border-radius: 3px;
        }
        
        .notice-title {
            font-weight: bold;
            color: #660B05;
            margin-bottom: 5px;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .container {
                border: 3px solid #660B05;
                box-shadow: none;
                page-break-after: avoid;
            }
            
            .no-print {
                display: none !important;
            }
            
            .orders-section {
                page-break-inside: avoid;
            }
        }
        
        .button-group {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 25px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-print {
            background-color: #660B05;
            color: white;
        }
        
        .btn-close {
            background-color: #666;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="official-mark">OFFICIAL BUSINESS DOCUMENT</div>
            <div class="company-name">Louies Dressed Chicken</div>
            <div class="document-type">Business License Renewal Report</div>
            <div class="document-subtitle">Financial & Operational Summary</div>
        </div>
        
        <!-- Date Printed -->
        <div class="date-printed">
            Document Generated: {{ date('F d, Y \a\t h:i A') }}
        </div>
        
        <!-- Business Information -->
        <div class="content-section">
            <div class="section-title">Business Information</div>
            <div class="business-info">
                <div class="info-item">
                    <div class="info-label">Business Name</div>
                    <div class="info-value">Louies Dressed Chicken</div>
                </div>
                <div class="info-item">
                    <div class="info-label">License Type</div>
                    <div class="info-value">Food Service / Retail</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Report Period</div>
                    <div class="info-value">
                        @if($period == 'today')
                            Today - {{ date('F d, Y') }}
                        @elseif($period == 'week')
                            Week of {{ date('F d, Y', strtotime('monday this week')) }}
                        @elseif($period == 'month')
                            {{ date('F Y') }}
                        @elseif($period == 'year')
                            Year {{ date('Y') }}
                        @else
                            {{ date('F d, Y') }}
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Report Status</div>
                    <div class="info-value">CURRENT</div>
                </div>
            </div>
        </div>
        
        <!-- Financial Summary -->
        <div class="content-section">
            <div class="section-title">Financial Summary</div>
            <div class="financial-summary">
                <div class="financial-box">
                    <div class="financial-label">Total Orders</div>
                    <div class="financial-value">{{ number_format($salesData['total_orders']) }}</div>
                </div>
                <div class="financial-box">
                    <div class="financial-label">Total Sales</div>
                    <div class="financial-value">₱{{ number_format($salesData['total_sales'], 2) }}</div>
                </div>
                <div class="financial-box">
                    <div class="financial-label">Total Payments</div>
                    <div class="financial-value">₱{{ number_format($salesData['total_paid'], 2) }}</div>
                </div>
                <div class="financial-box">
                    <div class="financial-label">Completed Orders</div>
                    <div class="financial-value">{{ number_format($salesData['completed_orders']) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Order Status Breakdown -->
        <div class="content-section">
            <div class="section-title">Transaction Status Breakdown</div>
            <div class="status-breakdown">
                <div class="status-box">
                    <div class="status-label">Pending Orders</div>
                    <div class="status-count">{{ number_format($salesData['pending_orders']) }}</div>
                </div>
                <div class="status-box">
                    <div class="status-label">Partial Orders</div>
                    <div class="status-count">{{ number_format($salesData['partial_orders']) }}</div>
                </div>
                <div class="status-box">
                    <div class="status-label">Completed Orders</div>
                    <div class="status-count">{{ number_format($salesData['completed_orders']) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Outstanding Balance Notice -->
        <div class="validity-notice">
            <div class="notice-title">Outstanding Receivables Notice</div>
            <div>Total Outstanding Balance: <strong>₱{{ number_format($salesData['outstanding_balance'], 2) }}</strong></div>
            <div style="margin-top: 5px;">This amount represents all pending and partial orders requiring follow-up or payment completion.</div>
        </div>
        
        <!-- Orders Details -->
        <div class="orders-section">
            <div class="section-title">Detailed Order Records</div>
            
            @if($orders->count() > 0)
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->customer_name ?? 'Walk-in Customer' }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="status-badge status-pending">Pending</span>
                                @elseif($order->status == 'partial')
                                    <span class="status-badge status-partial">Partial</span>
                                @else
                                    <span class="status-badge status-completed">Completed</span>
                                @endif
                            </td>
                            <td style="text-align: right;">₱{{ number_format($order->total, 2) }}</td>
                            <td style="text-align: right;">₱{{ number_format($order->amount_paid, 2) }}</td>
                            <td style="text-align: right;">₱{{ number_format(max(0, $order->total - $order->amount_paid), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 30px; color: #666;">
                    <p>No orders found for the selected period.</p>
                </div>
            @endif
        </div>
        
        <!-- Footer Section -->
        <div class="footer-section">
            <div class="section-title">Certification & Authorization</div>
            
            <div class="validity-notice">
                <div class="notice-title">License Renewal Eligibility</div>
                <div>This document certifies that Louies Dressed Chicken has maintained complete operational and financial records as required by local business regulations. All transactions have been properly documented and accounted for during the reporting period.</div>
            </div>
            
            <div class="signatures">
                <div class="signature-block">
                    <div style="font-size: 12px; margin-bottom: 5px;">Business Owner/Manager</div>
                    <div style="margin-bottom: 5px; margin-top: 20px;">
                        <span style="font-size: 14px; font-weight: bold; color: #000; text-decoration: underline;">________________Mr. Louie Lonzaga__________________</span>
                    </div>
                    <div style="border-bottom: 1px solid #000; margin-bottom: 3px; height: 20px;"></div>
                    <div style="font-size: 11px; margin-top: px;">Signature & Date</div>
                </div>
                <div class="signature-block">
                    <div style="font-size: 12px; margin-bottom: 5px;">Authorized Official</div>
                    <div class="signature-line">
                        ________________________________<br>
                        Signature & Date
                    </div>
                </div>
            </div>
        </div>
        
        <div class="official-seal">
            [OFFICIAL SEAL / STAMP AREA]<br>
            This document is valid for Business License Renewal purposes.
        </div>
        
        <!-- Print Buttons -->
        <div class="button-group no-print">
            <button class="btn btn-print" onclick="window.print()">Print Document</button>
            <button class="btn btn-close" onclick="window.close()">Close</button>
        </div>
    </div>
    
    <script>
        // Auto-print on page load (commented out for development)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
