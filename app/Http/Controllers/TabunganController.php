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
        $tabungan = Tabungan::with(['kategori', 'dompet'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('tabungan.catat_tabungan.index', compact('tabungan'));
    }

   public function create()
{
    $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();
    $dompet = Dompet::where('user_id', auth()->id())->get();

    return view(
        'tabungan.catat_tabungan.create',
        compact('kategoriTabungan', 'dompet')
    );
}


    public function store(Request $request)
    {
        $request->validate([
            'kategori_tabungan_id' => 'required',
            'dompet_id'            => 'required',
            'tanggal'              => 'required|date',
            'nominal'              => 'required|numeric',
        ]);

        Tabungan::create([
            'user_id'              => auth()->id(),
            'kategori_tabungan_id' => $request->kategori_tabungan_id,
            'dompet_id'            => $request->dompet_id,
            'tanggal'              => $request->tanggal,
            'nominal'              => $request->nominal,
            'keterangan'           => $request->keterangan,
        ]);

        return redirect()->route('tabungan.index')
            ->with('success', 'Tabungan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $tabungan = Tabungan::where('user_id', auth()->id())
            ->findOrFail($id);

        $kategori = KategoriTabungan::where('user_id', auth()->id())->get();
        $dompet   = Dompet::where('user_id', auth()->id())->get();

        return view('tabungan.catat_tabungan.edit',
            compact('tabungan', 'kategori', 'dompet')
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_tabungan_id' => 'required',
            'dompet_id'            => 'required',
            'tanggal'              => 'required|date',
            'nominal'              => 'required|numeric',
        ]);

        $tabungan = Tabungan::where('user_id', auth()->id())
            ->findOrFail($id);

        $tabungan->update([
            'kategori_tabungan_id' => $request->kategori_tabungan_id,
            'dompet_id'            => $request->dompet_id,
            'tanggal'              => $request->tanggal,
            'nominal'              => $request->nominal,
            'keterangan'           => $request->keterangan,
        ]);

        return redirect()->route('tabungan.index')
            ->with('success', 'Tabungan berhasil diupdate');
    }

    public function destroy($id)
    {
        Tabungan::where('user_id', auth()->id())
            ->findOrFail($id)
            ->delete();

        return back()->with('success', 'Tabungan berhasil dihapus');
    }
}
