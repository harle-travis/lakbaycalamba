<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * IMPORTANT: This creates the shared superadmin account that all collaborators use.
     * If you change the password here, all collaborators need to run this seeder again.
     */
    public function run(): void
    {
        //
         User::updateOrCreate(
            ['email' => 'superadmin@lakbay.com'], // Shared account for all collaborators
            [
                'lakbay_id' => 'LAK-' . Str::ulid()->toBase32(),
                'name' => 'Super Admin',
                'password' => Hash::make('angat_haters'),
                'role' => 'superadmin',
            ]
        );
    }
}
