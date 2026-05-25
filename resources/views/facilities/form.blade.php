<x-layouts.app title="{{ isset($facility) ? 'Edit Facility' : 'Add Facility' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div><h1 class="page-title">{{ isset($facility) ? 'Edit Facility' : 'Add Facility' }}</h1></div>
        <div class="card p-6">
            <form method="POST" action="{{ isset($facility) ? route('facilities.update', $facility->id) : route('facilities.store') }}" class="space-y-4">
                @csrf
                @if(isset($facility)) @method('PUT') @endif
                <div class="space-y-2">
                    <label class="label">Name</label>
                    <input type="text" name="name" class="input-field" value="{{ $facility->name ?? '' }}" required>
                </div>
                <div class="space-y-2">
                    <label class="label">Icon</label>
                    <input type="text" name="icon" class="input-field" value="{{ $facility->icon ?? '' }}" placeholder="emoji or icon name">
                </div>
                <div class="space-y-2">
                    <label class="label">Description</label>
                    <textarea name="description" class="input-field" rows="3">{{ $facility->description ?? '' }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">{{ isset($facility) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('facilities.index') }}" class="btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
