<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        // Facilities
        $facilities = [
            ['name' => 'Swimming Pool', 'icon' => '🏊', 'description' => 'Outdoor heated swimming pool open 6AM-10PM'],
            ['name' => 'Fitness Center', 'icon' => '💪', 'description' => 'Modern gym with cardio and weight equipment'],
            ['name' => 'Restaurant', 'icon' => '🍽️', 'description' => 'Fine dining restaurant serving local and international cuisine'],
            ['name' => 'Spa & Wellness', 'icon' => '🧖', 'description' => 'Full-service spa with massage, sauna, and steam room'],
            ['name' => 'Conference Room', 'icon' => '💼', 'description' => 'Fully equipped meeting room for up to 50 guests'],
            ['name' => 'Parking', 'icon' => '🅿️', 'description' => 'Secure parking with 24/7 surveillance'],
            ['name' => 'Free WiFi', 'icon' => '📶', 'description' => 'High-speed internet throughout the hotel'],
            ['name' => 'Airport Shuttle', 'icon' => '🚌', 'description' => 'Complimentary airport pickup and drop-off'],
        ];
        foreach ($facilities as $f) {
            DB::table('facilities')->insert($f + ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
        }
        $this->command->info('Facilities seeded.');

        // Guests
        $guests = [
            ['name' => 'John Mwangi', 'email' => 'john@example.com', 'phone' => '+255712100001', 'id_number' => 'ID-1001', 'address' => 'Dar es Salaam'],
            ['name' => 'Sarah Kimani', 'email' => 'sarah@example.com', 'phone' => '+255712100002', 'id_number' => 'ID-1002', 'address' => 'Nairobi'],
            ['name' => 'Peter Ochieng', 'email' => 'peter@example.com', 'phone' => '+255712100003', 'id_number' => 'ID-1003', 'address' => 'Mombasa'],
            ['name' => 'Grace Mtama', 'email' => 'grace@example.com', 'phone' => '+255712100004', 'id_number' => 'ID-1004', 'address' => 'Arusha'],
            ['name' => 'David Johnson', 'email' => 'david@example.com', 'phone' => '+255712100005', 'id_number' => 'ID-1005', 'address' => 'Lagos'],
            ['name' => 'Amina Hassan', 'email' => 'amina@example.com', 'phone' => '+255712100006', 'id_number' => 'ID-1006', 'address' => 'Zanzibar'],
            ['name' => 'James Ouma', 'email' => 'james@example.com', 'phone' => '+255712100007', 'id_number' => 'ID-1007', 'address' => 'Kampala'],
            ['name' => 'Mary Njoroge', 'email' => 'mary@example.com', 'phone' => '+255712100008', 'id_number' => 'ID-1008', 'address' => 'Nyeri'],
            ['name' => 'Robert Kisaka', 'email' => 'robert@example.com', 'phone' => '+255712100009', 'id_number' => 'ID-1009', 'address' => 'Dodoma'],
            ['name' => 'Elizabeth Mushi', 'email' => 'elizabeth@example.com', 'phone' => '+255712100010', 'id_number' => 'ID-1010', 'address' => 'Mbeya'],
            ['name' => 'Thomas Mboya', 'email' => 'thomas@example.com', 'phone' => '+255712100011', 'id_number' => 'ID-1011', 'address' => 'Kisumu'],
            ['name' => 'Catherine Wanjiku', 'email' => 'catherine@example.com', 'phone' => '+255712100012', 'id_number' => 'ID-1012', 'address' => 'Nakuru'],
        ];
        $guestIds = [];
        foreach ($guests as $g) {
            $guestIds[] = DB::table('guests')->insertGetId($g + ['created_at' => $now, 'updated_at' => $now]);
        }
        $this->command->info('Guests seeded.');

        $rooms = DB::table('rooms')->pluck('id', 'room_number');

        // Bookings (various statuses across dates)
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_in', 'checked_out', 'cancelled'];
        $revenueByStatus = ['pending' => 0, 'confirmed' => 0.3, 'checked_in' => 0.6, 'checked_out' => 1.0, 'cancelled' => 0];
        $methods = ['cash', 'mobile_money', 'card', 'bank_transfer'];

        // Force specific statuses for today's bookings so dashboard shows real data
        $todayStatuses = ['checked_in', 'checked_in', 'checked_in', 'confirmed', 'pending'];

        $bookings = [];
        $pivotRooms = [];
        $pivotGuests = [];
        $guestIdx = 0;

        for ($i = 1; $i <= 15; $i++) {
            $status = $i <= 5 ? $todayStatuses[$i - 1] : $statuses[array_rand($statuses)];
            $daysAgo = $i <= 5 ? 0 : rand(1, 20);
            $nights = $i === 1 ? 3 : rand(1, 4);
            $checkIn = now()->subDays($daysAgo);
            $checkOut = (clone $checkIn)->addDays($nights);
            $roomId = $rooms->random();
            $roomTypeId = DB::table('rooms')->where('id', $roomId)->value('room_type_id');
            $basePrice = DB::table('room_types')->where('id', $roomTypeId)->value('base_price');
            $totalAmount = $basePrice * $nights;

            $bookingNumber = 'BK-' . str_pad(1000 + $i, 5, '0', STR_PAD_LEFT);
            $bookingId = DB::table('bookings')->insertGetId([
                'booking_number' => $bookingNumber,
                'hotel_id' => 1,
                'user_id' => 1,
                'booking_type' => 'walk-in',
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => $status,
                'total_amount' => $totalAmount,
                'paid_amount' => $totalAmount * ($revenueByStatus[$status] ?? 0.5),
                'source' => ['walk-in', 'phone', 'email', 'online'][rand(0, 3)],
                'notes' => null,
                'created_at' => $checkIn,
                'updated_at' => $now,
            ]);

            DB::table('booking_room')->insert([
                'booking_id' => $bookingId,
                'room_id' => $roomId,
                'price' => $basePrice,
                'created_at' => $checkIn,
                'updated_at' => $checkIn,
            ]);

            // Update room status to match booking state
            if ($status === 'checked_in') {
                DB::table('rooms')->where('id', $roomId)->update(['status' => 'occupied', 'updated_at' => $now]);
            } elseif ($status === 'checked_out') {
                DB::table('rooms')->where('id', $roomId)->update(['status' => 'available', 'updated_at' => $now]);
            } elseif ($status === 'booked') {
                DB::table('rooms')->where('id', $roomId)->update(['status' => 'reserved', 'updated_at' => $now]);
            }

            $gId = $guestIds[$guestIdx % count($guestIds)];
            DB::table('booking_guest')->insert([
                'booking_id' => $bookingId,
                'guest_id' => $gId,
                'is_primary' => true,
                'created_at' => $checkIn,
                'updated_at' => $checkIn,
            ]);
            $guestIdx++;

            // Payments for checked_out and some checked_in
            if (in_array($status, ['checked_out', 'checked_in'])) {
                DB::table('payments')->insert([
                    'booking_id' => $bookingId,
                    'user_id' => 1,
                    'amount' => $totalAmount,
                    'method' => $methods[array_rand($methods)],
                    'reference' => 'PAY-' . strtoupper(substr(md5($bookingId), 0, 8)),
                    'notes' => null,
                    'paid_at' => $checkIn,
                    'created_at' => $checkIn,
                    'updated_at' => $checkIn,
                ]);
            }
        }
        $this->command->info('Bookings and payments seeded.');

        // EFD Transactions
        $paymentIds = DB::table('payments')->pluck('id');
        foreach ($paymentIds as $pid) {
            $payment = DB::table('payments')->where('id', $pid)->first();
            DB::table('efd_transactions')->insert([
                'receipt_number' => 'EFD-' . strtoupper(substr(md5($pid . 'xyz'), 0, 10)),
                'payable_type' => 'App\\Models\\Payment',
                'payable_id' => $pid,
                'amount' => $payment->amount,
                'vat' => $payment->amount * 0.18,
                'tin' => '123-456-789',
                'status' => 'completed',
                'created_at' => $payment->created_at,
                'updated_at' => $payment->created_at,
            ]);
        }
        $this->command->info('EFD transactions seeded.');

        // Smart Keys
        $roomSamples = $rooms->random(5);
        foreach ($roomSamples as $rmId) {
            $pin = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            DB::table('smart_keys')->insert([
                'room_id' => $rmId,
                'type' => 'pin',
                'code' => $pin,
                'status' => 'active',
                'issued_by' => 1,
                'activated_at' => $now,
                'expires_at' => (clone $now)->addDays(rand(1, 7)),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        DB::table('smart_keys')->insert([
            'room_id' => $rooms->random(),
            'type' => 'rfid',
            'code' => 'RFID-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'status' => 'deactivated',
            'issued_by' => 1,
            'activated_at' => (clone $now)->subDays(5),
            'expires_at' => (clone $now)->subDays(1),
            'created_at' => (clone $now)->subDays(5),
            'updated_at' => (clone $now)->subDays(1),
        ]);
        $this->command->info('Smart keys seeded.');

        // Feature flags
        DB::table('feature_flags')->insert([
            'key' => 'smart_key',
            'label' => 'Smart Keys',
            'enabled' => false,
            'module' => 'access',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->command->info('Feature flags seeded.');

        // CCTV Cameras
        $cameras = [
            ['name' => 'Lobby Main', 'location' => 'Ground Floor - Lobby', 'stream_url' => null, 'status' => 'online'],
            ['name' => 'Parking Lot A', 'location' => 'Outdoor - Main Entrance', 'stream_url' => null, 'status' => 'online'],
            ['name' => 'Restaurant', 'location' => 'Ground Floor - Restaurant', 'stream_url' => null, 'status' => 'online'],
            ['name' => 'Pool Area', 'location' => 'Rooftop - Pool Deck', 'stream_url' => null, 'status' => 'offline'],
            ['name' => 'Corridor 2F', 'location' => '2nd Floor - East Wing', 'stream_url' => null, 'status' => 'online'],
            ['name' => 'Reception Desk', 'location' => 'Ground Floor - Front Desk', 'stream_url' => null, 'status' => 'online'],
        ];
        foreach ($cameras as $c) {
            DB::table('cctv_cameras')->insert($c + ['created_at' => $now, 'updated_at' => $now]);
        }
        $this->command->info('CCTV cameras seeded.');
    }
}
