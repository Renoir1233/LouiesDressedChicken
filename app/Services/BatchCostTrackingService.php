<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\StockInTransaction;
use App\Models\StockOutTransaction;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

/**
 * Batch Cost Tracking Service
 * 
 * Handles FIFO-based batch selection and cost tracking for inventory items.
 * Especially useful for frozen products with fluctuating supplier prices.
 */
class BatchCostTrackingService
{
    /**
     * Get the next batch to use for a sale (FIFO method)
     * 
     * @param Inventory $inventory
     * @return StockInTransaction|null
     */
    public function getNextBatchForSale(Inventory $inventory): ?StockInTransaction
    {
        return $inventory->getOldestAvailableBatch();
    }

    /**
     * Allocate quantity from a specific batch
     * 
     * @param StockInTransaction $batch
     * @param float $quantity
     * @return array ['success' => bool, 'message' => string, 'allocated' => float]
     */
    public function allocateFromBatch(StockInTransaction $batch, float $quantity): array
    {
        if ($batch->remaining_quantity < $quantity) {
            return [
                'success' => false,
                'message' => "Not enough quantity in this batch. Available: {$batch->remaining_quantity}, Requested: {$quantity}",
                'allocated' => 0
            ];
        }

        $batch->remaining_quantity -= $quantity;
        $batch->save();

        return [
            'success' => true,
            'message' => "Allocated {$quantity} units from batch {$batch->batch_number}",
            'allocated' => $quantity
        ];
    }

    /**
     * Record a stock out transaction with batch tracking
     * 
     * @param Inventory $inventory
     * @param float $quantity
     * @param string $reason
     * @param int $handledBy (user ID)
     * @param StockInTransaction|null $batch (optional, will use FIFO if null)
     * @param string|null $notes
     * @return StockOutTransaction
     */
    public function recordStockOut(
        Inventory $inventory,
        float $quantity,
        string $reason,
        int $handledBy,
        StockInTransaction $batch = null,
        string $notes = null
    ): StockOutTransaction {
        // Use FIFO if batch not specified
        if (!$batch) {
            $batch = $this->getNextBatchForSale($inventory);
        }

        // Allocate from batch
        $this->allocateFromBatch($batch, $quantity);

        // Create stock out transaction
        $stockOut = new StockOutTransaction([
            'reference_number' => StockOutTransaction::generateReferenceNumber(),
            'inventory_id' => $inventory->id,
            'stock_in_transaction_id' => $batch->id,
            'supplier_id' => $batch->supplier_id,
            'batch_number' => $batch->batch_number,
            'quantity_removed' => $quantity,
            'unit_price' => $inventory->selling_price,
            'total_value' => $quantity * $inventory->selling_price,
            'unit_cost' => $batch->unit_cost, // Store the batch cost
            'total_cost' => $quantity * $batch->unit_cost, // Calculate total cost from batch
            'reason' => $reason,
            'date_removed' => now()->toDateString(),
            'handled_by' => $handledBy,
            'notes' => $notes,
            'is_active' => true
        ]);

        // Calculate profit
        $stockOut->profit_amount = $stockOut->total_value - $stockOut->total_cost;
        $stockOut->save();

        // Update inventory quantity
        $inventory->quantity -= $quantity;
        $inventory->save();

        return $stockOut;
    }

    /**
     * Record an order item with batch tracking (for sales)
     * 
     * @param OrderItem $orderItem
     * @param StockInTransaction|null $batch (optional, will use FIFO if null)
     * @return OrderItem
     */
    public function recordOrderItemWithBatch(OrderItem $orderItem, StockInTransaction $batch = null): OrderItem
    {
        $inventory = $orderItem->inventory;

        // Use FIFO if batch not specified
        if (!$batch) {
            $batch = $this->getNextBatchForSale($inventory);
        }

        if ($batch) {
            $orderItem->stock_in_transaction_id = $batch->id;
            $orderItem->supplier_id = $batch->supplier_id;
            $orderItem->batch_number = $batch->batch_number;
            $orderItem->cost_price = $batch->unit_cost;
            $orderItem->profit = ($orderItem->price - $batch->unit_cost) * $orderItem->quantity;
            
            // Allocate from batch
            $this->allocateFromBatch($batch, $orderItem->quantity);
        }

        $orderItem->save();
        return $orderItem;
    }

