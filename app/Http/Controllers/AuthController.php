<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use Illuminate\Http\Request; // Class Request untuk input HTTP.
use App\Models\User; // Model User.
use Illuminate\Support\Facades\Auth; // Facade Auth untuk autentikasi.
use Illuminate\Support\Facades\Hash; // Facade Hash untuk password.
use Laravel\Socialite\Facades\Socialite; // Facade Socialite untuk login Google.

class AuthController extends Controller // Controller autentikasi.
{
    // ===== VIEW =====
    public function showRegisterForm() // Menampilkan form registrasi.
    {
        return view('auth.register'); // Tampilkan view register.
    }

    public function showLoginForm() // Menampilkan form login.
    {
        return view('auth.login'); // Tampilkan view login.
    }

    // ===== REGISTER =====
    public function register(Request $request) // Menangani proses register.
    {
        $request->validate([ // Validasi input register.
            'name'     => 'required|string|max:255', // Nama wajib string max 255.
            'username' => 'required|string|max:100|unique:tb_users,username', // Username wajib unik.
            'email'    => 'required|email|unique:tb_users,email', // Email wajib unik.
            'password' => 'required|min:6', // Password minimal 6.
        ]);

        $user = User::create([ // Buat user baru.
            'name'     => $request->name, // Set nama.
            'username' => $request->username, // Set username.
            'email'    => $request->email, // Set email.
            'password' => Hash::make($request->password), // Hash password.
        ]);

        // login dulu
        Auth::login($user); // Login otomatis setelah register.

        // kirim email verifikasi
        $user->sendEmailVerificationNotification(); // Kirim email verifikasi.

        // arahkan ke halaman cek email
        return redirect()->route('verification.notice'); // Redirect ke halaman verifikasi.
    }

    // ===== LOGIN =====
    public function login(Request $request) // Menangani proses login.
    {
        $request->validate([ // Validasi input login.
            'email'    => 'required|email', // Email wajib valid.
            'password' => 'required', // Password wajib.
        ]);

        if (Auth::attempt($request->only('email', 'password'))) { // Coba login dengan kredensial.
            return redirect()->route('dashboard'); // Jika sukses, ke dashboard.
        }

        return back()->withErrors([ // Jika gagal, kembali dengan error.
            'email' => 'Email atau password salah',
        ]);
    }

    // ===== LOGOUT =====
    public function logout() // Logout user.
    {
        Auth::logout(); // Hapus sesi user.
        return redirect()->route('landing'); // Kembali ke halaman landing.
    }

    // ===== DASHBOARD =====
    public function dashboard() // Menampilkan dashboard.
    {
        return view('dashboard.index'); // Tampilkan view dashboard.
    }

    // ===== GOOGLE LOGIN =====
    public function redirectToGoogle() // Redirect ke Google OAuth.
    {
        return Socialite::driver('google')->stateless()->redirect(); // Arahkan ke Google.
    }


    public function handleGoogleCallback() // Callback setelah login Google.
    {
        $googleUser = Socialite::driver('google')->stateless()->user(); // Ambil data user dari Google.

        $user = User::updateOrCreate( // Update atau buat user baru.
            ['email' => $googleUser->email], // Kunci pencarian berdasarkan email.
            [
                'name' => $googleUser->name, // Set nama dari Google.
                'username' => str_replace(' ', '', strtolower($googleUser->name)), // Set username dari nama.
                'google_id' => $googleUser->id, // Simpan Google ID.
                'email_verified_at' => now(), // Tandai email terverifikasi.
                'password' => Hash::make(uniqid()), // Password random untuk user Google.
            ]
        );

        Auth::login($user); // Login user.

        return redirect()->route('dashboard'); // Redirect ke dashboard.
    }
}
