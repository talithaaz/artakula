@extends('layouts.index')

@section('title','Edit Kategori Tabungan')

@section('content')

<h5 class="fw-bold mb-4">Edit Kategori Tabungan</h5>

<div class="card-artakula p-4 col-md-8">
<form method="POST" action="{{ route('kategori-tabungan.update', $kategoriTabungan->id) }}">
@csrf
@method('PUT')

<input type="text" name="nama_kategori" 
    class="form-control mb-3" 
    value="{{ $kategoriTabungan->nama_kategori }}" required>

<input type="number" name="target_nominal" 
    class="form-control mb-3" 
    value="{{ $kategoriTabungan->target_nominal }}">

<button class="btn btn-success">Simpan</button>
<a href="{{ route('kategori-tabungan.index') }}" class="btn btn-secondary">Batal</a>

</form>
</div>
@endsection
