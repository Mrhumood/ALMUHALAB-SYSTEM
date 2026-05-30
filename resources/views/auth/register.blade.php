<x-guest-layout>
    <h5 class="fw-bold mb-1" style="color:#fff;letter-spacing:-.3px">{{ __('Create an account') }}</h5>
    <p style="color:rgba(255,255,255,.4);font-size:.82rem;margin-bottom:1.5rem">{{ __('Fill in your details to get started') }}</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Full Name') }}</label>
            <input id="name" type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   required autofocus autocomplete="name"
                   placeholder="{{ __('Your full name') }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   required autocomplete="username"
                   placeholder="you@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required autocomplete="new-password"
                   placeholder="••••••••">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Terms & Conditions -->
        <div class="mb-4">
            <div class="form-check">
                <input id="terms" type="checkbox" name="terms"
                       class="form-check-input @error('terms') is-invalid @enderror"
                       {{ old('terms') ? 'checked' : '' }} required>
                <label for="terms" class="form-check-label">
                    {{ __('I agree to the') }}
                    <a href="{{ route('terms') }}" target="_blank" style="color:#f59e0b;font-weight:600">{{ __('Terms & Conditions') }}</a>
                </label>
                @error('terms')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-person-plus me-1"></i>{{ __('Create Account') }}
        </button>

        <p class="text-center mt-4 mb-0" style="font-size:.82rem;color:rgba(255,255,255,.4)">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" style="color:#f59e0b;font-weight:600">{{ __('Sign in') }}</a>
        </p>
    </form>
</x-guest-layout>
