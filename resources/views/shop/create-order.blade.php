@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Create New Delivery Order</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('shop.orders.store') }}" method="POST">
                    @csrf
                    
                    <h5 class="mb-3">Client Information</h5>
                    
                    <div class="mb-3">
                        <label for="client_name" class="form-label">Client Name</label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                               id="client_name" name="client_name" 
                               value="{{ old('client_name') }}" required>
                        @error('client_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="client_phone" class="form-label">Client Phone</label>
                        <input type="text" class="form-control @error('client_phone') is-invalid @enderror" 
                               id="client_phone" name="client_phone" 
                               value="{{ old('client_phone') }}" required>
                        @error('client_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h5 class="mb-3 mt-4">Client Location</h5>

                    <div class="mb-3">
                        <p class="text-muted mb-2">Click on the map to set the client location. Coordinates will fill automatically.</p>
                        <div id="client-map" class="mb-3"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_lat" class="form-label">Latitude</label>
                                <input type="number" step="0.00000001" class="form-control @error('client_lat') is-invalid @enderror" 
                                       id="client_lat" name="client_lat" 
                                       value="{{ old('client_lat') }}" required>
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
                                       value="{{ old('client_lng') }}" required>
                                @error('client_lng')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Order Details</h5>

                    <div class="mb-3">
                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                        <select class="form-select @error('vehicle_type') is-invalid @enderror" 
                                id="vehicle_type" name="vehicle_type" required>
                            <option value="">Select vehicle...</option>
                            <option value="bike" {{ old('vehicle_type') == 'bike' ? 'selected' : '' }}>Bike</option>
                            <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                        </select>
                        @error('vehicle_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="profit" class="form-label">Delivery Profit ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('profit') is-invalid @enderror" 
                               id="profit" name="profit" 
                               value="{{ old('profit') }}" required>
                        @error('profit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Your shop location ({{ $shop->latitude }}, {{ $shop->longitude }}) will be used as pickup location.
                    </div>

                    <button type="submit" class="btn btn-primary">Create Order</button>
                    <a href="{{ route('shop.orders') }}" class="btn btn-secondary">Cancel</a>
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
    const oldLat = @json(old('client_lat'));
    const oldLng = @json(old('client_lng'));

    const startLat = oldLat ? parseFloat(oldLat) : shopLat;
    const startLng = oldLng ? parseFloat(oldLng) : shopLng;

    const map = L.map('client-map').setView([startLat, startLng], 13);
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
        document.getElementById('client_lat').value = lat.toFixed(8);
        document.getElementById('client_lng').value = lng.toFixed(8);
    }

    // Set initial marker if we have coordinates
    if (oldLat && oldLng) {
        setMarker(parseFloat(oldLat), parseFloat(oldLng));
    }

    map.on('click', function (e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });
});
</script>
@endpush
