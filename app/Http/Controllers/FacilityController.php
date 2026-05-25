<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = DB::table('facilities')->orderBy('name')->get();
        return view('facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('facilities.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        DB::table('facilities')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        return redirect()->route('facilities.index')->with('success', 'Facility created.');
    }

    public function edit($id)
    {
        $facility = DB::table('facilities')->where('id', $id)->first();
        abort_unless($facility, 404);
        return view('facilities.form', compact('facility'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        DB::table('facilities')->where('id', $id)->update($data + ['updated_at' => now()]);
        return redirect()->route('facilities.index')->with('success', 'Facility updated.');
    }

    public function destroy($id)
    {
        DB::table('facilities')->where('id', $id)->delete();
        return redirect()->route('facilities.index')->with('success', 'Facility deleted.');
    }
}
