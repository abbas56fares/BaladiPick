<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title') - BaladiPick</title>
    @if(app()->environment('production'))
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @else
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">BaladiPick</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if(auth()->user()->isShop())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop.orders') }}">Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop.orders.map') }}">Map</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop.orders.create') }}">Create Order</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop.profile') }}">Profile</a>
                            </li>
                        @elseif(auth()->user()->isDelivery())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('delivery.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('delivery.map') }}">Available Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('delivery.accepted.orders.map') }}">My Accepted Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('delivery.orders.my') }}">Orders List</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('delivery.settings') }}">Settings</a>
                            </li>
                        @elseif(auth()->user()->isAdmin())
                            @php
                                $unverifiedShopsCount = \App\Models\User::where('role', 'shop')
                                    ->where('verified', false)
                                    ->count();
                                $unverifiedDeliveriesCount = \App\Models\User::where('role', 'delivery')
                                    ->where('verified', false)
                                    ->count();
                                $unverifiedBikeDeliveriesCount = \App\Models\User::where('role', 'delivery')
                                    ->where('vehicle_type', 'bike')
                                    ->where('verified', false)
                                    ->count();
                                $unverifiedCarDeliveriesCount = \App\Models\User::where('role', 'delivery')
                                    ->where('vehicle_type', 'car')
                                    ->where('verified', false)
                                    ->count();
                                $unverifiedPickupDeliveriesCount = \App\Models\User::where('role', 'delivery')
                                    ->where('vehicle_type', 'pickup')
                                    ->where('verified', false)
                                    ->count();
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('admin.shops') }}">
                                    Shops
                                    @if($unverifiedShopsCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unverifiedShopsCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle position-relative" href="#" id="deliveriesDropdown" role="button" data-bs-toggle="dropdown">
                                    Deliveries
                                    @if($unverifiedDeliveriesCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unverifiedDeliveriesCount }}
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="deliveriesDropdown">
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('admin.deliveries') }}">
                                            <span><i class="bi bi-people-fill"></i> All Drivers</span>
                                            @if($unverifiedDeliveriesCount > 0)
                                                <span class="badge bg-danger ms-2">{{ $unverifiedDeliveriesCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('admin.deliveries.bikes') }}">
                                            <span><i class="bi bi-bicycle"></i> Bike Drivers</span>
                                            @if($unverifiedBikeDeliveriesCount > 0)
                                                <span class="badge bg-danger ms-2">{{ $unverifiedBikeDeliveriesCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('admin.deliveries.cars') }}">
                                            <span><i class="bi bi-car-front"></i> Car Drivers</span>
                                            @if($unverifiedCarDeliveriesCount > 0)
                                                <span class="badge bg-danger ms-2">{{ $unverifiedCarDeliveriesCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('admin.deliveries.pickups') }}">
                                            <span><i class="bi bi-truck"></i> Pickup Drivers</span>
                                            @if($unverifiedPickupDeliveriesCount > 0)
                                                <span class="badge bg-danger ms-2">{{ $unverifiedPickupDeliveriesCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.orders') }}">Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.settings') }}">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    @if(app()->environment('production'))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @else
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @endif
    
    <!-- Laravel Echo & Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        window.Pusher = Pusher;
        Pusher.logToConsole = false;
        
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_CLUSTER", "mt") }}',
            forceTLS: true,
            encrypted: true,
        });

        // Global user interaction detection
        let isUserActive = false;
        document.addEventListener('mousedown', () => { isUserActive = true; });
        document.addEventListener('mouseup', () => { isUserActive = false; });
        document.addEventListener('keydown', () => { isUserActive = true; });
        document.addEventListener('keyup', () => { isUserActive = false; });

        function canReload() {
            // Don't reload if modal is open or user is actively interacting
            return !document.querySelector('.modal.show') && !isUserActive;
        }

        // Listen for order updates
        const ordersChannel = pusher.subscribe('orders');
        
        ordersChannel.bind('order.accepted', function(data) {
            console.log('Order accepted:', data);
            // Smart reload: only reload if safe and on relevant page
            if (canReload()) {
                const currentUrl = window.location.href;
                // For shop: reload if on dashboard, orders, or orders-map for this shop
                if (currentUrl.includes('/shop/') && !currentUrl.includes('/orders/' + data.id)) {
                    setTimeout(() => location.reload(), 300);
                }
                // For delivery: always reload to show new accepted order
                else if (currentUrl.includes('/delivery/')) {
                    setTimeout(() => location.reload(), 300);
                }
            }
        });

        ordersChannel.bind('order.cancelled', function(data) {
            console.log('Order cancelled:', data);
            // Smart reload
            if (canReload()) {
                const currentUrl = window.location.href;
                // Reload on shop/delivery pages but not on specific order detail page
                if (!currentUrl.includes('/orders/' + data.id)) {
                    setTimeout(() => location.reload(), 300);
                }
            }
        });

        ordersChannel.bind('order.verified', function(data) {
            console.log('Order verified:', data);
            // Smart reload
            if (canReload()) {
                const currentUrl = window.location.href;
                // Reload on shop/delivery pages but not on specific order detail page
                if (!currentUrl.includes('/orders/' + data.id)) {
                    setTimeout(() => location.reload(), 300);
                }
            }
        });

        // Make window functions available globally
        window.pusher = pusher;
        window.ordersChannel = ordersChannel;
        window.canReload = canReload;
    </script>

    <!-- Table Sorting and Filtering Utility -->
    <script>
        // Initialize sortable tables on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeAllSortableTables();
        });

        function initializeAllSortableTables() {
            // Find all tables with sortable headers
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                const headers = table.querySelectorAll('th.sortable, th[data-sortable="true"]');
                if (headers.length > 0) {
                    attachTableSorting(table);
                }
            });
        }

        function attachTableSorting(table) {
            const headers = table.querySelectorAll('th.sortable, th[data-sortable="true"]');
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            headers.forEach((header) => {
                header.style.cursor = 'pointer';
                header.style.userSelect = 'none';
                header.addEventListener('click', function() {
                    // Get the actual column index from all th elements
                    const allHeaders = table.querySelectorAll('th');
                    let columnIndex = -1;
                    for (let i = 0; i < allHeaders.length; i++) {
                        if (allHeaders[i] === header) {
                            columnIndex = i;
                            break;
                        }
                    }
                    if (columnIndex >= 0) {
                        sortTableByColumn(table, columnIndex);
                    }
                });
            });
        }

        function sortTableByColumn(table, columnIndex) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const header = table.querySelectorAll('th')[columnIndex];
            
            // Determine sort direction
            let isAsc = true;
            if (header.classList.contains('sort-asc')) {
                isAsc = false;
                header.classList.remove('sort-asc');
                header.classList.add('sort-desc');
            } else {
                header.classList.remove('sort-desc');
                header.classList.add('sort-asc');
            }
            
            // Clear other headers
            table.querySelectorAll('th').forEach(h => {
                if (h !== header) {
                    h.classList.remove('sort-asc', 'sort-desc');
                }
            });
            
            // Sort rows
            rows.sort((a, b) => {
                let aVal = a.cells[columnIndex].textContent.trim();
                let bVal = b.cells[columnIndex].textContent.trim();
                
                // Try to parse as number if possible
                const aNum = parseFloat(aVal.replace(/[$#,%]/g, ''));
                const bNum = parseFloat(bVal.replace(/[$#,%]/g, ''));
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return isAsc ? aNum - bNum : bNum - aNum;
                }
                
                // String comparison
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
                return isAsc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });
            
            // Re-render sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        // Global filter function for tables
        function filterTableByColumn(inputSelector, tableSelector, columnIndex) {
            const input = document.querySelector(inputSelector);
            const table = document.querySelector(tableSelector);
            if (!input || !table) return;

            input.addEventListener('keyup', function() {
                const filterValue = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const cellText = row.cells[columnIndex].textContent.toLowerCase();
                    row.style.display = cellText.includes(filterValue) ? '' : 'none';
                });
            });
        }
    </script>

    <!-- Table Styling -->
    <style>
        th.sortable, th[data-sortable="true"] {
            cursor: pointer;
            user-select: none;
            position: relative;
        }
        th.sortable:hover, th[data-sortable="true"]:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        th.sort-asc::after {
            content: ' ↑';
            color: #0d6efd;
            font-weight: bold;
        }
        th.sort-desc::after {
            content: ' ↓';
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
    
    @stack('scripts')

</body>
</html>
