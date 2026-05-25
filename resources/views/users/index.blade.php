<x-layouts.app title="Users">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">Users</h1>
                <p class="page-description">Manage system users and their roles</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add User
            </a>
        </div>

        <div class="card">
            <div class="card-content p-0">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="table-header-cell">Name</th>
                            <th class="table-header-cell">Email</th>
                            <th class="table-header-cell">Role</th>
                            <th class="table-header-cell hidden md:table-cell">Status</th>
                            <th class="table-header-cell hidden md:table-cell">Last Login</th>
                            <th class="table-header-cell text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="table-row">
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-stone-200 flex items-center justify-center text-xs font-semibold text-stone-600 uppercase">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="table-cell text-sm text-muted-foreground">{{ $user->email }}</td>
                            <td class="table-cell">
                                <span class="badge text-[10px] {{ $user->role === 'creator' ? 'badge-danger' : ($user->role === 'manager' ? 'badge-info' : 'badge-default') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="table-cell hidden md:table-cell">
                                <span class="badge text-[10px] {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="table-cell text-xs text-muted-foreground hidden md:table-cell">
                                {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}
                            </td>
                            <td class="table-cell text-right">
                                <a href="{{ route('users.edit', $user) }}" class="btn-ghost btn-sm">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-xs text-muted-foreground">Showing page {{ $users->currentPage() }} of {{ $users->lastPage() }}</p>
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>
