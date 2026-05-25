<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'creator@inshotel.com'],
            ['name' => 'System Creator', 'password' => Hash::make('creator123'), 'role' => 'creator']
        );

        User::firstOrCreate(
            ['email' => 'manager@inshotel.com'],
            ['name' => 'Hotel Manager', 'password' => Hash::make('manager123'), 'role' => 'manager']
        );

        User::firstOrCreate(
            ['email' => 'reception@inshotel.com'],
            ['name' => 'Jane Reception', 'password' => Hash::make('reception123'), 'role' => 'receptionist']
        );
    }
}
