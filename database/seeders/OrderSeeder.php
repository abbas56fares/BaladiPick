<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed orders table with 3 orders per shop (bike, car, pickup).
     */
    public function run(): void
    {
        // Client delivery locations in Lebanon
        $deliveryLocations = [
            [
                'client_name' => 'John Anderson',
                'client_phone' => '+961-3-111111',
                'latitude' => 33.8652,
                'longitude' => 35.4832,
                'address' => 'Ras Beirut'
            ],
            [
                'client_name' => 'Layla Hassan',
                'client_phone' => '+961-3-222222',
                'latitude' => 33.8742,
                'longitude' => 35.4903,
                'address' => 'Downtown Beirut'
            ],
            [
                'client_name' => 'Amira Al-Khaled',
                'client_phone' => '+961-3-333333',
                'latitude' => 33.8512,
                'longitude' => 35.5023,
                'address' => 'Verdun Street'
            ],
        ];

        // Get all shops
        $shops = Shop::all();

        foreach ($shops as $shop) {
            // Create 3 orders per shop (one for each vehicle type: bike, car, pickup)
            $vehicleTypes = ['bike', 'car', 'pickup'];
            $orders = [
                [
                    'vehicle_type' => 'bike',
                    'contents' => 'Small package - Coffee & Pastries',
                    'price' => 15.00,
                    'distance' => 2.5,
                    'cost' => 3.00,
                    'profit' => 1.50,
                ],
                [
                    'vehicle_type' => 'car',
                    'contents' => 'Medium order - Lunch delivery',
                    'price' => 45.00,
                    'distance' => 5.8,
                    'cost' => 6.50,
                    'profit' => 3.25,
                ],
                [
                    'vehicle_type' => 'pickup',
                    'contents' => 'Large catering order - Party supplies',
                    'price' => 120.00,
                    'distance' => 12.3,
                    'cost' => 15.00,
                    'profit' => 7.50,
                ],
            ];

            foreach ($orders as $index => $orderData) {
                $deliveryLocation = $deliveryLocations[$index];

                Order::create([
                    'shop_id' => $shop->id,
                    'delivery_id' => null,
                    'client_name' => $deliveryLocation['client_name'],
                    'client_phone' => $deliveryLocation['client_phone'],
                    'client_lat' => $deliveryLocation['latitude'],
                    'client_lng' => $deliveryLocation['longitude'],
                    'shop_lat' => $shop->latitude,
                    'shop_lng' => $shop->longitude,
                    'order_contents' => $orderData['contents'],
                    'order_price' => $orderData['price'],
                    'distance_km' => $orderData['distance'],
                    'vehicle_type' => $orderData['vehicle_type'],
                    'delivery_cost' => $orderData['cost'],
                    'profit' => $orderData['profit'],
                    'status' => 'available',
                    'qr_code' => 'QR-' . uniqid(),
                    'delivery_otp' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }
}
