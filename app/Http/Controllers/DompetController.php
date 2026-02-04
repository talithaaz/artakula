<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use App\Models\Dompet; // Model Dompet untuk akses data dompet.
use App\Models\Tabungan; // Model Tabungan (dipakai di proyek ini).
use Auth; // Facade Auth untuk user login.
use Illuminate\Http\Request; // Class Request untuk input HTTP.
use Illuminate\Support\Facades\DB; // Facade DB untuk query builder.

class DompetController extends Controller // Controller untuk CRUD dompet.
{
    public function index() // Menampilkan daftar dompet.
    {
        $dompets = Dompet::where('user_id', Auth::id())->get(); // Ambil dompet milik user login.

        foreach ($dompets as $d) { // Loop setiap dompet untuk hitung ringkasan.
            // TOTAL TABUNGAN (SEMUA)
            $totalTabungan = DB::table('tb_tabungan') // Mulai query tabungan.
                ->where('user_id', auth()->id()) // Filter user login.
                ->where('dompet_id', $d->id) // Filter per dompet.
                ->sum('nominal'); // Jumlahkan nominal tabungan.

            // TABUNGAN TERKUNCI
            $totalTabunganTerkunci = DB::table('tb_tabungan as t') // Query tabungan terkunci.
                ->join('tb_kategori_tabungan as k', 'k.id', '=', 't.kategori_tabungan_id') // Join kategori tabungan.
                ->where('t.user_id', auth()->id()) // Filter user login.
                ->where('t.dompet_id', $d->id) // Filter per dompet.
                ->whereNull('k.dompet_tujuan_id') // Hanya yang tidak punya dompet tujuan.
                ->sum('t.nominal'); // Jumlahkan nominal tabungan terkunci.

            // TABUNGAN TIDAK TERKUNCI
            $totalTabunganTidakTerkunci = $totalTabungan - $totalTabunganTerkunci; // Hitung tabungan tidak terkunci.

            // HITUNG SALDO
            $d->total_tabungan = $totalTabungan; // Simpan total tabungan di objek dompet.
            $d->total_tabungan_terkunci = $totalTabunganTerkunci; // Simpan total tabungan terkunci.
            $d->total_tabungan_tidak_terkunci = $totalTabunganTidakTerkunci; // Simpan total tabungan tidak terkunci.

            // SALDO YANG BISA DIPAKAI
            $d->saldo_bisa_dipakai = $d->saldo - $totalTabungan; // Hitung saldo yang bisa dipakai.
        }

        return view('dompet.index', compact('dompets')); // Tampilkan view dengan data dompet.
    }

    public function create() // Menampilkan form tambah dompet.
    {
        return view('dompet.create'); // Tampilkan view form tambah.
    }

    public function store(Request $request) // Menyimpan dompet baru.
    {
        $request->validate([ // Validasi input request.
            'nama_dompet' => 'required', // Nama dompet wajib.
            'jenis' => 'required|in:cash,bank,ewallet', // Jenis wajib dan harus salah satu.
            'bank_code' => 'nullable|string', // Kode bank opsional.
            'saldo' => 'required|numeric', // Saldo wajib angka.
        ]); // Selesai validasi.

        // CEGAH MANUAL JIKA SUDAH PERNAH ITERASI
        if (in_array($request->jenis, ['bank', 'ewallet']) && $request->bank_code) { // Cek jika bank/ewallet dengan bank_code.
            $exists = Dompet::where('user_id', auth()->id()) // Cari dompet dummy milik user.
                ->where('bank_code', strtoupper($request->bank_code)) // Bandingkan kode bank uppercase.
                ->where('is_dummy', 1) // Hanya dompet dummy.
                ->exists(); // Cek apakah ada.

            if ($exists) { // Jika dompet dummy sudah ada.
                return back() // Kembali ke halaman sebelumnya.
                    ->withInput() // Bawa input lama.
                    ->with('error', 'Dompet ini sudah terhubung melalui iterasi digital dan tidak dapat ditambahkan secara manual.'); // Pesan error.
            } // Selesai cek duplikasi.
        }

        Dompet::create([ // Buat dompet baru.
            'user_id' => auth()->id(), // Set user pemilik.
            'nama_dompet' => $request->nama_dompet, // Set nama dompet.
            'jenis' => $request->jenis, // Set jenis dompet.
            'bank_code' => $request->bank_code ? strtoupper($request->bank_code) : null, // Simpan kode bank uppercase jika ada.
            'saldo' => $request->saldo, // Set saldo awal.
            'is_dummy' => 0, // Tanda dompet manual.
        ]); // Selesai membuat dompet.

        return redirect()->route('dompet.index') // Redirect ke daftar dompet.
            ->with('success', 'Dompet berhasil ditambahkan'); // Pesan sukses.
    }

