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
    public function index(Request $request)
{
    $bulan = $request->bulan ?? now()->month;
    $tahun = $request->tahun ?? now()->year;

    $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfMonth();
    $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth();

    $pengeluaran = Pengeluaran::with(['dompet', 'kategori'])
        ->where('user_id', auth()->id())
        ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
        ->orderBy('id', 'desc')
        ->get();

    return view(
        'pengeluaran.catat_pengeluaran.index',
        compact('pengeluaran', 'bulan', 'tahun')
    );
}

    public function create(Request $request)
{
    $dompets = Dompet::where('user_id', auth()->id())->get();

    $bulan = $request->bulan ?? now()->month;
    $tahun = $request->tahun ?? now()->year;

    // tanggal acuan dari filter, BUKAN now()
    $tanggalAcuan = Carbon::create($tahun, $bulan, 1)->toDateString();

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



    public function store(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required',
            'kategori_id' => 'required',
            'keterangan' => 'required|string',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
        ]);

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


        $dompet = Dompet::where('id', $request->dompet_id)
    ->where('user_id', auth()->id())
    ->firstOrFail();

$totalTabungan = DB::table('tb_tabungan')
    ->where('user_id', auth()->id())
    ->where('dompet_id', $request->dompet_id)
    ->sum('nominal');

$saldoBisaDipakai = $dompet->saldo - $totalTabungan;

if ($request->jumlah > $saldoBisaDipakai) {
    return back()->with('error', 'Saldo tidak mencukupi karena sebagian sudah masuk tabungan');
}

// ✅ baru simpan
Pengeluaran::create([
    'user_id' => auth()->id(),
    'dompet_id' => $request->dompet_id,
    'kategori_id' => $request->kategori_id,
    'keterangan' => $request->keterangan,
    'jumlah' => $request->jumlah,
    'tanggal' => $request->tanggal,
]);

// ✅ baru kurangi saldo
$dompet->decrement('saldo', $request->jumlah);



        return redirect()->route('pengeluaran.index', [
    'bulan' => $request->bulan,
    'tahun' => $request->tahun,])
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function edit($id)
{
    $pengeluaran = Pengeluaran::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $dompets = Dompet::where('user_id', auth()->id())->get();

    $tanggal = $pengeluaran->tanggal;

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

    return view('pengeluaran.catat_pengeluaran.edit', compact('pengeluaran', 'dompets', 'kategori'));
}


    public function update(Request $request, $id)
{
    $pengeluaran = Pengeluaran::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $request->validate([
        'dompet_id' => 'required',
        'kategori_id' => 'required',
        'keterangan' => 'required|string',
        'jumlah' => 'required|numeric|min:1',
        'tanggal' => 'required|date',
    ]);

    $dompetLama = Dompet::where('id', $pengeluaran->dompet_id)->first();
    $dompetBaru = Dompet::where('id', $request->dompet_id)->first();

    // rollback saldo lama
    $dompetLama->increment('saldo', $pengeluaran->jumlah);

    // cek saldo dompet baru
    $totalTabungan = DB::table('tb_tabungan')
        ->where('user_id', auth()->id())
        ->where('dompet_id', $request->dompet_id)
        ->sum('nominal');

    $saldoBisaDipakai = $dompetBaru->saldo - $totalTabungan;

    if ($request->jumlah > $saldoBisaDipakai) {
        // rollback lagi
        $dompetLama->decrement('saldo', $pengeluaran->jumlah);
        return back()->with('error', 'Saldo tidak mencukupi');
    }

    // update pengeluaran
    $pengeluaran->update([
        'dompet_id' => $request->dompet_id,
        'kategori_id' => $request->kategori_id,
        'keterangan' => $request->keterangan,
        'jumlah' => $request->jumlah,
        'tanggal' => $request->tanggal,
    ]);

    // kurangi saldo baru
    $dompetBaru->decrement('saldo', $request->jumlah);

    $bulan = Carbon::parse($request->tanggal)->month;
    $tahun = Carbon::parse($request->tanggal)->year;

    return redirect()->route('pengeluaran.index', compact('bulan', 'tahun'))
        ->with('success', 'Pengeluaran berhasil diupdate');
}


    public function destroy($id)
{
    DB::transaction(function () use ($id) {

        $pengeluaran = Pengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // kembalikan saldo dompet
        Dompet::where('id', $pengeluaran->dompet_id)
            ->where('user_id', auth()->id())
            ->increment('saldo', $pengeluaran->jumlah);

        // hapus pengeluaran
        $pengeluaran->delete();
    });

    return back()->with('success', 'Pengeluaran berhasil dihapus');
}

}
