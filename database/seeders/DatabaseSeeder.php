<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndUserSeeder::class,
            HotelSeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
            FeatureFlagSeeder::class,
            SystemSettingSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
