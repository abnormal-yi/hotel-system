<x-layouts.app title="Room Order">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="page-title">Room Order</h1>
            <p class="page-description">Order food and items for a room</p>
        </div>
        <div class="card">
            <div class="card-content">
                <form method="POST" action="{{ route('pos.room-order.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="room_id">Room *</label>
                            <select id="room_id" name="room_id" class="input-field" required>
                                <option value="">Select room...</option>
                                @foreach($rooms as $room)
                                <option value="{{ $room->id }}">Room {{ $room->room_number }} ({{ $room->status }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="guest_id">Guest (optional)</label>
                            <select id="guest_id" name="guest_id" class="input-field">
                                <option value="">Walk-in</option>
                                @foreach($guests as $guest)
                                <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="label">Order Items</label>
                            <button type="button" id="add-item" class="btn-primary btn-sm">+ Add Item</button>
                        </div>

                        <div class="space-y-2">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="text-xs font-medium text-stone-500 mr-2 self-center">Quick add:</span>
                                @foreach($items as $item)
                                <button type="button" class="quick-add text-xs px-2.5 py-1 rounded-lg border border-stone-200 hover:bg-stone-100 text-stone-700 transition-colors"
                                    data-name="{{ $item->name }}"
                                    data-price="{{ $item->price ?? 0 }}">
                                    {{ $item->name }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        <div id="items-container" class="space-y-2">
                            <div class="item-row grid grid-cols-12 gap-2 items-end">
                                <div class="col-span-5 space-y-1">
                                    <label class="label text-xs">Item Name</label>
                                    <input type="text" name="items[0][name]" class="input-field item-name text-sm" required>
                                </div>
                                <div class="col-span-2 space-y-1">
                                    <label class="label text-xs">Qty</label>
                                    <input type="number" name="items[0][quantity]" class="input-field item-qty text-sm" min="1" value="1" required>
                                </div>
                                <div class="col-span-2 space-y-1">
                                    <label class="label text-xs">Price</label>
                                    <input type="number" name="items[0][unit_price]" class="input-field item-price text-sm" step="0.01" min="0" required>
                                </div>
                                <div class="col-span-2 space-y-1">
                                    <label class="label text-xs">Total</label>
                                    <input type="text" class="input-field item-total text-sm bg-stone-50" readonly>
                                </div>
                                <div class="col-span-1 pb-1">
                                    <button type="button" class="btn-ghost btn-sm remove-item text-red-600 hidden">&times;</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4 space-y-2 text-right">
                        <div class="flex justify-end gap-8">
                            <span class="label">Subtotal:</span>
                            <span id="subtotal-display" class="font-medium">0.00</span>
                        </div>
                        <div class="flex justify-end gap-8">
                            <span class="label">Tax (18%):</span>
                            <span id="tax-display" class="font-medium">0.00</span>
                        </div>
                        <div class="flex justify-end gap-8 text-lg">
                            <span class="label text-base font-bold">Total:</span>
                            <span id="total-display" class="font-bold">0.00</span>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">Create Room Order</button>
                        <a href="{{ route('pos.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = 1;
            const container = document.getElementById('items-container');
            const addBtn = document.getElementById('add-item');

            document.querySelectorAll('.quick-add').forEach(btn => {
                btn.addEventListener('click', function() {
                    const firstRow = container.querySelector('.item-row');
                    if (firstRow) {
                        const nameInput = firstRow.querySelector('.item-name');
                        const priceInput = firstRow.querySelector('.item-price');
                        if (!nameInput.value) {
                            nameInput.value = this.dataset.name;
                            priceInput.value = this.dataset.price;
                            calcRow(firstRow);
                            return;
                        }
                    }
                    const template = container.querySelector('.item-row').cloneNode(true);
                    template.querySelectorAll('input').forEach(el => {
                        el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
                        if (el.classList.contains('item-name')) el.value = this.dataset.name;
                        else if (el.classList.contains('item-qty')) el.value = 1;
                        else if (el.classList.contains('item-price')) el.value = this.dataset.price;
                        else el.value = '';
                    });
                    template.querySelector('.remove-item').classList.remove('hidden');
                    container.appendChild(template);
                    rowIndex++;
                    calcTotals();
                });
            });

            function calcRow(row) {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const total = qty * price;
                row.querySelector('.item-total').value = total.toFixed(2);
                calcTotals();
            }

            function calcTotals() {
                let subtotal = 0;
                document.querySelectorAll('.item-total').forEach(el => {
                    subtotal += parseFloat(el.value) || 0;
                });
                const tax = subtotal * 0.18;
                const total = subtotal + tax;
                document.getElementById('subtotal-display').textContent = subtotal.toFixed(2);
                document.getElementById('tax-display').textContent = tax.toFixed(2);
                document.getElementById('total-display').textContent = total.toFixed(2);
            }

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price')) {
                    calcRow(e.target.closest('.item-row'));
                }
            });

            addBtn.addEventListener('click', function() {
                const template = container.querySelector('.item-row').cloneNode(true);
                template.querySelectorAll('input').forEach(el => {
                    el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
                    if (el.classList.contains('item-qty')) el.value = 1;
                    else if (!el.classList.contains('item-total')) el.value = '';
                    else el.value = '';
                });
                template.querySelector('.remove-item').classList.remove('hidden');
                container.appendChild(template);
                rowIndex++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item')) {
                    const rows = container.querySelectorAll('.item-row');
                    if (rows.length > 1) {
                        e.target.closest('.item-row').remove();
                        calcTotals();
                    }
                }
            });
        });
    </script>
</x-layouts.app>
