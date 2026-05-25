<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        Hotel::create([
            'name' => 'Inshotel Beach Resort',
            'address' => '123 Beach Road, Dar es Salaam',
            'phone' => '+255 712 345 678',
            'email' => 'info@inshotel.com',
            'tin_number' => '123-456-789',
            'is_active' => true,
        ]);
    }
}