    /* =====================
       LIST PROVIDER DUMMY
    ===================== */
    private function dummyProviders() // Daftar provider dummy untuk iterasi.
    {
        return [ // Kembalikan array provider.
            'bank' => [ // Provider bank.
                'BCA' => 'Bank Central Asia', // Kode dan nama bank.
                'MANDIRI' => 'Bank Mandiri', // Kode dan nama bank.
                'BNI' => 'Bank Negara Indonesia', // Kode dan nama bank.
                'BRI' => 'Bank Rakyat Indonesia', // Kode dan nama bank.
            ],
            'ewallet' => [ // Provider e-wallet.
                'GOPAY' => 'GoPay', // Kode dan nama e-wallet.
                'OVO' => 'OVO', // Kode dan nama e-wallet.
                'DANA' => 'Dana', // Kode dan nama e-wallet.
                'SHOPEEPAY' => 'ShopeePay', // Kode dan nama e-wallet.
            ],
        ]; // Selesai daftar provider.
    }

    /* =====================
       PROVIDER BELUM PERNAH DIPAKAI
    ===================== */
    public function availableProviders() // Mengembalikan provider yang belum dipakai.
    {
        $used = Dompet::where('user_id', auth()->id()) // Ambil dompet milik user.
            ->pluck('bank_code') // Ambil hanya kolom bank_code.
            ->toArray(); // Ubah ke array.

        $providers = $this->dummyProviders(); // Ambil daftar provider dummy.

        foreach ($providers as $jenis => $list) { // Loop per jenis provider.
            foreach ($list as $code => $name) { // Loop per provider.
                if (in_array($code, $used)) { // Jika sudah dipakai.
                    unset($providers[$jenis][$code]); // Hapus dari daftar.
                }
            }
        } // Selesai filter provider.

        return response()->json($providers); // Kembalikan JSON provider tersedia.
    }

    /* =====================
       BUAT DOMPET BARU DARI PROVIDER
    ===================== */
    public function createFromProvider(Request $request) // Buat dompet baru dari provider.
    {
        $request->validate([ // Validasi input request.
            'jenis' => 'required|in:bank,ewallet', // Jenis wajib dan harus salah satu.
            'bank_code' => 'required', // Kode bank wajib.
        ]); // Selesai validasi.

        $userId = auth()->id(); // Simpan ID user login.
        $code = strtoupper($request->bank_code); // Ubah kode bank jadi uppercase.

        // CEGAH ITERASI 2x
        $exists = Dompet::where('user_id', $userId) // Cek dompet dengan kode sama.
            ->where('bank_code', $code) // Filter kode bank.
            ->exists(); // Cek apakah ada.

        if ($exists) { // Jika sudah pernah dibuat.
            return back()->with('error', 'Dompet sudah pernah diiterasi.'); // Kembali dengan error.
        } // Selesai cek duplikasi.

        // BUAT DOMPET BARU (SALDO MASIH 0 DULU)
        $dompet = Dompet::create([ // Buat dompet baru dari provider.
            'user_id' => $userId, // Set user pemilik.
            'nama_dompet' => $code, // Set nama dompet sama dengan kode.
            'jenis' => $request->jenis, // Set jenis dompet.
            'bank_code' => $code, // Simpan kode bank.
            'saldo' => 0, // Saldo awal 0.
            'is_dummy' => 1, // Tandai sebagai dompet dummy.
        ]); // Selesai membuat dompet.

        // PANGGIL DUMMY API ITERASI SALDO
        return redirect()->route('dummy.wallet.iterate', $dompet->id); // Redirect ke proses iterasi dummy.
    }

    public function edit($id) // Menampilkan form edit dompet.
    {
        $dompet = Dompet::where('id', $id) // Cari dompet sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        return view('dompet.edit', compact('dompet')); // Tampilkan view form edit.
    }

    public function update(Request $request, $id) // Memperbarui data dompet.
    {
        $request->validate([ // Validasi input request.
            'nama_dompet' => 'required|string', // Nama dompet wajib string.
            'saldo' => 'required|numeric|min:0', // Saldo wajib angka minimal 0.
        ]); // Selesai validasi.

        $dompet = Dompet::where('id', $id) // Cari dompet sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        // Dompet hasil iterasi TIDAK BOLEH edit saldo manual
        if ($dompet->is_dummy) { // Jika dompet hasil iterasi.
            return back()->with( // Kembali dengan error.
                'error', // Key session error.
                'Saldo dompet hasil iterasi tidak dapat diubah secara manual.' // Pesan error.
            );
        } // Selesai cek dummy.

        $dompet->update([ // Update data dompet.
            'nama_dompet' => $request->nama_dompet, // Update nama dompet.
            'saldo' => $request->saldo, // Update saldo dompet.
        ]); // Selesai update.

        return redirect()->route('dompet.index') // Redirect ke daftar dompet.
            ->with('success', 'Dompet berhasil diperbarui'); // Pesan sukses.
    }

    public function destroy($id) // Menghapus dompet.
    {
        $dompet = Dompet::where('id', $id) // Cari dompet sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        $dompet->delete(); // Hapus dompet.

        return redirect()->route('dompet.index') // Redirect ke daftar dompet.
            ->with('success', 'Dompet berhasil dihapus'); // Pesan sukses.
    }
}
