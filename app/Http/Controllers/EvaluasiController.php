<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Evaluasi;

class EvaluasiController extends Controller
{
public function index(Request $request)
{
    // ================= FILTER =================
    $bulan = $request->bulan ?? now()->month;
    $tahun = $request->tahun ?? now()->year;

    $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
    $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth();

    // ================= HISTORICAL (3 BULAN) =================
    $riwayat = Evaluasi::where('user_id', auth()->id())
        ->where(function($q) use ($bulan, $tahun){
            $q->where('tahun', '<', $tahun)
              ->orWhere(function($qq) use ($bulan, $tahun){
                  $qq->where('tahun', $tahun)
                     ->where('bulan', '<', $bulan);
              });
        })
        ->orderByDesc('tahun')
        ->orderByDesc('bulan')
        ->take(3)
        ->get();

    $rataPemasukan   = $riwayat->avg('total_pemasukan') ?? 0;

    // ================= AMBIL TRANSAKSI =================
    $transaksi = DB::table('tb_pemasukan')
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
                ->join('tb_kategori_pengeluaran','tb_pengeluaran.kategori_id','=','tb_kategori_pengeluaran.id')
                ->join('tb_dompet','tb_pengeluaran.dompet_id','=','tb_dompet.id')
                ->where('tb_pengeluaran.user_id',auth()->id())
                ->whereBetween('tb_pengeluaran.tanggal',[$awalBulan,$akhirBulan])
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
                ->join('tb_kategori_tabungan','tb_tabungan.kategori_tabungan_id','=','tb_kategori_tabungan.id')
                ->join('tb_dompet','tb_tabungan.dompet_id','=','tb_dompet.id')
                ->where('tb_tabungan.user_id',auth()->id())
                ->whereBetween('tb_tabungan.tanggal',[$awalBulan,$akhirBulan])
        )
        ->orderBy('tanggal','desc')
        ->get();

    // ================= RINGKASAN =================
    $totalPemasukan   = $transaksi->where('jenis','Pemasukan')->sum('nominal');
    $totalPengeluaran = abs($transaksi->where('jenis','Pengeluaran')->sum('nominal'));
    $totalTabungan    = $transaksi->where('jenis','Tabungan')->sum('nominal');

    $sisaKas  = $totalPemasukan - $totalPengeluaran;
    $uangAman = $totalPemasukan - $totalPengeluaran - $totalTabungan;

    // ================= RASIO KEUANGAN =================
    $rasioPengeluaran = $totalPemasukan > 0
        ? ($totalPengeluaran / $totalPemasukan) * 100
        : 0;

    $rasioTabungan = $totalPemasukan > 0
        ? ($totalTabungan / $totalPemasukan) * 100
        : 0;

    // ================= TREND PEMASUKAN =================
    $trendPemasukan = 'Stabil';
    if($rataPemasukan > 0){
        $selisih = (($totalPemasukan - $rataPemasukan)/$rataPemasukan)*100;
        if($selisih > 15)      $trendPemasukan = 'Naik';
        elseif($selisih < -15) $trendPemasukan = 'Turun';
    }

    $analisisPemasukan = "Pemasukan stabil dibanding bulan sebelumnya.";

if($trendPemasukan == 'Naik')
    $analisisPemasukan = "Pemasukan meningkat dibanding rata-rata 3 bulan terakhir.";

elseif($trendPemasukan == 'Turun')
    $analisisPemasukan = "Pemasukan menurun dibanding rata-rata 3 bulan terakhir.";


    // ================= PREDIKAT KEUANGAN =================
    if($totalPemasukan == 0){
        $predikat = 'Belum Dapat Dievaluasi';
        $pesanUtama = 'Belum ada pemasukan pada bulan ini.';
        $warna = 'secondary';
    }
    elseif($rasioPengeluaran > 100){
        $predikat = 'Bahaya';
        $pesanUtama = 'Pengeluaran Anda melebihi pemasukan.';
        $warna = 'danger';
    }
    elseif($rasioPengeluaran > 85){
        $predikat = 'Waspada';
        $pesanUtama = 'Sebagian besar pemasukan habis untuk pengeluaran.';
        $warna = 'warning';
    }
    elseif($rasioPengeluaran > 60){
        $predikat = 'Cukup';
        $pesanUtama = 'Pengeluaran mulai tinggi, perlu dikontrol.';
        $warna = 'info';
    }
    else{
        $predikat = 'Sehat';
        $pesanUtama = 'Kondisi keuangan stabil dan terkendali.';
        $warna = 'success';
    }

    // ================= KATEGORI DOMINAN =================
    $kategoriDominan = DB::table('tb_pengeluaran')
        ->join('tb_kategori_pengeluaran','tb_pengeluaran.kategori_id','=','tb_kategori_pengeluaran.id')
        ->select('tb_kategori_pengeluaran.nama_kategori',DB::raw('SUM(tb_pengeluaran.jumlah) as total'))
        ->where('tb_pengeluaran.user_id',auth()->id())
        ->whereBetween('tb_pengeluaran.tanggal',[$awalBulan,$akhirBulan])
        ->groupBy('tb_kategori_pengeluaran.nama_kategori')
        ->orderByDesc('total')
        ->first();

    $persenDominan = ($kategoriDominan && $totalPengeluaran>0)
        ? ($kategoriDominan->total/$totalPengeluaran)*100
        : 0;

