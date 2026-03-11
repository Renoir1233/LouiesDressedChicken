<?php
// database/migrations/2024_01_01_000004_add_user_id_to_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToTables extends Migration
{
    public function up()
    {
        // Add user_id to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->index(['user_id']);
        });

        // Add user_id to inventory table
        Schema::table('inventory', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->index(['user_id']);
        });

        // Add user_id to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->index(['user_id']);
        });

        // Add user_id to suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->index(['user_id']);
        });

        // Add user_id to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->index(['user_id']);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}