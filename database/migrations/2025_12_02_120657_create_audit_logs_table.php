<?php
// database/migrations/2024_01_01_000003_create_audit_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // Created, Updated, Deleted, Viewed, Logged In, etc.
            $table->string('model_type')->nullable(); // App\Models\Order, App\Models\Inventory, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index(['model_type', 'model_id']);
            $table->index(['action']);
            $table->index(['user_id']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}