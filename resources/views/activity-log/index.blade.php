<x-layouts.app title="Activity Log">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Activity Log</h1>
                <p class="page-description">System activity and audit trail</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-muted-foreground">{{ $logs->total() }} total entries</span>
            </div>
        </div>

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Timestamp</th>
                            <th class="table-header-cell">User</th>
                            <th class="table-header-cell">Description</th>
                            <th class="table-header-cell hidden md:table-cell">IP Address</th>
                            <th class="table-header-cell hidden lg:table-cell">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="table-row">
                            <td class="table-cell text-xs whitespace-nowrap">
                                <span class="text-muted-foreground">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-stone-200 flex items-center justify-center text-[9px] font-semibold text-stone-600 uppercase">
                                        {{ substr($log->causer?->name ?? 'S', 0, 2) }}
                                    </div>
                                    <span class="text-sm font-medium">{{ $log->causer?->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0
                                        @if(str_contains($log->description, 'check')) bg-sky-400
                                        @elseif(str_contains($log->description, 'payment') || str_contains($log->description, 'Payment')) bg-green-400
                                        @elseif(str_contains($log->description, 'booking') || str_contains($log->description, 'Booking')) bg-amber-400
                                        @elseif(str_contains($log->description, 'login') || str_contains($log->description, 'logout')) bg-violet-400
                                        @elseif(str_contains($log->description, 'clean')) bg-blue-400
                                        @else bg-stone-300 @endif
                                    "></span>
                                    <span class="text-sm">{{ $log->description }}</span>
                                </div>
                            </td>
                            <td class="table-cell text-xs text-muted-foreground hidden md:table-cell font-mono">{{ $log->properties['ip'] ?? '-' }}</td>
                            <td class="table-cell text-xs text-muted-foreground hidden lg:table-cell">
                                @if($log->properties && count($log->properties) > 0)
                                <span class="text-[10px]">{{ collect($log->properties)->except('ip')->map(fn($v, $k) => "$k: $v")->implode(', ') }}</span>
                                @else
                                <span class="text-stone-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="table-cell text-center text-muted-foreground py-12">No activity recorded</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-xs text-muted-foreground">Showing page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</p>
            {{ $logs->links() }}
        </div>
    </div>
</x-layouts.app>
