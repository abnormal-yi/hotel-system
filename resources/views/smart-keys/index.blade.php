<x-layouts.app title="Smart Keys">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div><h1 class="page-title">Smart Keys</h1><p class="page-description">Digital keys and PIN codes</p></div>
            <a href="{{ route('smart-keys.create') }}" class="btn-primary">Issue Key</a>
        </div>
        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Room</th>
                            <th class="table-header-cell">Type</th>
                            <th class="table-header-cell">Code</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell">Issued</th>
                            <th class="table-header-cell">Expires</th>
                            <th class="table-header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keys as $k)
                        <tr class="table-row">
                            <td class="table-cell font-medium">Room {{ $k->room_number }}</td>
                            <td class="table-cell">{{ strtoupper($k->type) }}</td>
                            <td class="table-cell font-mono">{{ $k->code }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $k->status === 'active' ? 'badge-success' : ($k->status === 'deactivated' ? 'badge-default' : 'badge-destructive') }}">{{ $k->status }}</span>
                            </td>
                            <td class="table-cell text-sm">{{ $k->created_at ? substr($k->created_at, 0, 10) : '-' }}</td>
                            <td class="table-cell text-sm">{{ $k->expires_at ?? 'Never' }}</td>
                            <td class="table-cell">
                                <form method="POST" action="{{ route('smart-keys.toggle', $k->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-ghost btn-sm">{{ $k->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-4 text-center text-muted-foreground">No smart keys issued</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $keys->links() }}
    </div>
</x-layouts.app>
