<?php // Mulai file PHP.

namespace App\Models; // Namespace untuk model.

use Illuminate\Database\Eloquent\Factories\HasFactory; // Trait factory.
use Illuminate\Database\Eloquent\Model; // Base model Eloquent.

class Tabungan extends Model // Model untuk tabungan.
{
    use HasFactory; // Aktifkan factory untuk seeding/testing.

    // Nama tabel
    protected $table = 'tb_tabungan'; // Tabel yang dipakai model ini.

    // Kolom yang boleh diisi (mass assignment)
    protected $fillable = [ // Daftar kolom yang boleh diisi mass assignment.
        'user_id', // ID user pemilik tabungan.
        'kategori_tabungan_id', // ID kategori tabungan.
        'sumber_dompet_id', // ID dompet sumber.
        'dompet_id', // ID dompet tujuan.
        'tanggal', // Tanggal tabungan.
        'nominal', // Nilai tabungan.
        'keterangan', // Keterangan tabungan.
    ]; // Selesai definisi fillable.

    /**
     * Relasi ke kategori tabungan
     */
    public function kategori() // Relasi ke model KategoriTabungan.
    {
        return $this->belongsTo(KategoriTabungan::class, 'kategori_tabungan_id'); // Tabungan milik satu kategori.
    }

    /**
     * Relasi ke dompet tujuan
     */
    public function dompet() // Relasi ke model Dompet.
    {
        return $this->belongsTo(Dompet::class, 'dompet_id'); // Tabungan milik satu dompet tujuan.
    }

    /**
     * Relasi ke dompet sumber
     */
    public function sumberDompet() // Relasi ke model Dompet sumber.
    {
        return $this->belongsTo(Dompet::class, 'sumber_dompet_id'); // Tabungan milik satu dompet sumber.
    }

    /**
     * Relasi ke user
     */
    public function user() // Relasi ke model User.
    {
        return $this->belongsTo(User::class); // Tabungan milik satu user.
    }
}
