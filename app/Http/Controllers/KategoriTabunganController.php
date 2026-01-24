<?php

namespace App\Http\Controllers;

use App\Models\KategoriTabungan;
use Illuminate\Http\Request;

class KategoriTabunganController extends Controller
{
    public function index()
    {
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();

        return view('tabungan.kategori_tabungan.index', compact('kategoriTabungan'));
    }

    public function create()
    {
        return view('tabungan.kategori_tabungan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required',
            'target_nominal' => 'nullable|numeric',
            'target_waktu' => 'nullable|date',
        ]);

        KategoriTabungan::create([
            'user_id' => auth()->id(),
            'nama_kategori' => $request->nama_kategori,
            'target_nominal' => $request->target_nominal,
            'target_waktu' => $request->target_waktu,
        ]);

        return redirect()->route('kategoriTabungan.index');
    }

    public function edit(KategoriTabungan $kategoriTabungan)
    {
        return view('tabungan.kategori_tabungan.edit', compact('kategoriTabungan'));
    }

    public function update(Request $request, KategoriTabungan $kategoriTabungan)
    {
        $kategoriTabungan->update($request->all());
        return redirect()->route('kategoriTabungan.index');
    }

    public function destroy(KategoriTabungan $kategoriTabungan)
    {
        $kategoriTabungan->delete();
        return back();
    }
}
