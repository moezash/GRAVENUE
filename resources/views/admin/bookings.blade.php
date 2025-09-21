@extends('layouts.admin')

@section('title', 'Kelola Booking')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-number">{{ $bookings->where('status', 'pending')->count() }}</div>
        <div class="stat-label">Menunggu Konfirmasi</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon booking">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-number">{{ $bookings->where('status', 'approved')->count() }}</div>
        <div class="stat-label">Disetujui</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon facility">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-number">{{ $bookings->where('status', 'rejected')->count() }}</div>
        <div class="stat-label">Ditolak</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon payment">
            <i class="fas fa-ban"></i>
        </div>
        <div class="stat-number">{{ $bookings->where('status', 'cancelled')->count() }}</div>
        <div class="stat-label">Dibatalkan</div>
    </div>
</div>

<!-- Bookings Table -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Daftar Booking</h2>
    </div>
    
    <!-- Search and Filter -->
    <div class="search-filter-bar" style="padding: 0 24px; margin-top: 20px;">
        <form method="GET" action="{{ route('admin.bookings') }}" style="display: flex; gap: 16px; align-items: center; width: 100%;">
            <select name="status" class="filter-select">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <input type="text" name="search" class="search-input" placeholder="Cari Booking..." value="{{ request('search') }}">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Penyewa</th>
                    <th>Fasilitas</th>
                    <th>Acara</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>
                        <strong>{{ $booking->user_name }}</strong><br>
                        <small style="color: #64748b;">{{ $booking->user_email }}</small>
                    </td>
                    <td>{{ $booking->facility->name }}</td>
                    <td>{{ $booking->event_name }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                        @if($booking->end_date && $booking->end_date != $booking->booking_date)
                            - {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                        @endif
                        <br>
                        <small style="color: #64748b;">
                            {{ $booking->start_time }} - {{ $booking->end_time }}
                        </small>
                    </td>
                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
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
                    <td>
                        @if($booking->payment)
                            @if($booking->payment->payment_status == 'paid' || $booking->payment->payment_status == 'paid_dummy')
                                <span class="status-badge status-approved">Paid dummy</span>
                            @else
                                <span class="status-badge status-pending">{{ ucfirst($booking->payment->payment_status) }}</span>
                            @endif
                        @else
                            <span class="status-badge status-cancelled">No Payment</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-view" onclick="window.location.href='{{ route('admin.booking.detail', $booking->id) }}'">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($booking->status == 'pending')
                                <form method="POST" action="{{ route('admin.booking.update-status', $booking->id) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="action-btn btn-edit" onclick="return confirm('Setujui booking ini?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.booking.update-status', $booking->id) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="action-btn btn-delete" onclick="return confirm('Tolak booking ini?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <br>
                        Tidak ada data booking
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($bookings->hasPages())
    <div style="padding: 24px; border-top: 1px solid #e2e8f0;">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection