<?php
// app/Models/StockTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'reference_number',
        'reason',
        'notes',
        'transaction_date',
        'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date'
    ];

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'stock_in' => '<span class="badge bg-success">Stock In</span>',
            'stock_out' => '<span class="badge bg-danger">Stock Out</span>',
            'adjustment' => '<span class="badge bg-warning">Adjustment</span>',
            'initial' => '<span class="badge bg-info">Initial</span>'
        ];
        
        return $badges[$this->type] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getFormattedTransactionDateAttribute()
    {
        return $this->transaction_date->format('M d, Y');
    }
}