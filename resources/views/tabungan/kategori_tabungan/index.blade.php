@extends('layouts.index')

@section('title', 'Kategori Tabungan | Artakula')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Kategori Tabungan</h5>
    <a href="{{ route('kategoriTabungan.create') }}" class="btn btn-success">
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
            <th>Nama Tabungan</th>
            <th>Dompet Tujuan</th> {{-- 🔥 KOLOM BARU --}}
            <th>Target Nominal</th>
            <th>Target Waktu</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>

@foreach($kategoriTabungan as $item)
<tr>
    <td class="fw-semibold">{{ $item->nama_kategori }}</td>

    {{-- DOMPET TUJUAN --}}
    <td>
        @if ($item->dompetTujuan)
            <span class="badge bg-primary">
                {{ $item->dompetTujuan->nama_dompet }}
            </span>
        @else
            <span class="badge bg-secondary">
                Saldo Terkunci
            </span>
        @endif
    </td>

    <td>
        Rp {{ number_format($item->target_nominal,0,',','.') }}
    </td>

    <td>
        {{ \Carbon\Carbon::parse($item->target_waktu)->format('d M Y') }}
    </td>

    <td class="text-center">

        <a href="{{ route('kategoriTabungan.edit',$item->id) }}"
           class="btn btn-sm btn-outline-primary">
           Edit
        </a>

        <button type="button" class="btn btn-sm btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal{{ $item->id }}">
            Hapus
        </button>

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
                        <form action="{{ route('kategoriTabungan.destroy',$item->id) }}" method="POST">
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
