@extends('layouts.app')

@section('title', 'Shop Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Shop Dashboard</h2>
        <p class="text-muted">Welcome, {{ $shop->shop_name }}</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Orders</h5>
                <h2>{{ $totalOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Delivered</h5>
                <h2>{{ $deliveredOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Pending</h5>
                <h2>{{ $pendingOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title">Cancelled</h5>
                <h2>{{ $cancelledOrders }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Earnings</h5>
                <h2>${{ number_format($totalEarnings, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Pending Orders (Awaiting Pickup)</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                        <i class="bi bi-camera"></i> Scan QR Code
                    </button>
                    <a href="{{ route('shop.orders.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Create New Order
                    </a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $pendingForPickup = $recentOrders->where('status', 'pending')->where('qr_verified', false);
                @endphp
                
                <!-- Search Bar -->
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="searchInputDashboard" class="form-control" placeholder="Search by Order ID, Client Name, or Delivery Driver...">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearchDashboard()">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    </div>
                </div>
                
                @if($pendingForPickup->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="pendingOrdersTable">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Client</th>
                                    <th>Vehicle</th>
                                    <th>Profit</th>
                                    <th>Delivery Driver</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingForPickup as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->client_name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($order->vehicle_type) }}</span>
                                        </td>
                                        <td>${{ number_format($order->profit, 2) }}</td>
                                        <td>{{ $order->delivery ? $order->delivery->name : 'N/A' }}</td>
                                        <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                        <td>
                                            <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center"><strong>No pending orders awaiting pickup.</strong></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code for Pickup Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qr-scanner" style="width: 100%;"></div>
                <div id="scanner-status" class="mt-3 text-muted text-center">
                    <p>Initializing camera...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.4/dist/html5-qrcode.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.4/dist/html5-qrcode.min.js"></script>
<script>
// Search functionality for dashboard
document.getElementById('searchInputDashboard').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('pendingOrdersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const orderId = row.cells[0].textContent.toLowerCase();
        const clientName = row.cells[1].textContent.toLowerCase();
        const deliveryName = row.cells[4].textContent.toLowerCase();
        
        if (orderId.includes(searchTerm) || clientName.includes(searchTerm) || deliveryName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

function clearSearchDashboard() {
    document.getElementById('searchInputDashboard').value = '';
    const table = document.getElementById('pendingOrdersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}

// QR Scanner
let html5QrcodeScanner = null;
const modalElement = document.getElementById('qrScannerModal');

modalElement.addEventListener('shown.bs.modal', function() {
    document.getElementById('scanner-status').innerHTML = '<p class="text-info">Starting camera...</p>';
    
    setTimeout(() => {
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5Qrcode("qr-scanner");
            
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                document.getElementById('scanner-status').innerHTML = '<p class="text-success">Camera ready. Point at QR code.</p>';
            }).catch(err => {
                document.getElementById('scanner-status').innerHTML = '<p class="text-danger">Camera access denied or unavailable. Please check permissions.</p>';
                console.error('Camera error:', err);
            });
        }
    }, 300);
});

modalElement.addEventListener('hide.bs.modal', function() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
        }).catch(err => {
            console.log('Stop error:', err);
            html5QrcodeScanner = null;
        });
    }
});

function onScanSuccess(decodedText, decodedResult) {
    // Extract order ID from QR code (format: ORDER-{id}-{timestamp})
    const match = decodedText.match(/ORDER-(\d+)-/);
    if (match) {
        const orderId = match[1];
        verifyPickup(orderId, decodedText);
    } else {
        showScanError('Invalid QR code format. Expected: ORDER-{id}-{timestamp}');
    }
}

function onScanFailure(error) {
    // Silently handle scan failures
}

function showScanError(message) {
    const statusEl = document.getElementById('scanner-status');
    statusEl.innerHTML = '<p class="text-danger">' + message + '</p>';
    setTimeout(() => {
        statusEl.innerHTML = '<p class="text-muted">Ready to scan...</p>';
    }, 3000);
}

function verifyPickup(orderId, qrCode) {
    // Disable scanner while processing
    if (html5QrcodeScanner) {
        html5QrcodeScanner.pause(true);
    }

    document.getElementById('scanner-status').innerHTML = '<p class="text-info">Verifying...</p>';

    fetch(`{{ route('shop.orders.verify-pickup-api') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order_id: orderId, qr_code: qrCode })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('qrScannerModal'));
            modal.hide();
            
            // Show success message
            alert('Order #' + orderId + ' pickup verified successfully!');
            location.reload();
        } else {
            showScanError(data.message || 'Failed to verify order');
            if (html5QrcodeScanner) {
                html5QrcodeScanner.resume();
            }
        }
    })
    .catch(err => {
        showScanError('Error: ' + err.message);
        if (html5QrcodeScanner) {
            html5QrcodeScanner.resume();
        }
    });
}
</script>
@endpush
