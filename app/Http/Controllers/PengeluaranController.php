<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use App\Models\Pengeluaran; // Model pengeluaran.
use App\Models\Dompet; // Model dompet.
use App\Models\KategoriPengeluaran; // Model kategori pengeluaran.
use Illuminate\Http\Request; // Class Request untuk input HTTP.
use Illuminate\Support\Facades\DB; // Facade DB untuk query/transaction.
use Carbon\Carbon; // Library tanggal Carbon.

class PengeluaranController extends Controller // Controller untuk catat pengeluaran.
{
    /**
     * Menampilkan daftar pengeluaran berdasarkan bulan & tahun
     */
    public function index(Request $request) // Menampilkan daftar pengeluaran.
    {
        // Ambil bulan & tahun dari request, default ke bulan & tahun sekarang
        $bulan = $request->bulan ?? now()->month; // Bulan yang dipilih.
        $tahun = $request->tahun ?? now()->year; // Tahun yang dipilih.

        // Tentukan range awal & akhir bulan
        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfMonth(); // Tanggal awal bulan.
        $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth(); // Tanggal akhir bulan.

        // Ambil data pengeluaran user pada periode tersebut
        $pengeluaran = Pengeluaran::with(['dompet', 'kategori']) // Eager load dompet & kategori.
            ->where('user_id', auth()->id()) // Filter user login.
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan]) // Filter periode tanggal.
            ->orderBy('tanggal', 'desc') // Urutkan tanggal terbaru.
            ->orderBy('id', 'desc') // Urutkan ID terbaru.
            ->get(); // Ambil data pengeluaran.

        // Kirim data ke view
        return view( // Tampilkan view daftar pengeluaran.
            'pengeluaran.catat_pengeluaran.index',
            compact('pengeluaran', 'bulan', 'tahun')
        );
    }

    /**
     * Menampilkan form tambah pengeluaran
     */
    public function create(Request $request) // Menampilkan form tambah pengeluaran.
    {
        // Ambil semua dompet milik user
        $dompets = Dompet::where('user_id', auth()->id())->get(); // Daftar dompet user.

        // Ambil bulan & tahun dari filter
        $bulan = $request->bulan ?? now()->month; // Bulan yang dipilih.
        $tahun = $request->tahun ?? now()->year; // Tahun yang dipilih.

        // Tanggal acuan berdasarkan filter (BUKAN tanggal hari ini)
        $tanggalAcuan = Carbon::create($tahun, $bulan, 1)->toDateString(); // Tanggal acuan.

        // Ambil kategori yang aktif pada tanggal acuan
        $kategori = KategoriPengeluaran::where('user_id', auth()->id()) // Filter user login.
            ->where(function ($q) use ($tanggalAcuan) { // Filter periode awal.
                $q->whereNull('periode_awal') // Jika periode awal kosong.
                  ->orWhere('periode_awal', '<=', $tanggalAcuan); // Atau periode awal sebelum acuan.
            })
            ->where(function ($q) use ($tanggalAcuan) { // Filter periode akhir.
                $q->whereNull('periode_akhir') // Jika periode akhir kosong.
                  ->orWhere('periode_akhir', '>=', $tanggalAcuan); // Atau periode akhir setelah acuan.
            })
            ->get(); // Ambil daftar kategori aktif.

        return view( // Tampilkan view form tambah.
            'pengeluaran.catat_pengeluaran.create',
            compact('dompets', 'kategori', 'bulan', 'tahun')
        );
    }

    /**
     * Menyimpan data pengeluaran baru
     */
    public function store(Request $request) // Menyimpan pengeluaran baru.
    {
        // Validasi input
        $request->validate([ // Aturan validasi.
            'dompet_id'   => 'required', // Dompet wajib dipilih.
            'kategori_id' => 'required', // Kategori wajib dipilih.
            'keterangan'  => 'required|string', // Keterangan wajib string.
            'jumlah'      => 'required|numeric|min:1', // Jumlah wajib angka minimal 1.
            'tanggal'     => 'required|date', // Tanggal wajib valid.
        ]);

        // Validasi kategori milik user & masih dalam periode aktif
        $kategori = KategoriPengeluaran::where('id', $request->kategori_id) // Cari kategori sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->where(function ($q) use ($request) { // Filter periode awal.
                $q->whereNull('periode_awal') // Jika periode awal kosong.
                  ->orWhere('periode_awal', '<=', $request->tanggal); // Atau periode awal <= tanggal transaksi.
            })
            ->where(function ($q) use ($request) { // Filter periode akhir.
                $q->whereNull('periode_akhir') // Jika periode akhir kosong.
                  ->orWhere('periode_akhir', '>=', $request->tanggal); // Atau periode akhir >= tanggal transaksi.
            })
            ->firstOrFail(); // Gagal jika kategori tidak valid.

        // Ambil dompet user
        $dompet = Dompet::where('id', $request->dompet_id) // Cari dompet sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika dompet tidak valid.

        // Hitung total tabungan pada dompet tersebut
        $totalTabungan = DB::table('tb_tabungan') // Query tabungan.
            ->where('user_id', auth()->id()) // Filter user login.
            ->where('dompet_id', $request->dompet_id) // Filter dompet.
            ->sum('nominal'); // Jumlahkan nominal tabungan.

        // Saldo yang benar-benar bisa dipakai
        $saldoBisaDipakai = $dompet->saldo - $totalTabungan; // Saldo minus total tabungan.

        // Cek kecukupan saldo
        if ($request->jumlah > $saldoBisaDipakai) { // Jika jumlah melebihi saldo bisa dipakai.
            return back()->with('error', 'Saldo tidak mencukupi karena sebagian sudah masuk tabungan'); // Kembali dengan error.
        }

        // Simpan data pengeluaran
        Pengeluaran::create([ // Buat pengeluaran baru.
            'user_id'     => auth()->id(), // Set user pemilik.
            'dompet_id'   => $request->dompet_id, // Set dompet.
            'kategori_id' => $request->kategori_id, // Set kategori.
            'keterangan'  => $request->keterangan, // Set keterangan.
            'jumlah'      => $request->jumlah, // Set jumlah.
            'tanggal'     => $request->tanggal, // Set tanggal.
        ]);

        // Kurangi saldo dompet
        $dompet->decrement('saldo', $request->jumlah); // Kurangi saldo sesuai jumlah.
