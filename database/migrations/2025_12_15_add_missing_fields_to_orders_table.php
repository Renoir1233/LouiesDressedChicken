<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('orders', 'customer_type')) {
                $table->string('customer_type')->nullable()->after('customer_address');
            }
            
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('status')->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['user_id']);
            $table->dropColumnIfExists('customer_type');
            $table->dropColumnIfExists('payment_status');
            $table->dropColumnIfExists('user_id');
        });
    }
};
