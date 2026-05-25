<x-layouts.app title="Payment Details">
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Payment Details</h1>
                <p class="page-description">Receipt {{ $payment->receipt_number ?? 'N/A' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('payments.invoice', $payment) }}" class="btn-primary">Print Invoice</a>
                <a href="{{ route('payments.index') }}" class="btn-ghost">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Payment Information</h2>
            </div>
            <div class="card-content space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="label">Receipt Number</p>
                        <p class="font-medium">{{ $payment->receipt_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="label">Date</p>
                        <p class="font-medium">{{ $payment->paid_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="label">Amount</p>
                        <p class="font-medium text-lg">{{ number_format($payment->amount) }}</p>
                    </div>
                    <div>
                        <p class="label">Method</p>
                        <span class="badge {{ $payment->method === 'cash' ? 'badge-success' : ($payment->method === 'card' ? 'badge-info' : 'badge-default') }}">
                            {{ ucfirst($payment->method) }}
                        </span>
                    </div>
                    <div>
                        <p class="label">Status</p>
                        <span class="badge {{ $payment->status === 'completed' ? 'badge-success' : ($payment->status === 'refunded' ? 'badge-danger' : 'badge-warning') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="label">Reference</p>
                        <p class="font-medium">{{ $payment->reference ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Booking Details</h2>
            </div>
            <div class="card-content space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="label">Booking Number</p>
                        <p class="font-medium">
                            <a href="{{ route('bookings.show', $payment->booking) }}" class="text-primary hover:underline">
                                {{ $payment->booking->booking_number }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="label">Guest</p>
                        <p class="font-medium">{{ $payment->booking->guests->first()?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="label">Check In</p>
                        <p class="font-medium">{{ $payment->booking->check_in }}</p>
                    </div>
                    <div>
                        <p class="label">Check Out</p>
                        <p class="font-medium">{{ $payment->booking->check_out }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-6 pt-4 border-t">
            <a href="{{ route('efd.from-payment', $payment->id) }}" class="btn-primary">
                Issue EFD Receipt
            </a>
            <a href="{{ route('payments.invoice', $payment) }}" class="btn-ghost">
                View Invoice
            </a>
        </div>
    </div>
</x-layouts.app>
