<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use App\Models\Pemasukan; // Model Pemasukan untuk akses data pemasukan.
use App\Models\Dompet; // Model Dompet untuk akses data dompet.
use Illuminate\Http\Request; // Class Request untuk menangkap input HTTP.

class PemasukanController extends Controller // Controller untuk CRUD pemasukan.
{
    /**
     * Menampilkan daftar pemasukan per bulan & tahun
     */
    public function index(Request $request) // Menampilkan daftar pemasukan.
    {
        $bulan = $request->bulan ?? now()->month; // Ambil bulan dari request atau pakai bulan sekarang.
        $tahun = $request->tahun ?? now()->year; // Ambil tahun dari request atau pakai tahun sekarang.

        $pemasukan = Pemasukan::with('dompet') // Eager load relasi dompet.
            ->where('user_id', auth()->id()) // Filter hanya milik user yang login.
            ->whereMonth('tanggal', $bulan) // Filter berdasarkan bulan.
            ->whereYear('tanggal', $tahun) // Filter berdasarkan tahun.
            ->orderBy('tanggal', 'desc') // Urutkan tanggal terbaru dulu.
            ->orderBy('id', 'desc') // Urutkan ID terbaru sebagai penguat.
            ->get(); // Ambil hasil query.

        return view('pemasukan.index', compact('pemasukan', 'bulan', 'tahun')); // Tampilkan view dengan data.
    }

    /**
     * Form tambah pemasukan
     */
    public function create(Request $request) // Menampilkan form tambah pemasukan.
    {
        $bulan = $request->bulan ?? now()->month; // Ambil bulan dari request atau pakai bulan sekarang.
        $tahun = $request->tahun ?? now()->year; // Ambil tahun dari request atau pakai tahun sekarang.

        $dompets = Dompet::where('user_id', auth()->id())->get(); // Ambil daftar dompet milik user.

        return view('pemasukan.create', compact('dompets', 'bulan', 'tahun')); // Tampilkan view form dengan data.
    }

    /**
     * Simpan pemasukan baru
     */
    public function store(Request $request) // Menyimpan pemasukan baru.
    {
        $request->validate([ // Validasi input request.
            'dompet_id'  => 'required', // Dompet wajib dipilih.
            'keterangan' => 'required', // Keterangan wajib diisi.
            'jumlah'     => 'required|numeric|min:1', // Jumlah wajib angka minimal 1.
            'tanggal'    => 'required|date', // Tanggal wajib dan valid.
        ]); // Selesai validasi.

        // Pastikan dompet milik user
        $dompet = Dompet::where('id', $request->dompet_id) // Cari dompet sesuai pilihan.
            ->where('user_id', auth()->id()) // Pastikan dompet milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        // Simpan pemasukan
        $pemasukan = Pemasukan::create([ // Buat record pemasukan baru.
            'user_id'    => auth()->id(), // Set user pemilik.
            'dompet_id'  => $dompet->id, // Set dompet terkait.
            'keterangan' => $request->keterangan, // Simpan keterangan.
            'jumlah'     => (int) $request->jumlah, // Simpan jumlah sebagai integer.
            'tanggal'    => $request->tanggal, // Simpan tanggal pemasukan.
        ]); // Selesai membuat pemasukan.

        // Tambah saldo dompet
        $dompet->increment('saldo', $pemasukan->jumlah); // Tambah saldo sesuai jumlah pemasukan.

        return redirect()->route('pemasukan.index', [ // Redirect ke halaman daftar pemasukan.
            'bulan' => $request->bulan, // Bawa parameter bulan.
            'tahun' => $request->tahun, // Bawa parameter tahun.
        ])->with('success', 'Pemasukan berhasil ditambahkan'); // Pesan sukses.
    }

    /**
     * Form edit pemasukan
     */
    public function edit(Request $request, $id) // Menampilkan form edit pemasukan.
    {
        $pemasukan = Pemasukan::where('id', $id) // Cari pemasukan sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        $dompets = Dompet::where('user_id', auth()->id())->get(); // Ambil daftar dompet milik user.

        $bulan = $request->bulan ?? now()->month; // Ambil bulan dari request atau pakai bulan sekarang.
        $tahun = $request->tahun ?? now()->year; // Ambil tahun dari request atau pakai tahun sekarang.

        return view('pemasukan.edit', compact('pemasukan', 'dompets', 'bulan', 'tahun')); // Tampilkan view form edit.
    }

    /**
     * Update pemasukan
     */
    public function update(Request $request, $id) // Memperbarui data pemasukan.
    {
        $request->validate([ // Validasi input request.
            'dompet_id'  => 'required', // Dompet wajib dipilih.
            'keterangan' => 'required', // Keterangan wajib diisi.
            'jumlah'     => 'required|numeric|min:1', // Jumlah wajib angka minimal 1.
            'tanggal'    => 'required|date', // Tanggal wajib dan valid.
        ]); // Selesai validasi.

        $pemasukan = Pemasukan::where('id', $id) // Cari pemasukan sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        $oldDompet = Dompet::findOrFail($pemasukan->dompet_id); // Ambil dompet lama dari pemasukan.
        $newDompet = Dompet::where('id', $request->dompet_id) // Ambil dompet baru dari request.
            ->where('user_id', auth()->id()) // Pastikan dompet baru milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        $oldJumlah = (int) $pemasukan->jumlah; // Simpan jumlah lama sebagai integer.
        $newJumlah = (int) $request->jumlah; // Simpan jumlah baru sebagai integer.

        // Update data pemasukan
        $pemasukan->update([ // Update record pemasukan.
            'dompet_id'  => $newDompet->id, // Set dompet baru.
            'keterangan' => $request->keterangan, // Update keterangan.
            'jumlah'     => $newJumlah, // Update jumlah.
            'tanggal'    => $request->tanggal, // Update tanggal.
        ]); // Selesai update pemasukan.

        // Update saldo dompet
        if ($oldDompet->id === $newDompet->id) { // Jika dompet tidak berubah.
            $selisih = $newJumlah - $oldJumlah; // Hitung selisih jumlah baru dan lama.
            $oldDompet->increment('saldo', $selisih); // Sesuaikan saldo berdasarkan selisih.
        } else { // Jika dompet berubah.
            $oldDompet->decrement('saldo', $oldJumlah); // Kurangi saldo dompet lama.
            $newDompet->increment('saldo', $newJumlah); // Tambah saldo dompet baru.
        } // Selesai update saldo dompet.

        return redirect()->route('pemasukan.index', [ // Redirect ke halaman daftar pemasukan.
            'bulan' => $request->bulan, // Bawa parameter bulan.
            'tahun' => $request->tahun, // Bawa parameter tahun.
        ])->with('success', 'Pemasukan berhasil diupdate'); // Pesan sukses.
    }

    /**
     * Hapus pemasukan
     */
    public function destroy(Request $request, $id) // Menghapus pemasukan.
    {
        $pemasukan = Pemasukan::where('id', $id) // Cari pemasukan sesuai ID.
            ->where('user_id', auth()->id()) // Pastikan milik user login.
            ->firstOrFail(); // Gagal jika tidak ditemukan.

        $dompet = Dompet::findOrFail($pemasukan->dompet_id); // Ambil dompet terkait pemasukan.

        // Kurangi saldo dompet
        $dompet->decrement('saldo', $pemasukan->jumlah); // Kurangi saldo sesuai jumlah pemasukan.

        // Hapus data pemasukan
        $pemasukan->delete(); // Hapus record pemasukan.

        return back()->with('success', 'Pemasukan berhasil dihapus'); // Kembali dengan pesan sukses.
    }
}
