<x-layouts.app title="Settings">
    <div class="space-y-6">
        <div class="section-header">
            <div>
                <h1 class="page-title">System Settings</h1>
                <p class="page-description">Manage system configuration</p>
            </div>
        </div>

        @session('success') <div class="text-sm text-green-800 bg-green-100 rounded-lg p-3">{{ $value }}</div> @endsession

        @php
            $labels = [
                'hotel_name' => 'Hotel Name',
                'hotel_address' => 'Address',
                'hotel_phone' => 'Phone',
                'hotel_email' => 'Email',
                'app_url' => 'App URL / Domain',
                'tax_rate' => 'Tax Rate (%)',
                'currency' => 'Currency',
                'default_check_in_time' => 'Default Check-in Time',
                'default_check_out_time' => 'Default Check-out Time',
                'receipt_footer' => 'Receipt Footer',
            ];
        @endphp

        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($labels as $settingKey => $label)
                <div class="card">
                    <div class="card-content">
                        <div class="space-y-2">
                            <label class="label">{{ $label }}</label>
                            <input type="text" name="{{ $settingKey }}" class="input-field" value="{{ $settings[$settingKey]->value ?? '' }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</x-layouts.app>
