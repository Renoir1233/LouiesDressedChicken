<?php
// database/migrations/2025_12_07_000003_create_stock_movements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->string('movement_type'); // 'in' or 'out'
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->string('reference_type'); // 'stock_in_transaction', 'stock_out_transaction', 'order', 'manual'
            $table->unsignedBigInteger('reference_id');
            $table->decimal('remaining_quantity', 10, 2)->default(0); // For FIFO
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['inventory_id', 'movement_type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};