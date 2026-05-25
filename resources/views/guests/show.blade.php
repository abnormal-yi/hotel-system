<x-layouts.app title="{{ $guest->name }}">
    @php
        $statusBadge = match(true) {
            $guest->blacklisted => 'badge-danger',
            $guest->status === 'vip' => 'badge-warning',
            $guest->status === 'new' => 'badge-default',
            default => 'badge-success',
        };
        $statusLabel = match(true) {
            $guest->blacklisted => 'Blacklisted',
            $guest->status === 'vip' => 'VIP',
            $guest->status === 'new' => 'New',
            default => 'Active',
        };
        $maskedNida = $guest->nida_number
            ? substr($guest->nida_number, 0, 4) . str_repeat('*', max(0, strlen($guest->nida_number) - 8)) . substr($guest->nida_number, -4)
            : null;
    @endphp

    <div class="space-y-6">
        <div class="section-header">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('guests.index') }}" class="text-sm text-stone-400 hover:text-stone-600">&larr; Back</a>
                </div>
                <h1 class="page-title mt-1">{{ $guest->name }}</h1>
                <p class="page-description">Guest since {{ $guest->created_at->format('d M Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('guests.edit', $guest) }}" class="btn-primary btn-sm">Edit Guest</a>
                <a href="{{ route('bookings.create', ['guest_id' => $guest->id]) }}" class="btn-secondary btn-sm">New Booking</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-lg border border-stone-200 bg-white p-4 text-center">
                <p class="text-xs text-stone-500">Total Bookings</p>
                <p class="text-2xl font-bold text-stone-900 mt-1">{{ $guest->total_bookings ?? $guest->bookings->count() }}</p>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-4 text-center">
                <p class="text-xs text-stone-500">Total Spent</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalPaid) }}</p>
                <p class="text-xs text-stone-400">TZS</p>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-4 text-center">
                <p class="text-xs text-stone-500">Outstanding</p>
                <p class="text-2xl font-bold {{ $totalDue > 0 ? 'text-red-600' : 'text-stone-900' }} mt-1">{{ number_format($totalDue) }}</p>
                <p class="text-xs text-stone-400">TZS</p>
            </div>
            <div class="rounded-lg border border-stone-200 bg-white p-4 text-center">
                <p class="text-xs text-stone-500">Last Visit</p>
                <p class="text-sm font-semibold text-stone-900 mt-1">{{ $lastBooking ? $lastBooking->check_in->format('d M Y') : 'First time' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="space-y-6">
                <div class="rounded-lg border border-stone-200 bg-white">
                    <div class="p-5 border-b border-stone-100"><p class="text-sm font-semibold text-stone-900">Personal Information</p></div>
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between text-sm"><span class="text-stone-500">Name</span><span class="text-stone-900 font-medium">{{ $guest->name }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-stone-500">Phone</span><span class="text-stone-900">{{ $guest->phone ?? '-' }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-stone-500">Email</span><span class="text-stone-900">{{ $guest->email ?? '-' }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-stone-500">ID Proof</span><span class="text-stone-900">{{ $guest->id_number ?? '-' }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-stone-500">NIDA</span>
                            <span class="text-stone-900 font-mono text-xs">
                                @if($guest->nida_number)
                                    {{ $hasNidaAccess ? $guest->nida_number : $maskedNida }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-sm"><span class="text-stone-500">Nationality</span><span class="text-stone-900">{{ $guest->nationality ?? '-' }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-stone-500">Address</span><span class="text-stone-900 text-right max-w-[200px]">{{ $guest->address ?? '-' }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-stone-500">Status</span>
                            <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                        </div>
                    </div>
                </div>

                @if($guest->blacklisted)
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-semibold text-red-800">Blacklisted</p>
                    <p class="text-xs text-red-600 mt-1">{{ $guest->blacklist_reason ?? 'No reason provided' }}</p>
                    @if($guest->blacklisted_at)
                    <p class="text-xs text-red-500 mt-1">{{ $guest->blacklisted_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
                @endif
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-lg border border-stone-200 bg-white">
                    <div class="p-5 border-b border-stone-100"><p class="text-sm font-semibold text-stone-900">Booking History</p></div>
                    <div class="p-0">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-header-cell">Booking #</th>
                                    <th class="table-header-cell">Room</th>
                                    <th class="table-header-cell">Check In</th>
                                    <th class="table-header-cell">Check Out</th>
                                    <th class="table-header-cell">Amount</th>
                                    <th class="table-header-cell">Paid</th>
                                    <th class="table-header-cell">Status</th>
                                    <th class="table-header-cell text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guest->bookings as $booking)
                                <tr class="table-row">
                                    <td class="table-cell font-mono text-xs">{{ $booking->booking_number }}</td>
                                    <td class="table-cell">{{ $booking->rooms->first()?->room_number ?? '-' }}</td>
                                    <td class="table-cell text-sm">{{ $booking->check_in instanceof \Carbon\Carbon ? $booking->check_in->format('d M Y') : $booking->check_in }}</td>
                                    <td class="table-cell text-sm">{{ $booking->check_out instanceof \Carbon\Carbon ? $booking->check_out->format('d M Y') : $booking->check_out }}</td>
                                    <td class="table-cell">{{ number_format($booking->total_amount) }}</td>
                                    <td class="table-cell">{{ number_format($booking->paid_amount) }}</td>
                                    <td class="table-cell">
                                        <span class="badge {{ match($booking->status) {
                                            'checked_in' => 'badge-success',
                                            'checked_out' => 'badge-default',
                                            'cancelled' => 'badge-danger',
                                            'confirmed' => 'badge-info',
                                            default => 'badge-warning',
                                        } }}">{{ str_replace('_', ' ', ucfirst($booking->status)) }}</span>
                                    </td>
                                    <td class="table-cell text-right">
                                        <a href="{{ route('bookings.show', $booking) }}" class="btn-ghost btn-sm">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="table-cell text-center text-muted-foreground py-8">No booking history</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
