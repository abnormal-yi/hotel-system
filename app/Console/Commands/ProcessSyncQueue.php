<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessSyncQueue extends Command
{
    protected $signature = 'sync:process {--limit=50 : Items to process per run}';
    protected $description = 'Process pending offline sync queue items';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $items = DB::table('sync_queue')
            ->where('status', 'pending')
            ->where('retries', '<', 5)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        if ($items->isEmpty()) {
            $this->info('No pending items to process.');
            return 0;
        }

        $processed = 0;
        $failed = 0;

        foreach ($items as $item) {
            DB::table('sync_queue')->where('id', $item->id)->update(['status' => 'processing']);

            try {
                $payload = json_decode($item->payload, true) ?? [];

                match ($item->action) {
                    'create' => !empty($payload) ? DB::table($item->table_name)->insert($payload) : null,
                    'update' => (!empty($payload) && $item->record_id) ? DB::table($item->table_name)->where('id', $item->record_id)->update($payload) : null,
                    'delete' => $item->record_id ? DB::table($item->table_name)->where('id', $item->record_id)->delete() : null,
                    default => throw new \Exception("Unknown action: {$item->action}"),
                };

                DB::table('sync_queue')->where('id', $item->id)->update([
                    'status' => 'completed',
                    'synced_at' => now(),
                ]);
                $this->line("  ✓ Processed: {$item->table_name}#{$item->record_id} ({$item->action})");
                $processed++;
            } catch (\Exception $e) {
                $newRetries = $item->retries + 1;
                $newStatus = $newRetries >= 5 ? 'failed' : 'pending';
                DB::table('sync_queue')->where('id', $item->id)->update([
                    'status' => $newStatus,
                    'retries' => $newRetries,
                ]);
                $this->error("  ✗ Failed: {$item->table_name}#{$item->record_id} ({$item->action}): {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("Processed: {$processed} succeeded, {$failed} failed.");
        return $failed > 0 ? 1 : 0;
    }
}
