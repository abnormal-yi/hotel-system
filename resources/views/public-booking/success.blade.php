<x-layouts.guest title="Booking Confirmed">
    <div class="w-full max-w-2xl mx-auto text-center">
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 dark:text-green-400">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold tracking-tight">Booking Confirmed!</h1>
            <p class="text-muted-foreground mt-2">Your reservation has been received. We look forward to hosting you.</p>
        </div>

        <div class="card mb-6 text-left">
            <div class="card-content space-y-4">
                <div class="text-center pb-4 border-b border-border">
                    <p class="text-xs text-muted-foreground uppercase tracking-wider mb-1">Booking Number</p>
                    <p class="text-2xl font-bold tracking-mono">{{ $booking?->booking_number ?? 'N/A' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-muted-foreground uppercase tracking-wider">Check In</p>
                        <p class="font-semibold">{{ $booking?->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('l, d M Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground uppercase tracking-wider">Check Out</p>
                        <p class="font-semibold">{{ $booking?->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('l, d M Y') : 'N/A' }}</p>
                    </div>
                </div>

                <div class="border-t border-border pt-4">
                    <p class="text-xs text-muted-foreground uppercase tracking-wider mb-2">Room Details</p>
                    @foreach($booking?->rooms ?? [] as $room)
                        <div class="flex items-center justify-between py-1">
                            <span class="font-medium">{{ $room?->room_number ?? 'N/A' }}</span>
                            <span class="text-sm text-muted-foreground">{{ $room?->roomType?->name ?? 'N/A' }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-border pt-4">
                    <p class="text-xs text-muted-foreground uppercase tracking-wider mb-1">Total Amount</p>
                    <p class="text-3xl font-bold text-primary">TZS {{ number_format($booking?->total_amount ?? 0, 0) }}</p>
                </div>

                <div class="border-t border-border pt-4">
                    <p class="text-xs text-muted-foreground uppercase tracking-wider mb-2">Guest Information</p>
                    <p class="font-medium">{{ $booking?->guests->first()?->name ?? 'N/A' }}</p>
                    @if($booking?->guests->first()?->email)
                        <p class="text-sm text-muted-foreground">{{ $booking->guests->first()->email }}</p>
                    @endif
                    @if($booking?->guests->first()?->phone)
                        <p class="text-sm text-muted-foreground">{{ $booking->guests->first()->phone }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-muted bg-muted/30 px-6 py-4 mb-6">
            <h3 class="font-semibold text-sm mb-2">Check-in Instructions</h3>
            <p class="text-sm text-muted-foreground">Please present your booking number at the front desk upon arrival. Check-in time is from 2:00 PM and check-out is before 11:00 AM.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('public-booking.index') }}" class="btn-primary">Make Another Booking</a>
            <button onclick="window.print()" class="btn-ghost">Print Confirmation</button>
        </div>
    </div>
</x-layouts.guest>
