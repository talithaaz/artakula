@extends('layouts.index')

@section('title', 'Tambah Kategori Tabungan | Artakula')
@section('page_title', 'Overview Tabungan')

@section('content')

<h5 class="fw-bold mb-4">Tambah Kategori Tabungan</h5>

<div class="card-artakula p-4 col-md-6">

    {{-- Form tambah kategori --}}
    <form action="{{ route('kategoriTabungan.store') }}" method="POST">
        @csrf

        {{-- Simpan filter bulan & tahun --}}
        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">

        {{-- Nama kategori --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Tabungan</label>
            <input
                type="text"
                name="nama_kategori"
                class="form-control"
                placeholder="Contoh: Rumah, Liburan"
                required
            >
        </div>

        {{-- Dompet tujuan --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Dompet Tujuan</label>
            <select name="dompet_tujuan_id" class="form-select" required>
                <option value="">Pilih Dompet Tujuan</option>

                @foreach ($dompet as $d)
                    <option value="{{ $d->id }}">
                        {{ $d->nama_dompet }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Target nominal --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Target Nominal (Rp)</label>
            <input
                type="number"
                name="target_nominal"
                class="form-control"
                placeholder="Jumlah Target"
                required
            >
        </div>

        {{-- Target waktu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Target Waktu</label>
            <input
                type="date"
                name="target_waktu"
                class="form-control"
                required
            >
        </div>

        {{-- Tombol aksi --}}
        <div class="d-flex gap-2">
            <a
                href="{{ route('kategoriTabungan.index', [
                    'bulan' => request('bulan'),
                    'tahun' => request('tahun')
                ]) }}"
                class="btn btn-secondary"
            >
                Batal
            </a>

            <button type="submit" class="btn btn-success">
                Simpan
            </button>
        </div>

    </form>

</div>

@endsection
