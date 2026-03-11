<?php
// app/Models/StockHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_history';

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'cost_price',
        'selling_price',
        'reason',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getMovementTypeAttribute()
    {
        return $this->type === 'in' ? 'Stock In' : 'Stock Out';
    }

    public function getMovementBadgeAttribute()
    {
        if ($this->type === 'in') {
            return '<span class="badge bg-success">Stock In</span>';
        } else {
            return '<span class="badge bg-warning">Stock Out</span>';
        }
    }
}