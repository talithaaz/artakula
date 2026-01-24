<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Dompet;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluaran = Pengeluaran::with(['dompet', 'kategori'])
            ->where('user_id', auth()->id())
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pengeluaran.catat_pengeluaran.index', compact('pengeluaran'));
    }

    public function create()
    {
        $dompets = Dompet::where('user_id', auth()->id())->get();
        $kategori = KategoriPengeluaran::where('user_id', auth()->id())->get();

        return view('pengeluaran.catat_pengeluaran.create', compact('dompets', 'kategori'));
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

        // Simpan pengeluaran baru
        $pengeluaran = Pengeluaran::create([
            'user_id' => auth()->id(),
            'dompet_id' => $request->dompet_id,
            'kategori_id' => $request->kategori_id,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);

        // Kurangi saldo dompet
        Dompet::where('id', $request->dompet_id)
            ->decrement('saldo', $request->jumlah);

        // Tambah terpakai di kategori
        KategoriPengeluaran::where('id', $request->kategori_id)
            ->increment('terpakai', $request->jumlah);

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


        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pengeluaran = Pengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $dompets = Dompet::where('user_id', auth()->id())->get();
        $kategori = KategoriPengeluaran::where('user_id', auth()->id())->get();

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

        // Hitung selisih jumlah pengeluaran
        $selisih = $request->jumlah - $pengeluaran->jumlah;

        // Update pengeluaran
        $pengeluaran->update([
            'dompet_id' => $request->dompet_id,
            'kategori_id' => $request->kategori_id,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);

        // Update saldo dompet
        if($pengeluaran->dompet_id == $request->dompet_id){
            // sama dompet, cuma kurangi atau tambah sesuai selisih
            Dompet::where('id', $request->dompet_id)
                ->decrement('saldo', $selisih);
        } else {
            // beda dompet, rollback dompet lama + kurangi dompet baru
            Dompet::where('id', $pengeluaran->dompet_id)
                ->increment('saldo', $pengeluaran->jumlah);
            Dompet::where('id', $request->dompet_id)
                ->decrement('saldo', $request->jumlah);
        }

        // Update terpakai kategori
        if($pengeluaran->kategori_id == $request->kategori_id){
            // sama kategori, cuma update selisih
            KategoriPengeluaran::where('id', $request->kategori_id)
                ->increment('terpakai', $selisih);
        } else {
            // beda kategori, rollback kategori lama + increment kategori baru
            KategoriPengeluaran::where('id', $pengeluaran->kategori_id)
                ->decrement('terpakai', $pengeluaran->jumlah);
            KategoriPengeluaran::where('id', $request->kategori_id)
                ->increment('terpakai', $request->jumlah);
        }

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil diupdate');
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Kembalikan saldo dompet
        Dompet::where('id', $pengeluaran->dompet_id)
            ->increment('saldo', $pengeluaran->jumlah);

        // Kurangi terpakai kategori
        KategoriPengeluaran::where('id', $pengeluaran->kategori_id)
            ->decrement('terpakai', $pengeluaran->jumlah);

        // Hapus pengeluaran
        $pengeluaran->delete();

        return back()->with('success', 'Pengeluaran dihapus');
    }
}
