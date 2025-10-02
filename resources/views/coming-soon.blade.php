@extends('layouts.app')

@section('title', $title ?? 'Coming Soon')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="mb-4">
                <i class="bi bi-gear display-1 text-primary"></i>
            </div>

            <h1 class="display-4 fw-bold text-primary mb-3">
                {{ $title ?? 'Coming Soon' }}
            </h1>

            <p class="lead text-muted mb-4">
                This section is currently under development as part of our comprehensive Applax Payment Gateway showcase.
                We're building every feature to demonstrate the full power of the gate-sdk package.
            </p>

            <div class="alert alert-info d-inline-block">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Development in Progress:</strong> This feature will showcase specific gate-sdk functionality with live examples and documentation.
            </div>

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-3">
                    <i class="bi bi-house me-2"></i>Back to Home
                </a>
                <a href="https://github.com/applax-dev/gate-sdk" class="btn btn-outline-primary btn-lg" target="_blank">
                    <i class="bi bi-github me-2"></i>View SDK Repository
                </a>
            </div>

            <hr class="my-5">

            <div class="row text-start">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">What's Coming</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Complete SDK method demonstrations</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Interactive code examples</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Real-time payment processing</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Comprehensive error handling showcase</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Current Features</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Database architecture with migrations</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Eloquent models with relationships</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Complete GateSDK service wrapper</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Bootstrap 5 responsive design</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection