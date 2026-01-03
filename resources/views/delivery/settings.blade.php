@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-gear"></i> Profile Settings
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validation Error!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('delivery.settings.update') }}" method="POST">
                        @csrf

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $user->name) }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input 
                                type="tel" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" 
                                name="phone" 
                                value="{{ old('phone', $user->phone) }}"
                                required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Vehicle Type -->
                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <select 
                                class="form-select @error('vehicle_type') is-invalid @enderror" 
                                id="vehicle_type" 
                                name="vehicle_type"
                                required>
                                <option value="">-- Select Vehicle Type --</option>
                                <option value="bike" @selected(old('vehicle_type', $user->vehicle_type) === 'bike')>
                                    🏍️ Bike
                                </option>
                                <option value="car" @selected(old('vehicle_type', $user->vehicle_type) === 'car')>
                                    🚗 Car
                                </option>
                                <option value="pickup" @selected(old('vehicle_type', $user->vehicle_type) === 'pickup')>
                                    🚚 Pickup
                                </option>
                            </select>
                            @error('vehicle_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- License Number -->
                        <div class="mb-3">
                            <label for="license_number" class="form-label">License Number (Optional)</label>
                            <input 
                                type="text" 
                                class="form-control @error('license_number') is-invalid @enderror" 
                                id="license_number" 
                                name="license_number" 
                                value="{{ old('license_number', $user->license_number) }}"
                                placeholder="e.g., DL-ABC123">
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Latitude -->
                        <div class="mb-3">
                            <label for="latitude" class="form-label">Latitude (Optional)</label>
                            <input 
                                type="number" 
                                step="0.000001"
                                class="form-control @error('latitude') is-invalid @enderror" 
                                id="latitude" 
                                name="latitude" 
                                value="{{ old('latitude', $user->latitude) }}"
                                placeholder="e.g., 30.0444"
                                min="-90"
                                max="90">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div class="mb-3">
                            <label for="longitude" class="form-label">Longitude (Optional)</label>
                            <input 
                                type="number" 
                                step="0.000001"
                                class="form-control @error('longitude') is-invalid @enderror" 
                                id="longitude" 
                                name="longitude" 
                                value="{{ old('longitude', $user->longitude) }}"
                                placeholder="e.g., 31.2357"
                                min="-180"
                                max="180">
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('delivery.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Status Info -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">Account Information</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Account Type:</strong> Delivery Partner
                        </li>
                        <li class="mb-2">
                            <strong>Status:</strong> 
                            @if($user->verified)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-clock"></i> Pending Verification
                                </span>
                            @endif
                        </li>
                        <li>
                            <strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
