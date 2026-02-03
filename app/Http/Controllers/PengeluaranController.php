<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Dompet;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    /**
     * Menampilkan daftar pengeluaran berdasarkan bulan & tahun
     */
    public function index(Request $request)
    {
        // Ambil bulan & tahun dari request, default ke bulan & tahun sekarang
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Tentukan range awal & akhir bulan
        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Ambil data pengeluaran user pada periode tersebut
        $pengeluaran = Pengeluaran::with(['dompet', 'kategori'])
            ->where('user_id', auth()->id())
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Kirim data ke view
        return view(
            'pengeluaran.catat_pengeluaran.index',
            compact('pengeluaran', 'bulan', 'tahun')
        );
    }

    /**
     * Menampilkan form tambah pengeluaran
     */
    public function create(Request $request)
    {
        // Ambil semua dompet milik user
        $dompets = Dompet::where('user_id', auth()->id())->get();

        // Ambil bulan & tahun dari filter
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Tanggal acuan berdasarkan filter (BUKAN tanggal hari ini)
        $tanggalAcuan = Carbon::create($tahun, $bulan, 1)->toDateString();

        // Ambil kategori yang aktif pada tanggal acuan
        $kategori = KategoriPengeluaran::where('user_id', auth()->id())
            ->where(function ($q) use ($tanggalAcuan) {
                $q->whereNull('periode_awal')
                  ->orWhere('periode_awal', '<=', $tanggalAcuan);
            })
            ->where(function ($q) use ($tanggalAcuan) {
                $q->whereNull('periode_akhir')
                  ->orWhere('periode_akhir', '>=', $tanggalAcuan);
            })
            ->get();

        return view(
            'pengeluaran.catat_pengeluaran.create',
            compact('dompets', 'kategori', 'bulan', 'tahun')
        );
    }

    /**
     * Menyimpan data pengeluaran baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'dompet_id'   => 'required',
            'kategori_id' => 'required',
            'keterangan'  => 'required|string',
            'jumlah'      => 'required|numeric|min:1',
            'tanggal'     => 'required|date',
        ]);

        // Validasi kategori milik user & masih dalam periode aktif
        $kategori = KategoriPengeluaran::where('id', $request->kategori_id)
            ->where('user_id', auth()->id())
            ->where(function ($q) use ($request) {
                $q->whereNull('periode_awal')
                  ->orWhere('periode_awal', '<=', $request->tanggal);
            })
            ->where(function ($q) use ($request) {
                $q->whereNull('periode_akhir')
                  ->orWhere('periode_akhir', '>=', $request->tanggal);
            })
            ->firstOrFail();

        // Ambil dompet user
        $dompet = Dompet::where('id', $request->dompet_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Hitung total tabungan pada dompet tersebut
        $totalTabungan = DB::table('tb_tabungan')
            ->where('user_id', auth()->id())
            ->where('dompet_id', $request->dompet_id)
            ->sum('nominal');

        // Saldo yang benar-benar bisa dipakai
        $saldoBisaDipakai = $dompet->saldo - $totalTabungan;

        // Cek kecukupan saldo
        if ($request->jumlah > $saldoBisaDipakai) {
            return back()->with('error', 'Saldo tidak mencukupi karena sebagian sudah masuk tabungan');
        }

        // Simpan data pengeluaran
        Pengeluaran::create([
            'user_id'     => auth()->id(),
            'dompet_id'   => $request->dompet_id,
            'kategori_id' => $request->kategori_id,
            'keterangan'  => $request->keterangan,
            'jumlah'      => $request->jumlah,
            'tanggal'     => $request->tanggal,
        ]);

        // Kurangi saldo dompet
        $dompet->decrement('saldo', $request->jumlah);

        return redirect()->route('pengeluaran.index', [
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit pengeluaran
     */
    public function edit($id)
    {
        // Ambil data pengeluaran milik user
        $pengeluaran = Pengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Ambil semua dompet user
        $dompets = Dompet::where('user_id', auth()->id())->get();

        // Tanggal transaksi sebagai acuan periode kategori
        $tanggal = $pengeluaran->tanggal;

        // Ambil kategori yang aktif pada tanggal transaksi
        $kategori = KategoriPengeluaran::where('user_id', auth()->id())
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('periode_awal')
                  ->orWhere('periode_awal', '<=', $tanggal);
            })
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('periode_akhir')
                  ->orWhere('periode_akhir', '>=', $tanggal);
            })
            ->get();

        return view(
            'pengeluaran.catat_pengeluaran.edit',
            compact('pengeluaran', 'dompets', 'kategori')
        );
    }

    /**
     * Update data pengeluaran
     */
    public function update(Request $request, $id)
    {
        // Ambil data pengeluaran
        $pengeluaran = Pengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Validasi input
        $request->validate([
            'dompet_id'   => 'required',
            'kategori_id' => 'required',
            'keterangan'  => 'required|string',
            'jumlah'      => 'required|numeric|min:1',
            'tanggal'     => 'required|date',
        ]);

        // Ambil dompet lama & baru
        $dompetLama = Dompet::where('id', $pengeluaran->dompet_id)->first();
        $dompetBaru = Dompet::where('id', $request->dompet_id)->first();

        // Rollback saldo dompet lama
        $dompetLama->increment('saldo', $pengeluaran->jumlah);

        // Hitung saldo dompet baru yang bisa dipakai
        $totalTabungan = DB::table('tb_tabungan')
            ->where('user_id', auth()->id())
            ->where('dompet_id', $request->dompet_id)
            ->sum('nominal');

        $saldoBisaDipakai = $dompetBaru->saldo - $totalTabungan;

        // Jika saldo tidak cukup, rollback lagi
        if ($request->jumlah > $saldoBisaDipakai) {
            $dompetLama->decrement('saldo', $pengeluaran->jumlah);
            return back()->with('error', 'Saldo tidak mencukupi');
        }

        // Update data pengeluaran
        $pengeluaran->update([
            'dompet_id'   => $request->dompet_id,
            'kategori_id' => $request->kategori_id,
            'keterangan'  => $request->keterangan,
            'jumlah'      => $request->jumlah,
            'tanggal'     => $request->tanggal,
        ]);

        // Kurangi saldo dompet baru
        $dompetBaru->decrement('saldo', $request->jumlah);

        // Ambil bulan & tahun dari tanggal transaksi
        $bulan = Carbon::parse($request->tanggal)->month;
        $tahun = Carbon::parse($request->tanggal)->year;

        return redirect()
            ->route('pengeluaran.index', compact('bulan', 'tahun'))
            ->with('success', 'Pengeluaran berhasil diupdate');
    }

    /**
     * Menghapus pengeluaran
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            // Ambil data pengeluaran
            $pengeluaran = Pengeluaran::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Kembalikan saldo dompet
            Dompet::where('id', $pengeluaran->dompet_id)
                ->where('user_id', auth()->id())
                ->increment('saldo', $pengeluaran->jumlah);

            // Hapus pengeluaran
            $pengeluaran->delete();
        });

        return back()->with('success', 'Pengeluaran berhasil dihapus');
    }
}
