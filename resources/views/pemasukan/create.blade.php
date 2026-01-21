@extends('layouts.index')

@section('title','Tambah Pemasukan')

@section('content')

<h5 class="fw-bold mb-4">Tambah Pemasukan</h5>

<div class="card-artakula p-4 col-md-8">
<form method="POST" action="{{ route('pemasukan.store') }}">
@csrf

<select name="dompet_id" class="form-select mb-3" required>
    <option value="">Pilih Dompet</option>
    @foreach($dompets as $d)
        <option value="{{ $d->id }}">{{ $d->nama_dompet }}</option>
    @endforeach
</select>

<input type="text" name="keterangan" class="form-control mb-3" placeholder="Keterangan" required>
<input type="number" name="jumlah" class="form-control mb-3" placeholder="Jumlah" required>
<input type="date" name="tanggal" class="form-control mb-3" required>

<button class="btn btn-success">Simpan</button>
<a href="{{ route('pemasukan.index') }}" class="btn btn-secondary">Batal</a>

</form>
</div>
@endsection
