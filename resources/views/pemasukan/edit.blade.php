@extends('layouts.index')

@section('title','Edit Pemasukan')

@section('content')

<h5 class="fw-bold mb-4">Edit Pemasukan</h5>

<div class="card-artakula p-4 col-md-8">
<form method="POST" action="{{ route('pemasukan.update', $pemasukan->id) }}">
@csrf
@method('PUT')

<select name="dompet_id" class="form-select mb-3" required>
    <option value="">Pilih Dompet</option>
    @foreach($dompets as $d)
        <option value="{{ $d->id }}" 
            {{ $pemasukan->dompet_id == $d->id ? 'selected' : '' }}>
            {{ $d->nama_dompet }}
        </option>
    @endforeach
</select>

<input type="text" name="keterangan" class="form-control mb-3" 
    placeholder="Keterangan" value="{{ $pemasukan->keterangan }}" required>

<input type="number" name="jumlah" class="form-control mb-3" 
    placeholder="Jumlah" value="{{ $pemasukan->jumlah }}" required>

<input type="date" name="tanggal" class="form-control mb-3" 
    value="{{ $pemasukan->tanggal }}" required>

<button class="btn btn-success">Simpan</button>
<a href="{{ route('pemasukan.index') }}" class="btn btn-secondary">Batal</a>

</form>
</div>
@endsection
