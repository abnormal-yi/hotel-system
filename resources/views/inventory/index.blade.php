<x-layouts.app title="Inventory">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Inventory Management</h1>
                <p class="page-description">Track stock levels and reorder items</p>
            </div>
            <a href="{{ route('inventory.create') }}" class="btn-primary">Add Item</a>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h2 class="card-title">All Items</h2>
                    <form method="GET" action="{{ route('inventory.index') }}" class="flex gap-2">
                        <input type="text" name="search" class="input-field" placeholder="Search by name or category..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('inventory.index') }}" class="btn-ghost btn-sm">Clear</a>
                        @endif
                        <button type="submit" class="btn-primary btn-sm">Search</button>
                    </form>
                </div>
            </div>
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Name</th>
                            <th class="table-header-cell">Category</th>
                            <th class="table-header-cell">Quantity</th>
                            <th class="table-header-cell">Reorder Level</th>
                            <th class="table-header-cell">Unit Price</th>
                            <th class="table-header-cell">Unit</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr class="table-row {{ $item->quantity <= $item->reorder_level ? 'bg-red-50' : '' }}">
                            <td class="table-cell font-medium">{{ $item->name }}</td>
                            <td class="table-cell">{{ $item->category ?? 'N/A' }}</td>
                            <td class="table-cell">{{ $item->quantity }}</td>
                            <td class="table-cell">{{ $item->reorder_level }}</td>
                            <td class="table-cell">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="table-cell">{{ $item->unit }}</td>
                            <td class="table-cell text-right">
                                <a href="{{ route('inventory.edit', $item->id) }}" class="btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('inventory.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Delete this item?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="table-cell text-center text-muted-foreground py-8">No items found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div>{{ $items->links() }}</div>
    </div>
</x-layouts.app>
