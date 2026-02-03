@extends('layouts.index')

@section('title', 'Edit Pengeluaran')
@section('page_title', 'Overview Pengeluaran')

@section('content')
    <h5 class="fw-bold mb-4">Edit Pengeluaran</h5>

    <div class="card-artakula p-4 col-md-8">
        <form method="POST" action="{{ route('pengeluaran.update', $pengeluaran->id) }}">
            @csrf
            @method('PUT')

            {{-- Kategori --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Kategori Pengeluaran</label>
                <select name="kategori_id" class="form-select" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->id }}"
                            {{ $pengeluaran->kategori_id == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dompet --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Dompet</label>
                <select name="dompet_id" class="form-select" required>
                    <option value="">Pilih Dompet</option>
                    @foreach ($dompets as $d)
                        <option value="{{ $d->id }}"
                            {{ $pengeluaran->dompet_id == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_dompet }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Keterangan --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Keterangan</label>
                <input type="text"
                       name="keterangan"
                       class="form-control"
                       value="{{ $pengeluaran->keterangan }}"
                       required>
            </div>

            {{-- Jumlah --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Jumlah (Rp)</label>
                <input type="number"
                       name="jumlah"
                       class="form-control"
                       value="{{ $pengeluaran->jumlah }}"
                       required>
            </div>

            {{-- Tanggal --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Tanggal</label>
                <input type="date"
                       name="tanggal"
                       class="form-control"
                       value="{{ $pengeluaran->tanggal }}"
                       required>
            </div>

            {{-- Aksi --}}
            <a href="{{ route('pengeluaran.index', [
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
