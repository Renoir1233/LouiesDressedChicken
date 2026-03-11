<?php
// app/Models/StockInTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInTransaction extends Model
{
    use HasFactory;

    protected $table = 'stock_in_transactions'; // This should match your table name

    protected $fillable = [
        'reference_number',
        'inventory_id',
        'supplier_id',
        'quantity_received',
        'unit_cost',
        'total_cost',
        'date_received',
        'expiry_date',
        'batch_number',
        'received_by',
        'notes',
        'is_active',
        'remaining_quantity'
    ];

    protected $casts = [
        'date_received' => 'date',
        'expiry_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Generate reference number
    public static function generateReferenceNumber()
    {
        $prefix = 'SI-' . date('Ymd') . '-';
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

    // Generate batch number
    public static function generateBatchNumber()
    {
        $prefix = 'BATCH-' . date('Ymd') . '-';
        $last = self::where('batch_number', 'like', $prefix . '%')
                    ->orderBy('batch_number', 'desc')
                    ->first();
        
        if ($last) {
            $number = intval(str_replace($prefix, '', $last->batch_number)) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }
}