<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} | {{ config('app.name', 'Inshotel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grain-texture">
    <div class="flex min-h-screen bg-background" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
        {{-- Mobile backdrop --}}
        <div
            x-show="sidebarOpen && window.innerWidth < 768"
            x-transition:enter="transition-opacity duration-200"
            x-transition:leave="transition-opacity duration-200"
            x-cloak
            class="fixed inset-0 z-40 bg-black/50 md:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            x-show="sidebarOpen"
            x-transition:enter="transition-transform duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition-transform duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            x-cloak
            class="fixed inset-y-0 left-0 z-50 w-60 bg-sidebar-background border-r border-sidebar-border flex flex-col md:sticky md:z-auto md:translate-x-0"
        >
            {{-- Brand --}}
            <div class="p-6 pb-4 flex items-center justify-between border-b border-sidebar-border">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <span class="text-lg font-bold tracking-tight text-sidebar-foreground">{{ config('app.name', 'Inshotel') }}</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-sidebar-foreground hover:text-sidebar-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                    {{ request()->routeIs('dashboard') ? 'bg-stone-800 text-stone-50 shadow-[inset_0_1px_0px_rgba(255,255,255,0.25),inset_0_-2px_0px_rgba(0,0,0,0.35)]' : 'text-stone-700 hover:bg-stone-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Dashboard
                </a>

                {{-- Bookings --}}
                @can('view-bookings')
                <a href="{{ route('bookings.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                    {{ request()->routeIs('bookings.*') ? 'bg-stone-800 text-stone-50 shadow-[inset_0_1px_0px_rgba(255,255,255,0.25),inset_0_-2px_0px_rgba(0,0,0,0.35)]' : 'text-stone-700 hover:bg-stone-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                    Bookings
                </a>
                @endcan

                {{-- Rooms --}}
                @can('view-rooms')
                <a href="{{ route('rooms.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                    {{ request()->routeIs('rooms.*') ? 'bg-stone-800 text-stone-50 shadow-[inset_0_1px_0px_rgba(255,255,255,0.25),inset_0_-2px_0px_rgba(0,0,0,0.35)]' : 'text-stone-700 hover:bg-stone-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="M3 9V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4"/><path d="M12 13v4"/><path d="M9 17h6"/></svg>
                    Rooms
                </a>
                @endcan

                {{-- Guests --}}
                @can('view-guests')
                <a href="{{ route('guests.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                    {{ request()->routeIs('guests.*') ? 'bg-stone-800 text-stone-50 shadow-[inset_0_1px_0px_rgba(255,255,255,0.25),inset_0_-2px_0px_rgba(0,0,0,0.35)]' : 'text-stone-700 hover:bg-stone-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Guests
                </a>
                @endcan

                {{-- Payments --}}
                @can('view-payments')
                <a href="{{ route('payments.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                    {{ request()->routeIs('payments.*') ? 'bg-stone-800 text-stone-50 shadow-[inset_0_1px_0px_rgba(255,255,255,0.25),inset_0_-2px_0px_rgba(0,0,0,0.35)]' : 'text-stone-700 hover:bg-stone-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Payments
                </a>
                @endcan

                <hr class="my-4 border-sidebar-border">
            </nav>

            {{-- User footer --}}
            <div class="p-4 border-t border-sidebar-border">
                @auth
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="w-8 h-8 rounded-full bg-stone-200 flex items-center justify-center text-xs font-semibold text-stone-700 uppercase">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-sidebar-foreground truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-muted-foreground truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-muted-foreground hover:text-destructive transition-colors" title="Logout">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-h-screen">
            {{-- Top bar --}}
            <header class="sticky top-0 z-30 bg-background/80 backdrop-blur-sm border-b border-border">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-stone-700 hover:text-stone-900 transition-colors md:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                        </button>
                        <button @click="sidebarOpen = !sidebarOpen" class="text-stone-700 hover:text-stone-900 transition-colors hidden md:block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                        </button>
                        <div>
                            <h1 class="page-title">{{ $title ?? 'Dashboard' }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @auth
                        <span class="text-sm text-muted-foreground hidden sm:block">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 rounded-full bg-stone-200 flex items-center justify-center text-xs font-semibold text-stone-700 uppercase">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        @endauth
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            @session('success')
            <div class="px-6 pt-4">
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        {{ $value }}
                    </div>
                </div>
            </div>
            @endsession

            @session('error')
            <div class="px-6 pt-4">
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        {{ $value }}
                    </div>
                </div>
            </div>
            @endsession

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6 relative z-10">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
