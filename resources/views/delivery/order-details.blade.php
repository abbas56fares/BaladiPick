@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Order #{{ $order->id }} Details</h4>
                <div class="d-flex gap-2">
                    @if($order->status === 'pending' && $order->shop_lat && $order->shop_lng)
                        {{-- Navigation to shop for pickup --}}
                        <button type="button" class="btn btn-secondary"
                            onclick="showDeliveryRouteTo({{ $order->shop_lat }}, {{ $order->shop_lng }}, '{{ $order->shop->shop_name }}')">
                            <i class="bi bi-geo-alt"></i> Navigate to Shop
                        </button>
                        <button type="button" class="btn btn-primary"
                            onclick="openMobileMaps({{ $order->shop_lat }}, {{ $order->shop_lng }}, '{{ $order->shop->shop_name }}')">
                            <i class="bi bi-compass"></i> Open Maps (Shop)
                        </button>
                        <button type="button" class="btn btn-outline-success"
                            onclick="openShareModal({{ $order->shop_lat }}, {{ $order->shop_lng }}, '{{ $order->shop->shop_name }}', {{ $order->id }})">
                            <i class="bi bi-whatsapp"></i> Share Shop Location
                        </button>
                    @elseif(($order->qr_verified || $order->status === 'in_transit') && $order->client_lat && $order->client_lng)
                        {{-- Navigation to client for delivery --}}
                        <button type="button" class="btn btn-secondary"
                            onclick="showDeliveryRouteTo({{ $order->client_lat }}, {{ $order->client_lng }}, '{{ $order->client_name }}')">
                            <i class="bi bi-geo-alt"></i> Navigate to Client
                        </button>
                        <button type="button" class="btn btn-primary"
                            onclick="openMobileMaps({{ $order->client_lat }}, {{ $order->client_lng }}, '{{ $order->client_name }}')">
                            <i class="bi bi-compass"></i> Open Maps (Client)
                        </button>
                        <button type="button" class="btn btn-outline-success"
                            onclick="openShareModal({{ $order->client_lat }}, {{ $order->client_lng }}, '{{ $order->client_name }}', {{ $order->id }})">
                            <i class="bi bi-whatsapp"></i> Share Client Location
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Shop Information</h5>
                        <p><strong>Shop Name:</strong> {{ $order->shop->shop_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->shop->phone }}</p>
                        <p><strong>Address:</strong> {{ $order->shop->address }}</p>
                        <p><strong>Pickup Location:</strong> {{ $order->shop_lat }}, {{ $order->shop_lng }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Client Information</h5>
                        <p><strong>Name:</strong> {{ $order->client_name }}</p>
                        <p><strong>Phone:</strong> {{ $order->client_phone }}</p>
                        <p><strong>Delivery Location:</strong> {{ $order->client_lat }}, {{ $order->client_lng }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h5>Order Information</h5>
                        <p><strong>Status:</strong> 
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'in_transit' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </p>
                        <p><strong>Vehicle Type:</strong> {{ ucfirst($order->vehicle_type) }}</p>
                        <p><strong>Your Profit:</strong> ${{ number_format($order->profit, 2) }}</p>
                        <p><strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($order->status === 'pending')
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h5>Pickup Verification</h5>
                            <p class="text-muted">The shop will verify pickup using this QR code.</p>
                            <div class="text-center">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($order->qr_code) !!}
                                <p class="mt-2"><small class="text-muted">{{ $order->qr_code }}</small></p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($order->status === 'in_transit')
                    <div class="card bg-light mb-3 border-primary">
                        <div class="card-body">
                            <h5>Delivery Verification</h5>
                            <p class="text-muted">Please ask the client for the OTP sent to their phone, then enter it below.</p>
                            
                            <form action="{{ route('delivery.orders.verify-otp', $order->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" class="form-control" name="otp" 
                                           placeholder="Enter 6-digit OTP from client" maxlength="6" required>
                                    <button type="submit" class="btn btn-success">Verify & Complete</button>
                                </div>
                                @error('otp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </form>
                        </div>
                    </div>
                @endif

                @if($order->status === 'delivered')
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Order completed successfully!
                        Delivered at: {{ $order->delivery_verified_at->format('M d, Y H:i') }}
                    </div>
                @endif

                @if($order->status === 'pending')
                    <form action="{{ route('delivery.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Cancel this accepted order? It will return to the pool and a cooldown will apply.')">
                            Cancel Order
                        </button>
                    </form>
                @endif

                <a href="{{ route('delivery.orders.my') }}" class="btn btn-secondary">Back to My Orders</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Verification Status</h5>
            </div>
            <div class="card-body">
                <p>
                    <strong>QR Verified (Pickup):</strong><br>
                    @if($order->qr_verified)
                        <span class="badge bg-success">Yes</span>
                        <br><small>{{ $order->qr_verified_at->format('M d, Y H:i') }}</small>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </p>
                <p>
                    <strong>OTP Verified (Delivery):</strong><br>
                    @if($order->delivery_verified)
                        <span class="badge bg-success">Yes</span>
                        <br><small>{{ $order->delivery_verified_at->format('M d, Y H:i') }}</small>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="card mt-3">
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
@endsection

<!-- Location Map Modal -->
<div class="modal fade" id="deliveryLocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="width: 650px; max-width: 100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="deliveryLocationModalTitle">Route</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="orderDeliveryMap" style="width: 100%; height: 650px;"></div>
            </div>
        </div>
    </div>
    <div class="px-3 pb-3 text-muted small" id="deliveryLocationStatus" style="display:none;"></div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let deliveryMap = null;
let youMarker = null;
let destMarker = null;
let routeLayer = null;

function showDeliveryRouteTo(targetLat, targetLng, clientName) {
    const statusEl = document.getElementById('deliveryLocationStatus');
    statusEl.style.display = 'none';
    statusEl.textContent = '';

    if (!navigator.geolocation) {
        statusEl.textContent = 'Geolocation is not available in your browser.';
        statusEl.style.display = 'block';
    }

    const modal = new bootstrap.Modal(document.getElementById('deliveryLocationModal'));
    document.getElementById('deliveryLocationModalTitle').textContent = 'Your Location → ' + clientName;
    modal.show();

    navigator.geolocation.getCurrentPosition(function(pos) {
        const curLat = pos.coords.latitude;
        const curLng = pos.coords.longitude;

        setTimeout(() => {
            if (deliveryMap) {
                deliveryMap.remove();
            }

            const bounds = L.latLngBounds([
                [curLat, curLng],
                [targetLat, targetLng]
            ]);

            deliveryMap = L.map('orderDeliveryMap');

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(deliveryMap);

            youMarker = L.marker([curLat, curLng], { title: 'Your Location' }).addTo(deliveryMap)
                .bindPopup('<b>Your Location</b>').openPopup();

            destMarker = L.marker([targetLat, targetLng], { title: 'Destination: ' + clientName }).addTo(deliveryMap)
                .bindPopup('<b>Destination</b><br>' + clientName);

            const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${curLng},${curLat};${targetLng},${targetLat}?overview=full&geometries=geojson`;

            fetch(osrmUrl)
                .then(res => res.json())
                .then(data => {
                    if (data && data.routes && data.routes.length > 0) {
                        const geometry = data.routes[0].geometry;
                        const feature = { type: 'Feature', properties: {}, geometry };
                        routeLayer = L.geoJSON(feature, { style: { color: '#0d6efd', weight: 4, opacity: 0.9 } }).addTo(deliveryMap);
                        deliveryMap.fitBounds(routeLayer.getBounds(), { padding: [30, 30] });
                    } else {
                        // Fallback: straight line
                        routeLayer = L.polyline([[curLat, curLng], [targetLat, targetLng]], { color: '#0d6efd', weight: 4, opacity: 0.85 }).addTo(deliveryMap);
                        deliveryMap.fitBounds(bounds, { padding: [30, 30] });
                    }
                })
                .catch(() => {
                    routeLayer = L.polyline([[curLat, curLng], [targetLat, targetLng]], { color: '#0d6efd', weight: 4, opacity: 0.85 }).addTo(deliveryMap);
                    deliveryMap.fitBounds(bounds, { padding: [30, 30] });
                });
        }, 300);
    }, function(err) {
        statusEl.textContent = 'Could not get your location: ' + err.message;
        statusEl.style.display = 'block';
    }, { enableHighAccuracy: true, timeout: 10000 });
}
</script>
<script>
// WhatsApp share modal and logic
function openMobileMaps(lat, lng, clientName) {
    const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
    const isAndroid = /Android/i.test(navigator.userAgent);

    // Prefer native apps; fall back to web Google Maps
    const iosUrl = `http://maps.apple.com/?daddr=${lat},${lng}&dirflg=d`;
    const androidUrl = `google.navigation:q=${lat},${lng}`;
    const webUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}&travelmode=driving`;

    let target = webUrl;
    if (isIOS) {
        target = iosUrl;
    } else if (isAndroid) {
        target = androidUrl;
    }

    // Attempt deep link; if it doesn't resolve, fall back to web
    const start = Date.now();
    window.location.href = target;
    setTimeout(function () {
        if (Date.now() - start < 1600) {
            window.location.href = webUrl;
        }
    }, 1500);
}

function openShareModal(lat, lng, clientName, orderId) {
    const modalEl = document.getElementById('whatsAppShareModal');
    if (!modalEl) return;
    // Populate hidden fields
    document.getElementById('wa-lat').value = lat;
    document.getElementById('wa-lng').value = lng;
    document.getElementById('wa-client').value = clientName;
    document.getElementById('wa-order').value = orderId;
    document.getElementById('wa-title').textContent = 'Share Destination for Order #' + orderId;
    // Prefill phone from server if present
    const phoneInput = document.getElementById('wa-phone');
    phoneInput.value = '{{ auth()->user()->phone ?? '' }}';
    new bootstrap.Modal(modalEl).show();
}

function submitWhatsAppShare() {
    const phoneRaw = document.getElementById('wa-phone').value || '';
    const lat = document.getElementById('wa-lat').value;
    const lng = document.getElementById('wa-lng').value;
    const clientName = document.getElementById('wa-client').value;
    const orderId = document.getElementById('wa-order').value;

    // Sanitize phone: digits only for wa.me format; user must include country code
    const phone = (phoneRaw.match(/\d+/g) || []).join('');
    if (!phone || phone.length < 8) {
        alert('Please enter a valid phone number in international format (e.g., 9627XXXXXXXX).');
        return false;
    }

    const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
    const text = encodeURIComponent(
        `Order #${orderId} destination for ${clientName}\nMaps: ${mapsUrl}`
    );
    const waUrl = `https://wa.me/${phone}?text=${text}`;
    window.open(waUrl, '_blank');
    return true;
}
</script>
@endpush

<!-- WhatsApp Share Modal -->
<div class="modal fade" id="whatsAppShareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wa-title">Share Destination</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="wa-phone" class="form-label">Delivery Phone (WhatsApp)</label>
                    <input type="text" class="form-control" id="wa-phone" placeholder="Enter phone in international format (digits only)">
                    <div class="form-text">Example: 9627XXXXXXXX (no leading zeros or +)</div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-success" onclick="submitWhatsAppShare()">
                        <i class="bi bi-whatsapp"></i> Share
                    </button>
                </div>
                <input type="hidden" id="wa-lat">
                <input type="hidden" id="wa-lng">
                <input type="hidden" id="wa-client">
                <input type="hidden" id="wa-order">
            </div>
        </div>
    </div>
</div>
