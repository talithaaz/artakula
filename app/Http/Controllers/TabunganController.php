<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\KategoriTabungan;
use App\Models\Dompet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabunganController extends Controller
{
    /**
     * =========================
     * INDEX
     * Menampilkan daftar tabungan
     * berdasarkan filter bulan & tahun
     * =========================
     */
    public function index(Request $request)
    {
        // Ambil bulan dari request, default bulan sekarang
        $bulan = $request->bulan ?? now()->month;

        // Ambil tahun dari request, default tahun sekarang
        $tahun = $request->tahun ?? now()->year;

        // Tentukan tanggal awal bulan
        $awalBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();

        // Tentukan tanggal akhir bulan
        $akhirBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Ambil data tabungan user sesuai filter
        $tabungan = Tabungan::with(['kategori', 'dompet', 'sumberDompet'])
            ->where('user_id', auth()->id())
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->orderBy('tanggal', 'desc') // urut tanggal terbaru
            ->orderBy('id', 'desc')      // fallback jika tanggal sama
            ->get();

        // Kirim data ke view index
        return view('tabungan.catat_tabungan.index', compact(
            'tabungan',
            'bulan',
            'tahun'
        ));
    }

    /**
     * =========================
     * CREATE
     * Menampilkan form tambah tabungan
     * =========================
     */
    public function create(Request $request)
    {
        // Ambil semua kategori tabungan milik user
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();

        // Ambil semua dompet milik user
        $dompet = Dompet::where('user_id', auth()->id())->get();

        // Default kategori & dompet tujuan
        $kategoriTerpilih = null;
        $dompetTujuan = null;

        // Jika kategori dipilih (GET)
        if ($request->filled('kategori_tabungan_id')) {
            $kategoriTerpilih = KategoriTabungan::where('user_id', auth()->id())
                ->with('dompetTujuan')
                ->find($request->kategori_tabungan_id);

            // Ambil dompet tujuan dari kategori
            $dompetTujuan = $kategoriTerpilih?->dompetTujuan;
        }

        // Kirim data ke view create
        return view('tabungan.catat_tabungan.create', compact(
            'kategoriTabungan',
            'dompet',
            'kategoriTerpilih',
            'dompetTujuan'
        ));
    }

    /**
     * =========================
     * STORE
     * Menyimpan data tabungan baru
     * =========================
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kategori_tabungan_id' => 'required|exists:tb_kategori_tabungan,id',
            'sumber_dompet_id'     => 'required|exists:tb_dompet,id',
            'dompet_id'            => 'required|exists:tb_dompet,id',
            'tanggal'              => 'required|date',
            'nominal'              => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {

            // Ambil kategori tabungan
            $kategori = KategoriTabungan::where('user_id', auth()->id())
                ->findOrFail($request->kategori_tabungan_id);

            // Ambil dompet sumber & lock
            $dompetSumber = Dompet::where('user_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($request->sumber_dompet_id);

            // Validasi saldo dompet sumber
            if ($dompetSumber->saldo < $request->nominal) {
                abort(400, 'Saldo tidak mencukupi');
            }

            // Kurangi saldo dompet sumber
            $dompetSumber->decrement('saldo', $request->nominal);

            // Jika kategori memiliki dompet tujuan
            if ($kategori->dompet_tujuan_id) {
                Dompet::lockForUpdate()
                    ->where('id', $kategori->dompet_tujuan_id)
                    ->increment('saldo', $request->nominal);
            }

            // Simpan data tabungan
            Tabungan::create([
                'user_id'              => auth()->id(),
                'kategori_tabungan_id' => $kategori->id,
                'sumber_dompet_id'     => $request->sumber_dompet_id,
                'dompet_id'            => $request->dompet_id,
                'tanggal'              => $request->tanggal,
                'nominal'              => $request->nominal,
                'keterangan'           => $request->keterangan,
            ]);
        });

        // Redirect kembali ke index sesuai filter
        return redirect()->route('tabungan.index', [
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->with('success', 'Tabungan berhasil ditambahkan');
    }

    /**
     * =========================
     * EDIT
     * Menampilkan form edit tabungan
     * =========================
     */
    public function edit($id)
    {
        // Ambil data tabungan
        $tabungan = Tabungan::where('user_id', auth()->id())
            ->findOrFail($id);

        // Ambil kategori & dompet
        $kategori = KategoriTabungan::where('user_id', auth()->id())->get();
        $dompet   = Dompet::where('user_id', auth()->id())->get();

        return view(
            'tabungan.catat_tabungan.edit',
            compact('tabungan', 'kategori', 'dompet')
        );
    }

    /**
     * =========================
     * UPDATE
     * Memperbarui data tabungan
     * =========================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_tabungan_id' => 'required|exists:tb_kategori_tabungan,id',
            'sumber_dompet_id'     => 'required|exists:tb_dompet,id',
            'tanggal'              => 'required|date',
            'nominal'              => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {

            // Ambil data tabungan lama
            $tabungan = Tabungan::where('user_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            $kategoriLama = KategoriTabungan::find($tabungan->kategori_tabungan_id);
            $kategoriBaru = KategoriTabungan::find($request->kategori_tabungan_id);

            // Dompet lama
            $sumberLama = Dompet::lockForUpdate()->find($tabungan->sumber_dompet_id);
            $tujuanLama = Dompet::lockForUpdate()->find($kategoriLama->dompet_tujuan_id);

            // Kembalikan saldo lama
            $sumberLama->increment('saldo', $tabungan->nominal);
            $tujuanLama->decrement('saldo', $tabungan->nominal);

            // Dompet baru
            $sumberBaru = Dompet::lockForUpdate()->find($request->sumber_dompet_id);
            $tujuanBaru = Dompet::lockForUpdate()->find($kategoriBaru->dompet_tujuan_id);

            // Validasi saldo baru
            if ($sumberBaru->saldo < $request->nominal) {
                abort(400, 'Saldo dompet tidak mencukupi');
            }

            // Update saldo baru
            $sumberBaru->decrement('saldo', $request->nominal);
            $tujuanBaru->increment('saldo', $request->nominal);

            // Update data tabungan
            $tabungan->update([
                'kategori_tabungan_id' => $request->kategori_tabungan_id,
                'sumber_dompet_id'     => $request->sumber_dompet_id,
                'tanggal'              => $request->tanggal,
                'nominal'              => $request->nominal,
                'keterangan'           => $request->keterangan,
            ]);
        });

        return redirect()->route('tabungan.index', [
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->with('success', 'Tabungan berhasil diperbarui');
    }

    /**
     * =========================
     * DESTROY
     * Menghapus data tabungan
     * =========================
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $tabungan = Tabungan::where('user_id', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            $kategori = KategoriTabungan::find($tabungan->kategori_tabungan_id);

            // Kembalikan saldo ke dompet sumber
            Dompet::lockForUpdate()
                ->where('id', $tabungan->sumber_dompet_id)
                ->increment('saldo', $tabungan->nominal);

            // Kurangi saldo dompet tujuan
            if ($kategori?->dompet_tujuan_id) {
                Dompet::lockForUpdate()
                    ->where('id', $kategori->dompet_tujuan_id)
                    ->decrement('saldo', $tabungan->nominal);
            }

            // Hapus data tabungan
            $tabungan->delete();
        });

        return back()->with('success', 'Tabungan berhasil dihapus');
    }
}
