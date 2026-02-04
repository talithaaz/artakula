@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title','Tambah Kategori Pengeluaran') {{-- Judul halaman pada title tag. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Tambah Kategori Pengeluaran</h5> {{-- Judul kecil halaman. --}}

<div class="card-artakula p-4 col-md-6"> {{-- Card wrapper form. --}}

    {{-- Form tambah kategori --}}
    <form method="POST" action="{{ route('kategori_pengeluaran.store') }}"> {{-- Form simpan kategori baru. --}}
        @csrf {{-- Token CSRF. --}}

        {{-- Simpan konteks bulan & tahun --}}
        <input type="hidden" name="bulan" value="{{ request('bulan') }}"> {{-- Simpan bulan filter. --}}
        <input type="hidden" name="tahun" value="{{ request('tahun') }}"> {{-- Simpan tahun filter. --}}

        {{-- Nama kategori --}}
        <div class="mb-3"> {{-- Grup input nama kategori. --}}
            <label class="form-label fw-bold">Nama Kategori</label> {{-- Label nama kategori. --}}
            <input type="text"
                   name="nama_kategori"
                   class="form-control"
                   placeholder="Contoh: Makanan, Transportasi"
                   required> {{-- Input nama kategori. --}}
        </div> {{-- Tutup grup input nama. --}}

        {{-- Budget --}}
        <div class="mb-3"> {{-- Grup input budget. --}}
            <label class="form-label fw-bold">Budget (Rp)</label> {{-- Label budget. --}}
            <input type="number"
                   name="budget"
                   class="form-control"
                   placeholder="Jumlah budget"
                   required> {{-- Input budget. --}}
        </div> {{-- Tutup grup input budget. --}}

        {{-- Periode awal --}}
        <div class="mb-3"> {{-- Grup input periode awal. --}}
            <label class="form-label">Periode Awal</label> {{-- Label periode awal. --}}
            <input type="date"
                   name="periode_awal"
                   class="form-control"> {{-- Input periode awal. --}}
        </div> {{-- Tutup grup input periode awal. --}}

        {{-- Periode akhir --}}
        <div class="mb-3"> {{-- Grup input periode akhir. --}}
            <label class="form-label">Periode Akhir</label> {{-- Label periode akhir. --}}
            <input type="date"
                   name="periode_akhir"
                   class="form-control"> {{-- Input periode akhir. --}}
        </div> {{-- Tutup grup input periode akhir. --}}

        {{-- Tombol aksi --}}
        <a href="{{ route('kategori_pengeluaran.index', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun')
            ]) }}"
           class="btn btn-secondary"> {{-- Tombol batal. --}}
            Batal
        </a>

        <button class="btn btn-success">
            Simpan
        </button> {{-- Tombol simpan. --}}

    </form> {{-- Tutup form. --}}
</div> {{-- Tutup card wrapper. --}}

@endsection {{-- Akhiri section konten. --}}
