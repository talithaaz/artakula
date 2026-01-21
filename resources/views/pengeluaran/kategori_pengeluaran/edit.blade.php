@extends('layouts.index')

@section('title','Edit Kategori Pengeluaran')

@section('content')

<h5 class="fw-bold mb-4">Edit Kategori Pengeluaran</h5>

<div class="card-artakula p-4 col-md-6">
    <form method="POST" action="{{ route('kategori_pengeluaran.update', $kategori->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-bold">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" value="{{ $kategori->nama_kategori }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Budget (Rp)</label>
            <input type="number" name="budget" class="form-control" value="{{ $kategori->budget }}" required>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('kategori_pengeluaran.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection
