<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EnableTwoFactorAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:enable-2fa {email}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Enable two-factor authentication for a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        $user->enableTwoFactor();

        $this->info("Two-factor authentication has been enabled for {$user->name} ({$user->email})");
        return 0;
    }
}
