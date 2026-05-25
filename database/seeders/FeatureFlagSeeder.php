<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        FeatureFlag::create([
            'key' => 'pos_enabled',
            'label' => 'POS System',
            'enabled' => true,
            'module' => 'pos',
        ]);

        FeatureFlag::create([
            'key' => 'cctv_enabled',
            'label' => 'CCTV Integration',
            'enabled' => false,
            'module' => 'security',
        ]);

        FeatureFlag::create([
            'key' => 'smart_locks_enabled',
            'label' => 'Smart Door Locks',
            'enabled' => false,
            'module' => 'security',
        ]);

        FeatureFlag::create([
            'key' => 'efd_enabled',
            'label' => 'EFD Receipt Printing',
            'enabled' => true,
            'module' => 'compliance',
        ]);

        FeatureFlag::create([
            'key' => 'online_booking_enabled',
            'label' => 'Online Booking',
            'enabled' => false,
            'module' => 'booking',
        ]);

        FeatureFlag::create([
            'key' => 'inventory_enabled',
            'label' => 'Inventory System',
            'enabled' => false,
            'module' => 'inventory',
        ]);

        FeatureFlag::create([
            'key' => 'housekeeping_enabled',
            'label' => 'Housekeeping Module',
            'enabled' => true,
            'module' => 'operations',
        ]);
    }
}
