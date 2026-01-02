@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>My Delivery Orders</h4>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Shop</th>
                                    <th>Client</th>
                                    <th>Vehicle</th>
                                    <th>Profit</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->shop->shop_name }}</td>
                                        <td>{{ $order->client_name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($order->vehicle_type) }}</span>
                                        </td>
                                        <td>${{ number_format($order->profit, 2) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'in_transit' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('delivery.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                @else
                    <p class="text-muted">No orders yet. <a href="{{ route('delivery.map') }}">View available orders</a></p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh every 7 seconds to show order status updates -->
<script>
    setInterval(function() {
        if ($('.modal.show').length === 0 && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            location.reload();
        }
    }, 7000); // 7 seconds
</script>

@endsection
