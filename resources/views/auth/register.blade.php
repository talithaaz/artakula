<!DOCTYPE html> <!-- Deklarasi dokumen HTML5. -->
<html lang="id"> <!-- Bahasa dokumen: Indonesia. -->
<head> <!-- Bagian head dokumen. -->
    <meta charset="UTF-8"> <!-- Set encoding UTF-8. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsif di perangkat. -->
    <title>Daftar | Artakula</title> <!-- Judul halaman. -->

    <link rel="icon" type="image/png" href="{{ asset('img/logo2.png') }}"> <!-- Favicon. -->
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet"> <!-- Font Google. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- CSS Bootstrap. -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"> <!-- Icon Bootstrap. -->

    <link rel="stylesheet" href="{{ asset('css/main.css') }}"> <!-- CSS utama. -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}"> <!-- CSS khusus auth. -->
</head> <!-- Tutup head. -->

<body class="auth-body"> <!-- Body halaman register. -->

<div class="auth-card"> <!-- Card container register. -->
    {{-- HEADER LOGO - Dikecilkan sedikit agar hemat ruang --}}
    <div class="text-center mb-3"> <!-- Header logo & judul. -->
        <a href="/" class="text-decoration-none"> <!-- Link ke halaman utama. -->
            <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="rounded-circle mb-1" width="48"> <!-- Logo. -->
            <h4 class="fw-bold text-dark mb-0">Arta<span class="text-success-custom">kula.</span></h4> <!-- Nama brand. -->
        </a> <!-- Tutup link logo. -->
        <p class="text-secondary style" style="font-size: 0.75rem; margin-top: 5px;">Mulai kelola keuanganmu dengan lebih bijak.</p> <!-- Subjudul. -->
    </div> <!-- Tutup header logo. -->

    {{-- ERROR HANDLING --}}
    @if ($errors->any()) <!-- Jika ada error validasi. -->
        <div class="alert alert-danger border-0 py-2 px-3 mb-3" style="border-radius: 12px; font-size: 0.7rem;"> <!-- Box error. -->
            <ul class="mb-0 ps-2"> <!-- List error. -->
                @foreach ($errors->all() as $error) <!-- Loop pesan error. -->
                    <li>{{ $error }}</li> <!-- Tampilkan error. -->
                @endforeach <!-- Tutup loop error. -->
            </ul> <!-- Tutup list error. -->
        </div> <!-- Tutup box error. -->
    @endif <!-- Tutup kondisi error. -->

    {{-- FORM REGISTER - Menggunakan mb-2 agar tidak memanjang ke bawah --}}
    <form action="{{ route('register.submit') }}" method="POST"> <!-- Form register. -->
        @csrf <!-- Token CSRF. -->

        <div class="mb-2"> <!-- Grup input nama. -->
            <label class="auth-form-label mb-1">Nama Lengkap</label> <!-- Label nama. -->
            <input type="text" name="name" class="form-control auth-input py-2" placeholder="Nama Anda" value="{{ old('name') }}" required> <!-- Input nama. -->
        </div> <!-- Tutup grup input nama. -->

        <div class="mb-2"> <!-- Grup input username. -->
            <label class="auth-form-label mb-1">Username</label> <!-- Label username. -->
            <input type="text" name="username" class="form-control auth-input py-2" placeholder="Buat Username Anda" value="{{ old('username') }}" required> <!-- Input username. -->
        </div> <!-- Tutup grup input username. -->

        <div class="mb-2"> <!-- Grup input email. -->
            <label class="auth-form-label mb-1">Email</label> <!-- Label email. -->
            <input type="email" name="email" class="form-control auth-input py-2" placeholder="nama@email.com" value="{{ old('email') }}" required> <!-- Input email. -->
        </div> <!-- Tutup grup input email. -->

        <div class="mb-3"> <!-- Grup input password. -->
            <label class="auth-form-label mb-1">Password</label> <!-- Label password. -->
            <input type="password" name="password" class="form-control auth-input py-2" placeholder="Minimal 8 karakter" required> <!-- Input password. -->
        </div> <!-- Tutup grup input password. -->

        <button type="submit" class="btn btn-primary-custom w-100 py-2 shadow-sm"> <!-- Tombol submit register. -->
            Buat Akun Sekarang
        </button> <!-- Tutup tombol submit. -->
    </form> <!-- Tutup form register. -->

    {{-- GOOGLE LOGIN --}}
    <a href="{{ route('google.login') }}" class="btn-google btn-google-spacer w-100 d-flex align-items-center justify-content-center gap-2 text-dark small py-2 text-decoration-none"> <!-- Tombol daftar Google. -->
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" 
            alt="Google" 
            style="width: 18px; height: 18px; display: block;"> <!-- Ikon Google. -->
        <span>Daftar dengan Google</span> <!-- Teks tombol Google. -->
    </a> <!-- Tutup tombol daftar Google. -->

    <p class="text-center mt-3 mb-0 small text-secondary"> <!-- Teks footer. -->
        Sudah punya akun?
        <a href="{{ route('login.form') }}" class="text-success-custom fw-bold text-decoration-none">Masuk</a> <!-- Link login. -->
    </p> <!-- Tutup teks footer. -->
</div> <!-- Tutup card container. -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- JS Bootstrap. -->
</body> <!-- Tutup body. -->
</html> <!-- Tutup html. -->
