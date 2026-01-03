# Quick Reference: Auto Calculation Changes

## What Changed?

**Before**: Shop had to enter commission rate (%) for each order  
**After**: System automatically calculates commission (10% of order value) and delivery cost

## Form Fields Removed

❌ Removed from **Create Order** form:
- "Your Commission Rate (%)" input field

❌ Removed from **Edit Order** form:
- "Your Commission Rate (%)" input field

## Required Form Fields (Still Present)

✅ Create/Edit Order still requires:
1. Client Name
2. Client Phone
3. Client Location (coordinates)
4. Order Contents
5. **Order Value ($)** ← This is what commission is based on now
6. Vehicle Type ← This determines delivery cost

## Auto-Calculated Values

### Delivery Cost
Calculated from admin settings formula:
```
Base Fee + (Distance × Rate Per KM × Fuel Adjustment) 
or Minimum Charge (whichever is higher)
```

### Shop Commission
```
Order Value × 10% ÷ 100 = Earnings
```

**Example**: $50 order → $5 commission

## Success Message Format

After creating/updating order:
```
Order created successfully. 
Distance: 5.5 km, 
Delivery cost: $4.25. 
Your earnings: $6.50
```

## Controller Logic

### storeOrder()
```php
// Validation - no commission_rate needed
$validated = $request->validate([
    'order_price' => 'required|numeric|min:0',
    'vehicle_type' => 'required|in:bike,car,pickup',
    // ... other fields
]);

// Auto-calculation
$deliveryCost = $calculator->calculate($vehicleType, $distance);
$shopCommission = ($order_price * 10) / 100; // 10% default
```

### updateOrder()
Same logic as storeOrder()

## Changing Default Commission Rate

To change from 10% to a different rate, edit:

**File**: `app/Http/Controllers/ShopController.php`

**Location 1** - storeOrder() method:
```php
$defaultCommissionRate = 10; // Change this number
```

**Location 2** - updateOrder() method:
```php
$defaultCommissionRate = 10; // Change this number
```

## Database Impact

✅ **No migrations needed**
- Uses existing `delivery_cost` column
- Uses existing `profit` column (stores commission)
- No new columns required

## Testing Quick Checks

1. **Create an order** - no commission field shows ✓
2. **Submit form** - no validation error on missing commission_rate ✓
3. **View success message** - shows distance, cost, and earnings ✓
4. **Check order record** - has correct profit value (10% of order_price) ✓
5. **Edit order** - recalculates commission automatically ✓

## Common Scenarios

| Scenario | Before | After |
|----------|--------|-------|
| Create $100 order | Manual % entry → $? earnings | Auto: $10 earnings |
| Edit order | Manual % entry | No manual entry needed |
| What if distance changes? | Delivery cost NOT recalc | Auto recalculated |
| What if order value changes? | Manual % entry needed | Auto 10% applied |

## Files Changed

| File | Change |
|------|--------|
| `create-order.blade.php` | Removed commission_rate field & JS |
| `edit-order.blade.php` | Removed commission_rate field & JS |
| `ShopController.php` | Removed commission_rate validation, added auto-calc |

## Status

✅ Code errors: 0  
✅ Ready to test: Yes  
✅ Breaking changes: No  
✅ Database migrations: Not needed

---

**Date**: January 3, 2026
