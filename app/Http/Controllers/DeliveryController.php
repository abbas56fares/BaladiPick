<?php

namespace App\Http\Controllers;

use App\Events\OrderAccepted;
use App\Models\Order;
use App\Models\OrderLog;
use App\Services\DeliveryCostCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeliveryController extends Controller
{
    /**
     * Show delivery dashboard
     */
    public function dashboard()
    {
        $completedOrders = Auth::user()->deliveryOrders()->where('status', 'delivered')->count();
        $inProgressOrders = Auth::user()->deliveryOrders()->whereIn('status', ['pending', 'in_transit'])->count();
        
        $totalEarnings = Auth::user()->deliveryOrders()
            ->where('status', 'delivered')
            ->sum('profit');

        $recentOrders = Auth::user()->deliveryOrders()
            ->with('shop')
            ->latest()
            ->take(10)
            ->get();

        return view('delivery.dashboard', compact(
            'completedOrders',
            'inProgressOrders',
            'totalEarnings',
            'recentOrders'
        ));
    }

    /**
     * Show map view with available orders
     */
    public function map()
    {
        return view('delivery.map');
    }

    /**
     * Show map view with accepted orders (pending and in_transit)
     */
    public function acceptedOrdersMap()
    {
        return view('delivery.accepted-orders-map');
    }

    /**
     * Get available orders near delivery (JSON)
     */
    public function availableOrders(Request $request)
    {
        $deliveryUser = Auth::user();
        
        // Get delivery driver's vehicle type
        $vehicleType = $deliveryUser->vehicle_type;
        
        if (!$vehicleType) {
            return response()->json([
                'shops' => [],
                'orders' => [],
                'message' => 'Please update your profile with your vehicle type.'
            ]);
        }

        // Filter orders by matching vehicle type
        $orders = Order::where('status', 'available')
            ->where('vehicle_type', $vehicleType)
            ->with('shop')
            ->get();

        // Further filter by distance range
        $calculator = new DeliveryCostCalculator();
        $maxDistance = $calculator->getMaxDistance($vehicleType);
        
        $filteredOrders = $orders->filter(function ($order) use ($maxDistance) {
            // If distance_km is already calculated, use it
            if ($order->distance_km) {
                return $order->distance_km <= $maxDistance;
            }
            // Otherwise return true (shouldn't happen with new system)
            return true;
        });

        // Group orders by shop to show shop locations on map
        $shopsWithOrders = $filteredOrders->groupBy('shop_id')->map(function ($shopOrders) {
            $shop = $shopOrders->first()->shop;
            return [
                'shop_id' => $shop->id,
                'shop_name' => $shop->shop_name,
                'shop_phone' => $shop->phone,
                'latitude' => $shop->latitude,
                'longitude' => $shop->longitude,
                'available_orders_count' => $shopOrders->count(),
                'orders' => $shopOrders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'client_name' => $order->client_name,
                        'client_phone' => $order->client_phone,
                        'client_lat' => $order->client_lat,
                        'client_lng' => $order->client_lng,
                        'vehicle_type' => $order->vehicle_type,
                        'distance_km' => $order->distance_km,
                        'delivery_cost' => $order->delivery_cost,
                        'profit' => $order->profit,
                        'shop_id' => $order->shop_id,
                    ];
                })
            ];
        })->values();

        return response()->json([
            'shops' => $shopsWithOrders,
            'orders' => $filteredOrders, // Keep filtered orders for table
            'vehicle_type' => $vehicleType,
            'max_distance' => $maxDistance
        ]);
    }

    /**
     * Get delivery person's accepted orders (JSON)
     */
    public function acceptedOrders(Request $request)
    {
        try {
            $orders = Auth::user()->deliveryOrders()
                ->whereIn('status', ['pending', 'in_transit'])
                ->with('shop')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'orders' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept an order
     */
    public function acceptOrder($id)
    {
        $user = Auth::user();

        if (!$user->verified) {
            return back()->with('error', 'Your account is not verified yet. Please wait for admin approval. (Will be verified within 2 days) before accepting orders.');
        }

        // Global cooldown check (all orders)
        if ($user->cooldown_all_until && now()->lessThan($user->cooldown_all_until)) {
            return back()->with('error', 'You are on cooldown until ' . $user->cooldown_all_until->format('M d, Y H:i') . ' due to recent cancellations.');
        }

        DB::beginTransaction();
        
        try {
            // Lock the order row to prevent race conditions
            $order = Order::where('id', $id)->lockForUpdate()->first();
            
            if (!$order) {
                DB::rollBack();
                return back()->with('error', 'Order not found.');
            }

            // Check if order is still available (another delivery may have just taken it)
            if ($order->status !== 'available') {
                DB::rollBack();
                return back()->with('error', 'This order has already been accepted by another delivery driver.');
            }

            // Per-order cooldown check based on last cancellation log
            $baseOrderCooldown = 20; // minutes
            $penalty = ($user->cancellation_count ?? 0) * 5; // minutes
            $cooldownMinutes = $baseOrderCooldown + $penalty;

            $lastCancel = $order->logs()
                ->where('status', 'cancelled')
                ->where('changed_by', $user->id)
                ->latest()
                ->first();

            if ($lastCancel && $lastCancel->created_at->greaterThan(now()->subMinutes($cooldownMinutes))) {
                DB::rollBack();
                $remaining = $lastCancel->created_at->addMinutes($cooldownMinutes)->diffForHumans(null, true);
                return back()->with('error', 'You recently cancelled this order. Try again in ' . $remaining . '.');
            }

            // Generate QR code
            $qrData = 'ORDER-' . $order->id . '-' . time();
            
            $order->update([
                'delivery_id' => Auth::id(),
                'status' => 'pending',
                'qr_code' => $qrData,
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => 'pending',
                'note' => 'Order accepted by delivery',
            ]);

            DB::commit();

            // Broadcast event to all connected clients
            broadcast(new OrderAccepted($order));

            return redirect()->route('delivery.orders.show', $order->id)
                ->with('success', 'Order accepted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to accept order.');
        }
    }

    /**
     * Show delivery's orders
     */
    public function myOrders()
    {
        $orders = Auth::user()->deliveryOrders()
            ->with('shop')
            ->latest()
            ->paginate(15);

        return view('delivery.my-orders', compact('orders'));
    }

    /**
     * Show single order details
     */
    public function showOrder($id)
    {
        $order = Auth::user()->deliveryOrders()->with(['shop', 'logs.changedBy'])->findOrFail($id);

        return view('delivery.order-details', compact('order'));
    }

    /**
     * Cancel an accepted order (return to available) and apply cooldowns
     */
    public function cancelOrder($id)
    {
        $user = Auth::user();
        $order = Order::findOrFail($id);

        if ($order->delivery_id !== $user->id) {
            return back()->with('error', 'You cannot cancel an order you have not accepted.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending (accepted) orders can be cancelled.');
        }

        DB::beginTransaction();
        try {
            // Log a cancellation entry for cooldown tracking
            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => $user->id,
                'status' => 'cancelled',
                'note' => 'Delivery cancelled acceptance; order returned to available',
            ]);

            // Return order to pool
            $order->update([
                'delivery_id' => null,
                'status' => 'available',
                'qr_code' => null,
            ]);

            // Increment user cancellation count and set global cooldown
            $user->cancellation_count = ($user->cancellation_count ?? 0) + 1;
            $baseGlobal = 2; // minutes
            $penalty = $user->cancellation_count * 5; // minutes
            $user->cooldown_all_until = now()->addMinutes($baseGlobal + $penalty);
            $user->save();

            DB::commit();
            return redirect()->route('delivery.orders.my')->with('success', 'Order cancelled. You are on a temporary cooldown from accepting new orders.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order.');
        }
    }

    /**
     * Verify QR code at pickup
     */
    public function verifyQR(Request $request, $id)
    {
        $order = Auth::user()->deliveryOrders()->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Cannot verify QR at this stage.');
        }

        $request->validate([
            'qr_code' => 'required|string',
        ]);

        if ($request->qr_code !== $order->qr_code) {
            return back()->with('error', 'Invalid QR code.');
        }

        DB::beginTransaction();
        
        try {
            $order->update([
                'qr_verified' => true,
                'qr_verified_at' => now(),
                'status' => 'in_transit',
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => 'in_transit',
                'note' => 'QR verified, package picked up',
            ]);

            DB::commit();

            return back()->with('success', 'Package picked up successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to verify QR.');
        }
    }

    /**
     * Verify OTP for delivery completion
     */
    public function verifyOTP(Request $request, $id)
    {
        $order = Auth::user()->deliveryOrders()->findOrFail($id);

        if ($order->status !== 'in_transit') {
            return back()->with('error', 'Cannot verify OTP at this stage.');
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if ($request->otp !== $order->delivery_otp) {
            return back()->with('error', 'Invalid OTP.');
        }

        DB::beginTransaction();
        
        try {
            $order->update([
                'delivery_verified' => true,
                'delivery_verified_at' => now(),
                'status' => 'delivered',
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'changed_by' => Auth::id(),
                'status' => 'delivered',
                'note' => 'Order delivered successfully',
            ]);

            DB::commit();

            return redirect()->route('delivery.dashboard')
                ->with('success', 'Order delivered successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to verify OTP.');
        }
    }

    /**
     * Show delivery settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('delivery.settings', compact('user'));
    }

    /**
     * Update delivery settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|in:bike,car,pickup',
            'license_number' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'vehicle_type' => $validated['vehicle_type'],
                'license_number' => $validated['license_number'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);

            return back()->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Change password - Show form
     */
    public function changePassword()
    {
        return view('delivery.change-password');
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
