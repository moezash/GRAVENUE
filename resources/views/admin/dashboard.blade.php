@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon booking">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-number">{{ $stats['total_bookings'] }}</div>
        <div class="stat-label">Total Booking</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon facility">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-number">{{ $stats['total_facilities'] }}</div>
        <div class="stat-label">Total Fasilitas</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon payment">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-number">{{ $stats['approved_bookings'] }}</div>
        <div class="stat-label">Pembayaran Selesai</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-number">{{ $stats['pending_bookings'] }}</div>
        <div class="stat-label">Menunggu Konfirmasi</div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Booking Terbaru</h2>
        <a href="{{ route('admin.bookings') }}" class="card-action">
            <i class="fas fa-list"></i>
            Lihat Semua
        </a>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Penyewa</th>
                    <th>Fasilitas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recent_bookings'] as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user_name }}</td>
                    <td>{{ $booking->facility->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                    <td>
                        @if($booking->status == 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @elseif($booking->status == 'approved')
                            <span class="status-badge status-approved">Approved</span>
                        @elseif($booking->status == 'rejected')
                            <span class="status-badge status-rejected">Rejected</span>
                        @else
                            <span class="status-badge status-cancelled">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-view" onclick="window.location.href='{{ route('admin.booking.detail', $booking->id) }}'">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <br>
                        Tidak ada booking terbaru
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
