<?php
// database/migrations/2024_01_01_000002_create_roles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('permissions')->nullable(); // JSON encoded permissions
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'permissions' => json_encode(['*']), 'description' => 'Full system access', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin', 'slug' => 'admin', 'permissions' => json_encode(['dashboard', 'orders.*', 'inventory.*', 'employees.*', 'suppliers.*', 'billing.*', 'users.read', 'audit-logs.read']), 'description' => 'Administrator access', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'slug' => 'manager', 'permissions' => json_encode(['dashboard', 'orders.*', 'inventory.*', 'employees.*', 'suppliers.*', 'billing.*']), 'description' => 'Manager access', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cashier', 'slug' => 'cashier', 'permissions' => json_encode(['dashboard', 'orders.create', 'orders.store', 'billing.*']), 'description' => 'Cashier access', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Staff', 'slug' => 'staff', 'permissions' => json_encode(['dashboard', 'inventory.read', 'orders.read']), 'description' => 'Staff access', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}