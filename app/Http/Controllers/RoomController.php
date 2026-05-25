<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType', 'hotel')->orderBy('room_number')->paginate(30);

        return view('rooms.index', compact('rooms'));
    }

    public function show(Room $room)
    {
        $room->load('roomType', 'hotel');

        return view('rooms.show', compact('room'));
    }

    public function create()
    {
        $roomTypes = RoomType::all();

        return view('rooms.form', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50',
            'floor' => 'nullable|string|max:10',
            'status' => 'required|in:available,booked,occupied,maintenance,cleaning',
            'custom_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $room = Room::create($validated);

        activity()->causedBy(auth()->user())->log('Created room: ' . $room->room_number);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        $roomTypes = RoomType::all();

        return view('rooms.form', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50',
            'floor' => 'nullable|string|max:10',
            'status' => 'required|in:available,booked,occupied,maintenance,cleaning',
            'custom_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $room->update($validated);

        activity()->causedBy(auth()->user())->log('Updated room: ' . $room->room_number);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        activity()->causedBy(auth()->user())->log('Deleted room: ' . $room->room_number);

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
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

        if ($request->expectsJson()) {
            return response()->json($rooms);
        }

        return view('rooms.availability', compact('rooms', 'checkIn', 'checkOut'));
    }

}
