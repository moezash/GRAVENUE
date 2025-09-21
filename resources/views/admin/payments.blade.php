@extends('layouts.admin')

@section('title', 'Pembayaran')

@section('content')
<!-- Payments Table -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Daftar Pembayaran</h2>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Booking</th>
                    <th>Penyewa</th>
                    <th>Fasilitas</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>#{{ $payment->id }}</td>
                    <td>#{{ $payment->booking->id }}</td>
                    <td>
                        <strong>{{ $payment->booking->user_name }}</strong><br>
                        <small style="color: #64748b;">{{ $payment->booking->user_email }}</small>
                    </td>
                    <td>{{ $payment->booking->facility->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->booking->booking_date)->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($payment->payment_status == 'paid' || $payment->payment_status == 'paid_dummy')
                            <span class="status-badge status-approved">Paid</span>
                        @elseif($payment->payment_status == 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @elseif($payment->payment_status == 'failed')
                            <span class="status-badge status-rejected">Failed</span>
                        @else
                            <span class="status-badge status-cancelled">{{ ucfirst($payment->payment_status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($payment->payment_status == 'paid' || $payment->payment_status == 'paid_dummy')
                            {{ $payment->updated_at->format('d/m/Y H:i') }}
                        @else
                            <span style="color: #64748b;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-credit-card" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <br>
                        Tidak ada data pembayaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($payments->hasPages())
    <div style="padding: 24px; border-top: 1px solid #e2e8f0;">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
