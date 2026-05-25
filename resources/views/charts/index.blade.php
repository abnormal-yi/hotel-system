<x-layouts.app title="Analytics">
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
            const dates30 = @json($revenue->keys());
            const rev30 = @json($revenue->values());

            const revCtx = document.getElementById('revenueChart')?.getContext('2d');
            if (revCtx) new Chart(revCtx, { type: 'line', data: {
                labels: dates30, datasets: [{ label: 'Revenue', data: rev30, borderColor: colors[0], tension: 0.3, fill: false }]
            }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: function(v) { return 'TZS ' + v.toLocaleString(); } } } } } });

            const bookCtx = document.getElementById('bookingTrends')?.getContext('2d');
            if (bookCtx) {
                const dates = @json($bookingTrends->keys());
                const counts = @json($bookingTrends->values());
                new Chart(bookCtx, { type: 'bar', data: {
                    labels: dates, datasets: [{ label: 'Bookings', data: counts, backgroundColor: colors[1] }]
                }, options: { responsive: true, plugins: { legend: { display: false } } } });
            }

            const statusCtx = document.getElementById('statusChart')?.getContext('2d');
            if (statusCtx) {
                const labels = @json($statusCounts->keys());
                const data = @json($statusCounts->values());
                new Chart(statusCtx, { type: 'doughnut', data: {
                    labels, datasets: [{ data, backgroundColor: colors }]
                }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
            }

            const occCtx = document.getElementById('occupancyChart')?.getContext('2d');
            if (occCtx) {
                const labels = @json($occupancyByType->pluck('name'));
                const total = @json($occupancyByType->pluck('total'));
                const occupied = @json($occupancyByType->pluck('occupied'));
                new Chart(occCtx, { type: 'bar', data: {
                    labels, datasets: [
                        { label: 'Total Rooms', data: total, backgroundColor: colors[2] },
                        { label: 'Occupied', data: occupied, backgroundColor: colors[0] }
                    ]
                }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
            }

            document.getElementById('period')?.addEventListener('change', function() {
                document.getElementById('custom-dates').classList.toggle('hidden', this.value !== 'custom');
            });
        });
    </script>
    @endpush
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="page-title">Analytics & Reports</h1>
                <p class="page-description">Revenue, occupancy, and booking insights</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('charts.pdf', request()->only(['period', 'date_from', 'date_to'])) }}" class="btn btn-outline text-sm">
                    Export PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('charts.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="label text-xs mb-1">Period</label>
                <select name="period" id="period" class="input text-sm">
                    <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="1y" {{ $period === '1y' ? 'selected' : '' }}>Last Year</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div id="custom-dates" class="flex items-end gap-2 {{ $period !== 'custom' ? 'hidden' : '' }}">
                <div>
                    <label class="label text-xs mb-1">From</label>
                    <input type="date" name="date_from" value="{{ ($dateFrom instanceof \Carbon\Carbon) ? $dateFrom->format('Y-m-d') : '' }}" class="input text-sm">
                </div>
                <div>
                    <label class="label text-xs mb-1">To</label>
                    <input type="date" name="date_to" value="{{ ($dateTo instanceof \Carbon\Carbon) ? $dateTo->format('Y-m-d') : '' }}" class="input text-sm">
                </div>
            </div>
            <button type="submit" class="btn btn-primary text-sm">Filter</button>
        </form>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6">
                <h3 class="card-title mb-4">Revenue {{ $period === 'custom' ? '(' . ($dateFrom instanceof \Carbon\Carbon ? $dateFrom->format('d/m') : '') . ' - ' . ($dateTo instanceof \Carbon\Carbon ? $dateTo->format('d/m') : '') . ')' : '(' . ($period === '7d' ? '7' : ($period === '30d' ? '30' : ($period === '90d' ? '90' : '365'))) . ' days)' }}</h3>
                <canvas id="revenueChart" height="200"></canvas>
            </div>
            <div class="card p-6">
                <h3 class="card-title mb-4">Booking Trends</h3>
                <canvas id="bookingTrends" height="200"></canvas>
            </div>
            <div class="card p-6">
                <h3 class="card-title mb-4">Booking Status</h3>
                <canvas id="statusChart" height="200"></canvas>
            </div>
            <div class="card p-6">
                <h3 class="card-title mb-4">Occupancy by Room Type</h3>
                <canvas id="occupancyChart" height="200"></canvas>
            </div>
        </div>
        <div class="card p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b"><th class="text-left py-2">Date</th><th class="text-left py-2">Bookings</th><th class="text-left py-2">Revenue</th></tr></thead>
                    <tbody>
                        @forelse($dailyRevenue as $dr)
                        <tr class="border-b">
                            <td class="py-2">{{ $dr->date }}</td>
                            <td class="py-2">{{ $dr->bookings }}</td>
                            <td class="py-2">{{ number_format($dr->revenue) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-4 text-center text-muted">No data for this period</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
