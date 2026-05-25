<x-layouts.guest>
    <div class="w-full max-w-sm mx-4">
        <div class="card p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold tracking-tight">Inshotel</h1>
                <p class="text-sm text-muted-foreground mt-2">Hotel Management System</p>
            </div>
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="input-field @error('email') border-destructive @enderror" value="{{ old('email') }}" required autofocus>
                    @error('email') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="input-field @error('password') border-destructive @enderror" required>
                    @error('password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                </div>
                @if(session('failed'))
                    <div class="text-sm text-destructive bg-destructive/10 rounded-lg p-3">{{ session('failed') }}</div>
                @endif
                <input type="hidden" name="remember" value="1">
                <button type="submit" class="btn-primary w-full">Sign In</button>
            </form>
        </div>
        <div class="mt-4 card p-4">
            <p class="text-xs text-muted-foreground font-medium mb-2">Demo Accounts:</p>
            <div class="text-xs text-muted-foreground space-y-1">
                <p>Creator: creator@inshotel.com / creator123</p>
                <p>Manager: manager@inshotel.com / manager123</p>
                <p>Reception: reception@inshotel.com / reception123</p>
            </div>
        </div>
    </div>
</x-layouts.guest>
