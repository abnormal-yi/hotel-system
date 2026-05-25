<x-layouts.app title="Add User">
    <div class="max-w-lg mx-auto space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Add User</h1>
                <p class="page-description">Create a new system user</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn-secondary">Back</a>
        </div>

        <div class="card">
            <div class="card-content p-6">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="label" for="name">Full Name</label>
                        <input id="name" type="text" name="name" class="input-field @error('name') border-destructive @enderror" value="{{ old('name') }}" required>
                        @error('name') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="input-field @error('email') border-destructive @enderror" value="{{ old('email') }}" required>
                        @error('email') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="label" for="role">Role</label>
                        <select id="role" name="role" class="input-field @error('role') border-destructive @enderror" required>
                            <option value="">Select role...</option>
                            <option value="receptionist" {{ old('role') === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                        @error('role') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="label" for="password">Password</label>
                        <input id="password" type="password" name="password" class="input-field @error('password') border-destructive @enderror" required>
                        @error('password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="label" for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="input-field" required>
                    </div>

                    <button type="submit" class="btn-primary w-full">Create User</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
