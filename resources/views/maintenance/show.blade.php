<x-layouts.app title="Request Details">
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Request Details</h1>
                <p class="page-description">{{ $request->title }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('maintenance.edit', $request->id) }}" class="btn-primary">Edit</a>
                <a href="{{ route('maintenance.index') }}" class="btn-ghost">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-content space-y-4">
                <div>
                    <label class="label">Title</label>
                    <p class="text-lg font-medium">{{ $request->title }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Room</label>
                        <p>{{ $request->room_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="label">Priority</label>
                        <span class="badge @switch($request->priority)
                            @case('urgent') badge-danger @break
                            @case('high') badge-warning @break
                            @case('medium') badge-default @break
                            @default badge-ghost @endswitch">
                            {{ ucfirst($request->priority) }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="label">Description</label>
                    <p class="text-muted-foreground">{{ $request->description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Status</label>
                        <span class="badge @switch($request->status)
                            @case('resolved') badge-success @break
                            @case('in_progress') badge-primary @break
                            @default badge-ghost @endswitch">
                            {{ str_replace('_', ' ', ucfirst($request->status)) }}
                        </span>
                    </div>
                    <div>
                        <label class="label">Assigned To</label>
                        <p>{{ $request->assignee_name ?? 'Unassigned' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Created At</label>
                        <p>{{ $request->created_at }}</p>
                    </div>
                    <div>
                        <label class="label">Resolved At</label>
                        <p>{{ $request->resolved_at ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
