@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Edit Delivery Order #{{ $order->id }}</h4>
                <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('shop.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="mb-3">Client Information</h5>
                    
                    <div class="mb-3">
                        <label for="client_name" class="form-label">Client Name</label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                               id="client_name" name="client_name" 
                               value="{{ old('client_name', $order->client_name) }}" required>
                        @error('client_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="client_phone" class="form-label">Client Phone</label>
                        <input type="text" class="form-control @error('client_phone') is-invalid @enderror" 
                               id="client_phone" name="client_phone" 
                               value="{{ old('client_phone', $order->client_phone) }}" required>
                        @error('client_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h5 class="mb-3 mt-4">Client Location</h5>

                    <div class="mb-3">
                        <label for="location_search" class="form-label">Search Location</label>
                        <div class="input-group">
                            <input type="text" class="form-control" 
                                   id="location_search" placeholder="Enter location name or address (e.g., Beirut, Lebanon)">
                            <button class="btn btn-outline-secondary" type="button" id="search_location_btn">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                        <div id="search_results" class="mt-2" style="display: none;">
                            <div class="list-group" id="results_list"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted mb-2">Click on the map to set the client location. Coordinates will fill automatically.</p>
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="toggleRangeBtn">
                                <i class="bi bi-eye"></i> Toggle Service Area
                            </button>
                        </div>
                        <div id="client-map" class="mb-3"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_lat" class="form-label">Latitude</label>
                                <input type="number" step="0.00000001" class="form-control @error('client_lat') is-invalid @enderror" 
                                       id="client_lat" name="client_lat" 
                                       value="{{ old('client_lat', $order->client_lat) }}" required>
                                @error('client_lat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_lng" class="form-label">Longitude</label>
                                <input type="number" step="0.00000001" class="form-control @error('client_lng') is-invalid @enderror" 
                                       id="client_lng" name="client_lng" 
                                       value="{{ old('client_lng', $order->client_lng) }}" required>
                                @error('client_lng')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Order Details</h5>

                    <div class="mb-3">
                        <label for="order_contents" class="form-label">Order Contents</label>
                        <textarea class="form-control @error('order_contents') is-invalid @enderror" 
                                  id="order_contents" name="order_contents" rows="3" 
                                  placeholder="Describe what is being delivered" 
                                  required>{{ old('order_contents', $order->order_contents) }}</textarea>
                        <small class="form-text text-muted">Provide a clear description of items to be delivered.</small>
                        @error('order_contents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="order_price" class="form-label">Order Value ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('order_price') is-invalid @enderror" 
                               id="order_price" name="order_price" 
                               value="{{ old('order_price', $order->order_price) }}" 
                               placeholder="Total value of items being delivered" required>
                        <small class="form-text text-muted">The actual value/price of items (for insurance/reference).</small>
                        @error('order_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="vehicle_type" class="form-label">Required Vehicle Type</label>
                        <select class="form-select @error('vehicle_type') is-invalid @enderror" 
                                id="vehicle_type" name="vehicle_type" required>
                            <option value="">Select vehicle type...</option>
                            <option value="bike" {{ old('vehicle_type', $order->vehicle_type) == 'bike' ? 'selected' : '' }}>Bike/Motorcycle (up to 10km, small packages)</option>
                            <option value="car" {{ old('vehicle_type', $order->vehicle_type) == 'car' ? 'selected' : '' }}>Car (up to 90km, medium packages)</option>
                            <option value="pickup" {{ old('vehicle_type', $order->vehicle_type) == 'pickup' ? 'selected' : '' }}>Pickup Truck (up to 90km, large/heavy items)</option>
                        </select>
                        <small class="form-text text-muted">Delivery cost will be recalculated if distance or vehicle type changes.</small>
                        @error('vehicle_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($order->distance_km && $order->delivery_cost)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Current distance: {{ number_format($order->distance_km, 2) }} km | Delivery cost: ${{ number_format($order->delivery_cost, 2) }} | Your earnings: ${{ number_format($order->profit, 2) }}
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Pickup location uses your shop coordinates ({{ $shop->latitude }}, {{ $shop->longitude }}).
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #client-map { height: 320px; border: 1px solid #dee2e6; border-radius: 8px; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const shopLat = {{ $shop->latitude ?? 0 }};
    const shopLng = {{ $shop->longitude ?? 0 }};
    const currentLat = @json(old('client_lat', $order->client_lat));
    const currentLng = @json(old('client_lng', $order->client_lng));

    // Lebanon center coordinates
    const lebanonLat = 33.8547;
    const lebanonLng = 35.8623;

    const startLat = currentLat ? parseFloat(currentLat) : (shopLat && shopLng ? shopLat : lebanonLat);
    const startLng = currentLng ? parseFloat(currentLng) : (shopLat && shopLng ? shopLng : lebanonLng);

    const map = L.map('client-map').setView([startLat, startLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    let shopMarker = null;
    let rangeCircle = null;
    let rangeVisible = true;

    // Maximum distances for each vehicle type (in km)
    const maxDistances = {
        'bike': 10,
        'car': 90,
        'pickup': 90
    };

    // Add shop location marker with better icon
    if (shopLat && shopLng) {
        const shopIcon = L.icon({
            iconUrl: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI1MCIgdmlld0JveD0iMCAwIDQwIDUwIj48cGF0aCBkPSJNMjAgMEMxMC4wNiAwIDIgNy45NCAyIDIwYzAgMTAgNDI0LCAzMCA0IDMwczIwLTIwIDIwLTMwQzM4IDcuOTQgMjkuOTQgMCAyMCAweiIgZmlsbD0iI0RDMTQzQyIgc3Ryb2tlPSIjQzAxMDI2IiBzdHJva2Utd2lkdGg9IjIiIi8+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iOCIgZmlsbD0iI2ZmZiIgc3Ryb2tlPSIjREMxNDNDIiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTggMTlMMTggMjJMMjAgMjFMMjIgMjJMMjIgMTlIMTl6IE0xNyAxN0gyM1YxOEgxN3oiIGZpbGw9IiNEQzE0M0MiIi8+PC9zdmc+',
            iconSize: [40, 50],
            iconAnchor: [20, 50],
            popupAnchor: [0, -50],
            shadowUrl: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI1MCIgdmlld0JveD0iMCAwIDQwIDUwIj48ZWxsaXBzZSBjeD0iMjAiIGN5PSI0NiIgcng9IjE0IiByeT0iMyIgZmlsbD0iIzAwMDAwMCIgb3BhY2l0eT0iMC4zIi8+PC9zdmc+',
            shadowSize: [40, 50],
            shadowAnchor: [20, 50]
        });
        shopMarker = L.marker([shopLat, shopLng], {
            icon: shopIcon,
            title: 'Your Shop Location'
        }).addTo(map);
        shopMarker.bindPopup('<strong>🏪 Your Shop</strong><br>Service area shown as circle');
    }

    // Initialize range circle based on selected vehicle type
    function updateRangeCircle() {
        const vehicleType = document.getElementById('vehicle_type').value;
        const maxDistance = maxDistances[vehicleType] || 10;
        
        if (shopLat && shopLng) {
            if (rangeCircle) {
                map.removeLayer(rangeCircle);
            }
            
            if (rangeVisible) {
                rangeCircle = L.circle([shopLat, shopLng], {
                    radius: maxDistance * 1000, // Convert km to meters
                    color: vehicleType === 'bike' ? '#FFA500' : (vehicleType === 'car' ? '#4169E1' : '#DC143C'),
                    fillColor: vehicleType === 'bike' ? '#FFA500' : (vehicleType === 'car' ? '#4169E1' : '#DC143C'),
                    fillOpacity: 0.1,
                    weight: 2,
                    dashArray: '5, 5'
                }).addTo(map);
                
                rangeCircle.bindPopup(`<strong>Service Area</strong><br>${vehicleType.toUpperCase()}: ${maxDistance}km radius`);
            }
        }
    }

    // Toggle range circle visibility
    document.getElementById('toggleRangeBtn').addEventListener('click', function() {
        rangeVisible = !rangeVisible;
        const btn = document.getElementById('toggleRangeBtn');
        
        if (rangeVisible) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-outline-primary');
            btn.innerHTML = '<i class="bi bi-eye"></i> Toggle Service Area';
            updateRangeCircle();
        } else {
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-outline-secondary');
            btn.innerHTML = '<i class="bi bi-eye-slash"></i> Toggle Service Area';
            if (rangeCircle) {
                map.removeLayer(rangeCircle);
            }
        }
    });

    // Update circle when vehicle type changes
    document.getElementById('vehicle_type').addEventListener('change', updateRangeCircle);

    // Initialize on page load
    if (document.getElementById('vehicle_type').value) {
        updateRangeCircle();
    }

    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            const clientIcon = L.icon({
                iconUrl: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI1MCIgdmlld0JveD0iMCAwIDQwIDUwIj48cGF0aCBkPSJNMjAgMEMxMC4wNiAwIDIgNy45NCAyIDIwYzAgMTAgMTggMzAgMTggMzBzMTAtMjAgMTAtMzBDMzggNy45NCAyOS45NCAwIDIwIDB6IiBmaWxsPSIjNDE2OUUxIiBzdHJva2U9IiMxOTQ3RDIiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjIwIiBjeT0iMjAiIHI9IjgiIGZpbGw9IiNmZmYiIHN0cm9rZT0iIzQxNjlFMSIgc3Ryb2tlLXdpZHRoPSIyIi8+PHRleHQgeD0iMjAiIHk9IjI0IiBmb250LXNpemU9IjEyIiBmb250LXdlaWdodD0iYm9sZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzQxNjlFMSI+QTwvdGV4dD48L3N2Zz4=',
                iconSize: [40, 50],
                iconAnchor: [20, 50],
                popupAnchor: [0, -50],
                shadowUrl: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI1MCIgdmlld0JveD0iMCAwIDQwIDUwIj48ZWxsaXBzZSBjeD0iMjAiIGN5PSI0NiIgcng9IjE0IiByeT0iMyIgZmlsbD0iIzAwMDAwMCIgb3BhY2l0eT0iMC4zIi8+PC9zdmc+',
                shadowSize: [40, 50],
                shadowAnchor: [20, 50]
            });
            marker = L.marker([lat, lng], {
                icon: clientIcon,
                title: 'Client Location'
            }).addTo(map);
            marker.bindPopup('<strong>📍 Client Location</strong><br>Delivery destination');
        }
        document.getElementById('client_lat').value = lat.toFixed(8);
        document.getElementById('client_lng').value = lng.toFixed(8);
        map.setView([lat, lng], 13);
    }

    if (currentLat && currentLng) {
        setMarker(parseFloat(currentLat), parseFloat(currentLng));
    }

    map.on('click', function (e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });

    // Location search functionality
    const searchInput = document.getElementById('location_search');
    const searchBtn = document.getElementById('search_location_btn');
    const searchResults = document.getElementById('search_results');
    const resultsList = document.getElementById('results_list');

    function performSearch() {
        const query = searchInput.value.trim();
        if (!query) {
            searchResults.style.display = 'none';
            return;
        }

        const nominatimUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`;

        fetch(nominatimUrl)
            .then(response => response.json())
            .then(data => {
                resultsList.innerHTML = '';
                
                if (data.length === 0) {
                    resultsList.innerHTML = '<div class="alert alert-warning mb-0">No results found</div>';
                    searchResults.style.display = 'block';
                    return;
                }

                data.forEach(item => {
                    const resultItem = document.createElement('button');
                    resultItem.type = 'button';
                    resultItem.className = 'list-group-item list-group-item-action text-start';
                    resultItem.innerHTML = `
                        <strong>${item.name || item.display_name}</strong>
                        <br>
                        <small class="text-muted">${item.display_name}</small>
                    `;
                    resultItem.addEventListener('click', () => {
                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lon);
                        setMarker(lat, lng);
                        searchResults.style.display = 'none';
                        searchInput.value = '';
                    });
                    resultsList.appendChild(resultItem);
                });

                searchResults.style.display = 'block';
            })
            .catch(error => {
                console.error('Search error:', error);
                resultsList.innerHTML = '<div class="alert alert-danger mb-0">Search failed. Please try again.</div>';
                searchResults.style.display = 'block';
            });
    }

    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#location_search') && !e.target.closest('#search_location_btn') && !e.target.closest('#search_results')) {
            searchResults.style.display = 'none';
        }
    });
    // Earnings calculation
});
</script>
@endpush
