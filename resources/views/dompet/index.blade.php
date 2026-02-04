@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Dompet Saya | Artakula') {{-- Judul halaman pada title tag. --}}
@section('page_title', 'Overview Dompet') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}

<div class="d-flex justify-content-between align-items-center mb-4"> {{-- Baris header halaman. --}}
    <h5 class="fw-bold">Dompet Saya</h5> {{-- Judul kecil halaman. --}}
    <div class="d-flex gap-2"> {{-- Wrapper tombol aksi. --}}
        <a href="{{ route('dompet.create') }}" class="btn btn-success"> {{-- Link ke form tambah dompet. --}}
            <i class="bi bi-plus-circle"></i> Tambah Dompet {{-- Ikon + teks tombol. --}}
        </a> {{-- Tutup link tambah dompet. --}}

        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#iterasiModal"> {{-- Tombol buka modal iterasi. --}}
            <i class="bi bi-arrow-repeat"></i> Iterasi Dompet Digital {{-- Ikon + teks tombol. --}}
        </button> {{-- Tutup tombol modal iterasi. --}}
    </div> {{-- Tutup wrapper tombol aksi. --}}
</div> {{-- Tutup baris header halaman. --}}

@if(session('success')) {{-- Tampilkan alert jika ada pesan sukses. --}}
    <div class="alert alert-success">{{ session('success') }}</div> {{-- Kotak pesan sukses. --}}
@endif {{-- Tutup kondisi pesan sukses. --}}

