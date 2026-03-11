<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'gcash', 'bank_transfer'])->default('cash');
            $table->dateTime('payment_date');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('payment_date');
            $table->index('reference_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};