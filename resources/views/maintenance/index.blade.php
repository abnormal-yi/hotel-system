<x-layouts.app title="Maintenance">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Maintenance Requests</h1>
                <p class="page-description">Track and manage maintenance tasks</p>
            </div>
            <a href="{{ route('maintenance.create') }}" class="btn-primary">New Request</a>
        </div>

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Title</th>
                            <th class="table-header-cell">Room</th>
                            <th class="table-header-cell">Priority</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell">Assignee</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $r)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $r->title }}</td>
                            <td class="table-cell">{{ $r->room_number ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="badge @switch($r->priority)
                                    @case('urgent') badge-danger @break
                                    @case('high') badge-warning @break
                                    @case('medium') badge-default @break
                                    @default badge-ghost @endswitch">
                                    {{ ucfirst($r->priority) }}
                                </span>
                            </td>
                            <td class="table-cell">
                                <span class="badge @switch($r->status)
                                    @case('resolved') badge-success @break
                                    @case('in_progress') badge-primary @break
                                    @default badge-ghost @endswitch">
                                    {{ str_replace('_', ' ', ucfirst($r->status)) }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $r->assignee_name ?? 'Unassigned' }}</td>
                            <td class="table-cell text-right">
                                <a href="{{ route('maintenance.show', $r->id) }}" class="btn-ghost btn-sm">View</a>
                                <a href="{{ route('maintenance.edit', $r->id) }}" class="btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('maintenance.destroy', $r->id) }}" class="inline" onsubmit="return confirm('Delete this request?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="table-cell text-center text-muted-foreground py-8">No maintenance requests found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div>{{ $requests->links() }}</div>
    </div>
</x-layouts.app>
