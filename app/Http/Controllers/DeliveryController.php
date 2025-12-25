<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Get available orders near delivery (JSON)
     */
    public function availableOrders(Request $request)
    {
        $orders = Order::where('status', 'available')
            ->with('shop')
            ->get();

        // Group orders by shop to show shop locations on map
        $shopsWithOrders = $orders->groupBy('shop_id')->map(function ($shopOrders) {
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
                        'profit' => $order->profit,
                    ];
                })
            ];
        })->values();

        return response()->json([
            'shops' => $shopsWithOrders,
            'orders' => $orders // Keep original orders for table
        ]);
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

        $order = Order::findOrFail($id);

        if ($order->status !== 'available') {
            return back()->with('error', 'This order is not available.');
        }

        DB::beginTransaction();
        
        try {
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
            ->paginate(20);

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
}
