@extends('layouts.index')

@section('title', 'Dompet Saya | Artakula')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold">Dompet Saya</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('dompet.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tambah Dompet
        </a>

        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#iterasiModal">
            <i class="bi bi-arrow-repeat"></i> Iterasi Dompet Digital
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4">
@forelse($dompets as $dompet)
    <div class="col-md-4">
        <div class="card-artakula p-4 h-100">
            <h6 class="fw-bold mb-1">{{ $dompet->nama_dompet }}</h6>

            <p class="text-muted small mb-1">
                Jenis: {{ ucfirst($dompet->jenis) }}
                @if($dompet->bank_code)
                    • Provider: {{ strtoupper($dompet->bank_code) }}
                @endif
            </p>

            <h5 class="fw-bold text-success mb-3">
                Rp {{ number_format($dompet->saldo, 0, ',', '.') }}
            </h5>

             <p>
    <small>Total Tabungan:</small><br>
    <strong>
        Rp {{ number_format($dompet->total_tabungan, 0, ',', '.') }}
    </strong>
</p>

<p>
    <small>Saldo Bisa Dipakai:</small><br>
    <strong class="text-success">
        Rp {{ number_format($dompet->saldo_bisa_dipakai, 0, ',', '.') }}
    </strong>
</p>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('dompet.edit', $dompet->id) }}"
                   class="btn btn-sm btn-outline-primary">
                    Edit
                </a>

                {{-- 🔁 ITERASI SALDO (FIX ROUTE) --}}
                <!-- @if(in_array($dompet->jenis, ['bank','ewallet']))
                <a href="{{ route('dummy.wallet.iterate', $dompet->id) }}"
                   onclick="return confirm(
                   'Izinkan sistem mengiterasi saldo dari provider {{ strtoupper($dompet->bank_code) }}?')"
                   class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-arrow-repeat"></i> Iterasi
                </a>
                @endif -->

                {{-- Tombol Hapus dengan Modal --}}
    <button type="button" class="btn btn-sm btn-outline-danger" 
        data-bs-toggle="modal" data-bs-target="#deleteDompetModal{{ $dompet->id }}">
        Hapus
    </button>

    <!-- Modal Hapus Dompet -->
    <div class="modal fade" id="deleteDompetModal{{ $dompet->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Dompet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus dompet <strong>{{ $dompet->nama_dompet }}</strong> dengan saldo 
                    <strong>Rp {{ number_format($dompet->saldo,0,',','.') }}</strong>?
                    <br>
                    Semua pemasukan/ pengeluaran terkait dompet ini juga akan ikut terhapus.
                </div>
                <div class="modal-footer">
                    <form action="{{ route('dompet.destroy', $dompet->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Dompet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
            </div>

            @if($dompet->last_sync_at)
                <p class="text-muted small mt-3 mb-0">
                    Terakhir iterasi:
                    {{ $dompet->last_sync_at->diffForHumans() }}
                </p>
            @endif

        </div>
    </div>
@empty
    <p class="text-muted">Belum ada dompet.</p>
@endforelse
</div>

{{-- ================= MODAL ITERASI BARU ================= --}}
<div class="modal fade" id="iterasiModal">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="{{ route('dompet.iterate.create') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5>Iterasi Dompet Digital</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <label class="fw-bold">Jenis Dompet</label>
          <select id="jenisDompet" name="jenis" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="bank">Bank</option>
            <option value="ewallet">E-Wallet</option>
          </select>

          <label class="fw-bold mt-3">Provider</label>
          <select id="providerDompet" name="bank_code" class="form-select" required>
            <option value="">-- Pilih Provider --</option>
          </select>

          <div class="alert alert-warning small mt-3">
            Dengan melanjutkan, Anda mengizinkan sistem
            mengakses saldo dompet digital (simulasi dummy API).
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">
            Batal
          </button>
          <button class="btn btn-success">
            Setujui & Iterasi
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- ================= JS PROVIDER ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('/dompet/iterasi/providers')
        .then(res => res.json())
        .then(data => {
            document.getElementById('jenisDompet')
            .addEventListener('change', function () {

                let provider = document.getElementById('providerDompet');
                provider.innerHTML =
                  '<option value="">-- Pilih Provider --</option>';

                if (!data[this.value]) return;

                for (const code in data[this.value]) {
                    let opt = document.createElement('option');
                    opt.value = code;
                    opt.textContent = data[this.value][code];
                    provider.appendChild(opt);
                }
            });
        });
});
</script>

@endsection
