@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Order #{{ $order->id }} Details</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                    @if($order->shop_lat && $order->shop_lng && $order->client_lat && $order->client_lng)
                        <button type="button" class="btn btn-sm btn-secondary"
                            onclick="showOrderLocationMap({{ $order->shop_lat }}, {{ $order->shop_lng }}, {{ $order->client_lat }}, {{ $order->client_lng }}, '{{ $order->shop->shop_name }}', '{{ $order->client_name }}')">
                            <i class="bi bi-geo-alt"></i> Show Location
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Shop Information</h5>
                        <p><strong>Name:</strong> {{ $order->shop->shop_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->shop->phone }}</p>
                        <p><strong>Address:</strong> {{ $order->shop->address }}</p>
                        <p><strong>Pickup:</strong> {{ $order->shop_lat }}, {{ $order->shop_lng }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Client Information</h5>
                        <p><strong>Name:</strong> {{ $order->client_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->client_phone }}</p>
                        <p><strong>Delivery:</strong> {{ $order->client_lat }}, {{ $order->client_lng }}</p>
                    </div>
                </div>

                @if($order->delivery)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h5>Delivery Driver</h5>
                            <p><strong>Name:</strong> {{ $order->delivery->name }}</p>
                            <p><strong>Email:</strong> {{ $order->delivery->email }}</p>
                            <p><strong>Phone:</strong> {{ $order->delivery->phone }}</p>
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Order Contents & Description</h5>
                        @if($order->order_contents)
                            <p><strong>Items:</strong></p>
                            <p style="white-space: pre-wrap; background-color: #f8f9fa; padding: 10px; border-radius: 4px;">
                                {{ $order->order_contents }}
                            </p>
                        @else
                            <p class="text-muted">No order contents provided.</p>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Pricing Information</h5>
                        <p><strong>Order Price:</strong> ${{ number_format($order->order_price, 2) }}</p>
                        <p><strong>Delivery Cost:</strong> ${{ number_format($order->delivery_cost, 2) }}</p>
                        <p><strong>Shop Profit:</strong> ${{ number_format($order->profit, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Route Information</h5>
                        <p><strong>Distance:</strong> {{ $order->distance_km }} km</p>
                        <p><strong>Vehicle Type:</strong> <span class="badge bg-secondary">{{ ucfirst($order->vehicle_type) }}</span></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
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
                        <p><strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Verification Status</h5>
                        <p>
                            <strong>QR Verified:</strong> 
                            @if($order->qr_verified)
                                <span class="badge bg-success">Yes</span> 
                                ({{ $order->qr_verified_at->format('M d, Y H:i') }})
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </p>
                        <p>
                            <strong>OTP Verified:</strong> 
                            @if($order->delivery_verified)
                                <span class="badge bg-success">Yes</span>
                                ({{ $order->delivery_verified_at->format('M d, Y H:i') }})
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if(!in_array($order->status, ['delivered', 'cancelled']))
                    <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Cancel this order?')">
                            Cancel Order
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('admin.orders') }}" class="btn btn-secondary">Back to Orders</a>
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
<!-- Location Map Modal -->
<div class="modal fade" id="orderLocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="width: 650px; max-width: 100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="orderLocationModalTitle">Route</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="orderLocationMap" style="width: 100%; height: 650px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let orderMap = null;
let shopMarker = null;
let clientMarker = null;
let routeLayer = null;

function showOrderLocationMap(shopLat, shopLng, clientLat, clientLng, shopName, clientName) {
    const modal = new bootstrap.Modal(document.getElementById('orderLocationModal'));
    document.getElementById('orderLocationModalTitle').textContent = shopName + ' → ' + clientName;
    modal.show();

    setTimeout(() => {
        if (orderMap) {
            orderMap.remove();
        }

        // Use Lebanon as default if coordinates are missing
        const displayShopLat = shopLat || 33.8547;
        const displayShopLng = shopLng || 35.8623;
        const displayClientLat = clientLat || 33.8547;
        const displayClientLng = clientLng || 35.8623;

        // Center between two points
        const bounds = L.latLngBounds([
            [displayShopLat, displayShopLng],
            [displayClientLat, displayClientLng]
        ]);

        orderMap = L.map('orderLocationMap');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(orderMap);

        // Markers
        shopMarker = L.marker([displayShopLat, displayShopLng], { title: 'Pickup: ' + shopName }).addTo(orderMap)
            .bindPopup('<b>Pickup</b><br>' + shopName).openPopup();

        clientMarker = L.marker([displayClientLat, displayClientLng], { title: 'Drop-off: ' + clientName }).addTo(orderMap)
            .bindPopup('<b>Drop-off</b><br>' + clientName);

        // Try OSRM routing for street path
        const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${displayShopLng},${displayShopLat};${displayClientLng},${displayClientLat}?overview=full&geometries=geojson`;

        fetch(osrmUrl)
            .then(res => res.json())
            .then(data => {
                if (data && data.routes && data.routes.length > 0) {
                    const geometry = data.routes[0].geometry; // GeoJSON LineString
                    // Wrap into a Feature for Leaflet geoJSON
                    const feature = {
                        type: 'Feature',
                        properties: {},
                        geometry: geometry
                    };

                    routeLayer = L.geoJSON(feature, {
                        style: { color: '#0d6efd', weight: 4, opacity: 0.9 }
                    }).addTo(orderMap);

                    orderMap.fitBounds(routeLayer.getBounds(), { padding: [30, 30] });
                } else {
                    // Fallback: straight line
                    routeLayer = L.polyline([[displayShopLat, displayShopLng], [displayClientLat, displayClientLng]], {
                        color: '#0d6efd',
                        weight: 4,
                        opacity: 0.8
                    }).addTo(orderMap);
                    orderMap.fitBounds(bounds, { padding: [30, 30] });
                }
            })
            .catch(() => {
                // Fallback: straight line on error
                routeLayer = L.polyline([[displayShopLat, displayShopLng], [displayClientLat, displayClientLng]], {
                    color: '#0d6efd',
                    weight: 4,
                    opacity: 0.8
                }).addTo(orderMap);
                orderMap.fitBounds(bounds, { padding: [30, 30] });
            });
    }, 300);
}

// Manual refresh button
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            if ($('.modal.show').length === 0 && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                location.reload();
            }
        });
    }
});
</script>
@endpush
