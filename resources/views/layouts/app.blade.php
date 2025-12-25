<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - BaladiPick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
                                <a class="nav-link" href="{{ route('delivery.map') }}">Map</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('delivery.orders.my') }}">My Orders</a>
                            </li>
                        @elseif(auth()->user()->isAdmin())
                            @php
                                $unverifiedShopsCount = \App\Models\User::where('role', 'shop')
                                    ->where('verified', false)
                                    ->count();
                                $unverifiedDeliveriesCount = \App\Models\User::where('role', 'delivery')
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
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('admin.deliveries') }}">
                                    Deliveries
                                    @if($unverifiedDeliveriesCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unverifiedDeliveriesCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.orders') }}">Orders</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