    // ================= ANALISIS PENGELUARAN PER KATEGORI =================
$kategoriPengeluaran = DB::table('tb_pengeluaran')
    ->join('tb_kategori_pengeluaran','tb_pengeluaran.kategori_id','=','tb_kategori_pengeluaran.id')
    ->select(
        'tb_kategori_pengeluaran.nama_kategori',
        DB::raw('SUM(tb_pengeluaran.jumlah) as total')
    )
    ->where('tb_pengeluaran.user_id',auth()->id())
    ->whereBetween('tb_pengeluaran.tanggal',[$awalBulan,$akhirBulan])
    ->groupBy('tb_kategori_pengeluaran.nama_kategori')
    ->get();

$kategoriBoros = [];
$kategoriAman = [];

foreach($kategoriPengeluaran as $k){
    $persen = ($totalPemasukan>0) ? ($k->total/$totalPemasukan)*100 : 0;

    if($persen > 30){
        $kategoriBoros[] = $k->nama_kategori;
    }else{
        $kategoriAman[] = $k->nama_kategori;
    }
}

$tabunganKategori = DB::table('tb_tabungan')
    ->join('tb_kategori_tabungan','tb_tabungan.kategori_tabungan_id','=','tb_kategori_tabungan.id')
    ->select(
        'tb_kategori_tabungan.nama_kategori',
        DB::raw('SUM(tb_tabungan.nominal) as total'),
        'tb_kategori_tabungan.target_nominal'
    )
    ->where('tb_tabungan.user_id',auth()->id())
    ->whereBetween('tb_tabungan.tanggal',[$awalBulan,$akhirBulan])
    ->groupBy('tb_kategori_tabungan.nama_kategori','tb_kategori_tabungan.target_nominal')
    ->get();

$tabunganTercapai = [];
$tabunganHampir = [];

foreach($tabunganKategori as $t){
    if($t->target_nominal > 0){
        $persen = ($t->total/$t->target_nominal)*100;

        if($persen >= 100)
            $tabunganTercapai[] = $t->nama_kategori;

        elseif($persen >= 70)
            $tabunganHampir[] = $t->nama_kategori;
    }
}


$analisisTabungan = [];

if($rasioTabungan >= 20)
    $analisisTabungan[] = "Progres tabungan sangat baik bulan ini.";
elseif($rasioTabungan >= 10)
    $analisisTabungan[] = "Tabungan sudah memenuhi standar minimal.";
else
    $analisisTabungan[] = "Tabungan masih di bawah 10% pemasukan.";


    // ================= INSIGHT ENGINE =================
    $insights = [];

    if($trendPemasukan == 'Turun')
        $insights[] = "Pemasukan menurun dibanding rata-rata 3 bulan terakhir.";

    if($rasioTabungan < 10 && $totalPemasukan>0)
        $insights[] = "Tabungan Anda kurang dari 10% pemasukan.";

    if($kategoriDominan && $persenDominan > 40)
        $insights[] = "Pengeluaran didominasi kategori {$kategoriDominan->nama_kategori} ({$persenDominan}%).";

    if($uangAman < 0)
        $insights[] = "Dana bulan ini tidak mencukupi setelah menabung.";

$rekomendasi = [];

if($rasioPengeluaran > 80)
    $rekomendasi[] = "Kurangi pengeluaran konsumtif bulan depan.";

if($rasioTabungan < 10)
    $rekomendasi[] = "Sisihkan minimal 10% pemasukan untuk tabungan.";

if(!empty($kategoriBoros))
    $rekomendasi[] = "Prioritaskan pengeluaran penting dan batasi kategori: ".implode(', ',$kategoriBoros).".";

$motivasi = [];

if($predikat == 'Sehat')
    $motivasi[] = "Pertahankan kebiasaan finansialmu, kamu sudah berada di jalur yang benar!";

elseif($predikat == 'Waspada')
    $motivasi[] = "Sedikit pengendalian pengeluaran akan membuat kondisi keuanganmu jauh lebih stabil.";

else
    $motivasi[] = "Tidak apa-apa, memperbaiki kebiasaan keuangan adalah proses bertahap.";


    // ================= SIMPAN EVALUASI =================
    $evaluasi = Evaluasi::updateOrCreate(
        ['user_id'=>auth()->id(),'bulan'=>$bulan,'tahun'=>$tahun],
        [
            'total_pemasukan'=>$totalPemasukan,
            'total_pengeluaran'=>$totalPengeluaran,
            'total_tabungan'=>$totalTabungan,
            'sisa_kas'=>$sisaKas,
            'rasio_tabungan'=>$rasioTabungan,
            'predikat'=>$predikat,
            'trend_keuangan'=>$trendPemasukan,
            'kategori_dominan'=>$kategoriDominan->nama_kategori??null,
            'persen_dominan'=>$persenDominan,
            'last_calculated_at'=>now()
        ]
    );

    return view('evaluasi.index',compact(
        'transaksi','bulan','tahun',
        'totalPemasukan','totalPengeluaran','totalTabungan',
        'sisaKas','uangAman',
        'evaluasi','predikat','persenDominan',
        'kategoriDominan','insights',
        'pesanUtama','warna', 'analisisPemasukan',
'kategoriBoros',
'kategoriAman',
'analisisTabungan',
'tabunganTercapai',
'tabunganHampir',
'rekomendasi',
'motivasi'

    ));
}


}
