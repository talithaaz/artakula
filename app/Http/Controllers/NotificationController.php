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
        $bulan = now()->month;
        $tahun = now()->year;

        /* ================= PEMASUKAN VS PENGELUARAN ================= */
        $pemasukan = DB::table('tb_pemasukan')
            ->where('user_id',$userId)
            ->whereMonth('tanggal',$bulan)
            ->whereYear('tanggal',$tahun)
            ->sum('nominal');

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
        }

        /* ================= SALDO DOMPET ================= */
        $dompet = DB::table('tb_dompet')
            ->where('user_id',$userId)
            ->get();

        foreach($dompet as $d){
            if($d->saldo <= 100000){
                $this->store(
                    $userId,
                    "wallet_$d->id$bulan$tahun",
                    "Dompet",
                    "Saldo Dompet Menipis",
                    "Saldo {$d->nama_dompet} tersisa Rp ".number_format($d->saldo,0,',','.')
                );
            }
        }

        /* ================= BUDGET ================= */
        $kategori = DB::table('tb_kategori_pengeluaran')
            ->where('user_id',$userId)
            ->get();

        foreach($kategori as $k){

            if(!$k->budget) continue;

            $total = DB::table('tb_pengeluaran')
                ->where('kategori_id',$k->id)
                ->where('user_id',$userId)
                ->whereMonth('tanggal',$bulan)
                ->sum('jumlah');

            if($k->budget > 0){
                $persen = ($total/$k->budget)*100;

                if($persen >= 80){
                    $this->store(
                        $userId,
                        "budget_$k->id$bulan$tahun",
                        "Budget",
                        "Budget Hampir Habis",
                        "Kategori {$k->nama} hampir melebihi budget."
                    );
                }
            }
        }

        /* ================= TIDAK MENABUNG ================= */
        $lastSaving = DB::table('tb_tabungan')
            ->where('user_id',$userId)
            ->latest('tanggal')
            ->first();

        if($lastSaving){
            $hari = now()->diffInDays(Carbon::parse($lastSaving->tanggal));

            if($hari >= 7){
                $this->store(
                    $userId,
                    "nosave_$bulan$tahun",
                    "Tabungan",
                    "Kamu Belum Menabung",
                    "Sudah 7 hari kamu belum menabung."
                );
            }
        }

        return response()->json(['status'=>'ok']);
    }


    /* =========================================
       LIST NOTIF
    ========================================= */
    public function list()
    {
        $notif = Notification::where('user_id',auth()->id())
            ->orderBy('id','desc')
            ->take(10)
            ->get();

        return response()->json($notif);
    }


    /* =========================================
       TANDAI DIBACA
    ========================================= */
    public function read($id)
    {
        $notif = Notification::where('id',$id)
            ->where('user_id',auth()->id())
            ->first();

        if($notif){
            $notif->is_read = 1;
            $notif->save();
        }

        return response()->json(['status'=>'read']);
    }


    /* =========================================
       SIMPAN (ANTI DUPLIKAT)
    ========================================= */
    private function store($userId,$reference,$type,$title,$message)
    {
        $cek = Notification::where('reference',$reference)
            ->where('user_id',$userId)
            ->first();

        if($cek) return;

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
