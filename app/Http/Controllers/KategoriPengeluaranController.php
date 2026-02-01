<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pengeluaran;
use App\Models\Dompet;

class KategoriPengeluaranController extends Controller
{
    public function index(Request $request)
{
    $bulan = $request->bulan ?? now()->month;
    $tahun = $request->tahun ?? now()->year;

    $awalBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
    $akhirBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth();

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
    
    $kategori->map(function ($k) use ($awalBulan, $akhirBulan) {

    $terpakai = Pengeluaran::where('user_id', auth()->id())
        ->where('kategori_id', $k->id)
        ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
        ->sum('jumlah');

    $k->terpakai = $terpakai;
    $k->sisa = $k->budget - $terpakai;

    return $k;
});


    return view(
        'pengeluaran.kategori_pengeluaran.index',
        compact('kategori', 'bulan', 'tahun')
    );
}


    public function create()
    {
        return view('pengeluaran.kategori_pengeluaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'periode_awal' => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        KategoriPengeluaran::create([
            'user_id' => auth()->id(),
            'nama_kategori' => $request->nama_kategori,
            'budget' => $request->budget,
            'periode_awal' => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            // 'terpakai' => 0, // default awal
        ]);

        return redirect()->route('kategori_pengeluaran.index', [
    'bulan' => $request->bulan,
    'tahun' => $request->tahun,])
            ->with('success', 'Kategori pengeluaran berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kategori = KategoriPengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('pengeluaran.kategori_pengeluaran.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriPengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'nama_kategori' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'periode_awal' => 'nullable|date',
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal',
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'budget' => $request->budget,
            'periode_awal' => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
        ]);

        return redirect()->route('kategori_pengeluaran.index', [
    'bulan' => $request->bulan,
    'tahun' => $request->tahun,])
            ->with('success', 'Kategori pengeluaran berhasil diupdate');
    }

    

public function destroy($id)
{
    DB::transaction(function () use ($id) {

        $kategori = KategoriPengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pengeluaranList = Pengeluaran::where('kategori_id', $id)
            ->where('user_id', auth()->id())
            ->get();

        foreach ($pengeluaranList as $pengeluaran) {

            // kembalikan saldo dompet
            Dompet::where('id', $pengeluaran->dompet_id)
                ->where('user_id', auth()->id())
                ->increment('saldo', $pengeluaran->jumlah);

            /** @var \App\Models\Pengeluaran $pengeluaran */
            // hapus transaksi
            $pengeluaran->delete();
        }

        // hapus kategori (terpakai otomatis tidak relevan lagi)
        $kategori->delete();
    });

    return redirect()
        ->route('kategori_pengeluaran.index', request()->query())
        ->with('success', 'Kategori & seluruh transaksi dihapus. Saldo dompet berhasil dikembalikan.');
}


}
