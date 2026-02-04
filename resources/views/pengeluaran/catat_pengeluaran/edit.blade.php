@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Edit Pengeluaran') {{-- Judul halaman pada title tag. --}}
@section('page_title', 'Overview Pengeluaran') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}
    <h5 class="fw-bold mb-4">Edit Pengeluaran</h5> {{-- Judul kecil halaman. --}}

    <div class="card-artakula p-4 col-md-8"> {{-- Card wrapper form. --}}
        <form method="POST" action="{{ route('pengeluaran.update', $pengeluaran->id) }}"> {{-- Form update pengeluaran. --}}
            @csrf {{-- Token CSRF. --}}
            @method('PUT') {{-- Spoof method PUT. --}}

            {{-- Kategori --}}
            <div class="mb-3"> {{-- Grup input kategori. --}}
                <label class="form-label fw-bold">Kategori Pengeluaran</label> {{-- Label kategori. --}}
                <select name="kategori_id" class="form-select" required> {{-- Dropdown kategori. --}}
                    <option value="">Pilih Kategori</option> {{-- Placeholder kategori. --}}
                    @foreach ($kategori as $k) {{-- Loop daftar kategori. --}}
                        <option value="{{ $k->id }}"
                            {{ $pengeluaran->kategori_id == $k->id ? 'selected' : '' }}> {{-- Set selected jika cocok. --}}
                            {{ $k->nama_kategori }} {{-- Nama kategori. --}}
                        </option> {{-- Tutup opsi kategori. --}}
                    @endforeach {{-- Tutup loop kategori. --}}
                </select> {{-- Tutup dropdown kategori. --}}
            </div> {{-- Tutup grup input kategori. --}}

            {{-- Dompet --}}
            <div class="mb-3"> {{-- Grup input dompet. --}}
                <label class="form-label fw-bold">Dompet</label> {{-- Label dompet. --}}
                <select name="dompet_id" class="form-select" required> {{-- Dropdown dompet. --}}
                    <option value="">Pilih Dompet</option> {{-- Placeholder dompet. --}}
                    @foreach ($dompets as $d) {{-- Loop daftar dompet. --}}
                        <option value="{{ $d->id }}"
                            {{ $pengeluaran->dompet_id == $d->id ? 'selected' : '' }}> {{-- Set selected jika cocok. --}}
                            {{ $d->nama_dompet }} {{-- Nama dompet. --}}
                        </option> {{-- Tutup opsi dompet. --}}
                    @endforeach {{-- Tutup loop dompet. --}}
                </select> {{-- Tutup dropdown dompet. --}}
            </div> {{-- Tutup grup input dompet. --}}

            {{-- Keterangan --}}
            <div class="mb-3"> {{-- Grup input keterangan. --}}
                <label class="form-label fw-bold">Keterangan</label> {{-- Label keterangan. --}}
                <input type="text"
                       name="keterangan"
                       class="form-control"
                       value="{{ $pengeluaran->keterangan }}"
                       required> {{-- Input keterangan. --}}
            </div> {{-- Tutup grup input keterangan. --}}

            {{-- Jumlah --}}
            <div class="mb-3"> {{-- Grup input jumlah. --}}
                <label class="form-label fw-bold">Jumlah (Rp)</label> {{-- Label jumlah. --}}
                <input type="number"
                       name="jumlah"
                       class="form-control"
                       value="{{ $pengeluaran->jumlah }}"
                       required> {{-- Input jumlah. --}}
            </div> {{-- Tutup grup input jumlah. --}}

            {{-- Tanggal --}}
            <div class="mb-3"> {{-- Grup input tanggal. --}}
                <label class="form-label fw-bold">Tanggal</label> {{-- Label tanggal. --}}
                <input type="date"
                       name="tanggal"
                       class="form-control"
                       value="{{ $pengeluaran->tanggal }}"
                       required> {{-- Input tanggal. --}}
            </div> {{-- Tutup grup input tanggal. --}}

            {{-- Aksi --}}
            <a href="{{ route('pengeluaran.index', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun')
            ]) }}"
               class="btn btn-secondary"> {{-- Tombol batal. --}}
                Batal
            </a>

            <button class="btn btn-success">
                Update
            </button> {{-- Tombol update. --}}
        </form> {{-- Tutup form. --}}
    </div> {{-- Tutup card wrapper. --}}
@endsection {{-- Akhiri section konten. --}}
