<!DOCTYPE html> <!-- Deklarasi dokumen HTML5. -->
<html lang="id"> <!-- Bahasa dokumen: Indonesia. -->
<head> <!-- Bagian head dokumen. -->
    <meta charset="UTF-8"> <!-- Set encoding UTF-8. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif di perangkat. -->
    <title>Verifikasi Email | Artakula</title> <!-- Judul halaman. -->

    <link rel="icon" type="image/png" href="{{ asset('img/logo2.png') }}"> <!-- Favicon. -->

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet"> <!-- Font Google. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- CSS Bootstrap. -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"> <!-- Icon Bootstrap. -->

    <link rel="stylesheet" href="{{ asset('css/main.css') }}"> <!-- CSS utama. -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}"> <!-- CSS khusus auth. -->
</head> <!-- Tutup head. -->

<body class="auth-body"> <!-- Body halaman verifikasi. -->

<div class="auth-card text-center"> <!-- Card container verifikasi. -->
    
    {{-- HEADER LOGO - mb dikurangi --}}
    <div class="mb-3"> <!-- Header logo. -->
        <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="rounded-circle mb-2" width="50"> <!-- Logo. -->
        <h4 class="fw-bold text-dark">Arta<span class="text-success-custom">kula.</span></h4> <!-- Nama brand. -->
    </div> <!-- Tutup header logo. -->

    {{-- ICON VERIFIKASI - ukuran icon diperkecil sedikit --}}
    <div class="mb-3"> <!-- Wrapper ikon verifikasi. -->
        <div class="icon-circle mx-auto" style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 65px; height: 65px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"> <!-- Lingkaran ikon. -->
            <i class="bi bi-envelope-check fs-2"></i> <!-- Ikon amplop. -->
        </div> <!-- Tutup lingkaran ikon. -->
    </div> <!-- Tutup wrapper ikon. -->

    <h5 class="fw-bold mb-2">Verifikasi Email Kamu</h5> <!-- Judul verifikasi. -->
    <p class="text-secondary small mb-3"> <!-- Deskripsi verifikasi. -->
        Kami telah mengirimkan link verifikasi ke email kamu. 
        Silakan klik link tersebut untuk aktifkan akun.
    </p> <!-- Tutup deskripsi. -->

    {{-- STATUS --}}
    @if (session('status') === 'verification-link-sent') <!-- Jika link baru terkirim. -->
        <div class="alert alert-success border-0 small py-2 mb-3" style="border-radius: 10px; background-color: #ecfdf5; color: #065f46;"> <!-- Box status sukses. -->
            Link baru berhasil dikirim!
        </div> <!-- Tutup box status. -->
    @endif <!-- Tutup kondisi status. -->

    {{-- TOMBOL --}}
    <form method="POST" action="{{ route('verification.send') }}" class="mb-2"> <!-- Form kirim ulang verifikasi. -->
        @csrf <!-- Token CSRF. -->
        <button type="submit" class="btn btn-primary-custom w-100 py-2 shadow-sm"> <!-- Tombol kirim ulang. -->
            Kirim Ulang Email Verifikasi
        </button> <!-- Tutup tombol kirim ulang. -->
    </form> <!-- Tutup form kirim ulang. -->

    {{-- TOMBOL LOGOUT --}}
    <form method="POST" action="{{ route('logout') }}"> <!-- Form logout. -->
        @csrf <!-- Token CSRF. -->
        <button type="submit" class="btn btn-link text-danger text-decoration-none small fw-bold p-0"> <!-- Tombol logout. -->
            <i class="bi bi-box-arrow-left me-1"></i> Keluar
        </button> <!-- Tutup tombol logout. -->
    </form> <!-- Tutup form logout. -->

    <div class="mt-3 pt-2 border-top"> <!-- Footer informasi. -->
        <p class="text-muted mb-0" style="font-size: 0.7rem;">
            Cek inbox atau folder spam kamu.
        </p> <!-- Tutup teks info. -->
    </div> <!-- Tutup footer informasi. -->

</div> <!-- Tutup card container. -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- JS Bootstrap. -->
</body> <!-- Tutup body. -->
</html> <!-- Tutup html. -->
