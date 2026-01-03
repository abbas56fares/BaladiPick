@extends('layouts.app')

@section('title', 'Shop Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.shops') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Shops</a>
            <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Shop Information</h4>
                <div>
                    @if($shop->is_verified)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning">Unverified</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Shop Details</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Shop ID:</th>
                                <td>{{ $shop->id }}</td>
                            </tr>
                            <tr>
                                <th>Shop Name:</th>
                                <td><strong>{{ $shop->shop_name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $shop->phone }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{ $shop->address }}</td>
                            </tr>
                            <tr>
                                <th>Location:</th>
                                <td>
                                    @if($shop->latitude && $shop->longitude)
                                        {{ $shop->latitude }}, {{ $shop->longitude }}
                                        <button type="button" class="btn btn-sm btn-secondary ms-2" 
                                            onclick="showLocationMap({{ $shop->latitude }}, {{ $shop->longitude }}, '{{ $shop->shop_name }}')">
                                            <i class="bi bi-geo-alt"></i> View on Map
                                        </button>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Registered:</th>
                                <td>{{ $shop->created_at->format('F d, Y - H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Owner Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">User ID:</th>
                                <td>{{ $shop->user->id }}</td>
                            </tr>
                            <tr>
                                <th>Owner Name:</th>
                                <td><strong>{{ $shop->user->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $shop->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Personal Phone:</th>
                                <td>{{ $shop->user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>ID Document:</th>
                                <td>
                                    @if($shop->user->id_document_path)
                                        <a href="{{ asset('storage/' . $shop->user->id_document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="bi bi-file-image"></i> View Document
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Account Status:</th>
                                <td>
                                    @if($shop->user->verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Pending Verification</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    @if(!$shop->is_verified)
                        <form action="{{ route('admin.shops.verify', $shop->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Verify Shop
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.shops.disable', $shop->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle"></i> Disable Shop
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="card">
            <div class="card-header">
                <h4>Orders History</h4>
            </div>
            <div class="card-body">
                @if($shop->orders->count() > 0)
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>Total Orders</h6>
                                    <h3>{{ $shop->orders->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>Delivered</h6>
                                    <h3>{{ $shop->orders()->where('status', 'delivered')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6>Pending</h6>
                                    <h3>{{ $shop->orders()->whereIn('status', ['available', 'pending', 'in_transit'])->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6>Cancelled</h6>
                                    <h3>{{ $shop->orders()->where('status', 'cancelled')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Client Name</th>
                                    <th>Client Phone</th>
                                    <th>Vehicle</th>
                                    <th>Profit</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->id }}</a>
                                        </td>
                                        <td>{{ $order->client_name }}</td>
                                        <td>{{ $order->client_phone }}</td>
                                        <td>{{ ucfirst($order->vehicle_type) }}</td>
                                        <td>${{ number_format($order->profit, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $orders->links('pagination.custom') }}
                    </div>
                @else
                    <p class="text-muted">No orders yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Location Map Modal -->
<div class="modal fade" id="locationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="width: 650px; max-width: 100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalTitle">Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="locationMap" style="width: 100%; height: 650px;"></div>
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
let locationMap = null;
let locationMarker = null;

function showLocationMap(lat, lng, name) {
    const modal = new bootstrap.Modal(document.getElementById('locationModal'));
    document.getElementById('locationModalTitle').textContent = name + ' - Location';
    modal.show();
    
    setTimeout(() => {
        if (locationMap) {
            locationMap.remove();
        }
        
        // Use Lebanon as default if coordinates are missing
        const displayLat = lat || 33.8547;
        const displayLng = lng || 35.8623;
        
        locationMap = L.map('locationMap').setView([displayLat, displayLng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(locationMap);
        
        locationMarker = L.marker([displayLat, displayLng]).addTo(locationMap)
            .bindPopup('<b>' + name + '</b>').openPopup();
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
