@extends('layouts.app')

@section('title', 'My Orders Map')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>My Orders Map</h2>
            <p class="text-muted mb-0">View your shop's orders on the map - delivery destinations shown as markers.</p>
        </div>
        <div>
            <a href="{{ route('shop.orders') }}" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Shop Location (Static) -->
                <div class="mb-2">
                    <strong>Your Shop:</strong> {{ $shop->shop_name }} 
                    <span class="text-muted">({{ $shop->latitude }}, {{ $shop->longitude }})</span>
                </div>
                <div id="orders-map" style="height: 520px; border-radius: 10px; border: 1px solid #dee2e6;"></div>
                <p class="text-muted mt-2 mb-0" id="orders-count">Loading your orders...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .order-marker-available { background: #dc3545; border: 2px solid #fff; width: 16px; height: 16px; border-radius: 50%; box-shadow: 0 0 4px rgba(0,0,0,0.3); }
    .order-marker-pending { background: #ffc107; border: 2px solid #fff; width: 16px; height: 16px; border-radius: 50%; box-shadow: 0 0 4px rgba(0,0,0,0.3); }
    .order-marker-transit { background: #0dcaf0; border: 2px solid #fff; width: 16px; height: 16px; border-radius: 50%; box-shadow: 0 0 4px rgba(0,0,0,0.3); }
    .shop-marker { background: #198754; border: 2px solid #fff; width: 20px; height: 20px; border-radius: 50%; box-shadow: 0 0 6px rgba(0,0,0,0.4); }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const shopLat = {{ $shop->latitude }};
    const shopLng = {{ $shop->longitude }};
    
    const map = L.map('orders-map').setView([shopLat, shopLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © OpenStreetMap contributors'
    }).addTo(map);

    // Add shop marker (green)
    const shopIcon = L.divIcon({
        className: 'shop-marker',
        iconSize: [20, 20]
    });
    L.marker([shopLat, shopLng], { icon: shopIcon })
        .bindPopup('<strong>{{ $shop->shop_name }}</strong><br>Your Shop Location')
        .addTo(map);

    const markers = L.layerGroup().addTo(map);
    const countLabel = document.getElementById('orders-count');

    function getOrderIcon(status) {
        const className = status === 'available' ? 'order-marker-available' : 
                         status === 'pending' ? 'order-marker-pending' : 
                         'order-marker-transit';
        return L.divIcon({
            className: className,
            iconSize: [16, 16]
        });
    }

    function loadOrders() {
        fetch('{{ route('shop.orders.map.data') }}')
            .then(r => r.json())
            .then(data => {
                markers.clearLayers();
                if (!Array.isArray(data) || data.length === 0) {
                    countLabel.textContent = 'No active orders right now.';
                    return;
                }

                const statusColors = {
                    available: 'Available (Red)',
                    pending: 'Pending Pickup (Yellow)',
                    in_transit: 'In Transit (Blue)'
                };

                data.forEach(order => {
                    if (!order.lat || !order.lng) return;
                    const marker = L.marker([order.lat, order.lng], { icon: getOrderIcon(order.status) })
                        .bindPopup(`
                            <strong>Order #${order.id}</strong><br>
                            Client: ${order.client_name}<br>
                            Phone: ${order.client_phone}<br>
                            Status: ${order.status}<br>
                            ${order.delivery ? 'Delivery: ' + order.delivery + '<br>' : ''}
                            Vehicle: ${order.vehicle_type}<br>
                            Profit: $${order.profit.toFixed(2)}<br>
                            <a href="/shop/orders/${order.id}" class="btn btn-sm btn-info mt-1">View Details</a>
                        `);
                    markers.addLayer(marker);
                });

                countLabel.innerHTML = `${data.length} active order(s). 
                    <span class="ms-2">🔴 Available</span> 
                    <span class="ms-2">🟡 Pending Pickup</span> 
                    <span class="ms-2">🔵 In Transit</span>`;

                // Fit bounds to show shop + all orders
                const allMarkers = [L.marker([shopLat, shopLng])];
                markers.eachLayer(m => allMarkers.push(m));
                if (allMarkers.length > 1) {
                    const group = L.featureGroup(allMarkers);
                    map.fitBounds(group.getBounds().pad(0.2));
                }
            })
            .catch(() => {
                countLabel.textContent = 'Could not load orders. Please refresh.';
            });
    }

    loadOrders();
    // Refresh every 30 seconds
    setInterval(loadOrders, 30000);
});
</script>
@endpush
