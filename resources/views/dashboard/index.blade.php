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
                    <h2 class="fw-bold mb-0">Rp 15.240.000</h2>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card-artakula border-start border-success border-4">
                    <p class="text-secondary small mb-1 fw-bold">TOTAL PEMASUKAN</p>
                    <h4 class="fw-bold text-success">Rp 12.000.000</h4>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card-artakula border-start border-danger border-4">
                    <p class="text-secondary small mb-1 fw-bold">TOTAL PENGELUARAN</p>
                    <h4 class="fw-bold text-danger">Rp 4.520.000</h4>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
    <div class="card-artakula">
        <h6 class="fw-bold mb-4">Grafik Perkembangan Tabungan (2025)</h6>
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
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4">
                                <div class="d-flex justify-content-between mb-2 small fw-bold">
                                    <span>Beli Motor</span><span class="text-success">45%</span>
                                </div>
                                <div class="progress" style="height: 8px;"><div class="progress-bar bg-success" style="width: 45%;"></div></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4">
                                <div class="d-flex justify-content-between mb-2 small fw-bold">
                                    <span>Dana Darurat</span><span class="text-success">70%</span>
                                </div>
                                <div class="progress" style="height: 8px;"><div class="progress-bar bg-success" style="width: 70%;"></div></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4">
                                <div class="d-flex justify-content-between mb-2 small fw-bold">
                                    <span>Beli HP Baru</span><span class="text-success">30%</span>
                                </div>
                                <div class="progress" style="height: 8px;"><div class="progress-bar bg-success" style="width: 30%;"></div></div>
                            </div>
                        </div>
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
                                    <th>JENIS</th><th>KETERANGAN</th><th>KATEGORI</th><th>DOMPET</th><th class="text-end">JUMLAH</th>
                                </tr>
                            </thead>
                            <tbody class="small fw-bold">
                                <tr>
                                    <td><span class="badge bg-success-subtle text-success px-3">MASUK</span></td>
                                    <td>Gaji Freelance</td><td>Pekerjaan</td><td>Bank BCA</td>
                                    <td class="text-end text-success">+ Rp 2.000.000</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger-subtle text-danger px-3">KELUAR</span></td>
                                    <td>Beli Kopi</td><td>Ngopi</td><td>GoPay</td>
                                    <td class="text-end text-danger">- Rp 25.000</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary-subtle text-primary px-3">TABUNG</span></td>
                                    <td>Nabung Motor</td><td>Motor</td><td>Bank Mandiri</td>
                                    <td class="text-end text-primary">Rp 500.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('scripts')
<script>
    // SEMUA SCRIPT CHART & JS DASHBOARD KAMU
    // 5) Grafik Perkembangan Tabungan
        new Chart(document.getElementById('savingYearlyChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Tabungan',
                    data: [1.2, 1.5, 1.4, 2.0, 2.8, 2.5, 3.0, 3.5, 3.2, 4.0, 4.5, 5.0],
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
                labels: ['Makan', 'Ngopi', 'Transportasi', 'Lainnya'],
                datasets: [{
                    data: [60, 15, 20, 5],
                    backgroundColor: ['#10b981', '#34d399', '#059669', '#f1f5f9'],
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
