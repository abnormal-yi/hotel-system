<x-layouts.app title="{{ isset($roomType) ? 'Edit Room Type' : 'New Room Type' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div><h1 class="page-title">{{ isset($roomType) ? 'Edit Room Type' : 'New Room Type' }}</h1></div>
        <div class="card p-6">
            <form method="POST" action="{{ isset($roomType) ? route('room-types.update', $roomType->id) : route('room-types.store') }}" class="space-y-4">
                @csrf
                @if(isset($roomType)) @method('PUT') @endif
                <div class="space-y-2">
                    <label class="label">Name</label>
                    <input type="text" name="name" class="input-field" value="{{ $roomType->name ?? '' }}" required>
                </div>
                <div class="space-y-2">
                    <label class="label">Base Price</label>
                    <input type="number" name="base_price" class="input-field" step="0.01" value="{{ $roomType->base_price ?? '' }}" required>
                </div>
                <div class="space-y-2">
                    <label class="label">Max Guests</label>
                    <input type="number" name="max_guests" class="input-field" value="{{ $roomType->max_guests ?? '2' }}" required>
                </div>
                <div class="space-y-2">
                    <label class="label">Amenities</label>
                    <textarea name="amenities" class="input-field" rows="3">{{ $roomType->amenities ?? '' }}</textarea>
                </div>
                <div class="space-y-2">
                    <label class="label">Description</label>
                    <textarea name="description" class="input-field" rows="3">{{ $roomType->description ?? '' }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">{{ isset($roomType) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('room-types.index') }}" class="btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
