<x-layouts.app title="Bookings">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Bookings</h1>
                <p class="page-description">Manage guest bookings and reservations</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="btn-primary">New Booking</a>
        </div>

        @session('success') <div class="text-sm text-green-800 bg-green-100 rounded-lg p-3">{{ $value }}</div> @endsession

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Booking #</th>
                            <th class="table-header-cell">Guest</th>
                            <th class="table-header-cell">Room</th>
                            <th class="table-header-cell">Check In</th>
                            <th class="table-header-cell">Check Out</th>
                            <th class="table-header-cell">Amount</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $booking->booking_number }}</td>
                            <td class="table-cell">{{ $booking->guests->first()?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $booking->rooms->first()?->room_number ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $booking->check_in }}</td>
                            <td class="table-cell">{{ $booking->check_out }}</td>
                            <td class="table-cell">{{ number_format($booking->total_amount) }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $booking->status === 'checked_in' ? 'badge-success' : ($booking->status === 'checked_out' ? 'badge-default' : ($booking->status === 'cancelled' ? 'badge-danger' : ($booking->status === 'confirmed' ? 'badge-info' : 'badge-warning'))) }}">
                                    {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                                </span>
                            </td>
                            <td class="table-cell text-right">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn-ghost btn-sm">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="table-cell text-center text-muted-foreground py-8">No bookings found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
