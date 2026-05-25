<x-layouts.app title="Edit User">
    <div class="max-w-lg mx-auto space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Edit User</h1>
                <p class="page-description">{{ $user->name }} &middot; {{ ucfirst($user->role) }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn-secondary">Back</a>
        </div>

        <div class="card">
            <div class="card-content p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1.5">
                        <label class="label" for="name">Full Name</label>
                        <input id="name" type="text" name="name" class="input-field @error('name') border-destructive @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="input-field @error('email') border-destructive @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if(auth()->user()->role === 'creator' || $user->role !== 'creator')
                    <div class="space-y-1.5">
                        <label class="label" for="role">Role</label>
                        <select id="role" name="role" class="input-field @error('role') border-destructive @enderror">
                            <option value="receptionist" {{ old('role', $user->role) === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                            <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="creator" {{ old('role', $user->role) === 'creator' ? 'selected' : '' }}>Creator</option>
                        </select>
                        @error('role') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    <div class="flex items-center gap-3 py-2">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-stone-300 text-stone-800 focus:ring-stone-800" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="text-sm font-medium">Account Active</label>
                    </div>

                    <hr class="border-stone-200">

                    <p class="text-sm font-medium text-stone-700">Change Password</p>
                    <p class="text-xs text-muted-foreground -mt-3">Leave blank to keep current password</p>

                    <div class="space-y-1.5">
                        <label class="label" for="password">New Password</label>
                        <input id="password" type="password" name="password" class="input-field @error('password') border-destructive @enderror">
                        @error('password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="label" for="password_confirmation">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="input-field">
                    </div>

                    <button type="submit" class="btn-primary w-full">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
