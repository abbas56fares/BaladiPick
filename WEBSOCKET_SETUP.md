# WebSocket Setup for BaladiPick

This project now uses Pusher for real-time WebSocket communication locally.

## Prerequisites

- PHP 7.4+ (you have 8.2.12 ✅)
- Composer ✅
- Node.js & npm (for frontend tooling)

## Local Development Setup

### 1. Install Dependencies (Already Done)
```bash
composer require pusher/pusher-php-server
```

### 2. Environment Configuration (Already Done)
The `.env` file is already configured with your Pusher Cloud credentials:
```
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=2097607
PUSHER_APP_KEY=c531076a8a9abcba400c
PUSHER_APP_SECRET=0260cdd5a0269425b282
PUSHER_CLUSTER=eu
```

### 3. Start Laravel Server
```bash
php artisan serve
```

**That's it!** Your app is now connected to Pusher Cloud. No local server needed.

### 4. Test WebSocket Connection

Open 2 browsers (or tabs with different roles):
- Tab 1: Admin/Shop user
- Tab 2: Delivery user

Perform actions:
- Shop creates an order → Delivery map updates in real-time ✅
- Delivery accepts order → Shop dashboard updates in real-time ✅
- Shop cancels order → Delivery notified in real-time ✅

### Broadcasting Events

Currently broadcast on:
- **Channel**: `orders` (public)
- **Events**:
  - `order.accepted` - When delivery accepts an order
  - `order.cancelled` - When shop cancels an order
  - `order.verified` - When order verification status changes

## How WebSockets Work Here

1. **Controller broadcasts event** → `broadcast(new OrderAccepted($order))`
2. **Pusher receives event** → Forwards to subscribed clients
3. **JavaScript listener** → Receives event and reloads page
4. **User sees update** → No manual refresh needed!

## Disable WebSockets (Fallback to Polling)

If WebSockets aren't working, the app falls back to polling (every 10-15 seconds). This is already implemented in your views.

To completely disable WebSockets:
```bash
# In .env
BROADCAST_CONNECTION=log
```

Then WebSocket code runs but doesn't broadcast (only polling works).

## Production Setup

For production, you have options:
1. **Use Pusher Cloud** (Paid, but reliable) - https://pusher.com
2. **Use Redis** (Free, self-hosted) - Install Redis and use `redis` driver
3. **Use Laravel WebSockets** (When Laravel 11 is fully stable)

## Troubleshooting

**Pusher not connecting?**
- Check `.env` values are correct
- Ensure `localhost:6001` is accessible
- Check browser console for errors (F12 → Console)

**Events not being broadcast?**
- Verify `BROADCAST_CONNECTION=pusher` in `.env`
- Check server logs: `php artisan serve`

**Slow updates?**
- This is normal - Pusher has slight latency
- Use polling as fallback (already implemented)

## Next Steps

- Monitor WebSocket usage in Pusher dashboard
- Add more channels for specific users (private channels)
- Implement presence channels to show "online" users
- Switch to Redis for production if self-hosting

For questions or issues, check Laravel broadcasting docs:
https://laravel.com/docs/11.x/broadcasting
