@extends('layouts.index')

@section('title','Edit Pengeluaran')

@section('content')

<h5 class="fw-bold mb-4">Edit Pengeluaran</h5>

<div class="card-artakula p-4 col-md-8">
    <form method="POST" action="{{ route('pengeluaran.update', $pengeluaran->id) }}">
        @csrf
        @method('PUT')

        {{-- Pilih Kategori --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Kategori Pengeluaran</label>
            <select name="kategori_id" class="form-select" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->id }}" {{ $pengeluaran->kategori_id == $k->id ? 'selected' : '' }}>
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
                    <option value="{{ $d->id }}" {{ $pengeluaran->dompet_id == $d->id ? 'selected' : '' }}>
                        {{ $d->nama_dompet }} (Saldo: Rp {{ number_format($d->saldo,0,',','.') }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Keterangan --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" value="{{ $pengeluaran->keterangan }}" required>
        </div>

        {{-- Jumlah --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $pengeluaran->jumlah }}" required>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $pengeluaran->tanggal }}" required>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection
