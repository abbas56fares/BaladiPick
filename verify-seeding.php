<?php

// Quick verification script
// Run with: php verify-seeding.php

require 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Setting;

echo "\n╔════════════════════════════════════════════════════════╗\n";
echo "║        BALADIPICK SEEDING VERIFICATION REPORT          ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

// Users Summary
echo "📋 USERS SUMMARY\n";
echo "─────────────────────────────────\n";
echo "Total Users: " . User::count() . "\n";
echo "  • Admins: " . User::where('role', 'admin')->count() . "\n";
echo "  • Shops: " . User::where('role', 'shop')->count() . "\n";
echo "  • Deliveries: " . User::where('role', 'delivery')->count() . "\n\n";

// Admin Details
echo "👤 ADMIN USER\n";
echo "─────────────────────────────────\n";
$admin = User::where('role', 'admin')->first();
echo "Email: {$admin->email}\n";
echo "Password: password123\n\n";

// Shop Details
echo "🏪 SHOPS\n";
echo "─────────────────────────────────\n";
foreach (User::where('role', 'shop')->get() as $shop) {
    $shopData = Shop::where('user_id', $shop->id)->first();
    echo "{$shop->name}\n";
    echo "  Email: {$shop->email}\n";
    echo "  Location: {$shopData->address}\n";
    echo "  Orders: " . Order::where('shop_id', $shopData->id)->count() . "\n\n";
}

// Delivery Details
echo "🚚 DELIVERY USERS\n";
echo "─────────────────────────────────\n";
foreach (User::where('role', 'delivery')->get() as $driver) {
    echo "{$driver->name}\n";
    echo "  Email: {$driver->email}\n";
    echo "  Vehicle: " . ucfirst($driver->vehicle_type) . "\n\n";
}

// Orders Summary
echo "📦 ORDERS SUMMARY\n";
echo "─────────────────────────────────\n";
echo "Total Orders: " . Order::count() . "\n";
echo "  • Bike Orders: " . Order::where('vehicle_type', 'bike')->count() . "\n";
echo "  • Car Orders: " . Order::where('vehicle_type', 'car')->count() . "\n";
echo "  • Pickup Orders: " . Order::where('vehicle_type', 'pickup')->count() . "\n\n";

// Orders by Shop
echo "📍 ORDERS BY SHOP\n";
echo "─────────────────────────────────\n";
foreach (Shop::all() as $shop) {
    $orders = Order::where('shop_id', $shop->id)->get();
    echo "{$shop->shop_name}: " . count($orders) . " orders\n";
    foreach ($orders as $order) {
        echo "  - " . ucfirst($order->vehicle_type) . " ({$order->order_contents})\n";
    }
}
echo "\n";

// Settings Summary
echo "⚙️  APPLICATION SETTINGS\n";
echo "─────────────────────────────────\n";
echo "Total Settings: " . Setting::count() . "\n\n";

echo "✅ SEEDING COMPLETE!\n";
echo "═════════════════════════════════════════════════════════\n\n";
