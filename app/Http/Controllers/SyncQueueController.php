<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncQueueController extends Controller
{
    public function index()
    {
        $items = DB::table('sync_queue')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $stats = [
            'pending' => DB::table('sync_queue')->where('status', 'pending')->count(),
            'processing' => DB::table('sync_queue')->where('status', 'processing')->count(),
            'completed' => DB::table('sync_queue')->where('status', 'completed')->count(),
            'failed' => DB::table('sync_queue')->where('status', 'failed')->count(),
        ];
        return view('sync-queue.index', compact('items', 'stats'));
    }

    public function retry($id)
    {
        DB::table('sync_queue')->where('id', $id)->update([
            'status' => 'pending',
            'retries' => 0,
            'updated_at' => now(),
        ]);
        return redirect()->route('sync-queue.index')->with('success', 'Item queued for retry.');
    }

    public function clearCompleted()
    {
        DB::table('sync_queue')->where('status', 'completed')->delete();
        return redirect()->route('sync-queue.index')->with('success', 'Completed items cleared.');
    }
}
