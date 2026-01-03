@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <h2 class="mb-0">System Reports</h2>
            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                <a href="{{ route('admin.reports.export') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> <span class="d-none d-sm-inline">Export</span> CSV
                </a>
                <a href="{{ route('admin.reports.export.pdf') }}" class="btn btn-danger">
                    <i class="bi bi-file-pdf"></i> <span class="d-none d-sm-inline">Export</span> PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card">
            <div class="card-body p-3">
                <h6 class="text-muted mb-2" style="font-size: 0.85rem;">Total Orders</h6>
                <h3 class="mb-0" style="font-size: 1.5rem;">{{ $totalOrders }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="card">
            <div class="card-body p-3">
                <h6 class="text-muted mb-2" style="font-size: 0.85rem;">Delivered</h6>
                <h3 class="text-success mb-0" style="font-size: 1.5rem;">{{ $deliveredOrders }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <h6 class="text-muted mb-2" style="font-size: 0.85rem;">Cancelled</h6>
                <h3 class="text-danger mb-0" style="font-size: 1.5rem;">{{ $cancelledOrders }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <h6 class="text-muted mb-2" style="font-size: 0.85rem;">Total Revenue</h6>
                <h3 class="text-primary mb-0" style="font-size: 1.5rem;">${{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Orders by Status</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3 fw-bold">Status</th>
                                <th class="pe-3 fw-bold">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordersByStatus as $status)
                                <tr>
                                    <td class="ps-3">{{ ucfirst(str_replace('_', ' ', $status->status)) }}</td>
                                    <td class="pe-3">{{ $status->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Orders by Vehicle Type</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3 fw-bold">Vehicle</th>
                                <th class="pe-3 fw-bold">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordersByVehicle as $vehicle)
                                <tr>
                                    <td class="ps-3">{{ ucfirst($vehicle->vehicle_type) }}</td>
                                    <td class="pe-3">{{ $vehicle->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 10 Shops</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3 fw-bold">Shop Name</th>
                                <th class="pe-3 fw-bold">Delivered Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topShops as $shop)
                                <tr>
                                    <td class="ps-3">{{ $shop->shop_name }}</td>
                                    <td class="pe-3">{{ $shop->orders_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 10 Delivery Drivers</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3 fw-bold">Driver Name</th>
                                <th class="pe-3 fw-bold">Delivered Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topDeliveries as $delivery)
                                <tr>
                                    <td class="ps-3">{{ $delivery->name }}</td>
                                    <td class="pe-3">{{ $delivery->delivery_orders_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
