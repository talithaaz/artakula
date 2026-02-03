<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Models\Dompet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KategoriPengeluaranController extends Controller
{
    /**
     * Menampilkan daftar kategori pengeluaran
     * berdasarkan filter bulan dan tahun
     */
    public function index(Request $request)
    {
        // Ambil bulan & tahun dari request, jika tidak ada gunakan bulan & tahun saat ini
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Tentukan awal dan akhir bulan
        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Ambil kategori pengeluaran milik user
        // yang masih aktif pada periode bulan yang dipilih
        $kategori = KategoriPengeluaran::where('user_id', auth()->id())
            ->where(function ($q) use ($awalBulan, $akhirBulan) {
                $q->whereNull('periode_awal')
                  ->orWhere('periode_awal', '<=', $akhirBulan);
            })
            ->where(function ($q) use ($awalBulan, $akhirBulan) {
                $q->whereNull('periode_akhir')
                  ->orWhere('periode_akhir', '>=', $awalBulan);
            })
            ->get();

        // Hitung total pengeluaran (terpakai) dan sisa budget tiap kategori
        $kategori->map(function ($k) use ($awalBulan, $akhirBulan) {

            // Total pengeluaran pada kategori dan periode bulan aktif
            $terpakai = Pengeluaran::where('user_id', auth()->id())
                ->where('kategori_id', $k->id)
                ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
                ->sum('jumlah');

            // Tambahkan properti dinamis ke object kategori
            $k->terpakai = $terpakai;
            $k->sisa     = $k->budget - $terpakai;

            return $k;
        });

        // Kirim data ke view
        return view(
            'pengeluaran.kategori_pengeluaran.index',
            compact('kategori', 'bulan', 'tahun')
        );
    }

    /**
     * Menampilkan form tambah kategori pengeluaran
     */
    public function create()
    {
        return view('pengeluaran.kategori_pengeluaran.create');
    }

    /**
     * Menyimpan data kategori pengeluaran baru
     */
    public function store(Request $request)
    {
        // Validasi input user
        $request->validate([
            'nama_kategori' => 'required|string',
            'budget'        => 'required|numeric|min:0',
            'periode_awal'  => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        // Simpan kategori pengeluaran
        KategoriPengeluaran::create([
            'user_id'       => auth()->id(),
            'nama_kategori' => $request->nama_kategori,
            'budget'        => $request->budget,
            'periode_awal'  => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
        ]);

        // Redirect kembali ke halaman index sesuai bulan & tahun sebelumnya
        return redirect()->route('kategori_pengeluaran.index', [
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ])
            ->with('success', 'Kategori pengeluaran berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit kategori pengeluaran
     */
    public function edit($id)
    {
        // Ambil kategori berdasarkan id dan user
        $kategori = KategoriPengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('pengeluaran.kategori_pengeluaran.edit', compact('kategori'));
    }

    /**
     * Memperbarui data kategori pengeluaran
     */
    public function update(Request $request, $id)
    {
        // Ambil kategori yang akan diupdate
        $kategori = KategoriPengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string',
            'budget'        => 'required|numeric|min:0',
            'periode_awal'  => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        // Update data kategori
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'budget'        => $request->budget,
            'periode_awal'  => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
        ]);

        // Redirect kembali ke index sesuai filter sebelumnya
        return redirect()->route('kategori_pengeluaran.index', [
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ])
            ->with('success', 'Kategori pengeluaran berhasil diupdate');
    }

    /**
     * Menghapus kategori pengeluaran beserta seluruh transaksinya
     * dan mengembalikan saldo ke dompet
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            // Ambil kategori milik user
            $kategori = KategoriPengeluaran::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Ambil seluruh pengeluaran yang menggunakan kategori ini
            $pengeluaranList = Pengeluaran::where('kategori_id', $id)
                ->where('user_id', auth()->id())
                ->get();

            // Loop tiap pengeluaran
            foreach ($pengeluaranList as $pengeluaran) {

                // Kembalikan saldo ke dompet terkait
                Dompet::where('id', $pengeluaran->dompet_id)
                    ->where('user_id', auth()->id())
                    ->increment('saldo', $pengeluaran->jumlah);

                /** @var \App\Models\Pengeluaran $pengeluaran */  
                // Hapus transaksi pengeluaran
                $pengeluaran->delete();
            }

            // Hapus kategori pengeluaran
            $kategori->delete();
        });

        // Redirect kembali ke halaman index
        return redirect()
            ->route('kategori_pengeluaran.index', request()->query())
            ->with(
                'success',
                'Kategori & seluruh transaksi dihapus. Saldo dompet berhasil dikembalikan.'
            );
    }
}