    /**
     * Get detailed cost analysis for an inventory item
     * Shows all batches with their costs and quantities
     * 
     * @param Inventory $inventory
     * @return array
     */
    public function getDetailedCostAnalysis(Inventory $inventory): array
    {
        $batches = $inventory->getBatchBreakdown();
        $weightedAvgCost = $inventory->getWeightedAverageCost();
        $totalValue = $inventory->getTotalStockValueAttribute();

        return [
            'product' => [
                'id' => $inventory->id,
                'name' => $inventory->product_name,
                'code' => $inventory->product_code,
                'total_quantity' => $inventory->quantity,
                'selling_price' => $inventory->selling_price
            ],
            'cost_analysis' => [
                'weighted_average_cost' => $weightedAvgCost,
                'total_stock_value' => $totalValue,
                'potential_profit' => $inventory->getTotalSalesValueAttribute() - $totalValue,
                'has_mixed_suppliers' => $inventory->hasMixedSuppliers()
            ],
            'batches' => $batches,
            'supplier_breakdown' => $inventory->getSupplierBreakdown()
        ];
    }

    /**
     * Get cost comparison for same product from different suppliers
     * Useful for identifying price differences
     * 
     * @param Inventory $inventory
     * @return Collection
     */
    public function getSupplierCostComparison(Inventory $inventory): Collection
    {
        $suppliers = $inventory->getSupplierBreakdown();
        
        return collect($suppliers)->map(function ($supplier) {
            return [
                'supplier_name' => $supplier['supplier_name'],
                'quantity' => $supplier['quantity'],
                'cost_per_unit' => $supplier['average_cost'],
                'total_value' => $supplier['total_value'],
                'cost_difference_from_cheapest' => null // Will be calculated below
            ];
        })->sortBy('cost_per_unit')->map(function ($supplier, $index) use ($suppliers) {
            $cheapest = collect($suppliers)->min('average_cost');
            $supplier['cost_difference_from_cheapest'] = $supplier['cost_per_unit'] - $cheapest;
            return $supplier;
        });
    }

    /**
     * Get sales analysis with actual profit based on batch costs
     * 
     * @param Inventory $inventory
     * @param int $days (number of days to analyze)
     * @return array
     */
    public function getSalesAnalysisWithProfitTracking(Inventory $inventory, int $days = 30): array
    {
        $cutoffDate = now()->subDays($days)->toDateString();

        // Get stock out transactions for sales in the period
        $sales = $inventory->stockOutTransactions()
            ->where('reason', 'sale')
            ->where('date_removed', '>=', $cutoffDate)
            ->orderBy('date_removed', 'desc')
            ->get();

        $totalQuantitySold = $sales->sum('quantity_removed');
        $totalRevenue = $sales->sum('total_value');
        $totalCost = $sales->sum('total_cost');
        $totalProfit = $sales->sum('profit_amount');

        return [
            'period' => "{$days} days",
            'statistics' => [
                'total_quantity_sold' => $totalQuantitySold,
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin_percentage' => $totalRevenue > 0 ? (($totalProfit / $totalRevenue) * 100) : 0,
                'average_profit_per_unit' => $totalQuantitySold > 0 ? ($totalProfit / $totalQuantitySold) : 0
            ],
            'by_supplier' => $this->groupSalesBySupplier($sales),
            'by_batch' => $this->groupSalesByBatch($sales)
        ];
    }

