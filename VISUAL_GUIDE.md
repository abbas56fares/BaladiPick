# Visual Guide - New Order Creation Interface

## Form Layout - Create Order Page

```
┌─────────────────────────────────────────────────────────────────┐
│                     Create New Delivery Order                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  CLIENT INFORMATION                                              │
│  ┌──────────────────┐  ┌──────────────────┐                      │
│  │ Client Name      │  │ Client Phone     │                      │
│  │ [John Doe      ] │  │ [961-76-123456]  │                      │
│  └──────────────────┘  └──────────────────┘                      │
│                                                                   │
│  CLIENT LOCATION                                                 │
│  Search Location: [location search]  [Search]                   │
│                                                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                                                               │ │
│  │                    🗺 MAP VIEW                                │ │
│  │           ┌─────────────────────────┐                        │ │
│  │           │     🔴 Your Shop       │                        │ │
│  │           │      (Red marker)      │                        │ │
│  │           │                         │                        │ │
│  │           │  ◯═════════════◯       │ ← Service range       │ │
│  │           │ ╱   (Circle)    ╲     │   (Dashed border)     │ │
│  │           │ ║     Color by   ║     │                        │ │
│  │           │ ║   vehicle type ║     │                        │ │
│  │           │  ╲              ╱     │                        │ │
│  │           │   ◯═════════════◯     │                        │ │
│  │           │                         │                        │ │
│  │           │    🔵 Client Location  │ ← Blue marker         │ │
│  │           │       (when set)       │                        │ │
│  │           └─────────────────────────┘                        │ │
│  │                                                               │ │
│  │  Latitude: [33.8547]  Longitude: [35.8623]                  │ │
│  │                                                               │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ORDER DETAILS                                                   │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Order Contents:                                              │ │
│  │ [2 Electronics boxes - Laptop and accessories           ]    │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  Order Value ($):                                                │
│  [100.00]                                                        │
│                                                                   │
│  Required Vehicle Type:                                          │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │ ▼ Car (up to 90km, medium packages)                         │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  🔹 MAP UPDATES: Circle size and color change with selection    │
│                                                                   │
│  Your Commission Rate (%):                                       │
│  [10.0] %                                                        │
│  Provide a clear percentage of order value.                     │
│                                                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │ ✓ Real-time Earnings Calculator                             │ │
│  ├─────────────────────────────────────────────────────────────┤ │
│  │ Order Value:           $100.00                              │ │
│  │ Your Earnings:         $10.00  ← Updates as you type       │ │
│  │ Delivery Cost:         (calculated at checkout) → Driver   │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  [Create Order]  [Cancel]                                        │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Map Service Area Visualization

### Different Vehicle Types - Same Location

#### Bike Vehicle (10km)
```
        N
        ↑
    ┌───────────┐
    │    MAP    │
    │           │
    │  ◯════◯  │ ← 10 km radius
    │ ╱ BIKE ╲  │   Orange circle
    │║         ║ │
    │ ╲       ╱  │
    │  ◯════◯   │
    │           │
    │   🔴     │ (Shop)
    └───────────┘
```

#### Car Vehicle (90km)
```
        N
        ↑
    ┌───────────┐
    │    MAP    │
    │           │
    │  ◯════════════◯  │
    │ ╱   CAR        ╲  │ ← 90 km radius
    │║  (90km circle)  ║ │   Blue circle
    │ ╲               ╱  │
    │  ◯════════════◯   │
    │           🔴     │
    │                  │
    └───────────────────┘
```

#### Pickup Vehicle (90km)
```
        N
        ↑
    ┌───────────┐
    │    MAP    │
    │           │
    │  ◯════════════◯  │
    │ ╱   PICKUP     ╲  │ ← 90 km radius
    │║ (90km circle)  ║ │   Red circle
    │ ╲               ╱  │
    │  ◯════════════◯   │
    │           🔴     │
    │                  │
    └───────────────────┘
```

## Earnings Real-time Calculation

### As User Types

**Step 1: No values**
```
Order Value:         $0.00
Your Commission:     0%
Your Earnings:       $0.00
```

**Step 2: Enter order value**
```
Order Value:         $100.00
Your Commission:     0%
Your Earnings:       $0.00
```

**Step 3: Enter commission rate**
```
Order Value:         $100.00
Your Commission:     10%
Your Earnings:       $10.00 ← Live update!
```

**Step 4: Adjust commission rate**
```
Order Value:         $100.00
Your Commission:     15%
Your Earnings:       $15.00 ← Updates instantly!
```

## Vehicle Type Selection Impact

```
Vehicle Type Changed: BIKE → CAR
          │
          ↓
    ┌─────────────┐
    │  Update Map │
    │   Circle    │
    └─────────────┘
          │
          ├→ Remove old circle (10km, orange)
          ├→ Add new circle (90km, blue)
          └→ Update popup info
```

## Success Message After Creating Order

```
✓ Order created successfully.
  Distance: 5.00 km
  Delivery cost: $6.50
  Your earnings: $10.00
```

## Edit Form - With Existing Order

```
Same layout as Create, but:
- Order Contents: [Prefilled with existing data]
- Order Value: [Prefilled - $100.00]
- Vehicle Type: [Prefilled - Car (selected)]
- Commission Rate: [Auto-calculated from existing profit]
- Map: [Shows existing client location with blue marker]
- Circle: [Shows current vehicle type radius]
- Earnings: [Shows current earnings based on existing data]

Alert box:
ℹ Current distance: 5.00 km | Delivery cost: $6.50
```

## Color Coding Key

| Color | Meaning | Used For |
|-------|---------|----------|
| 🔴 Red | Your location | Shop marker |
| 🔵 Blue | Client location | Delivery address |
| 🟠 Orange | Bike service area | 10km radius circle |
| 🔵 Blue | Car service area | 90km radius circle |
| 🔴 Red | Pickup service area | 90km radius circle |

## Interactive Elements

### Map Interactions
- **Click on map**: Set client location (adds blue marker)
- **Click circle popup**: View service area details
- **Click shop marker**: View shop info

### Form Updates
- **Change vehicle type**: Circle updates immediately
- **Type order value**: Earnings display updates
- **Type commission rate**: Earnings display updates

## Mobile Responsiveness

```
Desktop (≥768px):
┌────────────────────────────────┐
│ Form Fields (Left) │ Map (Right)│
└────────────────────────────────┘

Tablet/Mobile (<768px):
┌──────────────────┐
│  Form Fields     │
├──────────────────┤
│  Map             │
└──────────────────┘
```

## Accessibility Features

- ✓ Form labels clearly associated with inputs
- ✓ Required fields marked with *
- ✓ Error messages displayed inline
- ✓ Map has keyboard navigation
- ✓ Color contrast meets WCAG standards
- ✓ Service range circle has text label in popup
