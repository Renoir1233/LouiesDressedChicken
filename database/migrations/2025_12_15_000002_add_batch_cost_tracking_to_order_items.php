<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds batch and cost tracking to order items.
     * This allows the system to track which batch/supplier the item was sold from
     * and the actual cost at time of sale (important for frozen products).
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Add reference to the batch (StockInTransaction) used for this sale
            $table->foreignId('stock_in_transaction_id')->nullable()->after('inventory_id')->constrained('stock_in_transactions')->onDelete('set null');
            
            // Add the actual cost price at time of sale
            $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            
            // Add profit tracking
            $table->decimal('profit', 10, 2)->nullable()->after('total');
            
            // Add supplier reference for quick access
            $table->foreignId('supplier_id')->nullable()->after('inventory_id')->constrained()->onDelete('set null');
            
            // Add batch number for reference
            $table->string('batch_number')->nullable()->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['stock_in_transaction_id']);
            $table->dropForeignKeyIfExists(['supplier_id']);
            $table->dropColumn([
                'stock_in_transaction_id',
                'cost_price',
                'profit',
                'supplier_id',
                'batch_number'
            ]);
        });
    }
};
