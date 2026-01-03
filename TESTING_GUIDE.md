# Testing Guide - Delivery Cost Calculation System

## Prerequisites
- All migrations have been run successfully
- XAMPP is running (Apache & MySQL)
- You have admin, shop, and delivery accounts

## Test Scenarios

### 1. Test Admin Settings Management

#### Access Settings Page
1. Login as admin
2. Click "Settings" (gear icon) in the navigation menu
3. You should see the "Delivery Pricing Settings" page

#### View Default Settings
**Expected Values:**
- **Bike:** Base $2.00, Rate $0.30/km, Min $3.00, Max 10km
- **Car:** Base $3.50, Rate $0.60/km, Min $4.00, Max 90km
- **Pickup:** Base $10.00, Rate $1.25/km, Min $10.00, Max 90km
- **Fuel Adjustment:** 1.00

#### Modify Settings
1. Change bike base fee to $2.50
2. Change fuel adjustment to 1.15 (simulate 15% fuel increase)
3. Click "Save Settings"
4. Verify success message appears
5. Refresh page and confirm values persisted

### 2. Test Delivery Driver Registration

#### Register New Delivery Driver
1. Go to registration page
2. Fill in basic information
3. Select "Delivery Driver" role
4. **Vehicle Type field should appear automatically**
5. Select a vehicle type (bike/car/pickup)
6. Upload ID document
7. Complete registration

#### Verify Field Behavior
- Vehicle type field should be hidden when "Shop Owner" is selected
- Vehicle type should be required when "Delivery Driver" is selected
- Form should not submit without vehicle type for delivery role

### 3. Test Order Creation with New Fields

#### As Shop Owner - Create Order
1. Login as shop owner (must be verified)
2. Navigate to "Create Order"
3. Fill in client information and location

**Test Order Contents:**
```
Contents: "2 Electronics boxes - Laptop and accessories"
Order Value: $1500.00
Vehicle Type: Car
Shop Profit: $10.00
```

4. Submit the order
5. Check success message for:
   - Calculated distance
   - Calculated delivery cost

**Expected Calculation Example:**
- If distance is 5km with default car settings:
- Formula: (3.50 + 5 × 0.60) × 1.00 = $6.50
- Message should show: "Distance: 5.00 km, Delivery cost: $6.50"

#### Test Distance Validation
1. Create order with bike selected
2. Set client location more than 10km away
3. Try to submit
4. **Expected:** Error message: "Distance exceeds maximum range for bike (10 km)"

### 4. Test Order Editing

#### Edit Existing Order
1. Go to an order with status "available"
2. Click "Edit"
3. Change order contents to: "Updated: 3 boxes of documents"
4. Change order value to $50.00
5. Change vehicle type from car to pickup
6. Save changes

**Verify:**
- Distance is recalculated
- Delivery cost is recalculated based on new vehicle type
- Success message shows new values

### 5. Test Delivery Driver Order Filtering

#### Setup Test Data
Create 3 orders:
1. Bike order - 5km distance
2. Car order - 15km distance
3. Pickup order - 20km distance

#### Test as Bike Delivery Driver
1. Login as delivery driver with vehicle_type = 'bike'
2. Go to "Available Orders" map
3. **Expected:** Only see the bike order (5km)
4. Should NOT see car or pickup orders

#### Test as Car Delivery Driver
1. Login as delivery driver with vehicle_type = 'car'
2. Go to "Available Orders" map
3. **Expected:** Only see the car order (15km)
4. Should NOT see bike or pickup orders

#### Test Distance Filtering
1. Create bike order with 12km distance (exceeds 10km max)
2. Login as bike delivery driver
3. **Expected:** Order should NOT appear (outside range)

### 6. Test Order Detail Views

#### Shop View
1. Go to any order details page
2. **Verify displayed information:**
   - Order Contents
   - Order Value
   - Distance (km)
   - Delivery Cost
   - Shop Profit
   - All other existing fields

#### Delivery View
1. Login as delivery driver
2. Accept an order
3. View order details
4. **Verify displayed information:**
   - Order Contents
   - Order Value
   - Distance (km)
   - Delivery Fee (your earnings)
   - All other existing fields

### 7. Test Price Calculation Formulas

#### Test Minimum Charge
Create bike order with very short distance (0.5km):
- Formula: (2.00 + 0.5 × 0.30) × 1.00 = $2.15
- **Expected:** System applies minimum charge of $3.00

#### Test Fuel Adjustment
1. Set fuel adjustment to 1.20 in admin settings
2. Create car order with 10km distance:
   - Formula: (3.50 + 10 × 0.60) × 1.20 = $11.40
   - **Expected:** Delivery cost = $11.40

