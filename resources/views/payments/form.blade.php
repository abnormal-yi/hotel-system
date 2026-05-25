<x-layouts.app title="{{ isset($payment) ? 'Edit Payment' : 'Add Payment' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="page-title">{{ isset($payment) ? 'Edit Payment' : 'Add Payment' }}</h1>
            <p class="page-description">{{ isset($payment) ? 'Update payment details' : 'Record a new payment' }}</p>
        </div>
        <div class="card">
            <div class="card-content">
                <form method="POST" action="{{ isset($payment) ? route('payments.update', $payment) : route('payments.store', $booking) }}" class="space-y-4">
                    @csrf @if(isset($payment)) @method('PUT') @endif

                    @if(!isset($payment) && isset($booking))
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <div class="rounded-lg bg-muted p-4">
                        <p class="text-sm text-muted-foreground">Recording payment for booking <strong>{{ $booking->booking_number }}</strong></p>
                        <p class="text-sm text-muted-foreground">Guest: <strong>{{ $booking->guests->first()?->name ?? 'N/A' }}</strong></p>
                        <p class="text-sm text-muted-foreground">Total: <strong>{{ number_format($booking->total_amount) }}</strong> | Due: <strong>{{ number_format($booking->total_amount - $booking->paid_amount) }}</strong></p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="amount">Amount *</label>
                            <input id="amount" name="amount" type="number" step="0.01" class="input-field" value="{{ old('amount', $payment->amount ?? '') }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="method">Payment Method *</label>
                            <select id="method" name="method" class="input-field" required>
                                <option value="cash" @selected(old('method', $payment->method ?? '') === 'cash')>Cash</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="label" for="reference">Reference (transaction ID / cheque no.)</label>
                        <input id="reference" name="reference" class="input-field" value="{{ old('reference', $payment->reference ?? '') }}">
                    </div>
                    <div class="space-y-2">
                        <label class="label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="input-field h-20">{{ old('notes', $payment->notes ?? '') }}</textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">{{ isset($payment) ? 'Update Payment' : 'Record Payment' }}</button>
                        <a href="{{ isset($payment) ? route('payments.index') : (isset($booking) ? route('bookings.show', $booking) : route('payments.index')) }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
