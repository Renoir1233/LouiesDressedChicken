<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds batch tracking and cost tracking to stock out transactions.
     * This allows the system to know which batch (supplier/cost) was used
     * when stock is sold or removed, enabling FIFO cost tracking.
     */
    public function up()
    {
        Schema::table('stock_out_transactions', function (Blueprint $table) {
            // Add batch reference to track which batch was used
            $table->foreignId('stock_in_transaction_id')->nullable()->after('inventory_id')->constrained('stock_in_transactions')->onDelete('set null');
            
            // Add actual cost price at time of sale (important for frozen products with changing prices)
            $table->decimal('unit_cost', 10, 2)->nullable()->after('unit_price');
            
            // Add total cost (for profit calculation)
            $table->decimal('total_cost', 10, 2)->nullable()->after('total_value');
            
            // Add profit tracking
            $table->decimal('profit_amount', 10, 2)->nullable()->after('total_cost');
            
            // Add supplier reference (for tracking which supplier the batch came from)
            $table->foreignId('supplier_id')->nullable()->after('inventory_id')->constrained()->onDelete('set null');
            
            // Add batch number for reference
            $table->string('batch_number')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('stock_out_transactions', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['stock_in_transaction_id']);
            $table->dropForeignKeyIfExists(['supplier_id']);
            $table->dropColumn([
                'stock_in_transaction_id',
                'unit_cost',
                'total_cost',
                'profit_amount',
                'supplier_id',
                'batch_number'
            ]);
        });
    }
};
