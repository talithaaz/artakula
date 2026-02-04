<?php // Mulai file PHP.

namespace App\Models; // Namespace untuk model.

use Illuminate\Database\Eloquent\Model; // Base model Eloquent.

class Pengeluaran extends Model // Model untuk pengeluaran.
{
    // Nama tabel
    protected $table = 'tb_pengeluaran'; // Tabel yang dipakai model ini.

    // Field yang boleh diisi mass assignment
    protected $fillable = [ // Daftar field yang boleh diisi mass assignment.
        'user_id', // ID user pemilik pengeluaran.
        'dompet_id', // ID dompet terkait.
        'kategori_id', // ID kategori pengeluaran.
        'keterangan', // Deskripsi pengeluaran.
        'jumlah', // Nilai pengeluaran.
        'tanggal', // Tanggal pengeluaran.
    ]; // Selesai definisi fillable.

    // Relasi ke tabel dompet
    public function dompet() // Relasi ke model Dompet.
    {
        return $this->belongsTo(Dompet::class); // Pengeluaran milik satu dompet.
    }

    // Relasi ke tabel kategori pengeluaran
    public function kategori() // Relasi ke model KategoriPengeluaran.
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_id'); // Pengeluaran milik satu kategori.
    }
}
