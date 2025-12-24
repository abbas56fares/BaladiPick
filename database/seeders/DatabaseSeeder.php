<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@baladipick.com',
            'password' => bcrypt('password'),
            'phone' => '0501234567',
            'role' => 'admin',
            'verified' => true,
        ]);

        // Create sample shop user
        $shopUser = User::create([
            'name' => 'Shop Owner',
            'email' => 'shop@baladipick.com',
            'password' => bcrypt('password'),
            'phone' => '0509876543',
            'role' => 'shop',
            'verified' => true,
        ]);

        // Create shop profile
        \App\Models\Shop::create([
            'user_id' => $shopUser->id,
            'shop_name' => 'Test Shop',
            'phone' => '0509876543',
            'address' => 'Tel Aviv, Israel',
            'latitude' => 32.0853,
            'longitude' => 34.7818,
            'is_verified' => true,
        ]);

        // Create sample delivery user
        User::create([
            'name' => 'Delivery Driver',
            'email' => 'delivery@baladipick.com',
            'password' => bcrypt('password'),
            'phone' => '0507654321',
            'role' => 'delivery',
            'verified' => true,
        ]);
    }
}
