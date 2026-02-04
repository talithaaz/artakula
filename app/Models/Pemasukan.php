<?php // Mulai file PHP.

namespace App\Models; // Namespace untuk model.

use Illuminate\Database\Eloquent\Model; // Base model Eloquent.

class Pemasukan extends Model // Model untuk tabel pemasukan.
{
    protected $table = 'tb_pemasukan'; // Nama tabel yang dipakai model ini.

    protected $fillable = [ // Daftar field yang boleh diisi mass assignment.
        'user_id', // ID user pemilik pemasukan.
        'dompet_id', // ID dompet terkait.
        'keterangan', // Deskripsi pemasukan.
        'jumlah', // Nilai pemasukan.
        'tanggal', // Tanggal pemasukan.
    ]; // Selesai definisi fillable.

    public function dompet() // Relasi ke model Dompet.
    {
        return $this->belongsTo(Dompet::class); // Pemasukan milik satu dompet.
    }
}
