@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>My Orders</h4>
                <a href="{{ route('shop.orders.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New Order
                </a>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Client</th>
                                    <th>Phone</th>
                                    <th>Vehicle</th>
                                    <th>Profit</th>
                                    <th>Status</th>
                                    <th>Delivery</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->client_name }}</td>
                                        <td>{{ $order->client_phone }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($order->vehicle_type) }}</span>
                                        </td>
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
                                        <td>
                                            @if($order->delivery)
                                                {{ $order->delivery->name }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                            @if(!in_array($order->status, ['delivered', 'cancelled', 'in_transit']))
                                                <a href="{{ route('shop.orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            @endif
                                            @if(!in_array($order->status, ['delivered', 'cancelled']))
                                                <form action="{{ route('shop.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to cancel this order?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
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
                    <p class="text-muted">No orders yet. <a href="{{ route('shop.orders.create') }}">Create your first order</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
