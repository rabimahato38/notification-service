<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL');
        $password = env('SEED_ADMIN_PASSWORD');

        if (! filled($email) || ! filled($password)) {
            return;
        }

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('SEED_ADMIN_NAME', 'Admin User'),
                'password' => Hash::make($password),
            ],
        );
    }
}
