@extends('layouts.index')

@section('title','Tambah Kategori Tabungan')

@section('content')

<h5 class="fw-bold mb-4">Tambah Kategori Tabungan</h5>

<div class="card-artakula p-4 col-md-8">
<form method="POST" action="{{ route('kategori-tabungan.store') }}">
@csrf

<input type="text" name="nama_kategori" 
    class="form-control mb-3" 
    placeholder="Nama Kategori Tabungan" required>

<input type="number" name="target_nominal" 
    class="form-control mb-3" 
    placeholder="Target Nominal (Opsional)">

<button class="btn btn-success">Simpan</button>
<a href="{{ route('kategori-tabungan.index') }}" class="btn btn-secondary">Batal</a>

</form>
</div>
@endsection
