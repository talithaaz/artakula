@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Tambah Kategori Tabungan | Artakula') {{-- Judul halaman pada title tag. --}}
@section('page_title', 'Overview Tabungan') {{-- Judul halaman di header layout. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Tambah Kategori Tabungan</h5> {{-- Judul kecil halaman. --}}

<div class="card-artakula p-4 col-md-6"> {{-- Card wrapper form. --}}

    {{-- Form tambah kategori --}}
    <form action="{{ route('kategoriTabungan.store') }}" method="POST"> {{-- Form simpan kategori tabungan. --}}
        @csrf {{-- Token CSRF. --}}

        {{-- Simpan filter bulan & tahun --}}
        <input type="hidden" name="bulan" value="{{ request('bulan') }}"> {{-- Simpan bulan filter. --}}
        <input type="hidden" name="tahun" value="{{ request('tahun') }}"> {{-- Simpan tahun filter. --}}

        {{-- Nama kategori --}}
        <div class="mb-3"> {{-- Grup input nama tabungan. --}}
            <label class="form-label fw-bold">Nama Tabungan</label> {{-- Label nama tabungan. --}}
            <input
                type="text"
                name="nama_kategori"
                class="form-control"
                placeholder="Contoh: Rumah, Liburan"
                required
            > {{-- Input nama tabungan. --}}
        </div> {{-- Tutup grup input nama. --}}

        {{-- Dompet tujuan --}}
        <div class="mb-3"> {{-- Grup input dompet tujuan. --}}
            <label class="form-label fw-bold">Dompet Tujuan</label> {{-- Label dompet tujuan. --}}
            <select name="dompet_tujuan_id" class="form-select" required> {{-- Dropdown dompet tujuan. --}}
                <option value="">Pilih Dompet Tujuan</option> {{-- Placeholder dompet tujuan. --}}

                @foreach ($dompet as $d) {{-- Loop daftar dompet. --}}
                    <option value="{{ $d->id }}"> {{-- Opsi dompet tujuan. --}}
                        {{ $d->nama_dompet }} {{-- Nama dompet. --}}
                    </option> {{-- Tutup opsi dompet. --}}
                @endforeach {{-- Tutup loop dompet. --}}
            </select> {{-- Tutup dropdown dompet tujuan. --}}
        </div> {{-- Tutup grup input dompet tujuan. --}}

        {{-- Target nominal --}}
        <div class="mb-3"> {{-- Grup input target nominal. --}}
            <label class="form-label fw-bold">Target Nominal (Rp)</label> {{-- Label target nominal. --}}
            <input
                type="number"
                name="target_nominal"
                class="form-control"
                placeholder="Jumlah Target"
                required
            > {{-- Input target nominal. --}}
        </div> {{-- Tutup grup input target nominal. --}}

        {{-- Target waktu --}}
        <div class="mb-3"> {{-- Grup input target waktu. --}}
            <label class="form-label fw-bold">Target Waktu</label> {{-- Label target waktu. --}}
            <input
                type="date"
                name="target_waktu"
                class="form-control"
                required
            > {{-- Input target waktu. --}}
        </div> {{-- Tutup grup input target waktu. --}}

        {{-- Tombol aksi --}}
        <div class="d-flex gap-2"> {{-- Wrapper tombol aksi. --}}
            <a
                href="{{ route('kategoriTabungan.index', [
                    'bulan' => request('bulan'),
                    'tahun' => request('tahun')
                ]) }}"
                class="btn btn-secondary"
            > {{-- Tombol batal. --}}
                Batal
            </a>

            <button type="submit" class="btn btn-success">
                Simpan
            </button> {{-- Tombol simpan. --}}
        </div> {{-- Tutup wrapper tombol aksi. --}}

    </form> {{-- Tutup form. --}}

</div> {{-- Tutup card wrapper. --}}

@endsection {{-- Akhiri section konten. --}}
