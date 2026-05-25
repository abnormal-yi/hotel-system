<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = DB::table('room_types')->orderBy('name')->get();
        return view('room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        $hotels = DB::table('hotels')->get();
        return view('room-types.form', compact('hotels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
        ]);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('room_types')->insert($data);
        return redirect()->route('room-types.index')->with('success', 'Room type created.');
    }

    public function edit($id)
    {
        $roomType = DB::table('room_types')->where('id', $id)->first();
        abort_unless($roomType, 404);
        return view('room-types.form', compact('roomType'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
        ]);
        $data['updated_at'] = now();
        DB::table('room_types')->where('id', $id)->update($data);
        return redirect()->route('room-types.index')->with('success', 'Room type updated.');
    }

    public function destroy($id)
    {
        DB::table('room_types')->where('id', $id)->delete();
        return redirect()->route('room-types.index')->with('success', 'Room type deleted.');
    }
}
