@extends('layouts.app')

@section('title', 'Gravenue - Penyewaan Sarana SMKN 4 Malang')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-container">
            <div class="hero-content">
                <h1 class="hero-title">
                    GRA<br>
                    VENUE
                </h1>
                <button class="hero-btn" onclick="window.location.href='{{ route('facilities') }}'">SEWA RUANGAN</button>
                
                <div class="hero-description">
                    <h3>Platform Penyewaan Fasilitas Terpercaya</h3>
                    <p>Gravenue hadir untuk memfasilitasi kebutuhan Anda dalam menyewa ruang & fasilitas berkualitas di SMKN 4 Malang. Dengan berbagai pilihan ruangan seperti aula, laboratorium, auditorium, hall serbaguna hingga lapangan, tempat yang nyaman dan lengkap dengan fasilitas pendukung yang memadai untuk berbagai kegiatan Anda. Sistem pemesanan online yang mudah dan transparan membuat pengalaman sewa menjadi lebih efisien dan menyenangkan.</p>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('assets/images/smkn4-building.png') }}" alt="SMKN 4 Malang Building" onerror="this.src='https://picsum.photos/800/600?random=1'">
            </div>
        </div>
    </div>
</section>

<!-- Our Space Section -->
<section class="our-space">
    <div class="container">
        <h2>Ruang Kami</h2>
        <div class="space-stats">
            <div class="stat-item">
                <h3>07</h3>
                <p>Ruang Acara</p>
            </div>
            <div class="stat-item">
                <h3>34</h3>
                <p>Ruang Kelas</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">Mengapa Memilih Gravenue?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Fasilitas Lengkap</h3>
                <p>Berbagai pilihan ruangan dengan fasilitas modern dan lengkap untuk semua kebutuhan Anda</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Pelayanan 24/7</h3>
                <p>Sistem pemesanan online yang dapat diakses kapan saja, dimana saja untuk kemudahan Anda</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Terpercaya</h3>
                <p>Platform yang aman dan terpercaya untuk semua kebutuhan penyewaan fasilitas Anda</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h3>Harga Terjangkau</h3>
                <p>Harga yang kompetitif dan transparan untuk semua fasilitas berkualitas tinggi</p>
            </div>
        </div>
    </div>
</section>

<!-- Facilities Preview -->
<section class="facilities-preview">
    <div class="container">
        <div class="facility-content">
            <img src="https://picsum.photos/600/400?random=2" alt="Facility Preview" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDYwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI2MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjZjhmOWZhIi8+CjxyZWN0IHg9IjE1MCIgeT0iMTAwIiB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI0ZGNzg0NCIvPgo8dGV4dCB4PSIzMDAiIHk9IjIxMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE4IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+RmFjaWxpdHkgUHJldmlldzwvdGV4dD4KPC9zdmc+">
            <div class="facility-info">
                <h3>Fasilitas Gravenue di SMK Negeri 4 Malang</h3>
                <ul>
                    <li>Ruangan Ber-AC Lengkap</li>
                    <li>Layanan Katering</li>
                    <li>Aula Multifungsi</li>
                    <li>Sistem Pemesanan Profesional dan Transparan</li>
                    <li>Harga Terjangkau dan Mudah Diakses</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- History Section -->
<section class="history">
    <div class="container">
        <h2>Sejarah</h2>
        <div class="history-content">
            <img src="https://picsum.photos/400/300?random=3" alt="Sejarah SMKN 4 Malang" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjZjhmOWZhIi8+CjxyZWN0IHg9IjEwMCIgeT0iNzUiIHdpZHRoPSIyMDAiIGhlaWdodD0iMTUwIiBmaWxsPSIjNTMzNTRBIi8+Cjx0ZXh0IHg9IjIwMCIgeT0iMTU4IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5TZWphcmFoPC90ZXh0Pgo8L3N2Zz4='">
            <div class="history-text">
                <p>Gravenue hadir sebagai platform digital inovatif yang dirancang khusus untuk memfasilitasi penyewaan ruang & fasilitas di SMKN 4 Malang yang berkualitas dan terpercaya. Dengan komitmen untuk memberikan layanan terbaik, Gravenue di SMKN 4 Malang telah menjadi pilihan utama bagi berbagai kalangan yang membutuhkan ruang berkualitas untuk acara mereka.</p>
                
                <p>Sejak didirikan, kami terus berinovasi dalam menyediakan fasilitas yang tidak hanya modern dan lengkap, tetapi juga mudah diakses melalui sistem pemesanan online yang transparan dan efisien. Kepercayaan klien adalah prioritas utama kami, sehingga setiap detail layanan dirancang untuk memberikan pengalaman terbaik bagi setiap pengguna.</p>
            </div>
        </div>
    </div>
</section>
@endsection
