@extends('layouts.index')
@section('title','Pengeluaran | Artakula')
@section('page_title', 'Overview Pengeluaran')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Catat Pengeluaran</h5>
    <a href="{{ route('pengeluaran.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Catat Pengeluaran
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
            <th>Kategori</th>
            <th>Dompet</th>
            <th class="text-end">Jumlah</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pengeluaran as $item)
        <tr>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
            <td>{{ $item->keterangan }}</td>
            <td>{{ $item->kategori->nama_kategori }}</td>
            <td>{{ $item->dompet->nama_dompet }}</td>
            <td class="text-end text-danger fw-bold">
                Rp {{ number_format($item->jumlah,0,',','.') }}
            </td>
            <td class="text-center">
                <a href="{{ route('pengeluaran.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="{{ route('pengeluaran.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Hapus pengeluaran ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted">Belum ada pengeluaran</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection
