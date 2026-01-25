<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dompet;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tabungan;



class DompetController extends Controller
{
    public function index()
{
    $dompets = Dompet::where('user_id', Auth::id())->get();

    foreach ($dompets as $d) {

        // tabungan TANPA dompet tujuan = TERKUNCI
        $totalTabunganTerkunci = DB::table('tb_tabungan as t')
            ->join('tb_kategori_tabungan as k', 'k.id', '=', 't.kategori_tabungan_id')
            ->where('t.user_id', auth()->id())
            ->where('t.dompet_id', $d->id)
            ->whereNull('k.dompet_tujuan_id')
            ->sum('t.nominal');

        $d->total_tabungan_terkunci = $totalTabunganTerkunci;
        $d->saldo_bisa_dipakai = $d->saldo - $totalTabunganTerkunci;
    }

    return view('dompet.index', compact('dompets'));
}



    public function create()
    {
        return view('dompet.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_dompet' => 'required',
        'jenis' => 'required|in:cash,bank,ewallet',
        'bank_code' => 'nullable|string',
        'saldo' => 'required|numeric'
    ]);

    // 🚫 CEGAH MANUAL JIKA SUDAH PERNAH ITERASI
    if (in_array($request->jenis, ['bank', 'ewallet']) && $request->bank_code) {

        $exists = Dompet::where('user_id', auth()->id())
            ->where('bank_code', strtoupper($request->bank_code))
            ->where('is_dummy', 1)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Dompet ini sudah terhubung melalui iterasi digital dan tidak dapat ditambahkan secara manual.');
        }
    }

    Dompet::create([
        'user_id' => auth()->id(),
        'nama_dompet' => $request->nama_dompet,
        'jenis' => $request->jenis,
        'bank_code' => $request->bank_code ? strtoupper($request->bank_code) : null,
        'saldo' => $request->saldo,
        'is_dummy' => 0
    ]);

    return redirect()->route('dompet.index')
        ->with('success', 'Dompet berhasil ditambahkan');
}


    /* =====================
       LIST PROVIDER DUMMY
    ===================== */
    private function dummyProviders()
    {
        return [
            'bank' => [
                'BCA' => 'Bank Central Asia',
                'MANDIRI' => 'Bank Mandiri',
                'BNI' => 'Bank Negara Indonesia',
                'BRI' => 'Bank Rakyat Indonesia',
            ],
            'ewallet' => [
                'GOPAY' => 'GoPay',
                'OVO' => 'OVO',
                'DANA' => 'Dana',
                'SHOPEEPAY' => 'ShopeePay',
            ]
        ];
    }

    /* =====================
       PROVIDER BELUM PERNAH DIPAKAI
    ===================== */
    public function availableProviders()
    {
        $used = Dompet::where('user_id', auth()->id())
            ->pluck('bank_code')
            ->toArray();

        $providers = $this->dummyProviders();

        foreach ($providers as $jenis => $list) {
            foreach ($list as $code => $name) {
                if (in_array($code, $used)) {
                    unset($providers[$jenis][$code]);
                }
            }
        }

        return response()->json($providers);
    }

    /* =====================
       BUAT DOMPET BARU DARI PROVIDER
    ===================== */
    public function createFromProvider(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:bank,ewallet',
            'bank_code' => 'required'
        ]);

        $userId = auth()->id();
        $code = strtoupper($request->bank_code);

        // ❌ cegah iterasi 2x
        $exists = Dompet::where('user_id', $userId)
            ->where('bank_code', $code)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Dompet sudah pernah diiterasi.');
        }

        // buat dompet BARU (saldo masih 0 dulu)
        $dompet = Dompet::create([
            'user_id' => $userId,
            'nama_dompet' => $code,
            'jenis' => $request->jenis,
            'bank_code' => $code,
            'saldo' => 0,
            'is_dummy' => 1,
        ]);

        // 🔁 PANGGIL DUMMY API ITERASI SALDO
        return redirect()->route('dummy.wallet.iterate', $dompet->id);
    }

    public function edit($id)
{
    $dompet = Dompet::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    return view('dompet.edit', compact('dompet'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_dompet' => 'required|string',
        'saldo' => 'required|numeric|min:0'
    ]);

    $dompet = Dompet::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    // 🚫 Dompet hasil iterasi TIDAK BOLEH edit saldo manual
    if ($dompet->is_dummy) {
        return back()->with(
            'error',
            'Saldo dompet hasil iterasi tidak dapat diubah secara manual.'
        );
    }

    $dompet->update([
        'nama_dompet' => $request->nama_dompet,
        'saldo' => $request->saldo
    ]);

    return redirect()->route('dompet.index')
        ->with('success', 'Dompet berhasil diperbarui');
}

    
    public function destroy($id)
{
    $dompet = Dompet::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $dompet->delete();

    return redirect()->route('dompet.index')
        ->with('success', 'Dompet berhasil dihapus');
}

}
