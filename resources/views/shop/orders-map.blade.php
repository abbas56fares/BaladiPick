@extends('layouts.app')

@section('title', 'Orders Map')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Available Orders Map</h2>
            <p class="text-muted mb-0">All available orders shown as red dots at shop pickup locations.</p>
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
                <div id="orders-map" style="height: 520px; border-radius: 10px; border: 1px solid #dee2e6;"></div>
                <p class="text-muted mt-2 mb-0" id="orders-count">Loading available orders...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .order-marker { background: #dc3545; border: 2px solid #fff; width: 14px; height: 14px; border-radius: 50%; box-shadow: 0 0 4px rgba(0,0,0,0.3); }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('orders-map').setView([31.9539, 35.9106], 7); // regional default
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © OpenStreetMap contributors'
    }).addTo(map);

    const markers = L.layerGroup().addTo(map);
    const countLabel = document.getElementById('orders-count');

    const orderDotIcon = L.divIcon({
        className: 'order-marker',
        iconSize: [14, 14]
    });

    function loadOrders() {
        fetch('{{ route('shop.orders.map.data') }}')
            .then(r => r.json())
            .then(data => {
                markers.clearLayers();
                if (!Array.isArray(data) || data.length === 0) {
                    countLabel.textContent = 'No available orders right now.';
                    return;
                }

                data.forEach(order => {
                    if (!order.lat || !order.lng) return;
                    const marker = L.marker([order.lat, order.lng], { icon: orderDotIcon })
                        .bindPopup(`
                            <strong>Order #${order.id}</strong><br>
                            Shop: ${order.shop}<br>
                            Vehicle: ${order.vehicle_type}<br>
                            Profit: $${order.profit.toFixed(2)}<br>
                            Created: ${order.created_at}
                        `);
                    markers.addLayer(marker);
                });

                countLabel.textContent = `${data.length} available orders.`;

                // Fit bounds if we have markers
                const bounds = markers.getBounds();
                if (bounds && bounds.isValid()) {
                    map.fitBounds(bounds.pad(0.2));
                }
            })
            .catch(() => {
                countLabel.textContent = 'Could not load orders. Please refresh.';
            });
    }

    loadOrders();
    // Refresh every 30 seconds to keep it live
    setInterval(loadOrders, 30000);
});
</script>
@endpush
