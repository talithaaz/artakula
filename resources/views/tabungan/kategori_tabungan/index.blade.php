@extends('layouts.index') {{-- Pakai layout utama. --}}

{{-- Judul halaman --}}
@section('title', 'Kategori Tabungan | Artakula') {{-- Judul halaman pada title tag. --}}

{{-- Judul besar halaman --}}
@section('page_title', 'Overview Tabungan') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}

{{-- Header halaman --}}
<div class="d-flex justify-content-between mb-4"> {{-- Baris header halaman. --}}
    <h5 class="fw-bold">Kategori Tabungan</h5> {{-- Judul kecil halaman. --}}
</div> {{-- Tutup baris header. --}}

{{-- FILTER BULAN & TAHUN + TOMBOL TAMBAH --}}
<div class="d-flex justify-content-between align-items-center flex-nowrap gap-2 mb-4"> {{-- Baris filter dan tombol. --}}

    {{-- Form filter bulan & tahun --}}
    <form method="GET" class="d-flex align-items-center gap-2 flex-nowrap mb-0"> {{-- Form filter periode. --}}

        {{-- Select bulan --}}
        <select name="bulan" class="form-select form-select-sm w-auto"> {{-- Dropdown bulan. --}}
            @for ($m = 1; $m <= 12; $m++) {{-- Loop bulan 1-12. --}}
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}> {{-- Opsi bulan. --}}
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }} {{-- Nama bulan terjemahan. --}}
                </option> {{-- Tutup opsi bulan. --}}
            @endfor {{-- Selesai loop bulan. --}}
        </select> {{-- Tutup dropdown bulan. --}}

        {{-- Select tahun --}}
        <select name="tahun" class="form-select form-select-sm w-auto"> {{-- Dropdown tahun. --}}
            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++) {{-- Loop tahun -5 s.d +5. --}}
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

    {{-- Tombol tambah kategori tabungan --}}
    <a
        href="{{ route('kategoriTabungan.create', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
        class="btn btn-sm btn-success text-nowrap"
    > {{-- Link ke form tambah kategori tabungan. --}}
        <i class="bi bi-plus-circle"></i> Tambah Kategori {{-- Ikon + teks tombol. --}}
    </a> {{-- Tutup link tambah kategori. --}}

</div> {{-- Tutup baris filter dan tombol. --}}

{{-- Alert sukses --}}
@if (session('success')) {{-- Tampilkan alert jika ada pesan sukses. --}}
    <div class="alert alert-success"> {{-- Kotak pesan sukses. --}}
        {{ session('success') }} {{-- Isi pesan sukses. --}}
    </div> {{-- Tutup kotak pesan sukses. --}}
@endif {{-- Tutup kondisi sukses. --}}

@endsection {{-- Akhiri section konten. --}}

{{-- ========================= --}}
{{-- SECTION TABLE --}}
{{-- ========================= --}}
@section('table') {{-- Mulai section tabel. --}}

<thead> {{-- Header tabel. --}}
    <tr> {{-- Baris judul kolom. --}}
        <th class="text-center">Nama Tabungan</th> {{-- Kolom nama tabungan. --}}
        <th class="text-center">Dompet Tujuan</th> {{-- Kolom dompet tujuan. --}}
        <th class="text-center">Target Nominal</th> {{-- Kolom target nominal. --}}
        <th class="text-center">Sudah Ditabung</th> {{-- Kolom sudah ditabung. --}}
        <th class="text-center">Sisa Target</th> {{-- Kolom sisa target. --}}
        <th class="text-center">Target Waktu</th> {{-- Kolom target waktu. --}}
        <th class="text-center">Aksi</th> {{-- Kolom aksi. --}}
    </tr> {{-- Tutup baris judul. --}}
</thead> {{-- Tutup header tabel. --}}

<tbody> {{-- Isi tabel. --}}

