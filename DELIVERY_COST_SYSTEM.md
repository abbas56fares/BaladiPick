# Delivery Cost Calculation System Implementation

## Overview
Implemented a comprehensive delivery cost calculation system for the BaladiPick application based on Lebanon 2026 market pricing. The system features admin-controlled pricing formulas, automatic distance calculation, vehicle type matching, and distance-based order filtering.

## Key Features

### 1. Dynamic Pricing System
- **Formula**: `(Base Fee + Distance × Rate/km) × Fuel Adjustment`
- **Minimum Charge**: System enforces minimum charge per vehicle type
- **Vehicle Types**: 
  - Bike/Motorcycle (up to 10km range)
  - Car (up to 90km range)
  - Pickup Truck (up to 90km range)
- **Fuel Adjustment**: Multiplier for market fuel price changes (default: 1.00)

### 2. Database Changes

#### New Tables
- **settings**: Stores admin-configurable pricing formulas and system settings
  - 13 default settings for pricing (base fees, rates, minimums, max distances)
  - Cached for performance (1 hour cache duration)

#### Modified Tables
- **orders**: Added fields:
  - `order_contents` (text): Description of items being delivered
  - `order_price` (decimal): Value of items for insurance/reference
  - `distance_km` (decimal): Calculated distance in kilometers
  - `delivery_cost` (decimal): Automatically calculated delivery fee
  - `vehicle_type`: Updated enum to include 'pickup'

- **users**: Added field:
  - `vehicle_type` (enum): bike/car/pickup for delivery drivers

### 3. New Components

#### Models
- **Setting.php**: 
  - Static methods: `get()`, `set()`, `getAllSettings()`
  - 1-hour cache for performance
  - Key-value storage with descriptions

#### Services
- **DeliveryCostCalculator.php**:
  - `calculate($vehicleType, $distanceKm)`: Calculates delivery cost using formula
  - `calculateDistance($lat1, $lng1, $lat2, $lng2)`: Haversine formula for distance
  - `getMaxDistance($vehicleType)`: Returns max range for vehicle
  - `isWithinRange($vehicleType, $distanceKm)`: Validation helper

#### Controllers
- **AdminSettingsController.php**:
  - `index()`: Display settings management page
  - `update()`: Save settings and clear cache

### 4. Updated Features

#### Registration
- Added vehicle type selection for delivery drivers
- Conditional field that appears when "delivery" role is selected
- JavaScript validation to ensure required when role is delivery

#### Order Creation/Editing (Shop)
- Added `order_contents` textarea field
- Added `order_price` input field
- Updated vehicle type dropdown to include pickup trucks
- Automatic distance calculation using shop and client coordinates
- Automatic delivery cost calculation
- Distance validation against vehicle type max range
- Success messages show calculated distance and cost

#### Delivery Order Filtering
- Delivery drivers only see orders matching their vehicle type
- Orders filtered by distance within vehicle's max range
- API responses include vehicle type and max distance info

#### Order Details Views
- Shop and delivery order detail pages now display:
  - Order contents
  - Order value
  - Distance in kilometers
  - Delivery cost
- Conditional rendering (only shows if data exists)

### 5. Admin Settings Interface

#### Settings Page
Located at: `/admin/settings`

**Configurable Parameters:**

**Bike/Motorcycle:**
- Base Fee (default: $2.00)
- Rate per KM (default: $0.30)
- Minimum Charge (default: $3.00)
- Max Distance (default: 10 km)

**Car:**
- Base Fee (default: $3.50)
- Rate per KM (default: $0.60)
- Minimum Charge (default: $4.00)
- Max Distance (default: 90 km)

**Pickup Truck:**
- Base Fee (default: $10.00)
- Rate per KM (default: $1.25)
- Minimum Charge (default: $10.00)
- Max Distance (default: 90 km)

**Fuel Adjustment:**
- Multiplier (default: 1.00, range: 0.1 - 5.0)
- Allows for market-based price adjustments

#### Features:
- Real-time formula display
- Validation on all inputs
- Cache clearing on update
- Success notifications
- Link in admin navigation menu

## Files Modified

### Database Migrations
1. `2025_01_03_000001_create_settings_table.php` - New settings table
2. `2025_01_03_000002_add_delivery_cost_fields_to_orders_table.php` - Order table updates
3. `2025_01_03_000003_add_vehicle_type_to_users_table.php` - User table updates

### Models
1. `app/Models/Setting.php` - New
2. `app/Models/Order.php` - Updated fillable and casts
3. `app/Models/User.php` - Added vehicle_type to fillable

### Services
1. `app/Services/DeliveryCostCalculator.php` - New

### Controllers
1. `app/Http/Controllers/AdminSettingsController.php` - New
2. `app/Http/Controllers/AuthController.php` - Updated registration
3. `app/Http/Controllers/ShopController.php` - Updated order create/update
4. `app/Http/Controllers/DeliveryController.php` - Updated filtering

### Views
1. `resources/views/admin/settings.blade.php` - New
2. `resources/views/auth/register.blade.php` - Added vehicle type field
3. `resources/views/shop/create-order.blade.php` - Added new fields
4. `resources/views/shop/edit-order.blade.php` - Added new fields
5. `resources/views/shop/order-details.blade.php` - Display new info
6. `resources/views/delivery/order-details.blade.php` - Display new info
7. `resources/views/layouts/app.blade.php` - Added settings link

### Routes
1. `routes/web.php` - Added settings routes

## Usage Example

### Admin
1. Navigate to Settings from admin menu
2. Adjust pricing formulas as needed
3. Modify fuel adjustment multiplier based on market conditions
4. Save changes (all new orders use updated pricing)

### Shop Owner
1. Create new order
2. Fill in client information and location
3. Add order contents description
4. Enter order value
5. Select required vehicle type
6. System automatically:
   - Calculates distance
   - Validates against vehicle range
   - Calculates delivery cost
   - Shows summary in success message

### Delivery Driver
1. Register with vehicle type selection
2. View available orders map
3. Only see orders matching vehicle type and within range
4. View complete order details including:
   - What's being delivered
   - Order value
   - Distance to travel
   - Delivery fee earned

## Pricing Examples

**Bike - 5km delivery:**
- Formula: (2.00 + 5 × 0.30) × 1.00 = $3.50
- Applied: $3.50 (exceeds minimum of $3.00)

**Car - 2km delivery:**
- Formula: (3.50 + 2 × 0.60) × 1.00 = $4.70
- Applied: $4.70 (exceeds minimum of $4.00)

**Pickup - 15km delivery with 1.15 fuel adjustment:**
- Formula: (10.00 + 15 × 1.25) × 1.15 = $33.00
- Applied: $33.00 (exceeds minimum of $10.00)

## Benefits

1. **Transparency**: Clear pricing visible to all parties
2. **Flexibility**: Admin can adjust prices without code changes
3. **Accuracy**: Automatic distance calculation prevents errors
4. **Market Responsive**: Fuel adjustment allows for price updates
5. **Efficiency**: Only relevant orders shown to delivery drivers
6. **Safety**: Distance validation prevents impossible deliveries
7. **Performance**: Settings cached for fast access

## Technical Notes

- All monetary values stored as `decimal(10,2)`
- Distance stored as `decimal(8,2)` (up to 999,999.99 km)
- Haversine formula provides accurate great-circle distance
- Settings cached with Laravel Cache facade (1-hour TTL)
- Enum validation on vehicle types ensures data integrity
- Migration system allows for database rollback if needed

## Migration Status
✅ All migrations executed successfully
✅ No database errors
✅ No code errors detected
