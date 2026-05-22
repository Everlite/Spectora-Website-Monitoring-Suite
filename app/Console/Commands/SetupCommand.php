<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spectora:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively create the initial administrator user for the private Spectora suite';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('==================================================');
        $this->info('      Spectora Private Suite - Admin Setup        ');
        $this->info('==================================================');
        $this->newLine();

        // 1. First Name
        $firstName = null;
        while (empty($firstName)) {
            $firstName = trim($this->ask('Enter administrator first name'));
            if (empty($firstName)) {
                $this->error('First name cannot be empty.');
            }
        }

        // 2. Last Name
        $lastName = null;
        while (empty($lastName)) {
            $lastName = trim($this->ask('Enter administrator last name'));
            if (empty($lastName)) {
                $this->error('Last name cannot be empty.');
            }
        }

        // 3. Email
        $email = null;
        while (empty($email)) {
            $email = trim($this->ask('Enter administrator email address'));
            if (empty($email)) {
                $this->error('Email address cannot be empty.');
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('Please enter a valid email address.');
                $email = null;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $this->error('A user with this email address already exists.');
                $email = null;
                continue;
            }
        }

        // 4. Password
        $password = null;
        while (empty($password)) {
            $password = $this->secret('Enter administrator password (min. 8 characters)');
            if (empty($password)) {
                $this->error('Password cannot be empty.');
                continue;
            }

            if (strlen($password) < 8) {
                $this->error('Password must be at least 8 characters long.');
                $password = null;
                continue;
            }

            $confirmPassword = $this->secret('Confirm administrator password');
            if ($password !== $confirmPassword) {
                $this->error('Passwords do not match. Please try again.');
                $password = null;
                continue;
            }
        }

        $this->newLine();
        $this->info('Creating administrator account...');

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'timezone' => 'Europe/Berlin',
        ]);

        $this->newLine();
        $this->info('==================================================');
        $this->info('  Success! Administrator account created.');
        $this->info('  Email: ' . $user->email);
        $this->info('==================================================');
        $this->newLine();

        return self::SUCCESS;
    }
}
