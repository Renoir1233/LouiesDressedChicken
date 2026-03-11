<?php

namespace App\Console\Commands;

use App\Models\WorkEmailDomain;
use Illuminate\Console\Command;

class ListWorkEmailDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'work-email:list';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'List all configured work email domains';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domains = WorkEmailDomain::all();

        if ($domains->isEmpty()) {
            $this->info("No work email domains configured yet.");
            return 0;
        }

        $headers = ['Role', 'Email Domain', 'Description', 'Status'];
        $rows = $domains->map(function ($domain) {
            return [
                $domain->role,
                $domain->email_domain,
                $domain->description ?? 'N/A',
                $domain->is_active ? 'Active' : 'Inactive',
            ];
        })->toArray();

        $this->table($headers, $rows);

        return 0;
    }
}
