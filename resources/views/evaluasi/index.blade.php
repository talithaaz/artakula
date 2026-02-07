@extends('layouts.index')

@section('title', 'Evaluasi Keuangan | Artakula')
@section('page_title', 'Evaluasi Keuangan')

{{-- ======================= CONTENT (ATAS HALAMAN) ======================= --}}
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Evaluasi Keuangan</h5>


{{-- FILTER BULAN & TAHUN --}}
<form method="GET" action="{{ route('evaluasi.index') }}" class="d-flex align-items-center gap-2 mb-0">

    {{-- Bulan --}}
    <select name="bulan" class="form-select form-select-sm w-auto">
        @for ($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
            </option>
        @endfor
    </select>

    {{-- Tahun --}}
    <select name="tahun" class="form-select form-select-sm w-auto">
        @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                {{ $y }}
            </option>
        @endfor
    </select>

    <button class="btn btn-sm btn-primary">
        <i class="bi bi-funnel"></i> Terapkan
    </button>

</form>


</div>

{{-- ======================= RINGKASAN KEUANGAN ======================= --}}

<div class="row g-4 mb-4">


{{-- Total Pemasukan --}}
<div class="col-md-3">
    <div class="card-artakula p-4 text-center">
        <small class="text-muted">Total Pemasukan</small>
        <h5 class="fw-bold text-success mt-2">
            Rp {{ number_format($totalPemasukan,0,',','.') }}
        </h5>
    </div>
</div>

{{-- Total Pengeluaran --}}
<div class="col-md-3">
    <div class="card-artakula p-4 text-center">
        <small class="text-muted">Total Pengeluaran</small>
        <h5 class="fw-bold text-danger mt-2">
            Rp {{ number_format($totalPengeluaran,0,',','.') }}
        </h5>
    </div>
</div>

{{-- Total Tabungan --}}
<div class="col-md-3">
    <div class="card-artakula p-4 text-center">
        <small class="text-muted">Total Tabungan</small>
        <h5 class="fw-bold text-primary mt-2">
            Rp {{ number_format($totalTabungan,0,',','.') }}
        </h5>
    </div>
</div>

{{-- Uang Aman Dipakai --}}
<div class="col-md-3">
    <div class="card-artakula p-4 text-center">
        <small class="text-muted">Uang Aman Dipakai</small>
        <h5 class="fw-bold {{ $uangAman >= 0 ? 'text-success' : 'text-danger' }} mt-2">
            Rp {{ number_format($uangAman,0,',','.') }}
        </h5>
    </div>
</div>


</div>

{{-- ======================= INSIGHT KEUANGAN ======================= --}}

<div class="card-artakula p-4 mb-4">
    <h6 class="fw-bold mb-2">Insight Keuangan</h6>


@if($uangAman < 0)
    <div class="alert alert-danger mb-0">
        Pengeluaran + tabungan Anda melebihi pemasukan. Dana bulan ini tidak mencukupi.
    </div>

@elseif($totalTabungan < ($totalPemasukan * 0.1))
    <div class="alert alert-warning mb-0">
        Anda belum menabung minimal 10% dari pemasukan bulan ini.
    </div>

@else
    <div class="alert alert-success mb-0">
        Kondisi keuangan Anda bulan ini cukup baik. Pertahankan kebiasaan ini!
    </div>
@endif


</div>

@endsection

{{-- ======================= TABEL (WAJIB DI SINI, BUKAN DI CONTENT) ======================= --}}
@section('table')

<thead class="border-bottom">
<tr>
    <th>Tanggal</th>
    <th>Jenis</th>
    <th>Kategori</th>
    <th>Keterangan</th>
    <th>Dompet</th>
    <th>Nominal</th>
</tr>
</thead>

<tbody>
@forelse($transaksi as $t)
<tr>


{{-- TANGGAL --}}
<td>
    {{ \Carbon\Carbon::parse($t->tanggal)->translatedFormat('d M Y') }}
</td>

{{-- JENIS TRANSAKSI --}}
<td>
    @if($t->jenis == 'Pemasukan')
        <span class="badge bg-success">Pemasukan</span>
    @elseif($t->jenis == 'Pengeluaran')
        <span class="badge bg-danger">Pengeluaran</span>
    @else
        <span class="badge bg-primary">Tabungan</span>
    @endif
</td>

{{-- KATEGORI --}}
<td>{{ $t->kategori }}</td>

{{-- KETERANGAN --}}
<td>{{ $t->keterangan ?? '-' }}</td>

<td>
    <span >
        {{ $t->dompet }}
    </span>
</td>

{{-- NOMINAL --}}
<td class="fw-bold
    @if($t->jenis == 'Pemasukan') text-success
    @elseif($t->jenis == 'Pengeluaran') text-danger
    @else text-primary
    @endif">

    {{-- PEMASUKAN --}}
    @if($t->jenis == 'Pemasukan')
        + Rp {{ number_format($t->nominal,0,',','.') }}

    {{-- PENGELUARAN --}}
    @elseif($t->jenis == 'Pengeluaran')
        - Rp {{ number_format(abs($t->nominal),0,',','.') }}

    {{-- TABUNGAN (TANPA TANDA) --}}
    @else
        Rp {{ number_format($t->nominal,0,',','.') }}
    @endif

</td>



</tr>
@empty
<tr>
    <td colspan="5" class="text-center text-muted">
        Tidak ada transaksi pada periode ini.
    </td>
</tr>
@endforelse
</tbody>

@endsection
