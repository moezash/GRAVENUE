@extends('layouts.app')

@section('title', 'Status Booking - Gravenue')

@section('content')
<!-- Booking Status -->
<section class="booking-status-section">
    <div class="container">
        <div class="status-container">
            <div class="status-header">
                <h1>Status Pengajuan Sewa</h1>
                <div class="booking-id">
                    <span>ID Booking: #{{ $booking->id }}</span>
                </div>
            </div>

            <div class="status-content">
                <!-- Status Badge -->
                <div class="status-badge {{ $booking->status }}">
                    @switch($booking->status)
                        @case('pending')
                            <i class="fas fa-clock"></i>
                            <span>Menunggu Persetujuan</span>
                            @break
                        @case('approved')
                            <i class="fas fa-check-circle"></i>
                            <span>Disetujui</span>
                            @break
                        @case('rejected')
                            <i class="fas fa-times-circle"></i>
                            <span>Ditolak</span>
                            @break
                        @case('cancelled')
                            <i class="fas fa-ban"></i>
                            <span>Dibatalkan</span>
                            @break
                    @endswitch
                </div>

                <!-- Booking Details -->
                <div class="booking-details">
                    <h2>Detail Booking</h2>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Fasilitas:</label>
                            <span>{{ $booking->facility->name }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Nama Penyewa:</label>
                            <span>{{ $booking->user_name }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Email:</label>
                            <span>{{ $booking->user_email }}</span>
                        </div>
                        <div class="detail-item">
                            <label>No. Telepon:</label>
                            <span>{{ $booking->user_phone }}</span>
                        </div>
                        @if($booking->organization)
                        <div class="detail-item">
                            <label>Organisasi:</label>
                            <span>{{ $booking->organization }}</span>
                        </div>
                        @endif
                        <div class="detail-item">
                            <label>Nama Acara:</label>
                            <span>{{ $booking->event_name }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Tanggal Mulai:</label>
                            <span>{{ \Carbon\Carbon::parse($booking->start_date)->format('d F Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <label>Tanggal Selesai:</label>
                            <span>{{ \Carbon\Carbon::parse($booking->end_date)->format('d F Y') }}</span>
                        </div>
                        @if($booking->start_time)
                        <div class="detail-item">
                            <label>Waktu Mulai:</label>
                            <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</span>
                        </div>
                        @endif
                        @if($booking->end_time)
                        <div class="detail-item">
                            <label>Waktu Selesai:</label>
                            <span>{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>
                        @endif
                        <div class="detail-item">
                            <label>Total Hari:</label>
                            <span>{{ $booking->total_days }} hari</span>
                        </div>
                        <div class="detail-item">
                            <label>Total Biaya:</label>
                            <span class="price">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                        @if($booking->additional_notes)
                        <div class="detail-item full-width">
                            <label>Keterangan Tambahan:</label>
                            <span>{{ $booking->additional_notes }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Status -->
                @if($booking->payment)
                <div class="payment-details">
                    <h2>Status Pembayaran</h2>
                    <div class="payment-status {{ $booking->payment->payment_status }}">
                        @switch($booking->payment->payment_status)
                            @case('pending')
                                <i class="fas fa-clock"></i>
                                <span>Menunggu Pembayaran</span>
                                @break
                            @case('paid_dummy')
                                <i class="fas fa-check-circle"></i>
                                <span>Sudah Dibayar (Dummy)</span>
                                @break
                            @case('paid_real')
                                <i class="fas fa-check-circle"></i>
                                <span>Sudah Dibayar</span>
                                @break
                            @case('failed')
                                <i class="fas fa-times-circle"></i>
                                <span>Pembayaran Gagal</span>
                                @break
                        @endswitch
                    </div>
                    
                    @if($booking->payment->payment_method)
                    <div class="payment-method">
                        <label>Metode Pembayaran:</label>
                        <span>{{ ucfirst(str_replace('_', ' ', $booking->payment->payment_method)) }}</span>
                    </div>
                    @endif
                    
                    @if($booking->payment->transaction_id)
                    <div class="transaction-id">
                        <label>ID Transaksi:</label>
                        <span>{{ $booking->payment->transaction_id }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Actions -->
                <div class="status-actions">
                    @if($booking->status === 'approved' && $booking->payment && $booking->payment->payment_status === 'pending')
                    <a href="{{ route('payment', $booking->id) }}" class="btn-primary">
                        <i class="fas fa-credit-card"></i>
                        Bayar Sekarang
                    </a>
                    @endif
                    
                    <a href="{{ route('facilities') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Fasilitas
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
