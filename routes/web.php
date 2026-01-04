<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

// Document Download Route
Route::get('/documents/{path}', function ($path) {
    $filePath = storage_path("app/public/{$path}");
    
    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }
    
    return response()->file($filePath);
})->where('path', '.*')->name('documents.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Shop Routes
Route::middleware(['auth', 'role:shop'])->prefix('shop')->name('shop.')->group(function () {
    Route::get('/dashboard', [ShopController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ShopController::class, 'profile'])->name('profile');
    Route::post('/profile', [ShopController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [ShopController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password', [ShopController::class, 'updatePassword'])->name('update-password');
    Route::get('/orders', [ShopController::class, 'orders'])->name('orders');
    Route::get('/orders/map', [ShopController::class, 'ordersMap'])->name('orders.map');
    Route::get('/orders/map/data', [ShopController::class, 'ordersMapData'])->name('orders.map.data');
    Route::get('/orders/create', [ShopController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders', [ShopController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders/{id}', [ShopController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{id}/edit', [ShopController::class, 'editOrder'])->name('orders.edit');
    Route::put('/orders/{id}', [ShopController::class, 'updateOrder'])->name('orders.update');
    Route::post('/orders/{id}/cancel', [ShopController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{id}/verify-pickup', [ShopController::class, 'verifyPickup'])->name('orders.verify-pickup');
    Route::post('/orders/verify-pickup-api', [ShopController::class, 'verifyPickupApi'])->name('orders.verify-pickup-api');
});

// Delivery Routes
Route::middleware(['auth', 'role:delivery'])->prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/dashboard', [DeliveryController::class, 'dashboard'])->name('dashboard');
    Route::get('/map', [DeliveryController::class, 'map'])->name('map');
    Route::get('/accepted-orders-map', [DeliveryController::class, 'acceptedOrdersMap'])->name('accepted.orders.map');
    Route::get('/settings', [DeliveryController::class, 'settings'])->name('settings');
    Route::post('/settings/update', [DeliveryController::class, 'updateSettings'])->name('settings.update');
    Route::get('/change-password', [DeliveryController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password', [DeliveryController::class, 'updatePassword'])->name('update-password');
    Route::get('/orders/available', [DeliveryController::class, 'availableOrders'])->name('orders.available');
    Route::get('/orders/accepted', [DeliveryController::class, 'acceptedOrders'])->name('orders.accepted');
    Route::post('/orders/{id}/accept', [DeliveryController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{id}/cancel', [DeliveryController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/orders/my', [DeliveryController::class, 'myOrders'])->name('orders.my');
    Route::get('/orders/{id}', [DeliveryController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/verify-qr', [DeliveryController::class, 'verifyQR'])->name('orders.verify-qr');
    Route::post('/orders/{id}/generate-otp', [DeliveryController::class, 'generateOTP'])->name('orders.generate-otp');
    Route::post('/orders/{id}/verify-otp', [DeliveryController::class, 'verifyOTP'])->name('orders.verify-otp');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Admin Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    
    // Settings Management
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    
    // Shop Management
    Route::get('/shops', [AdminController::class, 'shops'])->name('shops');
    Route::get('/shops/{id}', [AdminController::class, 'showShop'])->name('shops.show');
    Route::post('/shops/{id}/verify', [AdminController::class, 'verifyShop'])->name('shops.verify');
    Route::post('/shops/{id}/disable', [AdminController::class, 'disableShop'])->name('shops.disable');
    
    // Delivery Management
    Route::get('/deliveries', [AdminController::class, 'deliveries'])->name('deliveries');
    Route::get('/deliveries/{id}', [AdminController::class, 'showDelivery'])->name('deliveries.show');
    Route::post('/deliveries/{id}/verify', [AdminController::class, 'verifyDelivery'])->name('deliveries.verify');
    Route::post('/deliveries/{id}/disable', [AdminController::class, 'disableDelivery'])->name('deliveries.disable');
    
    // Vehicle-specific Delivery Management
    Route::get('/deliveries-bikes', [AdminController::class, 'deliveriesBike'])->name('deliveries.bikes');
    Route::get('/deliveries-cars', [AdminController::class, 'deliveriesCar'])->name('deliveries.cars');
    Route::get('/deliveries-pickups', [AdminController::class, 'deliveriesPickup'])->name('deliveries.pickups');
    
    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [AdminController::class, 'cancelOrder'])->name('orders.cancel');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('reports.export');
    Route::get('/reports/export/pdf', [AdminController::class, 'exportReportPdf'])->name('reports.export.pdf');
});

