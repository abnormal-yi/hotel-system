<x-layouts.app title="{{ isset($req) ? 'Edit Request' : 'New Request' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="page-title">{{ isset($req) ? 'Edit Request' : 'New Request' }}</h1>
            <p class="page-description">{{ isset($req) ? 'Update maintenance request details' : 'Create a new maintenance request' }}</p>
        </div>
        <div class="card">
            <div class="card-content">
                <form method="POST" action="{{ isset($req) ? route('maintenance.update', $req->id) : route('maintenance.store') }}" class="space-y-4">
                    @csrf @if(isset($req)) @method('PUT') @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="room_id">Room</label>
                            <select id="room_id" name="room_id" class="input-field">
                                <option value="">Select Room</option>
                                @foreach($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id', $req->room_id ?? '') == $room->id)>
                                    {{ $room->room_number }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="priority">Priority *</label>
                            <select id="priority" name="priority" class="input-field" required>
                                @foreach(['low', 'medium', 'high', 'urgent'] as $p)
                                <option value="{{ $p }}" @selected(old('priority', $req->priority ?? '') === $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="label" for="title">Title *</label>
                        <input id="title" name="title" class="input-field" value="{{ old('title', $req->title ?? '') }}" required>
                    </div>

                    <div class="space-y-2">
                        <label class="label" for="description">Description *</label>
                        <textarea id="description" name="description" class="input-field h-24" required>{{ old('description', $req->description ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="assigned_to">Assign To</label>
                            <select id="assigned_to" name="assigned_to" class="input-field">
                                <option value="">Unassigned</option>
                                @foreach($staff as $s)
                                <option value="{{ $s->id }}" @selected(old('assigned_to', $req->assigned_to ?? '') == $s->id)>
                                    {{ $s->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if(isset($req))
                        <div class="space-y-2">
                            <label class="label" for="status">Status *</label>
                            <select id="status" name="status" class="input-field" required>
                                @foreach(['pending', 'in_progress', 'resolved'] as $st)
                                <option value="{{ $st }}" @selected(old('status', $req->status ?? '') === $st)>
                                    {{ str_replace('_', ' ', ucfirst($st)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">{{ isset($req) ? 'Update Request' : 'Create Request' }}</button>
                        <a href="{{ route('maintenance.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
