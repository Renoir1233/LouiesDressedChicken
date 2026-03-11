<?php

namespace Database\Seeders;

use App\Models\WorkEmailDomain;
use Illuminate\Database\Seeder;

class WorkEmailDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * NOTE: Add your company's email domains here for each role.
     * Example: If super-admin should only use @company.com emails:
     * WorkEmailDomain::create([
     *     'role' => 'super-admin',
     *     'email_domain' => 'company.com',
     *     'description' => 'Official company domain for super administrators',
     * ]);
     */
    public function run(): void
    {
        // Example entries - UNCOMMENT AND MODIFY WITH YOUR ACTUAL COMPANY DOMAINS
        
        // Super Admin domain
        // WorkEmailDomain::create([
        //     'role' => 'super-admin',
        //     'email_domain' => 'louieschicken.com',
        //     'description' => 'Louis\' Chicken - Super Admin Domain',
        // ]);

        // // Admin domain
        // WorkEmailDomain::create([
        //     'role' => 'admin',
        //     'email_domain' => 'louieschicken.com',
        //     'description' => 'Louis\' Chicken - Admin Domain',
        // ]);

        // // Manager domain
        // WorkEmailDomain::create([
        //     'role' => 'manager',
        //     'email_domain' => 'louieschicken.com',
        //     'description' => 'Louis\' Chicken - Manager Domain',
        // ]);

        // // Cashier domain
        // WorkEmailDomain::create([
        //     'role' => 'cashier',
        //     'email_domain' => 'louieschicken.com',
        //     'description' => 'Louis\' Chicken - Cashier Domain',
        // ]);

        // // Staff domain
        // WorkEmailDomain::create([
        //     'role' => 'staff',
        //     'email_domain' => 'louieschicken.com',
        //     'description' => 'Louis\' Chicken - Staff Domain',
        // ]);

        echo "Work Email Domain Seeder Complete!\n";
        echo "===========================================\n";
        echo "To add email domain restrictions:\n";
        echo "1. Uncomment the entries in this seeder\n";
        echo "2. Replace 'louieschicken.com' with your company domain\n";
        echo "3. Run: php artisan db:seed --class=WorkEmailDomainSeeder\n";
        echo "===========================================\n";
    }
}
