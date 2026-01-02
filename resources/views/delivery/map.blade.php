@extends('layouts.app')

@section('title', 'Delivery Map')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Delivery Orders Map</h2>
        <p class="text-muted">Browse available orders and manage your accepted orders</p>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Your Accepted Orders <span id="shop-filter-badge" class="badge bg-info" style="display:none;"></span></h5>
                <button id="reset-view-btn" class="btn btn-sm btn-secondary" style="display:none;">View Available</button>
            </div>
            <div class="card-body">
                <div id="orders-list">
                    <p class="text-center">Loading your accepted orders...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Map</h5>
            </div>
            <div class="card-body">
                <div id="orders-map" style="height: 520px; border-radius: 10px; border: 1px solid #dee2e6;"></div>
                <p class="text-muted mt-2 mb-0" id="orders-map-count">Loading map...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .shop-marker {
        background: #dc3545;
        border: 2px solid #fff;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: white;
    }
    .order-marker {
        background: #dc3545;
        border: 2px solid #fff;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        box-shadow: 0 0 4px rgba(0,0,0,0.3);
    }
    .delivery-marker {
        background: #28a745;
        border: 2px solid #fff;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
    }
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
    const countLabel = document.getElementById('orders-map-count');
    const shopFilterBadge = document.getElementById('shop-filter-badge');
    const resetBtn = document.getElementById('reset-view-btn');

    let allData = null;
    let selectedShop = null;
    let routeLayer = null;
    let deliveryMarker = null;
    let deliveryLocation = null;

    const shopIcon = L.divIcon({
        className: 'shop-marker',
        html: '<i class="bi bi-shop"></i>',
        iconSize: [24, 24]
    });

    const orderDotIcon = L.divIcon({
        className: 'order-marker',
        iconSize: [14, 14]
    });

    const deliveryIcon = L.divIcon({
        className: 'delivery-marker',
        html: '<i class="bi bi-bicycle"></i>',
        iconSize: [20, 20]
    });

    // Get delivery person's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                deliveryLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                addDeliveryMarker();
            },
            function(error) {
                console.warn('Geolocation error:', error.message);
            }
        );
    }

    function addDeliveryMarker() {
        if (!deliveryLocation) return;
        
        if (deliveryMarker) {
            map.removeLayer(deliveryMarker);
        }
        
        deliveryMarker = L.marker([deliveryLocation.lat, deliveryLocation.lng], { icon: deliveryIcon })
            .bindPopup('<strong>Your Location</strong><br><small>Current position</small>')
            .addTo(map);
    }

    function drawRouteTo(targetLat, targetLng) {
        if (!deliveryLocation) {
            countLabel.textContent = 'Enable location to draw path.';
            return;
        }

        // Clear previous route
        if (routeLayer) {
            map.removeLayer(routeLayer);
        }

        const startLat = deliveryLocation.lat;
        const startLng = deliveryLocation.lng;

        // Try OSRM routing API for street path
        const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${targetLng},${targetLat}?overview=full&geometries=geojson`;

        fetch(osrmUrl)
            .then(res => res.json())
            .then(data => {
                if (data && data.routes && data.routes.length > 0) {
                    const geometry = data.routes[0].geometry; // GeoJSON LineString
                    const feature = {
                        type: 'Feature',
                        properties: {},
                        geometry
                    };
                    routeLayer = L.geoJSON(feature, {
                        style: { color: '#0d6efd', weight: 4, opacity: 0.9 }
                    }).addTo(map);

                    const bounds = routeLayer.getBounds();
                    if (bounds && bounds.isValid()) {
                        map.fitBounds(bounds.pad(0.2));
                    }
                } else {
                    // Fallback: straight line
                    routeLayer = L.polyline([[startLat, startLng], [targetLat, targetLng]], {
                        color: '#0d6efd',
                        weight: 4,
                        opacity: 0.85
                    }).addTo(map);
                    const bounds = routeLayer.getBounds();
                    if (bounds && bounds.isValid()) {
                        map.fitBounds(bounds.pad(0.2));
                    }
                }
            })
            .catch(() => {
                // Fallback on error
                routeLayer = L.polyline([[startLat, startLng], [targetLat, targetLng]], {
                    color: '#0d6efd',
                    weight: 4,
                    opacity: 0.85
                }).addTo(map);
                const bounds = routeLayer.getBounds();
                if (bounds && bounds.isValid()) {
                    map.fitBounds(bounds.pad(0.2));
                }
            });
    }

    resetBtn.addEventListener('click', function() {
        selectedShop = null;
        if (allData) {
            renderTable(allData.orders, false); // Show available orders
            renderMap(allData.shops, allData.orders);
            shopFilterBadge.style.display = 'none';
            resetBtn.style.display = 'none';
            addDeliveryMarker(); // Re-add delivery marker
        }
    });

    function renderTable(orders, isAccepted = false) {
        const container = document.getElementById('orders-list');
        if (!orders || orders.length === 0) {
            const msg = isAccepted 
                ? '<p class="text-muted text-center">You have not accepted any orders yet. <br><strong>Click on order red icons on the map to accept them.</strong></p>'
                : '<p class="text-muted text-center">No available orders at the moment.</p>';
            container.innerHTML = msg;
            return;
        }
        let html = '<div class="table-responsive"><table class="table table-hover">';
        html += '<thead><tr>';
        if (isAccepted) {
            html += '<th>Order ID</th><th>Shop</th><th>Client</th><th>Vehicle</th><th>Profit</th><th>Status</th><th>Action</th>';
        } else {
            html += '<th>Order ID</th><th>Shop</th><th>Client</th><th>Location</th><th>Vehicle</th><th>Profit</th><th>Action</th>';
        }
        html += '</tr></thead><tbody>';
        orders.forEach(order => {
            html += '<tr>';
            html += `<td>#${order.id}</td>`;
            html += `<td>${order.shop ? order.shop.shop_name : 'N/A'}</td>`;
            html += `<td>${order.client_name}</td>`;
            if (!isAccepted) {
                html += `<td>${order.client_lat}, ${order.client_lng}</td>`;
            }
            html += `<td><span class="badge bg-secondary">${order.vehicle_type}</span></td>`;
            html += `<td>$${parseFloat(order.profit).toFixed(2)}</td>`;
            if (isAccepted) {
                const statusBadge = order.status === 'pending' ? 'warning' : (order.status === 'in_transit' ? 'info' : 'success');
                const statusText = order.status.replace('_', ' ');
                html += `<td><span class="badge bg-${statusBadge}">${statusText}</span></td>`;
            }
            html += `<td>`;
            if (isAccepted) {
                html += `<a href="/delivery/orders/${order.id}" class="btn btn-sm btn-primary">View</a>`;
            } else {
                html += `
                    <form action="/delivery/orders/${order.id}/accept" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-sm btn-success">Accept</button>
                    </form>
                `;
            }
            html += `</td>`;
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    function renderMap(shops, availableOrders) {
        markers.clearLayers();
        if (!shops || shops.length === 0) {
            countLabel.textContent = 'No shops with available orders.';
            return;
        }
        
        let totalOrders = 0;
        shops.forEach(shop => {
            if (!shop.latitude || !shop.longitude) return;
            totalOrders += shop.available_orders_count;
            
            let popupContent = `
                <strong>${shop.shop_name}</strong><br>
                <small><i class="bi bi-telephone"></i> ${shop.shop_phone}</small><br>
                <span class="badge bg-danger mt-1">${shop.available_orders_count} Available Order${shop.available_orders_count > 1 ? 's' : ''}</span><br>
                <button class="btn btn-sm btn-primary mt-2" onclick="window.showShopOrders(${shop.shop_id})">View Orders</button>
            `;
            
            const marker = L.marker([shop.latitude, shop.longitude], { icon: shopIcon })
                .bindPopup(popupContent)
                .on('click', function() {
                    drawRouteTo(shop.latitude, shop.longitude);
                });
            markers.addLayer(marker);
        });

        // Add order markers
        if (availableOrders && availableOrders.length > 0) {
            availableOrders.forEach(order => {
                if (!order.client_lat || !order.client_lng) return;
                
                const marker = L.marker([order.client_lat, order.client_lng], { icon: orderDotIcon })
                    .bindPopup(`
                        <strong>Order #${order.id}</strong><br>
                        Client: ${order.client_name}<br>
                        Phone: ${order.client_phone}<br>
                        Shop: ${order.shop.shop_name}<br>
                        Vehicle: ${order.vehicle_type}<br>
                        Profit: $${parseFloat(order.profit).toFixed(2)}<br>
                        <button class="btn btn-sm btn-success mt-2" onclick="window.acceptOrderFromMap(${order.id})">Accept Order</button>
                    `)
                    .on('click', function() {
                        drawRouteTo(order.client_lat, order.client_lng);
                    });
                markers.addLayer(marker);
            });
        }
        
        const bounds = markers.getBounds();
        if (bounds && bounds.isValid()) {
            map.fitBounds(bounds.pad(0.2));
        }
        countLabel.textContent = `${shops.length} shop${shops.length > 1 ? 's' : ''} with ${totalOrders} available order${totalOrders > 1 ? 's' : ''}.`;
        
        addDeliveryMarker(); // Keep delivery marker visible
    }

    function renderShopOrders(shop, availableOrders) {
        // Clear existing markers and re-render all shops + only this shop's orders
        markers.clearLayers();
        
        // Re-add all shop markers
        if (allData && allData.shops) {
            allData.shops.forEach(s => {
                if (!s.latitude || !s.longitude) return;
                
                let popupContent = `
                    <strong>${s.shop_name}</strong><br>
                    <small><i class="bi bi-telephone"></i> ${s.shop_phone}</small><br>
                    <span class="badge bg-danger mt-1">${s.available_orders_count} Available Order${s.available_orders_count > 1 ? 's' : ''}</span><br>
                    <button class="btn btn-sm btn-primary mt-2" onclick="window.showShopOrders(${s.shop_id})">View Orders</button>
                `;
                
                const marker = L.marker([s.latitude, s.longitude], { icon: shopIcon })
                    .bindPopup(popupContent)
                    .on('click', function() {
                        drawRouteTo(s.latitude, s.longitude);
                    });
                markers.addLayer(marker);
            });
        }
        
        // Add order markers ONLY for the selected shop
        const shopOrdersFiltered = availableOrders.filter(o => o.shop_id === shop.shop_id);
        shopOrdersFiltered.forEach(order => {
            if (!order.client_lat || !order.client_lng) return;
            
            const marker = L.marker([order.client_lat, order.client_lng], { icon: orderDotIcon })
                .bindPopup(`
                    <strong>Order #${order.id}</strong><br>
                    Client: ${order.client_name}<br>
                    Phone: ${order.client_phone}<br>
                    Vehicle: ${order.vehicle_type}<br>
                    Profit: $${parseFloat(order.profit).toFixed(2)}<br>
                    <button class="btn btn-sm btn-success mt-2" onclick="window.acceptOrderFromMap(${order.id})">Accept Order</button>
                `)
                .on('click', function() {
                    drawRouteTo(order.client_lat, order.client_lng);
                });
            markers.addLayer(marker);
        });

        // Highlight the selected shop
        if (shop.latitude && shop.longitude) {
            const bounds = L.latLngBounds(
                [[shop.latitude, shop.longitude]],
                shopOrdersFiltered.length > 0 
                    ? shopOrdersFiltered.map(o => [o.client_lat, o.client_lng])
                    : []
            );
            
            if (bounds.isValid()) {
                map.fitBounds(bounds.pad(0.2));
            }
        }
        
        const orderCount = shopOrdersFiltered.length;
        countLabel.textContent = `Showing ${orderCount} order${orderCount > 1 ? 's' : ''} from ${shop.shop_name} | All shops still visible`;
        shopFilterBadge.textContent = shop.shop_name;
        shopFilterBadge.style.display = 'inline-block';
        resetBtn.style.display = 'inline-block';
        
        addDeliveryMarker(); // Keep delivery marker visible
    }

    window.showShopOrders = function(shopId) {
        if (!allData) return;
        
        selectedShop = allData.shops.find(s => s.shop_id === shopId);
        if (!selectedShop) return;
        
        // Filter orders for this shop
        const shopOrders = allData.orders.filter(o => o.shop_id === shopId);
        renderTable(shopOrders, false); // Show available orders table
        renderShopOrders(selectedShop, allData.orders);
    };

    window.acceptOrderFromMap = function(orderId) {
        if (!confirm('Accept this order?')) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/delivery/orders/${orderId}/accept`;
        
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        
        form.appendChild(token);
        document.body.appendChild(form);
        form.submit();
    };

    function loadAvailableOrders() {
        fetch('{{ route("delivery.orders.available") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                allData = data;
                // Don't render table here, let loadAcceptedOrders handle it
                renderMap(data.shops, data.orders);
            })
            .catch(error => {
                console.error('Error loading available orders:', error);
                countLabel.textContent = 'Could not load map data.';
            });
    }

    function loadAcceptedOrders() {
        fetch('{{ route("delivery.orders.accepted") }}', {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.orders !== undefined) {
                    renderTable(data.orders, true); // Show accepted orders
                } else {
                    console.error('Invalid data structure:', data);
                    document.getElementById('orders-list').innerHTML = '<p class="text-warning text-center">Invalid response format.</p>';
                }
            })
            .catch(error => {
                console.error('Error loading accepted orders:', error);
                document.getElementById('orders-list').innerHTML = '<p class="text-danger text-center">Error: ' + error.message + '</p>';
            });
    }

    // Load accepted orders first, then available orders after a short delay
    loadAcceptedOrders();
    setTimeout(() => {
        loadAvailableOrders();
    }, 500);
    
    // Refresh periodically
    setInterval(() => {
        loadAcceptedOrders();
        loadAvailableOrders();
    }, 30000);
});
</script>
@endpush
