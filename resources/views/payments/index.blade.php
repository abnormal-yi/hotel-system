<x-layouts.app title="Payments">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Payments</h1>
                <p class="page-description">View all payment transactions</p>
            </div>
        </div>

        @session('success') <div class="text-sm text-green-800 bg-green-100 rounded-lg p-3">{{ $value }}</div> @endsession

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Receipt #</th>
                            <th class="table-header-cell">Booking</th>
                            <th class="table-header-cell">Guest</th>
                            <th class="table-header-cell">Amount</th>
                            <th class="table-header-cell">Method</th>
                            <th class="table-header-cell">Date</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $payment->receipt_number ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <a href="{{ route('bookings.show', $payment->booking) }}" class="text-primary hover:underline">{{ $payment->booking->booking_number }}</a>
                            </td>
                            <td class="table-cell">{{ $payment->booking->guests->first()?->name ?? 'N/A' }}</td>
                            <td class="table-cell">{{ number_format($payment->amount) }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $payment->method === 'cash' ? 'badge-success' : ($payment->method === 'mobile_money' ? 'badge-warning' : ($payment->method === 'card' ? 'badge-info' : 'badge-default')) }}">
                                    {{ str_replace('_', ' ', ucfirst($payment->method)) }}
                                </span>
                            </td>
                            <td class="table-cell">{{ $payment->paid_at->format('d M Y H:i') }}</td>
                            <td class="table-cell text-right">
                                <a href="{{ route('payments.edit', $payment) }}" class="btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Delete this payment?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="table-cell text-center text-muted-foreground py-8">No payments recorded</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
