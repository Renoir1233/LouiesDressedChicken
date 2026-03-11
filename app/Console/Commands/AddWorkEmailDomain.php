<?php

namespace App\Console\Commands;

use App\Models\WorkEmailDomain;
use Illuminate\Console\Command;

class AddWorkEmailDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work-email:add {role} {domain} {--description=}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Add a work email domain for a specific role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $role = $this->argument('role');
        $domain = $this->argument('domain');
        $description = $this->option('description');

        // Validate role
        $validRoles = ['super-admin', 'admin', 'manager', 'cashier', 'staff'];
        if (!in_array($role, $validRoles)) {
            $this->error("Invalid role. Valid roles are: " . implode(', ', $validRoles));
            return 1;
        }

        // Check if domain already exists for this role
        if (WorkEmailDomain::where('role', $role)->where('email_domain', $domain)->exists()) {
            $this->error("Domain '{$domain}' is already registered for role '{$role}'");
            return 1;
        }

        // Create the entry
        WorkEmailDomain::create([
            'role' => $role,
            'email_domain' => $domain,
            'description' => $description,
        ]);

        $this->info("Email domain '{$domain}' has been added for role '{$role}'");
        return 0;
    }
}
