<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed settings table with default application settings.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'bike_base_cost',
                'value' => '2.00',
                'description' => 'Base delivery cost for bike deliveries',
            ],
            [
                'key' => 'bike_per_km_cost',
                'value' => '0.50',
                'description' => 'Cost per kilometer for bike deliveries',
            ],
            [
                'key' => 'car_base_cost',
                'value' => '4.00',
                'description' => 'Base delivery cost for car deliveries',
            ],
            [
                'key' => 'car_per_km_cost',
                'value' => '1.00',
                'description' => 'Cost per kilometer for car deliveries',
            ],
            [
                'key' => 'pickup_base_cost',
                'value' => '8.00',
                'description' => 'Base delivery cost for pickup deliveries',
            ],
            [
                'key' => 'pickup_per_km_cost',
                'value' => '2.00',
                'description' => 'Cost per kilometer for pickup deliveries',
            ],
            [
                'key' => 'shop_profit_percentage',
                'value' => '0.50',
                'description' => 'Shop profit percentage (50% of delivery cost)',
            ],
            [
                'key' => 'max_cancellation_count',
                'value' => '3',
                'description' => 'Maximum cancellations before cooldown',
            ],
            [
                'key' => 'cooldown_duration_hours',
                'value' => '24',
                'description' => 'Cooldown duration in hours after max cancellations',
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Beirut',
                'description' => 'Application timezone for Lebanon',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description'],
                ]
            );
        }
    }
}
