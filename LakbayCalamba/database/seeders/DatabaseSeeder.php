<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            AdminSeeder::class,
        ]);

        // Optional: create one or more test tourist users
        User::factory()->count(5)->create();
    }
}
