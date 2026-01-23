@extends('layouts.index')

@section('title','Tambah Tabungan')

@section('content')

<h5 class="fw-bold mb-4">Tambah Tabungan</h5>

<div class="card-artakula p-4 col-md-8">
<form method="POST" action="{{ route('tabungan.store') }}">
@csrf

<select name="kategori_tabungan_id" class="form-select mb-3" required>
    <option value="">Pilih Kategori Tabungan</option>

    @foreach($kategoriTabungan as $k)
        <option value="{{ $k->id }}">
            {{ $k->nama_kategori }}
        </option>
    @endforeach
</select>

<input type="number" name="jumlah" 
    class="form-control mb-3" 
    placeholder="Jumlah Tabungan" required>

<input type="date" name="tanggal" 
    class="form-control mb-3" required>

<input type="text" name="keterangan" 
    class="form-control mb-3" 
    placeholder="Keterangan (Opsional)">

<button class="btn btn-success">Simpan</button>
<a href="{{ route('tabungan.index') }}" class="btn btn-secondary">Batal</a>

</form>
</div>
@endsection
