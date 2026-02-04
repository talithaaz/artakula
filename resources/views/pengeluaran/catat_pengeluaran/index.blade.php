@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Pengeluaran | Artakula') {{-- Judul halaman pada title tag. --}}
@section('page_title', 'Overview Pengeluaran') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}
    <div class="d-flex justify-content-between mb-4"> {{-- Baris header halaman. --}}
        <h5 class="fw-bold">Catat Pengeluaran</h5> {{-- Judul kecil halaman. --}}
    </div> {{-- Tutup baris header. --}}

    {{-- FILTER BULAN & TAHUN --}}
    <div class="d-flex justify-content-between align-items-center gap-2 mb-4"> {{-- Baris filter dan tombol. --}}
        <form method="GET" class="d-flex align-items-center gap-2 mb-0"> {{-- Form filter bulan/tahun. --}}
            {{-- Pilih Bulan --}}
            <select name="bulan" class="form-select form-select-sm w-auto"> {{-- Dropdown bulan. --}}
                @for ($m = 1; $m <= 12; $m++) {{-- Loop 1-12 untuk bulan. --}}
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}> {{-- Opsi bulan. --}}
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }} {{-- Nama bulan terjemahan. --}}
                    </option> {{-- Tutup opsi bulan. --}}
                @endfor {{-- Selesai loop bulan. --}}
            </select> {{-- Tutup dropdown bulan. --}}

            {{-- Pilih Tahun --}}
            <select name="tahun" class="form-select form-select-sm w-auto"> {{-- Dropdown tahun. --}}
                @for ($y = now()->year - 5; $y <= now()->year + 5; $y++) {{-- Loop tahun -5 s.d +5. --}}
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}> {{-- Opsi tahun. --}}
                        {{ $y }} {{-- Tampilkan angka tahun. --}}
                    </option> {{-- Tutup opsi tahun. --}}
                @endfor {{-- Selesai loop tahun. --}}
            </select> {{-- Tutup dropdown tahun. --}}

            <button class="btn btn-sm btn-outline-primary"> {{-- Tombol submit filter. --}}
                Terapkan {{-- Label tombol. --}}
            </button> {{-- Tutup tombol submit. --}}
        </form> {{-- Tutup form filter. --}}

        {{-- Tombol Tambah Pengeluaran --}}
        <a href="{{ route('pengeluaran.create', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
           class="btn btn-sm btn-success"> {{-- Link ke form catat pengeluaran. --}}
            <i class="bi bi-plus-circle"></i> Catat Pengeluaran {{-- Ikon + teks tombol. --}}
        </a> {{-- Tutup link tombol tambah. --}}
    </div> {{-- Tutup baris filter dan tombol. --}}

    {{-- ALERT SUCCESS --}}
    @if (session('success')) {{-- Tampilkan alert jika ada pesan sukses. --}}
        <div class="alert alert-success"> {{-- Kotak pesan sukses. --}}
            {{ session('success') }} {{-- Isi pesan sukses. --}}
        </div> {{-- Tutup kotak pesan sukses. --}}
    @endif {{-- Tutup kondisi pesan sukses. --}}
@endsection {{-- Akhiri section konten. --}}

{{-- ================= TABEL PENGELUARAN ================= --}}
@section('table') {{-- Mulai section tabel. --}}
    <thead class="border-bottom"> {{-- Header tabel. --}}
        <tr> {{-- Baris judul kolom. --}}
            <th class="text-center">Tanggal</th> {{-- Kolom tanggal. --}}
            <th class="text-center">Keterangan</th> {{-- Kolom keterangan. --}}
            <th class="text-center">Kategori</th> {{-- Kolom kategori. --}}
            <th class="text-center">Dompet</th> {{-- Kolom dompet. --}}
            <th class="text-center">Jumlah</th> {{-- Kolom jumlah. --}}
            <th class="text-center">Aksi</th> {{-- Kolom aksi. --}}
        </tr> {{-- Tutup baris judul. --}}
    </thead> {{-- Tutup header tabel. --}}

    <tbody> {{-- Isi tabel. --}}
        @forelse ($pengeluaran as $item) {{-- Loop data pengeluaran, fallback jika kosong. --}}
            <tr> {{-- Baris data pengeluaran. --}}
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td> {{-- Tanggal format d M Y. --}}
                <td>{{ $item->keterangan }}</td> {{-- Tampilkan keterangan. --}}
                <td>{{ $item->kategori->nama_kategori }}</td> {{-- Tampilkan kategori. --}}
                <td>{{ $item->dompet->nama_dompet }}</td> {{-- Tampilkan dompet. --}}
                <td class="text-danger fw-bold"> {{-- Kolom jumlah dengan gaya. --}}
                    Rp {{ number_format($item->jumlah, 0, ',', '.') }} {{-- Format rupiah. --}}
                </td> {{-- Tutup kolom jumlah. --}}

                {{-- AKSI --}}
                <td class="text-center"> {{-- Kolom aksi. --}}
                    {{-- Edit --}}
                    <a href="{{ route('pengeluaran.edit', [
                        'pengeluaran' => $item->id,
                        'bulan' => $bulan,
                        'tahun' => $tahun
                    ]) }}"
                       class="btn btn-sm btn-outline-primary"> {{-- Link ke form edit pengeluaran. --}}
                        Edit {{-- Label tombol edit. --}}
                    </a> {{-- Tutup link edit. --}}

                    {{-- Hapus --}}
                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $item->id }}"> {{-- Tombol buka modal hapus. --}}
                        Hapus {{-- Label tombol hapus. --}}
                    </button> {{-- Tutup tombol hapus. --}}

                    {{-- MODAL KONFIRMASI HAPUS --}}
                    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"> {{-- Modal konfirmasi hapus. --}}
                        <div class="modal-dialog modal-dialog-centered"> {{-- Dialog modal. --}}
                            <div class="modal-content"> {{-- Konten modal. --}}
                                <div class="modal-header"> {{-- Header modal. --}}
                                    <h5 class="modal-title">Konfirmasi Hapus</h5> {{-- Judul modal. --}}
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button> {{-- Tombol close modal. --}}
                                </div> {{-- Tutup header modal. --}}

                                <div class="modal-body"> {{-- Isi modal. --}}
                                    Yakin mau hapus pengeluaran
                                    <strong>{{ $item->keterangan }}</strong>
                                    sebesar
                                    <strong class="text-danger">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </strong>? {{-- Pesan konfirmasi. --}}
                                </div> {{-- Tutup body modal. --}}

                                <div class="modal-footer"> {{-- Footer modal. --}}
                                    <form method="POST"
                                          action="{{ route('pengeluaran.destroy', [
                                              'pengeluaran' => $item->id,
                                              'bulan' => $bulan,
                                              'tahun' => $tahun
                                          ]) }}"> {{-- Form hapus pengeluaran. --}}
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
        @empty {{-- Jika tidak ada pengeluaran. --}}
            <tr> {{-- Baris kosong. --}}
                <td colspan="6" class="text-center text-muted"> {{-- Pesan kosong. --}}
                    Belum ada pengeluaran
                </td>
            </tr> {{-- Tutup baris kosong. --}}
        @endforelse {{-- Tutup loop pengeluaran. --}}
    </tbody> {{-- Tutup body tabel. --}}
@endsection {{-- Akhiri section tabel. --}}
