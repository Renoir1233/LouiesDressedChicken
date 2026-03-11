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
        Schema::create('device_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('device_fingerprint')->unique(); // Hash of device characteristics
            $table->string('device_name')->nullable(); // User-friendly device name
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            
            // Index for fast lookup
            $table->index('user_id');
            $table->index('device_fingerprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_logins');
    }
};
