<?php

namespace App\Console\Commands;

use App\Models\WorkEmailDomain;
use Illuminate\Console\Command;

class RemoveWorkEmailDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work-email:remove {role} {domain}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Remove a work email domain for a specific role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $role = $this->argument('role');
        $domain = $this->argument('domain');

        $deleted = WorkEmailDomain::where('role', $role)
            ->where('email_domain', $domain)
            ->delete();

        if ($deleted) {
            $this->info("Email domain '{$domain}' has been removed for role '{$role}'");
            return 0;
        } else {
            $this->error("Email domain '{$domain}' not found for role '{$role}'");
            return 1;
        }
    }
}
