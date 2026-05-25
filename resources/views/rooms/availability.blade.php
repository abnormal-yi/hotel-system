<x-layouts.app title="Room Availability">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Room Availability</h1>
                <p class="page-description">Check room availability by date range</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="btn-primary">New Booking</a>
        </div>

        <div class="card">
            <div class="card-header">
                <form method="GET" action="{{ route('rooms.availability') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="label">Check In</label>
                        <input type="date" name="check_in" class="input-field" value="{{ $checkIn ?? '' }}" required>
                    </div>
                    <div>
                        <label class="label">Check Out</label>
                        <input type="date" name="check_out" class="input-field" value="{{ $checkOut ?? '' }}" required>
                    </div>
                    <button type="submit" class="btn-primary">Check Availability</button>
                </form>
            </div>
        </div>

        @if(isset($rooms) && count($rooms))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($rooms as $room)
            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Room {{ $room->room_number }}</h3>
                        <span class="badge badge-success">Available</span>
                    </div>
                    <p class="text-sm text-muted-foreground">{{ $room->roomType->name ?? 'N/A' }} - Floor {{ $room->floor }}</p>
                    <p class="text-lg font-bold mt-2">{{ number_format($room->custom_price ?? $room->roomType->base_price ?? 0) }} <span class="text-sm font-normal text-muted-foreground">/night</span></p>
                    <a href="{{ route('bookings.create', ['room_id' => $room->id, 'check_in' => $checkIn, 'check_out' => $checkOut]) }}" class="btn-primary btn-sm w-full mt-4 text-center">Book Now</a>
                </div>
            </div>
            @endforeach
        </div>
        @elseif(isset($rooms))
        <div class="card">
            <div class="card-content text-center py-8 text-muted-foreground">
                No available rooms found for the selected dates.
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>
