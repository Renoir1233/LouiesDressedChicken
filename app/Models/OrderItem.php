<?php
// app/Models/OrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'inventory_id', // Fixed: was 'inventory'
        'product_name',
        'price',
        'quantity',
        'unit',
        'total',
        'stock_in_transaction_id',
        'cost_price',
        'profit',
        'supplier_id',
        'batch_number'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'decimal:2', // Changed to decimal
        'total' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'profit' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the batch this item was sold from
     */
    public function batchUsed()
    {
        return $this->belongsTo(StockInTransaction::class, 'stock_in_transaction_id');
    }

    /**
     * Get the supplier this item came from
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Calculate profit margin percentage
     */
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price && $this->cost_price > 0) {
            return (($this->price - $this->cost_price) / $this->cost_price) * 100;
        }
        return 0;
    }

    /**
     * Get batch information for display
     */
    public function getBatchInfoAttribute()
    {
        if ($this->batchUsed) {
            return [
                'supplier_name' => $this->batchUsed->supplier->name ?? 'Unknown',
                'batch_number' => $this->batch_number,
                'date_received' => $this->batchUsed->date_received,
                'cost_at_sale' => $this->cost_price,
                'original_cost' => $this->batchUsed->unit_cost
            ];
        }
        return null;
    }
}