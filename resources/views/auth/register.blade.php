@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Register for BaladiPick</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="timezone" class="form-label">Your Timezone</label>
                        <select class="form-select @error('timezone') is-invalid @enderror" 
                                id="timezone" name="timezone" required>
                            <option value="">Select your timezone...</option>
                            <optgroup label="Asia">
                                <option value="Asia/Amman" {{ old('timezone') == 'Asia/Amman' ? 'selected' : '' }}>Jordan (UTC+2/+3)</option>
                                <option value="Asia/Beirut" {{ old('timezone') == 'Asia/Beirut' ? 'selected' : '' }}>Lebanon (UTC+2/+3)</option>
                                <option value="Asia/Baghdad" {{ old('timezone') == 'Asia/Baghdad' ? 'selected' : '' }}>Iraq (UTC+3)</option>
                                <option value="Asia/Dubai" {{ old('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>UAE (UTC+4)</option>
                                <option value="Asia/Kolkata" {{ old('timezone') == 'Asia/Kolkata' ? 'selected' : '' }}>India (UTC+5:30)</option>
                                <option value="Asia/Bangkok" {{ old('timezone') == 'Asia/Bangkok' ? 'selected' : '' }}>Thailand (UTC+7)</option>
                                <option value="Asia/Shanghai" {{ old('timezone') == 'Asia/Shanghai' ? 'selected' : '' }}>China (UTC+8)</option>
                                <option value="Asia/Tokyo" {{ old('timezone') == 'Asia/Tokyo' ? 'selected' : '' }}>Japan (UTC+9)</option>
                            </optgroup>
                            <optgroup label="Africa">
                                <option value="Africa/Cairo" {{ old('timezone') == 'Africa/Cairo' ? 'selected' : '' }}>Egypt (UTC+2)</option>
                                <option value="Africa/Johannesburg" {{ old('timezone') == 'Africa/Johannesburg' ? 'selected' : '' }}>South Africa (UTC+2)</option>
                                <option value="Africa/Lagos" {{ old('timezone') == 'Africa/Lagos' ? 'selected' : '' }}>Nigeria (UTC+1)</option>
                                <option value="Africa/Nairobi" {{ old('timezone') == 'Africa/Nairobi' ? 'selected' : '' }}>Kenya (UTC+3)</option>
                            </optgroup>
                            <optgroup label="Europe">
                                <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>UK (UTC+0/+1)</option>
                                <option value="Europe/Paris" {{ old('timezone') == 'Europe/Paris' ? 'selected' : '' }}>France (UTC+1/+2)</option>
                                <option value="Europe/Berlin" {{ old('timezone') == 'Europe/Berlin' ? 'selected' : '' }}>Germany (UTC+1/+2)</option>
                                <option value="Europe/Istanbul" {{ old('timezone') == 'Europe/Istanbul' ? 'selected' : '' }}>Turkey (UTC+3)</option>
                                <option value="Europe/Moscow" {{ old('timezone') == 'Europe/Moscow' ? 'selected' : '' }}>Russia (UTC+3)</option>
                            </optgroup>
                            <optgroup label="Americas">
                                <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>USA East (UTC-5/-4)</option>
                                <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>USA Central (UTC-6/-5)</option>
                                <option value="America/Denver" {{ old('timezone') == 'America/Denver' ? 'selected' : '' }}>USA Mountain (UTC-7/-6)</option>
                                <option value="America/Los_Angeles" {{ old('timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>USA West (UTC-8/-7)</option>
                                <option value="America/Toronto" {{ old('timezone') == 'America/Toronto' ? 'selected' : '' }}>Canada (UTC-5/-4)</option>
                                <option value="America/Mexico_City" {{ old('timezone') == 'America/Mexico_City' ? 'selected' : '' }}>Mexico (UTC-6/-5)</option>
                            </optgroup>
                            <optgroup label="Oceania">
                                <option value="Australia/Sydney" {{ old('timezone') == 'Australia/Sydney' ? 'selected' : '' }}>Australia Sydney (UTC+10/+11)</option>
                                <option value="Australia/Melbourne" {{ old('timezone') == 'Australia/Melbourne' ? 'selected' : '' }}>Australia Melbourne (UTC+10/+11)</option>
                                <option value="Pacific/Auckland" {{ old('timezone') == 'Pacific/Auckland' ? 'selected' : '' }}>New Zealand (UTC+12/+13)</option>
                            </optgroup>
                            <optgroup label="UTC">
                                <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC (UTC+0)</option>
                            </optgroup>
                        </select>
                        @error('timezone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">I want to register as</label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                            <option value="">Select role...</option>
                            <option value="shop" {{ old('role') == 'shop' ? 'selected' : '' }}>Shop Owner</option>
                            <option value="delivery" {{ old('role') == 'delivery' ? 'selected' : '' }}>Delivery Driver</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="id_document" class="form-label">ID/Passport Document</label>
                        <input type="file" class="form-control @error('id_document') is-invalid @enderror" 
                               id="id_document" name="id_document" accept="image/*" required>
                        <small class="form-text text-muted">Upload a clear photo of your ID or passport. This will be verified by an admin before you can start.</small>
                        @error('id_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Register</button>
                    <a href="{{ route('login') }}" class="btn btn-link">Already have an account? Login</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
