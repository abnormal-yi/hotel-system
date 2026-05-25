<x-layouts.app title="{{ isset($item) ? 'Edit Item' : 'Add Item' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="page-title">{{ isset($item) ? 'Edit Item' : 'Add Item' }}</h1>
            <p class="page-description">{{ isset($item) ? 'Update inventory item details' : 'Add a new inventory item' }}</p>
        </div>
        <div class="card">
            <div class="card-content">
                <form method="POST" action="{{ isset($item) ? route('inventory.update', $item->id) : route('inventory.store') }}" class="space-y-4">
                    @csrf @if(isset($item)) @method('PUT') @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="name">Name *</label>
                            <input id="name" name="name" class="input-field" value="{{ old('name', $item->name ?? '') }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="category">Category</label>
                            <input id="category" name="category" class="input-field" value="{{ old('category', $item->category ?? '') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="quantity">Quantity *</label>
                            <input id="quantity" name="quantity" type="number" min="0" class="input-field" value="{{ old('quantity', $item->quantity ?? 0) }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="reorder_level">Reorder Level *</label>
                            <input id="reorder_level" name="reorder_level" type="number" min="0" class="input-field" value="{{ old('reorder_level', $item->reorder_level ?? 10) }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="unit_price">Unit Price *</label>
                            <input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="input-field" value="{{ old('unit_price', $item->unit_price ?? 0) }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="unit">Unit *</label>
                            <select id="unit" name="unit" class="input-field" required>
                                @foreach(['pcs', 'boxes', 'bottles', 'kg', 'liters', 'packs'] as $u)
                                <option value="{{ $u }}" @selected(old('unit', $item->unit ?? '') === $u)>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="input-field h-20">{{ old('notes', $item->notes ?? '') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">{{ isset($item) ? 'Update Item' : 'Create Item' }}</button>
                        <a href="{{ route('inventory.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
