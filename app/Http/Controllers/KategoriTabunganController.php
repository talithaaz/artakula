<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use App\Models\KategoriTabungan; // Model kategori tabungan.
use App\Models\Dompet; // Model dompet.
use Illuminate\Http\Request; // Request dari form.
use Carbon\Carbon; // Helper tanggal.
use DB; // Database transaction.

class KategoriTabunganController extends Controller // Controller kategori tabungan.
{
    /**
     * =========================
     * INDEX
     * =========================
     * Menampilkan daftar kategori tabungan
     * berdasarkan filter bulan & tahun
     */
    public function index(Request $request) // Menampilkan daftar kategori tabungan.
    {
        // Ambil bulan dari request, jika tidak ada pakai bulan sekarang
        $bulan = $request->bulan ?? now()->month; // Bulan yang dipilih.

        // Ambil tahun dari request, jika tidak ada pakai tahun sekarang
        $tahun = $request->tahun ?? now()->year; // Tahun yang dipilih.

        // Tentukan tanggal awal bulan (contoh: 2026-02-01)
        $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfMonth(); // Awal bulan.

        // Ambil kategori tabungan milik user
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id()) // Filter user login.

            // Filter target waktu
            ->where(function ($q) use ($awalBulan) { // Filter target waktu aktif.

                // Tampilkan kategori tanpa target waktu
                $q->whereNull('target_waktu') // Jika target waktu kosong.

                  // Atau target waktunya masih >= bulan filter
                  ->orWhere('target_waktu', '>=', $awalBulan); // Target waktu >= awal bulan.
            })

            // Hitung total nominal tabungan per kategori
            ->withSum('catatTabungan as total_ditabung', 'nominal') // Total nominal ditabung.

            // Ambil semua data
            ->get(); // Ambil daftar kategori.

