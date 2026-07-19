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
        // Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Regular User
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Dummy transactions for the user
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'income',
            'amount' => 5000000,
            'description' => 'Gaji Bulan Ini',
            'transaction_date' => now()->startOfMonth(),
        ]);
        
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 150000,
            'description' => 'Makan Siang',
            'transaction_date' => now(),
        ]);
    }
}
