<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationController extends Controller
{

    /* =========================================
       AUTO CHECK SAAT HALAMAN DIBUKA
    ========================================= */
    public function check()
    {

        $userId = auth()->id();

        // timezone wajib WIB
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $bulan = Carbon::now('Asia/Jakarta')->month;
        $tahun = Carbon::now('Asia/Jakarta')->year;

        /* ================= WELCOME USER BARU ================= */
        $punyaData =
            DB::table('tb_pemasukan')->where('user_id',$userId)->exists() ||
            DB::table('tb_pengeluaran')->where('user_id',$userId)->exists() ||
            DB::table('tb_tabungan')->where('user_id',$userId)->exists();

        if(!$punyaData){
            $this->store(
                $userId,
                "welcome_$userId",
                "Sistem",
                "Selamat Datang di Artakula 👋",
                "Mulai dengan mencatat pemasukan pertamamu agar sistem bisa mulai menganalisis kondisi keuanganmu."
            );
        }

        /* ===== TIDAK ADA TRANSAKSI 3 HARI ===== */
$lastPemasukan = DB::table('tb_pemasukan')
    ->where('user_id',$userId)
    ->max('tanggal');

$lastPengeluaran = DB::table('tb_pengeluaran')
    ->where('user_id',$userId)
    ->max('tanggal');

$lastTanggal = max($lastPemasukan, $lastPengeluaran);

if($lastTanggal){
    $selisihHari = Carbon::parse($lastTanggal)->diffInDays(Carbon::now('Asia/Jakarta'));

    if($selisihHari >= 3){
        $this->store(
            $userId,
"inactive_".date('Ymd', strtotime($lastTanggal)),            "Pengingat",
            "Belum Ada Pencatatan Transaksi",
            "Sudah $selisihHari hari kamu belum mencatat transaksi. "
            ."Mencatat secara rutin membantu sistem memberikan analisis yang lebih akurat."
        );
    }
}

        /* ================= PEMASUKAN VS PENGELUARAN ================= */
        $pemasukan = DB::table('tb_pemasukan')
            ->where('user_id',$userId)
            ->whereMonth('tanggal',$bulan)
            ->whereYear('tanggal',$tahun)
            ->sum('jumlah');

        $pengeluaran = DB::table('tb_pengeluaran')
            ->where('user_id',$userId)
            ->whereMonth('tanggal',$bulan)
            ->whereYear('tanggal',$tahun)
            ->sum('jumlah');

        if($pemasukan > 0){
            $rasio = ($pengeluaran/$pemasukan)*100;

            if($rasio >= 80){
                $this->store(
                    $userId,
                    "finance_$bulan$tahun",
                    "Keuangan",
                    "Pengeluaran Hampir Menghabiskan Pemasukan",
                    "Pengeluaranmu sudah mencapai 80% dari pemasukan bulan ini."
                );
            }

            /* ===== DEFISIT BULANAN ===== */
if($pemasukan > 0 && $pengeluaran > $pemasukan){
    $this->store(
        $userId,
        "deficit_$bulan$tahun",
        "Kesehatan Finansial",
        "Pengeluaran Melebihi Pemasukan",
        "Bulan ini total pengeluaranmu Rp "
        .number_format($pengeluaran,0,',','.')
        ." melebihi pemasukan Rp "
        .number_format($pemasukan,0,',','.')
        .". Jika terjadi terus menerus, kondisi keuanganmu bisa tidak stabil."
    );
}
        }

        /* ================= DOMPET ================= */
        $dompet = DB::table('tb_dompet')
            ->where('user_id',$userId)
            ->get();
            
            $medianHarian = 0;
            
        /* ===== RATA-RATA HARIAN USER (SEMUA DOMPET) ===== */
$hariAktifUser = DB::table('tb_pengeluaran')
    ->where('user_id',$userId)
    ->whereMonth('tanggal',$bulan)
    ->whereYear('tanggal',$tahun)
    ->selectRaw('COUNT(DISTINCT DATE(tanggal)) as hari')
    ->value('hari');

if($hariAktifUser > 0){
    $totalBulanUser = DB::table('tb_pengeluaran')
        ->where('user_id',$userId)
        ->whereMonth('tanggal',$bulan)
        ->whereYear('tanggal',$tahun)
        ->sum('jumlah');

    /* ===== AMBIL PENGELUARAN HARIAN USER DALAM 14 HARI TERAKHIR ===== */
$dailySpendings = DB::table('tb_pengeluaran')
    ->selectRaw('DATE(tanggal) as tgl, SUM(jumlah) as total')
    ->where('user_id',$userId)
    ->whereBetween('tanggal', [
        Carbon::now('Asia/Jakarta')->subDays(14)->startOfDay(),
        Carbon::now('Asia/Jakarta')->endOfDay()
    ])
    ->groupBy('tgl')
    ->pluck('total')
    ->toArray();

/* ===== HITUNG MEDIAN (KEBIASAAN HARIAN ASLI USER) ===== */
sort($dailySpendings);
$count = count($dailySpendings);

if($count > 0){
    $middle = floor($count/2);

    if($count % 2){
        $medianHarian = $dailySpendings[$middle];
    }else{
        $medianHarian = ($dailySpendings[$middle-1] + $dailySpendings[$middle]) / 2;
    }
}else{
    $medianHarian = 0;
}
}else{
    $rataHarianUser = 0;
}

/* ===== TOTAL PENGELUARAN HARI INI (SEMUA DOMPET) ===== */
$totalHariIni = DB::table('tb_pengeluaran')
    ->where('user_id',$userId)
    ->whereDate('tanggal',$today)
    ->sum('jumlah');

/* ===== PENGELUARAN TIDAK WAJAR (BOROS) ===== */
if($medianHarian > 0 && $totalHariIni > ($medianHarian * 2.2)){
    $this->store(
        $userId,
        "abnormal_total_$today",
        "Analisis Keuangan",
        "Pengeluaran Hari Ini Tidak Biasa",
        "Hari ini kamu mengeluarkan Rp "
        .number_format($totalHariIni,0,',','.')
        .". Biasanya pengeluaran harianmu sekitar Rp "
        .number_format($medianHarian,0,',','.')
        .". Pengeluaran hari ini jauh lebih tinggi dari kebiasaanmu."
    );
}

        foreach($dompet as $d){

            /* ====== TOTAL HARI INI ====== */
            $hariIni = DB::table('tb_pengeluaran')
                ->where('user_id',$userId)
                ->where('dompet_id',$d->id)
                ->whereDate('tanggal',$today)
                ->sum('jumlah');

            /* ====== JUMLAH TRANSAKSI HARI INI ====== */
            $jumlahTransaksi = DB::table('tb_pengeluaran')
                ->where('user_id',$userId)
                ->where('dompet_id',$d->id)
                ->whereDate('tanggal',$today)
                ->count();

            /* ====== RATA HARIAN ====== */
            $hariAktif = DB::table('tb_pengeluaran')
                ->where('user_id',$userId)
                ->where('dompet_id',$d->id)
                ->whereMonth('tanggal',$bulan)
                ->whereYear('tanggal',$tahun)
                ->selectRaw('COUNT(DISTINCT DATE(tanggal)) as hari')
                ->value('hari');

            

            if($hariAktif > 0){
                $totalBulan = DB::table('tb_pengeluaran')
                    ->where('user_id',$userId)
                    ->where('dompet_id',$d->id)
                    ->whereMonth('tanggal',$bulan)
                    ->whereYear('tanggal',$tahun)
                    ->sum('jumlah');

                $rataBulan = $totalBulan / $hariAktif;
            }else{
                $rataBulan = 0;
            }

            /* ====== SALDO MENIPIS ====== */
            if($rataBulan > 0 && $d->saldo <= ($rataBulan * 2)){
                $this->store(
                    $userId,
                    "walletlow_{$d->id}_$bulan$tahun",
                    "Dompet",
                    "Saldo {$d->nama_dompet} Menipis",
                    "Sisa saldo Rp ".number_format($d->saldo,0,',','.')." hanya cukup untuk ±2 hari."
                );
            }

            

            /* ====== TERLALU SERING TRANSAKSI (IMPULSIF) ====== */
if($jumlahTransaksi >= 5 && $hariIni >= 50000){
    $this->store(
        $userId,
        "freq_{$d->id}_$today",
        "Perilaku Penggunaan",
        "Terlalu Banyak Transaksi Hari Ini",
        "Hari ini {$d->nama_dompet} sudah dipakai {$jumlahTransaksi} kali. "
        ."Banyak transaksi kecil dalam satu hari sering tidak terasa, "
        ."namun bisa membuat pengeluaranmu membengkak tanpa disadari."
    );
}
        }

        /* ===== TABUNGAN TIDAK BERTAMBAH ===== */
$setoranTabungan = DB::table('tb_tabungan')
    ->where('user_id',$userId)
    ->whereMonth('tanggal',$bulan)
    ->whereYear('tanggal',$tahun)
    ->sum('jumlah');

$totalTabungan = DB::table('tb_tabungan')
    ->where('user_id',$userId)
    ->exists();

if($totalTabungan && $setoranTabungan == 0){
    $this->store(
        $userId,
       "saving_{$bulan}{$tahun}",
        "Tabungan",
        "Tabungan Belum Bertambah",
        "Bulan ini belum ada penambahan pada tabunganmu. "
        ."Coba sisihkan sedikit pemasukan untuk menjaga keamanan finansialmu."
    );
}

        return response()->json(['status'=>'ok']);
    }

    /* =========================================
       LIST NOTIF
    ========================================= */
    public function list()
    {
        return Notification::where('user_id',auth()->id())
            ->orderBy('id','desc')
            ->take(10)
            ->get();
    }

    /* =========================================
       READ
    ========================================= */
    public function read($id)
    {
        Notification::where('id',$id)
            ->where('user_id',auth()->id())
            ->update(['is_read'=>1]);

        return response()->json(['status'=>'read']);
    }

    /* =========================================
       STORE (ANTI DUPLIKAT)
    ========================================= */
    private function store($userId,$reference,$type,$title,$message)
    {
        if(Notification::where('reference',$reference)->where('user_id',$userId)->exists()){
            return;
        }

        Notification::create([
            'user_id'=>$userId,
            'reference'=>$reference,
            'type'=>$type,
            'title'=>$title,
            'message'=>$message,
            'is_read'=>0
        ]);
    }
}
