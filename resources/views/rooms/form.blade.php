<x-layouts.app title="{{ isset($room) ? 'Edit Room' : 'Add Room' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="page-title">{{ isset($room) ? 'Edit Room' : 'Add Room' }}</h1>
            <p class="page-description">{{ isset($room) ? 'Update room details' : 'Register a new room' }}</p>
        </div>
        <div class="card">
            <div class="card-content">
                <form method="POST" action="{{ isset($room) ? route('rooms.update', $room) : route('rooms.store') }}" class="space-y-4">
                    @csrf @if(isset($room)) @method('PUT') @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="room_number">Room Number *</label>
                            <input id="room_number" name="room_number" class="input-field" value="{{ old('room_number', $room->room_number ?? '') }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="floor">Floor</label>
                            <input id="floor" name="floor" type="number" class="input-field" value="{{ old('floor', $room->floor ?? '') }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="room_type_id">Room Type *</label>
                            <select id="room_type_id" name="room_type_id" class="input-field" required>
                                @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" @selected(old('room_type_id', $room->room_type_id ?? '') == $type->id)>{{ $type->name }} ({{ number_format($type->base_price) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="status">Status</label>
                            <select id="status" name="status" class="input-field">
                                @foreach(['available','maintenance'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $room->status ?? 'available') === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-stone-400">Occupied/reserved/cleaning are set automatically by the system.</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="label" for="custom_price">Custom Price (leave empty to use type price)</label>
                        <input id="custom_price" name="custom_price" type="number" step="0.01" class="input-field" value="{{ old('custom_price', $room->custom_price ?? '') }}">
                    </div>
                    <div class="space-y-2">
                        <label class="label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="input-field h-20">{{ old('notes', $room->notes ?? '') }}</textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">{{ isset($room) ? 'Update Room' : 'Create Room' }}</button>
                        <a href="{{ route('rooms.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
