<x-layouts.app title="{{ isset($camera) ? 'Edit Camera' : 'Add Camera' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div><h1 class="page-title">{{ isset($camera) ? 'Edit Camera' : 'Add Camera' }}</h1></div>
        <div class="card p-6">
            <form method="POST" action="{{ isset($camera) ? route('cctv.update', $camera->id) : route('cctv.store') }}" class="space-y-4">
                @csrf
                @if(isset($camera)) @method('PUT') @endif
                <div class="space-y-2">
                    <label class="label">Camera Name</label>
                    <input type="text" name="name" class="input-field" value="{{ $camera->name ?? '' }}" required>
                </div>
                <div class="space-y-2">
                    <label class="label">Location</label>
                    <input type="text" name="location" class="input-field" value="{{ $camera->location ?? '' }}" required>
                </div>
                <div class="space-y-2">
                    <label class="label">Stream URL (optional)</label>
                    <input type="url" name="stream_url" class="input-field" value="{{ $camera->stream_url ?? '' }}" placeholder="https:// or rtsp://">
                </div>
                @if(isset($camera))
                <div class="space-y-2">
                    <label class="label">Status</label>
                    <select name="status" class="input-field">
                        <option value="online" {{ $camera->status === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ $camera->status === 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </div>
                @endif
                <div class="space-y-2">
                    <label class="label">Notes</label>
                    <textarea name="notes" class="input-field" rows="2">{{ $camera->notes ?? '' }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">{{ isset($camera) ? 'Update' : 'Add Camera' }}</button>
                    <a href="{{ route('cctv.index') }}" class="btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
