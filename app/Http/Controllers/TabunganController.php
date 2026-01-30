<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\KategoriTabungan;
use App\Models\Dompet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TabunganController extends Controller
{
    public function index()
    {
        $tabungan = Tabungan::with(['kategori', 'dompet', 'sumberDompet'])
    ->where('user_id', auth()->id())
    ->orderBy('tanggal', 'desc')
    ->orderBy('id', 'desc')
    ->get();


        return view('tabungan.catat_tabungan.index', compact('tabungan'));
    }

   public function create(Request $request)
{
    $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();
    $dompet = Dompet::where('user_id', auth()->id())->get();

    $kategoriTerpilih = null;
    $dompetTujuan = null;

    if ($request->filled('kategori_tabungan_id')) {
        $kategoriTerpilih = KategoriTabungan::where('user_id', auth()->id())
            ->with('dompetTujuan')
            ->find($request->kategori_tabungan_id);

        $dompetTujuan = $kategoriTerpilih?->dompetTujuan;
    }

    return view('tabungan.catat_tabungan.create', compact(
        'kategoriTabungan',
        'dompet',
        'kategoriTerpilih',
        'dompetTujuan'
    ));
}



    public function store(Request $request)
{
    $request->validate([
        'kategori_tabungan_id' => 'required|exists:tb_kategori_tabungan,id',
        'sumber_dompet_id'     => 'required|exists:tb_dompet,id',
        'dompet_id'            => 'required|exists:tb_dompet,id',
        'tanggal'              => 'required|date',
        'nominal'              => 'required|numeric|min:1',
    ]);

    DB::transaction(function () use ($request) {

    $kategori = KategoriTabungan::where('user_id', auth()->id())
        ->findOrFail($request->kategori_tabungan_id);

    $dompetSumber = Dompet::where('user_id', auth()->id())
        ->lockForUpdate()
        ->findOrFail($request->sumber_dompet_id);

    if ($dompetSumber->saldo < $request->nominal) {
        abort(400, 'Saldo tidak mencukupi');
    }

    // ➖ selalu kurangi saldo sumber
    $dompetSumber->decrement('saldo', $request->nominal);

    // ➕ JIKA kategori punya dompet tujuan
    if ($kategori->dompet_tujuan_id) {
        Dompet::lockForUpdate()
            ->where('id', $kategori->dompet_tujuan_id)
            ->increment('saldo', $request->nominal);
    }

    // 📝 catat tabungan
    Tabungan::create([
        'user_id' => auth()->id(),
        'kategori_tabungan_id' => $kategori->id,
        'sumber_dompet_id'     => $request->sumber_dompet_id, // ✅ WAJIB
        'dompet_id'            => $request->dompet_id,        // tujuan
        'tanggal' => $request->tanggal,
        'nominal' => $request->nominal,
        'keterangan' => $request->keterangan,
    ]);
});


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
        'kategori_tabungan_id' => 'required|exists:tb_kategori_tabungan,id',
        'sumber_dompet_id'     => 'required|exists:tb_dompet,id',
        'tanggal'              => 'required|date',
        'nominal'              => 'required|numeric|min:1',
    ]);

    DB::transaction(function () use ($request, $id) {

        $tabungan = Tabungan::where('user_id', auth()->id())
            ->lockForUpdate()
            ->findOrFail($id);

        $kategoriLama = KategoriTabungan::find($tabungan->kategori_tabungan_id);
        $kategoriBaru = KategoriTabungan::find($request->kategori_tabungan_id);

        // DOMPET LAMA
        $sumberLama = Dompet::lockForUpdate()->find($tabungan->sumber_dompet_id);
        $tujuanLama = Dompet::lockForUpdate()->find($kategoriLama->dompet_tujuan_id);

        // 🔄 BALIKKAN SALDO LAMA (INI YANG KEMARIN SALAH)
        $sumberLama->increment('saldo', $tabungan->nominal);
        $tujuanLama->decrement('saldo', $tabungan->nominal);

        // DOMPET BARU
        $sumberBaru = Dompet::lockForUpdate()->find($request->sumber_dompet_id);
        $tujuanBaru = Dompet::lockForUpdate()->find($kategoriBaru->dompet_tujuan_id);

        // ❗ validasi saldo
        if ($sumberBaru->saldo < $request->nominal) {
            abort(400, 'Saldo dompet tidak mencukupi');
        }

        // ➖➕ SALDO BARU
        $sumberBaru->decrement('saldo', $request->nominal);
        $tujuanBaru->increment('saldo', $request->nominal);

        // 📝 UPDATE DATA
        $tabungan->update([
            'kategori_tabungan_id' => $request->kategori_tabungan_id,
            'sumber_dompet_id'     => $request->sumber_dompet_id,
            'tanggal'              => $request->tanggal,
            'nominal'              => $request->nominal,
            'keterangan'           => $request->keterangan,
        ]);
    });

    return redirect()->route('tabungan.index')
        ->with('success', 'Tabungan berhasil diperbarui');
}



    public function destroy($id)
{
    DB::transaction(function () use ($id) {

        $tabungan = Tabungan::where('user_id', auth()->id())
            ->lockForUpdate()
            ->findOrFail($id);

        $kategori = KategoriTabungan::find($tabungan->kategori_tabungan_id);

        // 🔙 balikin ke dompet sumber
        Dompet::lockForUpdate()
            ->where('id', $tabungan->dompet_id)
            ->increment('saldo', $tabungan->nominal);

        // 🔻 kurangi dari dompet tujuan
        if ($kategori?->dompet_tujuan_id) {
            Dompet::lockForUpdate()
                ->where('id', $kategori->dompet_tujuan_id)
                ->decrement('saldo', $tabungan->nominal);
        }

        $tabungan->delete();
    });

    return back()->with('success', 'Tabungan berhasil dihapus');
}

}
