@extends('layouts.index')

@section('title','Tambah Tabungan')

@section('content')

<h5 class="fw-bold mb-4">Tambah Tabungan</h5>

<div class="card-artakula p-4 col-md-8">

{{-- FORM GET → buat pilih kategori --}}
<form method="GET" action="{{ route('tabungan.create') }}">
    <select name="kategori_tabungan_id"
            class="form-select mb-3"
            onchange="this.form.submit()"
            required>
        <option value="">Pilih Kategori Tabungan</option>
        @foreach($kategoriTabungan as $k)
            <option value="{{ $k->id }}"
                {{ request('kategori_tabungan_id') == $k->id ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
            </option>
        @endforeach
    </select>
</form>

{{-- FORM POST → simpan data --}}
<form method="POST" action="{{ route('tabungan.store') }}">
@csrf

<input type="hidden"
       name="kategori_tabungan_id"
       value="{{ request('kategori_tabungan_id') }}">

<select name="dompet_id" class="form-select mb-3" required>
    <option value="">Pilih Sumber Dompet</option>
    @foreach($dompet as $d)
        <option value="{{ $d->id }}">{{ $d->nama_dompet }}</option>
    @endforeach
</select>

{{-- DOMPET TUJUAN (AUTO DARI KATEGORI) --}}
<div class="mb-3">
    <label class="form-label">Dompet Tujuan</label>
    <input type="text"
           class="form-control"
           value="{{ $dompetTujuan?->nama_dompet ?? '-' }}"
           readonly>
</div>

<input type="hidden"
       name="dompet_tujuan_id"
       value="{{ $dompetTujuan?->id }}">

<input type="number"
       name="nominal"
       class="form-control mb-3"
       placeholder="Nominal"
       required>

<input type="date"
       name="tanggal"
       class="form-control mb-3"
       required>

<input type="text"
       name="keterangan"
       class="form-control mb-3"
       placeholder="Keterangan (opsional)">

<a href="{{ route('tabungan.index') }}" class="btn btn-secondary">Batal</a>
<button class="btn btn-success">Simpan</button>

</form>
</div>

@endsection
