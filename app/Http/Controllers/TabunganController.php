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
        $tabungan = Tabungan::where('user_id', auth()->id())->latest()->get();

        return view('tabungan.catat_tabungan.index', compact('tabungan'));
    }

    public function create()
    {
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get();
        $dompet = Dompet::where('user_id', auth()->id())->get();

        return view('tabungan.catat_tabungan.create', compact('kategoriTabungan', 'dompet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_tabungan_id' => 'required',
            'dompet_id' => 'required',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);

        $dompet = Dompet::findOrFail($request->dompet_id);

        // kurangi saldo dompet
        $dompet->decrement('saldo', $request->nominal);

        Tabungan::create([
            'user_id' => auth()->id(),
            'kategori_tabungan_id' => $request->kategori_tabungan_id,
            'dompet_id' => $request->dompet_id,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('tabungan.index');
    }
}
