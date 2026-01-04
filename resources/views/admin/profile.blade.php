@extends('layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-person-circle"></i> Admin Profile</h4>
                    <p class="mb-0 text-muted">Update your account information and password</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="bi bi-person"></i> Full Name
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email Address
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Must be unique and valid</small>
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="bi bi-telephone"></i> Phone Number
                            </label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $admin->phone) }}" placeholder="+961...">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Optional - Include country code (e.g., +961)</small>
                        </div>

                        <!-- Timezone -->
                        <div class="mb-3">
                            <label for="timezone" class="form-label">
                                <i class="bi bi-globe"></i> Timezone
                            </label>
                            <select class="form-select @error('timezone') is-invalid @enderror" 
                                    id="timezone" name="timezone">
                                <option value="">-- Select Timezone --</option>
                                @php
                                    $timezones = [
                                        'Asia/Beirut' => 'Asia/Beirut (Lebanon)',
                                        'Europe/London' => 'Europe/London (GMT)',
                                        'Europe/Paris' => 'Europe/Paris (CET)',
                                        'America/New_York' => 'America/New_York (EST)',
                                        'America/Los_Angeles' => 'America/Los_Angeles (PST)',
                                        'Asia/Dubai' => 'Asia/Dubai (GST)',
                                        'Asia/Bangkok' => 'Asia/Bangkok (ICT)',
                                        'Australia/Sydney' => 'Australia/Sydney (AEDT)',
                                    ];
                                @endphp
                                @foreach($timezones as $tz => $label)
                                    <option value="{{ $tz }}" @selected(old('timezone', $admin->timezone) === $tz)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- Password Section -->
                        <h5 class="mb-3">
                            <i class="bi bi-lock"></i> Change Password (Optional)
                        </h5>
                        <p class="text-muted small">Leave empty to keep your current password</p>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-key"></i> New Password
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="At least 8 characters">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters. Leave empty to keep current password.</small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="bi bi-key"></i> Confirm Password
                            </label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Strength Indicator -->
                        <div id="passwordStrength" style="display: none;">
                            <small class="form-text">
                                <strong>Password Strength:</strong> 
                                <div class="progress" style="height: 5px;">
                                    <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <span id="strengthText"></span>
                            </small>
                        </div>

                        <hr>

                        <!-- Form Actions -->
                        <div class="d-grid gap-2 d-sm-flex">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>

                    <!-- Account Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">Account Information</h6>
                        <p class="mb-1"><strong>Role:</strong> Administrator</p>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Verified</span></p>
                        <p class="mb-0"><strong>Member Since:</strong> {{ $admin->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length === 0) {
            passwordStrength.style.display = 'none';
            return;
        }

        passwordStrength.style.display = 'block';

        let strength = 0;
        let text = 'Weak';
        let color = 'danger';

        // Check length
        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 25;

        // Check for numbers
        if (/\d/.test(password)) strength += 15;

        // Check for lowercase
        if (/[a-z]/.test(password)) strength += 10;

        // Check for uppercase
        if (/[A-Z]/.test(password)) strength += 10;

        // Check for special characters
        if (/[!@#$%^&*]/.test(password)) strength += 15;

        // Determine strength level
        if (strength < 30) {
            text = 'Weak';
            color = 'danger';
        } else if (strength < 60) {
            text = 'Fair';
            color = 'warning';
        } else if (strength < 80) {
            text = 'Good';
            color = 'info';
        } else {
            text = 'Strong';
            color = 'success';
        }

        strengthBar.style.width = strength + '%';
        strengthBar.className = `progress-bar bg-${color}`;
        strengthText.textContent = text;
        strengthText.className = `text-${color}`;
    });
</script>
@endpush
