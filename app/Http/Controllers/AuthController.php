<?php // Mulai file PHP.

namespace App\Http\Controllers; // Namespace controller Laravel.

use Illuminate\Http\Request; // Class Request untuk input HTTP.
use App\Models\User; // Model User.
use Illuminate\Support\Facades\Auth; // Facade Auth untuk autentikasi.
use Illuminate\Support\Facades\Hash; // Facade Hash untuk password.
use Laravel\Socialite\Facades\Socialite; // Facade Socialite untuk login Google.
use App\Models\Dompet;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use App\Models\Tabungan;
use Illuminate\Support\Facades\DB;
use App\Models\KategoriTabungan;





class AuthController extends Controller // Controller autentikasi.
{
    // ===== VIEW =====
    public function showRegisterForm() // Menampilkan form registrasi.
    {
        return view('auth.register'); // Tampilkan view register.
    }

    public function showLoginForm() // Menampilkan form login.
    {
        return view('auth.login'); // Tampilkan view login.
    }

    // ===== REGISTER =====
    public function register(Request $request) // Menangani proses register.
    {
        $request->validate([ // Validasi input register.
            'name'     => 'required|string|max:255', // Nama wajib string max 255.
            'username' => 'required|string|max:100|unique:tb_users,username', // Username wajib unik.
            'email'    => 'required|email|unique:tb_users,email', // Email wajib unik.
            'password' => 'required|min:6', // Password minimal 6.
        ]);

        $user = User::create([ // Buat user baru.
            'name'     => $request->name, // Set nama.
            'username' => $request->username, // Set username.
            'email'    => $request->email, // Set email.
            'password' => Hash::make($request->password), // Hash password.
        ]);

        // login dulu
        Auth::login($user); // Login otomatis setelah register.

        // kirim email verifikasi
        $user->sendEmailVerificationNotification(); // Kirim email verifikasi.

        // arahkan ke halaman cek email
        return redirect()->route('verification.notice'); // Redirect ke halaman verifikasi.
    }

    // ===== LOGIN =====
    public function login(Request $request) // Menangani proses login.
    {
        $request->validate([ // Validasi input login.
            'email'    => 'required|email', // Email wajib valid.
            'password' => 'required', // Password wajib.
        ]);

        if (Auth::attempt($request->only('email', 'password'))) { // Coba login dengan kredensial.
            return redirect()->route('dashboard'); // Jika sukses, ke dashboard.
        }

        return back()->withErrors([ // Jika gagal, kembali dengan error.
            'email' => 'Email atau password salah',
        ]);
    }

    // ===== LOGOUT =====
    public function logout() // Logout user.
    {
        Auth::logout(); // Hapus sesi user.
        return redirect()->route('landing'); // Kembali ke halaman landing.
    }

