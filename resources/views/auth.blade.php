<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Us | Artakula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body style="font-family: 'Plus Jakarta Sans', sans-serif; background: #f3f4f6;">
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4" style="border-radius: 24px; width: 100%; max-width: 420px;">
        <div class="text-center mb-4">
            <div class="mb-3">
                <i class="bi bi-rocket-takeoff-fill fs-1 text-primary"></i>
            </div>
            <h3>Daftar Akun</h3>
            <p class="text-muted small">Bergabunglah dengan ribuan pengguna lainnya</p>
        </div>

        <a href="{{ route('google.login') }}" class="btn btn-outline-dark w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" width="18">
            Daftar dengan Google
        </a>

        <div class="text-center text-muted mb-3">atau daftar manual</div>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Buat Akun Sekarang</button>
        </form>

        <p class="text-center mt-3">
            Sudah punya akun? <a href="{{ route('login.form') }}">Masuk di sini</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
