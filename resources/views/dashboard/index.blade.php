<x-layouts.app title="Dashboard">
    <div class="space-y-6" x-data="dashboardApp()" x-init="init()">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-description">Welcome back, {{ auth()->user()->name }}</p>
            </div>
            <span class="text-xs text-muted-foreground bg-stone-100 px-2.5 py-1 rounded-full capitalize">{{ auth()->user()->role }}</span>
        </div>

        @if(auth()->user()->role === 'creator')
            {{-- Creator Dashboard --}}
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="stat-card"><p class="stat-label">Total Users</p><p class="stat-value">{{ $totalUsers }}</p></div>
                <div class="stat-card"><p class="stat-label">Total Rooms</p><p class="stat-value">{{ $totalRooms }}</p></div>
                <div class="stat-card"><p class="stat-label">Total Bookings</p><p class="stat-value">{{ $totalBookings }}</p></div>
                <div class="stat-card"><p class="stat-label">Total Revenue</p><p class="stat-value">{{ number_format($totalRevenue) }}</p></div>
            </div>
            <div class="card">
                <div class="card-header"><h2 class="card-title text-base lg:text-2xl">Feature Flags</h2></div>
                <div class="card-content">
                    <div class="space-y-3">
                        @foreach($featureFlags as $flag)
                        <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                            <span class="text-sm font-medium">{{ $flag->label }}</span>
                            <span class="badge {{ $flag->enabled ? 'badge-success' : 'badge-default' }}">{{ $flag->enabled ? 'Enabled' : 'Disabled' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->role === 'manager')
            {{-- Manager Dashboard --}}
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="stat-card"><p class="stat-label">Today's Revenue</p><p class="stat-value">{{ number_format($todayRevenue) }}</p></div>
                <div class="stat-card"><p class="stat-label">Occupancy</p><p class="stat-value">{{ $occupancyRate }}%</p></div>
                <div class="stat-card"><p class="stat-label">Total Bookings</p><p class="stat-value">{{ $totalBookings }}</p></div>
                <div class="stat-card"><p class="stat-label">Available Rooms</p><p class="stat-value">{{ $availableRooms }}</p></div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <div class="card">
                    <div class="card-header"><h2 class="card-title text-base lg:text-2xl">Recent Bookings</h2></div>
                    <div class="card-content p-0 overflow-x-auto">
                        <table class="w-full min-w-[1200px] text-base">
                            <thead class="table-header text-base">
                                <tr><th class="table-header-cell hidden sm:table-cell">Booking #</th><th class="table-header-cell">Guest</th><th class="table-header-cell">Check In</th><th class="table-header-cell">Status</th><th class="table-header-cell hidden sm:table-cell">Amount</th></tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr class="table-row">
                                    <td class="table-cell font-mono hidden sm:table-cell">{{ $booking->booking_number }}</td>
                                    <td class="table-cell font-medium">{{ $booking->guests->first()?->name ?? 'N/A' }}</td>
                                    <td class="table-cell">{{ $booking->check_in }}</td>
                                    <td class="table-cell"><span class="badge {{ $booking->status === 'checked_in' ? 'badge-success' : ($booking->status === 'pending' ? 'badge-warning' : 'badge-default') }}">{{ $booking->status }}</span></td>
                                    <td class="table-cell hidden sm:table-cell">{{ number_format($booking->total_amount) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title text-base lg:text-2xl">Recent Activity</h2>
                    </div>
                    <div class="card-content p-0 max-h-72 overflow-y-auto custom-scrollbar">
                        @if($recentActivity->count() > 0)
                        <div class="divide-y divide-stone-50">
                            @foreach($recentActivity as $log)
                            <div class="flex items-start gap-2 sm:gap-3 px-3 sm:px-4 py-2.5 sm:py-3 hover:bg-stone-50/50 transition-colors">
                                <div class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0
                                    @if(str_contains($log->description, 'check')) bg-sky-400
                                    @elseif(str_contains($log->description, 'payment') || str_contains($log->description, 'Payment')) bg-green-400
                                    @elseif(str_contains($log->description, 'booking') || str_contains($log->description, 'Booking')) bg-amber-400
                                    @elseif(str_contains($log->description, 'clean')) bg-blue-400
                                    @else bg-stone-300 @endif
                                "></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-stone-700 truncate">{{ $log->description }}</p>
                                    <p class="text-[10px] text-stone-400 mt-0.5">
                                        <span>{{ $log->causer_name }}</span>
                                        <span class="mx-1">·</span>
                                        <span>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-sm text-stone-400">No recent activity</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 💰 TODAY'S PAYMENTS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title text-base lg:text-2xl">Today's Payments</h2>
                    </div>
                    <div class="card-content space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Total Revenue</span>
                            <span class="text-base lg:text-lg font-bold text-green-700">{{ number_format($todayRevenue) }} TZS</span>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-stone-100">
                            @foreach($paymentMethods as $key => $method)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full
                                        @if($key === 'cash') bg-emerald-500
                                        @elseif($key === 'mobile_money') bg-amber-500
                                        @elseif($key === 'card') bg-sky-500
                                        @else bg-violet-500 @endif
                                    "></span>
                                    <span>{{ $method['label'] }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-medium">{{ number_format($method['total']) }}</span>
                                    <span class="text-xs text-muted-foreground ml-1">({{ $method['count'] }})</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('payments.index') }}" class="block text-center text-xs font-medium text-sky-600 hover:text-sky-700 pt-2">View All Payments</a>
                    </div>
                </div>
                <div class="lg:col-span-2 card">
                    <div class="card-header">
                        <h2 class="card-title text-base lg:text-2xl">Recent Payments</h2>
                    </div>
                    <div class="card-content p-0 max-h-64 overflow-y-auto custom-scrollbar">
                        @if($todayPayments->count() > 0)
                        <div class="divide-y divide-stone-50">
                            @foreach($todayPayments as $payment)
                            <div class="flex items-center justify-between px-4 py-2.5 hover:bg-stone-50/50 transition-colors">
                                <div>
                                    <p class="text-xs font-medium">{{ $payment->booking?->booking_number ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-muted-foreground">
                                        <span class="badge {{ $payment->method === 'cash' ? 'badge-success' : ($payment->method === 'mobile_money' ? 'badge-warning' : ($payment->method === 'card' ? 'badge-info' : 'badge-default')) }} text-[9px] px-1.5 py-0.5">{{ str_replace('_', ' ', $payment->method) }}</span>
                                        <span class="ml-1">{{ $payment->created_at instanceof \Carbon\Carbon ? $payment->created_at->format('H:i') : substr($payment->created_at, 11, 5) }}</span>
                                    </p>
                                </div>
                                <span class="text-sm font-semibold">{{ number_format($payment->amount) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-sm text-stone-400">No payments yet today</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- OVERDUE CHECK-OUTS --}}
            @if($overdueCheckOutsCount > 0)
            <div class="card border-2 border-red-300 bg-red-50/50">
                <div class="card-header flex items-center justify-between">
                    <h2 class="card-title flex items-center gap-2 text-base lg:text-2xl">
                        <span>🔴</span> Overdue Check-outs
                        <span class="text-sm font-normal text-muted-foreground">({{ $overdueCheckOutsCount }})</span>
                    </h2>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">⚠ Immediate attention required</span>
                </div>
                <div class="card-content p-0 overflow-x-auto">
                    <table class="w-full min-w-[1200px] text-base">
                        <thead class="table-header text-base">
                            <tr><th class="table-header-cell hidden sm:table-cell">Booking</th><th class="table-header-cell">Guest</th><th class="table-header-cell">Room</th><th class="table-header-cell hidden sm:table-cell">Checked In</th><th class="table-header-cell">Should Have Left</th><th class="table-header-cell">Overdue</th><th class="table-header-cell">Severity</th><th class="table-header-cell text-right">Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($overdueCheckOuts as $b)
                            @php
                            $d = $b->days_overdue;
                            if ($d >= 5) { $sevLabel = 'Blacklisted'; $sevIcon = '⚫'; $sevColor = 'bg-stone-800 text-white'; }
                            elseif ($d >= 3) { $sevLabel = 'Manager Required'; $sevIcon = '🔴'; $sevColor = 'bg-red-200 text-red-800'; }
                            elseif ($d >= 2) { $sevLabel = 'Late Fee'; $sevIcon = '🟠'; $sevColor = 'bg-orange-100 text-orange-700'; }
                            else { $sevLabel = 'Warning'; $sevIcon = '🟡'; $sevColor = 'bg-amber-100 text-amber-700'; }
                            @endphp
                            <tr class="table-row {{ $d >= 5 ? 'bg-red-50/50' : '' }}">
                                <td class="table-cell font-mono hidden sm:table-cell">{{ $b->booking_number }}</td>
                                <td class="table-cell font-medium">{{ $b->guests->first()?->name ?? 'N/A' }}</td>
                                <td class="table-cell font-mono">{{ $b->rooms->first()?->room_number ?? '-' }}</td>
                                <td class="table-cell hidden sm:table-cell">{{ \Carbon\Carbon::parse($b->check_in)->format('d/m') }}</td>
                                <td class="table-cell">{{ \Carbon\Carbon::parse($b->check_out)->format('d/m/Y') }}</td>
                                <td class="table-cell"><span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full font-bold bg-red-100 text-red-700">🔴 {{ $d }} day(s)</span></td>
                                <td class="table-cell"><span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-semibold {{ $sevColor }}">{{ $sevIcon }} {{ $sevLabel }}</span></td>
                                <td class="table-cell text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <form action="{{ route('bookings.checkout', $b) }}" method="POST" class="inline" onsubmit="return confirm('Force check out {{ $b->guests->first()?->name }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-base font-bold px-5 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors shadow-md">Force Check-out</button>
                                        </form>
                                        <a href="{{ route('bookings.edit', $b) }}" class="text-base font-semibold px-4 py-2.5 rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors">Extend</a>
                                        @if($d >= 5 && $b->guests->first() && !$b->guests->first()->blacklisted)
                                        <form action="{{ route('guests.blacklist', $b->guests->first()) }}" method="POST" class="inline" onsubmit="return confirm('Blacklist {{ $b->guests->first()?->name }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-base font-bold px-4 py-2.5 rounded-lg bg-stone-800 text-white hover:bg-stone-900 transition-colors shadow-md">⚫ Blacklist</button>
                                        </form>
                                        @endif
                                        <a href="{{ route('bookings.show', $b) }}" class="text-base font-medium px-4 py-2.5 rounded-lg bg-white text-stone-500 border border-stone-200 hover:bg-stone-50 transition-colors">View</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @else
            {{-- ============================================ --}}
            {{-- RECEPTIONIST DASHBOARD - CONTROL ROOM       --}}
            {{-- ============================================ --}}

            {{-- 🚨 CRITICAL ALERTS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @if($overdueCheckOuts > 0)
                <div class="stat-card bg-red-50 border-2 border-red-300">
                    <p class="stat-label text-red-700 font-bold">Overdue Check-outs</p>
                    <p class="stat-value text-red-600">{{ $overdueCheckOuts }}</p>
                </div>
                @endif
                @if($cleaningRooms > 0)
                <div class="stat-card bg-sky-50 border-2 border-sky-300">
                    <p class="stat-label text-sky-700 font-bold">Rooms Cleaning</p>
                    <p class="stat-value text-sky-600">{{ $cleaningRooms }}</p>
                </div>
                @endif
                @if($tomorrowCheckIns > 0)
                <div class="stat-card bg-amber-50 border-2 border-amber-300">
                    <p class="stat-label text-amber-700 font-bold">Check-ins Tomorrow</p>
                    <p class="stat-value text-amber-600">{{ $tomorrowCheckIns }}</p>
                </div>
                @endif
                @if($unpaidInvoices > 0)
                <div class="stat-card bg-orange-50 border-2 border-orange-300">
                    <p class="stat-label text-orange-700 font-bold">Unpaid Invoices</p>
                    <p class="stat-value text-orange-600">{{ $unpaidInvoices }}</p>
                </div>
                @endif
                @if($pendingCheckIns > 0)
                <div class="stat-card bg-amber-50 border-2 border-amber-300">
                    <p class="stat-label text-amber-700 font-bold">Pending Check-ins</p>
                    <p class="stat-value text-amber-600">{{ $pendingCheckIns }}</p>
                </div>
                @endif
                @if($maintenanceRooms > 0)
                <div class="stat-card bg-stone-100 border-2 border-stone-300">
                    <p class="stat-label text-stone-700 font-bold">Maintenance</p>
                    <p class="stat-value text-stone-600">{{ $maintenanceRooms }}</p>
                </div>
                @endif
            </div>



            {{-- ⚡ QUICK ACTIONS --}}
            <div class="card">
                <div class="card-content py-3">
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            New Booking
                        </a>
                        <a href="{{ route('bookings.index', ['status' => 'confirmed']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Check-in Guest
                        </a>
                        <a href="{{ route('bookings.index', ['status' => 'checked_in']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                            Check-out Guest
                        </a>
                        <a href="{{ route('guests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-stone-700 text-white text-sm font-medium rounded-lg hover:bg-stone-800 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            Add Guest
                        </a>
                        <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Assign Room
                        </a>
                        <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            Room Status
                        </a>
                        @can('view-payments')
                        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            Generate Invoice
                        </a>
                        @endcan
                        <a href="{{ route('housekeeping.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-600 text-white text-sm font-medium rounded-lg hover:bg-cyan-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M16 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Laundry
                        </a>
                        <button @click="openSearch = true" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-stone-100 text-stone-700 text-sm font-medium rounded-lg hover:bg-stone-200 transition-colors border border-stone-200 flex-1 min-w-[140px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <span>Search</span>
                            <kbd class="hidden sm:inline-flex text-[10px] font-mono text-stone-400 bg-stone-50 border border-stone-300 rounded px-1.5 py-0.5 ml-auto">Ctrl+K</kbd>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 🔍 LIVE SEARCH OVERLAY --}}
            <div x-show="openSearch" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-24" @click.away="openSearch = false">
                <div class="fixed inset-0 bg-black/40" @click="openSearch = false"></div>
                <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl border border-stone-200 overflow-hidden">
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-stone-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-stone-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input id="search-input" type="text" x-model="searchQuery" @input="doSearch" @keydown.escape.window="openSearch = false" placeholder="Search guests, bookings, rooms..." class="flex-1 border-0 bg-transparent text-sm focus:ring-0 outline-none placeholder:text-stone-400">
                        <kbd class="hidden sm:inline-flex text-[10px] font-mono text-stone-400 bg-stone-50 border border-stone-200 rounded px-1.5 py-0.5">ESC</kbd>
                    </div>
                    <div x-show="searchQuery.length > 0" class="max-h-80 overflow-y-auto p-2" x-cloak>
                        <template x-for="item in searchResults" :key="item.type + item.id">
                            <a :href="item.type === 'guest' ? '/guests/' + item.id : (item.type === 'booking' ? '/bookings/' + item.id : '/rooms/' + item.id)" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-stone-50 transition-colors">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-stone-400 w-14 shrink-0" x-text="item.type"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-stone-800 truncate" x-text="item.label"></p>
                                    <p class="text-xs text-stone-400 truncate" x-text="item.sub"></p>
                                </div>
                            </a>
                        </template>
                        <p x-show="searchResults.length === 0" class="text-sm text-stone-400 text-center py-6">No results found</p>
                    </div>
                    <div x-show="searchQuery.length === 0" class="text-xs text-stone-400 text-center py-6 px-4">
                        Type to search — guests, booking numbers, or room numbers
                    </div>
                </div>
            </div>

            {{-- 📋 TODAY'S OPERATIONS + ROOM GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- LEFT: Today's Operations --}}
                <div class="lg:col-span-2 card">
                    <div class="card-header flex items-center justify-between">
                        <h2 class="card-title">Today's Operations</h2>
                        <a href="{{ route('bookings.index') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">View All</a>
                    </div>
                    <div class="card-content p-0">
                        @if($todayBookings->count() > 0)
                        <table class="w-full min-w-[1200px] text-base">
                            <thead class="table-header text-base">
                                <tr>
                                    <th class="table-header-cell">Booking</th>
                                    <th class="table-header-cell">Guest</th>
                                    <th class="table-header-cell">Room</th>
                                    <th class="table-header-cell">Check-in</th>
                                    <th class="table-header-cell">Status</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayBookings as $booking)
                                <tr class="table-row">
                                    <td class="table-cell font-mono">{{ $booking->booking_number }}</td>
                                    <td class="table-cell">
                                        <span class="font-medium">{{ $booking->guests->first()?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="font-mono">{{ $booking->rooms->first()?->room_number ?? '-' }}</span>
                                        @php $bookingRoom = $booking->rooms->first(); @endphp
                                        @if($bookingRoom)
                                        <span class="text-[10px] block mt-0.5 {{ $bookingRoom->status === 'available' ? 'text-emerald-600' : ($bookingRoom->status === 'cleaning' ? 'text-sky-600' : ($bookingRoom->status === 'occupied' ? 'text-red-600' : ($bookingRoom->status === 'reserved' ? 'text-amber-600' : 'text-stone-400'))) }}">

                                            @if($bookingRoom->status === 'available') 🟢 Available
                                            @elseif($bookingRoom->status === 'occupied') 🔴 Occupied
                                            @elseif($bookingRoom->status === 'cleaning') 🧹 Cleaning
                                            @elseif($bookingRoom->status === 'reserved') 🟡 Reserved
                                            @else {{ $bookingRoom->status }} @endif
                                        </span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        {{ $booking->check_in instanceof \Carbon\Carbon ? $booking->check_in->format('d/m') : $booking->check_in }}
                                        @if($booking->checked_in_at)
                                        <span class="text-xs text-green-600 font-medium block">{{ $booking->checked_in_at->format('H:i') }}</span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @php
                                        $statusColors = [
                                            'confirmed' => 'badge-warning',
                                            'checked_in' => 'badge-success',
                                            'checked_out' => 'badge-default',
                                            'cancelled' => 'badge-danger',
                                        ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$booking->status] ?? 'badge-default' }}">{{ str_replace('_', ' ', $booking->status) }}</span>
                                    </td>
                                    <td class="table-cell text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($booking->status === 'confirmed')
                                            <form action="{{ route('bookings.checkin', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Check in {{ $booking->guests->first()?->name }}?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-base font-bold px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm">Check-in</button>
                                            </form>
                                            @endif
                                            <a href="{{ route('bookings.show', $booking) }}" class="text-base font-medium px-4 py-2 rounded-lg bg-white text-stone-500 border border-stone-200 hover:bg-stone-50 transition-colors">View</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto text-stone-300 mb-3"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <p class="text-sm text-stone-400">No operations scheduled for today</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- RIGHT: Room Status Mini Grid --}}
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <h2 class="card-title">Room Status</h2>
                        <a href="{{ route('rooms.index') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">Manage</a>
                    </div>
                    <div class="card-content">
                        @php
                        $roomLabels = ['available' => 'Available', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'cleaning' => 'Cleaning', 'maintenance' => 'Maintenance'];
                        $roomIcons = ['available' => '🟢', 'occupied' => '🔴', 'reserved' => '🟡', 'cleaning' => '🧹', 'maintenance' => '🔧'];
                        $roomColors = ['available' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'occupied' => 'bg-red-50 text-red-700 border-red-200', 'reserved' => 'bg-amber-50 text-amber-700 border-amber-200', 'cleaning' => 'bg-sky-50 text-sky-700 border-sky-200', 'maintenance' => 'bg-stone-100 text-stone-500 border-stone-200'];
                        $statusOrder = ['available', 'occupied', 'reserved', 'cleaning', 'maintenance'];
                    @endphp
                    <div class="grid grid-cols-4 gap-2">
                            @foreach($uniqueRooms as $room)
                            <a href="{{ route('rooms.edit', $room) }}"
                               class="flex flex-col items-center justify-center rounded-lg py-2 px-1 text-center transition-all hover:scale-105 border {{ $roomColors[$room->status] ?? 'bg-stone-50 text-stone-500 border-stone-200' }}">
                                <span class="text-sm font-bold">{{ $room->room_number }}</span>
                                <span class="text-[9px] font-medium mt-0.5">{{ $roomIcons[$room->status] ?? '⬜' }} {{ $roomLabels[$room->status] ?? ucfirst($room->status) }}</span>
                            </a>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-3 mt-4 pt-3 border-t border-stone-100">
                            @foreach($statusOrder as $s)
                            @if(in_array($s, $usedStatuses))
                            <span class="flex items-center gap-1 text-[10px] text-stone-500">{{ $roomIcons[$s] ?? '⬜' }} {{ $roomLabels[$s] ?? ucfirst($s) }}</span>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🛏️ OCCUPIED ROOMS + RESERVED ROOMS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- LEFT: Currently Occupied Rooms --}}
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <h2 class="card-title">Occupied Rooms</h2>
                        <span class="text-xs font-medium text-muted-foreground">{{ $occupiedRoomsList->count() }} room(s)</span>
                    </div>
                    <div class="card-content p-0 max-h-72 overflow-y-auto custom-scrollbar">
                        @if($occupiedRoomsList->count() > 0)
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-header-cell">Room</th>
                                    <th class="table-header-cell">Guest</th>
                                    <th class="table-header-cell">Days Left</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($occupiedRoomsList as $occ)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="text-sm font-mono font-medium text-red-700">Room {{ $occ->room_number }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="text-sm font-medium">{{ $occ->guest_name }}</span>
                                    </td>
                                    <td class="table-cell">
                                        @if($occ->days_remaining > 0)
                                        <span class="badge badge-success text-[10px]">{{ $occ->days_remaining }} day(s)</span>
                                        @elseif($occ->days_remaining === 0)
                                        <span class="badge badge-warning text-[10px]">Check-out today</span>
                                        @else
                                        <span class="badge badge-danger text-[10px]">{{ abs($occ->days_remaining) }} day(s) overdue</span>
                                        @endif
                                    </td>
                                    <td class="table-cell text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form action="{{ route('bookings.checkout', $occ->booking_id) }}" method="POST" class="inline" onsubmit="return confirm('Check out {{ $occ->guest_name }}?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs font-medium px-2.5 py-1.5 rounded-lg bg-stone-100 text-stone-700 hover:bg-stone-200 transition-colors">Check-out</button>
                                            </form>
                                            <a href="{{ route('bookings.show', $occ->booking_id) }}" class="text-xs font-medium px-2.5 py-1.5 rounded-lg bg-white text-stone-500 border border-stone-200 hover:bg-stone-50 transition-colors">View</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto text-stone-300 mb-2"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="M3 9V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4"/></svg>
                            <p class="text-sm text-stone-400">No occupied rooms</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- RIGHT: Reserved Rooms (Future Bookings) --}}
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <h2 class="card-title">Reserved Rooms</h2>
                        <span class="text-xs font-medium text-muted-foreground">{{ $reservedBookings->count() }} upcoming</span>
                    </div>
                    <div class="card-content p-0 max-h-72 overflow-y-auto custom-scrollbar">
                        @if($reservedBookings->count() > 0)
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-header-cell">Guest</th>
                                    <th class="table-header-cell">Check In</th>
                                    <th class="table-header-cell">Room</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservedBookings as $res)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="text-sm font-medium">{{ $res->guests->first()?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="text-xs font-mono">{{ \Carbon\Carbon::parse($res->check_in)->format('d M') }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="text-xs font-mono text-amber-700">{{ $res->rooms->first()?->room_number ?? '-' }}</span>
                                    </td>
                                    <td class="table-cell text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('bookings.show', $res) }}" class="text-xs font-medium px-2.5 py-1.5 rounded-lg bg-stone-100 text-stone-700 hover:bg-stone-200 transition-colors">Check-in</a>
                                            <a href="{{ route('bookings.show', $res) }}" class="text-xs font-medium px-2.5 py-1.5 rounded-lg bg-white text-stone-500 border border-stone-200 hover:bg-stone-50 transition-colors">View</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto text-stone-300 mb-2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <p class="text-sm text-stone-400">No upcoming reservations</p>
                        </div>
                        @endif
                    </div>
                </div>


            {{-- Alpine.js for search --}}
            @push('scripts')
            <script>
                function dashboardApp() {
                    return {
                        openSearch: false,
                        searchQuery: '',
                        searchResults: [],
                        searchTimeout: null,
                        doSearch() {
                            clearTimeout(this.searchTimeout);
                            if (this.searchQuery.length < 1) { this.searchResults = []; return; }
                            this.searchTimeout = setTimeout(() => {
                                fetch('/dashboard/search?q=' + encodeURIComponent(this.searchQuery))
                                    .then(r => r.json())
                                    .then(data => { this.searchResults = data; })
                                    .catch(() => { this.searchResults = []; });
                            }, 250);
                        },
                        init() {
                            document.addEventListener('keydown', (e) => {
                                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                                    e.preventDefault();
                                    this.openSearch = true;
                                    this.$nextTick(() => {
                                        const input = document.querySelector('#search-input');
                                        if (input) input.focus();
                                    });
                                }
                            });
                        }
                    };
                }
            </script>
            @endpush
        @endif
    </div>
</x-layouts.app>