<div class="row g-4"> {{-- Grid daftar dompet. --}}
@forelse($dompets as $dompet) {{-- Loop data dompet, fallback jika kosong. --}}
    <div class="col-md-4"> {{-- Kolom card dompet. --}}
        <div class="card-artakula p-4 h-100"> {{-- Card dompet. --}}
            <h6 class="fw-bold mb-1">{{ $dompet->nama_dompet }}</h6> {{-- Nama dompet. --}}

            <p class="text-muted small mb-1"> {{-- Info jenis dan provider. --}}
                Jenis: {{ ucfirst($dompet->jenis) }} {{-- Tampilkan jenis dompet. --}}
                @if($dompet->bank_code) {{-- Jika ada provider. --}}
                    &bull; Provider: {{ strtoupper($dompet->bank_code) }} {{-- Tampilkan provider. --}}
                @endif {{-- Tutup kondisi provider. --}}
            </p> {{-- Tutup info jenis/provider. --}}

            <h5 class="fw-bold text-success mb-3"> {{-- Saldo dompet. --}}
                Rp {{ number_format($dompet->saldo, 0, ',', '.') }} {{-- Format saldo rupiah. --}}
            </h5> {{-- Tutup saldo dompet. --}}

            <p> {{-- Ringkasan total tabungan. --}}
                <small>Total Tabungan:</small><br> {{-- Label total tabungan. --}}
                <strong> {{-- Nilai total tabungan. --}}
                    Rp {{ number_format($dompet->total_tabungan, 0, ',', '.') }} {{-- Format total tabungan. --}}
                </strong> {{-- Tutup nilai total tabungan. --}}
            </p> {{-- Tutup ringkasan total tabungan. --}}

            <p> {{-- Ringkasan saldo bisa dipakai. --}}
                <small>Saldo Bisa Dipakai:</small><br> {{-- Label saldo bisa dipakai. --}}
                <strong class="text-success"> {{-- Nilai saldo bisa dipakai. --}}
                    Rp {{ number_format($dompet->saldo_bisa_dipakai, 0, ',', '.') }} {{-- Format saldo bisa dipakai. --}}
                </strong> {{-- Tutup nilai saldo bisa dipakai. --}}
            </p> {{-- Tutup ringkasan saldo bisa dipakai. --}}

            <div class="d-flex gap-2 flex-wrap"> {{-- Wrapper tombol aksi. --}}
                <a href="{{ route('dompet.edit', $dompet->id) }}" class="btn btn-sm btn-outline-primary"> {{-- Link ke form edit dompet. --}}
                    Edit {{-- Label tombol edit. --}}
                </a> {{-- Tutup link edit. --}}

                {{-- ITERASI SALDO (FIX ROUTE) --}}
                <!-- @if(in_array($dompet->jenis, ['bank','ewallet']))
                <a href="{{ route('dummy.wallet.iterate', $dompet->id) }}"
                   onclick="return confirm(
                   'Izinkan sistem mengiterasi saldo dari provider {{ strtoupper($dompet->bank_code) }}?')"
                   class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-arrow-repeat"></i> Iterasi
                </a>
                @endif -->

                {{-- Tombol Hapus dengan Modal --}}
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDompetModal{{ $dompet->id }}"> {{-- Tombol buka modal hapus. --}}
                    Hapus {{-- Label tombol hapus. --}}
                </button> {{-- Tutup tombol hapus. --}}

                <!-- Modal Hapus Dompet -->
                <div class="modal fade" id="deleteDompetModal{{ $dompet->id }}" tabindex="-1" aria-hidden="true"> {{-- Modal konfirmasi hapus. --}}
                    <div class="modal-dialog"> {{-- Wrapper dialog modal. --}}
                        <div class="modal-content"> {{-- Konten modal. --}}
                            <div class="modal-header"> {{-- Header modal. --}}
                                <h5 class="modal-title">Konfirmasi Hapus Dompet</h5> {{-- Judul modal. --}}
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button> {{-- Tombol close modal. --}}
                            </div> {{-- Tutup header modal. --}}
                            <div class="modal-body"> {{-- Isi modal. --}}
                                Apakah Anda yakin ingin menghapus dompet <strong>{{ $dompet->nama_dompet }}</strong> dengan saldo
                                <strong>Rp {{ number_format($dompet->saldo, 0, ',', '.') }}</strong>? {{-- Ringkasan dompet & saldo. --}}
                                <br>
                                Semua pemasukan/ pengeluaran terkait dompet ini juga akan ikut terhapus. {{-- Peringatan tambahan. --}}
                            </div> {{-- Tutup body modal. --}}
                            <div class="modal-footer"> {{-- Footer modal. --}}
                                <form action="{{ route('dompet.destroy', $dompet->id) }}" method="POST"> {{-- Form hapus dompet. --}}
                                    @csrf {{-- Token CSRF. --}}
                                    @method('DELETE') {{-- Spoof method DELETE. --}}
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> {{-- Tombol batal. --}}
                                    <button type="submit" class="btn btn-danger">Hapus Dompet</button> {{-- Tombol submit hapus. --}}
                                </form> {{-- Tutup form hapus. --}}
                            </div> {{-- Tutup footer modal. --}}
                        </div> {{-- Tutup konten modal. --}}
                    </div> {{-- Tutup dialog modal. --}}
                </div> {{-- Tutup modal. --}}
            </div> {{-- Tutup wrapper tombol aksi. --}}

            @if($dompet->last_sync_at) {{-- Tampilkan info iterasi terakhir jika ada. --}}
                <p class="text-muted small mt-3 mb-0"> {{-- Teks waktu iterasi terakhir. --}}
                    Terakhir iterasi: {{-- Label iterasi terakhir. --}}
                    {{ $dompet->last_sync_at->diffForHumans() }} {{-- Format waktu relatif. --}}
                </p> {{-- Tutup teks iterasi terakhir. --}}
            @endif {{-- Tutup kondisi iterasi terakhir. --}}

        </div> {{-- Tutup card dompet. --}}
    </div> {{-- Tutup kolom dompet. --}}
@empty {{-- Jika tidak ada dompet. --}}
    <p class="text-muted">Belum ada dompet.</p> {{-- Pesan kosong. --}}
@endforelse {{-- Tutup loop dompet. --}}
</div> {{-- Tutup grid dompet. --}}

