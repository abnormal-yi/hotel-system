<x-layouts.guest title="Book a Room">
    <div class="w-full max-w-5xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold tracking-tight">{{ $hotel?->name ?? 'Inshotel' }}</h1>
            <p class="text-muted-foreground mt-2 text-lg">{{ $hotel?->address ?? 'Book your perfect stay with us' }}</p>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-950/20 dark:border-red-800 px-4 py-3 mb-6 text-sm text-red-800 dark:text-red-300">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-8">
            <div class="card-content">
                <h2 class="card-title text-xl mb-4">Book Your Stay</h2>
                <form id="bookingForm" action="{{ route('public-booking.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel?->id ?? 1 }}">
                    <input type="hidden" name="room_id" id="selectedRoomId">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label" for="check_in">Check In</label>
                            <input type="date" name="check_in" id="check_in" class="input-field" value="{{ old('check_in') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label class="label" for="check_out">Check Out</label>
                            <input type="date" name="check_out" id="check_out" class="input-field" value="{{ old('check_out') }}" required>
                        </div>
                        <div>
                            <label class="label" for="room_type_id">Room Type</label>
                            <select name="room_type_id" id="room_type_id" class="input-field">
                                <option value="">All Room Types</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }} - TZS {{ number_format($type->base_price, 0) }}/night</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label" for="guest_name">Full Name</label>
                            <input type="text" name="guest_name" id="guest_name" class="input-field" value="{{ old('guest_name') }}" required>
                        </div>
                        <div>
                            <label class="label" for="guest_phone">Phone</label>
                            <input type="text" name="guest_phone" id="guest_phone" class="input-field" value="{{ old('guest_phone') }}">
                        </div>
                        <div>
                            <label class="label" for="guest_email">Email</label>
                            <input type="email" name="guest_email" id="guest_email" class="input-field" value="{{ old('guest_email') }}">
                        </div>
                        <div>
                            <label class="label" for="guest_id_number">ID Number</label>
                            <input type="text" name="guest_id_number" id="guest_id_number" class="input-field" value="{{ old('guest_id_number') }}">
                        </div>
                    </div>
                    <div>
                        <label class="label" for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="input-field" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <div id="availableRooms" class="hidden">
                        <label class="label">Select a Room</label>
                        <div id="roomList" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1"></div>
                    </div>

                    <button type="submit" class="btn-primary w-full" id="submitBtn" disabled>Complete Booking</button>
                </form>
            </div>
        </div>

        <h2 class="section-header text-xl mb-4">Our Rooms</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($roomTypes as $type)
                <div class="card">
                    <div class="card-content">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="card-title text-lg">{{ $type->name }}</h3>
                                <p class="text-muted-foreground text-sm mt-1">{{ $type->description ?? 'No description available' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-primary">TZS {{ number_format($type->base_price, 0) }}</p>
                                <p class="text-xs text-muted-foreground">per night</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="badge badge-info">{{ $type->max_guests ?? 1 }} Guest{{ ($type->max_guests ?? 1) > 1 ? 's' : '' }}</span>
                            <span class="badge badge-success">{{ $type->rooms->count() }} Room{{ $type->rooms->count() !== 1 ? 's' : '' }}</span>
                        </div>
                        @if($type->amenities && count($type->amenities) > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($type->amenities as $amenity)
                                    <span class="inline-block text-xs bg-muted px-2 py-0.5 rounded-full text-muted-foreground">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-8 text-muted-foreground">No room types available</div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');
        const roomTypeSelect = document.getElementById('room_type_id');
        const availableRooms = document.getElementById('availableRooms');
        const roomList = document.getElementById('roomList');
        const selectedRoomId = document.getElementById('selectedRoomId');
        const submitBtn = document.getElementById('submitBtn');

        function fetchAvailability() {
            if (!checkIn.value || !checkOut.value) return;
            const formData = new FormData();
            formData.append('check_in', checkIn.value);
            formData.append('check_out', checkOut.value);
            formData.append('room_type_id', roomTypeSelect.value || '');

            fetch('{{ route('public-booking.availability') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            })
            .then(r => r.json())
            .then(rooms => {
                if (rooms.length === 0) {
                    availableRooms.classList.remove('hidden');
                    roomList.innerHTML = '<p class="text-sm text-muted-foreground col-span-full py-4 text-center">No rooms available for selected dates.</p>';
                    submitBtn.disabled = true;
                    selectedRoomId.value = '';
                    return;
                }
                availableRooms.classList.remove('hidden');
                roomList.innerHTML = rooms.map(r => `
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-border cursor-pointer hover:bg-muted/50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5"
                           onclick="document.querySelectorAll('.room-option').forEach(e => e.checked = false); this.querySelector('.room-option').checked = true; selectedRoomId.value = '${r.id}'; submitBtn.disabled = false;">
                        <input type="radio" name="_room_select" class="room-option sr-only" value="${r.id}" data-room='${JSON.stringify(r)}'>
                        <div class="flex-1 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-sm">${r.room_number}</span>
                                <span class="text-xs text-muted-foreground ml-2">${r.room_type?.name || ''}</span>
                            </div>
                            <span class="text-sm font-semibold">${r.custom_price ? '$' + parseFloat(r.custom_price).toFixed(2) : ''}</span>
                        </div>
                    </label>
                `).join('');
            });
        }

        checkIn.addEventListener('change', fetchAvailability);
        checkOut.addEventListener('change', fetchAvailability);
        roomTypeSelect.addEventListener('change', fetchAvailability);

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('room-option')) {
                const room = JSON.parse(e.target.dataset.room);
                selectedRoomId.value = room.id;
                submitBtn.disabled = false;
            }
        });
    </script>
    @endpush
</x-layouts.guest>
