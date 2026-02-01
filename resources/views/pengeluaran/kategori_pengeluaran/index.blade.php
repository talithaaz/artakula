@extends('layouts.index')
@section('title','Kategori Pengeluaran | Artakula')
@section('page_title', 'Overview Pengeluaran')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Kategori Pengeluaran</h5>
    

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

    <a href="{{ route('kategori_pengeluaran.create', [
        'bulan' => $bulan,
        'tahun' => $tahun
    ]) }}"
    class="btn btn-sm btn-success text-nowrap">
        <i class="bi bi-plus-circle"></i> Tambah Kategori
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
            <th>Nama Kategori</th>
            <th class="text-center">Budget</th>
            <th class="text-center">Terpakai</th>
            <th class="text-center">Sisa</th>
            <th class="text-center">Periode</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategori as $item)
        <tr>
            <td>{{ $item->nama_kategori }}</td>
            <td >Rp {{ number_format($item->budget,0,',','.') }}</td>
            <td >Rp {{ number_format($item->terpakai,0,',','.') }}</td>
<td>Rp {{ number_format($item->sisa,0,',','.') }}</td>
            <td >
                {{ $item->periode_awal ? \Carbon\Carbon::parse($item->periode_awal)->format('d M Y') : '-' }}
                -
                {{ $item->periode_akhir ? \Carbon\Carbon::parse($item->periode_akhir)->format('d M Y') : '-' }}
            </td>
            <td class="text-center">
    <a href="{{ route('kategori_pengeluaran.edit', [
    'kategori_pengeluaran' => $item->id,
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
                    Yakin mau hapus kategori
                    <strong>{{ $item->nama_kategori }}</strong>?
                    <br>
                    Semua pengeluaran yang menggunakan kategori ini akan ikut terhapus.
                </div>
                <div class="modal-footer">
                    <form action="{{ route('kategori_pengeluaran.destroy', [
    'kategori_pengeluaran' => $item->id,
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
            <td colspan="6" class="text-center text-muted">Belum ada kategori</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection
