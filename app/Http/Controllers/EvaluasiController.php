<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // 1. Filter bulan & tahun
        // =========================
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // =========================
        // 2. Ambil seluruh transaksi (UNION)
        // =========================
        $transaksi =
    DB::table('tb_pemasukan')
        ->select(
            'tb_pemasukan.tanggal',
            DB::raw("'Pemasukan' as jenis"),
            DB::raw("'Pemasukan' as kategori"),
            'tb_pemasukan.keterangan',
            'tb_pemasukan.jumlah as nominal',
            'tb_dompet.nama_dompet as dompet'
        )
        ->join('tb_dompet', 'tb_pemasukan.dompet_id', '=', 'tb_dompet.id')
        ->where('tb_pemasukan.user_id', auth()->id())
        ->whereBetween('tb_pemasukan.tanggal', [$awalBulan, $akhirBulan])

    ->unionAll(

        DB::table('tb_pengeluaran')
            ->select(
                'tb_pengeluaran.tanggal',
                DB::raw("'Pengeluaran' as jenis"),
                'tb_kategori_pengeluaran.nama_kategori as kategori',
                'tb_pengeluaran.keterangan',
                DB::raw('-tb_pengeluaran.jumlah as nominal'),
                'tb_dompet.nama_dompet as dompet'
            )
            ->join('tb_kategori_pengeluaran', 'tb_pengeluaran.kategori_id', '=', 'tb_kategori_pengeluaran.id')
            ->join('tb_dompet', 'tb_pengeluaran.dompet_id', '=', 'tb_dompet.id')
            ->where('tb_pengeluaran.user_id', auth()->id())
            ->whereBetween('tb_pengeluaran.tanggal', [$awalBulan, $akhirBulan])
    )

    ->unionAll(

        DB::table('tb_tabungan')
            ->select(
                'tb_tabungan.tanggal',
                DB::raw("'Tabungan' as jenis"),
                'tb_kategori_tabungan.nama_kategori as kategori',
                'tb_tabungan.keterangan',
                'tb_tabungan.nominal as nominal',
                'tb_dompet.nama_dompet as dompet'
            )
            ->join('tb_kategori_tabungan', 'tb_tabungan.kategori_tabungan_id', '=', 'tb_kategori_tabungan.id')
            ->join('tb_dompet', 'tb_tabungan.dompet_id', '=', 'tb_dompet.id')
            ->where('tb_tabungan.user_id', auth()->id())
            ->whereBetween('tb_tabungan.tanggal', [$awalBulan, $akhirBulan])
    )

    ->orderBy('tanggal', 'desc')
    ->get();


        // =========================
        // 3. Hitung ringkasan
        // =========================
       $totalPemasukan   = $transaksi->where('jenis', 'Pemasukan')->sum('nominal');
$totalPengeluaran = abs($transaksi->where('jenis', 'Pengeluaran')->sum('nominal'));
$totalTabungan    = $transaksi->where('jenis', 'Tabungan')->sum('nominal');

// 1. Sisa kas bulanan (laporan keuangan)
$sisaKas = $totalPemasukan - $totalPengeluaran;

// 2. Uang yang benar-benar masih boleh dipakai user
$uangAman = $totalPemasukan - $totalPengeluaran - $totalTabungan;


        // =========================
        // 4. Kirim ke view
        // =========================
        return view('evaluasi.index', compact(
    'transaksi',
    'bulan',
    'tahun',
    'totalPemasukan',
    'totalPengeluaran',
    'totalTabungan',
    'sisaKas',
    'uangAman'
));

    }
}
