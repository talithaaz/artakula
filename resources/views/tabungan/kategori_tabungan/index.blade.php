@extends('layouts.index')

@section('title', 'Kategori Tabungan | Artakula')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Kategori Tabungan</h5>
    <a href="{{ route('kategori-tabungan.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Tambah Kategori
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card-artakula p-4">
<table class="table table-borderless align-middle">
    <thead class="border-bottom">
        <tr>
            <th>Nama Kategori</th>
            <th>Keterangan</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kategori as $item)
        <tr>
            <td class="fw-semibold">{{ $item->nama_kategori }}</td>
            <td>{{ $item->keterangan ?? '-' }}</td>
            <td class="text-center">
                <a href="{{ route('kategori-tabungan.edit',$item->id) }}" 
                   class="btn btn-sm btn-outline-primary">Edit</a>

                <button type="button" class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
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
                                Yakin ingin menghapus kategori 
                                <strong>{{ $item->nama_kategori }}</strong>?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('kategori-tabungan.destroy',$item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Hapus</button>
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
