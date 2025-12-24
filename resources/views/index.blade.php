<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artakula - Financial System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('img/logo1.png') }}" alt="Logo Artakula" width="40" height="40" class="me-2 rounded-circle">Arta<span class="text-success">kula.</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link px-3 fw-bold" href="#">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link px-3 fw-bold" href="#">Laporan</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="/register" class="btn btn-primary-custom">Mulai Sekarang</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 text-start">
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-4 py-2 mb-4 fw-bold">
                        <i class="bi bi-stars me-2"></i>Sistem Keuangan Cerdas
                    </span>
                    <h1 class="hero-title mb-4">Solusi Manajemen <br>Keuangan Masa Kini.</h1>
                    <p class="lead text-secondary mb-5 fs-5 w-75">Artakula dirancang untuk membantu pengelolaan aset finansial secara sistematis, aman, dan informatif bagi individu maupun UMKM.</p>
                    <div class="d-flex gap-3">
                        <a href="/register" class="btn btn-primary-custom shadow-lg text-decoration-none">Buka Akun Gratis</a>
                        <button class="btn btn-link text-decoration-none text-dark fw-bold px-3">
                            <i class="bi bi-play-circle me-2 fs-5"></i>Lihat Cara Kerja
                        </button>
                    </div>
                </div>
                <div class="col-lg-5 mt-5 mt-lg-0">
                    <div class="mockup-container">
                        <img src="https://images.unsplash.com/photo-1551288049-bbbda546697c?auto=format&fit=crop&q=80&w=1000" class="img-fluid rounded-4 shadow-sm" alt="Data Analytics">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="container py-5">
        <div class="row mb-5 justify-content-center text-center">
            <div class="col-lg-6">
                <h2 class="fw-extrabold fs-1 mb-3">Fitur Utama Penelitian</h2>
                <p class="text-muted">Dikembangkan dengan metodologi user-centered design untuk pengalaman terbaik.</p>
            </div>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="icon-circle shadow-sm mx-auto">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Analisis Tren</h4>
                    <p class="text-muted">Mengidentifikasi pola pengeluaran bulanan menggunakan algoritma klasifikasi data keuangan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="icon-circle shadow-sm mx-auto">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Keamanan Berlapis</h4>
                    <p class="text-muted">Implementasi sistem autentikasi dan enkripsi data untuk menjamin privasi pengguna.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="icon-circle shadow-sm mx-auto">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Pencatatan Cepat</h4>
                    <p class="text-muted">Antarmuka yang responsif memudahkan input transaksi kapan saja dan di mana saja.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="cta-section text-center">
            <h2 class="display-5 fw-bold mb-4">Siap Mengoptimalkan Keuangan Anda?</h2>
            <p class="fs-5 opacity-75 mb-5 mx-auto w-75">Bergabunglah dalam pengujian sistem Artakula dan rasakan kemudahan dalam mengelola setiap rupiah Anda.</p>
            <a href="/register" class="btn btn-light btn-lg px-5 py-3 rounded-4 fw-bold text-success shadow text-decoration-none">Coba Demo Sistem</a>
        </div>
    </section>

    <footer class="container py-5 text-center text-muted border-top mt-5">
        <p class="mb-0 fw-bold">&copy; {{ date('Y') }} Artakula Finance System. <br> <small class="fw-normal">Dibuat untuk Keperluan Penelitian Tugas Akhir / Skripsi</small></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>