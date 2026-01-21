<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Dompet;
use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    public function index()
    {
        $pemasukan = Pemasukan::with('dompet')
            ->where('user_id', auth()->id())
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pemasukan.index', compact('pemasukan'));
    }

    public function create()
    {
        $dompets = Dompet::where('user_id', auth()->id())->get();
        return view('pemasukan.create', compact('dompets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dompet_id' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date'
        ]);

        // pastikan dompet milik user
        $dompet = Dompet::where('id', $request->dompet_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // buat pemasukan
        $pemasukan = Pemasukan::create([
            'user_id' => auth()->id(),
            'dompet_id' => $dompet->id,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal
        ]);

        // update saldo dompet, pastikan integer
        $dompet->increment('saldo', (int) $request->jumlah);

        return redirect()->route('pemasukan.index')
            ->with('success', 'Pemasukan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pemasukan = Pemasukan::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $dompets = Dompet::where('user_id', auth()->id())->get();

        return view('pemasukan.edit', compact('pemasukan', 'dompets'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dompet_id' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date'
        ]);

        $pemasukan = Pemasukan::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // ambil dompet lama dan baru
        $oldDompet = Dompet::findOrFail($pemasukan->dompet_id);
        $newDompet = Dompet::where('id', $request->dompet_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $oldJumlah = $pemasukan->jumlah;
        $newJumlah = (int) $request->jumlah;

        // update pemasukan
        $pemasukan->update([
            'dompet_id' => $newDompet->id,
            'keterangan' => $request->keterangan,
            'jumlah' => $newJumlah,
            'tanggal' => $request->tanggal
        ]);

        // sesuaikan saldo dompet
        if ($oldDompet->id === $newDompet->id) {
            // sama dompet → update selisih
            $selisih = $newJumlah - $oldJumlah;
            $oldDompet->increment('saldo', $selisih);
        } else {
            // pindah dompet → kurangi dompet lama, tambah dompet baru
            $oldDompet->decrement('saldo', $oldJumlah);
            $newDompet->increment('saldo', $newJumlah);
        }

        return redirect()->route('pemasukan.index')
            ->with('success', 'Pemasukan berhasil diupdate');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $dompet = Dompet::findOrFail($pemasukan->dompet_id);

        // kurangi saldo
        $dompet->decrement('saldo', $pemasukan->jumlah);

        // hapus pemasukan
        $pemasukan->delete();

        return back()->with('success', 'Pemasukan dihapus');
    }
}
