@extends('layouts.index')

@section('title', 'Catat Pengeluaran')
@section('page_title', 'Overview Pengeluaran')

@section('content')
    <h5 class="fw-bold mb-4">Catat Pengeluaran</h5>

    <div class="card-artakula p-4 col-md-8">
        <form method="POST" action="{{ route('pengeluaran.store') }}">
            @csrf

            {{-- Simpan konteks filter --}}
            <input type="hidden" name="bulan" value="{{ request('bulan') }}">
            <input type="hidden" name="tahun" value="{{ request('tahun') }}">

            {{-- Kategori --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Kategori Pengeluaran</label>
                <select name="kategori_id" class="form-select" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->id }}">
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
                        <option value="{{ $d->id }}">
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
                       placeholder="Contoh: Makan siang, Transportasi"
                       required>
            </div>

            {{-- Jumlah --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Jumlah (Rp)</label>
                <input type="number"
                       name="jumlah"
                       class="form-control"
                       required>
            </div>

            {{-- Tanggal --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Tanggal</label>
                <input type="date"
                       name="tanggal"
                       class="form-control"
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
                Simpan
            </button>
        </form>
    </div>
@endsection
