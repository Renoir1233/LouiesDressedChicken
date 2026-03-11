<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_inventory_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_code')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable(); 
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('low_stock_alert', 10, 2)->default(10);
            $table->string('category');
            $table->string('unit')->default('pcs');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};