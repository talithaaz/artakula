@extends('layouts.index')

@section('title','Catat Pengeluaran')

@section('content')

<h5 class="fw-bold mb-4">Catat Pengeluaran</h5>

<div class="card-artakula p-4 col-md-8">
    <form method="POST" action="{{ route('pengeluaran.store') }}">
        @csrf

        {{-- Pilih Kategori --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Kategori Pengeluaran</label>
            <select name="kategori_id" class="form-select" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->id }}">
                        {{ $k->nama_kategori }} (Rp {{ number_format($k->budget,0,',','.') }})
                        [{{ \Carbon\Carbon::parse($k->periode_awal)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($k->periode_akhir)->format('d M Y') }}]
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Dompet --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Dompet</label>
            <select name="dompet_id" class="form-select" required>
                <option value="">Pilih Dompet</option>
                @foreach($dompets as $d)
                    <option value="{{ $d->id }}">{{ $d->nama_dompet }} (Saldo: Rp {{ number_format($d->saldo,0,',','.') }})</option>
                @endforeach
            </select>
        </div>

        {{-- Keterangan --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Makan siang, Transportasi" required>
        </div>

        {{-- Jumlah --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" placeholder="Jumlah pengeluaran" required>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection
