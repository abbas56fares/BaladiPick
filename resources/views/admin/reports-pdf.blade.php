<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orders Report - BaladiPick</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f8f9fa;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #0d6efd;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #0d6efd;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-delivered {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #842029;
        }
        .status-available {
            background-color: #cfe2ff;
            color: #084298;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #664d03;
        }
        .status-in_transit {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>BaladiPick - Orders Report</h1>
    <p style="text-align: center; color: #666; margin-bottom: 30px;">Generated on {{ date('F d, Y - H:i:s') }}</p>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Orders</div>
                <div class="summary-value">{{ $totalOrders }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Delivered</div>
                <div class="summary-value" style="color: #198754;">{{ $deliveredOrders }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Cancelled</div>
                <div class="summary-value" style="color: #dc3545;">{{ $cancelledOrders }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Revenue</div>
                <div class="summary-value" style="color: #198754;">${{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Shop</th>
                <th>Client Name</th>
                <th>Client Phone</th>
                <th>Delivery Driver</th>
                <th>Vehicle</th>
                <th>Profit</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->shop->shop_name }}</td>
                    <td>{{ $order->client_name }}</td>
                    <td>{{ $order->client_phone }}</td>
                    <td>{{ $order->delivery ? $order->delivery->name : 'N/A' }}</td>
                    <td>{{ ucfirst($order->vehicle_type) }}</td>
                    <td>${{ number_format($order->profit, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $order->status }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>BaladiPick Delivery Management System - {{ date('Y') }}</p>
    </div>
</body>
</html>
