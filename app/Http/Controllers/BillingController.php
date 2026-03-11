<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;

class BillingController extends Controller
{
    /**
     * Display billing dashboard
     */
    public function index()
    {
        return view('billing.index');
    }

    /**
     * Get pending and partial orders
     */
    public function getPendingOrders()
    {
        try {
            $pendingOrders = Order::with(['items', 'payments'])
                ->whereIn('status', ['pending', 'partial'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    $totalAmount = floatval($order->total);
                    $amountPaid = floatval($order->amount_paid);
                    $remainingBalance = $totalAmount - $amountPaid;

                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->customer_name ?? 'Walk-in Customer',
                        'customer_phone' => $order->customer_phone,
                        'customer_address' => $order->customer_address,
                        'total_amount' => $totalAmount,
                        'amount_paid' => $amountPaid,
                        'remaining_balance' => max(0, $remainingBalance),
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'is_fully_paid' => $order->is_fully_paid,
                        'payment_count' => $order->payments->count(),
                        'created_at' => $order->created_at->format('M d, Y h:i A'),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product_name,
                                'quantity' => floatval($item->quantity),
                                'unit' => $item->unit,
                                'price' => floatval($item->price),
                                'total' => floatval($item->total)
                            ];
                        }),
                        'recent_payments' => $order->payments->take(3)->map(function ($payment) {
                            return [
                                'id' => $payment->id,
                                'amount' => floatval($payment->amount),
                                'reference_number' => $payment->reference_number,
                                'payment_date' => $payment->payment_date->format('M d, Y h:i A'),
                                'notes' => $payment->notes,
                                'payment_method' => $payment->payment_method
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'orders' => $pendingOrders
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading pending orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders: ' . $e->getMessage(),
                'orders' => []
            ]);
        }
    }

    /**
     * Get completed orders with date filter
     */
    public function getCompletedOrders(Request $request)
    {
        try {
            $dateFilter = $request->get('date', 'today');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $query = Order::with(['items', 'payments'])
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc');

            // Apply date filter
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                    break;
                case 'custom':
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($startDate)->startOfDay(),
                            Carbon::parse($endDate)->endOfDay()
                        ]);
                    }
                    break;
            }

            $completedOrders = $query->get()->map(function ($order) {
                $totalAmount = floatval($order->total);
                $amountPaid = floatval($order->amount_paid);

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name ?? 'Walk-in Customer',
                    'customer_phone' => $order->customer_phone,
                    'customer_address' => $order->customer_address,
                    'total_amount' => $totalAmount,
                    'amount_paid' => $amountPaid,
                    'remaining_balance' => max(0, $totalAmount - $amountPaid),
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'is_fully_paid' => $order->is_fully_paid,
                    'payment_count' => $order->payments->count(),
                    'completed_at' => $order->updated_at->format('M d, Y h:i A'),
                    'created_at' => $order->created_at->format('M d, Y h:i A'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity' => floatval($item->quantity),
                            'unit' => $item->unit,
                            'price' => floatval($item->price),
                            'total' => floatval($item->total)
                        ];
                    }),
                    'payments' => $order->payments->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'amount' => floatval($payment->amount),
                            'reference_number' => $payment->reference_number,
                            'payment_method' => $payment->payment_method,
                            'payment_date' => $payment->payment_date->format('M d, Y h:i A'),
                            'notes' => $payment->notes
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'orders' => $completedOrders,
                'date_filter' => $dateFilter,
                'total_amount' => $completedOrders->sum('total_amount'),
                'total_paid' => $completedOrders->sum('amount_paid'),
                'total_orders' => $completedOrders->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading completed orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load completed orders: ' . $e->getMessage(),
                'orders' => []
            ]);
        }
    }

    /**
     * Get all orders with filters
     */
    public function getAllOrders(Request $request)
    {
        try {
            $statusFilter = $request->get('status', 'all');
            $dateFilter = $request->get('date', 'today');
            
            $query = Order::with(['items', 'payments'])
                ->orderBy('created_at', 'desc');

            // Apply status filter
            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            // Apply date filter
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                    break;
            }

            $orders = $query->get()->map(function ($order) {
                $totalAmount = floatval($order->total);
                $amountPaid = floatval($order->amount_paid);
                $remainingBalance = $totalAmount - $amountPaid;

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name ?? 'Walk-in Customer',
                    'customer_phone' => $order->customer_phone,
                    'customer_address' => $order->customer_address,
                    'total_amount' => $totalAmount,
                    'amount_paid' => $amountPaid,
                    'remaining_balance' => max(0, $remainingBalance),
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'is_fully_paid' => $order->is_fully_paid,
                    'payment_count' => $order->payments->count(),
                    'created_at' => $order->created_at->format('M d, Y h:i A'),
                    'updated_at' => $order->updated_at->format('M d, Y h:i A'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity' => floatval($item->quantity),
                            'unit' => $item->unit,
                            'price' => floatval($item->price),
                            'total' => floatval($item->total)
                        ];
                    }),
                    'recent_payments' => $order->payments->take(2)->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'amount' => floatval($payment->amount),
                            'reference_number' => $payment->reference_number,
                            'payment_date' => $payment->payment_date->format('M d, Y h:i A'),
                            'payment_method' => $payment->payment_method
                        ];
                    })
                ];
            });

            // Calculate summary statistics
            $summary = [
                'total_orders' => $orders->count(),
                'total_sales' => $orders->sum('total_amount'),
                'total_paid' => $orders->sum('amount_paid'),
                'total_balance' => $orders->sum('remaining_balance'),
                'completed_orders' => $orders->where('status', 'completed')->count(),
                'pending_orders' => $orders->where('status', 'pending')->count(),
                'partial_orders' => $orders->where('status', 'partial')->count(),
                'cancelled_orders' => $orders->where('status', 'cancelled')->count()
            ];

            return response()->json([
                'success' => true,
                'orders' => $orders,
                'summary' => $summary,
                'filters' => [
                    'status' => $statusFilter,
                    'date' => $dateFilter
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading all orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders: ' . $e->getMessage(),
                'orders' => []
            ]);
        }
    }

    /**
     * Process payment for an order
     */
    public function payOrder(Order $order, Request $request)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $paymentAmount = floatval($request->payment_amount);
            $previousPaid = floatval($order->amount_paid);
            $newPaidAmount = $previousPaid + $paymentAmount;
            $totalAmount = floatval($order->total);

            // Check if payment exceeds total amount
            if ($newPaidAmount > $totalAmount) {
                $excessAmount = $newPaidAmount - $totalAmount;
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds total order amount by ₱' . number_format($excessAmount, 2) . '. Total: ₱' . number_format($totalAmount, 2) . ', Attempted: ₱' . number_format($newPaidAmount, 2)
                ]);
            }

            // Check if order is already completed
            if ($order->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already completed and fully paid.'
                ]);
            }

            // Use the Order model's addPayment method
            $payment = $order->addPayment($paymentAmount, $request->payment_method, $request->notes);

            DB::commit();

            $statusChange = $order->status === 'completed' ? ' and completed' : '';
            
            Log::info("Payment processed: ₱" . number_format($paymentAmount, 2) . " for Order {$order->order_number}{$statusChange}. Reference: {$payment->reference_number}");

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully!' . ($order->status === 'completed' ? ' Order is now completed.' : ''),
                'payment_reference' => $payment->reference_number,
                'payment_id' => $payment->id,
                'order' => [
                    'status' => $order->status,
                    'amount_paid' => $order->amount_paid,
                    'remaining_balance' => $order->remaining_balance,
                    'is_fully_paid' => $order->is_fully_paid,
                    'payment_status' => $order->payment_status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process full payment for an order
     */
    public function processFullPayment(Order $order)
    {
        try {
            DB::beginTransaction();

            $totalAmount = floatval($order->total);
            $amountPaid = floatval($order->amount_paid);
            $remainingBalance = $totalAmount - $amountPaid;

            if ($remainingBalance <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already fully paid.'
                ]);
            }

            if ($order->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already completed.'
                ]);
            }

            // Use the Order model's addPayment method for the full remaining balance
            $payment = $order->addPayment($remainingBalance, 'cash', 'Full payment completion');

            DB::commit();

            Log::info("Full payment processed: ₱" . number_format($remainingBalance, 2) . " for Order {$order->order_number}. Reference: {$payment->reference_number}");

            return response()->json([
                'success' => true,
                'message' => 'Full payment processed successfully! Order is now completed.',
                'payment_reference' => $payment->reference_number,
                'payment_id' => $payment->id,
                'order' => [
                    'status' => $order->status,
                    'amount_paid' => $order->amount_paid,
                    'remaining_balance' => $order->remaining_balance,
                    'is_fully_paid' => $order->is_fully_paid
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing full payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process full payment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get sales reports data
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        \Log::info('Reports request', [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        $salesData = $this->getSalesData($period, $startDate, $endDate);
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $partialOrdersCount = Order::where('status', 'partial')->count();
        $completedOrdersCount = Order::where('status', 'completed')->count();

        // Get recent transactions for the dashboard
        $recentTransactions = Payment::with(['order'])
            ->whereBetween('payment_date', [
                Carbon::parse($salesData['start_date'])->startOfDay(),
                Carbon::parse($salesData['end_date'])->endOfDay()
            ])
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'order_number' => $payment->order->order_number ?? 'N/A',
                    'customer_name' => $payment->order->customer_name ?? 'Walk-in Customer',
                    'amount' => floatval($payment->amount),
                    'reference_number' => $payment->reference_number,
                    'payment_method' => $payment->payment_method,
                    'payment_date' => $payment->payment_date->format('M d, h:i A'),
                    'notes' => $payment->notes
                ];
            });

        return response()->json([
            'success' => true,
            'sales_data' => $salesData,
            'recent_transactions' => $recentTransactions,
            'counts' => [
                'pending' => $pendingOrdersCount,
                'partial' => $partialOrdersCount,
                'completed' => $completedOrdersCount,
                'total' => $pendingOrdersCount + $partialOrdersCount + $completedOrdersCount
            ]
        ]);
    }

    /**
     * Get sales data for a specific period
     */
    private function getSalesData($period, $customStartDate = null, $customEndDate = null)
    {
        // Query only completed orders for date filtering
        $query = Order::where('status', 'completed');

        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;
                
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;
                
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;
                
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $query->whereBetween('created_at', [$startDate, $endDate]);
                break;
                
            case 'custom':
                if ($customStartDate && $customEndDate) {
                    $startDate = Carbon::parse($customStartDate)->startOfDay();
                    $endDate = Carbon::parse($customEndDate)->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                break;
        }

        // For completed orders: apply date filter
        $completedOrders = $query->count();
        
        // For pending and partial orders: ignore date filter, always include them
        $pendingOrders = Order::where('status', 'pending')->count();
        $partialOrders = Order::where('status', 'partial')->count();
        
        // Total orders includes all completed (filtered by date) + all pending/partial (no date filter)
        $totalOrders = $completedOrders + $pendingOrders + $partialOrders;
        
        // Sales totals only from completed orders in the period
        $totalSales = floatval($query->sum('total'));
        $totalPaid = floatval($query->sum('amount_paid'));

        // Get cash payments total from payments table
        $cashPayments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('payment_method', 'cash')
            ->sum('amount');

        // Get payment statistics
        $totalPayments = Payment::whereBetween('payment_date', [$startDate, $endDate])->count();
        $averagePayment = $totalPayments > 0 ? $cashPayments / $totalPayments : 0;

        // Calculate total outstanding balance from ALL pending/partial orders (not filtered by date)
        // Receivables exist regardless of when the order was created
        $outstandingBalance = Order::whereIn('status', ['pending', 'partial'])
            ->sum(DB::raw('GREATEST(0, total - amount_paid)'));

        return [
            'total_sales' => $totalSales,
            'total_paid' => $totalPaid,
            'total_balance' => abs($totalSales - $totalPaid),
            'outstanding_balance' => max(0, floatval($outstandingBalance)),
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'partial_orders' => $partialOrders,
            'cash_payments' => floatval($cashPayments),
            'total_payments_count' => $totalPayments,
            'average_payment' => floatval($averagePayment),
            'period' => $period,
            'start_date' => $startDate ? $startDate->format('M d, Y') : null,
            'end_date' => $endDate ? $endDate->format('M d, Y') : null
        ];
    }

    /**
     * Print report view
     */
    public function printReport(Request $request)
    {
        $period = $request->get('period', 'today');
        $salesData = $this->getSalesData($period);
        
        // Get completed orders for the period + all pending/partial orders
        $completedOrders = Order::with('items')
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::parse($salesData['start_date'])->startOfDay(),
                Carbon::parse($salesData['end_date'])->endOfDay()
            ])
            ->get();
        
        // Get all pending and partial orders (regardless of date)
        $pendingPartialOrders = Order::with('items')
            ->whereIn('status', ['pending', 'partial'])
            ->get();
        
        // Merge both collections
        $orders = $completedOrders->concat($pendingPartialOrders)->sortByDesc('created_at');

        // Get payment details for the period
        $payments = Payment::with('order')
            ->whereBetween('payment_date', [
                Carbon::parse($salesData['start_date'])->startOfDay(),
                Carbon::parse($salesData['end_date'])->endOfDay()
            ])
            ->orderBy('payment_date', 'desc')
            ->get();
        
        return view('billing.print-report', compact('salesData', 'period', 'orders', 'payments'));
    }

    /**
     * Fix order totals (maintenance function)
     */
    public function fixOrderTotals()
    {
        try {
            $orders = Order::with('items')->get();
            $fixedCount = 0;

            foreach ($orders as $order) {
                // Calculate correct total from items
                $calculatedTotal = $order->items->sum(function ($item) {
                    return floatval($item->price) * floatval($item->quantity);
                });

                // Update if there's a discrepancy
                if (abs(floatval($order->total) - $calculatedTotal) > 0.01) {
                    $oldTotal = $order->total;
                    $order->total = $calculatedTotal;
                    $order->subtotal = $calculatedTotal / 1.12;
                    $order->tax = $calculatedTotal - $order->subtotal;
                    
                    $order->save();
                    $fixedCount++;
                    
                    Log::info("Fixed order total: {$order->order_number} - ₱{$oldTotal} -> ₱{$calculatedTotal}");
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Fixed {$fixedCount} orders with incorrect totals"
            ]);

        } catch (\Exception $e) {
            Log::error('Error fixing order totals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fix order totals: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get order payments
     */
    public function getOrderPayments(Order $order)
    {
        try {
            $payments = $order->payments()->orderBy('payment_date', 'desc')->get();

            $paymentSummary = [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'total_amount' => floatval($order->total),
                'amount_paid' => floatval($order->amount_paid),
                'remaining_balance' => floatval($order->remaining_balance),
                'payment_status' => $order->payment_status,
                'is_fully_paid' => $order->is_fully_paid,
                'payments' => $payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => floatval($payment->amount),
                        'payment_method' => $payment->payment_method,
                        'reference_number' => $payment->reference_number,
                        'payment_date' => $payment->payment_date->format('M d, Y h:i A'),
                        'notes' => $payment->notes,
                        'created_at' => $payment->created_at->format('M d, Y h:i A')
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'payment_summary' => $paymentSummary
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading order payments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load payment history'
            ]);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            $today = Carbon::today();
            $weekStart = Carbon::now()->startOfWeek();
            $monthStart = Carbon::now()->startOfMonth();

            // Calculate total receivables (outstanding balance - positive values only)
            $totalReceivables = Order::whereIn('status', ['pending', 'partial'])
                ->sum(DB::raw('GREATEST(0, total - amount_paid)'));

            $stats = [
                'today' => [
                    'orders' => Order::whereDate('created_at', $today)->count(),
                    'sales' => floatval(Order::whereDate('created_at', $today)->sum('total')),
                    'payments' => floatval(Payment::whereDate('payment_date', $today)->sum('amount')),
                    'payment_count' => Payment::whereDate('payment_date', $today)->count(),
                    'completed_orders' => Order::whereDate('created_at', $today)->where('status', 'completed')->count()
                ],
                'week' => [
                    'orders' => Order::where('created_at', '>=', $weekStart)->count(),
                    'sales' => floatval(Order::where('created_at', '>=', $weekStart)->sum('total')),
                    'payments' => floatval(Payment::where('payment_date', '>=', $weekStart)->sum('amount')),
                    'payment_count' => Payment::where('payment_date', '>=', $weekStart)->count(),
                    'completed_orders' => Order::where('created_at', '>=', $weekStart)->where('status', 'completed')->count()
                ],
                'month' => [
                    'orders' => Order::where('created_at', '>=', $monthStart)->count(),
                    'sales' => floatval(Order::where('created_at', '>=', $monthStart)->sum('total')),
                    'payments' => floatval(Payment::where('payment_date', '>=', $monthStart)->sum('amount')),
                    'payment_count' => Payment::where('payment_date', '>=', $monthStart)->count(),
                    'completed_orders' => Order::where('created_at', '>=', $monthStart)->where('status', 'completed')->count()
                ],
                'pending_orders' => Order::where('status', 'pending')->count(),
                'partial_orders' => Order::where('status', 'partial')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'cancelled_orders' => Order::where('status', 'cancelled')->count(),
                'total_payments' => Payment::count(),
                'total_cash_collected' => floatval(Payment::where('payment_method', 'cash')->sum('amount')),
                'total_receivables' => floatval($totalReceivables),
                'average_order_value' => Order::count() > 0 ? floatval(Order::sum('total') / Order::count()) : 0
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'updated_at' => Carbon::now()->format('M d, Y h:i A')
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading dashboard stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard statistics'
            ]);
        }
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Order $order, Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            if ($order->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already cancelled.'
                ]);
            }

            if ($order->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel completed order.'
                ]);
            }

            // Use the Order model's cancelOrder method
            $order->cancelOrder($request->reason);

            DB::commit();

            Log::info("Order {$order->order_number} cancelled. Reason: " . ($request->reason ?? 'No reason provided'));

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get payment history for a specific order
     */
    public function getPaymentHistory(Order $order)
    {
        try {
            $payments = $order->payments()
                ->orderBy('payment_date', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'reference_number' => $payment->reference_number,
                        'amount' => floatval($payment->amount),
                        'payment_method' => $payment->payment_method,
                        'payment_date' => $payment->payment_date->format('M d, Y h:i A'),
                        'notes' => $payment->notes,
                        'created_at' => $payment->created_at->format('M d, Y h:i A')
                    ];
                });

            return response()->json([
                'success' => true,
                'order' => [
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total_amount' => floatval($order->total),
                    'amount_paid' => floatval($order->amount_paid),
                    'remaining_balance' => floatval($order->remaining_balance),
                    'status' => $order->status,
                    'payment_status' => $order->payment_status
                ],
                'payments' => $payments
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading payment history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load payment history'
            ]);
        }
    }

    /**
     * Get detailed order information
     */
    public function getOrderDetails(Order $order)
    {
        try {
            $order->load(['items', 'payments']);
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_address' => $order->customer_address,
                    'total_amount' => floatval($order->total),
                    'amount_paid' => floatval($order->amount_paid),
                    'remaining_balance' => floatval($order->remaining_balance),
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'is_fully_paid' => $order->is_fully_paid,
                    'created_at' => $order->created_at->format('M d, Y h:i A'),
                    'completed_at' => $order->status === 'completed' ? $order->updated_at->format('M d, Y h:i A') : null,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity' => floatval($item->quantity),
                            'unit' => $item->unit,
                            'price' => floatval($item->price),
                            'total' => floatval($item->total)
                        ];
                    }),
                    'payments' => $order->payments->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'amount' => floatval($payment->amount),
                            'payment_method' => $payment->payment_method,
                            'reference_number' => $payment->reference_number,
                            'payment_date' => $payment->payment_date->format('M d, Y h:i A'),
                            'notes' => $payment->notes
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading order details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load order details'
            ]);
        }
    }

    /**
     * Mark order as completed manually
     */
    public function completeOrder(Order $order, Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            if ($order->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already completed.'
                ]);
            }

            // Check if order is fully paid
            if (floatval($order->remaining_balance) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot complete order with outstanding balance.'
                ]);
            }

            $order->status = 'completed';
            $order->payment_status = 'paid';
            $order->is_fully_paid = true;
            $order->save();

            // Log the completion
            Log::info("Order {$order->order_number} marked as completed manually. Notes: " . ($request->notes ?? 'No notes provided'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order marked as completed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete order: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate receipt for payment
     */
    public function generateReceipt(Payment $payment)
    {
        try {
            $payment->load(['order', 'order.items']);
            $receiptData = [
                'payment' => $payment,
                'order' => $payment->order,
                'date' => $payment->payment_date->format('M d, Y h:i A'),
                'company_name' => config('app.name', 'Your Business Name'),
                'company_address' => config('app.address', 'Your Business Address'),
                'company_phone' => config('app.phone', 'Your Business Phone'),
                'tax_rate' => 0.12, // 12% VAT
            ];

            // Calculate tax
            $subtotal = $payment->order->total / 1.12;
            $tax = $payment->order->total - $subtotal;

            $receiptData['subtotal'] = $subtotal;
            $receiptData['tax'] = $tax;
            $receiptData['remaining_balance'] = $payment->order->remaining_balance;

            // Return a web view for printing instead of PDF
            return view('billing.receipt', $receiptData);

        } catch (\Exception $e) {
            \Log::error('Error generating receipt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate receipt: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get recent transactions for dashboard
     */
    public function getRecentTransactions()
    {
        try {
            $recentTransactions = Payment::with(['order'])
                ->orderBy('payment_date', 'desc')
                ->take(10)
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'order_number' => $payment->order->order_number ?? 'N/A',
                        'customer_name' => $payment->order->customer_name ?? 'Walk-in Customer',
                        'amount' => floatval($payment->amount),
                        'reference_number' => $payment->reference_number,
                        'payment_method' => $payment->payment_method,
                        'payment_date' => $payment->payment_date->format('M d, h:i A'),
                        'notes' => $payment->notes,
                        'status_color' => $payment->order->status === 'completed' ? 'success' : 
                                          ($payment->order->status === 'partial' ? 'info' : 'warning')
                    ];
                });

            return response()->json([
                'success' => true,
                'transactions' => $recentTransactions
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading recent transactions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load recent transactions'
            ]);
        }
    }
}