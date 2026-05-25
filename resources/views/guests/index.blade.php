<x-layouts.app title="Guests">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Guests</h1>
                <p class="page-description">Manage guest records</p>
            </div>
            <a href="{{ route('guests.create') }}" class="btn-primary">Add Guest</a>
        </div>

        @session('success') <div class="text-sm text-green-800 bg-green-100 rounded-lg p-3">{{ $value }}</div> @endsession

        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-sm font-semibold text-stone-900">All Guests</p>
                    <form method="GET" action="{{ route('guests.index') }}" class="flex gap-2">

                        <input type="text" name="search" class="input-field" placeholder="Search by name, phone, email, NIDA..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('guests.index', ['tab' => $tab]) }}" class="btn-ghost btn-sm">Clear</a>
                        @endif
                        <button type="submit" class="btn-primary btn-sm">Search</button>
                    </form>
                </div>
            </div>
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Name</th>
                            <th class="table-header-cell">Phone</th>
                            <th class="table-header-cell">Email</th>
                            <th class="table-header-cell">Bookings</th>
                            <th class="table-header-cell">Total Spent</th>
                            <th class="table-header-cell">Status</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $guest)
                        @php
                            $statusBadge = match(true) {
                                $guest->blacklisted => 'badge-danger',
                                $guest->status === 'vip' => 'badge-warning',
                                $guest->status === 'new' => 'badge-default',
                                default => 'badge-success',
                            };
                            $statusLabel = match(true) {
                                $guest->blacklisted => 'Blacklisted',
                                $guest->status === 'vip' => 'VIP',
                                $guest->status === 'new' => 'New',
                                default => 'Active',
                            };
                        @endphp
                        <tr class="table-row">
                            <td class="table-cell font-medium">{{ $guest->name }}</td>
                            <td class="table-cell">{{ $guest->phone ?? '-' }}</td>
                            <td class="table-cell">{{ $guest->email ?? '-' }}</td>
                            <td class="table-cell">{{ $guest->bookings_count ?? 0 }}</td>
                            <td class="table-cell">{{ number_format($guest->total_spent ?? 0) }}</td>
                            <td class="table-cell">
                                <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="table-cell text-right">
                                <a href="{{ route('guests.show', $guest) }}" class="btn-ghost btn-sm">View</a>
                                <a href="{{ route('guests.edit', $guest) }}" class="btn-ghost btn-sm">Edit</a>
                                @can('view-users')
                                <form method="POST" action="{{ route('guests.destroy', $guest) }}" class="inline" onsubmit="return confirm('Delete this guest?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-destructive">Delete</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="table-cell text-center text-muted-foreground py-8">No guests found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
