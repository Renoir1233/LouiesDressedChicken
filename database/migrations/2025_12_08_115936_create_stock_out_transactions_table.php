<?php
// database/migrations/2025_12_07_000002_create_stock_out_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_out_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->decimal('quantity_removed', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_value', 10, 2);
            $table->string('reason'); // sale, damage, expiration, return, internal_use
            $table->date('date_removed');
            $table->foreignId('handled_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_out_transactions');
    }
};