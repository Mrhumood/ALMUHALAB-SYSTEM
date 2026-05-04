@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

    <div class="mb-3">
        <h1 class="h4 fw-bold mb-0"><i class="bi bi-person-circle text-primary me-2"></i>My Profile</h1>
        <p class="text-muted small">Manage your account information and password.</p>
    </div>

    {{-- Profile Info --}}
    <div class="page-card mb-4">
        <h6 class="fw-bold mb-4 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
            <i class="bi bi-person me-1"></i>Personal Information
        </h6>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="text" id="phone_number" name="phone_number"
                               class="form-control @error('phone_number') is-invalid @enderror"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               placeholder="+966 5x xxx xxxx">
                    </div>
                    @error('phone_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" id="address" name="address"
                               class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address', $user->address) }}"
                               placeholder="City, Country">
                    </div>
                    @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            @if(session('status') === 'profile-updated')
                <div class="alert alert-success py-2 small mb-3">
                    <i class="bi bi-check-circle me-1"></i>Profile saved successfully.
                </div>
            @endif

            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i>Save Profile
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="page-card mb-4">
        <h6 class="fw-bold mb-4 text-uppercase text-muted" style="font-size:.72rem;letter-spacing:.07em">
            <i class="bi bi-lock me-1"></i>Change Password
        </h6>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                           class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                           autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" autocomplete="new-password">
                </div>
            </div>

            @if(session('status') === 'password-updated')
                <div class="alert alert-success py-2 small mb-3">
                    <i class="bi bi-check-circle me-1"></i>Password updated successfully.
                </div>
            @endif

            <button type="submit" class="btn btn-warning px-4">
                <i class="bi bi-lock me-1"></i>Update Password
            </button>
        </form>
    </div>

    {{-- Delete Account --}}
    <div class="page-card border border-danger">
        <h6 class="fw-bold mb-1 text-danger">
            <i class="bi bi-exclamation-triangle me-1"></i>Delete Account
        </h6>
        <p class="text-muted small mb-3">Once deleted, all your data will be permanently removed.</p>

        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            Delete My Account
        </button>
    </div>

</div>
</div>

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="text-muted small">Please enter your password to confirm account deletion. This action is irreversible.</p>
                    <label for="del_password" class="form-label">Password</label>
                    <input type="password" id="del_password" name="password"
                           class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                           placeholder="Enter your current password">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Delete My Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
