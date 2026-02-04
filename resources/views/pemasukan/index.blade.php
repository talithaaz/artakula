@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Pemasukan | Artakula') {{-- Judul halaman pada title tag. --}}
@section('page_title', 'Overview Pemasukan') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}

<div class="d-flex justify-content-between mb-4"> {{-- Baris header konten. --}}
    <h5 class="fw-bold">Catat Pemasukan</h5> {{-- Judul kecil pada konten. --}}
    {{-- Ruang kosong untuk elemen tambahan jika dibutuhkan. --}}
</div> {{-- Akhir baris header konten. --}}

<div class="d-flex justify-content-between align-items-center gap-2 mb-4"> {{-- Baris filter dan tombol tambah. --}}
        <form method="GET" class="d-flex align-items-center gap-2 mb-0"> {{-- Form filter bulan/tahun (GET). --}}
            {{-- Pilih Bulan --}}
            <select name="bulan" class="form-select form-select-sm w-auto"> {{-- Dropdown bulan. --}}
                @for ($m = 1; $m <= 12; $m++) {{-- Loop 1-12 untuk bulan. --}}
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}> {{-- Opsi bulan, set selected jika cocok. --}}
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }} {{-- Nama bulan terjemahan. --}}
                    </option> {{-- Tutup opsi bulan. --}}
                @endfor {{-- Selesai loop bulan. --}}
            </select> {{-- Tutup dropdown bulan. --}}

            {{-- Pilih Tahun --}}
            <select name="tahun" class="form-select form-select-sm w-auto"> {{-- Dropdown tahun. --}}
                @for ($y = now()->year - 5; $y <= now()->year + 5; $y++) {{-- Loop tahun dari -5 s.d +5. --}}
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}> {{-- Opsi tahun, set selected jika cocok. --}}
                        {{ $y }} {{-- Tampilkan angka tahun. --}}
                    </option> {{-- Tutup opsi tahun. --}}
                @endfor {{-- Selesai loop tahun. --}}
            </select> {{-- Tutup dropdown tahun. --}}

            <button class="btn btn-sm btn-outline-primary"> {{-- Tombol submit filter. --}}
                Terapkan {{-- Label tombol. --}}
            </button> {{-- Tutup tombol submit. --}}
        </form> {{-- Tutup form filter. --}}

        {{-- Tombol Tambah Pengeluaran --}}
        {{-- Link ke form tambah pemasukan. --}}
        <a href="{{ route('pemasukan.create', [
    'bulan' => $bulan, {{-- Kirim parameter bulan. --}}
    'tahun' => $tahun {{-- Kirim parameter tahun. --}}
]) }}" class="btn btn-success"> {{-- Gaya tombol tambah. --}}
        <i class="bi bi-plus-circle"></i> Tambah Pemasukan {{-- Ikon + teks tombol. --}}
    </a> {{-- Tutup link tombol tambah. --}}
    </div> {{-- Tutup baris filter dan tombol tambah. --}}

@if(session('success')) {{-- Tampilkan alert jika ada pesan sukses. --}}
<div class="alert alert-success">{{ session('success') }}</div> {{-- Kotak pesan sukses. --}}
@endif {{-- Tutup kondisi pesan sukses. --}}
@endsection {{-- Akhiri section konten. --}}

@section('table') {{-- Mulai section tabel (di layout). --}}
    <thead class="border-bottom"> {{-- Header tabel. --}}
        <tr> {{-- Baris judul kolom. --}}
            <th>Tanggal</th> {{-- Kolom tanggal. --}}
            <th>Keterangan</th> {{-- Kolom keterangan. --}}
            <th>Dompet</th> {{-- Kolom dompet. --}}
            <th class="text-end">Jumlah</th> {{-- Kolom jumlah (rata kanan). --}}
            <th class="text-center">Aksi</th> {{-- Kolom aksi (rata tengah). --}}
        </tr> {{-- Tutup baris judul kolom. --}}
    </thead> {{-- Tutup header tabel. --}}
    <tbody> {{-- Isi tabel. --}}
        @foreach($pemasukan as $item) {{-- Loop data pemasukan. --}}
<tr> {{-- Baris data pemasukan. --}}
    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td> {{-- Tanggal format d M Y. --}}
    <td>{{ $item->keterangan }}</td> {{-- Tampilkan keterangan. --}}
    <td>{{ $item->dompet->nama_dompet }}</td> {{-- Tampilkan nama dompet. --}}
    <td class="text-end text-success fw-bold"> {{-- Sel jumlah dengan gaya. --}}
        Rp {{ number_format($item->jumlah,0,',','.') }} {{-- Format rupiah. --}}
    </td> {{-- Tutup sel jumlah. --}}
    <td class="text-center"> {{-- Sel aksi. --}}
        {{-- Link ke form edit. --}}
        <a href="{{ route('pemasukan.edit', [
                        'pemasukan' => $item->id, {{-- Parameter id pemasukan. --}}
                        'bulan' => $bulan, {{-- Parameter bulan. --}}
                        'tahun' => $tahun {{-- Parameter tahun. --}}
                    ]) }}" class="btn btn-sm btn-outline-primary">Edit</a> {{-- Tombol edit. --}}

        <!-- Tombol hapus -->
        <button type="button" class="btn btn-sm btn-outline-danger"  {{-- Tombol buka modal hapus. --}}
            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}"> {{-- Target modal per item. --}}
            Hapus {{-- Label tombol hapus. --}}
        </button> {{-- Tutup tombol hapus. --}}

        <!-- Modal hapus -->
        <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true"> {{-- Modal konfirmasi hapus. --}}
            <div class="modal-dialog"> {{-- Wrapper dialog modal. --}}
                <div class="modal-content"> {{-- Konten modal. --}}
                    <div class="modal-header"> {{-- Header modal. --}}
                        <h5 class="modal-title">Konfirmasi Hapus</h5> {{-- Judul modal. --}}
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button> {{-- Tombol close modal. --}}
                    </div> {{-- Tutup header modal. --}}
                    <div class="modal-body"> {{-- Isi modal. --}}
                        Apakah kamu yakin ingin menghapus pemasukan <strong>{{ $item->keterangan }}</strong> <br>senilai <strong>Rp {{ number_format($item->jumlah,0,',','.') }}</strong>? {{-- Pesan konfirmasi. --}}
                    </div> {{-- Tutup body modal. --}}
                    <div class="modal-footer"> {{-- Footer modal. --}}
                        <form action="{{ route('pemasukan.destroy', $item->id) }}" method="POST"> {{-- Form hapus. --}}
                            @csrf {{-- Token CSRF. --}}
                            @method('DELETE') {{-- Spoof method DELETE. --}}
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> {{-- Tombol batal. --}}
                            <button type="submit" class="btn btn-danger">Hapus</button> {{-- Tombol submit hapus. --}}
                        </form> {{-- Tutup form hapus. --}}
                    </div> {{-- Tutup footer modal. --}}
                </div> {{-- Tutup konten modal. --}}
            </div> {{-- Tutup dialog modal. --}}
        </div> {{-- Tutup modal. --}}

    </td> {{-- Tutup sel aksi. --}}
</tr> {{-- Tutup baris data. --}}
@endforeach {{-- Selesai loop data. --}}

    </tbody> {{-- Tutup body tabel. --}}
</table> {{-- Tutup tabel (dibuka di layout). --}}
</div> {{-- Tutup wrapper tabel (dibuka di layout). --}}

@endsection {{-- Akhiri section tabel. --}}
