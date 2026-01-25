<?php

namespace App\Http\Controllers;

use App\Models\KategoriTabungan;
use Illuminate\Http\Request;
use App\Models\Dompet;

class KategoriTabunganController extends Controller
{
    public function index()
    {
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();

        return view('tabungan.kategori_tabungan.index', compact('kategoriTabungan'));
    }

    public function create()
{
    $dompet = Dompet::where('user_id', auth()->id())->get();

    return view(
        'tabungan.kategori_tabungan.create',
        compact('dompet')
    );
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
            'dompet_tujuan_id' => $request->dompet_tujuan_id, // ← INI
            'target_nominal' => $request->target_nominal,
            'target_waktu' => $request->target_waktu,
        ]);

        return redirect()->route('kategoriTabungan.index');
    }

    public function edit(KategoriTabungan $kategoriTabungan)
{
    $kategori = $kategoriTabungan;

    $dompet = Dompet::where('user_id', auth()->id())->get();

    $jumlahTransaksi = $kategori->tabungan()->count();

    return view(
        'tabungan.kategori_tabungan.edit',
        compact('kategoriTabungan','dompet','jumlahTransaksi')
    );
}


    public function update(Request $request, KategoriTabungan $kategoriTabungan)
    {
        $kategoriTabungan->update($request->all());
$data = $request->validate([
        'nama_kategori'      => 'required|string',
        'target_nominal'     => 'required|numeric',
        'target_waktu'       => 'required|date',
        'dompet_tujuan_id'   => 'nullable|exists:dompet,id',
    ]);
        // cek apakah kategori tabungan sudah punya transaksi
    if ($kategoriTabungan->catatTabungan()->count() == 0) {
        // BELUM ADA TRANSAKSI → dompet tujuan boleh diubah
        $kategoriTabungan->update($data);
    } else {
        // SUDAH ADA TRANSAKSI → dompet tujuan DIKUNCI
        unset($data['dompet_tujuan_id']);
        $kategoriTabungan->update($data);
    }
        return redirect()->route('kategoriTabungan.index')->with('success', 'Kategori tabungan berhasil diperbarui');
    }

    public function destroy(KategoriTabungan $kategoriTabungan)
    {
        $kategoriTabungan->delete();
        return back();
    }
}
