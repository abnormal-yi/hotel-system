<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('guests', 'rooms', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(20);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms = Room::with('roomType')->where('is_active', true)->orderBy('room_number')->get();
        $guests = Guest::orderBy('name')->get();
        $roomTypes = RoomType::all();
        $hotels = Hotel::where('is_active', true)->get();
        $booking = null;

        return view('bookings.form', compact('rooms', 'guests', 'roomTypes', 'hotels', 'booking'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'exists:rooms,id',
            'guests' => 'required|array|min:1',
            'guests.*.id' => 'nullable|exists:guests,id',
            'guests.*.name' => 'required_without:guests.*.id|string|max:255',
            'guests.*.phone' => 'nullable|string|max:50',
            'guests.*.email' => 'nullable|email|max:255',
            'guests.*.id_number' => 'nullable|string|max:50',
            'guests.*.address' => 'nullable|string|max:500',
            'guests.*.guest_type' => 'nullable|in:main,companion,child',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'booking_type' => 'required|in:advance,walk_in,group',
            'source' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];

        $bookedRoomIds = DB::table('booking_room')
            ->join('bookings', 'booking_room.booking_id', '=', 'bookings.id')
            ->whereIn('booking_room.room_id', $validated['room_ids'])
            ->whereIn('bookings.status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('booking_room.check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('booking_room.check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('booking_room.check_in', '<=', $checkIn)
                          ->where('booking_room.check_out', '>=', $checkOut);
                    });
            })
            ->pluck('booking_room.room_id')
            ->unique()
            ->toArray();

        $conflictingRooms = array_intersect($validated['room_ids'], $bookedRoomIds);
        if (!empty($conflictingRooms)) {
            $conflictingNames = Room::whereIn('id', $conflictingRooms)->pluck('room_number')->implode(', ');
            return redirect()->back()->withErrors(['room_ids' => 'Selected rooms are not available: ' . $conflictingNames])->withInput();
        }

        $unavailableRooms = Room::whereIn('id', $validated['room_ids'])
            ->where('status', '!=', 'available')
            ->where('is_active', true)
            ->exists();
        if ($unavailableRooms) {
            return redirect()->back()->withErrors(['room_ids' => 'Some selected rooms are not available.'])->withInput();
        }

        if ($validated['booking_type'] !== 'group') {
            foreach ($validated['guests'] as $guestData) {
                if (!empty($guestData['id'])) {
                    $activeBooking = DB::table('booking_guest')
                        ->join('bookings', 'booking_guest.booking_id', '=', 'bookings.id')
                        ->where('booking_guest.guest_id', $guestData['id'])
                        ->whereIn('bookings.status', ['confirmed', 'checked_in'])
                        ->exists();
                    if ($activeBooking) {
                        $guestName = Guest::where('id', $guestData['id'])->value('name');
                        return redirect()->back()
                            ->withErrors(['guests' => "⚠ Guest '{$guestName}' already has active occupancy in another room."])
                            ->withInput();
                    }
                }
            }
        }

        $bookingNumber = 'BK-' . now()->format('Ymd') . '-' . str_pad(Booking::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $totalAmount = 0;
        $roomPivotData = [];
        foreach ($validated['room_ids'] as $roomId) {
            $room = Room::with('roomType')->find($roomId);
            $price = $room->custom_price ?? $room->roomType->base_price;
            $totalAmount += $price;
            $roomPivotData[$roomId] = [
                'price' => $price,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ];
        }

        $booking = Booking::create([
            'booking_number' => $bookingNumber,
            'hotel_id' => $validated['hotel_id'],
            'user_id' => auth()->id(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'confirmed',
            'booking_type' => $validated['booking_type'],
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'source' => $validated['source'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $booking->rooms()->attach($roomPivotData);

        Room::whereIn('id', $validated['room_ids'])->update(['status' => 'reserved']);

        foreach ($validated['guests'] as $i => $guestData) {
            if (!empty($guestData['id'])) {
                $booking->guests()->attach($guestData['id'], ['is_primary' => $i === 0]);
                if (!empty($guestData['guest_type'])) {
                    Guest::where('id', $guestData['id'])->update(['guest_type' => $guestData['guest_type']]);
                }
            } else {
                $guest = Guest::create([
                    'name' => $guestData['name'],
                    'phone' => $guestData['phone'] ?? null,
                    'email' => $guestData['email'] ?? null,
                    'id_number' => $guestData['id_number'] ?? null,
                    'address' => $guestData['address'] ?? null,
                    'guest_type' => $guestData['guest_type'] ?? ($i === 0 ? 'main' : 'companion'),
                ]);
                $booking->guests()->attach($guest->id, ['is_primary' => $i === 0]);
            }
        }

        activity()->causedBy(auth()->user())->log('Created booking: ' . $bookingNumber);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $booking->load('rooms.roomType', 'guests', 'payments', 'invoices', 'user');

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $booking->load('guests', 'rooms.roomType');
        $rooms = Room::with('roomType')->where('is_active', true)->orderBy('room_number')->get();
        $guests = Guest::orderBy('name')->get();
        $roomTypes = RoomType::all();
        $hotels = Hotel::where('is_active', true)->get();

        return view('bookings.form', compact('booking', 'rooms', 'guests', 'roomTypes', 'hotels'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'notes' => 'nullable|string|max:1000',
            'total_amount' => 'nullable|numeric|min:0',
            'room_ids' => 'nullable|array',
            'room_ids.*' => 'exists:rooms,id',
            'booking_type' => 'nullable|in:advance,walk_in,group',
        ]);

        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];
        $bookingType = $validated['booking_type'] ?? $booking->booking_type;

        if ($request->filled('guests') && $bookingType !== 'group') {
            foreach ($request->input('guests') as $guestData) {
                if (!empty($guestData['id'])) {
                    $activeBooking = DB::table('booking_guest')
                        ->join('bookings', 'booking_guest.booking_id', '=', 'bookings.id')
                        ->where('booking_guest.guest_id', $guestData['id'])
                        ->where('booking_guest.booking_id', '!=', $booking->id)
                        ->whereIn('bookings.status', ['confirmed', 'checked_in'])
                        ->exists();
                    if ($activeBooking) {
                        $guestName = Guest::where('id', $guestData['id'])->value('name');
                        return redirect()->back()
                            ->withErrors(['guests' => "⚠ Guest '{$guestName}' already has an active booking."])
                            ->withInput();
                    }
                }
            }
        }

        if ($request->filled('room_ids')) {
            $bookedRoomIds = DB::table('booking_room')
                ->join('bookings', 'booking_room.booking_id', '=', 'bookings.id')
                ->whereIn('booking_room.room_id', $validated['room_ids'])
                ->where('booking_room.booking_id', '!=', $booking->id)
                ->whereIn('bookings.status', ['confirmed', 'checked_in'])
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('booking_room.check_in', [$checkIn, $checkOut])
                        ->orWhereBetween('booking_room.check_out', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('booking_room.check_in', '<=', $checkIn)
                              ->where('booking_room.check_out', '>=', $checkOut);
                        });
                })
                ->pluck('booking_room.room_id')
                ->unique()
                ->toArray();

            $conflictingRooms = array_intersect($validated['room_ids'], $bookedRoomIds);
            if (!empty($conflictingRooms)) {
                $conflictingNames = Room::whereIn('id', $conflictingRooms)->pluck('room_number')->implode(', ');
                return redirect()->back()->withErrors(['room_ids' => 'Some rooms are not available: ' . $conflictingNames])->withInput();
            }

            $totalAmount = 0;
            $roomPivotData = [];
            foreach ($validated['room_ids'] as $roomId) {
                $room = Room::with('roomType')->find($roomId);
                $price = $room->custom_price ?? $room->roomType->base_price;
                $totalAmount += $price;
                $roomPivotData[$roomId] = [
                    'price' => $price,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                ];
            }

            $booking->rooms()->sync($roomPivotData);
            $booking->update(['total_amount' => $totalAmount]);
            Room::whereIn('id', $validated['room_ids'])->where('status', '!=', 'occupied')->update(['status' => 'reserved']);
        } else {
            $booking->update([
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'notes' => $validated['notes'] ?? $booking->notes,
            ]);
        }

        if ($request->filled('total_amount') && $request->user()->role !== 'receptionist') {
            $booking->update(['total_amount' => $validated['total_amount']]);
        }

        activity()->causedBy(auth()->user())->log('Updated booking: ' . $booking->booking_number);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking updated successfully.');
    }

    public function checkin(Booking $booking)
    {
        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        $roomIds = $booking->rooms()->pluck('rooms.id');
        Room::whereIn('id', $roomIds)->update(['status' => 'occupied']);

        activity()->causedBy(auth()->user())->log('Checked in booking: ' . $booking->booking_number);

        return redirect()->route('bookings.show', $booking)->with('success', 'Guest checked in successfully.');
    }

    public function checkout(Booking $booking)
    {
        $booking->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
        ]);

        // Set rooms to 'cleaning' after checkout — housekeeping must mark them clean before next booking
        $roomIds = $booking->rooms()->pluck('rooms.id');
        Room::whereIn('id', $roomIds)->update(['status' => 'cleaning']);

        activity()->causedBy(auth()->user())->log('Checked out booking: ' . $booking->booking_number . '. Rooms set to cleaning.');

        return redirect()->route('bookings.show', $booking)->with('success', 'Guest checked out. Rooms marked for cleaning.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'] ?? null,
        ]);

        $roomIds = $booking->rooms()->pluck('rooms.id');
        Room::whereIn('id', $roomIds)->update(['status' => 'available']);

        activity()->causedBy(auth()->user())->log('Cancelled booking: ' . $booking->booking_number);

        return redirect()->route('bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    public function calendar()
    {
        $bookings = Booking::with('rooms.roomType', 'guests')
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get();

        return view('bookings.calendar', compact('bookings'));
    }

    public function availability(Request $request)
    {
        $checkIn = $request->date('check_in');
        $checkOut = $request->date('check_out');

        $query = Room::with('roomType', 'hotel')->where('status', 'available')->where('is_active', true);

        if ($checkIn && $checkOut) {
            $bookedRoomIds = DB::table('booking_room')
                ->join('bookings', 'booking_room.booking_id', '=', 'bookings.id')
                ->whereIn('bookings.status', ['confirmed', 'checked_in'])
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('booking_room.check_in', [$checkIn, $checkOut])
                        ->orWhereBetween('booking_room.check_out', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('booking_room.check_in', '<=', $checkIn)
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
}
