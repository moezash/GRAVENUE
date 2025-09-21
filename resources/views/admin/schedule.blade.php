@extends('layouts.admin')

@section('title', 'Jadwal Penyewaan')

@push('styles')
<style>
    .calendar-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .calendar-header {
        background: #1e293b;
        color: white;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .calendar-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .filter-section {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .filter-row {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        background: white;
        min-width: 150px;
    }

    .filter-btn {
        background: #f97316;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        font-size: 14px;
        margin-top: 18px;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border-bottom: 1px solid #e2e8f0;
    }

    .calendar-day-header {
        padding: 12px 8px;
        text-align: center;
        font-weight: 600;
        font-size: 12px;
        color: #64748b;
        background: #f8fafc;
        border-right: 1px solid #e2e8f0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .calendar-day-header:last-child {
        border-right: none;
    }

    .calendar-day {
        min-height: 100px;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px;
        position: relative;
        background: white;
    }

    .calendar-day:last-child {
        border-right: none;
    }

    .calendar-day.other-month {
        background: #f9fafb;
        color: #9ca3af;
    }

    /* Has booking styling - HIGHEST PRIORITY */
    .calendar-container .calendar-grid .calendar-day.has-booking {
        background-color: #dcfce7 !important;
        background: #dcfce7 !important;
    }

    /* Today styling */
    .calendar-container .calendar-grid .calendar-day.today {
        background-color: #fef3c7 !important;
        background: #fef3c7 !important;
        border: 2px solid #f59e0b !important;
    }

    /* Combined today + has booking */
    .calendar-container .calendar-grid .calendar-day.has-booking.today {
        background: linear-gradient(135deg, #fef3c7 0%, #dcfce7 100%) !important;
        background-color: #dcfce7 !important;
        border: 2px solid #f59e0b !important;
    }

    /* Force override any conflicting styles with maximum specificity */
    div.calendar-container div.calendar-grid div.calendar-day[data-has-booking="true"] {
        background: #dcfce7 !important;
        background-color: #dcfce7 !important;
    }

    div.calendar-container div.calendar-grid div.calendar-day[data-is-today="true"] {
        background: #fef3c7 !important;
        background-color: #fef3c7 !important;
        border: 2px solid #f59e0b !important;
    }

    div.calendar-container div.calendar-grid div.calendar-day[data-has-booking="true"][data-is-today="true"] {
        background: linear-gradient(135deg, #fef3c7 0%, #dcfce7 100%) !important;
        background-color: #dcfce7 !important;
        border: 2px solid #f59e0b !important;
    }

    /* Additional ultra-specific selectors */
    .calendar-container .calendar-grid .calendar-day.has-booking[data-has-booking="true"] {
        background: #dcfce7 !important;
        background-color: #dcfce7 !important;
    }

    .calendar-container .calendar-grid .calendar-day.today[data-is-today="true"] {
        background: #fef3c7 !important;
        background-color: #fef3c7 !important;
        border: 2px solid #f59e0b !important;
    }

    .day-number {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
        color: #1e293b;
    }

    .day-events {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .event-item {
        padding: 3px 6px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 500;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        line-height: 1.2;
        margin-bottom: 1px;
    }

    .event-available {
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .event-booked {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .event-maintenance {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .legend {
        padding: 16px 24px;
        background: #f8fafc;
        display: flex;
        gap: 24px;
        justify-content: center;
        flex-wrap: wrap;
        border-top: 1px solid #e2e8f0;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #64748b;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        border: 1px solid;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-item {
        background: white;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .calendar-grid {
            font-size: 12px;
        }
        
        .calendar-day {
            min-height: 80px;
            padding: 4px;
        }
        
        .filter-row {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-select {
            min-width: auto;
        }
    }
</style>
@endpush

@section('content')
<!-- Stats Row -->
@php
    $bookedSchedulesStats = $schedules->flatten()->where('status', 'booked');
    $totalRevenue = $bookedSchedulesStats->sum(function($schedule) { 
        return $schedule->booking ? $schedule->booking->total_price : 0; 
    });
    $approvedBookings = $bookedSchedulesStats->where('booking.status', 'approved');
@endphp
<div class="stats-row">
    <div class="stat-item">
        <div class="stat-number">{{ $bookedSchedulesStats->count() }}</div>
        <div class="stat-label">Total Booking</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">{{ $facilities->count() }}</div>
        <div class="stat-label">Total Fasilitas</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-label">Total Pembayaran</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">{{ $approvedBookings->count() }}</div>
        <div class="stat-label">Booking Disetujui</div>
    </div>
</div>

<!-- Calendar Container -->
<div class="calendar-container">
    <div class="calendar-header">
        <h2 class="calendar-title">Jadwal Penyewaan</h2>
        <div style="color: rgba(255,255,255,0.8); font-size: 14px;">
            Selamat datang, Administrator | Hari ini: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y') }}
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.schedule') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Pilih Bulan</label>
                    <input type="month" name="month" class="filter-select" value="{{ request('month', now()->setTimezone('Asia/Jakarta')->format('Y-m')) }}">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Pilih Fasilitas</label>
                    <select name="facility_id" class="filter-select">
                        <option value="">Semua Fasilitas</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ $selectedFacility == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Calendar Grid -->
    <div class="calendar-grid">
        <div class="calendar-day-header">Sen</div>
        <div class="calendar-day-header">Sel</div>
        <div class="calendar-day-header">Rab</div>
        <div class="calendar-day-header">Kam</div>
        <div class="calendar-day-header">Jum</div>
        <div class="calendar-day-header">Sab</div>
        <div class="calendar-day-header">Ming</div>
    </div>
    
    <div class="calendar-grid">
        @php
            $now = now()->setTimezone('Asia/Jakarta');
            $selectedMonth = request('month', $now->format('Y-m'));
            $monthStart = \Carbon\Carbon::parse($selectedMonth . '-01')->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $currentDate = $monthStart->copy()->startOfWeek();
            $today = $now;
        @endphp
        
        @for($week = 0; $week < 6; $week++)
            @for($day = 0; $day < 7; $day++)
                @php
                    $isCurrentMonth = $currentDate->month == $monthStart->month;
                    $isToday = $currentDate->isSameDay($today);
                    $daySchedules = $schedules->get($currentDate->format('Y-m-d'), collect());
                    $bookedSchedules = $daySchedules->where('status', 'booked');
                    $hasBooking = $bookedSchedules->count() > 0;
                @endphp
                
                <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }} {{ $hasBooking ? 'has-booking' : '' }}" 
                     data-date="{{ $currentDate->format('Y-m-d') }}" 
                     data-has-booking="{{ $hasBooking ? 'true' : 'false' }}"
                     data-is-today="{{ $isToday ? 'true' : 'false' }}"
                     data-schedules-count="{{ $daySchedules->count() }}"
                     data-booked-count="{{ $bookedSchedules->count() }}"
                     style="{{ $hasBooking && $isToday ? 'background: linear-gradient(135deg, #fef3c7 0%, #dcfce7 100%) !important; border: 2px solid #f59e0b !important;' : ($hasBooking ? 'background: #dcfce7 !important; background-color: #dcfce7 !important;' : ($isToday ? 'background: #fef3c7 !important; border: 2px solid #f59e0b !important;' : '')) }}">
                    <div class="day-number">{{ $currentDate->day }}</div>
                    <div class="day-events">
                        @if($isCurrentMonth && $hasBooking)
                            @foreach($bookedSchedules->take(3) as $schedule)
                                <div class="event-item event-booked"
                                     title="{{ $schedule->facility->name }} - {{ $schedule->booking->event_name }} ({{ $schedule->booking->user_name }})">
                                    <strong>{{ Str::limit($schedule->facility->name, 8) }}</strong>: {{ Str::limit($schedule->booking->event_name, 10) }}
                                </div>
                            @endforeach
                            @if($bookedSchedules->count() > 3)
                                <div class="event-item" style="background: #e5e7eb; color: #6b7280; font-size: 9px;">
                                    +{{ $bookedSchedules->count() - 3 }} lainnya
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                
                @php $currentDate->addDay(); @endphp
            @endfor
            
            @if($currentDate->month != $monthStart->month && $currentDate->month != $monthEnd->month)
                @break
            @endif
        @endfor
    </div>
    
    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background: #dcfce7; border-color: #bbf7d0;"></div>
            <span>Ada Booking</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #fef3c7; border-color: #fde68a;"></div>
            <span>Hari Ini</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #f3f4f6; border-color: #d1d5db;"></div>
            <span>Tidak Ada Booking</span>
        </div>
    </div>
</div>

<!-- Booking Details Section -->
<div style="margin-top: 32px;">
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Daftar Booking Periode Ini</h2>
        </div>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Fasilitas</th>
                        <th>Acara</th>
                        <th>Penyewa</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bookedSchedules = $schedules->flatten()->where('status', 'booked')->sortBy('date');
                    @endphp
                    @forelse($bookedSchedules as $schedule)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $schedule->facility->name }}</strong><br>
                                <small style="color: #64748b;">{{ $schedule->facility->category ?: 'Event Space' }}</small>
                            </td>
                            <td>{{ $schedule->booking->event_name }}</td>
                            <td>
                                <strong>{{ $schedule->booking->user_name }}</strong><br>
                                <small style="color: #64748b;">{{ $schedule->booking->organization ?: '-' }}</small>
                            </td>
                            <td>
                                {{ $schedule->booking->start_time }} - {{ $schedule->booking->end_time }}<br>
                                <small style="color: #64748b;">{{ $schedule->booking->participants }} peserta</small>
                            </td>
                            <td>
                                @if($schedule->booking->status == 'approved')
                                    <span class="status-badge status-approved">Approved</span>
                                @elseif($schedule->booking->status == 'pending')
                                    <span class="status-badge status-pending">Pending</span>
                                @else
                                    <span class="status-badge status-rejected">{{ ucfirst($schedule->booking->status) }}</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($schedule->booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                                <br>
                                Tidak ada booking pada periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookedSchedules->count() > 0)
        <div style="padding: 20px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <strong>Total Booking: {{ $bookedSchedules->count() }}</strong>
                </div>
                <div>
                    <strong>Total Revenue: Rp {{ number_format($bookedSchedules->sum(function($schedule) { return $schedule->booking->total_price; }), 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