    /**
     * Group sales by supplier for analysis
     * 
     * @param Collection $sales
     * @return array
     */
    private function groupSalesBySupplier(Collection $sales): array
    {
        return $sales->groupBy('supplier_id')->map(function ($supplierSales) {
            $supplier = $supplierSales->first()->supplier;
            $quantity = $supplierSales->sum('quantity_removed');
            $revenue = $supplierSales->sum('total_value');
            $cost = $supplierSales->sum('total_cost');
            $profit = $revenue - $cost;

            return [
                'supplier_name' => $supplier->name ?? 'Unknown',
                'quantity_sold' => $quantity,
                'revenue' => $revenue,
                'total_cost' => $cost,
                'profit' => $profit,
                'profit_margin_percentage' => $revenue > 0 ? (($profit / $revenue) * 100) : 0,
                'average_cost_per_unit' => $quantity > 0 ? ($cost / $quantity) : 0
            ];
        })->values()->all();
    }

    /**
     * Group sales by batch for detailed analysis
     * 
     * @param Collection $sales
     * @return array
     */
    private function groupSalesByBatch(Collection $sales): array
    {
        return $sales->groupBy('stock_in_transaction_id')->map(function ($batchSales) {
            $batch = $batchSales->first()->batchUsed;
            $quantity = $batchSales->sum('quantity_removed');
            $revenue = $batchSales->sum('total_value');
            $cost = $batchSales->sum('total_cost');
            $profit = $revenue - $cost;

            return [
                'batch_number' => $batch->batch_number,
                'date_received' => $batch->date_received,
                'supplier_name' => $batch->supplier->name ?? 'Unknown',
                'quantity_sold' => $quantity,
                'cost_per_unit' => $batch->unit_cost,
                'revenue' => $revenue,
                'total_cost' => $cost,
                'profit' => $profit,
                'profit_margin_percentage' => $revenue > 0 ? (($profit / $revenue) * 100) : 0
            ];
        })->values()->all();
    }

    /**
     * Identify pricing conflicts (where high-cost items might be sold at low price)
     * 
     * @param Inventory $inventory
     * @return array
     */
    public function identifyPricingConflicts(Inventory $inventory): array
    {
        $batches = $inventory->getBatchBreakdown();
        $conflicts = [];

        if (count($batches) > 1) {
            // Sort by cost
            $sortedByPrice = collect($batches)->sortBy('unit_cost');
            $highestCost = $sortedByPrice->last()['unit_cost'];
            $lowestCost = $sortedByPrice->first()['unit_cost'];
            $costDifference = $highestCost - $lowestCost;
            $percentageDifference = (($costDifference / $lowestCost) * 100);

            if ($percentageDifference > 5) { // More than 5% difference
                $conflicts[] = [
                    'type' => 'PRICE_VARIATION',
                    'severity' => $percentageDifference > 10 ? 'HIGH' : 'MEDIUM',
                    'message' => "Product has {$percentageDifference}% cost variation between suppliers",
                    'highest_cost_supplier' => $sortedByPrice->last()['supplier_name'],
                    'lowest_cost_supplier' => $sortedByPrice->first()['supplier_name'],
                    'difference_amount' => $costDifference,
                    'percentage_difference' => $percentageDifference
                ];
            }
        }

        // Check if single price is used for multiple costs
        if ($inventory->cost_price) {
            $batches = $inventory->getAvailableBatches();
            $differsFromMainPrice = $batches->filter(function ($batch) use ($inventory) {
                return $batch->unit_cost != $inventory->cost_price;
            })->count();

            if ($differsFromMainPrice > 0) {
                $conflicts[] = [
                    'type' => 'SINGLE_PRICE_MISMATCH',
                    'severity' => 'HIGH',
                    'message' => "{$differsFromMainPrice} batches have different costs than the main inventory price",
                    'main_price' => $inventory->cost_price,
                    'batches_with_different_costs' => $differsFromMainPrice
                ];
            }
        }

        return $conflicts;
    }
}
