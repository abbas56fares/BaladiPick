@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="py-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <span class="badge bg-primary-subtle text-primary fw-semibold mb-3">All-in-one delivery orchestration</span>
            <h1 class="display-5 fw-bold">BaladiPick keeps shops, couriers, and customers in sync.</h1>
            <p class="lead text-muted">Manage shop orders, route deliveries, and keep every handoff verified with QR and OTP flows. Built for teams that want reliable last-mile execution without the busywork.</p>
            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">Go to Login</a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-4">Create an Account</a>
            </div>
            <div class="mt-4 text-muted small">Already onboarded? Jump straight into your dashboard after signing in.</div>
        </div>
        <div class="col-lg-6">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-primary mb-2"><i class="bi bi-shop-window fs-3"></i></div>
                            <h5 class="card-title mb-2">Shop owners</h5>
                            <p class="text-muted mb-0">Create orders, track pickups, and verify every collection with QR codes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-success mb-2"><i class="bi bi-truck fs-3"></i></div>
                            <h5 class="card-title mb-2">Delivery teams</h5>
                            <p class="text-muted mb-0">Accept nearby jobs, navigate via the live map, and confirm drop-offs with OTP.</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-warning mb-2"><i class="bi bi-shield-check fs-3"></i></div>
                            <h5 class="card-title mb-2">Operations leaders</h5>
                            <p class="text-muted mb-0">Audit orders end-to-end, manage shops and couriers, and export reports with one click.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-10 col-xl-8">
            <h2 class="h3 fw-bold mb-3">How it works</h2>
            <p class="text-muted">A quick walkthrough of the BaladiPick flow—ideal if you are onboarding a shop or delivery partner.</p>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">1</span>
                        <div class="fw-semibold">Register and verify</div>
                    </div>
                    <p class="text-muted mb-3">Create a shop or courier account. Admin can verify and enable the profile.</p>
                    
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">2</span>
                        <div class="fw-semibold">Create & assign orders</div>
                    </div>
                    <p class="text-muted mb-3">Shops submit pickup orders, track them on the map, and hand off with QR verification.</p>
                    
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">3</span>
                        <div class="fw-semibold">Deliver with OTP</div>
                    </div>
                    <p class="text-muted mb-3">Couriers accept nearby jobs, navigate via the live map, then complete drop-off with OTP confirmation.</p>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
