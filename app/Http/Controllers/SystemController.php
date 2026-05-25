<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class SystemController extends Controller
{
    public function settings()
    {
        $settings = DB::table('system_settings')->get()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
        return redirect()->route('settings.index')->with('success', 'Settings saved.');
    }

    public function activityLog()
    {
        $logs = Activity::with('causer')->latest()->paginate(50);
        return view('activity-log.index', compact('logs'));
    }
}
