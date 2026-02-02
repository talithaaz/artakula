@extends('layouts.index')

@section('title', 'Tabungan | Artakula')
@section('page_title', 'Overview Tabungan')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Catat Tabungan</h5>
    
</div>

<div class="d-flex justify-content-between align-items-center mb-4">

    <form method="GET" class="d-flex gap-2 mb-0">
        <select name="bulan" class="form-select form-select-sm w-auto">
            @for($m=1;$m<=12;$m++)
                <option value="{{ $m }}" @selected($bulan==$m)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endfor
        </select>

        <select name="tahun" class="form-select form-select-sm w-auto">
            @for($y=now()->year-5;$y<=now()->year+5;$y++)
                <option value="{{ $y }}" @selected($tahun==$y)>
                    {{ $y }}
                </option>
            @endfor
        </select>

        <button class="btn btn-sm btn-outline-primary">
            Terapkan
        </button>
    </form>

    <a href="{{ route('tabungan.create', [
    'bulan' => $bulan,
    'tahun' => $tahun
]) }}"
       class="btn btn-sm btn-success">
        <i class="bi bi-plus-circle"></i> Tambah Tabungan
    </a>

</div>


@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@endsection


{{-- ================= GLOBAL TABLE ================= --}}
@section('table')
<thead>
    <tr>
        <th class="text-center">Tanggal</th>
        <th class="text-center">Kategori</th>
        <th class="text-center">Keterangan</th>
        <th class="text-center">Sumber Dompet</th>
        <th class="text-center">Dompet Tujuan</th>
        <th class="text-center">Nominal</th>
        <th class="text-center">Aksi</th>
    </tr>
</thead>
<tbody>
@forelse($tabungan as $item)
<tr>
    <td>
        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
    </td>

    <td title="{{ $item->kategori->nama_kategori }}">
        {{ $item->kategori->nama_kategori }}
    </td>

    <td title="{{ $item->keterangan }}">
        {{ $item->keterangan ?? '-' }}
    </td>

    <td title="{{ $item->sumberDompet->nama_dompet }}">
        {{ $item->sumberDompet->nama_dompet }}
    </td>

    <td title="{{ $item->kategori->dompetTujuan->nama_dompet ?? '-' }}">
        {{ $item->kategori->dompetTujuan->nama_dompet ?? '-' }}
    </td>

    <td class="fw-bold text-primary ">
        Rp {{ number_format($item->nominal,0,',','.') }}
    </td>

    <td class="text-center">
        <a href="{{ route('tabungan.edit', [
    'tabungan' => $item->id,
    'bulan' => $bulan,
    'tahun' => $tahun
]) }}"
           class="btn btn-sm btn-outline-primary">
            Edit
        </a>

        <button type="button"
                class="btn btn-sm btn-outline-danger"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal{{ $item->id }}">
            Hapus
        </button>

        {{-- MODAL HAPUS --}}
        <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Yakin mau hapus tabungan
                        <strong>{{ $item->kategori->nama_kategori }}</strong>
                        sebesar
                        <strong class="text-nominal">
                            Rp {{ number_format($item->nominal,0,',','.') }}
                        </strong>?
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('tabungan.destroy', [
    'tabungan' => $item->id,
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
    <td colspan="7" class="text-center text-muted py-4">
        Belum ada data tabungan
    </td>
</tr>
@endforelse
</tbody>
@endsection
