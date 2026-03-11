<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\AuditLog;
use App\Models\StockInTransaction;
use App\Models\StockOutTransaction;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\AuditLogService;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:orders.*')->only(['index', 'getProducts', 'listOrders', 'searchOrders', 'getOrderStats', 'searchCustomers', 'getCustomerPendingOrders', 'searchCustomersEnhanced']);
        $this->middleware('permission:orders.create')->only(['store', 'addItemsToOrder', 'addItemsToExistingOrder']);
        $this->middleware('permission:orders.edit')->only(['show', 'completeOrder', 'cancelOrder']);
        $this->middleware('permission:orders.delete')->only(['deletePendingOrder']);
    }

    /**
     * Display the order management page
     */
    public function index()
    {
        // Log view action
        AuditLogService::log('viewed', null, 'Viewed order management page');
        
        return view('orders.index');
    }

    /**
     * Get all active products for ordering
     */
    public function getProducts()
    {
        try {
            Log::info('Fetching products for order page...');
            
            $products = Inventory::with('supplier')
                ->where('is_active', true)
                ->where('quantity', '>', 0)
                ->orderBy('product_name')
                ->get();

            Log::info('Products found: ' . $products->count());

            $formattedProducts = $products->map(function($product) {
                $imageUrl = $product->image ? asset('storage/' . $product->image) : 
                            'https://via.placeholder.com/300x200/EFEFEF/666666?text=No+Image';
                
                return [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'image' => $imageUrl,
                    'selling_price' => (float) $product->selling_price,
                    'quantity' => (float) $product->quantity,
                    'unit' => $product->unit,
                    'category' => $product->category,
                    'supplier' => $product->supplier->name ?? 'N/A',
                    'low_stock_alert' => (float) $product->low_stock_alert,
                    'cost_price' => (float) $product->cost_price
                ];
            });

            return response()->json([
                'success' => true,
                'products' => $formattedProducts,
                'total' => $formattedProducts->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching products for order: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load products: ' . $e->getMessage(),
                'products' => [],
                'total' => 0
            ], 500);
        }
    }

    /**
     * Store a new order
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Check if this is adding items to existing order
            if ($request->has('link_to_existing_order') && $request->link_to_existing_order && $request->has('existing_order_id')) {
                return $this->addItemsToExistingOrder($request->existing_order_id, $request);
            }

            // Log order creation attempt
            AuditLogService::log(
                'order_attempt',
                null,
                'Order creation attempted by ' . auth()->user()->name,
                null,
                ['customer_name' => $request->customer_name, 'items_count' => count($request->items ?? [])]
            );
            
            // Validate the request
            $validated = $request->validate([
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_address' => 'nullable|string|max:255',
                'customer_type' => 'required|string|in:walk-in,regular',
                'payment_status' => 'required|string|in:paid,credit',
                'amount_paid' => 'required|numeric|min:0',
                'change' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'status' => 'required|string|in:pending,completed',
                'items' => 'required|array|min:1',
                'items.*.inventory_id' => 'required|exists:inventory,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.price' => 'required|numeric|min:0.01',
                'items.*.unit' => 'required|string|max:50'
            ]);

            // Walk-in customers can only have cash payment (paid status)
            if ($validated['customer_type'] === 'walk-in' && $validated['payment_status'] === 'credit') {
                throw new \Exception('Walk-in customers cannot use credit payment. Only regular customers can purchase on credit.');
            }
            // For regular customers, payment_status is determined by status: completed = paid, pending = credit
            if ($validated['customer_type'] === 'regular') {
                if ($validated['status'] === 'pending' && $validated['payment_status'] !== 'credit') {
                    throw new \Exception('Pending regular orders must be credit.');
                }
                if ($validated['status'] === 'completed' && $validated['payment_status'] !== 'paid') {
                    throw new \Exception('Completed regular orders must be paid.');
                }
                if ($validated['payment_status'] === 'credit' && (empty($validated['customer_name']) || empty($validated['customer_phone']))) {
                    throw new \Exception('Customer name and phone number are required for credit orders.');
                }
                
                // RESTRICTION: Regular customers can only have one pending/partial unpaid order
                if ($validated['status'] === 'pending' || ($validated['status'] === 'partial' && $validated['payment_status'] === 'credit')) {
                    $existingUnpaidOrder = Order::where('customer_name', $validated['customer_name'])
                        ->where('customer_phone', $validated['customer_phone'])
                        ->whereIn('status', ['pending', 'partial'])
                        ->where('amount_paid', '<', DB::raw('total'))
                        ->first();
                    
                    if ($existingUnpaidOrder) {
                        throw new \Exception(
                            "This customer already has an unpaid pending/partial order (#{$existingUnpaidOrder->order_number}). " .
                            "Regular customers can only have one pending/partial order at a time unless fully paid. " .
                            "Please complete or cancel the existing order first."
                        );
                    }
                }
            }

            // Validate stock availability for ALL orders (both pending and completed)
            foreach ($validated['items'] as $item) {
                $inventory = Inventory::find($item['inventory_id']);
                if (!$inventory) {
                    throw new \Exception("Product not found: {$item['inventory_id']}");
                }

                if ($inventory->quantity < $item['quantity']) {
                    $errorMsg = "Insufficient stock for {$inventory->product_name}. Available: {$inventory->quantity} {$inventory->unit}, Requested: {$item['quantity']} {$item['unit']}";
                    
                    // Log stock shortage
                    AuditLogService::log(
                        'stock_shortage',
                        $inventory,
                        $errorMsg . " - Order attempted by " . auth()->user()->name
                    );
                    
                    throw new \Exception($errorMsg);
                }
            }

            // Calculate totals
            $subtotal = 0;
            $itemsDetail = [];
            foreach ($validated['items'] as $item) {
                $inventory = Inventory::find($item['inventory_id']);
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;
                
                $itemsDetail[] = [
                    'product' => $inventory->product_name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $itemTotal
                ];
            }

            $tax = $subtotal * 0.12; // 12% VAT
            $total = $subtotal + $tax;

            // Validate payment for completed orders
            if ($validated['status'] === 'completed') {
                if ($validated['amount_paid'] < $total) {
                    $errorMsg = "Amount paid (₱{$validated['amount_paid']}) is less than total amount (₱{$total}).";
                    // Log payment validation failure
                    AuditLogService::log(
                        'payment_failure',
                        null,
                        $errorMsg . " - Order attempted by " . auth()->user()->name
                    );
                    throw new \Exception($errorMsg);
                }
            }

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'customer_type' => $validated['customer_type'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'amount_paid' => $validated['amount_paid'],
                'change' => $validated['change'],
                'payment_method' => 'cash',
                'payment_status' => $validated['payment_status'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
                'user_id' => auth()->id()
            ]);

            // Create order items and process stock deduction with FIFO
            foreach ($validated['items'] as $item) {
                $inventory = Inventory::find($item['inventory_id']);
                $itemTotal = $item['price'] * $item['quantity'];

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'inventory_id' => $item['inventory_id'],
                    'product_name' => $inventory->product_name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'total' => $itemTotal
                ]);

                // Update inventory stock
                $oldQuantity = $inventory->quantity;
                $inventory->decrement('quantity', $item['quantity']);
                $newQuantity = $inventory->fresh()->quantity;
                
                // Process FIFO batches and create stock out transaction
                $quantityToDeduct = $item['quantity'];
                
                // Get batches ordered by date (FIFO)
                $batches = StockInTransaction::where('inventory_id', $inventory->id)
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('date_received', 'asc')
                    ->get();

                // Create stock out transaction
                $stockOut = StockOutTransaction::create([
                    'reference_number' => StockOutTransaction::generateReferenceNumber(),
                    'inventory_id' => $inventory->id,
                    'quantity_removed' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_value' => $item['price'] * $item['quantity'],
                    'reason' => 'sale',
                    'date_removed' => now(),
                    'handled_by' => auth()->id(),
                    'notes' => "Sale - Order {$orderNumber}",
                ]);

                foreach ($batches as $batch) {
                    if ($quantityToDeduct <= 0) break;

                    $deductAmount = min($batch->remaining_quantity, $quantityToDeduct);
                    
                    // Update batch remaining quantity
                    $batch->decrement('remaining_quantity', $deductAmount);
                    
                    // Record stock movement
                    StockMovement::create([
                        'inventory_id' => $inventory->id,
                        'movement_type' => 'out',
                        'quantity' => $deductAmount,
                        'unit_price' => $batch->unit_cost,
                        'reference_type' => StockOutTransaction::class,
                        'reference_id' => $stockOut->id,
                        'remaining_quantity' => $batch->remaining_quantity,
                        'user_id' => auth()->id(),
                        'notes' => "Sale - Order {$orderNumber} - Batch: {$batch->batch_number}"
                    ]);

                    $quantityToDeduct -= $deductAmount;
                }
                
                // Log inventory deduction for order
                AuditLogService::log(
                    'stock_out',
                    $inventory,
                    "Stock decreased by {$item['quantity']} {$item['unit']} for Order {$orderNumber}. Order status: {$validated['status']}",
                    ['quantity' => $oldQuantity],
                    ['quantity' => $newQuantity]
                );
                
                Log::info("Stock decreased for product {$inventory->product_name}: -{$item['quantity']} {$item['unit']}. New quantity: {$inventory->quantity}. Order status: {$validated['status']}");
            }

            DB::commit();

            // Log successful order creation with full details
            AuditLogService::log(
                'created',
                $order,
                "Order {$orderNumber} created by " . auth()->user()->name . 
                " - Customer: " . ($order->customer_name ?? 'Walk-in') . 
                " - Total: ₱{$total} - Status: {$order->status}",
                null,
                array_merge($order->toArray(), ['items' => $itemsDetail])
            );

            // Log order creation
            Log::info("Order created successfully: {$order->order_number}, Total: ₱{$total}, Status: {$order->status}");

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => $order->status === 'completed' ? 'Order completed successfully!' : 'Order saved as pending!',
                'data' => [
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                    'amount_paid' => $validated['amount_paid'],
                    'change' => $validated['change'],
                    'items_count' => count($validated['items']),
                    'status' => $order->status
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Order validation failed: ' . $e->getMessage());
            
            // Log validation failure
            AuditLogService::log(
                'validation_failed',
                null,
                'Order validation failed: ' . implode(', ', array_flatten($e->errors())),
                null,
                ['errors' => $e->errors()]
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            
            // Log order creation failure
            AuditLogService::log(
                'creation_failed',
                null,
                'Order creation failed: ' . $e->getMessage(),
                null,
                ['error' => $e->getMessage()]
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search for customers with pending orders
     */
    public function searchCustomers(Request $request)
    {
        try {
            $searchTerm = $request->input('search', '');

            if (strlen($searchTerm) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term must be at least 2 characters',
                    'customers' => []
                ]);
            }

            // Get distinct customers from orders where customer_type is 'regular'
            $customers = Order::where('customer_type', 'regular')
                ->where(function($query) use ($searchTerm) {
                    $query->where('customer_name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('customer_phone', 'LIKE', "%{$searchTerm}%");
                })
                ->whereNotNull('customer_name')
                ->whereNotNull('customer_phone')
                ->select('customer_name', 'customer_phone', 'customer_address')
                ->selectRaw('COUNT(*) as order_count')
                ->groupBy('customer_name', 'customer_phone', 'customer_address')
                ->orderBy('order_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function($customer) {
                    // Check if customer has pending or partial orders
                    $hasPending = Order::where('customer_name', $customer->customer_name)
                        ->where('customer_phone', $customer->customer_phone)
                        ->whereIn('status', ['pending', 'partial'])
                        ->exists();
                    
                    return [
                        'id' => md5($customer->customer_name . $customer->customer_phone), // Generate unique ID
                        'name' => $customer->customer_name,
                        'phone' => $customer->customer_phone,
                        'address' => $customer->customer_address,
                        'order_count' => $customer->order_count,
                        'has_pending' => $hasPending
                    ];
                });

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search customers',
                'customers' => []
            ], 500);
        }
    }

    /**
     * Enhanced customer search with better filtering
     */
    public function searchCustomersEnhanced(Request $request)
    {
        try {
            $searchTerm = $request->input('search', '');

            if (strlen($searchTerm) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term must be at least 2 characters',
                    'customers' => []
                ]);
            }

            // Get customers with their latest order and order count
            $customers = Order::where('customer_type', 'regular')
                ->where(function($query) use ($searchTerm) {
                    $query->where('customer_name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('customer_phone', 'LIKE', "%{$searchTerm}%");
                })
                ->whereNotNull('customer_name')
                ->whereNotNull('customer_phone')
                ->select(
                    'customer_name',
                    'customer_phone',
                    'customer_address',
                    DB::raw('COUNT(*) as order_count'),
                    DB::raw('MAX(created_at) as last_order_date'),
                    DB::raw('SUM(total) as total_spent')
                )
                ->groupBy('customer_name', 'customer_phone', 'customer_address')
                ->orderBy('last_order_date', 'desc')
                ->limit(15)
                ->get()
                ->map(function($customer) {
                     // Get pending/partial orders count
                     $pendingCount = Order::where('customer_name', $customer->customer_name)
                         ->where('customer_phone', $customer->customer_phone)
                         ->whereIn('status', ['pending', 'partial'])
                         ->count();
                     
                     // Get latest pending order if exists
                     $latestPendingOrder = Order::where('customer_name', $customer->customer_name)
                         ->where('customer_phone', $customer->customer_phone)
                         ->whereIn('status', ['pending', 'partial'])
                         ->orderBy('created_at', 'desc')
                         ->first();
                     
                     // Check if customer has unpaid pending/partial order(s)
                     $hasUnpaidOrder = Order::customerHasUnpaidOrder($customer->customer_name, $customer->customer_phone);
                     
                     return [
                         'id' => md5($customer->customer_name . $customer->customer_phone),
                         'name' => $customer->customer_name,
                         'phone' => $customer->customer_phone,
                         'address' => $customer->customer_address,
                         'order_count' => $customer->order_count,
                         'total_spent' => (float) $customer->total_spent,
                         'last_order_date' => $customer->last_order_date ? Carbon::parse($customer->last_order_date)->format('M d, Y') : null,
                         'has_pending' => $pendingCount > 0,
                         'pending_count' => $pendingCount,
                         'has_unpaid_order' => $hasUnpaidOrder,
                         'can_create_order' => !$hasUnpaidOrder,
                         'latest_pending_order' => $latestPendingOrder ? [
                             'id' => $latestPendingOrder->id,
                             'order_number' => $latestPendingOrder->order_number,
                             'total' => (float) $latestPendingOrder->total,
                             'amount_paid' => (float) $latestPendingOrder->amount_paid,
                             'remaining_balance' => (float) ($latestPendingOrder->total - $latestPendingOrder->amount_paid),
                             'status' => $latestPendingOrder->status,
                             'created_at' => $latestPendingOrder->created_at->format('M d, Y h:i A')
                         ] : null
                     ];
                 });

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);

        } catch (\Exception $e) {
            Log::error('Error in enhanced customer search: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search customers',
                'customers' => []
            ], 500);
        }
    }

    /**
     * Get customer's pending and partial orders
     */
    public function getCustomerPendingOrders(Request $request)
    {
        try {
            $customerName = $request->input('name', '');
            $customerPhone = $request->input('phone', '');

            if (empty($customerName) || empty($customerPhone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer name and phone are required',
                    'orders' => []
                ]);
            }

            $orders = Order::with(['items'])
                ->where('customer_name', $customerName)
                ->where('customer_phone', $customerPhone)
                ->whereIn('status', ['pending', 'partial'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'total' => (float) $order->total,
                        'amount_paid' => (float) $order->amount_paid,
                        'remaining_balance' => (float) ($order->total - $order->amount_paid),
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('M d, Y h:i A'),
                        'items_count' => $order->items->count(),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product_name,
                                'quantity' => (float) $item->quantity,
                                'unit' => $item->unit,
                                'price' => (float) $item->price,
                                'total' => (float) $item->total
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'orders' => $orders,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'total_pending_orders' => $orders->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading customer pending orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pending orders',
                'orders' => []
            ], 500);
        }
    }

    /**
     * Add items to existing order
     */
    public function addItemsToExistingOrder($id, Request $request)
    {
        DB::beginTransaction();
        
        try {
            $order = Order::with(['items', 'user'])->findOrFail($id);
            
            if ($order->status === 'completed') {
                throw new \Exception('Cannot add items to completed order.');
            }

            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_address' => 'nullable|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.inventory_id' => 'required|exists:inventory,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.price' => 'required|numeric|min:0.01',
                'items.*.unit' => 'required|string|max:50',
                'notes' => 'nullable|string|max:500',
                'link_to_existing_order' => 'required|boolean',
                'existing_order_id' => 'required|exists:orders,id'
            ]);

            // Verify customer matches order customer
            if ($order->customer_name !== $request->customer_name || $order->customer_phone !== $request->customer_phone) {
                throw new \Exception('Customer information does not match the existing order.');
            }

            // Validate stock availability
            foreach ($request->items as $item) {
                $inventory = Inventory::find($item['inventory_id']);
                if ($inventory->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$inventory->product_name}");
                }
            }

            $additionalSubtotal = 0;
            $addedItems = [];
            
            foreach ($request->items as $item) {
                $inventory = Inventory::find($item['inventory_id']);
                $itemTotal = $item['price'] * $item['quantity'];
                $additionalSubtotal += $itemTotal;

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'inventory_id' => $item['inventory_id'],
                    'product_name' => $inventory->product_name,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'total' => $itemTotal
                ]);

                // Update inventory
                $oldQuantity = $inventory->quantity;
                $inventory->decrement('quantity', $item['quantity']);
                $newQuantity = $inventory->fresh()->quantity;
                
                // Log inventory deduction
                AuditLogService::log(
                    'stock_out',
                    $inventory,
                    "Stock decreased by {$item['quantity']} {$item['unit']} for additional items in Order {$order->order_number}",
                    ['quantity' => $oldQuantity],
                    ['quantity' => $newQuantity]
                );
                
                $addedItems[] = [
                    'product' => $inventory->product_name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            // Update order totals
            $additionalTax = $additionalSubtotal * 0.12;
            $additionalTotal = $additionalSubtotal + $additionalTax;

            $oldTotal = $order->total;
            $order->update([
                'subtotal' => $order->subtotal + $additionalSubtotal,
                'tax' => $order->tax + $additionalTax,
                'total' => $order->total + $additionalTotal,
                'notes' => $order->notes . ($request->notes ? "\nAdditional items: " . $request->notes : ''),
                'user_id' => auth()->id()
            ]);
            
            $newTotal = $order->fresh()->total;

            DB::commit();

            // Log items addition to order
            $itemsSummary = implode(', ', array_map(function($item) {
                return "{$item['product']} ({$item['quantity']} × ₱{$item['price']})";
            }, $addedItems));
            
            AuditLogService::log(
                'updated',
                $order,
                "Items added to Order {$order->order_number} by " . auth()->user()->name . 
                " - Items: {$itemsSummary} - Additional total: ₱{$additionalTotal}",
                ['total' => $oldTotal, 'items_count' => $order->items->count() - count($addedItems)],
                ['total' => $newTotal, 'items_count' => $order->items->count()]
            );

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => 'Items added to existing order successfully!',
                'data' => [
                    'added_items_count' => count($addedItems),
                    'additional_total' => $additionalTotal,
                    'new_total' => $newTotal,
                    'order_status' => $order->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding items to order: ' . $e->getMessage());
            
            // Log addition failure
            AuditLogService::log(
                'addition_failed',
                null,
                "Failed to add items to order #{$id}: " . $e->getMessage()
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        try {
            $order = Order::with(['items', 'user'])->findOrFail($id);
            
            // Log order view
            AuditLogService::log(
                'viewed',
                $order,
                "Order {$order->order_number} viewed by " . auth()->user()->name
            );
            
            return view('orders.show', compact('order'));
        } catch (\Exception $e) {
            Log::error('Error showing order: ' . $e->getMessage());
            
            // Log error
            AuditLogService::log(
                'error',
                null,
                "Failed to view order #{$id}: " . $e->getMessage()
            );
            
            abort(404, 'Order not found.');
        }
    }

    /**
     * Generate PDF receipt
     */
    public function generatePdfReceipt($id)
    {
        try {
            $order = Order::with(['items' => function($query) {
                $query->orderBy('id');
            }, 'user'])->findOrFail($id);

            // Log PDF generation
            AuditLogService::log(
                'pdf_generated',
                $order,
                "PDF receipt generated for Order {$order->order_number} by " . auth()->user()->name
            );

            $pdf = PDF::loadView('orders.pdf-receipt', compact('order'));
            return $pdf->download("receipt-{$order->order_number}.pdf");

        } catch (\Exception $e) {
            Log::error('Error generating PDF receipt: ' . $e->getMessage());
            
            // Log error
            AuditLogService::log(
                'pdf_error',
                null,
                "Failed to generate PDF for order #{$id}: " . $e->getMessage()
            );
            
            abort(404, 'Order not found.');
        }
    }

    /**
     * Complete a pending order
     */
    public function completeOrder($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::with(['items.inventory', 'user'])->findOrFail($id);

            if ($order->status === 'completed') {
                throw new \Exception('Order is already completed.');
            }

            // Since inventory is already deducted for pending orders, 
            // we only need to update the order status
            $oldStatus = $order->status;
            $order->update([
                'status' => 'completed',
                'user_id' => auth()->id()
            ]);

            DB::commit();

            // Log order completion
            AuditLogService::log(
                'completed',
                $order,
                "Order {$order->order_number} completed by " . auth()->user()->name,
                ['status' => $oldStatus],
                ['status' => 'completed']
            );

            Log::info("Order completed: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            
            // Log completion failure
            AuditLogService::log(
                'completion_failed',
                null,
                "Failed to complete order #{$id}: " . $e->getMessage()
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending orders
     */
    public function getPendingOrders()
    {
        try {
            $orders = Order::with(['items', 'user'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching pending orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pending orders'
            ], 500);
        }
    }

    /**
     * Delete a pending order
     */
    public function deletePendingOrder($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::with(['items.inventory', 'user'])->findOrFail($id);

            if ($order->status !== 'pending') {
                throw new \Exception('Only pending orders can be deleted.');
            }

            $orderDetails = $order->toArray();
            $orderNumber = $order->order_number;
            $itemsCount = $order->items->count();

            // Restore inventory stock before deleting
            foreach ($order->items as $item) {
                if ($item->inventory) {
                    $oldQuantity = $item->inventory->quantity;
                    $item->inventory->increment('quantity', $item->quantity);
                    $newQuantity = $item->inventory->fresh()->quantity;
                    
                    // Log stock restoration
                    AuditLogService::log(
                        'stock_in',
                        $item->inventory,
                        "Stock restored by {$item->quantity} {$item->unit} due to deletion of Order {$orderNumber}",
                        ['quantity' => $oldQuantity],
                        ['quantity' => $newQuantity]
                    );
                    
                    Log::info("Stock restored for product {$item->inventory->product_name}: +{$item->quantity} {$item->unit}. New quantity: {$item->inventory->quantity}");
                }
            }

            // Delete order items and order
            $order->items()->delete();
            $order->delete();

            DB::commit();

            // Log order deletion
            AuditLogService::log(
                'deleted',
                null,
                "Pending Order {$orderNumber} deleted by " . auth()->user()->name . 
                " - Items restored: {$itemsCount}",
                $orderDetails,
                null
            );

            Log::info("Pending order deleted: {$order->order_number}");

            return response()->json([
                'success' => true,
                'message' => 'Pending order deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pending order: ' . $e->getMessage());
            
            // Log deletion failure
            AuditLogService::log(
                'deletion_failed',
                null,
                "Failed to delete pending order #{$id}: " . $e->getMessage()
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pending order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add items to existing order (legacy method)
     */
    public function addItemsToOrder($id, Request $request)
    {
        return $this->addItemsToExistingOrder($id, $request);
    }

    /**
     * Get order details
     */
    public function getOrderDetails($id)
    {
        try {
            $order = Order::with(['items.inventory', 'user', 'payments'])->findOrFail($id);
            
            // Log order details view
            AuditLogService::log(
                'viewed',
                $order,
                "Order details retrieved for Order {$order->order_number} by " . auth()->user()->name
            );
            
            // Add computed fields for frontend compatibility
            $orderArray = $order->toArray();
            $orderArray['remaining_balance'] = $order->remaining_balance;
            $orderArray['total_amount'] = $order->total;
            return response()->json([
                'success' => true,
                'order' => $orderArray
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting order details: ' . $e->getMessage());
            
            // Log error
            AuditLogService::log(
                'error',
                null,
                "Failed to get order details #{$id}: " . $e->getMessage()
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $order = Order::with(['items.inventory', 'user'])->findOrFail($id);

            if ($order->status === 'completed') {
                throw new \Exception('Cannot cancel completed order.');
            }

            if ($order->status === 'cancelled') {
                throw new \Exception('Order is already cancelled.');
            }

            $oldStatus = $order->status;
            $orderNumber = $order->order_number;
            $itemsCount = $order->items->count();
            $reason = $request->reason ?? 'No reason provided';

            // Restore inventory
            foreach ($order->items as $item) {
                if ($item->inventory) {
                    $oldQuantity = $item->inventory->quantity;
                    $item->inventory->increment('quantity', $item->quantity);
                    $newQuantity = $item->inventory->fresh()->quantity;
                    
                    // Log stock restoration due to cancellation
                    AuditLogService::log(
                        'stock_in',
                        $item->inventory,
                        "Stock restored by {$item->quantity} {$item['unit']} due to cancellation of Order {$orderNumber}. Reason: {$reason}",
                        ['quantity' => $oldQuantity],
                        ['quantity' => $newQuantity]
                    );
                }
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'notes' => $order->notes . ($reason ? "\nCancelled: " . $reason : ''),
                'user_id' => auth()->id()
            ]);

            DB::commit();

            // Log order cancellation
            AuditLogService::log(
                'cancelled',
                $order,
                "Order {$orderNumber} cancelled by " . auth()->user()->name . 
                " - Reason: {$reason} - Items restored: {$itemsCount}",
                ['status' => $oldStatus],
                ['status' => 'cancelled']
            );

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            
            // Log cancellation failure
            AuditLogService::log(
                'cancellation_failed',
                null,
                "Failed to cancel order #{$id}: " . $e->getMessage()
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all orders
     */
    public function listOrders(Request $request)
    {
        try {
            $status = $request->get('status');
            $date = $request->get('date');
            
            $query = Order::with(['items', 'user'])->latest();

            if ($status) {
                $query->where('status', $status);
            }

            if ($date) {
                $query->whereDate('created_at', $date);
            }

            $orders = $query->paginate(20);

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error listing orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders'
            ], 500);
        }
    }

    /**
     * Search orders
     */
    public function searchOrders(Request $request)
    {
        try {
            $search = $request->get('search');
            
            // Log search activity
            AuditLogService::log(
                'searched',
                null,
                "Order search performed by " . auth()->user()->name . 
                " - Search term: '{$search}'"
            );
            
            $orders = Order::with(['items', 'user'])
                ->where('order_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%")
                ->latest()
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search orders'
            ], 500);
        }
    }

    /**
     * Get order statistics
     */
    public function getOrderStats()
    {
        try {
            $today = Carbon::today();
            $weekStart = Carbon::now()->startOfWeek();
            $monthStart = Carbon::now()->startOfMonth();

            $stats = [
                'today' => [
                    'total_orders' => Order::whereDate('created_at', $today)->count(),
                    'total_sales' => Order::whereDate('created_at', $today)->sum('total'),
                    'completed_orders' => Order::whereDate('created_at', $today)->where('status', 'completed')->count(),
                    'pending_orders' => Order::whereDate('created_at', $today)->where('status', 'pending')->count(),
                ],
                'week' => [
                    'total_orders' => Order::where('created_at', '>=', $weekStart)->count(),
                    'total_sales' => Order::where('created_at', '>=', $weekStart)->sum('total'),
                    'completed_orders' => Order::where('created_at', '>=', $weekStart)->where('status', 'completed')->count(),
                    'pending_orders' => Order::where('created_at', '>=', $weekStart)->where('status', 'pending')->count(),
                ],
                'month' => [
                    'total_orders' => Order::where('created_at', '>=', $monthStart)->count(),
                    'total_sales' => Order::where('created_at', '>=', $monthStart)->sum('total'),
                    'completed_orders' => Order::where('created_at', '>=', $monthStart)->where('status', 'completed')->count(),
                    'pending_orders' => Order::where('created_at', '>=', $monthStart)->where('status', 'pending')->count(),
                ],
                'pending_orders' => Order::where('status', 'pending')->count(),
                'partial_orders' => Order::where('status', 'partial')->count(),
                'cancelled_orders' => Order::where('status', 'cancelled')->count(),
                'total_revenue' => Order::where('status', 'completed')->sum('total'),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting order stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Print receipt for an order
     */
    public function printReceipt($id)
    {
        try {
            $order = Order::with(['items' => function($query) {
                $query->orderBy('id');
            }, 'user'])->findOrFail($id);

            // Log receipt printing
            AuditLogService::log(
                'receipt_printed',
                $order,
                "Receipt printed for Order {$order->order_number} by " . auth()->user()->name
            );

            return view('orders.print-receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Error printing receipt: ' . $e->getMessage());
            
            // Log error
            AuditLogService::log(
                'print_error',
                null,
                "Failed to print receipt for order #{$id}: " . $e->getMessage()
            );
            
            abort(404, 'Order not found.');
        }
    }

    /**
     * Generate sales report PDF
     */
    public function generateSalesReport()
    {
        try {
            $startDate = request('start_date', Carbon::now()->startOfMonth());
            $endDate = request('end_date', Carbon::now()->endOfMonth());

            $orders = Order::with(['items', 'user'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalSales = $orders->sum('total');
            $totalOrders = $orders->count();

            // Log report generation
            AuditLogService::log(
                'report_generated',
                null,
                "Sales report generated by " . auth()->user()->name . 
                " - Period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}" .
                " - Total sales: ₱{$totalSales} - Orders: {$totalOrders}"
            );

            $pdf = PDF::loadView('reports.sales-pdf', compact('orders', 'totalSales', 'totalOrders', 'startDate', 'endDate'));
            return $pdf->download("sales-report-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.pdf");

        } catch (\Exception $e) {
            Log::error('Error generating sales report: ' . $e->getMessage());
            
            // Log error
            AuditLogService::log(
                'report_error',
                null,
                "Failed to generate sales report: " . $e->getMessage()
            );
            
            return back()->with('error', 'Failed to generate sales report.');
        }
    }

    /**
     * Generate inventory report PDF
     */
    public function generateInventoryReport()
    {
        try {
            $inventory = Inventory::with('supplier')
                ->orderBy('product_name')
                ->get();

            // Log report generation
            AuditLogService::log(
                'report_generated',
                null,
                "Inventory report generated by " . auth()->user()->name
            );

            $pdf = PDF::loadView('reports.inventory-pdf', compact('inventory'));
            return $pdf->download("inventory-report-" . Carbon::now()->format('Y-m-d') . ".pdf");

        } catch (\Exception $e) {
            Log::error('Error generating inventory report: ' . $e->getMessage());
            
            // Log error
            AuditLogService::log(
                'report_error',
                null,
                "Failed to generate inventory report: " . $e->getMessage()
            );
            
            return back()->with('error', 'Failed to generate inventory report.');
        }
    }

    /**
     * Generate daily sales summary PDF
     */
    public function generateDailySalesSummary()
    {
        try {
            $date = request('date', Carbon::today());
            
            $orders = Order::with('items')
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalSales = $orders->sum('total');
            $totalOrders = $orders->count();

            // Log report generation
            AuditLogService::log(
                'report_generated',
                null,
                "Daily sales summary generated by " . auth()->user()->name . " for date: {$date}"
            );

            $pdf = PDF::loadView('reports.daily-sales-pdf', compact('orders', 'totalSales', 'totalOrders', 'date'));
            return $pdf->download("daily-sales-{$date}.pdf");

        } catch (\Exception $e) {
            Log::error('Error generating daily sales summary: ' . $e->getMessage());
            
            // Log error
            AuditLogService::log(
                'report_error',
                null,
                "Failed to generate daily sales summary: " . $e->getMessage()
            );
            
            return back()->with('error', 'Failed to generate daily sales summary.');
        }
    }

    /**
     * Export orders to CSV
     */
    public function exportOrdersToCsv()
    {
        try {
            $startDate = request('start_date', Carbon::now()->startOfMonth());
            $endDate = request('end_date', Carbon::now()->endOfMonth());

            $orders = Order::with(['items', 'user'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            $fileName = "orders-export-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.csv";

            // Log export activity
            AuditLogService::log(
                'exported',
                null,
                "Orders exported to CSV by " . auth()->user()->name . 
                " - Period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}" .
                " - Orders: {$orders->count()}"
            );

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ];

            $callback = function() use ($orders) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'Order Number',
                    'Customer Name',
                    'Customer Phone',
                    'Status',
                    'Subtotal',
                    'Tax',
                    'Total',
                    'Amount Paid',
                    'Change',
                    'Payment Method',
                    'Notes',
                    'Created By',
                    'Created At'
                ]);

                // Add order data
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->order_number,
                        $order->customer_name,
                        $order->customer_phone,
                        $order->status,
                        $order->subtotal,
                        $order->tax,
                        $order->total,
                        $order->amount_paid,
                        $order->change,
                        $order->payment_method,
                        $order->notes,
                        $order->user ? $order->user->name : 'N/A',
                        $order->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting orders to CSV: ' . $e->getMessage());
            
            // Log export error
            AuditLogService::log(
                'export_error',
                null,
                "Failed to export orders to CSV: " . $e->getMessage()
            );
            
            return back()->with('error', 'Failed to export orders.');
        }
    }

    /**
     * Get order audit trail
     */
    public function getOrderAuditTrail($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            $auditLogs = AuditLog::where(function($query) use ($order) {
                $query->where('model_type', Order::class)
                    ->where('model_id', $order->id);
            })
            ->orWhere('description', 'like', "%{$order->order_number}%")
            ->orWhere('description', 'like', "%Order {$order->order_number}%")
            ->with('user')
            ->latest()
            ->get();

            return response()->json([
                'success' => true,
                'order' => $order,
                'audit_logs' => $auditLogs
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting order audit trail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load audit trail'
            ], 500);
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $lastOrder = Order::where('order_number', 'like', "ORD-{$date}-%")->latest()->first();
        
        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -3) + 1 : 1;
        
        return "ORD-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}