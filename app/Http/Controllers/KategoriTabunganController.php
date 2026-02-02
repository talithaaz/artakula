<?php

namespace App\Http\Controllers;

use App\Models\KategoriTabungan;
use Illuminate\Http\Request;
use App\Models\Dompet;

class KategoriTabunganController extends Controller
{
    public function index(Request $request)
{
    $bulan = $request->bulan ?? now()->month;
    $tahun = $request->tahun ?? now()->year;

    $awalBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();

    $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())
        ->where(function ($q) use ($awalBulan) {
            $q->whereNull('target_waktu')
              ->orWhere('target_waktu', '>=', $awalBulan);
        })
        ->withSum('catatTabungan as total_ditabung', 'nominal')
        ->get();

    return view(
        'tabungan.kategori_tabungan.index',
        compact('kategoriTabungan','bulan','tahun')
    );
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
            'dompet_tujuan_id'  => 'required|exists:tb_dompet,id',
        ]);

        KategoriTabungan::create([
            'user_id' => auth()->id(),
            'nama_kategori' => $request->nama_kategori,
            'dompet_tujuan_id' => $request->dompet_tujuan_id, // ← INI
            'target_nominal' => $request->target_nominal,
            'target_waktu' => $request->target_waktu,
        ]);

        return redirect()->route('kategoriTabungan.index', [
    'bulan' => $request->bulan,
    'tahun' => $request->tahun,])
        ->with('success', 'Kategori tabungan berhasil ditambahkan');
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
        'dompet_tujuan_id'   => 'nullable|exists:tb_dompet,id',
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
        return redirect()->route('kategoriTabungan.index', [
    'bulan' => $request->bulan,
    'tahun' => $request->tahun,])->with('success', 'Kategori tabungan berhasil diperbarui');
    }

    public function destroy(KategoriTabungan $kategoriTabungan)
    {
        $kategoriTabungan->delete();
        return back()->with('success', 'Kategori tabungan berhasil dihapus');
    }
}
