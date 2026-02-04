<?php // Mulai file PHP.

namespace App\Models; // Namespace untuk model.

use Illuminate\Database\Eloquent\Model; // Base model Eloquent.

class KategoriPengeluaran extends Model // Model untuk kategori pengeluaran.
{
    // Nama tabel di database
    protected $table = 'tb_kategori_pengeluaran'; // Tabel yang dipakai model ini.

    // Field yang boleh diisi secara mass assignment
    protected $fillable = [ // Daftar field yang boleh diisi mass assignment.
        'user_id', // ID user pemilik kategori.
        'nama_kategori', // Nama kategori pengeluaran.
        'budget', // Budget kategori.
        'periode_awal', // Tanggal mulai periode.
        'periode_akhir', // Tanggal akhir periode.
    ]; // Selesai definisi fillable.

    /**
     * Relasi one-to-many
     * Satu kategori memiliki banyak pengeluaran
     */
    public function pengeluaran() // Relasi ke model Pengeluaran.
    {
        return $this->hasMany(Pengeluaran::class, 'kategori_id'); // Kategori punya banyak pengeluaran.
    }
}
