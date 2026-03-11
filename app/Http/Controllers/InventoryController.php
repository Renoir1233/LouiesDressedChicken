<?php
// app/Http\Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\StockInTransaction;
use App\Models\StockOutTransaction;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::with('supplier')
            ->latest()
            ->paginate(15);

        // Get alerts for dashboard
        $lowStockCount = Inventory::where('quantity', '>', 0)
            ->whereRaw('quantity <= low_stock_alert')
            ->where('is_active', true)
            ->count();
            
        $outOfStockCount = Inventory::where('quantity', 0)
            ->where('is_active', true)
            ->count();

        // Get suppliers and categories for modals
        $suppliers = Supplier::where('is_active', true)->get();
        $categories = Inventory::CATEGORIES;

        return view('inventory.index', compact('inventory', 'lowStockCount', 'outOfStockCount', 'suppliers', 'categories'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $categories = Inventory::CATEGORIES;
        return view('inventory.create', compact('suppliers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'nullable|string|unique:inventory,product_code',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'supplier_id' => 'required|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0.01',
            'selling_price' => 'required|numeric|min:0.01|gt:cost_price',
            'low_stock_alert' => 'required|integer|min:0',
            'category' => 'required|in:' . implode(',', Inventory::CATEGORIES),
            'unit' => 'required|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $productCode = $request->product_code;
        if (empty($productCode)) {
            $productCode = 'PROD-' . strtoupper(uniqid());
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('inventory', 'public');
        }

        $inventory = Inventory::create([
            'product_name' => $request->product_name,
            'product_code' => $productCode,
            'description' => $request->description,
            'image' => $imagePath,
            'supplier_id' => $request->supplier_id,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'quantity' => 0, // Always start with 0 quantity
            'low_stock_alert' => $request->low_stock_alert,
            'category' => $request->category,
            'unit' => $request->unit,
            'is_active' => 1 // New products are active by default
        ]);

        return redirect()->route('inventory.index')
            ->with('success', 'Product added to inventory successfully. Use Stock In to add initial stock.');
    }

    public function show(Inventory $inventory)
    {
        $inventory->load('supplier', 'stockMovements.user', 'stockMovements.reference');
        
        // Get recent stock in transactions
        $recentStockIns = $inventory->stockInTransactions()
            ->with('supplier', 'receivedBy')
            ->latest()
            ->limit(10)
            ->get();
            
        // Get recent stock out transactions
        $recentStockOuts = $inventory->stockOutTransactions()
            ->with('handledBy')
            ->latest()
            ->limit(10)
            ->get();
            
        // Get available batches for FIFO
        $availableBatches = $inventory->getAvailableBatches();

        return view('inventory.show', compact('inventory', 'recentStockIns', 'recentStockOuts', 'availableBatches'));
    }

    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $categories = Inventory::CATEGORIES;
        return view('inventory.edit', compact('inventory', 'suppliers', 'categories'));
    }

    public function getProduct($id)
    {
        $product = Inventory::with('supplier')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|unique:inventory,product_code,' . $inventory->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'supplier_id' => 'required|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0.01',
            'selling_price' => 'required|numeric|min:0.01|gt:cost_price',
            'low_stock_alert' => 'required|integer|min:0',
            'category' => 'required|in:' . implode(',', Inventory::CATEGORIES),
            'unit' => 'required|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $data = [
            'product_name' => $request->product_name,
            'product_code' => $request->product_code,
            'description' => $request->description,
            'supplier_id' => $request->supplier_id,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'low_stock_alert' => $request->low_stock_alert,
            'category' => $request->category,
            'unit' => $request->unit,
            'is_active' => $request->filled('is_active') && $request->input('is_active') == 1 ? 1 : 0
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }

        $inventory->update($data);

        return redirect()->route('inventory.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        // Delete image if exists
        if ($inventory->image) {
            Storage::disk('public')->delete($inventory->image);
        }
        
        $inventory->delete();
        return redirect()->route('inventory.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Inventory $inventory)
    {
        $inventory->update([
            'is_active' => !$inventory->is_active
        ]);

        $status = $inventory->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Product {$status} successfully.");
    }

    public function stockIn(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_cost' => 'required|numeric|min:0.01',
            'date_received' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:date_received',
            'batch_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $stockIn = StockInTransaction::create([
                'reference_number' => StockInTransaction::generateReferenceNumber(),
                'inventory_id' => $inventory->id,
                'supplier_id' => $request->supplier_id,
                'quantity_received' => $request->quantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $request->quantity * $request->unit_cost,
                'date_received' => $request->date_received,
                'expiry_date' => $request->expiry_date,
                'batch_number' => $request->batch_number,
                'received_by' => auth()->id(),
                'notes' => $request->notes,
                'remaining_quantity' => $request->quantity
            ]);

            // Update inventory quantity
            $inventory->increment('quantity', $request->quantity);

            // Record stock movement
            StockMovement::create([
                'inventory_id' => $inventory->id,
                'movement_type' => 'in',
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_cost,
                'reference_type' => 'stock_in_transaction',
                'reference_id' => $stockIn->id,
                'remaining_quantity' => $request->quantity,
                'user_id' => auth()->id(),
                'notes' => $request->notes
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', "Stock increased by {$request->quantity} units. Reference: {$stockIn->reference_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock in error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process stock in. Please try again.');
        }
    }

    public function stockOut(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $inventory->quantity,
            'reason' => 'required|in:sale,damage,expiration,return,internal_use,sample,wastage,adjustment',
            'date_removed' => 'required|date',
            'unit_price' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Check if we have enough stock using FIFO
            $availableStock = $inventory->getAvailableBatches()->sum('remaining_quantity');
            if ($availableStock < $request->quantity) {
                return redirect()->back()->with('error', "Insufficient stock. Available: {$availableStock}, Requested: {$request->quantity}");
            }

            // Create stock out transaction
            $stockOut = StockOutTransaction::create([
                'reference_number' => StockOutTransaction::generateReferenceNumber(),
                'inventory_id' => $inventory->id,
                'quantity_removed' => $request->quantity,
                'unit_price' => $request->unit_price,
                'total_value' => $request->quantity * $request->unit_price,
                'reason' => $request->reason,
                'date_removed' => $request->date_removed,
                'handled_by' => auth()->id(),
                'notes' => $request->notes
            ]);

            // Process FIFO allocation
            $quantityToRemove = $request->quantity;
            $batches = $inventory->getAvailableBatches();

            foreach ($batches as $batch) {
                if ($quantityToRemove <= 0) break;

                $removeFromBatch = min($batch->remaining_quantity, $quantityToRemove);
                
                // Update batch remaining quantity
                $batch->decrement('remaining_quantity', $removeFromBatch);
                
                // Record stock movement for this batch
                StockMovement::create([
                    'inventory_id' => $inventory->id,
                    'movement_type' => 'out',
                    'quantity' => $removeFromBatch,
                    'unit_price' => $batch->unit_cost,
                    'reference_type' => 'stock_out_transaction',
                    'reference_id' => $stockOut->id,
                    'remaining_quantity' => $batch->remaining_quantity - $removeFromBatch,
                    'user_id' => auth()->id(),
                    'notes' => "From batch {$batch->batch_number} - {$request->notes}"
                ]);

                $quantityToRemove -= $removeFromBatch;
            }

            // Update inventory quantity
            $inventory->decrement('quantity', $request->quantity);

            DB::commit();

            return redirect()->back()
                ->with('success', "Stock decreased by {$request->quantity} units. Reference: {$stockOut->reference_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock out error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process stock out. Please try again.');
        }
    }

    public function lowStock()
    {
        $lowStockItems = Inventory::with('supplier')
            ->where('quantity', '>', 0)
            ->whereRaw('quantity <= low_stock_alert')
            ->where('is_active', true)
            ->latest()
            ->paginate(15);

        return view('inventory.low-stock', compact('lowStockItems'));
    }

    public function outOfStock()
    {
        $outOfStockItems = Inventory::with('supplier')
            ->where('quantity', 0)
            ->where('is_active', true)
            ->latest()
            ->paginate(15);

        return view('inventory.out-of-stock', compact('outOfStockItems'));
    }

    public function stockInIndex(Request $request)
    {
        $query = StockInTransaction::with(['inventory', 'supplier', 'receivedBy'])
            ->latest();

        // Search and filters
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('inventory', function($q2) use ($search) {
                      $q2->where('product_name', 'like', "%{$search}%")
                         ->orWhere('product_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date_received', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date_received', '<=', $request->date_to);
        }

        if ($request->has('supplier_id') && $request->supplier_id != '') {
            $query->where('supplier_id', $request->supplier_id);
        }

        $stockIns = $query->paginate(20);
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Inventory::where('is_active', true)->get();

        return view('inventory.stock-in.index', compact('stockIns', 'suppliers', 'products'));
    }

    public function stockOutIndex(Request $request)
    {
        $query = StockOutTransaction::with(['inventory', 'handledBy'])
            ->latest();

        // Search and filters
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('inventory', function($q2) use ($search) {
                      $q2->where('product_name', 'like', "%{$search}%")
                         ->orWhere('product_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date_removed', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date_removed', '<=', $request->date_to);
        }

        if ($request->has('reason') && $request->reason != '') {
            $query->where('reason', $request->reason);
        }

        $stockOuts = $query->paginate(20);
        $products = Inventory::where('is_active', true)->get();
        $reasons = [
            'sale' => 'Sale',
            'damage' => 'Damage',
            'expiration' => 'Expired',
            'return' => 'Return to Supplier',
            'internal_use' => 'Internal Use',
            'sample' => 'Sample',
            'wastage' => 'Wastage',
            'adjustment' => 'Stock Adjustment'
        ];

        return view('inventory.stock-out.index', compact('stockOuts', 'products', 'reasons'));
    }

    public function stockMovementHistory(Request $request)
    {
        // Initialize variables with default values
        $totalIn = 0;
        $totalOut = 0;
        $movements = collect();
        $products = collect();

        try {
            // Base query for stock movements
            $query = StockMovement::with(['inventory', 'user'])
                ->latest();

            // Search and filters
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('inventory', function($q2) use ($search) {
                        $q2->where('product_name', 'like', "%{$search}%")
                           ->orWhere('product_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
                });
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->has('movement_type') && $request->movement_type != '') {
                $query->where('movement_type', $request->movement_type);
            }

            if ($request->has('product_id') && $request->product_id != '') {
                $query->where('inventory_id', $request->product_id);
            }

            // Clone query for calculations before pagination
            $calculationQuery = clone $query;
            
            // Calculate totals - ensure we get numeric values
            $totalIn = (int) ($calculationQuery->where('movement_type', 'in')->sum('quantity') ?? 0);
            $totalOut = (int) ($calculationQuery->where('movement_type', 'out')->sum('quantity') ?? 0);
            
            // Paginate the results
            $movements = $query->paginate(30);
            
            // Get products for filter dropdown
            $products = Inventory::where('is_active', true)
                ->orderBy('product_name', 'asc')
                ->get();

        } catch (\Exception $e) {
            Log::error('Error in stockMovementHistory: ' . $e->getMessage());
            // Return view with empty/default values
            return view('inventory.movement-history', compact(
                'movements', 
                'products', 
                'totalIn', 
                'totalOut'
            ));
        }

        return view('inventory.movement-history', compact(
            'movements', 
            'products', 
            'totalIn', 
            'totalOut'
        ));
    }

    public function exportStockIns(Request $request)
    {
        $query = StockInTransaction::with(['inventory', 'supplier', 'receivedBy'])
            ->latest();

        // Apply filters if any
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date_received', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date_received', '<=', $request->date_to);
        }

        $stockIns = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_in_transactions_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($stockIns) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Reference Number',
                'Date Received',
                'Product Code',
                'Product Name',
                'Supplier',
                'Quantity Received',
                'Unit Cost',
                'Total Cost',
                'Batch Number',
                'Expiry Date',
                'Received By',
                'Notes'
            ]);

            // Data rows
            foreach ($stockIns as $stockIn) {
                fputcsv($file, [
                    $stockIn->reference_number,
                    $stockIn->date_received->format('Y-m-d'),
                    $stockIn->inventory->product_code,
                    $stockIn->inventory->product_name,
                    $stockIn->supplier->name,
                    $stockIn->quantity_received,
                    number_format($stockIn->unit_cost, 2),
                    number_format($stockIn->total_cost, 2),
                    $stockIn->batch_number ?? 'N/A',
                    $stockIn->expiry_date ? $stockIn->expiry_date->format('Y-m-d') : 'N/A',
                    $stockIn->receivedBy->name,
                    $stockIn->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportStockOuts(Request $request)
    {
        $query = StockOutTransaction::with(['inventory', 'handledBy'])
            ->latest();

        // Apply filters if any
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('date_removed', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('date_removed', '<=', $request->date_to);
        }

        $stockOuts = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_out_transactions_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($stockOuts) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Reference Number',
                'Date Removed',
                'Product Code',
                'Product Name',
                'Quantity Removed',
                'Unit Price',
                'Total Value',
                'Reason',
                'Handled By',
                'Notes'
            ]);

            // Data rows
            foreach ($stockOuts as $stockOut) {
                fputcsv($file, [
                    $stockOut->reference_number,
                    $stockOut->date_removed->format('Y-m-d'),
                    $stockOut->inventory->product_code,
                    $stockOut->inventory->product_name,
                    $stockOut->quantity_removed,
                    number_format($stockOut->unit_price, 2),
                    number_format($stockOut->total_value, 2),
                    $stockOut->getReasonLabelAttribute(),
                    $stockOut->handledBy->name,
                    $stockOut->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function expiringSoon(Request $request)
    {
        $query = StockInTransaction::with(['inventory', 'supplier'])
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date', 'asc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('inventory', function($q2) use ($search) {
                      $q2->where('product_name', 'like', "%{$search}%")
                         ->orWhere('product_code', 'like', "%{$search}%");
                  });
            });
        }

        $expiringItems = $query->paginate(20);

        return view('inventory.expiring-soon', compact('expiringItems'));
    }

    public function getFifoBatches(Inventory $inventory)
    {
        $batches = $inventory->stockInTransactions()
            ->where('remaining_quantity', '>', 0)
            ->orderBy('date_received', 'asc')
            ->get()
            ->map(function($batch) {
                return [
                    'id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'remaining_quantity' => $batch->remaining_quantity,
                    'unit_cost' => $batch->unit_cost,
                    'expiry_date' => $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : null,
                    'date_received' => $batch->date_received->format('Y-m-d'),
                    'days_until_expiry' => $batch->expiry_date ? now()->diffInDays($batch->expiry_date, false) : null
                ];
            });

        return response()->json([
            'batches' => $batches,
            'total_available' => $batches->sum('remaining_quantity')
        ]);
    }

    public function showBatch(StockInTransaction $batch)
    {
        $batch->load(['inventory', 'supplier', 'receivedBy', 'stockMovements']);
        return view('inventory.batch-show', compact('batch'));
    }

    public function adjustBatch(Request $request, StockInTransaction $batch)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $oldQuantity = $batch->remaining_quantity;
            $inventory = $batch->inventory;

            switch ($request->adjustment_type) {
                case 'add':
                    $newQuantity = $oldQuantity + $request->quantity;
                    $batch->increment('remaining_quantity', $request->quantity);
                    $inventory->increment('quantity', $request->quantity);
                    break;
                    
                case 'remove':
                    if ($request->quantity > $oldQuantity) {
                        return redirect()->back()->with('error', 'Cannot remove more than available quantity.');
                    }
                    $newQuantity = $oldQuantity - $request->quantity;
                    $batch->decrement('remaining_quantity', $request->quantity);
                    $inventory->decrement('quantity', $request->quantity);
                    break;
                    
                case 'set':
                    $newQuantity = $request->quantity;
                    $difference = $request->quantity - $oldQuantity;
                    $batch->remaining_quantity = $request->quantity;
                    $batch->save();
                    if ($difference > 0) {
                        $inventory->increment('quantity', $difference);
                    } else {
                        $inventory->decrement('quantity', abs($difference));
                    }
                    break;
            }

            // Record adjustment movement
            StockMovement::create([
                'inventory_id' => $inventory->id,
                'movement_type' => $request->adjustment_type == 'add' ? 'in' : 'out',
                'quantity' => abs($newQuantity - $oldQuantity),
                'unit_price' => $batch->unit_cost,
                'reference_type' => 'stock_in_transaction',
                'reference_id' => $batch->id,
                'remaining_quantity' => $newQuantity,
                'user_id' => auth()->id(),
                'notes' => "Batch adjustment: {$request->reason}. " . ($request->notes ?? '')
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', "Batch adjusted successfully. New quantity: {$newQuantity}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch adjustment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to adjust batch. Please try again.');
        }
    }

    public function exportInventory(Request $request)
    {
        $query = Inventory::with('supplier');

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active');
        }

        $inventory = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($inventory) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Product Code',
                'Product Name',
                'Category',
                'Supplier',
                'Cost Price',
                'Selling Price',
                'Quantity',
                'Unit',
                'Low Stock Alert',
                'Stock Value',
                'Status'
            ]);

            // Data rows
            foreach ($inventory as $item) {
                fputcsv($file, [
                    $item->product_code,
                    $item->product_name,
                    $item->category,
                    $item->supplier->name,
                    number_format($item->cost_price, 2),
                    number_format($item->selling_price, 2),
                    $item->quantity,
                    $item->unit,
                    $item->low_stock_alert,
                    number_format($item->quantity * $item->cost_price, 2),
                    $item->is_active ? 'Active' : 'Inactive'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printLowStock()
    {
        $lowStockItems = Inventory::with('supplier')
            ->where('quantity', '>', 0)
            ->whereRaw('quantity <= low_stock_alert')
            ->where('is_active', true)
            ->get();

        return view('inventory.print.low-stock', compact('lowStockItems'));
    }

    public function printExpiring()
    {
        $expiringItems = StockInTransaction::with(['inventory', 'supplier'])
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('inventory.print.expiring', compact('expiringItems'));
    }

    public function getHistory(Inventory $inventory)
    {
        $movements = $inventory->stockMovements()
            ->with(['user', 'reference'])
            ->latest()
            ->paginate(20);
            
        return view('inventory.history', compact('inventory', 'movements'));
    }

    public function exportWithAudit()
    {
        // Implementation for exporting inventory with audit trail
        return response()->download(public_path('exports/inventory-audit.csv'));
    }

    public function dashboardStats()
    {
        $stats = [
            'total_products' => Inventory::count(),
            'active_products' => Inventory::where('is_active', true)->count(),
            'low_stock' => Inventory::where('quantity', '>', 0)
                ->whereRaw('quantity <= low_stock_alert')
                ->where('is_active', true)
                ->count(),
            'out_of_stock' => Inventory::where('quantity', 0)
                ->where('is_active', true)
                ->count(),
            'total_stock_value' => Inventory::sum(DB::raw('quantity * cost_price')),
            'total_potential_sales' => Inventory::sum(DB::raw('quantity * selling_price')),
        ];

        return response()->json($stats);
    }

    public function bulkStockUpdate(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:inventory,id',
            'updates.*.quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->updates as $update) {
                $inventory = Inventory::find($update['id']);
                $oldQuantity = $inventory->quantity;
                $newQuantity = $update['quantity'];
                
                if ($newQuantity != $oldQuantity) {
                    $inventory->update(['quantity' => $newQuantity]);
                    
                    // Record movement
                    StockMovement::create([
                        'inventory_id' => $inventory->id,
                        'movement_type' => $newQuantity > $oldQuantity ? 'in' : 'out',
                        'quantity' => abs($newQuantity - $oldQuantity),
                        'unit_price' => $inventory->cost_price,
                        'reference_type' => 'manual',
                        'reference_id' => 0,
                        'remaining_quantity' => $newQuantity,
                        'user_id' => auth()->id(),
                        'notes' => "Bulk update: {$request->reason}"
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Bulk stock update completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk stock update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update stock. Please try again.');
        }
    }

    public function inventoryReport(Request $request)
    {
        try {
            // Base query for inventory
            $query = Inventory::with('supplier');

            // Filters
            if ($request->has('category') && $request->category != '') {
                $query->where('category', $request->category);
            }

            if ($request->has('supplier_id') && $request->supplier_id != '') {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->has('status') && $request->status != '') {
                $query->where('is_active', $request->status == 'active');
            }

            // Get inventory data
            $inventoryData = $query->get();

            // Calculate statistics
            $stats = [
                'total_products' => $inventoryData->count(),
                'active_products' => $inventoryData->where('is_active', 1)->count(),
                'inactive_products' => $inventoryData->where('is_active', 0)->count(),
                'low_stock_count' => $inventoryData->filter(function($item) {
                    return $item->quantity > 0 && $item->quantity <= $item->low_stock_alert;
                })->count(),
                'out_of_stock_count' => $inventoryData->where('quantity', 0)->count(),
                'in_stock_count' => $inventoryData->filter(function($item) {
                    return $item->quantity > $item->low_stock_alert;
                })->count(),
                'total_stock_value' => $inventoryData->sum(function($item) {
                    return $item->quantity * $item->cost_price;
                }),
                'total_potential_sales' => $inventoryData->sum(function($item) {
                    return $item->quantity * $item->selling_price;
                }),
                'total_potential_profit' => $inventoryData->sum(function($item) {
                    return $item->quantity * ($item->selling_price - $item->cost_price);
                })
            ];

            // Group by category
            $byCategory = $inventoryData->groupBy('category')->map(function($items) {
                return [
                    'count' => $items->count(),
                    'total_value' => $items->sum(function($item) {
                        return $item->quantity * $item->cost_price;
                    }),
                    'total_quantity' => $items->sum('quantity'),
                    'potential_sales' => $items->sum(function($item) {
                        return $item->quantity * $item->selling_price;
                    })
                ];
            });

            // Group by supplier
            $bySupplier = $inventoryData->groupBy('supplier.id')->map(function($items, $supplierId) {
                $supplier = $items->first()->supplier;
                return [
                    'supplier_name' => $supplier->name ?? 'Unknown',
                    'count' => $items->count(),
                    'total_value' => $items->sum(function($item) {
                        return $item->quantity * $item->cost_price;
                    }),
                    'total_quantity' => $items->sum('quantity'),
                    'potential_sales' => $items->sum(function($item) {
                        return $item->quantity * $item->selling_price;
                    })
                ];
            });

            // Get top 10 highest value items
            $topValueItems = $inventoryData->sortByDesc(function($item) {
                return $item->quantity * $item->selling_price;
            })->take(10)->values();

            // Get movement history for the report period
            $stockInsCount = StockInTransaction::whereDate('date_received', '>=', now()->subDays(30))->sum('quantity_received');
            $stockOutsCount = StockOutTransaction::whereDate('date_removed', '>=', now()->subDays(30))->sum('quantity_removed');

            // Get detailed stock in transactions
            $stockInTransactions = StockInTransaction::with(['inventory', 'supplier', 'receivedBy'])
                ->latest()
                ->limit(50)
                ->get();

            // Get detailed stock out transactions
            $stockOutTransactions = StockOutTransaction::with(['inventory', 'handledBy'])
                ->latest()
                ->limit(50)
                ->get();

            // Prepare data for the view
            $categories = Inventory::CATEGORIES;
            $suppliers = Supplier::where('is_active', true)->get();

            return view('inventory.report', compact(
                'inventoryData',
                'stats',
                'byCategory',
                'bySupplier',
                'topValueItems',
                'stockInsCount',
                'stockOutsCount',
                'stockInTransactions',
                'stockOutTransactions',
                'categories',
                'suppliers'
            ));
        } catch (\Exception $e) {
            Log::error('Inventory report error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating inventory report.');
        }
    }

    public function printInventoryReport(Request $request)
    {
        try {
            // Get filtered inventory
            $query = Inventory::with('supplier');

            if ($request->has('category') && $request->category != '') {
                $query->where('category', $request->category);
            }

            if ($request->has('supplier_id') && $request->supplier_id != '') {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->has('status') && $request->status != '') {
                $query->where('is_active', $request->status == 'active');
            }

            $inventoryData = $query->get();

            // Calculate statistics
            $stats = [
                'total_products' => $inventoryData->count(),
                'active_products' => $inventoryData->where('is_active', 1)->count(),
                'inactive_products' => $inventoryData->where('is_active', 0)->count(),
                'low_stock_count' => $inventoryData->filter(function($item) {
                    return $item->quantity > 0 && $item->quantity <= $item->low_stock_alert;
                })->count(),
                'out_of_stock_count' => $inventoryData->where('quantity', 0)->count(),
                'in_stock_count' => $inventoryData->filter(function($item) {
                    return $item->quantity > $item->low_stock_alert;
                })->count(),
                'total_stock_value' => $inventoryData->sum(function($item) {
                    return $item->quantity * $item->cost_price;
                }),
                'total_potential_sales' => $inventoryData->sum(function($item) {
                    return $item->quantity * $item->selling_price;
                }),
                'total_potential_profit' => $inventoryData->sum(function($item) {
                    return $item->quantity * ($item->selling_price - $item->cost_price);
                })
            ];

            // Get movement history
            $stockIns = StockInTransaction::whereDate('date_received', '>=', now()->subDays(30))->sum('quantity_received');
            $stockOuts = StockOutTransaction::whereDate('date_removed', '>=', now()->subDays(30))->sum('quantity_removed');

            return view('inventory.report-print', compact('inventoryData', 'stats', 'stockIns', 'stockOuts'));
        } catch (\Exception $e) {
            Log::error('Print inventory report error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating print report.');
        }
    }

    public function exportInventoryReport(Request $request)
    {
        try {
            // Get filtered inventory
            $query = Inventory::with('supplier');

            if ($request->has('category') && $request->category != '') {
                $query->where('category', $request->category);
            }

            if ($request->has('supplier_id') && $request->supplier_id != '') {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->has('status') && $request->status != '') {
                $query->where('is_active', $request->status == 'active');
            }

            $inventory = $query->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="inventory_report_' . date('Y-m-d_H-i-s') . '.csv"',
            ];

            $callback = function() use ($inventory) {
                $file = fopen('php://output', 'w');
                
                // Header row
                fputcsv($file, [
                    'Product Code',
                    'Product Name',
                    'Category',
                    'Supplier',
                    'Quantity',
                    'Unit',
                    'Cost Price',
                    'Selling Price',
                    'Profit Per Unit',
                    'Stock Value (Cost)',
                    'Stock Value (Sales)',
                    'Potential Profit',
                    'Low Stock Alert',
                    'Stock Status',
                    'Status'
                ]);

                // Data rows
                foreach ($inventory as $item) {
                    $stockStatus = $item->quantity == 0 ? 'Out of Stock' :
                                   ($item->quantity <= $item->low_stock_alert ? 'Low Stock' : 'In Stock');
                    
                    $costValue = $item->quantity * $item->cost_price;
                    $salesValue = $item->quantity * $item->selling_price;
                    $profit = $item->quantity * ($item->selling_price - $item->cost_price);
                    $profitPerUnit = $item->selling_price - $item->cost_price;
                    
                    fputcsv($file, [
                        $item->product_code,
                        $item->product_name,
                        $item->category,
                        $item->supplier->name ?? 'Unknown',
                        $item->quantity,
                        $item->unit,
                        number_format($item->cost_price, 2),
                        number_format($item->selling_price, 2),
                        number_format($profitPerUnit, 2),
                        number_format($costValue, 2),
                        number_format($salesValue, 2),
                        number_format($profit, 2),
                        $item->low_stock_alert,
                        $stockStatus,
                        $item->is_active ? 'Active' : 'Inactive'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Export inventory report error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export report.');
        }
    }
}