<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmartKeyController extends Controller
{
    public function index()
    {
        $keys = DB::table('smart_keys')
            ->join('rooms', 'smart_keys.room_id', '=', 'rooms.id')
            ->select('smart_keys.*', 'rooms.room_number')
            ->orderBy('smart_keys.created_at', 'desc')
            ->paginate(20);
        return view('smart-keys.index', compact('keys'));
    }

    public function create()
    {
        $rooms = DB::table('rooms')->get();
        return view('smart-keys.form', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'type' => 'required|in:pin,rfid',
            'expires_at' => 'nullable|date',
        ]);

        $data['code'] = $data['type'] === 'pin'
            ? str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT)
            : 'RFID-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $data['issued_by'] = auth()->id();
        $data['status'] = 'active';

        DB::table('smart_keys')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        return redirect()->route('smart-keys.index')->with('success', 'Smart key created.');
    }

    public function toggle($id)
    {
        $key = DB::table('smart_keys')->where('id', $id)->first();
        abort_unless($key, 404);
        $newStatus = $key->status === 'active' ? 'deactivated' : 'active';
        DB::table('smart_keys')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);
        return redirect()->route('smart-keys.index')->with('success', 'Key ' . $newStatus . '.');
    }
}
