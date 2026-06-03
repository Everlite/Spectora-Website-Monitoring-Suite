<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::create([
            'first_name' => 'Konstantin',
            'last_name' => 'Balzer',
            'email' => 'kbalzer92@outlook.de',
            'password' => \Illuminate\Support\Facades\Hash::make('Morrjes123#2025'),
            'timezone' => 'Europe/Berlin',
        ]);

        $user->is_admin = true;
        $user->save();
    }
}
