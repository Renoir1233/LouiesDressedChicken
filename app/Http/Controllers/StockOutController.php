<?php

namespace App\Http\Controllers;

use App\Models\StockOutTransaction;
use App\Models\Inventory;
use App\Models\StockInTransaction;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockOutController extends Controller
{
    /**
     * Display a listing of stock out transactions.
     */
    public function index(Request $request)
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

    /**
     * Show the form for creating a new stock out transaction.
     */
    public function create()
    {
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
        
        return view('inventory.stock-out.create', compact('products', 'reasons'));
    }

    /**
     * Store a newly created stock out transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|in:sale,damage,expiration,return,internal_use,sample,wastage,adjustment',
            'date_removed' => 'required|date',
            'unit_price' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $inventory = Inventory::findOrFail($request->inventory_id);
            
            // Check if we have enough stock
            if ($inventory->quantity < $request->quantity) {
                return redirect()->back()->with('error', "Insufficient stock. Available: {$inventory->quantity}, Requested: {$request->quantity}");
            }

            // Check if we have enough stock using FIFO
            $availableStock = $inventory->stockInTransactions()
                ->where('remaining_quantity', '>', 0)
                ->sum('remaining_quantity');
                
            if ($availableStock < $request->quantity) {
                return redirect()->back()->with('error', "Insufficient stock in batches. Available: {$availableStock}, Requested: {$request->quantity}");
            }

            // Create stock out transaction
            $stockOut = StockOutTransaction::create([
                'reference_number' => StockOutTransaction::generateReferenceNumber(),
                'inventory_id' => $request->inventory_id,
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
            $batches = $inventory->stockInTransactions()
                ->where('remaining_quantity', '>', 0)
                ->orderBy('date_received', 'asc')
                ->get();

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
                    'reference_type' => StockOutTransaction::class,
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

            return redirect()->route('inventory.stock-out.index')
                ->with('success', "Stock decreased by {$request->quantity} units. Reference: {$stockOut->reference_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock out error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process stock out. Please try again.');
        }
    }

    /**
     * Display the specified stock out transaction.
     */
    public function show(StockOutTransaction $stockOutTransaction)
    {
        $stockOutTransaction->load(['inventory', 'handledBy', 'stockMovements.reference']);
        
        return view('inventory.stock-out.show', compact('stockOutTransaction'));
    }

    /**
     * Show the form for editing the specified stock out transaction.
     */
    public function edit(StockOutTransaction $stockOutTransaction)
    {
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
        
        return view('inventory.stock-out.edit', compact('stockOutTransaction', 'products', 'reasons'));
    }

    /**
     * Update the specified stock out transaction.
     */
    public function update(Request $request, StockOutTransaction $stockOutTransaction)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|in:sale,damage,expiration,return,internal_use,sample,wastage,adjustment',
            'date_removed' => 'required|date',
            'unit_price' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $oldQuantity = $stockOutTransaction->quantity_removed;
            $newQuantity = $request->quantity;
            $quantityDifference = $newQuantity - $oldQuantity;
            
            // If quantity increased, need to check stock availability
            if ($quantityDifference > 0) {
                $inventory = $stockOutTransaction->inventory;
                
                // Check if we have enough stock using FIFO
                $availableStock = $inventory->stockInTransactions()
                    ->where('remaining_quantity', '>', 0)
                    ->sum('remaining_quantity');
                    
                if ($availableStock < $quantityDifference) {
                    return redirect()->back()->with('error', "Insufficient stock to increase quantity. Available: {$availableStock}, Needed: {$quantityDifference}");
                }
                
                // Process additional FIFO allocation
                $quantityToRemove = $quantityDifference;
                $batches = $inventory->stockInTransactions()
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('date_received', 'asc')
                    ->get();

                foreach ($batches as $batch) {
                    if ($quantityToRemove <= 0) break;

                    $removeFromBatch = min($batch->remaining_quantity, $quantityToRemove);
                    
                    // Update batch remaining quantity
                    $batch->decrement('remaining_quantity', $removeFromBatch);
                    
                    // Record additional stock movement
                    StockMovement::create([
                        'inventory_id' => $inventory->id,
                        'movement_type' => 'out',
                        'quantity' => $removeFromBatch,
                        'unit_price' => $batch->unit_cost,
                        'reference_type' => 'stock_out_transaction',
                        'reference_id' => $stockOutTransaction->id,
                        'remaining_quantity' => $batch->remaining_quantity - $removeFromBatch,
                        'user_id' => auth()->id(),
                        'notes' => "Additional from batch {$batch->batch_number} - Transaction adjustment"
                    ]);

                    $quantityToRemove -= $removeFromBatch;
                }
                
                // Update inventory quantity
                $inventory->decrement('quantity', $quantityDifference);
            }
            // If quantity decreased, need to return stock
            elseif ($quantityDifference < 0) {
                $inventory = $stockOutTransaction->inventory;
                
                // Find oldest batch to return stock to (FIFO reversal)
                $batch = $inventory->stockInTransactions()
                    ->orderBy('date_received', 'desc')
                    ->first();
                
                if ($batch) {
                    // Return stock to batch
                    $batch->increment('remaining_quantity', abs($quantityDifference));
                    
                    // Record reversal movement
                    StockMovement::create([
                        'inventory_id' => $inventory->id,
                        'movement_type' => 'in',
                        'quantity' => abs($quantityDifference),
                        'unit_price' => $batch->unit_cost,
                        'reference_type' => 'stock_out_transaction',
                        'reference_id' => $stockOutTransaction->id,
                        'remaining_quantity' => $batch->remaining_quantity + abs($quantityDifference),
                        'user_id' => auth()->id(),
                        'notes' => 'Stock returned - Transaction adjustment'
                    ]);
                }
                
                // Update inventory quantity
                $inventory->increment('quantity', abs($quantityDifference));
            }
            
            // Update stock out transaction
            $stockOutTransaction->update([
                'quantity_removed' => $newQuantity,
                'unit_price' => $request->unit_price,
                'total_value' => $newQuantity * $request->unit_price,
                'reason' => $request->reason,
                'date_removed' => $request->date_removed,
                'notes' => $request->notes
            ]);

            DB::commit();

            return redirect()->route('inventory.stock-out.show', $stockOutTransaction)
                ->with('success', 'Stock out transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock out update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update stock out transaction. Please try again.');
        }
    }

    /**
     * Remove the specified stock out transaction.
     */
    public function destroy(StockOutTransaction $stockOutTransaction)
    {
        DB::beginTransaction();
        try {
            $inventory = $stockOutTransaction->inventory;
            
            // Return stock to inventory using FIFO reversal
            $quantityToReturn = $stockOutTransaction->quantity_removed;
            $batches = $inventory->stockInTransactions()
                ->orderBy('date_received', 'desc')
                ->get();
            
            foreach ($batches as $batch) {
                if ($quantityToReturn <= 0) break;
                
                // Return to this batch (prefer batches that originally had this stock)
                $returnToBatch = min(100, $quantityToReturn); // Limit return per batch
                $batch->increment('remaining_quantity', $returnToBatch);
                
                $quantityToReturn -= $returnToBatch;
            }
            
            // Update inventory quantity
            $inventory->increment('quantity', $stockOutTransaction->quantity_removed);
            
            // Record reversal movement
            StockMovement::create([
                'inventory_id' => $inventory->id,
                'movement_type' => 'in',
                'quantity' => $stockOutTransaction->quantity_removed,
                'unit_price' => $stockOutTransaction->unit_price,
                'reference_type' => 'stock_out_transaction',
                'reference_id' => $stockOutTransaction->id,
                'remaining_quantity' => $inventory->quantity + $stockOutTransaction->quantity_removed,
                'user_id' => auth()->id(),
                'notes' => 'Stock out transaction deleted - stock returned'
            ]);
            
            $stockOutTransaction->delete();
            
            DB::commit();

            return redirect()->route('inventory.stock-out.index')
                ->with('success', 'Stock out transaction deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock out delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete stock out transaction. Please try again.');
        }
    }

    /**
     * Export stock out transactions to CSV.
     */
    public function export(Request $request)
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
}