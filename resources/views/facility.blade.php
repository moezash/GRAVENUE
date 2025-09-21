@extends('layouts.app')

@section('title', $facility->name . ' - Detail Fasilitas')

@section('content')
<!-- Facility Detail Section -->
<section class="facility-detail-section">
    <div class="container">
        <div class="facility-detail-layout">
            <!-- Facility Image -->
            <div class="facility-image-container">
                <img src="{{ $facility->image ? asset('storage/' . $facility->image) : 'https://picsum.photos/600/400?random=' . $facility->id }}" 
                     alt="{{ $facility->name }}"
                     class="facility-main-image">
            </div>

            <!-- Facility Info -->
            <div class="facility-info-container">
                <h1 class="facility-title">{{ $facility->name }}</h1>
                
                <div class="facility-category-badge">
                    <span class="category-tag">{{ ucfirst($facility->category) }}</span>
                </div>

                <div class="facility-description">
                    <h3>Deskripsi</h3>
                    <p>{{ $facility->description }}</p>
                </div>

                <div class="facility-specifications">
                    <h3>Spesifikasi</h3>
                    
                    <div class="spec-grid">
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="spec-content">
                                <span class="spec-label">Kapasitas</span>
                                <span class="spec-value">{{ $facility->capacity }} orang</span>
                            </div>
                        </div>
                        
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-money-bill"></i>
                            </div>
                            <div class="spec-content">
                                <span class="spec-label">Harga per Hari</span>
                                <span class="spec-value">Rp {{ number_format($facility->price_per_day, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="spec-content">
                                <span class="spec-label">Lokasi</span>
                                <span class="spec-value">SMKN 4 Malang</span>
                            </div>
                        </div>
                        
                        <div class="spec-card">
                            <div class="spec-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="spec-content">
                                <span class="spec-label">Jam Operasional</span>
                                <span class="spec-value">07:00 - 16:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="facility-actions">
                    @auth
                        @if($facility->status === 'available')
                            <button class="btn-book-facility" onclick="window.location.href='{{ route('booking.form', $facility->id) }}'">
                                <i class="fas fa-calendar-plus"></i>
                                Booking Sekarang
                            </button>
                        @else
                            <button class="btn-unavailable" disabled>
                                <i class="fas fa-times-circle"></i>
                                Tidak Tersedia
                            </button>
                        @endif
                    @else
                        <button class="btn-login-required" onclick="window.location.href='{{ route('login') }}'">
                            <i class="fas fa-sign-in-alt"></i>
                            Login untuk Booking
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Card -->
<section class="pricing-section">
    <div class="container">
        <div class="pricing-card">
            <div class="price-header">
                <span class="price-amount">Rp {{ number_format($facility->price_per_hour, 0, ',', '.') }}</span>
                <span class="price-period">per jam</span>
            </div>
            
            <div class="price-features">
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <span>Kapasitas: {{ $facility->capacity }} orang</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $facility->description }}</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-star"></i>
                    <span>{{ ucfirst($facility->category) }}</span>
                </div>
            </div>
            
            @auth
                @if($facility->status === 'available')
                    <button class="btn-book-now" onclick="window.location.href='{{ route('booking.form', $facility->id) }}'">
                        Ajukan Sewa Sekarang
                    </button>
                @else
                    <button class="btn-unavailable" disabled>
                        Tidak Tersedia
                    </button>
                @endif
            @else
                <button class="btn-login-first" onclick="window.location.href='{{ route('login') }}'">
                    Login untuk Booking
                </button>
            @endauth
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-help-section">
    <div class="container">
        <div class="contact-help-card">
            <h3>Butuh Bantuan?</h3>
            <div class="contact-info">
                <div class="contact-item">
                    <span class="contact-label">Telephone</span>
                    <span class="contact-value">(0341) 551431</span>
                </div>
                <div class="contact-item">
                    <span class="contact-label">Email</span>
                    <span class="contact-value">info@smkn4malang.sch.id</span>
                </div>
                <div class="contact-item">
                    <span class="contact-label">Jam Operasional</span>
                    <span class="contact-value">Senin - Jumat ( 08.00 - 16.00 )</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
