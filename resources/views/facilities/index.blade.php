<x-layouts.app title="Facilities">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div><h1 class="page-title">Facilities</h1><p class="page-description">Hotel amenities and services</p></div>
            <a href="{{ route('facilities.create') }}" class="btn-primary">Add Facility</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($facilities as $f)
            <div class="card p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold">{{ $f->name }}</h3>
                        @if($f->description)<p class="text-sm text-muted-foreground mt-1">{{ $f->description }}</p>@endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('facilities.edit', $f->id) }}" class="btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('facilities.destroy', $f->id) }}" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-ghost btn-sm text-destructive">X</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8 text-muted-foreground">No facilities added yet</div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
