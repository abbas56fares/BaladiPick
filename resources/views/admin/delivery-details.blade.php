@extends('layouts.app')

@section('title', 'Delivery Driver Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.deliveries') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Deliveries</a>
            <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Delivery Driver Information</h4>
                <div>
                    @if($delivery->verified)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning">Unverified</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Personal Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">User ID:</th>
                                <td>{{ $delivery->id }}</td>
                            </tr>
                            <tr>
                                <th>Full Name:</th>
                                <td><strong>{{ $delivery->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $delivery->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $delivery->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td><span class="badge bg-info">{{ ucfirst($delivery->role) }}</span></td>
                            </tr>
                            <tr>
                                <th>Registered:</th>
                                <td>{{ $delivery->created_at->format('F d, Y - H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Verification & Status</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Account Status:</th>
                                <td>
                                    @if($delivery->verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Pending Verification</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>ID Document:</th>
                                <td>
                                    @if($delivery->id_document_path)
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                            onclick="loadDocument('{{ asset('storage/' . $delivery->id_document_path) }}', '{{ $delivery->name }} - ID Document')">
                                            <i class="bi bi-file-image"></i> View Document
                                        </button>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Email Verified:</th>
                                <td>
                                    @if($delivery->email_verified_at)
                                        <span class="badge bg-success">Yes</span>
                                        <small class="text-muted d-block">{{ $delivery->email_verified_at->format('M d, Y') }}</small>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Last Login:</th>
                                <td>{{ $delivery->updated_at->diffForHumans() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    @if(!$delivery->verified)
                        <form action="{{ route('admin.deliveries.verify', $delivery->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Verify Driver
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.deliveries.disable', $delivery->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle"></i> Disable Driver
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="card">
            <div class="card-header">
                <h4>Delivery History</h4>
            </div>
            <div class="card-body">
                @if($delivery->deliveryOrders->count() > 0)
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>Total Orders</h6>
                                    <h3>{{ $delivery->deliveryOrders->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>Delivered</h6>
                                    <h3>{{ $delivery->deliveryOrders->where('status', 'delivered')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6>In Progress</h6>
                                    <h3>{{ $delivery->deliveryOrders->whereIn('status', ['pending', 'in_transit'])->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>Total Earnings</h6>
                                    <h3>${{ number_format($delivery->deliveryOrders->where('status', 'delivered')->sum('profit'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Shop</th>
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
                                        <td>{{ $order->shop->shop_name }}</td>
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
                    <p class="text-muted">No delivery orders yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Manual refresh button -->
<script>
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

@endsection
