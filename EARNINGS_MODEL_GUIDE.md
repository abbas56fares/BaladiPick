# Updated Earnings Model - Field Reference Guide

## Order Model Field Mapping

### Database Columns (in orders table)

| Column | Type | Purpose | Notes |
|--------|------|---------|-------|
| `order_price` | decimal(10,2) | Order/package value | What shop owner is selling (for insurance/reference) |
| `delivery_cost` | decimal(10,2) | Delivery fee | Calculated automatically; goes to delivery driver |
| `profit` | decimal(10,2) | Shop's earnings | Shop commission = (order_price × commission_rate) / 100 |
| `vehicle_type` | enum | Transport type | bike, car, or pickup; determines delivery cost & distance limits |
| `distance_km` | decimal(8,2) | Distance to deliver | Calculated using Haversine formula |

### Form Fields (Order Creation/Edit)

| Form Field | Stored As | Type | Notes |
|------------|-----------|------|-------|
| Order Contents | `order_contents` | text | Description of items |
| Order Value | `order_price` | decimal | Amount client is paying for items |
| Required Vehicle Type | `vehicle_type` | enum | Affects delivery cost & service range |
| Your Commission Rate | Calculated → `profit` | percentage | NOT stored directly; calculated to profit |

## Earnings Breakdown Example

### Scenario
- Order value: **$100**
- Commission rate: **10%**
- Distance: **5 km**
- Vehicle type: **Car**

### Calculations

**Shop Owner Earnings**
```
Shop Commission = (Order Value × Commission Rate) ÷ 100
               = ($100 × 10) ÷ 100
               = $10
```
Stored in: `orders.profit = 10.00`

**Delivery Driver Earnings**
Using default car rates:
```
Delivery Cost = (Base Fee + Distance × Rate/km) × Fuel Adjustment
             = ($3.50 + 5 × $0.60) × 1.00
             = $6.50
```
Stored in: `orders.delivery_cost = 6.50`

**Total Cost to Customer**: $100 (for items)  
**Platform Fees**: Handled separately by app  

## Commission Rate Logic

### When to Use Different Rates

| Rate | Order Type | Example |
|------|-----------|---------|
| 5-8% | High-value items | Electronics, jewelry |
| 10-15% | Standard items | Groceries, clothing |
| 15-20% | Low-value items | Small accessories |
| 20%+ | Very low-value items | Single items under $5 |

### Real-time Display

**Before Entry**: Shows live calculation as user types
```
Order Value: (empty or 0)
Your Earnings: $0.00
```

**After Entering Values**:
```
Order Value: $100.00
Commission Rate: 10%
Your Earnings: $10.00 ← Updates in real-time
```

## Flow Comparison

### Old System (Before Update)
```
Shop enters: "Delivery Profit" = $5
↓
Stored as: profit = 5.00
↓
Problem: Not tied to order value, confusing naming
```

### New System (After Update)
```
Shop enters: Order Value ($100) + Commission Rate (10%)
↓
System calculates: Commission = $100 × 10% = $10
↓
Stored as: profit = 10.00 (for earnings calculation)
↓
Benefits: Clear relationship, automatic calculation, scalable
```

## Service Area Visualization

### Map Display
Shows on order creation/edit form:
- **Shop location**: Red marker at shop coordinates
- **Service range**: Circle around shop
  - Radius = max distance for selected vehicle type
  - Color matches vehicle type
  - Dashed border, semi-transparent fill

### Vehicle Type Ranges
| Vehicle Type | Radius on Map | Color |
|--------------|---------------|-------|
| Bike | 10 km | Orange (#FFA500) |
| Car | 90 km | Blue (#4169E1) |
| Pickup | 90 km | Red (#DC143C) |

### Dynamic Updates
- Circle size changes when vehicle type dropdown changes
- No page reload needed
- Helps shop visualize delivery area before creating order

## API Response Fields (For Delivery Driver)

When delivery driver views available orders:

```json
{
  "id": 1,
  "client_name": "John Doe",
  "order_contents": "2 Electronics boxes",
  "order_price": 100.00,
  "vehicle_type": "car",
  "distance_km": 5.00,
  "delivery_cost": 6.50,  ← Driver earns this
  "profit": 10.00,         ← Not shown to driver (shop's commission)
  "status": "available"
}
```

Delivery driver sees:
- Distance: 5 km
- Delivery fee (their profit): $6.50

Delivery driver does NOT see:
- Order price ($100)
- Shop commission ($10)

## Payment Summary (Example Order)

| Party | Amount | Source |
|-------|--------|--------|
| **Shop** | $10.00 | Order value ($100) × 10% commission |
| **Delivery Driver** | $6.50 | Calculated from distance + vehicle type |
| **Customer** | -$100.00 | Charged for items |
| **Platform** | TBD | Any platform fee (not yet implemented) |

## Important Notes

1. **Commission is calculated, not stored**: Form takes commission_rate (%), database stores calculated profit ($)
2. **Backward compatible**: Existing orders still work, new orders use new system
3. **No delivery driver impact**: They still see their delivery_cost; commission is transparent to them
4. **Flexible for future**: Can add tier-based commissions, special rates, etc.
5. **Real-time feedback**: Shop sees earnings instantly as they adjust values

## Validation Rules

### Commission Rate
- Minimum: 0%
- Maximum: 100%
- Decimals: Yes (e.g., 10.5%)
- Required: Yes (for new orders)

### Order Value
- Minimum: $0.00
- Maximum: $9,999,999.99 (decimal(10,2) limit)
- Decimals: Yes (e.g., $19.99)
- Required: Yes

### Result (Earnings)
- Calculated: (order_price × commission_rate) / 100
- Minimum: $0.00
- Decimals: 2 places
- Example: $100 × 10% = $10.00

## Migration Path (If Updating Existing Orders)

For shops with old "flat profit" orders wanting to convert:
```
Old order: profit = $5.00, order_price = unknown
New order: order_price = $X, commission_rate = ($5 / $X) × 100

Example:
Old order: profit = $5, assumed order_price = $50
→ Equivalent rate: ($5 / $50) × 100 = 10%
```

Not automatic (preserves historical data); can be done manually if needed.

## Summary for Shop Owners

**You earn money two ways:**
1. ✅ **Commission on orders** - Percentage of what customer pays for items
2. ✅ **Visible in real-time** - See exactly how much you'll earn before creating order

**Delivery drivers earn separately:**
- From the delivery cost (based on distance and vehicle type)
- Not affected by your commission
- Clearly shown in driver interface

**Benefits for you:**
- Flexible commission rate per order
- More earnings on higher-value orders
- Clear, transparent system
- Visual service area helps plan operations
