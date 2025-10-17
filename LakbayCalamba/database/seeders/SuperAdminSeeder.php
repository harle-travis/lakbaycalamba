<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@lakbay.com'],
            [
                'lakbay_id' => 'LAK-' . Str::ulid()->toBase32(),
                'name' => 'Super Admin',
                'password' => Hash::make('angat_haters'),
                'role' => 'superadmin',
            ]
        );
    }
}
