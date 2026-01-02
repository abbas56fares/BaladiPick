@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>My Orders</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                        <i class="bi bi-camera"></i> Scan QR
                    </button>
                    <a href="{{ route('shop.orders.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Order
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by Order ID, Client Name, or Phone...">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    </div>
                </div>

                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="ordersTable">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Client</th>
                                    <th>Phone</th>
                                    <th>Vehicle</th>
                                    <th>Profit</th>
                                    <th>Status</th>
                                    <th>Delivery</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->client_name }}</td>
                                        <td>{{ $order->client_phone }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($order->vehicle_type) }}</span>
                                        </td>
                                        <td>${{ number_format($order->profit, 2) }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'available' => 'info',
                                                    'pending' => 'warning',
                                                    'in_transit' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$order->status] }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->delivery)
                                                {{ $order->delivery->name }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('shop.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                            @if(!in_array($order->status, ['delivered', 'cancelled', 'in_transit']))
                                                <a href="{{ route('shop.orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            @endif
                                            @if(!in_array($order->status, ['delivered', 'cancelled']))
                                                <form action="{{ route('shop.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to cancel this order?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                @else
                    <p class="text-muted">No orders yet. <a href="{{ route('shop.orders.create') }}">Create your first order</a></p>
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
                <div id="qr-scanner-orders" style="width: 100%;"></div>
                <div id="scanner-status-orders" class="mt-3 text-muted text-center">
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
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('ordersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const orderId = row.cells[0].textContent.toLowerCase();
        const clientName = row.cells[1].textContent.toLowerCase();
        const phone = row.cells[2].textContent.toLowerCase();
        
        if (orderId.includes(searchTerm) || clientName.includes(searchTerm) || phone.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

function clearSearch() {
    document.getElementById('searchInput').value = '';
    const table = document.getElementById('ordersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}

// QR Scanner
let html5QrcodeScannerOrders = null;
const modalEl = document.getElementById('qrScannerModal');

modalEl.addEventListener('shown.bs.modal', function() {
    document.getElementById('scanner-status-orders').innerHTML = '<p class="text-info">Starting camera...</p>';
    
    setTimeout(() => {
        if (!html5QrcodeScannerOrders) {
            html5QrcodeScannerOrders = new Html5Qrcode("qr-scanner-orders");
            
            html5QrcodeScannerOrders.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccessOrders,
                onScanFailureOrders
            ).then(() => {
                document.getElementById('scanner-status-orders').innerHTML = '<p class="text-success">Camera ready. Point at QR code.</p>';
            }).catch(err => {
                document.getElementById('scanner-status-orders').innerHTML = '<p class="text-danger">Camera access denied or unavailable. Please check permissions.</p>';
                console.error('Camera error:', err);
            });
        }
    }, 300);
});

modalEl.addEventListener('hide.bs.modal', function() {
    if (html5QrcodeScannerOrders) {
        html5QrcodeScannerOrders.stop().then(() => {
            html5QrcodeScannerOrders.clear();
            html5QrcodeScannerOrders = null;
        }).catch(err => {
            console.log('Stop error:', err);
            html5QrcodeScannerOrders = null;
        });
    }
});

function onScanSuccessOrders(decodedText, decodedResult) {
    const match = decodedText.match(/ORDER-(\d+)-/);
    if (match) {
        const orderId = match[1];
        verifyPickupOrders(orderId, decodedText);
    } else {
        showScanErrorOrders('Invalid QR code format');
    }
}

function onScanFailureOrders(error) {
    // Silently handle scan failures
}

function showScanErrorOrders(message) {
    const statusEl = document.getElementById('scanner-status-orders');
    statusEl.innerHTML = '<p class="text-danger">' + message + '</p>';
    setTimeout(() => {
        statusEl.innerHTML = '<p class="text-muted">Ready to scan...</p>';
    }, 3000);
}

function verifyPickupOrders(orderId, qrCode) {
    if (html5QrcodeScannerOrders) {
        html5QrcodeScannerOrders.pause(true);
    }

    document.getElementById('scanner-status-orders').innerHTML = '<p class="text-info">Verifying...</p>';

    fetch('{{ route('shop.orders.verify-pickup-api') }}', {
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
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            alert('Order #' + orderId + ' pickup verified successfully!');
            location.reload();
        } else {
            showScanErrorOrders(data.message || 'Failed to verify');
            if (html5QrcodeScannerOrders) {
                html5QrcodeScannerOrders.resume();
            }
        }
    })
    .catch(err => {
        showScanErrorOrders('Error: ' + err.message);
        if (html5QrcodeScannerOrders) {
            html5QrcodeScannerOrders.resume();
        }
    });
}

// Auto-refresh page every 7 seconds to show real-time updates (less frequent due to pagination)
setInterval(function() {
    // Only reload if no modal is open
    if (!document.querySelector('.modal.show')) {
        location.reload();
    }
}, 7000);
</script>
@endpush
