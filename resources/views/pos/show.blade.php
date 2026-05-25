<x-layouts.app title="Order Details">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Order {{ $order->order_number }}</h1>
                <p class="page-description">{{ $order->created_at }}</p>
            </div>
            <div class="flex gap-2">
                @if($order->status === 'pending')
                <form method="POST" action="{{ route('pos.complete', $order->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-primary">Complete Order</button>
                </form>
                @endif
                <a href="{{ route('pos.index') }}" class="btn-ghost">Back to List</a>
            </div>
        </div>

        <div class="card">
            <div class="card-content space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="label">Order Number</label>
                        <p class="font-medium">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <label class="label">Status</label>
                        <span class="badge {{ $order->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="label">Guest</label>
                        <p>{{ $guest->name ?? 'Walk-in' }}</p>
                    </div>
                    <div>
                        <label class="label">Type</label>
                        <span class="badge text-[10px] {{ ($order->order_type ?? 'pos') === 'room_order' ? 'badge-info' : 'badge-default' }}">
                            {{ ($order->order_type ?? 'pos') === 'room_order' ? 'Room Order' : 'POS' }}
                        </span>
                    </div>
                </div>
                @if($order->room_id)
                <div>
                    <label class="label">Room</label>
                    <p>Room {{ \DB::table('rooms')->where('id', $order->room_id)->value('room_number') ?? $order->room_id }}</p>
                </div>
                @endif

                <div>
                    <label class="label">Processed By</label>
                    <p>{{ $user->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Order Items</h2>
            </div>
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Item</th>
                            <th class="table-header-cell text-right">Qty</th>
                            <th class="table-header-cell text-right">Unit Price</th>
                            <th class="table-header-cell text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr class="table-row">
                            <td class="table-cell">{{ $item->name }}</td>
                            <td class="table-cell text-right">{{ $item->quantity }}</td>
                            <td class="table-cell text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="table-cell text-right">{{ number_format((float) $item->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="table-cell text-center text-muted-foreground py-8">No items</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="space-y-2 text-right">
                    <div class="flex justify-end gap-8">
                        <span class="label">Subtotal:</span>
                        <span class="font-medium">{{ number_format((float) $order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-end gap-8">
                        <span class="label">Tax (18%):</span>
                        <span class="font-medium">{{ number_format((float) $order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-end gap-8 text-lg border-t pt-2">
                        <span class="label text-base font-bold">Grand Total:</span>
                        <span class="font-bold">{{ number_format((float) $order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
