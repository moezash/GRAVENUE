@extends('layouts.app')

@section('title', 'Pembayaran - Gravenue')

@section('content')
<!-- Payment Section -->
<section class="payment-section">
    <div class="container">
        <div class="payment-container">
            <div class="payment-header">
                <h1>Pembayaran</h1>
                <div class="booking-info">
                    <span>ID Booking: #{{ $booking->id }}</span>
                    <span>{{ $booking->facility->name }}</span>
                </div>
            </div>

            <div class="payment-content">
                <!-- Payment Summary -->
                <div class="payment-summary">
                    <h2>Ringkasan Pembayaran</h2>
                    <div class="summary-details">
                        <div class="summary-item">
                            <span>Fasilitas:</span>
                            <span>{{ $booking->facility->name }}</span>
                        </div>
                        <div class="summary-item">
                            <span>Periode:</span>
                            <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d F Y') }}
                            @if($booking->end_date)
                                - {{ \Carbon\Carbon::parse($booking->end_date)->format('d F Y') }}
                            @endif
                            </span>
                        </div>
                        <div class="summary-item">
                            <span>Durasi:</span>
                            <span>
                                @if($booking->start_time && $booking->end_time)
                                    @php
                                        $startTime = \Carbon\Carbon::createFromFormat('H:i', $booking->start_time);
                                        $endTime = \Carbon\Carbon::createFromFormat('H:i', $booking->end_time);
                                        $hours = ceil($startTime->diffInMinutes($endTime) / 60);
                                    @endphp
                                    {{ $hours }} jam
                                @else
                                    1 jam
                                @endif
                            </span>
                        </div>
                        <div class="summary-item">
                            <span>Harga per jam:</span>
                            <span>Rp {{ number_format($booking->facility->price_per_hour, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-item total">
                            <span>Total Pembayaran:</span>
                            <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="payment-form">
                    <h2>Pembayaran dengan Midtrans</h2>
                    <p class="payment-note">
                        <i class="fas fa-info-circle"></i>
                        Sistem pembayaran menggunakan Midtrans Sandbox. Anda dapat memilih berbagai metode pembayaran.
                    </p>

                    @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="payment-info">
                        <div class="payment-details">
                            <h3>Detail Pembayaran</h3>
                            <div class="detail-item">
                                <span>Order ID:</span>
                                <span>{{ $booking->payment->transaction_id ?? 'Belum dibuat' }}</span>
                            </div>
                            <div class="detail-item">
                                <span>Status:</span>
                                <span class="status-{{ $booking->payment->payment_status }}">
                                    {{ ucfirst($booking->payment->payment_status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="midtrans-info">
                            <div class="info-card">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <h4>Pembayaran Aman</h4>
                                    <p>Diproses oleh Midtrans dengan enkripsi SSL</p>
                                </div>
                            </div>
                            <div class="info-card">
                                <i class="fas fa-credit-card"></i>
                                <div>
                                    <h4>Multi Payment</h4>
                                    <p>Kartu Kredit, E-Wallet, Bank Transfer, QRIS</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="payment-actions">
                        <button id="pay-button" class="btn-primary">
                            <i class="fas fa-credit-card"></i>
                            Bayar Sekarang
                        </button>
                        <a href="{{ route('booking.status', $booking->id) }}" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Status Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<!-- Include Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');
    
    payButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        @if(isset($snapToken))
        // Trigger snap popup
        snap.pay('{{ $snapToken }}', {
            // Optional
            onSuccess: function(result) {
                console.log('Payment success:', result);
                alert('Pembayaran berhasil!');
                // Redirect to booking status
                window.location.href = '{{ route("booking.status", $booking->id) }}';
            },
            // Optional
            onPending: function(result) {
                console.log('Payment pending:', result);
                alert('Pembayaran sedang diproses!');
                // Redirect to booking status
                window.location.href = '{{ route("booking.status", $booking->id) }}';
            },
            // Optional
            onError: function(result) {
                console.log('Payment error:', result);
                alert('Pembayaran gagal!');
            },
            // Optional
            onClose: function() {
                console.log('Payment popup closed');
            }
        });
        @else
        alert('Token pembayaran belum tersedia. Silakan refresh halaman.');
        @endif
    });
});
</script>
@endpush
