@extends('layouts.index')

@section('title', 'Tabungan | Artakula')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Catatan Tabungan</h5>
    <a href="{{ route('tabungan.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Tambah Tabungan
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card-artakula p-4">
<table class="table table-borderless align-middle">
    <thead class="border-bottom">
        <tr>
            <th>Tanggal</th>
            <th>Tabungan</th>
            <th>Dompet</th>
            <th>Keterangan</th>
            <th class="text-end">Nominal</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>

@foreach($tabungan as $item)
<tr>
    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
<td>{{ $item->dompet->nama_dompet ?? '-' }}</td>

    <td>{{ $item->catatan ?? '-' }}</td>
    <td class="text-end fw-bold text-primary">
        Rp {{ number_format($item->nominal,0,',','.') }}
    </td>
    <td class="text-center">

        <a href="{{ route('tabungan.edit',$item->id) }}"
           class="btn btn-sm btn-outline-primary">
           Edit
        </a>

        <button type="button" class="btn btn-sm btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal{{ $item->id }}">
            Hapus
        </button>

        <!-- Modal Hapus -->
        <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus tabungan
                        <strong>{{ $item->kategori->nama_kategori }}</strong>
                        sebesar
                        <strong>Rp {{ number_format($item->nominal,0,',','.') }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('tabungan.destroy',$item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-danger">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </td>
</tr>
@endforeach

    </tbody>
</table>
</div>

@endsection
