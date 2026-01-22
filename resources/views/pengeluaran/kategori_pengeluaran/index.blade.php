@extends('layouts.index')
@section('title','Kategori Pengeluaran | Artakula')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Kategori Pengeluaran</h5>
    <a href="{{ route('kategori_pengeluaran.create') }}" class="btn btn-success">
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
            <th class="text-end">Budget</th>
            <th class="text-end">Terpakai</th>
            <th class="text-end">Sisa</th>
            <th class="text-center">Periode</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategori as $item)
        <tr>
            <td>{{ $item->nama_kategori }}</td>
            <td class="text-end">Rp {{ number_format($item->budget,0,',','.') }}</td>
            <td class="text-end">Rp {{ number_format($item->terpakai,0,',','.') }}</td>
            <td class="text-end">Rp {{ number_format($item->budget - $item->terpakai,0,',','.') }}</td>
            <td class="text-center">
                {{ $item->periode_awal ? \Carbon\Carbon::parse($item->periode_awal)->format('d M Y') : '-' }}
                -
                {{ $item->periode_akhir ? \Carbon\Carbon::parse($item->periode_akhir)->format('d M Y') : '-' }}
            </td>
            <td class="text-center">
                <a href="{{ route('kategori_pengeluaran.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="{{ route('kategori_pengeluaran.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted">Belum ada kategori</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection
