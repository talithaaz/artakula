<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Artakula</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo2.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body class="auth-body">

<div class="auth-card">
    
    {{-- HEADER LOGO - Disesuaikan ukurannya agar hemat ruang --}}
    <div class="text-center mb-3">
        <a href="/" class="text-decoration-none">
            <img src="{{ asset('img/logo1.png') }}" alt="Logo" class="rounded-circle mb-1" width="50">
            <h4 class="fw-bold text-dark mb-0">Arta<span class="text-success-custom">kula.</span></h4>
        </a>
        <p class="text-secondary small mt-2" style="font-size: 0.75rem;">Selamat datang kembali! Silakan masuk ke akunmu.</p>
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

    {{-- FORM LOGIN --}}
    <form action="{{ route('login.submit') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="auth-form-label mb-1">Email</label>
            <input type="email" 
                   name="email" 
                   class="form-control auth-input py-2" 
                   placeholder="nama@email.com" 
                   value="{{ old('email') }}" 
                   required>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="auth-form-label mb-0">Password</label>
                {{-- <a href="#" class="text-success-custom fw-bold text-decoration-none" style="font-size: 0.7rem;">Lupa?</a> --}}
            </div>
            <input type="password" 
                   name="password" 
                   class="form-control auth-input py-2" 
                   placeholder="Masukkan password" 
                   required>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-2 shadow-sm">
            Masuk Sekarang
        </button>
    </form>

    {{-- GOOGLE LOGIN --}}
    <a href="{{ route('google.login') }}" class="btn-google btn-google-spacer w-100 d-flex align-items-center justify-content-center gap-2 text-dark small py-2 text-decoration-none">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" 
            alt="Google" 
            style="width: 18px; height: 18px; display: block;">
        <span>Masuk dengan Google</span>
    </a>

    <p class="text-center mt-3 mb-0 small text-secondary">
        Belum punya akun? 
        <a href="{{ route('register.form') }}" class="text-success-custom fw-bold text-decoration-none">Daftar sekarang</a>
    </p>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>