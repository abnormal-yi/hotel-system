<x-layouts.app title="Invoice">
    <div class="max-w-3xl mx-auto">
        <div class="card">
            <div class="card-content p-8">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
                        <p class="text-muted-foreground text-sm">Hotel Management System</p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-lg font-semibold">INVOICE</h2>
                        <p class="text-muted-foreground text-sm">{{ $payment->receipt_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="border-t border-b py-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-muted-foreground">Date</p>
                            <p class="font-medium">{{ $payment->paid_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Payment Method</p>
                            <p class="font-medium">{{ ucfirst($payment->method) }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Booking #</p>
                            <p class="font-medium">{{ $payment->booking->booking_number }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Guest</p>
                            <p class="font-medium">{{ $payment->booking->guests->first()?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <table class="w-full mb-6">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-sm font-medium text-muted-foreground">Description</th>
                            <th class="text-right py-2 text-sm font-medium text-muted-foreground">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-3">Room Charges ({{ $payment->booking->rooms->count() }} room(s))</td>
                            <td class="text-right py-3 font-medium">{{ number_format($payment->booking->total_amount) }}</td>
                        </tr>
                        @if($payment->amount != $payment->booking->total_amount)
                        <tr class="border-b">
                            <td class="py-3">Partial Payment</td>
                            <td class="text-right py-3 font-medium">{{ number_format($payment->amount) }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="py-3 text-lg font-bold">Total Paid</td>
                            <td class="text-right py-3 text-lg font-bold">{{ number_format($payment->amount) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-center text-sm text-muted-foreground pt-4 border-t">
                    <p>Thank you for your stay!</p>
                    <p class="mt-1">{{ config('app.name') }} - Hotel Management System</p>
                </div>

                <div class="text-center mt-6">
                    <button onclick="window.print()" class="btn-primary">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
