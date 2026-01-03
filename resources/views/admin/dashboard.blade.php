@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <h2>Admin Dashboard</h2>
        <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

<div class="row mt-4">
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card text-white bg-primary">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">Total Shops</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">{{ $totalShops }}</h3>
                <small style="font-size: 0.75rem;">Verified: {{ $verifiedShops }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card text-white bg-info">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">Delivery Drivers</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">{{ $totalDeliveries }}</h3>
                <small style="font-size: 0.75rem;">Verified: {{ $verifiedDeliveries }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card text-white bg-warning">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">Total Orders</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">{{ $totalOrders }}</h3>
                <small style="font-size: 0.75rem;">Pending: {{ $pendingOrders }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">Delivered</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">{{ $deliveredOrders }}</h3>
                <small style="font-size: 0.75rem;">Revenue: ${{ number_format($totalRevenue, 2) }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card text-white bg-danger">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">Cancelled Orders</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">{{ $cancelledOrders }}</h3>
                <small style="font-size: 0.75rem;">Lost revenue</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card text-white bg-secondary">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">In Transit</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">{{ $inTransitOrders }}</h3>
                <small style="font-size: 0.75rem;">Active deliveries</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card text-white bg-dark">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">Avg Revenue/Order</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">${{ number_format($avgRevenuePerOrder, 2) }}</h3>
                <small style="font-size: 0.75rem;">Delivered orders</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body p-3">
                <h6 class="card-title mb-2" style="font-size: 0.85rem;">Available Orders</h6>
                <h3 class="mb-1" style="font-size: 1.75rem;">{{ $availableOrders }}</h3>
                <small style="font-size: 0.75rem;">Waiting assignment</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Recent Orders</h5>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Shop</th>
                                    <th>Client</th>
                                    <th>Delivery</th>
                                    <th>Vehicle</th>
                                    <th>Profit</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->shop->shop_name }}</td>
                                        <td>{{ $order->client_name }}</td>
                                        <td>
                                            @if($order->delivery)
                                                {{ $order->delivery->name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($order->vehicle_type) }}</span></td>
                                        <td>${{ number_format($order->profit, 2) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'available' => 'info',
                                                    'pending' => 'warning',
                                                    'in_transit' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$order->status] }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No orders yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Manual refresh button
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }
});
</script>
@endpush
