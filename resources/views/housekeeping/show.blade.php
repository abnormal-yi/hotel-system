<x-layouts.app title="Task #{{ $task->id }}">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Task #{{ $task->id }}</h1>
                <p class="page-description">{{ ucfirst($task->task_type) }} task details</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('housekeeping.edit', $task->id) }}" class="btn-primary btn-sm">Edit</a>
                <a href="{{ route('housekeeping.index') }}" class="btn-ghost btn-sm">Back to List</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h2 class="card-title">Task Information</h2></div>
            <div class="card-content">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Room</p>
                        <p class="font-medium">{{ $task?->room_number ?? $task->room_id }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Task Type</p>
                        <p class="font-medium">{{ ucfirst($task->task_type) }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Priority</p>
                        <span class="badge {{ $task->priority === 'high' ? 'badge-danger' : ($task->priority === 'medium' ? 'badge-warning' : 'badge-default') }}">{{ ucfirst($task->priority) }}</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Status</p>
                        <span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Assigned To</p>
                        <p class="font-medium">{{ $task?->assigned_name ?? $task?->assigned_to ?? 'Unassigned' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">Created</p>
                        <p class="font-medium">{{ $task->created_at }}</p>
                    </div>
                    @if($task?->notes)
                    <div class="space-y-1 col-span-2">
                        <p class="text-sm text-muted-foreground">Notes</p>
                        <p class="font-medium">{{ $task->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
