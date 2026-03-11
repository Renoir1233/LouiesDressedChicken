<?php
// app/Models/StockOutTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'inventory_id',
        'quantity_removed',
        'unit_price',
        'total_value',
        'reason',
        'date_removed',
        'handled_by',
        'notes',
        'is_active',
        'stock_in_transaction_id',
        'unit_cost',
        'total_cost',
        'profit_amount',
        'supplier_id',
        'batch_number'
    ];

    protected $casts = [
        'date_removed' => 'date',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    // Relationship to the batch (StockInTransaction) that was used
    public function batchUsed()
    {
        return $this->belongsTo(StockInTransaction::class, 'stock_in_transaction_id');
    }

    // Relationship to supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Generate reference number
    public static function generateReferenceNumber()
    {
        $prefix = 'SO-' . date('Ymd') . '-';
        $last = self::where('reference_number', 'like', $prefix . '%')
                    ->orderBy('reference_number', 'desc')
                    ->first();
        
        if ($last) {
            $number = intval(str_replace($prefix, '', $last->reference_number)) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Get reason label
    public function getReasonLabelAttribute()
    {
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
        
        return $reasons[$this->reason] ?? $this->reason;
    }

    /**
     * Calculate profit for this transaction
     * Used for sales to calculate actual profit based on batch cost
     */
    public function calculateProfit()
    {
        if ($this->unit_cost && $this->unit_price) {
            $profit_per_unit = $this->unit_price - $this->unit_cost;
            return $profit_per_unit * $this->quantity_removed;
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
                'batch_number' => $this->batchUsed->batch_number,
                'date_received' => $this->batchUsed->date_received,
                'original_cost' => $this->batchUsed->unit_cost,
                'cost_at_sale' => $this->unit_cost
            ];
        }
        return null;
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginAttribute()
    {
        if ($this->unit_cost && $this->unit_cost > 0) {
            return (($this->unit_price - $this->unit_cost) / $this->unit_cost) * 100;
        }
        return 0;
    }
}