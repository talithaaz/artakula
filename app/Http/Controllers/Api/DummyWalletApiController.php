<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dompet;
use Carbon\Carbon;

class DummyWalletApiController extends Controller
{
    public function iterate($id)
    {
        $dompet = Dompet::findOrFail($id);

        $saldoProvider = [
            'bca' => 3250000,
            'mandiri' => 3614091,
            'bni' => 2104500,
            'bri' => 1782000,
            'gopay' => 511367,
            'ovo' => 109375,
            'dana' => 752100,
            'shopeepay' => 930250
        ];

        $provider = strtolower($dompet->bank_code);

        $dompet->update([
            'saldo' => $saldoProvider[$provider] ?? 500000,
            'is_dummy' => 1,
            'last_sync_at' => Carbon::now()
        ]);

        return redirect()->route('dompet.index')
            ->with('success', 'Dompet ' . $dompet->nama_dompet . ' berhasil diiterasi');
    }
}
