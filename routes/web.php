<?php // Mulai file PHP.

use App\Http\Controllers\AuthController; // Controller auth.
use App\Http\Controllers\Index; // Controller landing page.
use Illuminate\Support\Facades\Route; // Facade Route.
use Illuminate\Foundation\Auth\EmailVerificationRequest; // Request verifikasi email.
use App\Http\Controllers\DompetController; // Controller dompet.
use App\Http\Controllers\Api\DummyWalletApiController; // Controller dummy wallet API.
use App\Http\Controllers\PemasukanController; // Controller pemasukan.
use App\Http\Controllers\KategoriPengeluaranController; // Controller kategori pengeluaran.
use App\Http\Controllers\PengeluaranController; // Controller pengeluaran.
use App\Http\Controllers\KategoriTabunganController; // Controller kategori tabungan.
use App\Http\Controllers\TabunganController; // Controller tabungan.
use App\Http\Controllers\EvaluasiController;

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
| EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/

// Halaman cek email (PAKAI verify.blade.php)
Route::get('/email/verify', function () { // Halaman notice verifikasi.
    return view('auth.verify'); // Tampilkan view verifikasi.
})
    ->middleware('auth') // Wajib login.
    ->name('verification.notice'); // Nama route notice verifikasi.

// Klik link dari email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) { // Callback verifikasi email.
    $request->fulfill(); // Tandai email terverifikasi.

    return redirect()->route('dashboard'); // Redirect ke dashboard.
})
    ->middleware(['auth', 'signed']) // Wajib login + link signed.
    ->name('verification.verify'); // Nama route verifikasi.

// Kirim ulang email verifikasi
Route::post('/email/verification-notification', function () { // Proses kirim ulang email verifikasi.
    request()->user()->sendEmailVerificationNotification(); // Kirim ulang email verifikasi.

    return back()->with('status', 'verification-link-sent'); // Kembali dengan status sukses.
})
    ->middleware(['auth', 'throttle:6,1']) // Wajib login + throttle.
    ->name('verification.send'); // Nama route kirim ulang.

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [AuthController::class, 'dashboard']) // Route dashboard.
    ->middleware(['auth', 'verified']) // Wajib login + email terverifikasi.
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

    Route::resource('pemasukan', PemasukanController::class)->middleware(['auth']); // Resource pemasukan.
    Route::resource('kategori_pengeluaran', KategoriPengeluaranController::class); // Resource kategori pengeluaran.
    Route::resource('pengeluaran', PengeluaranController::class); // Resource pengeluaran.
    Route::resource('kategoriTabungan', KategoriTabunganController::class); // Resource kategori tabungan.
    Route::resource('tabungan', TabunganController::class); // Resource tabungan.
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');

});
