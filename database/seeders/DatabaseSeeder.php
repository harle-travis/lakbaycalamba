<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\AdminSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            AdminSeeder::class,
        ]);
        // Example test user (tourist)
        User::factory()->create([
            'lakbay_id' => 'LAK-' . Str::ulid()->toBase32(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'tourist',
        ]);
    }
}
