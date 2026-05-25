<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index()
    {
        $requests = DB::table('maintenance_requests')
            ->leftJoin('rooms', 'maintenance_requests.room_id', '=', 'rooms.id')
            ->leftJoin('users', 'maintenance_requests.assigned_to', '=', 'users.id')
            ->select('maintenance_requests.*', 'rooms.room_number', 'users.name as assignee_name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('maintenance.index', compact('requests'));
    }

    public function create()
    {
        $rooms = Room::where('is_active', true)->get();
        $staff = User::whereIn('role', ['creator', 'manager'])->get();
        return view('maintenance.form', compact('rooms', 'staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        DB::table('maintenance_requests')->insert($data + [
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('maintenance.index')->with('success', 'Request created.');
    }

    public function show($id)
    {
        $request = DB::table('maintenance_requests')
            ->leftJoin('rooms', 'maintenance_requests.room_id', '=', 'rooms.id')
            ->leftJoin('users', 'maintenance_requests.assigned_to', '=', 'users.id')
            ->select('maintenance_requests.*', 'rooms.room_number', 'users.name as assignee_name')
            ->where('maintenance_requests.id', $id)
            ->first();
        abort_unless($request, 404);
        return view('maintenance.show', compact('request'));
    }

    public function edit($id)
    {
        $req = DB::table('maintenance_requests')->where('id', $id)->first();
        abort_unless($req, 404);
        $rooms = Room::where('is_active', true)->get();
        $staff = User::whereIn('role', ['creator', 'manager'])->get();
        return view('maintenance.form', compact('req', 'rooms', 'staff'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,resolved',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $updateData = $data + ['updated_at' => now()];
        if ($data['status'] === 'resolved') {
            $updateData['resolved_at'] = now();
        }
        DB::table('maintenance_requests')->where('id', $id)->update($updateData);
        return redirect()->route('maintenance.index')->with('success', 'Request updated.');
    }

    public function destroy($id)
    {
        DB::table('maintenance_requests')->where('id', $id)->delete();
        return redirect()->route('maintenance.index')->with('success', 'Request removed.');
    }
}
