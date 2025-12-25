@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>System Reports</h2>
            <div>
                <a href="{{ route('admin.reports.export') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Export CSV
                </a>
                <a href="{{ route('admin.reports.export.pdf') }}" class="btn btn-danger ms-2">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Orders</h6>
                <h3>{{ $totalOrders }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Delivered</h6>
                <h3 class="text-success">{{ $deliveredOrders }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Cancelled</h6>
                <h3 class="text-danger">{{ $cancelledOrders }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Revenue</h6>
                <h3 class="text-primary">${{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Orders by Status</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordersByStatus as $status)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $status->status)) }}</td>
                                <td>{{ $status->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Orders by Vehicle Type</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordersByVehicle as $vehicle)
                            <tr>
                                <td>{{ ucfirst($vehicle->vehicle_type) }}</td>
                                <td>{{ $vehicle->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 10 Shops (by Delivered Orders)</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th>Delivered Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topShops as $shop)
                            <tr>
                                <td>{{ $shop->shop_name }}</td>
                                <td>{{ $shop->orders_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 10 Delivery Drivers (by Delivered Orders)</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Driver Name</th>
                            <th>Delivered Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topDeliveries as $delivery)
                            <tr>
                                <td>{{ $delivery->name }}</td>
                                <td>{{ $delivery->delivery_orders_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
