@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Tabungan | Artakula') {{-- Judul halaman pada title tag. --}}
@section('page_title', 'Overview Tabungan') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}

{{-- ================= HEADER ================= --}}
<div class="d-flex justify-content-between mb-4"> {{-- Baris header halaman. --}}
    <h5 class="fw-bold">Catat Tabungan</h5> {{-- Judul kecil halaman. --}}
</div> {{-- Tutup baris header. --}}

{{-- ================= FILTER BULAN & TAHUN ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4"> {{-- Baris filter dan tombol. --}}

    {{-- Form filter --}}
    <form method="GET" class="d-flex gap-2 mb-0"> {{-- Form filter bulan/tahun. --}}
        {{-- Pilih bulan --}}
        <select name="bulan" class="form-select form-select-sm w-auto"> {{-- Dropdown bulan. --}}
            @for ($m = 1; $m <= 12; $m++) {{-- Loop bulan 1-12. --}}
                <option value="{{ $m }}" @selected($bulan == $m)> {{-- Opsi bulan. --}}
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }} {{-- Nama bulan terjemahan. --}}
                </option> {{-- Tutup opsi bulan. --}}
            @endfor {{-- Selesai loop bulan. --}}
        </select> {{-- Tutup dropdown bulan. --}}

        {{-- Pilih tahun --}}
        <select name="tahun" class="form-select form-select-sm w-auto"> {{-- Dropdown tahun. --}}
            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++) {{-- Loop tahun -5 s.d +5. --}}
                <option value="{{ $y }}" @selected($tahun == $y)> {{-- Opsi tahun. --}}
                    {{ $y }} {{-- Tampilkan angka tahun. --}}
                </option> {{-- Tutup opsi tahun. --}}
            @endfor {{-- Selesai loop tahun. --}}
        </select> {{-- Tutup dropdown tahun. --}}

        <button class="btn btn-sm btn-outline-primary"> {{-- Tombol submit filter. --}}
            Terapkan {{-- Label tombol. --}}
        </button> {{-- Tutup tombol submit. --}}
    </form> {{-- Tutup form filter. --}}

    {{-- Tombol tambah tabungan --}}
    <a href="{{ route('tabungan.create', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
       class="btn btn-sm btn-success"> {{-- Link ke form tambah tabungan. --}}
        <i class="bi bi-plus-circle"></i> Tambah Tabungan {{-- Ikon + teks tombol. --}}
    </a> {{-- Tutup link tambah tabungan. --}}

</div> {{-- Tutup baris filter dan tombol. --}}

{{-- ================= ALERT SUCCESS ================= --}}
@if (session('success')) {{-- Tampilkan alert jika ada pesan sukses. --}}
    <div class="alert alert-success"> {{-- Kotak pesan sukses. --}}
        {{ session('success') }} {{-- Isi pesan sukses. --}}
    </div> {{-- Tutup kotak pesan sukses. --}}
@endif {{-- Tutup kondisi sukses. --}}

@endsection {{-- Akhiri section konten. --}}


{{-- ================= TABEL TABUNGAN ================= --}}
@section('table') {{-- Mulai section tabel. --}}
<thead> {{-- Header tabel. --}}
    <tr> {{-- Baris judul kolom. --}}
        <th class="text-center">Tanggal</th> {{-- Kolom tanggal. --}}
        <th class="text-center">Kategori</th> {{-- Kolom kategori. --}}
        <th class="text-center">Keterangan</th> {{-- Kolom keterangan. --}}
        <th class="text-center">Sumber Dompet</th> {{-- Kolom sumber dompet. --}}
        <th class="text-center">Dompet Tujuan</th> {{-- Kolom dompet tujuan. --}}
        <th class="text-center">Nominal</th> {{-- Kolom nominal. --}}
        <th class="text-center">Aksi</th> {{-- Kolom aksi. --}}
    </tr> {{-- Tutup baris judul. --}}
</thead> {{-- Tutup header tabel. --}}

