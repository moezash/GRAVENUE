@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<!-- Filter Section -->
<div class="content-card" style="margin-bottom: 32px;">
    <div style="padding: 24px;">
        <form method="GET" action="{{ route('admin.reports') }}" style="display: flex; gap: 16px; align-items: end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
            </div>
            
            <button type="submit" class="btn-primary" style="height: fit-content;">
                <i class="fas fa-search"></i>
                Generate Laporan
            </button>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon booking">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-number">{{ $bookings->count() }}</div>
        <div class="stat-label">Total Booking</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon payment">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-number">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
        <div class="stat-label">Total Pendapatan</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon facility">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-number">{{ $bookings->where('status', 'approved')->count() }}</div>
        <div class="stat-label">Booking Disetujui</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-number">{{ $bookings->where('status', 'pending')->count() }}</div>
        <div class="stat-label">Menunggu Konfirmasi</div>
    </div>
</div>

<!-- Facility Usage Report -->
<div class="content-card" style="margin-bottom: 32px;">
    <div class="card-header">
        <h2 class="card-title">Penggunaan Fasilitas</h2>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fasilitas</th>
                    <th>Jumlah Booking</th>
                    <th>Total Pendapatan</th>
                    <th>Tingkat Penggunaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilityUsage as $usage)
                <tr>
                    <td>
                        <strong>{{ $usage['facility']->name }}</strong><br>
                        <small style="color: #64748b;">{{ $usage['facility']->category }}</small>
                    </td>
                    <td>{{ $usage['count'] }} booking</td>
                    <td>Rp {{ number_format($usage['revenue'], 0, ',', '.') }}</td>
                    <td>
                        @php
                            $percentage = $bookings->count() > 0 ? ($usage['count'] / $bookings->count()) * 100 : 0;
                        @endphp
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="flex: 1; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; background: #f97316; width: {{ $percentage }}%;"></div>
                            </div>
                            <span style="font-size: 12px; color: #64748b;">{{ number_format($percentage, 1) }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <br>
                        Tidak ada data penggunaan fasilitas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Booking Details -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Detail Booking</h2>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Penyewa</th>
                    <th>Fasilitas</th>
                    <th>Acara</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $booking->user_name }}</strong><br>
                        <small style="color: #64748b;">{{ $booking->organization }}</small>
                    </td>
                    <td>{{ $booking->facility->name }}</td>
                    <td>{{ $booking->event_name }}</td>
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
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <br>
                        Tidak ada data booking untuk periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Export Options -->
<div style="margin-top: 32px; text-align: center;">
    <p style="color: #64748b; margin-bottom: 16px;">Periode Laporan: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
        <button onclick="window.print()" class="btn-primary">
            <i class="fas fa-print"></i>
            Cetak Laporan
        </button>
        <button onclick="exportToCSV()" style="background: #22c55e; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-download"></i>
            Export CSV
        </button>
    </div>
</div>

@push('scripts')
<script>
function exportToCSV() {
    // Simple CSV export - in real app, you'd implement server-side export
    alert('Fitur export CSV akan segera tersedia');
}

// Print styles
const printStyles = `
    @media print {
        .sidebar, .top-bar, .card-action, .btn-primary, button {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .content-area {
            padding: 0 !important;
        }
        .content-card {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            margin-bottom: 20px !important;
        }
        body {
            font-size: 12px !important;
        }
    }
`;

// Add print styles to head
const styleSheet = document.createElement('style');
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);
</script>
@endpush
@endsection
