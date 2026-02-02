@extends('layouts.index')

@section('title', 'Kategori Tabungan | Artakula')
@section('page_title', 'Overview Tabungan')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <h5 class="fw-bold">Kategori Tabungan</h5>
    
</div>

<div class="d-flex justify-content-between align-items-center flex-nowrap gap-2 mb-4">

    {{-- FILTER BULAN & TAHUN --}}
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

    {{-- TOMBOL TAMBAH KATEGORI TABUNGAN --}}
    <a href="{{ route('kategoriTabungan.create', [
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

@section('table')
<!-- <div class="card-artakula p-4"> -->
<!-- <table class="table table-borderless align-middle"> -->
    <thead>
        <tr>
            <th class="text-center">Nama Tabungan</th>
            <th class="text-center">Dompet Tujuan</th> {{-- 🔥 KOLOM BARU --}}
            <th class="text-center">Target Nominal</th>
            <th class="text-center">Sudah Ditabung</th>
            <th class="text-center">Sisa Target</th>
            <th class="text-center">Target Waktu</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>

@foreach($kategoriTabungan as $item)
@php
    $sudahDitabung = $item->total_ditabung ?? 0;
    $sisaTarget = $item->target_nominal - $sudahDitabung;
@endphp
<tr>
    <td >{{ $item->nama_kategori }}</td>

    {{-- DOMPET TUJUAN --}}
    <td>
        <!-- @if ($item->dompetTujuan) -->
            <!-- <span class="badge bg-primary"> -->
                {{ $item->dompetTujuan->nama_dompet }}
            <!-- </span> -->
        <!-- @else
            <span class="badge bg-secondary">
                Saldo Terkunci
            </span>
        @endif -->
    </td>

    <td>
        Rp {{ number_format($item->target_nominal,0,',','.') }}
    </td>

    <td>
        Rp {{ number_format($sudahDitabung,0,',','.') }}
    </td>

    <td>
        @if($sisaTarget > 0)
            Rp {{ number_format($sisaTarget,0,',','.') }}
        @else
            <span class="badge bg-success">Target Tercapai</span>
        @endif
    </td>

    <td>
        {{ \Carbon\Carbon::parse($item->target_waktu)->format('d M Y') }}
    </td>

    <td class="text-center">

        <a href="{{ route('kategoriTabungan.edit', [
    'kategoriTabungan' => $item->id,
    'bulan' => $bulan,
    'tahun' => $tahun
]) }}"
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
                        Yakin ingin menghapus kategori tabungan
                        <strong>{{ $item->nama_kategori }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('kategoriTabungan.destroy', [
    'kategoriTabungan' => $item->id,
    'bulan' => $bulan,
    'tahun' => $tahun
]) }}" method="POST">
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
