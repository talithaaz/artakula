@extends('layouts.index')

@section('title','Tambah Kategori Pengeluaran')

@section('content')

<h5 class="fw-bold mb-4">Tambah Kategori Pengeluaran</h5>

<div class="card-artakula p-4 col-md-6">

    {{-- Form tambah kategori --}}
    <form method="POST" action="{{ route('kategori_pengeluaran.store') }}">
        @csrf

        {{-- Simpan konteks bulan & tahun --}}
        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">

        {{-- Nama kategori --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Kategori</label>
            <input type="text"
                   name="nama_kategori"
                   class="form-control"
                   placeholder="Contoh: Makanan, Transportasi"
                   required>
        </div>

        {{-- Budget --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Budget (Rp)</label>
            <input type="number"
                   name="budget"
                   class="form-control"
                   placeholder="Jumlah budget"
                   required>
        </div>

        {{-- Periode awal --}}
        <div class="mb-3">
            <label class="form-label">Periode Awal</label>
            <input type="date"
                   name="periode_awal"
                   class="form-control">
        </div>

        {{-- Periode akhir --}}
        <div class="mb-3">
            <label class="form-label">Periode Akhir</label>
            <input type="date"
                   name="periode_akhir"
                   class="form-control">
        </div>

        {{-- Tombol aksi --}}
        <a href="{{ route('kategori_pengeluaran.index', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun')
            ]) }}"
           class="btn btn-secondary">
            Batal
        </a>

        <button class="btn btn-success">
            Simpan
        </button>

    </form>
</div>

@endsection
