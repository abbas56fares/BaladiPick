## Vehicle-Type Delivery Management Pages - v2.31.0

### Overview
Added three vehicle-type filtered delivery management pages to the admin panel, allowing administrators to view and manage delivery drivers organized by vehicle type (bikes, cars, pickups).

### New Components

#### 1. Views Created
- `resources/views/admin/delivery-bikes.blade.php` - Bike delivery drivers overview
- `resources/views/admin/delivery-cars.blade.php` - Car delivery drivers overview
- `resources/views/admin/delivery-pickups.blade.php` - Pickup delivery drivers overview

Each view includes:
- **Stats Cards** (2x2 mobile, 1x4 desktop layout):
  - Total drivers for vehicle type
  - Verified drivers count
  - Total orders (for vehicle type)
  - Delivered orders (for vehicle type)
  
- **Search & Filter Form**:
  - Search by name, email, or phone
  - Filter by verification status (All/Verified/Unverified)
  
- **Drivers Table** with columns:
  - ID, Name, Email, Phone, ID Document link
  - Order count, Verification status, Registration date
  - View and Verify action buttons

- **Mobile Responsive Design**:
  - 2x2 card grid on mobile devices
  - Horizontal scrolling table on small screens
  - Touch-optimized buttons

#### 2. Controller Methods Added to AdminController
Three new public methods:
- `deliveriesBike(Request $request)` - Show bike drivers
- `deliveriesCar(Request $request)` - Show car drivers
- `deliveriesPickup(Request $request)` - Show pickup drivers

Two private helper methods:
- `getDeliveriesByVehicle($vehicleType, $search, $verified)` - Query builder for filtered drivers
- `getVehicleStats($vehicleType)` - Get statistics for each vehicle type

Features:
- Pagination (15 drivers per page)
- Search functionality
- Verification status filtering
- Vehicle-type specific metrics

#### 3. Routes Added to routes/web.php
```php
Route::get('/deliveries-bikes', [AdminController::class, 'deliveriesBike'])->name('deliveries.bikes');
Route::get('/deliveries-cars', [AdminController::class, 'deliveriesCar'])->name('deliveries.cars');
Route::get('/deliveries-pickups', [AdminController::class, 'deliveriesPickup'])->name('deliveries.pickups');
```

#### 4. Navigation Updates to layouts/app.blade.php
Modified admin navbar "Deliveries" link to a dropdown menu with:
- All Drivers (link to existing deliveries page)
- Divider
- Bike Drivers (new)
- Car Drivers (new)
- Pickup Drivers (new)

Each menu item includes appropriate Bootstrap icons:
- `bi-people-fill` for All Drivers
- `bi-bicycle` for Bike Drivers
- `bi-car-front` for Car Drivers
- `bi-truck` for Pickup Drivers

### Statistics Tracked per Vehicle Type

Each page displays:
1. **Total Drivers** - Count of registered drivers with vehicle_type
2. **Verified Drivers** - Count of verified drivers
3. **Total Orders** - Count of all orders for that vehicle type
4. **Delivered Orders** - Count of completed orders for that vehicle type

### Features per Vehicle Type Page

- **Search & Filtering**
  - Real-time search by name, email, phone
  - Filter unverified drivers for priority verification
  - Search state persists in pagination

- **Driver Management**
  - View driver details (linked to existing delivery details page)
  - Quick verification button for unverified drivers
  - ID document review links
  - Order count per driver

- **Mobile Optimization**
  - Stats cards displayed in 2x2 grid on phones
  - Responsive search form with dropdown on mobile
  - Horizontal scrolling tables instead of card view
  - Compact spacing and sizing

### Database Queries
Uses existing `User` and `Order` models:
- `User::where('role', 'delivery')->where('vehicle_type', $vehicleType)`
- `Order::where('vehicle_type', $vehicleType)`

All existing routes, policies, and relationships remain unchanged.

### Files Modified
1. **app/Http/Controllers/AdminController.php** - Added 5 methods
2. **routes/web.php** - Added 3 new routes
3. **resources/views/layouts/app.blade.php** - Updated navbar dropdown

### Files Created
1. **resources/views/admin/delivery-bikes.blade.php**
2. **resources/views/admin/delivery-cars.blade.php**
3. **resources/views/admin/delivery-pickups.blade.php**

### Next Steps
- Run `php artisan serve` to test the new pages
- Navigate to Admin > Deliveries dropdown to access vehicle-type pages
- Verify search, filter, and verification features work correctly
- Check mobile responsiveness on phones/tablets
