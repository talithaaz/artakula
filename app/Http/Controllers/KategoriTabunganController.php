<?php

namespace App\Http\Controllers;

use App\Models\KategoriTabungan; // Model kategori tabungan
use App\Models\Dompet;           // Model dompet
use Illuminate\Http\Request;     // Request dari form
use Carbon\Carbon;               // Helper tanggal
use DB;                           // Database transaction

class KategoriTabunganController extends Controller
{
    /**
     * =========================
     * INDEX
     * =========================
     * Menampilkan daftar kategori tabungan
     * berdasarkan filter bulan & tahun
     */
    public function index(Request $request)
    {
        // Ambil bulan dari request, jika tidak ada pakai bulan sekarang
        $bulan = $request->bulan ?? now()->month;

        // Ambil tahun dari request, jika tidak ada pakai tahun sekarang
        $tahun = $request->tahun ?? now()->year;

        // Tentukan tanggal awal bulan (contoh: 2026-02-01)
        $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfMonth();

        // Ambil kategori tabungan milik user
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())

            // Filter target waktu
            ->where(function ($q) use ($awalBulan) {

                // Tampilkan kategori tanpa target waktu
                $q->whereNull('target_waktu')

                  // Atau target waktunya masih >= bulan filter
                  ->orWhere('target_waktu', '>=', $awalBulan);
            })

            // Hitung total nominal tabungan per kategori
            ->withSum('catatTabungan as total_ditabung', 'nominal')

            // Ambil semua data
            ->get();

        // Kirim data ke halaman index
        return view(
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
    public function create()
    {
        // Ambil semua dompet milik user
        $dompet = Dompet::where('user_id', auth()->id())->get();

        // Kirim data dompet ke halaman create
        return view(
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
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_kategori'     => 'required',
            'target_nominal'    => 'nullable|numeric',
            'target_waktu'      => 'nullable|date',
            'dompet_tujuan_id'  => 'required|exists:tb_dompet,id',
        ]);

        // Simpan data kategori tabungan
        KategoriTabungan::create([
            'user_id'           => auth()->id(),
            'nama_kategori'     => $request->nama_kategori,
            'dompet_tujuan_id'  => $request->dompet_tujuan_id,
            'target_nominal'    => $request->target_nominal,
            'target_waktu'      => $request->target_waktu,
        ]);

        // Redirect kembali ke index sesuai filter sebelumnya
        return redirect()
            ->route('kategoriTabungan.index', [
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ])
            ->with('success', 'Kategori tabungan berhasil ditambahkan');
    }

    /**
     * =========================
     * EDIT
     * =========================
     * Menampilkan form edit kategori tabungan
     */
    public function edit(KategoriTabungan $kategoriTabungan)
    {
        // Ambil semua dompet milik user
        $dompet = Dompet::where('user_id', auth()->id())->get();

        // Hitung jumlah transaksi tabungan
        $jumlahTransaksi = $kategoriTabungan->tabungan()->count();

        // Kirim data ke halaman edit
        return view(
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
    public function update(Request $request, KategoriTabungan $kategoriTabungan)
    {
        // Validasi input
        $data = $request->validate([
            'nama_kategori'     => 'required|string',
            'target_nominal'    => 'required|numeric',
            'target_waktu'      => 'required|date',
            'dompet_tujuan_id'  => 'nullable|exists:tb_dompet,id',
        ]);

        // Jika kategori belum memiliki transaksi
        if ($kategoriTabungan->catatTabungan()->count() == 0) {

            // Dompet tujuan masih boleh diubah
            $kategoriTabungan->update($data);

        } else {

            // Jika sudah ada transaksi, dompet tujuan dikunci
            unset($data['dompet_tujuan_id']);

            // Update data selain dompet tujuan
            $kategoriTabungan->update($data);
        }

        // Redirect kembali ke index
        return redirect()
            ->route('kategoriTabungan.index', [
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ])
            ->with('success', 'Kategori tabungan berhasil diperbarui');
    }

    /**
     * =========================
     * DESTROY
     * =========================
     * Menghapus kategori tabungan
     * + rollback saldo dompet
     */
    public function destroy(KategoriTabungan $kategoriTabungan)
    {
        // Gunakan transaction agar data aman
        DB::transaction(function () use ($kategoriTabungan) {

            // Ambil ID user
            $userId = auth()->id();

            // Ambil semua transaksi tabungan kategori ini
            $transaksi = $kategoriTabungan->catatTabungan;

            // Hitung total tabungan kategori
            $totalKategori = $transaksi->sum('nominal');

            // Kurangi saldo dompet tujuan
            Dompet::where('id', $kategoriTabungan->dompet_tujuan_id)
                ->where('user_id', $userId)
                ->decrement('saldo', $totalKategori);

            // Kembalikan saldo ke dompet sumber masing-masing
            foreach ($transaksi as $t) {
                Dompet::where('id', $t->sumber_dompet_id)
                    ->where('user_id', $userId)
                    ->increment('saldo', $t->nominal);
            }

            // Hapus semua transaksi tabungan
            $kategoriTabungan->catatTabungan()->delete();

            // Hapus kategori tabungan
            $kategoriTabungan->delete();
        });

        // Kembali ke halaman sebelumnya
        return back()->with('success', 'Kategori tabungan berhasil dihapus');
    }
}
