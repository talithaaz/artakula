<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use App\Models\KategoriPengeluaran; // Model kategori pengeluaran.
use App\Models\Pengeluaran; // Model pengeluaran.
use App\Models\Dompet; // Model dompet.
use Illuminate\Http\Request; // Class Request untuk input HTTP.
use Illuminate\Support\Facades\DB; // Facade DB untuk transaksi.
use Carbon\Carbon; // Library tanggal Carbon.

class KategoriPengeluaranController extends Controller // Controller kategori pengeluaran.
{
    /**
     * Menampilkan daftar kategori pengeluaran
     * berdasarkan filter bulan dan tahun
     */
    public function index(Request $request) // Menampilkan daftar kategori pengeluaran.
    {
        // Ambil bulan & tahun dari request, jika tidak ada gunakan bulan & tahun saat ini
        $bulan = $request->bulan ?? now()->month; // Bulan yang dipilih.
        $tahun = $request->tahun ?? now()->year; // Tahun yang dipilih.

        // Tentukan awal dan akhir bulan
        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfMonth(); // Tanggal awal bulan.
        $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth(); // Tanggal akhir bulan.

        // Ambil kategori pengeluaran milik user
        // yang masih aktif pada periode bulan yang dipilih
        $kategori = KategoriPengeluaran::where('user_id', auth()->id()) // Filter user login.
            ->where(function ($q) use ($awalBulan, $akhirBulan) { // Filter periode awal.
                $q->whereNull('periode_awal') // Jika periode awal kosong.
                  ->orWhere('periode_awal', '<=', $akhirBulan); // Atau periode awal sebelum akhir bulan.
            })
            ->where(function ($q) use ($awalBulan, $akhirBulan) { // Filter periode akhir.
                $q->whereNull('periode_akhir') // Jika periode akhir kosong.
                  ->orWhere('periode_akhir', '>=', $awalBulan); // Atau periode akhir setelah awal bulan.
            })
            ->get(); // Ambil data kategori.

        // Hitung total pengeluaran (terpakai) dan sisa budget tiap kategori
        $kategori->map(function ($k) use ($awalBulan, $akhirBulan) { // Loop tiap kategori.

            // Total pengeluaran pada kategori dan periode bulan aktif
            $terpakai = Pengeluaran::where('user_id', auth()->id()) // Filter user login.
                ->where('kategori_id', $k->id) // Filter kategori.
                ->whereBetween('tanggal', [$awalBulan, $akhirBulan]) // Filter tanggal dalam bulan.
                ->sum('jumlah'); // Jumlahkan pengeluaran.

            // Tambahkan properti dinamis ke object kategori
            $k->terpakai = $terpakai; // Simpan total terpakai.
            $k->sisa     = $k->budget - $terpakai; // Hitung sisa budget.

            return $k; // Kembalikan kategori.
        });

        // Kirim data ke view
        return view( // Tampilkan view daftar kategori.
            'pengeluaran.kategori_pengeluaran.index',
            compact('kategori', 'bulan', 'tahun')
        );
    }

    /**
     * Menampilkan form tambah kategori pengeluaran
     */
    public function create() // Menampilkan form tambah kategori.
    {
        return view('pengeluaran.kategori_pengeluaran.create'); // Tampilkan view form tambah.
    }

    /**
     * Menyimpan data kategori pengeluaran baru
     */
    public function store(Request $request) // Menyimpan kategori pengeluaran baru.
    {
        // Validasi input user
        $request->validate([ // Aturan validasi.
            'nama_kategori' => 'required|string', // Nama kategori wajib string.
            'budget'        => 'required|numeric|min:0', // Budget wajib angka minimal 0.
            'periode_awal'  => 'nullable|date', // Periode awal opsional.
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal', // Periode akhir >= periode awal.
        ]);

        // Simpan kategori pengeluaran
        KategoriPengeluaran::create([ // Buat kategori baru.
            'user_id'       => auth()->id(), // Set user pemilik.
            'nama_kategori' => $request->nama_kategori, // Set nama kategori.
            'budget'        => $request->budget, // Set budget.
            'periode_awal'  => $request->periode_awal, // Set periode awal.
            'periode_akhir' => $request->periode_akhir, // Set periode akhir.
        ]);

        // Redirect kembali ke halaman index sesuai bulan & tahun sebelumnya
        return redirect()->route('kategori_pengeluaran.index', [ // Redirect ke daftar kategori.
                'bulan' => $request->bulan, // Bawa parameter bulan.
                'tahun' => $request->tahun, // Bawa parameter tahun.
            ])
            ->with('success', 'Kategori pengeluaran berhasil ditambahkan'); // Pesan sukses.
    }

