<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        $creator = ['creator'];
        $manager = ['creator', 'manager'];
        $reception = ['creator', 'manager', 'receptionist'];

        Gate::define('view-dashboard', fn($u) => true);
        Gate::define('view-bookings', fn($u) => in_array($u->role, $reception));
        Gate::define('view-rooms', fn($u) => in_array($u->role, $reception));
        Gate::define('view-guests', fn($u) => in_array($u->role, $reception));
        Gate::define('view-payments', fn($u) => in_array($u->role, $manager));
        Gate::define('view-housekeeping', fn($u) => in_array($u->role, $reception));
        Gate::define('view-feature-flags', fn($u) => in_array($u->role, $creator));
        Gate::define('view-settings', fn($u) => in_array($u->role, $creator));
        Gate::define('view-activity-log', fn($u) => in_array($u->role, $manager));
        Gate::define('view-inventory', fn($u) => in_array($u->role, $manager));
        Gate::define('view-maintenance', fn($u) => in_array($u->role, $manager));
        Gate::define('view-pos', fn($u) => in_array($u->role, $manager));
        Gate::define('view-sync-queue', fn($u) => in_array($u->role, $creator));
        Gate::define('view-room-types', fn($u) => in_array($u->role, $manager));
        Gate::define('view-facilities', fn($u) => in_array($u->role, $creator));
        Gate::define('view-analytics', fn($u) => in_array($u->role, $manager));
        Gate::define('view-smart-keys', function ($u) {
            $enabled = \DB::table('feature_flags')->where('key', 'smart_key')->value('enabled');
            if ($enabled) {
                return in_array($u->role, ['creator', 'manager', 'receptionist']);
            }
            return in_array($u->role, ['creator']);
        });
        Gate::define('view-cctv', fn($u) => in_array($u->role, $manager));
        Gate::define('view-users', fn($u) => in_array($u->role, $manager));
        Gate::define('view-efd', fn($u) => in_array($u->role, $reception));
    }
}
