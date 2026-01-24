<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Index;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\DompetController;
use App\Http\Controllers\Api\DummyWalletApiController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KategoriTabunganController;
use App\Http\Controllers\TabunganController;

// Landing page
Route::get('/', [Index::class, 'index'])->name('landing');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

// Register
Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register.form');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.submit');

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login.form');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/

// Halaman cek email (PAKAI verify.blade.php)
Route::get('/email/verify', function () {
    return view('auth.verify');
})
    ->middleware('auth')
    ->name('verification.notice');

// Klik link dari email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('dashboard');
})
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

// Kirim ulang email verifikasi
Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| GOOGLE AUTH
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])
    ->name('google.login');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dompet', [DompetController::class, 'index'])->name('dompet.index');
    Route::get('/dompet/create', [DompetController::class, 'create'])->name('dompet.create');
    Route::post('/dompet', [DompetController::class, 'store'])->name('dompet.store');
    Route::get('/dompet/{id}/edit', [DompetController::class, 'edit'])->name('dompet.edit');
    Route::put('/dompet/{id}', [DompetController::class, 'update'])->name('dompet.update');
    Route::delete('/dompet/{id}', [DompetController::class, 'destroy'])->name('dompet.destroy');

    // ambil provider dummy (buat modal)
    Route::get('/dompet/iterasi/providers',[DompetController::class, 'availableProviders']);

    // create dompet hasil iterasi (SETELAH IZIN)
    Route::post('/dompet/iterasi/create',[DompetController::class, 'createFromProvider'])->name('dompet.iterate.create');

    // ITERASI SALDO (PAKAI DUMMY API CONTROLLER)
    Route::get('/api/dummy-wallet/iterate/{id}',[DummyWalletApiController::class, 'iterate'])->name('dummy.wallet.iterate');

    Route::resource('pemasukan', PemasukanController::class)->middleware(['auth']);
    Route::resource('kategori_pengeluaran', KategoriPengeluaranController::class);
    Route::resource('pengeluaran', PengeluaranController::class);
    Route::resource('kategoriTabungan', KategoriTabunganController::class);
    Route::resource('tabungan', TabunganController::class);


});


