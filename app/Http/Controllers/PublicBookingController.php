<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicBookingController extends Controller
{
    public function index()
    {
        $hotel = Hotel::first();
        $roomTypes = RoomType::with(['rooms' => function($q) {
            $q->where('status', 'available')->where('is_active', true);
        }])->get();
        return view('public-booking.index', compact('hotel', 'roomTypes'));
    }

    public function checkAvailability(Request $request)
    {
        $checkIn = $request->date('check_in');
        $checkOut = $request->date('check_out');
        $roomTypeId = $request->input('room_type_id');

        $query = Room::with('roomType')->where('status', 'available')->where('is_active', true);
        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        }

        if ($checkIn && $checkOut) {
            $bookedRoomIds = DB::table('booking_room')
                ->join('bookings', 'booking_room.booking_id', '=', 'bookings.id')
                ->whereIn('bookings.status', ['confirmed', 'checked_in'])
                ->where(function ($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('booking_room.check_in', [$checkIn, $checkOut])
                        ->orWhereBetween('booking_room.check_out', [$checkIn, $checkOut])
                        ->orWhere(function ($sq) use ($checkIn, $checkOut) {
                            $sq->where('booking_room.check_in', '<=', $checkIn)
                              ->where('booking_room.check_out', '>=', $checkOut);
                        });
                })
                ->pluck('booking_room.room_id')
                ->unique();
            $query->whereNotIn('id', $bookedRoomIds);
        }

        $rooms = $query->get();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'hotel_id' => 'required|exists:hotels,id',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string|max:50',
            'guest_email' => 'nullable|email|max:255',
            'guest_id_number' => 'nullable|string|max:50',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'notes' => 'nullable|string|max:1000',
        ]);

        $exists = Room::where('id', $validated['room_id'])
            ->where('status', '!=', 'available')
            ->exists();
        if ($exists) {
            return back()->withErrors(['room_id' => 'Room is no longer available.'])->withInput();
        }

        Room::where('id', $validated['room_id'])->update(['status' => 'reserved']);

        $bookingNumber = 'PB-' . now()->format('Ymd') . '-' . str_pad(Booking::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        $room = Room::with('roomType')->find($validated['room_id']);
        $price = $room->custom_price ?? $room->roomType->base_price;
        $nights = \Carbon\Carbon::parse($validated['check_in'])->diffInDays(\Carbon\Carbon::parse($validated['check_out']));
        $totalAmount = $price * max($nights, 1);

        $booking = Booking::create([
            'booking_number' => $bookingNumber,
            'hotel_id' => $validated['hotel_id'],
            'user_id' => auth()->id() ?? 1,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'status' => 'confirmed',
            'booking_type' => 'walk_in',
            'source' => 'online',
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        $booking->rooms()->attach($validated['room_id'], [
            'price' => $price,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
        ]);

        $guest = Guest::firstOrCreate(
            ['phone' => $validated['guest_phone'] ?? 'unknown-' . uniqid()],
            [
                'name' => $validated['guest_name'],
                'email' => $validated['guest_email'] ?? null,
                'id_number' => $validated['guest_id_number'] ?? null,
            ]
        );
        $booking->guests()->attach($guest->id, ['is_primary' => true]);

        return redirect()->route('public-booking.success', $booking);
    }

    public function success(Booking $booking)
    {
        $booking->load('rooms.roomType');
        return view('public-booking.success', compact('booking'));
    }
}
