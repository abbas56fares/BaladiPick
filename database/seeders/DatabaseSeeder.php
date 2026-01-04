<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * 
     * Creates:
     * - 1 Admin user
     * - 4 Shop users with shops in Lebanon
     * - 3 Delivery users (bike, car, pickup)
     * - 12 Orders (3 per shop, one for each vehicle type)
     * - Application settings for delivery cost calculation
     */
    public function run(): void
    {
        // Run seeders in order
        $this->call([
            UserSeeder::class,
            ShopSeeder::class,
            OrderSeeder::class,
            SettingsSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
    }
}
