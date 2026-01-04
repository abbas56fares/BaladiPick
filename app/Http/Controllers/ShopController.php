<?php

namespace App\Http\Controllers;

use App\Events\OrderCancelled;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Shop;
use App\Services\DeliveryCostCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $user = Auth::user();
        $shop = $user->shop;
        
        if (!$user->verified) {
            return redirect()->route('shop.dashboard')->with('error', 'Your account is not verified yet. Please wait for admin approval. (Will be verified within 2 days)');
        }
        
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
        $user = Auth::user();

        if (!$user->verified) {
            return back()->with('error', 'Your account is not verified yet. Please wait for admin approval. (Will be verified within 2 days)');
        }

        $shop = $user->shop;

        if (!$shop || !$shop->latitude || !$shop->longitude) {
            return back()->with('error', 'Please set your shop location first.');
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_lat' => 'required|numeric|between:-90,90',
            'client_lng' => 'required|numeric|between:-180,180',
            'order_contents' => 'required|string',
            'order_price' => 'required|numeric|min:0',
            'vehicle_type' => 'required|in:bike,car,pickup',
        ]);

        // Calculate distance and delivery cost
        $calculator = new DeliveryCostCalculator();
        $distance = $calculator->calculateDistance(
            $shop->latitude,
            $shop->longitude,
            $validated['client_lat'],
            $validated['client_lng']
        );

        // Check if distance is within range for selected vehicle type
        if (!$calculator->isWithinRange($validated['vehicle_type'], $distance)) {
            $maxDistance = $calculator->getMaxDistance($validated['vehicle_type']);
            return back()->withInput()->with('error', "Distance ({$distance} km) exceeds maximum range for {$validated['vehicle_type']} ({$maxDistance} km).");
        }

        $deliveryCost = $calculator->calculate($validated['vehicle_type'], $distance);
        
        // Shop profit equals the order value
        $shopProfit = $validated['order_price'];

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
                'order_contents' => $validated['order_contents'],
                'order_price' => $validated['order_price'],
                'vehicle_type' => $validated['vehicle_type'],
                'distance_km' => $distance,
                'delivery_cost' => $deliveryCost,
                'profit' => $shopProfit,
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

            return redirect()->route('shop.orders')->with('success', "Order created successfully. Distance: {$distance} km, Delivery cost: \${$deliveryCost}. Your earnings: \${$shopProfit}");
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
            ->paginate(15);

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
            'order_contents' => 'required|string',
            'order_price' => 'required|numeric|min:0',
            'vehicle_type' => 'required|in:bike,car,pickup',
        ]);

        // Recalculate distance and delivery cost
        $calculator = new DeliveryCostCalculator();
        $distance = $calculator->calculateDistance(
            $shop->latitude,
            $shop->longitude,
            $validated['client_lat'],
            $validated['client_lng']
        );

        // Check if distance is within range for selected vehicle type
        if (!$calculator->isWithinRange($validated['vehicle_type'], $distance)) {
            $maxDistance = $calculator->getMaxDistance($validated['vehicle_type']);
            return back()->withInput()->with('error', "Distance ({$distance} km) exceeds maximum range for {$validated['vehicle_type']} ({$maxDistance} km).");
        }

        $deliveryCost = $calculator->calculate($validated['vehicle_type'], $distance);
        
        // Shop profit equals the order value
        $shopProfit = $validated['order_price'];

        DB::beginTransaction();

        try {
            $order->update([
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'],
                'client_lat' => $validated['client_lat'],
                'client_lng' => $validated['client_lng'],
                'shop_lat' => $shop->latitude,
                'shop_lng' => $shop->longitude,
                'order_contents' => $validated['order_contents'],
                'order_price' => $validated['order_price'],
                'vehicle_type' => $validated['vehicle_type'],
                'distance_km' => $distance,
                'delivery_cost' => $deliveryCost,
                'profit' => $shopProfit,
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => $order->status,
                'note' => 'Order updated by shop',
            ]);

            DB::commit();

            return redirect()->route('shop.orders.show', $order->id)
                ->with('success', "Order updated successfully. Distance: {$distance} km, Delivery cost: \${$deliveryCost}. Your earnings: \${$shopProfit}");
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
        
        DB::beginTransaction();
        
        try {
            // Lock the order row to prevent race conditions
            $order = $shop->orders()->where('id', $id)->lockForUpdate()->first();
            
            if (!$order) {
                DB::rollBack();
                return back()->with('error', 'Order not found.');
            }

            if (in_array($order->status, ['delivered', 'cancelled'])) {
                DB::rollBack();
                return back()->with('error', 'Cannot cancel this order.');
            }
            
            // Check if order was just accepted by delivery
            if ($order->status === 'in_transit') {
                DB::rollBack();
                return back()->with('error', 'Cannot cancel - order is already in transit.');
            }

            $order->update(['status' => 'cancelled']);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => 'cancelled',
                'note' => 'Order cancelled by shop',
            ]);

            DB::commit();

            // Broadcast event to all connected clients
            broadcast(new OrderCancelled($order));

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

            // Broadcast order verification event to notify delivery and other clients
            broadcast(new \App\Events\OrderVerified($order));

            // Here you would send SMS to client with OTP
            // For now, we just display it in a message
            return back()->with('success', 'Pickup confirmed! OTP sent to client at ' . $order->client_phone);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to verify pickup.');
        }
    }

    /**
     * Verify pickup via QR scanner (API endpoint)
     */
    public function verifyPickupApi(Request $request)
    {
        $shop = Auth::user()->shop;
        
        $request->validate([
            'order_id' => 'required|integer',
            'qr_code' => 'required|string',
        ]);

        $order = $shop->orders()->find($request->order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not in pending status.'
            ], 400);
        }

        if ($request->qr_code !== $order->qr_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code.'
            ], 400);
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
                'note' => 'QR verified by shop via scanner. Package picked up by delivery.',
            ]);

            DB::commit();

            // Broadcast order verification event to notify delivery and other clients
            broadcast(new \App\Events\OrderVerified($order));

            return response()->json([
                'success' => true,
                'message' => 'Pickup confirmed! OTP: ' . $otp,
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify pickup: ' . $e->getMessage()
            ], 500);
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
     * Provide available orders as geo JSON (only for logged-in shop)
     */
    public function ordersMapData()
    {
        $shop = Auth::user()->shop;
        
        $orders = Order::where('shop_id', $shop->id)
            ->whereIn('status', ['available', 'pending', 'in_transit'])
            ->select('id', 'shop_id', 'client_lat', 'client_lng', 'client_name', 'client_phone', 'vehicle_type', 'profit', 'status', 'created_at')
            ->with(['delivery:id,name'])
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'client_name' => $order->client_name,
                    'client_phone' => $order->client_phone,
                    'lat' => (float) $order->client_lat,
                    'lng' => (float) $order->client_lng,
                    'vehicle_type' => $order->vehicle_type,
                    'profit' => (float) $order->profit,
                    'status' => $order->status,
                    'delivery' => $order->delivery ? $order->delivery->name : null,
                    'created_at' => $order->created_at->toDateTimeString(),
                ];
            });

        return response()->json($orders);
    }

    /**
     * Change password - Show form
     */
    public function changePassword()
    {
        return view('shop.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Check if new password is different from current password
        if (Hash::check($validated['new_password'], $user->password)) {
            return back()->withErrors(['new_password' => 'The new password must be different from the current password.']);
        }

        // Update password
        $user->update(['password' => Hash::make($validated['new_password'])]);

        return back()->with('success', 'Password changed successfully!');
    }
}
