@extends('layouts.app')

@section('title', 'Delivery Pricing Settings')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-gear"></i> Delivery Pricing Settings</h4>
                    <p class="mb-0 text-muted">Control delivery cost calculations for all orders (Lebanon 2026 pricing model)</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Formula:</strong> Delivery Cost = (Base Fee + Distance × Rate per KM) × Fuel Adjustment<br>
                            <small>The final cost will be at least the minimum charge specified for each vehicle type.</small>
                        </div>

                        <h5 class="mb-3"><i class="bi bi-bicycle"></i> Bike/Motorcycle Settings</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bike_base_fee" class="form-label">Base Fee ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('bike_base_fee') is-invalid @enderror" 
                                           id="bike_base_fee" name="bike_base_fee" 
                                           value="{{ old('bike_base_fee', $settings['bike_base_fee']->value ?? 2.00) }}" required>
                                    @error('bike_base_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bike_rate_per_km" class="form-label">Rate per KM ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('bike_rate_per_km') is-invalid @enderror" 
                                           id="bike_rate_per_km" name="bike_rate_per_km" 
                                           value="{{ old('bike_rate_per_km', $settings['bike_rate_per_km']->value ?? 0.30) }}" required>
                                    @error('bike_rate_per_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bike_min_charge" class="form-label">Min Charge ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('bike_min_charge') is-invalid @enderror" 
                                           id="bike_min_charge" name="bike_min_charge" 
                                           value="{{ old('bike_min_charge', $settings['bike_min_charge']->value ?? 3.00) }}" required>
                                    @error('bike_min_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bike_max_distance" class="form-label">Max Distance (KM)</label>
                                    <input type="number" step="1" min="1" class="form-control @error('bike_max_distance') is-invalid @enderror" 
                                           id="bike_max_distance" name="bike_max_distance" 
                                           value="{{ old('bike_max_distance', $settings['bike_max_distance']->value ?? 10) }}" required>
                                    @error('bike_max_distance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3"><i class="bi bi-car-front"></i> Car Settings</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="car_base_fee" class="form-label">Base Fee ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('car_base_fee') is-invalid @enderror" 
                                           id="car_base_fee" name="car_base_fee" 
                                           value="{{ old('car_base_fee', $settings['car_base_fee']->value ?? 3.50) }}" required>
                                    @error('car_base_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="car_rate_per_km" class="form-label">Rate per KM ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('car_rate_per_km') is-invalid @enderror" 
                                           id="car_rate_per_km" name="car_rate_per_km" 
                                           value="{{ old('car_rate_per_km', $settings['car_rate_per_km']->value ?? 0.60) }}" required>
                                    @error('car_rate_per_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="car_min_charge" class="form-label">Min Charge ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('car_min_charge') is-invalid @enderror" 
                                           id="car_min_charge" name="car_min_charge" 
                                           value="{{ old('car_min_charge', $settings['car_min_charge']->value ?? 4.00) }}" required>
                                    @error('car_min_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="car_max_distance" class="form-label">Max Distance (KM)</label>
                                    <input type="number" step="1" min="1" class="form-control @error('car_max_distance') is-invalid @enderror" 
                                           id="car_max_distance" name="car_max_distance" 
                                           value="{{ old('car_max_distance', $settings['car_max_distance']->value ?? 90) }}" required>
                                    @error('car_max_distance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3"><i class="bi bi-truck"></i> Pickup Truck Settings</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pickup_base_fee" class="form-label">Base Fee ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('pickup_base_fee') is-invalid @enderror" 
                                           id="pickup_base_fee" name="pickup_base_fee" 
                                           value="{{ old('pickup_base_fee', $settings['pickup_base_fee']->value ?? 10.00) }}" required>
                                    @error('pickup_base_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pickup_rate_per_km" class="form-label">Rate per KM ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('pickup_rate_per_km') is-invalid @enderror" 
                                           id="pickup_rate_per_km" name="pickup_rate_per_km" 
                                           value="{{ old('pickup_rate_per_km', $settings['pickup_rate_per_km']->value ?? 1.25) }}" required>
                                    @error('pickup_rate_per_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pickup_min_charge" class="form-label">Min Charge ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('pickup_min_charge') is-invalid @enderror" 
                                           id="pickup_min_charge" name="pickup_min_charge" 
                                           value="{{ old('pickup_min_charge', $settings['pickup_min_charge']->value ?? 10.00) }}" required>
                                    @error('pickup_min_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="pickup_max_distance" class="form-label">Max Distance (KM)</label>
                                    <input type="number" step="1" min="1" class="form-control @error('pickup_max_distance') is-invalid @enderror" 
                                           id="pickup_max_distance" name="pickup_max_distance" 
                                           value="{{ old('pickup_max_distance', $settings['pickup_max_distance']->value ?? 90) }}" required>
                                    @error('pickup_max_distance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3"><i class="bi bi-fuel-pump"></i> Fuel Adjustment</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fuel_adjustment" class="form-label">Fuel Adjustment Multiplier</label>
                                    <input type="number" step="0.01" min="0.1" max="5" class="form-control @error('fuel_adjustment') is-invalid @enderror" 
                                           id="fuel_adjustment" name="fuel_adjustment" 
                                           value="{{ old('fuel_adjustment', $settings['fuel_adjustment']->value ?? 1.00) }}" required>
                                    <small class="form-text text-muted">1.00 = normal pricing, 1.15 = 15% increase due to fuel costs, etc.</small>
                                    @error('fuel_adjustment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
