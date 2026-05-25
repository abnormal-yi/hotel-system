<x-layouts.app title="Booking Calendar">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Booking Calendar</h1>
                <p class="page-description">Visual 30-day timeline of room occupancy</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1.5 text-sm"><span class="inline-block w-3 h-3 rounded-sm bg-blue-500"></span> Confirmed</span>
                <span class="flex items-center gap-1.5 text-sm"><span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span> Checked In</span>
                <a href="{{ route('bookings.create') }}" class="btn-primary">New Booking</a>
            </div>
        </div>

        @php
            $rooms = App\Models\Room::with('roomType')->get();
            $today = \Carbon\Carbon::today();
            $dates = [];
            for ($i = 0; $i < 30; $i++) {
                $dates[] = $today->copy()->addDays($i);
            }

            $bookingMap = [];
            foreach ($bookings as $booking) {
                foreach ($booking->rooms as $room) {
                    $pivotCheckIn = $room->pivot?->check_in ?? $booking->check_in;
                    $pivotCheckOut = $room->pivot?->check_out ?? $booking->check_out;
                    $bookingMap[$room->id][] = [
                        'booking' => $booking,
                        'check_in' => \Carbon\Carbon::parse($pivotCheckIn)->startOfDay(),
                        'check_out' => \Carbon\Carbon::parse($pivotCheckOut)->startOfDay(),
                    ];
                }
            }
        @endphp

        <div class="card overflow-hidden">
            <div class="card-content p-0 overflow-x-auto">
                <div class="min-w-max" style="min-width: max-content;">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-muted/50">
                                <th class="sticky left-0 bg-muted/50 z-10 text-left px-4 py-2.5 text-sm font-semibold text-muted-foreground border-r border-border min-w-[200px] shadow-[2px_0_6px_-2px_rgba(0,0,0,0.08)]">Room</th>
                                @foreach($dates as $date)
                                    <th class="px-1.5 py-2.5 text-center text-xs font-medium text-muted-foreground border-r border-border min-w-[40px] {{ $date->isToday() ? 'bg-primary/5' : '' }}">
                                        <div class="text-[10px] uppercase tracking-wider">{{ $date->format('D') }}</div>
                                        <div class="text-sm font-bold {{ $date->isToday() ? 'text-primary' : '' }}">{{ $date->format('d') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                                @php
                                    $roomBookings = $bookingMap[$room->id] ?? [];
                                @endphp
                                <tr class="border-b border-border hover:bg-muted/20 transition-colors">
                                    <td class="sticky left-0 bg-white z-10 px-4 py-3 border-r border-border shadow-[2px_0_6px_-2px_rgba(0,0,0,0.08)]">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-sm">{{ $room->room_number }}</span>
                                            <span class="text-xs text-muted-foreground bg-muted px-1.5 py-0.5 rounded">{{ $room->roomType?->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    @foreach($dates as $date)
                                        @php
                                            $matchingBooking = null;
                                            $isFirstDay = false;
                                            $current = $date->copy()->startOfDay();
                                            foreach ($roomBookings as $rb) {
                                                if ($current->gte($rb['check_in']) && $current->lt($rb['check_out'])) {
                                                    $matchingBooking = $rb['booking'];
                                                    $isFirstDay = $current->eq($rb['check_in']);
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <td class="px-0 py-0 border-r border-border {{ $date->isToday() ? 'bg-primary/5' : '' }}">
                                            @if($matchingBooking)
                                                @php
                                                    $isLastDay = $current->copy()->addDay()->gte($matchingBooking->check_out ?? $current);
                                                    $name = $matchingBooking->guests->first()?->name ?? '';
                                                    $initials = collect(explode(' ', $name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('');
                                                @endphp
                                                <div class="h-full min-h-[32px] flex items-center px-1 {{ $matchingBooking->status === 'checked_in' ? 'bg-green-100' : 'bg-blue-100' }} {{ $isFirstDay ? 'rounded-l-md' : '' }} {{ $isLastDay ? 'rounded-r-md' : '' }}">
                                                    <span class="truncate text-[11px] leading-tight font-medium {{ $matchingBooking->status === 'checked_in' ? 'text-green-800' : 'text-blue-800' }} {{ $isFirstDay ? '' : 'hidden sm:inline' }}">
                                                        <a href="{{ route('bookings.show', $matchingBooking) }}" class="hover:underline">{{ $isFirstDay ? $name : $initials }}</a>
                                                    </span>
                                                    <span class="sm:hidden text-[10px] font-bold {{ $matchingBooking->status === 'checked_in' ? 'text-green-800' : 'text-blue-800' }}">{{ $initials }}</span>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="31" class="px-4 py-8 text-center text-muted-foreground">No rooms found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
