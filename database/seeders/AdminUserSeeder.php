<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Remove old super admin email if it exists and create new one
        User::where('email', 'admin@louieschicken.com')->delete();

        // Check if new admin user already exists
        if (!User::where('email', 'guadalquivercarlpatrick@gmail.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'guadalquivercarlpatrick@gmail.com',
                'password' => Hash::make('Admin123!'),
                'role' => 'super-admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create additional test users
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin2@louieschicken.com',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@louieschicken.com',
                'password' => Hash::make('Manager123!'),
                'role' => 'manager',
                'is_active' => true,
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@louieschicken.com',
                'password' => Hash::make('Cashier123!'),
                'role' => 'cashier',
                'is_active' => true,
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@louieschicken.com',
                'password' => Hash::make('Staff123!'),
                'role' => 'staff',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                User::create($userData);
            }
        }

        echo "==========================================\n";
        echo "Users created successfully!\n";
        echo "==========================================\n";
        echo "Login Credentials:\n";
        echo "==========================================\n";
        echo "Super Admin: guadalquivercarlpatrick@gmail.com / Admin123!\n";
        echo "Admin: admin2@louieschicken.com / Admin123!\n";
        echo "Manager: manager@louieschicken.com / Manager123!\n";
        echo "Cashier: cashier@louieschicken.com / Cashier123!\n";
        echo "Staff: staff@louieschicken.com / Staff123!\n";
        echo "==========================================\n";
    }
}