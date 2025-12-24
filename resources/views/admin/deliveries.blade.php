@extends('layouts.app')

@section('title', 'Manage Deliveries')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Manage Delivery Drivers</h4>
            </div>
            <div class="card-body">
                @if($deliveries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Verified</th>
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
                                            @if($delivery->verified)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $delivery->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if(!$delivery->verified)
                                                <form action="{{ route('admin.deliveries.verify', $delivery->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.deliveries.disable', $delivery->id) }}" method="POST" class="d-inline">
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
                        {{ $deliveries->links() }}
                    </div>
                @else
                    <p class="text-muted">No delivery drivers registered yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
