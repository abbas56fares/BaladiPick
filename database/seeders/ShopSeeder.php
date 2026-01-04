<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed shops table with Lebanese shops.
     */
    public function run(): void
    {
        // Lebanon shop locations (realistic coordinates)
        $shops = [
            [
                'user_id' => 2, // Beirut Cafe & Restaurant
                'shop_name' => 'Beirut Cafe & Restaurant',
                'phone' => '+961-1-234568',
                'address' => 'Hamra Street, Beirut',
                'latitude' => 33.8547,
                'longitude' => 35.4889,
                'is_verified' => true,
            ],
            [
                'user_id' => 3, // Tripoli Food Market
                'shop_name' => 'Tripoli Food Market',
                'phone' => '+961-6-234569',
                'address' => 'Mina Street, Tripoli',
                'latitude' => 34.4386,
                'longitude' => 35.8395,
                'is_verified' => true,
            ],
            [
                'user_id' => 4, // Sidon Bakery Shop
                'shop_name' => 'Sidon Bakery Shop',
                'phone' => '+961-7-234570',
                'address' => 'Sea Castle, Sidon',
                'latitude' => 33.5613,
                'longitude' => 35.3698,
                'is_verified' => true,
            ],
            [
                'user_id' => 5, // Tyre General Store
                'shop_name' => 'Tyre General Store',
                'phone' => '+961-7-234571',
                'address' => 'Downtown Tyre, Tyre',
                'latitude' => 33.2732,
                'longitude' => 35.2222,
                'is_verified' => true,
            ],
        ];

        foreach ($shops as $shop) {
            Shop::create($shop);
        }
    }
}
