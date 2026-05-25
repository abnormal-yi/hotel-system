<x-layouts.app title="Issue Smart Key">
    <div class="max-w-2xl mx-auto space-y-6">
        <div><h1 class="page-title">Issue New Key</h1></div>
        <div class="card p-6">
            <form method="POST" action="{{ route('smart-keys.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="label">Room</label>
                    <select name="room_id" class="input-field" required>
                        <option value="">Select Room</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}">Room {{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="label">Key Type</label>
                    <select name="type" class="input-field" required>
                        <option value="pin">PIN Code</option>
                        <option value="rfid">RFID Card</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="label">Expires At (optional)</label>
                    <input type="date" name="expires_at" class="input-field">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">Generate Key</button>
                    <a href="{{ route('smart-keys.index') }}" class="btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
