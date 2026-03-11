<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DisableTwoFactorAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:disable-2fa {email}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Disable two-factor authentication for a user';

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

        $user->disableTwoFactor();

        $this->info("Two-factor authentication has been disabled for {$user->name} ({$user->email})");
        return 0;
    }
}
