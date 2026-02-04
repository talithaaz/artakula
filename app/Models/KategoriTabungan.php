<?php // Mulai file PHP.

namespace App\Models; // Namespace untuk model.

use Illuminate\Database\Eloquent\Model; // Base model Eloquent.
use App\Models\Tabungan; // Model Tabungan.
use App\Models\Dompet; // Model Dompet.

class KategoriTabungan extends Model // Model untuk kategori tabungan.
{
    // Nama tabel di database
    protected $table = 'tb_kategori_tabungan'; // Tabel yang dipakai model ini.

    // Field yang boleh diisi mass assignment
    protected $fillable = [ // Daftar field yang boleh diisi mass assignment.
        'user_id', // ID user pemilik kategori.
        'nama_kategori', // Nama kategori tabungan.
        'dompet_tujuan_id', // ID dompet tujuan.
        'target_nominal', // Target nominal tabungan.
        'target_waktu', // Target waktu tabungan.
    ]; // Selesai definisi fillable.

    /**
     * Relasi ke dompet tujuan
     */
    public function dompetTujuan() // Relasi ke model Dompet.
    {
        return $this->belongsTo(Dompet::class, 'dompet_tujuan_id'); // Kategori punya dompet tujuan.
    }

    /**
     * Relasi ke tabel tabungan
     */
    public function tabungan() // Relasi ke model Tabungan.
    {
        return $this->hasMany(Tabungan::class, 'kategori_tabungan_id'); // Kategori punya banyak tabungan.
    }

    /**
     * Alias relasi tabungan
     * Digunakan di controller
     */
    public function catatTabungan() // Alias relasi tabungan.
    {
        return $this->hasMany(Tabungan::class, 'kategori_tabungan_id'); // Alias ke relasi tabungan.
    }

    /**
     * Hitung total tabungan terkumpul
     */
    public function totalTerkumpul() // Hitung total tabungan terkumpul.
    {
        return $this->tabungan()->sum('nominal'); // Jumlahkan nominal tabungan.
    }
}
