<x-layouts.app title="{{ isset($task) ? 'Edit Task' : 'New Task' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="page-title">{{ isset($task) ? 'Edit Task' : 'New Task' }}</h1>
            <p class="page-description">{{ isset($task) ? 'Update housekeeping task details' : 'Create a new housekeeping task' }}</p>
        </div>
        <div class="card">
            <div class="card-content">
                <form method="POST" action="{{ isset($task) ? route('housekeeping.update', $task->id) : route('housekeeping.store') }}" class="space-y-4">
                    @csrf
                    @if(isset($task)) @method('PUT') @endif

                    <div class="space-y-2">
                        <label class="label" for="room_id">Room *</label>
                        <select id="room_id" name="room_id" class="input-field" required>
                            <option value="">Select a room</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id', $task?->room_id ?? '') == $room->id)>
                                Room {{ $room->room_number }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label" for="task_type">Task Type *</label>
                        <select id="task_type" name="task_type" class="input-field" required>
                            <option value="">Select task type</option>
                            @foreach(['cleaning', 'maintenance', 'inspection', 'laundry', 'repair'] as $type)
                            <option value="{{ $type }}" @selected(old('task_type', $task?->task_type ?? '') === $type)>
                                {{ ucfirst($type) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label" for="priority">Priority *</label>
                        <select id="priority" name="priority" class="input-field" required>
                            <option value="low" @selected(old('priority', $task?->priority ?? '') === 'low')>Low</option>
                            <option value="medium" @selected(old('priority', $task?->priority ?? '') === 'medium')>Medium</option>
                            <option value="high" @selected(old('priority', $task?->priority ?? '') === 'high')>High</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label" for="assigned_to">Assigned To</label>
                        <select id="assigned_to" name="assigned_to" class="input-field">
                            <option value="">Unassigned</option>
                            @foreach($staff as $member)
                            <option value="{{ $member->id }}" @selected(old('assigned_to', $task?->assigned_to ?? '') == $member->id)>
                                {{ $member->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="input-field h-20">{{ old('notes', $task?->notes ?? '') }}</textarea>
                    </div>

                    @if(isset($task))
                    <div class="space-y-2">
                        <label class="label" for="status">Status</label>
                        <select id="status" name="status" class="input-field">
                            <option value="pending" @selected(old('status', $task?->status ?? '') === 'pending')>Pending</option>
                            <option value="in_progress" @selected(old('status', $task?->status ?? '') === 'in_progress')>In Progress</option>
                            <option value="completed" @selected(old('status', $task?->status ?? '') === 'completed')>Completed</option>
                        </select>
                    </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">{{ isset($task) ? 'Update Task' : 'Create Task' }}</button>
                        <a href="{{ route('housekeeping.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
