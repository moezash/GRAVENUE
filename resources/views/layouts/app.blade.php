<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gravenue - Penyewaan Sarana SMKN 4 Malang')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @if(request()->routeIs('facilities') || request()->routeIs('facility'))
        <link rel="stylesheet" href="{{ asset('assets/css/facilities.css') }}">
    @endif

    @if(request()->routeIs('schedule'))
        <link rel="stylesheet" href="{{ asset('assets/css/schedule.css') }}">
    @endif

    @if(request()->routeIs('booking.form'))
        <link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">
    @endif

    @if(request()->routeIs('booking.status'))
        <link rel="stylesheet" href="{{ asset('assets/css/booking-status.css') }}">
    @endif

    @if(request()->routeIs('dashboard'))
        <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    @endif

    @if(request()->routeIs('login') || request()->routeIs('register'))
        <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    @endif

    @if(request()->routeIs('about') || request()->routeIs('contact'))
        <link rel="stylesheet" href="{{ asset('assets/css/pages.css') }}">
    @endif

    @if(request()->routeIs('payment'))
        <link rel="stylesheet" href="{{ asset('assets/css/payment.css') }}">
    @endif

    @if(request()->routeIs('booking.status'))
        <link rel="stylesheet" href="{{ asset('assets/css/booking-status.css') }}">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @yield('additional_css')
    
    <!-- Global Consistent Margin System -->
    <style>
        /* SISTEM MARGIN KONSISTEN UNTUK SEMUA HALAMAN */
        * {
            box-sizing: border-box;
        }
        
        /* Container utama yang konsisten */
        .container {
            max-width: 1300px !important;
            margin: 0 auto !important;
            padding: 0 30px !important;
        }
        
        /* Semua section menggunakan container yang sama */
        section {
            width: 100% !important;
        }
        
        section .container {
            max-width: 1300px !important;
            margin: 0 auto !important;
            padding: 0 30px !important;
        }
        
        /* Override untuk halaman spesifik */
        .hero-section .container,
        .facilities-grid .container,
        .booking-status-section .container,
        .auth-section .container,
        .dashboard-section .container,
        .about-content .container,
        .contact-content .container {
            max-width: 1300px !important;
            margin: 0 auto !important;
            padding: 0 30px !important;
        }
        
        /* Grid facilities sejajar */
        .grid {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Dashboard cards sejajar */
        .dashboard-header,
        .stats-section {
            margin: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        /* Booking status sejajar */
        .status-container {
            max-width: 1300px !important;
            margin: 0 auto !important;
            padding: 0 30px !important;
        }
        
        /* Auth pages sejajar */
        .auth-container {
            max-width: 1300px !important;
            margin: 0 auto !important;
            padding: 0 30px !important;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .container,
            section .container,
            .hero-section .container,
            .facilities-grid .container,
            .booking-status-section .container,
            .auth-section .container,
            .dashboard-section .container,
            .about-content .container,
            .contact-content .container,
            .status-container,
            .auth-container {
                padding: 0 20px !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="{{ request()->routeIs('login') || request()->routeIs('register') ? 'auth-page' : '' }}">
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <img src="{{ asset('assets/images/gravenue-logo.png') }}" alt="Gravenue" class="logo-img">
                    <span>GRAVENUE</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
                    <li><a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">Tentang</a></li>
                    <li><a href="{{ route('facilities') }}" class="nav-link {{ request()->routeIs('facilities') ? 'active' : '' }}">Fasilitas</a></li>
                    <li><a href="{{ route('schedule') }}" class="nav-link {{ request()->routeIs('schedule') ? 'active' : '' }}">Jadwal</a></li>
                    <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Kontak</a></li>
                </ul>
                <div class="nav-auth">
                    @auth
                        <div class="user-dropdown">
                            <button class="user-btn" onclick="toggleUserMenu()">
                                <i class="fas fa-user-circle"></i>
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" id="userMenu">
                                <a href="{{ route('dashboard') }}" class="dropdown-item">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                                <a href="{{ route('profile') }}" class="dropdown-item">
                                    <i class="fas fa-user-edit"></i>
                                    Profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item logout-btn">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="auth-buttons">
                            <a href="{{ route('login') }}" class="btn-login">
                                <i class="fas fa-sign-in-alt"></i>
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="btn-register">
                                <i class="fas fa-user-plus"></i>
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/images/gravenue-logo.png') }}" alt="Gravenue Logo" class="footer-logo-img">
                        <span>GRAVENUE</span>
                        <p class="footer-tagline">Platform penyewaan fasilitas terpercaya di SMKN 4 Malang</p>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Navigasi</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                        <li><a href="{{ route('facilities') }}">Fasilitas</a></li>
                        <li><a href="{{ route('schedule') }}">Jadwal</a></li>
                        <li><a href="{{ route('contact') }}">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Informasi Kontak</h3>
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Tanimbar No.22, Kasin, Kec. Klojen, Kota Malang</p>
                        <p><i class="fas fa-phone"></i> (0341) 551431</p>
                        <p><i class="fas fa-envelope"></i> info@smkn4malang.sch.id</p>
                        <p><i class="fas fa-clock"></i> Senin - Sabtu: 07:00 - 16:00</p>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Ikuti Kami</h3>
                    <div class="social-links">
                        <a href="#" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link youtube"><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="footer-cta">
                        <p>Butuh bantuan?</p>
                        <a href="{{ route('contact') }}" class="cta-button">
                            <i class="fas fa-headset"></i> Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2024 Gravenue by SMKN 4 Malang. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="#">Kebijakan Privasi</a>
                        <a href="#">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.user-btn') && !event.target.closest('.user-btn')) {
            const dropdowns = document.getElementsByClassName('dropdown-menu');
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
    </script>

    <style>
    .nav-auth {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .auth-buttons {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .btn-login, .btn-register {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-login {
        color: #53354a;
        border: 2px solid #53354a;
        background: transparent;
    }

    .btn-login:hover {
        background: #53354a;
        color: white;
    }

    .btn-register {
        background: linear-gradient(135deg, #ff7844 0%, #e5673a 100%);
        color: white;
        border: 2px solid #ff7844;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 120, 68, 0.3);
    }

    .user-dropdown {
        position: relative;
    }

    .user-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        color: #53354a;
        transition: all 0.3s;
    }

    .user-btn:hover {
        background: #e9ecef;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        min-width: 200px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-radius: 10px;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s;
        z-index: 1000;
    }

    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        color: #333;
        text-decoration: none;
        transition: background 0.3s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
    }

    .dropdown-item i {
        width: 16px;
        color: #ff7844;
    }

    .dropdown-divider {
        height: 1px;
        background: #e9ecef;
        margin: 0.5rem 0;
    }

    .logout-btn {
        color: #dc3545;
    }

    .logout-btn i {
        color: #dc3545;
    }

    @media (max-width: 768px) {
        .nav-auth {
            margin-top: 1rem;
            width: 100%;
            justify-content: center;
        }

        .auth-buttons {
            width: 100%;
            justify-content: center;
        }

        .btn-login, .btn-register {
            flex: 1;
            justify-content: center;
        }

        .user-dropdown {
            width: 100%;
        }

        .user-btn {
            width: 100%;
            justify-content: center;
        }

        .dropdown-menu {
            right: auto;
            left: 0;
            width: 100%;
        }
    }
    </style>

    @stack('scripts')
</body>
</html>
