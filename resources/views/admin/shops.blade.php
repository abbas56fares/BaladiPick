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
                @if($shops->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Shop Name</th>
                                    <th>Owner</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Verified</th>
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
                                        <td>{{ Str::limit($shop->address, 30) }}</td>
                                        <td>
                                            @if($shop->is_verified)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$shop->is_verified)
                                                <form action="{{ route('admin.shops.verify', $shop->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.shops.disable', $shop->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">Disable</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $shops->links() }}
                    </div>
                @else
                    <p class="text-muted">No shops registered yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
