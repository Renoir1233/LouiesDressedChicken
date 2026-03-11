<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the seeders in correct order
        $this->call([
            // RoleSeeder::class,      // First create roles
            AdminUserSeeder::class, // Create admin user
        ]);
    }
}