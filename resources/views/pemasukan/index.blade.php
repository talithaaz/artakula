@extends('layouts.index')

@section('title', 'Pemasukan | Artakula')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Pemasukan</h5>
    <a href="{{ route('pemasukan.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Tambah Pemasukan
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
            <th>Keterangan</th>
            <th>Dompet</th>
            <th class="text-end">Jumlah</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pemasukan as $item)
<tr>
    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
    <td>{{ $item->keterangan }}</td>
    <td>{{ $item->dompet->nama_dompet }}</td>
    <td class="text-end text-success fw-bold">
        Rp {{ number_format($item->jumlah,0,',','.') }}
    </td>
    <td class="text-center">
        <a href="{{ route('pemasukan.edit',$item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>

        <!-- Tombol hapus -->
        <button type="button" class="btn btn-sm btn-outline-danger" 
            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
            Hapus
        </button>

        <!-- Modal hapus -->
        <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Apakah kamu yakin ingin menghapus pemasukan <strong>{{ $item->keterangan }}</strong> senilai <strong>Rp {{ number_format($item->jumlah,0,',','.') }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('pemasukan.destroy', $item->id) }}" method="POST">
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
