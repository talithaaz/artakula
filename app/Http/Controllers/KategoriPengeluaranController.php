<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class KategoriPengeluaranController extends Controller
{
    public function index()
    {
        $kategori = KategoriPengeluaran::where('user_id', auth()->id())->get();
        return view('pengeluaran.kategori_pengeluaran.index', compact('kategori'));
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
        ]);

        KategoriPengeluaran::create([
            'user_id' => auth()->id(),
            'nama_kategori' => $request->nama_kategori,
            'budget' => $request->budget,
        ]);

        return redirect()->route('kategori_pengeluaran.index')
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
        ]);

        $kategori->update($request->all());

        return redirect()->route('kategori_pengeluaran.index')
            ->with('success', 'Kategori pengeluaran berhasil diupdate');
    }

    public function destroy($id)
    {
        $kategori = KategoriPengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $kategori->delete();

        return back()->with('success', 'Kategori pengeluaran dihapus');
    }
}
