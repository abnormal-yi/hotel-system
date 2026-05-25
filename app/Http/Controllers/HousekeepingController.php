<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HousekeepingController extends Controller
{
    public function index()
    {
        $tasks = DB::table('housekeeping_tasks')
            ->leftJoin('rooms', 'housekeeping_tasks.room_id', '=', 'rooms.id')
            ->leftJoin('users as assigned', 'housekeeping_tasks.assigned_to', '=', 'assigned.id')
            ->leftJoin('users as creator', 'housekeeping_tasks.assigned_by', '=', 'creator.id')
            ->select(
                'housekeeping_tasks.*',
                'rooms.room_number',
                'assigned.name as assigned_name',
                'creator.name as creator_name'
            )
            ->orderBy('housekeeping_tasks.created_at', 'desc')
            ->paginate(15);
        return view('housekeeping.index', compact('tasks'));
    }

    public function create()
    {
        $rooms = DB::table('rooms')->whereIn('status', ['occupied', 'available', 'cleaning'])->get();
        $staff = DB::table('users')->whereIn('role', ['manager', 'receptionist'])->get();
        return view('housekeeping.form', compact('rooms', 'staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'task_type' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $data['status'] = 'pending';
        $data['assigned_by'] = auth()->id();

        $room = DB::table('rooms')->where('id', $data['room_id'])->first();
        $data['room_number'] = $room->room_number ?? null;

        DB::table('housekeeping_tasks')->insert($data);
        return redirect()->route('housekeeping.index')->with('success', 'Housekeeping task created.');
    }

    public function show($id)
    {
        $task = DB::table('housekeeping_tasks')
            ->leftJoin('rooms', 'housekeeping_tasks.room_id', '=', 'rooms.id')
            ->leftJoin('users as assigned', 'housekeeping_tasks.assigned_to', '=', 'assigned.id')
            ->leftJoin('users as creator', 'housekeeping_tasks.assigned_by', '=', 'creator.id')
            ->select(
                'housekeeping_tasks.*',
                'rooms.room_number',
                'assigned.name as assigned_name',
                'creator.name as creator_name'
            )
            ->where('housekeeping_tasks.id', $id)
            ->first();
        abort_unless($task, 404);
        return view('housekeeping.show', compact('task'));
    }

    public function edit($id)
    {
        $task = DB::table('housekeeping_tasks')->where('id', $id)->first();
        abort_unless($task, 404);
        $rooms = DB::table('rooms')->get();
        $staff = DB::table('users')->whereIn('role', ['manager', 'receptionist'])->get();
        return view('housekeeping.form', compact('task', 'rooms', 'staff'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'task_type' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
            DB::table('rooms')->where('id', $data['room_id'])->update(['status' => 'available']);
        }

        DB::table('housekeeping_tasks')->where('id', $id)->update($data);
        return redirect()->route('housekeeping.index')->with('success', 'Task updated.');
    }

    public function destroy($id)
    {
        DB::table('housekeeping_tasks')->where('id', $id)->delete();
        return redirect()->route('housekeeping.index')->with('success', 'Task deleted.');
    }
}
