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
                        <h5>Shop Information</h5>
                        <p><strong>Shop Name:</strong> {{ $order->shop->shop_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->shop->phone }}</p>
                        <p><strong>Address:</strong> {{ $order->shop->address }}</p>
                        <p><strong>Pickup Location:</strong> {{ $order->shop_lat }}, {{ $order->shop_lng }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Client Information</h5>
                        <p><strong>Name:</strong> {{ $order->client_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->client_phone }}</p>
                        <p><strong>Delivery Location:</strong> {{ $order->client_lat }}, {{ $order->client_lng }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Order Information</h5>
                        <p><strong>Status:</strong> 
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
                        </p>
                        <p><strong>Vehicle Type:</strong> {{ ucfirst($order->vehicle_type) }}</p>
                        <p><strong>Your Profit:</strong> ${{ number_format($order->profit, 2) }}</p>
                        <p><strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($order->status === 'pending')
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5>Pickup Verification</h5>
                            <p class="text-muted">The shop will verify pickup using this QR code.</p>
                            <p><strong>QR Code:</strong> <code class="bg-white p-2">{{ $order->qr_code }}</code></p>
                        </div>
                    </div>
                @endif

                @if($order->status === 'in_transit')
                    <div class="card bg-light mb-3 border-primary">
                        <div class="card-body">
                            <h5>Delivery Verification</h5>
                            <p class="text-muted">Please ask the client for the OTP sent to their phone, then enter it below.</p>
                            
                            <form action="{{ route('delivery.orders.verify-otp', $order->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" class="form-control" name="otp" 
                                           placeholder="Enter 6-digit OTP from client" maxlength="6" required>
                                    <button type="submit" class="btn btn-success">Verify & Complete</button>
                                </div>
                                @error('otp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </form>
                        </div>
                    </div>
                @endif

                @if($order->status === 'delivered')
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Order completed successfully!
                        Delivered at: {{ $order->delivery_verified_at->format('M d, Y H:i') }}
                    </div>
                @endif

                <a href="{{ route('delivery.orders.my') }}" class="btn btn-secondary">Back to My Orders</a>

                <a href="{{ route('delivery.orders.my') }}" class="btn btn-secondary">Back to My Orders</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Verification Status</h5>
            </div>
            <div class="card-body">
                <p>
                    <strong>QR Verified (Pickup):</strong><br>
                    @if($order->qr_verified)
                        <span class="badge bg-success">Yes</span>
                        <br><small>{{ $order->qr_verified_at->format('M d, Y H:i') }}</small>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </p>
                <p>
                    <strong>OTP Verified (Delivery):</strong><br>
                    @if($order->delivery_verified)
                        <span class="badge bg-success">Yes</span>
                        <br><small>{{ $order->delivery_verified_at->format('M d, Y H:i') }}</small>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="card mt-3">
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
