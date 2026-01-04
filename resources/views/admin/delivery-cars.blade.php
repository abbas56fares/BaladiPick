@extends('layouts.app')

@section('title', 'Car Delivery Drivers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <h4 class="mb-0"><i class="bi bi-car-front"></i> Car Delivery Drivers</h4>
            </div>
            <div class="card-body">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-6 col-md-3 mb-3 mb-md-0">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Total Drivers</h6>
                                <h3 style="font-size: 1.5rem;">{{ $totalCarDrivers }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3 mb-md-0">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Verified</h6>
                                <h3 style="font-size: 1.5rem;">{{ $verifiedCarDrivers }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3 mb-md-0">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Total Orders</h6>
                                <h3 style="font-size: 1.5rem;">{{ $totalCarOrders }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Delivered</h6>
                                <h3 style="font-size: 1.5rem;">{{ $deliveredCarOrders }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Form -->
                <form action="{{ route('admin.deliveries.cars') }}" method="GET" class="mb-4">
                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-12 col-sm-3">
                            <select name="verified" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
                                <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-3">
                            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> Search</button>
                        </div>
                    </div>
                </form>

                @if($deliveries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Document</th>
                                    <th>Orders</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveries as $delivery)
                                    <tr>
                                        <td>{{ $delivery->id }}</td>
                                        <td>{{ $delivery->name }}</td>
                                        <td>{{ $delivery->email }}</td>
                                        <td>{{ $delivery->phone }}</td>
                                        <td>
                                            @if($delivery->id_document_path)
                                                <a href="{{ \App\Helpers\DocumentHelper::getDocumentUrl($delivery->id_document_path) }}" target="_blank" class="btn btn-xs btn-info"><i class="bi bi-eye"></i></a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $delivery->deliveryOrders()->count() }}</td>
                                        <td>
                                            @if($delivery->verified)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $delivery->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                            @if(!$delivery->verified)
                                                <form action="{{ route('admin.deliveries.verify', $delivery->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $deliveries->links('pagination.custom') }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No car delivery drivers found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
