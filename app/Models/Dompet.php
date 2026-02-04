<?php // Mulai file PHP.

namespace App\Models; // Namespace untuk model.

use Illuminate\Database\Eloquent\Factories\HasFactory; // Trait untuk factory model.
use Illuminate\Database\Eloquent\Model; // Base model Eloquent.

class Dompet extends Model // Model untuk tabel dompet.
{
    use HasFactory; // Aktifkan factory untuk seeding/testing.

    protected $table = 'tb_dompet'; // Nama tabel yang dipakai model ini.

    protected $fillable = [ // Daftar field yang boleh diisi mass assignment.
        'user_id', // ID user pemilik dompet.
        'nama_dompet', // Nama dompet.
        'jenis', // Jenis dompet (cash/bank/ewallet).
        'bank_code', // Kode bank/ewallet jika ada.
        'saldo', // Saldo dompet.
        'is_dummy', // Penanda dompet dummy hasil iterasi.
        'last_sync_at', // Waktu terakhir sinkronisasi.
    ]; // Selesai definisi fillable.

    protected $casts = [ // Casting tipe data untuk kolom tertentu.
        'last_sync_at' => 'datetime', // Cast ke objek datetime.
        'is_dummy' => 'boolean', // Cast ke boolean.
        'saldo' => 'integer', // Cast ke integer.
    ]; // Selesai definisi casts.

    public function tabungan() // Relasi ke tabel tabungan.
    {
        return $this->hasMany(Tabungan::class, 'dompet_id'); // Dompet punya banyak tabungan.
    }
}
