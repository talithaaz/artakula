@extends('layouts.index')

@section('title', 'Dashboard | Artakula')

@section('content')

{{-- 
PASTE SEMUA ISI DASHBOARD DI SINI 
MULAI DARI:
<div class="row g-4 mb-4">

SAMPAI:
SEBELUM FOOTER
--}}
<div class="row g-4 mb-4 text-center text-md-start">
            <div class="col-lg-4">
                <div class="card-artakula card-gradient text-white">
                    <p class="small opacity-75 mb-1 text-uppercase fw-bold">Total Saldo Seluruh Dompet</p>
                    <h2 class="fw-bold mb-0">
                        Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card-artakula border-start border-success border-4">
                    <p class="text-secondary small mb-1 fw-bold">TOTAL PEMASUKAN</p>
                    <h4 class="fw-bold text-success">
                        Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </h4>
                    <p class="small text-muted mb-0">
                        Periode {{ $namaPeriode }}
                    </p>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card-artakula border-start border-danger border-4">
                    <p class="text-secondary small mb-1 fw-bold">TOTAL PENGELUARAN</p>
                    <h4 class="fw-bold text-danger">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </h4>
                    <p class="small text-muted mb-0">
                        Periode {{ $namaPeriode }}
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
    <div class="card-artakula">
        <h6 class="fw-bold mb-4">
            Grafik Perkembangan Tabungan ({{ $tahun }})
        </h6>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="savingYearlyChart"></canvas>
        </div>
    </div>
</div>

<div class="col-lg-4">
    <div class="card-artakula">
        <h6 class="fw-bold mb-4 text-center">Proporsi Pengeluaran</h6>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="expenseCircleChart"></canvas>
        </div>
    </div>
</div>

            <div class="col-lg-12">
                <div class="card-artakula">
                    <h6 class="fw-bold mb-4">Target Tabungan Per Kategori</h6>
                    <div class="row g-3">
    @forelse ($targetTabungan as $item)
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-4">
                <div class="d-flex justify-content-between mb-2 small fw-bold">
                    <span>{{ $item->nama_kategori }}</span>
                    <span class="text-success">
                        {{ $item->persen }}%
                    </span>
                </div>

                <div class="progress" style="height: 8px;">
                    <div
                        class="progress-bar bg-success"
                        style="width: {{ $item->persen }}%;">
                    </div>
                </div>

                <div class="small text-muted mt-2">
                    Rp {{ number_format($item->total_tabungan, 0, ',', '.') }}
                    /
                    Rp {{ number_format($item->target_nominal, 0, ',', '.') }}
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center text-muted small">
            Belum ada target tabungan
        </div>
    @endforelse
</div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card-artakula">
                    <h6 class="fw-bold mb-4">Tabel Transaksi Terkini</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead class="bg-light small">
                                <tr>
                                <th>TANGGAL</th>    
                                <th>JENIS</th>
                                    <th>KETERANGAN</th>
                                    <th>KATEGORI</th>
                                    <th>DOMPET</th>
                                    <th>NOMINAL</th>
                                    
                            </thead>
                            <tbody class="small fw-bold">
                               @forelse ($transaksiTerkini as $trx)
<tr>
    <td class="text-muted">
    {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}
</td>

    <td>
        @if ($trx->jenis === 'MASUK')
            <span class="badge bg-success-subtle text-success px-3">MASUK</span>
        @elseif ($trx->jenis === 'KELUAR')
            <span class="badge bg-danger-subtle text-danger px-3">KELUAR</span>
        @else
            <span class="badge bg-primary-subtle text-primary px-3">TABUNG</span>
        @endif
    </td>
    
    <td>{{ $trx->nama }}</td>
    <td>{{ $trx->kategori }}</td>
    <td>{{ $trx->dompet }}</td>

    <td class="
        {{ $trx->jenis === 'MASUK' ? 'text-success' : '' }}
        {{ $trx->jenis === 'KELUAR' ? 'text-danger' : '' }}
        {{ $trx->jenis === 'TABUNG' ? 'text-primary' : '' }}
    ">
        @if ($trx->jenis === 'MASUK')
            + Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
        @elseif ($trx->jenis === 'KELUAR')
            - Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
        @else
            Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">
        Belum ada transaksi 2 hari terakhir
    </td>
</tr>
@endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('scripts')
<script>
    const dataTabunganTahunan = @json($dataTabunganTahunan);
    const labelPengeluaran = @json($labelPengeluaran);
    const dataPengeluaran = @json($dataPengeluaran);
</script>

<script>
    // SEMUA SCRIPT CHART & JS DASHBOARD KAMU
    // 5) Grafik Perkembangan Tabungan
        new Chart(document.getElementById('savingYearlyChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Tabungan',
                    data: dataTabunganTahunan,
                    borderColor: '#10b981',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(16, 185, 129, 0.05)'
                }]
            },
            options: {
        responsive: true,
        maintainAspectRatio: false, // WAJIB FALSE
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }}
        });

        // 6) Grafik Bulat Pengeluaran
        new Chart(document.getElementById('expenseCircleChart'), {
            type: 'doughnut',
            data: {
                labels: labelPengeluaran,
datasets: [{
    data: dataPengeluaran,
    backgroundColor: [
        '#10b981',
        '#34d399',
        '#059669',
        '#6ee7b7',
        '#a7f3d0'
    ],
    borderWidth: 0
}]

            },
options: {
        responsive: true,
        maintainAspectRatio: false, // WAJIB FALSE
        cutout: '70%',
        plugins: {
            legend: { position: 'bottom' }
        }
    }        });
</script>
@endpush
