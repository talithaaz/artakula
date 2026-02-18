<?php // Mulai file PHP.

use App\Http\Controllers\AuthController; // Controller auth.
use App\Http\Controllers\Index; // Controller landing page.
use Illuminate\Support\Facades\Route; // Facade Route.
// use Illuminate\Foundation\Auth\EmailVerificationRequest; // Request verifikasi email.
use App\Http\Controllers\DompetController; // Controller dompet.
use App\Http\Controllers\Api\DummyWalletApiController; // Controller dummy wallet API.
use App\Http\Controllers\PemasukanController; // Controller pemasukan.
use App\Http\Controllers\KategoriPengeluaranController; // Controller kategori pengeluaran.
use App\Http\Controllers\PengeluaranController; // Controller pengeluaran.
use App\Http\Controllers\KategoriTabunganController; // Controller kategori tabungan.
use App\Http\Controllers\TabunganController; // Controller tabungan.
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

// Landing page
Route::get('/', [Index::class, 'index'])->name('landing'); // Route landing.

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

// Register
Route::get('/register', [AuthController::class, 'showRegisterForm']) // Tampilkan form register.
    ->name('register.form'); // Nama route form register.

Route::post('/register', [AuthController::class, 'register']) // Proses register.
    ->name('register.submit'); // Nama route submit register.

// Login
Route::get('/login', [AuthController::class, 'showLoginForm']) // Tampilkan form login.
    ->name('login.form'); // Nama route form login.

Route::post('/login', [AuthController::class, 'login']) // Proses login.
    ->name('login.submit'); // Nama route submit login.

// Logout
Route::post('/logout', [AuthController::class, 'logout']) // Proses logout.
    ->name('logout'); // Nama route logout.


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [AuthController::class, 'dashboard']) // Route dashboard.
    ->middleware(['auth']) // Wajib login + email terverifikasi.
    ->name('dashboard'); // Nama route dashboard.


/*
|--------------------------------------------------------------------------
| GOOGLE AUTH
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']) // Redirect ke Google OAuth.
    ->name('google.login'); // Nama route login Google.

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']); // Callback Google OAuth.

Route::middleware(['auth'])->group(function () { // Group route yang butuh login.

    Route::get('/dompet', [DompetController::class, 'index'])->name('dompet.index'); // Daftar dompet.
    Route::get('/dompet/create', [DompetController::class, 'create'])->name('dompet.create'); // Form tambah dompet.
    Route::post('/dompet', [DompetController::class, 'store'])->name('dompet.store'); // Simpan dompet.
    Route::get('/dompet/{id}/edit', [DompetController::class, 'edit'])->name('dompet.edit'); // Form edit dompet.
    Route::put('/dompet/{id}', [DompetController::class, 'update'])->name('dompet.update'); // Update dompet.
    Route::delete('/dompet/{id}', [DompetController::class, 'destroy'])->name('dompet.destroy'); // Hapus dompet.

    // ambil provider dummy (buat modal)
    Route::get('/dompet/iterasi/providers',[DompetController::class, 'availableProviders']); // Provider dummy.

    // create dompet hasil iterasi (SETELAH IZIN)
    Route::post('/dompet/iterasi/create',[DompetController::class, 'createFromProvider'])->name('dompet.iterate.create'); // Buat dompet iterasi.

    // ITERASI SALDO (PAKAI DUMMY API CONTROLLER)
    Route::get('/api/dummy-wallet/iterate/{id}',[DummyWalletApiController::class, 'iterate'])->name('dummy.wallet.iterate'); // Iterasi saldo dummy.

    Route::resource('pemasukan', PemasukanController::class); // Resource pemasukan.
    Route::resource('kategori_pengeluaran', KategoriPengeluaranController::class); // Resource kategori pengeluaran.
    Route::resource('pengeluaran', PengeluaranController::class); // Resource pengeluaran.
    Route::resource('kategoriTabungan', KategoriTabunganController::class); // Resource kategori tabungan.
    Route::resource('tabungan', TabunganController::class); // Resource tabungan.
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/notifications/check', [NotificationController::class,'check']);
    Route::get('/notifications/list', [NotificationController::class,'list']);
    Route::post('/notifications/{id}/read', [NotificationController::class,'read']);
});
