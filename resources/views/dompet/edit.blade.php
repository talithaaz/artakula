@extends('layouts.index')

@section('title', 'Edit Dompet | Artakula')

@section('content')

<h5 class="fw-bold mb-4">Edit Dompet</h5>

<div class="card-artakula p-4 col-md-8">
<form action="{{ route('dompet.update', $dompet->id) }}" method="POST">
@csrf
@method('PUT')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="mb-3">
    <label class="form-label">Nama Dompet</label>
    <input type="text" name="nama_dompet" class="form-control"
           value="{{ $dompet->nama_dompet }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Saldo</label>
    <input type="number" name="saldo" class="form-control"
           value="{{ $dompet->saldo }}" required>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-success">Update</button>
    <a href="{{ route('dompet.index') }}" class="btn btn-secondary">Batal</a>
</div>

</form>
</div>

@endsection
