@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Order #{{ $order->id }} Details</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Client Information</h5>
                        <p><strong>Name:</strong> {{ $order->client_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->client_phone }}</p>
                        <p><strong>Location:</strong> {{ $order->client_lat }}, {{ $order->client_lng }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Information</h5>
                        <p><strong>Status:</strong> 
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
                        </p>
                        <p><strong>Vehicle Type:</strong> {{ ucfirst($order->vehicle_type) }}</p>
                        <p><strong>Profit:</strong> ${{ number_format($order->profit, 2) }}</p>
                        <p><strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($order->delivery)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h5>Delivery Driver</h5>
                            <p><strong>Name:</strong> {{ $order->delivery->name }}</p>
                            <p><strong>Phone:</strong> {{ $order->delivery->phone }}</p>
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Verification Status</h5>
                        <p>
                            <strong>QR Code Verified:</strong> 
                            @if($order->qr_verified)
                                <span class="badge bg-success">Yes</span>
                                ({{ $order->qr_verified_at->format('M d, Y H:i') }})
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </p>
                        <p>
                            <strong>Delivery OTP Verified:</strong> 
                            @if($order->delivery_verified)
                                <span class="badge bg-success">Yes</span>
                                ({{ $order->delivery_verified_at->format('M d, Y H:i') }})
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($order->status === 'pending')
                    <div class="card bg-light mb-3 border-warning">
                        <div class="card-body">
                            <h5>Pickup Confirmation</h5>
                            <p class="text-muted">Delivery has accepted this order. Enter the QR code to confirm pickup.</p>
                            <p><strong>QR Code:</strong> <code class="bg-white p-2">{{ $order->qr_code }}</code></p>
                            <form action="{{ route('shop.orders.verify-pickup', $order->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" class="form-control" name="qr_code" 
                                           placeholder="Scan or enter the QR code" required autofocus>
                                    <button type="submit" class="btn btn-success">Confirm Pickup</button>
                                </div>
                                @error('qr_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </form>
                        </div>
                    </div>
                @endif

                @if(!in_array($order->status, ['delivered', 'cancelled', 'in_transit', 'pending']))
                    <a href="{{ route('shop.orders.edit', $order->id) }}" class="btn btn-warning">Edit Order</a>
                @endif

                @if(!in_array($order->status, ['delivered', 'cancelled']))
                    <form action="{{ route('shop.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to cancel this order?')">
                            Cancel Order
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('shop.orders') }}" class="btn btn-secondary">Back to Orders</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Order History</h5>
            </div>
            <div class="card-body">
                @if($order->logs->count() > 0)
                    <ul class="list-unstyled">
                        @foreach($order->logs as $log)
                            <li class="mb-3">
                                <strong>{{ ucfirst(str_replace('_', ' ', $log->status)) }}</strong>
                                <br>
                                <small class="text-muted">
                                    By {{ $log->changedBy->name }}<br>
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </small>
                                @if($log->note)
                                    <br><small>{{ $log->note }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No history yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
