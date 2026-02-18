<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use App\Models\Tabungan; // Model tabungan.
use App\Models\KategoriTabungan; // Model kategori tabungan.
use App\Models\Dompet; // Model dompet.
use Illuminate\Http\Request; // Request dari form.
use Illuminate\Support\Facades\DB; // Facade DB untuk transaksi.

class TabunganController extends Controller // Controller untuk catat tabungan.
{
    /**
     * =========================
     * INDEX
     * Menampilkan daftar tabungan
     * berdasarkan filter bulan & tahun
     * =========================
     */
    public function index(Request $request) // Menampilkan daftar tabungan.
    {
        // Ambil bulan dari request, default bulan sekarang
        $bulan = $request->bulan ?? now()->month; // Bulan yang dipilih.

        // Ambil tahun dari request, default tahun sekarang
        $tahun = $request->tahun ?? now()->year; // Tahun yang dipilih.

        // Tentukan tanggal awal bulan
        $awalBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth(); // Awal bulan.

        // Tentukan tanggal akhir bulan
        $akhirBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth(); // Akhir bulan.

        // Ambil data tabungan user sesuai filter
        $tabungan = Tabungan::with(['kategori', 'dompet', 'sumberDompet']) // Eager load relasi.
            ->where('user_id', auth()->id()) // Filter user login.
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan]) // Filter periode.
            ->orderBy('tanggal', 'desc') // Urut tanggal terbaru.
            ->orderBy('id', 'desc') // Fallback jika tanggal sama.
            ->get(); // Ambil data tabungan.

        // Kirim data ke view index
        return view('tabungan.catat_tabungan.index', compact(
            'tabungan',
            'bulan',
            'tahun'
        )); // Tampilkan view index tabungan.
    }

    /**
     * =========================
     * CREATE
     * Menampilkan form tambah tabungan
     * =========================
     */
    public function create(Request $request) // Menampilkan form tambah tabungan.
    {
        // Ambil semua kategori tabungan milik user
        $kategoriTabungan = KategoriTabungan::where('user_id', auth()->id())->get(); // Daftar kategori.

        // Ambil semua dompet milik user
        $dompet = Dompet::where('user_id', auth()->id())->get(); // Daftar dompet.

        // Default kategori & dompet tujuan
        $kategoriTerpilih = null; // Kategori terpilih default null.
        $dompetTujuan = null; // Dompet tujuan default null.

        // Jika kategori dipilih (GET)
        if ($request->filled('kategori_tabungan_id')) { // Cek jika kategori dipilih.
            $kategoriTerpilih = KategoriTabungan::where('user_id', auth()->id()) // Cari kategori terpilih.
                ->with('dompetTujuan') // Load relasi dompet tujuan.
                ->find($request->kategori_tabungan_id); // Cari berdasarkan ID.

            // Ambil dompet tujuan dari kategori
            $dompetTujuan = $kategoriTerpilih?->dompetTujuan; // Ambil dompet tujuan jika ada.
        }

        // Kirim data ke view create
        return view('tabungan.catat_tabungan.create', compact(
            'kategoriTabungan',
            'dompet',
            'kategoriTerpilih',
            'dompetTujuan'
        )); // Tampilkan view create tabungan.
    }

    /**
     * =========================
     * STORE
     * Menyimpan data tabungan baru
     * =========================
     */
    public function store(Request $request) // Menyimpan tabungan baru.
    {
        // Validasi input
        $request->validate([ // Aturan validasi.
            'kategori_tabungan_id' => 'required|exists:tb_kategori_tabungan,id', // Kategori wajib valid.
            'sumber_dompet_id'     => 'required|exists:tb_dompet,id', // Dompet sumber wajib valid.
            'dompet_id'            => 'required|exists:tb_dompet,id', // Dompet tujuan wajib valid.
            'tanggal'              => 'required|date', // Tanggal wajib valid.
            'nominal'              => 'required|numeric|min:1', // Nominal wajib angka minimal 1.
        ]);

        DB::transaction(function () use ($request) { // Bungkus proses dalam transaksi.

            // Ambil kategori tabungan
            $kategori = KategoriTabungan::where('user_id', auth()->id()) // Cari kategori milik user.
                ->findOrFail($request->kategori_tabungan_id); // Gagal jika tidak ditemukan.

            // Ambil dompet sumber & lock
            $dompetSumber = Dompet::where('user_id', auth()->id()) // Cari dompet sumber.
                ->lockForUpdate() // Kunci baris untuk konsistensi saldo.
                ->findOrFail($request->sumber_dompet_id); // Gagal jika tidak ditemukan.

            // Validasi saldo dompet sumber
            if ($dompetSumber->saldo < $request->nominal) { // Jika saldo kurang.
                abort(400, 'Saldo tidak mencukupi'); // Hentikan dengan error.
            }

            // Kurangi saldo dompet sumber
            $dompetSumber->decrement('saldo', $request->nominal); // Kurangi saldo.

            // Jika kategori memiliki dompet tujuan
            if ($kategori->dompet_tujuan_id) { // Jika ada dompet tujuan.
                Dompet::lockForUpdate() // Kunci baris dompet tujuan.
                    ->where('id', $kategori->dompet_tujuan_id) // Filter dompet tujuan.
                    ->increment('saldo', $request->nominal); // Tambah saldo dompet tujuan.
            }

            // Simpan data tabungan
            Tabungan::create([ // Buat data tabungan.
                'user_id'              => auth()->id(), // Set user pemilik.
                'kategori_tabungan_id' => $kategori->id, // Set kategori tabungan.
                'sumber_dompet_id'     => $request->sumber_dompet_id, // Set dompet sumber.
                'dompet_id'            => $request->dompet_id, // Set dompet tujuan.
                'tanggal'              => $request->tanggal, // Set tanggal.
                'nominal'              => $request->nominal, // Set nominal.
                'keterangan'           => $request->keterangan, // Set keterangan.
            ]);
        });

        // app(\App\Http\Controllers\NotificationController::class)->generate();

        // Redirect kembali ke index sesuai filter
        return redirect()->route('tabungan.index', [ // Redirect ke daftar tabungan.
            'bulan' => $request->bulan, // Bawa parameter bulan.
            'tahun' => $request->tahun, // Bawa parameter tahun.
        ])->with('success', 'Tabungan berhasil ditambahkan'); // Pesan sukses.
    }

    /**
     * =========================
     * EDIT
     * Menampilkan form edit tabungan
     * =========================
     */
    public function edit($id) // Menampilkan form edit tabungan.
    {
        // Ambil data tabungan
        $tabungan = Tabungan::where('user_id', auth()->id()) // Cari tabungan milik user.
            ->findOrFail($id); // Gagal jika tidak ditemukan.

        // Ambil kategori & dompet
        $kategori = KategoriTabungan::where('user_id', auth()->id())->get(); // Daftar kategori.
        $dompet   = Dompet::where('user_id', auth()->id())->get(); // Daftar dompet.

        return view(
            'tabungan.catat_tabungan.edit',
            compact('tabungan', 'kategori', 'dompet')
        ); // Tampilkan view edit tabungan.
    }

    /**
     * =========================
     * UPDATE
     * Memperbarui data tabungan
     * =========================
     */
    public function update(Request $request, $id) // Memperbarui tabungan.
    {
        $request->validate([ // Aturan validasi.
            'kategori_tabungan_id' => 'required|exists:tb_kategori_tabungan,id', // Kategori wajib valid.
            'sumber_dompet_id'     => 'required|exists:tb_dompet,id', // Dompet sumber wajib valid.
            'tanggal'              => 'required|date', // Tanggal wajib valid.
            'nominal'              => 'required|numeric|min:1', // Nominal wajib angka minimal 1.
        ]);

        DB::transaction(function () use ($request, $id) { // Bungkus proses dalam transaksi.

            // Ambil data tabungan lama
            $tabungan = Tabungan::where('user_id', auth()->id()) // Cari tabungan milik user.
                ->lockForUpdate() // Kunci baris untuk konsistensi saldo.
                ->findOrFail($id); // Gagal jika tidak ditemukan.

            $kategoriLama = KategoriTabungan::find($tabungan->kategori_tabungan_id); // Kategori lama.
            $kategoriBaru = KategoriTabungan::find($request->kategori_tabungan_id); // Kategori baru.

            // Dompet lama
            $sumberLama = Dompet::lockForUpdate()->find($tabungan->sumber_dompet_id); // Dompet sumber lama.
            $tujuanLama = Dompet::lockForUpdate()->find($kategoriLama->dompet_tujuan_id); // Dompet tujuan lama.

            // Kembalikan saldo lama
            $sumberLama->increment('saldo', $tabungan->nominal); // Kembalikan saldo sumber lama.
            $tujuanLama->decrement('saldo', $tabungan->nominal); // Kurangi saldo tujuan lama.

            // Dompet baru
            $sumberBaru = Dompet::lockForUpdate()->find($request->sumber_dompet_id); // Dompet sumber baru.
            $tujuanBaru = Dompet::lockForUpdate()->find($kategoriBaru->dompet_tujuan_id); // Dompet tujuan baru.

            // Validasi saldo baru
            if ($sumberBaru->saldo < $request->nominal) { // Jika saldo kurang.
                abort(400, 'Saldo dompet tidak mencukupi'); // Hentikan dengan error.
            }

            // Update saldo baru
            $sumberBaru->decrement('saldo', $request->nominal); // Kurangi saldo sumber baru.
            $tujuanBaru->increment('saldo', $request->nominal); // Tambah saldo tujuan baru.

            // Update data tabungan
            $tabungan->update([ // Update data tabungan.
                'kategori_tabungan_id' => $request->kategori_tabungan_id, // Update kategori.
                'sumber_dompet_id'     => $request->sumber_dompet_id, // Update dompet sumber.
                'tanggal'              => $request->tanggal, // Update tanggal.
                'nominal'              => $request->nominal, // Update nominal.
                'keterangan'           => $request->keterangan, // Update keterangan.
            ]);
        });

        return redirect()->route('tabungan.index', [ // Redirect ke daftar tabungan.
            'bulan' => $request->bulan, // Bawa parameter bulan.
            'tahun' => $request->tahun, // Bawa parameter tahun.
        ])->with('success', 'Tabungan berhasil diperbarui'); // Pesan sukses.
    }

    /**
     * =========================
     * DESTROY
     * Menghapus data tabungan
     * =========================
     */
    public function destroy($id) // Menghapus tabungan.
    {
        DB::transaction(function () use ($id) { // Bungkus proses dalam transaksi.

            $tabungan = Tabungan::where('user_id', auth()->id()) // Cari tabungan milik user.
                ->lockForUpdate() // Kunci baris untuk konsistensi saldo.
                ->findOrFail($id); // Gagal jika tidak ditemukan.

            $kategori = KategoriTabungan::find($tabungan->kategori_tabungan_id); // Ambil kategori tabungan.

            // Kembalikan saldo ke dompet sumber
            Dompet::lockForUpdate() // Kunci baris dompet sumber.
                ->where('id', $tabungan->sumber_dompet_id) // Filter dompet sumber.
                ->increment('saldo', $tabungan->nominal); // Tambah saldo dompet sumber.

            // Kurangi saldo dompet tujuan
            if ($kategori?->dompet_tujuan_id) { // Jika ada dompet tujuan.
                Dompet::lockForUpdate() // Kunci baris dompet tujuan.
                    ->where('id', $kategori->dompet_tujuan_id) // Filter dompet tujuan.
                    ->decrement('saldo', $tabungan->nominal); // Kurangi saldo dompet tujuan.
            }

            // Hapus data tabungan
            $tabungan->delete(); // Hapus tabungan.
        });

        return back()->with('success', 'Tabungan berhasil dihapus'); // Pesan sukses.
    }
}
