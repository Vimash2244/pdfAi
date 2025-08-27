<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@pdfparseai.com',
            'password' => Hash::make('password'),
            'role' => 'super',
            'email_verified_at' => now(),
        ]);

        // Create a regular user for testing
        User::create([
            'name' => 'Test User',
            'email' => 'user@pdfparseai.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
