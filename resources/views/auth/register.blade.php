<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Artakula</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo2.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body class="auth-body">

<div class="auth-card">
    {{-- HEADER LOGO - Dikecilkan sedikit agar hemat ruang --}}
    <div class="text-center mb-3">
        <a href="/" class="text-decoration-none">
            <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="rounded-circle mb-1" width="48">
            <h4 class="fw-bold text-dark mb-0">Arta<span class="text-success-custom">kula.</span></h4>
        </a>
        <p class="text-secondary style" style="font-size: 0.75rem; margin-top: 5px;">Mulai kelola keuanganmu dengan lebih bijak.</p>
    </div>

    {{-- ERROR HANDLING --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 py-2 px-3 mb-3" style="border-radius: 12px; font-size: 0.7rem;">
            <ul class="mb-0 ps-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM REGISTER - Menggunakan mb-2 agar tidak memanjang ke bawah --}}
    <form action="{{ route('register.submit') }}" method="POST">
        @csrf

        <div class="mb-2">
            <label class="auth-form-label mb-1">Nama Lengkap</label>
            <input type="text" name="name" class="form-control auth-input py-2" placeholder="Nama Anda" value="{{ old('name') }}" required>
        </div>

        <div class="mb-2">
            <label class="auth-form-label mb-1">Username</label>
            <input type="text" name="username" class="form-control auth-input py-2" placeholder="Buat Username Anda" value="{{ old('username') }}" required>
        </div>

        <div class="mb-2">
            <label class="auth-form-label mb-1">Email</label>
            <input type="email" name="email" class="form-control auth-input py-2" placeholder="nama@email.com" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label class="auth-form-label mb-1">Password</label>
            <input type="password" name="password" class="form-control auth-input py-2" placeholder="Minimal 8 karakter" required>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-2 shadow-sm">
            Buat Akun Sekarang
        </button>
    </form>

    {{-- GOOGLE LOGIN --}}
    <a href="{{ route('google.login') }}" class="btn-google btn-google-spacer w-100 d-flex align-items-center justify-content-center gap-2 text-dark small py-2 text-decoration-none">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" 
            alt="Google" 
            style="width: 18px; height: 18px; display: block;">
        <span>Daftar dengan Google</span>
    </a>

    <p class="text-center mt-3 mb-0 small text-secondary">
        Sudah punya akun?
        <a href="{{ route('login.form') }}" class="text-success-custom fw-bold text-decoration-none">Masuk</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>