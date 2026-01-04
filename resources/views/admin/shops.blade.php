@extends('layouts.app')

@section('title', 'Manage Shops')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Manage Shops</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form action="{{ route('admin.shops') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, address..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="verified" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
                                <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                            <a href="{{ route('admin.shops') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
                        </div>
                    </div>
                </form>

                @if($shops->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="sortable">ID</th>
                                    <th class="sortable">Shop Name</th>
                                    <th class="sortable">Owner</th>
                                    <th class="sortable">Email</th>
                                    <th class="sortable">Phone</th>
                                    <th>ID Document</th>
                                    <th class="sortable">Address</th>
                                    <th class="sortable">Verified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shops as $shop)
                                    <tr>
                                        <td>{{ $shop->id }}</td>
                                        <td>{{ $shop->shop_name }}</td>
                                        <td>{{ $shop->user->name }}</td>
                                        <td>{{ $shop->user->email }}</td>
                                        <td>{{ $shop->phone }}</td>
                                        <td>
                                            @if($shop->user->id_document_path)
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                                    onclick="loadDocument('{{ asset('storage/' . $shop->user->id_document_path) }}', '{{ $shop->user->name }} - ID Document')">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($shop->address, 30) }}</td>
                                        <td>
                                            @if($shop->is_verified)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.shops.show', $shop->id) }}" class="btn btn-sm btn-primary mb-1" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($shop->latitude && $shop->longitude)
                                                <button type="button" class="btn btn-sm btn-secondary mb-1" title="See Location" 
                                                    onclick="showLocationMap({{ $shop->latitude }}, {{ $shop->longitude }}, '{{ $shop->shop_name }}')">
                                                    <i class="bi bi-geo-alt"></i>
                                                </button>
                                            @endif
                                            @if(!$shop->is_verified)
                                                <form action="{{ route('admin.shops.verify', $shop->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success mb-1">Verify</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.shops.disable', $shop->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger mb-1">Disable</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $shops->links('pagination.custom') }}
                    </div>
                @else
                    <p class="text-muted">No shops registered yet.</p>
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
document.getElementById('refreshBtn').addEventListener('click', function() {
    location.reload();
});

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

// Auto-refresh page every 15 seconds to show real-time updates
setInterval(function() {
    location.reload();
}, 15000);

// Document Modal
function loadDocument(url, title) {
    document.getElementById('documentModalLabel').innerText = title;
    document.getElementById('documentImage').src = url;
}
</script>

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel">Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="documentImage" src="" alt="Document" style="max-width: 100%; max-height: 600px;">
            </div>
        </div>
    </div>
</div>
@endpush