    // ===== DASHBOARD =====
    public function dashboard()
{
    $bulan = now()->month;
    $tahun = now()->year;

    $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfMonth();
    $akhirBulan = Carbon::create($tahun, $bulan, 1)->endOfMonth();

    // TOTAL SALDO SELURUH DOMPET
    $totalSaldo = Dompet::where('user_id', auth()->id())
        ->sum('saldo');

    // TOTAL PEMASUKAN BULAN BERJALAN
    $totalPemasukan = Pemasukan::where('user_id', auth()->id())
        ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
        ->sum('jumlah');

    // TOTAL PENGELUARAN BULAN BERJALAN
    $totalPengeluaran = Pengeluaran::where('user_id', auth()->id())
        ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
        ->sum('jumlah');

    // TEKS PERIODE (READ-ONLY)
    $namaPeriode = Carbon::now()->translatedFormat('F Y');

    // ===== GRAFIK TABUNGAN TAHUNAN =====
$tabunganPerBulan = Tabungan::select(
        DB::raw('MONTH(tanggal) as bulan'),
        DB::raw('SUM(nominal) as total')
    )
    ->where('user_id', auth()->id())
    ->whereYear('tanggal', $tahun)
    ->groupBy(DB::raw('MONTH(tanggal)'))
    ->pluck('total', 'bulan');

// Pastikan semua bulan ada (Jan–Des)
$dataTabunganTahunan = [];
for ($i = 1; $i <= 12; $i++) {
    $dataTabunganTahunan[] = $tabunganPerBulan[$i] ?? 0;
}

// ===== GRAFIK PROPORSI PENGELUARAN (BULAN BERJALAN) =====
$pengeluaranPerKategori = Pengeluaran::select(
        'tb_kategori_pengeluaran.nama_kategori',
        DB::raw('SUM(tb_pengeluaran.jumlah) as total')
    )
    ->join(
        'tb_kategori_pengeluaran',
        'tb_pengeluaran.kategori_id',
        '=',
        'tb_kategori_pengeluaran.id'
    )
    ->where('tb_pengeluaran.user_id', auth()->id())
    ->whereBetween('tb_pengeluaran.tanggal', [$awalBulan, $akhirBulan])
    ->groupBy('tb_kategori_pengeluaran.nama_kategori')
    ->get();

    $labelPengeluaran = $pengeluaranPerKategori->pluck('nama_kategori');
$dataPengeluaran  = $pengeluaranPerKategori->pluck('total');

$targetTabungan = KategoriTabungan::select(
        'tb_kategori_tabungan.id',
        'tb_kategori_tabungan.nama_kategori',
        'tb_kategori_tabungan.target_nominal',
        DB::raw('COALESCE(SUM(tb_tabungan.nominal), 0) as total_tabungan')
    )
    ->leftJoin('tb_tabungan', function ($join) {
        $join->on(
            'tb_tabungan.kategori_tabungan_id',
            '=',
            'tb_kategori_tabungan.id'
        )
        ->where('tb_tabungan.user_id', auth()->id());
    })
    ->where('tb_kategori_tabungan.user_id', auth()->id())
    ->groupBy(
        'tb_kategori_tabungan.id',
        'tb_kategori_tabungan.nama_kategori',
        'tb_kategori_tabungan.target_nominal'
    )
    ->get()
    ->map(function ($item) {
        $item->persen = $item->target_nominal > 0
            ? min(100, round(($item->total_tabungan / $item->target_nominal) * 100))
            : 0;
        return $item;
    });

    $awal  = Carbon::now()->subDays(1)->startOfDay();
$akhir = Carbon::now()->endOfDay();

$transaksiTerkini =
    DB::table('tb_pemasukan')
        ->select(
            'tanggal',
            DB::raw("'MASUK' as jenis"),
            'keterangan as nama',
            DB::raw("'Pekerjaan' as kategori"),
            'tb_dompet.nama_dompet as dompet',
            'jumlah'
        )
        ->join('tb_dompet', 'tb_pemasukan.dompet_id', '=', 'tb_dompet.id')
        ->where('tb_pemasukan.user_id', auth()->id())
        ->whereBetween('tanggal', [$awal, $akhir])

    ->unionAll(

        DB::table('tb_pengeluaran')
            ->select(
                'tanggal',
                DB::raw("'KELUAR' as jenis"),
                'keterangan as nama',
                'tb_kategori_pengeluaran.nama_kategori as kategori',
                'tb_dompet.nama_dompet as dompet',
                'jumlah'
            )
            ->join('tb_dompet', 'tb_pengeluaran.dompet_id', '=', 'tb_dompet.id')
            ->join('tb_kategori_pengeluaran', 'tb_pengeluaran.kategori_id', '=', 'tb_kategori_pengeluaran.id')
            ->where('tb_pengeluaran.user_id', auth()->id())
            ->whereBetween('tanggal', [$awal, $akhir])
    )

    ->unionAll(

        DB::table('tb_tabungan')
            ->select(
                'tanggal',
                DB::raw("'TABUNG' as jenis"),
                'keterangan as nama',
                'tb_kategori_tabungan.nama_kategori as kategori',
                'tb_dompet.nama_dompet as dompet',
                'nominal as jumlah'
            )
            ->join('tb_dompet', 'tb_tabungan.dompet_id', '=', 'tb_dompet.id')
            ->join('tb_kategori_tabungan', 'tb_tabungan.kategori_tabungan_id', '=', 'tb_kategori_tabungan.id')
            ->where('tb_tabungan.user_id', auth()->id())
            ->whereBetween('tanggal', [$awal, $akhir])
    )

    ->orderBy('tanggal', 'desc')
    ->limit(10)
    ->get();





    return view('dashboard.index', compact(
    'totalSaldo',
    'totalPemasukan',
    'totalPengeluaran',
    'namaPeriode',
    'tahun',
    'dataTabunganTahunan',
    'labelPengeluaran',
    'dataPengeluaran',
    'targetTabungan',
    'transaksiTerkini'
));


}


    // ===== GOOGLE LOGIN =====
    public function redirectToGoogle() // Redirect ke Google OAuth.
    {
        return Socialite::driver('google')->stateless()->redirect(); // Arahkan ke Google.
    }


    public function handleGoogleCallback() // Callback setelah login Google.
    {
        $googleUser = Socialite::driver('google')->stateless()->user(); // Ambil data user dari Google.

        $user = User::updateOrCreate( // Update atau buat user baru.
            ['email' => $googleUser->email], // Kunci pencarian berdasarkan email.
            [
                'name' => $googleUser->name, // Set nama dari Google.
                'username' => str_replace(' ', '', strtolower($googleUser->name)), // Set username dari nama.
                'google_id' => $googleUser->id, // Simpan Google ID.
                'email_verified_at' => now(), // Tandai email terverifikasi.
                'password' => Hash::make(uniqid()), // Password random untuk user Google.
            ]
        );

        Auth::login($user); // Login user.

        return redirect()->route('dashboard'); // Redirect ke dashboard.
    }
}
