@extends('layouts.index')

@section('title', 'Tambah Tabungan | Artakula')

@section('content')

<h5 class="fw-bold mb-4">Tambah Tabungan</h5>

<div class="card-artakula p-4 col-md-6">
<form action="{{ route('tabungan.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label class="form-label">Tanggal</label>
    <input type="date" name="tanggal" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Kategori Tabungan</label>
    <select name="kategori_tabungan_id" class="form-select" required>
        <option value="">-- Pilih Tabungan --</option>
        @foreach($kategoriTabungan as $item)
        <option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Dompet</label>
    <select name="dompet_id" class="form-select" required>
        <option value="">-- Pilih Dompet --</option>
        @foreach($dompet as $d)
        <option value="{{ $d->id }}">{{ $d->nama_dompet }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Nominal</label>
    <input type="number" name="nominal" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="catatan" class="form-control" rows="3"></textarea>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('tabungan.index') }}" class="btn btn-secondary">
        Kembali
    </a>
    <button type="submit" class="btn btn-success">
        Simpan
    </button>
</div>

</form>
</div>

@endsection
