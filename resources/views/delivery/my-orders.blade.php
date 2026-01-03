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
                                    <th class="sortable">Shop</th>
                                    <th class="sortable">Client</th>
                                    <th class="sortable">Vehicle</th>
                                    <th class="sortable">Profit</th>
                                    <th class="sortable">Status</th>
                                    <th class="sortable">Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td data-label="Shop">{{ $order->shop->shop_name }}</td>
                                        <td data-label="Client">{{ $order->client_name }}</td>
                                        <td data-label="Vehicle">
                                            <span class="badge bg-secondary">{{ ucfirst($order->vehicle_type) }}</span>
                                        </td>
                                        <td data-label="Profit">${{ number_format($order->profit, 2) }}</td>
                                        <td data-label="Status">
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
                                        <td data-label="Date">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td data-label="Actions">
                                            <a href="{{ route('delivery.orders.show', $order->id) }}" class="btn btn-sm btn-info btn-block-mobile">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $orders->links('pagination.custom') }}
                    </div>
                @else
                    <p class="text-muted">No orders yet. <a href="{{ route('delivery.map') }}">View available orders</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
