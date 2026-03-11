<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{


    use HasFactory;

    /**
     * Use reference_number for route model binding.
     */
    public function getRouteKeyName()
    {
        return 'reference_number';
    }

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Generate a unique reference number for cash payments
     */
    public static function generateReferenceNumber()
    {
        $date = now()->format('Ymd');
        $lastPayment = self::where('reference_number', 'like', "CASH-{$date}-%")->latest()->first();
        
        $sequence = $lastPayment ? (int) substr($lastPayment->reference_number, -4) + 1 : 1;
        
        return "CASH-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}