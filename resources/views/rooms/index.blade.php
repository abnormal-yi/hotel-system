<x-layouts.app title="Rooms">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Rooms</h1>
                <p class="page-description">Manage hotel rooms and their status</p>
            </div>
            @can('view-room-types')
            <a href="{{ route('rooms.create') }}" class="btn-primary">Add Room</a>
            @endcan
        </div>

        @session('success') <div class="text-sm text-green-800 bg-green-100 rounded-lg p-3">{{ $value }}</div> @endsession
        @session('error') <div class="text-sm text-red-800 bg-red-100 rounded-lg p-3">{{ $value }}</div> @endsession

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Room #</th>
                            <th class="table-header-cell">Type</th>
                            <th class="table-header-cell">Floor</th>
                            <th class="table-header-cell">Price</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $room->room_number }}</td>
                            <td class="table-cell">{{ $room->roomType->name ?? 'N/A' }}</td>
                            <td class="table-cell">Floor {{ $room->floor }}</td>
                            <td class="table-cell">{{ number_format($room->custom_price ?? $room->roomType->base_price ?? 0) }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $room->status === 'available' ? 'badge-success' : ($room->status === 'occupied' ? 'badge-danger' : ($room->status === 'reserved' ? 'badge-warning' : ($room->status === 'cleaning' ? 'badge-info' : 'badge-default'))) }}">
                                    {{ $room->status === 'reserved' ? 'Reserved' : ucfirst($room->status) }}
                                </span>
                            </td>
                            <td class="table-cell text-right">
                                <a href="{{ route('rooms.edit', $room) }}" class="btn-ghost btn-sm">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="table-cell text-center text-muted-foreground py-8">No rooms found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