#### Test Pickup Truck Pricing
Create pickup order with 25km distance:
- Formula: (10.00 + 25 × 1.25) × 1.00 = $41.25
- **Expected:** Delivery cost = $41.25

### 8. Test Edge Cases

#### Zero Distance Order
1. Set client location same as shop location
2. **Expected:** Distance = 0km, but minimum charge applies

#### Maximum Distance Order
1. Create car order with exactly 90km distance
2. **Expected:** Order accepts (at limit)
3. Create car order with 91km distance
4. **Expected:** Error message about exceeding range

#### Empty Vehicle Type (Old Users)
If a delivery user was registered before the update (no vehicle_type):
1. Login as that user
2. Go to available orders
3. **Expected:** Message asking to update profile with vehicle type
4. No orders shown until vehicle_type is set

### 9. Test Admin Order View

#### Admin Order List
1. Login as admin
2. Go to Orders page
3. **Verify columns show:**
   - Order ID
   - Shop Name
   - Client Name
   - Vehicle Type (bike/car/pickup)
   - Distance
   - Delivery Cost
   - Status

### 10. Test Settings Cache

#### Verify Cache Performance
1. Make note of settings values
2. Manually check database and change a value directly
3. Refresh settings page
4. **Expected:** Shows old cached value (not database value)
5. Wait 1 hour OR clear cache: `php artisan cache:clear`
6. Refresh settings page
7. **Expected:** Now shows updated database value

## Database Verification Queries

You can run these in phpMyAdmin or MySQL CLI:

```sql
-- Check settings
SELECT * FROM settings;

-- Check users with vehicle types
SELECT id, name, role, vehicle_type FROM users WHERE role = 'delivery';

-- Check orders with new fields
SELECT id, vehicle_type, order_contents, order_price, distance_km, delivery_cost, profit 
FROM orders 
ORDER BY id DESC 
LIMIT 10;

-- Verify pickup enum value exists
DESCRIBE orders;  -- Check vehicle_type column definition
```

## Common Issues & Solutions

### Issue: Settings not showing default values
**Solution:** Run migrations again: `php artisan migrate:fresh` (Warning: loses data)

### Issue: Vehicle type field not appearing
**Solution:** Clear browser cache, check JavaScript console for errors

### Issue: Distance calculation returns 0
**Solution:** Verify shop has latitude/longitude set in profile

### Issue: Delivery drivers see no orders
**Solution:** 
1. Check if driver has vehicle_type set
2. Verify orders exist with matching vehicle_type
3. Check if orders are within driver's vehicle range

### Issue: Settings changes not applying
**Solution:** Clear cache: `php artisan cache:clear`

## Performance Testing

### Load Test Settings Cache
1. Create 100 orders in quick succession
2. Monitor page load times
3. **Expected:** Fast load times due to cached settings
4. Check logs for database queries
5. **Expected:** Settings only queried once, then cached

### Distance Calculation Performance
1. Create order with complex route
2. Monitor time taken for distance calculation
3. **Expected:** < 100ms for Haversine calculation

## Security Testing

### Test Settings Authorization
1. Try to access `/admin/settings` as shop owner
2. **Expected:** 403 Forbidden or redirect to shop dashboard

### Test Order Creation Authorization
1. Try to create order without being verified
2. **Expected:** Error message about verification

### Test SQL Injection
1. Try entering SQL in order_contents field:
   ```
   Contents: '; DROP TABLE orders; --
   ```
2. **Expected:** Field accepts as text, no SQL execution

## Success Criteria

✅ All 13 default settings exist in database  
✅ Vehicle type selection works on registration  
✅ Order creation calculates distance and cost automatically  
✅ Distance validation prevents out-of-range orders  
✅ Delivery drivers only see matching vehicle type orders  
✅ Order details show all new fields  
✅ Admin can modify settings and changes persist  
✅ Settings are cached for performance  
✅ Minimum charge is enforced  
✅ Fuel adjustment applies correctly  
✅ All vehicle types (bike/car/pickup) work  

## Regression Testing

Verify existing features still work:
- ✅ Shop registration
- ✅ Order status changes
- ✅ QR code verification
- ✅ OTP verification
- ✅ Payment processing
- ✅ Order history
- ✅ Admin user management
- ✅ Reports generation

## Next Steps After Testing

If all tests pass:
1. ✅ System is ready for production use
2. Update user documentation
3. Train users on new fields
4. Monitor real-world pricing accuracy

If tests fail:
1. Document specific failures
2. Check error logs in `storage/logs/`
3. Review database integrity
4. Fix issues and retest
