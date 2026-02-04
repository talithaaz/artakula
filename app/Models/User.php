<?php // Mulai file PHP.

namespace App\Models; // Namespace untuk model.

use Illuminate\Contracts\Auth\MustVerifyEmail; // Interface verifikasi email.
use Illuminate\Database\Eloquent\Factories\HasFactory; // Trait factory.
use Illuminate\Foundation\Auth\User as Authenticatable; // Base user autentikasi.
use Illuminate\Notifications\Notifiable; // Trait notifikasi.

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements MustVerifyEmail // Model user dengan verifikasi email.
{
    use HasFactory, Notifiable; // Aktifkan factory dan notifikasi.

    protected $table = 'tb_users'; // Tabel yang dipakai model ini.

    protected $fillable = [ // Daftar field yang boleh diisi mass assignment.
        'name', // Nama user.
        'username', // Username.
        'email', // Email user.
        'password', // Password user.
        'google_id', // Google ID untuk login Google.
        'email_verified_at', // Waktu email diverifikasi.
    ]; // Selesai definisi fillable.

    protected $hidden = [ // Field yang disembunyikan saat serialisasi.
        'password', // Sembunyikan password.
        'remember_token', // Sembunyikan remember token.
    ]; // Selesai definisi hidden.

    protected $casts = [ // Casting tipe data.
        'email_verified_at' => 'datetime', // Cast ke datetime.
        'password' => 'hashed', // Otomatis hash password.
    ]; // Selesai definisi casts.
}