{{-- ================= MODAL ITERASI BARU ================= --}}
<div class="modal fade" id="iterasiModal"> {{-- Modal iterasi dompet digital. --}}
    <div class="modal-dialog modal-dialog-centered"> {{-- Posisi modal di tengah. --}}
        <form method="POST" action="{{ route('dompet.iterate.create') }}"> {{-- Form submit iterasi. --}}
            @csrf {{-- Token CSRF. --}}
            <div class="modal-content"> {{-- Konten modal. --}}
                <div class="modal-header"> {{-- Header modal. --}}
                    <h5>Iterasi Dompet Digital</h5> {{-- Judul modal. --}}
                    <button class="btn-close" data-bs-dismiss="modal"></button> {{-- Tombol close modal. --}}
                </div> {{-- Tutup header modal. --}}

                <div class="modal-body"> {{-- Isi modal. --}}
                    <label class="fw-bold">Jenis Dompet</label> {{-- Label jenis dompet. --}}
                    <select id="jenisDompet" name="jenis" class="form-select" required> {{-- Dropdown jenis dompet. --}}
                        <option value="">-- Pilih --</option> {{-- Placeholder jenis dompet. --}}
                        <option value="bank">Bank</option> {{-- Opsi bank. --}}
                        <option value="ewallet">E-Wallet</option> {{-- Opsi e-wallet. --}}
                    </select> {{-- Tutup dropdown jenis dompet. --}}

                    <label class="fw-bold mt-3">Provider</label> {{-- Label provider. --}}
                    <select id="providerDompet" name="bank_code" class="form-select" required> {{-- Dropdown provider. --}}
                        <option value="">-- Pilih Provider --</option> {{-- Placeholder provider. --}}
                    </select> {{-- Tutup dropdown provider. --}}

                    <div class="alert alert-warning small mt-3"> {{-- Peringatan sebelum iterasi. --}}
                        Dengan melanjutkan, Anda mengizinkan sistem
                        mengakses saldo dompet digital (simulasi dummy API).
                    </div> {{-- Tutup peringatan. --}}
                </div> {{-- Tutup body modal. --}}

                <div class="modal-footer"> {{-- Footer modal. --}}
                    <button class="btn btn-secondary" data-bs-dismiss="modal"> {{-- Tombol batal. --}}
                        Batal
                    </button> {{-- Tutup tombol batal. --}}
                    <button class="btn btn-success"> {{-- Tombol submit iterasi. --}}
                        Setujui & Iterasi
                    </button> {{-- Tutup tombol submit iterasi. --}}
                </div> {{-- Tutup footer modal. --}}
            </div> {{-- Tutup konten modal. --}}
        </form> {{-- Tutup form iterasi. --}}
    </div> {{-- Tutup dialog modal. --}}
</div> {{-- Tutup modal iterasi. --}}

{{-- ================= JS PROVIDER ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () { // Jalankan setelah DOM siap.
    fetch('/dompet/iterasi/providers') // Ambil data provider tersedia.
        .then(res => res.json()) // Ubah response ke JSON.
        .then(data => { // Terima data provider.
            document.getElementById('jenisDompet') // Ambil dropdown jenis dompet.
                .addEventListener('change', function () { // Saat jenis berubah.
                    let provider = document.getElementById('providerDompet'); // Ambil dropdown provider.
                    provider.innerHTML =
                        '<option value="">-- Pilih Provider --</option>'; // Reset opsi provider.

                    if (!data[this.value]) return; // Jika jenis tidak ada, berhenti.

                    for (const code in data[this.value]) { // Loop provider sesuai jenis.
                        let opt = document.createElement('option'); // Buat elemen option baru.
                        opt.value = code; // Set value option.
                        opt.textContent = data[this.value][code]; // Set label option.
                        provider.appendChild(opt); // Tambahkan option ke dropdown.
                    }
                }); // Tutup event change.
        }); // Tutup then data.
}); // Tutup event DOMContentLoaded.
</script>

@endsection {{-- Akhiri section konten. --}}
