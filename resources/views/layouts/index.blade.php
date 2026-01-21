<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard | Artakula')</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo2.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<nav id="sidebar">
    {{-- SIDEBAR FULL PUNYA KAMU --}}
    <div class="p-4 mb-3 d-flex align-items-center gap-2">
            <img src="{{ asset('img/logo1.png') }}" width="35" class="rounded-circle shadow-sm">
            <h4 class="fw-bold text-white mb-0">Arta<span class="text-success-custom">kula.</span></h4>
        </div>
        
        <div class="nav-list mt-2">
            <a href="{{ route('dashboard') }}" class="nav-link-custom active">
                <i class="bi bi-grid-fill"></i> <span>Dashboard</span>
            </a>
            
            <a href="{{ route('dompet.index') }}" class="nav-link-custom">
                <i class="bi bi-wallet2"></i> <span>Dompet Saya</span>
            </a>
            
            
            <a href="#keuanganSub" class="nav-link-custom" data-bs-toggle="collapse" aria-expanded="false">
                <i class="bi bi-journal-plus"></i> <span>Catat Keuangan</span>
            </a>
            <div class="collapse" id="keuanganSub">
                <ul class="sub-menu">
                    <li><a href="pemasukan">Pemasukan</a></li>
                    <li><a href="#pengeluaranSub" data-bs-toggle="collapse">Pengeluaran <i class="bi bi-chevron-down ms-1" style="font-size: 0.6rem;"></i></a></li>
                    <div class="collapse ps-3" id="pengeluaranSub">
                        <li><a href="kategori_pengeluaran">• Budget Pengeluaran</a></li>
                        <li><a href="pengeluaran">• Catat Pengeluaran</a></li>
                    </div>
                </ul>
            </div>

            <a href="#tabunganSub" class="nav-link-custom" data-bs-toggle="collapse" aria-expanded="false">
                <i class="bi bi-piggy-bank"></i> <span>Tabungan</span>
            </a>
            <div class="collapse" id="tabunganSub">
                <ul class="sub-menu">
                    <li><a href="#">Kategori Tabungan</a></li>
                    <li><a href="#">Mencatat Tabungan</a></li>
                </ul>
            </div>

            <a href="#" class="nav-link-custom">
                <i class="bi bi-bar-chart-steps"></i> <span>Evaluasi Keuangan</span>
            </a>
            
            <a href="#" class="nav-link-custom">
                <i class="bi bi-person-gear"></i> <span>Profil</span>
            </a>
            
            <hr class="mx-4 my-4 opacity-10" style="border-color: #ffffff20;">

<form action="{{ route('logout') }}" method="POST" class="mt-2">
    @csrf
    <button type="submit" class="nav-link-custom text-danger border-0 bg-transparent w-100 shadow-none">
        <i class="bi bi-box-arrow-left"></i> <span>Keluar</span>
    </button>
</form>
        </div>
</nav>

<main id="content">

    {{-- TOPBAR FULL PUNYA KAMU --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-white shadow-sm" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h4 class="fw-bold mb-0">Overview Dashboard</h4>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-white rounded-circle shadow-sm p-2 position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5 text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-3" style="width: 300px; border-radius: 15px;">
                        <h6 class="fw-bold mb-3">Notifikasi</h6>
                        <li class="small text-muted mb-2"><i class="bi bi-info-circle me-1 text-success"></i> Saldo masuk Rp 5.000.000</li>
                        <li class="small text-muted"><i class="bi bi-envelope me-1"></i> Laporan bulanan dikirim ke email.</li>
                    </ul>
                </div>

                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none bg-white p-1 pe-3 rounded-pill shadow-sm">
                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=10b981&color=fff" class="rounded-circle" width="38">
                    <div class="lh-1 d-none d-md-block">
                        <p class="mb-0 small fw-bold text-dark">{{ auth()->user()->name }}</p>
                        <span style="font-size: 0.6rem;" class="text-muted">Premium Member</span>
                    </div>
                </a>
            </div>
        </div>

    @yield('content')

    {{-- FOOTER FULL PUNYA KAMU --}}
    <footer class="mt-5 pt-4 pb-2">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted small mb-0">
                        &copy; 2026 <span class="fw-bold" style="color: var(--primary-color);">Artakula.</span> 
                        
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-decoration-none text-muted small hover-primary">Panduan Pengguna</a></li>
                        <li class="list-inline-item mx-3 text-muted opacity-25">|</li>
                        <li class="list-inline-item"><a href="#" class="text-decoration-none text-muted small hover-primary">Kebijakan Privasi</a></li>
                        <li class="list-inline-item mx-3 text-muted opacity-25">|</li>
                        <li class="list-inline-item text-muted small"><span class="badge bg-success-subtle text-success fw-normal">Sistem Online</span></li>
                    </ul>
                </div>
            </div>
        </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const overlay = document.getElementById('overlay');

    if (window.innerWidth > 992) {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');
    } else {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }
}
</script>

@stack('scripts')
</body>
</html>
