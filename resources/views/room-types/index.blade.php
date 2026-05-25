<x-layouts.app title="Room Types">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div><h1 class="page-title">Room Types</h1><p class="page-description">Manage room categories</p></div>
            <a href="{{ route('room-types.create') }}" class="btn-primary">New Room Type</a>
        </div>
        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Name</th>
                            <th class="table-header-cell">Base Price</th>
                            <th class="table-header-cell">Max Guests</th>
                            <th class="table-header-cell">Amenities</th>
                            <th class="table-header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roomTypes as $rt)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $rt->name }}</td>
                            <td class="table-cell">{{ number_format($rt->base_price) }}</td>
                            <td class="table-cell">{{ $rt->max_guests }}</td>
                            <td class="table-cell text-sm text-muted-foreground">{{ Str::limit($rt->amenities ?? '-', 40) }}</td>
                            <td class="table-cell">
                                <a href="{{ route('room-types.edit', $rt->id) }}" class="btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('room-types.destroy', $rt->id) }}" class="inline" onsubmit="return confirm('Delete this room type?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-destructive">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-4 text-center text-muted-foreground">No room types yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
