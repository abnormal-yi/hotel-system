<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CCTVController extends Controller
{
    public function index()
    {
        $cameras = DB::table('cctv_cameras')->orderBy('name')->get();
        return view('cctv.index', compact('cameras'));
    }

    public function create()
    {
        return view('cctv.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'stream_url' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);
        $data['status'] = 'offline';
        DB::table('cctv_cameras')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        return redirect()->route('cctv.index')->with('success', 'Camera added.');
    }

    public function edit($id)
    {
        $camera = DB::table('cctv_cameras')->where('id', $id)->first();
        abort_unless($camera, 404);
        return view('cctv.form', compact('camera'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'stream_url' => 'nullable|string|max:500',
            'status' => 'required|in:online,offline',
            'notes' => 'nullable|string',
        ]);
        DB::table('cctv_cameras')->where('id', $id)->update($data + ['updated_at' => now()]);
        return redirect()->route('cctv.index')->with('success', 'Camera updated.');
    }

    public function destroy($id)
    {
        DB::table('cctv_cameras')->where('id', $id)->delete();
        return redirect()->route('cctv.index')->with('success', 'Camera removed.');
    }
}
