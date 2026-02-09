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

<!-- <div class="card-artakula p-4 mb-4">
    <h6 class="fw-bold mb-2">Insight Keuangan</h6>
<div class="alert alert-info">
    <b>Predikat Keuangan:</b> {{ $evaluasi->predikat }} <br>

    <b>Kategori Pengeluaran Terbesar:</b>
    {{ $evaluasi->kategori_dominan ?? '-' }}
    ({{ number_format($evaluasi->persen_dominan,1) }}%)
</div> -->


<div class="card-artakula p-4 mb-4">
    <h6 class="fw-bold mb-2">Insight Keuangan</h6>

    <div class="alert alert-{{ $warna }}">
        <b>Predikat Keuangan:</b> {{ $predikat }} <br>

        <b>Kategori Pengeluaran Terbesar:</b>
        {{ $evaluasi->kategori_dominan ?? '-' }}
        ({{ number_format($evaluasi->persen_dominan,1) }}%)

        <div class="mt-2">
            {{ $pesanUtama }}
        </div>
    </div>

    {{-- ANALISIS DETAIL --}}
    @if(!empty($insights))
        <div class="alert alert-light border mt-3 mb-0">
            <b>Analisis Sistem:</b>
            <p><b>Trend Pemasukan:</b> {{ $analisisPemasukan }}</p>

@if(!empty($kategoriBoros))
<p><b>Kategori Boros:</b> {{ implode(', ', $kategoriBoros) }}</p>
@endif

@if(!empty($kategoriAman))
<p><b>Kategori Aman:</b> {{ implode(', ', $kategoriAman) }}</p>
@endif

<p><b>Progres Tabungan:</b></p>
<ul>
@foreach($analisisTabungan as $t)
<li>{{ $t }}</li>
@endforeach
</ul>

@if(!empty($tabunganTercapai))
<p><b>Target Tercapai:</b> {{ implode(', ', $tabunganTercapai) }}</p>
@endif

@if(!empty($tabunganHampir))
<p><b>Hampir Target:</b> {{ implode(', ', $tabunganHampir) }}</p>
@endif

<hr>

<h6 class="fw-bold">Rekomendasi Bulan Depan</h6>
<ul>
@foreach($rekomendasi as $r)
<li>{{ $r }}</li>
@endforeach
</ul>

<h6 class="fw-bold mt-3">Motivasi</h6>
@foreach($motivasi as $m)
<p>{{ $m }}</p>
@endforeach
        </div>
    @endif
</div>





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
