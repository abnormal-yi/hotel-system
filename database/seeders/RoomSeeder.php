<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $hotelId = 1;

        for ($i = 1; $i <= 10; $i++) {
            Room::create([
                'hotel_id' => $hotelId,
                'room_type_id' => 1,
                'room_number' => '10' . $i,
                'floor' => 1,
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        for ($i = 1; $i <= 6; $i++) {
            Room::create([
                'hotel_id' => $hotelId,
                'room_type_id' => 2,
                'room_number' => '20' . $i,
                'floor' => 2,
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            Room::create([
                'hotel_id' => $hotelId,
                'room_type_id' => 3,
                'room_number' => '30' . $i,
                'floor' => 3,
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        for ($i = 1; $i <= 2; $i++) {
            Room::create([
                'hotel_id' => $hotelId,
                'room_type_id' => 4,
                'room_number' => '40' . $i,
                'floor' => 4,
                'status' => 'available',
                'is_active' => true,
            ]);
        }
    }
}
