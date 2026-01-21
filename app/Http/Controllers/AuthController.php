<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // ===== VIEW =====
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ===== REGISTER =====
    public function register(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'username' => 'required|string|max:100|unique:tb_users,username',
        'email'    => 'required|email|unique:tb_users,email',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'name'     => $request->name,
        'username' => $request->username,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // login dulu
    Auth::login($user);

    // kirim email verifikasi
    $user->sendEmailVerificationNotification();

    // arahkan ke halaman cek email
    return redirect()->route('verification.notice');
}


    // ===== LOGIN =====
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    // ===== LOGOUT =====
    public function logout()
    {
        Auth::logout();
        return redirect()->route('landing');
    }

    // ===== DASHBOARD =====
    public function dashboard()
    {
        return view('dashboard.index');
    }

    // ===== GOOGLE LOGIN =====
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }


    public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->email],
        [
            'name' => $googleUser->name,
            'username' => str_replace(' ', '', strtolower($googleUser->name)),
            'google_id' => $googleUser->id,
            'email_verified_at' => now(), // PENTING
            'password' => Hash::make(uniqid()),
        ]
    );

    Auth::login($user);

    return redirect()->route('dashboard');
}


}
