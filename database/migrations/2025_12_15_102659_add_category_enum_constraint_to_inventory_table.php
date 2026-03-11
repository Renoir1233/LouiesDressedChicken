<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, delete records with invalid categories
        \Illuminate\Support\Facades\DB::table('inventory')
            ->whereNotIn('category', ['Whole Chicken', 'Chicken Part', 'Hotdog', 'Fish'])
            ->delete();
        
        // Then change the column to enum
        Schema::table('inventory', function (Blueprint $table) {
            $table->enum('category', ['Whole Chicken', 'Chicken Part', 'Hotdog', 'Fish'])
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            // Revert to string column
            $table->string('category')->change();
        });
    }
};
