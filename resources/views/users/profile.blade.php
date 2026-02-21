@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user-circle me-2"></i>My Profile
                </h1>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Profile Information</h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Required only if changing password</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Profile
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Account Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                @if($user->avatar)
                                    <div class="d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; overflow: hidden; border-radius: 50%;">
                                        <img src="{{ asset($user->avatar) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-user fa-2x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <strong>Role:</strong>
                                <span class="badge bg-secondary float-end" data-role="{{ $user->role }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Member since:</strong>
                                <span class="float-end">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Last updated:</strong>
                                <span class="float-end">{{ $user->updated_at->format('M d, Y') }}</span>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Contact administrator for role changes
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .badge-notification {
        position: absolute;
        top: 3px;
        right: 3px;
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
    }
    .navbar-nav .dropdown-toggle {
        outline: none;
    }
    .form-control-dark {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }
    .form-control-dark::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    .form-control-dark:focus {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    }
    
    /* Role-specific badge colors */
    .badge[data-role="admin"] {
        background-color: #dc3545 !important;
    }
    .badge[data-role="manager"] {
        background-color: #0d6efd !important;
    }
    .badge[data-role="cashier"] {
        background-color: #198754 !important;
    }
    .badge[data-role="superadmin"] {
        background-color: #6f42c1 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleBadges = document.querySelectorAll('.badge[data-role]');
        roleBadges.forEach(badge => {
            const role = badge.getAttribute('data-role');
            badge.classList.add('bg-secondary');
        });
    });
</script>
@endsection