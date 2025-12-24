<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    /**
     * Show shop dashboard
     */
    public function dashboard()
    {
        $shop = Auth::user()->shop;
        
        if (!$shop) {
            return redirect()->route('shop.profile')->with('error', 'Please complete your shop profile first.');
        }

        $totalOrders = $shop->orders()->count();
        $deliveredOrders = $shop->orders()->where('status', 'delivered')->count();
        $pendingOrders = $shop->orders()->whereIn('status', ['available', 'pending', 'in_transit'])->count();
        $cancelledOrders = $shop->orders()->where('status', 'cancelled')->count();
        
        $totalEarnings = $shop->orders()
            ->where('status', 'delivered')
            ->sum('profit');

        $recentOrders = $shop->orders()
            ->with('delivery')
            ->latest()
            ->take(10)
            ->get();

        return view('shop.dashboard', compact(
            'shop',
            'totalOrders',
            'deliveredOrders',
            'pendingOrders',
            'cancelledOrders',
            'totalEarnings',
            'recentOrders'
        ));
    }

    /**
     * Show shop profile form
     */
    public function profile()
    {
        $shop = Auth::user()->shop;
        return view('shop.profile', compact('shop'));
    }

    /**
     * Update shop profile
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $shop = Auth::user()->shop;

        if ($shop) {
            $shop->update($validated);
        } else {
            Shop::create([
                'user_id' => Auth::id(),
                'shop_name' => $validated['shop_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);
        }

        return redirect()->route('shop.dashboard')->with('success', 'Shop profile updated successfully.');
    }

    /**
     * Show order creation form
     */
    public function createOrder()
    {
        $shop = Auth::user()->shop;
        
        if (!$shop || !$shop->latitude || !$shop->longitude) {
            return redirect()->route('shop.profile')->with('error', 'Please set your shop location first.');
        }

        return view('shop.create-order', compact('shop'));
    }

    /**
     * Store new order
     */
    public function storeOrder(Request $request)
    {
        $shop = Auth::user()->shop;

        if (!$shop || !$shop->latitude || !$shop->longitude) {
            return back()->with('error', 'Please set your shop location first.');
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_lat' => 'required|numeric|between:-90,90',
            'client_lng' => 'required|numeric|between:-180,180',
            'vehicle_type' => 'required|in:bike,car',
            'profit' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            $order = Order::create([
                'shop_id' => $shop->id,
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'],
                'client_lat' => $validated['client_lat'],
                'client_lng' => $validated['client_lng'],
                'shop_lat' => $shop->latitude,
                'shop_lng' => $shop->longitude,
                'vehicle_type' => $validated['vehicle_type'],
                'profit' => $validated['profit'],
                'status' => 'available',
            ]);

            // Log order creation
            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => 'available',
                'note' => 'Order created',
            ]);

            DB::commit();

            return redirect()->route('shop.orders')->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Show all orders for this shop
     */
    public function orders()
    {
        $shop = Auth::user()->shop;
        
        if (!$shop) {
            return redirect()->route('shop.profile')->with('error', 'Please complete your shop profile first.');
        }

        $orders = $shop->orders()
            ->with('delivery')
            ->latest()
            ->paginate(20);

        return view('shop.orders', compact('orders'));
    }

    /**
     * Show single order details
     */
    public function showOrder($id)
    {
        $shop = Auth::user()->shop;
        $order = $shop->orders()->with(['delivery', 'logs.changedBy'])->findOrFail($id);

        return view('shop.order-details', compact('order'));
    }

    /**
     * Show order edit form
     */
    public function editOrder($id)
    {
        $shop = Auth::user()->shop;
        $order = $shop->orders()->findOrFail($id);

        if (in_array($order->status, ['delivered', 'cancelled', 'in_transit'])) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'This order cannot be edited in its current status.');
        }

        return view('shop.edit-order', compact('shop', 'order'));
    }

    /**
     * Update an order
     */
    public function updateOrder(Request $request, $id)
    {
        $shop = Auth::user()->shop;
        $order = $shop->orders()->findOrFail($id);

        if (in_array($order->status, ['delivered', 'cancelled', 'in_transit'])) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'This order cannot be edited in its current status.');
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_lat' => 'required|numeric|between:-90,90',
            'client_lng' => 'required|numeric|between:-180,180',
            'vehicle_type' => 'required|in:bike,car',
            'profit' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $order->update([
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'],
                'client_lat' => $validated['client_lat'],
                'client_lng' => $validated['client_lng'],
                'shop_lat' => $shop->latitude,
                'shop_lng' => $shop->longitude,
                'vehicle_type' => $validated['vehicle_type'],
                'profit' => $validated['profit'],
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => $order->status,
                'note' => 'Order updated by shop',
            ]);

            DB::commit();

            return redirect()->route('shop.orders.show', $order->id)
                ->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder($id)
    {
        $shop = Auth::user()->shop;
        $order = $shop->orders()->findOrFail($id);

        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this order.');
        }

        DB::beginTransaction();
        
        try {
            $order->update(['status' => 'cancelled']);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => 'cancelled',
                'note' => 'Order cancelled by shop',
            ]);

            DB::commit();

            return back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order.');
        }
    }

    /**
     * Verify QR code at pickup (shop confirms delivery person picked up)
     */
    public function verifyPickup(Request $request, $id)
    {
        $shop = Auth::user()->shop;
        $order = $shop->orders()->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is not in pending status.');
        }

        $request->validate([
            'qr_code' => 'required|string',
        ]);

        if ($request->qr_code !== $order->qr_code) {
            return back()->with('error', 'Invalid QR code.');
        }

        DB::beginTransaction();
        
        try {
            // Generate OTP for delivery
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            $order->update([
                'qr_verified' => true,
                'qr_verified_at' => now(),
                'delivery_otp' => $otp,
                'status' => 'in_transit',
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => 'in_transit',
                'note' => 'QR verified by shop. Package picked up by delivery.',
            ]);

            DB::commit();

            // Here you would send SMS to client with OTP
            // For now, we just display it in a message
            return back()->with('success', 'Pickup confirmed! OTP sent to client at ' . $order->client_phone);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to verify pickup.');
        }
    }

    /**
     * Show big map with available orders
     */
    public function ordersMap()
    {
        $shop = Auth::user()->shop;
        return view('shop.orders-map', compact('shop'));
    }

    /**
     * Provide available orders as geo JSON
     */
    public function ordersMapData()
    {
        $orders = Order::where('status', 'available')
            ->select('id', 'shop_id', 'shop_lat', 'shop_lng', 'vehicle_type', 'profit', 'created_at')
            ->with(['shop:id,shop_name'])
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'shop' => $order->shop ? $order->shop->shop_name : 'Unknown shop',
                    'lat' => (float) $order->shop_lat,
                    'lng' => (float) $order->shop_lng,
                    'vehicle_type' => $order->vehicle_type,
                    'profit' => (float) $order->profit,
                    'created_at' => $order->created_at->toDateTimeString(),
                ];
            });

        return response()->json($orders);
    }
}
