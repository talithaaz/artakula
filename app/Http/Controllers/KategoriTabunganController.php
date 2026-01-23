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
            'nama_tabungan' => 'required',
        ]);

        KategoriTabungan::create([
            'user_id' => auth()->id(),
            'nama_tabungan' => $request->nama_tabungan,
            'keterangan' => $request->keterangan,
            'target_nominal' => $request->target_nominal,
            'target_mulai' => $request->target_mulai,
            'target_selesai' => $request->target_selesai,
        ]);

        return redirect()->route('kategori-tabungan.index')
            ->with('success','Kategori tabungan berhasil ditambahkan');
    }

    public function edit(KategoriTabungan $kategori_tabungan)
    {
        return view('tabungan.kategori_tabungan.edit', compact('kategori_tabungan'));
    }

    public function update(Request $request, KategoriTabungan $kategori_tabungan)
    {
        $request->validate([
            'nama_tabungan' => 'required',
        ]);

        $kategori_tabungan->update($request->all());

        return redirect()->route('kategori-tabungan.index')
            ->with('success','Kategori tabungan berhasil diupdate');
    }

    public function destroy(KategoriTabungan $kategori_tabungan)
    {
        $kategori_tabungan->delete();

        return back()->with('success','Kategori tabungan dihapus');
    }
}
