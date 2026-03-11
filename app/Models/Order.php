<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_type',
        'subtotal',
        'tax',
        'total',
        'amount_paid',
        'change',
        'payment_method',
        'payment_status',
        'notes',
        'status',
        'user_id' 
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who created the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for the order
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate total from items
     */
    public function calculateTotal()
    {
        return $this->items->sum(function ($item) {
            return floatval($item->price) * floatval($item->quantity);
        });
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total - $this->amount_paid);
    }

    /**
     * Check if order is fully paid
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->amount_paid >= $this->total;
    }

    /**
     * Get payment status
     */
    public function getPaymentStatusAttribute()
    {
        if ($this->amount_paid == 0) {
            return 'unpaid';
        } elseif ($this->amount_paid < $this->total) {
            return 'partial';
        } else {
            return 'paid';
        }
    }

    /**
     * Get total payments amount
     */
    public function getTotalPaymentsAttribute()
    {
        return $this->payments->sum('amount');
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for partial orders
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Get orders with remaining balance
     */
    public function scopeWithBalance($query)
    {
        return $query->whereRaw('amount_paid < total');
    }

    /**
     * Get orders by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get orders by customer
     */
    public function scopeByCustomer($query, $name, $phone)
    {
        return $query->where('customer_name', $name)
                     ->where('customer_phone', $phone);
    }

    /**
     * Get pending and partial orders by customer
     */
    public function scopePendingByCustomer($query, $name, $phone)
    {
        return $query->where('customer_name', $name)
                     ->where('customer_phone', $phone)
                     ->whereIn('status', ['pending', 'partial']);
    }

    /**
     * Get the cashier name (safely)
     */
    public function getCashierNameAttribute()
    {
        if ($this->user_id && $this->relationLoaded('user') && $this->user) {
            return $this->user->name;
        }
        
        // Fallback: try to load the user if not loaded
        if ($this->user_id && !$this->relationLoaded('user')) {
            $this->load('user');
            return $this->user ? $this->user->name : 'Unknown';
        }
        
        return 'Unknown';
    }

    /**
     * Get formatted totals
     */
    public function getFormattedTotalAttribute()
    {
        return '₱' . number_format($this->total, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return '₱' . number_format($this->subtotal, 2);
    }

    public function getFormattedTaxAttribute()
    {
        return '₱' . number_format($this->tax, 2);
    }

    public function getFormattedAmountPaidAttribute()
    {
        return '₱' . number_format($this->amount_paid, 2);
    }

    public function getFormattedChangeAttribute()
    {
        return '₱' . number_format($this->change, 2);
    }

    /**
     * Auto-calculate total when saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            // If order has items, ensure total is calculated from items
            if ($order->items->count() > 0 && !$order->total) {
                $order->total = $order->calculateTotal();
            }
            
            // *** FIXED: Calculate change correctly ***
            // DO NOT override amount_paid - it should remain what was actually paid
            // Change = amount_paid - total (when amount_paid > total)
            if ($order->amount_paid > $order->total) {
                $order->change = $order->amount_paid - $order->total;
            } elseif ($order->amount_paid == $order->total) {
                $order->change = 0;
            } else {
                // If amount_paid < total (partial payment), change is 0
                $order->change = 0;
            }
            
            // Auto-update status based on payment
            if ($order->amount_paid == 0) {
                $order->status = 'pending';
            } elseif ($order->amount_paid < $order->total) {
                $order->status = 'partial';
            } else {
                $order->status = 'completed';
            }
        });

        static::created(function ($order) {
            // Log order creation
            \Illuminate\Support\Facades\Log::info("Order created: {$order->order_number}, Total: ₱{$order->total}, Amount Paid: ₱{$order->amount_paid}, Change: ₱{$order->change}, Status: {$order->status}");
        });

        static::updated(function ($order) {
            // Log order updates
            \Illuminate\Support\Facades\Log::info("Order updated: {$order->order_number}, Amount Paid: ₱{$order->amount_paid}, Change: ₱{$order->change}, Status: {$order->status}");
        });
    }

    /**
     * Add a payment to this order
     */
    public function addPayment($amount, $paymentMethod = 'cash', $notes = null)
    {
        if ($amount <= 0) {
            throw new \Exception('Payment amount must be greater than 0.');
        }

        if ($this->amount_paid + $amount > $this->total) {
            throw new \Exception('Payment amount exceeds order total.');
        }

        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_date' => now(),
            'reference_number' => Payment::generateReferenceNumber(),
            'notes' => $notes
        ]);

        // Update order amount_paid
        $this->amount_paid += $amount;
        
        // Update status based on new amount_paid
        if ($this->amount_paid >= $this->total) {
            $this->status = 'completed';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        }
        
        $this->save();

        return $payment;
    }

    /**
     * Add items to existing order
     */
    public function addItems($items, $notes = null)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($items, $notes) {
            $additionalSubtotal = 0;
            
            foreach ($items as $itemData) {
                $inventory = Inventory::find($itemData['inventory_id']);
                
                if (!$inventory) {
                    throw new \Exception("Product not found: {$itemData['inventory_id']}");
                }
                
                if ($inventory->quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock for {$inventory->product_name}");
                }
                
                $itemTotal = $itemData['price'] * $itemData['quantity'];
                $additionalSubtotal += $itemTotal;

                // Create order item
                OrderItem::create([
                    'order_id' => $this->id,
                    'inventory_id' => $itemData['inventory_id'],
                    'product_name' => $inventory->product_name,
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'total' => $itemTotal
                ]);

                // Update inventory
                $inventory->decrement('quantity', $itemData['quantity']);
            }

            // Update order totals
            $additionalTax = $additionalSubtotal * 0.12;
            $additionalTotal = $additionalSubtotal + $additionalTax;

            $this->update([
                'subtotal' => $this->subtotal + $additionalSubtotal,
                'tax' => $this->tax + $additionalTax,
                'total' => $this->total + $additionalTotal,
                'notes' => $this->notes . ($notes ? "\nAdditional items: " . $notes : '')
            ]);
        });
    }

    /**
     * Get payment history summary
     */
    public function getPaymentSummary()
    {
        $payments = $this->payments()->orderBy('payment_date', 'asc')->get();
        
        return [
            'total_order_amount' => $this->total,
            'total_paid' => $this->amount_paid,
            'remaining_balance' => $this->remaining_balance,
            'payment_count' => $payments->count(),
            'payments' => $payments,
            'is_fully_paid' => $this->is_fully_paid,
            'payment_status' => $this->payment_status
        ];
    }

    /**
     * Cancel order and restore inventory
     */
    public function cancelOrder($reason = null)
    {
        if ($this->status === 'cancelled') {
            throw new \Exception('Order is already cancelled.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($reason) {
            // Restore inventory for all items
            foreach ($this->items as $item) {
                if ($item->inventory) {
                    $item->inventory->increment('quantity', $item->quantity);
                }
            }

            // Update order status
            $this->update([
                'status' => 'cancelled',
                'notes' => $this->notes . ($reason ? "\nCancelled: " . $reason : '')
            ]);

            \Illuminate\Support\Facades\Log::info("Order cancelled: {$this->order_number}, Reason: {$reason}");
        });
    }

    /**
     * Complete order (mark as fully paid)
     */
    public function completeOrder()
    {
        if ($this->status === 'completed') {
            throw new \Exception('Order is already completed.');
        }

        $remainingBalance = $this->remaining_balance;
        
        if ($remainingBalance > 0) {
            // Add final payment for remaining balance
            $this->addPayment($remainingBalance, 'cash', 'Final payment to complete order');
        }

        $this->update(['status' => 'completed']);
        
        \Illuminate\Support\Facades\Log::info("Order completed: {$this->order_number}");
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $lastOrder = self::where('order_number', 'like', "ORD-{$date}-%")->latest()->first();
        
        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -3) + 1 : 1;
        
        return "ORD-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if customer has pending orders
     */
    public static function customerHasPendingOrders($name, $phone)
    {
        return self::where('customer_name', $name)
            ->where('customer_phone', $phone)
            ->whereIn('status', ['pending', 'partial'])
            ->exists();
    }

    /**
     * Check if customer has unpaid pending/partial order(s)
     * Used to enforce the rule: Regular customers can only have 1 unpaid pending/partial order
     */
    public static function customerHasUnpaidOrder($name, $phone)
    {
        return self::where('customer_name', $name)
            ->where('customer_phone', $phone)
            ->whereIn('status', ['pending', 'partial'])
            ->where('amount_paid', '<', DB::raw('total'))
            ->exists();
    }

    /**
     * Get customer's unpaid pending/partial order
     */
    public static function getCustomerUnpaidOrder($name, $phone)
    {
        return self::where('customer_name', $name)
            ->where('customer_phone', $phone)
            ->whereIn('status', ['pending', 'partial'])
            ->where('amount_paid', '<', DB::raw('total'))
            ->first();
    }
}