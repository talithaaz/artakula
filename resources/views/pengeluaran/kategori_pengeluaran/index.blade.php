@extends('layouts.index') {{-- Pakai layout utama. --}}

{{-- Title halaman --}}
@section('title','Kategori Pengeluaran | Artakula') {{-- Judul halaman pada title tag. --}}

{{-- Judul halaman --}}
@section('page_title', 'Overview Pengeluaran') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}

{{-- Header --}}
<div class="d-flex justify-content-between mb-4"> {{-- Baris header halaman. --}}
    <h5 class="fw-bold">Kategori Pengeluaran</h5> {{-- Judul kecil halaman. --}}
</div> {{-- Tutup baris header halaman. --}}

{{-- Filter bulan & tahun + tombol tambah --}}
<div class="d-flex justify-content-between align-items-center flex-nowrap gap-2 mb-4"> {{-- Baris filter + tombol tambah. --}}

    {{-- Form filter bulan & tahun --}}
    <form method="GET" class="d-flex align-items-center gap-2 flex-nowrap mb-0"> {{-- Form filter periode. --}}

        {{-- Dropdown bulan --}}
        <select name="bulan" class="form-select form-select-sm w-auto"> {{-- Pilihan bulan. --}}
            @for ($m = 1; $m <= 12; $m++) {{-- Loop bulan 1-12. --}}
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}> {{-- Opsi bulan. --}}
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }} {{-- Nama bulan terjemahan. --}}
                </option> {{-- Tutup opsi bulan. --}}
            @endfor {{-- Selesai loop bulan. --}}
        </select> {{-- Tutup dropdown bulan. --}}

        {{-- Dropdown tahun --}}
        <select name="tahun" class="form-select form-select-sm w-auto"> {{-- Pilihan tahun. --}}
            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++) {{-- Loop tahun dari -5 s.d +5. --}}
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}> {{-- Opsi tahun. --}}
                    {{ $y }} {{-- Tampilkan angka tahun. --}}
                </option> {{-- Tutup opsi tahun. --}}
            @endfor {{-- Selesai loop tahun. --}}
        </select> {{-- Tutup dropdown tahun. --}}

        {{-- Tombol terapkan filter --}}
        <button class="btn btn-sm btn-outline-primary text-nowrap"> {{-- Tombol submit filter. --}}
            Terapkan {{-- Label tombol. --}}
        </button> {{-- Tutup tombol submit. --}}
    </form> {{-- Tutup form filter. --}}

    {{-- Tombol tambah kategori --}}
    <a href="{{ route('kategori_pengeluaran.create', [
            'bulan' => $bulan,
            'tahun' => $tahun
        ]) }}"
       class="btn btn-sm btn-success text-nowrap"> {{-- Link ke form tambah kategori. --}}
        <i class="bi bi-plus-circle"></i> Tambah Kategori {{-- Ikon + teks tombol. --}}
    </a> {{-- Tutup link tambah kategori. --}}

</div> {{-- Tutup baris filter + tombol tambah. --}}

{{-- Alert sukses --}}
@if(session('success')) {{-- Tampilkan alert jika ada pesan sukses. --}}
    <div class="alert alert-success"> {{-- Kotak pesan sukses. --}}
        {{ session('success') }} {{-- Isi pesan sukses. --}}
    </div> {{-- Tutup kotak pesan sukses. --}}
@endif {{-- Tutup kondisi sukses. --}}

@endsection {{-- Akhiri section konten. --}}

{{-- ================= TABLE ================= --}}
@section('table') {{-- Mulai section tabel. --}}

<thead class="border-bottom"> {{-- Header tabel. --}}
    <tr> {{-- Baris judul kolom. --}}
        <th>Nama Kategori</th> {{-- Kolom nama kategori. --}}
        <th class="text-center">Budget</th> {{-- Kolom budget. --}}
        <th class="text-center">Terpakai</th> {{-- Kolom terpakai. --}}
        <th class="text-center">Sisa</th> {{-- Kolom sisa. --}}
        <th class="text-center">Periode</th> {{-- Kolom periode. --}}
        <th class="text-center">Aksi</th> {{-- Kolom aksi. --}}
    </tr> {{-- Tutup baris judul. --}}
