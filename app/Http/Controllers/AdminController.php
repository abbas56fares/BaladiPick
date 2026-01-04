<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $totalShops = Shop::count();
        $verifiedShops = Shop::where('is_verified', true)->count();
        
        $totalDeliveries = User::where('role', 'delivery')->count();
        $verifiedDeliveries = User::where('role', 'delivery')->where('verified', true)->count();
        
        $totalOrders = Order::count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $inTransitOrders = Order::where('status', 'in_transit')->count();
        $availableOrders = Order::where('status', 'available')->count();
        $pendingOrders = Order::whereIn('status', ['available', 'pending', 'in_transit'])->count();
        
        $totalRevenue = Order::where('status', 'delivered')->sum('delivery_cost');
        
        // Calculate average revenue per delivered order
        $avgRevenuePerOrder = $deliveredOrders > 0 
            ? $totalRevenue / $deliveredOrders 
            : 0;

        $recentOrders = Order::with(['shop', 'delivery'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalShops',
            'verifiedShops',
            'totalDeliveries',
            'verifiedDeliveries',
            'totalOrders',
            'deliveredOrders',
            'cancelledOrders',
            'inTransitOrders',
            'availableOrders',
            'pendingOrders',
            'totalRevenue',
            'avgRevenuePerOrder',
            'recentOrders'
        ));
    }

    /**
     * Show all shops
     */
    public function shops(Request $request)
    {
        $query = Shop::with('user');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Verification status filter
        if ($request->has('verified') && $request->verified !== '') {
            $query->where('is_verified', $request->verified);
        }

        $shops = $query->latest()->paginate(15)->withQueryString();
        return view('admin.shops', compact('shops'));
    }

    /**
     * Show single shop details
     */
    public function showShop($id)
    {
        $shop = Shop::with('user')->findOrFail($id);
        $orders = $shop->orders()->latest()->paginate(15);
        return view('admin.shop-details', compact('shop', 'orders'));
    }

    /**
     * Verify shop
     */
    public function verifyShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['is_verified' => true]);
        $shop->user->update(['verified' => true]);

        return back()->with('success', 'Shop verified successfully.');
    }

    /**
     * Disable shop
     */
    public function disableShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['is_verified' => false]);
        $shop->user->update(['verified' => false]);

        return back()->with('success', 'Shop disabled successfully.');
    }

    /**
     * Show all deliveries
     */
    public function deliveries(Request $request)
    {
        $query = User::where('role', 'delivery');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Verification status filter
        if ($request->has('verified') && $request->verified !== '') {
            $query->where('verified', $request->verified);
        }

        $deliveries = $query->latest()->paginate(15)->withQueryString();
        return view('admin.deliveries', compact('deliveries'));
    }

    /**
     * Show single delivery details
     */
    public function showDelivery($id)
    {
        $delivery = User::where('role', 'delivery')->findOrFail($id);
        $orders = $delivery->deliveryOrders()->latest()->paginate(15);
        return view('admin.delivery-details', compact('delivery', 'orders'));
    }

    /**
     * Verify delivery user
     */
    public function verifyDelivery($id)
    {
        $delivery = User::where('role', 'delivery')->findOrFail($id);
        $delivery->update(['verified' => true]);

        return back()->with('success', 'Delivery user verified successfully.');
    }

    /**
     * Disable delivery user
     */
    public function disableDelivery($id)
    {
        $delivery = User::where('role', 'delivery')->findOrFail($id);
        $delivery->update(['verified' => false]);

        return back()->with('success', 'Delivery user disabled successfully.');
    }

    /**
     * Show all orders
     */
    public function orders()
    {
        $orders = Order::with(['shop', 'delivery'])
            ->latest()
            ->paginate(15);

        return view('admin.orders', compact('orders'));
    }

    /**
     * Show single order
     */
    public function showOrder($id)
    {
        $order = Order::with(['shop', 'delivery', 'logs.changedBy'])->findOrFail($id);
        return view('admin.order-details', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

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
                'note' => 'Order cancelled by admin',
            ]);

            DB::commit();

            return back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order.');
        }
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        $totalOrders = Order::count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        // Revenue should reflect completed deliveries only and use delivery earnings, not total order value
        $totalRevenue = Order::where('status', 'delivered')->sum('delivery_cost');

        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $ordersByVehicle = Order::select('vehicle_type', DB::raw('count(*) as count'))
            ->groupBy('vehicle_type')
            ->get();

        $topShops = Shop::withCount(['orders' => function($query) {
                $query->where('status', 'delivered');
            }])
            ->orderBy('orders_count', 'desc')
            ->take(10)
            ->get();

        $topDeliveries = User::where('role', 'delivery')
            ->withCount(['deliveryOrders' => function($query) {
                $query->where('status', 'delivered');
            }])
            ->orderBy('delivery_orders_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports', compact(
            'totalOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalRevenue',
            'ordersByStatus',
            'ordersByVehicle',
            'topShops',
            'topDeliveries'
        ));
    }

    /**
     * Export report as CSV
     */
    public function exportReport()
    {
        $orders = Order::with(['shop', 'delivery'])->get();

        $filename = 'orders_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Order ID',
                'Shop',
                'Client Name',
                'Client Phone',
                'Delivery Driver',
                'Vehicle Type',
                'Profit',
                'Status',
                'Created At',
                'Delivered At'
            ]);

            // Data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->shop->shop_name,
                    $order->client_name,
                    $order->client_phone,
                    $order->delivery ? $order->delivery->name : 'N/A',
                    $order->vehicle_type,
                    $order->profit,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->delivery_verified_at ? $order->delivery_verified_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export report as PDF
     */
    public function exportReportPdf()
    {
        $orders = Order::with(['shop', 'delivery'])->get();
        
        $totalOrders = Order::count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $totalRevenue = Order::where('status', 'delivered')->sum('delivery_cost');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports-pdf', compact(
            'orders',
            'totalOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalRevenue'
        ));

        $filename = 'orders_report_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Show delivery drivers filtered by vehicle type
     */
    private function getDeliveriesByVehicle($vehicleType, $search = null, $verified = null)
    {
        $query = User::where('role', 'delivery')->where('vehicle_type', $vehicleType);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($verified !== null) {
            $query->where('verified', (bool) $verified);
        }

        return $query->paginate(15);
    }

    /**
     * Get vehicle-type specific statistics
     */
    private function getVehicleStats($vehicleType)
    {
        $totalDrivers = User::where('role', 'delivery')->where('vehicle_type', $vehicleType)->count();
        $verifiedDrivers = User::where('role', 'delivery')->where('vehicle_type', $vehicleType)->where('verified', true)->count();
        
        // Get orders for this vehicle type
        $totalOrders = Order::where('vehicle_type', $vehicleType)->count();
        $deliveredOrders = Order::where('vehicle_type', $vehicleType)->where('status', 'delivered')->count();

        return compact('totalDrivers', 'verifiedDrivers', 'totalOrders', 'deliveredOrders');
    }

    /**
     * Show bike delivery drivers
     */
    public function deliveriesBike(Request $request)
    {
        $deliveries = $this->getDeliveriesByVehicle('bike', $request->search, $request->verified);
        
        $stats = $this->getVehicleStats('bike');
        
        return view('admin.delivery-bikes', [
            'deliveries' => $deliveries,
            'totalBikeDrivers' => $stats['totalDrivers'],
            'verifiedBikeDrivers' => $stats['verifiedDrivers'],
            'totalBikeOrders' => $stats['totalOrders'],
            'deliveredBikeOrders' => $stats['deliveredOrders'],
        ]);
    }

    /**
     * Show car delivery drivers
     */
    public function deliveriesCar(Request $request)
    {
        $deliveries = $this->getDeliveriesByVehicle('car', $request->search, $request->verified);
        
        $stats = $this->getVehicleStats('car');
        
        return view('admin.delivery-cars', [
            'deliveries' => $deliveries,
            'totalCarDrivers' => $stats['totalDrivers'],
            'verifiedCarDrivers' => $stats['verifiedDrivers'],
            'totalCarOrders' => $stats['totalOrders'],
            'deliveredCarOrders' => $stats['deliveredOrders'],
        ]);
    }

    /**
     * Show pickup delivery drivers
     */
    public function deliveriesPickup(Request $request)
    {
        $deliveries = $this->getDeliveriesByVehicle('pickup', $request->search, $request->verified);
        
        $stats = $this->getVehicleStats('pickup');
        
        return view('admin.delivery-pickups', [
            'deliveries' => $deliveries,
            'totalPickupDrivers' => $stats['totalDrivers'],
            'verifiedPickupDrivers' => $stats['verifiedDrivers'],
            'totalPickupOrders' => $stats['totalOrders'],
            'deliveredPickupOrders' => $stats['deliveredOrders'],
        ]);
    }

    /**
     * Show admin profile
     */
    public function profile()
    {
        $admin = Auth::user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'timezone' => 'nullable|timezone',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Update basic info
        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $admin->phone,
            'timezone' => $validated['timezone'] ?? $admin->timezone,
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $admin->update(['password' => bcrypt($validated['password'])]);
        }

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }
}

