@extends('layouts.app')

@section('title', 'Jadwal - Gravenue')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Jadwal Penyewaan</h1>
        <p>Lihat ketersediaan fasilitas dan jadwal penyewaan</p>
    </div>
</section>

<!-- Schedule Content -->
<section class="schedule-content">
    <div class="container">
        <div class="schedule-filters">
            <div class="filter-group">
                <label for="facility-select">Pilih Fasilitas:</label>
                <select id="facility-select" onchange="filterSchedule()">
                    <option value="">Semua Fasilitas</option>
                    @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="month-select">Pilih Bulan:</label>
                <input type="month" id="month-select" value="{{ date('Y-m') }}" onchange="filterSchedule()">
            </div>
        </div>
        
        <div class="calendar-container">
            <div class="calendar-header">
                <button onclick="previousMonth()" class="calendar-nav">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 id="current-month">{{ date('F Y') }}</h2>
                <button onclick="nextMonth()" class="calendar-nav">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="calendar-grid" id="calendar-grid">
                <!-- Calendar will be populated by JavaScript -->
            </div>
        </div>
        
        <div class="schedule-legend">
            <h3>Keterangan Status:</h3>
            <div class="legend-items">
                <div class="legend-item">
                    <span class="status-indicator available"></span>
                    <span>Tersedia</span>
                </div>
                <div class="legend-item">
                    <span class="status-indicator booked"></span>
                    <span>Disewa</span>
                </div>
                <div class="legend-item">
                    <span class="status-indicator maintenance"></span>
                    <span>Maintenance</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let currentDate = new Date();
let selectedFacility = '';

function filterSchedule() {
    selectedFacility = document.getElementById('facility-select').value;
    const selectedMonth = document.getElementById('month-select').value;
    currentDate = new Date(selectedMonth + '-01');
    generateCalendar();
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    document.getElementById('month-select').value = currentDate.getFullYear() + '-' + String(currentDate.getMonth() + 1).padStart(2, '0');
    generateCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    document.getElementById('month-select').value = currentDate.getFullYear() + '-' + String(currentDate.getMonth() + 1).padStart(2, '0');
    generateCalendar();
}

function generateCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update month display
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    document.getElementById('current-month').textContent = monthNames[month] + ' ' + year;
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    
    // Generate calendar grid
    const calendarGrid = document.getElementById('calendar-grid');
    calendarGrid.innerHTML = '';
    
    // Add day headers
    const dayHeaders = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        calendarGrid.appendChild(dayHeader);
    });
    
    // Add empty cells for days before month starts
    for (let i = 0; i < startingDayOfWeek; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'calendar-day empty';
        calendarGrid.appendChild(emptyCell);
    }
    
    // Add days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        dayCell.textContent = day;
        
        // Add click event for booking
        dayCell.onclick = () => {
            if (selectedFacility) {
                window.location.href = '{{ route("booking.form", ":id") }}'.replace(':id', selectedFacility) + '?date=' + year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            }
        };
        
        calendarGrid.appendChild(dayCell);
    }
}

// Initialize calendar on page load
document.addEventListener('DOMContentLoaded', function() {
    generateCalendar();
});
</script>
@endpush