</thead> {{-- Tutup header tabel. --}}

<tbody> {{-- Isi tabel. --}}
@forelse ($kategori as $item) {{-- Loop data kategori, fallback jika kosong. --}}
    <tr> {{-- Baris data kategori. --}}
        <td>{{ $item->nama_kategori }}</td> {{-- Tampilkan nama kategori. --}}

        <td> {{-- Kolom budget. --}}
            Rp {{ number_format($item->budget, 0, ',', '.') }} {{-- Format budget rupiah. --}}
        </td> {{-- Tutup kolom budget. --}}

        <td> {{-- Kolom terpakai. --}}
            Rp {{ number_format($item->terpakai, 0, ',', '.') }} {{-- Format terpakai rupiah. --}}
        </td> {{-- Tutup kolom terpakai. --}}

        <td> {{-- Kolom sisa. --}}
            Rp {{ number_format($item->sisa, 0, ',', '.') }} {{-- Format sisa rupiah. --}}
        </td> {{-- Tutup kolom sisa. --}}

        {{-- Periode kategori --}}
        <td> {{-- Kolom periode. --}}
            {{ $item->periode_awal
                ? \Carbon\Carbon::parse($item->periode_awal)->format('d M Y')
                : '-' }} {{-- Format tanggal awal atau '-' --}}
            - {{-- Pemisah periode. --}}
            {{ $item->periode_akhir
                ? \Carbon\Carbon::parse($item->periode_akhir)->format('d M Y')
                : '-' }} {{-- Format tanggal akhir atau '-' --}}
        </td> {{-- Tutup kolom periode. --}}

        {{-- Aksi --}}
        <td class="text-center"> {{-- Kolom aksi. --}}

            {{-- Tombol edit --}}
            <a href="{{ route('kategori_pengeluaran.edit', [
                    'kategori_pengeluaran' => $item->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]) }}"
               class="btn btn-sm btn-outline-primary"> {{-- Link ke form edit kategori. --}}
                Edit {{-- Label tombol edit. --}}
            </a> {{-- Tutup link edit. --}}

            {{-- Tombol hapus --}}
            <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $item->id }}"> {{-- Tombol buka modal hapus. --}}
                Hapus {{-- Label tombol hapus. --}}
            </button> {{-- Tutup tombol hapus. --}}

            {{-- Modal konfirmasi hapus --}}
            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"> {{-- Modal hapus. --}}
                <div class="modal-dialog modal-dialog-centered"> {{-- Dialog modal. --}}
                    <div class="modal-content"> {{-- Konten modal. --}}

                        <div class="modal-header"> {{-- Header modal. --}}
                            <h5 class="modal-title">Konfirmasi Hapus</h5> {{-- Judul modal. --}}
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button> {{-- Tombol close modal. --}}
                        </div> {{-- Tutup header modal. --}}

                        <div class="modal-body"> {{-- Isi modal. --}}
                            Yakin mau hapus kategori
                            <strong>{{ $item->nama_kategori }}</strong>? {{-- Nama kategori yang dihapus. --}}
                            <br>
                            Semua pengeluaran dengan kategori ini akan ikut terhapus. {{-- Peringatan. --}}
                        </div> {{-- Tutup body modal. --}}

                        <div class="modal-footer"> {{-- Footer modal. --}}
                            <form method="POST"
                                  action="{{ route('kategori_pengeluaran.destroy', [
                                        'kategori_pengeluaran' => $item->id,
                                        'bulan' => $bulan,
                                        'tahun' => $tahun
                                  ]) }}"> {{-- Form hapus kategori. --}}
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
@empty {{-- Jika tidak ada kategori. --}}
    <tr> {{-- Baris kosong. --}}
        <td colspan="6" class="text-center text-muted"> {{-- Pesan kosong. --}}
            Belum ada kategori
        </td>
    </tr> {{-- Tutup baris kosong. --}}
@endforelse {{-- Tutup loop kategori. --}}
</tbody> {{-- Tutup body tabel. --}}

@endsection {{-- Akhiri section tabel. --}}
