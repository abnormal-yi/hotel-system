<x-layouts.app title="{{ isset($booking) ? 'Edit Booking' : 'New Booking' }}">
    <div class="max-w-4xl mx-auto space-y-6" x-data="bookingForm()" x-init="init()">
        <div class="text-center">
            <h1 class="page-title">{{ isset($booking) ? 'Edit Booking' : 'New Booking' }}</h1>
            <p class="page-description">{{ isset($booking) ? 'Update booking details' : 'Create a new reservation' }}</p>
        </div>

        @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Step Indicator --}}
        <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
            <div class="flex items-center justify-between max-w-2xl mx-auto">
                <template x-for="(s, i) in steps" :key="i">
                    <div class="flex items-center">
                        <button type="button" @click="step > i + 1 ? step = i + 1 : null"
                            class="flex flex-col items-center gap-1.5 transition-all duration-300"
                            :class="step === i + 1 ? 'scale-105' : step > i + 1 ? 'cursor-pointer opacity-70 hover:opacity-100' : 'opacity-50 cursor-default'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold transition-all duration-300 shadow-sm"
                                :class="step === i + 1 ? 'bg-stone-800 text-white ring-4 ring-stone-100' : step > i + 1 ? 'bg-emerald-500 text-white' : 'bg-stone-100 text-stone-400'">
                                <svg x-show="step > i + 1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                <span x-show="step <= i + 1" x-text="i + 1"></span>
                            </span>
                            <span class="text-xs font-medium hidden sm:block" :class="step === i + 1 ? 'text-stone-800' : 'text-stone-400'" x-text="s"></span>
                        </button>
                        <template x-if="i < steps.length - 1">
                            <div class="w-16 sm:w-24 md:w-32 h-0.5 mx-2 sm:mx-4 rounded-full transition-all duration-500" :class="step > i + 1 ? 'bg-emerald-400' : 'bg-stone-200'"></div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <form method="POST" action="{{ isset($booking) ? route('bookings.update', $booking) : route('bookings.store') }}">
            @csrf
            @if(isset($booking)) @method('PUT') @endif
            <input type="hidden" name="hotel_id" value="{{ $hotels->first()?->id ?? 1 }}">

            {{-- ============================================= --}}
            {{-- STEP 1: GUEST INFORMATION                     --}}
            {{-- ============================================= --}}
            <div x-show="step === 1" x-cloak x-transition:enter.duration.300.opacity x-transition:leave.duration.150>
                <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-stone-900 to-stone-800 px-6 py-5">
                        <h2 class="text-white text-lg font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Who is checking in?
                        </h2>
                        <p class="text-stone-400 text-sm mt-0.5">Select number of guests and their details</p>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Guest Count --}}
                        <div>
                            <label class="label block mb-3 text-sm font-semibold text-stone-700">Number of Guests</label>
                            <div class="flex flex-wrap gap-3">
                                <template x-for="n in 5" :key="n">
                                    <button type="button" @click="guestCount = n; rebuildGuests()"
                                        class="w-14 h-14 rounded-xl font-bold text-base transition-all duration-200 border-2 flex flex-col items-center justify-center leading-tight"
                                        :class="guestCount === n ? 'bg-stone-800 text-white border-stone-800 shadow-lg scale-110' : 'bg-white text-stone-500 border-stone-200 hover:border-stone-400 hover:shadow'">
                                        <span x-text="n"></span>
                                        <span class="text-[9px] font-normal opacity-70">guest(s)</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Validation Error --}}
                        <div x-show="stepErrors[1] && stepErrors[1].length > 0" x-cloak
                            class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                            <p class="text-xs font-medium text-red-700">Please fix the following:</p>
                            <ul class="list-disc list-inside text-xs text-red-600 mt-1 space-y-0.5">
                                <template x-for="err in stepErrors[1]" :key="err">
                                    <li x-text="err"></li>
                                </template>
                            </ul>
                        </div>

                        {{-- Guest Forms --}}
                        <template x-for="(g, idx) in activeGuests" :key="idx">
                            <div class="rounded-xl border-2 transition-all duration-200 overflow-hidden"
                                :class="idx === 0
                                    ? 'border-emerald-300 shadow-sm' + (g.errors?.length ? ' border-red-300 bg-red-50/30' : ' bg-emerald-50/20')
                                    : 'border-stone-200' + (g.errors?.length ? ' border-red-300 bg-red-50/30' : '')">
                                <div class="flex items-center justify-between px-5 py-3"
                                    :class="idx === 0 ? 'bg-emerald-50 border-b border-emerald-200' : 'bg-stone-50 border-b border-stone-200'">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold shadow-sm"
                                            :class="idx === 0 ? 'bg-emerald-600 text-white' : 'bg-stone-400 text-white'"
                                            x-text="idx + 1"></span>
                                        <div>
                                            <span class="font-semibold text-sm" :class="idx === 0 ? 'text-emerald-900' : 'text-stone-700'"
                                                x-text="idx === 0 ? 'Main Guest' : 'Guest ' + (idx + 1)"></span>
                                            <select :name="'guests[' + idx + '][guest_type]'" x-model="g.guest_type"
                                                class="ml-2 text-[11px] border rounded-lg px-2 py-1 bg-white"
                                                :class="idx === 0 ? 'border-emerald-200' : 'border-stone-200'">
                                                <option value="main" x-bind:disabled="idx !== 0">Main</option>
                                                <option value="companion">Companion</option>
                                                <option value="child">Child</option>
                                            </select>
                                        </div>
                                    </div>
                                    <template x-if="idx > 0">
                                        <button type="button" @click="removeGuest(idx)"
                                            class="flex items-center gap-1 text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Remove
                                        </button>
                                    </template>
                                </div>

                                <div class="p-5 space-y-4">
                                    {{-- Existing Guest Search --}}
                                    <div class="space-y-1.5">
                                        <label class="label text-xs text-stone-500">Search Existing Guest</label>
                                        <div class="relative">
                                            <input type="text"
                                                x-model="g.searchQuery"
                                                @input="searchGuest(idx)"
                                                @focus="g.showDropdown = true"
                                                @keydown.escape="g.showDropdown = false"
                                                class="input-field text-sm pl-10"
                                                placeholder="Type name or phone to search...">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                            <div x-show="g.showDropdown && g.searchResults.length > 0" x-cloak
                                                @click.away="g.showDropdown = false"
                                                class="absolute z-20 w-full bg-white border border-stone-200 rounded-lg shadow-lg mt-1 max-h-40 overflow-y-auto">
                                                <template x-for="guest in g.searchResults" :key="guest.id">
                                                    <button type="button" @click="selectExistingGuest(idx, guest)"
                                                        class="w-full text-left px-4 py-2.5 hover:bg-stone-50 border-b border-stone-100 last:border-0 flex items-center gap-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-stone-400 shrink-0"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                        <div>
                                                            <p class="text-sm font-medium text-stone-800" x-text="guest.label"></p>
                                                            <p class="text-xs text-stone-400" x-text="guest.sub"></p>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Selected existing guest banner --}}
                                    <div x-show="g.selectedId" x-cloak
                                        class="flex items-center gap-3 text-sm bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600 shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        <div class="flex-1">
                                            <p class="font-medium text-emerald-900" x-text="g.selectedName"></p>
                                            <p class="text-xs text-emerald-600">Existing guest selected</p>
                                        </div>
                                        <button type="button" @click="clearGuest(idx)"
                                            class="text-xs font-medium text-emerald-600 hover:text-red-600 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors">Change</button>
                                    </div>

                                    {{-- New guest fields --}}
                                    <div x-show="!g.selectedId" class="space-y-4">
                                        <div class="space-y-1.5">
                                            <label class="label text-xs">Full Name <span class="text-red-500">*</span></label>
                                            <input type="text" :name="'guests[' + idx + '][name]'" x-model="g.name"
                                                class="input-field" placeholder="Full name"
                                                :class="g.errors?.includes('name') ? 'border-red-400 ring-red-200' : ''">
                                            <p x-show="g.errors?.includes('name')" class="text-[11px] text-red-500">Name is required</p>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div class="space-y-1.5">
                                                <label class="label text-xs">Phone</label>
                                                <input type="text" :name="'guests[' + idx + '][phone]'" x-model="g.phone" class="input-field" placeholder="+255...">
                                            </div>
                                            <div class="space-y-1.5">
                                                <label class="label text-xs">ID Number</label>
                                                <input type="text" :name="'guests[' + idx + '][id_number]'" x-model="g.id_number" class="input-field" placeholder="National ID / Passport">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div class="space-y-1.5">
                                                <label class="label text-xs">Email</label>
                                                <input type="email" :name="'guests[' + idx + '][email]'" x-model="g.email" class="input-field" placeholder="optional">
                                            </div>
                                            <div class="space-y-1.5">
                                                <label class="label text-xs">Address</label>
                                                <input type="text" :name="'guests[' + idx + '][address]'" x-model="g.address" class="input-field" placeholder="optional">
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" :name="'guests[' + idx + '][id]'" x-model="g.selectedId">
                                </div>
                            </div>
                        </template>

                        {{-- Add Guest Button --}}
                        <button type="button" @click="addGuest()" x-show="guestCount < 5"
                            class="flex items-center justify-center gap-2 w-full py-3 border-2 border-dashed border-stone-300 rounded-xl text-sm font-medium text-stone-500 hover:text-stone-700 hover:border-stone-400 hover:bg-stone-50 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Another Guest
                        </button>

                        <div class="flex justify-end pt-2">
                            <button type="button" @click="validateStep1()"
                                class="inline-flex items-center gap-2 bg-stone-800 text-white px-8 py-3 rounded-xl font-medium text-sm hover:bg-stone-700 transition-all shadow-lg hover:shadow-xl">
                                <span>Next Step</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- STEP 2: ROOMS & DATES                         --}}
            {{-- ============================================= --}}
            <div x-show="step === 2" x-cloak x-transition:enter.duration.300.opacity x-transition:leave.duration.150>
                <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-stone-900 to-stone-800 px-6 py-5">
                        <h2 class="text-white text-lg font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Room & Booking Details
                        </h2>
                        <p class="text-stone-400 text-sm mt-0.5">Select dates, rooms, and booking preferences</p>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Validation Error --}}
                        <div x-show="stepErrors[2] && stepErrors[2].length > 0" x-cloak
                            class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                            <p class="text-xs font-medium text-red-700">Please fix the following:</p>
                            <ul class="list-disc list-inside text-xs text-red-600 mt-1 space-y-0.5">
                                <template x-for="err in stepErrors[2]" :key="err">
                                    <li x-text="err"></li>
                                </template>
                            </ul>
                        </div>

                        {{-- Dates --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="label text-sm">Check In *</label>
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <input type="date" name="check_in" x-model="check_in"
                                        class="input-field pl-10"
                                        min="{{ date('Y-m-d') }}"
                                        @change="updateCheckout" required>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="label text-sm">Check Out *</label>
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <input type="date" name="check_out" x-model="check_out" class="input-field pl-10" required>
                                </div>
                            </div>
                        </div>

                        {{-- Nights summary --}}
                        <div x-show="check_in && check_out" x-cloak
                            class="bg-stone-50 rounded-lg px-4 py-3 border border-stone-200 flex items-center justify-between">
                            <span class="text-sm text-stone-600">Stay duration</span>
                            <span class="text-sm font-bold text-stone-800" x-text="nights + ' night(s)'"></span>
                        </div>

                        {{-- Rooms --}}
                        <div class="space-y-3">
                            <label class="label text-sm">Select Room(s) *</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-80 overflow-y-auto custom-scrollbar">
                                @foreach($rooms as $room)
                                @php
                                    $isOccupied = in_array($room->status, ['occupied', 'maintenance']);
                                    $color = match($room->status) {
                                        'available' => 'emerald',
                                        'occupied' => 'red',
                                        'reserved' => 'amber',
                                        'maintenance' => 'stone',
                                        'cleaning' => 'sky',
                                        default => 'stone',
                                    };
                                @endphp
                                <label
                                    class="relative flex flex-col rounded-xl border-2 cursor-pointer transition-all duration-150 overflow-hidden group"
                                    :class="room_ids.includes('{{ $room->id }}')
                                        ? 'border-stone-800 bg-stone-50 shadow-md'
                                        : 'border-stone-200 hover:border-stone-400 bg-white'"
                                    style="{{ $isOccupied ? 'opacity:0.5;cursor:not-allowed;' : '' }}">
                                    <input type="checkbox" name="room_ids[]" value="{{ $room->id }}"
                                        class="sr-only"
                                        x-model="room_ids"
                                        {{ $isOccupied ? 'disabled' : '' }}>
                                    <div class="p-3 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-stone-800">Room {{ $room->room_number }}</span>
                                            <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-150"
                                                :class="room_ids.includes('{{ $room->id }}') ? 'bg-stone-800 border-stone-800' : 'border-stone-300 group-hover:border-stone-500'">
                                                <svg x-show="room_ids.includes('{{ $room->id }}')" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            </div>
                                        </div>
                                        <span class="block text-[11px] text-stone-500">{{ $room->roomType?->name ?? 'Standard' }}</span>
                                        <span class="block text-xs font-bold text-stone-800">{{ number_format($room->custom_price ?? $room->roomType?->base_price ?? 0) }} TZS</span>
                                        <span class="block text-[10px] uppercase tracking-wider font-semibold"
                                            style="color: var(--{{ $color }}-600)">{{ $room->status }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            <p x-show="room_ids.length === 0" class="text-xs text-red-500">Select at least one room</p>
                        </div>

                        {{-- Selected Rooms Summary --}}
                        <div x-show="room_ids.length > 0" x-cloak
                            class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-emerald-800">
                                    <span x-text="room_ids.length"></span> room(s) selected
                                </span>
                                <span class="text-xs text-emerald-600">Total: <strong x-text="totalAmount + ' TZS'"></strong></span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="rid in room_ids" :key="rid">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-white border border-emerald-300 rounded-lg px-3 py-1.5 text-emerald-700 shadow-sm"
                                        x-text="roomLabel(rid)"></span>
                                </template>
                            </div>
                        </div>

                        {{-- Type & Source --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="label text-sm">Booking Type</label>
                                <select name="booking_type" x-model="booking_type" class="input-field">
                                    <option value="walk_in">Walk-in</option>
                                    <option value="advance">Advance Reservation</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="label text-sm">Source</label>
                                <select name="source" x-model="source" class="input-field">
                                    <option value="reception">Reception</option>
                                    <option value="phone">Phone</option>
                                    <option value="online">Online</option>
                                    <option value="agent">Agent</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="label text-sm">Notes</label>
                            <textarea name="notes" x-model="notes" class="input-field h-20 resize-none" placeholder="Special requests, notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="step = 1"
                        class="inline-flex items-center gap-2 btn-secondary px-6 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Back
                    </button>
                    <button type="button" @click="validateStep2()"
                        class="inline-flex items-center gap-2 bg-stone-800 text-white px-8 py-3 rounded-xl font-medium text-sm hover:bg-stone-700 transition-all shadow-lg">
                        <span>Review Booking</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- STEP 3: REVIEW & CONFIRM                      --}}
            {{-- ============================================= --}}
            <div x-show="step === 3" x-cloak x-transition:enter.duration.300.opacity x-transition:leave.duration.150>
                <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 px-6 py-5">
                        <h2 class="text-white text-lg font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Review & Confirm
                        </h2>
                        <p class="text-emerald-200 text-sm mt-0.5">Please verify all information before creating the booking</p>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Guests --}}
                        <div class="bg-stone-50 rounded-xl border border-stone-200 overflow-hidden">
                            <div class="bg-white border-b border-stone-200 px-5 py-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-stone-500"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <span class="font-semibold text-sm text-stone-700">Guests</span>
                                <span class="text-xs text-stone-400 ml-auto" x-text="guestCount + ' guest(s)'"></span>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="(g, idx) in activeGuests" :key="idx">
                                    <div class="bg-white rounded-lg border border-stone-200 p-4 flex items-start gap-3">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold shrink-0"
                                            :class="g.guest_type === 'main' ? 'bg-emerald-100 text-emerald-700' : g.guest_type === 'child' ? 'bg-sky-100 text-sky-700' : 'bg-stone-200 text-stone-600'"
                                            x-text="idx + 1"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-sm text-stone-800 truncate" x-text="g.selectedName || g.name"></span>
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase"
                                                    :class="g.guest_type === 'main' ? 'bg-emerald-100 text-emerald-700' : g.guest_type === 'child' ? 'bg-sky-100 text-sky-700' : 'bg-stone-100 text-stone-500'"
                                                    x-text="g.guest_type"></span>
                                            </div>
                                            <p class="text-xs text-stone-400 mt-0.5" x-text="g.phone || g.email || 'No contact'"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Booking Details --}}
                        <div class="bg-stone-50 rounded-xl border border-stone-200 overflow-hidden">
                            <div class="bg-white border-b border-stone-200 px-5 py-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-stone-500"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span class="font-semibold text-sm text-stone-700">Booking Details</span>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-2.5">
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-2.5 border border-stone-100">
                                            <span class="text-stone-500">Check In</span>
                                            <span class="font-semibold text-stone-800" x-text="check_in"></span>
                                        </div>
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-2.5 border border-stone-100">
                                            <span class="text-stone-500">Check Out</span>
                                            <span class="font-semibold text-stone-800" x-text="check_out"></span>
                                        </div>
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-2.5 border border-stone-100">
                                            <span class="text-stone-500">Duration</span>
                                            <span class="font-semibold text-stone-800" x-text="nights + ' night(s)'"></span>
                                        </div>
                                    </div>
                                    <div class="space-y-2.5">
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-2.5 border border-stone-100">
                                            <span class="text-stone-500">Type</span>
                                            <span class="font-semibold text-stone-800 capitalize" x-text="booking_type.replace('_', ' ')"></span>
                                        </div>
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-2.5 border border-stone-100">
                                            <span class="text-stone-500">Source</span>
                                            <span class="font-semibold text-stone-800 capitalize" x-text="source"></span>
                                        </div>
                                        <div class="flex items-center justify-between bg-white rounded-lg px-4 py-2.5 border border-stone-100">
                                            <span class="text-stone-500">Rooms</span>
                                            <span class="font-semibold text-stone-800" x-text="room_ids.length"></span>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="notes" x-cloak class="mt-3 bg-white rounded-lg border border-stone-100 px-4 py-3">
                                    <p class="text-xs font-medium text-stone-500 mb-1">Notes</p>
                                    <p class="text-sm text-stone-700" x-text="notes"></p>
                                </div>

                                <div class="mt-3 bg-white rounded-lg border border-stone-100 px-4 py-3">
                                    <p class="text-xs font-medium text-stone-500 mb-2">Selected Rooms</p>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="rid in room_ids" :key="rid">
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-stone-100 border border-stone-200 rounded-lg px-3 py-1.5 text-stone-700"
                                                x-text="roomLabel(rid)"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="bg-gradient-to-r from-stone-800 to-stone-900 rounded-xl p-5 shadow-lg flex items-center justify-between">
                            <div>
                                <p class="text-sm text-stone-400">Total Amount</p>
                                <p class="text-xs text-stone-500" x-text="room_ids.length + ' room(s) &times; ' + nights + ' night(s)'"></p>
                            </div>
                            <span class="text-3xl font-black text-white" x-text="totalAmount + ' TZS'"></span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="step = 2"
                        class="inline-flex items-center gap-2 btn-secondary px-6 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Back
                    </button>
                    <button type="submit" :disabled="submitting"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-3.5 rounded-xl font-semibold text-base transition-all shadow-lg hover:shadow-xl disabled:opacity-50">
                        <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <svg x-show="submitting" class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-dasharray="31.4 31.4" stroke-linecap="round"/></svg>
                        <span x-text="submitting ? 'Creating Booking...' : 'Confirm & Create Booking'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    function bookingForm() {
        return {
            steps: ['Guests', 'Rooms & Dates', 'Review'],
            step: 1,
            guestCount: {{ old('guests') ? count(old('guests')) : ($booking?->guests->count() ?? 1) }},
            guests: [],
            check_in: '{{ old('check_in', $booking?->check_in?->format('Y-m-d') ?? '') }}',
            check_out: '{{ old('check_out', $booking?->check_out?->format('Y-m-d') ?? '') }}',
            room_ids: {!! old('room_ids') ? json_encode(old('room_ids')) : ($booking ? json_encode($booking->rooms->pluck('id')->map(fn($id) => (string)$id)->values()->toArray()) : '[]') !!},
            booking_type: '{{ old('booking_type', $booking?->booking_type ?? 'walk_in') }}',
            source: '{{ old('source', $booking?->source ?? 'reception') }}',
            notes: '{{ old('notes', $booking?->notes ?? '') }}',
            submitting: false,
            searchTimeouts: [],
            stepErrors: { 1: [], 2: [] },

            init() {
                this.rebuildGuests();
                @if(old('guests'))
                const oldGuests = @json(old('guests'));
                oldGuests.forEach((og, i) => {
                    if (this.guests[i]) {
                        this.guests[i].name = og.name || '';
                        this.guests[i].phone = og.phone || '';
                        this.guests[i].email = og.email || '';
                        this.guests[i].id_number = og.id_number || '';
                        this.guests[i].address = og.address || '';
                        this.guests[i].guest_type = og.guest_type || (i === 0 ? 'main' : 'companion');
                        this.guests[i].selectedId = og.id || '';
                    }
                });
                @elseif($booking)
                @php
                    $existingGuestData = $booking->guests->map(fn($g) => [
                        'id' => $g->id,
                        'name' => $g->name,
                        'phone' => $g->phone,
                        'email' => $g->email,
                        'id_number' => $g->id_number,
                        'address' => $g->address,
                        'guest_type' => $g->pivot->is_primary ? 'main' : ($g->guest_type ?: 'companion'),
                    ])->values()->toArray();
                @endphp
                const existingGuests = @json($existingGuestData);
                existingGuests.forEach((g, i) => {
                    if (this.guests[i]) {
                        this.guests[i].selectedId = g.id;
                        this.guests[i].selectedName = g.name;
                        this.guests[i].name = g.name;
                        this.guests[i].phone = g.phone || '';
                        this.guests[i].email = g.email || '';
                        this.guests[i].id_number = g.id_number || '';
                        this.guests[i].address = g.address || '';
                        this.guests[i].guest_type = g.guest_type || (i === 0 ? 'main' : 'companion');
                    }
                });
                @endif
            },

            roomLabel(id) {
                const map = {
                    @foreach($rooms as $room) '{{ $room->id }}': '{{ $room->room_number }} ({{ $room->roomType?->name ?? '' }})',
                    @endforeach
                };
                return map[id] || 'Room ' + id;
            },

            makeGuest() {
                return {
                    selectedId: '',
                    selectedName: '',
                    name: '',
                    phone: '',
                    email: '',
                    id_number: '',
                    address: '',
                    guest_type: 'companion',
                    searchQuery: '',
                    searchResults: [],
                    showDropdown: false,
                    errors: [],
                };
            },

            rebuildGuests() {
                while (this.guests.length < this.guestCount) {
                    const g = this.makeGuest();
                    if (this.guests.length === 0) g.guest_type = 'main';
                    this.guests.push(g);
                }
                this.guests = this.guests.slice(0, this.guestCount);
                this.guests[0].guest_type = 'main';
            },

            get activeGuests() {
                return this.guests.slice(0, this.guestCount);
            },

            addGuest() {
                if (this.guestCount < 5) {
                    this.guestCount++;
                    this.rebuildGuests();
                }
            },

            removeGuest(idx) {
                this.guests.splice(idx, 1);
                this.guestCount--;
                if (this.guests.length > 0) this.guests[0].guest_type = 'main';
            },

            searchGuest(idx) {
                clearTimeout(this.searchTimeouts[idx]);
                const g = this.guests[idx];
                if (g.searchQuery.length < 1) { g.searchResults = []; return; }
                this.searchTimeouts[idx] = setTimeout(() => {
                    fetch('/dashboard/search?q=' + encodeURIComponent(g.searchQuery))
                        .then(r => r.json())
                        .then(data => {
                            g.searchResults = data.filter(d => d.type === 'guest');
                            g.showDropdown = true;
                        });
                }, 250);
            },

            selectExistingGuest(idx, guest) {
                const g = this.guests[idx];
                g.selectedId = guest.id;
                g.selectedName = guest.label;
                g.name = guest.label;
                g.searchQuery = guest.label;
                g.showDropdown = false;
                g.errors = [];
            },

            clearGuest(idx) {
                const g = this.guests[idx];
                g.selectedId = '';
                g.selectedName = '';
                g.searchQuery = '';
                g.name = '';
                g.phone = '';
                g.email = '';
                g.id_number = '';
                g.address = '';
                g.errors = [];
            },

            validateStep1() {
                const errors = [];
                const guests = this.activeGuests;
                for (let i = 0; i < guests.length; i++) {
                    const g = guests[i];
                    g.errors = [];
                    if (!g.selectedId && !g.name.trim()) {
                        g.errors.push('name');
                        errors.push('Guest ' + (i + 1) + ': Name is required');
                    }
                }
                this.stepErrors[1] = errors;
                if (errors.length === 0) {
                    this.step = 2;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            validateStep2() {
                const errors = [];
                if (!this.check_in) errors.push('Check-in date is required');
                if (!this.check_out) errors.push('Check-out date is required');
                if (this.check_in && this.check_out && this.check_out <= this.check_in) {
                    errors.push('Check-out must be after check-in');
                }
                if (this.room_ids.length === 0) errors.push('Select at least one room');
                this.stepErrors[2] = errors;
                if (errors.length === 0) {
                    this.step = 3;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            updateCheckout() {
                if (this.check_in && (!this.check_out || this.check_out <= this.check_in)) {
                    const d = new Date(this.check_in);
                    d.setDate(d.getDate() + 1);
                    this.check_out = this.formatDate(d);
                }
            },

            formatDate(d) {
                return d.getFullYear() + '-' +
                    String(d.getMonth() + 1).padStart(2, '0') + '-' +
                    String(d.getDate()).padStart(2, '0');
            },

            get nights() {
                if (!this.check_in || !this.check_out) return 0;
                const a = new Date(this.check_in);
                const b = new Date(this.check_out);
                return Math.max(0, Math.ceil((b - a) / (1000 * 60 * 60 * 24)));
            },

            get totalAmount() {
                let total = 0;
                @foreach($rooms as $room)
                if (this.room_ids.includes('{{ $room->id }}')) {
                    total += {{ $room->custom_price ?? $room->roomType?->base_price ?? 0 }};
                }
                @endforeach
                return total.toLocaleString();
            },
        };
    }
    </script>
    @endpush
</x-layouts.app>
