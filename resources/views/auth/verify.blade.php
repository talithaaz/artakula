<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | Artakula</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo2.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body class="auth-body">

<div class="auth-card text-center">
    
    {{-- HEADER LOGO - mb dikurangi --}}
    <div class="mb-3">
        <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="rounded-circle mb-2" width="50">
        <h4 class="fw-bold text-dark">Arta<span class="text-success-custom">kula.</span></h4>
    </div>

    {{-- ICON VERIFIKASI - ukuran icon diperkecil sedikit --}}
    <div class="mb-3">
        <div class="icon-circle mx-auto" style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 65px; height: 65px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-envelope-check fs-2"></i>
        </div>
    </div>

    <h5 class="fw-bold mb-2">Verifikasi Email Kamu</h5>
    <p class="text-secondary small mb-3">
        Kami telah mengirimkan link verifikasi ke email kamu. 
        Silakan klik link tersebut untuk aktifkan akun.
    </p>

    {{-- STATUS --}}
    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success border-0 small py-2 mb-3" style="border-radius: 10px; background-color: #ecfdf5; color: #065f46;">
            Link baru berhasil dikirim!
        </div>
    @endif

    {{-- TOMBOL --}}
    <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
        @csrf
        <button type="submit" class="btn btn-primary-custom w-100 py-2 shadow-sm">
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    {{-- TOMBOL LOGOUT --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-link text-danger text-decoration-none small fw-bold p-0">
            <i class="bi bi-box-arrow-left me-1"></i> Keluar
        </button>
    </form>

    <div class="mt-3 pt-2 border-top">
        <p class="text-muted mb-0" style="font-size: 0.7rem;">
            Cek inbox atau folder spam kamu.
        </p>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>