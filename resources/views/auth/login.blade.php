@extends('layouts.app')

@section('title', 'Login - Gravenue')


@section('content')
<section class="gravenue-auth-glassmorphism-section">
    <div class="gravenue-auth-unique-container">
        <div class="gravenue-auth-form-glassmorphism">
            <div class="auth-header">
                <button class="auth-back-btn" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h1>Login</h1>
                <a href="{{ route('register') }}" class="auth-register-link">Register</a>
            </div>

                @if (isset($message))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <p>{{ $message }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                        </div>
                        @error('email')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Password">
                        </div>
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Ingat saya
                        </label>
                        <a href="#" class="forgot-password">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn-auth">
                        Masuk
                    </button>
                </form>

        </div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle intended URL from sessionStorage
    const intendedUrl = sessionStorage.getItem('intended_url');
    if (intendedUrl) {
        const form = document.querySelector('.auth-form');
        if (form) {
            // Add intended URL as hidden input
            const intendedInput = document.createElement('input');
            intendedInput.type = 'hidden';
            intendedInput.name = 'intended';
            intendedInput.value = intendedUrl;
            form.appendChild(intendedInput);

            // Clear from sessionStorage
            sessionStorage.removeItem('intended_url');
        }
    }
});
</script>
@endsection
