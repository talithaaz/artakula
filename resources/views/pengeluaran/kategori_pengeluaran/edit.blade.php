@extends('layouts.index')

@section('title','Edit Kategori Pengeluaran')

@section('content')

<h5 class="fw-bold mb-4">Edit Kategori Pengeluaran</h5>

<div class="card-artakula p-4 col-md-6">

    {{-- Form edit kategori --}}
    <form method="POST"
          action="{{ route('kategori_pengeluaran.update', $kategori->id) }}">
        @csrf
        @method('PUT')

        {{-- Simpan konteks bulan & tahun --}}
        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">

        {{-- Nama kategori --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Kategori</label>
            <input type="text"
                   name="nama_kategori"
                   class="form-control"
                   value="{{ $kategori->nama_kategori }}"
                   required>
        </div>

        {{-- Budget --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Budget (Rp)</label>
            <input type="number"
                   name="budget"
                   class="form-control"
                   value="{{ $kategori->budget }}"
                   required>
        </div>

        {{-- Periode awal --}}
        <div class="mb-3">
            <label class="form-label">Periode Awal</label>
            <input type="date"
                   name="periode_awal"
                   class="form-control"
                   value="{{ old('periode_awal', $kategori->periode_awal) }}">
        </div>

        {{-- Periode akhir --}}
        <div class="mb-3">
            <label class="form-label">Periode Akhir</label>
            <input type="date"
                   name="periode_akhir"
                   class="form-control"
                   value="{{ old('periode_akhir', $kategori->periode_akhir) }}">
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
            Update
        </button>

    </form>
</div>

@endsection
