@extends('layouts.index')

@section('title', 'Tambah Kategori Tabungan | Artakula')
@section('page_title', 'Overview Tabungan')

@section('content')

<h5 class="fw-bold mb-4">Tambah Kategori Tabungan</h5>

<div class="card-artakula p-4 col-md-6">
<form action="{{ route('kategoriTabungan.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label class="form-label fw-bold">Nama Tabungan</label>
    <input type="text"
           name="nama_kategori"
           class="form-control"
           required>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Dompet Tujuan</label>
    <select name="dompet_tujuan_id" class="form-select">
        <!-- <option value="">
            -- Saldo Terkunci (Tanpa Dompet Tujuan) --
        </option> -->

        @foreach($dompet as $d)
            <option value="{{ $d->id }}">
                {{ $d->nama_dompet }}
            </option>
        @endforeach
    </select>

    <!-- <small class="text-muted">
        Jika tidak dipilih, tabungan akan dianggap sebagai saldo terkunci.
    </small> -->
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Target Nominal (Rp)</label>
    <input type="number"
           name="target_nominal"
           class="form-control"
           required>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Target Waktu</label>
    <input type="date"
           name="target_waktu"
           class="form-control"
           required>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('kategoriTabungan.index') }}" class="btn btn-secondary">
        Batal
    </a>
    <button type="submit" class="btn btn-success">
        Simpan
    </button>
</div>

</form>
</div>

@endsection
