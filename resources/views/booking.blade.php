@extends('layouts.app')

@section('title', 'Booking - ' . $facility->name)

@section('content')
<!-- Booking Form -->
<section class="booking-section">
    <div class="container">
        <div class="booking-container">
            <!-- Facility Info -->
            <div class="facility-summary">
                <h2>Detail Fasilitas</h2>
                <div class="facility-card-summary">
                    <img src="{{ $facility->image ? asset('storage/' . $facility->image) : 'https://picsum.photos/400/300?random=' . $facility->id }}" 
                         alt="{{ $facility->name }}"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjZjhmOWZhIi8+CjxyZWN0IHg9IjEwMCIgeT0iNzUiIHdpZHRoPSIyMDAiIGhlaWdodD0iMTUwIiBmaWxsPSIjRkY3ODQ0Ii8+Cjx0ZXh0IHg9IjIwMCIgeT0iMTU4IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5GYWNpbGl0eTwvdGV4dD4KPC9zdmc+'">
                    <div class="facility-info">
                        <h3>{{ $facility->name }}</h3>
                        <p>{{ $facility->description }}</p>
                        <div class="facility-details">
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <span>Kapasitas: {{ $facility->capacity }} orang</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-tag"></i>
                                <span>Rp {{ number_format($facility->price_per_day, 0, ',', '.') }}/hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="booking-form">
                <h2>Form Pengajuan Sewa</h2>
                
                @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <div class="alert-actions">
                        <a href="{{ route('facilities') }}" class="btn-secondary">Kembali ke Fasilitas</a>
                    </div>
                </div>
                @endif
                
                @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('booking.submit') }}" class="form">
                    @csrf
                    <input type="hidden" name="facility_id" value="{{ $facility->id }}">
                    
                    <div class="form-group">
                        <label for="user_name">Nama Lengkap *</label>
                        <input type="text" id="user_name" name="user_name" value="{{ old('user_name', Auth::user()->name ?? '') }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="user_email">Email *</label>
                            <input type="email" id="user_email" name="user_email" value="{{ old('user_email', Auth::user()->email ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="user_phone">No. Telepon *</label>
                            <input type="tel" id="user_phone" name="user_phone" value="{{ old('user_phone', Auth::user()->phone ?? '') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="organization">Organisasi/Instansi</label>
                        <input type="text" id="organization" name="organization" value="{{ old('organization') }}">
                    </div>

                    <div class="form-group">
                        <label for="event_name">Nama Acara *</label>
                        <input type="text" id="event_name" name="event_name" value="{{ old('event_name') }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="booking_date">Tanggal Mulai *</label>
                            <input type="date" id="booking_date" name="booking_date" value="{{ old('booking_date', request('date')) }}" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="end_date">Tanggal Selesai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}">
                            <small class="form-help">Kosongkan jika hanya satu hari</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_time">Waktu Mulai</label>
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}">
                        </div>
                        <div class="form-group">
                            <label for="end_time">Waktu Selesai</label>
                            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="participants">Jumlah Peserta *</label>
                        <input type="number" id="participants" name="participants" value="{{ old('participants', 1) }}" required min="1" max="1000">
                    </div>

                    <div class="form-group">
                        <label for="additional_notes">Keterangan Tambahan</label>
                        <textarea id="additional_notes" name="additional_notes" rows="4" 
                                  placeholder="Jelaskan kebutuhan khusus atau informasi tambahan...">{{ old('additional_notes') }}</textarea>
                    </div>

                    <div class="price-summary">
                        <div class="price-item">
                            <span>Harga per jam:</span>
                            <span>Rp {{ number_format($facility->price_per_hour, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-item">
                            <span>Total jam:</span>
                            <span id="total-hours">1 jam</span>
                        </div>
                        <div class="price-item total">
                            <span>Total Biaya:</span>
                            <span id="total-price">Rp {{ number_format($facility->price_per_hour, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Calculate total price based on time
const startTimeInput = document.getElementById('start_time');
const endTimeInput = document.getElementById('end_time');
const totalHoursSpan = document.getElementById('total-hours');
const totalPriceSpan = document.getElementById('total-price');
const pricePerHour = {{ $facility->price_per_hour }};

function calculateTotal() {
    const startTime = startTimeInput.value;
    const endTime = endTimeInput.value;
    
    if (startTime && endTime) {
        // Parse time strings (HH:MM format)
        const [startHour, startMin] = startTime.split(':').map(Number);
        const [endHour, endMin] = endTime.split(':').map(Number);
        
        // Convert to minutes for easier calculation
        const startTotalMin = startHour * 60 + startMin;
        const endTotalMin = endHour * 60 + endMin;
        
        if (endTotalMin > startTotalMin) {
            // Calculate difference in hours (rounded up to nearest hour)
            const diffMin = endTotalMin - startTotalMin;
            const totalHours = Math.ceil(diffMin / 60);
            const totalPrice = totalHours * pricePerHour;
            
            totalHoursSpan.textContent = totalHours + ' jam';
            totalPriceSpan.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
        } else {
            // Invalid time range
            totalHoursSpan.textContent = '1 jam';
            totalPriceSpan.textContent = 'Rp ' + pricePerHour.toLocaleString('id-ID');
        }
    } else {
        // Default to 1 hour if no time selected
        totalHoursSpan.textContent = '1 jam';
        totalPriceSpan.textContent = 'Rp ' + pricePerHour.toLocaleString('id-ID');
    }
}

startTimeInput.addEventListener('change', calculateTotal);
endTimeInput.addEventListener('change', calculateTotal);

// Initialize calculation
calculateTotal();
</script>
@endpush