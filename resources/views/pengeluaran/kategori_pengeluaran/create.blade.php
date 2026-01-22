@extends('layouts.index')

@section('title','Tambah Kategori Pengeluaran')

@section('content')

<h5 class="fw-bold mb-4">Tambah Kategori Pengeluaran</h5>

<div class="card-artakula p-4 col-md-6">
    <form method="POST" action="{{ route('kategori_pengeluaran.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-bold">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Makanan, Transportasi" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Budget (Rp)</label>
            <input type="number" name="budget" class="form-control" placeholder="Jumlah budget" required>
        </div>

        <div class="mb-3">
    <label for="periode_awal" class="form-label">Periode Awal</label>
    <input type="date" class="form-control" name="periode_awal" id="periode_awal" value="{{ old('periode_awal', $kategori->periode_awal ?? '') }}">
</div>

<div class="mb-3">
    <label for="periode_akhir" class="form-label">Periode Akhir</label>
    <input type="date" class="form-control" name="periode_akhir" id="periode_akhir" value="{{ old('periode_akhir', $kategori->periode_akhir ?? '') }}">
</div>


        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('kategori_pengeluaran.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection
