@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>My Orders</h4>
                <div class="d-flex gap-2">
                    <input type="text" id="qrCodeInput" class="form-control" style="width: 250px;" 
                           placeholder="Paste QR code to find order" 
                           onkeypress="if(event.key==='Enter') findOrderByQR()">
                    <button class="btn btn-info" onclick="findOrderByQR()">
                        <i class="bi bi-search"></i> Find
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by Client Name or Phone...">
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
                                    <th class="sortable">Client</th>
                                    <th class="sortable">Phone</th>
                                    <th class="sortable">Vehicle</th>
                                    <th class="sortable">Profit</th>
                                    <th class="sortable">Status</th>
                                    <th class="sortable">Delivery</th>
                                    <th class="sortable">Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
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
                        {{ $orders->links('pagination.custom') }}
                    </div>
                @else
                    <p class="text-muted">No orders yet. <a href="{{ route('shop.orders.create') }}">Create your first order</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('ordersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const clientName = row.cells[0].textContent.toLowerCase();
        const phone = row.cells[1].textContent.toLowerCase();
        
        if (clientName.includes(searchTerm) || phone.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
    });
}

window.clearSearch = function() {
    document.getElementById('searchInput').value = '';
    const table = document.getElementById('ordersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}

// Find order by QR code
window.findOrderByQR = function() {
    const input = document.getElementById('qrCodeInput');
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