@foreach ($kategoriTabungan as $item) {{-- Loop data kategori tabungan. --}}

    @php
        // Total tabungan yang sudah masuk
        $sudahDitabung = $item->total_ditabung ?? 0;

        // Sisa target tabungan
        $sisaTarget = $item->target_nominal - $sudahDitabung;
    @endphp

    <tr> {{-- Baris data kategori tabungan. --}}
        {{-- Nama kategori --}}
        <td>{{ $item->nama_kategori }}</td> {{-- Tampilkan nama kategori. --}}

        {{-- Dompet tujuan --}}
        <td>
            {{ $item->dompetTujuan->nama_dompet }} {{-- Tampilkan dompet tujuan. --}}
        </td>

        {{-- Target nominal --}}
        <td>
            Rp {{ number_format($item->target_nominal, 0, ',', '.') }} {{-- Format target nominal. --}}
        </td>

        {{-- Sudah ditabung --}}
        <td>
            Rp {{ number_format($sudahDitabung, 0, ',', '.') }} {{-- Format sudah ditabung. --}}
        </td>

        {{-- Sisa target --}}
        <td>
            @if ($sisaTarget > 0) {{-- Jika sisa target masih ada. --}}
                Rp {{ number_format($sisaTarget, 0, ',', '.') }} {{-- Format sisa target. --}}
            @else
                <span class="badge bg-success">Target Tercapai</span> {{-- Tanda target tercapai. --}}
            @endif
        </td>

        {{-- Target waktu --}}
        <td>
            {{ \Carbon\Carbon::parse($item->target_waktu)->format('d M Y') }} {{-- Format target waktu. --}}
        </td>

        {{-- Aksi --}}
        <td class="text-center"> {{-- Kolom aksi. --}}

            {{-- Tombol edit --}}
            <a
                href="{{ route('kategoriTabungan.edit', [
                    'kategoriTabungan' => $item->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]) }}"
                class="btn btn-sm btn-outline-primary"
            > {{-- Link ke form edit kategori tabungan. --}}
                Edit
            </a> {{-- Tutup link edit. --}}

            {{-- Tombol hapus --}}
            <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal{{ $item->id }}"
            > {{-- Tombol buka modal hapus. --}}
                Hapus
            </button> {{-- Tutup tombol hapus. --}}

            {{-- Modal konfirmasi hapus --}}
            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"> {{-- Modal konfirmasi hapus. --}}
                <div class="modal-dialog"> {{-- Dialog modal. --}}
                    <div class="modal-content"> {{-- Konten modal. --}}

                        <div class="modal-header"> {{-- Header modal. --}}
                            <h5 class="modal-title">Konfirmasi Hapus</h5> {{-- Judul modal. --}}
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button> {{-- Tombol close modal. --}}
                        </div> {{-- Tutup header modal. --}}

                        <div class="modal-body"> {{-- Isi modal. --}}
                            Yakin ingin menghapus kategori tabungan
                            <strong>{{ $item->nama_kategori }}</strong>? {{-- Nama kategori yang dihapus. --}}
                        </div> {{-- Tutup body modal. --}}

                        <div class="modal-footer"> {{-- Footer modal. --}}
                            <form
                                action="{{ route('kategoriTabungan.destroy', [
                                    'kategoriTabungan' => $item->id,
                                    'bulan' => $bulan,
                                    'tahun' => $tahun
                                ]) }}"
                                method="POST"
                            > {{-- Form hapus kategori tabungan. --}}
                                @csrf {{-- Token CSRF. --}}
                                @method('DELETE') {{-- Spoof method DELETE. --}}

                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Batal
                                </button> {{-- Tombol batal. --}}

                                <button type="submit" class="btn btn-danger">
                                    Hapus
                                </button> {{-- Tombol konfirmasi hapus. --}}
                            </form> {{-- Tutup form hapus. --}}
                        </div> {{-- Tutup footer modal. --}}

                    </div> {{-- Tutup konten modal. --}}
                </div> {{-- Tutup dialog modal. --}}
            </div> {{-- Tutup modal. --}}

        </td> {{-- Tutup kolom aksi. --}}
    </tr> {{-- Tutup baris data. --}}

@endforeach {{-- Selesai loop kategori tabungan. --}}

</tbody> {{-- Tutup body tabel. --}}

@endsection {{-- Akhiri section tabel. --}}
