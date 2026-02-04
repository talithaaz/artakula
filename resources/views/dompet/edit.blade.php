@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Edit Dompet | Artakula') {{-- Judul halaman pada title tag. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Edit Dompet</h5> {{-- Judul kecil halaman. --}}

<div class="card-artakula p-4 col-md-8"> {{-- Card wrapper form. --}}
<form action="{{ route('dompet.update', $dompet->id) }}" method="POST"> {{-- Form update dompet. --}}
@csrf {{-- Token CSRF untuk keamanan form. --}}
@method('PUT') {{-- Spoof method PUT untuk update. --}}

@if(session('error')) {{-- Tampilkan error jika ada. --}}
    <div class="alert alert-danger"> {{-- Kotak pesan error. --}}
        {{ session('error') }} {{-- Tampilkan isi error. --}}
    </div> {{-- Tutup kotak error. --}}
@endif {{-- Tutup kondisi error. --}}

<div class="mb-3"> {{-- Grup input nama dompet. --}}
    <label class="form-label">Nama Dompet</label> {{-- Label nama dompet. --}}
    <input type="text" name="nama_dompet" class="form-control" {{-- Input nama dompet. --}}
           value="{{ $dompet->nama_dompet }}" required> {{-- Isi nilai awal nama dompet. --}}
</div> {{-- Tutup grup input nama. --}}

<div class="mb-3"> {{-- Grup input saldo. --}}
    <label class="form-label">Saldo</label> {{-- Label saldo. --}}
    <input type="number" name="saldo" class="form-control" {{-- Input saldo. --}}
           value="{{ $dompet->saldo }}" required> {{-- Isi nilai awal saldo. --}}
</div> {{-- Tutup grup input saldo. --}}

<div class="d-flex gap-2"> {{-- Wrapper tombol aksi. --}}
    <a href="{{ route('dompet.index') }}" class="btn btn-secondary">Batal</a> {{-- Tombol batal. --}}
    <button class="btn btn-success">Update</button> {{-- Tombol update. --}}
</div> {{-- Tutup wrapper tombol aksi. --}}

</form> {{-- Tutup form. --}}
</div> {{-- Tutup card wrapper form. --}}

@endsection {{-- Akhiri section konten. --}}
