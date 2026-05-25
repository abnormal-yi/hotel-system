<x-layouts.app title="Feature Flags">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Feature Flags</h1>
                <p class="page-description">Toggle system features on/off</p>
            </div>
        </div>

        @session('success') <div class="text-sm text-green-800 bg-green-100 rounded-lg p-3">{{ $value }}</div> @endsession

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Key</th>
                            <th class="table-header-cell">Label</th>
                            <th class="table-header-cell">Module</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flags as $flag)
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $flag->key }}</td>
                            <td class="table-cell">{{ $flag->label }}</td>
                            <td class="table-cell">{{ $flag->module ?? 'N/A' }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $flag->enabled ? 'badge-success' : 'badge-danger' }}">{{ $flag->enabled ? 'Enabled' : 'Disabled' }}</span>
                            </td>
                            <td class="table-cell text-right">
                                <form method="POST" action="{{ route('feature-flags.toggle', $flag->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-ghost btn-sm">{{ $flag->enabled ? 'Disable' : 'Enable' }}</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="table-cell text-center text-muted-foreground py-8">No feature flags found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
