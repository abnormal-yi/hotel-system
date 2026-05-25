<x-layouts.app title="Sync Queue">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Sync Queue</h1>
                <p class="page-description">Offline sync queue items</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('sync-queue.clear') }}" method="POST" onsubmit="return confirm('Clear all completed items?')">
                    @csrf
                    <button type="submit" class="btn-ghost">Clear Completed</button>
                </form>
                <a href="{{ route('dashboard') }}" class="btn-ghost">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div class="card">
                <div class="card-content text-center py-4">
                    <p class="text-3xl font-bold">{{ $stats['pending'] }}</p>
                    <p class="label">Pending</p>
                </div>
            </div>
            <div class="card">
                <div class="card-content text-center py-4">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['processing'] }}</p>
                    <p class="label">Processing</p>
                </div>
            </div>
            <div class="card">
                <div class="card-content text-center py-4">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                    <p class="label">Completed</p>
                </div>
            </div>
            <div class="card">
                <div class="card-content text-center py-4">
                    <p class="text-3xl font-bold text-red-600">{{ $stats['failed'] }}</p>
                    <p class="label">Failed</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table">
                <div class="table-header">
                    <div class="table-header-cell">ID</div>
                    <div class="table-header-cell">Table</div>
                    <div class="table-header-cell">Record ID</div>
                    <div class="table-header-cell">Action</div>
                    <div class="table-header-cell">Status</div>
                    <div class="table-header-cell">Retries</div>
                    <div class="table-header-cell">Created</div>
                    <div class="table-header-cell">Actions</div>
                </div>
                @forelse ($items as $item)
                    <div class="table-row">
                        <div class="table-cell">{{ $item->id }}</div>
                        <div class="table-cell">{{ $item->table_name }}</div>
                        <div class="table-cell">{{ $item->record_id ?? '-' }}</div>
                        <div class="table-cell">{{ $item->action }}</div>
                        <div class="table-cell">
                            <span class="badge @switch($item->status)
                                @case('pending') badge-warning @break
                                @case('processing') badge-info @break
                                @case('completed') badge-success @break
                                @case('failed') badge-danger @break
                            @endswitch">{{ $item->status }}</span>
                        </div>
                        <div class="table-cell">{{ $item->retries }}</div>
                        <div class="table-cell">{{ $item->created_at }}</div>
                        <div class="table-cell">
                            @if ($item->status === 'failed')
                                <form action="{{ route('sync-queue.retry', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-primary text-sm">Retry</button>
                                </form>
                            @else
                                <span class="text-muted-foreground text-sm">-</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="table-row">
                        <div class="table-cell text-center text-muted-foreground" colspan="8">No sync queue items found.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</x-layouts.app>