    /**
     * Menampilkan form edit kategori pengeluaran
     */
    public function edit($id) // Menampilkan form edit kategori.
    {
        // Ambil kategori berdasarkan id dan user
        $kategori = KategoriPengeluaran::where('id', $id) // Cari kategori sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        return view('pengeluaran.kategori_pengeluaran.edit', compact('kategori')); // Tampilkan view form edit.
    }

    /**
     * Memperbarui data kategori pengeluaran
     */
    public function update(Request $request, $id) // Memperbarui kategori pengeluaran.
    {
        // Ambil kategori yang akan diupdate
        $kategori = KategoriPengeluaran::where('id', $id) // Cari kategori sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        // Validasi input
        $request->validate([ // Aturan validasi.
            'nama_kategori' => 'required|string', // Nama kategori wajib string.
            'budget'        => 'required|numeric|min:0', // Budget wajib angka minimal 0.
            'periode_awal'  => 'nullable|date', // Periode awal opsional.
            'periode_akhir' => 'nullable|date|after_or_equal:periode_awal', // Periode akhir >= periode awal.
        ]);

        // Update data kategori
        $kategori->update([ // Update data kategori.
            'nama_kategori' => $request->nama_kategori, // Update nama kategori.
            'budget'        => $request->budget, // Update budget.
            'periode_awal'  => $request->periode_awal, // Update periode awal.
            'periode_akhir' => $request->periode_akhir, // Update periode akhir.
        ]);

        // Redirect kembali ke index sesuai filter sebelumnya
        return redirect()->route('kategori_pengeluaran.index', [ // Redirect ke daftar kategori.
                'bulan' => $request->bulan, // Bawa parameter bulan.
                'tahun' => $request->tahun, // Bawa parameter tahun.
            ])
            ->with('success', 'Kategori pengeluaran berhasil diupdate'); // Pesan sukses.
    }

    /**
     * Menghapus kategori pengeluaran beserta seluruh transaksinya
     * dan mengembalikan saldo ke dompet
     */
    public function destroy($id) // Menghapus kategori dan transaksi terkait.
    {
        DB::transaction(function () use ($id) { // Bungkus proses dalam transaksi.

            // Ambil kategori milik user
            $kategori = KategoriPengeluaran::where('id', $id) // Cari kategori sesuai ID.
                ->where('user_id', auth()->id()) // Pastikan milik user login.
                ->firstOrFail(); // Gagal jika tidak ditemukan.

            // Ambil seluruh pengeluaran yang menggunakan kategori ini
            $pengeluaranList = Pengeluaran::where('kategori_id', $id) // Filter pengeluaran per kategori.
                ->where('user_id', auth()->id()) // Filter user login.
                ->get(); // Ambil daftar pengeluaran.

            // Loop tiap pengeluaran
            foreach ($pengeluaranList as $pengeluaran) { // Iterasi daftar pengeluaran.

                // Kembalikan saldo ke dompet terkait
                Dompet::where('id', $pengeluaran->dompet_id) // Cari dompet terkait.
                    ->where('user_id', auth()->id()) // Pastikan milik user login.
                    ->increment('saldo', $pengeluaran->jumlah); // Tambah saldo dompet.

                /** @var \App\Models\Pengeluaran $pengeluaran */
                // Hapus transaksi pengeluaran
                $pengeluaran->delete(); // Hapus pengeluaran.
            }

            // Hapus kategori pengeluaran
            $kategori->delete(); // Hapus kategori.
        });

        // Redirect kembali ke halaman index
        return redirect() // Redirect ke daftar kategori.
            ->route('kategori_pengeluaran.index', request()->query()) // Bawa query filter.
            ->with(
                'success', // Key pesan sukses.
                'Kategori & seluruh transaksi dihapus. Saldo dompet berhasil dikembalikan.' // Pesan sukses.
            );
    }
}
