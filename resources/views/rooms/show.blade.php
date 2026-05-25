<x-layouts.app title="Room {{ $room->room_number }}">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Room {{ $room->room_number }}</h1>
                <p class="page-description">{{ $room->roomType->name ?? 'Standard' }} &middot; Floor {{ $room->floor }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('rooms.edit', $room) }}" class="btn-primary">Edit Room</a>
                <a href="{{ route('rooms.index') }}" class="btn-ghost">All Rooms</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6">
                <p class="text-xs text-muted-foreground mb-1">Status</p>
                <span class="badge text-sm {{ $room->status === 'available' ? 'badge-success' : ($room->status === 'occupied' ? 'badge-danger' : ($room->status === 'reserved' ? 'badge-warning' : ($room->status === 'cleaning' ? 'badge-info' : 'badge-default'))) }}">
                    {{ $room->status === 'reserved' ? 'Reserved' : ucfirst($room->status) }}
                </span>
            </div>
            <div class="card p-6">
                <p class="text-xs text-muted-foreground mb-1">Price</p>
                <p class="text-2xl font-bold">{{ number_format($room->custom_price ?? $room->roomType->base_price ?? 0) }} TZS</p>
            </div>
            <div class="card p-6">
                <p class="text-xs text-muted-foreground mb-1">Hotel</p>
                <p class="font-medium">{{ $room->hotel->name ?? 'Main' }}</p>
            </div>
        </div>

        @if($room->notes)
        <div class="card p-6">
            <h3 class="font-semibold mb-2">Notes</h3>
            <p class="text-sm text-muted-foreground">{{ $room->notes }}</p>
        </div>
        @endif

        @php
            $recentBookings = \App\Models\Booking::whereHas('rooms', fn($q) => $q->where('rooms.id', $room->id))
                ->with('guests', 'user')
                ->latest()
                ->take(10)
                ->get();
        @endphp
        @if($recentBookings->count())
        <div class="card">
            <div class="card-header"><h2 class="card-title">Recent Bookings</h2></div>
            <div class="card-content p-0">
                <table class="w-full text-sm">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Booking #</th>
                            <th class="table-header-cell">Guest</th>
                            <th class="table-header-cell">Check In</th>
                            <th class="table-header-cell">Check Out</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $b)
                        <tr class="table-row">
                            <td class="table-cell">{{ $b->booking_number }}</td>
                            <td class="table-cell">{{ $b->guests->first()?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $b->check_in }}</td>
                            <td class="table-cell">{{ $b->check_out }}</td>
                            <td class="table-cell text-right">
                                <a href="{{ route('bookings.show', $b) }}" class="btn-ghost btn-sm">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>
