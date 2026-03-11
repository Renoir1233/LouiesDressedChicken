<?php
// app/Models/Inventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'product_name',
        'product_code',
        'description',
        'image',
        'supplier_id',
        'cost_price',
        'selling_price',
        'quantity',
        'low_stock_alert',
        'category',
        'unit',
        'is_active'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Allowed categories
     */
    public const CATEGORIES = [
        'Whole Chicken',
        'Chicken Part',
        'Hotdog',
        'Fish'
    ];

    // Accessor for stock status
    public function getStockStatusAttribute()
    {
        if ($this->quantity == 0) {
            return 'out_of_stock';
        } elseif ($this->quantity <= $this->low_stock_alert) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    // Accessor for stock status badge
    public function getStockStatusBadgeAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return '<span class="badge bg-danger">Out of Stock</span>';
            case 'low_stock':
                return '<span class="badge bg-warning">Low Stock</span>';
            default:
                return '<span class="badge bg-success">In Stock</span>';
        }
    }

    // Accessor for active status badge
    public function getActiveStatusBadgeAttribute()
    {
        if ($this->is_active) {
            return '<span class="badge bg-success">Active</span>';
        } else {
            return '<span class="badge bg-secondary">Inactive</span>';
        }
    }

    // Calculate profit margin
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
        }
        return 0;
    }

    // Get total value of current stock
    public function getTotalStockValueAttribute()
    {
        return $this->quantity * $this->cost_price;
    }

    // Get total potential sales value
    public function getTotalSalesValueAttribute()
    {
        return $this->quantity * $this->selling_price;
    }

    // Relationship with supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship with stock in transactions
    public function stockInTransactions()
    {
        return $this->hasMany(StockInTransaction::class)->orderBy('date_received', 'asc');
    }

    // Relationship with stock out transactions
    public function stockOutTransactions()
    {
        return $this->hasMany(StockOutTransaction::class);
    }

    // Relationship with stock movements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    // Scope for low stock items
    public function scopeLowStock($query)
    {
        return $query->where('quantity', '>', 0)
                    ->whereRaw('quantity <= low_stock_alert')
                    ->where('is_active', true);
    }

    // Scope for out of stock items
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0)
                    ->where('is_active', true);
    }

    // Scope for active items
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for inactive items
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Get available batches for FIFO (batches with remaining quantity)
    public function getAvailableBatches()
    {
        return $this->stockInTransactions()
            ->where('remaining_quantity', '>', 0)
            ->orderBy('date_received', 'asc')
            ->get();
    }

    // Get oldest batch with available stock
    public function getOldestAvailableBatch()
    {
        return $this->stockInTransactions()
            ->where('remaining_quantity', '>', 0)
            ->orderBy('date_received', 'asc')
            ->first();
    }

    // Get stock value by FIFO
    public function getFifoStockValue()
    {
        $value = 0;
        $batches = $this->getAvailableBatches();
        
        foreach ($batches as $batch) {
            $value += $batch->remaining_quantity * $batch->unit_cost;
        }
        
        return $value;
    }

    /**
     * Get detailed batch breakdown showing each batch with supplier and cost
     * Used for frozen products with changing prices
     */
    public function getBatchBreakdown()
    {
        return $this->stockInTransactions()
            ->where('remaining_quantity', '>', 0)
            ->with('supplier')
            ->orderBy('date_received', 'asc')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'supplier_name' => $batch->supplier->name ?? 'Unknown',
                    'supplier_id' => $batch->supplier_id,
                    'quantity' => $batch->remaining_quantity,
                    'unit_cost' => $batch->unit_cost,
                    'total_value' => $batch->remaining_quantity * $batch->unit_cost,
                    'date_received' => $batch->date_received,
                    'expiry_date' => $batch->expiry_date,
                    'percentage_of_total' => ($batch->remaining_quantity / $this->quantity) * 100
                ];
            });
    }

    /**
     * Get weighted average cost for the current stock
     * Useful for quick price estimation
     */
    public function getWeightedAverageCost()
    {
        if ($this->quantity == 0) {
            return 0;
        }

        $totalCost = 0;
        $batches = $this->getAvailableBatches();

        foreach ($batches as $batch) {
            $totalCost += $batch->remaining_quantity * $batch->unit_cost;
        }

        return $totalCost / $this->quantity;
    }

    /**
     * Check if this product has multiple suppliers (mixed batches)
     */
    public function hasMixedSuppliers()
    {
        $suppliers = $this->stockInTransactions()
            ->where('remaining_quantity', '>', 0)
            ->distinct('supplier_id')
            ->count('supplier_id');

        return $suppliers > 1;
    }

    /**
     * Get suppliers and their quantities in stock
     */
    public function getSupplierBreakdown()
    {
        return $this->stockInTransactions()
            ->where('remaining_quantity', '>', 0)
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($batches, $supplierId) {
                $supplier = $batches->first()->supplier;
                $totalQuantity = $batches->sum('remaining_quantity');
                $totalValue = $batches->sum(function ($batch) {
                    return $batch->remaining_quantity * $batch->unit_cost;
                });

                return [
                    'supplier_id' => $supplierId,
                    'supplier_name' => $supplier->name ?? 'Unknown',
                    'quantity' => $totalQuantity,
                    'average_cost' => $totalValue / $totalQuantity,
                    'total_value' => $totalValue,
                    'batch_count' => $batches->count(),
                    'percentage_of_total' => ($totalQuantity / $this->quantity) * 100
                ];
            })
            ->values();
    }
}