@extends('layouts.app')

@section('title', 'Manage Orders')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>All Orders</h4>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="sortable">ID</th>
                                    <th class="sortable">Shop</th>
                                    <th class="sortable">Client</th>
                                    <th class="sortable">Delivery</th>
                                    <th class="sortable">Vehicle</th>
                                    <th class="sortable">Profit</th>
                                    <th class="sortable">Status</th>
                                    <th class="sortable">Date</th>
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
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                            @if(!in_array($order->status, ['delivered', 'cancelled']))
                                                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Cancel this order?')">
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
                    <p class="text-muted">No orders yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh page every 10 seconds to show real-time updates
setInterval(function() {
    location.reload();
}, 10000);
</script>
@endpush