        // Kirim data ke halaman index
        return view( // Tampilkan view daftar kategori.
            'tabungan.kategori_tabungan.index',
            compact('kategoriTabungan', 'bulan', 'tahun')
        );
    }

    /**
     * =========================
     * CREATE
     * =========================
     * Menampilkan form tambah kategori tabungan
     */
    public function create() // Menampilkan form tambah kategori.
    {
        // Ambil semua dompet milik user
        $dompet = Dompet::where('user_id', auth()->id())->get(); // Daftar dompet user.

        // Kirim data dompet ke halaman create
        return view( // Tampilkan view form tambah.
            'tabungan.kategori_tabungan.create',
            compact('dompet')
        );
    }

    /**
     * =========================
     * STORE
     * =========================
     * Menyimpan kategori tabungan baru
     */
    public function store(Request $request) // Menyimpan kategori tabungan baru.
    {
        // Validasi input dari form
        $request->validate([ // Aturan validasi.
            'nama_kategori'     => 'required', // Nama kategori wajib.
            'target_nominal'    => 'nullable|numeric', // Target nominal opsional.
            'target_waktu'      => 'nullable|date', // Target waktu opsional.
            'dompet_tujuan_id'  => 'required|exists:tb_dompet,id', // Dompet tujuan wajib ada.
        ]);

        // Simpan data kategori tabungan
        KategoriTabungan::create([ // Buat kategori tabungan baru.
            'user_id'           => auth()->id(), // Set user pemilik.
            'nama_kategori'     => $request->nama_kategori, // Set nama kategori.
            'dompet_tujuan_id'  => $request->dompet_tujuan_id, // Set dompet tujuan.
            'target_nominal'    => $request->target_nominal, // Set target nominal.
            'target_waktu'      => $request->target_waktu, // Set target waktu.
        ]);

        // Redirect kembali ke index sesuai filter sebelumnya
        return redirect() // Redirect ke daftar kategori.
            ->route('kategoriTabungan.index', [
                'bulan' => $request->bulan, // Bawa parameter bulan.
                'tahun' => $request->tahun, // Bawa parameter tahun.
            ])
            ->with('success', 'Kategori tabungan berhasil ditambahkan'); // Pesan sukses.
    }

    /**
     * =========================
     * EDIT
     * =========================
     * Menampilkan form edit kategori tabungan
     */
    public function edit(KategoriTabungan $kategoriTabungan) // Menampilkan form edit kategori.
    {
        // Ambil semua dompet milik user
        $dompet = Dompet::where('user_id', auth()->id())->get(); // Daftar dompet user.

        // Hitung jumlah transaksi tabungan
        $jumlahTransaksi = $kategoriTabungan->tabungan()->count(); // Hitung transaksi tabungan.

        // Kirim data ke halaman edit
        return view( // Tampilkan view form edit.
            'tabungan.kategori_tabungan.edit',
            compact('kategoriTabungan', 'dompet', 'jumlahTransaksi')
        );
    }

    /**
     * =========================
     * UPDATE
     * =========================
     * Mengupdate kategori tabungan
     */
    public function update(Request $request, KategoriTabungan $kategoriTabungan) // Mengupdate kategori tabungan.
    {
        // Validasi input
        $data = $request->validate([ // Aturan validasi.
            'nama_kategori'     => 'required|string', // Nama kategori wajib string.
            'target_nominal'    => 'required|numeric', // Target nominal wajib angka.
            'target_waktu'      => 'required|date', // Target waktu wajib date.
            'dompet_tujuan_id'  => 'nullable|exists:tb_dompet,id', // Dompet tujuan opsional.
        ]);

        // Jika kategori belum memiliki transaksi
        if ($kategoriTabungan->catatTabungan()->count() == 0) { // Cek apakah ada transaksi.

            // Dompet tujuan masih boleh diubah
            $kategoriTabungan->update($data); // Update semua data.

        } else { // Jika sudah ada transaksi.

            // Jika sudah ada transaksi, dompet tujuan dikunci
            unset($data['dompet_tujuan_id']); // Hapus field dompet tujuan.

            // Update data selain dompet tujuan
            $kategoriTabungan->update($data); // Update data tanpa dompet tujuan.
        }

        // Redirect kembali ke index
        return redirect() // Redirect ke daftar kategori.
            ->route('kategoriTabungan.index', [
                'bulan' => $request->bulan, // Bawa parameter bulan.
                'tahun' => $request->tahun, // Bawa parameter tahun.
            ])
            ->with('success', 'Kategori tabungan berhasil diperbarui'); // Pesan sukses.
    }

    /**
     * =========================
     * DESTROY
     * =========================
     * Menghapus kategori tabungan
     * + rollback saldo dompet
     */
    public function destroy(KategoriTabungan $kategoriTabungan) // Menghapus kategori tabungan.
    {
        // Gunakan transaction agar data aman
        DB::transaction(function () use ($kategoriTabungan) { // Bungkus proses dalam transaksi.

            // Ambil ID user
            $userId = auth()->id(); // ID user login.

            // Ambil semua transaksi tabungan kategori ini
            $transaksi = $kategoriTabungan->catatTabungan; // Daftar transaksi tabungan.

            // Hitung total tabungan kategori
            $totalKategori = $transaksi->sum('nominal'); // Total tabungan kategori.

            // Kurangi saldo dompet tujuan
            Dompet::where('id', $kategoriTabungan->dompet_tujuan_id) // Cari dompet tujuan.
                ->where('user_id', $userId) // Pastikan milik user login.
                ->decrement('saldo', $totalKategori); // Kurangi saldo dompet tujuan.

            // Kembalikan saldo ke dompet sumber masing-masing
            foreach ($transaksi as $t) { // Loop transaksi tabungan.
                Dompet::where('id', $t->sumber_dompet_id) // Cari dompet sumber.
                    ->where('user_id', $userId) // Pastikan milik user login.
                    ->increment('saldo', $t->nominal); // Tambah saldo dompet sumber.
            }

            // Hapus semua transaksi tabungan
            $kategoriTabungan->catatTabungan()->delete(); // Hapus transaksi tabungan.

            // Hapus kategori tabungan
            $kategoriTabungan->delete(); // Hapus kategori tabungan.
        });

        // Kembali ke halaman sebelumnya
        return back()->with('success', 'Kategori tabungan berhasil dihapus'); // Pesan sukses.
    }
}
