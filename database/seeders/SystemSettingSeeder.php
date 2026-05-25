<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::create([
            'key' => 'hotel_name',
            'value' => 'Inshotel Beach Resort',
        ]);

        SystemSetting::create([
            'key' => 'default_currency',
            'value' => 'TZS',
        ]);

        SystemSetting::create([
            'key' => 'check_in_time',
            'value' => '14:00',
        ]);

        SystemSetting::create([
            'key' => 'check_out_time',
            'value' => '11:00',
        ]);

        SystemSetting::create([
            'key' => 'default_vat_rate',
            'value' => '18',
        ]);

        SystemSetting::create([
            'key' => 'backup_enabled',
            'value' => 'true',
        ]);

        SystemSetting::create([
            'key' => 'backup_time',
            'value' => '02:00',
        ]);
    }
}
