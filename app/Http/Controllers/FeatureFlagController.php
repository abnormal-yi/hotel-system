<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeatureFlagController extends Controller
{
    public function index()
    {
        $flags = DB::table('feature_flags')->orderBy('key')->get();
        return view('feature-flags.index', compact('flags'));
    }

    public function toggle(Request $request, $id)
    {
        $flag = DB::table('feature_flags')->where('id', $id)->first();
        abort_unless($flag, 404);
        DB::table('feature_flags')->where('id', $id)->update([
            'enabled' => !$flag->enabled,
        ]);
        return redirect()->route('feature-flags.index')->with('success', 'Feature flag toggled.');
    }
}
