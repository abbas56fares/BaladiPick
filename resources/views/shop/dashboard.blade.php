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
                    <input type="text" id="qrCodeInputDashboard" class="form-control" style="width: 250px;" 
                           placeholder="Paste QR code to find order" 
                           onkeypress="if(event.key==='Enter') findOrderByQRDashboard()">
                    <button class="btn btn-info btn-sm" onclick="findOrderByQRDashboard()">
                        <i class="bi bi-search"></i> Find
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
                        <input type="text" id="searchInputDashboard" class="form-control" placeholder="Search by Client Name or Delivery Driver...">
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

@push('scripts')
<script>
// Search functionality for dashboard
const searchInput = document.getElementById('searchInputDashboard');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('pendingOrdersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const clientName = row.cells[0].textContent.toLowerCase();
        const deliveryName = row.cells[3].textContent.toLowerCase();
        
        if (clientName.includes(searchTerm) || deliveryName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
    });
}

window.clearSearchDashboard = function() {
    document.getElementById('searchInputDashboard').value = '';
    const table = document.getElementById('pendingOrdersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}

// Find order by QR code
window.findOrderByQRDashboard = function() {
    const input = document.getElementById('qrCodeInputDashboard');
    const qrCode = input.value.trim();
    
    if (!qrCode) {
        alert('Please enter a QR code');
        return;
    }
    
    // Extract order ID from QR code (format: ORDER-{id}-{timestamp})
    const match = qrCode.match(/ORDER-(\d+)-/);
    if (match) {
        const orderId = match[1];
        window.location.href = '/shop/orders/' + orderId;
    } else {
        alert('Invalid QR code format. Expected: ORDER-{id}-{timestamp}');
    }
}
</script>
@endpush
