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
        Schema::table('device_logins', function (Blueprint $table) {
            // Drop the global unique on device_fingerprint alone
            $table->dropUnique(['device_fingerprint']);
            // Add composite unique so two different users can use the same device
            $table->unique(['user_id', 'device_fingerprint'], 'device_logins_user_fingerprint_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_logins', function (Blueprint $table) {
            $table->dropUnique('device_logins_user_fingerprint_unique');
            $table->unique('device_fingerprint');
        });
    }
};
