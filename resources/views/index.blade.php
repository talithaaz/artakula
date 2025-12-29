<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artakula</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo2.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img
                src="{{ asset('img/logo1.png') }}"
                alt="Logo Artakula"
                class="logo-artakula me-2 rounded-circle"
            >
            Arta<span class="text-success">kula.</span>
        </a>

        <button
            class="navbar-toggler border-0"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
            <span class="bi bi-list fs-1"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link fw-bold px-3" href="#tentang">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold px-3" href="#fitur">Fitur</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a href="/register" class="btn btn-primary-custom">
                        Masuk
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ================= HERO ================= -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">

            <!-- KIRI : TEKS (DESKTOP) -->
            <div class="col-lg-7">

                <!-- HEADLINE -->
                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-4 py-2 mb-4 fw-bold mt-3 d-inline-block">
                    <i class="bi bi-stars me-2"></i>
                    Sistem Manajemen Keuangan Pribadi
                </span>

                <h1 class="hero-title mb-4">
                    Uang Datang, Uang Pergi<br>
                    Tapi Kamu Tetap Pegang Kendali
                </h1>

                <!-- TEKS + TOMBOL (DESKTOP) -->
                <div class="d-none d-lg-block">
                    <p class="lead text-secondary mb-5 fs-5 w-75">
                        Artakula membantumu mencatat pemasukan, pengeluaran, dan tabungan
                        dalam satu tempat supaya kamu benar-benar paham ke mana uangmu pergi.
                    </p>

                    <a href="/register" class="btn btn-primary-custom shadow-lg">
                        Mulai Kelola Keuangan
                    </a>
                </div>

            </div>

            <!-- KANAN : GAMBAR -->
            <div class="col-lg-5 mt-4 mt-lg-0 d-flex justify-content-center justify-content-lg-end">
                <img
                    src="{{ asset('img/landing2.png') }}"
                    class="img-fluid rounded-4"
                    alt="Mockup Artakula"
                >
            </div>

            <!-- MOBILE ONLY : TEKS + TOMBOL -->
            <div class="col-12 d-lg-none text-center mt-4">
                <p class="lead text-secondary mb-4 fs-5">
                    Artakula membantumu mencatat pemasukan, pengeluaran, dan tabungan
                    dalam satu tempat supaya kamu benar-benar paham ke mana uangmu pergi.
                </p>

                <a href="/register" class="btn btn-primary-custom shadow-lg">
                    Mulai Kelola Keuangan
                </a>
            </div>

        </div>
    </div>
</section>



<!-- ================= TENTANG ARTAKULA ================= -->
<section id="tentang" class="py-5">
    <div class="container">

        <!-- JUDUL -->
        <div class="text-center mb-5">
            <p class="text-success fw-semibold tracking-wide mb-2">
                TENTANG ARTAKULA
            </p>
            <h2 class="fw-bold">
                Mengelola Keuangan Pribadi dengan Lebih Terarah
            </h2>
        </div>

        <!-- GAMBAR + TEKS -->
        <div class="row align-items-start">

            <!-- Gambar -->
            <div class="col-md-6 text-center mb-4 mb-md-0">
                <img
                    src="{{ asset('img/abt1.png') }}"
                    class="img-fluid about-image mx-auto d-block"
                    alt="Ilustrasi Artakula"
                >
            </div>

            <!-- Teks -->
            <div class="col-md-6">
                <p class="mb-4">
                    Nama <strong>Artakula</strong> berasal dari bahasa jawa yaitu
                    <strong><em>arta/artho</em></strong> (uang) dan <strong><em>kula/kulo</em></strong> (saya),
                    yang bermakna <strong>keuangan saya</strong> 
                    pengingat bahwa kendali keuangan selalu ada di tanganmu.
                </p>

                <p class="mb-3">
                    <strong>Artakula</strong> adalah sistem manajemen keuangan pribadi yang dirancang untuk membantu pengguna
                    mencatat pemasukan, pengeluaran, dan tabungan secara terstruktur. 
                    Tidak hanya berfokus pada pencatatan, Artakula mendorong pengguna untuk memahami
                    pola keuangan dan kebiasaan finansial agar dapat mengambil keputusan yang lebih bijak.
                </p>

                

                <ul class="about-points">
                    <li>Mencatat pemasukan dan pengeluaran secara manual</li>
                    <li>Membantu memahami pola dan kebiasaan finansial</li>
                    <li>Mendukung pengambilan keputusan keuangan yang lebih bijak</li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- ================= FITUR ================= -->
