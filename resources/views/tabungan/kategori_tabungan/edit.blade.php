@extends('layouts.index')

@section('title', 'Edit Kategori Tabungan | Artakula')

@section('content')

<h5 class="fw-bold mb-4">Edit Kategori Tabungan</h5>

<div class="card-artakula p-4 col-md-6">
<form action="{{ route('kategoriTabungan.update',$kategoriTabungan->id) }}" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
    <label class="form-label">Nama Tabungan</label>
    <input type="text" name="nama_kategori"
           value="{{ $kategoriTabungan->nama_kategori }}"
           class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Dompet Tujuan</label>

<select name="dompet_tujuan_id"
    class="form-select"
    @if($jumlahTransaksi > 0) disabled @endif>

    <option value="">-- Saldo Terkunci --</option>

    @foreach($dompet as $d)
        <option value="{{ $d->id }}"
            @selected($kategoriTabungan->dompet_tujuan_id == $d->id)>
            {{ $d->nama_dompet }}
        </option>
    @endforeach
</select>

@if($jumlahTransaksi > 0)
<small class="text-muted">
    Dompet tujuan tidak bisa diubah karena tabungan sudah memiliki transaksi.
</small>
@endif

</div>

<div class="mb-3">
    <label class="form-label">Target Nominal</label>
    <input type="number" name="target_nominal"
           value="{{ $kategoriTabungan->target_nominal }}"
           class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Target Waktu</label>
    <input type="date" name="target_waktu"
           value="{{ $kategoriTabungan->target_waktu }}"
           class="form-control" required>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('kategoriTabungan.index') }}" class="btn btn-secondary">
        Kembali
    </a>
    <button type="submit" class="btn btn-primary">
        Update
    </button>
</div>

</form>
</div>

@endsection
