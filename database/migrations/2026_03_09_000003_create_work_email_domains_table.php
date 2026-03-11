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
        Schema::create('work_email_domains', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // Role like 'super-admin', 'admin', 'manager', etc.
            $table->string('email_domain'); // Domain like 'company.com'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure unique combination of role and domain
            $table->unique(['role', 'email_domain']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_email_domains');
    }
};
