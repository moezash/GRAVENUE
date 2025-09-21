@extends('layouts.admin')

@section('title', 'Detail Booking #' . $booking->id)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/booking-detail.css') }}">
@endpush

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Detail Booking #{{ $booking->id }}</h2>
        <a href="{{ route('admin.bookings') }}" class="card-action">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
    
    <div class="booking-detail-container" style="padding: 32px;">
        <div class="booking-detail-grid">
            <!-- Booking Information -->
            <div class="info-section">
                <div class="info-section-header">
                    <h3>Informasi Booking</h3>
                </div>
                
                <div class="info-section-content">
                    <div class="detail-item">
                        <label class="detail-label">Status Booking</label>
                        @if($booking->status == 'pending')
                            <span class="status-badge status-pending">
                                <i class="fas fa-clock"></i>
                                Pending
                            </span>
                        @elseif($booking->status == 'approved')
                            <span class="status-badge status-approved">
                                <i class="fas fa-check-circle"></i>
                                Approved
                            </span>
                        @elseif($booking->status == 'rejected')
                            <span class="status-badge status-rejected">
                                <i class="fas fa-times-circle"></i>
                                Rejected
                            </span>
                        @else
                            <span class="status-badge status-cancelled">
                                <i class="fas fa-ban"></i>
                                {{ ucfirst($booking->status) }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Nama Acara</label>
                        <p class="detail-value">{{ $booking->event_name }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Fasilitas</label>
                        <p class="detail-value">{{ $booking->facility->name }}</p>
                        <div class="detail-sub">{{ $booking->facility->category }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Tanggal & Waktu</label>
                        <p class="detail-value">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('d F Y') }}
                            @if($booking->end_date && $booking->end_date != $booking->booking_date)
                                - {{ \Carbon\Carbon::parse($booking->end_date)->format('d F Y') }}
                            @endif
                        </p>
                        <div class="detail-sub">{{ $booking->start_time }} - {{ $booking->end_time }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Jumlah Peserta</label>
                        <p class="detail-value">{{ $booking->participants }} orang</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Total Harga</label>
                        <p class="detail-value large">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                    
                    @if($booking->additional_notes)
                    <div class="detail-item">
                        <label class="detail-label">Catatan Tambahan</label>
                        <div class="notes-section">
                            <p class="notes-content">{{ $booking->additional_notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="info-section">
                <div class="info-section-header">
                    <h3>Informasi Penyewa</h3>
                </div>
                
                <div class="info-section-content">
                    <div class="detail-item">
                        <label class="detail-label">Nama Lengkap</label>
                        <p class="detail-value">{{ $booking->user_name }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Email</label>
                        <p class="detail-value">{{ $booking->user_email }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Nomor Telepon</label>
                        <p class="detail-value">{{ $booking->user_phone ?: '-' }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Organisasi</label>
                        <p class="detail-value">{{ $booking->organization ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        @if($booking->payment)
        <div class="info-section" style="margin-top: 2rem;">
            <div class="info-section-header">
                <h3>Informasi Pembayaran</h3>
            </div>
            
            <div class="info-section-content">
                <div class="detail-item">
                    <label class="detail-label">Status Pembayaran</label>
                    @if($booking->payment->payment_status == 'paid' || $booking->payment->payment_status == 'paid_dummy')
                        <span class="status-badge status-approved">
                            <i class="fas fa-check-circle"></i>
                            Paid
                        </span>
                    @elseif($booking->payment->payment_status == 'pending')
                        <span class="status-badge status-pending">
                            <i class="fas fa-clock"></i>
                            Pending
                        </span>
                    @else
                        <span class="status-badge status-rejected">
                            <i class="fas fa-times-circle"></i>
                            {{ ucfirst($booking->payment->payment_status) }}
                        </span>
                    @endif
                </div>
                
                <div class="detail-item">
                    <label class="detail-label">Jumlah Pembayaran</label>
                    <p class="detail-value large">Rp {{ number_format($booking->payment->payment_amount, 0, ',', '.') }}</p>
                </div>
                
                <div class="detail-item">
                    <label class="detail-label">Tanggal Pembayaran</label>
                    <p class="detail-value">
                        @if($booking->payment->payment_status == 'paid' || $booking->payment->payment_status == 'paid_dummy')
                            {{ $booking->payment->updated_at->format('d F Y H:i') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Action Buttons -->
        @if($booking->status == 'pending')
        <div class="actions-section">
            <div class="actions-header">
                <h3>Aksi</h3>
            </div>
            
            <div class="actions-content">
                <div class="action-buttons">
                    <form method="POST" action="{{ route('admin.booking.update-status', $booking->id) }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" onclick="return confirm('Setujui booking ini?')" class="btn-action btn-approve">
                            <i class="fas fa-check"></i>
                            Setujui Booking
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.booking.update-status', $booking->id) }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" onclick="return confirm('Tolak booking ini?')" class="btn-action btn-reject">
                            <i class="fas fa-times"></i>
                            Tolak Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Booking Timeline -->
        <div class="timeline-section">
            <div class="timeline-header">
                <h3>Timeline</h3>
            </div>
            
            <div class="timeline-content">
                <div class="timeline-item">
                    <div class="timeline-dot created"></div>
                    <div class="timeline-info">
                        <div class="timeline-date">{{ $booking->created_at->format('d F Y H:i') }}</div>
                        <p class="timeline-text">Booking dibuat</p>
                    </div>
                </div>
                
                @if($booking->status != 'pending')
                <div class="timeline-item">
                    <div class="timeline-dot {{ $booking->status == 'approved' ? 'approved' : 'rejected' }}"></div>
                    <div class="timeline-info">
                        <div class="timeline-date">{{ $booking->updated_at->format('d F Y H:i') }}</div>
                        <p class="timeline-text">Booking {{ $booking->status == 'approved' ? 'disetujui' : 'ditolak' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
