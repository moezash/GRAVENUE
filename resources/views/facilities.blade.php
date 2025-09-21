@extends('layouts.app')

@section('title', 'Fasilitas - Gravenue')

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Fasilitas Tersedia</h1>
        <p>Pilih fasilitas yang sesuai dengan kebutuhan acara Anda</p>
    </div>
</section>

<!-- Filters -->
<section class="filters">
    <div class="container">
        <div class="filter-buttons">
            <button class="filter-btn {{ request('category') == 'all' || !request('category') ? 'active' : '' }}" data-filter="all">Semua</button>
            <button class="filter-btn {{ request('category') == 'Event Space' ? 'active' : '' }}" data-filter="Event Space">Ruang Acara</button>
            <button class="filter-btn {{ request('category') == 'Classroom' ? 'active' : '' }}" data-filter="Classroom">Ruang Kelas</button>
            <button class="filter-btn {{ request('category') == 'Laboratory' ? 'active' : '' }}" data-filter="Laboratory">Laboratorium</button>
            <button class="filter-btn {{ request('category') == 'Computer Lab' ? 'active' : '' }}" data-filter="Computer Lab">Lab Komputer</button>
            <button class="filter-btn {{ request('category') == 'Language Lab' ? 'active' : '' }}" data-filter="Language Lab">Lab Bahasa</button>
            <button class="filter-btn {{ request('category') == 'Sports' ? 'active' : '' }}" data-filter="Sports">Olahraga</button>
            <button class="filter-btn {{ request('category') == 'Entertainment' ? 'active' : '' }}" data-filter="Entertainment">Hiburan</button>
            <button class="filter-btn {{ request('category') == 'Cafe' ? 'active' : '' }}" data-filter="Cafe">Kafe</button>
        </div>
    </div>
</section>

<!-- Facilities Grid -->
<section class="facilities-grid">
    <div class="container">
        <div class="grid">
            @foreach($facilities as $facility)
            <div class="facility-card" data-category="{{ $facility->category }}">
                <div class="facility-image">
                    <img src="{{ $facility->image ? asset('storage/' . $facility->image) : 'https://picsum.photos/400/300?random=' . $facility->id }}"
                         alt="{{ $facility->name }}"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjZjhmOWZhIi8+CjxyZWN0IHg9IjEwMCIgeT0iNzUiIHdpZHRoPSIyMDAiIGhlaWdodD0iMTUwIiBmaWxsPSIjRkY3ODQ0Ii8+Cjx0ZXh0IHg9IjIwMCIgeT0iMTU4IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5GYWNpbGl0eTwvdGV4dD4KPC9zdmc+'">
                    <div class="facility-status available">
                        <i class="fas fa-check-circle"></i> Tersedia
                    </div>
                </div>
                <div class="facility-info">
                    <h3>{{ $facility->name }}</h3>
                    <p class="facility-description">{{ $facility->description }}</p>
                    <div class="facility-details">
                        <div class="detail-item">
                            <i class="fas fa-users"></i>
                            <span>Kapasitas: {{ $facility->capacity }} orang</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-tag"></i>
                            <span>Rp {{ number_format($facility->price_per_hour, 0, ',', '.') }}/jam</span>
                        </div>
                        @if($facility->features)
                        <div class="detail-item">
                            <i class="fas fa-star"></i>
                            <span>{{ $facility->features }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="facility-actions">
                        <button class="btn-primary" onclick="bookFacility({{ $facility->id }})">
                            <i class="fas fa-calendar-plus"></i> Ajukan Sewa
                        </button>
                        <button class="btn-secondary" onclick="viewDetails({{ $facility->id }})">
                            <i class="fas fa-info-circle"></i> Detail
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function bookFacility(facilityId) {
    @auth
        window.location.href = '{{ route("booking.form", ":id") }}'.replace(':id', facilityId);
    @else
        // Store intended URL and redirect to login
        sessionStorage.setItem('intended_url', '{{ route("booking.form", ":id") }}'.replace(':id', facilityId));
        window.location.href = '{{ route("login") }}?message=login_required';
    @endauth
}

function viewDetails(facilityId) {
    window.location.href = '{{ route("facility", ":id") }}'.replace(':id', facilityId);
}

// Filter functionality
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');

        // Update active button
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Filter facilities
        document.querySelectorAll('.facility-card').forEach(card => {
            if (filter === 'all' || card.getAttribute('data-category') === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Update URL
        const url = new URL(window.location);
        if (filter === 'all') {
            url.searchParams.delete('category');
        } else {
            url.searchParams.set('category', filter);
        }
        window.history.pushState({}, '', url);
    });
});
</script>
@endpush
