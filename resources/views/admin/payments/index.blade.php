@extends('layouts.app')

@section('title', 'Payments Management')

@section('content')
<div class="container my-4">
    <div class="text-center py-5">
        <i class="bi bi-credit-card text-muted" style="font-size: 4rem;"></i>
        <h3 class="text-muted mt-3">Payments Management</h3>
        <p class="text-muted">Coming Soon - Admin payments monitoring interface</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
</div>
@endsection