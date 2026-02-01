@extends('layouts.index')
@section('title','Pengeluaran | Artakula')
@section('page_title', 'Overview Pengeluaran')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Catat Pengeluaran</h5>
    
</div>

<div class="d-flex justify-content-between align-items-center flex-nowrap gap-2 mb-4">

    <form method="GET" class="d-flex align-items-center gap-2 flex-nowrap mb-0">
        <select name="bulan" class="form-select form-select-sm w-auto">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endfor
        </select>

        <select name="tahun" class="form-select form-select-sm w-auto">
            @for($y = now()->year - 5; $y <= now()->year + 5; $y++)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>

        <button class="btn btn-sm btn-outline-primary text-nowrap">
            Terapkan
        </button>
    </form>

    <a href="{{ route('pengeluaran.create', [
        'bulan' => $bulan,
        'tahun' => $tahun
    ]) }}"
    class="btn btn-sm btn-success text-nowrap">
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
    <a href="{{ route('pengeluaran.edit', [
    'pengeluaran' => $item->id,
    'bulan' => $bulan,
    'tahun' => $tahun
]) }}" class="btn btn-sm btn-outline-primary">
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
                    <form action="{{ route('pengeluaran.destroy', [
    'pengeluaran' => $item->id,
    'bulan' => $bulan,
    'tahun' => $tahun
]) }}" method="POST">

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
