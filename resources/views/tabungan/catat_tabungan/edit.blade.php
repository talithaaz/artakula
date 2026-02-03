@extends('layouts.index')

@section('title', 'Edit Tabungan | Artakula')

@section('content')

<h5 class="fw-bold mb-4">Edit Tabungan</h5>

<div class="card-artakula p-4 col-md-6">

    <form method="POST" action="{{ route('tabungan.update', $tabungan->id) }}">
        @csrf
        @method('PUT')

        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">

        {{-- Kategori --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Kategori Tabungan</label>
            <select name="kategori_tabungan_id" class="form-select" required>
                @foreach ($kategori as $item)
                    <option value="{{ $item->id }}"
                        @selected($item->id == $tabungan->kategori_tabungan_id)>
                        {{ $item->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sumber dompet --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Sumber Dompet</label>
            <select name="sumber_dompet_id" class="form-select" required>
                @foreach ($dompet as $d)
                    <option value="{{ $d->id }}"
                        @selected($d->id == $tabungan->sumber_dompet_id)>
                        {{ $d->nama_dompet }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Dompet tujuan --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Dompet Tujuan</label>
            <input type="text"
                   class="form-control"
                   value="{{ optional($tabungan->kategori->dompetTujuan)->nama_dompet ?? '-' }}"
                   readonly>
        </div>

        {{-- Nominal --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nominal (Rp)</label>
            <input type="number"
                   name="nominal"
                   value="{{ $tabungan->nominal }}"
                   class="form-control"
                   required>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tanggal</label>
            <input type="date"
                   name="tanggal"
                   value="{{ $tabungan->tanggal }}"
                   class="form-control"
                   required>
        </div>

        {{-- Keterangan --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Keterangan</label>
            <textarea name="keterangan"
                      class="form-control"
                      rows="3">{{ old('keterangan', $tabungan->keterangan) }}</textarea>
        </div>

        {{-- Aksi --}}
        <div class="d-flex gap-2">
            <a href="{{ route('tabungan.index', [
                    'bulan' => request('bulan'),
                    'tahun' => request('tahun')
                ]) }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                Update
            </button>
        </div>

    </form>
</div>

@endsection
