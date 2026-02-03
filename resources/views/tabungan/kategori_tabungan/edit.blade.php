@extends('layouts.index')

@section('title', 'Edit Kategori Tabungan | Artakula')
@section('page_title', 'Overview Tabungan')

@section('content')

<h5 class="fw-bold mb-4">Edit Kategori Tabungan</h5>

<div class="card-artakula p-4 col-md-6">

    {{-- Form edit kategori --}}
    <form action="{{ route('kategoriTabungan.update', $kategoriTabungan->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Simpan filter bulan & tahun --}}
        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">

        {{-- Nama kategori --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Tabungan</label>
            <input
                type="text"
                name="nama_kategori"
                value="{{ $kategoriTabungan->nama_kategori }}"
                class="form-control"
                required
            >
        </div>

        {{-- Dompet tujuan --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Dompet Tujuan</label>

            <select
                name="dompet_tujuan_id"
                class="form-select"
                @if ($jumlahTransaksi > 0) disabled @endif
            >
                @foreach ($dompet as $d)
                    <option
                        value="{{ $d->id }}"
                        @selected($kategoriTabungan->dompet_tujuan_id == $d->id)
                    >
                        {{ $d->nama_dompet }}
                    </option>
                @endforeach
            </select>

            {{-- Keterangan jika dompet terkunci --}}
            @if ($jumlahTransaksi > 0)
                <small class="text-muted">
                    Dompet tujuan tidak bisa diubah karena tabungan sudah memiliki transaksi.
                </small>
            @endif
        </div>

        {{-- Target nominal --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Target Nominal (Rp)</label>
            <input
                type="number"
                name="target_nominal"
                value="{{ $kategoriTabungan->target_nominal }}"
                class="form-control"
                required
            >
        </div>

        {{-- Target waktu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Target Waktu</label>
            <input
                type="date"
                name="target_waktu"
                value="{{ $kategoriTabungan->target_waktu }}"
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

            <button type="submit" class="btn btn-primary">
                Update
            </button>
        </div>

    </form>

</div>

@endsection
