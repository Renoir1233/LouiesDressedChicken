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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address');
            $table->integer('failed_attempts')->default(1);
            $table->timestamp('first_attempt_at')->useCurrent();
            $table->timestamp('last_attempt_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
            
            // Indexes for fast lookups
            $table->index(['email', 'ip_address']);
            $table->index('locked_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