<section class="container py-5" id="fitur">
    <div class="row mb-5 text-center">
        <h2 class="fw-bold fs-1 mb-3">
            Kelola Keuangan Lebih Mudah dengan Artakula
        </h2>
        <p class="text-muted fs-5">
            Fitur utama yang membantumu mengatur keuangan sehari-hari dengan lebih sadar dan terencana.
        </p>
    </div>

    <div class="row g-4 text-center">

        <!-- Feature 1 -->
        <div class="col-md-4">
            <div class="card feature-card h-100 p-4">
                <div class="icon-circle mx-auto mb-4">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h4 class="fw-bold mb-3">Pencatatan Keuangan</h4>
                <p class="text-muted mb-4">
                    Catat setiap pemasukan dan pengeluaran agar kamu tahu ke mana uangmu pergi dan bisa lebih hemat.
                </p>
                <a href="/register" class="feature-link">
                    Mulai Catat <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Feature 2 -->
        <div class="col-md-4">
            <div class="card feature-card h-100 p-4">
                <div class="icon-circle mx-auto mb-4">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <h4 class="fw-bold mb-3">Tabungan Tujuan</h4>
                <p class="text-muted mb-4">
                    Tentukan target tabungan dan pantau progresnya hingga tujuan keuanganmu tercapai.
                </p>
                <a href="/register" class="feature-link">
                    Atur Target <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Feature 3 -->
        <div class="col-md-4">
            <div class="card feature-card h-100 p-4">
                <div class="icon-circle mx-auto mb-4">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <h4 class="fw-bold mb-3">Evaluasi Keuangan</h4>
                <p class="text-muted mb-4">
                    Dapatkan laporan dan notifikasi real-time untuk membantu pengambilan keputusan finansial.
                </p>
                <a href="/register" class="feature-link">
                    Lihat Laporan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

    </div>
</section>


<!-- ================= CTA ================= -->
<section class="container">
    <div class="cta-section text-center">
        <h2 class="display-6 fw-bold mb-4">
            Ambil Kendali atas Keuanganmu Mulai Sekarang
        </h2>
        <p class="fs-5 opacity-75 mb-5 mx-auto w-75">
            Bergabunglah dan rasakan kemudahan mencatat serta mengevaluasi
            kondisi keuangan pribadi.
        </p>
        <a
            href="/register"
            class="btn btn-light btn-lg px-5 py-3 rounded-4 fw-bold text-success shadow"
        >
            Coba Sistem Artakula
        </a>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer-dark mt-5">
    <div class="container py-4">
        <div class="row text-muted small">

            <!-- Brand -->
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold text-light mb-2">Artakula</h6>
                <p class="mb-0">
                    Sistem pengelolaan keuangan pribadi untuk membantu pengguna
                    mencatat dan mengevaluasi kondisi keuangan secara terarah.
                </p>
            </div>

            <!-- Menu -->
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold text-light mb-2">Menu</h6>
                <ul class="list-unstyled mb-0">
                    <li><a href="#fitur" class="footer-link">Fitur</a></li>
                    <li><a href="#tentang" class="footer-link">Tentang Sistem</a></li>
                    <li><a href="/register" class="footer-link">Mulai Sekarang</a></li>
                </ul>
            </div>

            <!-- Informasi -->
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold text-light mb-2">Informasi</h6>
                <ul class="list-unstyled mb-0">
                    <li><span class="text-muted">Versi 1.0</span></li>
                    <!-- <li><span class="text-muted">Kebijakan Privasi</span></li>
                    <li><span class="text-muted">Syarat & Ketentuan</span></li> -->
                </ul>
            </div>

        </div>

        <div class="text-center text-muted small border-top pt-2 mt-3">
            &copy; {{ date('Y') }} Artakula — made by Talithaaz · Final Project
        </div>
    </div>
</footer>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
