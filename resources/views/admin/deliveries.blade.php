@extends('layouts.app')

@section('title', 'Manage Deliveries')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Manage Delivery Drivers</h4>
                <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form action="{{ route('admin.deliveries') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="verified" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
                                <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                            <a href="{{ route('admin.deliveries') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
                        </div>
                    </div>
                </form>

                @if($deliveries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="sortable">ID</th>
                                    <th class="sortable">Name</th>
                                    <th class="sortable">Email</th>
                                    <th class="sortable">Phone</th>
                                    <th>ID Document</th>
                                    <th class="sortable">Verified</th>
                                    <th class="sortable">Registered</th>
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
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#documentModal" 
                                                    onclick="loadDocument('{{ asset('storage/' . $delivery->id_document_path) }}', '{{ $delivery->name }} - ID Document')">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($delivery->verified)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $delivery->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="btn btn-sm btn-primary mb-1" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(!$delivery->verified)
                                                <form action="{{ route('admin.deliveries.verify', $delivery->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success mb-1">Verify</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.deliveries.disable', $delivery->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger mb-1">Disable</button>
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
                    <p class="text-muted">No delivery drivers registered yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh script for manual refresh button -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                location.reload();
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