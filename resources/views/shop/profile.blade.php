@extends('layouts.app')

@section('title', 'Shop Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Shop Profile</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('shop.profile.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="shop_name" class="form-label">Shop Name</label>
                        <input type="text" class="form-control @error('shop_name') is-invalid @enderror" 
                               id="shop_name" name="shop_name" 
                               value="{{ old('shop_name', $shop->shop_name ?? '') }}" required>
                        @error('shop_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" 
                               value="{{ old('phone', $shop->phone ?? '') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3" required>{{ old('address', $shop->address ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h5 class="mb-3">Shop Location</h5>

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
                        <p class="text-muted mb-2">Click on the map to set your shop location. Coordinates will fill automatically.</p>
                        <div id="shop-map" class="mb-3"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="0.00000001" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" 
                                       value="{{ old('latitude', $shop->latitude ?? '') }}">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="0.00000001" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" 
                                       value="{{ old('longitude', $shop->longitude ?? '') }}">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You need to set your shop location (latitude & longitude) to create orders.
                    </div>

                    <button type="submit" class="btn btn-primary">Save Profile</button>
                    @if($shop)
                        <a href="{{ route('shop.change-password') }}" class="btn btn-warning">Change Password</a>
                        <a href="{{ route('shop.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #shop-map { height: 320px; border: 1px solid #dee2e6; border-radius: 8px; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const existingLat = @json(old('latitude', $shop->latitude ?? null));
    const existingLng = @json(old('longitude', $shop->longitude ?? null));

    // Lebanon center coordinates
    const lebanonLat = 33.8547;
    const lebanonLng = 35.8623;

    // Fallback center (Lebanon)
    const fallbackLat = lebanonLat;
    const fallbackLng = lebanonLng;

    const startLat = existingLat ? parseFloat(existingLat) : fallbackLat;
    const startLng = existingLng ? parseFloat(existingLng) : fallbackLng;

    const map = L.map('shop-map').setView([startLat, startLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;

    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        map.setView([lat, lng], 13);
    }

    if (existingLat && existingLng) {
        setMarker(parseFloat(existingLat), parseFloat(existingLng));
    } else {
        // Set default marker at Lebanon
        setMarker(fallbackLat, fallbackLng);
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

        // Use Nominatim API for geocoding
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

    // Hide search results when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#location_search') && !e.target.closest('#search_location_btn') && !e.target.closest('#search_results')) {
            searchResults.style.display = 'none';
        }
    });
});
</script>
@endpush
