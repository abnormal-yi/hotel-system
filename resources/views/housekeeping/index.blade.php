<x-layouts.app title="Housekeeping">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Housekeeping Tasks</h1>
                <p class="page-description">Manage cleaning and maintenance tasks</p>
            </div>
            <a href="{{ route('housekeeping.create') }}" class="btn-primary">New Task</a>
        </div>

        @session('success') <div class="text-sm text-green-800 bg-green-100 rounded-lg p-3">{{ $value }}</div> @endsession

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Room #</th>
                            <th class="table-header-cell">Task Type</th>
                            <th class="table-header-cell">Priority</th>
                            <th class="table-header-cell">Assigned To</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell">Created</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $task?->room_number ?? $task->room_id }}</td>
                            <td class="table-cell">{{ ucfirst($task->task_type) }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $task->priority === 'high' ? 'badge-danger' : ($task->priority === 'medium' ? 'badge-warning' : 'badge-default') }}">{{ ucfirst($task->priority) }}</span>
                            </td>
                            <td class="table-cell">{{ $task?->assigned_name ?? $task?->assigned_to ?? 'Unassigned' }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-info' : 'badge-warning') }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                            </td>
                            <td class="table-cell text-sm">{{ $task->created_at }}</td>
                            <td class="table-cell text-right">
                                <a href="{{ route('housekeeping.show', $task->id) }}" class="btn-ghost btn-sm">View</a>
                                <a href="{{ route('housekeeping.edit', $task->id) }}" class="btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('housekeeping.destroy', $task->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="table-cell text-center text-muted-foreground py-8">No tasks found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $tasks->links() }}
    </div>
</x-layouts.app>
