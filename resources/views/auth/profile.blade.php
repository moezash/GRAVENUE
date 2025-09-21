@extends('layouts.app')

@section('title', 'Profil - Gravenue')

@section('content')
<section class="profile-section">
    <div class="container">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info">
                    <h1>{{ Auth::user()->name }}</h1>
                    <p>{{ Auth::user()->email }}</p>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ Auth::user()->bookings()->count() }}</span>
                            <span class="stat-label">Total Booking</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ Auth::user()->bookings()->where('status', 'approved')->count() }}</span>
                            <span class="stat-label">Disetujui</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-content">
                <!-- Edit Profile Form -->
                <div class="profile-form-section">
                    <h2>Edit Profil</h2>

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <p>{{ session('success') }}</p>
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

                    <form method="POST" action="{{ route('profile') }}" class="profile-form">
                        @csrf

                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                            </div>
                            @error('name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            </div>
                            @error('email')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required>
                            </div>
                            @error('phone')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="password-form-section">
                    <h2>Ubah Password</h2>

                    <form method="POST" action="{{ route('change-password') }}" class="password-form">
                        @csrf

                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            @error('current_password')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <div class="input-wrapper">
                                <i class="fas fa-key"></i>
                                <input type="password" id="password" name="password" required>
                            </div>
                            @error('password')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <div class="input-wrapper">
                                <i class="fas fa-key"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-secondary">
                            <i class="fas fa-shield-alt"></i>
                            Ubah Password
                        </button>
                    </form>
                </div>

                <!-- Account Actions -->
                <div class="account-actions">
                    <h2>Aksi Akun</h2>
                    <div class="action-buttons">
                        <a href="{{ route('dashboard') }}" class="btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-danger" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.profile-section {
    padding: 2rem 0 4rem;
    background: #f8f9fa;
    min-height: calc(100vh - 160px);
}

.profile-container {
    max-width: 800px;
    margin: 0 auto;
}

.profile-header {
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    border-left: 5px solid #ff7844;
}

.profile-avatar {
    font-size: 5rem;
    color: #ff7844;
    background: rgba(255, 120, 68, 0.1);
    border-radius: 50%;
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-info h1 {
    font-size: 2.5rem;
    color: #1b1f3a;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.profile-info p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

.profile-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #ff7844;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.profile-content {
    display: grid;
    gap: 2rem;
}

.profile-form-section,
.password-form-section,
.account-actions {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.profile-form-section h2,
.password-form-section h2,
.account-actions h2 {
    color: #1b1f3a;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f1f3f5;
}

.alert {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #f0fff4;
    color: #2d7d32;
    border: 1px solid #c6f6d5;
}

.alert-error {
    background: #fee;
    color: #c53030;
    border: 1px solid #fed7d7;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #1b1f3a;
    margin-bottom: 0.5rem;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    z-index: 2;
}

.input-wrapper input {
    width: 100%;
    padding: 1rem 1rem 1rem 2.5rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 1rem;
    transition: border-color 0.3s;
    background: white;
}

.input-wrapper input:focus {
    outline: none;
    border-color: #ff7844;
}

.error-text {
    color: #c53030;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.btn-primary,
.btn-secondary,
.btn-outline,
.btn-danger {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    border: 2px solid transparent;
}

.btn-primary {
    background: linear-gradient(135deg, #ff7844 0%, #e5673a 100%);
    color: white;
    border-color: #ff7844;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 120, 68, 0.3);
}

.btn-secondary {
    background: #53354a;
    color: white;
    border-color: #53354a;
}

.btn-secondary:hover {
    background: #3d263a;
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    color: #53354a;
    border-color: #53354a;
}

.btn-outline:hover {
    background: #53354a;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 2rem;
    }

    .profile-info h1 {
        font-size: 2rem;
    }

    .profile-stats {
        justify-content: center;
    }

    .profile-form-section,
    .password-form-section,
    .account-actions {
        padding: 1.5rem;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn-primary,
    .btn-secondary,
    .btn-outline,
    .btn-danger {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
