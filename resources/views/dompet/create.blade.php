@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Tambah Dompet | Artakula') {{-- Judul halaman pada title tag. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Tambah Dompet</h5> {{-- Judul kecil halaman. --}}

<div class="card-artakula p-4 col-md-8"> {{-- Card wrapper form. --}}
<form action="{{ route('dompet.store') }}" method="POST"> {{-- Form simpan dompet baru. --}}
@csrf {{-- Token CSRF untuk keamanan form. --}}

<div class="mb-3"> {{-- Grup input nama dompet. --}}
    <label class="form-label">Nama Dompet</label> {{-- Label nama dompet. --}}
    <input type="text" name="nama_dompet" class="form-control" required> {{-- Input nama dompet. --}}
</div> {{-- Tutup grup input nama. --}}

@if(session('error')) {{-- Tampilkan error jika ada. --}}
    <div class="alert alert-danger"> {{-- Kotak pesan error. --}}
        {{ session('error') }} {{-- Tampilkan isi error. --}}
    </div> {{-- Tutup kotak error. --}}
@endif {{-- Tutup kondisi error. --}}

<div class="mb-3"> {{-- Grup input jenis dompet. --}}
    <label class="form-label">Jenis Dompet</label> {{-- Label jenis dompet. --}}
    <select name="jenis" id="jenis" class="form-select" required> {{-- Dropdown jenis dompet. --}}
        <option value="">-- Pilih --</option> {{-- Placeholder jenis dompet. --}}
        <option value="cash">Cash</option> {{-- Opsi cash. --}}
        <option value="bank">Bank</option> {{-- Opsi bank. --}}
        <option value="ewallet">E-Wallet</option> {{-- Opsi e-wallet. --}}
    </select> {{-- Tutup dropdown jenis dompet. --}}
</div> {{-- Tutup grup input jenis. --}}

<div class="mb-3 d-none" id="providerBox"> {{-- Grup pilihan provider (hidden default). --}}
    <label class="form-label">Provider</label> {{-- Label provider. --}}
    <select name="bank_code" class="form-select"> {{-- Dropdown provider. --}}
        <option value="">-- Pilih Provider --</option> {{-- Placeholder provider. --}}
        <option value="bca">BCA</option> {{-- Opsi provider BCA. --}}
        <option value="mandiri">Mandiri</option> {{-- Opsi provider Mandiri. --}}
        <option value="bni">BNI</option> {{-- Opsi provider BNI. --}}
        <option value="bri">BRI</option> {{-- Opsi provider BRI. --}}
        <option value="gopay">GoPay</option> {{-- Opsi provider GoPay. --}}
        <option value="ovo">OVO</option> {{-- Opsi provider OVO. --}}
        <option value="dana">DANA</option> {{-- Opsi provider DANA. --}}
        <option value="shopeepay">ShopeePay</option> {{-- Opsi provider ShopeePay. --}}
    </select> {{-- Tutup dropdown provider. --}}
</div> {{-- Tutup grup provider. --}}

<div class="alert alert-danger d-none" id="warningSaldo"> {{-- Peringatan saldo (hidden default). --}}
    ⚠️ Jika Anda menambahkan dompet bank / e-wallet secara manual, pastikan saldo sesuai dengan saldo asli Anda. {{-- Pesan peringatan. --}}
</div> {{-- Tutup peringatan saldo. --}}

<div class="mb-3"> {{-- Grup input saldo awal. --}}
    <label class="form-label">Saldo Awal</label> {{-- Label saldo awal. --}}
    <input type="number" name="saldo" class="form-control" required> {{-- Input saldo awal. --}}
</div> {{-- Tutup grup input saldo. --}}

<div class="d-flex gap-2"> {{-- Wrapper tombol aksi. --}}
        <a href="{{ route('dompet.index') }}" class="btn btn-secondary">Batal</a> {{-- Tombol batal. --}}
    <button class="btn btn-success">Simpan</button> {{-- Tombol simpan. --}}
</div> {{-- Tutup wrapper tombol aksi. --}}

</form> {{-- Tutup form. --}}
</div> {{-- Tutup card wrapper form. --}}

<script>
document.getElementById('jenis').addEventListener('change', function () { // Saat jenis dompet berubah.
    const provider = document.getElementById('providerBox'); // Ambil elemen provider.
    const warning = document.getElementById('warningSaldo'); // Ambil elemen peringatan saldo.

    if (this.value === 'bank' || this.value === 'ewallet') { // Jika bank/ewallet.
        provider.classList.remove('d-none'); // Tampilkan provider.
        warning.classList.remove('d-none'); // Tampilkan peringatan.
    } else { // Jika selain bank/ewallet.
        provider.classList.add('d-none'); // Sembunyikan provider.
        warning.classList.add('d-none'); // Sembunyikan peringatan.
    }
});
</script>

@endsection {{-- Akhiri section konten. --}}
