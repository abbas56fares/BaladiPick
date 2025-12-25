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