// app(\App\Http\Controllers\NotificationController::class)->generate();

        return redirect()->route('pengeluaran.index', [ // Redirect ke daftar pengeluaran.
            'bulan' => $request->bulan, // Bawa parameter bulan.
            'tahun' => $request->tahun, // Bawa parameter tahun.
        ])->with('success', 'Pengeluaran berhasil ditambahkan'); // Pesan sukses.
    }

    /**
     * Menampilkan form edit pengeluaran
     */
    public function edit($id) // Menampilkan form edit pengeluaran.
    {
        // Ambil data pengeluaran milik user
        $pengeluaran = Pengeluaran::where('id', $id) // Cari pengeluaran sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        // Ambil semua dompet user
        $dompets = Dompet::where('user_id', auth()->id())->get(); // Daftar dompet user.

        // Tanggal transaksi sebagai acuan periode kategori
        $tanggal = $pengeluaran->tanggal; // Tanggal transaksi.

        // Ambil kategori yang aktif pada tanggal transaksi
        $kategori = KategoriPengeluaran::where('user_id', auth()->id()) // Filter user login.
            ->where(function ($q) use ($tanggal) { // Filter periode awal.
                $q->whereNull('periode_awal') // Jika periode awal kosong.
                  ->orWhere('periode_awal', '<=', $tanggal); // Atau periode awal <= tanggal transaksi.
            })
            ->where(function ($q) use ($tanggal) { // Filter periode akhir.
                $q->whereNull('periode_akhir') // Jika periode akhir kosong.
                  ->orWhere('periode_akhir', '>=', $tanggal); // Atau periode akhir >= tanggal transaksi.
            })
            ->get(); // Ambil daftar kategori aktif.

        return view( // Tampilkan view form edit.
            'pengeluaran.catat_pengeluaran.edit',
            compact('pengeluaran', 'dompets', 'kategori')
        );
    }

    /**
     * Update data pengeluaran
     */
    public function update(Request $request, $id) // Memperbarui pengeluaran.
    {
        // Ambil data pengeluaran
        $pengeluaran = Pengeluaran::where('id', $id) // Cari pengeluaran sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        // Validasi input
        $request->validate([ // Aturan validasi.
            'dompet_id'   => 'required', // Dompet wajib dipilih.
            'kategori_id' => 'required', // Kategori wajib dipilih.
            'keterangan'  => 'required|string', // Keterangan wajib string.
            'jumlah'      => 'required|numeric|min:1', // Jumlah wajib angka minimal 1.
            'tanggal'     => 'required|date', // Tanggal wajib valid.
        ]);

        // Ambil dompet lama & baru
        $dompetLama = Dompet::where('id', $pengeluaran->dompet_id)->first(); // Dompet sebelumnya.
        $dompetBaru = Dompet::where('id', $request->dompet_id)->first(); // Dompet baru.

        // Rollback saldo dompet lama
        $dompetLama->increment('saldo', $pengeluaran->jumlah); // Kembalikan saldo lama.

        // Hitung saldo dompet baru yang bisa dipakai
        $totalTabungan = DB::table('tb_tabungan') // Query tabungan.
            ->where('user_id', auth()->id()) // Filter user login.
            ->where('dompet_id', $request->dompet_id) // Filter dompet baru.
            ->sum('nominal'); // Jumlahkan tabungan.

        $saldoBisaDipakai = $dompetBaru->saldo - $totalTabungan; // Hitung saldo bisa dipakai.

        // Jika saldo tidak cukup, rollback lagi
        if ($request->jumlah > $saldoBisaDipakai) { // Jika jumlah melebihi saldo bisa dipakai.
            $dompetLama->decrement('saldo', $pengeluaran->jumlah); // Kembalikan perubahan rollback.
            return back()->with('error', 'Saldo tidak mencukupi'); // Kembali dengan error.
        }

        // Update data pengeluaran
        $pengeluaran->update([ // Update data pengeluaran.
            'dompet_id'   => $request->dompet_id, // Update dompet.
            'kategori_id' => $request->kategori_id, // Update kategori.
            'keterangan'  => $request->keterangan, // Update keterangan.
            'jumlah'      => $request->jumlah, // Update jumlah.
            'tanggal'     => $request->tanggal, // Update tanggal.
        ]);

        // Kurangi saldo dompet baru
        $dompetBaru->decrement('saldo', $request->jumlah); // Kurangi saldo dompet baru.

        // Ambil bulan & tahun dari tanggal transaksi
        $bulan = Carbon::parse($request->tanggal)->month; // Bulan transaksi.
        $tahun = Carbon::parse($request->tanggal)->year; // Tahun transaksi.

        return redirect() // Redirect ke daftar pengeluaran.
            ->route('pengeluaran.index', compact('bulan', 'tahun'))
            ->with('success', 'Pengeluaran berhasil diupdate'); // Pesan sukses.
    }

    /**
     * Menghapus pengeluaran
     */
    public function destroy($id) // Menghapus pengeluaran.
    {
        DB::transaction(function () use ($id) { // Bungkus proses dalam transaksi.
            // Ambil data pengeluaran
            $pengeluaran = Pengeluaran::where('id', $id) // Cari pengeluaran sesuai ID.
                ->where('user_id', auth()->id()) // Pastikan milik user login.
                ->firstOrFail(); // Gagal jika tidak ditemukan.

            // Kembalikan saldo dompet
            Dompet::where('id', $pengeluaran->dompet_id) // Cari dompet terkait.
                ->where('user_id', auth()->id()) // Pastikan milik user login.
                ->increment('saldo', $pengeluaran->jumlah); // Tambah saldo dompet.

            // Hapus pengeluaran
            $pengeluaran->delete(); // Hapus data pengeluaran.
        });

        return back()->with('success', 'Pengeluaran berhasil dihapus'); // Kembali dengan pesan sukses.
    }
}
