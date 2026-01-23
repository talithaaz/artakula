<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\KategoriTabungan;
use App\Models\Dompet;
use Illuminate\Http\Request;

class TabunganController extends Controller
{
    public function index()
    {
        $tabungan = Tabungan::where('user_id', auth()->id())->get();

        return view('tabungan.catat_tabungan.index', compact('tabungan'));
    }

    public function create()
    {
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();
        $dompets = Dompet::where('user_id', auth()->id())->get();

        return view('tabungan.catat_tabungan.create', compact(
            'kategoriTabungan',
            'dompets'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_tabungan_id' => 'required',
            'dompet_id' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
        ]);

        Tabungan::create([
            'user_id' => auth()->id(),
            'kategori_tabungan_id' => $request->kategori_tabungan_id,
            'dompet_id' => $request->dompet_id,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);

        // kalau MAU dompet berkurang
        // Dompet::where('id',$request->dompet_id)
        //     ->decrement('saldo',$request->jumlah);

        return redirect()->route('tabungan.index')
            ->with('success','Tabungan berhasil dicatat');
    }

    public function edit(Tabungan $tabungan)
    {
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();
        $dompets = Dompet::where('user_id', auth()->id())->get();

        return view('tabungan.catat_tabungan.edit', compact(
            'tabungan',
            'kategoriTabungan',
            'dompets'
        ));
    }

    public function update(Request $request, Tabungan $tabungan)
    {
        $request->validate([
            'kategori_tabungan_id' => 'required',
            'dompet_id' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
        ]);

        $tabungan->update($request->all());

        return redirect()->route('tabungan.index')
            ->with('success','Tabungan berhasil diupdate');
    }

    public function destroy(Tabungan $tabungan)
    {
        $tabungan->delete();

        return back()->with('success','Tabungan dihapus');
    }
}
