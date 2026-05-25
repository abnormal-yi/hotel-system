<x-layouts.app title="EFD Receipts">
    <div class="space-y-6">
        <div><h1 class="page-title">EFD Receipts</h1><p class="page-description">Electronic Fiscal Device transactions</p></div>
        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Receipt #</th>
                            <th class="table-header-cell">Amount</th>
                            <th class="table-header-cell">VAT (18%)</th>
                            <th class="table-header-cell">TIN</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell">Date</th>
                            <th class="table-header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $t->receipt_number }}</td>
                            <td class="table-cell">{{ number_format($t->amount) }}</td>
                            <td class="table-cell">{{ number_format($t->vat, 2) }}</td>
                            <td class="table-cell text-sm">{{ $t->tin ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $t->status === 'completed' ? 'badge-success' : 'badge-default' }}">{{ $t->status }}</span>
                            </td>
                            <td class="table-cell text-sm">{{ $t->created_at }}</td>
                            <td class="table-cell">
                                <a href="{{ route('efd.receipt', $t->id) }}" class="btn-ghost btn-sm" target="_blank">Receipt</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-4 text-center text-muted-foreground">No EFD transactions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $transactions->links() }}
    </div>
</x-layouts.app>
