<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Dompet;
use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

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

        Pengeluaran::create([
            'user_id' => auth()->id(),
            'dompet_id' => $request->dompet_id,
            'kategori_id' => $request->kategori_id,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);

        Dompet::where('id', $request->dompet_id)
            ->decrement('saldo', $request->jumlah);

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

        $selisih = $request->jumlah - $pengeluaran->jumlah;

        $pengeluaran->update($request->all());

        Dompet::where('id', $pengeluaran->dompet_id)
            ->decrement('saldo', $selisih);

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil diupdate');
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Dompet::where('id', $pengeluaran->dompet_id)
            ->increment('saldo', $pengeluaran->jumlah);

        $pengeluaran->delete();

        return back()->with('success', 'Pengeluaran dihapus');
    }
}
