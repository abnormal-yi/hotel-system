<x-layouts.app title="{{ isset($guest) ? 'Edit Guest' : 'Add Guest' }}">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('guests.index') }}" class="text-sm text-stone-400 hover:text-stone-600">&larr; Back</a>
            </div>
            <h1 class="page-title mt-1">{{ isset($guest) ? 'Edit Guest' : 'Add Guest' }}</h1>
            <p class="page-description">{{ isset($guest) ? 'Update guest information' : 'Register a new guest' }}</p>
        </div>
        <div class="rounded-lg border border-stone-200 bg-white">
            <div class="p-6">
                <form method="POST" action="{{ isset($guest) ? route('guests.update', $guest) : route('guests.store') }}" class="space-y-4">
                    @csrf @if(isset($guest)) @method('PUT') @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="name">Name *</label>
                            <input id="name" name="name" class="input-field" value="{{ old('name', $guest->name ?? '') }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="phone">Phone (+255XXXXXXXXX)</label>
                            <input id="phone" name="phone" class="input-field" value="{{ old('phone', $guest->phone ?? '') }}" placeholder="+255712000000">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="email">Email</label>
                            <input id="email" name="email" type="email" class="input-field" value="{{ old('email', $guest->email ?? '') }}">
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="nida_number">NIDA Number (20 digits)</label>
                            <input id="nida_number" name="nida_number" class="input-field" value="{{ old('nida_number', $guest->nida_number ?? '') }}" maxlength="20" placeholder="199812345678901234">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="id_number">Passport / ID (for non-Tanzanians)</label>
                            <input id="id_number" name="id_number" class="input-field" placeholder="Passport or National ID" value="{{ old('id_number', $guest->id_number ?? '') }}">
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="nationality">Nationality</label>
                            <input id="nationality" name="nationality" class="input-field" placeholder="e.g. Tanzania, Kenya, Uganda" value="{{ old('nationality', $guest->nationality ?? '') }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="label" for="guest_type">Guest Type</label>
                            <select id="guest_type" name="guest_type" class="input-field">
                                <option value="main" @selected(old('guest_type', $guest->guest_type ?? '') === 'main')>Main</option>
                                <option value="companion" @selected(old('guest_type', $guest->guest_type ?? '') === 'companion')>Companion</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="label" for="status">Status</label>
                            <select id="status" name="status" class="input-field">
                                <option value="new" @selected(old('status', $guest->status ?? '') === 'new')>New</option>
                                <option value="active" @selected(old('status', $guest->status ?? '') === 'active')>Active</option>
                                <option value="vip" @selected(old('status', $guest->status ?? '') === 'vip')>VIP</option>
                                <option value="blacklisted" @selected(old('status', $guest->status ?? '') === 'blacklisted')>Blacklisted</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="label" for="address">Address</label>
                        <textarea id="address" name="address" class="input-field h-20">{{ old('address', $guest->address ?? '') }}</textarea>
                    </div>

                    @if(isset($guest))
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_blacklisted" name="is_blacklisted" value="1" @checked(old('is_blacklisted', $guest->is_blacklisted))>
                        <label class="label mb-0" for="is_blacklisted">Blacklist this guest</label>
                    </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary">{{ isset($guest) ? 'Update Guest' : 'Create Guest' }}</button>
                        <a href="{{ route('guests.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
