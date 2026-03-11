<?php
// app/Models/StockMovement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'movement_type',
        'quantity',
        'unit_price',
        'reference_type',
        'reference_id',
        'remaining_quantity',
        'user_id',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'remaining_quantity' => 'decimal:2'
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

    // Polymorphic relationship
    public function reference()
    {
        return $this->morphTo();
    }

    // Get movement type label
    public function getMovementTypeLabelAttribute()
    {
        return $this->movement_type == 'in' ? 'Stock In' : 'Stock Out';
    }

    // Get badge class for movement type
    public function getMovementBadgeClassAttribute()
    {
        return $this->movement_type == 'in' ? 'badge bg-success' : 'badge bg-danger';
    }
}