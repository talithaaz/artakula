<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Artakula</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body style="font-family: 'Plus Jakarta Sans', sans-serif; background:#f3f4f6;">

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Artakula</a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-muted small">
                {{ Auth::user()->email }}
            </span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger btn-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-2">
                        Halo, {{ Auth::user()->username ?? Auth::user()->name }} 👋
                    </h4>

                    <p class="text-muted">
                        Selamat datang di dashboard <b>Artakula</b>.
                        Kamu berhasil login menggunakan
                        <b>
                            {{ Auth::user()->google_id ? 'Google Account' : 'Akun Manual' }}
                        </b>.
                    </p>

                    <hr>

                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded-3">
                                <h6 class="text-muted">Pemasukan</h6>
                                <h5 class="fw-bold">Rp 0</h5>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded-3">
                                <h6 class="text-muted">Pengeluaran</h6>
                                <h5 class="fw-bold">Rp 0</h5>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded-3">
                                <h6 class="text-muted">Tabungan</h6>
                                <h5 class="fw-bold">Rp 0</h5>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Dashboard ini masih tahap awal (MVP) 🚀
                        </small>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
