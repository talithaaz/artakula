@extends('layouts.index')

@section('title', 'Tambah Dompet | Artakula')

@section('content')

<h5 class="fw-bold mb-4">Tambah Dompet</h5>

<div class="card-artakula p-4 col-md-8">
<form action="{{ route('dompet.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label class="form-label">Nama Dompet</label>
    <input type="text" name="nama_dompet" class="form-control" required>
</div>

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="mb-3">
    <label class="form-label">Jenis Dompet</label>
    <select name="jenis" id="jenis" class="form-select" required>
        <option value="">-- Pilih --</option>
        <option value="cash">Cash</option>
        <option value="bank">Bank</option>
        <option value="ewallet">E-Wallet</option>
    </select>
</div>

<div class="mb-3 d-none" id="providerBox">
    <label class="form-label">Provider</label>
    <select name="bank_code" class="form-select">
        <option value="">-- Pilih Provider --</option>
        <option value="bca">BCA</option>
        <option value="mandiri">Mandiri</option>
        <option value="bni">BNI</option>
        <option value="bri">BRI</option>
        <option value="gopay">GoPay</option>
        <option value="ovo">OVO</option>
        <option value="dana">DANA</option>
        <option value="shopeepay">ShopeePay</option>
    </select>
</div>

<div class="alert alert-danger d-none" id="warningSaldo">
    ⚠️ Jika Anda menambahkan dompet bank / e-wallet secara manual, pastikan saldo sesuai dengan saldo asli Anda.
</div>

<div class="mb-3">
    <label class="form-label">Saldo Awal</label>
    <input type="number" name="saldo" class="form-control" required>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-success">Simpan</button>
    <a href="{{ route('dompet.index') }}" class="btn btn-secondary">Batal</a>
</div>

</form>
</div>

<script>
document.getElementById('jenis').addEventListener('change', function () {
    const provider = document.getElementById('providerBox');
    const warning = document.getElementById('warningSaldo');

    if (this.value === 'bank' || this.value === 'ewallet') {
        provider.classList.remove('d-none');
        warning.classList.remove('d-none');
    } else {
        provider.classList.add('d-none');
        warning.classList.add('d-none');
    }
});
</script>

@endsection
