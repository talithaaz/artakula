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

@endsection

{{-- ================= GLOBAL TABLE ================= --}}
@section('table')
    <thead class="border-bottom">
        <tr>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center">Kategori</th>
            <th class="text-center">Dompet</th>
            <th class="text-center">Jumlah</th>
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
            <td class="text-danger fw-bold">
                Rp {{ number_format($item->jumlah,0,',','.') }}
            </td>
            <td class="text-center">
    <a href="{{ route('pengeluaran.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
        Edit
    </a>

    <!-- Tombol Hapus dengan Modal -->
    <button type="button"
            class="btn btn-sm btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal{{ $item->id }}">
        Hapus
    </button>

    <!-- MODAL HAPUS -->
    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Yakin mau hapus pengeluaran
                    <strong>{{ $item->keterangan }}</strong>
                    sebesar
                    <strong class="text-danger">
                        Rp {{ number_format($item->jumlah,0,',','.') }}
                    </strong>?
                </div>
                <div class="modal-footer">
                    <form action="{{ route('pengeluaran.destroy', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-danger">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
