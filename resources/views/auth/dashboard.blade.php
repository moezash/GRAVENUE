@extends('layouts.app')

@section('title', 'Dashboard - Gravenue')

@section('content')
<section class="dashboard-section">
    <div class="container">
        <!-- Welcome Header -->
        <div class="dashboard-welcome">
            <div class="welcome-text">
                <h1>Selamat Datang, {{ $user->name }}!</h1>
                <p>Kelola booking dan profil Anda dengan mudah</p>
            </div>
            <div class="welcome-actions">
                <a href="{{ route('facilities') }}" class="btn-booking">
                    <i class="fas fa-plus"></i>
                    Booking Baru
                </a>
                <a href="{{ route('profile') }}" class="btn-profile">
                    <i class="fas fa-user-edit"></i>
                    Edit Profil
                </a>
            </div>
        </div>

        <!-- User Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_bookings'] }}</h3>
                    <p>TOTAL<br>BOOKING</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['pending_bookings'] }}</h3>
                    <p>MENUNGGU<br>PERSETUJUAN</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['approved_bookings'] }}</h3>
                    <p>DISETUJUI</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['completed_bookings'] }}</h3>
                    <p>SELESAI</p>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bookings-section">
            <h2>Booking Terbaru</h2>
            
            @if($bookings->count() > 0)
                <div class="bookings-list">
                    @foreach($bookings as $booking)
                        <div class="booking-item">
                            <div class="booking-info">
                                <h4>{{ $booking->facility->name }}</h4>
                                <p>{{ $booking->booking_date->format('d M Y') }} - {{ ucfirst($booking->status) }}</p>
                            </div>
                            <div class="booking-action">
                                <a href="{{ route('booking.status', $booking->id) }}" class="btn-detail">Lihat Detail</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-bookings">
                    <div class="empty-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                        <h3>Belum Ada Booking</h3>
                        <p>Mulai booking fasilitas untuk event atau kegiatan Anda</p>
                        <a href="{{ route('facilities') }}" class="btn-primary">
                            <i class="fas fa-search"></i>
                            Lihat Fasilitas
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>Menu Cepat</h3>
                <div class="actions-grid">
                    <a href="{{ route('facilities') }}" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="action-content">
                            <h4>Lihat Fasilitas</h4>
                            <p>Jelajahi semua fasilitas tersedia</p>
                        </div>
                    </a>
                    <a href="{{ route('schedule') }}" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="action-content">
                            <h4>Cek Jadwal</h4>
                            <p>Lihat ketersediaan fasilitas</p>
                        </div>
                    </a>
                    <a href="{{ route('contact') }}" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="action-content">
                            <h4>Bantuan</h4>
                            <p>Hubungi customer support</p>
                        </div>
                    </a>
                    <a href="{{ route('profile') }}" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div class="action-content">
                            <h4>Pengaturan</h4>
                            <p>Kelola profil dan akun</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
