<x-layouts.app title="Booking {{ $booking->booking_number }}">
    @php
        $nights = $booking->check_in instanceof \Carbon\Carbon && $booking->check_out instanceof \Carbon\Carbon
            ? $booking->check_in->diffInDays($booking->check_out)
            : \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out));

        $payStatus = $booking->paid_amount >= $booking->total_amount ? 'paid' : ($booking->paid_amount > 0 ? 'partial' : 'unpaid');
        $payColors = ['paid' => 'bg-emerald-100 text-emerald-800', 'partial' => 'bg-amber-100 text-amber-800', 'unpaid' => 'bg-red-100 text-red-800'];
        $bStatusColors = ['checked_in' => 'bg-emerald-100 text-emerald-800', 'checked_out' => 'bg-stone-100 text-stone-600', 'cancelled' => 'bg-red-100 text-red-800', 'confirmed' => 'bg-blue-100 text-blue-800', 'pending' => 'bg-amber-100 text-amber-800'];
        $rColors = ['available' => 'text-emerald-600', 'occupied' => 'text-red-600', 'reserved' => 'text-amber-600', 'cleaning' => 'text-sky-600', 'maintenance' => 'text-stone-500'];
        $rLabels = ['available' => 'Available', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'cleaning' => 'Cleaning', 'maintenance' => 'Maintenance'];
        $tabNames = ['guest' => 'Guest', 'room' => 'Room', 'payments' => 'Payments', 'timeline' => 'Timeline'];
        $firstGuest = $booking->guests->first();
    @endphp

    <div x-data="{ tab: 'guest' }" class="space-y-4">

        {{-- HEADER BAR --}}
        <div class="rounded-lg border border-stone-200 bg-white">
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('bookings.index') }}" class="text-sm text-stone-400 hover:text-stone-600">&larr; Back</a>
                            <span class="text-stone-200">|</span>
                            <span class="text-sm text-stone-500 font-mono">{{ $booking->booking_number }}</span>
                        </div>
                        <h1 class="text-xl font-semibold text-stone-900 mt-1">{{ $firstGuest?->name ?? 'Guest' }}</h1>
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payColors[$payStatus] }}">{{ ucfirst($payStatus) }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bStatusColors[$booking->status] }}">{{ str_replace('_', ' ', ucfirst($booking->status)) }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-stone-100 text-sm text-stone-500">
                    <span>{{ $booking->check_in instanceof \Carbon\Carbon ? $booking->check_in->format('d M Y') : $booking->check_in }} &rarr; {{ $booking->check_out instanceof \Carbon\Carbon ? $booking->check_out->format('d M Y') : $booking->check_out }}</span>
                    <span class="text-stone-300">&middot;</span>
                    <span>{{ $nights }} {{ $nights === 1 ? 'Night' : 'Nights' }}</span>
                    <span class="text-stone-300">&middot;</span>
                    <span class="capitalize">{{ str_replace('_', ' ', $booking->booking_type) }}</span>
                    @foreach($booking->rooms as $room)
                    <span class="text-stone-300">&middot;</span>
                    <span>Room {{ $room->room_number }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TABS + CONTENT --}}
        <div class="rounded-lg border border-stone-200 bg-white">
            {{-- Tab Nav --}}
            <div class="border-b border-stone-200">
                <nav class="flex">
                    @foreach($tabNames as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-stone-900 text-stone-900' : 'border-transparent text-stone-400 hover:text-stone-600'"
                        class="px-5 py-3 text-sm font-medium border-b-2 transition-colors">
                        {{ $label }}
                    </button>
                    @endforeach
                </nav>
            </div>

            {{-- Tab: Guest --}}
            <div x-show="tab === 'guest'" x-cloak class="p-5 space-y-4">
                @foreach($booking->guests as $guest)
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-stone-100 flex items-center justify-center text-sm font-medium text-stone-600 shrink-0">
                        {{ strtoupper(substr($guest->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-stone-900">{{ $guest->name }}</p>
                        <p class="text-xs text-stone-500 mt-0.5">{{ $guest->guest_type === 'main' ? 'Main Guest' : ucfirst($guest->guest_type) }}</p>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 mt-2 text-xs text-stone-500">
                            @if($guest->phone)<div><span class="text-stone-400">Phone</span><p class="text-stone-700">{{ $guest->phone }}</p></div>@endif
                            @if($guest->email)<div><span class="text-stone-400">Email</span><p class="text-stone-700">{{ $guest->email }}</p></div>@endif
                            @if($guest->id_number)<div><span class="text-stone-400">ID Number</span><p class="text-stone-700">{{ $guest->id_number }}</p></div>@endif
                            @if($guest->address)<div><span class="text-stone-400">Address</span><p class="text-stone-700">{{ $guest->address }}</p></div>@endif
                        </div>
                    </div>
                </div>
                @if(!$loop->last)<hr class="border-stone-100">@endif
                @endforeach
            </div>

            {{-- Tab: Room --}}
            <div x-show="tab === 'room'" x-cloak class="p-5 space-y-3">
                @foreach($booking->rooms as $room)
                @php
                    $rc = $rColors[$room->status] ?? 'text-stone-500';
                    $rl = $rLabels[$room->status] ?? ucfirst($room->status);
                    $price = $room->custom_price ?? $room->roomType->base_price ?? 0;
                @endphp
                <div class="border border-stone-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-stone-900">Room {{ $room->room_number }}</p>
                        <span class="text-xs font-medium {{ $rc }}">{{ $rl }}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-1.5 text-xs text-stone-500">
                        <span>{{ $room->roomType->name ?? 'N/A' }}</span>
                        <span class="text-stone-300">&middot;</span>
                        <span>Floor {{ $room->floor }}</span>
                        <span class="text-stone-300">&middot;</span>
                        <span>{{ number_format($price) }} TZS / night</span>
                    </div>
                    @php
                        $pivot = $room->pivot;
                    @endphp
                    @if($pivot && ($pivot->check_in ?? $booking->check_in))
                    <div class="flex items-center gap-3 mt-2 text-xs text-stone-400">
                        <span>Check in: {{ $pivot->check_in instanceof \Carbon\Carbon ? $pivot->check_in->format('d M Y') : ($pivot->check_in ?? $booking->check_in) }}</span>
                        <span class="text-stone-300">&middot;</span>
                        <span>Check out: {{ $pivot->check_out instanceof \Carbon\Carbon ? $pivot->check_out->format('d M Y') : ($pivot->check_out ?? $booking->check_out) }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Tab: Payments --}}
            <div x-show="tab === 'payments'" x-cloak class="p-5 space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-stone-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-stone-500">Total</p>
                        <p class="text-lg font-semibold text-stone-900">{{ number_format($booking->total_amount) }}</p>
                        <p class="text-xs text-stone-400">TZS</p>
                    </div>
                    <div class="bg-stone-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-stone-500">Paid</p>
                        <p class="text-lg font-semibold {{ $booking->paid_amount > 0 ? 'text-emerald-600' : 'text-stone-900' }}">{{ number_format($booking->paid_amount) }}</p>
                        <p class="text-xs text-stone-400">TZS</p>
                    </div>
                    <div class="bg-stone-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-stone-500">Due</p>
                        <p class="text-lg font-semibold {{ $booking->total_amount - $booking->paid_amount > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($booking->total_amount - $booking->paid_amount) }}</p>
                        <p class="text-xs text-stone-400">TZS</p>
                    </div>
                </div>

                @if($booking->payments->count())
                <div>
                    <p class="text-xs font-medium text-stone-500 mb-2">Payment Transactions</p>
                    <div class="space-y-1.5">
                        @foreach($booking->payments as $payment)
                        <div class="flex items-center justify-between text-sm bg-stone-50 rounded-lg px-3.5 py-2.5">
                            <div class="flex items-center gap-3">
                                <span class="font-medium text-stone-900">{{ number_format($payment->amount) }} TZS</span>
                                <span class="text-xs text-stone-400">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span>
                            </div>
                            <span class="text-xs text-stone-400">{{ $payment->paid_at instanceof \Carbon\Carbon ? $payment->paid_at->format('d M Y H:i') : $payment->paid_at }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Tab: Timeline --}}
            <div x-show="tab === 'timeline'" x-cloak class="p-5">
                <div class="space-y-0">
                    @if($booking->checked_out_at)
                    <div class="flex gap-3 pb-3">
                        <div class="flex flex-col items-center">
                            <div class="w-2 h-2 rounded-full bg-blue-500 ring-2 ring-blue-100"></div>
                            <div class="w-px h-full bg-stone-200 mt-1"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-900">Checked Out</p>
                            <p class="text-xs text-stone-500">{{ $booking->checked_out_at instanceof \Carbon\Carbon ? $booking->checked_out_at->format('d M Y - h:i A') : $booking->checked_out_at }}</p>
                        </div>
                    </div>
                    @endif
                    @if($booking->checked_in_at)
                    <div class="flex gap-3 pb-3">
                        <div class="flex flex-col items-center">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></div>
                            <div class="w-px h-full bg-stone-200 mt-1"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-900">Checked In</p>
                            <p class="text-xs text-stone-500">{{ $booking->checked_in_at instanceof \Carbon\Carbon ? $booking->checked_in_at->format('d M Y - h:i A') : $booking->checked_in_at }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex gap-3 pb-3">
                        <div class="flex flex-col items-center">
                            <div class="w-2 h-2 rounded-full {{ $booking->status === 'confirmed' ? 'bg-blue-500 ring-2 ring-blue-100' : 'bg-stone-300' }}"></div>
                            <div class="w-px h-full bg-stone-200 mt-1"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium {{ $booking->status === 'confirmed' ? 'text-stone-900' : 'text-stone-400' }}">Confirmed</p>
                            <p class="text-xs text-stone-500">{{ $booking->created_at->format('d M Y - h:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-2 h-2 rounded-full bg-stone-400 ring-2 ring-stone-100"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-900">Booking Created</p>
                            <p class="text-xs text-stone-500">{{ $booking->created_at->format('d M Y - h:i A') }} @if($booking->user) by {{ $booking->user->name }}@endif</p>
                        </div>
                    </div>
                </div>
                @if($booking->cancellation_reason)
                <div class="mt-4 pt-4 border-t border-stone-200">
                    <p class="text-xs font-medium text-red-500">Cancellation Reason</p>
                    <p class="text-sm text-stone-600 mt-1">{{ $booking->cancellation_reason }}</p>
                </div>
                @endif
                @if($booking->notes)
                <div class="mt-4 pt-4 border-t border-stone-200">
                    <p class="text-xs font-medium text-stone-500">Notes</p>
                    <p class="text-sm text-stone-600 mt-1">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ACTIONS BAR --}}
        <div class="rounded-lg border border-stone-200 bg-white p-4">
            <div class="flex flex-wrap gap-2">
                @if(in_array($booking->status, ['pending', 'confirmed']))
                <form method="POST" action="{{ route('bookings.checkin', $booking) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">Check In</button>
                </form>
                @endif
                @if($booking->status === 'checked_in')
                <form method="POST" action="{{ route('bookings.checkout', $booking) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">Check Out</button>
                </form>
                @endif
                @can('view-payments')
                <a href="{{ route('payments.create', $booking) }}" class="px-4 py-2 rounded-lg bg-stone-800 text-stone-50 text-sm font-medium hover:bg-stone-700 transition-colors shadow-sm">Add Payment</a>
                @endcan
                @if($booking->payments->count())
                <a href="{{ route('efd.from-payment', $booking->payments->first()->id) }}" class="px-4 py-2 rounded-lg bg-stone-100 text-stone-700 text-sm font-medium hover:bg-stone-200 transition-colors">EFD Receipt</a>
                @endif
                <a href="{{ route('bookings.edit', $booking) }}" class="px-4 py-2 rounded-lg bg-stone-100 text-stone-700 text-sm font-medium hover:bg-stone-200 transition-colors">Edit</a>
                <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-stone-100 text-stone-700 text-sm font-medium hover:bg-stone-200 transition-colors">Print</button>
                @if(!in_array($booking->status, ['cancelled', 'checked_out']))
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="inline" onsubmit="return confirm('Cancel this booking?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-medium hover:bg-red-100 transition-colors">Cancel</button>
                </form>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>
