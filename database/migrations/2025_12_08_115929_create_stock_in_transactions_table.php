<?php
// database/migrations/2025_12_07_000001_create_stock_in_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_in_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_received', 10, 2);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->date('date_received');
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('remaining_quantity', 10, 2)->default(0); // For FIFO tracking
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_in_transactions');
    }
};