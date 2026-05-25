<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        RoomType::create([
            'name' => 'Standard',
            'base_price' => 80000,
            'max_guests' => 2,
            'amenities' => ['TV', 'WiFi', 'AC'],
        ]);

        RoomType::create([
            'name' => 'Deluxe',
            'base_price' => 150000,
            'max_guests' => 3,
            'amenities' => ['TV', 'WiFi', 'AC', 'Mini Bar', 'Safe'],
        ]);

        RoomType::create([
            'name' => 'Suite',
            'base_price' => 250000,
            'max_guests' => 4,
            'amenities' => ['TV', 'WiFi', 'AC', 'Mini Bar', 'Safe', 'Living Room', 'Jacuzzi'],
        ]);

        RoomType::create([
            'name' => 'VIP',
            'base_price' => 500000,
            'max_guests' => 6,
            'amenities' => ['TV', 'WiFi', 'AC', 'Mini Bar', 'Safe', 'Living Room', 'Jacuzzi', 'Butler Service'],
        ]);
    }
}
