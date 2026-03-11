<?php

namespace App\Http\Controllers;

use App\Models\StockInTransaction;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockInController extends Controller
{
    /**
     * Display a listing of stock in transactions.
     */
    public function index(Request $request)
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

    /**
     * Show the form for creating a new stock in transaction.
     */
    public function create()
    {
        $products = Inventory::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('inventory.stock-in.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created stock in transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
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
            $inventory = Inventory::findOrFail($request->inventory_id);
            
            $stockIn = StockInTransaction::create([
                'reference_number' => StockInTransaction::generateReferenceNumber(),
                'batch_number' => $request->batch_number ?: StockInTransaction::generateBatchNumber(),
                'inventory_id' => $request->inventory_id,
                'supplier_id' => $request->supplier_id,
                'quantity_received' => $request->quantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $request->quantity * $request->unit_cost,
                'date_received' => $request->date_received,
                'expiry_date' => $request->expiry_date,
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

            return redirect()->route('inventory.stock-in.index')
                ->with('success', "Stock increased by {$request->quantity} units. Reference: {$stockIn->reference_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock in error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process stock in. Please try again.');
        }
    }

    /**
     * Display the specified stock in transaction.
     */
    public function show(StockInTransaction $stockInTransaction)
    {
        $stockInTransaction->load(['inventory', 'supplier', 'receivedBy', 'stockMovements']);
        
        return view('inventory.stock-in.show', compact('stockInTransaction'));
    }

    /**
     * Show the form for editing the specified stock in transaction.
     */
    public function edit(StockInTransaction $stockInTransaction)
    {
        $products = Inventory::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('inventory.stock-in.edit', compact('stockInTransaction', 'products', 'suppliers'));
    }

    /**
     * Update the specified stock in transaction.
     */
    public function update(Request $request, StockInTransaction $stockInTransaction)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0.01',
            'date_received' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:date_received',
            'batch_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $oldQuantity = $stockInTransaction->quantity_received;
            $newQuantity = $request->quantity;
            $quantityDifference = $newQuantity - $oldQuantity;
            
            $stockInTransaction->update([
                'quantity_received' => $newQuantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $newQuantity * $request->unit_cost,
                'date_received' => $request->date_received,
                'expiry_date' => $request->expiry_date,
                'batch_number' => $request->batch_number,
                'notes' => $request->notes,
                'remaining_quantity' => $stockInTransaction->remaining_quantity + $quantityDifference
            ]);

            // Update inventory quantity if quantity changed
            if ($quantityDifference != 0) {
                $stockInTransaction->inventory->increment('quantity', $quantityDifference);
                
                // Record adjustment movement
                StockMovement::create([
                    'inventory_id' => $stockInTransaction->inventory_id,
                    'movement_type' => $quantityDifference > 0 ? 'in' : 'out',
                    'quantity' => abs($quantityDifference),
                    'unit_price' => $request->unit_cost,
                    'reference_type' => 'stock_in_transaction',
                    'reference_id' => $stockInTransaction->id,
                    'remaining_quantity' => $stockInTransaction->remaining_quantity + $quantityDifference,
                    'user_id' => auth()->id(),
                    'notes' => "Stock in transaction adjusted. " . ($request->notes ?? '')
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.stock-in.show', $stockInTransaction)
                ->with('success', 'Stock in transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock in update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update stock in transaction. Please try again.');
        }
    }

    /**
     * Remove the specified stock in transaction.
     */
    public function destroy(StockInTransaction $stockInTransaction)
    {
        DB::beginTransaction();
        try {
            if ($stockInTransaction->remaining_quantity > 0) {
                // Return stock to inventory
                $stockInTransaction->inventory->decrement('quantity', $stockInTransaction->remaining_quantity);
                
                // Record reversal movement
                StockMovement::create([
                    'inventory_id' => $stockInTransaction->inventory_id,
                    'movement_type' => 'out',
                    'quantity' => $stockInTransaction->remaining_quantity,
                    'unit_price' => $stockInTransaction->unit_cost,
                    'reference_type' => 'stock_in_transaction',
                    'reference_id' => $stockInTransaction->id,
                    'remaining_quantity' => 0,
                    'user_id' => auth()->id(),
                    'notes' => 'Stock in transaction deleted - stock reversed'
                ]);
            }
            
            $stockInTransaction->delete();
            
            DB::commit();

            return redirect()->route('inventory.stock-in.index')
                ->with('success', 'Stock in transaction deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock in delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete stock in transaction. Please try again.');
        }
    }

    /**
     * Export stock in transactions to CSV.
     */
    public function export(Request $request)
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
}