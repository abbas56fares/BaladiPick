<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed users table with admin, shops, and delivery users.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@baladipick.com',
            'password' => bcrypt('password123'),
            'phone' => '+961-1-234567',
            'role' => 'admin',
            'verified' => true,
            'timezone' => 'Asia/Beirut',
        ]);

        // Create 4 Shop Users
        $shopData = [
            [
                'name' => 'Beirut Cafe & Restaurant',
                'email' => 'beirut.cafe@baladipick.com',
                'phone' => '+961-1-234568',
            ],
            [
                'name' => 'Tripoli Food Market',
                'email' => 'tripoli.market@baladipick.com',
                'phone' => '+961-6-234569',
            ],
            [
                'name' => 'Sidon Bakery Shop',
                'email' => 'sidon.bakery@baladipick.com',
                'phone' => '+961-7-234570',
            ],
            [
                'name' => 'Tyre General Store',
                'email' => 'tyre.store@baladipick.com',
                'phone' => '+961-7-234571',
            ],
        ];

        foreach ($shopData as $shop) {
            User::create([
                'name' => $shop['name'],
                'email' => $shop['email'],
                'password' => bcrypt('password123'),
                'phone' => $shop['phone'],
                'role' => 'shop',
                'verified' => true,
                'timezone' => 'Asia/Beirut',
            ]);
        }

        // Create 3 Delivery Users (one for each vehicle type)
        User::create([
            'name' => 'Ahmed - Bike Delivery',
            'email' => 'ahmed.bike@baladipick.com',
            'password' => bcrypt('password123'),
            'phone' => '+961-3-456789',
            'role' => 'delivery',
            'vehicle_type' => 'bike',
            'verified' => true,
            'timezone' => 'Asia/Beirut',
        ]);

        User::create([
            'name' => 'Fatima - Car Delivery',
            'email' => 'fatima.car@baladipick.com',
            'password' => bcrypt('password123'),
            'phone' => '+961-3-456790',
            'role' => 'delivery',
            'vehicle_type' => 'car',
            'verified' => true,
            'timezone' => 'Asia/Beirut',
        ]);

        User::create([
            'name' => 'Hassan - Pickup Delivery',
            'email' => 'hassan.pickup@baladipick.com',
            'password' => bcrypt('password123'),
            'phone' => '+961-3-456791',
            'role' => 'delivery',
            'vehicle_type' => 'pickup',
            'verified' => true,
            'timezone' => 'Asia/Beirut',
        ]);
    }
}
