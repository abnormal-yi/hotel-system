<x-layouts.app title="POS Orders">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">POS Orders</h1>
                <p class="page-description">Point of sale order management</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pos.create') }}" class="btn-primary">New Order</a>
                <a href="{{ route('pos.room-order') }}" class="btn-secondary">Room Order</a>
            </div>
        </div>

        <div class="card">
            <div class="card-content p-0">
                    <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Order #</th>
                            <th class="table-header-cell">Type</th>
                            <th class="table-header-cell">Guest/Room</th>
                            <th class="table-header-cell">User</th>
                            <th class="table-header-cell hidden sm:table-cell">Subtotal</th>
                            <th class="table-header-cell">Total</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell hidden md:table-cell">Date</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $order->order_number }}</td>
                            <td class="table-cell">
                                <span class="badge text-[10px] {{ ($order->order_type ?? 'pos') === 'room_order' ? 'badge-info' : 'badge-default' }}">
                                    {{ ($order->order_type ?? 'pos') === 'room_order' ? 'Room' : 'POS' }}
                                </span>
                            </td>
                            <td class="table-cell">
                                {{ $order->guest_name ?? 'Walk-in' }}
                                @if($order->room_number)
                                <span class="text-xs text-muted-foreground">(Rm {{ $order->room_number }})</span>
                                @endif
                            </td>
                            <td class="table-cell">{{ $order->user_name }}</td>
                            <td class="table-cell hidden sm:table-cell">{{ number_format((float) $order->subtotal, 2) }}</td>
                            <td class="table-cell">{{ number_format((float) $order->total, 2) }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $order->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="table-cell hidden md:table-cell text-xs">{{ $order->created_at }}</td>
                            <td class="table-cell text-right">
                                <a href="{{ route('pos.show', $order->id) }}" class="btn-ghost btn-sm">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="table-cell text-center text-muted-foreground py-8">No orders found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div>{{ $orders->links() }}</div>
    </div>
</x-layouts.app>
