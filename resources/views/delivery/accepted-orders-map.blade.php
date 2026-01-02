@extends('layouts.app')

@section('title', 'My Accepted Orders Map')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>My Accepted Orders Map</h2>
                <p class="text-muted mb-0">View your current location and all pending/in-transit orders on the map.</p>
            </div>
            <div>
                <a href="{{ route('delivery.orders.my') }}" class="btn btn-secondary">Orders List</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="accepted-orders-map" style="height: 600px; border-radius: 10px; border: 1px solid #dee2e6;"></div>
                <div class="mt-3">
                    <p class="mb-2"><strong>Legend:</strong></p>
                    <div class="d-flex gap-4 flex-wrap">
                        <div><span style="display:inline-block;width:20px;height:20px;background:#0d6efd;border-radius:50%;vertical-align:middle;"></span> Your Location</div>
                        <div><span style="display:inline-block;width:16px;height:16px;background:#ffc107;border-radius:50%;vertical-align:middle;"></span> Shop (Pending Pickup)</div>
                        <div><span style="display:inline-block;width:16px;height:16px;background:#dc3545;border-radius:50%;vertical-align:middle;"></span> Client (In Transit)</div>
                    </div>
                    <p class="text-muted mt-2 mb-0" id="status-message">Getting your location...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>My Active Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Shop</th>
                                <th>Client</th>
                                <th>Vehicle</th>
                                <th>Profit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Loading orders...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .my-location-marker {
        background: #0d6efd;
        border: 3px solid #fff;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(13,110,253,0.6);
    }
    .shop-marker-pending {
        background: #ffc107;
        border: 2px solid #fff;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        box-shadow: 0 0 4px rgba(0,0,0,0.3);
    }
    .client-marker-transit {
        background: #dc3545;
        border: 2px solid #fff;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        box-shadow: 0 0 4px rgba(0,0,0,0.3);
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let map = null;
let myLocationMarker = null;
let ordersLayer = null;
let currentLat = null;
let currentLng = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    map = L.map('accepted-orders-map').setView([31.9539, 35.9106], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    ordersLayer = L.layerGroup().addTo(map);

    // Get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            currentLat = position.coords.latitude;
            currentLng = position.coords.longitude;

            // Add "You are here" marker
            const myLocationIcon = L.divIcon({
                className: 'my-location-marker',
                iconSize: [20, 20]
            });

            myLocationMarker = L.marker([currentLat, currentLng], { icon: myLocationIcon })
                .bindPopup('<strong>Your Current Location</strong>')
                .addTo(map);

            map.setView([currentLat, currentLng], 12);
            
            document.getElementById('status-message').textContent = 'Location acquired. Loading orders...';
            
            // Load accepted orders
            loadAcceptedOrders();
        }, function(error) {
            document.getElementById('status-message').textContent = 'Could not get your location: ' + error.message + '. Showing orders without your location.';
            loadAcceptedOrders();
        }, { enableHighAccuracy: true, timeout: 10000 });
    } else {
        document.getElementById('status-message').textContent = 'Geolocation not supported. Showing orders without your location.';
        loadAcceptedOrders();
    }

    // Refresh every 30 seconds
    setInterval(loadAcceptedOrders, 30000);
});

function loadAcceptedOrders() {
    fetch('{{ route('delivery.orders.accepted') }}', {
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderOrdersOnMap(data.orders);
            renderOrdersTable(data.orders);
            
            const count = data.orders.length;
            if (count > 0) {
                document.getElementById('status-message').textContent = `Showing ${count} accepted order(s).`;
            } else {
                document.getElementById('status-message').textContent = 'No accepted orders. Visit Available Orders map to accept new orders.';
            }
        } else {
            document.getElementById('status-message').textContent = 'Error loading orders.';
            document.getElementById('ordersTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Failed to load orders</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('status-message').textContent = 'Error loading orders. Please refresh.';
        document.getElementById('ordersTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading orders</td></tr>';
    });
}

function renderOrdersOnMap(orders) {
    // Clear existing markers
    ordersLayer.clearLayers();

    if (orders.length === 0) {
        return;
    }

    orders.forEach(order => {
        let markerLat, markerLng, markerIcon, popupContent, markerClass;

        if (order.status === 'pending') {
            // Show shop location (pending pickup)
            markerLat = parseFloat(order.shop.latitude);
            markerLng = parseFloat(order.shop.longitude);
            markerClass = 'shop-marker-pending';
            popupContent = `
                <strong>Order #${order.id} - Pending Pickup</strong><br>
                Shop: ${order.shop.shop_name}<br>
                Phone: ${order.shop.phone}<br>
                Client: ${order.client_name}<br>
                Vehicle: ${order.vehicle_type}<br>
                Profit: $${parseFloat(order.profit).toFixed(2)}<br>
                <a href="/delivery/orders/${order.id}" class="btn btn-sm btn-info mt-1">View Details</a>
            `;
        } else if (order.status === 'in_transit') {
            // Show client location (in transit to delivery)
            markerLat = parseFloat(order.client_lat);
            markerLng = parseFloat(order.client_lng);
            markerClass = 'client-marker-transit';
            popupContent = `
                <strong>Order #${order.id} - In Transit</strong><br>
                Client: ${order.client_name}<br>
                Phone: ${order.client_phone}<br>
                Vehicle: ${order.vehicle_type}<br>
                Profit: $${parseFloat(order.profit).toFixed(2)}<br>
                <a href="/delivery/orders/${order.id}" class="btn btn-sm btn-info mt-1">View Details</a>
            `;
        }

        if (markerLat && markerLng && !isNaN(markerLat) && !isNaN(markerLng)) {
            const icon = L.divIcon({
                className: markerClass,
                iconSize: [16, 16]
            });

            const marker = L.marker([markerLat, markerLng], { icon: icon })
                .bindPopup(popupContent);
            
            ordersLayer.addLayer(marker);
        }
    });

    // Fit map to show all markers (user location + orders)
    const allMarkers = [];
    if (myLocationMarker) {
        allMarkers.push(myLocationMarker);
    }
    ordersLayer.eachLayer(marker => allMarkers.push(marker));

    if (allMarkers.length > 0) {
        const group = L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

function renderOrdersTable(orders) {
    const tbody = document.getElementById('ordersTableBody');
    
    if (orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No accepted orders. <a href="{{ route('delivery.map') }}">View available orders</a></td></tr>';
        return;
    }

    let html = '';
    orders.forEach(order => {
        const statusBadge = order.status === 'pending' 
            ? '<span class="badge bg-warning">Pending Pickup</span>'
            : '<span class="badge bg-primary">In Transit</span>';

        html += `
            <tr>
                <td>#${order.id}</td>
                <td>${statusBadge}</td>
                <td>${order.shop.shop_name}</td>
                <td>${order.client_name}</td>
                <td><span class="badge bg-secondary">${order.vehicle_type}</span></td>
                <td>$${parseFloat(order.profit).toFixed(2)}</td>
                <td>
                    <a href="/delivery/orders/${order.id}" class="btn btn-sm btn-info">View</a>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}
</script>
@endpush