<tbody> {{-- Isi tabel. --}}
@forelse ($tabungan as $item) {{-- Loop data tabungan, fallback jika kosong. --}}
    <tr> {{-- Baris data tabungan. --}}
        {{-- Tanggal --}}
        <td>
            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }} {{-- Format tanggal d M Y. --}}
        </td>

        {{-- Kategori --}}
        <td title="{{ $item->kategori->nama_kategori }}"> {{-- Tooltip nama kategori. --}}
            {{ $item->kategori->nama_kategori }} {{-- Tampilkan kategori. --}}
        </td>

        {{-- Keterangan --}}
        <td title="{{ $item->keterangan }}"> {{-- Tooltip keterangan. --}}
            {{ $item->keterangan ?? '-' }} {{-- Tampilkan keterangan atau '-' --}}
        </td>

        {{-- Dompet sumber --}}
        <td title="{{ $item->sumberDompet->nama_dompet }}"> {{-- Tooltip dompet sumber. --}}
            {{ $item->sumberDompet->nama_dompet }} {{-- Tampilkan dompet sumber. --}}
        </td>

        {{-- Dompet tujuan --}}
        <td title="{{ $item->kategori->dompetTujuan->nama_dompet ?? '-' }}"> {{-- Tooltip dompet tujuan. --}}
            {{ $item->kategori->dompetTujuan->nama_dompet ?? '-' }} {{-- Tampilkan dompet tujuan atau '-' --}}
        </td>

        {{-- Nominal --}}
        <td class="fw-bold text-primary"> {{-- Kolom nominal dengan gaya. --}}
            Rp {{ number_format($item->nominal, 0, ',', '.') }} {{-- Format rupiah. --}}
        </td>

        {{-- Aksi --}}
        <td class="text-center"> {{-- Kolom aksi. --}}
            <a href="{{ route('tabungan.edit', [
                    'tabungan' => $item->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]) }}"
               class="btn btn-sm btn-outline-primary"> {{-- Link ke form edit tabungan. --}}
                Edit {{-- Label tombol edit. --}}
            </a> {{-- Tutup link edit. --}}

            <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $item->id }}"> {{-- Tombol buka modal hapus. --}}
                Hapus {{-- Label tombol hapus. --}}
            </button> {{-- Tutup tombol hapus. --}}

            {{-- Modal hapus --}}
            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"> {{-- Modal konfirmasi hapus. --}}
                <div class="modal-dialog modal-dialog-centered"> {{-- Dialog modal. --}}
                    <div class="modal-content"> {{-- Konten modal. --}}
                        <div class="modal-header"> {{-- Header modal. --}}
                            <h5 class="modal-title">Konfirmasi Hapus</h5> {{-- Judul modal. --}}
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button> {{-- Tombol close modal. --}}
                        </div> {{-- Tutup header modal. --}}

                        <div class="modal-body"> {{-- Isi modal. --}}
                            Yakin mau hapus tabungan
                            <strong>{{ $item->kategori->nama_kategori }}</strong>
                            sebesar
                            <strong>
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </strong>? {{-- Pesan konfirmasi. --}}
                        </div> {{-- Tutup body modal. --}}

                        <div class="modal-footer"> {{-- Footer modal. --}}
                            <form method="POST"
                                  action="{{ route('tabungan.destroy', [
                                        'tabungan' => $item->id,
                                        'bulan' => $bulan,
                                        'tahun' => $tahun
                                  ]) }}"> {{-- Form hapus tabungan. --}}
                                @csrf {{-- Token CSRF. --}}
                                @method('DELETE') {{-- Spoof method DELETE. --}}

                                <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">
                                    Batal
                                </button> {{-- Tombol batal. --}}

                                <button class="btn btn-danger">
                                    Hapus
                                </button> {{-- Tombol konfirmasi hapus. --}}
                            </form> {{-- Tutup form hapus. --}}
                        </div> {{-- Tutup footer modal. --}}
                    </div> {{-- Tutup konten modal. --}}
                </div> {{-- Tutup dialog modal. --}}
            </div> {{-- Tutup modal. --}}

        </td> {{-- Tutup kolom aksi. --}}
    </tr> {{-- Tutup baris data. --}}
@empty {{-- Jika tidak ada data. --}}
    <tr> {{-- Baris kosong. --}}
        <td colspan="7" class="text-center text-muted py-4"> {{-- Pesan kosong. --}}
            Belum ada data tabungan
        </td>
    </tr> {{-- Tutup baris kosong. --}}
@endforelse {{-- Tutup loop tabungan. --}}
</tbody> {{-- Tutup body tabel. --}}
@endsection {{-- Akhiri section tabel. --}}
